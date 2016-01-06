<?php
/**
 * momusicMusic
 *
 * Stored in momusicMusic.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package momusic
 * @subpackage momusicMusic
 * @category momusicMusic
 * @version $Rev: 840 $
 */


/**
 * momusicMusic Class
 *
 * Provides access to records in momusic_content.music
 *
 * Creating a new record:
 * <code>
 * $oMomusicMusic = new momusicMusic();
 * $oMomusicMusic->setID($inID);
 * $oMomusicMusic->setName($inName);
 * $oMomusicMusic->setDescription($inDescription);
 * $oMomusicMusic->setUserID($inUserID);
 * $oMomusicMusic->setDuration($inDuration);
 * $oMomusicMusic->setDateUploaded($inDateUploaded);
 * $oMomusicMusic->setRating($inRating);
 * $oMomusicMusic->setPath($inPath);
 * $oMomusicMusic->setStatus($inStatus);
 * $oMomusicMusic->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMomusicMusic = new momusicMusic($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMomusicMusic = new momusicMusic();
 * $oMomusicMusic->setID($inID);
 * $oMomusicMusic->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMomusicMusic = momusicMusic::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package momusic
 * @subpackage momusicMusic
 * @category momusicMusic
 */
class momusicMusic implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of momusicMusic
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
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_Duration
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Duration;

	/**
	 * Stores $_DateUploaded
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_DateUploaded;

	/**
	 * Stores $_Rating
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Rating;

	/**
	 * Stores $_Path
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Path;

	/**
	 * Stores $_Status
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Status;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of momusicMusic
	 *
	 * @param integer $inID
	 * @return momusicMusic
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
	 * Get an instance of momusicMusic by primary key
	 *
	 * @param integer $inID
	 * @return momusicMusic
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
		$oObject = new momusicMusic();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of momusicMusic
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
			SELECT ID, name, description, userID, duration, dateUploaded, rating, path, status
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.music
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicMusic();
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
			SELECT ID, name, description, userID, duration, dateUploaded, rating, path, status
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.music';

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
		$this->setName($inArray['name']);
		$this->setDescription($inArray['description']);
		$this->setUserID((int)$inArray['userID']);
		$this->setDuration((int)$inArray['duration']);
		$this->setDateUploaded($inArray['dateUploaded']);
		$this->setRating((int)$inArray['rating']);
		$this->setPath((int)$inArray['path']);
		$this->setStatus((int)$inArray['status']);
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
				INSERT INTO '.system::getConfig()->getDatabase('momusic_content').'.music
					( ID, name, description, userID, duration, dateUploaded, rating, path, status )
				VALUES
					( :ID, :Name, :Description, :UserID, :Duration, :DateUploaded, :Rating, :Path, :Status )
				ON DUPLICATE KEY UPDATE
					name=VALUES(name),
					description=VALUES(description),
					userID=VALUES(userID),
					duration=VALUES(duration),
					dateUploaded=VALUES(dateUploaded),
					rating=VALUES(rating),
					path=VALUES(path),
					status=VALUES(status)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':Name', $this->getName());
				$oStmt->bindValue(':Description', $this->getDescription());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':Duration', $this->getDuration());
				$oStmt->bindValue(':DateUploaded', $this->getDateUploaded());
				$oStmt->bindValue(':Rating', $this->getRating());
				$oStmt->bindValue(':Path', $this->getPath());
				$oStmt->bindValue(':Status', $this->getStatus());

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
			DELETE FROM '.system::getConfig()->getDatabase('momusic_content').'.music
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
	 * @return momusicMusic
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Name = '';
		$this->_Description = '';
		$this->_UserID = 0;
		$this->_Duration = 0;
		$this->_DateUploaded = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Rating = 0;
		$this->_Path = '';
		$this->_Status = 0;
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
	 * @return momusicMusic
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
			'_Name' => array(
				'string' => array(),
			),
			'_Description' => array(
				'string' => array(),
			),
			'_UserID' => array(
				'number' => array(),
			),
			'_Duration' => array(
				'number' => array(),
			),
			'_DateUploaded' => array(
				'dateTime' => array(),
			),
			'_Rating' => array(
				'number' => array(),
			),
			'_Path' => array(
				'string' => array(),
			),
			'_Status' => array(
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
	 * @return momusicMusic
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
	 * momusicMusic::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return momusicMusic
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
	 * @return momusicMusic
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
	 * @return momusicMusic
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
	 * @return momusicMusic
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
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
	 * @return momusicMusic
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Duration
	 *
	 * @return integer
 	 */
	function getDuration() {
		return $this->_Duration;
	}

	/**
	 * Set the object property _Duration to $inDuration
	 *
	 * @param integer $inDuration
	 * @return momusicMusic
	 */
	function setDuration($inDuration) {
		if ( $inDuration !== $this->_Duration ) {
			$this->_Duration = $inDuration;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_DateUploaded
	 *
	 * @return systemDateTime
 	 */
	function getDateUploaded() {
		return $this->_DateUploaded;
	}

	/**
	 * Set the object property _DateUploaded to $inDateUploaded
	 *
	 * @param systemDateTime $inDateUploaded
	 * @return momusicMusic
	 */
	function setDateUploaded($inDateUploaded) {
		if ( $inDateUploaded !== $this->_DateUploaded ) {
			if ( !$inDateUploaded instanceof DateTime ) {
				$inDateUploaded = new systemDateTime($inDateUploaded, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_DateUploaded = $inDateUploaded;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Rating
	 *
	 * @return integer
 	 */
	function getRating() {
		return $this->_Rating;
	}

	/**
	 * Set the object property _Rating to $inRating
	 *
	 * @param integer $inRating
	 * @return momusicMusic
	 */
	function setRating($inRating) {
		if ( $inRating !== $this->_Rating ) {
			$this->_Rating = $inRating;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Path
	 *
	 * @return integer
 	 */
	function getPath() {
		return $this->_Path;
	}

	/**
	 * Set the object property _Path to $inPath
	 *
	 * @param integer $inPath
	 * @return momusicMusic
	 */
	function setPath($inPath) {
		if ( $inPath !== $this->_Path ) {
			$this->_Path = $inPath;
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
	 * @return momusicMusic
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
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
	 * @return momusicMusic
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}