<?php
/**
 * mofilmPermissionGroup
 * 
 * Stored in mofilmPermissionGroup.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmPermissionGroup
 * @category mofilmPermissionGroup
 * @version $Rev: 10 $
 */


/**
 * mofilmPermissionGroup Class
 * 
 * Provides access to records in mofilm_content.permissionGroups
 * 
 * Creating a new record:
 * <code>
 * $oMofilmPermissionGroup = new mofilmPermissionGroup();
 * $oMofilmPermissionGroup->setID($inID);
 * $oMofilmPermissionGroup->setDescription($inDescription);
 * $oMofilmPermissionGroup->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmPermissionGroup = new mofilmPermissionGroup($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmPermissionGroup = new mofilmPermissionGroup();
 * $oMofilmPermissionGroup->setID($inID);
 * $oMofilmPermissionGroup->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmPermissionGroup = mofilmPermissionGroup::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmPermissionGroup
 * @category mofilmPermissionGroup
 */
class mofilmPermissionGroup implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmPermissionGroup
	 * 
	 * @var array
	 * @access protected
	 * @static 
	 */
	protected static $_Instances = array();
	
	/**
	 * An array of "public" groups that can be seen by other clients
	 * 
	 * @var array
	 * @access public
	 * @static
	 */
	public static $publicGroups = array(3, 4);
	
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
	 * Stores $_Description
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Description;
	
	/**
	 * Stores $_Namespace
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Namespace;
	
	/**
	 * Stores an instance of mofilmPermissionGroupPermissions
	 *
	 * @var mofilmPermissionGroupPermissions
	 * @access protected
	 */
	protected $_Permissions;
			
	
	
	/**
	 * Returns a new instance of mofilmPermissionGroup
	 * 
	 * @param integer $inID
	 * @return mofilmPermissionGroup
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
	 * Creates a new mofilmPermissionGroup containing non-unique properties
	 * 
	 * @param string $inDescription
	 * @return mofilmPermissionGroup
	 * @static 
	 */
	public static function factory($inDescription = null) {
		$oObject = new mofilmPermissionGroup;
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmPermissionGroup by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmPermissionGroup
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
		$oObject = new mofilmPermissionGroup();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
	
	/**
	 * Attempts to locate a group from the supplied user ID
	 * 
	 * Note: this will return an object with either an id of -1 i.e. no
	 * group at all (not an admin user), 0 - a root user or an ID > 0
	 * that corresponds to an actual group.
	 * 
	 * @param integer $inUserID
	 * @return mofilmPermissionGroup
	 * @static
	 */
	public static function getPermissionGroupFromUser($inUserID) {
		$oGroup = new mofilmPermissionGroup();
		$oGroup->setID(-1);
		
		if ( is_numeric($inUserID) && $inUserID> 0 ) {
			$query = '
				SELECT IF(permissionID = 1, 0, SUBSTRING(permissionID, 2)) as groupID
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPermissions
				 WHERE userID = :UserID
				   AND (permissionID LIKE :Permission OR permissionID = 1)
				 LIMIT 1';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $inUserID, PDO::PARAM_INT);
			$oStmt->bindValue(':Permission', 'G%', PDO::PARAM_STR);
			if ( $oStmt->execute() ) {
				$res = $oStmt->fetchAll();
				if ( is_array($res) && isset($res[0]) && count($res) == 1 ) {
					$groupID = (int) $res[0]['groupID'];
					if ( $groupID === 0 ) {
						/*
						 * For root users, create a pseudo group
						 */
						$oGroup = new mofilmPermissionGroup();
						$oGroup->setID(0);
						$oGroup->setDescription('Root');
					} elseif ( $groupID > 0 ) {
						$oGroup = self::getInstance($groupID);
					}
				}
			}
			$oStmt->closeCursor();
		}
		return $oGroup;
	}
				
	/**
	 * Returns an array of objects of mofilmPermissionGroup
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.permissionGroups';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmPermissionGroup();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.permissionGroups';
		
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.permissionGroups
					( ID, description)
				VALUES 
					(:ID, :Description)
				ON DUPLICATE KEY UPDATE
					description=VALUES(description)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
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
			
			if ( $this->_Permissions instanceof mofilmPermissionGroupPermissions ) {
				$this->_Permissions->setGroupID($this->getID());
				$return = $this->_Permissions->save() && $return;
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.permissionGroups
		WHERE
			ID = :ID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ID', $this->_ID);
				
			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				
				/*
				 * Clear user references to this group
				 */
				$query = 'DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPermissions WHERE permissionID = :GroupID';
				$oStmt = dbManager::getInstance()->prepare($query);
				$oStmt->bindValue(':GroupID', 'G'.$this->getID());
				$oStmt->execute();
				$oStmt->closeCursor();
				
				$this->getPermissions()->delete();
				
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
	 * @return mofilmPermissionGroup
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Description = '';
		$this->_Namespace = null;
		$this->_Permissions = null;
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
		$className = 'mofilmPermissionGroup';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
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
		if ( $isValid && strlen($this->_Description) > 50 ) {
			$inMessage .= "Description cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
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
		if ( !$modified && $this->_Permissions !== null ) {
			$modified = $modified || $this->_Permissions->isModified();
		}
		return $modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return mofilmPermissionGroup
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
	 * @return mofilmPermissionGroup
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
	 * @return mofilmPermissionGroup
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
	 * Returns $_Namespace
	 *
	 * @return string
	 */
	function getNamespace() {
		return $this->_Namespace;
	}
	
	/**
	 * Set $_Namespace to $inNamespace
	 *
	 * @param string $inNamespace
	 * @return mofilmPermissionGroup
	 */
	function setNamespace($inNamespace) {
		if ( $inNamespace !== $this->_Namespace ) {
			$this->_Namespace = $inNamespace;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns an instance of mofilmPermissionGroupPermissions, which is lazy loaded upon request
	 *
	 * @return mofilmPermissionGroupPermissions
	 */
	function getPermissions() {
		if ( !$this->_Permissions instanceof mofilmPermissionGroupPermissions ) {
			$this->_Permissions = new mofilmPermissionGroupPermissions($this->getID(), $this->getNamespace());
			if ( $this->getID() > 0 ) {
				$this->_Permissions->load();
			}
		}
		return $this->_Permissions;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param mofilmPermissionGroupPermissions $inObject
	 * @return mofilmPermissionGroup
	 */
	function setPermissions(mofilmPermissionGroupPermissions $inObject) {
		$this->_Permissions = $inObject;
		return $this;
	}
}