<?php
/**
 * mofilmMovieRating
 * 
 * Stored in mofilmMovieRating.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMovieRating
 * @category mofilmMovieRating
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieRating Class
 * 
 * Provides access to records in mofilm_content.movieRatings
 * 
 * Creating a new record:
 * <code>
 * $oMofilmMovieRating = new mofilmMovieRating();
 * $oMofilmMovieRating->setMovieID($inMovieID);
 * $oMofilmMovieRating->setUserID($inUserID);
 * $oMofilmMovieRating->setRating($inRating);
 * $oMofilmMovieRating->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMovieRating = new mofilmMovieRating($inMovieID, $inUserID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmMovieRating = new mofilmMovieRating();
 * $oMofilmMovieRating->setMovieID($inMovieID);
 * $oMofilmMovieRating->setUserID($inUserID);
 * $oMofilmMovieRating->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmMovieRating = mofilmMovieRating::getInstance($inMovieID, $inUserID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmMovieRating
 * @category mofilmMovieRating
 */
class mofilmMovieRating implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmMovieRating
	 * 
	 * @var array
	 * @access protected
	 * @static 
	 */
	protected static $_Instances = array();
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
		
	/**
	 * Stores $_MovieID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;
			
	/**
	 * Stores $_UserID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;
			
	/**
	 * Stores $_Rating
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_Rating;
	
	/**
	 * Stores the user object
	 * 
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_User;
			
	
	
	/**
	 * Returns a new instance of mofilmMovieRating
	 * 
	 * @param integer $inMovieID
	 * @param integer $inUserID
	 * @return mofilmMovieRating
	 */
	function __construct($inMovieID = null, $inUserID = null) {
		$this->reset();
		if ( $inMovieID !== null && $inUserID !== null ) {
			$this->setMovieID($inMovieID);
			$this->setUserID($inUserID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new mofilmMovieRating containing non-unique properties
	 * 
	 * @param integer $inRating
	 * @return mofilmMovieRating
	 * @static 
	 */
	public static function factory($inRating = null) {
		$oObject = new mofilmMovieRating;
		if ( $inRating !== null ) {
			$oObject->setRating($inRating);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmMovieRating by primary key
	 * 
	 * @param integer $inMovieID
	 * @param integer $inUserID
	 * @return mofilmMovieRating
	 * @static 
	 */
	public static function getInstance($inMovieID, $inUserID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMovieID.'.'.$inUserID]) ) {
			return self::$_Instances[$inMovieID.'.'.$inUserID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmMovieRating();
		$oObject->setMovieID($inMovieID);
		$oObject->setUserID($inUserID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMovieID.'.'.$inUserID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmMovieRating
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inMovieID = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieRatings';
		if ( $inMovieID !== null ) {
			$query .= ' WHERE movieID = '.dbManager::getInstance()->quote($inMovieID).' AND userID > 0';
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMovieRating();
					$oObject->loadFromArray($row);
					$list[] = $oObject;
				}
			}
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $list;
	}
	
	/**
	 * Updates the average movie rating and counts
	 * 
	 * @param integer $inMovieID
	 * @return boolean
	 * @static
	 */
	public static function updateMovieRating($inMovieID) {
		$res = false;
		$query = '
			INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movies
				(ID, avgRating, ratingCount)
			(
				SELECT movieID AS ID, AVG(rating) AS avgRating, COUNT(rating) AS ratingCount
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieRatings
				 WHERE movieID = :MovieID AND rating > 0
				 GROUP BY movieID
			)
			ON DUPLICATE KEY UPDATE
				avgRating = VALUES(avgRating),
				ratingCount = VALUES(ratingCount)';
		
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $inMovieID, PDO::PARAM_INT);
			$res = $oStmt->execute();
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $res;
	}
	
	
	
	/**
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieRatings';
		
		$where = array();
		if ( $this->_MovieID !== 0 ) {
			$where[] = ' movieID = :MovieID ';
		}
		if ( $this->_UserID !== 0 ) {
			$where[] = ' userID = :UserID ';
		}
						
		if ( count($where) == 0 ) {
			return false;
		}
		
		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_MovieID !== 0 ) {
				$oStmt->bindValue(':MovieID', $this->_MovieID);
			}
		if ( $this->_UserID !== 0 ) {
				$oStmt->bindValue(':UserID', $this->_UserID);
			}
							
			$this->reset();
			if ( $oStmt->execute() ) {
				$row = $oStmt->fetch();
				if ( $row !== false && is_array($row) ) {
					$this->loadFromArray($row);
					$oStmt->closeCursor();
					$return = true;
				}
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $return;
	}
	
	/**
	 * Loads a record by array
	 * 
	 * @param array $inArray
	 */
	function loadFromArray($inArray) {
		$this->setMovieID((int)$inArray['movieID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setRating((int)$inArray['rating']);
		$this->setModified(false);
	}
	
	/**
	 * Saves object to the table
	 * 
	 * @return boolean
	 */
	function save() {
		$return = false;
		if ( $this->isModified() ) {
			$message = '';
			if ( !$this->isValid($message) ) {
				throw new mofilmException($message);
			}
						
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieRatings
					( movieID, userID, rating)
				VALUES 
					(:MovieID, :UserID, :Rating)
				ON DUPLICATE KEY UPDATE
					rating=VALUES(rating)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MovieID', $this->_MovieID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':Rating', $this->_Rating);
								
					if ( $oStmt->execute() ) {

						$this->setModified(false);
						$return = true;
					}
				} catch ( Exception $e ) {
					systemLog::error($e->getMessage());
					throw $e;
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
		$query = '
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieRatings
		WHERE
			movieID = :MovieID AND 
			userID = :UserID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $this->_MovieID);
			$oStmt->bindValue(':UserID', $this->_UserID);
				
			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				$this->reset();
				return true;
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return false;
	}
	
	/**
	 * Resets object properties to defaults
	 * 
	 * @return mofilmMovieRating
	 */
	function reset() {
		$this->_MovieID = 0;
		$this->_UserID = 0;
		$this->_Rating = 0;
		$this->_User = null;
		$this->setModified(false);
		return $this;
	}
	
	/**
	 * Returns object as a string with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toString($newLine = "\n") {
		$string  = '';
		$string .= " MovieID[$this->_MovieID] $newLine";
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " Rating[$this->_Rating] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmMovieRating';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"MovieID\" value=\"$this->_MovieID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Rating\" value=\"$this->_Rating\" type=\"integer\" /> $newLine";
		$xml .= "</$className>$newLine";
		return $xml;
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
	 * Returns true if object is valid
	 * 
	 * @return boolean
	 */
	function isValid(&$message = '') {
		$valid = true;
		if ( $valid ) {
			$valid = $this->checkMovieID($message);
		}
		if ( $valid ) {
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkRating($message);
		}
		return $valid;
	}
		
	/**
	 * Checks that $_MovieID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkMovieID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MovieID) && $this->_MovieID !== 0 ) {
			$inMessage .= "{$this->_MovieID} is not a valid value for MovieID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_UserID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkUserID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_UserID) && $this->_UserID !== 0 ) {
			$inMessage .= "{$this->_UserID} is not a valid value for UserID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Rating has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkRating(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Rating) && $this->_Rating !== 0 ) {
			$inMessage .= "{$this->_Rating} is not a valid value for Rating";
			$isValid = false;
		}
		return $isValid;
	}
		
	
	
	/**
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return mofilmMovieRating
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns the primaryKey index
	 * 
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->_MovieID.'.'.$this->_UserID;
	}
		
	/**
	 * Return value of $_MovieID
	 * 
	 * @return integer
	 * @access public
	 */
	function getMovieID() {
		return $this->_MovieID;
	}
	
	/**
	 * Set $_MovieID to MovieID
	 * 
	 * @param integer $inMovieID
	 * @return mofilmMovieRating
	 * @access public
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
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
	 * Returns the user object, loading it if not set
	 * 
	 * @return mofilmUser
	 */
	function getUser() {
		if ( !$this->_User instanceof mofilmUser ) {
			if ( $this->getUserID() > 0 ) {
				$this->_User = mofilmUserManager::getInstance()
					->setLoadOnlyActive(false)
					->getUserByID($this->getUserID());
			} else {
				$this->_User = new mofilmUser();
			}
		}
		return $this->_User;
	}
	
	/**
	 * Set $_UserID to UserID
	 * 
	 * @param integer $inUserID
	 * @return mofilmMovieRating
	 * @access public
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->_User = null;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Rating
	 * 
	 * @return integer
	 * @access public
	 */
	function getRating() {
		return $this->_Rating;
	}
	
	/**
	 * Set $_Rating to Rating
	 * 
	 * @param integer $inRating
	 * @return mofilmMovieRating
	 * @access public
	 */
	function setRating($inRating) {
		if ( $inRating !== $this->_Rating ) {
			$this->_Rating = $inRating;
			$this->setModified();
		}
		return $this;
	}
}