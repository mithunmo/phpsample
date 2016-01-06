<?php
/**
 * mofilmUserAwardSet
 * 
 * Stored in set.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmUserAwardSet
 * @version $Rev: 10 $
 */


/**
 * mofilmUserAwardSet
 * 
 * Manages user awards, optionally only loading awards for the specified event.
 * 
 * @package mofilm
 * @subpackage movie
 * @category mofilmUserAwardSet
 */
class mofilmUserAwardSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;
	
	/**
	 * Stores $_EventID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_EventID;
	
	
	
	/**
	 * Returns new instance of mofilmUserAwardSet
	 * 
	 * @param integer $inUserID
	 * @param integer $inEventID (optional)
	 * @return mofilmUserAwardSet
	 */
	function __construct($inUserID = null, $inEventID = null) {
		$this->reset();
		if ( $inUserID !== null ) {
			$this->setUserID($inUserID);
			$this->setEventID($inEventID);
			$this->load();
		}
	}
	

	
	/**
	 * Deletes the objects
	 * 
	 * @return boolean 
	 * @see systemDaoInterface::delete()
	 */
	function delete() {
		if ( $this->_UserID && $this->getCount() > 0 ) {
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
		if ( $this->_UserID ) {
			$values = array(
				':UserID' => $this->getUserID(),
				':Active' => mofilmMovie::ACTIVE_Y,
				':Status' => mofilmMovie::STATUS_APPROVED,
				':Winner' => mofilmMovieAward::TYPE_WINNER,
				':Finalist' => mofilmMovieAward::TYPE_FINALIST,
			);
			$query = '
				SELECT movieAwards.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movies ON (movieAwards.movieID = movies.ID)
				 WHERE movies.userID = :UserID
				   AND movieAwards.type IN (:Winner, :Finalist)
				   AND movies.active = :Active
			       AND movies.status = :Status';

			if ( $this->getEventID() ) {
				$query .= ' AND movieAwards.eventID = :EventID';
				$values[':EventID'] = $this->getEventID();
			}

			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute($values) ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMovieAward();
					$oObject->loadFromArray($row);

					$this->setObject($oObject);
				}
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
		$this->_UserID = 0;
		$this->_EventID = null;
		return $this->_resetSet();
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
				if ( $this->getCount() > 0 ) {
					if ( false ) $oObject = new mofilmMovieAward();
	 				foreach ( $this as $oObject ) {
	 					if ( $oObject->getMarkForDeletion() ) {
	 						$oObject->delete();
	 					} else {
							$oObject->setUserID($this->getUserID());
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
	 * Returns $_UserID
	 *
	 * @return integer
	 */
	function getUserID() {
		return $this->_UserID;
	}
	 
	/**
	 * Sets $_MovieID to $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmUserAwardSet
	 */
	function setUserID($inMovieID) {
		if ( $this->_UserID !== $inMovieID ) {
			$this->_UserID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns $_EventID
	 *
	 * @return integer
	 */
	function getEventID() {
		return $this->_EventID;
	}
	
	/**
	 * Set $_EventID to $inEventID
	 *
	 * @param integer $inEventID
	 * @return mofilmUserAwardSet
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the object by ID, returns null if not found
	 * 
	 * @param integer $inObjectID
	 * @return mofilmMovieAward
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
	 * Returns the object at $inIndex, the array offset position
	 * 
	 * @param integer $inIndex
	 * @return mofilmMovieAward
	 */
	function getObjectByIndex($inIndex) {
		return $this->_getItem($inIndex);
	}
	
	/**
	 * Sets an array of award objects to the set
	 * 
	 * @param array $inArray Array of objects
	 * @return mofilmUserAwardSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}

	/**
	 * Add the award to the object
	 *
	 * @param mofilmMovieAward $inObject
	 * @return mofilmUserAwardSet
	 */
	function setObject(mofilmMovieAward $inObject) {
		return $this->_setValue($inObject);
	}

	/**
	 * Marks the award by $inID for deletion
	 *
	 * @param integer $inID
	 * @return mofilmUserAwardSet
	 */
	function removeObjectByID($inID) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oComment = new mofilmMovieAward();
			foreach ( $this as $oComment ) {
				if ( $oComment->getID() == $inID ) {
					$oComment->setMarkForDeletion(true);
				}
			}
		}
		return $this;
	}
	
	/**
	 * Returns a new mofilmUserAwardSet of objects matching $inEventID, if not specified the internal eventID will be used
	 * 
	 * @param integer $inEventID
	 * @return mofilmUserAwardSet
	 */
	function getObjectsByEventID($inEventID = null) {
		if ( $inEventID === null && $this->getEventID() ) {
			$inEventID = $this->getEventID();
		}
		
		$oSet = new mofilmUserAwardSet();
		$oSet->setUserID($this->getUserID());
		$oSet->setEventID($inEventID);
		
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getEventID() == $inEventID ) {
					$oSet->setObject($oObject);
				}
			}
		}
		return $oSet;
	}

	/**
	 * Returns a new mofilmUserAwardSet of objects matching $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmUserAwardSet
	 */
	function getObjectsByMovieID($inMovieID) {
		$oSet = new mofilmUserAwardSet();
		$oSet->setUserID($this->getUserID());
		$oSet->setEventID($inMovieID);

		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getMovieID() == $inMovieID ) {
					$oSet->setObject($oObject);
				}
			}
		}
		return $oSet;
	}

	/**
	 * Returns a single mofilmMovieAward matching $inMovieID, or false if not found
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieAward
	 */
	function getObjectByMovieID($inMovieID) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getMovieID() == $inMovieID ) {
					return $oObject;
				}
			}
		}

		return false;
	}
	
	/**
	 * Returns a new mofilmUserAwardSet of objects matching $inType
	 * 
	 * @param string $inType Constant from mofilmMovieAward object
	 * @return mofilmUserAwardSet
	 */
	function getObjectsByType($inType) {
		$oSet = new mofilmUserAwardSet();
		$oSet->setUserID($this->getUserID());
		$oSet->setEventID($this->getEventID());
		
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getType() == $inType ) {
					$oSet->setObject($oObject);
				}
			}
		}
		
		return $oSet;
	}

	/**
	 * Returns true if the set already contains $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return boolean
	 */
	function hasMovieInSet($inMovieID) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getMovieID() == $inMovieID ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns the best awards this user has received
	 *
	 * @return mofilmUserAwardSet
	 */
	function getBestAwards() {
		$oSet = new mofilmUserAwardSet();
		$oSet->setUserID($this->getUserID());
		$oSet->setEventID($this->getEventID());

		if ( $this->getCount() > 0 ) {
			/**
			 * @var mofilmMovieAward $oObject
			 */
			foreach ( $this as $oObject ) {
				$this->_assignUniqueMovies($oSet, $oObject);
			}

			$array = $oSet->_getItem();
			usort($array, array($this, '_sortNewestFirt'));
			$oSet->_setItem($array, null);
		}

		return $oSet;
	}

	/**
	 * Adds $inAward to the set, but only it if does not exist, and if it is better than an existing one
	 *
	 * @param mofilmUserAwardSet $inSet
	 * @param mofilmMovieAward $inAward
	 * @return void
	 */
	protected function _assignUniqueMovies(mofilmUserAwardSet $inSet, mofilmMovieAward $inAward) {
		if ( !$inSet->hasMovieInSet($inAward->getMovieID()) ) {
			$inSet->setObject($inAward);
		} else {
			$oExist = $inSet->getObjectByMovieID($inAward->getMovieID());
			if ( $this->_compareAwards($oExist, $inAward) ) {
				$inSet->_removeItemWithValue($oExist);
				$inSet->setObject($inAward);
			}
		}
	}

	/**
	 * Returns true if $inAward2 is "better" than $inAward1
	 *
	 * The logic applied here is as follows:
	 *
	 * * Compare Winner (overall event winner)
	 * * Compare if finalists
	 * * If finalist, compare position with 1>2>3>4>5 etc.
	 * * Compare Runner Up
	 * * Compare Shortlisted
	 *
	 * Both compares have to be done, i.e. is Award2 a winner vs Award1 not and
	 * is Award1 a winner vs Award2 not.
	 *
	 * @param mofilmMovieAward $inAward1
	 * @param mofilmMovieAward $inAward2
	 * @return boolean
	 */
	protected function _compareAwards(mofilmMovieAward $inAward1, mofilmMovieAward $inAward2) {
		if ( !$inAward1->isWinner() && $inAward2->isWinner() ) {
			return true;
		}
		if ( $inAward1->isWinner() && !$inAward2->isWinner() ) {
			return false;
		}
		if ( !$inAward1->isFinalist() && $inAward2->isFinalist() ) {
			return true;
		}
		if ( $inAward1->isFinalist() && !$inAward2->isFinalist() ) {
			return false;
		}

		if ( $inAward1->isFinalist() && $inAward2->isFinalist() ) {
			if ( $inAward2->getPosition() < $inAward1->getPosition() ) {
				return true;
			} else {
				return false;
			}
		}
		if ( !$inAward1->isRunnerUp() && $inAward2->isRunnerUp() ) {
			return true;
		}
		if ( $inAward1->isRunnerUp() && !$inAward2->isRunnerUp() ) {
			return false;
		}
		if ( !$inAward1->isShortlisted() && $inAward2->isShortlisted() ) {
			return true;
		}
		if ( $inAward1->isShortlisted() && !$inAward2->isShortlisted() ) {
			return false;
		}

		return false;
	}

	/**
	 * Returns +1 if $inAward2 is newer than $inAward1, 0 if the same, -1 if older
	 *
	 * @param mofilmMovieAward $inAward1
	 * @param mofilmMovieAward $inAward2
	 * @return integer
	 */
	protected function _sortNewestFirt(mofilmMovieAward $inAward1, mofilmMovieAward $inAward2) {
		if ( $inAward1->getSourceID() && $inAward2->getSourceID() ) {
			if ( $inAward2->getSourceID() > $inAward1->getSourceID() ) {
				return 1;
			} elseif ( $inAward2->getSourceID() == $inAward1->getSourceID() ) {
				return 0;
			} else {
				return -1;
			}
		} elseif ( $inAward1->getYear() && $inAward2->getYear() ) {
			if ( $inAward2->getYear() > $inAward1->getYear() ) {
				return 1;
			} elseif ( $inAward2->getYear() == $inAward1->getYear() ) {
				return 0;
			} else {
				return -1;
			}
		} else {
			return 0;
		}
	}

	/**
	 * Returns the total number of awards by type
	 *
	 * @param string $inType
	 * @return integer
	 */
	function getAwardCountByType($inType) {
		return $this->getObjectsByType($inType)->getCount();
	}
	
	/**
	 * Returns the highest finishing place by type for the current movie
	 * 
	 * @param string $inType Constant from mofilmMovieAward object
	 * @return mofilmMovieAward
	 */
	function getBestAwardResultByType($inType) {
		$oTmp = null;
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getType() == $inType ) {
					if ( $oTmp === null ) {
						$oTmp = $oObject;
					} elseif ( $oObject->getPosition() < $oTmp->getPosition() ) {
						$oTmp = $oObject;
					}
				}
			}
		}
		return $oTmp;
	}


	
	/**
	 * Returns true if the movie has won the event / any event
	 * 
	 * @param integer $inEventID (optional)
	 * @return boolean
	 */
	function isWinner($inEventID = null) {
		if ( $inEventID ) {
			return $this->getObjectsByEventID($inEventID)->getObjectsByType(mofilmMovieAward::TYPE_WINNER)->getCount() > 0;
		} else {
			return $this->getObjectsByType(mofilmMovieAward::TYPE_WINNER)->getCount() > 0;
		}
	}

	/**
	 * Returns true if the movie was a finalist in the event / any event
	 * 
	 * @param integer $inEventID (optional)
	 * @return boolean
	 */
	function isFinalist($inEventID = null) {
		if ( $inEventID ) {
			return $this->getObjectsByEventID($inEventID)->getObjectsByType(mofilmMovieAward::TYPE_FINALIST)->getCount() > 0;
		} else {
			return $this->getObjectsByType(mofilmMovieAward::TYPE_FINALIST)->getCount() > 0;
		}
	}

	/**
	 * Returns true if the movie was a runner up in the event / any event
	 * 
	 * @param integer $inEventID (optional)
	 * @return boolean
	 */
	function isRunnerUp($inEventID = null) {
		if ( $inEventID ) {
			return $this->getObjectsByEventID($inEventID)->getObjectsByType(mofilmMovieAward::TYPE_RUNNER_UP)->getCount() > 0;
		} else {
			return $this->getObjectsByType(mofilmMovieAward::TYPE_RUNNER_UP)->getCount() > 0;
		}
	}
	
	/**
	 * Returns true if the movie was shortlisted in the event / any event
	 * 
	 * @param integer $inEventID (optional)
	 * @return boolean
	 */
	function isShortlisted($inEventID = null) {
		if ( $inEventID ) {
			return $this->getObjectsByEventID($inEventID)->getObjectsByType(mofilmMovieAward::TYPE_SHORTLISTED)->getCount() > 0;
		} else {
			return $this->getObjectsByType(mofilmMovieAward::TYPE_SHORTLISTED)->getCount() > 0;
		}
	}
	
	/**
	 * Returns true if the movie was nominated in the event / any event
	 * 
	 * @param integer $inEventID (optional)
	 * @return boolean
	 */
	function isNominated($inEventID = null) {
		if ( $inEventID ) {
			return $this->getObjectsByEventID($inEventID)->getObjectsByType(mofilmMovieAward::TYPE_NOMINATED)->getCount() > 0;
		} else {
			return $this->getObjectsByType(mofilmMovieAward::TYPE_NOMINATED)->getCount() > 0;
		}
	}
	
	/**
	 * Returns true if the movie has one any other award
	 * 
	 * @param integer $inEventID (optional)
	 * @return boolean
	 */
	function hasOtherAward($inEventID = null) {
		if ( $inEventID ) {
			return $this->getObjectsByEventID($inEventID)->getObjectsByType(mofilmMovieAward::TYPE_OTHER)->getCount() > 0;
		} else {
			return $this->getObjectsByType(mofilmMovieAward::TYPE_OTHER)->getCount() > 0;
		}
	}
}