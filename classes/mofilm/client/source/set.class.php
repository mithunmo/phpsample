<?php
/**
 * mofilmClientSourceSet
 *
 * Stored in set.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage user
 * @category mofilmClientSourceSet
 * @version $Rev: 10 $
 */


/**
 * mofilmClientSourceSet
 *
 * Loads sources that a user can access.
 *
 * @package mofilm
 * @subpackage user
 * @category mofilmClientSourceSet
 */
class mofilmClientSourceSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_ClientID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_ClientID;
	
	
	
	/**
	 * Returns new instance of mofilmClientSourceSet
	 *
	 * @param integer $inClientID
	 * @return mofilmClientSourceSet
	 */
	function __construct($inClientID = null) {
		$this->reset();
		if ( $inClientID !== null ) {
			$this->setClientID($inClientID);
			$this->load();
		}
	}

	/**
	 * Required for interface, but sources cannot be deleted
	 *
	 * @return boolean
	 * @see systemDaoInterface::delete()
	 */
	function delete() {
		$return = false;
		if ( $this->getClientID() ) {
			$query = '
				DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.clientSources
				 WHERE clientID = :ClientID';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ClientID', $this->getClientID(), PDO::PARAM_INT);
			if ( $oStmt->execute() ) {
				$return = true;
			}
			$oStmt->closeCursor();
		}
		return $return;
	}

	/**
	 * Loads the set with data
	 *
	 * @return boolean
	 * @see systemDaoInterface::load()
	 */
	function load() {
		if ( $this->_ClientID ) {
			$query = '
				SELECT sources.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.clientSources
					   INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (sources.ID = clientSources.sourceID)
				 WHERE clientSources.clientID = :ClientID';

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ClientID', $this->_ClientID);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmSource();
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
		$this->_ClientID = null;
		return $this->_resetSet();
	}

	/**
	 * Saves object, required for interface - changes cannot be saved
	 *
	 * @return boolean
	 * @see systemDaoInterface::save()
	 */
	function save() {
		$return = false;
		if ( $this->getClientID() ) {
			if ( $this->isModified() ) {
				/*
				 * Clear existing entries
				 */
				$this->delete();
				
				if ( $this->getCount() ) {
					$values = array();
					foreach ( $this as $oSource ) {
						if ( $oSource->getID() ) {
							$values[] = "({$this->getClientID()}, {$oSource->getID()})";
						}
					}
					
					if ( count($values) > 0 ) {
						$query = '
							INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.clientSources
								(clientID, sourceID)
							VALUES '.implode(', ', $values);
						
						$oStmt = dbManager::getInstance()->prepare($query);
						if ( $oStmt->execute() ) {
							$return = true;
						}
						$oStmt->closeCursor();
					}
				}
			}
		}
		return $return;
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
	 * Returns $_ClientID
	 *
	 * @return integer
	 */
	function getClientID() {
		return $this->_ClientID;
	}
	
	/**
	 * Set $_ClientID to $inClientID
	 *
	 * @param integer $inClientID
	 * @return mofilmClientSourceSet
	 */
	function setClientID($inClientID) {
		if ( $inClientID !== $this->_ClientID ) {
			$this->_ClientID = $inClientID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the object by ID, returns null if not found
	 *
	 * @param integer $inObjectID
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
	 * Returns an array of just event IDs from the loaded sources
	 * 
	 * @return array
	 */
	function getEventIDs() {
		$tmp = array();
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( !in_array($oObject->getEventID(), $tmp) ) {
					$tmp[] = $oObject->getEventID();
				}
			}
		}
		return $tmp;
	}
	
	/**
	 * Returns the source with name $inDescription, or null if not found
	 *
	 * @param string $inDescription
	 * @return mofilmSource
	 */
	function getObjectByDescription($inDescription) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new mofilmSource();
			foreach ( $this as $oObject ) {
				if ( $oObject->getName() == $inDescription ) {
					return $oObject;
				}
			}
		}
		return null;
	}

	/**
	 * Sets an array of objects to the set
	 *
	 * @param array $inArray Array of mofilmSource objects
	 * @return mofilmClientSourceSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the object to the set
	 *
	 * @param mofilmSource $inObject
	 * @return mofilmClientSourceSet
	 */
	function setObject(mofilmSource $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Removes the object from the set
	 *
	 * @param mofilmSource $inObject
	 * @return mofilmClientSourceSet
	 */
	function removeObjectByValue(mofilmSource $inObject) {
		return $this->_removeItemWithValue($inObject);
	}

	/**
	 * Removes the object by ID
	 *
	 * @param integer $inID
	 * @return mofilmClientSourceSet
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