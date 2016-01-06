<?php
/**
 * mofilmUserSourceSet
 *
 * Stored in set.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage user
 * @category mofilmUserSourceSet
 * @version $Rev: 10 $
 */


/**
 * mofilmUserSourceSet
 *
 * Loads sources that a user can access.
 *
 * @package mofilm
 * @subpackage user
 * @category mofilmUserSourceSet
 */
class mofilmUserSourceSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;



	/**
	 * Returns new instance of mofilmUserSourceSet
	 *
	 * @param integer $inUserID
	 * @return mofilmUserSourceSet
	 */
	function __construct($inUserID = null) {
		$this->reset();
		if ( $inUserID !== null ) {
			$this->setUserID($inUserID);
			$this->load();
		}
	}

	/**
	 * Required for interface, but sources cannot be deleted
	 *
	 * @return boolean
	 * @see systemDaoInterface::delete()
	 */
	function delete() {
		return true;
	}

	/**
	 * Loads the set with data
	 *
	 * @return boolean
	 * @see systemDaoInterface::load()
	 */
	function load() {
		if ( $this->_UserID ) {
			$query = '
				SELECT sources.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.users
					   INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.clientSources ON (clientSources.clientID = users.clientID)
					   INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (sources.ID = clientSources.sourceID)
				 WHERE users.ID = :UserID';

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->_UserID);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmSource();
					$oObject->loadFromArray($row);
					$this->setObject($oObject);
				}
				$oStmt->closeCursor();
			}
			$this->setModified(false);
			return true;
		}
		return false;
	}

	/**
	 * Resets set to defaults
	 *
	 * @return systemDaoInterface
	 * @see systemDaoInterface::reset()
	 */
	function reset() {
		$this->_UserID = 0;
		return $this->_resetSet();
	}

	/**
	 * Saves object, required for interface - changes cannot be saved
	 *
	 * @return boolean
	 * @see systemDaoInterface::save()
	 */
	function save() {
		return true;
	}



	/**
	 * Returns true if the object or sub-objects have been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( !$modified && $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				$modified = $modified || $oObject->isModified();
			}
		}
		return $modified;
	}

	/**
	 * Returns object properties as array
	 *
	 * @return array
	 * @see systemDaoInterface::toArray()
	 */
	function toArray() {
		return get_object_vars($this);
	}

	/**
	 * Returns user ID
	 *
	 * @return integer
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Sets userID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserSourceSet
	 */
	function setUserID($inUserID) {
		if ( $this->_UserID !== $inUserID ) {
			$this->_UserID = $inUserID;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the object by ID, returns null if not found
	 *
	 * @param integer $inObjectID
	 * @return mofilmSource
	 */
	function getObjectByID($inObjectID) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getID() == $inObjectID ) {
					return $oObject;
				}
			}
		}
		return null;
	}

	/**
	 * Returns an array containing only the object IDs
	 *
	 * @return array
	 */
	function getObjectIDs() {
		$tmp = array();
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				$tmp[] = $oObject->getID();
			}
		}
		return $tmp;
	}
	
	/**
	 * Returns an array of just event IDs from the loaded sources
	 * 
	 * @return array
	 */
	function getEventIDs() {
		$tmp = array();
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( !in_array($oObject->getEventID(), $tmp) ) {
					$tmp[] = $oObject->getEventID();
				}
			}
		}
		return $tmp;
	}
	
	/**
	 * Returns the source with name $inDescription, or null if not found
	 *
	 * @param string $inDescription
	 * @return mofilmSource
	 */
	function getObjectByDescription($inDescription) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmSource();
			foreach ( $this as $oObject ) {
				if ( $oObject->getName() == $inDescription ) {
					return $oObject;
				}
			}
		}
		return null;
	}

	/**
	 * Sets an array of objects to the set
	 *
	 * @param array $inArray Array of mofilmSource objects
	 * @return mofilmUserSourceSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the object to the set
	 *
	 * @param mofilmSource $inObject
	 * @return mofilmUserSourceSet
	 */
	function setObject(mofilmSource $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Removes the object from the set
	 *
	 * @param mofilmSource $inObject
	 * @return mofilmUserSourceSet
	 */
	function removeObjectByValue(mofilmSource $inObject) {
		return $this->_removeItemWithValue($inObject);
	}

	/**
	 * Removes the object by ID
	 *
	 * @param integer $inID
	 * @return mofilmUserSourceSet
	 */
	function removeObjectByID($inID) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getID() == $inID ) {
					$this->removeObjectByValue($oObject);
					break;
				}
			}
		}
		return $this;
	}
}