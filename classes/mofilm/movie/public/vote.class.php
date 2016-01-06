<?php
/**
 * mofilmMoviePublicVote
 *
 * Stored in mofilmMoviePublicVote.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMoviePublicVote
 * @category mofilmMoviePublicVote
 * @version $Rev: 10 $
 */


/**
 * mofilmMoviePublicVote Class
 *
 * Provides access to records in mofilm_comms.moviePublicVotes
 *
 * Creating a new record:
 * <code>
 * $oMofilmMoviePublicVote = new mofilmMoviePublicVote();
 * $oMofilmMoviePublicVote->setSourceID($inSourceID);
 * $oMofilmMoviePublicVote->setMovieID($inMovieID);
 * $oMofilmMoviePublicVote->setUserIdentity($inUserIdentity);
 * $oMofilmMoviePublicVote->setDate($inDate);
 * $oMofilmMoviePublicVote->setScore($inScore);
 * $oMofilmMoviePublicVote->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMoviePublicVote = new mofilmMoviePublicVote($inSourceID, $inMovieID, $inUserIdentity);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmMoviePublicVote = new mofilmMoviePublicVote();
 * $oMofilmMoviePublicVote->setSourceID($inSourceID);
 * $oMofilmMoviePublicVote->setMovieID($inMovieID);
 * $oMofilmMoviePublicVote->setUserIdentity($inUserIdentity);
 * $oMofilmMoviePublicVote->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmMoviePublicVote = mofilmMoviePublicVote::getInstance($inSourceID, $inMovieID, $inUserIdentity);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmMoviePublicVote
 * @category mofilmMoviePublicVote
 */
