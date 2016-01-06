<?php
/**
 * mofilmUserEventFavourites
 * 
 * Stored in mofilmUserEventFavourites.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserEventFavourites
 * @category mofilmUserEventFavourites
 * @version $Rev: 20 $
 */


/**
 * mofilmUserEventFavourites Class
 * 
 * Provides an interface to allow managing events bookmarking and ordering
 * via a set object.
 * 
 * @package mofilm
 * @subpackage mofilmUserEventFavourites
 * @category mofilmUserEventFavourites
 */
class mofilmUserEventFavourites extends baseSet implements systemDaoInterface {
		
	/**
	 * Stores $_UserID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;
	
	
	
	/**
	 * Returns a new instance of mofilmUserEventFavourites
	 * 
	 * @param integer $inUserID
	 * @return mofilmUserEventFavourites
	 */
	function __construct($inUserID = null) {
		$this->reset();
		if ( $inUserID !== null ) {
			$this->setUserID($inUserID);
		}
		$this->load();
	}
	
	
	
	/**
	 * Loads the set with data
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		if ( $this->getUserID() ) {
			$query = "SELECT eventID FROM ".system::getConfig()->getDatabase('mofilm_content').'.userEventFavourites WHERE userID = :UserID ORDER BY sequence ASC';
			$tmp = array();

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->getUserID());
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$tmp[] = $row['eventID'];
				}
				$return = true;
				
				if ( count($tmp) > 0 ) {
					$this->_setItem(mofilmEvent::listOfObjects(null, null, false, mofilmEvent::ORDERBY_SET_ID, $tmp));
				}
			}
			$this->setModified(false);
		}
		return $return;
	}
	
	/**
	 * Saves object to the table
	 * 
	 * @return boolean
	 */
	function save() {
		$return = false;
		if ( $this->getUserID() ) {
			if ( $this->isModified() ) {
				/*
				 * Clear all existing objects
				 */
				$this->delete();
				
				if ( $this->getCount() > 0 ) {
					$query = '
						INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userEventFavourites
							(userID, eventID, sequence)
						VALUES';
					
					$values = array();
					foreach ( $this as $index => $oObject ) {
						$values[] = "({$this->getUserID()}, {$oObject->getID()}, $index)";
					}
					
					if ( count($values) > 0 ) {
						$query .= implode(', ', $values);
						
						$oStmt = dbManager::getInstance()->prepare($query);
						if ( $oStmt->execute() ) {
							$return = true;
							$this->setModified(false);
						}
						$oStmt->closeCursor();
					}
				}
			}
		}
		return $return;
	}
	
	/**
	 * Deletes the object from the table
	 * 
	 * @return boolean
	 */
	function delete() {
		if ( $this->getUserID() ) {
			$query = 'DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userEventFavourites WHERE userID = :UserID';
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->getUserID());
			$oStmt->execute();
			$oStmt->closeCursor();
			return true;
		}
		return false;
	}
	
	/**
	 * Resets object properties to defaults
	 * 
	 * @return mofilmUserEventFavourites
	 */
	function reset() {
		$this->_UserID = 0;
		return parent::_resetSet();
	}
	
	/**
	 * Returns properties of object as an array
	 *
	 * @return array
	 */
	function toArray() {
		return get_object_vars($this);
	}



	/**
	 * Return value of $_UserID
	 * 
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}
	
	/**
	 * Set $_UserID to $inUserID
	 * 
	 * @param integer $inUserID
	 * @return mofilmUserEventFavourites
	 * @access public
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Adds an object to the set
	 *
	 * @param mixed $inObject Either eventId or mofilmEvent object
	 * @return mofilmUserEventFavourites
	 */
	function addObject($inObject) {
		if ( !$inObject instanceof mofilmEvent ) {
			$inObject = mofilmEvent::getInstance($inObject);
		}
		return $this->_setValue($inObject);
	}

	/**
	 * Removes the object from the set
	 *
	 * @param mofilmEvent $inObject
	 * @return mofilmUserEventFavourites
	 */
	function removeObject(mofilmEvent $inObject) {
		return $this->_removeItemWithValue($inObject);
	}
	
	/**
	 * Returns the object with $inObject ID from the set, or false if not found
	 * 
	 * @param mixed $inObject EventId or event object
	 * @return mofilmEvent
	 */
	function getObject($inObject) {
		if ( $inObject instanceof mofilmEvent ) {
			$inObject = $inObject->getID();
		}
		
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getID() == $inObject ) {
					return $oObject;
				}
			}
		}
		return false;
	}

	/**
	 * Returns an array of event IDs
	 *
	 * @return array
	 */
	function getEventIDs() {
		$return = array();

		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				$return[] = $oObject->getID();
			}
		}

		return $return;
	}
	
	/**
	 * Returns true if event is a favourite
	 *
	 * @param mixed $inObject Either eventId or mofilmEvent object
	 * @return boolean
	 */
	function isFavourite($inObject) {
		if ( $inObject instanceof mofilmEvent ) {
			$inObject = $inObject->getID();
		}
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getID() == $inObject ) {
					return true;
				}
			}
		}
		return false;
	}
}