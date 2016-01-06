<?php
/**
 * mofilmMovieAwardSet
 * 
 * Stored in set.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieAwardSet
 * @version $Rev: 241 $
 */


/**
 * mofilmMovieAwardSet
 * 
 * Manages movie award, optionally only loading awards for the specified event.
 * 
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieAwardSet
 */
class mofilmMovieAwardSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	/**
	 * Stores $_EventID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_EventID;

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;

	
	
	/**
	 * Returns new instance of mofilmMovieAwardSet
	 * 
	 * @param integer $inMovieID
	 * @param integer $inEventID (optional)
	 * @return mofilmMovieAwardSet
	 */
	function __construct($inMovieID = null, $inEventID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
			$this->setEventID($inEventID);
			$this->load();
		}
	}
	
	

	/**
	 * Attaches the mofilmMovieAwardSet to each movie in $inMovies. Expects $inMovies to be indexed by movie ID
	 *
	 * @param array $inMovies
	 * @return boolean
	 * @static
	 */
	static function loadArrayOfMoviesWithProperties(array $inMovies) {
		$return = false;
		$properties = array();
		if ( count($inMovies) > 0 ) {
			$query = '
				SELECT movieAwards.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
				 WHERE movieAwards.movieID IN ('.implode(',', array_keys($inMovies)).')
				 ORDER BY movieAwards.movieID ASC, movieAwards.year DESC';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMovieAward();
					$oObject->loadFromArray($row);
					$properties[$row['movieID']][] = $oObject; 
				}
			}
			$oStmt->closeCursor();

			foreach ( $inMovies as $oMovie ) {
				if ( $oMovie instanceof mofilmMovie ) {
					if ( array_key_exists($oMovie->getID(), $properties) ) {
						$oObject = new mofilmMovieAwardSet();
						$oObject->setMovieID($oMovie->getID());
						$oObject->setObjects($properties[$oMovie->getID()]);
						$oObject->setModified(false);
						
						$oMovie->setAwardSet($oObject);
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
		if ( $this->_MovieID && $this->getCount() > 0 ) {
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
		if ( $this->_MovieID ) {
			$this->setObjects(mofilmMovieAward::listOfObjects(null, null, $this->getMovieID(), $this->getEventID()));
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
		$this->_MovieID = 0;
		$this->_EventID = null;
		$this->_UserID = null;
		return $this->_resetSet();
	}

	/**
	 * Saves object
	 * 
	 * @return boolean 
	 * @see systemDaoInterface::save()
	 */
	function save() {
		if ( $this->_MovieID ) {
			if ( $this->isModified() ) {
				if ( $this->getCount() > 0 ) {
					if ( false ) $oObject = new mofilmMovieAward();
	 				foreach ( $this as $oObject ) {
	 					$oObject->setMovieID($this->getMovieID());
						$oObject->setUserID($this->getUserID());

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
	 * Returns $_MovieID
	 *
	 * @return integer
	 */
	function getMovieID() {
		return $this->_MovieID;
	}
	 
	/**
	 * Sets $_MovieID to $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieAwardSet
	 */
	function setMovieID($inMovieID) {
		if ( $this->_MovieID !== $inMovieID ) {
			$this->_MovieID = $inMovieID;
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
	 * @return mofilmMovieAwardSet
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_UserID
	 *
	 * @return integer
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmMovieAwardSet
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
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
	 * @return mofilmMovieAwardSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}
	
	/**
	 * Add the award to the object
	 *
	 * @param mofilmMovieAward $inObject
	 * @return mofilmMovieAwardSet
	 */
	function setObject(mofilmMovieAward $inObject, $ignoreRules = false) {
		if ( $ignoreRules !== true ) {
			if ( $inObject->getType() == mofilmMovieAward::TYPE_WINNER ) {
				$oAward = mofilmMovieAward::getInstanceByEventAndType($inObject->getEventID(), mofilmMovieAward::TYPE_WINNER);
				if ( $oAward instanceof mofilmMovieAward ) {
					throw new mofilmException('Event '.$inObject->getEventID().' already has an overall winner');
				}
			}
			
			if ( $this->getMovieID() ) {
				switch ( $inObject->getType() ) {
					case mofilmMovieAward::TYPE_FINALIST:
						if ( $this->isFinalist($inObject->getEventID()) ) {
							throw new mofilmException('Movie '.$this->getMovieID().' is already a listed finalist for event '.$inObject->getEventID());
						}
						if ( $this->isRunnerUp($inObject->getEventID()) ) {
							throw new mofilmException('Movie '.$this->getMovieID().' is already a runner-up for event '.$inObject->getEventID());
						}
					break;
					
					case mofilmMovieAward::TYPE_RUNNER_UP:
						if ( $this->isFinalist($inObject->getEventID()) ) {
							throw new mofilmException('Movie '.$this->getMovieID().' is already a finalist for event '.$inObject->getEventID());
						}
						if ( $this->isRunnerUp($inObject->getEventID()) ) {
							throw new mofilmException('Movie '.$this->getMovieID().' is already a runner-up for event '.$inObject->getEventID());
						}
					break;
					
					case mofilmMovieAward::TYPE_SHORTLISTED:
						if ( $this->isShortlisted($inObject->getEventID()) ) {
							throw new mofilmException('Movie '.$this->getMovieID().' is already shortlisted for event '.$inObject->getEventID());
						}
                                        break;
                                                
                                        case mofilmMovieAward::TYPE_PRO_FINAL:
						if ( $this->isProFinal($inObject->getEventID()) ) {
							throw new mofilmException('Movie '.$this->getMovieID().' is already Pro Final for event '.$inObject->getEventID());
						}
                                        break;
                                                
                                        case mofilmMovieAward::TYPE_PRO_SHOWCASE:
						if ( $this->isProShowcase($inObject->getEventID()) ) {
							throw new mofilmException('Movie '.$this->getMovieID().' is already Pro Showcase for event '.$inObject->getEventID());
						}        
					break;
				}
			}
		}
		return $this->_setValue($inObject);
	}

	/**
	 * Marks the award by $inID for deletion
	 *
	 * @param integer $inID
	 * @return mofilmMovieAwardSet
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
	 * Returns a new mofilmMovieAwardSet of objects matching $inEventID, if not specified the internal eventID will be used
	 * 
	 * @param integer $inEventID
	 * @return mofilmMovieAwardSet
	 */
	function getObjectsByEventID($inEventID = null) {
		if ( $inEventID === null && $this->getEventID() ) {
			$inEventID = $this->getEventID();
		}
		
		$oSet = new mofilmMovieAwardSet();
		$oSet->setMovieID($this->getMovieID());
		$oSet->setEventID($inEventID);
		
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getEventID() == $inEventID ) {
					$oSet->setObject($oObject, true);
				}
			}
		}
		return $oSet;
	}
	
	/**
	 * Returns a new mofilmMovieAwardSet of objects matching $inType
	 * 
	 * @param string $inType Constant from mofilmMovieAward object
	 * @return mofilmMovieAwardSet
	 */
	function getObjectsByType($inType) {
		$oSet = new mofilmMovieAwardSet();
		$oSet->setMovieID($this->getMovieID());
		$oSet->setEventID($this->getEventID());
		
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oObject ) {
				if ( $oObject->getType() == $inType ) {
					$oSet->setObject($oObject, true);
				}
			}
		}
		return $oSet;
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
			$i = 0;
			foreach ( $this as $oObject ) {
				if ( $oObject->getType() == $inType ) {
					if ( $i == 0 ) {
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
	 * Returns true if the movie was pro final in the event / any event
	 * 
	 * @param integer $inEventID (optional)
	 * @return boolean
	 */
	function isProFinal($inEventID = null) {
		if ( $inEventID ) {
			return $this->getObjectsByEventID($inEventID)->getObjectsByType(mofilmMovieAward::TYPE_PRO_FINAL)->getCount() > 0;
		} else {
			return $this->getObjectsByType(mofilmMovieAward::TYPE_PRO_FINAL)->getCount() > 0;
		}
	}
        
         /**
	 * Returns true if the movie was pro final in the event / any event
	 * 
	 * @param integer $inEventID (optional)
	 * @return boolean
	 */
	function isProShowcase($inEventID = null) {
		if ( $inEventID ) {
			return $this->getObjectsByEventID($inEventID)->getObjectsByType(mofilmMovieAward::TYPE_PRO_SHOWCASE)->getCount() > 0;
		} else {
			return $this->getObjectsByType(mofilmMovieAward::TYPE_PRO_SHOWCASE)->getCount() > 0;
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