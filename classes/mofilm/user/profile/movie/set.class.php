<?php
/**
 * mofilmUserProfileMovieSet
 *
 * Stored in set.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage user
 * @category mofilmUserProfileMovieSet
 * @version $Rev: 10 $
 */


/**
 * mofilmUserProfileMovieSet
 *
 * Manages the users profile movies.
 *
 * @package mofilm
 * @subpackage user
 * @category mofilmUserProfileMovieSet
 */
class mofilmUserProfileMovieSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;



	/**
	 * Returns new instance of mofilmUserProfileMovieSet
	 *
	 * @param integer $inUserID
	 * @return mofilmUserProfileMovieSet
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
		if ( $this->getUserID() ) {
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
		if ( $this->getUserID() ) {
			$this->setObjects(mofilmUserProfileMovie::listOfObjects(null, 5, $this->getUserID()));
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
		if ( $this->getUserID() ) {
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
	 * Marks all objects for deletion
	 * 
	 * @return true
	 */
	function markAllForDeletion() {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				$oObject->setMarkForDeletion(true);
			}
			$this->setModified();
		}
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
	 * Returns $_UserID
	 *
	 * @return integer
	 */
	function getUserID() {
		return $this->_UserID;
	}
	
	/**
	 * Set $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserProfileMovieSet
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the object by ID, returns null if not found
	 *
	 * @param integer $inObjectID
	 * @return mofilmUserProfileMovie
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
	 * Returns the object by movieID, returns null if not found
	 *
	 * @param integer $inObjectID
	 * @return mofilmUserProfileMovie
	 */
	function getObjectByMovieID($inObjectID) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getMovieID() == $inObjectID ) {
					return $oObject;
				}
			}
		}
		return null;
	}

	/**
	 * Returns the object by the position, returns null if not found
	 *
	 * @param integer $inPosition
	 * @return mofilmUserProfileMovie
	 */
	function getObjectByPosition($inPosition) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getPosition() == $inPosition ) {
					return $oObject;
				}
			}
		}
		return null;
	}
	
	/**
	 * Returns true if $inMovieID is in the profile already
	 * 
	 * @param integer $inMovieID
	 * @return boolean
	 */
	function isMovieInProfile($inMovieID) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getMovieID() == $inMovieID ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Returns the object at $inIndex, the array offset position
	 *
	 * @param integer $inIndex
	 * @return mofilmUserProfileMovie
	 */
	function getObjectByIndex($inIndex) {
		return $this->_getItem($inIndex);
	}

	/**
	 * Sets an array of downloads objects to the set
	 *
	 * @param array $inArray Array of mofilmUserProfileMovie objects
	 * @return mofilmUserProfileMovieSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the download to the object
	 *
	 * @param mofilmUserProfileMovie $inObject
	 * @return mofilmUserProfileMovieSet
	 */
	function setObject(mofilmUserProfileMovie $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Marks the profile movie by $inID for deletion
	 *
	 * @param integer $inID
	 * @return mofilmUserProfileMovieSet
	 */
	function removeObjectByID($inID) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmUserProfileMovie();
			foreach ( $this as $oObject ) {
				if ( $oObject->getID() == $inID ) {
					$oObject->setMarkForDeletion(true);
				}
			}
		}
		return $this;
	}
}