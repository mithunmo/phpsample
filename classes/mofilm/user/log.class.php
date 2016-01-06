<?php
/**
 * mofilmUserLog
 * 
 * Stored in mofilmUserLog.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserLog
 * @category mofilmUserLog
 * @version $Rev: 10 $
 */


/**
 * mofilmUserLog Class
 * 
 * Provides access to records in mofilm_content.userLog
 * 
 * Creating a new record:
 * <code>
 * $oMofilmUserLog = new mofilmUserLog();
 * $oMofilmUserLog->setID($inID);
 * $oMofilmUserLog->setUserID($inUserID);
 * $oMofilmUserLog->setTimestamp($inTimestamp);
 * $oMofilmUserLog->setType($inType);
 * $oMofilmUserLog->setDescription($inDescription);
 * $oMofilmUserLog->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserLog = new mofilmUserLog($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmUserLog = new mofilmUserLog();
 * $oMofilmUserLog->setID($inID);
 * $oMofilmUserLog->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserLog = mofilmUserLog::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmUserLog
 * @category mofilmUserLog
 */
class mofilmUserLog implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmUserLog
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
	 * Stores $_Timestamp
	 * 
	 * @var datetime
	 * @access protected
	 */
	protected $_Timestamp;
			
	/**
	 * Stores $_Type
	 * 
	 * @var string (TYPE_LOGIN,TYPE_UPLOAD,TYPE_OTHER,)
	 * @access protected
	 */
	protected $_Type;
	const TYPE_LOGIN = 'Login';
	const TYPE_UPLOAD = 'Upload';
	const TYPE_OTHER = 'Other';
	const TYPE_REFER = "refer";
				
	/**
	 * Stores $_Description
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_MarkForDeletion
	 *
	 * @var boolean
	 * @access private
	 */
	private $_MarkForDeletion;
			
	
	
	/**
	 * Returns a new instance of mofilmUserLog
	 * 
	 * @param integer $inID
	 * @return mofilmUserLog
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
	 * Creates a new mofilmUserLog containing non-unique properties
	 * 
	 * @param integer $inUserID
	 * @param timestamp $inTimestamp
	 * @param string $inType
	 * @param string $inDescription
	 * @return mofilmUserLog
	 * @static 
	 */
	public static function factory($inUserID = null, $inTimestamp = null, $inType = null, $inDescription = null) {
		$oObject = new mofilmUserLog;
		if ( $inUserID !== null ) {
			$oObject->setUserID($inUserID);
		}
		if ( $inTimestamp !== null ) {
			$oObject->setTimestamp($inTimestamp);
		}
		if ( $inType !== null ) {
			$oObject->setType($inType);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmUserLog by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmUserLog
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
		$oObject = new mofilmUserLog();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmUserLog
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inUserID
	 * @param string $inLogType
	 * @param string $inDescription
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inUserID = null, $inLogType = null, $inDescription = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLog WHERE 1 ';
		if ( $inUserID !== null && is_numeric($inUserID) && $inUserID > 0 ) {
			$query .= ' AND userID = '.dbManager::getInstance()->quote($inUserID);
		}
		if ( $inLogType !== null && in_array($inLogType, array(self::TYPE_LOGIN, self::TYPE_OTHER, self::TYPE_UPLOAD)) ) {
			$query .= ' AND type = '.dbManager::getInstance()->quote($inLogType);
		}
		if ( $inDescription !== null && strlen($inDescription) > 1 ) {
			$query .= ' AND description LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ', '%', $inDescription).'%');
		}
		$query .= ' ORDER BY timestamp DESC ';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserLog();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLog';
		
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
		$this->setTimestamp($inArray['timestamp']);
		$this->setType($inArray['type']);
		$this->setDescription($inArray['description']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userLog
					( ID, userID, timestamp, type, description)
				VALUES 
					(:ID, :UserID, :Timestamp, :Type, :Description)
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					timestamp=VALUES(timestamp),
					type=VALUES(type),
					description=VALUES(description)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':Timestamp', $this->_Timestamp);
					$oStmt->bindValue(':Type', $this->_Type);
					$oStmt->bindValue(':Description', $this->_Description);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLog
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
	 * @return mofilmUserLog
	 */
	function reset() {
		$this->_ID = 0;
		$this->_UserID = 0;
		$this->_Timestamp = date(system::getConfig()->getDatabaseDatetimeFormat());
		$this->_Type = '';
		$this->_Description = null;
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
		$string .= " Timestamp[$this->_Timestamp] $newLine";
		$string .= " Type[$this->_Type] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmUserLog';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Timestamp\" value=\"$this->_Timestamp\" type=\"timestamp\" /> $newLine";
		$xml .= "\t<property name=\"Type\" value=\"$this->_Type\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
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
			$valid = $this->checkTimestamp($message);
		}
		if ( $valid ) {
			$valid = $this->checkType($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
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
	 * Checks that $_Timestamp has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkTimestamp(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Timestamp) && $this->_Timestamp !== '' ) {
			$inMessage .= "{$this->_Timestamp} is not a valid value for Timestamp";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Type has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkType(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Type) && $this->_Type !== '' ) {
			$inMessage .= "{$this->_Type} is not a valid value for Type";
			$isValid = false;
		}		
		if ( $isValid && $this->_Type != '' && !in_array($this->_Type, array(self::TYPE_LOGIN,self::TYPE_REFER, self::TYPE_UPLOAD, self::TYPE_OTHER)) ) {
			$inMessage .= "Type must be one of TYPE_LOGIN, TYPE_UPLOAD, TYPE_OTHER";
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
	 * @return mofilmUserLog
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
	 * @return mofilmUserLog
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
	 * @return mofilmUserLog
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
	 * Return value of $_Timestamp
	 * 
	 * @return datetime
	 * @access public
	 */
	function getTimestamp() {
		return $this->_Timestamp;
	}
	
	/**
	 * Set $_Timestamp to Timestamp
	 * 
	 * @param datetime $inTimestamp
	 * @return mofilmUserLog
	 * @access public
	 */
	function setTimestamp($inTimestamp) {
		if ( $inTimestamp !== $this->_Timestamp ) {
			$this->_Timestamp = $inTimestamp;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Type
	 * 
	 * @return string
	 * @access public
	 */
	function getType() {
		return $this->_Type;
	}
	
	/**
	 * Set $_Type to Type
	 * 
	 * @param string $inType
	 * @return mofilmUserLog
	 * @access public
	 */
	function setType($inType) {
		if ( $inType !== $this->_Type ) {
			$this->_Type = $inType;
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
	 * @return mofilmUserLog
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
	 * @return mofilmUserLog
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}