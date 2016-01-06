<?php
/**
 * mofilmUserMovieStats
 *
 * Stored in stats.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserMovieStats
 * @category mofilmUserMovieStats
 * @version $Rev: 146 $
 */


/**
 * mofilmUserMovieStats Class
 *
 * Collects statistics for a specific mofilm user
 *
 * @package mofilm
 * @subpackage mofilmUserMovieStats
 * @category mofilmUserMovieStats
 */
class mofilmUserMovieStats {

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;
	
	/**
	 * Stores $_OptionsSet
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_OptionsSet;

	const STAT_TOTAL_MOVIES = 'totalMovies';
	const STAT_TOTAL_APPROVED = 'totalApproved';
	const STAT_TOTAL_REJECTED = 'totalRejected';
	const STAT_TOTAL_AWAITING = 'totalAwaiting';
	const STAT_COMPS_ENTERED = 'competitionsEntered';
	const STAT_TIMES_SHORTLISTED = 'shortlisted';



	/**
	 * Creates a new stats instance
	 *
	 * @param integer $inUserID
	 */
	function __construct($inUserID = null) {
		$this->reset();
		if ( $inUserID !== null ) {
			$this->setUserID($inUserID);
			$this->load();
		}
	}
	
	/**
	 * Loads an array of users with statistics data, expects array to be indexed by userID
	 * 
	 * @param array $inUsers
	 * @return void
	 * @static
	 */
	static function loadArrayOfUsersWithProperties(array $inUsers) {
		$return = false;
		$properties = array();
		if ( count($inUsers) > 0 ) {
			$query = '
				SELECT users.ID AS userID,
					(SELECT count(movies.ID) FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies WHERE status = 5 AND userID = users.ID) AS totalApproved,
					(SELECT count(movies.ID) FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies WHERE status = 4 AND userID = users.ID) AS totalRejected,
					(SELECT count(movies.ID) FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies WHERE status <> 4 AND status <> 5 AND userID = users.ID) AS totalAwaiting,
					(SELECT count(movies.ID) FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies WHERE userID = users.ID) AS totalMovies,
					IFNULL(
					  (SELECT COUNT(DISTINCT(sources.eventID))
					     FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
					          INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movies.ID = movieSources.movieID)
					          INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (movieSources.sourceID = sources.ID)
					    WHERE movies.userID = users.ID
					      AND movies.status = 5
					      AND movies.active = "Y"
					    GROUP BY movies.userID
					), 0) AS competitionsEntered,
					(SELECT COUNT(DISTINCT(movieAwards.movieID))
					   FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
					        INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movies ON (movieAwards.movieID = movies.ID)
					  WHERE movies.userID = users.ID
					    AND movies.active = "Y"
					    AND movies.status = "Approved"
					    AND movieAwards.type = "Shortlisted"
					) AS shortlisted
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.users
				 WHERE users.ID IN ('.implode(',', array_keys($inUsers)).')';

			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$properties[$row['userID']] = $row; 
				}
			}
			$oStmt->closeCursor();
			
			if ( false ) $oUser = new mofilmUser();
			foreach ( $inUsers as $oUser ) {
				if ( $oUser instanceof mofilmUser ) {
					if ( array_key_exists($oUser->getID(), $properties) ) {
						$oObject = new mofilmUserMovieStats();
						$oObject->setUserID($oUser->getID());
						$oObject->getOptionsSet()->setOptions($properties[$oUser->getID()]);
						$oObject->setModified(false);
						
						$oUser->setStats($oObject);
						$return = true;
					}
				}
			}
		}
		return $return;
	}

	/**
	 * Loads the object with data
	 *
	 * @return boolean
	 */
	function load() {
		if ( $this->getUserID() ) {
			// 'Encoding','Pending','Removed','Rejected','Approved','Disputed','Failed Encoding'
			$query = '
				SELECT
				    (SELECT COUNT(movies.ID) FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies WHERE status = 5 AND userID = :UserID) AS totalApproved,
					(SELECT COUNT(movies.ID) FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies WHERE status = 4 AND userID = :UserID) AS totalRejected,
					(SELECT COUNT(movies.ID) FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies WHERE status <> 4 AND status <> 5 AND userID = :UserID) AS totalAwaiting,
					(SELECT COUNT(movies.ID) FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies WHERE userID = :UserID) AS totalMovies,
					IFNULL(
					  (SELECT COUNT(DISTINCT(sources.eventID))
					     FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
					          INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movies.ID = movieSources.movieID)
					          INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (movieSources.sourceID = sources.ID)
					    WHERE movies.userID = :UserID
					      AND movies.status = 5
					      AND movies.active = "Y"
					    GROUP BY movies.userID) , 0) AS competitionsEntered,
					
					(SELECT COUNT(DISTINCT(movieAwards.movieID))
					   FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
					        INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movies ON (movieAwards.movieID = movies.ID)
					  WHERE movies.userID = :UserID
					    AND movies.active = "Y"
					    AND movies.status = "Approved"
					    AND movieAwards.type = "Shortlisted"
					) AS shortlisted';

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->getUserID(), PDO::PARAM_INT);
			if ( $oStmt->execute() ) {
				$res = $oStmt->fetchAll();
				if ( is_array($res) && count($res) == 1 ) {
					$this->getOptionsSet()->setOptions($res[0]);
				}
				unset($res);
			}
			$oStmt->closeCursor();
		}
		return true;
	}

	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_UserID = null;
		$this->_OptionsSet = null;
		$this->setModified(false);
	}

	

	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 * @access public
	 */
	function isModified() {
		return $this->_Modified;
	}

	/**
	 * Set $_Modified to $inModified
	 *
	 * @param boolean $inModified
	 * @return mofilmUserMovieStats
	 * @access public
	 */
	function setModified($inModified = true) {
		if ( $inModified !== $this->_Modified ) {
			$this->_Modified = $inModified;
		}
		return $this;
	}

	/**
	 * Return value of $_User
	 *
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserMovieStats
	 * @access public
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the baseOptionsSet object
	 *
	 * @return baseOptionsSet
	 * @access public
	 */
	function getOptionsSet() {
		if ( !$this->_OptionsSet instanceof baseOptionsSet ) {
			$this->_OptionsSet = new baseOptionsSet();
		}
		return $this->_OptionsSet;
	}

	/**
	 * Set a new instance of the options set
	 *
	 * @param baseOptionsSet $inOptionsSet
	 * @return mofilmUserMovieStats
	 * @access public
	 */
	function setOptionsSet(baseOptionsSet $inOptionsSet) {
		if ( $inOptionsSet !== $this->_OptionsSet ) {
			$this->_OptionsSet = $inOptionsSet;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns option named $inOption, or $inDefault if not found
	 *
	 * @param string $inOption
	 * @param mixed $inDefault
	 * @return mixed
	 */
	function getOption($inOption, $inDefault = null) {
		return $this->getOptionsSet()->getOptions($inOption, $inDefault);
	}

	/**
	 * Set the option $inOption to $inValue
	 *
	 * @param string $inOption
	 * @param mixed $inValue
	 * @return mofilmUserMovieStats
	 */
	function setOption($inOption, $inValue) {
		$this->getOptionsSet()->setOptions(array($inOption => $inValue));
		return $this;
	}

	/**
	 * Returns the total movie count
	 *
	 * @return integer
	 */
	function getMovieCount() {
		return $this->getOption(self::STAT_TOTAL_MOVIES, 0);
	}
	
	/**
	 * Alias of getMovieCount for consistency
	 * 
	 * @return integer
	 */
	function getTotalMovies() {
		return $this->getMovieCount();
	}

	/**
	 * Returns the total approved count
	 *
	 * @return integer
	 */
	function getTotalApproved() {
		return $this->getOption(self::STAT_TOTAL_APPROVED, 0);
	}

	/**
	 * Returns the total awaiting count
	 *
	 * @return integer
	 */
	function getTotalAwaiting() {
		return $this->getOption(self::STAT_TOTAL_AWAITING, 0);
	}

	/**
	 * Returns the total rejected count
	 *
	 * @return integer
	 */
	function getTotalRejected() {
		return $this->getOption(self::STAT_TOTAL_REJECTED, 0);
	}

	/**
	 * Returns the number of unique competitions entered
	 *
	 * @return integer
	 */
	function getCompetitionsEntered() {
		return $this->getOption(self::STAT_COMPS_ENTERED, 0);
	}

	/**
	 * Returns the number of times videos were shortlisted
	 *
	 * @return integer
	 */
	function getShortlistedCount() {
		return $this->getOption(self::STAT_TIMES_SHORTLISTED, 0);
	}
}