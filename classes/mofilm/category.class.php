<?php
/**
 * mofilmCategory
 * 
 * Stored in mofilmCategory.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCategory
 * @category mofilmCategory
 * @version $Rev: 10 $
 */


/**
 * mofilmCategory Class
 * 
 * Provides access to records in mofilm_content.categories
 * 
 * Creating a new record:
 * <code>
 * $oMofilmCategory = new mofilmCategory();
 * $oMofilmCategory->setID($inID);
 * $oMofilmCategory->setSourceID($inSourceID);
 * $oMofilmCategory->setDescription($inDescription);
 * $oMofilmCategory->setExclusive($inExclusive);
 * $oMofilmCategory->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCategory = new mofilmCategory($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmCategory = new mofilmCategory();
 * $oMofilmCategory->setID($inID);
 * $oMofilmCategory->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmCategory = mofilmCategory::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmCategory
 * @category mofilmCategory
 */
class mofilmCategory implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmCategory
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
	 * Stores $_SourceID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_SourceID;
			
	/**
	 * Stores $_Description
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Description;
			
	/**
	 * Stores $_Exclusive
	 * 
	 * @var string (EXCLUSIVE_Y,EXCLUSIVE_N,)
	 * @access protected
	 */
	protected $_Exclusive;
	const EXCLUSIVE_Y = 'Y';
	const EXCLUSIVE_N = 'N';
				
	
	
	/**
	 * Returns a new instance of mofilmCategory
	 * 
	 * @param integer $inID
	 * @return mofilmCategory
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
	 * Creates a new mofilmCategory containing non-unique properties
	 * 
	 * @param integer $inSourceID
	 * @param string $inDescription
	 * @param string $inExclusive
	 * @return mofilmCategory
	 * @static 
	 */
	public static function factory($inSourceID = null, $inDescription = null, $inExclusive = null) {
		$oObject = new mofilmCategory;
		if ( $inSourceID !== null ) {
			$oObject->setSourceID($inSourceID);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inExclusive !== null ) {
			$oObject->setExclusive($inExclusive);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmCategory by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmCategory
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
		$oObject = new mofilmCategory();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmCategory
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.categories';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmCategory();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.categories';
		
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
		$this->setSourceID((int)$inArray['sourceID']);
		$this->setDescription($inArray['description']);
		$this->setExclusive($inArray['exclusive']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.categories
					( ID, sourceID, description, exclusive)
				VALUES 
					(:ID, :SourceID, :Description, :Exclusive)
				ON DUPLICATE KEY UPDATE
					sourceID=VALUES(sourceID),
					description=VALUES(description),
					exclusive=VALUES(exclusive)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':SourceID', $this->_SourceID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':Exclusive', $this->_Exclusive);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.categories
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
	 * @return mofilmCategory
	 */
	function reset() {
		$this->_ID = 0;
		$this->_SourceID = 0;
		$this->_Description = '';
		$this->_Exclusive = 'N';
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
		$string .= " SourceID[$this->_SourceID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " Exclusive[$this->_Exclusive] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmCategory';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"SourceID\" value=\"$this->_SourceID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Exclusive\" value=\"$this->_Exclusive\" type=\"string\" /> $newLine";
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
			$valid = $this->checkSourceID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkExclusive($message);
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
	 * Checks that $_SourceID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkSourceID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_SourceID) && $this->_SourceID !== 0 ) {
			$inMessage .= "{$this->_SourceID} is not a valid value for SourceID";
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
	 * Checks that $_Exclusive has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkExclusive(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Exclusive) && $this->_Exclusive !== '' ) {
			$inMessage .= "{$this->_Exclusive} is not a valid value for Exclusive";
			$isValid = false;
		}		
		if ( $isValid && $this->_Exclusive != '' && !in_array($this->_Exclusive, array(self::EXCLUSIVE_Y, self::EXCLUSIVE_N)) ) {
			$inMessage .= "Exclusive must be one of EXCLUSIVE_Y, EXCLUSIVE_N";
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
	 * @return mofilmCategory
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
	 * @return mofilmCategory
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
	 * Return value of $_SourceID
	 * 
	 * @return integer
	 * @access public
	 */
	function getSourceID() {
		return $this->_SourceID;
	}
	
	/**
	 * Returns the mofilmSource object
	 * 
	 * @return mofilmSource
	 */
	function getSource() {
		return mofilmSource::getInstance($this->getSourceID());
	}
	
	/**
	 * Set $_SourceID to SourceID
	 * 
	 * @param integer $inSourceID
	 * @return mofilmCategory
	 * @access public
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
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
	 * @return mofilmCategory
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
	 * Return value of $_Exclusive
	 * 
	 * @return string
	 * @access public
	 */
	function getExclusive() {
		return $this->_Exclusive;
	}
	
	/**
	 * Set $_Exclusive to Exclusive
	 * 
	 * @param string $inExclusive
	 * @return mofilmCategory
	 * @access public
	 */
	function setExclusive($inExclusive) {
		if ( $inExclusive !== $this->_Exclusive ) {
			$this->_Exclusive = $inExclusive;
			$this->setModified();
		}
		return $this;
	}
}