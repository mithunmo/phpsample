<?php
/**
 * mofilmSourceBudgetLog
 *
 * Stored in mofilmSourceBudgetLog.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSourceBudgetLog
 * @category mofilmSourceBudgetLog
 * @version $Rev: 840 $
 */


/**
 * mofilmSourceBudgetLog Class
 *
 * Provides access to records in mofilm_content.sourceBudgetLog
 *
 * Creating a new record:
 * <code>
 * $oMofilmSourceBudgetLog = new mofilmSourceBudgetLog();
 * $oMofilmSourceBudgetLog->setID($inID);
 * $oMofilmSourceBudgetLog->setSrcID($inSrcID);
 * $oMofilmSourceBudgetLog->setChangeLog($inChangeLog);
 * $oMofilmSourceBudgetLog->setModifiedTime($inModifiedTime);
 * $oMofilmSourceBudgetLog->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmSourceBudgetLog = new mofilmSourceBudgetLog($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmSourceBudgetLog = new mofilmSourceBudgetLog();
 * $oMofilmSourceBudgetLog->setID($inID);
 * $oMofilmSourceBudgetLog->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmSourceBudgetLog = mofilmSourceBudgetLog::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmSourceBudgetLog
 * @category mofilmSourceBudgetLog
 */
class mofilmSourceBudgetLog implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmSourceBudgetLog
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
	 * Stores the validator for this object
	 *
	 * @var utilityValidator
	 * @access protected
	 */
	protected $_Validator;

	/**
	 * Stores $_ID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ID;

	/**
	 * Stores $_SrcID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SrcID;

	/**
	 * Stores $_ChangeLog
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ChangeLog;

	/**
	 * Stores $_ModifiedTime
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_ModifiedTime;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmSourceBudgetLog
	 *
	 * @param integer $inID
	 * @return mofilmSourceBudgetLog
	 */
	function __construct($inID = null) {
		$this->reset();
		if ( $inID !== null ) {
			$this->setID($inID);
			$this->load();
		}
	}

	/**
	 * Object destructor, used to remove internal object instances
	 *
	 * @return void
 	 */
	function __destruct() {
		if ( $this->_Validator instanceof utilityValidator ) {
			$this->_Validator = null;
		}
	}

	/**
	 * Get an instance of mofilmSourceBudgetLog by primary key
	 *
	 * @param integer $inID
	 * @return mofilmSourceBudgetLog
	 * @static
	 */
	public static function getInstance($inID) {
		$key = $inID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmSourceBudgetLog();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmSourceBudgetLog
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, srcID, changeLog, modifiedTime
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourceBudgetLog
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmSourceBudgetLog();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}



	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$values = array();

		$query = '
			SELECT ID, srcID, changeLog, modifiedTime
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourceBudgetLog';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);

		$this->reset();
		if ( $oStmt->execute($values) ) {
			$row = $oStmt->fetch();
			if ( $row !== false && is_array($row) ) {
				$this->loadFromArray($row);
				$oStmt->closeCursor();
				$return = true;
			}
		}

		return $return;
	}

	/**
	 * Loads a record by array
	 *
	 * @param array $inArray
	 * @return void
 	 */
	function loadFromArray(array $inArray) {
		$this->setID((int)$inArray['ID']);
		$this->setSrcID((int)$inArray['srcID']);
		$this->setChangeLog($inArray['changeLog']);
		$this->setModifiedTime($inArray['modifiedTime']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.sourceBudgetLog
					( ID, srcID, changeLog, modifiedTime )
				VALUES
					( :ID, :SrcID, :ChangeLog, :ModifiedTime )
				ON DUPLICATE KEY UPDATE
					srcID=VALUES(srcID),
					changeLog=VALUES(changeLog),
					modifiedTime=VALUES(modifiedTime)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':SrcID', $this->getSrcID());
				$oStmt->bindValue(':ChangeLog', $this->getChangeLog());
				$oStmt->bindValue(':ModifiedTime', $this->getModifiedTime());

				if ( $oStmt->execute() ) {
					if ( !$this->getID() ) {
						$this->setID($oDB->lastInsertId());
					}
					$this->setModified(false);
					$return = true;
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourceBudgetLog
			WHERE
				ID = :ID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':ID', $this->getID());

		if ( $oStmt->execute() ) {
			$oStmt->closeCursor();
			$this->reset();
			return true;
		}

		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mofilmSourceBudgetLog
	 */
	function reset() {
		$this->_ID = 0;
		$this->_SrcID = 0;
		$this->_ChangeLog = '';
		$this->_ModifiedTime = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Validator = null;
		$this->setModified(false);
		$this->setMarkForDeletion(false);
		return $this;
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
	 * Returns the validator, creating one if not set
	 *
	 * @return utilityValidator
	 */
	function getValidator() {
		if ( !$this->_Validator instanceof utilityValidator ) {
			$this->_Validator = new utilityValidator();
		}
		return $this->_Validator;
	}

	/**
	 * Set a pre-built validator instance
	 *
	 * @param utilityValidator $inValidator
	 * @return mofilmSourceBudgetLog
	 */
	function setValidator(utilityValidator $inValidator) {
		$this->_Validator = $inValidator;
		return $this;
	}

	/**
	 * Returns true if object is valid, any errors are added to $inMessage
	 *
	 * @param string $inMessage
	 * @return boolean
	 */
	function isValid(&$inMessage = '') {
		$valid = true;

		$oValidator = $this->getValidator();
		$oValidator->reset();
		$oValidator->setData($this->toArray())->setRules($this->getValidationRules());
		if ( !$oValidator->isValid() ) {
			foreach ( $oValidator->getMessages() as $key => $messages ) {
				$inMessage .= "Error with $key: ".implode(', ', $messages)."\n";
			}
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Returns the array of rules used to validate this object
	 *
	 * @return array
 	 */
	function getValidationRules() {
		return array(
			'_ID' => array(
				'number' => array(),
			),
			'_SrcID' => array(
				'number' => array(),
			),
			'_ChangeLog' => array(
				'string' => array(),
			),
			'_ModifiedTime' => array(
				'dateTime' => array(),
			),
		);
	}



	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmSourceBudgetLog
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the primaryKey
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->_ID;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * mofilmSourceBudgetLog::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmSourceBudgetLog
  	 */
	function setPrimaryKey($inKey) {
		list($ID) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setID($ID);
	}

	/**
	 * Return the current value of the property $_ID
	 *
	 * @return integer
 	 */
	function getID() {
		return $this->_ID;
	}

	/**
	 * Set the object property _ID to $inID
	 *
	 * @param integer $inID
	 * @return mofilmSourceBudgetLog
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SrcID
	 *
	 * @return integer
 	 */
	function getSrcID() {
		return $this->_SrcID;
	}

	/**
	 * Set the object property _SrcID to $inSrcID
	 *
	 * @param integer $inSrcID
	 * @return mofilmSourceBudgetLog
	 */
	function setSrcID($inSrcID) {
		if ( $inSrcID !== $this->_SrcID ) {
			$this->_SrcID = $inSrcID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ChangeLog
	 *
	 * @return string
 	 */
	function getChangeLog() {
		return $this->_ChangeLog;
	}

	/**
	 * Set the object property _ChangeLog to $inChangeLog
	 *
	 * @param string $inChangeLog
	 * @return mofilmSourceBudgetLog
	 */
	function setChangeLog($inChangeLog) {
		if ( $inChangeLog !== $this->_ChangeLog ) {
			$this->_ChangeLog = $inChangeLog;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ModifiedTime
	 *
	 * @return systemDateTime
 	 */
	function getModifiedTime() {
		return $this->_ModifiedTime;
	}

	/**
	 * Set the object property _ModifiedTime to $inModifiedTime
	 *
	 * @param systemDateTime $inModifiedTime
	 * @return mofilmSourceBudgetLog
	 */
	function setModifiedTime($inModifiedTime) {
		if ( $inModifiedTime !== $this->_ModifiedTime ) {
			if ( !$inModifiedTime instanceof DateTime ) {
				$inModifiedTime = new systemDateTime($inModifiedTime, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_ModifiedTime = $inModifiedTime;
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
	 * @return mofilmSourceBudgetLog
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}