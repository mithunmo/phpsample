<?php
/**
 * mofilmMovieAssetSet
 * 
 * Stored in assetSet.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieAssetSet
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieAssetSet
 * 
 * Manages movie assets.
 * 
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieAssetSet
 */
class mofilmMovieAssetSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	
	
	/**
	 * Returns new instance of mofilmMovieAssetSet
	 * 
	 * @param integer $inMovieID
	 * @return mofilmMovieAssetSet
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
			$this->load();
		}
	}
	
	

	/**
	 * Attaches the mofilmMovieAssetSet to each movie in $inMovies. Expects $inMovies to be indexed by movie ID
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
		 		SELECT movieAssets.*
		 		  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAssets
		 		 WHERE movieAssets.movieID IN ('.implode(',', array_keys($inMovies)).')';
		 	
		 	$oStmt = dbManager::getInstance()->prepare($query);
		 	if ( $oStmt->execute() ) {
		 		foreach ( $oStmt as $row ) {
		 			$oObject = new mofilmMovieAsset();
		 			$oObject->loadFromArray($row);
		 			$properties[$row['movieID']][] = $oObject; 
		 		}
		 	}
		 	$oStmt->closeCursor();
		 	
		 	if ( false ) $oMovie = new mofilmMovie();
		 	foreach ( $inMovies as $oMovie ) {
		 		if ( $oMovie instanceof mofilmMovie ) {
		 			if ( array_key_exists($oMovie->getID(), $properties) ) {
		 				$oObject = new mofilmMovieAssetSet();
		 				$oObject->setMovieID($oMovie->getID());
		 				$oObject->setObjects($properties[$oMovie->getID()]);
		 				$oObject->setModified(false);
		 				
		 				$oMovie->setAssetSet($oObject);
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
			$this->setObjects(mofilmMovieAsset::listOfObjects(null, null, $this->getMovieID()));
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
					if ( false ) $oObject = new mofilmMovieAsset();
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
	 * @return mofilmMovieAssetSet
	 */
	function setMovieID($inMovieID) {
		if ( $this->_MovieID !== $inMovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the asset by ID, returns null if not found
	 * 
	 * @param integer $inObjectID
	 * @return mofilmMovieAsset
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
	 * Returns a new mofilmMovieAssetSet of assets matching description $inDescription
	 * 
	 * $inDescription is a valid asset description e.g. ThumbNail_150x84
	 * 
	 * @param string $inDescription
	 * @return mofilmMovieAssetSet
	 */
	function getObjectByDescription($inDescription) {
		$oResSet = new mofilmMovieAssetSet();
		$oResSet->setMovieID($this->getMovieID());
		
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getDescription() == $inDescription ) {
					$oResSet->addObject($oObject);
				}
			}
		}
		return $oResSet;
	}
	
	/**
	 * Returns a new mofilmMovieAssetSet of assets matching type $inAssetType
	 * 
	 * Asset type is one of the type constants from {@link mofilmMovieAsset}.
	 * 
	 * @param string $inAssetType
	 * @return mofilmMovieAssetSet
	 */
	function getObjectByAssetType($inAssetType) {
		$oResSet = new mofilmMovieAssetSet();
		$oResSet->setMovieID($this->getMovieID());
		
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getType() == $inAssetType ) {
					$oResSet->addObject($oObject);
				}
			}
		}
		return $oResSet;
	}

	/**
	 * Returns a new mofilmMovieAssetSet of assets matching type $inFileType
	 * 
	 * $inFileType is the file extension required, e.g. MOV, JPG, WMV etc.
	 * 
	 * @param string $inFileType
	 * @return mofilmMovieAssetSet
	 */
	function getObjectByFileType($inFileType) {
		$oResSet = new mofilmMovieAssetSet();
		$oResSet->setMovieID($this->getMovieID());
		
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getExt() == $inFileType ) {
					$oResSet->addObject($oObject);
				}
			}
		}
		return $oResSet;
	}
	
	/**
	 * Returns a new mofilmMovieAssetSet of assets matching $inAssetType and $inFileType
	 * 
	 * $inAssetType is a constant from mofilmMovieAsset
	 * $inFileType is the uppercase file extension e.g. FLV, MOV, AVI etc.
	 * 
	 * @param string $inAssetType
	 * @param string $inFileType
	 * @return mofilmMovieAssetSet
	 */
	function getObjectByAssetAndFileType($inAssetType, $inFileType) {
		$oResSet = new mofilmMovieAssetSet();
		$oResSet->setMovieID($this->getMovieID());
		
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getType() == $inAssetType && $oObject->getExt() == $inFileType ) {
					$oResSet->addObject($oObject);
				}
			}
		}
		return $oResSet;
	}
	
	/**
	 * Returns the first object from the set, empty asset if none found
	 * 
	 * @return mofilmMovieAsset
	 */
	function getFirst() {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				return $oObject;
			}
		}
		return new mofilmMovieAsset();
	}
	
	/**
	 * Sets an array of asset objects to the set
	 * 
	 * @param array $inArray Array of objects
	 * @return mofilmMovieAssetSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}
	
	/**
	 * Add the asset to the set
	 *
	 * @param mofilmMovieAsset $inObject
	 * @return mofilmMovieAssetSet
	 */
	function setObject(mofilmMovieAsset $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Add the asset to the set
	 *
	 * @param mofilmMovieAsset $inObject
	 * @return mofilmMovieAssetSet
	 */
	function addObject(mofilmMovieAsset $inObject) {
		return $this->setObject($inObject);
	}
	
	/**
	 * Marks the asset with $inObjectID for removal
	 * 
	 * @param integer $inObjectID
	 * @return mofilmMovieAssetSet
	 */
	function removeObjectByID($inObjectID) {
		$oObject = $this->getObjectByID($inObjectID);
		if ( $oObject instanceof mofilmMovieAsset ) {
			$oObject->setMarkForDeletion(true);
		}
		return $this;
	}
}