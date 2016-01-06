<?php
/**
 * mofilmEventSourceSet
 *
 * Stored in set.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmEventSourceSet
 * @version $Rev: 10 $
 */


/**
 * mofilmEventSourceSet
 *
 * Manages event sources.
 *
 * @package mofilm
 * @subpackage movie
 * @category mofilmEventSourceSet
 */
class mofilmEventSourceSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_EventID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_EventID;



	/**
	 * Returns new instance of mofilmEventSourceSet
	 *
	 * @param integer $inEventID
	 * @return mofilmEventSourceSet
	 */
	function __construct($inEventID = null) {
		$this->reset();
		if ( $inEventID !== null ) {
			$this->setEventID($inEventID);
			$this->load();
		}
	}



	/**
	 * Attaches the mofilmEventSourceSet to each event in $inEvents. Expects $inEvents to be indexed by event ID
	 *
	 * @param array $inEvents
	 * @return boolean
	 * @static
	 */
	static function loadArrayOfEventsWithProperties(array $inEvents) {
		 $return = false;
		 $properties = array();
		 if ( count($inEvents) > 0 ) {
		 	$query = '
		 		SELECT sources.*
		 		  FROM '.system::getConfig()->getDatabase('mofilm_content').'.sources
		 		 WHERE sources.eventID IN ('.implode(',', array_keys($inEvents)).')
		 		 ORDER BY sources.eventID ASC';

		 	$oStmt = dbManager::getInstance()->prepare($query);
		 	if ( $oStmt->execute() ) {
		 		foreach ( $oStmt as $row ) {
		 			$oObject = new mofilmSource();
		 			$oObject->loadFromArray($row);
		 			$properties[$row['eventID']][] = $oObject;
		 		}
		 	}
		 	$oStmt->closeCursor();

		 	if ( false ) $oEvent = new mofilmEvent();
		 	foreach ( $inEvents as $oEvent ) {
		 		if ( $oEvent instanceof mofilmEvent ) {
		 			if ( array_key_exists($oEvent->getID(), $properties) ) {
		 				$oObject = new mofilmEventSourceSet();
		 				$oObject->setEventID($oEvent->getID());
		 				$oObject->setObjects($properties[$oEvent->getID()]);
		 				$oObject->setModified(false);

		 				$oEvent->setSourceSet($oObject);
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
		if ( $this->_EventID && $this->getCount() > 0 ) {
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
		if ( $this->_EventID ) {
			$this->setObjects(mofilmSource::listOfObjects(null, null, $this->getEventID()));
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
		$this->_EventID = 0;
		return $this->_resetSet();
	}

	/**
	 * Saves object
	 *
	 * @return boolean
	 * @see systemDaoInterface::save()
	 */
	function save() {
		if ( $this->_EventID ) {
			if ( $this->isModified() ) {
				if ( $this->getCount() > 0 ) {
					if ( false ) $oObject = new mofilmSource();
	 				foreach ( $this as $oObject ) {
	 					$oObject->setEventID($this->getEventID());
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
	 * Returns eventID
	 *
	 * @return integer
	 */
	function getEventID() {
		return $this->_EventID;
	}

	/**
	 * Sets the eventID to $inEventID
	 *
	 * @param integer $inEventID
	 * @return mofilmEventSourceSet
	 */
	function setEventID($inEventID) {
		if ( $this->_EventID !== $inEventID ) {
			$this->_EventID = $inEventID;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the object by ID, returns null if not found
	 *
	 * @param integer $inTagID
	 * @return mofilmSource
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
	 * @return mofilmSource
	 */
	function getObjectByIndex($inIndex) {
		return $this->_getItem($inIndex);
	}

	/**
	 * Sets an array of award objects to the set
	 *
	 * @param array $inArray Array of objects
	 * @return mofilmEventSourceSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the award to the object
	 *
	 * @param mofilmSource $inObject
	 * @return mofilmEventSourceSet
	 */
	function setObject(mofilmSource $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Marks the award by $inID for deletion
	 *
	 * @param integer $inID
	 * @return mofilmEventSourceSet
	 */
	function removeObjectByID($inID) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmSource();
			foreach ( $this as $oObject ) {
				if ( $oObject->getID() == $inID ) {
					$oObject->setMarkForDeletion(true);
				}
			}
		}
		return $this;
	}
}