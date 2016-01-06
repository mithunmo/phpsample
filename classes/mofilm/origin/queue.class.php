<?php
/**
 * mofilmOriginQueue
 *
 * Stored in mofilmOriginQueue.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmOriginQueue
 * @category mofilmOriginQueue
 * @version $Rev: 361 $
 */


/**
 * mofilmOriginQueue Class
 *
 * Provides access to records in mofilm_content.originQueue
 *
 * Creating a new record:
 * <code>
 * $oMofilmOriginQueue = new mofilmOriginQueue();
 * $oMofilmOriginQueue->setID($inID);
 * $oMofilmOriginQueue->setMovieID($inMovieID);
 * $oMofilmOriginQueue->setBulkID($inBulkID);
 * $oMofilmOriginQueue->setUserID($inUserID);
 * $oMofilmOriginQueue->setQueued($inQueued);
 * $oMofilmOriginQueue->setProfile($inProfile);
 * $oMofilmOriginQueue->setStatus($inStatus);
 * $oMofilmOriginQueue->setSent($inSent);
 * $oMofilmOriginQueue->setNotes($inNotes);
 * $oMofilmOriginQueue->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmOriginQueue = new mofilmOriginQueue($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmOriginQueue = new mofilmOriginQueue();
 * $oMofilmOriginQueue->setID($inID);
 * $oMofilmOriginQueue->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmOriginQueue = mofilmOriginQueue::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmOriginQueue
 * @category mofilmOriginQueue
 */
