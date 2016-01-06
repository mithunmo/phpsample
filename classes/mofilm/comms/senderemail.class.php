<?php
/**
 * mofilmCommsSenderemail
 *
 * Stored in mofilmCommsSenderemail.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsSenderemail
 * @category mofilmCommsSenderemail
 * @version $Rev: 806 $
 */


/**
 * mofilmCommsSenderemail Class
 *
 * Provides access to records in mofilm_comms.senderEmails
 *
 * Creating a new record:
 * <code>
 * $oMofilmCommsSenderemail = new mofilmCommsSenderemail();
 * $oMofilmCommsSenderemail->setID($inID);
 * $oMofilmCommsSenderemail->setImapServerID($inImapServerID);
 * $oMofilmCommsSenderemail->setName($inName);
 * $oMofilmCommsSenderemail->setSenderEmail($inSenderEmail);
 * $oMofilmCommsSenderemail->setSenderPassword($inSenderPassword);
 * $oMofilmCommsSenderemail->setCreateDate($inCreateDate);
 * $oMofilmCommsSenderemail->setUpdateDate($inUpdateDate);
 * $oMofilmCommsSenderemail->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCommsSenderemail = new mofilmCommsSenderemail($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmCommsSenderemail = new mofilmCommsSenderemail();
 * $oMofilmCommsSenderemail->setID($inID);
 * $oMofilmCommsSenderemail->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmCommsSenderemail = mofilmCommsSenderemail::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmCommsSenderemail
 * @category mofilmCommsSenderemail
 */
