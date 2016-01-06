<?php
/**
 * mofilmSourceTrackSet
 *
 * Stored in trackSet.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage source
 * @category mofilmSourceTrackSet
 * @version $Rev: 10 $
 */


/**
 * mofilmSourceTrackSet
 *
 * Manages which tracks are associated with the source.
 *
 * @package mofilm
 * @subpackage source
 * @category mofilmSourceTrackSet
 */
class mofilmSourceTrackSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_SourceID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_SourceID;



	/**
	 * Returns new instance of mofilmSourceTrackSet
	 *
	 * @param integer $inSourceID
	 * @return mofilmSourceTrackSet
	 */
	function __construct($inSourceID = null) {
		$this->reset();
		if ( $inSourceID !== null ) {
			$this->setSourceID($inSourceID);
			$this->load();
		}
	}



	/**
	 * Attaches the mofilmSourceTrackSet to each source in $inSources. Expects $inSources to be indexed by source ID
	 *
	 * @param array $inSources
	 * @return boolean
	 * @static
	 */
	static function loadArrayOfSourcesWithProperties(array $inSources) {
		 $return = false;
		 $properties = array();
		 if ( count($inSources) > 0 ) {
		 	$query = '
		 		SELECT downloadSources.sourceID, downloadSources.downloadHash, downloadFiles.*
		 		  FROM '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles
		 		       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.downloadSources ON (downloadFiles.ID = downloadSources.downloadID)
		 		 WHERE downloadSources.sourceID IN ('.implode(',', array_keys($inSources)).')
		 		   AND downloadFiles.filetype = "music"';

		 	$oStmt = dbManager::getInstance()->prepare($query);
		 	if ( $oStmt->execute() ) {
		 		foreach ( $oStmt as $row ) {
					$oObject = new mofilmTrack();
					$oObject->loadFromArray($row);

		 			$properties[$row['sourceID']][] = $oObject;
		 		}
		 	}
		 	$oStmt->closeCursor();

		 	if ( false ) $oObject = new mofilmSource();
		 	foreach ( $inSources as $oObject ) {
		 		if ( $oObject instanceof mofilmSource ) {
		 			if ( array_key_exists($oObject->getID(), $properties) ) {
		 				$oObject = new mofilmSourceTrackSet();
		 				$oObject->setSourceID($oObject->getID());
		 				$oObject->setObjects($properties[$oObject->getID()]);
		 				$oObject->setModified(false);

		 				$oObject->setTrackSet($oObject);
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
		if ( $this->_SourceID ) {
			$query = '
				DELETE '.system::getConfig()->getDatabase('mofilm_content').'.downloadSources
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.downloadSources
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles
				 WHERE downloadFiles.ID = downloadSources.downloadID
				   AND downloadSources.sourceID = :SourceID
				   AND downloadFiles.filetype = "music"';

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':SourceID', $this->_SourceID);
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
		if ( $this->_SourceID ) {
			$query = '
				SELECT downloadSources.downloadHash, downloadFiles.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.downloadSources
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles ON (downloadSources.downloadID = downloadFiles.ID)
				 WHERE downloadSources.sourceID = :SourceID
				   AND downloadFiles.filetype = "music"';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':SourceID', $this->_SourceID);
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
		$this->_SourceID = 0;
		return $this->_resetSet();
	}

	/**
	 * Saves object
	 *
	 * @return boolean
	 * @see systemDaoInterface::save()
	 */
	function save() {
		if ( $this->_SourceID ) {
			if ( $this->isModified() ) {
				/*
 				 * Delete existing records so we dont get duplicates or old records
 				 */
	 			$this->delete();

				if ( $this->getCount() > 0 ) {
					$query = '
						INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.downloadSources
							(downloadID, sourceID, downloadHash)
						VALUES ';

					$values = array();
					if ( false ) $oObject = new mofilmTrack();
	 				foreach ( $this as $oObject ) {
		 				$oObject->save();

		 				$values[] = "({$oObject->getID()}, $this->_SourceID, ".dbManager::getInstance()->quote($oObject->getDownloadHash()).")";
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
	 * Returns $_SourceID
	 *
	 * @return integer
	 */
	function getSourceID() {
		return $this->_SourceID;
	}

	/**
	 * Sets $_SourceID to $inSourceID
	 *
	 * @param integer $inSourceID
	 * @return mofilmSourceTrackSet
	 */
	function setSourceID($inSourceID) {
		if ( $this->_SourceID !== $inSourceID ) {
			$this->_SourceID = $inSourceID;
			$this->_Modified = true;
		}
		return $this;
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
	 * @return mofilmSourceTrackSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the track to the object
	 *
	 * @param mofilmTrack $inObject
	 * @return mofilmSourceTrackSet
	 */
	function setObject(mofilmTrack $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Removes the track
	 *
	 * @param mofilmTrack $inObject
	 * @return mofilmSourceTrackSet
	 */
	function removeObject(mofilmTrack $inObject) {
		return $this->_removeItemWithValue($inObject);
	}
}