class mofilmOriginQueue implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmOriginQueue
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
	 * Stores $_MovieID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;

	/**
	 * Stores $_BulkID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_BulkID;

	/**
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_Queued
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_Queued;

	/**
	 * Stores $_Profile
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Profile;

	/**
	 * Stores $_Status
	 *
	 * @var string (STATUS_HOLD,STATUS_QUEUED,STATUS_PROCESSING,STATUS_SENT,STATUS_FAILED,)
	 * @access protected
	 */
	protected $_Status;
	const STATUS_HOLD = 'Hold';
	const STATUS_QUEUED = 'Queued';
	const STATUS_PROCESSING = 'Processing';
	const STATUS_SENT = 'Sent';
	const STATUS_FAILED = 'Failed';

	/**
	 * Stores $_Sent
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_Sent;

	/**
	 * Stores $_Notes
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Notes;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmOriginQueue
	 *
	 * @param integer $inID
	 * @return mofilmOriginQueue
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
	 * Get an instance of mofilmOriginQueue by primary key
	 *
	 * @param integer $inID
	 * @return mofilmOriginQueue
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
		$oObject = new mofilmOriginQueue();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmOriginQueue
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
			SELECT ID, movieID, bulkID, userID, queued, profile, status, sent, notes
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.originQueue
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmOriginQueue();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}


	/**
	 * Returns an array of objects of mofilmOriginQueue
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function getMovieFromQueue() {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, movieID, bulkID, userID, queued, profile, status, sent, notes
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.originQueue
			 WHERE status = :Status';
		
			$query .= ' LIMIT 1';

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Status', self::STATUS_QUEUED);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmOriginQueue();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $oObject;
	}
	

	/**
	 * Returns an array of objects of mofilmOriginQueue
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function getLatestMovieFromQueue() {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, movieID, bulkID, userID, queued, profile, status, sent, notes
			FROM '.system::getConfig()->getDatabase('mofilm_content').'.originQueue
			WHERE status = :Status AND ID = ( select max(ID) FROM '.system::getConfig()->getDatabase('mofilm_content').'.originQueue
			WHERE status = :Status GROUP BY status HAVING count(status) > 2 )';
		

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Status', self::STATUS_QUEUED);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmOriginQueue();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $oObject;
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
			SELECT ID, movieID, bulkID, userID, queued, profile, status, sent, notes
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.originQueue';

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
		$this->setMovieID((int)$inArray['movieID']);
		$this->setBulkID((int)$inArray['bulkID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setQueued($inArray['queued']);
		$this->setProfile($inArray['profile']);
		$this->setStatus($inArray['status']);
		$this->setSent($inArray['sent']);
		$this->setNotes($inArray['notes']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.originQueue
					( ID, movieID, bulkID, userID, queued, profile, status, sent, notes )
				VALUES
					( :ID, :MovieID, :BulkID, :UserID, :Queued, :Profile, :Status, :Sent, :Notes )
				ON DUPLICATE KEY UPDATE
					movieID=VALUES(movieID),
					bulkID=VALUES(bulkID),
					userID=VALUES(userID),
					queued=VALUES(queued),
					profile=VALUES(profile),
					status=VALUES(status),
					sent=VALUES(sent),
					notes=VALUES(notes)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':MovieID', $this->getMovieID());
				$oStmt->bindValue(':BulkID', $this->getBulkID());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':Queued', $this->getQueued());
				$oStmt->bindValue(':Profile', $this->getProfile());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':Sent', $this->getSent());
				$oStmt->bindValue(':Notes', $this->getNotes());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.originQueue
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
	 * @return mofilmOriginQueue
	 */
	function reset() {
		$this->_ID = 0;
		$this->_MovieID = 0;
		$this->_BulkID = null;
		$this->_UserID = 0;
		$this->_Queued = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Profile = null;
		$this->_Status = 'Queued';
		$this->_Sent = null;
		$this->_Notes = null;
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
	 * @return mofilmOriginQueue
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
			'_MovieID' => array(
				'number' => array(),
			),
			'_UserID' => array(
				'number' => array(),
			),
			'_Queued' => array(
				'dateTime' => array(),
			),
			'_Status' => array(
				'inArray' => array('values' => array(self::STATUS_HOLD, self::STATUS_QUEUED, self::STATUS_PROCESSING, self::STATUS_SENT, self::STATUS_FAILED),),
			)
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
	 * @return mofilmOriginQueue
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
	 * mofilmOriginQueue::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmOriginQueue
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
	 * @return mofilmOriginQueue
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_MovieID
	 *
	 * @return integer
 	 */
	function getMovieID() {
		return $this->_MovieID;
	}

	/**
	 * Set the object property _MovieID to $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmOriginQueue
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_BulkID
	 *
	 * @return integer
 	 */
	function getBulkID() {
		return $this->_BulkID;
	}

	/**
	 * Set the object property _BulkID to $inBulkID
	 *
	 * @param integer $inBulkID
	 * @return mofilmOriginQueue
	 */
	function setBulkID($inBulkID) {
		if ( $inBulkID !== $this->_BulkID ) {
			$this->_BulkID = $inBulkID;
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
	 * @return mofilmOriginQueue
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Queued
	 *
	 * @return systemDateTime
 	 */
	function getQueued() {
		return $this->_Queued;
	}

	/**
	 * Set the object property _Queued to $inQueued
	 *
	 * @param systemDateTime $inQueued
	 * @return mofilmOriginQueue
	 */
	function setQueued($inQueued) {
		if ( $inQueued !== $this->_Queued ) {
			if ( !$inQueued instanceof DateTime ) {
				$inQueued = new systemDateTime($inQueued, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_Queued = $inQueued;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Profile
	 *
	 * @return string
 	 */
	function getProfile() {
		return $this->_Profile;
	}

	/**
	 * Set the object property _Profile to $inProfile
	 *
	 * @param string $inProfile
	 * @return mofilmOriginQueue
	 */
	function setProfile($inProfile) {
		if ( $inProfile !== $this->_Profile ) {
			$this->_Profile = $inProfile;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Status
	 *
	 * @return string
 	 */
	function getStatus() {
		return $this->_Status;
	}

	/**
	 * Set the object property _Status to $inStatus
	 *
	 * @param string $inStatus
	 * @return mofilmOriginQueue
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
	 * @return mofilmOriginQueue
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
	 * Return the current value of the property $_Notes
	 *
	 * @return string
 	 */
	function getNotes() {
		return $this->_Notes;
	}

	/**
	 * Set the object property _Notes to $inNotes
	 *
	 * @param string $inNotes
	 * @return mofilmOriginQueue
	 */
	function setNotes($inNotes) {
		if ( $inNotes !== $this->_Notes ) {
			$this->_Notes = $inNotes;
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
	 * @return mofilmOriginQueue
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}
