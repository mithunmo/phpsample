<?php
/**
 * mofilmSystemAPIRequest
 *
 * Stored in mofilmSystemAPIRequest.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSystemAPIRequest
 * @category mofilmSystemAPIRequest
 * @version $Rev: 806 $
 */


/**
 * mofilmSystemAPIRequest Class
 *
 * Provides access to records in mofilm_system.mofilmAPIRequest
 *
 * Creating a new record:
 * <code>
 * $oMofilmSystemAPIRequest = new mofilmSystemAPIRequest();
 * $oMofilmSystemAPIRequest->setID($inID);
 * $oMofilmSystemAPIRequest->setRequestAPIkey($inRequestAPIkey);
 * $oMofilmSystemAPIRequest->setRequestToken($inRequestToken);
 * $oMofilmSystemAPIRequest->setRequestAction($inRequestAction);
 * $oMofilmSystemAPIRequest->setTimeStamp($inTimeStamp);
 * $oMofilmSystemAPIRequest->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmSystemAPIRequest = new mofilmSystemAPIRequest($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmSystemAPIRequest = new mofilmSystemAPIRequest();
 * $oMofilmSystemAPIRequest->setID($inID);
 * $oMofilmSystemAPIRequest->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmSystemAPIRequest = mofilmSystemAPIRequest::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmSystemAPIRequest
 * @category mofilmSystemAPIRequest
 */
class mofilmSystemAPIRequest implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmSystemAPIRequest
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
	 * Stores $_RequestAPIkey
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_RequestAPIkey;

	/**
	 * Stores $_RequestToken
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_RequestToken;

	/**
	 * Stores $_RequestAction
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_RequestAction;

	/**
	 * Stores $_TimeStamp
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_TimeStamp;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmSystemAPIRequest
	 *
	 * @param integer $inID
	 * @return mofilmSystemAPIRequest
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
	 * Get an instance of mofilmSystemAPIRequest by primary key
	 *
	 * @param integer $inID
	 * @return mofilmSystemAPIRequest
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
		$oObject = new mofilmSystemAPIRequest();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get instance of mofilmSystemAPIRequest by unique key (requestToken)
	 *
	 * @param string $inRequestToken
	 * @return mofilmSystemAPIRequest
	 * @static
	 */
	public static function getInstanceByRequestToken($inRequestToken) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inRequestToken]) ) {
			return self::$_Instances[$inRequestToken];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmSystemAPIRequest();
		$oObject->setRequestToken($inRequestToken);
		if ( $oObject->load() ) {
			self::$_Instances[$inRequestToken] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmSystemAPIRequest
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
			SELECT ID, requestAPIkey, requestToken, requestAction, timeStamp
			  FROM '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIRequest
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmSystemAPIRequest();
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
			SELECT ID, requestAPIkey, requestToken, requestAction, timeStamp
			  FROM '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIRequest';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		if ( $this->_RequestToken !== '' ) {
			$where[] = ' requestToken = :RequestToken ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $this->_ID !== 0 ) {
			$oStmt->bindValue(':ID', $this->getID());
		}
		if ( $this->_RequestToken !== '' ) {
			$oStmt->bindValue(':RequestToken', $this->getRequestToken());
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
		$this->setRequestAPIkey($inArray['requestAPIkey']);
		$this->setRequestToken($inArray['requestToken']);
		$this->setRequestAction($inArray['requestAction']);
		$this->setTimeStamp($inArray['timeStamp']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIRequest
					( ID, requestAPIkey, requestToken, requestAction, timeStamp)
				VALUES
					(:ID, :RequestAPIkey, :RequestToken, :RequestAction, :TimeStamp)
				ON DUPLICATE KEY UPDATE
					requestAPIkey=VALUES(requestAPIkey),
					requestAction=VALUES(requestAction),
					timeStamp=VALUES(timeStamp)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':RequestAPIkey', $this->getRequestAPIkey());
				$oStmt->bindValue(':RequestToken', $this->getRequestToken());
				$oStmt->bindValue(':RequestAction', $this->getRequestAction());
				$oStmt->bindValue(':TimeStamp', $this->getTimeStamp());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIRequest
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
	 * @return mofilmSystemAPIRequest
	 */
	function reset() {
		$this->_ID = 0;
		$this->_RequestAPIkey = '';
		$this->_RequestToken = '';
		$this->_RequestAction = '';
		$this->_TimeStamp = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
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
	 * @return mofilmSystemAPIRequest
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
			'_RequestAPIkey' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_RequestToken' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_RequestAction' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_TimeStamp' => array(
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
	 * @return mofilmSystemAPIRequest
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
	 * {@link mofilmSystemAPIRequest::PRIMARY_KEY_SEPARATOR}.
 	 *
	 * @param string $inKey
	 * @return mofilmSystemAPIRequest
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
	 * @return mofilmSystemAPIRequest
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_RequestAPIkey
	 *
	 * @return string
 	 */
	function getRequestAPIkey() {
		return $this->_RequestAPIkey;
	}

	/**
	 * Set the object property _RequestAPIkey to $inRequestAPIkey
	 *
	 * @param string $inRequestAPIkey
	 * @return mofilmSystemAPIRequest
	 */
	function setRequestAPIkey($inRequestAPIkey) {
		if ( $inRequestAPIkey !== $this->_RequestAPIkey ) {
			$this->_RequestAPIkey = $inRequestAPIkey;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_RequestToken
	 *
	 * @return string
 	 */
	function getRequestToken() {
		return $this->_RequestToken;
	}

	/**
	 * Set the object property _RequestToken to $inRequestToken
	 *
	 * @param string $inRequestToken
	 * @return mofilmSystemAPIRequest
	 */
	function setRequestToken($inRequestToken) {
		if ( $inRequestToken !== $this->_RequestToken ) {
			$this->_RequestToken = $inRequestToken;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_RequestAction
	 *
	 * @return string
 	 */
	function getRequestAction() {
		return $this->_RequestAction;
	}

	/**
	 * Set the object property _RequestAction to $inRequestAction
	 *
	 * @param string $inRequestAction
	 * @return mofilmSystemAPIRequest
	 */
	function setRequestAction($inRequestAction) {
		if ( $inRequestAction !== $this->_RequestAction ) {
			$this->_RequestAction = $inRequestAction;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_TimeStamp
	 *
	 * @return systemDateTime
 	 */
	function getTimeStamp() {
		return $this->_TimeStamp;
	}

	/**
	 * Set the object property _TimeStamp to $inTimeStamp
	 *
	 * @param systemDateTime $inTimeStamp
	 * @return mofilmSystemAPIRequest
	 */
	function setTimeStamp($inTimeStamp) {
		if ( $inTimeStamp !== $this->_TimeStamp ) {
			if ( !$inTimeStamp instanceof DateTime ) {
				$inTimeStamp = new systemDateTime($inTimeStamp, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_TimeStamp = $inTimeStamp;
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
	 * @return mofilmSystemAPIRequest
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}