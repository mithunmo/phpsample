<?php
/**
 * mofilmMovieBroadcastSet
 *
 * Stored in set.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @license mofilmMovieBroadcastSet
 * @version $Rev: 326 $
 */


/**
 * mofilmMovieBroadcastSet
 *
 * Manages license associations with a movie.
 *
 * @package mofilm
 * @subpackage movie
 * @license mofilmMovieBroadcastSet
 */
class mofilmMovieBroadcastSet extends baseSet implements systemDaoInterface {
    /**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;



	/**
	 * Returns new instance of mofilmMovieBroadcastSet
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieBroadcastSet
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
			$this->load();
		}
	}
        
        /**
	 * Attaches the mofilmMovieMusicLicenseSet to each movie in $inMovies. Expects $inMovies to be indexed by movie ID
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
                           SELECT movieBroadcast.*
                             FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieBroadcast
                            WHERE movieBroadcast.movieID IN ('.implode(',', array_keys($inMovies)).')';

                   $oStmt = dbManager::getInstance()->prepare($query);
                   if ( $oStmt->execute() ) {
                           foreach ( $oStmt as $row ) {
                                   $oObject = new mofilmMovieAsset();
                                   $oObject->loadFromArray($row);
                                   $properties[$row['movieID']][] = $oObject; 
                           }
                   }
                   $oStmt->closeCursor();

                   if ( false ) $oMovie = new mofilmMovie();
                   foreach ( $inMovies as $oMovie ) {
                           if ( $oMovie instanceof mofilmMovie ) {
                                   if ( array_key_exists($oMovie->getID(), $properties) ) {
                                           $oObject = new mofilmMovieBroadcastSet();
                                           $oObject->setMovieID($oMovie->getID());
                                           $oObject->setObjects($properties[$oMovie->getID()]);
                                           $oObject->setModified(false);

                                           $oMovie->setBroadcastSet($oObject);
                                           $return = true;
                                   }
                           }
                   }
            }
            return $return;
	}

    public function delete() {
        if ( $this->_MovieID && $this->getCount() > 0 ) {
                $return = true;
                foreach ( $this as $oObject ) {
                        $return = $oObject->delete() && $return;
                }
                return $return;
        }
        return false;
    }

    public function load() {
        if ( $this->_MovieID ) {
                $this->setObjects(mofilmMovieBroadcast::getBroadCastDetails($this->getMovieID()));
                $this->setModified(false);
                return true;
        }
        return false;
    }

    public function reset() {
        $this->_MovieID = 0;
        return $this->_resetSet();
    }

    public function save() {
        if ( $this->_MovieID ) {
            if ( $this->isModified() ) {
                    if ( $this->getCount() > 0 ) {
                            if ( false ) $oObject = new mofilmMovieBroadcast();
                            foreach ( $this as $oObject ) {
                                    $oObject->setMovieID($this->getMovieID());
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

    public function toArray() {
        return get_object_vars($this);
    }

    function getMovieID() {
		return $this->_MovieID;
	}
	 
	/**
	 * Sets $_MovieID to $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieBroadcastSet
	 */
    function setMovieID($inMovieID) {
            if ( $this->_MovieID !== $inMovieID ) {
                    $this->_MovieID = $inMovieID;
                    $this->_Modified = true;
            }
            return $this;
    }
    
    /**
	 * Sets an array of tag objects to the set
	 * 
	 * @param array $inArray Array of mofilmMovieBroadcast objects
	 * @return mofilmMovieBroadcastSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}
	
	/**
	 * Add the tag to the object
	 *
	 * @param mofilmMovieBroadcast $inObject
	 * @return mofilmMovieBroadcastSet
	 */
	function setObject(mofilmMovieBroadcast $inObject) {
		return $this->_setValue($inObject);
	}
	
	/**
	 * Removes the object by value
	 * 
	 * @param mofilmMovieBroadcast $inObject
	 * @return mofilmMovieBroadcastSet
	 */
	function removeObjectByValue(mofilmMovieBroadcast $inObject) {
		return $this->_removeItemWithValue($inObject);
	}

	/**
	 * Removes the object by ID
	 *
	 * @param integer $inID
	 * @return mofilmMovieBroadcastSet
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