<?php
/**
 * mofilmCommsEmail
 *
 * Stored in mofilmCommsEmail.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsEmail
 * @category mofilmCommsEmail
 * @version $Rev: 275 $
 */


/**
 * mofilmCommsEmail Class
 *
 * Provides access to records in mofilm_comms.emails
 *
 * Creating a new record:
 * <code>
 * $oMofilmCommsEmail = new mofilmCommsEmail();
 * $oMofilmCommsEmail->setID($inID);
 * $oMofilmCommsEmail->setEmail($inEmail);
 * $oMofilmCommsEmail->setUserID($inUserID);
 * $oMofilmCommsEmail->setRegistered($inRegistered);
 * $oMofilmCommsEmail->setHash($inHash);
 * $oMofilmCommsEmail->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCommsEmail = new mofilmCommsEmail($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmCommsEmail = new mofilmCommsEmail();
 * $oMofilmCommsEmail->setID($inID);
 * $oMofilmCommsEmail->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmCommsEmail = mofilmCommsEmail::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmCommsEmail
 * @category mofilmCommsEmail
 */
class mofilmCommsEmail implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmCommsEmail
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
	 * Stores $_Email
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Email;

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_Registered
	 *
	 * @var datetime
	 * @access protected
	 */
	protected $_Registered;

	/**
	 * Stores $_Hash
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Hash;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of mofilmCommsEmail
	 *
	 * @param integer $inID
	 * @return mofilmCommsEmail
	 */
	function __construct($inID = null) {
		$this->reset();
		if ( $inID !== null ) {
			$this->setID($inID);
			$this->load();
		}
		return $this;
	}

	function  __destruct() {
		unset($this);
	}

	/**
	 * Unloads and destroys a mofilmCommsEmail instance matching $inId
	 *
	 * @param integer $inId
	 * @return void
	 * @static
	 */
	public static function destroy($inId) {
		if ( isset(self::$_Instances[$inId]) ) {
			self::$_Instances[$inId] = null;
			unset(self::$_Instances[$inId]);
		}
	}


	/**
	 * Creates a new mofilmCommsEmail containing non-unique properties
	 *
	 * @param integer $inUserID
	 * @param datetime $inRegistered
	 * @return mofilmCommsEmail
	 * @static
	 */
	public static function factory($inUserID = null, $inRegistered = null) {
		$oObject = new mofilmCommsEmail;
		if ( $inUserID !== null ) {
			$oObject->setUserID($inUserID);
		}
		if ( $inRegistered !== null ) {
			$oObject->setRegistered($inRegistered);
		}
		return $oObject;
	}

	/**
	 * Creates a new mofilmCommsEmail from a user object
	 *
	 * @param mofilmUserBase $inUser
	 * @return mofilmCommsEmail
	 * @static
	 */
	public static function factoryFromUser(mofilmUserBase $inUser) {
		$oObject = new mofilmCommsEmail();
		$oObject->setEmail($inUser->getEmail());
		$oObject->setHash(md5(date('U') . ':' . $inUser->getEmail()));
		$oObject->setUserID($inUser->getID());
		return $oObject;
	}

	/**
	 * Get an instance of mofilmCommsEmail by primary key
	 *
	 * @param integer $inID
	 * @return mofilmCommsEmail
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
		$oObject = new mofilmCommsEmail();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of mofilmCommsEmail by unique key (userid)
	 *
	 * @param string $inUser
	 * @return mofilmCommsEmail
	 * @static
	 */
	public static function getInstanceByUser($inUser) {
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmCommsEmail();
		$oObject->setUserID($inUser);
		$oObject->load();

		return $oObject;
	}

	/**
	 * Get instance of mofilmCommsEmail by unique key (hash)
	 *
	 * @param string $inHash
	 * @return mofilmCommsEmail
	 * @static
	 */
	public static function getInstanceByHash($inHash) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inHash]) ) {
			return self::$_Instances[$inHash];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmCommsEmail();
		$oObject->setHash($inHash);
		if ( $oObject->load() ) {
			self::$_Instances[$inHash] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of mofilmCommsEmail by unique key (email)
	 *
	 * @param string $inEmail
	 * @return mofilmCommsEmail
	 * @static
	 */
	public static function getInstanceByEmail($inEmail) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inEmail]) ) {
			return self::$_Instances[$inEmail];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmCommsEmail();
		$oObject->setEmail($inEmail);
		if ( $oObject->load() ) {
			self::$_Instances[$inEmail] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmCommsEmail
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.emails';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT ' . $inOffset . ',' . $inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmCommsEmail();
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
			SELECT ID, email, userID, registered, hash
			  FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.emails';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		if ( $this->_Hash !== '' ) {
			$where[] = ' hash = :Hash ';
		}
		if ( $this->_Email !== '' ) {
			$where[] = ' email = :Email ';
		}
		if ( $this->_UserID !== null ) {
			$where[] = ' userID = :userID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE ' . implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_ID !== 0 ) {
				$oStmt->bindValue(':ID', $this->_ID);
			}
			if ( $this->_Hash !== '' ) {
				$oStmt->bindValue(':Hash', $this->_Hash);
			}
			if ( $this->_Email !== '' ) {
				$oStmt->bindValue(':Email', $this->_Email);
			}
			if ( $this->_UserID !== null ) {
				$oStmt->bindValue(':userID', $this->_UserID);
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
		$this->setID((int) $inArray['ID']);
		$this->setEmail($inArray['email']);
		$this->setUserID((int) $inArray['userID']);
		$this->setRegistered($inArray['registered']);
		$this->setHash($inArray['hash']);
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
				INSERT INTO ' . system::getConfig()->getDatabase('mofilm_comms') . '.emails
					( ID, email, userID, registered, hash)
				VALUES
					(:ID, :Email, :UserID, :Registered, :Hash)
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					registered=VALUES(registered)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Email', $this->_Email);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':Registered', $this->_Registered);
					$oStmt->bindValue(':Hash', $this->_Hash);

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
			DELETE FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.emails
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
	 * @return mofilmCommsEmail
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Email = '';
		$this->_UserID = null;
		$this->_Registered = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Hash = '';
		$this->setModified(false);
		$this->setMarkForDeletion(false);
		return $this;
	}

	/**
	 * Returns object as a string with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toString($newLine = "\n") {
		$string = '';
		$string .= " ID[$this->_ID] $newLine";
		$string .= " Email[$this->_Email] $newLine";
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " Registered[$this->_Registered] $newLine";
		$string .= " Hash[$this->_Hash] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmCommsEmail';
		$xml = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Email\" value=\"$this->_Email\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Registered\" value=\"$this->_Registered\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Hash\" value=\"$this->_Hash\" type=\"string\" /> $newLine";
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
			$valid = $this->checkEmail($message);
		}
		if ( $valid ) {
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkRegistered($message);
		}
		if ( $valid ) {
			$valid = $this->checkHash($message);
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
			$inMessage .= "{$this->_ID} is not a valid value for ID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Email has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkEmail(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Email) && $this->_Email !== '' ) {
			$inMessage .= "{$this->_Email} is not a valid value for Email";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Email) > 100 ) {
			$inMessage .= "Email cannot be more than 100 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Email) <= 1 ) {
			$inMessage .= "Email must be more than 1 character";
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
		if ( !is_numeric($this->_UserID) && $this->_UserID !== null && $this->_UserID !== 0 ) {
			$inMessage .= "{$this->_UserID} is not a valid value for UserID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Registered has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkRegistered(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Registered) && $this->_Registered !== '' ) {
			$inMessage .= "{$this->_Registered} is not a valid value for Registered";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Hash has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkHash(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Hash) && $this->_Hash !== '' ) {
			$inMessage .= "{$this->_Hash} is not a valid value for Hash";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Hash) > 32 ) {
			$inMessage .= "Hash cannot be more than 32 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Hash) <= 1 ) {
			$inMessage .= "Hash must be more than 1 character";
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
	 * @return mofilmCommsEmail
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
	 * @return mofilmCommsEmail
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
	 * Return value of $_Email
	 *
	 * @return string
	 * @access public
	 */
	function getEmail() {
		return $this->_Email;
	}

	/**
	 * Set $_Email to Email
	 *
	 * @param string $inEmail
	 * @return mofilmCommsEmail
	 * @access public
	 */
	function setEmail($inEmail) {
		if ( $inEmail !== $this->_Email ) {
			$this->_Email = $inEmail;
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
	 * @return mofilmCommsEmail
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
	 * Return value of $_Registered
	 *
	 * @return datetime
	 * @access public
	 */
	function getRegistered() {
		return $this->_Registered;
	}

	/**
	 * Set $_Registered to Registered
	 *
	 * @param datetime $inRegistered
	 * @return mofilmCommsEmail
	 * @access public
	 */
	function setRegistered($inRegistered) {
		if ( $inRegistered !== $this->_Registered ) {
			$this->_Registered = $inRegistered;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Hash
	 *
	 * @return string
	 * @access public
	 */
	function getHash() {
		return $this->_Hash;
	}

	/**
	 * Set $_Hash to Hash
	 *
	 * @param string $inHash
	 * @return mofilmCommsEmail
	 * @access public
	 */
	function setHash($inHash) {
		if ( $inHash !== $this->_Hash ) {
			$this->_Hash = $inHash;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_MarkForDeletion
	 *
	 * @return boolean
	 */
	function getMarkForDeletion() {
		return $this->_MarkForDeletion;
	}

	/**
	 * Set $_MarkForDeletion to $inMarkForDeletion
	 *
	 * @param boolean $inMarkForDeletion
	 * @return mofilmCommsEmail
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}

	/**
	 * Gets whether an email has been subscribed
	 *
	 * @param integer $inEmailID
	 * @return boolean
	 */
	function getSubscribedStatus() {
		$query = 'SELECT COUNT(*) AS Count FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.subscriptions';
		$query .= ' WHERE emailID =' . $this->getID();

		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && $res["Count"] > 0 ) {
			return true;
		} else {
			return false;
		}
	}

}