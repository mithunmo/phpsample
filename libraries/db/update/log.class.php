<?php
/**
 * dbUpdateLog
 * 
 * Stored in dbUpdateLog.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbUpdateLog
 * @version $Rev: 650 $
 */


/**
 * dbUpdateLog Class
 * 
 * Provides access to records in scorpio_system.dbUpdateLog. Used to log all
 * dbUpdate executions and the result of the update.
 * 
 * @todo: DR re-write to be DB agnostic or use XML format to store data.
 * 
 * Creating a new record:
 * <code>
 * $oDbUpdateLog = new dbUpdateLog();
 * $oDbUpdateLog->setDbUpdateID($inDbUpdateID);
 * $oDbUpdateLog->setUpdateType($inUpdateType);
 * $oDbUpdateLog->setUpdateCommand($inUpdateCommand);
 * $oDbUpdateLog->setUpdateResult($inUpdateResult);
 * $oDbUpdateLog->setMessages($inMessages);
 * $oDbUpdateLog->setCreateDate($inCreateDate);
 * $oDbUpdateLog->setUpdateDate($inUpdateDate);
 * $oDbUpdateLog->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oDbUpdateLog = new dbUpdateLog($inDbUpdateID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oDbUpdateLog = new dbUpdateLog();
 * $oDbUpdateLog->setDbUpdateID($inDbUpdateID);
 * $oDbUpdateLog->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oDbUpdateLog = dbUpdateLog::getInstance($inDbUpdateID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbUpdateLog
 */
