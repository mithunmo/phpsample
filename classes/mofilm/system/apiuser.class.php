<?php
/**
 * mofilmSystemAPIUser
 *
 * Stored in mofilmSystemAPIUser.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSystemAPIUser
 * @category mofilmSystemAPIUser
 * @version $Rev: 806 $
 */


/**
 * mofilmSystemAPIUser Class
 *
 * Provides access to records in mofilm_system.mofilmAPIUsers
 *
 * Creating a new record:
 * <code>
 * $oMofilmSystemAPIUser = new mofilmSystemAPIUser();
 * $oMofilmSystemAPIUser->setID($inID);
 * $oMofilmSystemAPIUser->setMofilmAPIKeyID($inMofilmAPIKeyID);
 * $oMofilmSystemAPIUser->setCompanyName($inCompanyName);
 * $oMofilmSystemAPIUser->setEmailContact($inEmailContact);
 * $oMofilmSystemAPIUser->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmSystemAPIUser = new mofilmSystemAPIUser($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmSystemAPIUser = new mofilmSystemAPIUser();
 * $oMofilmSystemAPIUser->setID($inID);
 * $oMofilmSystemAPIUser->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmSystemAPIUser = mofilmSystemAPIUser::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmSystemAPIUser
 * @category mofilmSystemAPIUser
 */
class mofilmSystemAPIUser implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmSystemAPIUser
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
	 * Stores $_MofilmAPIKeyID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MofilmAPIKeyID;

	/**
	 * Stores $_CompanyName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_CompanyName;

	/**
	 * Stores $_EmailContact
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_EmailContact;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmSystemAPIUser
	 *
	 * @param integer $inID
	 * @return mofilmSystemAPIUser
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
	 * Get an instance of mofilmSystemAPIUser by primary key
	 *
	 * @param integer $inID
	 * @return mofilmSystemAPIUser
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
		$oObject = new mofilmSystemAPIUser();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmSystemAPIUser
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
			SELECT ID, mofilmAPIKeyID, companyName, emailContact
			  FROM '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIUsers
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmSystemAPIUser();
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
			SELECT ID, mofilmAPIKeyID, companyName, emailContact
			  FROM '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIUsers';

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
		$this->setMofilmAPIKeyID((int)$inArray['mofilmAPIKeyID']);
		$this->setCompanyName($inArray['companyName']);
		$this->setEmailContact($inArray['emailContact']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIUsers
					( ID, mofilmAPIKeyID, companyName, emailContact)
				VALUES
					(:ID, :MofilmAPIKeyID, :CompanyName, :EmailContact)
				ON DUPLICATE KEY UPDATE
					mofilmAPIKeyID=VALUES(mofilmAPIKeyID),
					companyName=VALUES(companyName),
					emailContact=VALUES(emailContact)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':MofilmAPIKeyID', $this->getMofilmAPIKeyID());
				$oStmt->bindValue(':CompanyName', $this->getCompanyName());
				$oStmt->bindValue(':EmailContact', $this->getEmailContact());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIUsers
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
	 * @return mofilmSystemAPIUser
	 */
	function reset() {
		$this->_ID = 0;
		$this->_MofilmAPIKeyID = 0;
		$this->_CompanyName = '';
		$this->_EmailContact = '';
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
	 * @return mofilmSystemAPIUser
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
			'_MofilmAPIKeyID' => array(
				'number' => array(),
			),
			'_CompanyName' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_EmailContact' => array(
				'string' => array('min' => 1,'max' => 255,),
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

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmSystemAPIUser
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
	 * {@link mofilmSystemAPIUser::PRIMARY_KEY_SEPARATOR}.
 	 *
	 * @param string $inKey
	 * @return mofilmSystemAPIUser
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
	 * @return mofilmSystemAPIUser
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_MofilmAPIKeyID
	 *
	 * @return integer
 	 */
	function getMofilmAPIKeyID() {
		return $this->_MofilmAPIKeyID;
	}

	/**
	 * Set the object property _MofilmAPIKeyID to $inMofilmAPIKeyID
	 *
	 * @param integer $inMofilmAPIKeyID
	 * @return mofilmSystemAPIUser
	 */
	function setMofilmAPIKeyID($inMofilmAPIKeyID) {
		if ( $inMofilmAPIKeyID !== $this->_MofilmAPIKeyID ) {
			$this->_MofilmAPIKeyID = $inMofilmAPIKeyID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_CompanyName
	 *
	 * @return string
 	 */
	function getCompanyName() {
		return $this->_CompanyName;
	}

	/**
	 * Set the object property _CompanyName to $inCompanyName
	 *
	 * @param string $inCompanyName
	 * @return mofilmSystemAPIUser
	 */
	function setCompanyName($inCompanyName) {
		if ( $inCompanyName !== $this->_CompanyName ) {
			$this->_CompanyName = $inCompanyName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_EmailContact
	 *
	 * @return string
 	 */
	function getEmailContact() {
		return $this->_EmailContact;
	}

	/**
	 * Set the object property _EmailContact to $inEmailContact
	 *
	 * @param string $inEmailContact
	 * @return mofilmSystemAPIUser
	 */
	function setEmailContact($inEmailContact) {
		if ( $inEmailContact !== $this->_EmailContact ) {
			$this->_EmailContact = $inEmailContact;
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
	 * @return mofilmSystemAPIUser
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}