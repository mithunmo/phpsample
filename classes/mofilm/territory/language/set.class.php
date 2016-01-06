<?php
/**
 * mofilmTerritoryLanguageSet
 *
 * Stored in set.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmTerritoryLanguageSet
 * @version $Rev: 10 $
 */


/**
 * mofilmTerritoryLanguageSet
 *
 * Loads territory languages.
 *
 * @package mofilm
 * @subpackage movie
 * @category mofilmTerritoryLanguageSet
 */
class mofilmTerritoryLanguageSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_TerritoryID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TerritoryID;



	/**
	 * Returns new instance of mofilmTerritoryLanguageSet
	 *
	 * @param integer $inTerritoryID
	 * @return mofilmTerritoryLanguageSet
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
			$query = '
				DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.territoryLanguages
				 WHERE territoryLanguages.territoryID = :TerritoryID';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':TerritoryID', $this->getTerritoryID());
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
		if ( $this->_TerritoryID ) {
			$query = '
				SELECT languages.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.territoryLanguages
					   INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.languages ON (territoryLanguages.languageID = languages.ID)
				 WHERE territoryLanguages.territoryID = :TerritoryID';

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':TerritoryID', $this->_TerritoryID);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmLanguage();
					$oObject->loadFromArray($row);
					$this->setObject($oObject);
				}
				$oStmt->closeCursor();
			}
			
			/*
			 * Always load English if no language mapped yet
			 */
			if ( $this->getCount() == 0 ) {
				$this->setObject(mofilmLanguage::getInstance(mofilmLanguage::LANG_EN));
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
				/*
 				 * Delete existing records so we dont get duplicates or old records
 				 */
	 			$this->delete();

				if ( $this->getCount() > 0 ) {
					$query = '
						INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.territoryLanguages
							(territoryID, languageID)
						VALUES ';

					$values = array();
					if ( false ) $oObject = new mofilmLanguage();
	 				foreach ( $this as $oObject ) {
		 				$oObject->save();

		 				$values[] = "($this->_TerritoryID, {$oObject->getID()})";
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
	 * @return mofilmTerritoryLanguageSet
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
	 * @return mofilmLanguage
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
	 * @return mofilmLanguage
	 */
	function getObjectByDescription($inDescription) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmLanguage();
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
	 * @return mofilmLanguage
	 */
	function getFirst() {
		return $this->_getItem(0);
	}

	/**
	 * Sets an array of objects to the set
	 *
	 * @param array $inArray Array of mofilmLanguage objects
	 * @return mofilmTerritoryLanguageSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the object to the set
	 *
	 * @param mofilmLanguage $inObject
	 * @return mofilmTerritoryLanguageSet
	 */
	function setObject(mofilmLanguage $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Removes the object from the set
	 *
	 * @param mofilmLanguage $inObject
	 * @return mofilmTerritoryLanguageSet
	 */
	function removeObjectByValue(mofilmLanguage $inObject) {
		return $this->_removeItemWithValue($inObject);
	}

	/**
	 * Removes the object by ID
	 *
	 * @param integer $inID
	 * @return mofilmTerritoryLanguageSet
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