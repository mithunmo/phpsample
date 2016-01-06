<?php
/**
 * mofilmMovieRatingSet
 * 
 * Stored in set.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieRatingSet
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieRatingSet
 * 
 * Manages movie ratings for the movie.
 * 
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieRatingSet
 */
class mofilmMovieRatingSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	
	
	/**
	 * Returns new instance of mofilmMovieRatingSet
	 * 
	 * @param integer $inMovieID
	 * @return mofilmMovieRatingSet
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
			$this->load();
		}
	}
	
	

	/**
	 * Attaches the mofilmMovieRatingSet to each movie in $inMovies. Expects $inMovies to be indexed by movie ID
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
				SELECT movieRatings.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieRatings
				 WHERE movieRatings.movieID IN ('.implode(',', array_keys($inMovies)).') AND movieRatings.userID > 0
				 ORDER BY movieRatings.movieID ASC';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMovieRating();
					$oObject->loadFromArray($row);
					$properties[$row['movieID']][] = $oObject; 
				}
			}
			$oStmt->closeCursor();
			
			if ( false ) $oMovie = new mofilmMovie();
			foreach ( $inMovies as $oMovie ) {
				if ( $oMovie instanceof mofilmMovie ) {
					if ( array_key_exists($oMovie->getID(), $properties) ) {
						$oObject = new mofilmMovieRatingSet();
						$oObject->setMovieID($oMovie->getID());
						$oObject->setObjects($properties[$oMovie->getID()]);
						$oObject->setModified(false);
						
						$oMovie->setRatingSet($oObject);
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
			$this->setObjects(mofilmMovieRating::listOfObjects(null, null, $this->getMovieID()));
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
					if ( false ) $oObject = new mofilmMovieRating();
	 				foreach ( $this as $oObject ) {
	 					$oObject->setMovieID($this->getMovieID());
	 					if ( $oObject->getMarkForDeletion() ) {
	 						$oObject->delete();
	 					} else {
		 					$oObject->save();
	 					}
	 				}
				}
				
				mofilmMovieRating::updateMovieRating($this->getMovieID());
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
	 * @return mofilmMovieRatingSet
	 */
	function setMovieID($inMovieID) {
		if ( $this->_MovieID !== $inMovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns true if the specified user has rated this movie
	 * 
	 * @param integer $inUserID
	 * @return boolean
	 */
	function hasUserRatedMovie($inUserID) {
		return ($this->getObjectByUserID($inUserID) instanceof mofilmMovieRating);
	}
	
	/**
	 * Returns the object by userID, returns null if not found
	 * 
	 * @param integer $inObjectID
	 * @return mofilmMovieRating
	 */
	function getObjectByUserID($inObjectID) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getUserID() == $inObjectID ) {
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
	 * @return mofilmMovieRating
	 */
	function getObjectByIndex($inIndex) {
		return $this->_getItem($inIndex);
	}
	
	/**
	 * Sets an array of objects to the set
	 * 
	 * @param array $inArray Array of objects
	 * @return mofilmMovieRatingSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}
	
	/**
	 * Add the object to the set
	 *
	 * @param mofilmMovieRating $inObject
	 * @return mofilmMovieRatingSet
	 */
	function setObject(mofilmMovieRating $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Marks the object by $inUserID for deletion
	 *
	 * @param integer $inUserID
	 * @return mofilmMovieRatingSet
	 */
	function removeObjectByUserID($inUserID) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmMovieRating();
			foreach ( $this as $oObject ) {
				if ( $oObject->getUserID() == $inUserID ) {
					$oObject->setMarkForDeletion(true);
				}
			}
		}
		return $this;
	}
}