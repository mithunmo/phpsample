<?php
/**
 * mofilmUserEventFilter
 *
 * Stored in filter.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage user
 * @category mofilmUserEventFilter
 * @version $Rev: 10 $
 */


/**
 * mofilmUserEventFilter
 *
 * Contains a list of event ids to be filtered from result sets.
 *
 * @package mofilm
 * @subpackage user
 * @category mofilmUserEventFilter
 */
class mofilmUserEventFilter extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;



	/**
	 * Returns new instance of mofilmUserEventFilter
	 *
	 * @param integer $inUserID
	 * @return mofilmUserEventFilter
	 */
	function __construct($inUserID = null) {
		$this->reset();
		if ( $inUserID !== null ) {
			$this->setUserID($inUserID);
			$this->load();
		}
	}



	/**
	 * Deletes the properties
	 *
	 * @return boolean
	 * @see systemDaoInterface::delete()
	 */
	function delete() {
		if ( $this->_UserID ) {
			$query = '
				DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userEventFilter
				 WHERE userID = :UserID';

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->_UserID);
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
		if ( $this->_UserID ) {
			$query = '
				SELECT userID, eventID FROM '.system::getConfig()->getDatabase('mofilm_content').'.userEventFilter
				 WHERE userID = :UserID';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->_UserID);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$this->setObject($row['eventID']);
				}
			}
			$oStmt->closeCursor();
			$this->setModified(false);
			return true;
		}
		return false;
	}

	/**
	 * Saves object
	 *
	 * @return boolean
	 * @see systemDaoInterface::save()
	 */
	function save() {
		if ( $this->_UserID ) {
			if ( $this->isModified() ) {
				$this->delete();
				
				$query = '
					INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userEventFilter
						(userID, eventID)
					VALUES ';
				
				$tmp = array();
				foreach ( $this as $eventID ) {
					$tmp[] = '('.$this->getUserID().', '.$eventID.')';
				}
				
				if ( count($tmp) > 0 ) {
					$query .= implode(', ', $tmp);
					
					$oStmt = dbManager::getInstance()->prepare($query);
					$oStmt->execute();
					$oStmt->closeCursor();
				}
				
				$this->setModified(false);
				return true;
			}
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
		$this->_UserID = 0;
		return $this->_resetSet();
	}
	
	
	
	/**
	 * Returns the array of event ids
	 *
	 * @return array
	 * @see systemDaoInterface::toArray()
	 */
	function toArray() {
		return $this->_getItem();
	}

	/**
	 * Returns $_UserID
	 *
	 * @return integer
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Sets $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserEventFilter
	 */
	function setUserID($inUserID) {
		if ( $this->_UserID !== $inUserID ) {
			$this->_UserID = $inUserID;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the object by ID, returns null if not found
	 *
	 * @param integer $inObjectID
	 * @return mofilmUserTerms
	 */
	function hasEvent($inObjectID) {
		return $this->_itemValueInSet($inObjectID);
	}
	
	/**
	 * Sets an array of objects to the set
	 *
	 * @param array $inArray Array of mofilmUserTerms objects
	 * @return mofilmUserEventFilter
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the object to the set
	 *
	 * @param integer $inObject
	 * @return mofilmUserEventFilter
	 */
	function setObject($inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Removes an object by ID
	 *
	 * @param integer $inTermsID
	 * @return mofilmUserEventFilter
	 */
	function removeObjectByID($inObjectID) {
		return $this->_removeItemWithValue($inObjectID);
	}
}