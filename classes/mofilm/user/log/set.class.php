<?php
/**
 * mofilmUserLogSet
 *
 * Stored in set.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage user
 * @category mofilmUserLogSet
 * @version $Rev: 10 $
 */


/**
 * mofilmUserLogSet
 *
 * Manages the user download log
 *
 * @package mofilm
 * @subpackage user
 * @category mofilmUserLogSet
 */
class mofilmUserLogSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;



	/**
	 * Returns new instance of mofilmUserLogSet
	 *
	 * @param integer $inUserID
	 * @return mofilmUserLogSet
	 */
	function __construct($inUserID = null) {
		$this->reset();
		if ( $inUserID !== null ) {
			$this->setUserID($inUserID);
			$this->load();
		}
	}

	/**
	 * Deletes the objects
	 *
	 * @return boolean
	 * @see systemDaoInterface::delete()
	 */
	function delete() {
		if ( $this->_UserID ) {
			foreach ( $this as $oObject ) {
				$oObject->delete();
			}
			return true;
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
			$this->setObjects(mofilmUserLog::listOfObjects(0, 30, $this->getUserID()));
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
	 * @return mofilmUserLogSet
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
	 * @return mofilmUserLog
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
	 * @return mofilmUserLog
	 */
	function getObjectByIndex($inIndex) {
		return $this->_getItem($inIndex);
	}

	/**
	 * Sets an array of log objects to the set
	 *
	 * @param array $inArray Array of mofilmUserLog objects
	 * @return mofilmUserLogSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the log to the object
	 *
	 * @param mofilmUserLog $inObject
	 * @return mofilmUserLogSet
	 */
	function setObject(mofilmUserLog $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Marks the object for deletion
	 *
	 * @param integer $inID
	 * @return mofilmUserLogSet
	 */
	function removeObjectByID($inID) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getID() == $inID ) {
					$oObject->setMarkForDeletion(true);
				}
			}
		}
		return $this;
	}
}