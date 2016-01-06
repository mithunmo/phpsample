<?php
/**
 * mofilmTerritoryStateSet
 *
 * Stored in set.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmTerritoryStateSet
 * @version $Rev: 10 $
 */


/**
 * mofilmTerritoryStateSet
 *
 * Loads territory states.
 *
 * @package mofilm
 * @subpackage movie
 * @category mofilmTerritoryStateSet
 */
class mofilmTerritoryStateSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_TerritoryID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TerritoryID;



	/**
	 * Returns new instance of mofilmTerritoryStateSet
	 *
	 * @param integer $inTerritoryID
	 * @return mofilmTerritoryStateSet
	 */
	function __construct($inTerritoryID = null) {
		$this->reset();
		if ( $inTerritoryID !== null ) {
			$this->setTerritoryID($inTerritoryID);
			$this->load();
		}
	}

	/**
	 * Deletes relations between object and sub-objects
	 *
	 * @return boolean
	 * @see systemDaoInterface::delete()
	 */
	function delete() {
		if ( $this->getTerritoryID() ) {
			foreach ( $this as $oObject ) {
				$oObject->delete();
			}
			return true;
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
		if ( $this->_TerritoryID ) {
			$this->_setItem(mofilmTerritoryState::listOfObjects(null, null, $this->getTerritoryID()));
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
		$this->_TerritoryID = 0;
		return $this->_resetSet();
	}

	/**
	 * Saves object and sub-objects
	 *
	 * @return boolean
	 * @see systemDaoInterface::save()
	 */
	function save() {
		if ( $this->getTerritoryID() ) {
			if ( $this->isModified() ) {
				if ( $this->getCount() > 0 ) {
					if ( false ) $oObject = new mofilmTerritoryState();
	 				foreach ( $this as $oObject ) {
	 					$oObject->setTerritoryID($this->getTerritoryID());
	 					if ( $oObject->getMarkForDeletion() ) {
	 						$oObject->delete();
	 					} else {
		 					$oObject->save();
	 					}
	 				}
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
	 * Returns territory ID
	 *
	 * @return integer
	 */
	function getTerritoryID() {
		return $this->_TerritoryID;
	}

	/**
	 * Sets territoryID to $inTerritoryID
	 *
	 * @param integer $inTerritoryID
	 * @return mofilmTerritoryStateSet
	 */
	function setTerritoryID($inTerritoryID) {
		if ( $this->_TerritoryID !== $inTerritoryID ) {
			$this->_TerritoryID = $inTerritoryID;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the object by ID, returns null if not found
	 *
	 * @param integer $inObjectID
	 * @return mofilmTerritoryState
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
	 * Returns the source with name $inDescription, or null if not found
	 *
	 * @param string $inDescription
	 * @return mofilmTerritoryState
	 */
	function getObjectByDescription($inDescription) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmTerritoryState();
			foreach ( $this as $oObject ) {
				if ( $oObject->getName() == $inDescription ) {
					return $oObject;
				}
			}
		}
		return null;
	}
	
	/**
	 * Returns the first object in the set
	 * 
	 * @return mofilmTerritoryState
	 */
	function getFirst() {
		return $this->_getItem(0);
	}

	/**
	 * Sets an array of objects to the set
	 *
	 * @param array $inArray Array of mofilmTerritoryState objects
	 * @return mofilmTerritoryStateSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the object to the set
	 *
	 * @param mofilmTerritoryState $inObject
	 * @return mofilmTerritoryStateSet
	 */
	function setObject(mofilmTerritoryState $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Removes the object from the set
	 *
	 * @param mofilmTerritoryState $inObject
	 * @return mofilmTerritoryStateSet
	 */
	function removeObjectByValue(mofilmTerritoryState $inObject) {
		return $this->_removeItemWithValue($inObject);
	}

	/**
	 * Removes the object by ID
	 *
	 * @param integer $inID
	 * @return mofilmTerritoryStateSet
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