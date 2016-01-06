<?php
/**
 * mofilmMovieBroadcast
 *
 * Stored in mofilmMovieBroadcast.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMovieBroadcast
 * @category mofilmMovieBroadcast
 * @version $Rev: 840 $
 */


/**
 * mofilmMovieBroadcast Class
 *
 * Provides access to records in mofilm_content.movieBroadcast
 *
 * Creating a new record:
 * <code>
 * $oMofilmMovieBroadcast = new mofilmMovieBroadcast();
 * $oMofilmMovieBroadcast->setMovieID($inMovieID);
 * $oMofilmMovieBroadcast->setUserID($inUserID);
 * $oMofilmMovieBroadcast->setCountryID($inCountryID);
 * $oMofilmMovieBroadcast->setBroadCastDate($inBroadCastDate);
 * $oMofilmMovieBroadcast->setCreatedDate($inCreatedDate);
 * $oMofilmMovieBroadcast->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMovieBroadcast = new mofilmMovieBroadcast($inMovieID, $inCountryID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmMovieBroadcast = new mofilmMovieBroadcast();
 * $oMofilmMovieBroadcast->setMovieID($inMovieID);
 * $oMofilmMovieBroadcast->setCountryID($inCountryID);
 * $oMofilmMovieBroadcast->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmMovieBroadcast = mofilmMovieBroadcast::getInstance($inMovieID, $inCountryID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmMovieBroadcast
 * @category mofilmMovieBroadcast
 */
class mofilmMovieBroadcast implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmMovieBroadcast
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
	 * Stores $_MovieID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;

	/**
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_CountryID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_CountryID;

	/**
	 * Stores $_BroadCastDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_BroadCastDate;

	/**
	 * Stores $_CreatedDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_CreatedDate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmMovieBroadcast
	 *
	 * @param integer $inMovieID
	 * @param integer $inCountryID
	 * @return mofilmMovieBroadcast
	 */
	function __construct($inMovieID = null, $inCountryID = null) {
		$this->reset();
		if ( $inMovieID !== null && $inCountryID !== null ) {
			$this->setMovieID($inMovieID);
			$this->setCountryID($inCountryID);
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
	 * Get an instance of mofilmMovieBroadcast by primary key
	 *
	 * @param integer $inMovieID
	 * @param integer $inCountryID
	 * @return mofilmMovieBroadcast
	 * @static
	 */
	public static function getInstance($inMovieID, $inCountryID) {
		$key = $inMovieID.'.'.$inCountryID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmMovieBroadcast();
		$oObject->setMovieID($inMovieID);
		$oObject->setCountryID($inCountryID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get instance of mofilmMovieBroadcast by unique key (movieID)
	 *
	 * @param integer $inMovieID
	 * @param integer $inCountryID
	 * @return mofilmMovieBroadcast
	 * @static
	 */
	public static function getInstanceByMovieID($inMovieID, $inCountryID) {
		$key = $inMovieID.'.'.$inCountryID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmMovieBroadcast();
		$oObject->setMovieID($inMovieID);
		$oObject->setCountryID($inCountryID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmMovieBroadcast
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
			SELECT movieID, UserID, CountryID, broadCastDate, createdDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieBroadcast
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmMovieBroadcast();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

        /**
	 * Returns an array of objects of mofilmMovieBroadcast
	 *
	 * @param integer $inMovieID
	 * @return array
	 * @static
	 */

        public static function getBroadCastDetails($inMovieID) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT movieID, CountryID, broadCastDate, createdDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieBroadcast
			 WHERE 1';
                if ( $inMovieID !== null ) {
			$query .= ' AND movieID = :MovieID ';
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
                if ( $inMovieID !== null ) {
                        $oStmt->bindValue(':MovieID', $inMovieID, PDO::PARAM_INT);
                }
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmMovieBroadcast();
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
			SELECT movieID, UserID, CountryID, broadCastDate, createdDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieBroadcast';

		$where = array();
		if ( $this->_MovieID !== 0 ) {
			$where[] = ' movieID = :MovieID ';
			$values[':MovieID'] = $this->getMovieID();
		}
		if ( $this->_CountryID !== 0 ) {
			$where[] = ' CountryID = :CountryID ';
			$values[':CountryID'] = $this->getCountryID();
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
		$this->setMovieID((int)$inArray['movieID']);
		$this->setUserID((int)$inArray['UserID']);
		$this->setCountryID((int)$inArray['CountryID']);
		$this->setBroadCastDate($inArray['broadCastDate']);
		$this->setCreatedDate($inArray['createdDate']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieBroadcast
					( movieID, UserID, CountryID, broadCastDate, createdDate )
				VALUES
					( :MovieID, :UserID, :CountryID, :BroadCastDate, :CreatedDate )
				ON DUPLICATE KEY UPDATE
					UserID=VALUES(UserID),
					broadCastDate=VALUES(broadCastDate),
					createdDate=VALUES(createdDate)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':MovieID', $this->getMovieID());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':CountryID', $this->getCountryID());
				$oStmt->bindValue(':BroadCastDate', $this->getBroadCastDate());
				$oStmt->bindValue(':CreatedDate', $this->getCreatedDate());

				if ( $oStmt->execute() ) {
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieBroadcast
			WHERE
				movieID = :MovieID AND
				CountryID = :CountryID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':MovieID', $this->getMovieID());
		$oStmt->bindValue(':CountryID', $this->getCountryID());

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
	 * @return mofilmMovieBroadcast
	 */
	function reset() {
		$this->_MovieID = 0;
		$this->_UserID = 0;
		$this->_CountryID = 0;
		$this->_BroadCastDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_CreatedDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
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
	 * @return mofilmMovieBroadcast
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
			'_MovieID' => array(
				'number' => array(),
			),
			'_UserID' => array(
				'number' => array(),
			),
			'_CountryID' => array(
				'number' => array(),
			),
			'_BroadCastDate' => array(
				'dateTime' => array(),
			),
			'_CreatedDate' => array(
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
	 * @return mofilmMovieBroadcast
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
		return $this->_MovieID.self::PRIMARY_KEY_SEPARATOR.$this->_CountryID;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * mofilmMovieBroadcast::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmMovieBroadcast
  	 */
	function setPrimaryKey($inKey) {
		list($movieID, $CountryID) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setMovieID($movieID);
		$this->setCountryID($CountryID);
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
	 * @return mofilmMovieBroadcast
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
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
	 * @return mofilmMovieBroadcast
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_CountryID
	 *
	 * @return integer
 	 */
	function getCountryID() {
		return $this->_CountryID;
	}

	/**
	 * Set the object property _CountryID to $inCountryID
	 *
	 * @param integer $inCountryID
	 * @return mofilmMovieBroadcast
	 */
	function setCountryID($inCountryID) {
		if ( $inCountryID !== $this->_CountryID ) {
			$this->_CountryID = $inCountryID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_BroadCastDate
	 *
	 * @return systemDateTime
 	 */
	function getBroadCastDate() {
		return $this->_BroadCastDate;
	}

	/**
	 * Set the object property _BroadCastDate to $inBroadCastDate
	 *
	 * @param systemDateTime $inBroadCastDate
	 * @return mofilmMovieBroadcast
	 */
	function setBroadCastDate($inBroadCastDate) {
		if ( $inBroadCastDate !== $this->_BroadCastDate ) {
			if ( !$inBroadCastDate instanceof DateTime ) {
				$inBroadCastDate = new systemDateTime($inBroadCastDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_BroadCastDate = $inBroadCastDate;
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
	 * @return mofilmMovieBroadcast
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
	 * @return mofilmMovieBroadcast
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}