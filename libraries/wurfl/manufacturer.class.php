<?php
/**
 * wurflManufacturer
 * 
 * Stored in wurflManufacturer.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflManufacturer
 * @version $Rev: 650 $
 */


/**
 * wurflManufacturer Class
 * 
 * Provides access to records in wurfl.manufacturers
 * 
 * Creating a new record:
 * <code>
 * $oWurflManufacturer = new wurflManufacturer();
 * $oWurflManufacturer->setManufacturerID($inManufacturerID);
 * $oWurflManufacturer->setDescription($inDescription);
 * $oWurflManufacturer->setActive($inActive);
 * $oWurflManufacturer->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oWurflManufacturer = new wurflManufacturer($inManufacturerID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oWurflManufacturer = new wurflManufacturer();
 * $oWurflManufacturer->setManufacturerID($inManufacturerID);
 * $oWurflManufacturer->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oWurflManufacturer = wurflManufacturer::getInstance($inManufacturerID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflManufacturer
 */
class wurflManufacturer implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of wurflManufacturer
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
	 * Stores $_ManufacturerID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_ManufacturerID;
			
	/**
	 * Stores $_Description
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Description;
			
	/**
	 * Stores $_Active
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_Active;
			
	
	
	/**
	 * Returns a new instance of wurflManufacturer
	 * 
	 * @param integer $ManufacturerID
	 * @return wurflManufacturer
	 */
	function __construct($inManufacturerID = null) {
		$this->reset();
		if ( $inManufacturerID !== null ) {
			$this->setManufacturerID($inManufacturerID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new wurflManufacturer containing non-unique properties
	 * 
	 * @param string $inDescription
	 * @param integer $inActive
	 * @return wurflManufacturer
	 * @static 
	 */
	public static function factory($inDescription = null, $inActive = null) {
		$oObject = new wurflManufacturer;
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inActive !== null ) {
			$oObject->setActive($inActive);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of wurflManufacturer by primary key
	 * 
	 * @param integer $inManufacturerID
	 * @return wurflManufacturer
	 * @static 
	 */
	public static function getInstance($inManufacturerID) {
		/**
		 * Check for an existing instance
		 */
		if ( is_numeric($inManufacturerID) ) {
			if ( isset(self::$_Instances[$inManufacturerID]) ) {
				return self::$_Instances[$inManufacturerID];
			}
		} elseif ( is_string($inManufacturerID) && strlen($inManufacturerID) > 1 ) {
			foreach ( self::$_Instances as $oObject ) {
				if ( $oObject->getDescription() == $inManufacturerID ) {
					return $oObject;
				}
			}
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new wurflManufacturer();
		if ( is_numeric($inManufacturerID) ) {
			$oObject->setManufacturerID($inManufacturerID);
		} else {
			$oObject->setDescription($inManufacturerID);
		}
		if ( $oObject->load() ) {
			self::$_Instances[$inManufacturerID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
			
	/**
	 * Returns an array of objects of wurflManufacturer, if $inActive is true, gets only active manufacturers
	 * 
	 * @param boolean $inActive
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inActive = false, $inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.manufacturers';
		if ( $inActive ) {
			$query .= ' WHERE active = 1 ';
		}
		$query .= ' ORDER BY description ASC';
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new wurflManufacturer();
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
		if ( $this->_ManufacturerID !== 0 || $this->_Description !== '' ) {
			$query = 'SELECT * FROM '.system::getConfig()->getDatabase('wurfl').'.manufacturers';
			
			$where = array();
			if ( $this->_ManufacturerID !== 0 ) {
				$where[] = ' manufacturerID = :ManufacturerID ';
			}
			if ( $this->_Description !== '' ) {
				$where[] = ' description = :Description ';
			}
							
			if ( count($where) > 0 ) {
				$query .= ' WHERE '.implode(' AND ', $where);
			}
	
			try {
				$oStmt = dbManager::getInstance()->prepare($query);
				if ( $this->_ManufacturerID !== 0 ) {
					$oStmt->bindValue(':ManufacturerID', $this->_ManufacturerID);
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
		$this->setManufacturerID((int)$inArray['manufacturerID']);
		$this->setDescription($inArray['description']);
		$this->setActive($inArray['active']);
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
				INSERT INTO '.system::getConfig()->getDatabase('wurfl').'.manufacturers
					( manufacturerID, description, active)
				VALUES 
					(:ManufacturerID, :Description, :Active)
				ON DUPLICATE KEY UPDATE
					description=VALUES(description),
					active=VALUES(active)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ManufacturerID', $this->_ManufacturerID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':Active', $this->_Active);
								
					if ( $oStmt->execute() ) {
						if ( !$this->getManufacturerID() ) {
							$this->setManufacturerID($oDB->lastInsertId());
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
		DELETE FROM '.system::getConfig()->getDatabase('wurfl').'.manufacturers
		WHERE
			manufacturerID = :ManufacturerID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ManufacturerID', $this->_ManufacturerID);
				
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
	 * @return wurflManufacturer
	 */
	function reset() {
		$this->_ManufacturerID = 0;
		$this->_Description = '';
		$this->_Active = 0;
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
		$string .= " ManufacturerID[$this->_ManufacturerID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " Active[$this->_Active] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'wurflManufacturer';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ManufacturerID\" value=\"$this->_ManufacturerID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Active\" value=\"$this->_Active\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkManufacturerID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkActive($message);
		}
		return $valid;
	}
		
	/**
	 * Checks that $_ManufacturerID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkManufacturerID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ManufacturerID) && $this->_ManufacturerID !== 0 ) {
			$inMessage .= "{$this->_ManufacturerID} is not a valid value for ManufacturerID\n";
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
	 * Checks that $_Active has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkActive(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Active) && $this->_Active !== 0 ) {
			$inMessage .= "{$this->_Active} is not a valid value for Active\n";
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
	 * @return wurflManufacturer
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
		return $this->_ManufacturerID;
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
	 * Set $_ManufacturerID to ManufacturerID
	 * 
	 * @param integer $inManufacturerID
	 * @return wurflManufacturer
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
	 * @return wurflManufacturer
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
	 * Return value of $_Active
	 * 
	 * @return integer
	 * @access public
	 */
	function getActive() {
		return $this->_Active;
	}
	
	/**
	 * Set $_Active to Active
	 * 
	 * @param integer $inActive
	 * @return wurflManufacturer
	 * @access public
	 */
	function setActive($inActive) {
		if ( $inActive !== $this->_Active ) {
			$this->_Active = $inActive;
			$this->setModified();
		}
		return $this;
	}
}