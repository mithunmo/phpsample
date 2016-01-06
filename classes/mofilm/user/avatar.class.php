<?php
/**
 * mofilmUserAvatar
 * 
 * Stored in mofilmUserAvatar.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserAvatar
 * @category mofilmUserAvatar
 * @version $Rev: 10 $
 */


/**
 * mofilmUserAvatar Class
 * 
 * Provides access to records in mofilm_content.userAvatars
 * 
 * Creating a new record:
 * <code>
 * $oMofilmUserAvatar = new mofilmUserAvatar();
 * $oMofilmUserAvatar->setUserID($inUserID);
 * $oMofilmUserAvatar->setImageFilename($inImageFilename);
 * $oMofilmUserAvatar->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserAvatar = new mofilmUserAvatar($inUserID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmUserAvatar = new mofilmUserAvatar();
 * $oMofilmUserAvatar->setUserID($inUserID);
 * $oMofilmUserAvatar->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserAvatar = mofilmUserAvatar::getInstance($inUserID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmUserAvatar
 * @category mofilmUserAvatar
 */
class mofilmUserAvatar implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmUserAvatar
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
	 * Stores $_UserID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;
			
	/**
	 * Stores $_ImageFilename
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_ImageFilename;
			
	
	
	/**
	 * Returns a new instance of mofilmUserAvatar
	 * 
	 * @param integer $inUserID
	 * @return mofilmUserAvatar
	 */
	function __construct($inUserID = null) {
		$this->reset();
		if ( $inUserID !== null ) {
			$this->setUserID($inUserID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new mofilmUserAvatar containing non-unique properties
	 * 
	 * @param string $inImageFilename
	 * @return mofilmUserAvatar
	 * @static 
	 */
	public static function factory($inImageFilename = null) {
		$oObject = new mofilmUserAvatar;
		if ( $inImageFilename !== null ) {
			$oObject->setImageFilename($inImageFilename);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmUserAvatar by primary key
	 * 
	 * @param integer $inUserID
	 * @return mofilmUserAvatar
	 * @static 
	 */
	public static function getInstance($inUserID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inUserID]) ) {
			return self::$_Instances[$inUserID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserAvatar();
		$oObject->setUserID($inUserID);
		if ( $oObject->load() ) {
			self::$_Instances[$inUserID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmUserAvatar
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userAvatars';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserAvatar();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userAvatars';
		
		$where = array();
		if ( $this->_UserID !== 0 ) {
			$where[] = ' userID = :UserID ';
		}
						
		if ( count($where) == 0 ) {
			return false;
		}
		
		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
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
		$this->setUserID((int)$inArray['userID']);
		$this->setImageFilename($inArray['imageFilename']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userAvatars
					( userID, imageFilename)
				VALUES 
					(:UserID, :ImageFilename)
				ON DUPLICATE KEY UPDATE
					imageFilename=VALUES(imageFilename)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':ImageFilename', $this->_ImageFilename);
								
					if ( $oStmt->execute() ) {
						if ( !$this->getUserID() ) {
							$this->setUserID($oDB->lastInsertId());
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userAvatars
		WHERE
			userID = :UserID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
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
	 * @return mofilmUserAvatar
	 */
	function reset() {
		$this->_UserID = 0;
		$this->_ImageFilename = '';
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
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " ImageFilename[$this->_ImageFilename] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmUserAvatar';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ImageFilename\" value=\"$this->_ImageFilename\" type=\"string\" /> $newLine";
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
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkImageFilename($message);
		}
		return $valid;
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
	 * Checks that $_ImageFilename has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkImageFilename(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ImageFilename) && $this->_ImageFilename !== '' ) {
			$inMessage .= "{$this->_ImageFilename} is not a valid value for ImageFilename";
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
	 * @return mofilmUserAvatar
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
		return $this->_UserID;
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
	 * @return mofilmUserAvatar
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
	 * Return value of $_ImageFilename
	 * 
	 * @return string
	 * @access public
	 */
	function getImageFilename() {
		return $this->_ImageFilename;
	}
	
	/**
	 * Set $_ImageFilename to ImageFilename
	 * 
	 * @param string $inImageFilename
	 * @return mofilmUserAvatar
	 * @access public
	 */
	function setImageFilename($inImageFilename) {
		if ( $inImageFilename !== $this->_ImageFilename ) {
			$this->_ImageFilename = $inImageFilename;
			$this->setModified();
		}
		return $this;
	}
}