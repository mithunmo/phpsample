<?php
/**
 * wurflCapability
 * 
 * Stored in wurflCapability.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflCapability
 * @version $Rev: 650 $
 */


/**
 * wurflCapability Class
 * 
 * Provides access to records in wurfl.capabilities
 * 
 * Creating a new record:
 * <code>
 * $oWurflCapability = new wurflCapability();
 * $oWurflCapability->setCapabilityID($inCapabilityID);
 * $oWurflCapability->setCapabilityGroupID($inCapabilityGroupID);
 * $oWurflCapability->setDescription($inDescription);
 * $oWurflCapability->setVarType($inVarType);
 * $oWurflCapability->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oWurflCapability = new wurflCapability($inCapabilityID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oWurflCapability = new wurflCapability();
 * $oWurflCapability->setCapabilityID($inCapabilityID);
 * $oWurflCapability->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oWurflCapability = wurflCapability::getInstance($inCapabilityID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflCapability
 */
class wurflCapability implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of wurflCapability
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
	 * Stores $_CapabilityID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_CapabilityID;
			
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
	 * Stores $_VarType
	 * 
	 * @var string (VARTYPE_BOOLEAN,VARTYPE_INTEGER,VARTYPE_STRING,)
	 * @access protected
	 */
	protected $_VarType;
	const VARTYPE_BOOLEAN = 'Boolean';
	const VARTYPE_INTEGER = 'Integer';
	const VARTYPE_STRING = 'String';
	
	/**
	 * Stores $_HelpText
	 *
	 * @var string
	 * @access protected
	 */
	protected $_HelpText;
	
	
	
	/**
	 * Returns a new instance of wurflCapability
	 * 
	 * @param integer $inCapabilityID
	 * @return wurflCapability
	 */
	function __construct($inCapabilityID = null) {
		$this->reset();
		if ( $inCapabilityID !== null ) {
			$this->setCapabilityID($inCapabilityID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new wurflCapability containing non-unique properties
	 * 
	 * @param integer inCapabilityGroupID
	 * @param string inDescription
	 * @param string inVarType
	 * @return wurflCapability
	 * @static 
	 */
	public static function factory($inCapabilityGroupID = null, $inDescription = null, $inVarType = null) {
		$oObject = new wurflCapability;
		if ( $inCapabilityGroupID !== null ) {
			$oObject->setCapabilityGroupID($inCapabilityGroupID);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inVarType !== null ) {
			$oObject->setVarType($inVarType);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of wurflCapability by primary key, will accept capId as int or wurfl cap name
	 * 
	 * @param mixed $inCapabilityID
	 * @return wurflCapability
	 * @static 
	 */
	public static function getInstance($inCapabilityID) {
		/**
		 * Check for an existing instance
		 */
		if ( is_numeric($inCapabilityID) ) {
			if ( isset(self::$_Instances[$inCapabilityID]) ) {
				return self::$_Instances[$inCapabilityID];
			}
		} elseif ( is_string($inCapabilityID) && strlen($inCapabilityID) > 1 ) {
			foreach ( self::$_Instances as $oCapability ) {
				if ( $oCapability->getDescription() == $inCapabilityID ) {
					return $oCapability;
				}
			}
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new wurflCapability();
		if ( is_numeric($inCapabilityID) ) {
			$oObject->setCapabilityID($inCapabilityID);
		} else {
			$oObject->setDescription($inCapabilityID);
		}
		if ( $oObject->load() ) {
			self::$_Instances[$inCapabilityID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
			
	/**
	 * Returns an array of objects of wurflCapability
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.capabilities';
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new wurflCapability();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.capabilities';
		
		$where = array();
		if ( $this->_CapabilityID !== 0 ) {
			$where[] = ' capabilityID = :CapabilityID ';
		}
		if ( $this->_Description !== '' ) {
			$where[] = ' description = :Description ';
		}
						
		if ( count($where) > 0 ) {
			$query .= ' WHERE '.implode(' AND ', $where);
		}

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_CapabilityID !== 0 ) {
				$oStmt->bindValue(':CapabilityID', $this->_CapabilityID);
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
		return $return;
	}
	
	/**
	 * Loads a record by array
	 * 
	 * @param array $inArray
	 */
	function loadFromArray($inArray) {
		$this->setCapabilityID((int)$inArray['capabilityID']);
		$this->setCapabilityGroupID((int)$inArray['capabilityGroupID']);
		$this->setDescription($inArray['description']);
		$this->setVarType($inArray['varType']);
		$this->setHelpText($inArray['helpText']);
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
				INSERT INTO '.system::getConfig()->getDatabase('wurfl').'.capabilities
					( capabilityID, capabilityGroupID, description, varType, helpText )
				VALUES 
					(:CapabilityID, :CapabilityGroupID, :Description, :VarType, :HelpText)
				ON DUPLICATE KEY UPDATE
					capabilityGroupID=VALUES(capabilityGroupID),
					description=VALUES(description),
					varType=VALUES(varType),
					helpText=VALUES(helpText)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':CapabilityID', $this->_CapabilityID);
					$oStmt->bindValue(':CapabilityGroupID', $this->_CapabilityGroupID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':VarType', $this->_VarType);
					$oStmt->bindValue(':HelpText', $this->_HelpText);
								
					if ( $oStmt->execute() ) {
						if ( !$this->getCapabilityID() ) {
							$this->setCapabilityID($oDB->lastInsertId());
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
		DELETE FROM '.system::getConfig()->getDatabase('wurfl').'.capabilities
		WHERE
			capabilityID = :CapabilityID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
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
	 * @return wurflCapability
	 */
	function reset() {
		$this->_CapabilityID = 0;
		$this->_CapabilityGroupID = 0;
		$this->_Description = '';
		$this->_VarType = 'String';
		$this->_HelpText = '';
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
		$string .= " CapabilityID[$this->_CapabilityID] $newLine";
		$string .= " CapabilityGroupID[$this->_CapabilityGroupID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " VarType[$this->_VarType] $newLine";
		$string .= " HelpText[$this->_HelpText] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'wurflCapability';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"CapabilityID\" value=\"$this->_CapabilityID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CapabilityGroupID\" value=\"$this->_CapabilityGroupID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"VarType\" value=\"$this->_VarType\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"HelpText\" value=\"$this->_HelpText\" type=\"string\" /> $newLine";
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
			$valid = $this->checkCapabilityID($message);
		}
		if ( $valid ) {
			$valid = $this->checkCapabilityGroupID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkVarType($message);
		}
		return $valid;
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
	 * Checks that $_VarType has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkVarType(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_VarType) && $this->_VarType !== '' ) {
			$inMessage .= "{$this->_VarType} is not a valid value for VarType\n";
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
	 * @return wurflCapability
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
		return $this->_CapabilityID;
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
	 * Set $_CapabilityID to CapabilityID
	 * 
	 * @param integer $inCapabilityID
	 * @return wurflCapability
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
	 * Return value of $_CapabilityGroupID
	 * 
	 * @return integer
	 * @access public
	 */
	function getCapabilityGroupID() {
		return $this->_CapabilityGroupID;
	}
	
	/**
	 * Returns the wurflCapabilityGroup object
	 *
	 * @return wurflCapabilityGroup
	 */
	function getCapabilityGroup() {
		return wurflCapabilityGroup::getInstance($this->_CapabilityGroupID);
	}
	
	/**
	 * Set $_CapabilityGroupID to CapabilityGroupID
	 * 
	 * @param integer $inCapabilityGroupID
	 * @return wurflCapability
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
	 * Returns the description formatted for display
	 *
	 * @return string
	 */
	function getFormattedDescription() {
		return ucwords(str_replace('_', ' ', $this->getDescription()));
	}
	
	/**
	 * Set $_Description to Description
	 * 
	 * @param string $inDescription
	 * @return wurflCapability
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
	 * Return value of $_VarType
	 * 
	 * @return string
	 * @access public
	 */
	function getVarType() {
		return $this->_VarType;
	}
	
	/**
	 * Set $_VarType to VarType
	 * 
	 * @param string $inVarType
	 * @return wurflCapability
	 * @access public
	 */
	function setVarType($inVarType) {
		if ( $inVarType !== $this->_VarType ) {
			$this->_VarType = $inVarType;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_HelpText
	 * 
	 * @return string
	 * @access public
	 */
	function getHelpText() {
		return $this->_HelpText;
	}
	
	/**
	 * Set $_HelpText to HelpText
	 * 
	 * @param string $inHelpText
	 * @return wurflCapability
	 * @access public
	 */
	function setHelpText($inHelpText) {
		if ( $inHelpText !== $this->_HelpText ) {
			$this->_HelpText = $inHelpText;
			$this->setModified();
		}
		return $this;
	}
}