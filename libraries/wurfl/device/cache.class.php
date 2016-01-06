<?php
/**
 * wurflDeviceCache
 * 
 * Stored in wurflDeviceCache.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflDeviceCache
 * @version $Rev: 650 $
 */


/**
 * wurflDeviceCache Class
 * 
 * Provides access to records in wurfl.deviceCache
 * 
 * Creating a new record:
 * <code>
 * $oWurflDeviceCache = new wurflDeviceCache();
 * $oWurflDeviceCache->setDeviceID($inDeviceID);
 * $oWurflDeviceCache->setCreateDate($inCreateDate);
 * $oWurflDeviceCache->setUpdateDate($inUpdateDate);
 * $oWurflDeviceCache->setData($inData);
 * $oWurflDeviceCache->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oWurflDeviceCache = new wurflDeviceCache($inDeviceID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oWurflDeviceCache = new wurflDeviceCache();
 * $oWurflDeviceCache->setDeviceID($inDeviceID);
 * $oWurflDeviceCache->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oWurflDeviceCache = wurflDeviceCache::getInstance($inDeviceID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflDeviceCache
 */
class wurflDeviceCache implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * How long the device should be cached before a refresh is needed
	 *
	 * @var integer
	 */
	const CACHE_LIFETIME = 7200;
	
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
	 * Stores $_Data
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Data;
			
	
	
	/**
	 * Returns a new instance of wurflDeviceCache
	 * 
	 * @param integer $inDeviceID
	 * @return wurflDeviceCache
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
	 * Creates a new wurflDeviceCache containing non-unique properties
	 * 
	 * @param datetime $inCreateDate
	 * @param datetime $inUpdateDate
	 * @param string $inData
	 * @return wurflDeviceCache
	 * @static 
	 */
	public static function factory($inCreateDate = null, $inUpdateDate = null, $inData = null) {
		$oObject = new wurflDeviceCache;
		if ( $inCreateDate !== null ) {
			$oObject->setCreateDate($inCreateDate);
		}
		if ( $inUpdateDate !== null ) {
			$oObject->setUpdateDate($inUpdateDate);
		}
		if ( $inData !== null ) {
			$oObject->setData($inData);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of wurflDeviceCache by primary key
	 * 
	 * @param integer $inDeviceID
	 * @return wurflDeviceCache
	 * @static 
	 */
	public static function getInstance($inDeviceID) {
		/**
		 * No instance, create one
		 */
		$oObject = new wurflDeviceCache();
		$oObject->setDeviceID($inDeviceID);
		if ( $oObject->load() ) {
			return $oObject;
		}
		return $oObject;
	}
		
	/**
	 * Get instance of wurflDeviceCache by unique key (deviceID)
	 * 
	 * @param integer $inDeviceID
	 * @return wurflDeviceCache
	 * @static
	 */
	public static function getInstanceByDeviceID($inDeviceID) {
		/**
		 * No instance, create one
		 */
		$oObject = new wurflDeviceCache();
		$oObject->setDeviceID($inDeviceID);
		if ( $oObject->load() ) {
			return $oObject;
		}
		return $oObject;
	}
			
	/**
	 * Returns an array of objects of wurflDeviceCache
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.deviceCache';
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new wurflDeviceCache();
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
		if ( $this->_DeviceID !== 0 ) {
			$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.deviceCache';
			
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
		$this->setCreateDate($inArray['createDate']);
		$this->setUpdateDate($inArray['updateDate']);
		$this->setData(unserialize($inArray['data']));
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
				INSERT INTO '.system::getConfig()->getDatabase('wurfl').'.deviceCache
					( deviceID, createDate, updateDate, data)
				VALUES 
					(:DeviceID, :CreateDate, :UpdateDate, :Data)
				ON DUPLICATE KEY UPDATE
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate),
					data=VALUES(data)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':DeviceID', $this->_DeviceID);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':UpdateDate', $this->_UpdateDate);
					$oStmt->bindValue(':Data', serialize($this->_Data));
								
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
		DELETE FROM '.system::getConfig()->getDatabase('wurfl').'.deviceCache
		WHERE
			deviceID = :DeviceID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':DeviceID', $this->_DeviceID);
				
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
	 * @return wurflDeviceCache
	 */
	function reset() {
		$this->_DeviceID = 0;
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_UpdateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Data = '';
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
		$string .= " CreateDate[$this->_CreateDate] $newLine";
		$string .= " UpdateDate[$this->_UpdateDate] $newLine";
		$string .= " Data[$this->_Data] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'wurflDeviceCache';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"DeviceID\" value=\"$this->_DeviceID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CreateDate\" value=\"$this->_CreateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"UpdateDate\" value=\"$this->_UpdateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Data\" value=\"$this->_Data\" type=\"string\" /> $newLine";
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
	 * @param string $inMessage
	 * @return boolean
	 */
	function isValid(&$inMessage = '') {
		$valid = true;
		if ( $valid ) {
			$valid = $this->checkDeviceID($inMessage);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($inMessage);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateDate($inMessage);
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
	 * Checks that $_CreateDate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkCreateDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_CreateDate) && $this->_CreateDate !== '' ) {
			$inMessage .= "{$this->_CreateDate} is not a valid value for CreateDate\n";
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
			$inMessage .= "{$this->_UpdateDate} is not a valid value for UpdateDate\n";
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
	 * @param boolean $inStatus
	 * @return wurflDeviceCache
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
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
	 * @return wurflDeviceCache
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
	 * @return wurflDeviceCache
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
	 * @return wurflDeviceCache
	 * @access public
	 */
	function setUpdateDate($inUpdateDate) {
		if ( $inUpdateDate !== $this->_UpdateDate ) {
			$this->_UpdateDate = $inUpdateDate;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Data
	 * 
	 * @return string
	 * @access public
	 */
	function getData() {
		return $this->_Data;
	}
	
	/**
	 * Set $_Data to Data
	 * 
	 * @param string $inData
	 * @return wurflDeviceCache
	 * @access public
	 */
	function setData($inData) {
		if ( $inData !== $this->_Data ) {
			$this->_Data = $inData;
			$this->setModified();
		}
		return $this;
	}
}