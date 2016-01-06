<?php
/**
 * mofilmSystemHelpTags
 *
 * Stored in mofilmSystemHelpTags.class.php
 *
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSystemHelpTags
 * @category mofilmSystemHelpTags
 * @version $Rev: 806 $
 */


/**
 * mofilmSystemHelpTags Class
 *
 * Provides access to records in mofilm_system.helpTags
 *
 * Creating a new record:
 * <code>
 * $oMofilmSystemHelpTag = new mofilmSystemHelpTags();
 * $oMofilmSystemHelpTag->setID($inID);
 * $oMofilmSystemHelpTag->setTag($inTag);
 * $oMofilmSystemHelpTag->setCreatedDate($inCreatedDate);
 * $oMofilmSystemHelpTag->setUpdatedDate($inUpdatedDate);
 * $oMofilmSystemHelpTag->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmSystemHelpTag = new mofilmSystemHelpTags($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmSystemHelpTag = new mofilmSystemHelpTags();
 * $oMofilmSystemHelpTag->setID($inID);
 * $oMofilmSystemHelpTag->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmSystemHelpTag = mofilmSystemHelpTags::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmSystemHelpTags
 * @category mofilmSystemHelpTags
 */
class mofilmSystemHelpTags implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmSystemHelpTags
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
	 * Stores $_Tag
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Tag;

	/**
	 * Stores $_CreatedDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_CreatedDate;

	/**
	 * Stores $_UpdatedDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_UpdatedDate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmSystemHelpTags
	 *
	 * @param integer $inID
	 * @return mofilmSystemHelpTags
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
	 * Get an instance of mofilmSystemHelpTags by primary key
	 *
	 * @param integer $inID
	 * @return mofilmSystemHelpTags
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
		$oObject = new mofilmSystemHelpTags();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get instance of mofilmSystemHelpTags by unique key (tag)
	 *
	 * @param string $inTag
	 * @return mofilmSystemHelpTags
	 * @static
	 */
	public static function getInstanceByTag($inTag) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inTag]) ) {
			return self::$_Instances[$inTag];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmSystemHelpTags();
		$oObject->setTag($inTag);
		if ( $oObject->load() ) {
			self::$_Instances[$inTag] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmSystemHelpTags
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
			SELECT ID, tag, createdDate, updatedDate
			  FROM '.system::getConfig()->getDatabase('mofilm_system').'.helpTags
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmSystemHelpTags();
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
			SELECT ID, tag, createdDate, updatedDate
			  FROM '.system::getConfig()->getDatabase('mofilm_system').'.helpTags';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		if ( $this->_Tag !== '' ) {
			$where[] = ' tag = :Tag ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $this->_ID !== 0 ) {
			$oStmt->bindValue(':ID', $this->getID());
		}
		if ( $this->_Tag !== '' ) {
			$oStmt->bindValue(':Tag', $this->getTag());
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
		$this->setTag($inArray['tag']);
		$this->setCreatedDate($inArray['createdDate']);
		$this->setUpdatedDate($inArray['updatedDate']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_system').'.helpTags
					( ID, tag, createdDate, updatedDate)
				VALUES
					(:ID, :Tag, :CreatedDate, :UpdatedDate)
				ON DUPLICATE KEY UPDATE
					createdDate=VALUES(createdDate),
					updatedDate=VALUES(updatedDate)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':Tag', $this->getTag());
				$oStmt->bindValue(':CreatedDate', $this->getCreatedDate());
				$oStmt->bindValue(':UpdatedDate', $this->getUpdatedDate());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_system').'.helpTags
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
	 * @return mofilmSystemHelpTags
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Tag = '';
		$this->_CreatedDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_UpdatedDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
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
	 * @return mofilmSystemHelpTags
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
			'_Tag' => array(
				'string' => array('min' => 1,'max' => 50,),
			),
			'_CreatedDate' => array(
				'dateTime' => array(),
			),
			'_UpdatedDate' => array(
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
	 * @return mofilmSystemHelpTags
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
	 * {@link mofilmSystemHelpTags::PRIMARY_KEY_SEPARATOR}.
 	 *
	 * @param string $inKey
	 * @return mofilmSystemHelpTags
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
	 * @return mofilmSystemHelpTags
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Tag
	 *
	 * @return string
 	 */
	function getTag() {
		return $this->_Tag;
	}

	/**
	 * Set the object property _Tag to $inTag
	 *
	 * @param string $inTag
	 * @return mofilmSystemHelpTags
	 */
	function setTag($inTag) {
		if ( $inTag !== $this->_Tag ) {
			$this->_Tag = $inTag;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_CreatedDate
	 *
	 * @return systemDateTime
 	 */
	function getCreatedDate() {
		return $this->_CreatedDate;
	}

	/**
	 * Set the object property _CreatedDate to $inCreatedDate
	 *
	 * @param systemDateTime $inCreatedDate
	 * @return mofilmSystemHelpTags
	 */
	function setCreatedDate($inCreatedDate) {
		if ( $inCreatedDate !== $this->_CreatedDate ) {
			if ( !$inCreatedDate instanceof DateTime ) {
				$inCreatedDate = new systemDateTime($inCreatedDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_CreatedDate = $inCreatedDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_UpdatedDate
	 *
	 * @return systemDateTime
 	 */
	function getUpdatedDate() {
		return $this->_UpdatedDate;
	}

	/**
	 * Set the object property _UpdatedDate to $inUpdatedDate
	 *
	 * @param systemDateTime $inUpdatedDate
	 * @return mofilmSystemHelpTags
	 */
	function setUpdatedDate($inUpdatedDate) {
		if ( $inUpdatedDate !== $this->_UpdatedDate ) {
			if ( !$inUpdatedDate instanceof DateTime ) {
				$inUpdatedDate = new systemDateTime($inUpdatedDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_UpdatedDate = $inUpdatedDate;
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
	 * @return mofilmSystemHelpTags
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}