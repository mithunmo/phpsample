<?php
/**
 * mofilmNotification
 *
 * Stored in mofilmNotification.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmNotification
 * @category mofilmNotification
 * @version $Rev: 840 $
 */


/**
 * mofilmNotification Class
 *
 * Provides access to records in mofilm_content.userNotification
 *
 * Creating a new record:
 * <code>
 * $oMofilmNotification = new mofilmNotification();
 * $oMofilmNotification->setId($inId);
 * $oMofilmNotification->setSourceID($inSourceID);
 * $oMofilmNotification->setTitle($inTitle);
 * $oMofilmNotification->setStatus($inStatus);
 * $oMofilmNotification->setSent($inSent);
 * $oMofilmNotification->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmNotification = new mofilmNotification($inId);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmNotification = new mofilmNotification();
 * $oMofilmNotification->setId($inId);
 * $oMofilmNotification->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmNotification = mofilmNotification::getInstance($inId);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmNotification
 * @category mofilmNotification
 */
class mofilmNotification implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmNotification
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
	 * Stores $_SourceID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SourceID;

	/**
	 * Stores $_Title
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Title;

	/**
	 * Stores $_Status
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Status;

	/**
	 * Stores $_Sent
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_Sent;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmNotification
	 *
	 * @param integer $inId
	 * @return mofilmNotification
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
	 * Get an instance of mofilmNotification by primary key
	 *
	 * @param integer $inId
	 * @return mofilmNotification
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
		$oObject = new mofilmNotification();
		$oObject->setId($inId);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmNotification
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
			SELECT id, sourceID, title, status, sent
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userNotification
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmNotification();
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
			SELECT id, sourceID, title, status, sent
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userNotification';

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
		$this->setSourceID((int)$inArray['sourceID']);
		$this->setTitle($inArray['title']);
		$this->setStatus((int)$inArray['status']);
		$this->setSent($inArray['sent']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userNotification
					( id, sourceID, title, status, sent )
				VALUES
					( :Id, :SourceID, :Title, :Status, :Sent )
				ON DUPLICATE KEY UPDATE
					sourceID=VALUES(sourceID),
					title=VALUES(title),
					status=VALUES(status),
					sent=VALUES(sent)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':Id', $this->getId());
				$oStmt->bindValue(':SourceID', $this->getSourceID());
				$oStmt->bindValue(':Title', $this->getTitle());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':Sent', $this->getSent());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userNotification
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
	 * @return mofilmNotification
	 */
	function reset() {
		$this->_Id = 0;
		$this->_SourceID = 0;
		$this->_Title = '';
		$this->_Status = 0;
		$this->_Sent = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
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
	 * @return mofilmNotification
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
			'_SourceID' => array(
				'number' => array(),
			),
			'_Title' => array(
				'string' => array(),
			),
			'_Status' => array(
				'number' => array(),
			),
			'_Sent' => array(
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
	 * @return mofilmNotification
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
	 * mofilmNotification::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmNotification
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
	 * @return mofilmNotification
	 */
	function setId($inId) {
		if ( $inId !== $this->_Id ) {
			$this->_Id = $inId;
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
	 * @return mofilmNotification
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Title
	 *
	 * @return string
 	 */
	function getTitle() {
		return $this->_Title;
	}

	/**
	 * Set the object property _Title to $inTitle
	 *
	 * @param string $inTitle
	 * @return mofilmNotification
	 */
	function setTitle($inTitle) {
		if ( $inTitle !== $this->_Title ) {
			$this->_Title = $inTitle;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Status
	 *
	 * @return integer
 	 */
	function getStatus() {
		return $this->_Status;
	}

	/**
	 * Set the object property _Status to $inStatus
	 *
	 * @param integer $inStatus
	 * @return mofilmNotification
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Sent
	 *
	 * @return systemDateTime
 	 */
	function getSent() {
		return $this->_Sent;
	}

	/**
	 * Set the object property _Sent to $inSent
	 *
	 * @param systemDateTime $inSent
	 * @return mofilmNotification
	 */
	function setSent($inSent) {
		if ( $inSent !== $this->_Sent ) {
			if ( !$inSent instanceof DateTime ) {
				$inSent = new systemDateTime($inSent, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_Sent = $inSent;
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
	 * @return mofilmNotification
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}