<?php
/**
 * mofilmMovieCategorySet
 *
 * Stored in set.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieCategorySet
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieCategorySet
 *
 * Manages category associations with a movie.
 *
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieCategorySet
 */
class mofilmMovieCategorySet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;



	/**
	 * Returns new instance of mofilmMovieCategorySet
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieCategorySet
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
			$this->load();
		}
	}



	/**
	 * Attaches the mofilmMovieCategorySet to each movie in $inMovies. Expects $inMovies to be indexed by movie ID
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
		 		SELECT movieCategories.movieID AS movieID, categories.*
		 		  FROM '.system::getConfig()->getDatabase('mofilm_content').'.categories
		 		       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieCategories ON (movieCategories.categoryID = categories.ID)
		 		 WHERE movieCategories.movieID IN ('.implode(',', array_keys($inMovies)).')';

		 	$oStmt = dbManager::getInstance()->prepare($query);
		 	if ( $oStmt->execute() ) {
		 		foreach ( $oStmt as $row ) {
					$oObject = new mofilmCategory();
					$oObject->loadFromArray($row);

		 			$properties[$row['movieID']][] = $oObject;
		 		}
		 	}
		 	$oStmt->closeCursor();

		 	if ( false ) $oMovie = new mofilmMovie();
		 	foreach ( $inMovies as $oMovie ) {
		 		if ( $oMovie instanceof mofilmMovie ) {
		 			if ( array_key_exists($oMovie->getID(), $properties) ) {
		 				$oObject = new mofilmMovieCategorySet();
		 				$oObject->setMovieID($oMovie->getID());
		 				$oObject->setObjects($properties[$oMovie->getID()]);
		 				$oObject->setModified(false);

		 				$oMovie->setCategorySet($oObject);
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
				DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieCategories
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
				SELECT categories.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieCategories
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.categories ON (movieCategories.categoryID = categories.ID)
				 WHERE movieCategories.movieID = :MovieID';

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $this->_MovieID);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmCategory();
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
						INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieCategories
							(movieID, categoryID)
						VALUES ';

					$values = array();
					if ( false ) $oObject = new mofilmCategory();
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
	 * @return mofilmMovieCategorySet
	 */
	function setMovieID($inMovieID) {
		if ( $this->_MovieID !== $inMovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns true if movie has $inCategoryID associated with it
	 * 
	 * @param mixed $inCategoryID Integer or category object
	 * @return boolean
	 */
	function hasCategory($inCategoryID) {
		if ( $inCategoryID instanceof mofilmCategory ) {
			$inCategoryID = $inCategoryID->getID();
		}
		return ($this->getObjectByID($inCategoryID) instanceof mofilmCategory);
	}
	
	/**
	 * Returns the category by ID, returns null if not found
	 *
	 * @param integer $inObjectID
	 * @return mofilmCategory
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
	 * Returns the category with name $inDescription, or null if not found
	 *
	 * @param string $inDescription
	 * @return mofilmCategory
	 */
	function getObjectByDescription($inDescription) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmCategory();
			foreach ( $this as $oObject ) {
				if ( $oObject->getDescription() == $inDescription ) {
					return $oObject;
				}
			}
		}
		return null;
	}

	/**
	 * Sets an array of category objects to the set
	 *
	 * @param array $inArray Array of mofilmCategory objects
	 * @return mofilmMovieCategorySet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the category to the object
	 *
	 * @param mofilmCategory $inObject
	 * @return mofilmMovieCategorySet
	 */
	function setObject(mofilmCategory $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Removes the category
	 *
	 * @param mofilmCategory $inObject
	 * @return mofilmMovieCategorySet
	 */
	function removeObjectByValue(mofilmCategory $inObject) {
		return $this->_removeItemWithValue($inObject);
	}

	/**
	 * Removes the object by ID
	 *
	 * @param integer $inID
	 * @return mofilmMovieTractSet
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