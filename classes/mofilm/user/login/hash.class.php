<?php
/**
 * mofilmUserLoginHash
 *
 * Stored in mofilmUserLoginHash.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserLoginHash
 * @category mofilmUserLoginHash
 * @version $Rev: 10 $
 */


/**
 * mofilmUserLoginHash Class
 *
 * Provides access to records in mofilm_content.userLoginHashes
 *
 * Creating a new record:
 * <code>
 * $oMofilmUserLoginHash = new mofilmUserLoginHash();
 * $oMofilmUserLoginHash->setHash($inHash);
 * $oMofilmUserLoginHash->setUserID($inUserID);
 * $oMofilmUserLoginHash->setEmail($inEmail);
 * $oMofilmUserLoginHash->setCreateDate($inCreateDate);
 * $oMofilmUserLoginHash->setUpdateDate($inUpdateDate);
 * $oMofilmUserLoginHash->setExpiryDate($inExpiryDate);
 * $oMofilmUserLoginHash->setOriginatingIpAddress($inOriginatingIpAddress);
 * $oMofilmUserLoginHash->setOriginatingUserAgent($inOriginatingUserAgent);
 * $oMofilmUserLoginHash->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserLoginHash = new mofilmUserLoginHash($inHash);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmUserLoginHash = new mofilmUserLoginHash();
 * $oMofilmUserLoginHash->setHash($inHash);
 * $oMofilmUserLoginHash->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserLoginHash = mofilmUserLoginHash::getInstance($inHash);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmUserLoginHash
 * @category mofilmUserLoginHash
 */
