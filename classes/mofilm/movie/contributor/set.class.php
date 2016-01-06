<?php
/**
 * mofilmMovieContributorSet
 * 
 * Stored in set.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieContributorSet
 * @version $Rev: 10 $
 */



/**
 * mofilmMovieContributorSet Class
 * 
 * Links contributors to movies
 * 
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieContributorSet
 */
class mofilmMovieContributorSet extends baseSet implements systemDaoInterface {
	
	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID = false;
	
	
	
	/**
	 * Returns an instance of mofilmMovieContributorSet
	 *
	 * @param integer $inMovieID
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
		}
	}
	
	/**
	 * Custom destructor to ensure sub-objects are unloaded
	 *
	 * @return void
	 */
	function __destruct() {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmMovieContributorMap();
			foreach ( $this as $oObject ) {
				$oObject->__destruct();
			}
		}
	}
	
	/**
	 * Attaches the mofilmMovieContributorSet to each product in $inProducts. Expects $inProducts to be indexed by productID
	 *
	 * @param array $inProducts
	 * @return boolean
	 * @static
	 */
	static function loadArrayOfMoviesWithProperties(array $inMovies) {
		 $return = false;
		 $mapObjects = array();
		 if ( count($inMovies) > 0 ) {
		 	$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieContributors WHERE movieID IN ('.implode(',', array_keys($inMovies)).')';
		 	$oStmt = dbManager::getInstance()->prepare($query);
		 	if ( $oStmt->execute() ) {
		 		foreach ( $oStmt as $row ) {
		 			$oObject = new mofilmMovieContributorMap();
		 			$oObject->loadFromArray($row);
		 			
		 			$mapObjects[$row['movieID']][] = $oObject;
		 		}
		 	}
		 	$oStmt->closeCursor();
		 	
		 	if ( false ) $oObject = new mofilmMovie();
		 	foreach ( $inMovies as $oObject ) {
		 		if ( $oObject instanceof mofilmMovie ) {
		 			if ( array_key_exists($oObject->getID(), $mapObjects) ) {
		 				$oSet = new mofilmMovieContributorSet();
		 				$oSet->setMovieID($oObject->getID());
		 				$oSet->setContributorMappings($mapObjects[$oObject->getID()]);
		 				
		 				$oObject->setContributorSet($oSet);
		 				$return = true;
		 			}
		 		}
		 	}
		 }
		 return $return;
	}
	
	
	
	/**
	 * Loads the artists
	 *
	 * @return boolean
	 */
	function load() {
		if ( $this->_MovieID ) {
			$this->_setItem(mofilmMovieContributorMap::listOfObjects(null, null, $this->getMovieID()));
			$this->setModified(false);
		}
		return true;
	}
	
	/**
 	 * Save all changes to objects to the database
 	 *
 	 * @return boolean
 	 */
 	function save() {
 		if ( $this->_MovieID ) {
 			if ( $this->isModified() ) {
	 			if ( $this->getCount() > 0 ) {
	 				if ( false ) $oObject = new mofilmMovieContributorMap();
	 				foreach ( $this as $oObject ) {
	 					if ( $oObject->getMarkForDeletion() ) {
	 						$oObject->delete();
	 					} else {
	 						$oObject->setMovieID($this->getMovieID());
	 						$oObject->save();
	 					}
	 				}
	 				$this->setModified(false);
	 			}
 			}
 		}
 		return true;
 	}
 	
 	/**
 	 * Deletes the items in the set
 	 *
 	 * @return boolean
 	 */
 	function delete() {
 		if ( $this->_MovieID ) {
 			$query = '
				DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieContributors
				 WHERE movieID = :MovieID';
		
			try {
				$oStmt = dbManager::getInstance()->prepare($query);
				$oStmt->bindValue(':MovieID', $this->_MovieID);
				$oStmt->execute();
				$oStmt->closeCursor();
				return true;
				
			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
				throw $e;
			}
 		}
 		return false;
 	}
 	
 	/**
 	 * Resets the set
 	 *
 	 * @return baseSet
 	 */
 	function reset() {
 		return parent::_resetSet();
 	}
 	
 	/**
 	 * Marks all objects in set to be removed
 	 *
 	 * @return mofilmMovieContributorSet
 	 */
 	function markAllRemoved() {
 		if ( $this->getCount() > 0 ) {
	 		foreach ( $this as $oObject ) {
	 			$oObject->setMarkForDelation(true);
	 		}
 		}
 		return $this;
 	}
 	
 	/**
 	 * Returns object properties as array
 	 *
 	 * @return array
 	 */
 	function toArray() {
 		return get_class_vars($this);
 	}
 	
 	
 	
	/**
	 * Returns true if the set or an item in it has been modified
	 *
	 * @return boolean
	 */
 	function isModified() {
 		$modified = $this->_Modified;
 		if ( !$modified && $this->getCount() > 0 ) {
 			if ( false ) $oObject = new mofilmMovieContributorMap();
 			foreach ( $this as $oObject ) {
 				$modified = $modified || $oObject->isModified();
 			}
 		}
 		return $modified;
 	}
 	
	/**
	 * Returns $inMovieID
	 *
	 * @return integer
	 */
	function getMovieID() {
		return $this->_MovieID;
	}
	
	/**
	 * Set $inMovieID property
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieContributorSet
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns the mapping with $inKey
	 *
	 * @param integer $inKey
	 * @return mofilmMovieContributorMap
	 */
	function getContributor($inKey) {
		return $this->_getItem($inKey);
	}
	
	/**
	 * Updates a mapping between a movie and a contributor
	 *
	 * @param mofilmMovieContributorMap $inMap
	 * @return mofilmMovieContributorSet
	 */
	function setContributor(mofilmMovieContributorMap $inMap) {
		if ( $this->getMappingByContributorMap($inMap) ) {
			$this->getMappingByContributorMap($inMap)->setMarkForDeletion(false);
		} else {
			$inMap->setMarkForDeletion(false);
			$this->_setValue($inMap);
		}
		return $this;
	}
	
	/**
	 * Set an entire array of mappings to the set, $inMappings is array(mofilmMovieContributorMap)
	 *
	 * @param array $inMappings
	 * @return mofilmMovieContributorSet
	 */
	function setContributorMappings(array $inMappings) {
		return $this->_setItem($inMappings);
	}
	
	/**
	 * Removes the mofilmMovieContributorMap
	 *
	 * @param mofilmMovieContributorMap $inMap
	 * @return mofilmMovieContributorSet
	 */
	function removeContributor(mofilmMovieContributorMap $inMap) {
		return parent::_removeItemWithValue($inMap);
	}
	
	/**
	 * Returns the mofilmMovieContributorMap if one is set in this set, false otherwise
	 *
	 * @param mofilmMovieContributorMap $inMap
	 * @return mofilmMovieContributorMap
	 */
	function getMappingByContributorMap(mofilmMovieContributorMap $inMap) {
		if ( $this->getCount() > 0 && ($inMap->getContributorID() > 0 && $inMap->getRoleID() > 0) ) {
			if ( false ) $oObject = new mofilmMovieContributorMap();
			foreach ( $this as $oObject ) {
				if ( $oObject->getContributorID() == $inMap->getContributorID() && $oObject->getRoleID() == $inMap->getRoleID() ) {
					return $oObject;
				}
			}
		}
		return false;
	}

	/**
	 * Returns the mofilmContributor if one is set in this set matching the contributor and role, false otherwise
	 *
	 * @param mofilmContributor $inContributor
	 * @param integer $inRoleID
	 * @return mofilmContributorMap
	 */
	function getMappingByContributorRole(mofilmContributor $inContributor, $inRoleID) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmMovieContributorMap();
			foreach ( $this as $oObject ) {
				if ( $oObject->getContributorID() == $inContributor->getContributorID() && $oObject->getRoleID() == $inRoleID ) {
					return $oObject;
				}
			}
		}
		return false;
	}

	/**
	 * Returns the mofilmMovieContributorMap matching $inContributorID, false if not in set
	 *
	 * @param integer $inContributorID
	 * @return mofilmMovieContributorMap
	 */
	function getMappingByContributorID($inContributorID) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmMovieContributorMap();
			foreach ( $this as $oObject ) {
				if ( $oObject->getContributorID() == $inContributorID ) {
					return $oObject;
				}
			}
		}
		return false;
	}
}