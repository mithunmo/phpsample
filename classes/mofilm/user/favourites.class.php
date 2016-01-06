<?php
/**
 * mofilmUserFavourites
 * 
 * Stored in mofilmUserFavourites.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserFavourites
 * @category mofilmUserFavourites
 * @version $Rev: 27 $
 */


/**
 * mofilmUserFavourites Class
 * 
 * Handles the movies flagged as favourites by the current user. This class allows
 * the favourites set to be manipulated, updated and removed.
 * 
 * @package mofilm
 * @subpackage mofilmUserFavourites
 * @category mofilmUserFavourites
 */
class mofilmUserFavourites extends baseSet implements systemDaoInterface {
		
	/**
	 * Stores $_UserID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;
	
	
	
	/**
	 * Returns a new instance of mofilmUserFavourites
	 * 
	 * @param integer $inUserID
	 * @return mofilmUserFavourites
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
			$query = "SELECT movieID FROM ".system::getConfig()->getDatabase('mofilm_content').'.userFavourites WHERE userID = :UserID ORDER BY sequence ASC';
			$tmp = array();

			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->getUserID());
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$tmp[] = $row['movieID'];
				}
				$return = true;
				
				if ( count($tmp) > 0 ) {
					$this->_setItem(mofilmMovieManager::loadInstancesByArray($tmp));
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
						INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userFavourites
							(userID, movieID, sequence)
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
			$query = 'DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userFavourites WHERE userID = :UserID';
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
	 * @return mofilmUserFavourites
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
	 * @return mofilmUserFavourites
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
	 * @param mixed $inMovie Either movieID or mofilmMovie object
	 * @return mofilmUserFavourites
	 */
	function addObject($inMovie) {
		if ( !$inMovie instanceof mofilmMovie ) {
			$inMovie = mofilmMovieManager::getInstanceByID($inMovie);
		}
		return $this->_setValue($inMovie);
	}

	/**
	 * Removes the object from the set
	 *
	 * @param mofilmMovie $inMovie
	 * @return mofilmUserFavourites
	 */
	function removeObject(mofilmMovie $inMovie) {
		return $this->_removeItemWithValue($inMovie);
	}
	
	/**
	 * Returns the object with $inMovie ID from the set, or false if not found
	 * 
	 * @param mixed $inMovie MovieID or movie object
	 * @return mofilmMovie
	 */
	function getObject($inMovie) {
		if ( $inMovie instanceof mofilmMovie ) {
			$inMovie = $inMovie->getID();
		}
		
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getID() == $inMovie ) {
					return $oObject;
				}
			}
		}
		return false;
	}
	
	/**
	 * Returns true if movie is a favourite
	 *
	 * @param mixed $inMovie Either movieID or mofilmMovie object
	 * @return boolean
	 */
	function isFavourite($inMovie) {
		if ( $inMovie instanceof mofilmMovie ) {
			$inMovie = $inMovie->getID();
		}
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getID() == $inMovie ) {
					return true;
				}
			}
		}
		return false;
	}
}