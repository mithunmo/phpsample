<?php
/**
 * wurflDeviceCapability
 * 
 * Stored in wurflDeviceCapability.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflDeviceCapability
 * @version $Rev: 650 $
 */


/**
 * wurflDeviceCapability Class
 * 
 * Provides access to records in wurfl.deviceCapabilities
 * 
 * Creating a new record:
 * <code>
 * $oWurflDeviceCapability = new wurflDeviceCapability();
 * $oWurflDeviceCapability->setDeviceID($inDeviceID);
 * $oWurflDeviceCapability->setCapabilityID($inCapabilityID);
 * $oWurflDeviceCapability->setWurflValue($inWurflValue);
 * $oWurflDeviceCapability->setCustomValue($inCustomValue);
 * $oWurflDeviceCapability->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oWurflDeviceCapability = new wurflDeviceCapability($inDeviceID, $inCapabilityID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oWurflDeviceCapability = new wurflDeviceCapability();
 * $oWurflDeviceCapability->setDeviceID($inDeviceID);
 * $oWurflDeviceCapability->setCapabilityID($inCapabilityID);
 * $oWurflDeviceCapability->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oWurflDeviceCapability = wurflDeviceCapability::getInstance($inDeviceID, $inCapabilityID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflDeviceCapability
 */
