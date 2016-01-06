<?php
/**
 * mofilmReportUserRatedMovieExport
 * 
 * Stored in mofilmReportUserRatedMovieExport.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserRatedMovieExport
 * @version $Rev: 245 $
 */


/**
 * mofilmReportUserRatedMovieExport
 * 
 * Exports the movies with a rating greater than or equal to that specified,
 * but only approved movies.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserRatedMovieExport
 */
class mofilmReportUserRatedMovieExport extends mofilmReportBase {

	const OPTION_MOVIE_RATING = 'report.movie.rating';

	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( !$this->getMovieRating() || !is_numeric($this->getMovieRating()) || $this->getMovieRating() < 1 || $this->getMovieRating() > 10 ) {
			throw new reportCentreException('Invalid value supplied for the movie rating, please select between 1 and 10');
		}
		return true;
	}
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('movieID', 'Movie ID', 10));
		$this->addReportColumn(new reportColumn('title', 'Movie Title ', 40));
		$this->addReportColumn(new reportColumn('event', 'Event ', 30));
		$this->addReportColumn(new reportColumn('source', 'Brand', 30));
		$this->addReportColumn(new reportColumn('rating', 'Rating', 10));
		$this->addReportColumn(new reportColumn('tinyUrl', 'Link', 40));
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = '
			SELECT movies.id AS movieID, movies.avgRating AS rating, sources.name AS source,
			       events.name AS event, movies.shortDesc AS title
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources
			          ON ( movies.ID = movieSources.movieID)
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources
			          ON ( movieSources.sourceID = sources.ID)
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.events
			          ON (sources.eventID = events.ID)
			 WHERE movies.status = :Status
			   AND movies.active = :Active
			   AND avgRating >= :Rating';

		if ( $this->getEventID() ) {
			$query .= ' AND movieSources.sourceID IN ('.implode(',', $this->getEvent()->getSourceSet()->getObjectIDs()).')';
		}

		$query .= '
			   AND events.ID NOT IN (4, 11, 22)
			 ORDER BY source ASC, movies.ID DESC ';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':Rating', $this->getMovieRating(), PDO::PARAM_INT);
		$oStmt->bindValue(':Status', mofilmMovieBase::STATUS_APPROVED);
		$oStmt->bindValue(':Active', mofilmMovieBase::ACTIVE_Y);
		
		if ( $oStmt->execute() ) {
			/*
			 * Pre-cache the results so that we can try to get the TinyURLs in one go
			 * instead of multiple queries for each row.
			 */
			$tmp = array();
			$movies = array();
			foreach ( $oStmt as $row ) {
				$tmp[$row['movieID']] = $row;
				$tmp[$row['movieID']]['tinyUrl'] = '';
				$movies[] = $row['movieID'];
			}

			/*
			 * Fetch pre-made user generated tinyUrls
			 */
			$query = '
				SELECT movieLinks.movieID, movieLinks.hash
				  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.movieLinks
				 WHERE senderID = :SenderID
				   AND movieID IN ('.implode(',', $movies).')';
			$oStmt2 = dbManager::getInstance()->prepare($query);
			$oStmt2->bindValue(':SenderID', $this->getUserID());
			if ( $oStmt2->execute() ) {
				foreach ( $oStmt2 as $row ) {
					$tmp[$row['movieID']]['tinyUrl'] = $row['hash'];
				}
			}
			$oStmt2->closeCursor();

			/*
			 * Create report data set
			 */

			$screeningUrl = system::getConfig()->getParam('mofilm', 'screeningUrl', 'http://mofilm.com/p').'/';
			$this->getReportData()->query()->beginTransaction();
			
			foreach ( $tmp as $row ) {
				if ( !$row['tinyUrl'] ) {
					$oObject = mofilmMovieLink::getInstanceByUnqSenderMovie($this->getUserID(), $row['movieID']);
					if ( $oObject->getID() == 0 || !$oObject->getHash() ) {
						$oObject->setMovieID($row['movieID']);
						$oObject->setSenderID($this->getUserID());
						$oObject->setHash(mofilmUtilities::buildMiniHash($oObject, 6));
						$oObject->save();
					}
					$row['tinyUrl'] = $oObject->getHash();
				}

				$row['tinyUrl'] = $screeningUrl.$row['tinyUrl'];
				
				$this->getReportData()->addRow($row);
			}
			
			$this->getReportData()->query()->commit();
		}
		$oStmt->closeCursor();
		return true;
	}

	/**
	 * @see reportBase::getReportName()
	 *
	 * @return string
	 */
	function getReportName() {
		return 'Top Rated Movies'.($this->getEventID() ? ' '.$this->getEventID() : '');
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		$string = 'Report detailing all movies rated at or above '.$this->getMovieRating();
		if ( $this->getEventID() ) {
			$string .= ' for event '.$this->getEvent()->getName();
		}
		return $string;
	}
	
	/**
	 * @see reportBase::getValidGroupByOptions() 
	 */
	function getValidGroupByOptions() {
		return array();
	}

	/**
	 * @see reportBase::getValidOrderByOptions()
	 */
	function getValidOrderByOptions() {
		return array();
	}



	/**
	 * Returns the movie rating to report on
	 *
	 * @return integer
	 */
	function getMovieRating() {
		return $this->getOption(self::OPTION_MOVIE_RATING, 9);
	}

	/**
	 * Sets the movie rating to report on
	 *
	 * @param integer $inOption
	 * @return reportBase
	 */
	function setMovieRating($inOption) {
		return $this->setOptions(array(self::OPTION_MOVIE_RATING => $inOption));
	}
}