class mofilmUserLoginHash implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmUserLoginHash
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
	 * Stores $_Hash
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Hash;

	/**
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_Email
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Email;

	/**
	 * Stores $_CreateDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_CreateDate;

	/**
	 * Stores $_UpdateDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_UpdateDate;

	/**
	 * Stores $_ExpiryDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_ExpiryDate;

	/**
	 * Stores $_OriginatingIpAddress
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_OriginatingIpAddress;

	/**
	 * Stores $_OriginatingUserAgent
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_OriginatingUserAgent;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmUserLoginHash
	 *
	 * @param string $inHash
	 * @return mofilmUserLoginHash
	 */
	function __construct($inHash = null) {
		$this->reset();
		if ( $inHash !== null ) {
			$this->setHash($inHash);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new mofilmUserLoginHash containing non-unique properties
	 *
	 * @param integer $inUserID
	 * @param string $inEmail
	 * @param datetime $inCreateDate
	 * @param datetime $inUpdateDate
	 * @param datetime $inExpiryDate
	 * @param string $inOriginatingIpAddress
	 * @param string $inOriginatingUserAgent
	 * @return mofilmUserLoginHash
	 * @static
	 */
	public static function factory($inUserID = null, $inEmail = null, $inCreateDate = null, $inUpdateDate = null, $inExpiryDate = null, $inOriginatingIpAddress = null, $inOriginatingUserAgent = null) {
		$oObject = new mofilmUserLoginHash;
		if ( $inUserID !== null ) {
			$oObject->setUserID($inUserID);
		}
		if ( $inEmail !== null ) {
			$oObject->setEmail($inEmail);
		}
		if ( $inCreateDate !== null ) {
			$oObject->setCreateDate($inCreateDate);
		}
		if ( $inUpdateDate !== null ) {
			$oObject->setUpdateDate($inUpdateDate);
		}
		if ( $inExpiryDate !== null ) {
			$oObject->setExpiryDate($inExpiryDate);
		}
		if ( $inOriginatingIpAddress !== null ) {
			$oObject->setOriginatingIpAddress($inOriginatingIpAddress);
		}
		if ( $inOriginatingUserAgent !== null ) {
			$oObject->setOriginatingUserAgent($inOriginatingUserAgent);
		}
		return $oObject;
	}
	
	/**
	 * Creates a new login hash object from the mofilmUser object
	 * 
	 * @param mofilmUserBase $inUser
	 * @return mofilmUserLoginHash
	 * @static
	 */
	public static function factoryFromUser(mofilmUserBase $inUser) {
		$oObject = new mofilmUserLoginHash();
		$oObject->setUserID($inUser->getID());
		$oObject->setEmail($inUser->getEmail());
		$oObject->setHash($inUser->getLoginHash());
		$oObject->setOriginatingIpAddress($_SERVER['REMOTE_ADDR']);
		$oObject->setOriginatingUserAgent($_SERVER['HTTP_USER_AGENT']);
		return $oObject;
	}

	/**
	 * Get an instance of mofilmUserLoginHash by primary key
	 *
	 * @param string $inHash
	 * @return mofilmUserLoginHash
	 * @static
	 */
	public static function getInstance($inHash) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inHash]) ) {
			return self::$_Instances[$inHash];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserLoginHash();
		$oObject->setHash($inHash);
		if ( $oObject->load() ) {
			self::$_Instances[$inHash] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of mofilmUserLoginHash by unique key (hash)
	 *
	 * @param string $inHash
	 * @return mofilmUserLoginHash
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
		$oObject = new mofilmUserLoginHash();
		$oObject->setHash($inHash);
		if ( $oObject->load() ) {
			self::$_Instances[$inHash] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmUserLoginHash
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLoginHashes';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserLoginHash();
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
			SELECT hash, userID, email, createDate, updateDate, expiryDate, originatingIpAddress, originatingUserAgent
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLoginHashes';

		$where = array();
		if ( $this->_Hash !== '' ) {
			$where[] = ' hash = :Hash ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_Hash !== '' ) {
				$oStmt->bindValue(':Hash', $this->_Hash);
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
		$this->setHash($inArray['hash']);
		$this->setUserID((int)$inArray['userID']);
		$this->setEmail($inArray['email']);
		$this->setCreateDate($inArray['createDate']);
		$this->setUpdateDate($inArray['updateDate']);
		$this->setExpiryDate($inArray['expiryDate']);
		$this->setOriginatingIpAddress($inArray['originatingIpAddress']);
		$this->setOriginatingUserAgent($inArray['originatingUserAgent']);
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
			$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userLoginHashes
					( hash, userID, email, createDate, updateDate, expiryDate, originatingIpAddress, originatingUserAgent)
				VALUES
					(:Hash, :UserID, :Email, :CreateDate, :UpdateDate, :ExpiryDate, :OriginatingIpAddress, :OriginatingUserAgent)
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					email=VALUES(email),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate),
					expiryDate=VALUES(expiryDate),
					originatingIpAddress=VALUES(originatingIpAddress),
					originatingUserAgent=VALUES(originatingUserAgent)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':Hash', $this->_Hash);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':Email', $this->_Email);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':UpdateDate', $this->_UpdateDate);
					$oStmt->bindValue(':ExpiryDate', $this->_ExpiryDate);
					$oStmt->bindValue(':OriginatingIpAddress', $this->_OriginatingIpAddress);
					$oStmt->bindValue(':OriginatingUserAgent', $this->_OriginatingUserAgent);

					if ( $oStmt->execute() ) {
						if ( !$this->getHash() ) {
							$this->setHash($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLoginHashes
			WHERE
				hash = :Hash
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':Hash', $this->_Hash);

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
	 * @return mofilmUserLoginHash
	 */
	function reset() {
		$this->_Hash = '';
		$this->_UserID = 0;
		$this->_Email = '';
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_UpdateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_ExpiryDate = date(
			system::getConfig()->getDatabaseDatetimeFormat()->getParamValue(),
			strtotime(system::getConfig()->getParam('mofilm', 'loginHashExpiry', '+1 week')->getParamValue())
		);
		$this->_OriginatingIpAddress = '';
		$this->_OriginatingUserAgent = '';
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
		$string  = '';
		$string .= " Hash[$this->_Hash] $newLine";
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " Email[$this->_Email] $newLine";
		$string .= " CreateDate[$this->_CreateDate] $newLine";
		$string .= " UpdateDate[$this->_UpdateDate] $newLine";
		$string .= " ExpiryDate[$this->_ExpiryDate] $newLine";
		$string .= " OriginatingIpAddress[$this->_OriginatingIpAddress] $newLine";
		$string .= " OriginatingUserAgent[$this->_OriginatingUserAgent] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmUserLoginHash';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"Hash\" value=\"$this->_Hash\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Email\" value=\"$this->_Email\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"CreateDate\" value=\"$this->_CreateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"UpdateDate\" value=\"$this->_UpdateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"ExpiryDate\" value=\"$this->_ExpiryDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"OriginatingIpAddress\" value=\"$this->_OriginatingIpAddress\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"OriginatingUserAgent\" value=\"$this->_OriginatingUserAgent\" type=\"string\" /> $newLine";
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
			$valid = $this->checkHash($message);
		}
		if ( $valid ) {
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkEmail($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkExpiryDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkOriginatingIpAddress($message);
		}
		if ( $valid ) {
			$valid = $this->checkOriginatingUserAgent($message);
		}
		return $valid;
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
		if ( $isValid && strlen($this->_Hash) > 255 ) {
			$inMessage .= "Hash cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Hash) <= 1 ) {
			$inMessage .= "Hash must be more than 1 character";
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
		if ( $isValid && strlen($this->_Email) > 255 ) {
			$inMessage .= "Email cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Email) <= 1 ) {
			$inMessage .= "Email must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_CreateDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCreateDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_CreateDate) && $this->_CreateDate !== '' ) {
			$inMessage .= "{$this->_CreateDate} is not a valid value for CreateDate";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_UpdateDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkUpdateDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_UpdateDate) && $this->_UpdateDate !== '' ) {
			$inMessage .= "{$this->_UpdateDate} is not a valid value for UpdateDate";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_ExpiryDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkExpiryDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ExpiryDate) && $this->_ExpiryDate !== '' ) {
			$inMessage .= "{$this->_ExpiryDate} is not a valid value for ExpiryDate";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_OriginatingIpAddress has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkOriginatingIpAddress(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_OriginatingIpAddress) && $this->_OriginatingIpAddress !== '' ) {
			$inMessage .= "{$this->_OriginatingIpAddress} is not a valid value for OriginatingIpAddress";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_OriginatingIpAddress) > 50 ) {
			$inMessage .= "OriginatingIpAddress cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_OriginatingIpAddress) <= 1 ) {
			$inMessage .= "OriginatingIpAddress must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_OriginatingUserAgent has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkOriginatingUserAgent(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_OriginatingUserAgent) && $this->_OriginatingUserAgent !== '' ) {
			$inMessage .= "{$this->_OriginatingUserAgent} is not a valid value for OriginatingUserAgent";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_OriginatingUserAgent) > 255 ) {
			$inMessage .= "OriginatingUserAgent cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_OriginatingUserAgent) <= 1 ) {
			$inMessage .= "OriginatingUserAgent must be more than 1 character";
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
	 * @return mofilmUserLoginHash
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
		return $this->_Hash;
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
	 * @return mofilmUserLoginHash
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
	 * @return mofilmUserLoginHash
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
	 * @return mofilmUserLoginHash
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
	 * Return value of $_CreateDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getCreateDate() {
		return $this->_CreateDate;
	}

	/**
	 * Set $_CreateDate to CreateDate
	 *
	 * @param datetime $inCreateDate
	 * @return mofilmUserLoginHash
	 * @access public
	 */
	function setCreateDate($inCreateDate) {
		if ( $inCreateDate !== $this->_CreateDate ) {
			$this->_CreateDate = $inCreateDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_UpdateDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getUpdateDate() {
		return $this->_UpdateDate;
	}

	/**
	 * Set $_UpdateDate to UpdateDate
	 *
	 * @param datetime $inUpdateDate
	 * @return mofilmUserLoginHash
	 * @access public
	 */
	function setUpdateDate($inUpdateDate) {
		if ( $inUpdateDate !== $this->_UpdateDate ) {
			$this->_UpdateDate = $inUpdateDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_ExpiryDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getExpiryDate() {
		return $this->_ExpiryDate;
	}

	/**
	 * Set $_ExpiryDate to ExpiryDate
	 *
	 * @param datetime $inExpiryDate
	 * @return mofilmUserLoginHash
	 * @access public
	 */
	function setExpiryDate($inExpiryDate) {
		if ( $inExpiryDate !== $this->_ExpiryDate ) {
			$this->_ExpiryDate = $inExpiryDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_OriginatingIpAddress
	 *
	 * @return string
	 * @access public
	 */
	function getOriginatingIpAddress() {
		return $this->_OriginatingIpAddress;
	}

	/**
	 * Set $_OriginatingIpAddress to OriginatingIpAddress
	 *
	 * @param string $inOriginatingIpAddress
	 * @return mofilmUserLoginHash
	 * @access public
	 */
	function setOriginatingIpAddress($inOriginatingIpAddress) {
		if ( $inOriginatingIpAddress !== $this->_OriginatingIpAddress ) {
			$this->_OriginatingIpAddress = $inOriginatingIpAddress;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_OriginatingUserAgent
	 *
	 * @return string
	 * @access public
	 */
	function getOriginatingUserAgent() {
		return $this->_OriginatingUserAgent;
	}

	/**
	 * Set $_OriginatingUserAgent to OriginatingUserAgent
	 *
	 * @param string $inOriginatingUserAgent
	 * @return mofilmUserLoginHash
	 * @access public
	 */
	function setOriginatingUserAgent($inOriginatingUserAgent) {
		if ( $inOriginatingUserAgent !== $this->_OriginatingUserAgent ) {
			$this->_OriginatingUserAgent = $inOriginatingUserAgent;
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
	 * @return mofilmUserLoginHash
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}