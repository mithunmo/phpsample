<?php
/**
 * mofilmChannel
 *
 * Stored in mofilmChannel.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmChannel
 * @category mofilmChannel
 * @version $Rev: 840 $
 */


/**
 * mofilmChannel Class
 *
 * Provides access to records in mofilm_content.channels
 *
 * Creating a new record:
 * <code>
 * $oMofilmChannel = new mofilmChannel();
 * $oMofilmChannel->setID($inID);
 * $oMofilmChannel->setLink($inLink);
 * $oMofilmChannel->setDescription($inDescription);
 * $oMofilmChannel->setUpdateDate($inUpdateDate);
 * $oMofilmChannel->setName($inName);
 * $oMofilmChannel->setCategory($inCategory);
 * $oMofilmChannel->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmChannel = new mofilmChannel($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmChannel = new mofilmChannel();
 * $oMofilmChannel->setID($inID);
 * $oMofilmChannel->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmChannel = mofilmChannel::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmChannel
 * @category mofilmChannel
 */
class mofilmChannel implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmChannel
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
	 * Stores $_Link
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Link;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_UpdateDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_UpdateDate;

	/**
	 * Stores $_Name
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Name;

	/**
	 * Stores $_Category
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Category;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmChannel
	 *
	 * @param integer $inID
	 * @return mofilmChannel
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
	 * Get an instance of mofilmChannel by primary key
	 *
	 * @param integer $inID
	 * @return mofilmChannel
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
		$oObject = new mofilmChannel();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmChannel
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
			SELECT ID, link, description, updateDate, name, category
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.channels
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmChannel();
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
			SELECT ID, link, description, updateDate, name, category
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.channels';

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
		$this->setLink($inArray['link']);
		$this->setDescription($inArray['description']);
		$this->setUpdateDate($inArray['updateDate']);
		$this->setName($inArray['name']);
		$this->setCategory($inArray['category']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.channels
					( ID, link, description, updateDate, name, category )
				VALUES
					( :ID, :Link, :Description, :UpdateDate, :Name, :Category )
				ON DUPLICATE KEY UPDATE
					link=VALUES(link),
					description=VALUES(description),
					updateDate=VALUES(updateDate),
					name=VALUES(name),
					category=VALUES(category)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':Link', $this->getLink());
				$oStmt->bindValue(':Description', $this->getDescription());
				$oStmt->bindValue(':UpdateDate', $this->getUpdateDate());
				$oStmt->bindValue(':Name', $this->getName());
				$oStmt->bindValue(':Category', $this->getCategory());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.channels
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
	 * @return mofilmChannel
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Link = '';
		$this->_Description = '';
		$this->_UpdateDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Name = '';
		$this->_Category = '';
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
	 * @return mofilmChannel
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
			'_Link' => array(
				'string' => array(),
			),
			'_Description' => array(
				'string' => array(),
			),
			'_UpdateDate' => array(
				'dateTime' => array(),
			),
			'_Name' => array(
				'string' => array('min' => 1,'max' => 32,),
			),
			'_Category' => array(
				'string' => array(),
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
	 * @return mofilmChannel
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
	 * mofilmChannel::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmChannel
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
	 * @return mofilmChannel
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Link
	 *
	 * @return string
 	 */
	function getLink() {
		return $this->_Link;
	}

	/**
	 * Set the object property _Link to $inLink
	 *
	 * @param string $inLink
	 * @return mofilmChannel
	 */
	function setLink($inLink) {
		if ( $inLink !== $this->_Link ) {
			$this->_Link = $inLink;
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
	 * @return mofilmChannel
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
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
	 * @return mofilmChannel
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
	 * @return mofilmChannel
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Category
	 *
	 * @return string
 	 */
	function getCategory() {
		return $this->_Category;
	}

	/**
	 * Set the object property _Category to $inCategory
	 *
	 * @param string $inCategory
	 * @return mofilmChannel
	 */
	function setCategory($inCategory) {
		if ( $inCategory !== $this->_Category ) {
			$this->_Category = $inCategory;
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
	 * @return mofilmChannel
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}