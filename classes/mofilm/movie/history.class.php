<?php
/**
 * mofilmMovieHistory
 * 
 * Stored in mofilmMovieHistory.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMovieHistory
 * @category mofilmMovieHistory
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieHistory Class
 * 
 * Provides access to records in mofilm_content.movieEditHistory
 * 
 * Creating a new record:
 * <code>
 * $oMofilmMovieHistory = new mofilmMovieHistory();
 * $oMofilmMovieHistory->setID($inID);
 * $oMofilmMovieHistory->setMovieID($inMovieID);
 * $oMofilmMovieHistory->setUserID($inUserID);
 * $oMofilmMovieHistory->setTimestamp($inTimestamp);
 * $oMofilmMovieHistory->setComment($inComment);
 * $oMofilmMovieHistory->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMovieHistory = new mofilmMovieHistory($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmMovieHistory = new mofilmMovieHistory();
 * $oMofilmMovieHistory->setID($inID);
 * $oMofilmMovieHistory->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmMovieHistory = mofilmMovieHistory::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmMovieHistory
 * @category mofilmMovieHistory
 */
class mofilmMovieHistory implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmMovieHistory
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
	 * Stores $_ID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_ID;
	
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
	 * Stores $_Timestamp
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_Timestamp;
	
	/**
	 * Stores $_Comment
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Comment;

	/**
	* Stores $_MarkForDeletion
	*
	* @var boolean
	* @access protected
	*/
	protected $_MarkForDeletion;

	
	
	/**
	 * Returns a new instance of mofilmMovieHistory
	 * 
	 * @param integer $inID
	 * @return mofilmMovieHistory
	 */
	function __construct($inID = null) {
		$this->reset();
		if ( $inID !== null ) {
			$this->setID($inID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new mofilmMovieHistory containing non-unique properties
	 * 
	 * @param integer $inMovieID
	 * @param integer $inUserID
	 * @param datetime $inTimestamp
	 * @param string $inComment
	 * @return mofilmMovieHistory
	 * @static 
	 */
	public static function factory($inMovieID = null, $inUserID = null, $inTimestamp = null, $inComment = null) {
		$oObject = new mofilmMovieHistory;
		if ( $inMovieID !== null ) {
			$oObject->setMovieID($inMovieID);
		}
		if ( $inUserID !== null ) {
			$oObject->setUserID($inUserID);
		}
		if ( $inTimestamp !== null ) {
			$oObject->setTimestamp($inTimestamp);
		}
		if ( $inComment !== null ) {
			$oObject->setComment($inComment);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmMovieHistory by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmMovieHistory
	 * @static 
	 */
	public static function getInstance($inID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inID]) ) {
			return self::$_Instances[$inID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmMovieHistory();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
	
	/**
	 * Returns an array of objects of mofilmMovieHistory
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inMovieID = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieEditHistory';
		if ( $inMovieID !== null ) {
			$query .= ' WHERE movieID = '.dbManager::getInstance()->quote($inMovieID).' ORDER BY timestamp ASC ';
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMovieHistory();
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
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT ID, movieID, userID, timestamp, comment
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieEditHistory';
		
		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		
		if ( count($where) == 0 ) {
			return false;
		}
		
		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_ID !== 0 ) {
				$oStmt->bindValue(':ID', $this->_ID);
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
		$this->setID((int)$inArray['ID']);
		$this->setMovieID((int)$inArray['movieID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setTimestamp($inArray['timestamp']);
		$this->setComment($inArray['comment']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieEditHistory
					( ID, movieID, userID, timestamp, comment)
				VALUES 
					(:ID, :MovieID, :UserID, :Timestamp, :Comment)
				ON DUPLICATE KEY UPDATE
					movieID=VALUES(movieID),
					userID=VALUES(userID),
					timestamp=VALUES(timestamp),
					comment=VALUES(comment)';
				
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':MovieID', $this->_MovieID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':Timestamp', $this->_Timestamp);
					$oStmt->bindValue(':Comment', $this->_Comment);
					
					if ( $oStmt->execute() ) {
						if ( !$this->getID() ) {
							$this->setID($oDB->lastInsertId());
						}
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieEditHistory
			WHERE
				ID = :ID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ID', $this->_ID);
			
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
	 * @return mofilmMovieHistory
	 */
	function reset() {
		$this->_ID = 0;
		$this->_MovieID = 0;
		$this->_UserID = 0;
		$this->_Timestamp = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Comment = '';
		$this->_MarkForDeletion = false;
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
		$string .= " ID[$this->_ID] $newLine";
		$string .= " MovieID[$this->_MovieID] $newLine";
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " Timestamp[$this->_Timestamp] $newLine";
		$string .= " Comment[$this->_Comment] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmMovieHistory';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"MovieID\" value=\"$this->_MovieID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Timestamp\" value=\"$this->_Timestamp\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Comment\" value=\"$this->_Comment\" type=\"string\" /> $newLine";
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
			$valid = $this->checkID($message);
		}
		if ( $valid ) {
			$valid = $this->checkMovieID($message);
		}
		if ( $valid ) {
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkTimestamp($message);
		}
		if ( $valid ) {
			$valid = $this->checkComment($message);
		}
		return $valid;
	}
	
	/**
	 * Checks that $_ID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ID) && $this->_ID !== 0 ) {
			$inMessage .= " is not a valid value for ID";
			$isValid = false;
		}
		return $isValid;
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
			$inMessage .= " is not a valid value for MovieID";
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
			$inMessage .= " is not a valid value for UserID";
			$isValid = false;
		}
		return $isValid;
	}
	
	/**
	 * Checks that $_Timestamp has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkTimestamp(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Timestamp) && $this->_Timestamp !== '' ) {
			$inMessage .= " is not a valid value for Timestamp";
			$isValid = false;
		}
		return $isValid;
	}
	
	/**
	 * Checks that $_Comment has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkComment(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Comment) && $this->_Comment !== '' ) {
			$inMessage .= " is not a valid value for Comment";
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
	 * @return mofilmMovieHistory
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
		return $this->_ID;
	}
	
	/**
	 * Return value of $_ID
	 * 
	 * @return integer
	 * @access public
	 */
	function getID() {
		return $this->_ID;
	}
	
	/**
	 * Set $_ID to ID
	 * 
	 * @param integer $inID
	 * @return mofilmMovieHistory
	 * @access public
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
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
	 * @return mofilmMovieHistory
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
	 * Set $_UserID to UserID
	 * 
	 * @param integer $inUserID
	 * @return mofilmMovieHistory
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
	 * Return value of $_Timestamp
	 * 
	 * @return datetime
	 * @access public
	 */
	function getTimestamp() {
		return $this->_Timestamp;
	}
	
	/**
	 * Set $_Timestamp to Timestamp
	 * 
	 * @param datetime $inTimestamp
	 * @return mofilmMovieHistory
	 * @access public
	 */
	function setTimestamp($inTimestamp) {
		if ( $inTimestamp !== $this->_Timestamp ) {
			$this->_Timestamp = $inTimestamp;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Comment
	 * 
	 * @return string
	 * @access public
	 */
	function getComment() {
		return $this->_Comment;
	}
	
	/**
	 * Set $_Comment to Comment
	 * 
	 * @param string $inComment
	 * @return mofilmMovieHistory
	 * @access public
	 */
	function setComment($inComment) {
		if ( $inComment !== $this->_Comment ) {
			$this->_Comment = $inComment;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_MarkForDeletion
	 *
	 * @return boolean
	 * @access public
	 */
	function getMarkForDeletion() {
		return $this->_MarkForDeletion;
	}

	/**
	 * Set $_MarkForDeletion to $inMarkForDeletion
	 *
	 * @param boolean $inMarkForDeletion
	 * @return mofilmMovieHistory
	 * @access public
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
			$this->setModified();
		}
		return $this;
	}
}