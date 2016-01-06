<?php
/**
 * momusicSyncMovies
 *
 * Stored in momusicSyncMovies.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package momusic
 * @subpackage momusicSyncMovies
 * @category momusicSyncMovies
 * @version $Rev: 840 $
 */


/**
 * momusicSyncMovies Class
 *
 * Provides access to records in momusic_content.syncMovies
 *
 * Creating a new record:
 * <code>
 * $oMomusicSyncMovie = new momusicSyncMovies();
 * $oMomusicSyncMovie->setID($inID);
 * $oMomusicSyncMovie->setUserID($inUserID);
 * $oMomusicSyncMovie->setName($inName);
 * $oMomusicSyncMovie->setPath($inPath);
 * $oMomusicSyncMovie->setDate($inDate);
 * $oMomusicSyncMovie->setStatus($inStatus);
 * $oMomusicSyncMovie->setUniqID($inUniqID);
 * $oMomusicSyncMovie->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMomusicSyncMovie = new momusicSyncMovies($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMomusicSyncMovie = new momusicSyncMovies();
 * $oMomusicSyncMovie->setID($inID);
 * $oMomusicSyncMovie->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMomusicSyncMovie = momusicSyncMovies::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package momusic
 * @subpackage momusicSyncMovies
 * @category momusicSyncMovies
 */
class momusicSyncMovies implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of momusicSyncMovies
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
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_Name
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Name;

	/**
	 * Stores $_Path
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Path;

	/**
	 * Stores $_Date
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_Date;

	/**
	 * Stores $_Status
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Status;

	/**
	 * Stores $_UniqID
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_UniqID;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of momusicSyncMovies
	 *
	 * @param integer $inID
	 * @return momusicSyncMovies
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
	 * Get an instance of momusicSyncMovies by primary key
	 *
	 * @param integer $inID
	 * @return momusicSyncMovies
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
		$oObject = new momusicSyncMovies();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get instance of momusicSyncMovie by unique key (uniqID)
	 *
	 * @param string $inUniqID
	 * @return momusicSyncMovie
	 * @static
	 */
	public static function getInstanceByUniqID($inUniqID) {
		$key = (string) $inUniqID;
		/**
		 * Check for an existing instance
		 */
		
		self::$_Instances[$key] = self::$_Instances[$key];
		
		
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new momusicSyncMovies();
		$oObject->setUniqID($inUniqID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}
	
	
	/**
	 * Returns an array of objects of momusicSyncMovies
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
			SELECT ID, userID, name, path, date, status, uniqID
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.syncMovies
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicSyncMovies();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}


	/**
	 * Returns an array of objects of momusicSyncMovies
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjectsByStatus($inStatus) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, userID, name, path, date, status, uniqID
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.syncMovies';
		
		$where = array();
		$where[] = ' status = :status ';
		$query .= ' WHERE ' . implode(' AND ', $where);

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':status', $inStatus);
		
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicSyncMovies();
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
			SELECT ID, userID, name, path, date, status, uniqID
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.syncMovies';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}

		if ( $this->_UniqID !== '' ) {
			$where[] = ' uniqID = :UniqID ';
			$values[':UniqID'] = $this->getUniqID();
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
		$this->setUserID((int)$inArray['userID']);
		$this->setName($inArray['name']);
		$this->setPath($inArray['path']);
		$this->setDate($inArray['date']);
		$this->setStatus((int)$inArray['status']);
		$this->setUniqID($inArray['uniqID']);
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
				INSERT INTO '.system::getConfig()->getDatabase('momusic_content').'.syncMovies
					( ID, userID, name, path, date, status, uniqID )
				VALUES
					( :ID, :UserID, :Name, :Path, :Date, :Status, :UniqID )
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					name=VALUES(name),
					path=VALUES(path),
					date=VALUES(date),
					status=VALUES(status),
					uniqID=VALUES(uniqID)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':Name', $this->getName());
				$oStmt->bindValue(':Path', $this->getPath());
				$oStmt->bindValue(':Date', $this->getDate());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':UniqID', $this->getUniqID());

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
			DELETE FROM '.system::getConfig()->getDatabase('momusic_content').'.syncMovies
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
	 * @return momusicSyncMovies
	 */
	function reset() {
		$this->_ID = 0;
		$this->_UserID = 0;
		$this->_Name = '';
		$this->_Path = '';
		$this->_Date = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Status = 0;
		$this->_UniqID = '';
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
	 * @return momusicSyncMovies
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
			'_Name' => array(
				'string' => array(),
			),
			'_Path' => array(
				'string' => array(),
			),
			'_Date' => array(
				'dateTime' => array(),
			),
			'_Status' => array(
				'number' => array(),
			),
			'_UniqID' => array(
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

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return momusicSyncMovies
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
	 * momusicSyncMovies::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return momusicSyncMovies
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
	 * @return momusicSyncMovies
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
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
	 * @return momusicSyncMovies
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
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
	 * @return momusicSyncMovies
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Path
	 *
	 * @return string
 	 */
	function getPath() {
		return $this->_Path;
	}

	/**
	 * Set the object property _Path to $inPath
	 *
	 * @param string $inPath
	 * @return momusicSyncMovies
	 */
	function setPath($inPath) {
		if ( $inPath !== $this->_Path ) {
			$this->_Path = $inPath;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Date
	 *
	 * @return systemDateTime
 	 */
	function getDate() {
		return $this->_Date;
	}

	/**
	 * Set the object property _Date to $inDate
	 *
	 * @param systemDateTime $inDate
	 * @return momusicSyncMovies
	 */
	function setDate($inDate) {
		if ( $inDate !== $this->_Date ) {
			if ( !$inDate instanceof DateTime ) {
				$inDate = new systemDateTime($inDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_Date = $inDate;
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
	 * @return momusicSyncMovies
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_UniqID
	 *
	 * @return string
 	 */
	function getUniqID() {
		return $this->_UniqID;
	}

	/**
	 * Set the object property _UniqID to $inUniqID
	 *
	 * @param string $inUniqID
	 * @return momusicSyncMovies
	 */
	function setUniqID($inUniqID) {
		if ( $inUniqID !== $this->_UniqID ) {
			$this->_UniqID = $inUniqID;
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
	 * @return momusicSyncMovies
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}