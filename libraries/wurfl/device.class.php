<?php
/**
 * wurflDevice
 * 
 * Stored in wurflDevice.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflDevice
 * @version $Rev: 650 $
 */


/**
 * wurflDevice Class
 * 
 * Provides access to records in wurfl.devices
 * 
 * Creating a new record:
 * <code>
 * $oWurflDevice = new wurflDevice();
 * $oWurflDevice->setDeviceID($inDeviceID);
 * $oWurflDevice->setManufacturerID($inManufacturerID);
 * $oWurflDevice->setModelName($inModelName);
 * $oWurflDevice->setUserAgent($inUserAgent);
 * $oWurflDevice->setWurflID($inWurflID);
 * $oWurflDevice->setFallBackID($inFallBackID);
 * $oWurflDevice->setRootDevice($inRootDevice);
 * $oWurflDevice->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oWurflDevice = new wurflDevice($inDeviceID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oWurflDevice = new wurflDevice();
 * $oWurflDevice->setDeviceID($inDeviceID);
 * $oWurflDevice->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oWurflDevice = wurflDevice::getInstance($inDeviceID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflDevice
 */
class wurflDevice implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
		
	/**
	 * Stores $_DeviceID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_DeviceID;
			
	/**
	 * Stores $_ManufacturerID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_ManufacturerID;
			
	/**
	 * Stores $_ModelName
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_ModelName;
			
	/**
	 * Stores $_UserAgent
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_UserAgent;
			
	/**
	 * Stores $_WurflID
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_WurflID;
			
	/**
	 * Stores $_FallBackID
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_FallBackID;
			
	/**
	 * Stores $_RootDevice
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_RootDevice;

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
	 * Array of devices that make up the full profile
	 *
	 * @var array
	 * @access protected
	 */
	protected $_DevicePath = array();
	
	/**
	 * Stores $_Capabilities
	 *
	 * @var wurflDeviceCapabilities
	 * @access protected
	 */
	protected $_Capabilities			= false;
	
	
	
	/**
	 * Returns a new instance of wurflDevice
	 * 
	 * @param integer $inDeviceID
	 * @return wurflDevice
	 */
	function __construct($inDeviceID = null) {
		$this->reset();
		if ( $inDeviceID !== null ) {
			$this->setDeviceID($inDeviceID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new wurflDevice containing non-unique properties
	 * 
	 * @param integer $inManufacturerID
	 * @param string $inModelName
	 * @param string $inUserAgent
	 * @param string $inWurflID
	 * @param string $inFallBackID
	 * @param integer $inRootDevice
	 * @return wurflDevice
	 * @static 
	 */
	public static function factory($inManufacturerID = null, $inModelName = null, $inUserAgent = null, $inWurflID = null, $inFallBackID = null, $inRootDevice = null) {
		$oObject = new wurflDevice;
		if ( $inManufacturerID !== null ) {
			$oObject->setManufacturerID($inManufacturerID);
		}
		if ( $inModelName !== null ) {
			$oObject->setModelName($inModelName);
		}
		if ( $inUserAgent !== null ) {
			$oObject->setUserAgent($inUserAgent);
		}
		if ( $inWurflID !== null ) {
			$oObject->setWurflID($inWurflID);
		}
		if ( $inFallBackID !== null ) {
			$oObject->setFallBackID($inFallBackID);
		}
		if ( $inRootDevice !== null ) {
			$oObject->setRootDevice($inRootDevice);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of wurflDevice by primary key
	 * 
	 * @param integer $inDeviceID
	 * @return wurflDevice
	 * @static 
	 */
	public static function getInstance($inDeviceID) {
		/**
		 * No instance, create one
		 */
		$oObject = new wurflDevice();
		$oObject->setDeviceID($inDeviceID);
		if ( $oObject->load() ) {
			return $oObject;
		}
		return $oObject;
	}
	
	/**
	 * Returns an array of objects of wurflDevice
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.devices';
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new wurflDevice();
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
	 * Loads a record from the database systemd on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.devices';
		
		$where = array();
		if ( $this->_DeviceID !== 0 ) {
			$where[] = ' deviceID = :DeviceID ';
		}
						
		if ( count($where) > 0 ) {
			$query .= ' WHERE '.implode(' AND ', $where);
		}

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_DeviceID !== 0 ) {
				$oStmt->bindValue(':DeviceID', $this->_DeviceID);
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
		$this->setDeviceID((int)$inArray['deviceID']);
		$this->setManufacturerID((int)$inArray['manufacturerID']);
		$this->setModelName($inArray['modelName']);
		$this->setUserAgent($inArray['userAgent']);
		$this->setWurflID($inArray['wurflID']);
		$this->setFallBackID($inArray['fallBackID']);
		$this->setRootDevice((int)$inArray['rootDevice']);
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
				throw new wurflException($message);
			}
			$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));			
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('wurfl').'.devices
					( deviceID, manufacturerID, modelName, userAgent, wurflID, fallBackID, rootDevice, createDate, updateDate)
				VALUES 
					(:DeviceID, :ManufacturerID, :ModelName, :UserAgent, :WurflID, :FallBackID, :RootDevice, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					manufacturerID=VALUES(manufacturerID),
					modelName=VALUES(modelName),
					userAgent=VALUES(userAgent),
					wurflID=VALUES(wurflID),
					fallBackID=VALUES(fallBackID),
					rootDevice=VALUES(rootDevice),
					updateDate=VALUES(updateDate)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':DeviceID', $this->_DeviceID);
					$oStmt->bindValue(':ManufacturerID', $this->_ManufacturerID);
					$oStmt->bindValue(':ModelName', $this->_ModelName);
					$oStmt->bindValue(':UserAgent', $this->_UserAgent);
					$oStmt->bindValue(':WurflID', $this->_WurflID);
					$oStmt->bindValue(':FallBackID', $this->_FallBackID);
					$oStmt->bindValue(':RootDevice', $this->_RootDevice);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':UpdateDate', $this->_UpdateDate);
								
					if ( $oStmt->execute() ) {
						if ( !$this->getDeviceID() ) {
							$this->setDeviceID($oDB->lastInsertId());
						}
						$this->setModified(false);
						$return = true;
					}
				} catch ( Exception $e ) {
					systemLog::error($e->getMessage());
					throw $e;
				}
			}
			
			if ( $this->_Capabilities instanceof wurflDeviceCapabilities ) {
				$return = $this->_Capabilities->save();
				systemLog::info("wurflDeviceCapabilities::save() Saved $return capabilities to device");
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
		DELETE FROM '.system::getConfig()->getDatabase('wurfl').'.devices
		WHERE
			deviceID = :DeviceID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':DeviceID', $this->_DeviceID);
				
			if ( $oStmt->execute() ) {
				$res = $this->getCapabilities()->delete();
				systemLog::info("wurflDeviceCapabilities::delete() removed $res capabilities from device");
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
	 * @return wurflDevice
	 */
	function reset() {
		$this->_DeviceID = 0;
		$this->_ManufacturerID = 0;
		$this->_ModelName = '';
		$this->_UserAgent = '';
		$this->_WurflID = '';
		$this->_FallBackID = '';
		$this->_RootDevice = 0;
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat());
		$this->_UpdateDate = date(system::getConfig()->getDatabaseDatetimeFormat());
		$this->_Capabilities = false;
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
		$string .= " DeviceID[$this->_DeviceID] $newLine";
		$string .= " ManufacturerID[$this->_ManufacturerID] $newLine";
		$string .= " ModelName[$this->_ModelName] $newLine";
		$string .= " UserAgent[$this->_UserAgent] $newLine";
		$string .= " WurflID[$this->_WurflID] $newLine";
		$string .= " FallBackID[$this->_FallBackID] $newLine";
		$string .= " RootDevice[$this->_RootDevice] $newLine";
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
		$className = 'wurflDevice';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"DeviceID\" value=\"$this->_DeviceID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ManufacturerID\" value=\"$this->_ManufacturerID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ModelName\" value=\"$this->_ModelName\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"UserAgent\" value=\"$this->_UserAgent\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"WurflID\" value=\"$this->_WurflID\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"FallBackID\" value=\"$this->_FallBackID\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"RootDevice\" value=\"$this->_RootDevice\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkDeviceID($message);
		}
		if ( $valid ) {
			$valid = $this->checkUserAgent($message);
		}
		if ( $valid ) {
			$valid = $this->checkWurflID($message);
		}
		if ( $valid ) {
			$valid = $this->checkFallBackID($message);
		}
		if ( $valid ) {
			$valid = $this->checkRootDevice($message);
		}
		return $valid;
	}
		
	/**
	 * Checks that $_DeviceID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkDeviceID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_DeviceID) && $this->_DeviceID !== 0 ) {
			$inMessage .= "{$this->_DeviceID} is not a valid value for DeviceID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_UserAgent has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkUserAgent(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_UserAgent) && $this->_UserAgent !== '' ) {
			$inMessage .= "{$this->_UserAgent} is not a valid value for UserAgent";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_UserAgent) > 255 ) {
			$inMessage .= "UserAgent can not be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_UserAgent) <= 1 ) {
			$inMessage .= "UserAgent must be more than 1 character";
			$isValid = false;
		}		
		return $isValid;
	}
		
	/**
	 * Checks that $_WurflID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkWurflID(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_WurflID) && $this->_WurflID !== '' ) {
			$inMessage .= "{$this->_WurflID} is not a valid value for WurflID";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_WurflID) > 255 ) {
			$inMessage .= "WurflID cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_WurflID) <= 1 ) {
			$inMessage .= "WurflID must be more than 1 character";
			$isValid = false;
		}
		if ( !$this->getDeviceID() ) {
			$query = 'SELECT deviceID FROM '.system::getConfig()->getDatabase('wurfl').'.devices WHERE wurflID = :WurflID LIMIT 1';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':WurflID', $this->_WurflID);
			if ( $oStmt->execute() ) {
				$row = $oStmt->fetch();
				if ( $row !== false || (is_array($row) && isset($row['deviceID']) && $row['deviceID'] > 0) ) {
					$inMessage .= "The supplied WURFL ID ($this->_WurflID) is already in use by deviceID ({$row['DeviceID']})";
					$isValid = false;
				}
			}
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_FallBackID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkFallBackID(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_FallBackID) && $this->_FallBackID !== '' ) {
			$inMessage .= "{$this->_FallBackID} is not a valid value for FallBackID";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_FallBackID) > 255 ) {
			$inMessage .= "FallBackID cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_FallBackID) <= 1 ) {
			$inMessage .= "FallBackID must be more than 1 character";
			$isValid = false;
		}		
		return $isValid;
	}
		
	/**
	 * Checks that $_RootDevice has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkRootDevice(&$inMessage = '') {
		$isValid = true;
		if ( $this->_RootDevice > 1 || $this->_RootDevice < 0 ) {
			$inMessage .= "RootDevice can only be 1 or 0";
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
		$modified = $this->_Modified;
		if ( !$modified && $this->_Capabilities instanceof wurflDeviceCapabilities ) {
			$modified = $this->_Capabilities->isModified();
		}
		return $modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return wurflDevice
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns the primary key value
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->_DeviceID;
	}
	
	/**
	 * Return value of $_DeviceID
	 * 
	 * @return integer
	 * @access public
	 */
	function getDeviceID() {
		return $this->_DeviceID;
	}
	
	/**
	 * Set $_DeviceID to DeviceID
	 * 
	 * @param integer $inDeviceID
	 * @return wurflDevice
	 * @access public
	 */
	function setDeviceID($inDeviceID) {
		if ( $inDeviceID !== $this->_DeviceID ) {
			$this->_DeviceID = $inDeviceID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_ManufacturerID
	 * 
	 * @return integer
	 * @access public
	 */
	function getManufacturerID() {
		return $this->_ManufacturerID;
	}
	
	/**
	 * Returns the wurflManufacturer object
	 *
	 * @return wurflManufacturer
	 */
	function getManufacturer() {
		return wurflManufacturer::getInstance($this->_ManufacturerID);
	}
	
	/**
	 * Set $_ManufacturerID to ManufacturerID
	 * 
	 * @param integer $inManufacturerID
	 * @return wurflDevice
	 * @access public
	 */
	function setManufacturerID($inManufacturerID) {
		if ( $inManufacturerID !== $this->_ManufacturerID ) {
			$this->_ManufacturerID = $inManufacturerID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_ModelName
	 * 
	 * @return string
	 * @access public
	 */
	function getModelName() {
		return $this->_ModelName;
	}
	
	/**
	 * Set $_ModelName to ModelName
	 * 
	 * @param string $inModelName
	 * @return wurflDevice
	 * @access public
	 */
	function setModelName($inModelName) {
		if ( $inModelName !== $this->_ModelName ) {
			$this->_ModelName = $inModelName;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_UserAgent
	 * 
	 * @return string
	 * @access public
	 */
	function getUserAgent() {
		return $this->_UserAgent;
	}
	
	/**
	 * Set $_UserAgent to UserAgent
	 * 
	 * @param string $inUserAgent
	 * @return wurflDevice
	 * @access public
	 */
	function setUserAgent($inUserAgent) {
		if ( $inUserAgent !== $this->_UserAgent ) {
			$this->_UserAgent = $inUserAgent;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_WurflID
	 * 
	 * @return string
	 * @access public
	 */
	function getWurflID() {
		return $this->_WurflID;
	}
	
	/**
	 * Set $_WurflID to WurflID
	 * 
	 * @param string $inWurflID
	 * @return wurflDevice
	 * @access public
	 */
	function setWurflID($inWurflID) {
		if ( $inWurflID !== $this->_WurflID ) {
			$this->_WurflID = $inWurflID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_FallBackID
	 * 
	 * @return string
	 * @access public
	 */
	function getFallBackID() {
		return $this->_FallBackID;
	}
	
	/**
	 * Set $_FallBackID to FallBackID
	 * 
	 * @param string $inFallBackID
	 * @return wurflDevice
	 * @access public
	 */
	function setFallBackID($inFallBackID) {
		if ( $inFallBackID !== $this->_FallBackID ) {
			$this->_FallBackID = $inFallBackID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_RootDevice
	 * 
	 * @return integer
	 * @access public
	 */
	function getRootDevice() {
		return $this->_RootDevice;
	}
	
	/**
	 * Set $_RootDevice to RootDevice
	 * 
	 * @param integer $inRootDevice
	 * @return wurflDevice
	 * @access public
	 */
	function setRootDevice($inRootDevice) {
		if ( $inRootDevice !== $this->_RootDevice ) {
			$this->_RootDevice = $inRootDevice;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns CreateDate
	 *
	 * @return datetime
	 */
	function getCreateDate() {
		return $this->_CreateDate;
	}
	
	/**
	 * Set CreateDate property
	 *
	 * @param datetime $CreateDate
	 * @return wurflDevice
	 */
	function setCreateDate($inCreateDate) {
		if ( $inCreateDate !== $this->_CreateDate ) {
			$this->_CreateDate = $inCreateDate;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns UpdateDate
	 *
	 * @return datetime
	 */
	function getUpdateDate() {
		return $this->_UpdateDate;
	}
	
	/**
	 * Set UpdateDate property
	 *
	 * @param datetime $UpdateDate
	 * @return wurflDevice
	 */
	function setUpdateDate($inUpdateDate) {
		if ( $inUpdateDate !== $this->_UpdateDate ) {
			$this->_UpdateDate = $inUpdateDate;
			$this->_Modified = true;
		}
		return $this;
	}
	
	
	
	/**
	 * Returns Capabilities
	 *
	 * @return wurflDeviceCapabilities
	 */
	function getCapabilities() {
		if ( !$this->_Capabilities instanceof wurflDeviceCapabilities ) {
			$this->_Capabilities = wurflDeviceCapabilities::getInstance($this);
		}
		return $this->_Capabilities;
	}
	
	/**
	 * Set Capabilities property
	 *
	 * @param wurflDeviceCapabilities $Capabilities
	 * @return wurflDevice
	 */
	function setCapabilities($inCapabilities) {
		if ( $inCapabilities !== $this->_Capabilities ) {
			$this->_Capabilities = $inCapabilities;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns the capability value for $inCapability
	 *
	 * @param string $inCapability
	 * @return mixed
	 */
	function getCapability($inCapability) {
		return $this->getCapabilities()->getCapability($inCapability);
	}
	
	/**
	 * Returns the array of devices
	 *
	 * @return array
	 */
	function getDevicePath() {
		if ( !is_array($this->_DevicePath) || count($this->_DevicePath) == 0 ) {
			$this->buildDevicePath();
		}
		return $this->_DevicePath;
	}
	
	/**
	 * Sets an array of deviceIDs to use as the device path, this function should not be used in normal operation
	 * Path is an array of deviceIDs from generic to specific
	 *
	 * @param array $path
	 */
	function setDevicePath($path) {
		if ( is_array($path) && count($path) > 0 ) {
			$this->_DevicePath = $path;
		}
	}
	
	/**
	 * Builds the device path for the current device, always returns root -> selected
	 *
	 * @return void
	 */
	protected function buildDevicePath() {
		if ( $this->_DeviceID ) {
			$devices[] = $this->_DeviceID;
			$fallBackID = $this->_FallBackID;
			if ( $fallBackID !== '' ) {
				$oStmt = dbManager::getInstance()->prepare('SELECT deviceID, fallBackID FROM '.system::getConfig()->getDatabase('wurfl').'.devices WHERE wurflID = :fallBackID LIMIT 1');
				$i = 50; #safety catcher to prevent infinite loops
				
				while ( $fallBackID != 'root' || $fallBackID != 'generic' ) {
					$oStmt->bindValue(':fallBackID', $fallBackID);
					$oStmt->execute();
					
					$row = $oStmt->fetch();
					if ( $row !== false && is_array($row) ) {
						$fallBackID = $row['fallBackID'];
						$parentDeviceID = $row['deviceID'];
						if ( !in_array($parentDeviceID, $devices) ) {
							$devices[] = $parentDeviceID;
						}
					}
					
					if ( $i == 0 ) {
						break;
					}
					$i--;
				}
				$this->_DevicePath = array_reverse($devices);
			}
		}
	}
}