<?php
/**
 * mofilmMoviePublicVoteSummary
 *
 * Stored in mofilmMoviePublicVoteSummary.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMoviePublicVoteSummary
 * @category mofilmMoviePublicVoteSummary
 * @version $Rev: 10 $
 */


/**
 * mofilmMoviePublicVoteSummary Class
 *
 * Provides access to records in mofilm_comms.moviePublicVoteSummary
 *
 * Creating a new record:
 * <code>
 * $oMofilmMoviePublicVoteSummary = new mofilmMoviePublicVoteSummary();
 * $oMofilmMoviePublicVoteSummary->setSourceID($inSourceID);
 * $oMofilmMoviePublicVoteSummary->setMovieID($inMovieID);
 * $oMofilmMoviePublicVoteSummary->setVotes($inVotes);
 * $oMofilmMoviePublicVoteSummary->setScore($inScore);
 * $oMofilmMoviePublicVoteSummary->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMoviePublicVoteSummary = new mofilmMoviePublicVoteSummary($inSourceID, $inMovieID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmMoviePublicVoteSummary = new mofilmMoviePublicVoteSummary();
 * $oMofilmMoviePublicVoteSummary->setSourceID($inSourceID);
 * $oMofilmMoviePublicVoteSummary->setMovieID($inMovieID);
 * $oMofilmMoviePublicVoteSummary->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmMoviePublicVoteSummary = mofilmMoviePublicVoteSummary::getInstance($inSourceID, $inMovieID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmMoviePublicVoteSummary
 * @category mofilmMoviePublicVoteSummary
 */
