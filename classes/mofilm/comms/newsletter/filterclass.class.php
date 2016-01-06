<?php
/**
 * mofilmCommsNewsletterFilterclass
 *
 * Stored in mofilmCommsNewsletterFilterclass.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterFilterclass
 * @category mofilmCommsNewsletterFilterclass
 * @version $Rev: 838 $
 */


/**
 * mofilmCommsNewsletterFilterclass Class
 *
 * Provides access to records in mofilm_comms.newsletterFilterclass
 *
 * Creating a new record:
 * <code>
 * $oMofilmCommsNewsletterFilterclas = new mofilmCommsNewsletterFilterclass();
 * $oMofilmCommsNewsletterFilterclas->setID($inID);
 * $oMofilmCommsNewsletterFilterclas->setDescription($inDescription);
 * $oMofilmCommsNewsletterFilterclas->setClassname($inClassname);
 * $oMofilmCommsNewsletterFilterclas->setDefaultParams($inDefaultParams);
 * $oMofilmCommsNewsletterFilterclas->setCreateDate($inCreateDate);
 * $oMofilmCommsNewsletterFilterclas->setUpdateDate($inUpdateDate);
 * $oMofilmCommsNewsletterFilterclas->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCommsNewsletterFilterclas = new mofilmCommsNewsletterFilterclass($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmCommsNewsletterFilterclas = new mofilmCommsNewsletterFilterclass();
 * $oMofilmCommsNewsletterFilterclas->setID($inID);
 * $oMofilmCommsNewsletterFilterclas->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmCommsNewsletterFilterclas = mofilmCommsNewsletterFilterclass::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmCommsNewsletterFilterclass
 * @category mofilmCommsNewsletterFilterclass
 */
class mofilmCommsNewsletterFilterclass implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmCommsNewsletterFilterclass
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
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_Classname
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Classname;

	/**
	 * Stores $_DefaultParams
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_DefaultParams;

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
	 * Returns a new instance of mofilmCommsNewsletterFilterclass
	 *
	 * @param integer $inID
	 * @return mofilmCommsNewsletterFilterclass
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
	 * Get an instance of mofilmCommsNewsletterFilterclass by primary key
	 *
	 * @param integer $inID
	 * @return mofilmCommsNewsletterFilterclass
	 * @static
	 */
	public static function getInstance($inID) {
		$key = $inID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmCommsNewsletterFilterclass();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmCommsNewsletterFilterclass
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
			SELECT ID, description, classname, defaultParams, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterFilterclass
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmCommsNewsletterFilterclass();
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
		$values = array();

		$query = '
			SELECT ID, description, classname, defaultParams, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterFilterclass';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);

		$this->reset();
		if ( $oStmt->execute($values) ) {
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
		$this->setDescription($inArray['description']);
		$this->setClassname($inArray['classname']);
		$this->setDefaultParams($inArray['defaultParams']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterFilterclass
					( ID, description, classname, defaultParams, createDate, updateDate )
				VALUES
					( :ID, :Description, :Classname, :DefaultParams, :CreateDate, :UpdateDate )
				ON DUPLICATE KEY UPDATE
					description=VALUES(description),
					classname=VALUES(classname),
					defaultParams=VALUES(defaultParams),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':Description', $this->getDescription());
				$oStmt->bindValue(':Classname', $this->getClassname());
				$oStmt->bindValue(':DefaultParams', $this->getDefaultParams());
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterFilterclass
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
	 * @return mofilmCommsNewsletterFilterclass
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Description = '';
		$this->_Classname = '';
		$this->_DefaultParams = '';
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
	 * @return mofilmCommsNewsletterFilterclass
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
			'_Description' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_Classname' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_DefaultParams' => array(
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
	 * @return mofilmCommsNewsletterFilterclass
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
	 * mofilmCommsNewsletterFilterclass::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmCommsNewsletterFilterclass
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
	 * @return mofilmCommsNewsletterFilterclass
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Description
	 *
	 * @return string
 	 */
	function getDescription() {
		return $this->_Description;
	}

	/**
	 * Set the object property _Description to $inDescription
	 *
	 * @param string $inDescription
	 * @return mofilmCommsNewsletterFilterclass
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Classname
	 *
	 * @return string
 	 */
	function getClassname() {
		return $this->_Classname;
	}

	/**
	 * Set the object property _Classname to $inClassname
	 *
	 * @param string $inClassname
	 * @return mofilmCommsNewsletterFilterclass
	 */
	function setClassname($inClassname) {
		if ( $inClassname !== $this->_Classname ) {
			$this->_Classname = $inClassname;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_DefaultParams
	 *
	 * @return string
 	 */
	function getDefaultParams() {
		return $this->_DefaultParams;
	}

	/**
	 * Set the object property _DefaultParams to $inDefaultParams
	 *
	 * @param string $inDefaultParams
	 * @return mofilmCommsNewsletterFilterclass
	 */
	function setDefaultParams($inDefaultParams) {
		if ( $inDefaultParams !== $this->_DefaultParams ) {
			$this->_DefaultParams = $inDefaultParams;
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
	 * @return mofilmCommsNewsletterFilterclass
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
	 * @return mofilmCommsNewsletterFilterclass
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
	 * @return mofilmCommsNewsletterFilterclass
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}