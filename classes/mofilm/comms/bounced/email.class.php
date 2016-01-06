<?php
/**
 * mofilmCommsBouncedEmail
 *
 * Stored in mofilmCommsBouncedEmail.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsBouncedEmail
 * @category mofilmCommsBouncedEmail
 * @version $Rev: 806 $
 */


/**
 * mofilmCommsBouncedEmail Class
 *
 * Provides access to records in mofilm_comms.bouncedEmails
 *
 * Creating a new record:
 * <code>
 * $oMofilmCommsBouncedEmail = new mofilmCommsBouncedEmail();
 * $oMofilmCommsBouncedEmail->setID($inID);
 * $oMofilmCommsBouncedEmail->setEmailAddress($inEmailAddress);
 * $oMofilmCommsBouncedEmail->setErrorDescription($inErrorDescription);
 * $oMofilmCommsBouncedEmail->setSenderAddress($inSenderAddress);
 * $oMofilmCommsBouncedEmail->setCreateDate($inCreateDate);
 * $oMofilmCommsBouncedEmail->setUpdateDate($inUpdateDate);
 * $oMofilmCommsBouncedEmail->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCommsBouncedEmail = new mofilmCommsBouncedEmail($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmCommsBouncedEmail = new mofilmCommsBouncedEmail();
 * $oMofilmCommsBouncedEmail->setID($inID);
 * $oMofilmCommsBouncedEmail->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmCommsBouncedEmail = mofilmCommsBouncedEmail::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmCommsBouncedEmail
 * @category mofilmCommsBouncedEmail
 */
class mofilmCommsBouncedEmail implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmCommsBouncedEmail
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
	 * Stores $_EmailAddress
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_EmailAddress;

	/**
	 * Stores $_ErrorDescription
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ErrorDescription;

	/**
	 * Stores $_SenderAddress
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_SenderAddress;

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
	 * Returns a new instance of mofilmCommsBouncedEmail
	 *
	 * @param integer $inID
	 * @return mofilmCommsBouncedEmail
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
	 * Get an instance of mofilmCommsBouncedEmail by primary key
	 *
	 * @param integer $inID
	 * @return mofilmCommsBouncedEmail
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
		$oObject = new mofilmCommsBouncedEmail();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmCommsBouncedEmail
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
			SELECT ID, emailAddress, errorDescription, senderAddress, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.bouncedEmails
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmCommsBouncedEmail();
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
			SELECT ID, emailAddress, errorDescription, senderAddress, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.bouncedEmails';

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
		$this->setEmailAddress($inArray['emailAddress']);
		$this->setErrorDescription($inArray['errorDescription']);
		$this->setSenderAddress($inArray['senderAddress']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.bouncedEmails
					( ID, emailAddress, errorDescription, senderAddress, createDate, updateDate)
				VALUES
					(:ID, :EmailAddress, :ErrorDescription, :SenderAddress, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					emailAddress=VALUES(emailAddress),
					errorDescription=VALUES(errorDescription),
					senderAddress=VALUES(senderAddress),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':EmailAddress', $this->getEmailAddress());
				$oStmt->bindValue(':ErrorDescription', $this->getErrorDescription());
				$oStmt->bindValue(':SenderAddress', $this->getSenderAddress());
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.bouncedEmails
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
	 * @return mofilmCommsBouncedEmail
	 */
	function reset() {
		$this->_ID = 0;
		$this->_EmailAddress = '';
		$this->_ErrorDescription = '';
		$this->_SenderAddress = '';
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
	 * @return mofilmCommsBouncedEmail
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
			'_EmailAddress' => array(
				'string' => array(),
			),
			'_ErrorDescription' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_SenderAddress' => array(
				'string' => array('min' => 1,'max' => 255,),
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
	 * @return mofilmCommsBouncedEmail
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
	 * {@link mofilmCommsBouncedEmail::PRIMARY_KEY_SEPARATOR}.
 	 *
	 * @param string $inKey
	 * @return mofilmCommsBouncedEmail
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
	 * @return mofilmCommsBouncedEmail
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_EmailAddress
	 *
	 * @return string
 	 */
	function getEmailAddress() {
		return $this->_EmailAddress;
	}

	/**
	 * Set the object property _EmailAddress to $inEmailAddress
	 *
	 * @param string $inEmailAddress
	 * @return mofilmCommsBouncedEmail
	 */
	function setEmailAddress($inEmailAddress) {
		if ( $inEmailAddress !== $this->_EmailAddress ) {
			$this->_EmailAddress = $inEmailAddress;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ErrorDescription
	 *
	 * @return string
 	 */
	function getErrorDescription() {
		return $this->_ErrorDescription;
	}

	/**
	 * Set the object property _ErrorDescription to $inErrorDescription
	 *
	 * @param string $inErrorDescription
	 * @return mofilmCommsBouncedEmail
	 */
	function setErrorDescription($inErrorDescription) {
		if ( $inErrorDescription !== $this->_ErrorDescription ) {
			$this->_ErrorDescription = $inErrorDescription;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SenderAddress
	 *
	 * @return string
 	 */
	function getSenderAddress() {
		return $this->_SenderAddress;
	}

	/**
	 * Set the object property _SenderAddress to $inSenderAddress
	 *
	 * @param string $inSenderAddress
	 * @return mofilmCommsBouncedEmail
	 */
	function setSenderAddress($inSenderAddress) {
		if ( $inSenderAddress !== $this->_SenderAddress ) {
			$this->_SenderAddress = $inSenderAddress;
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
	 * @return mofilmCommsBouncedEmail
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
	 * @return mofilmCommsBouncedEmail
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
	 * @return mofilmCommsBouncedEmail
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}