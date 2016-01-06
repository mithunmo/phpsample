<?php
/**
 * wurflDeviceCapabilities
 * 
 * Stored in wurflDeviceCapabilities.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflDeviceCapabilities
 * @version $Rev: 650 $
 */


/**
 * wurflDeviceCapabilities Class
 * 
 * Handles mapping capabilities to a device
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflDeviceCapabilities
 */
class wurflDeviceCapabilities extends baseSet implements systemDaoInterface {
	
	/**
	 * Holds an instance of wurflDeviceCapabilities
	 *
	 * @var wurflDeviceCapabilities
	 * @access protected
	 * @static 
	 */
	protected static $_Instance = false;
	
	/**
	 * Stores $_WurflDevice
	 *
	 * @var wurflDevice
	 * @access protected
	 */
	protected $_WurflDevice			= false;
	
	
	
	/**
	 * Returns a new instance of wurflDeviceCapabilities
	 *
	 * @param wurflDevice $oDevice
	 */
	function __construct(wurflDevice $oDevice) {
		$this->setWurflDevice($oDevice);
	}
	
	/**
	 * Returns an instance of wurflDeviceCapabilities
	 *
	 * @param wurflDevice $oDevice
	 * @return wurflDeviceCapabilities
	 */
	static function getInstance(wurflDevice $oDevice) {
		if ( isset(self::$_Instance) && self::$_Instance instanceof wurflDeviceCapabilities ) {
			$oObject = self::$_Instance;
			$oObject->setWurflDevice($oDevice);
			return $oObject;
		}
		
		self::$_Instance = new wurflDeviceCapabilities($oDevice);
		return self::$_Instance;
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
	 * Returns true if the object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( $this->_itemCount() > 0 ) {
			if ( false ) $oCapability = new wurflDeviceCapability();
			foreach ( $this as $capability => $oCapability ) {
				$modified = $modified || $oCapability->isModified();
			}
		}
		return $modified;
	}

 	/**
 	 * Load the object based on properties
 	 * 
 	 * Properties are loaded via a single UNION statement reducing the number of
 	 * DB calls to only one. From testing, issuing a single UNION is faster than
 	 * multiple individual selects per path device. Even 11 UNION joins results
 	 * in a query that executes in under .05 seconds.
 	 * 
 	 * Tested on MySQL 5.1.30 WAMP Server 2.0g, Windows XP, P4 3.2GHz, 2GB ram.
 	 * Fetching: SonyEricssonW880i and LG/U890/v1.0
 	 * 
 	 * @return boolean
 	 */
 	function load() {
 		$this->reset();
 		try {
	 		if ( $this->_WurflDevice instanceof wurflDevice && $this->_WurflDevice->getDeviceID() > 0 ) {
	 			$devicePath = $this->_WurflDevice->getDevicePath();
	 			if ( is_array($devicePath) && count($devicePath) > 0 ) {
	 				$devicePath = array_reverse($devicePath);
	 				$union = array();
	 				foreach ( $devicePath as $deviceID ) {
	 					$union[] = '(SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.deviceCapabilities WHERE deviceID = '.dbManager::getInstance()->quote($deviceID).')';
	 				}
	 				$query = '
	 					SELECT capabilities.description, t1.*
	 					  FROM ('.implode(' UNION ', $union).') AS t1
	 					       LEFT JOIN '.system::getConfig()->getDatabase('wurfl').'.capabilities ON (t1.capabilityID = capabilities.capabilityID)
	 					 GROUP BY t1.capabilityID';
	 				
	 				$oStmt = dbManager::getInstance()->prepare($query);
	 				if ( $oStmt->execute() ) {
	 					foreach ( $oStmt as $row ) {
	 						$oCapability = new wurflDeviceCapability();
	 						$oCapability->loadFromArray($row);
	 						$this->_setItem($row['description'], $oCapability);
	 					}
 					}
 					$oStmt->closeCursor();
	 				$this->setModified(false);
	 				return true;
	 			}
	 		}
	 	} catch ( Exception $e ) {
	 		systemLog::error($e->getMessage());
	 	}
 		return false;
 	}
 	
 	/**
 	 * Commits the object and any changes to the database, returns number of updated objects
 	 *
 	 * @return integer
 	 */
 	function save() {
 		$i = 0;
 		if ( $this->isModified() ) {
 			if ( false ) $oCapability = new wurflDeviceCapability();
 			foreach ( $this as $capability => $oCapability ) {
 				if ( !$oCapability->getDeviceID() ) {
 					$oCapability->setDeviceID($this->getWurflDevice()->getDeviceID());
 				}
 				if ( $oCapability->save() ) {
 					$i++;
 				}
 			}
 			$this->setModified(false);
 		}
 		return $i;
 	}
 	
 	/**
 	 * Deletes the object and any sub-objects
 	 *
 	 * @return boolean
 	 */
 	function delete() {
 		$i = 0;
 		if ( $this->_itemCount() > 0 ) {
 			if ( false ) $oCapability = new wurflDeviceCapability();
 			foreach ( $this as $capability => $oCapability ) {
 				if ( $oCapability->getDeviceID() == $this->getWurflDevice()->getDeviceID() && $oCapability->delete() ) {
 					$this->_removeItem($capability);
 					$i++;
 				}
 			}
 		}
 		return $i;
 	}
 	
