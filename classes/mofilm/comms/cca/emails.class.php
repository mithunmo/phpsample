<?php
/**
 * mofilmCommsCcaEmails
 *
 * Stored in mofilmCommsCcaEmails.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsCcaEmails
 * @category mofilmCommsCcaEmails
 * @version $Rev: 838 $
 */


/**
 * mofilmCommsCcaEmails Class
 *
 * Provides access to records in mofilm_comms.ccaEmails
 *
 * Creating a new record:
 * <code>
 * $oMofilmCommsCcaEmail = new mofilmCommsCcaEmails();
 * $oMofilmCommsCcaEmail->setID($inID);
 * $oMofilmCommsCcaEmail->setEventID($inEventID);
 * $oMofilmCommsCcaEmail->setMovieID($inMovieID);
 * $oMofilmCommsCcaEmail->setEmail($inEmail);
 * $oMofilmCommsCcaEmail->setSent($inSent);
 * $oMofilmCommsCcaEmail->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCommsCcaEmail = new mofilmCommsCcaEmails($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmCommsCcaEmail = new mofilmCommsCcaEmails();
 * $oMofilmCommsCcaEmail->setID($inID);
 * $oMofilmCommsCcaEmail->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmCommsCcaEmail = mofilmCommsCcaEmails::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmCommsCcaEmails
 * @category mofilmCommsCcaEmails
 */
class mofilmCommsCcaEmails implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmCommsCcaEmails
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
	 * Stores $_EventID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_EventID;

	/**
	 * Stores $_MovieID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;

	/**
	 * Stores $_Email
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Email;

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
	 * Returns a new instance of mofilmCommsCcaEmails
	 *
	 * @param integer $inID
	 * @return mofilmCommsCcaEmails
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
	 * Get an instance of mofilmCommsCcaEmails by primary key
	 *
	 * @param integer $inID
	 * @return mofilmCommsCcaEmails
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
		$oObject = new mofilmCommsCcaEmails();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}
	
	/**
	 * Checks if the record exist with eventID,movieID
	 * 
	 * @param integer $inMovieID
	 * @param integer $inEventID
	 * @return boolean 
	 * @static
	 */
	public static function getCCARecordByMovieandEventID($inMovieID,$inEventID) {
		$query = '
			SELECT 1 AS emailSent
			FROM '.system::getConfig()->getDatabase('mofilm_comms').'.ccaEmails
			WHERE  eventID = :EventID
			   AND movieID = :MovieID';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':EventID', $inEventID);
		$oStmt->bindValue(':MovieID', $inMovieID);
		$oStmt->execute();
		$res = $oStmt->rowCount();
		if ( isset($res) && $res > 0 ) {
			return false;
		} else {
			return true;
		}

		$oStmt->closeCursor();
	}
	
	
	/**
	 * Returns an array of objects of mofilmCommsCcaEmails
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
			SELECT ID, eventID, movieID, email, sent
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.ccaEmails
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmCommsCcaEmails();
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
			SELECT ID, eventID, movieID, email, sent
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.ccaEmails';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}
		
		if ( $this->_MovieID !== 0 ) {
			$where[] = ' movieID = :movieID ';
			$values[':movieID'] = $this->getMovieID();
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
		$this->setEventID((int)$inArray['eventID']);
		$this->setMovieID((int)$inArray['movieID']);
		$this->setEmail($inArray['email']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.ccaEmails
					( ID, eventID, movieID, email, sent )
				VALUES
					( :ID, :EventID, :MovieID, :Email, :Sent )
				ON DUPLICATE KEY UPDATE
					eventID=VALUES(eventID),
					movieID=VALUES(movieID),
					email=VALUES(email),
					sent=VALUES(sent)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':EventID', $this->getEventID());
				$oStmt->bindValue(':MovieID', $this->getMovieID());
				$oStmt->bindValue(':Email', $this->getEmail());
				$oStmt->bindValue(':Sent', $this->getSent());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.ccaEmails
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
	 * @return mofilmCommsCcaEmails
	 */
	function reset() {
		$this->_ID = 0;
		$this->_EventID = 0;
		$this->_MovieID = 0;
		$this->_Email = '';
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
	 * @return mofilmCommsCcaEmails
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
			'_EventID' => array(
				'number' => array(),
			),
			'_MovieID' => array(
				'number' => array(),
			),
			'_Email' => array(
				'string' => array('min' => 1,'max' => 100,),
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
	 * @return mofilmCommsCcaEmails
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
	 * mofilmCommsCcaEmails::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmCommsCcaEmails
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
	 * @return mofilmCommsCcaEmails
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
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
	 * @return mofilmCommsCcaEmails
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
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
	 * @return mofilmCommsCcaEmails
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Email
	 *
	 * @return string
 	 */
	function getEmail() {
		return $this->_Email;
	}

	/**
	 * Set the object property _Email to $inEmail
	 *
	 * @param string $inEmail
	 * @return mofilmCommsCcaEmails
	 */
	function setEmail($inEmail) {
		if ( $inEmail !== $this->_Email ) {
			$this->_Email = $inEmail;
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
	 * @return mofilmCommsCcaEmails
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
	 * @return mofilmCommsCcaEmails
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}