class mofilmMoviePublicVoteSummary implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmMoviePublicVoteSummary
	 *
	 * @var array
	 * @access protected
	 * @static
	 */
	protected static $_Instances = array();

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;

	/**
	 * Stores $_SourceID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SourceID;

	/**
	 * Stores $_MovieID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;

	/**
	 * Stores $_Votes
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Votes;

	/**
	 * Stores $_Score
	 *
	 * @var float 
	 * @access protected
	 */
	protected $_Score;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmMoviePublicVoteSummary
	 *
	 * @param integer $inSourceID
	 * @param integer $inMovieID
	 * @return mofilmMoviePublicVoteSummary
	 */
	function __construct($inSourceID = null, $inMovieID = null) {
		$this->reset();
		if ( $inSourceID !== null && $inMovieID !== null ) {
			$this->setSourceID($inSourceID);
			$this->setMovieID($inMovieID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new mofilmMoviePublicVoteSummary containing non-unique properties
	 *
	 * @param integer $inVotes
	 * @param float $inScore
	 * @return mofilmMoviePublicVoteSummary
	 * @static
	 */
	public static function factory($inVotes = null, $inScore = null) {
		$oObject = new mofilmMoviePublicVoteSummary;
		if ( $inVotes !== null ) {
			$oObject->setVotes($inVotes);
		}
		if ( $inScore !== null ) {
			$oObject->setScore($inScore);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmMoviePublicVoteSummary by primary key
	 *
	 * @param integer $inSourceID
	 * @param integer $inMovieID
	 * @return mofilmMoviePublicVoteSummary
	 * @static
	 */
	public static function getInstance($inSourceID, $inMovieID) {
		$oObject = new mofilmMoviePublicVoteSummary();
		$oObject->setSourceID($inSourceID);
		$oObject->setMovieID($inMovieID);
		$oObject->load();
		return $oObject;
	}
	
	/**
	 * Updates the summary table aggregating votes and scores
	 *
	 * @return void
	 */
	public static function updateSummaryTable() {
		$success = false;
		
		$query = '
			INSERT INTO '.system::getConfig()->getDatabase('moiflm_comms').'.moviePublicVoteSummary
				(sourceID, movieID, votes, score)
			
			(SELECT sourceID, movieID, COUNT(userIdentity) AS votes, AVG(score) AS score
				  FROM '.system::getConfig()->getDatabase('moiflm_comms').'.moviePublicVotes
				 GROUP BY sourceID, movieID)
			
			ON DUPLICATE KEY UPDATE
				votes = VALUES(votes),
				score = VALUES(score)';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute() ) {
			$success = true;
		}
		$oStmt->closeCursor();
		return $success;
	}
	
	/**
	 * Returns an array of objects matching the criteria
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inSourceID
	 * @param boolean $inTopVotes
	 * @param boolean $inTopScore
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = 0, $inLimit = 10, $inSourceID = null, $inTopVotes = null, $inTopScore = null) {
		$orderBy = array();
		$return = array();
		
		$query = '
			SELECT sourceID, movieID, votes, score
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.moviePublicVoteSummary';
		
		if ( $inSourceID !== null ) {
			$query .= ' WHERE sourceID = '.dbManager::getInstance()->quote($inSourceID);
		}
		if ( $inTopVotes ) {
			$orderBy[] = ' votes DESC ';
		}
		if ( $inTopScore ) {
			$orderBy[] = ' score DESC ';
		}
		
		if ( count($orderBy) > 0 ) {
			$query .= ' ORDER BY '.implode(', ', $orderBy);
		}
		
		if ( $inOffset !== null && $inLimit !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMoviePublicVoteSummary();
					$oObject->loadFromArray($row);
					$list[] = $oObject;
				}
			}
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $list;
	}
	
	/**
	 * Returns the total number of movies in the specified leaderboard
	 *
	 * @param integer $inSourceID
	 * @return integer
	 * @static
	 */
	public static function getMovieCountForSource($inSourceID) {
		$return = 0;
		$query = '
			SELECT COUNT(*) AS itemCount
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.moviePublicVoteSummary
			 WHERE sourceID = '.dbManager::getInstance()->quote($inSourceID);
		
		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute() ) {
			$return = $oStmt->fetchColumn();
		}
		$oStmt->closeCursor();
		
		return $return;
	}
	
	

	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT sourceID, movieID, votes, score
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.moviePublicVoteSummary';

		$where = array();
		if ( $this->_SourceID !== 0 ) {
			$where[] = ' sourceID = :SourceID ';
		}
		if ( $this->_MovieID !== 0 ) {
			$where[] = ' movieID = :MovieID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_SourceID !== 0 ) {
				$oStmt->bindValue(':SourceID', $this->_SourceID);
			}
			if ( $this->_MovieID !== 0 ) {
				$oStmt->bindValue(':MovieID', $this->_MovieID);
			}

			$this->reset();
			if ( $oStmt->execute() ) {
				$row = $oStmt->fetch();
				if ( $row !== false && is_array($row) ) {
					$this->loadFromArray($row);
					$oStmt->closeCursor();
					$return = true;
				}
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $return;
	}

	/**
	 * Loads a record by array
	 *
	 * @param array $inArray
	 */
	function loadFromArray($inArray) {
		$this->setSourceID((int)$inArray['sourceID']);
		$this->setMovieID((int)$inArray['movieID']);
		$this->setVotes((int)$inArray['votes']);
		$this->setScore($inArray['score']);
		$this->setModified(false);
	}

	/**
	 * Saves object to the table
	 *
	 * @return boolean
	 */
	function save() {
		$return = false;
		if ( $this->isModified() ) {
			$message = '';
			if ( !$this->isValid($message) ) {
				throw new mofilmException($message);
			}
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.moviePublicVoteSummary
					( sourceID, movieID, votes, score)
				VALUES
					(:SourceID, :MovieID, :Votes, :Score)
				ON DUPLICATE KEY UPDATE
					votes=VALUES(votes),
					score=VALUES(score)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':SourceID', $this->_SourceID);
					$oStmt->bindValue(':MovieID', $this->_MovieID);
					$oStmt->bindValue(':Votes', $this->_Votes);
					$oStmt->bindValue(':Score', $this->_Score);

					if ( $oStmt->execute() ) {
						$this->setModified(false);
						$return = true;
					}
				} catch ( Exception $e ) {
					systemLog::error($e->getMessage());
					throw $e;
				}
			}
		}
		return $return;
	}

	/**
	 * Deletes the object from the table
	 *
	 * @return boolean
	 */
	function delete() {
		$query = '
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.moviePublicVoteSummary
			WHERE
				sourceID = :SourceID AND
				movieID = :MovieID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':SourceID', $this->_SourceID);
			$oStmt->bindValue(':MovieID', $this->_MovieID);

			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				$this->reset();
				return true;
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mofilmMoviePublicVoteSummary
	 */
	function reset() {
		$this->_SourceID = 0;
		$this->_MovieID = 0;
		$this->_Votes = 0;
		$this->_Score = 0;
		$this->setModified(false);
		$this->setMarkForDeletion(false);
		return $this;
	}

	/**
	 * Returns object as a string with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toString($newLine = "\n") {
		$string  = '';
		$string .= " SourceID[$this->_SourceID] $newLine";
		$string .= " MovieID[$this->_MovieID] $newLine";
		$string .= " Votes[$this->_Votes] $newLine";
		$string .= " Score[$this->_Score] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmMoviePublicVoteSummary';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"SourceID\" value=\"$this->_SourceID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"MovieID\" value=\"$this->_MovieID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Votes\" value=\"$this->_Votes\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Score\" value=\"$this->_Score\" type=\"float\" /> $newLine";
		$xml .= "</$className>$newLine";
		return $xml;
	}

	/**
	 * Returns properties of object as an array
	 *
	 * @return array
	 */
	function toArray() {
		return get_object_vars($this);
	}



	/**
	 * Returns true if object is valid
	 *
	 * @return boolean
	 */
	function isValid(&$message = '') {
		$valid = true;
		if ( $valid ) {
			$valid = $this->checkSourceID($message);
		}
		if ( $valid ) {
			$valid = $this->checkMovieID($message);
		}
		if ( $valid ) {
			$valid = $this->checkVotes($message);
		}
		if ( $valid ) {
			$valid = $this->checkScore($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_SourceID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkSourceID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_SourceID) && $this->_SourceID !== 0 ) {
			$inMessage .= "{$this->_SourceID} is not a valid value for SourceID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_MovieID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMovieID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MovieID) && $this->_MovieID !== 0 ) {
			$inMessage .= "{$this->_MovieID} is not a valid value for MovieID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Votes has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkVotes(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Votes) && $this->_Votes !== 0 ) {
			$inMessage .= "{$this->_Votes} is not a valid value for Votes";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Score has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkScore(&$inMessage = '') {
		$isValid = true;
		if ( !is_float($this->_Score) && $this->_Score !== 0.8 ) {
			$inMessage .= "{$this->_Score} is not a valid value for Score";
			$isValid = false;
		}
		return $isValid;
	}



	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmMoviePublicVoteSummary
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the primaryKey index
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->_SourceID.'.'.$this->_MovieID;
	}

	/**
	 * Return value of $_SourceID
	 *
	 * @return integer
	 * @access public
	 */
	function getSourceID() {
		return $this->_SourceID;
	}
	
	/**
	 * Returns the source object
	 * 
	 * @return mofilmSource
	 */
	function getSource() {
		return mofilmSource::getInstance($this->getSourceID());
	}
	
	/**
	 * Set $_SourceID to SourceID
	 *
	 * @param integer $inSourceID
	 * @return mofilmMoviePublicVoteSummary
	 * @access public
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_MovieID
	 *
	 * @return integer
	 * @access public
	 */
	function getMovieID() {
		return $this->_MovieID;
	}
	
	/**
	 * Returns the movie instance
	 * 
	 * @return mofilmMovie
	 */
	function getMovie() {
		return mofilmMovieManager::getInstanceByID($this->getMovieID());
	}

	/**
	 * Set $_MovieID to MovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmMoviePublicVoteSummary
	 * @access public
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Votes
	 *
	 * @return integer
	 * @access public
	 */
	function getVotes() {
		return $this->_Votes;
	}

	/**
	 * Set $_Votes to Votes
	 *
	 * @param integer $inVotes
	 * @return mofilmMoviePublicVoteSummary
	 * @access public
	 */
	function setVotes($inVotes) {
		if ( $inVotes !== $this->_Votes ) {
			$this->_Votes = $inVotes;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Score
	 *
	 * @return float
	 * @access public
	 */
	function getScore() {
		return $this->_Score;
	}

	/**
	 * Set $_Score to Score
	 *
	 * @param float $inScore
	 * @return mofilmMoviePublicVoteSummary
	 * @access public
	 */
	function setScore($inScore) {
		if ( $inScore !== $this->_Score ) {
			$this->_Score = $inScore;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_MarkForDeletion
	 *
	 * @return boolean
	 */
	function getMarkForDeletion() {
		return $this->_MarkForDeletion;
	}

	/**
	 * Set $_MarkForDeletion to $inMarkForDeletion
	 *
	 * @param boolean $inMarkForDeletion
	 * @return mofilmMoviePublicVoteSummary
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}