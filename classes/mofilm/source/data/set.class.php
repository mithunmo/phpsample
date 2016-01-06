<?php
/**
 * mofilmSourceDataSet
 *
 * Stored in mofilmSourceDataSet.class.php
 *
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSourceDataSet
 * @category mofilmSourceDataSet
 * @version $Rev: 840 $
 */


/**
 * mofilmSourceDataSet Class
 *
 * Provides access to records in mofilm_content.sourcesData
 *
 * Creating a new record:
 * <code>
 * $oMofilmSourceDataSet = new mofilmSourceDataSet();
 * $oMofilmSourceDataSet->setID($inID);
 * $oMofilmSourceDataSet->setSourceID($inSourceID);
 * $oMofilmSourceDataSet->setName($inName);
 * $oMofilmSourceDataSet->setDescription($inDescription);
 * $oMofilmSourceDataSet->setTerms($inTerms);
 * $oMofilmSourceDataSet->setHash($inHash);
 * $oMofilmSourceDataSet->setLang($inLang);
 * $oMofilmSourceDataSet->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmSourceDataSet = new mofilmSourceDataSet($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmSourceDataSet = new mofilmSourceDataSet();
 * $oMofilmSourceDataSet->setID($inID);
 * $oMofilmSourceDataSet->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmSourceDataSet = mofilmSourceDataSet::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmSourceDataSet
 * @category mofilmSourceDataSet
 */
class mofilmSourceDataSet implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmSourceDataSet
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
	 * Stores $_SourceID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SourceID;

	/**
	 * Stores $_Name
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Name;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;
	
	/**
	 * Stores $_Terms
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Terms;
	
	/**
	 * Stores $_hash
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Hash;

	/**
	 * Stores $_Lang
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Lang;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmSourceDataSet
	 *
	 * @param integer $inID
	 * @return mofilmSourceDataSet
	 */
	function __construct($inSourceID = null, $inLang = null) {
		$this->reset();
		if ( $inSourceID !== 0 && $inLang !== null ) {
			$this->setSourceID($inSourceID);
			$this->setLang($inLang);
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
	 * Get an instance of mofilmSourceDataSet by primary key
	 *
	 * @param integer $inID
	 * @return mofilmSourceDataSet
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
		$oObject = new mofilmSourceDataSet();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmSourceDataSet by SourceID
	 *
	 * @param integer $inSourceID
	 * @return mofilmSourceDataSet
	 * @static
	 */
	public static function getInstanceBySourceID($inSourceID) {
		$key = $inSourceID;
		
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmSourceDataSet();
		$oObject->setSourceID($inSourceID);
		$oObject->setLang('en');
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmSourceDataSet
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
			SELECT ID, sourceID, name, description, terms, hash, lang
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourcesData
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmSourceDataSet();
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
			SELECT ID, sourceID, name, description, terms, hash, lang
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourcesData';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}
		
		if ( $this->_SourceID !== 0 ) {
			$where[] = ' sourceID = :sourceID ';
			$values[':sourceID'] = $this->getSourceID();
		}
		
		if ( isset ($this->_Lang) ) {
			$where[] = ' lang = :lang ';
			$values[':lang'] = $this->getLang();
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
		$this->setSourceID((int)$inArray['sourceID']);
		$this->setName($inArray['name']);
		$this->setDescription($inArray['description']);
		$this->setTerms($inArray['terms']);
		$this->setHash($inArray['hash']);
		$this->setLang($inArray['lang']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.sourcesData
					( ID, sourceID, name, description, terms, hash, lang )
				VALUES
					( :ID, :SourceID, :Name, :Description, :Terms, :Hash, :Lang )
				ON DUPLICATE KEY UPDATE
					sourceID=VALUES(sourceID),
					name=VALUES(name),
					description=VALUES(description),
					terms=VALUES(terms),
					hash=VALUES(hash),
					lang=VALUES(lang)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':SourceID', $this->getSourceID());
				$oStmt->bindValue(':Name', $this->getName());
				$oStmt->bindValue(':Description', $this->getDescription());
				$oStmt->bindValue(':Terms', $this->getTerms());
				$oStmt->bindValue(':Hash', $this->getHash());
				$oStmt->bindValue(':Lang', $this->getLang());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourcesData
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
	 * @return mofilmSourceDataSet
	 */
	function reset() {
		$this->_ID = 0;
		$this->_SourceID = 0;
		$this->_Name = '';
		$this->_Description = '';
		$this->_Terms = '';
		$this->_Hash = '';
		$this->_Lang = '';
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
	 * @return mofilmSourceDataSet
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
			'_SourceID' => array(
				'number' => array(),
			),
			'_Name' => array(
				'string' => array('min' => 1,'max' => 225,),
			),
			'_Description' => array(
				'string' => array(),
			),
			'_Terms' => array(
				'string' => array(),
			),
			'_Lang' => array(
				'string' => array('min' => 1,'max' => 5,),
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
	 * @return mofilmSourceDataSet
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
	 * mofilmSourceDataSet::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmSourceDataSet
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
	 * @return mofilmSourceDataSet
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SourceID
	 *
	 * @return integer
 	 */
	function getSourceID() {
		return $this->_SourceID;
	}

	/**
	 * Set the object property _SourceID to $inSourceID
	 *
	 * @param integer $inSourceID
	 * @return mofilmSourceDataSet
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
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
	 * @return mofilmSourceDataSet
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
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
	 * @return mofilmSourceDataSet
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return the current value of the property $_Terms
	 *
	 * @return string
 	 */
	function getTerms() {
		return $this->_Terms;
	}

	/**
	 * Set the object property _Terms to $inTerms
	 *
	 * @param string $inTerms
	 * @return mofilmSourceDataSet
	 */
	function setTerms($inTerms) {
		if ( $inTerms !== $this->_Terms ) {
			$this->_Terms = $inTerms;
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
	 * @return mofilmSourceDataSet
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
	 * Return the current value of the property $_Lang
	 *
	 * @return string
 	 */
	function getLang() {
		return $this->_Lang;
	}

	/**
	 * Set the object property _Lang to $inLang
	 *
	 * @param string $inLang
	 * @return mofilmSourceDataSet
	 */
	function setLang($inLang) {
		if ( $inLang !== $this->_Lang ) {
			$this->_Lang = $inLang;
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
	 * @return mofilmSourceDataSet
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}