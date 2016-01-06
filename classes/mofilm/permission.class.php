<?php
/**
 * mofilmPermission
 * 
 * Stored in mofilmPermission.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmPermission
 * @category mofilmPermission
 * @version $Rev: 10 $
 */


/**
 * mofilmPermission Class
 * 
 * Provides access to records in mofilm_content.permissions
 * 
 * Creating a new record:
 * <code>
 * $oMofilmPermission = new mofilmPermission();
 * $oMofilmPermission->setID($inID);
 * $oMofilmPermission->setName($inName);
 * $oMofilmPermission->setDescription($inDescription);
 * $oMofilmPermission->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmPermission = new mofilmPermission($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmPermission = new mofilmPermission();
 * $oMofilmPermission->setID($inID);
 * $oMofilmPermission->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmPermission = mofilmPermission::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmPermission
 * @category mofilmPermission
 */
class mofilmPermission implements systemDaoInterface, systemDaoValidatorInterface {
	
	const MATCH_LIKE = 'LIKE';
	const MATCH_NOT_LIKE = 'NOT LIKE';
	const MATCH_EQUAL = '=';
	
	/**
	 * Container for static instances of mofilmPermission
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
	 * Stores $_Name
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Name;
			
	/**
	 * Stores $_Description
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Description;
			
	
	
	/**
	 * Returns a new instance of mofilmPermission
	 * 
	 * @param integer $inID
	 * @return mofilmPermission
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
	 * Creates a new mofilmPermission containing non-unique properties
	 * 
	 * @param string $inName
	 * @param string $inDescription
	 * @return mofilmPermission
	 * @static 
	 */
	public static function factory($inName = null, $inDescription = null) {
		$oObject = new mofilmPermission;
		if ( $inName !== null ) {
			$oObject->setName($inName);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmPermission by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmPermission
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
		$oObject = new mofilmPermission();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmPermission by permission
	 *
	 * @param string $inPermission
	 * @return mofilmPermission
	 * @static
	 */
	public static function getInstanceByPermission($inPermission) {
		/**
		 * Check for an existing instance
		 */
		if ( count(self::$_Instances) > 0 ) {
			foreach ( self::$_Instances as $oPermission ) {
				if ( $oPermission->getName() == $inPermission ) {
					return $oPermission;
				}
			}
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmPermission();
		$oObject->setName($inPermission);
		if ( $oObject->load() ) {
			self::$_Instances[$oObject->getID()] = $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmPermission
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param string $inKeyword (optional)
	 * @param string $inMatchType (optional) defaults to MATCH_LIKE
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inKeyword = null, $inMatchType = self::MATCH_LIKE) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.permissions';
		
		if ( $inKeyword !== null && strlen($inKeyword) > 0 ) {
			if ( !in_array($inMatchType, array(self::MATCH_EQUAL, self::MATCH_LIKE, self::MATCH_NOT_LIKE)) ) {
				$inMatchType = self::MATCH_LIKE;
			}
			$query .= ' WHERE name '.$inMatchType.' '.dbManager::getInstance()->quote($inKeyword);
		}
		
		$query .= ' ORDER BY name ASC';
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmPermission();
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
	 * Gets permissions matching $inControllerName
	 *
	 * @param string $inControllerName
	 * @return array(mofilmPermission)
	 */
	static function getControllerPermissions($inControllerName) {
		if ( strpos($inControllerName, 'Controller') === false ) {
			$inControllerName .= 'Controller';
		}
		
		return mofilmPermission::listOfObjects(null, null, '%'.$inControllerName.'%');
	}
	
	
	
	/**
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.permissions';
		
		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		if ( $this->_Name !== '' ) {
			$where[] = ' name = :Permission ';
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
			if ( $this->_Name !== '' ) {
				$oStmt->bindValue(':Permission', $this->_Name);
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
		$this->setName($inArray['name']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.permissions
					( ID, name, description)
				VALUES 
					(:ID, :Name, :Description)
				ON DUPLICATE KEY UPDATE
					name=VALUES(name),
					description=VALUES(description)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Name', $this->_Name);
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.permissions
		WHERE
			ID = :ID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ID', $this->_ID);
				
			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				
				/*
				 * Clean up users and group permissions
				 */
				$queries = array();
				$queries[] = 'DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPermissions WHERE permissionID = :PermissionID';
				$queries[] = 'DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.permissionGroupLinks WHERE permissionID = :PermissionID';
				
				foreach ( $queries as $query ) {
					$oStmt = dbManager::getInstance()->prepare($query);
					$oStmt->bindValue(':PermissionID', $this->getID());
					$oStmt->execute();
					$oStmt->closeCursor();
					unset($oStmt);
				}
				
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
	 * @return mofilmPermission
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Name = '';
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
		$string .= " Name[$this->_Name] $newLine";
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
		$className = 'mofilmPermission';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Name\" value=\"$this->_Name\" type=\"string\" /> $newLine";
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
			$valid = $this->checkName($message);
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
	 * Checks that $_Name has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkName(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Name) && $this->_Name !== '' ) {
			$inMessage .= "{$this->_Name} is not a valid value for Name";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_Name) > 60 ) {
			$inMessage .= "Name cannot be more than 60 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Name) <= 1 ) {
			$inMessage .= "Name must be more than 1 character";
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
	 * @return mofilmPermission
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
	 * @return mofilmPermission
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
	 * Return value of $_Name
	 * 
	 * @return string
	 * @access public
	 */
	function getName() {
		return $this->_Name;
	}
	
	/**
	 * Set $_Name to Name
	 * 
	 * @param string $inName
	 * @return mofilmPermission
	 * @access public
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
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
	 * @return mofilmPermission
	 * @access public
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}
}