class mofilmCommsSenderemail implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmCommsSenderemail
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
	 * Stores the validator for this object
	 *
	 * @var utilityValidator
	 * @access protected
	 */
	protected $_Validator;

	/**
	 * Stores $_ID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ID;

	/**
	 * Stores $_ImapServerID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ImapServerID;

	/**
	 * Stores $_Name
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Name;

	/**
	 * Stores $_SenderEmail
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_SenderEmail;

	/**
	 * Stores $_SenderPassword
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_SenderPassword;

	/**
	 * Stores $_CreateDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_CreateDate;

	/**
	 * Stores $_UpdateDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_UpdateDate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;

	/**
	 *  SALT for the encryption
	 *  @var SALT
	 */
	const SALT = "mofilm";



	/**
	 * Returns a new instance of mofilmCommsSenderemail
	 *
	 * @param integer $inID
	 * @return mofilmCommsSenderemail
	 */
	function __construct($inID = null) {
		$this->reset();
		if ( $inID !== null ) {
			$this->setID($inID);
			$this->load();
		}
	}

	/**
	 * Object destructor, used to remove internal object instances
	 *
	 * @return void
 	 */
	function __destruct() {
		if ( $this->_Validator instanceof utilityValidator ) {
			$this->_Validator = null;
		}
	}

	/**
	 * Get an instance of mofilmCommsSenderemail by primary key
	 *
	 * @param integer $inID
	 * @return mofilmCommsSenderemail
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
		$oObject = new mofilmCommsSenderemail();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmCommsSenderemail
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, imapServerID, name, senderEmail, senderPassword, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.senderEmails
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmCommsSenderemail();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

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
			SELECT ID, imapServerID, name, senderEmail, senderPassword, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.senderEmails';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $this->_ID !== 0 ) {
			$oStmt->bindValue(':ID', $this->getID());
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

		return $return;
	}

	/**
	 * Loads a record by array
	 *
	 * @param array $inArray
	 * @return void
 	 */
	function loadFromArray(array $inArray) {
		$this->setID((int)$inArray['ID']);
		$this->setImapServerID((int)$inArray['imapServerID']);
		$this->setName($inArray['name']);
		$this->setSenderEmail($inArray['senderEmail']);
		$this->setSenderPassword($inArray['senderPassword']);
		$this->setCreateDate($inArray['createDate']);
		$this->setUpdateDate($inArray['updateDate']);
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

			$this->getUpdateDate()->setTimestamp(time());
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.senderEmails
					( ID, imapServerID, name, senderEmail, senderPassword, createDate, updateDate)
				VALUES
					(:ID, :ImapServerID, :Name, :SenderEmail, :SenderPassword, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					imapServerID=VALUES(imapServerID),
					name=VALUES(name),
					senderEmail=VALUES(senderEmail),
					senderPassword=VALUES(senderPassword),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':ImapServerID', $this->getImapServerID());
				$oStmt->bindValue(':Name', $this->getName());
				$oStmt->bindValue(':SenderEmail', $this->getSenderEmail());
				$oStmt->bindValue(':SenderPassword', $this->getSenderPassword());
				$oStmt->bindValue(':CreateDate', $this->getCreateDate());
				$oStmt->bindValue(':UpdateDate', $this->getUpdateDate());

				if ( $oStmt->execute() ) {
					if ( !$this->getID() ) {
						$this->setID($oDB->lastInsertId());
					}
					$this->setModified(false);
					$return = true;
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.senderEmails
			WHERE
				ID = :ID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':ID', $this->getID());

		if ( $oStmt->execute() ) {
			$oStmt->closeCursor();
			$this->reset();
			return true;
		}

		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mofilmCommsSenderemail
	 */
	function reset() {
		$this->_ID = 0;
		$this->_ImapServerID = 0;
		$this->_Name = '';
		$this->_SenderEmail = '';
		$this->_SenderPassword = '';
		$this->_CreateDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_UpdateDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Validator = null;
		$this->setModified(false);
		$this->setMarkForDeletion(false);
		return $this;
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
	 * Returns the validator, creating one if not set
	 *
	 * @return utilityValidator
	 */
	function getValidator() {
		if ( !$this->_Validator instanceof utilityValidator ) {
			$this->_Validator = new utilityValidator();
		}
		return $this->_Validator;
	}

	/**
	 * Set a pre-built validator instance
	 *
	 * @param utilityValidator $inValidator
	 * @return mofilmCommsSenderemail
	 */
	function setValidator(utilityValidator $inValidator) {
		$this->_Validator = $inValidator;
		return $this;
	}

	/**
	 * Returns true if object is valid, any errors are added to $inMessage
	 *
	 * @param string $inMessage
	 * @return boolean
	 */
	function isValid(&$inMessage = '') {
		$valid = true;

		$oValidator = $this->getValidator();
		$oValidator->reset();
		$oValidator->setData($this->toArray())->setRules($this->getValidationRules());
		if ( !$oValidator->isValid() ) {
			foreach ( $oValidator->getMessages() as $key => $messages ) {
				$inMessage .= "Error with $key: ".implode(', ', $messages)."\n";
			}
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Returns the array of rules used to validate this object
	 *
	 * @return array
 	 */
	function getValidationRules() {
		return array(
			'_ID' => array(
				'number' => array(),
			),
			'_ImapServerID' => array(
				'number' => array(),
			),
			'_Name' => array(
				'string' => array(),
			),
			'_SenderEmail' => array(
				'string' => array(),
			),
			'_SenderPassword' => array(
				'string' => array(),
			),
			'_CreateDate' => array(
				'dateTime' => array(),
			),
			'_UpdateDate' => array(
				'dateTime' => array(),
			),
		);
	}
	


	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( !$modified && $this->getUpdateDate() ) {
			$this->_Modified = $this->getUpdateDate()->isModified() || $modified;
		}

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmCommsSenderemail
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the primaryKey
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->_ID;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * {@link mofilmCommsSenderemail::PRIMARY_KEY_SEPARATOR}.
 	 *
	 * @param string $inKey
	 * @return mofilmCommsSenderemail
  	 */
	function setPrimaryKey($inKey) {
		list($ID) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setID($ID);
	}

	/**
	 * Return the current value of the property $_ID
	 *
	 * @return integer
 	 */
	function getID() {
		return $this->_ID;
	}

	/**
	 * Set the object property _ID to $inID
	 *
	 * @param integer $inID
	 * @return mofilmCommsSenderemail
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ImapServerID
	 *
	 * @return integer
 	 */
	function getImapServerID() {
		return $this->_ImapServerID;
	}

	/**
	 * Set the object property _ImapServerID to $inImapServerID
	 *
	 * @param integer $inImapServerID
	 * @return mofilmCommsSenderemail
	 */
	function setImapServerID($inImapServerID) {
		if ( $inImapServerID !== $this->_ImapServerID ) {
			$this->_ImapServerID = $inImapServerID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Name
	 *
	 * @return string
 	 */
	function getName() {
		return $this->_Name;
	}

	/**
	 * Set the object property _Name to $inName
	 *
	 * @param string $inName
	 * @return mofilmCommsSenderemail
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SenderEmail
	 *
	 * @return string
 	 */
	function getSenderEmail() {
		return $this->_SenderEmail;
	}

	/**
	 * Set the object property _SenderEmail to $inSenderEmail
	 *
	 * @param string $inSenderEmail
	 * @return mofilmCommsSenderemail
	 */
	function setSenderEmail($inSenderEmail) {
		if ( $inSenderEmail !== $this->_SenderEmail ) {
			$this->_SenderEmail = $inSenderEmail;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SenderPassword
	 *
	 * @return string
 	 */
	function getSenderPassword() {
		return $this->_SenderPassword;
	}

	/**
	 * Set the object property _SenderPassword to $inSenderPassword
	 *
	 * @param string $inSenderPassword
	 * @return mofilmCommsSenderemail
	 */
	function setSenderPassword($inSenderPassword) {
		if ( $inSenderPassword !== $this->_SenderPassword ) {
			$this->_SenderPassword = $inSenderPassword;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_CreateDate
	 *
	 * @return systemDateTime
 	 */
	function getCreateDate() {
		return $this->_CreateDate;
	}

	/**
	 * Set the object property _CreateDate to $inCreateDate
	 *
	 * @param systemDateTime $inCreateDate
	 * @return mofilmCommsSenderemail
	 */
	function setCreateDate($inCreateDate) {
		if ( $inCreateDate !== $this->_CreateDate ) {
			if ( !$inCreateDate instanceof DateTime ) {
				$inCreateDate = new systemDateTime($inCreateDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_CreateDate = $inCreateDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_UpdateDate
	 *
	 * @return systemDateTime
 	 */
	function getUpdateDate() {
		return $this->_UpdateDate;
	}

	/**
	 * Set the object property _UpdateDate to $inUpdateDate
	 *
	 * @param systemDateTime $inUpdateDate
	 * @return mofilmCommsSenderemail
	 */
	function setUpdateDate($inUpdateDate) {
		if ( $inUpdateDate !== $this->_UpdateDate ) {
			if ( !$inUpdateDate instanceof DateTime ) {
				$inUpdateDate = new systemDateTime($inUpdateDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_UpdateDate = $inUpdateDate;
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
	 * @return mofilmCommsSenderemail
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	/**
	 * Encrypts the string passed using base_encode
	 * @param string $text
	 * @return string
	 */
	function encrypt($text){
		return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, self::SALT, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
	}

	/**
	 * Decrypts the string passed to the origianl string
	 * @param string $text
	 * @return string
	 */
	function decrypt($text){
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, self::SALT, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
	}

}