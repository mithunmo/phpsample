<?php
/**
 * mofilmMovieSourceSet
 * 
 * Stored in sourceSet.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieSourceSet
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieSourceSet
 * 
 * Manages which tags are associated with the movie.
 * 
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieSourceSet
 */
class mofilmMovieSourceSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	
	
	/**
	 * Returns new instance of mofilmMovieSourceSet
	 * 
	 * @param integer $inMovieID
	 * @return mofilmMovieSourceSet
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
			$this->load();
		}
	}
	
	

	/**
	 * Attaches the mofilmMovieSourceSet to each movie in $inMovies. Expects $inMovies to be indexed by movie ID
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
		 		SELECT movies.ID AS movieID, movieSources.sourceID
		 		  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
		 		       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movies.ID = movieSources.movieID)
		 		 WHERE movies.ID IN ('.implode(',', array_keys($inMovies)).')';
		 	
		 	$oStmt = dbManager::getInstance()->prepare($query);
		 	if ( $oStmt->execute() ) {
		 		foreach ( $oStmt as $row ) {
		 			/*
		 			 * Load from instance as sources are shared resources
		 			 */
		 			$properties[$row['movieID']][] = mofilmSource::getInstance($row['sourceID']);
		 		}
		 	}
		 	$oStmt->closeCursor();
		 	
		 	if ( false ) $oMovie = new mofilmMovie();
		 	foreach ( $inMovies as $oMovie ) {
		 		if ( $oMovie instanceof mofilmMovie ) {
		 			if ( array_key_exists($oMovie->getID(), $properties) ) {
		 				$oObject = new mofilmMovieSourceSet();
		 				$oObject->setMovieID($oMovie->getID());
		 				$oObject->setObjects($properties[$oMovie->getID()]);
		 				$oObject->setModified(false);
		 				
		 				$oMovie->setSourceSet($oObject);
		 				$return = true;
		 			}
		 		}
		 	}
		 }
		 return $return;
	}

	
	
	/**
	 * Deletes the properties
	 * 
	 * @return boolean 
	 * @see systemDaoInterface::delete()
	 */
	function delete() {
		if ( $this->_MovieID ) {
			$query = '
				DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieSources
				 WHERE movieID = :MovieID';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $this->_MovieID);
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
		if ( $this->_MovieID ) {
			$query = '
				SELECT sources.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieSources
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (movieSources.sourceID = sources.ID)
				 WHERE movieSources.movieID = :MovieID';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $this->_MovieID);
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
				/*
 				 * Delete existing records so we dont get duplicates or old records
 				 */
	 			$this->delete();
				
				if ( $this->getCount() > 0 ) {
					$query = '
						INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieSources
							(movieID, sourceID)
						VALUES ';
					
					$values = array();
					if ( false ) $oObject = new mofilmSource();
	 				foreach ( $this as $oObject ) {
		 				$oObject->save();
		 					
		 				$values[] = "($this->_MovieID, {$oObject->getID()})";
	 				}
	 				$query .= implode(', ', $values);
	 				
	 				$res = dbManager::getInstance()->exec($query);
	 				if ( $res > 0 ) {
	 					$this->setModified(false);
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
	 * @return mofilmMovieSourceSet
	 */
	function setMovieID($inMovieID) {
		if ( $this->_MovieID !== $inMovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns true if $inSourceID is attached to this movie
	 *
	 * @param integer $inSourceID
	 * @return boolean
	 */
	function hasSource($inSourceID) {
		return ($this->getObjectByID($inSourceID) instanceof mofilmSource);
	}

	/**
	 * Returns the tag by ID, returns null if not found
	 * 
	 * @param integer $inTagID
	 * @return mofilmTag
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
	 * Returns the object at $inIndex, the array offset position
	 *
	 * @param integer $inIndex
	 * @return mofilmTag
	 */
	function getObjectByIndex($inIndex) {
		return $this->_getItem($inIndex);
	}
	
	/**
	 * Sets an array of mofilmSource objects to the set
	 * 
	 * @param array $inTags Array of mofilmSource objects
	 * @return mofilmMovieSourceSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}
	
	/**
	 * Add the mofilmSource to the object
	 *
	 * @param mofilmSource $inObject
	 * @return mofilmMovieSourceSet
	 */
	function setObject(mofilmSource $inObject) {
		return $this->_setValue($inObject);
	}
	
	/**
	 * Removes the mofilmSource
	 * 
	 * @param mofilmSource $inObject
	 * @return mofilmMovieSourceSet
	 */
	function removeObjectByValue(mofilmSource $inObject) {
		return $this->_removeItemWithValue($inObject);
	}

	/**
	 * Removes the object by ID
	 *
	 * @param integer $inID
	 * @return mofilmMovieSourceSet
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