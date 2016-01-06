<?php
/**
 * momusicTags
 *
 * Stored in momusicTags.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package momusic
 * @subpackage momusicTags
 * @category momusicTags
 * @version $Rev: 840 $
 */


/**
 * momusicTags Class
 *
 * Provides access to records in momusic_content.musicTags
 *
 * Creating a new record:
 * <code>
 * $oMomusicTag = new momusicTags();
 * $oMomusicTag->setWorksID($inWorksID);
 * $oMomusicTag->setTagID($inTagID);
 * $oMomusicTag->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMomusicTag = new momusicTags($inWorksID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMomusicTag = new momusicTags();
 * $oMomusicTag->setWorksID($inWorksID);
 * $oMomusicTag->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMomusicTag = momusicTags::getInstance($inWorksID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package momusic
 * @subpackage momusicTags
 * @category momusicTags
 */
class momusicTags implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of momusicTags
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
	 * Stores $_WorksID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_WorksID;

	/**
	 * Stores $_TagID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_TagID;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of momusicTags
	 *
	 * @param integer $inWorksID
	 * @return momusicTags
	 */
	function __construct($inWorksID = null) {
		$this->reset();
		if ( $inWorksID !== null ) {
			$this->setWorksID($inWorksID);
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
	 * Get an instance of momusicTags by primary key
	 *
	 * @param integer $inWorksID
	 * @return momusicTags
	 * @static
	 */
	public static function getInstance($inWorksID) {
		$key = $inWorksID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new momusicTags();
		$oObject->setWorksID($inWorksID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of momusicTags
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
			SELECT worksID, tagID
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicTags
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicTags();
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
			SELECT worksID, tagID
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicTags';

		$where = array();
		if ( $this->_WorksID !== 0 ) {
			$where[] = ' worksID = :WorksID ';
			$values[':WorksID'] = $this->getWorksID();
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
		$this->setWorksID((int)$inArray['worksID']);
		$this->setTagID((int)$inArray['tagID']);
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
				throw new momusicException($message);
			}

			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('momusic_content').'.musicTags
					( worksID, tagID )
				VALUES
					( :WorksID, :TagID )
				ON DUPLICATE KEY UPDATE
					tagID=VALUES(tagID)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':WorksID', $this->getWorksID());
				$oStmt->bindValue(':TagID', $this->getTagID());

				if ( $oStmt->execute() ) {
					if ( !$this->getWorksID() ) {
						$this->setWorksID($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('momusic_content').'.musicTags
			WHERE
				worksID = :WorksID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':WorksID', $this->getWorksID());

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
	 * @return momusicTags
	 */
	function reset() {
		$this->_WorksID = 0;
		$this->_TagID = 0;
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
	 * @return momusicTags
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
			'_WorksID' => array(
				'number' => array(),
			),
			'_TagID' => array(
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
	 * @return momusicTags
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
		return $this->_WorksID;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * momusicTags::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return momusicTags
  	 */
	function setPrimaryKey($inKey) {
		list($worksID) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setWorksID($worksID);
	}

	/**
	 * Return the current value of the property $_WorksID
	 *
	 * @return integer
 	 */
	function getWorksID() {
		return $this->_WorksID;
	}

	/**
	 * Set the object property _WorksID to $inWorksID
	 *
	 * @param integer $inWorksID
	 * @return momusicTags
	 */
	function setWorksID($inWorksID) {
		if ( $inWorksID !== $this->_WorksID ) {
			$this->_WorksID = $inWorksID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_TagID
	 *
	 * @return integer
 	 */
	function getTagID() {
		return $this->_TagID;
	}

	/**
	 * Set the object property _TagID to $inTagID
	 *
	 * @param integer $inTagID
	 * @return momusicTags
	 */
	function setTagID($inTagID) {
		if ( $inTagID !== $this->_TagID ) {
			$this->_TagID = $inTagID;
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
	 * @return momusicTags
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}