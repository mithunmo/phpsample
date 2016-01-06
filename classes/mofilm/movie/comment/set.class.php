<?php
/**
 * mofilmMovieCommentSet
 * 
 * Stored in assetSet.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieCommentSet
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieCommentSet
 * 
 * Manages movie assets.
 * 
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieCommentSet
 */
class mofilmMovieCommentSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	
	
	/**
	 * Returns new instance of mofilmMovieCommentSet
	 * 
	 * @param integer $inMovieID
	 * @return mofilmMovieCommentSet
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
			$this->load();
		}
	}
	
	

	/**
	 * Attaches the mofilmMovieCommentSet to each movie in $inMovies. Expects $inMovies to be indexed by movie ID
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
		 		SELECT movieComments.*
		 		  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieComments
		 		 WHERE movieComments.movieID IN ('.implode(',', array_keys($inMovies)).')
		 		 ORDER BY movieComments.movieID ASC, movieComments.timestamp ASC';
		 	
		 	$oStmt = dbManager::getInstance()->prepare($query);
		 	if ( $oStmt->execute() ) {
		 		foreach ( $oStmt as $row ) {
		 			$oObject = new mofilmMovieComment();
		 			$oObject->loadFromArray($row);
		 			$properties[$row['movieID']][] = $oObject; 
		 		}
		 	}
		 	$oStmt->closeCursor();
		 	
		 	if ( false ) $oMovie = new mofilmMovie();
		 	foreach ( $inMovies as $oMovie ) {
		 		if ( $oMovie instanceof mofilmMovie ) {
		 			if ( array_key_exists($oMovie->getID(), $properties) ) {
		 				$oObject = new mofilmMovieCommentSet();
		 				$oObject->setMovieID($oMovie->getID());
		 				$oObject->setObjects($properties[$oMovie->getID()]);
		 				$oObject->setModified(false);
		 				
		 				$oMovie->setCommentSet($oObject);
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
			$this->setObjects(mofilmMovieComment::listOfObjects(null, null, $this->getMovieID(), mofilmMovieComment::ORDER_ASC));
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
					if ( false ) $oObject = new mofilmMovieComment();
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
	 * @return mofilmMovieCommentSet
	 */
	function setMovieID($inMovieID) {
		if ( $this->_MovieID !== $inMovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns the comment at $inIndex, the array offset position
	 * 
	 * @param integer $inIndex
	 * @return mofilmMovieComment
	 */
	function getObjectByIndex($inIndex) {
		return $this->_getItem($inIndex);
	}
	
	/**
	 * Sets an array of comment objects to the set
	 * 
	 * @param array $inArray Array of objects
	 * @return mofilmMovieCommentSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}
	
	/**
	 * Add the comment to the object
	 *
	 * @param mofilmMovieComment $inObject
	 * @return mofilmMovieCommentSet
	 */
	function setObject(mofilmMovieComment $inObject) {
		return $this->_setValue($inObject);
	}
	
	/**
	 * Marks the comment by $inUserID at $inTimestamp for deletion
	 * 
	 * @param integer $inUserID
	 * @param datetime $inTimestamp
	 * @return mofilmMovieCommentSet
	 */
	function removeObjectByUserTimestamp($inUserID, $inTimestamp) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oComment = new mofilmMovieComment();
			foreach ( $this as $oComment ) {
				if (
					$oComment->getMovieID() == $this->getMovieID() &&
					$oComment->getUserID() == $inUserID &&
					$oComment->getTimestamp() == $inTimestamp
				) {
					$oComment->setMarkForDeletion(true);
				}
			}
		}
		return $this;
	}

	/**
	 * Marks the comment by $inID for deletion
	 *
	 * @param integer $inID
	 * @return mofilmMovieCommentSet
	 */
	function removeObjectByID($inID) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oComment = new mofilmMovieComment();
			foreach ( $this as $oComment ) {
				if ( $oComment->getID() == $inID ) {
					$oComment->setMarkForDeletion(true);
				}
			}
		}
		return $this;
	}
}