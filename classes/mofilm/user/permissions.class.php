<?php
/**
 * mofilmUserPermissions
 * 
 * Stored in mofilmUserPermissions.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserPermissions
 * @category mofilmUserPermissions
 * @version $Rev: 178 $
 */


/**
 * mofilmUserPermissions Class
 * 
 * Provides an interface to the permissions system. Mofilm permissions
 * can be "namespaced". This allows only a portion of the full permissions
 * set to be loaded based on requirement.
 *
 * Currently the namespaces are split between "admin", "root" and the normal
 * client website. Root users are a special case who automatically get full
 * access to all resources within the site.
 *
 * For normal users, they are assigned to a group and inherit those group
 * permissions. Additional permissions can be assigned to the specific user
 * if required.
 * 
 * @package mofilm
 * @subpackage mofilmUserPermissions
 * @category mofilmUserPermissions
 */
class mofilmUserPermissions extends baseSet implements systemDaoInterface {
		
	/**
	 * Stores $_UserID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;
	
	/**
	 * Stores $_Namespace
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Namespace;
	
	/**
	 * Stores an array mapping permissions to their source
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_PermissionSource;
	
	const PERMISSION_SOURCE_GROUP = 'group';
	const PERMISSION_SOURCE_USER = 'user';
	
	
	
	/**
	 * Returns a new instance of mofilmUserPermissions
	 * 
	 * @param integer $inUserID
	 * @param string $inNamespace
	 * @return mofilmUserPermissions
	 */
	function __construct($inUserID = null, $inNamespace = null) {
		$this->reset();
		if ( $inUserID !== null ) {
			$this->setUserID($inUserID);
		}
		if ( $inNamespace !== null ) {
			$this->setNamespace($inNamespace);
		}
		$this->load();
	}
	
	
	
	/**
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		if ( $this->getUserID() ) {
			$query = "
				SELECT permissions.name, 'user' AS source
				  FROM ".system::getConfig()->getDatabase('mofilm_content').".userPermissions
				       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".permissions ON (permissions.ID=userPermissions.permissionID)
				 WHERE userPermissions.userID = :UserID

				 UNION ALL

				 SELECT permissions.name, 'group' AS source
				   FROM ".system::getConfig()->getDatabase('mofilm_content').".userPermissions
				        LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".permissionGroupLinks ON (permissionGroupLinks.permissionGroupID = substring(userPermissions.permissionID,2))
						LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".permissions ON (permissions.ID = permissionGroupLinks.permissionID)
				  WHERE userPermissions.userID = :UserID
				    AND userPermissions.permissionID LIKE 'G%'";

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->getUserID());
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$this->addPermission($row['name'], $row['source']);
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
		if ( $this->getUserID() ) {
			if ( $this->isModified() ) {
				/*
				 * Clear all existing permissions
				 */
				$this->delete();
				
				if ( $this->getCount() > 0 ) {
					$query = '
						INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userPermissions
							(userID, permissionID)
						VALUES';
					
					$values = array();
					foreach ( $this as $permission ) {
						if ( $this->isUserPermission($permission) ) {
							$oPerm = mofilmPermission::getInstanceByPermission($permission);
							if ( !$oPerm->getID() ) {
								$oPerm->setName($permission);
								$oPerm->save();
							}
							
							$values[] = "({$this->getUserID()}, {$oPerm->getID()})";
						}
						/*
						 * Add in permission group
						 */
						if ( preg_match('/^G\d+$/', $permission) ) {
							$values[] = "({$this->getUserID()}, '{$permission}')";
						}
					}
					
					if ( count($values) > 0 ) {
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
		}
		return $return;
	}
	
	/**
	 * Deletes the object from the table
	 * 
	 * @return boolean
	 */
	function delete() {
		$return = false;

		if ( $this->getUserID() ) {
			$query = 'DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPermissions WHERE userID = :UserID';
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->getUserID());
			if ( $oStmt->execute() ) {
				$return = true;
			}
			$oStmt->closeCursor();
		}

