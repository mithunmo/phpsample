<?php
/**
 * mofilmMovieContributorMap
 * 
 * Stored in mofilmMovieContributorMap.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm_content
 * @subpackage mofilmMovieContributorMap
 * @category mofilmMovieContributorMap
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieContributorMap Class
 * 
 * Provides access to records in mofilm_content.movieContributors
 * 
 * Creating a new record:
 * <code>
 * $oMofilmMovieContributorMap = new mofilmMovieContributorMap();
 * $oMofilmMovieContributorMap->setMovieID($inMovieID);
 * $oMofilmMovieContributorMap->setContributorID($inContributorID);
 * $oMofilmMovieContributorMap->setRoleID($inRoleID);
 * $oMofilmMovieContributorMap->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMovieContributorMap = new mofilmMovieContributorMap($inMovieID, $inContributorID, $inRoleID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmMovieContributorMap = new mofilmMovieContributorMap();
 * $oMofilmMovieContributorMap->setMovieID($inMovieID);
 * $oMofilmMovieContributorMap->setContributorID($inContributorID);
 * $oMofilmMovieContributorMap->setRoleID($inRoleID);
 * $oMofilmMovieContributorMap->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmMovieContributorMap = mofilmMovieContributorMap::getInstance($inMovieID, $inContributorID, $inRoleID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm_content
 * @subpackage mofilmMovieContributorMap
 * @category mofilmMovieContributorMap
 */
class mofilmMovieContributorMap implements systemDaoInterface {
	
	/**
	 * Container for static instances of mofilmMovieContributorMap
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
	 * Stores $_MovieID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;
			
	/**
	 * Stores $_ContributorID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_ContributorID;
				
	/**
	 * Stores $_RoleID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_RoleID;
	
	/**
	 * Stores $_MarkForDeletion
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion;
	
	/**
	 * Stores $_Contributor
	 *
	 * @var mofilmContributor
	 * @access protected
	 */
	protected $_Contributor;
	
	/**
	 * Stores $_Role
	 *
	 * @var mofilmRole
	 * @access protected
	 */
	protected $_Role;
	
	
	
