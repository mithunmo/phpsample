<?php
/**
 * mofilmTerritory
 * 
 * Stored in mofilmTerritory.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmTerritory
 * @category mofilmTerritory
 * @version $Rev: 10 $
 */


/**
 * mofilmTerritory Class
 * 
 * Provides access to records in mofilm_content.territories
 * 
 * Creating a new record:
 * <code>
 * $oMofilmTerritory = new mofilmTerritory();
 * $oMofilmTerritory->setID($inID);
 * $oMofilmTerritory->setCountry($inCountry);
 * $oMofilmTerritory->setShortName($inShortName);
 * $oMofilmTerritory->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmTerritory = new mofilmTerritory($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmTerritory = new mofilmTerritory();
 * $oMofilmTerritory->setID($inID);
 * $oMofilmTerritory->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmTerritory = mofilmTerritory::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmTerritory
 * @category mofilmTerritory
 */
class mofilmTerritory implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmTerritory
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
	 * Stores $_Country
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Country;
			
	/**
	 * Stores $_ShortName
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_ShortName;
	
	/**
	 * Stores an instance of mofilmTerritoryLanguageSet
	 *
	 * @var mofilmTerritoryLanguageSet
	 * @access protected
	 */
	protected $_LanguageSet;
	
	/**
	 * Stores an instance of mofilmTerritoryStateSet
	 *
	 * @var mofilmTerritoryStateSet
	 * @access protected
	 */
	protected $_StateSet;
			
	
	
	/**
	 * Returns a new instance of mofilmTerritory
	 * 
	 * @param integer $inID
	 * @return mofilmTerritory
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
	 * Creates a new mofilmTerritory containing non-unique properties
	 * 
	 * @param string $inCountry
	 * @return mofilmTerritory
	 * @static 
	 */
	public static function factory($inCountry = null) {
		$oObject = new mofilmTerritory;
		if ( $inCountry !== null ) {
			$oObject->setCountry($inCountry);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmTerritory by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmTerritory
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
		$oObject = new mofilmTerritory();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
		
	/**
	 * Get instance of mofilmTerritory by unique key (shortName)
	 * 
	 * @param string $inShortName
	 * @return mofilmTerritory
	 * @static
	 */
	public static function getInstanceByShortName($inShortName) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inShortName]) ) {
			return self::$_Instances[$inShortName];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmTerritory();
		$oObject->setShortName($inShortName);
		if ( $oObject->load() ) {
			self::$_Instances[$inShortName] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
			
	/**
	 * Returns an array of objects of mofilmTerritory
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.territories';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmTerritory();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.territories';
		
		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		if ( $this->_ShortName !== '' ) {
			$where[] = ' shortName = :ShortName ';
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
			if ( $this->_ShortName !== '' ) {
				$oStmt->bindValue(':ShortName', $this->_ShortName);
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
		$this->setCountry($inArray['country']);
		$this->setShortName($inArray['shortName']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.territories
					( ID, country, shortName)
				VALUES 
					(:ID, :Country, :ShortName)
				ON DUPLICATE KEY UPDATE
					country=VALUES(country)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Country', $this->_Country);
					$oStmt->bindValue(':ShortName', $this->_ShortName);
								
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
			
			if ( $this->_LanguageSet instanceof mofilmTerritoryLanguageSet ) {
				$this->_LanguageSet->setTerritoryID($this->getID());
				$this->_LanguageSet->save();
			}
			if ( $this->_StateSet instanceof mofilmTerritoryStateSet ) {
				$this->_StateSet->setTerritoryID($this->getID());
				$this->_StateSet->save();
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.territories
		WHERE
			ID = :ID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ID', $this->_ID);
				
			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				
				$this->getLanguageSet()->delete();
				$this->getStateSet()->delete();
				
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
	 * @return mofilmTerritory
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Country = '';
		$this->_ShortName = '';
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
		$string .= " Country[$this->_Country] $newLine";
		$string .= " ShortName[$this->_ShortName] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmTerritory';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Country\" value=\"$this->_Country\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"ShortName\" value=\"$this->_ShortName\" type=\"string\" /> $newLine";
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
			$valid = $this->checkCountry($message);
		}
		if ( $valid ) {
			$valid = $this->checkShortName($message);
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
	 * Checks that $_Country has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkCountry(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Country) && $this->_Country !== '' ) {
			$inMessage .= "{$this->_Country} is not a valid value for Country";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_Country) > 40 ) {
			$inMessage .= "Country cannot be more than 40 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Country) <= 1 ) {
			$inMessage .= "Country must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_ShortName has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkShortName(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ShortName) && $this->_ShortName !== '' ) {
			$inMessage .= "{$this->_ShortName} is not a valid value for ShortName";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_ShortName) > 2 ) {
			$inMessage .= "ShortName cannot be more than 2 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_ShortName) <= 1 ) {
			$inMessage .= "ShortName must be more than 1 character";
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
		if ( !$modified && $this->_LanguageSet !== null ) {
			$modified = $modified || $this->_LanguageSet->isModified();
		}
		if ( !$modified && $this->_StateSet !== null ) {
			$modified = $modified || $this->_StateSet->isModified();
		}
		return $modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return mofilmTerritory
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
	 * @return mofilmTerritory
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
	 * Return value of $_Country
	 * 
	 * @return string
	 * @access public
	 */
	function getCountry() {
		return utilityStringFunction::capitaliseEncodedString($this->_Country);
	}
	
	/**
	 * Set $_Country to Country
	 * 
	 * @param string $inCountry
	 * @return mofilmTerritory
	 * @access public
	 */
	function setCountry($inCountry) {
		if ( $inCountry !== $this->_Country ) {
			$this->_Country = $inCountry;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_ShortName
	 * 
	 * @return string
	 * @access public
	 */
	function getShortName() {
		return $this->_ShortName;
	}
	
	/**
	 * Set $_ShortName to ShortName
	 * 
	 * @param string $inShortName
	 * @return mofilmTerritory
	 * @access public
	 */
	function setShortName($inShortName) {
		if ( $inShortName !== $this->_ShortName ) {
			$this->_ShortName = $inShortName;
			$this->setModified();
		}
		return $this;
	}
	
	

	/**
	 * Returns an instance of mofilmTerritoryLanguageSet, which is lazy loaded upon request
	 *
	 * @return mofilmTerritoryLanguageSet
	 */
	function getLanguageSet() {
		if ( !$this->_LanguageSet instanceof mofilmTerritoryLanguageSet ) {
			$this->_LanguageSet = new mofilmTerritoryLanguageSet($this->getID());
		}
		return $this->_LanguageSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param mofilmTerritoryLanguageSet $inObject
	 * @return mofilmTerritory
	 */
	function setLanguageSet(mofilmTerritoryLanguageSet $inObject) {
		$this->_LanguageSet = $inObject;
		return $this;
	}

	/**
	 * Returns an instance of mofilmTerritoryStateSet, which is lazy loaded upon request
	 *
	 * @return mofilmTerritoryStateSet
	 */
	function getStateSet() {
		if ( !$this->_StateSet instanceof mofilmTerritoryStateSet ) {
			$this->_StateSet = new mofilmTerritoryStateSet($this->getID());
		}
		return $this->_StateSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param mofilmTerritoryStateSet $inObject
	 * @return mofilmTerritory
	 */
	function setStateSet(mofilmTerritoryStateSet $inObject) {
		$this->_StateSet = $inObject;
		return $this;
	}
}