<?php
/**
 * mofilmMovieHistorySet
 * 
 * Stored in set.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieHistorySet
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieHistorySet
 * 
 * Holds the movie editing history.
 * 
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieHistorySet
 */
class mofilmMovieHistorySet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	
	
	/**
	 * Returns new instance of mofilmMovieHistorySet
	 * 
	 * @param integer $inMovieID
	 * @return mofilmMovieHistorySet
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
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
		if ( $this->_MovieID ) {
			if ( $this->getCount() > 0 ) {
				foreach ( $this as $oObject ) {
					$oObject->delete();
				}
				$this->reset();
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
		if ( $this->_MovieID ) {
			$this->setObjects(mofilmMovieHistory::listOfObjects(null, null, $this->getMovieID()));
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
		$this->_MovieID = 0;
		return $this->_resetSet();
	}

	/**
	 * Saves object
	 * 
	 * @return boolean 
	 * @see systemDaoInterface::save()
	 */
	function save() {
		if ( $this->_MovieID ) {
			if ( $this->isModified() ) {
				if ( $this->getCount() > 0 ) {
					if ( false ) $oObject = new mofilmMovieHistory();
	 				foreach ( $this as $oObject ) {
	 					$oObject->setMovieID($this->getMovieID());
						if ( $oObject->getMarkForDeletion() ) {
							$oObject->delete();
						} else {
							$oObject->save();
						}
	 				}
	 				$this->setModified(false);
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
	 * Returns $_MovieID
	 *
	 * @return integer
	 */
	function getMovieID() {
		return $this->_MovieID;
	}
	 
	/**
	 * Sets $_MovieID to $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieHistorySet
	 */
	function setMovieID($inMovieID) {
		if ( $this->_MovieID !== $inMovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the object at index $inIndex
	 * 
	 * @param integer $inIndex
	 * @return mofilmMovieHistory
	 */
	function getObjectByIndex($inIndex) {
		return $this->_getItem($inIndex);
	}
	
	/**
	 * Sets an array of mofilmMovieHistory objects to the set
	 * 
	 * @param array $inArray Array of mofilmMovieHistory objects
	 * @return mofilmMovieHistorySet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}
	
	/**
	 * Add the object to the set
	 *
	 * @param mofilmMovieHistory $inObject
	 * @return mofilmMovieHistorySet
	 */
	function setObject(mofilmMovieHistory $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Removes the object by value
	 *
	 * @param mofilmMovieHistory $inObject
	 * @return mofilmMovieHistorySet
	 */
	function removeObjectByValue(mofilmMovieHistory $inObject) {
		return $this->removeObjectByID($inObject->getID());
	}

	/**
	 * Removes the object by ID
	 *
	 * @param integer $inID
	 * @return mofilmMovieHistorySet
	 */
	function removeObjectByID($inID) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getID() == $inID ) {
					$oObject->setMarkForDeletion();
					break;
				}
			}
		}
		return $this;
	}
}