 	/**
 	 * Resets object properties to defaults
 	 *
 	 * @return baseDaoInterface
 	 */
 	function reset() {
 		return $this->_resetSet();
 	}
 	
	
 	
 	/**
	 * Returns WurflDevice
	 *
	 * @return wurflDevice
	 */
	function getWurflDevice() {
		return $this->_WurflDevice;
	}
	
	/**
	 * Set WurflDevice property
	 *
	 * @param wurflDevice $WurflDevice
	 * @return wurflDeviceCapabilities
	 */
	function setWurflDevice(wurflDevice $WurflDevice) {
		if ( $WurflDevice !== $this->_WurflDevice ) {
			$this->_WurflDevice = $WurflDevice;
			$this->load();
			$this->_Modified = true;
		}
		return $this;
	}
	
	
	
	/**
	 * Returns an object from the list with key $inCapability
	 *
	 * @param mixed $inCapability
	 * @return wurflDeviceCapability
	 */
	function getObject($inCapability) {
		try {
			if ( is_object($inCapability) && $inCapability instanceof wurflCapability ) {
				$inCapability = $inCapability->getDescription();
			}
			
			if ( false ) $oCapability = new wurflDeviceCapability();
			foreach ( $this as $capability => $oCapability ) {
				if ( is_numeric($inCapability) ) {
					if ( $inCapability == $oCapability->getCapabilityID() ) {
						return $oCapability;
					}
				} else {
					if ($inCapability == $capability ) {
						return $oCapability;
					}
				}
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
		}
		return false;
	}
	
	/**
	 * Add capability to device
	 *
	 * @param string $inCapability
	 * @param mixed $inValue
	 * @return wurflDeviceCapabilities
	 */
	function setCapability($inCapability, $inValue) {
		$deviceID = 0;
		if ( is_object($this->getWurflDevice()) && $this->getWurflDevice()->getDeviceID() ) {
			$deviceID = $this->getWurflDevice()->getDeviceID();
		}
		
		$oCapability = $this->getObject($inCapability);
		if ( !is_object($oCapability) ) {
			$oCapability = new wurflDeviceCapability();
		} else {
			if ( $inValue !== $oCapability->getWurflValue() ) {
				$oCapability->setDeviceID($deviceID);
			}
		}
		
		$oCapability->setCapabilityID(wurflCapability::getInstance($inCapability)->getCapabilityID());
		$oCapability->setWurflValue($inValue);
		$this->_setItem($inCapability, $oCapability);
		return $this;
	}
	
	/**
	 * Sets a custom value to a capability overriding the WURFL value, always assigns the current deviceID to the capability
	 *
	 * @param string $inCapability
	 * @param mixed $inValue
	 * @return wurflDeviceCapabilities
	 */
	function setCustomCapability($inCapability, $inValue) {
		$deviceID = 0;
		if ( is_object($this->getWurflDevice()) && $this->getWurflDevice()->getDeviceID() ) {
			$deviceID = $this->getWurflDevice()->getDeviceID();
		}
		
		$oCapability = $this->getObject($inCapability);
		if ( !is_object($oCapability) ) {
			$oCapability = new wurflDeviceCapability();
		}
		$oCapability->setDeviceID($deviceID);
		$oCapability->setCapabilityID(wurflCapability::getInstance($inCapability)->getCapabilityID());
		$oCapability->setCustomValue($inValue);
		$this->_setItem($inCapability, $oCapability);
		return $this;
	}
	
	/**
	 * Returns the capability value; if a custom value has been set this will be returned instead
	 *
	 * @param string $inCapability
	 * @return mixed
	 */
	function getCapability($inCapability) {
		$oCapability = $this->getObject($inCapability);
		if ( is_object($oCapability) && $oCapability instanceof wurflDeviceCapability ) {
			if ( $oCapability->getCustomValue() !== null ) {
				return $oCapability->getCustomValue();
			}
			return $oCapability->getWurflValue();
		}
		return false;
	}
	
	/**
	 * Removes a capability from the device
	 *
	 * @param mixed $inCapability
	 * @return boolean
	 */
	function removeCapability($inCapability) {
		try {
			if ( false ) $oCapability = new wurflDeviceCapability();
			$oCapability = $this->getObject($inCapability);
			if ( is_object($oCapability) && $oCapability instanceof wurflDeviceCapability ) {
				if ( $oCapability->getDeviceID() == $this->getWurflDevice()->getDeviceID() ) {
					if ( $oCapability->delete() ) {
						$this->_removeItem($inCapability);
						return true;
					}
				}
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
		}
		return false;
	}
	
	/**
	 * Returns the total number of capabilities loaded into the device
	 *
	 * @return integer
	 */
	function countCapabilities() {
		return $this->_itemCount();
	}
}