<?php
/**
 * mofilmBulkUser
 * 
 * Stored in mofilmBulkUser.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmBulkUser
 * @category mofilmBulkUser
 * @version $Rev: 10 $
 */


/**
 * mofilmBulkUser Class
 * 
 * Provides access to records in mofilm_content.bulkUsers
 * 
 * Creating a new record:
 * <code>
 * $oMofilmBulkUser = new mofilmBulkUser();
 * $oMofilmBulkUser->setUserID($inUserID);
 * $oMofilmBulkUser->setFtpDir($inFtpDir);
 * $oMofilmBulkUser->setUsername($inUsername);
 * $oMofilmBulkUser->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmBulkUser = new mofilmBulkUser($inUserID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmBulkUser = new mofilmBulkUser();
 * $oMofilmBulkUser->setUserID($inUserID);
 * $oMofilmBulkUser->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmBulkUser = mofilmBulkUser::getInstance($inUserID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmBulkUser
 * @category mofilmBulkUser
 */
class mofilmBulkUser implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmBulkUser
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
	 * Stores $_FtpDir
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_FtpDir;
			
	/**
	 * Stores $_Username
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Username;
			
	
	
	/**
	 * Returns a new instance of mofilmBulkUser
	 * 
	 * @param integer $inUserID
	 * @return mofilmBulkUser
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
	 * Creates a new mofilmBulkUser containing non-unique properties
	 * 
	 * @param string $inFtpDir
	 * @param string $inUsername
	 * @return mofilmBulkUser
	 * @static 
	 */
	public static function factory($inFtpDir = null, $inUsername = null) {
		$oObject = new mofilmBulkUser;
		if ( $inFtpDir !== null ) {
			$oObject->setFtpDir($inFtpDir);
		}
		if ( $inUsername !== null ) {
			$oObject->setUsername($inUsername);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmBulkUser by primary key
	 * 
	 * @param integer $inUserID
	 * @return mofilmBulkUser
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
		$oObject = new mofilmBulkUser();
		$oObject->setUserID($inUserID);
		if ( $oObject->load() ) {
			self::$_Instances[$inUserID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmBulkUser
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.bulkUsers';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmBulkUser();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.bulkUsers';
		
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
		$this->setFtpDir($inArray['ftpDir']);
		$this->setUsername($inArray['username']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.bulkUsers
					( userID, ftpDir, username)
				VALUES 
					(:UserID, :FtpDir, :Username)
				ON DUPLICATE KEY UPDATE
					ftpDir=VALUES(ftpDir),
					username=VALUES(username)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':FtpDir', $this->_FtpDir);
					$oStmt->bindValue(':Username', $this->_Username);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.bulkUsers
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
	 * @return mofilmBulkUser
	 */
	function reset() {
		$this->_UserID = 0;
		$this->_FtpDir = '';
		$this->_Username = '';
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
		$string .= " FtpDir[$this->_FtpDir] $newLine";
		$string .= " Username[$this->_Username] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmBulkUser';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"FtpDir\" value=\"$this->_FtpDir\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Username\" value=\"$this->_Username\" type=\"string\" /> $newLine";
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
			$valid = $this->checkFtpDir($message);
		}
		if ( $valid ) {
			$valid = $this->checkUsername($message);
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
	 * Checks that $_FtpDir has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkFtpDir(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_FtpDir) && $this->_FtpDir !== '' ) {
			$inMessage .= "{$this->_FtpDir} is not a valid value for FtpDir";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_FtpDir) > 50 ) {
			$inMessage .= "FtpDir cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_FtpDir) <= 1 ) {
			$inMessage .= "FtpDir must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Username has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkUsername(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Username) && $this->_Username !== '' ) {
			$inMessage .= "{$this->_Username} is not a valid value for Username";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_Username) > 20 ) {
			$inMessage .= "Username cannot be more than 20 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Username) <= 1 ) {
			$inMessage .= "Username must be more than 1 character";
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
	 * @return mofilmBulkUser
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
	 * @return mofilmBulkUser
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
	 * Return value of $_FtpDir
	 * 
	 * @return string
	 * @access public
	 */
	function getFtpDir() {
		return $this->_FtpDir;
	}
	
	/**
	 * Set $_FtpDir to FtpDir
	 * 
	 * @param string $inFtpDir
	 * @return mofilmBulkUser
	 * @access public
	 */
	function setFtpDir($inFtpDir) {
		if ( $inFtpDir !== $this->_FtpDir ) {
			$this->_FtpDir = $inFtpDir;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Username
	 * 
	 * @return string
	 * @access public
	 */
	function getUsername() {
		return $this->_Username;
	}
	
	/**
	 * Set $_Username to Username
	 * 
	 * @param string $inUsername
	 * @return mofilmBulkUser
	 * @access public
	 */
	function setUsername($inUsername) {
		if ( $inUsername !== $this->_Username ) {
			$this->_Username = $inUsername;
			$this->setModified();
		}
		return $this;
	}
}