	/**
	 * Returns a new instance of mofilmMovieContributorMap
	 * 
	 * @param integer $inMovieID
	 * @param integer $inContributorID
	 * @param integer $inRoleID
	 * @return mofilmMovieContributorMap
	 */
	function __construct($inMovieID = null, $inContributorID = null, $inRoleID = null) {
		$this->reset();
		if ( $inMovieID !== null && $inContributorID !== null && $inRoleID !== null ) {
			$this->setMovieID($inMovieID);
			$this->setContributorID($inContributorID);
			$this->setRoleID($inRoleID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Custom destructor to unload contributor object
	 *
	 * @return void
	 */
	function __destruct() {
		unset($this->_Contributor, $this->_Role);
	}
	
	/**
	 * Creates a new mofilmMovieContributorMap containing non-unique properties
	 * 
	 * @param mofilmContributor $inContributor
	 * @param mofilmRole $inRole
	 * @return mofilmMovieContributorMap
	 * @static 
	 */
	public static function factory(mofilmContributor $inContributor, mofilmRole $inRole) {
		$oObject = new mofilmMovieContributorMap();
		$oObject->setContributor($inContributor);
		$oObject->setRole($inRole);
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmMovieContributorMap by primary key
	 * 
	 * @param integer $inMovieID
	 * @param integer $inContributorID
	 * @param integer $inRoleID
	 * @return mofilmMovieContributorMap
	 * @static 
	 */
	public static function getInstance($inMovieID, $inContributorID, $inRoleID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMovieID.'.'.$inContributorID.'.'.$inRoleID]) ) {
			return self::$_Instances[$inMovieID.'.'.$inContributorID.'.'.$inRoleID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmMovieContributorMap();
		$oObject->setMovieID($inMovieID);
		$oObject->setContributorID($inContributorID);
		$oObject->setRoleID($inRoleID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMovieID.'.'.$inContributorID.'.'.$inRoleID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmMovieContributorMap
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inMovieID = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieContributors';
		if ( $inMovieID !== null ) {
			$query .= ' WHERE movieID = :MovieID ';
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $inMovieID !== null ) {
				$oStmt->bindValue(':MovieID', $inMovieID, PDO::PARAM_INT);
			}
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMovieContributorMap();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieContributors';
		
		$where = array();
		if ( $this->_MovieID !== 0 ) {
			$where[] = ' movieID = :MovieID ';
		}
		if ( $this->_ContributorID !== 0 ) {
			$where[] = ' contributorID = :ContributorID ';
		}		
		if ( $this->_RoleID !== 0 ) {
			$where[] = ' roleID = :RoleID ';
		}
						
		if ( count($where) == 0 ) {
			return false;
		}
		
		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_MovieID !== 0 ) {
				$oStmt->bindValue(':MovieID', $this->_MovieID);
			}
			if ( $this->_ContributorID !== 0 ) {
				$oStmt->bindValue(':ContributorID', $this->_ContributorID);
			}			 
			if ( $this->_RoleID !== 0 ) {
				$oStmt->bindValue(':RoleID', $this->_RoleID);
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
		$this->setMovieID((int)$inArray['movieID']);
		$this->setContributorID((int)$inArray['contributorID']);
		$this->setRoleID((int)$inArray['roleID']);
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
			if ( $this->_Contributor !== null ) {
				$this->_Contributor->save();				
				if ( $this->_ContributorID != $this->_Contributor->getID() ) {
					$this->_ContributorID = $this->_Contributor->getID();
					$this->setModified();
				}
			}
			if ( $this->_Role !== null ) {
				$this->_Role->save();
				if ( $this->_RoleID != $this->_Role->getID() ) {
					$this->_RoleID = $this->_Role->getID();
					$this->setModified();
				}
			}
			
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieContributors
					( movieID, contributorID, roleID)
				VALUES 
					(:MovieID, :ContributorID, :RoleID)
				ON DUPLICATE KEY UPDATE
					roleID=VALUES(roleID)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MovieID', $this->_MovieID);
					$oStmt->bindValue(':ContributorID', $this->_ContributorID);
					$oStmt->bindValue(':RoleID', $this->_RoleID);
					
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieContributors
		WHERE
			movieID = :MovieID AND 
			contributorID = :ContributorID AND 
			roleID = :RoleID 
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $this->_MovieID);
			$oStmt->bindValue(':ContributorID', $this->_ContributorID);
			$oStmt->bindValue(':RoleID', $this->_RoleID);
				
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
	 * @return mofilmMovieContributorMap
	 */
	function reset() {
		$this->_MovieID = 0;
		$this->_ContributorID = 0;
		$this->_RoleID = 0;
		$this->_MarkForDeletion = false;
		$this->_Contributor = null;
		$this->_Role = null;
		$this->setModified(false);
		return $this;
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
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( !$modified && $this->_Contributor !== null ) {
			$modified = $this->_Contributor->isModified() || $modified;
		}
		if ( !$modified && $this->_Role !== null ) {
			$modified = $this->_Role->isModified() || $modified;
		}
		return $modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return mofilmMovieContributorMap
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
		return $this->_MovieID.'.'.$this->_ContributorID.'.'.$this->_RoleID;
	}
		
	/**
	 * Return value of $_MovieID
	 * 
	 * @return integer
	 * @access public
	 */
	function getMovieID() {
		return $this->_MovieID;
	}
	
	/**
	 * Set $_MovieID to MovieID
	 * 
	 * @param integer $inMovieID
	 * @return mofilmMovieContributorMap
	 * @access public
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_ContributorID
	 * 
	 * @return integer
	 * @access public
	 */
	function getContributorID() {
		return $this->_ContributorID;
	}
	
	/**
	 * Set $_ContributorID to ContributorID
	 * 
	 * @param integer $inContributorID
	 * @return mofilmMovieContributorMap
	 * @access public
	 */
	function setContributorID($inContributorID) {
		if ( $inContributorID !== $this->_ContributorID ) {
			$this->_ContributorID = $inContributorID;
			$this->_Contributor = null;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Contributor
	 *
	 * @return mofilmContributor
	 */
	function getContributor() {
		if ( !$this->_Contributor instanceof mofilmContributor ) {
			$this->_Contributor = mofilmContributor::getInstance($this->getContributorID());
		}
		return $this->_Contributor;
	}
	
	/**
	 * Set $_Contributor to $inContributor
	 *
	 * @param mofilmContributor $inContributor
	 * @return mofilmMovieContributorMap
	 */
	function setContributor($inContributor) {
		if ( $inContributor !== $this->_Contributor ) {
			$this->_Contributor = $inContributor;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_RoleID
	 * 
	 * @return integer
	 * @access public
	 */
	function getRoleID() {
		return $this->_RoleID;
	}
	
	/**
	 * Set $_RoleID to RoleID
	 * 
	 * @param integer $inRoleID
	 * @return mofilmMovieContributorMap
	 * @access public
	 */
	function setRoleID($inRoleID) {
		if ( $inRoleID !== $this->_RoleID ) {
			$this->_RoleID = $inRoleID;
			$this->_Role = null;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Role
	 *
	 * @return mofilmRole
	 */
	function getRole() {
		if ( !$this->_Role instanceof mofilmRole ) {
			$this->_Role = mofilmRole::getInstance($this->getRoleID());
		}
		return $this->_Role;
	}
	
	/**
	 * Set $_Role to $inRole
	 *
	 * @param mofilmRole $inRole
	 * @return mofilmMovieContributorMap
	 */
	function setRole(mofilmRole $inRole) {
		if ( $inRole !== $this->_Role ) {
			$this->_Role = $inRole;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Returns $_MarkForDeletion
	 *
	 * @return boolean
	 */
	function getMarkForDeletion() {
		return $this->_MarkForDeletion;
	}
	
	/**
	 * Set $_MarkForDeletion to $inMarkForDeletion
	 *
	 * @param boolean $inMarkForDeletion
	 * @return mofilmMovieContributorMap
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
			$this->setModified();
		}
		return $this;
	}
}