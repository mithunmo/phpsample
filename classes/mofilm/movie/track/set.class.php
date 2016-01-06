<?php
/**
 * mofilmMovieTrackSet
 *
 * Stored in trackSet.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieTrackSet
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieTrackSet
 *
 * Manages which tracks are associated with the movie.
 *
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieTrackSet
 */
class mofilmMovieTrackSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;



	/**
	 * Returns new instance of mofilmMovieTrackSet
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieTrackSet
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
			$this->load();
		}
	}



	/**
	 * Attaches the mofilmMovieTrackSet to each movie in $inMovies. Expects $inMovies to be indexed by movie ID
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
				SELECT movieTracks.movieID, downloadFiles.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieTracks ON (movieTracks.downloadID = downloadFiles.ID)
				 WHERE movieTracks.movieID IN ('.implode(',', array_keys($inMovies)).')';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmTrack();
					$oObject->loadFromArray($row);
		 			$properties[$row['movieID']][] = $oObject;
				}
			}
			$oStmt->closeCursor();
			
			if ( false ) $oMovie = new mofilmMovie();
			foreach ( $inMovies as $oMovie ) {
				if ( $oMovie instanceof mofilmMovie ) {
					if ( array_key_exists($oMovie->getID(), $properties) ) {
						$oObject = new mofilmMovieTrackSet();
						$oObject->setMovieID($oMovie->getID());
						$oObject->setObjects($properties[$oMovie->getID()]);
						$oObject->setModified(false);
						
		 				$oMovie->setTrackSet($oObject);
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
				DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieTracks
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
				SELECT downloadFiles.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieTracks
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles ON (movieTracks.downloadID = downloadFiles.ID)
				 WHERE movieTracks.movieID = :MovieID';

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $this->_MovieID);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmTrack();
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
						INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieTracks
							(movieID, downloadID)
						VALUES ';

					$values = array();
					if ( false ) $oObject = new mofilmTrack();
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
	 * @return mofilmMovieTrackSet
	 */
	function setMovieID($inMovieID) {
		if ( $this->_MovieID !== $inMovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns the first track in the set
	 */
	function getTrack() {
		if ( $this->getCount() > 0 ) {
			return $this->_getItem(0);
		}
		return null;
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
	 * Returns an array of the object IDs
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
	 * Returns the track with $inDigitalISRC, or null if not found
	 *
	 * @param string $inDigitalISRC
	 * @return mofilmTrack
	 */
	function getObjectByDigitalISRC($inDigitalISRC) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmTrack();
			foreach ( $this as $oObject ) {
				if ( $oObject->getDigitalISRC() == $inDigitalISRC ) {
					return $oObject;
				}
			}
		}
		return null;
	}

	/**
	 * Sets an array of track objects to the set
	 *
	 * @param array $inArray Array of mofilmTrack objects
	 * @return mofilmMovieTrackSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the track to the object
	 *
	 * @param mofilmTrack $inObject
	 * @return mofilmMovieTrackSet
	 */
	function setObject(mofilmTrack $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Removes the track
	 *
	 * @param mofilmTrack $inObject
	 * @return mofilmMovieTrackSet
	 */
	function removeObjectByValue(mofilmTrack $inObject) {
		return $this->_removeItemWithValue($inObject);
	}

	/**
	 * Removes the track by ID
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