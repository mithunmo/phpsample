<?php
/**
 * mofilmBulkJob
 * 
 * Stored in mofilmBulkJob.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmBulkJob
 * @category mofilmBulkJob
 * @version $Rev: 10 $
 */


/**
 * mofilmBulkJob Class
 * 
 * Provides access to records in mofilm_content.bulkJobs
 * 
 * Creating a new record:
 * <code>
 * $oMofilmBulkJob = new mofilmBulkJob();
 * $oMofilmBulkJob->setID($inID);
 * $oMofilmBulkJob->setUserID($inUserID);
 * $oMofilmBulkJob->setDatetime($inDatetime);
 * $oMofilmBulkJob->setCount($inCount);
 * $oMofilmBulkJob->setDescription($inDescription);
 * $oMofilmBulkJob->setStatus($inStatus);
 * $oMofilmBulkJob->setFilename($inFilename);
 * $oMofilmBulkJob->setSystemResult($inSystemResult);
 * $oMofilmBulkJob->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmBulkJob = new mofilmBulkJob($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmBulkJob = new mofilmBulkJob();
 * $oMofilmBulkJob->setID($inID);
 * $oMofilmBulkJob->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmBulkJob = mofilmBulkJob::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmBulkJob
 * @category mofilmBulkJob
 */
