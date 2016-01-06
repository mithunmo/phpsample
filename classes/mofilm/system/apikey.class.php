<?php
/**
 * mofilmSystemAPIKey
 *
 * Stored in mofilmSystemAPIKey.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSystemAPIKey
 * @category mofilmSystemAPIKey
 * @version $Rev: 806 $
 */


/**
 * mofilmSystemAPIKey Class
 *
 * Provides access to records in mofilm_system.mofilmAPIKeys
 *
 * Creating a new record:
 * <code>
 * $oMofilmSystemAPIKey = new mofilmSystemAPIKey();
 * $oMofilmSystemAPIKey->setID($inID);
 * $oMofilmSystemAPIKey->setPublicKey($inPublicKey);
 * $oMofilmSystemAPIKey->setPrivateKey($inPrivateKey);
 * $oMofilmSystemAPIKey->setActive($inActive);
 * $oMofilmSystemAPIKey->setCreateDate($inCreateDate);
 * $oMofilmSystemAPIKey->setUpdateDate($inUpdateDate);
 * $oMofilmSystemAPIKey->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmSystemAPIKey = new mofilmSystemAPIKey($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmSystemAPIKey = new mofilmSystemAPIKey();
 * $oMofilmSystemAPIKey->setID($inID);
 * $oMofilmSystemAPIKey->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmSystemAPIKey = mofilmSystemAPIKey::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmSystemAPIKey
 * @category mofilmSystemAPIKey
 */
class mofilmSystemAPIKey implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmSystemAPIKey
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
	 * Stores $_PublicKey
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_PublicKey;

	/**
	 * Stores $_PrivateKey
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_PrivateKey;

	/**
	 * Stores $_Active
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Active;

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
	 * Returns a new instance of mofilmSystemAPIKey
	 *
	 * @param integer $inID
	 * @return mofilmSystemAPIKey
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
	 * Get an instance of mofilmSystemAPIKey by primary key
	 *
	 * @param integer $inID
	 * @return mofilmSystemAPIKey
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
		$oObject = new mofilmSystemAPIKey();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get instance of mofilmSystemAPIKey by unique key (publicKey)
	 *
	 * @param string $inPublicKey
	 * @return mofilmSystemAPIKey
	 * @static
	 */
	public static function getInstanceByPublicKey($inPublicKey) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inPublicKey]) ) {
			return self::$_Instances[$inPublicKey];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmSystemAPIKey();
		$oObject->setPublicKey($inPublicKey);
		if ( $oObject->load() ) {
			self::$_Instances[$inPublicKey] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmSystemAPIKey
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
			SELECT ID, publicKey, privateKey, active, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIKeys
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmSystemAPIKey();
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
			SELECT ID, publicKey, privateKey, active, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIKeys';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		if ( $this->_PublicKey !== '' ) {
			$where[] = ' publicKey = :PublicKey ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $this->_ID !== 0 ) {
			$oStmt->bindValue(':ID', $this->getID());
		}
		if ( $this->_PublicKey !== '' ) {
			$oStmt->bindValue(':PublicKey', $this->getPublicKey());
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
		$this->setPublicKey($inArray['publicKey']);
		$this->setPrivateKey($inArray['privateKey']);
		$this->setActive((int)$inArray['active']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIKeys
					( ID, publicKey, privateKey, active, createDate, updateDate)
				VALUES
					(:ID, :PublicKey, :PrivateKey, :Active, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					privateKey=VALUES(privateKey),
					active=VALUES(active),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':PublicKey', $this->getPublicKey());
				$oStmt->bindValue(':PrivateKey', $this->getPrivateKey());
				$oStmt->bindValue(':Active', $this->getActive());
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIKeys
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
	 * @return mofilmSystemAPIKey
	 */
	function reset() {
		$this->_ID = 0;
		$this->_PublicKey = '';
		$this->_PrivateKey = '';
		$this->_Active = 0;
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
	 * @return mofilmSystemAPIKey
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
			'_PublicKey' => array(
				'string' => array('min' => 1,'max' => 50,),
			),
			'_PrivateKey' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_Active' => array(
				'number' => array(),
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
	 * @return mofilmSystemAPIKey
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
	 * {@link mofilmSystemAPIKey::PRIMARY_KEY_SEPARATOR}.
 	 *
	 * @param string $inKey
	 * @return mofilmSystemAPIKey
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
	 * @return mofilmSystemAPIKey
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_PublicKey
	 *
	 * @return string
 	 */
	function getPublicKey() {
		return $this->_PublicKey;
	}

	/**
	 * Set the object property _PublicKey to $inPublicKey
	 *
	 * @param string $inPublicKey
	 * @return mofilmSystemAPIKey
	 */
	function setPublicKey($inPublicKey) {
		if ( $inPublicKey !== $this->_PublicKey ) {
			$this->_PublicKey = $inPublicKey;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_PrivateKey
	 *
	 * @return string
 	 */
	function getPrivateKey() {
		return $this->_PrivateKey;
	}

	/**
	 * Set the object property _PrivateKey to $inPrivateKey
	 *
	 * @param string $inPrivateKey
	 * @return mofilmSystemAPIKey
	 */
	function setPrivateKey($inPrivateKey) {
		if ( $inPrivateKey !== $this->_PrivateKey ) {
			$this->_PrivateKey = $inPrivateKey;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Active
	 *
	 * @return integer
 	 */
	function getActive() {
		return $this->_Active;
	}

	/**
	 * Set the object property _Active to $inActive
	 *
	 * @param integer $inActive
	 * @return mofilmSystemAPIKey
	 */
	function setActive($inActive) {
		if ( $inActive !== $this->_Active ) {
			$this->_Active = $inActive;
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
	 * @return mofilmSystemAPIKey
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
	 * @return mofilmSystemAPIKey
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
	 * @return mofilmSystemAPIKey
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}