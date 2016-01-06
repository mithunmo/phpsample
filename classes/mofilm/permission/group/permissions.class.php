<?php
/**
 * mofilmPermissionGroupPermissions
 * 
 * Stored in mofilmPermissionGroupPermissions.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmPermissionGroupPermissions
 * @category mofilmPermissionGroupPermissions
 * @version $Rev: 10 $
 */


/**
 * mofilmPermissionGroupPermissions Class
 * 
 * Maps permissions to a group and allows them to be modified.
 * 
 * @package mofilm
 * @subpackage mofilmPermissionGroupPermissions
 * @category mofilmPermissionGroupPermissions
 */
class mofilmPermissionGroupPermissions extends baseSet implements systemDaoInterface {
		
	/**
	 * Stores $_GroupID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_GroupID;
	
	/**
	 * Stores $_Namespace
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Namespace;
			
	
	
	/**
	 * Returns a new instance of mofilmPermissionGroupPermissions
	 * 
	 * @param integer $inGroupID
	 * @param string $inNamespace
	 * @return mofilmPermissionGroupPermissions
	 */
	function __construct($inGroupID = null, $inNamespace = null) {
		$this->reset();
		if ( $inGroupID !== null ) {
			$this->setGroupID($inGroupID);
		}
		if ( $inNamespace !== null ) {
			$this->setNamespace($inNamespace);
		}
	}
	
	
	
	/**
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		if ( $this->getGroupID() ) {
			$query = "
				 SELECT permissions.name
				   FROM ".system::getConfig()->getDatabase('mofilm_content').".permissionGroupLinks
						INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".permissions ON (permissions.ID = permissionGroupLinks.permissionID)
				  WHERE permissionGroupLinks.permissionGroupID = :GroupID
				  ORDER BY permissions.name ASC";

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':GroupID', $this->getGroupID());
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$this->addPermission($row['name']);
				}
				$return = true;
			}
			$this->setModified(false);
		}
		return $return;
	}
	
	/**
	 * Saves object to the table
	 * 
	 * @return boolean
	 */
	function save() {
		$return = false;
		if ( $this->getGroupID() ) {
			if ( $this->isModified() ) {
				/*
				 * Clear all existing permissions
				 */
				$this->delete();
				
				if ( $this->getCount() > 0 ) {
					$query = '
						INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.permissionGroupLinks
							(permissionID, permissionGroupID)
						VALUES';
					
					$values = array();
					foreach ( $this as $permission ) {
						$oPerm = mofilmPermission::getInstanceByPermission($permission);
						if ( !$oPerm->getID() ) {
							$oPerm->setName($permission);
							$oPerm->save();
						}
						
						$values[] = "({$oPerm->getID()}, {$this->getGroupID()})";
					}
					
					$query .= implode(', ', $values);
					
					$oStmt = dbManager::getInstance()->prepare($query);
					if ( $oStmt->execute() ) {
						$return = true;
						$this->setModified(false);
					}
					$oStmt->closeCursor();
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
		if ( $this->getGroupID() ) {
			$query = 'DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.permissionGroupLinks WHERE permissionGroupID = :GroupID';

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':GroupID', $this->getGroupID());
			$oStmt->execute();
			$oStmt->closeCursor();
			return true;
		}
		return false;
	}
	
	/**
	 * Resets object properties to defaults
	 * 
	 * @return mofilmPermissionGroupPermissions
	 */
	function reset() {
		$this->_GroupID = 0;
		$this->_Namespace = '';
		return parent::_resetSet();
	}
	
	/**
	 * Returns permissions as an array
	 *
	 * @return array
	 */
	function toArray() {
		return $this->_getItem();
	}



	/**
	 * Return value of $_GroupID
	 * 
	 * @return integer
	 * @access public
	 */
	function getGroupID() {
		return $this->_GroupID;
	}
	
	/**
	 * Set $_GroupID to $inGroupID
	 * 
	 * @param integer $inGroupID
	 * @return mofilmPermissionGroupPermissions
	 * @access public
	 */
	function setGroupID($inGroupID) {
		if ( $inGroupID !== $this->_GroupID ) {
			$this->_GroupID = $inGroupID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Namespace
	 * 
	 * @return string
	 * @access public
	 */
	function getNamespace() {
		return $this->_Namespace;
	}
	
	/**
	 * Set $_Namespace to $inNamespace
	 * 
	 * @param string $inNamespace
	 * @return mofilmPermissionGroupPermissions
	 * @access public
	 */
	function setNamespace($inNamespace) {
		if ( $inNamespace !== $this->_Namespace ) {
			$this->_Namespace = $inNamespace;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Adds $inPermission to the users permissions
	 *
	 * @param string $inPermission
	 * @return mofilmPermissionGroupPermissions
	 */
	function addPermission($inPermission) {
		if ( $this->getNamespace() ) {
			if ( stripos($inPermission, $this->getNamespace()) === 0 ) {
				$this->_setValue($inPermission);
			}  elseif ( strtolower($inPermission) == 'root' ) {
				$this->_setValue($inPermission);
			}
		} else {
			$this->_setValue($inPermission);
		}
		return $this;
	}

	/**
	 * Removes the permission from the set
	 *
	 * @param string $inPermission
	 * @return mofilmPermissionGroupPermissions
	 */
	function removePermission($inPermission) {
		return $this->_removeItemWithValue($inPermission);
	}
	
	/**
	 * Returns true if $inPermission is in the set
	 *
	 * @param string $inPermission
	 * @return boolean
	 */
	function hasPermission($inPermission) {
		return $this->_itemValueInSet($this->_checkNamespace($inPermission));
	}

	/**
	 * Returns true if user is a root user
	 *
	 * @return boolean
	 */
	function isRoot() {
		return $this->hasPermission('root');
	}

	/**
	 * Returns true if the current user has $inPermission
	 *
	 * @param string $inPermission
	 * @return boolean
	 */
	function isAuthorised($inPermission) {
		if ( $this->isRoot() ) {
			return true;
		}
		return $this->hasPermission($this->_checkNamespace($inPermission));
	}
	

	/**
	 * Checks the permission to ensure the namespace is prefixed
	 *
	 * @param string $inPermission
	 * @return string
	 * @access protected
	 */
	protected function _checkNamespace($inPermission) {
		if ( $this->getNamespace() && strtolower($inPermission) != 'root' ) {
			$namespace = $this->getNamespace();
			
			if ( strpos($inPermission, $namespace) !== 0 ) {
				$inPermission = $namespace.'.'.$inPermission;
			}
		}

		return $inPermission;
	}
}