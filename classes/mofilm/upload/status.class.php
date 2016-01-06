<?php
/**
 * mofilmUploadStatus
 *
 * Stored in mofilmUploadStatus.class.php
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUploadStatus
 * @category mofilmUploadStatus
 * @version $Rev: 840 $
 */


/**
 * mofilmUploadStatus Class
 *
 * Provides access to records in mofilm_content.uploadStatus
 *
 * Creating a new record:
 * <code>
 * $oMofilmUploadStatu = new mofilmUploadStatus();
 * $oMofilmUploadStatu->setID($inID);
 * $oMofilmUploadStatu->setMovieID($inMovieID);
 * $oMofilmUploadStatu->setVideoCloudID($inVideoCloudID);
 * $oMofilmUploadStatu->setStatus($inStatus);
 * $oMofilmUploadStatu->setUpdateDate($inUpdateDate);
 * $oMofilmUploadStatu->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUploadStatu = new mofilmUploadStatus($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmUploadStatu = new mofilmUploadStatus();
 * $oMofilmUploadStatu->setID($inID);
 * $oMofilmUploadStatu->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmUploadStatu = mofilmUploadStatus::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmUploadStatus
 * @category mofilmUploadStatus
 */
class mofilmUploadStatus implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmUploadStatus
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
	 * Stores $_VideoCloudID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_VideoCloudID;

	/**
	 * Stores $_Status
	 *
	 * @var string (STATUS_QUEUED,STATUS_PROCESSING,STATUS_FAILED,STATUS_SUCCESS,STATUS_ENCODING,)
	 * @access protected
	 */
	protected $_Status;
	const STATUS_QUEUED = 'Queued';
	const STATUS_PROCESSING = 'Processing';
	const STATUS_FAILED = 'Failed';
	const STATUS_SUCCESS = 'Success';
	const STATUS_ENCODING = 'Encoding';

	/**
	 * Stores $_UpdateDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_UpdateDate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmUploadStatus
	 *
	 * @param integer $inID
	 * @return mofilmUploadStatus
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
	 * Get an instance of mofilmUploadStatus by primary key
	 *
	 * @param integer $inID
	 * @return mofilmUploadStatus
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
		$oObject = new mofilmUploadStatus();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmUploadStatus by movieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmUploadStatus
	 * @static
	 */
	public static function getInstanceByMovieID($inMovieID) {
		$query = '
			SELECT ID, movieID, videoCloudID, status, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.uploadStatus
			 WHERE movieID = :movieID';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':movieID', $inMovieID);
		
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUploadStatus();
				$oObject->loadFromArray($row);
			}
		}
		$oStmt->closeCursor();

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmUploadStatus
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
			SELECT ID, movieID, videoCloudID, status, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.uploadStatus
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUploadStatus();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}


	/**
	 * Returns an array of objects of mofilmUploadStatus
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function getEncodingMovies() {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, movieID, videoCloudID, status
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.uploadStatus
			 WHERE status = :Status';

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Status', self::STATUS_PROCESSING);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUploadStatus();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();
		return $list;
	}
	
	/**
	 * Returns an array of objects of mofilmUploadStatus
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function getOldEncodingMovies() {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, movieID, videoCloudID, status
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.uploadStatus
			 WHERE status = :Status';
		$query .= ' ORDER BY movieID DESC LIMIT 1';

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Status', self::STATUS_ENCODING);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUploadStatus();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();
		return $oObject;
	}
	
	/**
	 * Returns an array of objects of mofilmUploadStatus
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function getSuccessMovies() {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, movieID, videoCloudID, status
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.uploadStatus
			 WHERE status = :Status';

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Status', self::STATUS_SUCCESS);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUploadStatus();
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
			SELECT ID, movieID, videoCloudID, status, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.uploadStatus';

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
		$this->setVideoCloudID((int)$inArray['videoCloudID']);
		$this->setStatus($inArray['status']);
		$this->setUpdateDate($inArray['updateDate']);
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

			// $this->getUpdateDate()->setTimestamp(time());
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.uploadStatus
					( ID, movieID, videoCloudID, status, updateDate )
				VALUES
					( :ID, :MovieID, :VideoCloudID, :Status, :UpdateDate )
				ON DUPLICATE KEY UPDATE
					movieID=VALUES(movieID),
					videoCloudID=VALUES(videoCloudID),
					status=VALUES(status),
					updateDate=VALUES(updateDate)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':MovieID', $this->getMovieID());
				$oStmt->bindValue(':VideoCloudID', $this->getVideoCloudID());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':UpdateDate', $this->getUpdateDate());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.uploadStatus
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
	 * @return mofilmUploadStatus
	 */
	function reset() {
		$this->_ID = 0;
		$this->_MovieID = 0;
		$this->_VideoCloudID = 0;
		$this->_Status = '';
		$this->_UpdateDate = null;
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
	 * @return mofilmUploadStatus
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
			'_VideoCloudID' => array(
				'number' => array(),
			),
			'_Status' => array(
				'inArray' => array('values' => array(self::STATUS_QUEUED, self::STATUS_PROCESSING, self::STATUS_FAILED, self::STATUS_SUCCESS, self::STATUS_ENCODING),),
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
	 * @return mofilmUploadStatus
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
	 * mofilmUploadStatus::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmUploadStatus
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
	 * @return mofilmUploadStatus
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
	 * @return mofilmUploadStatus
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_VideoCloudID
	 *
	 * @return integer
 	 */
	function getVideoCloudID() {
		return $this->_VideoCloudID;
	}

	/**
	 * Set the object property _VideoCloudID to $inVideoCloudID
	 *
	 * @param integer $inVideoCloudID
	 * @return mofilmUploadStatus
	 */
	function setVideoCloudID($inVideoCloudID) {
		if ( $inVideoCloudID !== $this->_VideoCloudID ) {
			$this->_VideoCloudID = $inVideoCloudID;
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
	 * @return mofilmUploadStatus
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
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
	 * @return mofilmUploadStatus
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
	 * @return mofilmUploadStatus
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}