<?php
/**
 * mofilmLeaderboardUtilities
 * 
 * Stored in mofilmLeaderboardUtilities.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2011
 * @package mofilm
 * @subpackage mofilmLeaderboardUtilities
 * @category mofilmLeaderboardUtilities
 * @version $Rev: 41 $
 */


/**
 * mofilmLeaderboardUtilities Class
 * 
 * Assorted utility methods for managing the leaderboard, building and resetting it.
 * 
 * @package mofilm
 * @subpackage mofilmLeaderboardUtilities
 * @category mofilmLeaderboardUtilities
 */
class mofilmLeaderboardUtilities {
	
	const POINTS_SUBMISSION = 25;
	const POINTS_SHORTLISTED = 25;
	const POINTS_EVENT_WINNER = 250;
	const POINTS_FINALIST_1 = 750;
	const POINTS_FINALIST_2 = 500;
	const POINTS_FINALIST_3 = 400;
	const POINTS_FINALIST_4 = 400;
	const POINTS_FINALIST_5 = 400;

	/**
	 * Returns an array of all the countries that have winners
	 *
	 * @return array
	 * @static
	 */
	static function getCountriesWithWinners() {
		$return = array();

		$query = '
			SELECT territories.*
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.users ON (movieAwards.userID = users.ID)
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.territories ON (users.territoryID = territories.ID)
			 WHERE movieAwards.type = :Type
			   AND movieAwards.position > 0
			   AND users.enabled = :Active
			 GROUP BY territories.ID
			 ORDER BY territories.country ASC';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Type', mofilmMovieAward::TYPE_FINALIST);
		$oStmt->bindValue(':Active', mofilmUserBase::ENABLED_Y);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmTerritory();
				$oObject->loadFromArray($row);

