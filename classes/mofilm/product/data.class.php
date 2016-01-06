<?php
/**
 * mofilmProductData
 *
 * Stored in mofilmProductData.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmProductData
 * @category mofilmProductData
 * @version $Rev: 840 $
 */


/**
 * mofilmProductData Class
 *
 * Provides access to records in mofilm_content.productData
 *
 * Creating a new record:
 * <code>
 * $oMofilmProductDatum = new mofilmProductData();
 * $oMofilmProductDatum->setId($inId);
 * $oMofilmProductDatum->setProductID($inProductID);
 * $oMofilmProductDatum->setProjectID($inProjectID);
 * $oMofilmProductDatum->setEventID($inEventID);
 * $oMofilmProductDatum->setSourceID($inSourceID);
 * $oMofilmProductDatum->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmProductDatum = new mofilmProductData($inId);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmProductDatum = new mofilmProductData();
 * $oMofilmProductDatum->setId($inId);
 * $oMofilmProductDatum->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmProductDatum = mofilmProductData::getInstance($inId);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmProductData
 * @category mofilmProductData
 */
class mofilmProductData implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmProductData
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
	 * Stores $_Id
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Id;

	/**
	 * Stores $_ProductID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ProductID;

	/**
	 * Stores $_ProjectID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ProjectID;

	/**
	 * Stores $_EventID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_EventID;

	/**
	 * Stores $_SourceID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SourceID;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmProductData
	 *
	 * @param integer $inId
	 * @return mofilmProductData
	 */
	function __construct($inId = null) {
		$this->reset();
		if ( $inId !== null ) {
			$this->setId($inId);
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
	 * Get an instance of mofilmProductData by primary key
	 *
	 * @param integer $inId
	 * @return mofilmProductData
	 * @static
	 */
	public static function getInstance($inId) {
		$key = $inId;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmProductData();
		$oObject->setId($inId);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmProductData
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
			SELECT id, productID, projectID, eventID, sourceID
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.productData
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmProductData();
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
			SELECT id, productID, projectID, eventID, sourceID
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.productData';

		$where = array();
		if ( $this->_Id !== 0 ) {
			$where[] = ' id = :Id ';
			$values[':Id'] = $this->getId();
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
		$this->setId((int)$inArray['id']);
		$this->setProductID((int)$inArray['productID']);
		$this->setProjectID((int)$inArray['projectID']);
		$this->setEventID((int)$inArray['eventID']);
		$this->setSourceID((int)$inArray['sourceID']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.productData
					( id, productID, projectID, eventID, sourceID )
				VALUES
					( :Id, :ProductID, :ProjectID, :EventID, :SourceID )
				ON DUPLICATE KEY UPDATE
					productID=VALUES(productID),
					projectID=VALUES(projectID),
					eventID=VALUES(eventID),
					sourceID=VALUES(sourceID)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':Id', $this->getId());
				$oStmt->bindValue(':ProductID', $this->getProductID());
				$oStmt->bindValue(':ProjectID', $this->getProjectID());
				$oStmt->bindValue(':EventID', $this->getEventID());
				$oStmt->bindValue(':SourceID', $this->getSourceID());

				if ( $oStmt->execute() ) {
					if ( !$this->getId() ) {
						$this->setId($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.productData
			WHERE
				id = :Id
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Id', $this->getId());

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
	 * @return mofilmProductData
	 */
	function reset() {
		$this->_Id = 0;
		$this->_ProductID = 0;
		$this->_ProjectID = 0;
		$this->_EventID = 0;
		$this->_SourceID = 0;
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
	 * @return mofilmProductData
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
			'_Id' => array(
				'number' => array(),
			),
			'_ProductID' => array(
				'number' => array(),
			),
			'_ProjectID' => array(
				'number' => array(),
			),
			'_EventID' => array(
				'number' => array(),
			),
			'_SourceID' => array(
				'number' => array(),
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
	 * @return mofilmProductData
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
		return $this->_Id;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * mofilmProductData::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmProductData
  	 */
	function setPrimaryKey($inKey) {
		list($id) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setId($id);
	}

	/**
	 * Return the current value of the property $_Id
	 *
	 * @return integer
 	 */
	function getId() {
		return $this->_Id;
	}

	/**
	 * Set the object property _Id to $inId
	 *
	 * @param integer $inId
	 * @return mofilmProductData
	 */
	function setId($inId) {
		if ( $inId !== $this->_Id ) {
			$this->_Id = $inId;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ProductID
	 *
	 * @return integer
 	 */
	function getProductID() {
		return $this->_ProductID;
	}

	/**
	 * Set the object property _ProductID to $inProductID
	 *
	 * @param integer $inProductID
	 * @return mofilmProductData
	 */
	function setProductID($inProductID) {
		if ( $inProductID !== $this->_ProductID ) {
			$this->_ProductID = $inProductID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ProjectID
	 *
	 * @return integer
 	 */
	function getProjectID() {
		return $this->_ProjectID;
	}

	/**
	 * Set the object property _ProjectID to $inProjectID
	 *
	 * @param integer $inProjectID
	 * @return mofilmProductData
	 */
	function setProjectID($inProjectID) {
		if ( $inProjectID !== $this->_ProjectID ) {
			$this->_ProjectID = $inProjectID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_EventID
	 *
	 * @return integer
 	 */
	function getEventID() {
		return $this->_EventID;
	}

	/**
	 * Set the object property _EventID to $inEventID
	 *
	 * @param integer $inEventID
	 * @return mofilmProductData
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
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
	 * @return mofilmProductData
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
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
	 * @return mofilmProductData
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}