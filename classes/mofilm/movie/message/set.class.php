<?php
/**
 * mofilmMovieMessageSet
 * 
 * Stored in set.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieMessageSet
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieMessageSet
 * 
 * Manages movie award, optionally only loading awards for the specified event.
 * 
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieMessageSet
 */
class mofilmMovieMessageSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	
	
	/**
	 * Returns new instance of mofilmMovieMessageSet
	 * 
	 * @param integer $inMovieID
	 * @return mofilmMovieMessageSet
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
			$this->load();
		}
	}
	
	

	/**
	 * Attaches the mofilmMovieMessageSet to each movie in $inMovies. Expects $inMovies to be indexed by movie ID
	 *
	 * @param array $inMovies
	 * @return boolean
	 * @static
	 */
	static function loadArrayOfMoviesWithProperties(array $inMovies) {
		$return = false;
		$properties = array();
		if ( count($inMovies) > 0 ) {
			$query = '
				SELECT movieMessageHistory.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieMessageHistory
				 WHERE movieMessageHistory.movieID IN ('.implode(',', array_keys($inMovies)).')
				 ORDER BY movieMessageHistory.movieID ASC, movieMessageHistory.sentDate DESC';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMovieMessageHistory();
					$oObject->loadFromArray($row);
					$properties[$row['movieID']][] = $oObject; 
				}
			}
			$oStmt->closeCursor();
			
			if ( false ) $oMovie = new mofilmMovie();
			foreach ( $inMovies as $oMovie ) {
				if ( $oMovie instanceof mofilmMovie ) {
					if ( array_key_exists($oMovie->getID(), $properties) ) {
						$oObject = new mofilmMovieMessageSet();
						$oObject->setMovieID($oMovie->getID());
						$oObject->setObjects($properties[$oMovie->getID()]);
						$oObject->setModified(false);
						
						$oMovie->setMessageSet($oObject);
						$return = true;
					}
				}
			}
		}
		return $return;
	}

	
	
	/**
	 * Deletes the objects
	 * 
	 * @return boolean 
	 * @see systemDaoInterface::delete()
	 */
	function delete() {
		if ( $this->_MovieID && $this->getCount() > 0 ) {
			$return = true;
			foreach ( $this as $oObject ) {
				$return = $oObject->delete() && $return;
			}
			return $return;
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
			$this->setObjects(mofilmMovieMessageHistory::listOfObjects(null, null, $this->getMovieID()));
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
					if ( false ) $oObject = new mofilmMovieMessageHistory();
	 				foreach ( $this as $oObject ) {
	 					$oObject->setMovieID($this->getMovieID());
	 					if ( $oObject->getMarkForDeletion() ) {
	 						$oObject->delete();
	 					} else {
		 					$oObject->save();
	 					}
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
	 * @return mofilmMovieMessageSet
	 */
	function setMovieID($inMovieID) {
		if ( $this->_MovieID !== $inMovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns the object by ID, returns null if not found
	 * 
	 * @param integer $inObjectID
	 * @return mofilmMovieMessageHistory
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
	 * @return mofilmMovieMessageHistory
	 */
	function getObjectByIndex($inIndex) {
		return $this->_getItem($inIndex);
	}
	
	/**
	 * Sets an array of objects to the set
	 * 
	 * @param array $inArray Array of objects
	 * @return mofilmMovieMessageSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}
	
	/**
	 * Add the object to the set
	 *
	 * @param mofilmMovieMessageHistory $inObject
	 * @return mofilmMovieMessageSet
	 */
	function setObject(mofilmMovieMessageHistory $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Marks the object by $inID for deletion
	 *
	 * @param integer $inID
	 * @return mofilmMovieMessageSet
	 */
	function removeObjectByID($inID) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmMovieMessageHistory();
			foreach ( $this as $oObject ) {
				if ( $oObject->getID() == $inID ) {
					$oObject->setMarkForDeletion(true);
				}
			}
		}
		return $this;
	}
}