<?php
/**
 * mofilmMotdLog
 *
 * Stored in mofilmMotdLog.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMotdLog
 * @category mofilmMotdLog
 * @version $Rev: 10 $
 */


/**
 * mofilmMotdLog Class
 *
 * Provides access to records in system.motdLog
 *
 * Creating a new record:
 * <code>
 * $oMofilmMotdLog = new mofilmMotdLog();
 * $oMofilmMotdLog->setMotdID($inMotdID);
 * $oMofilmMotdLog->setUserID($inUserID);
 * $oMofilmMotdLog->setReadDate($inReadDate);
 * $oMofilmMotdLog->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMotdLog = new mofilmMotdLog($inMotdID, $inUserID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmMotdLog = new mofilmMotdLog();
 * $oMofilmMotdLog->setMotdID($inMotdID);
 * $oMofilmMotdLog->setUserID($inUserID);
 * $oMofilmMotdLog->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmMotdLog = mofilmMotdLog::getInstance($inMotdID, $inUserID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmMotdLog
 * @category mofilmMotdLog
 */
class mofilmMotdLog implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmMotdLog
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
	 * Stores $_MotdID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MotdID;

	/**
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_ReadDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_ReadDate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmMotdLog
	 *
	 * @param integer $inMotdID
	 * @param integer $inUserID
	 * @return mofilmMotdLog
	 */
	function __construct($inMotdID = null, $inUserID = null) {
		$this->reset();
		if ( $inMotdID !== null && $inUserID !== null ) {
			$this->setMotdID($inMotdID);
			$this->setUserID($inUserID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new mofilmMotdLog containing non-unique properties
	 *
	 * @param datetime $inReadDate
	 * @return mofilmMotdLog
	 * @static
	 */
	public static function factory($inReadDate = null) {
		$oObject = new mofilmMotdLog;
		if ( $inReadDate !== null ) {
			$oObject->setReadDate($inReadDate);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmMotdLog by primary key
	 *
	 * @param integer $inMotdID
	 * @param integer $inUserID
	 * @return mofilmMotdLog
	 * @static
	 */
	public static function getInstance($inMotdID, $inUserID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMotdID.'.'.$inUserID]) ) {
			return self::$_Instances[$inMotdID.'.'.$inUserID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmMotdLog();
		$oObject->setMotdID($inMotdID);
		$oObject->setUserID($inUserID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMotdID.'.'.$inUserID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmMotdLog
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('system').'.motdLog';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMotdLog();
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
			SELECT motdID, userID, readDate
			  FROM '.system::getConfig()->getDatabase('system').'.motdLog';

		$where = array();
		if ( $this->_MotdID !== 0 ) {
			$where[] = ' motdID = :MotdID ';
		}
		if ( $this->_UserID !== 0 ) {
			$where[] = ' userID = :UserID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_MotdID !== 0 ) {
				$oStmt->bindValue(':MotdID', $this->_MotdID);
			}
			if ( $this->_UserID !== 0 ) {
				$oStmt->bindValue(':UserID', $this->_UserID);
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
		$this->setMotdID((int)$inArray['motdID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setReadDate($inArray['readDate']);
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
				INSERT INTO '.system::getConfig()->getDatabase('system').'.motdLog
					( motdID, userID, readDate)
				VALUES
					(:MotdID, :UserID, :ReadDate)
				ON DUPLICATE KEY UPDATE
					readDate=VALUES(readDate)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MotdID', $this->_MotdID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':ReadDate', $this->_ReadDate);

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
			DELETE FROM '.system::getConfig()->getDatabase('system').'.motdLog
			WHERE
				motdID = :MotdID AND
				userID = :UserID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MotdID', $this->_MotdID);
			$oStmt->bindValue(':UserID', $this->_UserID);

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
	 * @return mofilmMotdLog
	 */
	function reset() {
		$this->_MotdID = 0;
		$this->_UserID = 0;
		$this->_ReadDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
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
		$string .= " MotdID[$this->_MotdID] $newLine";
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " ReadDate[$this->_ReadDate] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmMotdLog';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"MotdID\" value=\"$this->_MotdID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ReadDate\" value=\"$this->_ReadDate\" type=\"datetime\" /> $newLine";
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
			$valid = $this->checkMotdID($message);
		}
		if ( $valid ) {
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkReadDate($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_MotdID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMotdID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MotdID) && $this->_MotdID !== 0 ) {
			$inMessage .= "{$this->_MotdID} is not a valid value for MotdID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_UserID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkUserID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_UserID) && $this->_UserID !== 0 ) {
			$inMessage .= "{$this->_UserID} is not a valid value for UserID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_ReadDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkReadDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ReadDate) && $this->_ReadDate !== '' ) {
			$inMessage .= "{$this->_ReadDate} is not a valid value for ReadDate";
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
	 * @return mofilmMotdLog
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
		return $this->_MotdID.'.'.$this->_UserID;
	}

	/**
	 * Return value of $_MotdID
	 *
	 * @return integer
	 * @access public
	 */
	function getMotdID() {
		return $this->_MotdID;
	}

	/**
	 * Set $_MotdID to MotdID
	 *
	 * @param integer $inMotdID
	 * @return mofilmMotdLog
	 * @access public
	 */
	function setMotdID($inMotdID) {
		if ( $inMotdID !== $this->_MotdID ) {
			$this->_MotdID = $inMotdID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_UserID
	 *
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set $_UserID to UserID
	 *
	 * @param integer $inUserID
	 * @return mofilmMotdLog
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
	 * Return value of $_ReadDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getReadDate() {
		return $this->_ReadDate;
	}

	/**
	 * Set $_ReadDate to ReadDate
	 *
	 * @param datetime $inReadDate
	 * @return mofilmMotdLog
	 * @access public
	 */
	function setReadDate($inReadDate) {
		if ( $inReadDate !== $this->_ReadDate ) {
			$this->_ReadDate = $inReadDate;
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
	 * @return mofilmMotdLog
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}