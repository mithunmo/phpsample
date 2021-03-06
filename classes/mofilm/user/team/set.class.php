<?php
/**
 * mofilmUserTeamSet
 *
 * Stored in trackSet.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage user
 * @category mofilmUserTeamSet
 * @version $Rev: 10 $
 */


/**
 * mofilmUserTeamSet
 *
 * Manages the team relations
 *
 * @package mofilm
 * @subpackage user
 * @category mofilmUserTeamSet
 */
class mofilmUserTeamSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;



	/**
	 * Returns new instance of mofilmUserTeamSet
	 *
	 * @param integer $inUserID
	 * @return mofilmUserTeamSet
	 */
	function __construct($inUserID = null) {
		$this->reset();
		if ( $inUserID !== null ) {
			$this->setUserID($inUserID);
			$this->load();
		}
	}



	/**
	 * Deletes the properties
	 *
	 * @return boolean
	 * @see systemDaoInterface::delete()
	 */
	function delete() {
		if ( $this->_UserID ) {
			$query = '
				DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userTeams
				 WHERE userID = :UserID';

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->_UserID);
			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				return true;
			}
		}
		return false;
	}

	/**
	 * Loads the set with data
	 *
	 * @return boolean
	 * @see systemDaoInterface::load()
	 */
	function load() {
		if ( $this->_UserID ) {
			$this->setObjects(mofilmUserTeam::listOfObjects(NULL, 30, $this->getUserID()));
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
	 * Saves object
	 *
	 * @return boolean
	 * @see systemDaoInterface::save()
	 */
	function save() {
		if ( $this->_UserID ) {
			if ( $this->isModified() ) {
				foreach ( $this as $oObject ) {
					$oObject->setUserID($this->getUserID());
					if ( $oObject->getMarkForDeletion() ) {
						$oObject->delete();
					} else {
						$oObject->save();
					}
				}
				$this->setModified(false);
				return true;
			}
		}
		return false;
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
	 * Returns $_UserID
	 *
	 * @return integer
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Sets $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserTeamSet
	 */
	function setUserID($inUserID) {
		if ( $this->_UserID !== $inUserID ) {
			$this->_UserID = $inUserID;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the track by ID, returns null if not found
	 *
	 * @param integer $inObjectID
	 * @return mofilmTrack
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
	 * Returns the object at $inIndex, the array offset position
	 *
	 * @param integer $inIndex
	 * @return mofilmUserTerm
	 */
	function getObjectByIndex($inIndex) {
		return $this->_getItem($inIndex);
	}

	/**
	 * Sets an array of track objects to the set
	 *
	 * @param array $inArray Array of mofilmUserTeam objects
	 * @return mofilmUserTeamSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the object to the set
	 *
	 * @param mofilmUserTeam $inObject
	 * @return mofilmUserTeamSet
	 */
	function setObject(mofilmUserTeam $inObject) {
		return $this->_setValue($inObject);
	}
	
	/**
	 * Removes an object by ID
	 *
	 * @param integer $inTeamID
	 * @return mofilmUserTeamSet
	 */
	function removeObjectByID($inTeamID) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getTeamID() == $inTeamID ) {
					$oObject->setMarkForDeletion(true);
				}
			}
		}
		return $this;
	}
}