class wurflDeviceCapability implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of wurflDeviceCapability
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
	 * Stores $_DeviceID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_DeviceID;
			
	/**
	 * Stores $_CapabilityID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_CapabilityID;
			
	/**
	 * Stores $_WurflValue
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_WurflValue;
			
	/**
	 * Stores $_CustomValue
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_CustomValue;
			
	
	
	/**
	 * Returns a new instance of wurflDeviceCapability
	 * 
	 * @param integer $DeviceID
	 * @param integer $CapabilityID
	 * @return wurflDeviceCapability
	 */
	function __construct($inDeviceID = null, $inCapabilityID = null) {
		$this->reset();
		if ( $inDeviceID !== null && $inCapabilityID !== null ) {
			$this->setDeviceID($inDeviceID);
			$this->setCapabilityID($inCapabilityID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new wurflDeviceCapability containing non-unique properties
	 * 
	 * @param string WurflValue
	 * @param string CustomValue
	 * @return wurflDeviceCapability
	 * @static 
	 */
	public static function factory($inWurflValue = null, $inCustomValue = null) {
		$oObject = new wurflDeviceCapability;
		if ( $inWurflValue !== null ) {
			$oObject->setWurflValue($inWurflValue);
		}
		if ( $inCustomValue !== null ) {
			$oObject->setCustomValue($inCustomValue);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of wurflDeviceCapability by primary key
	 * 
	 * @param integer $DeviceID
	 * @param integer $CapabilityID
	 * @return wurflDeviceCapability
	 * @static 
	 */
	public static function getInstance($inDeviceID, $inCapabilityID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inDeviceID.'.'.$inCapabilityID]) ) {
			return self::$_Instances[$inDeviceID.'.'.$inCapabilityID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new wurflDeviceCapability();
		$oObject->setDeviceID($inDeviceID);
		$oObject->setCapabilityID($inCapabilityID);
		if ( $oObject->load() ) {
			self::$_Instances[$inDeviceID.'.'.$inCapabilityID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
			
	/**
	 * Returns an array of objects of wurflDeviceCapability
	 * 
	 * @param integer $inDeviceID
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inDeviceID) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.deviceCapabilities WHERE deviceID = :deviceID';
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':deviceID', $inDeviceID);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new wurflDeviceCapability();
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
		if ( $this->_DeviceID !== 0 || $this->_CapabilityID !== 0 ) {
			$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.deviceCapabilities';
			
			$where = array();
			if ( $this->_DeviceID !== 0 ) {
				$where[] = ' deviceID = :DeviceID ';
			}
			if ( $this->_CapabilityID !== 0 ) {
				$where[] = ' capabilityID = :CapabilityID ';
			}
							
			if ( count($where) > 0 ) {
				$query .= ' WHERE '.implode(' AND ', $where);
			}
	
			try {
				$oStmt = dbManager::getInstance()->prepare($query);
				if ( $this->_DeviceID !== 0 ) {
					$oStmt->bindValue(':DeviceID', $this->_DeviceID);
				}
				if ( $this->_CapabilityID !== 0 ) {
					$oStmt->bindValue(':CapabilityID', $this->_CapabilityID);
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
		}
		return $return;
	}
	
	/**
	 * Loads a record by array
	 * 
	 * @param array $inArray
	 */
	function loadFromArray($inArray) {
		$this->setDeviceID($inArray['deviceID']);
		$this->setCapabilityID($inArray['capabilityID']);
		$this->setWurflValue($inArray['wurflValue']);
		$this->setCustomValue($inArray['customValue']);
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
			
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('wurfl').'.deviceCapabilities
					( deviceID, capabilityID, wurflValue, customValue)
				VALUES 
					(:DeviceID, :CapabilityID, :WurflValue, :CustomValue)
				ON DUPLICATE KEY UPDATE
					wurflValue=VALUES(wurflValue),
					customValue=VALUES(customValue)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':DeviceID', $this->_DeviceID);
					$oStmt->bindValue(':CapabilityID', $this->_CapabilityID);
					$oStmt->bindValue(':WurflValue', $this->_WurflValue);
					$oStmt->bindValue(':CustomValue', $this->_CustomValue);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('wurfl').'.deviceCapabilities
		WHERE
			deviceID = :DeviceID AND 
			capabilityID = :CapabilityID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':DeviceID', $this->_DeviceID);
			$oStmt->bindValue(':CapabilityID', $this->_CapabilityID);
				
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
	 * @return wurflDeviceCapability
	 */
	function reset() {
		$this->_DeviceID = 0;
		$this->_CapabilityID = 0;
		$this->_WurflValue = null;
		$this->_CustomValue = null;
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
		$string .= " CapabilityID[$this->_CapabilityID] $newLine";
		$string .= " WurflValue[$this->_WurflValue] $newLine";
		$string .= " CustomValue[$this->_CustomValue] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'wurflDeviceCapability';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"DeviceID\" value=\"$this->_DeviceID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CapabilityID\" value=\"$this->_CapabilityID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"WurflValue\" value=\"$this->_WurflValue\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"CustomValue\" value=\"$this->_CustomValue\" type=\"string\" /> $newLine";
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
			$valid = $this->checkCapabilityID($message);
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
			$inMessage .= "{$this->_DeviceID} is not a valid value for DeviceID\n";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_CapabilityID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkCapabilityID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_CapabilityID) && $this->_CapabilityID !== 0 ) {
			$inMessage .= "{$this->_CapabilityID} is not a valid value for CapabilityID\n";
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
	 * @return wurflDeviceCapability
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
		return $this->_DeviceID.'.'.$this->_CapabilityID;
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
	 * @return wurflDeviceCapability
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
	 * Return value of $_CapabilityID
	 * 
	 * @return integer
	 * @access public
	 */
	function getCapabilityID() {
		return $this->_CapabilityID;
	}
	
	/**
	 * Returns the wurflCapability object
	 *
	 * @return wurflCapability
	 */
	function getCapability() {
		return wurflCapability::getInstance($this->_CapabilityID);
	}
	
	/**
	 * Set $_CapabilityID to CapabilityID
	 * 
	 * @param integer $inCapabilityID
	 * @return wurflDeviceCapability
	 * @access public
	 */
	function setCapabilityID($inCapabilityID) {
		if ( $inCapabilityID !== $this->_CapabilityID ) {
			$this->_CapabilityID = $inCapabilityID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_WurflValue
	 * 
	 * @return string
	 * @access public
	 */
	function getWurflValue() {
		return $this->_WurflValue;
	}
	
	/**
	 * Set $_WurflValue to WurflValue
	 * 
	 * @param string $inWurflValue
	 * @return wurflDeviceCapability
	 * @access public
	 */
	function setWurflValue($inWurflValue) {
		if ( $inWurflValue !== $this->_WurflValue ) {
			$this->_WurflValue = $inWurflValue;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_CustomValue
	 * 
	 * @return string
	 * @access public
	 */
	function getCustomValue() {
		return $this->_CustomValue;
	}
	
	/**
	 * Set $_CustomValue to CustomValue
	 * 
	 * @param string $inCustomValue
	 * @return wurflDeviceCapability
	 * @access public
	 */
	function setCustomValue($inCustomValue) {
		if ( $inCustomValue !== $this->_CustomValue ) {
			$this->_CustomValue = $inCustomValue;
			$this->setModified();
		}
		return $this;
	}
}