				$return[] = $oObject;
			}
		}

		return $return;
	}

	/**
	 * Resets all stats to 0
	 *
	 * @return void
	 * @static
	 */
	static function resetPoints() {
		$queryReset = '
			UPDATE '.system::getConfig()->getDatabase('mofilm_content').'.userPoints
			   SET score = 0, highScore = 0';

		dbManager::getInstance()->exec($queryReset);
	}

	/**
	 * Builds the user points based on pre-defined criteria for the last 12 months
	 *
	 * Position	Points
	 * 1	750
	 * 2	500
	 * 3	400
	 * 4	400
	 * 5	400
	 * Grand Prize	250
	 * Submission	25
	 * Shortlisted  25
	 * 
	 * @return void
	 * @static
	 */
	static function buildStats() {
		$stats = array();
		$minDate = date(system::getConfig()->getDatabaseDatetimeFormat(), strtotime('-1 year'));

		/*
		 * Assign points for uploading an approved movie
		 * 2011-07-04: DR, commented out because the scoring changed...
		 */
		//self::buildUserUploadScores($stats, $minDate);
		
		/*
		 * Tally those that have won an event
		 */
		self::buildUserAwardsByTypeScores($stats, mofilmMovieAward::TYPE_WINNER, null, $minDate);

		/*
		 * Tally shortlisted awards
		 */
		self::buildUserAwardsByTypeScores($stats, mofilmMovieAward::TYPE_SHORTLISTED, null, $minDate);

		/*
		 * Get all the finalists (those that won placed 1-n+1)
		 */
		self::buildUserAwardsByTypeScores($stats, mofilmMovieAward::TYPE_FINALIST, 5, $minDate);

		/*
		 * Build insert query
		 */
		$values = array();
		foreach ( $stats as $userID => $score ) {
			$values[] = sprintf('(%d, %d, "%s", "%s")', $userID, $score, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
		}

		$queryInsert = '
			INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userPoints
				(userID, score, createDate, updateDate)
			VALUES
				'.implode(',', $values).'
			ON DUPLICATE KEY UPDATE
				score=VALUES(score),
				updateDate=VALUES(updateDate)';

		$rows = dbManager::getInstance()->exec($queryInsert);

		$stats = null;
		unset($stats);
	}

	/**
	 * Builds the user high score points based on pre-defined criteria
	 *
	 * @return void
	 * @static
	 */
	static function buildHighScoreStats() {
		$stats = array();

		/*
		 * Assign points for uploading an approved movie
		 * 2011-07-04: DR, commented out because the scoring changed...
		 */
		//self::buildUserUploadScores($stats);

		/*
		 * Tally those that have won an event
		 */
		self::buildUserAwardsByTypeScores($stats, mofilmMovieAward::TYPE_WINNER);

		/*
		 * Tally shortlisted awards
		 */
		self::buildUserAwardsByTypeScores($stats, mofilmMovieAward::TYPE_SHORTLISTED);

		/*
		 * Get all the finalists (those that won placed 1-n+1)
		 */
		self::buildUserAwardsByTypeScores($stats, mofilmMovieAward::TYPE_FINALIST, 5);

		$values = array();
		foreach ( $stats as $userID => $score ) {
			$values[] = sprintf('(%d, %d, "%s", "%s")', $userID, $score, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
		}

		$queryInsert = '
			INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userPoints
				(userID, highScore, createDate, updateDate)
			VALUES
				'.implode(',', $values).'
			ON DUPLICATE KEY UPDATE
				highScore=VALUES(highScore),
				updateDate=VALUES(updateDate)';

		$rows = dbManager::getInstance()->exec($queryInsert);

		$stats = null;
		unset($stats);
	}


	
	/**
	 * Populates $inUsers with the scores of people who have uploaded, approved movies
	 * 
	 * @param array $inUsers (reference) Array to populate
	 * @param datetime $inStartDate (optional) Start date to collect data from
	 * @return void
	 * @static
	 */
	static function buildUserUploadScores(array &$inUsers = array(), $inStartDate = null) {
		$values = array(
			':Status' => mofilmMovie::STATUS_APPROVED,
			':Active' => mofilmMovie::ACTIVE_Y,
		);
		
		$query = '
			SELECT userID, COUNT(*) AS cnt
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
			 WHERE status = :Status
			   AND active = :Active';

		if ( $inStartDate !== null ) {
			$query .= ' AND movies.uploaded >= :MinDate ';
			$values[':MinDate'] = $inStartDate;
		}
				
		$query .= ' GROUP BY userID';

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				if ( !array_key_exists($row['userID'], $inUsers) ) {
					$inUsers[$row['userID']] = 0;
				}
				$inUsers[$row['userID']] += self::POINTS_SUBMISSION*$row['cnt'];
			}
		}
		$oStmt->closeCursor();
	}

	/**
	 * Populates the array $inUsers with scores derived from awards
	 *
	 * @param array $inUsers (reference) Array to populate
	 * @param string $inAwardType Award string from {@link mofilmMovieAward}
	 * @param integer $inMinPosition (optional) A minimum award position to count from e.g. 3rd, 5th etc
	 * @param null $inStartDate (optional) Award start date
	 * @return void
	 * @static
	 * @throws mofilmException
	 */
	static function buildUserAwardsByTypeScores(array &$inUsers = array(), $inAwardType, $inMinPosition = null, $inStartDate = null) {
		if ( !in_array($inAwardType, mofilmMovieAward::getTypes()) ) {
			throw new mofilmException(sprintf('Invalid award type (%s) specified', $inAwardType));
		}
		
		$values = array(
			':Type' => $inAwardType,
			':Status' => mofilmMovie::STATUS_APPROVED,
			':Active' => mofilmMovie::ACTIVE_Y,
		);
		
		$query = '
			SELECT movies.userID, movieAwards.position, COUNT(*) AS cnt
			    FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
                            INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movies ON (movieAwards.movieID = movies.ID)
                            JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movieSources.movieID = movies.ID)
                            JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (movieSources.sourceID = sources.ID)
                            JOIN '.system::getConfig()->getDatabase('mofilm_content').'.events ON (sources.eventID = events.ID)
                            JOIN '.system::getConfig()->getDatabase('mofilm_content').'.products ON (products.id = events.productID)
			 WHERE movieAwards.type = :Type
			   AND movies.active = :Active
			   AND movies.status = :Status
                           AND products.name IN ("Marquee","MOPHOTO","MOMINDS")';

		if ( $inMinPosition !== null && is_numeric($inMinPosition) && $inMinPosition > 0 ) {
			$query .= ' AND movieAwards.position <= :Position ';
			$values[':Position'] = $inMinPosition;
		}
		if ( $inStartDate !== null ) {
			$query .= ' AND movies.uploaded >= :MinDate ';
			$values[':MinDate'] = $inStartDate;
		}
		
		$query .= ' GROUP BY movieAwards.position, movies.userID';

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				if ( !array_key_exists($row['userID'], $inUsers) ) {
					$inUsers[$row['userID']] = 0;
				}

				switch ( $inAwardType ) {
					case mofilmMovieAward::TYPE_FINALIST:
						switch ( $row['position'] ) {
							case 1: $inUsers[$row['userID']] += self::POINTS_FINALIST_1*$row['cnt']; break;
							case 2: $inUsers[$row['userID']] += self::POINTS_FINALIST_2*$row['cnt']; break;
							case 3: $inUsers[$row['userID']] += self::POINTS_FINALIST_3*$row['cnt']; break;
							case 4: $inUsers[$row['userID']] += self::POINTS_FINALIST_4*$row['cnt']; break;
							case 5: $inUsers[$row['userID']] += self::POINTS_FINALIST_5*$row['cnt']; break;
						}
					break;

					case mofilmMovieAward::TYPE_SHORTLISTED:
						$inUsers[$row['userID']] += self::POINTS_SHORTLISTED*$row['cnt']; break;
					break;

					case mofilmMovieAward::TYPE_WINNER:
						$inUsers[$row['userID']] += self::POINTS_EVENT_WINNER*$row['cnt']; break;
					break;
				}
			}
		}
		$oStmt->closeCursor();
	}
}