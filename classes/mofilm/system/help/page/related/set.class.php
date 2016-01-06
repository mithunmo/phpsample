<?php
/**
 * mofilmSystemHelpPageRelatedSet
 *
 * Stored in mofilmSystemHelpPageRelatedSet.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2011
 * @package mofilm
 * @subpackage mofilmSystemHelp
 * @category mofilmSystemHelpPageRelatedSet
 * @version $Rev: 256 $
 */


/**
 * mofilmSystemHelpPageRelatedSet Class
 *
 * Handles a set of objects.
 *
 * @package mofilm
 * @subpackage mofilmSystemHelp
 * @category mofilmSystemHelpPageRelatedSet
 */
class mofilmSystemHelpPageRelatedSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores the parent objects primary key
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_HelpPageID;


	/**
	 * Creates a new set instance
	 *
	 * @param integer $inHelpPageID
	 */
	function __construct($inHelpPageID = null) {
		$this->reset();
		$this->setHelpPageID($inHelpPageID);
		$this->load();
	}

	/**
	 * Injects a pre-built array of objects into the array
	 *
	 * $inArray is an associative array indexed by the primary key. The
	 * objects within this array should have a "set" method corresponding
	 * to this set object.
	 *
	 * @param array $inArray An array of mofilmSystemHelpTags objects
	 * @return boolean
	 * @static
	 */
	static function loadArrayOfObjectsWithProperties(array $inArray) {
		$return = false;
		$properties = array();
		if ( count($inArray) > 0 ) {
			$query = '
				SELECT helpRelations.helpID, helpPages.*
				  FROM '.system::getConfig()->getDatabase('system').'.helpRelations
					   INNER JOIN '.system::getConfig()->getDatabase('system').'.helpPages ON (helpRelations.relatedHelpID = helpPages.ID)
				 WHERE helpRelations.helpID IN ('.implode(',', array_keys($inArray)).')';

			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmSystemHelpPages();
					$oObject->loadFromArray($row);
					$properties[$row['helpID']][] = $oObject;
				}
			}
			$oStmt->closeCursor();

			/* @var mofilmSystemHelpPages $oObject */
			foreach ( $inArray as $oObject ) {
				if ( $oObject instanceof mofilmSystemHelpPages ) {
					if ( array_key_exists($oObject->getID(), $properties) ) {
						$oObjectSet = new mofilmSystemHelpPageRelatedSet();
						$oObjectSet->setHelpPageID($oObject->getID());
						$oObjectSet->setObjects($properties[$oObject->getID()]);
						$oObjectSet->setModified(false);

						$oObject->setRelatedSet($oObjectSet);
						$return = true;
					}
				}
			}
		}

		return $return;
	}

	/**
	 * Loads the set with data
	 *
	 * @return boolean
	 */
	function load() {
		if ( $this->getHelpPageID() ) {
			$query = '
				SELECT helpPages.*
				  FROM '.system::getConfig()->getDatabase('system').'.helpRelations
				       INNER JOIN '.system::getConfig()->getDatabase('system').'.helpPages ON (helpRelations.relatedHelpID = helpPages.ID)
				 WHERE helpRelations.helpID = :HelpID';

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':HelpID', $this->getHelpPageID());
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmSystemHelpPages();
					$oObject->loadFromArray($row);

					$this->addObject($oObject);
				}
			}

			$this->setModified(false);

			return true;
		}

		return false;
	}

	/**
	 * Saves changes to the objects in the set
	 *
	 * @return boolean
	 */
	function save() {
		if ( $this->getHelpPageID() ) {
			if ( false ) {
				$oObject = new mofilmSystemHelpPages(); // for IDE auto-complete
			}

			/*
			 * Clear all current relationships
			 */
			$this->delete();

			$values = array();
			foreach ( $this as $oObject ) {
				if ( !$oObject->getID() ) {
					$oObject->save();
				}

				$values[] = sprintf('(%d, %d)', $this->getHelpPageID(), $oObject->getID());
			}

			if ( count($values) > 0 ) {
				$query = '
					INSERT INTO '.system::getConfig()->getDatabase('system').'.helpRelations
						(helpID, relatedHelpID)
					VALUES';

				$query .= implode(', ', $values);

				$oStmt = dbManager::getInstance()->prepare($query);
				$oStmt->execute();
				$oStmt->closeCursor();
			}

			$this->setModified(false);

			return true;
		}

		return false;
	}

	/**
	 * Deletes the objects in the set
	 *
	 * @return boolean
	 */
	function delete() {
		$return = false;
		if ( $this->getHelpPageID() ) {
			$query = '
				DELETE FROM '.system::getConfig()->getDatabase('system').'.helpRelations
				 WHERE helpID = :HelpID';

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':HelpID', $this->getHelpPageID());
			if ( $oStmt->execute() ) {
				$return = true;
			}
			$oStmt->closeCursor();
		}

		return $return;
	}

	/**
	 * Returns the array of objects
	 *
	 * @return array
	 */
	function toArray() {
		return $this->_getItem();
	}

	/**
	 * Resets the object to defaults
	 *
	 * @return void
	 */
	function reset() {
		$this->_HelpPageID = null;
		parent::_resetSet();
	}


	/**
	 * Returns true if the object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( $this->getCount() > 0 ) {
			/* @var mofilmSystemHelpPages $oObject */
			foreach ( $this as $oObject ) {
				$modified = $oObject->isModified() || $modified;
			}
		}

		return $modified;
	}

	/**
	 * Returns the value of $_HelpPageId
	 *
	 * @return integer
	 */
	function getHelpPageID() {
		return $this->_HelpPageID;
	}

	/**
	 * Set the parent content Id to $inHelpPageId
	 *
	 * @param integer $inHelpPageID
	 * @return mofilmSystemHelpPageRelatedSet
	 */
	function setHelpPageID($inHelpPageID) {
		if ( $inHelpPageID !== $this->_HelpPageID ) {
			$this->_HelpPageID = $inHelpPageID;
			$this->setModified();
		}
		return $this;
	}


	/**
	 * Returns the first object in the set
	 *
	 * @return mofilmSystemHelpPages
	 */
	function getFirst() {
		return $this->_getItem(0);
	}

	/**
	 * Returns the last object in the set
	 *
	 * @return mofilmSystemHelpPages
	 */
	function getLast() {
		return $this->_getItem($this->getCount() - 1);
	}

	/**
	 * Returns the object with Id $inId, or null if not found
	 *
	 * @param integer $inId
	 * @return mixed
	 */
	function getObjectById($inId) {
		if ( $this->getCount() > 0 ) {
			/* @var mofilmSystemHelpPages $oObject */
			foreach ( $this as $oObject ) {
				if ( $oObject->getID() == $inId ) {
					return $oObject;
				}
			}
		}

		return null;
	}

	/**
	 * Add an object to the set
	 *
	 * @param mofilmSystemHelpPages $inObject
	 * @return mofilmSystemHelpPageRelatedSet
	 */
	function addObject(mofilmSystemHelpPages $inObject) {
		$this->_setValue($inObject);
		return $this;
	}

	/**
	 * Marks the object for removal
	 *
	 * @param integer $inId
	 * @return mofilmSystemHelpPageRelatedSet
	 */
	function removeObject($inId) {
		$oObject = $this->getObjectById($inId);

		if ( $oObject ) {
			$this->_removeItemWithValue($oObject);
		}

		return $this;
	}

	/**
	 * Sets an array of objects to the set
	 *
	 * @param array $inObjects
	 * @return mofilmSystemHelpPageRelatedSet
	 */
	function setObjects(array $inObjects) {
		return $this->_setItem($inObjects, null);
	}
}