class mofilmBulkJob implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmBulkJob
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
	 * Stores $_ID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_ID;
			
	/**
	 * Stores $_UserID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;
			
	/**
	 * Stores $_Datetime
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_Datetime;
			
	/**
	 * Stores $_Count
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_Count;
			
	/**
	 * Stores $_Description
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Description;
			
	/**
	 * Stores $_Status
	 * 
	 * @var string (STATUS_QUEUED,STATUS_PROCESSING,STATUS_FAILED,STATUS_ABANDONED,STATUS_SUCCESS,)
	 * @access protected
	 */
	protected $_Status;
	const STATUS_QUEUED = 'Queued';
	const STATUS_PROCESSING = 'Processing';
	const STATUS_FAILED = 'Failed';
	const STATUS_ABANDONED = 'Abandoned';
	const STATUS_SUCCESS = 'Success';
				
	/**
	 * Stores $_Filename
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Filename;
			
	/**
	 * Stores $_SystemResult
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_SystemResult;
			
	
	
	/**
	 * Returns a new instance of mofilmBulkJob
	 * 
	 * @param integer $inID
	 * @return mofilmBulkJob
	 */
	function __construct($inID = null) {
		$this->reset();
		if ( $inID !== null ) {
			$this->setID($inID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new mofilmBulkJob containing non-unique properties
	 * 
	 * @param integer $inUserID
	 * @param datetime $inDatetime
	 * @param integer $inCount
	 * @param string $inDescription
	 * @param string $inStatus
	 * @param string $inFilename
	 * @param string $inSystemResult
	 * @return mofilmBulkJob
	 * @static 
	 */
	public static function factory($inUserID = null, $inDatetime = null, $inCount = null, $inDescription = null, $inStatus = null, $inFilename = null, $inSystemResult = null) {
		$oObject = new mofilmBulkJob;
		if ( $inUserID !== null ) {
			$oObject->setUserID($inUserID);
		}
		if ( $inDatetime !== null ) {
			$oObject->setDatetime($inDatetime);
		}
		if ( $inCount !== null ) {
			$oObject->setCount($inCount);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inStatus !== null ) {
			$oObject->setStatus($inStatus);
		}
		if ( $inFilename !== null ) {
			$oObject->setFilename($inFilename);
		}
		if ( $inSystemResult !== null ) {
			$oObject->setSystemResult($inSystemResult);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmBulkJob by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmBulkJob
	 * @static 
	 */
	public static function getInstance($inID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inID]) ) {
			return self::$_Instances[$inID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmBulkJob();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmBulkJob
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.bulkJobs';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmBulkJob();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.bulkJobs';
		
		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
						
		if ( count($where) == 0 ) {
			return false;
		}
		
		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_ID !== 0 ) {
				$oStmt->bindValue(':ID', $this->_ID);
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
		$this->setID((int)$inArray['ID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setDatetime($inArray['datetime']);
		$this->setCount((int)$inArray['count']);
		$this->setDescription($inArray['description']);
		$this->setStatus($inArray['status']);
		$this->setFilename($inArray['filename']);
		$this->setSystemResult($inArray['systemResult']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.bulkJobs
					( ID, userID, datetime, count, description, status, filename, systemResult)
				VALUES 
					(:ID, :UserID, :Datetime, :Count, :Description, :Status, :Filename, :SystemResult)
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					datetime=VALUES(datetime),
					count=VALUES(count),
					description=VALUES(description),
					status=VALUES(status),
					filename=VALUES(filename),
					systemResult=VALUES(systemResult)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':Datetime', $this->_Datetime);
					$oStmt->bindValue(':Count', $this->_Count);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':Status', $this->_Status);
					$oStmt->bindValue(':Filename', $this->_Filename);
					$oStmt->bindValue(':SystemResult', $this->_SystemResult);
								
					if ( $oStmt->execute() ) {
						if ( !$this->getID() ) {
							$this->setID($oDB->lastInsertId());
						}
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.bulkJobs
		WHERE
			ID = :ID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ID', $this->_ID);
				
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
	 * @return mofilmBulkJob
	 */
	function reset() {
		$this->_ID = 0;
		$this->_UserID = 0;
		$this->_Datetime = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Count = 0;
		$this->_Description = null;
		$this->_Status = 'Queued';
		$this->_Filename = '';
		$this->_SystemResult = null;
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
		$string .= " ID[$this->_ID] $newLine";
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " Datetime[$this->_Datetime] $newLine";
		$string .= " Count[$this->_Count] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " Status[$this->_Status] $newLine";
		$string .= " Filename[$this->_Filename] $newLine";
		$string .= " SystemResult[$this->_SystemResult] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmBulkJob';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Datetime\" value=\"$this->_Datetime\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Count\" value=\"$this->_Count\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Status\" value=\"$this->_Status\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Filename\" value=\"$this->_Filename\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"SystemResult\" value=\"$this->_SystemResult\" type=\"string\" /> $newLine";
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
			$valid = $this->checkID($message);
		}
		if ( $valid ) {
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDatetime($message);
		}
		if ( $valid ) {
			$valid = $this->checkCount($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkStatus($message);
		}
		if ( $valid ) {
			$valid = $this->checkFilename($message);
		}
		if ( $valid ) {
			$valid = $this->checkSystemResult($message);
		}
		return $valid;
	}
		
	/**
	 * Checks that $_ID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ID) && $this->_ID !== 0 ) {
			$inMessage .= "{$this->_ID} is not a valid value for ID";
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
	 * Checks that $_Datetime has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkDatetime(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Datetime) && $this->_Datetime !== '' ) {
			$inMessage .= "{$this->_Datetime} is not a valid value for Datetime";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Count has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkCount(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Count) && $this->_Count !== 0 ) {
			$inMessage .= "{$this->_Count} is not a valid value for Count";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Description has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkDescription(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Description) && $this->_Description !== null && $this->_Description !== '' ) {
			$inMessage .= "{$this->_Description} is not a valid value for Description";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_Description) > 40 ) {
			$inMessage .= "Description cannot be more than 40 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Status has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkStatus(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Status) && $this->_Status !== '' ) {
			$inMessage .= "{$this->_Status} is not a valid value for Status";
			$isValid = false;
		}		
		if ( $isValid && $this->_Status != '' && !in_array($this->_Status, array(self::STATUS_QUEUED, self::STATUS_PROCESSING, self::STATUS_FAILED, self::STATUS_ABANDONED, self::STATUS_SUCCESS)) ) {
			$inMessage .= "Status must be one of STATUS_QUEUED, STATUS_PROCESSING, STATUS_FAILED, STATUS_ABANDONED, STATUS_SUCCESS";
			$isValid = false;
		}		
		return $isValid;
	}
		
	/**
	 * Checks that $_Filename has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkFilename(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Filename) && $this->_Filename !== '' ) {
			$inMessage .= "{$this->_Filename} is not a valid value for Filename";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_SystemResult has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkSystemResult(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_SystemResult) && $this->_SystemResult !== null && $this->_SystemResult !== '' ) {
			$inMessage .= "{$this->_SystemResult} is not a valid value for SystemResult";
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
	 * @return mofilmBulkJob
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
		return $this->_ID;
	}
		
	/**
	 * Return value of $_ID
	 * 
	 * @return integer
	 * @access public
	 */
	function getID() {
		return $this->_ID;
	}
	
	/**
	 * Set $_ID to ID
	 * 
	 * @param integer $inID
	 * @return mofilmBulkJob
	 * @access public
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
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
	 * @return mofilmBulkJob
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
	 * Return value of $_Datetime
	 * 
	 * @return datetime
	 * @access public
	 */
	function getDatetime() {
		return $this->_Datetime;
	}
	
	/**
	 * Set $_Datetime to Datetime
	 * 
	 * @param datetime $inDatetime
	 * @return mofilmBulkJob
	 * @access public
	 */
	function setDatetime($inDatetime) {
		if ( $inDatetime !== $this->_Datetime ) {
			$this->_Datetime = $inDatetime;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Count
	 * 
	 * @return integer
	 * @access public
	 */
	function getCount() {
		return $this->_Count;
	}
	
	/**
	 * Set $_Count to Count
	 * 
	 * @param integer $inCount
	 * @return mofilmBulkJob
	 * @access public
	 */
	function setCount($inCount) {
		if ( $inCount !== $this->_Count ) {
			$this->_Count = $inCount;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Description
	 * 
	 * @return string
	 * @access public
	 */
	function getDescription() {
		return $this->_Description;
	}
	
	/**
	 * Set $_Description to Description
	 * 
	 * @param string $inDescription
	 * @return mofilmBulkJob
	 * @access public
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Status
	 * 
	 * @return string
	 * @access public
	 */
	function getStatus() {
		return $this->_Status;
	}
	
	/**
	 * Set $_Status to Status
	 * 
	 * @param string $inStatus
	 * @return mofilmBulkJob
	 * @access public
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Filename
	 * 
	 * @return string
	 * @access public
	 */
	function getFilename() {
		return $this->_Filename;
	}
	
	/**
	 * Set $_Filename to Filename
	 * 
	 * @param string $inFilename
	 * @return mofilmBulkJob
	 * @access public
	 */
	function setFilename($inFilename) {
		if ( $inFilename !== $this->_Filename ) {
			$this->_Filename = $inFilename;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_SystemResult
	 * 
	 * @return string
	 * @access public
	 */
	function getSystemResult() {
		return $this->_SystemResult;
	}
	
	/**
	 * Set $_SystemResult to SystemResult
	 * 
	 * @param string $inSystemResult
	 * @return mofilmBulkJob
	 * @access public
	 */
	function setSystemResult($inSystemResult) {
		if ( $inSystemResult !== $this->_SystemResult ) {
			$this->_SystemResult = $inSystemResult;
			$this->setModified();
		}
		return $this;
	}
}