class mofilmMoviePublicVote implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmMoviePublicVote
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
	 * Stores $_UserIdentity
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_UserIdentity;

	/**
	 * Stores $_Date
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Date;

	/**
	 * Stores $_Score
	 *
	 * @var integer 
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
	 * Returns a new instance of mofilmMoviePublicVote
	 *
	 * @param integer $inSourceID
	 * @param integer $inMovieID
	 * @param string $inUserIdentity
	 * @return mofilmMoviePublicVote
	 */
	function __construct($inSourceID = null, $inMovieID = null, $inUserIdentity = null) {
		$this->reset();
		if ( $inSourceID !== null && $inMovieID !== null && $inUserIdentity !== null ) {
			$this->setSourceID($inSourceID);
			$this->setMovieID($inMovieID);
			$this->setUserIdentity($inUserIdentity);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new mofilmMoviePublicVote containing non-unique properties
	 *
	 * @param string $inDate
	 * @param integer $inScore
	 * @return mofilmMoviePublicVote
	 * @static
	 */
	public static function factory($inDate = null, $inScore = null) {
		$oObject = new mofilmMoviePublicVote;
		if ( $inDate !== null ) {
			$oObject->setDate($inDate);
		}
		if ( $inScore !== null ) {
			$oObject->setScore($inScore);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmMoviePublicVote by primary key
	 *
	 * @param integer $inSourceID
	 * @param integer $inMovieID
	 * @param string $inUserIdentity
	 * @return mofilmMoviePublicVote
	 * @static
	 */
	public static function getInstance($inSourceID, $inMovieID, $inUserIdentity) {
		$oObject = new mofilmMoviePublicVote();
		$oObject->setSourceID($inSourceID);
		$oObject->setMovieID($inMovieID);
		$oObject->setUserIdentity($inUserIdentity);
		$oObject->load();
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmMoviePublicVote
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_comms').'.moviePublicVotes';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMoviePublicVote();
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
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT sourceID, movieID, userIdentity, date, score
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.moviePublicVotes';

		$where = array();
		if ( $this->_SourceID !== 0 ) {
			$where[] = ' sourceID = :SourceID ';
		}
		if ( $this->_MovieID !== 0 ) {
			$where[] = ' movieID = :MovieID ';
		}
		if ( $this->_UserIdentity !== '' ) {
			$where[] = ' userIdentity = :UserIdentity ';
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
			if ( $this->_UserIdentity !== '' ) {
				$oStmt->bindValue(':UserIdentity', $this->_UserIdentity);
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
		$this->setUserIdentity($inArray['userIdentity']);
		$this->setDate($inArray['date']);
		$this->setScore((int)$inArray['score']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.moviePublicVotes
					( sourceID, movieID, userIdentity, date, score)
				VALUES
					(:SourceID, :MovieID, :UserIdentity, :Date, :Score)
				ON DUPLICATE KEY UPDATE
					date=VALUES(date),
					score=VALUES(score)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':SourceID', $this->_SourceID);
					$oStmt->bindValue(':MovieID', $this->_MovieID);
					$oStmt->bindValue(':UserIdentity', $this->_UserIdentity);
					$oStmt->bindValue(':Date', $this->_Date);
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.moviePublicVotes
			WHERE
				sourceID = :SourceID AND
				movieID = :MovieID AND
				userIdentity = :UserIdentity
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':SourceID', $this->_SourceID);
			$oStmt->bindValue(':MovieID', $this->_MovieID);
			$oStmt->bindValue(':UserIdentity', $this->_UserIdentity);

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
	 * @return mofilmMoviePublicVote
	 */
	function reset() {
		$this->_SourceID = 0;
		$this->_MovieID = 0;
		$this->_UserIdentity = '';
		$this->_Date = date(system::getConfig()->getDatabaseDateFormat()->getParamValue());
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
		$string .= " UserIdentity[$this->_UserIdentity] $newLine";
		$string .= " Date[$this->_Date] $newLine";
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
		$className = 'mofilmMoviePublicVote';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"SourceID\" value=\"$this->_SourceID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"MovieID\" value=\"$this->_MovieID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserIdentity\" value=\"$this->_UserIdentity\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Date\" value=\"$this->_Date\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Score\" value=\"$this->_Score\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkUserIdentity($message);
		}
		if ( $valid ) {
			$valid = $this->checkDate($message);
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
	 * Checks that $_UserIdentity has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkUserIdentity(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_UserIdentity) && $this->_UserIdentity !== '' ) {
			$inMessage .= "{$this->_UserIdentity} is not a valid value for UserIdentity";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_UserIdentity) > 255 ) {
			$inMessage .= "UserIdentity cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_UserIdentity) <= 1 ) {
			$inMessage .= "UserIdentity must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Date has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Date) && $this->_Date !== '' ) {
			$inMessage .= "{$this->_Date} is not a valid value for Date";
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
		if ( !is_numeric($this->_Score) && $this->_Score !== 0 ) {
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
	 * @return mofilmMoviePublicVote
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
		return $this->_SourceID.'.'.$this->_MovieID.'.'.$this->_UserIdentity;
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
	 * Set $_SourceID to SourceID
	 *
	 * @param integer $inSourceID
	 * @return mofilmMoviePublicVote
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
	 * Set $_MovieID to MovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmMoviePublicVote
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
	 * Return value of $_UserIdentity
	 *
	 * @return string
	 * @access public
	 */
	function getUserIdentity() {
		return $this->_UserIdentity;
	}

	/**
	 * Set $_UserIdentity to UserIdentity
	 *
	 * @param string $inUserIdentity
	 * @return mofilmMoviePublicVote
	 * @access public
	 */
	function setUserIdentity($inUserIdentity) {
		if ( $inUserIdentity !== $this->_UserIdentity ) {
			$this->_UserIdentity = $inUserIdentity;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Date
	 *
	 * @return string
	 * @access public
	 */
	function getDate() {
		return $this->_Date;
	}

	/**
	 * Set $_Date to Date
	 *
	 * @param string $inDate
	 * @return mofilmMoviePublicVote
	 * @access public
	 */
	function setDate($inDate) {
		if ( $inDate !== $this->_Date ) {
			$this->_Date = $inDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Score
	 *
	 * @return integer
	 * @access public
	 */
	function getScore() {
		return $this->_Score;
	}

	/**
	 * Set $_Score to Score
	 *
	 * @param integer $inScore
	 * @return mofilmMoviePublicVote
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
	 * @return mofilmMoviePublicVote
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}