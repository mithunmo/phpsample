<?php
/**
 * momusicMash
 *
 * Stored in momusicMash.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package momusic
 * @subpackage momusicMash
 * @category momusicMash
 * @version $Rev: 840 $
 */


/**
 * momusicMash Class
 *
 * Provides access to records in momusic_content.mash
 *
 * Creating a new record:
 * <code>
 * $oMomusicMash = new momusicMash();
 * $oMomusicMash->setID($inID);
 * $oMomusicMash->setUserID($inUserID);
 * $oMomusicMash->setXml($inXml);
 * $oMomusicMash->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMomusicMash = new momusicMash($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMomusicMash = new momusicMash();
 * $oMomusicMash->setID($inID);
 * $oMomusicMash->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMomusicMash = momusicMash::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package momusic
 * @subpackage momusicMash
 * @category momusicMash
 */
class momusicMash implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of momusicMash
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
	 * Stores $_Name
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Name;

	/**
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_Xml
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Xml;

	/**
	 * Stores $_Hash
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Hash;

	/**
	 * Stores $_CreateDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_CreateDate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of momusicMash
	 *
	 * @param integer $inID
	 * @return momusicMash
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
	 * Get an instance of momusicMash by primary key
	 *
	 * @param integer $inID
	 * @return momusicMash
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
		$oObject = new momusicMash();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of momusicMash
	 *
	 * @param string $inHash
	 * @return momusicMash1
	 * @static
	 */
	public static function getInstanceByHash($inHash) {
		$key = $inHash;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new momusicMash();
		$oObject->setHash($inHash);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of momusicMash1
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
			SELECT ID, name, userID, xml, hash, createDate
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.mash
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicMash();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}


	/**
	 * Returns an array of objects of momusicMash by userID
	 *
	 * @param integer $inUserID
	 * @return array
	 * @static
	 */
	public static function listOfObjectsByUserID($inUserID, $inOffset = null, $inLimit = 10) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
		 * 
  		 */
		
		$values = array();

		$query = '
			SELECT ID, name, userID, xml, hash, createDate
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.mash';

		$where = array();
		$where[] = ' userID = :UserID ';
		$query .= ' WHERE ' . implode(' AND ', $where);
		
		$query.= ' ORDER BY ID DESC';
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':UserID', $inUserID);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicMash();
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
			SELECT ID, name, userID, xml, hash, createDate
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.mash';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}
		if ( $this->_Hash !== '' ) {
			$where[] = ' hash = :Hash ';
			$values[':Hash'] = $this->getHash();
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
		$this->setName($inArray['name']);
		$this->setUserID((int)$inArray['userID']);
		$this->setXml($inArray['xml']);
		$this->setHash($inArray['hash']);
		$this->setCreateDate($inArray['createDate']);
		$this->setModified(false);
	}

	/**
	 * Saves object to the table
	 *
	 * @return boolean
	 */
	function save() {
		$return = false;
		systemLog::message("coming here");
		if ( $this->isModified() ) {
			
			$message = '';
			if ( !$this->isValid($message) ) {
				systemLog::message("coming here 2");
				throw new momusicException($message);
			}
			
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('momusic_content').'.mash
					( ID, name, userID, xml, hash, createDate )
				VALUES
					( :ID, :Name, :UserID, :Xml, :Hash, :CreateDate )
				ON DUPLICATE KEY UPDATE
					name=VALUES(name),
					userID=VALUES(userID),
					hash=VALUES(hash),
					xml=VALUES(xml),
					createDate=VALUES(createDate)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':Name', $this->getName());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':Xml', $this->getXml());
				$oStmt->bindValue(':Hash', $this->getHash());
				$oStmt->bindValue(':CreateDate', $this->getCreateDate());

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
			DELETE FROM '.system::getConfig()->getDatabase('momusic_content').'.mash
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
	 * @return momusicMash
	 */
	function reset() {
		$this->_ID = 0;
		$this->_UserID = 0;
		$this->_Name = '';
		$this->_Xml = '';
		$this->_Hash = '';
		$this->_CreateDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
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
	 * @return momusicMash
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
			'_UserID' => array(
				'number' => array(),
			),
			'_Xml' => array(
				'string' => array(),
			),
			'_Hash' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_CreateDate' => array(
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

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return momusicMash
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
	 * momusicMash::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return momusicMash
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
	 * @return momusicMash
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
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
	 * @return momusicMash1
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_UserID
	 *
	 * @return integer
 	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set the object property _UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return momusicMash
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Xml
	 *
	 * @return string
 	 */
	function getXml() {
		return $this->_Xml;
	}

	/**
	 * Set the object property _Xml to $inXml
	 *
	 * @param string $inXml
	 * @return momusicMash
	 */
	function setXml($inXml) {
		if ( $inXml !== $this->_Xml ) {
			$this->_Xml = $inXml;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Hash
	 *
	 * @return string
 	 */
	function getHash() {
		return $this->_Hash;
	}

	/**
	 * Set the object property _Hash to $inHash
	 *
	 * @param string $inHash
	 * @return momusicMash1
	 */
	function setHash($inHash) {
		if ( $inHash !== $this->_Hash ) {
			$this->_Hash = $inHash;
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
	 * @return momusicMash1
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
	 * @return momusicMash
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}