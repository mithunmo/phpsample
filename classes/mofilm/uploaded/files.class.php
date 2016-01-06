<?php
/**
 * mofilmUploadedFiles
 *
 * Stored in mofilmUploadedFiles.class.php
 *
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUploadedFiles
 * @category mofilmUploadedFiles
 * @version $Rev: 840 $
 */


/**
 * mofilmUploadedFiles Class
 *
 * Provides access to records in mofilm_content.uploadedFiles
 *
 * Creating a new record:
 * <code>
 * $oMofilmUploadedFile = new mofilmUploadedFiles();
 * $oMofilmUploadedFile->setID($inID);
 * $oMofilmUploadedFile->setUserID($inUserID);
 * $oMofilmUploadedFile->setSourceID($inSourceID);
 * $oMofilmUploadedFile->setUploadType($inUploadType);
 * $oMofilmUploadedFile->setFileName($inFileName);
 * $oMofilmUploadedFile->setStatus($inStatus);
 * $oMofilmUploadedFile->setPreferredLanguage($inPreferredLanguage);
 * $oMofilmUploadedFile->setCreated($inCreated);
 * $oMofilmUploadedFile->setModeratorID($inModeratorID);
 * $oMofilmUploadedFile->setModeratorComments($inModeratorComments);
 * $oMofilmUploadedFile->setModerated($inModerated);
 * $oMofilmUploadedFile->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUploadedFile = new mofilmUploadedFiles($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmUploadedFile = new mofilmUploadedFiles();
 * $oMofilmUploadedFile->setID($inID);
 * $oMofilmUploadedFile->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmUploadedFile = mofilmUploadedFiles::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmUploadedFiles
 * @category mofilmUploadedFiles
 */
