<?php
/**
 * mofilmMovieTagSet
 * 
 * Stored in tagSet.class.php
 * 
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieTagSet
 * @version $Rev: 15 $
 */


/**
 * mofilmMovieTagSet
 * 
 * Manages which tags are associated with the movie.
 * 
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieTagSet
 */
class mofilmMovieTagSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	/**
	 * Stores $_TagID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TagID;	
	

	/**
	 * Returns new instance of mofilmMovieTagSet
	 * 
	 * @param integer $inMovieID
	 * @return mofilmMovieTagSet
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
			$this->load();
		}
	}
	
	

	/**
	 * Attaches the mofilmMovieTagSet to each movie in $inMovies. Expects $inMovies to be indexed by movie ID
	 *
	 * @param array $inMovies
	 * @return boolean
	 * @static
	 */
	static function loadArrayOfMoviesWithProperties(array $inMovies) {
		 $return = false;
		 $properties = array();
		 if ( count($inMovies) > 0 ) {
		 	mofilmTag::preloadInstances();
		 	
		 	$query = '
		 		SELECT movies.ID AS movieID, movieTags.tagID
		 		  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
		 		       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieTags ON (movies.ID = movieTags.movieID)
		 		 WHERE movies.ID IN ('.implode(',', array_keys($inMovies)).')';
		 	
		 	$oStmt = dbManager::getInstance()->prepare($query);
		 	if ( $oStmt->execute() ) {
		 		foreach ( $oStmt as $row ) {
		 			$properties[$row['movieID']][] = mofilmTag::getInstance($row['tagID']);
		 		}
		 	}
		 	$oStmt->closeCursor();
		 	
		 	if ( false ) $oMovie = new mofilmMovie();
		 	foreach ( $inMovies as $oMovie ) {
		 		if ( $oMovie instanceof mofilmMovie ) {
		 			if ( array_key_exists($oMovie->getID(), $properties) ) {
		 				$oObject = new mofilmMovieTagSet();
		 				$oObject->setMovieID($oMovie->getID());
		 				$oObject->setObjects($properties[$oMovie->getID()]);
		 				$oObject->setModified(false);
		 				
		 				$oMovie->setTagSet($oObject);
		 				$return = true;
		 			}
		 		}
		 	}
		 }
		 return $return;
	}
	
	/**
	 * 
	 *
	 * 
	 * @return boolean
	 * @static
	 */
	public static function checkForMovieIDAndTagID($inMovieID = NULL, $inTagID = NULL) {
		$return = false;
		$query = '
			SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieTags
			    WHERE movieID = :MovieID AND tagID = :TagID';
			
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':MovieID', $inMovieID);
		$oStmt->bindValue(':TagID', $inTagID);
			
		if ( $oStmt->execute() ) {
			$row = $oStmt->fetch();
			if ( $row !== false && is_array($row) ) {
				$return = true;
				$oStmt->closeCursor();
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
				DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieTags
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
	 * Deletes the properties matching tag and movie id
	 * 
	 * @return boolean 
	 * 
	 */
	function deleteByTagAndMovieID() {
		if ( $this->_MovieID && $this->_TagID ) {
			$query = '
				DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieTags
				 WHERE movieID = :MovieID AND tagID = :TagID' ;
			systemLog::message($query);
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $this->_MovieID);
			$oStmt->bindValue(':TagID', $this->_TagID);
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
				SELECT tags.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieTags
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.tags ON (movieTags.tagID = tags.ID)
				 WHERE movieTags.movieID = :MovieID';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $this->_MovieID);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmTag();
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
		$this->_TagID = 0;
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
				

				$query = '
						INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieTags
							(movieID, tagID)
						VALUES ';

				if ( $this->getCount() > 0 ) {
					$values = array();
					if ( false ) $oObject = new mofilmTag();
					foreach ( $this as $oObject ) {
						$oObject->save();

						$values[] = "($this->_MovieID, {$oObject->getID()})";
					}
					$query .= implode(', ', $values);

				} elseif ( $this->_TagID ) {
					$query .= "($this->_MovieID, $this->_TagID)";
				} else {
					return false;
				}

				$res = dbManager::getInstance()->exec($query);
				if ( $res > 0 ) {
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
	 * @return mofilmMovieTagSet
	 */
	function setMovieID($inMovieID) {
		if ( $this->_MovieID !== $inMovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns $_TagID
	 *
	 * @return integer
	 */
	function getTagID() {
		return $this->_TagID;
	}
	 
	/**
	 * Sets $_TagID to $inTagID
	 *
	 * @param integer $inTagID
	 * @return mofilmMovieTagSet
	 */
	function setTagID($inTagID) {
		if ( $this->_TagID !== $inTagID ) {
			$this->_TagID = $inTagID;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns true if $inTag is active
	 *
	 * @param string $inTag
	 * @return boolean
	 */
	function hasTag($inTag) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getName() == $inTag ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Returns the tag by ID, returns null if not found
	 * 
	 * @param integer $inObjectID
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
	 * Returns the tag named $inName, returns null if not found
	 * 
	 * @param string $inName
	 * @return mofilmTag
	 */
	function getObjectByName($inName) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getName() == $inName ) {
					return $oObject;
				}
			}
		}
		return null;
	}
	
	/**
	 * Sets an array of tag objects to the set
	 * 
	 * @param array $inArray Array of mofilmTag objects
	 * @return mofilmMovieTagSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}
	
	/**
	 * Add the tag to the object
	 *
	 * @param mofilmTag $inObject
	 * @return mofilmMovieTagSet
	 */
	function setObject(mofilmTag $inObject) {
		return $this->_setValue($inObject);
	}
	
	/**
	 * Removes the object by value
	 * 
	 * @param mofilmTag $inObject
	 * @return mofilmMovieTagSet
	 */
	function removeObjectByValue(mofilmTag $inObject) {
		return $this->_removeItemWithValue($inObject);
	}

	/**
	 * Removes the object by ID
	 *
	 * @param integer $inID
	 * @return mofilmMovieTagSet
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