		return $return;
	}
	
	/**
	 * Resets object properties to defaults
	 * 
	 * @return mofilmUserPermissions
	 */
	function reset() {
		$this->_UserID = 0;
		$this->_Namespace = '';
		$this->_PermissionSource = array();
		return parent::_resetSet();
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
	 * Return value of $_UserID
	 * 
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}
	
	/**
	 * Set $_UserID to $inUserID
	 * 
	 * @param integer $inUserID
	 * @return mofilmUserPermissions
	 * @access public
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
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
	 * @return mofilmUserPermissions
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
	 * @param string $inSource (optional) where the permission is inherited from, default group
	 * @return mofilmUserPermissions
	 */
	function addPermission($inPermission, $inSource = self::PERMISSION_SOURCE_GROUP) {
		$this->_addPermissionSource($inPermission, $inSource);
		if ( $this->getNamespace() ) {
			if ( strtolower($inPermission) == 'root' || preg_match('/^G\d+$/', $inPermission) ) {
				$this->_setValue($inPermission);
			} elseif ( stripos($inPermission, $this->getNamespace()) === 0 ) {
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
	 * @return mofilmUserPermissions
	 */
	function removePermission($inPermission) {
		$this->_removePermissionSource($inPermission);
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
	 * Returns true if the permission came from the user
	 * 
	 * @param string $inPermission
	 * @return boolean
	 */
	function isUserPermission($inPermission) {
		return (isset($this->_PermissionSource[$inPermission]) && $this->_PermissionSource[$inPermission] == self::PERMISSION_SOURCE_USER);
	}
	
	/**
	 * Returns true if the permission came from the group
	 * 
	 * @param string $inPermission
	 * @return boolean
	 */
	function isGroupPermission($inPermission) {
		return (isset($this->_PermissionSource[$inPermission]) && $this->_PermissionSource[$inPermission] == self::PERMISSION_SOURCE_GROUP);
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
	 * Returns an array of all the status options the current user can see
	 * 
	 * @return array
	 */
	function getViewableVideoStatuses() {
		if ( $this->isRoot() ) {
			return mofilmMovieManager::getAvailableMovieStatuses();
		}
		
		$status = array(mofilmMovieBase::STATUS_APPROVED);
		if ( $this->isAuthorised('seeEncodingVideos') ) {
			$status[] = mofilmMovieBase::STATUS_ENCODING;
		}
		if ( $this->isAuthorised('seePendingVideos') ) {
			$status[] = mofilmMovieBase::STATUS_PENDING;
		}
		if ( $this->isAuthorised('seeRemovedVideos') ) {
			$status[] = mofilmMovieBase::STATUS_REMOVED;
		}
		if ( $this->isAuthorised('seeRejectedVideos') ) {
			$status[] = mofilmMovieBase::STATUS_REJECTED;
		}
		if ( $this->isAuthorised('seeDisputedVideos') ) {
			$status[] = mofilmMovieBase::STATUS_DISPUTED;
		}
		if ( $this->isAuthorised('seeFailedVideos') ) {
			$status[] = mofilmMovieBase::STATUS_FAILED_ENCODING;
		}
		return $status;
	}
	
	/**
	 * Returns an array of all the status options the current user can see
	 * 
	 * @return array
	 */
	function getViewableGrantsStatuses() {
		return mofilmUserMovieGrants::getAvailableGrantsStatus();
		/*if ( $this->isRoot() ) {
			return mofilmMovieManager::getAvailableMovieStatuses();
		}
		
		$status = array(mofilmMovieBase::STATUS_APPROVED);
		if ( $this->isAuthorised('seeEncodingVideos') ) {
			$status[] = mofilmMovieBase::STATUS_ENCODING;
		}
		if ( $this->isAuthorised('seePendingVideos') ) {
			$status[] = mofilmMovieBase::STATUS_PENDING;
		}
		if ( $this->isAuthorised('seeRemovedVideos') ) {
			$status[] = mofilmMovieBase::STATUS_REMOVED;
		}
		if ( $this->isAuthorised('seeRejectedVideos') ) {
			$status[] = mofilmMovieBase::STATUS_REJECTED;
		}
		if ( $this->isAuthorised('seeDisputedVideos') ) {
			$status[] = mofilmMovieBase::STATUS_DISPUTED;
		}
		if ( $this->isAuthorised('seeFailedVideos') ) {
			$status[] = mofilmMovieBase::STATUS_FAILED_ENCODING;
		}
		return $status;*/
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
	
	/**
	 * Adds a permission source
	 * 
	 * @param string $inPermission
	 * @param string $inSource
	 * @return mofilmUserPermissions
	 * @access protected
	 */
	protected function _addPermissionSource($inPermission, $inSource) {
		$this->_PermissionSource[$inPermission] = $inSource;
		return $this;
	}
	
	/**
	 * Removes a permission source
	 * 
	 * @param string $inPermission
	 * @return mofilmUserPermissions
	 * @access protected
	 */
	protected function _removePermissionSource($inPermission) {
		if ( isset($this->_PermissionSource[$inPermission]) ) {
			unset($this->_PermissionSource[$inPermission]);
		}
		return $this;
	}
}