class dbUpdateLog implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of dbUpdateLog
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
	 * Stores $_DbUpdateID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_DbUpdateID;
			
	/**
	 * Stores $_UpdateType
	 * 
	 * @var string (UPDATETYPE_SQL,UPDATETYPE_FUNCTION,)
	 * @access protected
	 */
	protected $_UpdateType;
	const UPDATETYPE_SQL = 'SQL';
	const UPDATETYPE_FUNCTION = 'Function';
				
	/**
	 * Stores $_UpdateCommand
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_UpdateCommand;
			
	/**
	 * Stores $_UpdateResult
	 * 
	 * @var string (UPDATERESULT_SUCCESS,UPDATERESULT_FAILURE,UPDATERESULT_TEST)
	 * @access protected
	 */
	protected $_UpdateResult;
	const UPDATERESULT_SUCCESS = 'Success';
	const UPDATERESULT_FAILURE = 'Failure';
	const UPDATERESULT_TEST = 'Test';
				
	/**
	 * Stores $_Messages
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Messages;
			
	/**
	 * Stores $_CreateDate
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_CreateDate;
			
	/**
	 * Stores $_UpdateDate
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_UpdateDate;
			
	
	
	/**
	 * Returns a new instance of dbUpdateLog
	 * 
	 * @param integer $inDbUpdateID
	 * @return dbUpdateLog
	 */
	function __construct($inDbUpdateID = null) {
		$this->reset();
		if ( $inDbUpdateID !== null ) {
			$this->setDbUpdateID($inDbUpdateID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new dbUpdateLog containing non-unique properties
	 * 
	 * @param string $inUpdateType
	 * @param string $inUpdateCommand
	 * @param string $inUpdateResult
	 * @param string $inMessages
	 * @param datetime $inCreateDate
	 * @param datetime $inUpdateDate
	 * @return dbUpdateLog
	 * @static 
	 */
	public static function factory($inUpdateType = null, $inUpdateCommand = null, $inUpdateResult = null, $inMessages = null, $inCreateDate = null, $inUpdateDate = null) {
		$oObject = new dbUpdateLog;
		if ( $inUpdateType !== null ) {
			$oObject->setUpdateType($inUpdateType);
		}
		if ( $inUpdateCommand !== null ) {
			$oObject->setUpdateCommand($inUpdateCommand);
		}
		if ( $inUpdateResult !== null ) {
			$oObject->setUpdateResult($inUpdateResult);
		}
		if ( $inMessages !== null ) {
			$oObject->setMessages($inMessages);
		}
		if ( $inCreateDate !== null ) {
			$oObject->setCreateDate($inCreateDate);
		}
		if ( $inUpdateDate !== null ) {
			$oObject->setUpdateDate($inUpdateDate);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of dbUpdateLog by primary key
	 * 
	 * @param integer $inDbUpdateID
	 * @return dbUpdateLog
	 * @static 
	 */
	public static function getInstance($inDbUpdateID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inDbUpdateID]) ) {
			return self::$_Instances[$inDbUpdateID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new dbUpdateLog();
		$oObject->setDbUpdateID($inDbUpdateID);
		if ( $oObject->load() ) {
			self::$_Instances[$inDbUpdateID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of dbUpdateLog
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('system').'.dbUpdateLog';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new dbUpdateLog();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('system').'.dbUpdateLog';
		
		$where = array();
		if ( $this->_DbUpdateID !== 0 ) {
			$where[] = ' dbUpdateID = :DbUpdateID ';
		}
						
		if ( count($where) > 0 ) {
			$query .= ' WHERE '.implode(' AND ', $where);
		}

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_DbUpdateID !== 0 ) {
				$oStmt->bindValue(':DbUpdateID', $this->_DbUpdateID);
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
		$this->setDbUpdateID((int)$inArray['dbUpdateID']);
		$this->setUpdateType($inArray['updateType']);
		$this->setUpdateCommand($inArray['updateCommand']);
		$this->setUpdateResult($inArray['updateResult']);
		$this->setMessages($inArray['messages']);
		$this->setCreateDate($inArray['createDate']);
		$this->setUpdateDate($inArray['updateDate']);
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
				throw new systemException($message);
			}
			$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));			
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('system').'.dbUpdateLog
					( dbUpdateID, updateType, updateCommand, updateResult, messages, createDate, updateDate)
				VALUES 
					(:DbUpdateID, :UpdateType, :UpdateCommand, :UpdateResult, :Messages, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					updateType=VALUES(updateType),
					updateCommand=VALUES(updateCommand),
					updateResult=VALUES(updateResult),
					messages=VALUES(messages),
					updateDate=VALUES(updateDate)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':DbUpdateID', $this->_DbUpdateID);
					$oStmt->bindValue(':UpdateType', $this->_UpdateType);
					$oStmt->bindValue(':UpdateCommand', $this->_UpdateCommand);
					$oStmt->bindValue(':UpdateResult', $this->_UpdateResult);
					$oStmt->bindValue(':Messages', $this->_Messages);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':UpdateDate', $this->_UpdateDate);
								
					if ( $oStmt->execute() ) {
						if ( !$this->getDbUpdateID() ) {
							$this->setDbUpdateID($oDB->lastInsertId());
						}
						$this->setModified(false);
						$return = true;
					}
					$oStmt->closeCursor();
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
		DELETE FROM '.system::getConfig()->getDatabase('system').'.dbUpdateLog
		WHERE
			dbUpdateID = :DbUpdateID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':DbUpdateID', $this->_DbUpdateID);
				
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
	 * @return dbUpdateLog
	 */
	function reset() {
		$this->_DbUpdateID = 0;
		$this->_UpdateType = 'SQL';
		$this->_UpdateCommand = '';
		$this->_UpdateResult = '';
		$this->_Messages = '';
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_UpdateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->setModified(false);
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
		$string .= " DbUpdateID[$this->_DbUpdateID] $newLine";
		$string .= " UpdateType[$this->_UpdateType] $newLine";
		$string .= " UpdateCommand[$this->_UpdateCommand] $newLine";
		$string .= " UpdateResult[$this->_UpdateResult] $newLine";
		$string .= " Messages[$this->_Messages] $newLine";
		$string .= " CreateDate[$this->_CreateDate] $newLine";
		$string .= " UpdateDate[$this->_UpdateDate] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'dbUpdateLog';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"DbUpdateID\" value=\"$this->_DbUpdateID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UpdateType\" value=\"$this->_UpdateType\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"UpdateCommand\" value=\"$this->_UpdateCommand\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"UpdateResult\" value=\"$this->_UpdateResult\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Messages\" value=\"$this->_Messages\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"CreateDate\" value=\"$this->_CreateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"UpdateDate\" value=\"$this->_UpdateDate\" type=\"datetime\" /> $newLine";
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
			$valid = $this->checkDbUpdateID($message);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateType($message);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateCommand($message);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateResult($message);
		}
		if ( $valid ) {
			$valid = $this->checkMessages($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateDate($message);
		}
		return $valid;
	}
		
	/**
	 * Checks that $_DbUpdateID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkDbUpdateID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_DbUpdateID) && $this->_DbUpdateID !== 0 ) {
			$inMessage .= "{$this->_DbUpdateID} is not a valid value for DbUpdateID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_UpdateType has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkUpdateType(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_UpdateType) && $this->_UpdateType !== '' ) {
			$inMessage .= "{$this->_UpdateType} is not a valid value for UpdateType";
			$isValid = false;
		}		
		if ( $isValid && $this->_UpdateType != '' && !in_array($this->_UpdateType, array(self::UPDATETYPE_SQL, self::UPDATETYPE_FUNCTION)) ) {
			$inMessage .= "UpdateType must be one of UPDATETYPE_SQL, UPDATETYPE_FUNCTION";
			$isValid = false;
		}		
		return $isValid;
	}
		
	/**
	 * Checks that $_UpdateCommand has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkUpdateCommand(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_UpdateCommand) && $this->_UpdateCommand !== '' ) {
			$inMessage .= "{$this->_UpdateCommand} is not a valid value for UpdateCommand";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_UpdateResult has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkUpdateResult(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_UpdateResult) && $this->_UpdateResult !== '' ) {
			$inMessage .= "{$this->_UpdateResult} is not a valid value for UpdateResult";
			$isValid = false;
		}		
		if ( $isValid && $this->_UpdateResult != '' && !in_array($this->_UpdateResult, array(self::UPDATERESULT_SUCCESS, self::UPDATERESULT_FAILURE, self::UPDATERESULT_TEST)) ) {
			$inMessage .= "UpdateResult must be one of UPDATERESULT_SUCCESS, UPDATERESULT_FAILURE";
			$isValid = false;
		}		
		return $isValid;
	}
		
	/**
	 * Checks that $_Messages has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkMessages(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Messages) && $this->_Messages !== '' ) {
			$inMessage .= "{$this->_Messages} is not a valid value for Messages";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_CreateDate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkCreateDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_CreateDate) && $this->_CreateDate !== '' ) {
			$inMessage .= "{$this->_CreateDate} is not a valid value for CreateDate";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_UpdateDate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkUpdateDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_UpdateDate) && $this->_UpdateDate !== '' ) {
			$inMessage .= "{$this->_UpdateDate} is not a valid value for UpdateDate";
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
	 * @return dbUpdateLog
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
		return $this->_DbUpdateID;
	}
		
	/**
	 * Return value of $_DbUpdateID
	 * 
	 * @return integer
	 * @access public
	 */
	function getDbUpdateID() {
		return $this->_DbUpdateID;
	}
	
	/**
	 * Set $_DbUpdateID to DbUpdateID
	 * 
	 * @param integer $inDbUpdateID
	 * @return dbUpdateLog
	 * @access public
	 */
	function setDbUpdateID($inDbUpdateID) {
		if ( $inDbUpdateID !== $this->_DbUpdateID ) {
			$this->_DbUpdateID = $inDbUpdateID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_UpdateType
	 * 
	 * @return string
	 * @access public
	 */
	function getUpdateType() {
		return $this->_UpdateType;
	}
	
	/**
	 * Set $_UpdateType to UpdateType
	 * 
	 * @param string $inUpdateType
	 * @return dbUpdateLog
	 * @access public
	 */
	function setUpdateType($inUpdateType) {
		if ( $inUpdateType !== $this->_UpdateType ) {
			$this->_UpdateType = $inUpdateType;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_UpdateCommand
	 * 
	 * @return string
	 * @access public
	 */
	function getUpdateCommand() {
		return $this->_UpdateCommand;
	}
	
	/**
	 * Set $_UpdateCommand to UpdateCommand
	 * 
	 * @param string $inUpdateCommand
	 * @return dbUpdateLog
	 * @access public
	 */
	function setUpdateCommand($inUpdateCommand) {
		if ( $inUpdateCommand !== $this->_UpdateCommand ) {
			$this->_UpdateCommand = $inUpdateCommand;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_UpdateResult
	 * 
	 * @return string
	 * @access public
	 */
	function getUpdateResult() {
		return $this->_UpdateResult;
	}
	
	/**
	 * Returns true if this log has an error
	 *
	 * @return boolean
	 */
	function isError() {
		return $this->getUpdateResult() == self::UPDATERESULT_FAILURE;
	}
	
	/**
	 * Set $_UpdateResult to UpdateResult
	 * 
	 * @param string $inUpdateResult
	 * @return dbUpdateLog
	 * @access public
	 */
	function setUpdateResult($inUpdateResult) {
		if ( $inUpdateResult !== $this->_UpdateResult ) {
			$this->_UpdateResult = $inUpdateResult;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Messages
	 * 
	 * @return string
	 * @access public
	 */
	function getMessages() {
		return $this->_Messages;
	}
	
	/**
	 * Adds a message to the messages entry
	 *
	 * @param string $inMessage
	 * @return dbUpdateLog
	 */
	function addMessage($inMessage) {
		if ( strlen($this->getMessages()) > 0 ) {
			$this->setMessages($this->getMessages()."\n".$inMessage);
		} else {
			$this->setMessages($inMessage);
		}
		return $this;
	}
	
	/**
	 * Set $_Messages to Messages
	 * 
	 * @param string $inMessages
	 * @return dbUpdateLog
	 * @access public
	 */
	function setMessages($inMessages) {
		if ( $inMessages !== $this->_Messages ) {
			$this->_Messages = $inMessages;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_CreateDate
	 * 
	 * @return datetime
	 * @access public
	 */
	function getCreateDate() {
		return $this->_CreateDate;
	}
	
	/**
	 * Set $_CreateDate to CreateDate
	 * 
	 * @param datetime $inCreateDate
	 * @return dbUpdateLog
	 * @access public
	 */
	function setCreateDate($inCreateDate) {
		if ( $inCreateDate !== $this->_CreateDate ) {
			$this->_CreateDate = $inCreateDate;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_UpdateDate
	 * 
	 * @return datetime
	 * @access public
	 */
	function getUpdateDate() {
		return $this->_UpdateDate;
	}
	
	/**
	 * Set $_UpdateDate to UpdateDate
	 * 
	 * @param datetime $inUpdateDate
	 * @return dbUpdateLog
	 * @access public
	 */
	function setUpdateDate($inUpdateDate) {
		if ( $inUpdateDate !== $this->_UpdateDate ) {
			$this->_UpdateDate = $inUpdateDate;
			$this->setModified();
		}
		return $this;
	}
}