class mofilmUploadedFiles implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmUploadedFiles
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
	 * Stores $_SourceID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SourceID;

	/**
	 * Stores $_UploadType
	 *
	 * @var string (UPLOADTYPE_NDA,)
	 * @access protected
	 */
	protected $_UploadType;
	const UPLOADTYPE_NDA = 'NDA';

	/**
	 * Stores $_FileName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_FileName;

	/**
	 * Stores $_Status
	 *
	 * @var string (STATUS_APPROVED,STATUS_REJECTED,STATUS_PENDING,)
	 * @access protected
	 */
	protected $_Status;
	const STATUS_APPROVED = 'Approved';
	const STATUS_REJECTED = 'Rejected';
	const STATUS_PENDING = 'Pending';
	
	/**
	 * Stores $_PreferredLanguage
	 *
	 * @var string
	 * @access protected
	 */
	protected $_PreferredLanguage;

	/**
	 * Stores $_Created
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_Created;

	/**
	 * Stores $_ModeratorID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ModeratorID;

	/**
	 * Stores $_ModeratorComments
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ModeratorComments;

	/**
	 * Stores $_Moderated
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_Moderated;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmUploadedFiles
	 *
	 * @param integer $inID
	 * @return mofilmUploadedFiles
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
	 * Get an instance of mofilmUploadedFiles by primary key
	 *
	 * @param integer $inID
	 * @return mofilmUploadedFiles
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
		$oObject = new mofilmUploadedFiles();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmUploadedFiles
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
			SELECT ID, userID, sourceID, uploadType, fileName, status, preferredLanguage, created, moderatorID, moderatorComments, moderated
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.uploadedFiles
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUploadedFiles();
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
			SELECT ID, userID, sourceID, uploadType, fileName, status, preferredLanguage, created, moderatorID, moderatorComments, moderated
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.uploadedFiles';

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
		$this->setUserID((int)$inArray['userID']);
		$this->setSourceID((int)$inArray['sourceID']);
		$this->setUploadType($inArray['uploadType']);
		$this->setFileName($inArray['fileName']);
		$this->setStatus($inArray['status']);
		$this->setPreferredLanguage($inArray['preferredLanguage']);
		$this->setCreated($inArray['created']);
		$this->setModeratorID((int)$inArray['moderatorID']);
		$this->setModeratorComments($inArray['moderatorComments']);
		$this->setModerated($inArray['moderated']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.uploadedFiles
					( ID, userID, sourceID, uploadType, fileName, status, preferredLanguage, created, moderatorID, moderatorComments, moderated )
				VALUES
					( :ID, :UserID, :SourceID, :UploadType, :FileName, :Status, :PreferredLanguage, :Created, :ModeratorID, :ModeratorComments, :Moderated )
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					sourceID=VALUES(sourceID),
					uploadType=VALUES(uploadType),
					fileName=VALUES(fileName),
					status=VALUES(status),
					preferredLanguage=VALUES(preferredLanguage),
					created=VALUES(created),
					moderatorID=VALUES(moderatorID),
					moderatorComments=VALUES(moderatorComments),
					moderated=VALUES(moderated)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':SourceID', $this->getSourceID());
				$oStmt->bindValue(':UploadType', $this->getUploadType());
				$oStmt->bindValue(':FileName', $this->getFileName());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':PreferredLanguage', $this->getPreferredLanguage());
				$oStmt->bindValue(':Created', $this->getCreated());
				$oStmt->bindValue(':ModeratorID', $this->getModeratorID());
				$oStmt->bindValue(':ModeratorComments', $this->getModeratorComments());
				$oStmt->bindValue(':Moderated', $this->getModerated());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.uploadedFiles
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
	 * @return mofilmUploadedFiles
	 */
	function reset() {
		$this->_ID = 0;
		$this->_UserID = 0;
		$this->_SourceID = 0;
		$this->_UploadType = 'NDA';
		$this->_FileName = '';
		$this->_Status = 'Pending';
		$this->_Created = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_PreferredLanguage = "en";
		$this->_ModeratorID = null;
		$this->_ModeratorComments = null;
		$this->_Moderated = null;
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
	 * @return mofilmUploadedFiles
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
			'_SourceID' => array(
				'number' => array(),
			),
			'_UploadType' => array(
				'inArray' => array('values' => array(self::UPLOADTYPE_NDA),),
			),
			'_FileName' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_Status' => array(
				'inArray' => array('values' => array(self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_PENDING),),
			),
			'_PreferredLanguage' => array(
				'string' => array('min' => 1,'max' => 5,),
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
	 * @return mofilmUploadedFiles
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
	 * mofilmUploadedFiles::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmUploadedFiles
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
	 * @return mofilmUploadedFiles
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
	 * @return mofilmUploadedFiles
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the Mofilm User Object
	 * 
	 * @return mofilmUser
	 */
	function getUser(){
	    	return mofilmUserManager::getInstanceByID($this->getUserID());
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
	 * @return mofilmUploadedFiles
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the Mofilm Source Object
	 * 
	 * @return mofilmSource
	 */
	function getSource(){
	    return mofilmSource::getInstance($this->getSourceID());
	}

	/**
	 * Return the current value of the property $_UploadType
	 *
	 * @return string
 	 */
	function getUploadType() {
		return $this->_UploadType;
	}

	/**
	 * Set the object property _UploadType to $inUploadType
	 *
	 * @param string $inUploadType
	 * @return mofilmUploadedFiles
	 */
	function setUploadType($inUploadType) {
		if ( $inUploadType !== $this->_UploadType ) {
			$this->_UploadType = $inUploadType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_FileName
	 *
	 * @return string
 	 */
	function getDownloadFileName() {
		return mofilmConstants::getUploadedFilesFolder().DIRECTORY_SEPARATOR.$this->_FileName;
	}

	/**
	 * Return the current value of the property $_FileName
	 *
	 * @return string
 	 */
	function getFileName() {
		return $this->_FileName;
	}

	/**
	 * Set the object property _FileName to $inFileName
	 *
	 * @param string $inFileName
	 * @return mofilmUploadedFiles
	 */
	function setFileName($inFileName) {
		if ( $inFileName !== $this->_FileName ) {
			$this->_FileName = $inFileName;
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
	 * @return mofilmUploadedFiles
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return the current value of the property $_PreferredLanguage
	 *
	 * @return string
 	 */
	function getPreferredLanguage() {
		return $this->_PreferredLanguage;
	}

	/**
	 * Set the object property _PreferredLanguage to $inPreferredLanguage
	 *
	 * @param string $inPreferredLanguage
	 * @return mofilmUploadedFiles
	 */
	function setPreferredLanguage($inPreferredLanguage) {
		if ( $inPreferredLanguage !== $this->_PreferredLanguage ) {
			$this->_PreferredLanguage = $inPreferredLanguage;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Created
	 *
	 * @return systemDateTime
 	 */
	function getCreated() {
		return $this->_Created;
	}

	/**
	 * Set the object property _Created to $inCreated
	 *
	 * @param systemDateTime $inCreated
	 * @return mofilmUploadedFiles
	 */
	function setCreated($inCreated) {
		if ( $inCreated !== $this->_Created ) {
			if ( !$inCreated instanceof DateTime ) {
				$inCreated = new systemDateTime($inCreated, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_Created = $inCreated;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ModeratorID
	 *
	 * @return integer
 	 */
	function getModeratorID() {
		return $this->_ModeratorID;
	}

	/**
	 * Set the object property _ModeratorID to $inModeratorID
	 *
	 * @param integer $inModeratorID
	 * @return mofilmUploadedFiles
	 */
	function setModeratorID($inModeratorID) {
		if ( $inModeratorID !== $this->_ModeratorID ) {
			$this->_ModeratorID = $inModeratorID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ModeratorComments
	 *
	 * @return string
 	 */
	function getModeratorComments() {
		return $this->_ModeratorComments;
	}

	/**
	 * Set the object property _ModeratorComments to $inModeratorComments
	 *
	 * @param string $inModeratorComments
	 * @return mofilmUploadedFiles
	 */
	function setModeratorComments($inModeratorComments) {
		if ( $inModeratorComments !== $this->_ModeratorComments ) {
			$this->_ModeratorComments = $inModeratorComments;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Moderated
	 *
	 * @return systemDateTime
 	 */
	function getModerated() {
		return $this->_Moderated;
	}

	/**
	 * Set the object property _Moderated to $inModerated
	 *
	 * @param systemDateTime $inModerated
	 * @return mofilmUploadedFiles
	 */
	function setModerated($inModerated) {
		if ( $inModerated !== $this->_Moderated ) {
			if ( !$inModerated instanceof DateTime ) {
				$inModerated = new systemDateTime($inModerated, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_Moderated = $inModerated;
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
	 * @return mofilmUploadedFiles
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	/**
	 * Returns an array of available uploadedFilesStatus
	 *
	 * @return array
	 * @static
	 */
	static function getAvailableUploadedFilesStatus() {
		return array(
			self::STATUS_APPROVED,
			self::STATUS_PENDING,
			self::STATUS_REJECTED,
		);
	}
}