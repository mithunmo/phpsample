<?php
/**
 * wurflCapabilityGroup
 * 
 * Stored in wurflCapabilityGroup.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflCapabilityGroup
 * @version $Rev: 650 $
 */


/**
 * wurflCapabilityGroup Class
 * 
 * Provides access to records in wurfl.capabilityGroups
 * 
 * Creating a new record:
 * <code>
 * $oWurflCapabilityGroup = new wurflCapabilityGroup();
 * $oWurflCapabilityGroup->setCapabilityGroupID($inCapabilityGroupID);
 * $oWurflCapabilityGroup->setDescription($inDescription);
 * $oWurflCapabilityGroup->setDisplayName($inDisplayName);
 * $oWurflCapabilityGroup->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oWurflCapabilityGroup = new wurflCapabilityGroup($inCapabilityGroupID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oWurflCapabilityGroup = new wurflCapabilityGroup();
 * $oWurflCapabilityGroup->setCapabilityGroupID($inCapabilityGroupID);
 * $oWurflCapabilityGroup->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oWurflCapabilityGroup = wurflCapabilityGroup::getInstance($inCapabilityGroupID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflCapabilityGroup
 */
class wurflCapabilityGroup implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of wurflCapabilityGroup
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
	 * Stores $_CapabilityGroupID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_CapabilityGroupID;
			
	/**
	 * Stores $_Description
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Description;
			
	/**
	 * Stores $_DisplayName
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_DisplayName;
			
	
	
	/**
	 * Returns a new instance of wurflCapabilityGroup
	 * 
	 * @param integer $inCapabilityGroupID
	 * @return wurflCapabilityGroup
	 */
	function __construct($inCapabilityGroupID = null) {
		$this->reset();
		if ( $inCapabilityGroupID !== null ) {
			$this->setCapabilityGroupID($inCapabilityGroupID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new wurflCapabilityGroup containing non-unique properties
	 * 
	 * @param string inDescription
	 * @param string inDisplayName
	 * @return wurflCapabilityGroup
	 * @static 
	 */
	public static function factory($inDescription = null, $inDisplayName = null) {
		$oObject = new wurflCapabilityGroup;
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inDisplayName !== null ) {
			$oObject->setDisplayName($inDisplayName);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of wurflCapabilityGroup by primary key
	 * 
	 * @param integer $inCapabilityGroupID
	 * @return wurflCapabilityGroup
	 * @static 
	 */
	public static function getInstance($inCapabilityGroupID) {
		/**
		 * Check for an existing instance
		 */
		if ( is_numeric($inCapabilityGroupID) ) {
			if ( isset(self::$_Instances[$inCapabilityGroupID]) ) {
				return self::$_Instances[$inCapabilityGroupID];
			}
		} elseif ( is_string($inCapabilityGroupID) && strlen($inCapabilityGroupID) > 1 ) {
			foreach ( self::$_Instances as $oObject ) {
				if ( $oObject->getDescription() == $inCapabilityGroupID ) {
					return $oObject;
				}
			}
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new wurflCapabilityGroup();
		if ( is_numeric($inCapabilityGroupID) ) {
			$oObject->setCapabilityGroupID($inCapabilityGroupID);
		} else {
			$oObject->setDescription($inCapabilityGroupID);
		}
		if ( $oObject->load() ) {
			self::$_Instances[$inCapabilityGroupID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
			
	/**
	 * Returns an array of objects of wurflCapabilityGroup
	 * 
	 * @return array
	 * @static 
	 */
	public static function listOfObjects() {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.capabilityGroups';
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new wurflCapabilityGroup();
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
	 * Loads a record from the Database systemd on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		if ( $this->_CapabilityGroupID !== 0 || $this->_Description !== '' ) {
			$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.capabilityGroups';
			
			$where = array();
			if ( $this->_CapabilityGroupID !== 0 ) {
				$where[] = ' capabilityGroupID = :CapabilityGroupID ';
			}
			if ( $this->_Description !== '' ) {
				$where[] = ' description = :Description ';
			}
							
			if ( count($where) > 0 ) {
				$query .= ' WHERE '.implode(' AND ', $where);
			}
	
			try {
				$oStmt = dbManager::getInstance()->prepare($query);
				if ( $this->_CapabilityGroupID !== 0 ) {
					$oStmt->bindValue(':CapabilityGroupID', $this->_CapabilityGroupID);
				}
				if ( $this->_Description !== '' ) {
					$oStmt->bindValue(':Description', $this->_Description);
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
		$this->setCapabilityGroupID((int)$inArray['capabilityGroupID']);
		$this->setDescription($inArray['description']);
		$this->setDisplayName($inArray['displayName']);
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
				INSERT INTO '.system::getConfig()->getDatabase('wurfl').'.capabilityGroups
					( capabilityGroupID, description, displayName)
				VALUES 
					(:CapabilityGroupID, :Description, :DisplayName)
				ON DUPLICATE KEY UPDATE
					description=VALUES(description),
					displayName=VALUES(displayName)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':CapabilityGroupID', $this->_CapabilityGroupID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':DisplayName', $this->_DisplayName);
								
					if ( $oStmt->execute() ) {
						if ( !$this->getCapabilityGroupID() ) {
							$this->setCapabilityGroupID($oDB->lastInsertId());
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
		DELETE FROM '.system::getConfig()->getDatabase('wurfl').'.capabilityGroups
		WHERE
			capabilityGroupID = :CapabilityGroupID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':CapabilityGroupID', $this->_CapabilityGroupID);
				
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
	 * @return wurflCapabilityGroup
	 */
	function reset() {
		$this->_CapabilityGroupID = 0;
		$this->_Description = '';
		$this->_DisplayName = '';
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
		$string .= " CapabilityGroupID[$this->_CapabilityGroupID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " DisplayName[$this->_DisplayName] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'wurflCapabilityGroup';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"CapabilityGroupID\" value=\"$this->_CapabilityGroupID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"DisplayName\" value=\"$this->_DisplayName\" type=\"string\" /> $newLine";
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
			$valid = $this->checkCapabilityGroupID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkDisplayName($message);
		}
		return $valid;
	}
		
	/**
	 * Checks that $_CapabilityGroupID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkCapabilityGroupID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_CapabilityGroupID) && $this->_CapabilityGroupID !== 0 ) {
			$inMessage .= "{$this->_CapabilityGroupID} is not a valid value for CapabilityGroupID\n";
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
		if ( !is_string($this->_Description) && $this->_Description !== '' ) {
			$inMessage .= "{$this->_Description} is not a valid value for Description\n";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_Description) > 100 ) {
			$inMessage .= "Description can not be more than 100 characters\n";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character\n";
			$isValid = false;
		}		
		return $isValid;
	}
		
	/**
	 * Checks that $_DisplayName has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkDisplayName(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_DisplayName) && $this->_DisplayName !== '' ) {
			$inMessage .= "{$this->_DisplayName} is not a valid value for DisplayName\n";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_DisplayName) > 100 ) {
			$inMessage .= "DisplayName can not be more than 100 characters\n";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_DisplayName) <= 1 ) {
			$inMessage .= "DisplayName must be more than 1 character\n";
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
	 * @return wurflCapabilityGroup
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
		return $this->_CapabilityGroupID;
	}
	
	/**
	 * Return value of $_CapabilityGroupID
	 * 
	 * @return integer
	 * @access public
	 */
	function getCapabilityGroupID() {
		return $this->_CapabilityGroupID;
	}
	
	/**
	 * Set $_CapabilityGroupID to CapabilityGroupID
	 * 
	 * @param integer $inCapabilityGroupID
	 * @return wurflCapabilityGroup
	 * @access public
	 */
	function setCapabilityGroupID($inCapabilityGroupID) {
		if ( $inCapabilityGroupID !== $this->_CapabilityGroupID ) {
			$this->_CapabilityGroupID = $inCapabilityGroupID;
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
	 * @return wurflCapabilityGroup
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
	 * Return value of $_DisplayName
	 * 
	 * @return string
	 * @access public
	 */
	function getDisplayName() {
		return $this->_DisplayName;
	}
	
	/**
	 * Set $_DisplayName to DisplayName
	 * 
	 * @param string $inDisplayName
	 * @return wurflCapabilityGroup
	 * @access public
	 */
	function setDisplayName($inDisplayName) {
		if ( $inDisplayName !== $this->_DisplayName ) {
			$this->_DisplayName = $inDisplayName;
			$this->setModified();
		}
		return $this;
	}
}