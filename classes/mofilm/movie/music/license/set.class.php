<?php
/**
 * mofilmMovieMusicLicenseSet
 *
 * Stored in set.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @license mofilmMovieMusicLicenseSet
 * @version $Rev: 326 $
 */


/**
 * mofilmMovieMusicLicenseSet
 *
 * Manages license associations with a movie.
 *
 * @package mofilm
 * @subpackage movie
 * @license mofilmMovieMusicLicenseSet
 */
class mofilmMovieMusicLicenseSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;



	/**
	 * Returns new instance of mofilmMovieMusicLicenseSet
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieMusicLicenseSet
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
			$this->load();
		}
	}



	/**
	 * Attaches the mofilmMovieMusicLicenseSet to each movie in $inMovies. Expects $inMovies to be indexed by movie ID
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
		 		SELECT movies.ID AS movieID, userLicenses.*
		 		  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
		 		       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieLicenses ON (movies.ID = movieLicenses.movieID)
		 		       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.userLicenes ON (movieLicenses.licenseID = userLicenses.ID)
		 		 WHERE movies.ID IN ('.implode(',', array_keys($inMovies)).')';			

		 	$oStmt = dbManager::getInstance()->prepare($query);
		 	if ( $oStmt->execute() ) {
		 		foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserMusicLicense();
					$oObject->loadFromArray($row);

		 			$properties[$row['movieID']][] = $oObject;
		 		}
		 	}
		 	$oStmt->closeCursor();

		 	if ( false ) $oMovie = new mofilmMovie();
		 	foreach ( $inMovies as $oMovie ) {
		 		if ( $oMovie instanceof mofilmMovie ) {
		 			if ( array_key_exists($oMovie->getID(), $properties) ) {
		 				$oObject = new mofilmMovieMusicLicenseSet();
		 				$oObject->setMovieID($oMovie->getID());
		 				$oObject->setObjects($properties[$oMovie->getID()]);
		 				$oObject->setModified(false);

		 				$oMovie->setLicenseSet($oObject);
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
				DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieLicenses
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
				SELECT userLicenses.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieLicenses
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.userLicenses ON (movieLicenses.licenseID = userLicenses.ID)
				 WHERE movieLicenses.movieID = :MovieID';
			//AND userLicenses.status = :Status
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $this->_MovieID);
			//$oStmt->bindValue(':Status', mofilmUserMusicLicense::VALID_LICENSE);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserMusicLicense();
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
						INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieLicenses
							(movieID, licenseID)
						VALUES ';

					$values = array();
					if ( false ) $oObject = new mofilmUserMusicLicense();
	 				foreach ( $this as $oObject ) {
		 				$oObject->save();

						$values[] = sprintf('(%d, %d)', $this->getMovieID(), $oObject->getID());
	 				}

					if ( count($values) > 0 ) {
						$query .= implode(', ', $values);

						dbManager::getInstance()->exec($query);
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
			/* @var mofilmUserMusicLicense $oObject */
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
	 * @return mofilmMovieMusicLicenseSet
	 */
	function setMovieID($inMovieID) {
		if ( $this->_MovieID !== $inMovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns true if movie has $inlicenseID associated with it
	 * 
	 * @param mixed $inLicenseID Integer or license object
	 * @return boolean
	 */
	function hasLicense($inLicenseID) {
		if ( $inLicenseID instanceof mofilmUserMusicLicense ) {
			$inLicenseID = $inLicenseID->getLicenseID();
		}
		return ($this->getObjectByID($inLicenseID) instanceof mofilmUserMusicLicense);
	}
	
	/**
	 * Returns the license by ID, returns null if not found
	 *
	 * @param integer $inObjectID
	 * @return mofilmUserMusicLicense
	 */
	function getObjectByID($inObjectID) {
		if ( $this->getCount() > 0 ) {
			/* @var mofilmUserMusicLicense $oObject */
			foreach ( $this as $oObject ) {
				if ( $oObject->getLicenseID() == $inObjectID ) {
					return $oObject;
				}
			}
		}
		return null;
	}

	/**
	 * Returns the license with name $inDescription (track name), or null if not found
	 *
	 * @param string $inDescription
	 * @return mofilmUserMusicLicense
	 */
	function getObjectByDescription($inDescription) {
		if ( $this->getCount() > 0 ) {
			/* @var mofilmUserMusicLicense $oObject */
			foreach ( $this as $oObject ) {
				if ( $oObject->getTrackName() == $inDescription ) {
					return $oObject;
				}
			}
		}
		return null;
	}

	/**
	 * Sets an array of license objects to the set
	 *
	 * @param array $inArray Array of mofilmUserMusicLicense objects
	 * @return mofilmMovieMusicLicenseSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the license to the object
	 *
	 * @param mofilmUserMusicLicense $inObject
	 * @return mofilmMovieMusicLicenseSet
	 */
	function setObject(mofilmUserMusicLicense $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Removes the license
	 *
	 * @param mofilmUserMusicLicense $inObject
	 * @return mofilmMovieMusicLicenseSet
	 */
	function removeObjectByValue(mofilmUserMusicLicense $inObject) {
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
			/* @var mofilmUserMusicLicense $oObject */
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
