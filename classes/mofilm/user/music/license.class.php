<?php
/**
 * mofilmUserMusicLicense
 *
 * Stored in mofilmUserMusicLicense.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserMusicLicense
 * @category mofilmUserMusicLicense
 * @version $Rev: 840 $
 */


/**
 * mofilmUserMusicLicense Class
 *
 * Provides access to records in mofilm_content.userLicenses
 *
 * Creating a new record:
 * <code>
 * $oMofilmUserMusicLicense = new mofilmUserMusicLicense();
 * $oMofilmUserMusicLicense->setID($inID);
 * $oMofilmUserMusicLicense->setLicenseID($inLicenseID);
 * $oMofilmUserMusicLicense->setUserID($inUserID);
 * $oMofilmUserMusicLicense->setTrackName($inTrackName);
 * $oMofilmUserMusicLicense->setStatus($inStatus);
 * $oMofilmUserMusicLicense->setExpiryDate($inExpiryDate);
 * $oMofilmUserMusicLicense->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserMusicLicense = new mofilmUserMusicLicense($inLicenseID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmUserMusicLicense = new mofilmUserMusicLicense();
 * $oMofilmUserMusicLicense->setLicenseID($inLicenseID);
 * $oMofilmUserMusicLicense->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserMusicLicense = mofilmUserMusicLicense::getInstance($inLicenseID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmUserMusicLicense
 * @category mofilmUserMusicLicense
 */
class mofilmUserMusicLicense implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmUserMusicLicense
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
	protected $_TrackID;
	
	
	/**
	 * Stores $_LicenseID
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_LicenseID;

	/**
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_TrackName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_TrackName;

	/**
	 * Stores $_Status
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Status;

	/**
	 * Stores $_ExpiryDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_ExpiryDate;

	/**
	 * Stores $_ExpiryDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_MusicSource;
	
	/**
	 * Stores $_CreateDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_CreateDate;	
		
	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;
	
	const VALID_LICENSE = 0;
	const INVALID_LICENSE = 1;


	
	/**
	 * Returns a new instance of mofilmUserMusicLicense
	 *
	 * @param string $inLicenseID
	 * @return mofilmUserMusicLicense
	 */
	function __construct($inLicenseID = null) {
		$this->reset();
		if ( $inLicenseID !== null ) {
			$this->setLicenseID($inLicenseID);
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
	 * Get an instance of mofilmUserMusicLicense by primary key
	 *
	 * @param string $inLicenseID
	 * @return mofilmUserMusicLicense
	 * @static
	 */
	public static function getInstance($inLicenseID) {
		$key = $inLicenseID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserMusicLicense();
		$oObject->setLicenseID($inLicenseID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get an instance of mofilmUserMusicLicense by primary key
	 *
	 * @param integer $inID
	 * @return mofilmUserMusicLicense
	 * @static
	 */
	public static function getInstanceByID($inID) {
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
		$oObject = new mofilmUserMusicLicense();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmUserMusicLicense by combination of userID and licenseID
	 *
	 * @param integer $inUserID
	 * @param string $inLicenseID
	 * @return mofilmUserMusicLicense
	 * @static
	 */
	public static function getInstanceByLicenseAndUserID($inUserID, $inLicenseID) {
		$key = $inLicenseID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserMusicLicense();
		$oObject->setLicenseID($inLicenseID);
		$oObject->setUserID($inUserID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmUserMusicLicense
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
			SELECT ID, trackID, licenseID, userID, trackName, status, expiryDate, musicSource, createDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLicenses
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUserMusicLicense();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

	/**
	 * Returns an array of objects of mofilmUserMusicLicense
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfTotalDownloads($inOffset = null, $inLimit = 30) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT count(*) as downloads, userLicenses.trackID, userLicenses.trackName, userLicenses.musicSource
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLicenses
			 INNER JOIN '.system::getConfig()->getDatabase('momusic_content').'.work 
		     ON work.ID = userLicenses.trackID where userLicenses.trackID != 0 group by userLicenses.trackID
			 ORDER by downloads DESC
		';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				//systemLog::message($row);
			//	$oObject = new mofilmUserMusicLicense();
			//	$oObject->loadFromArray($row);
				$valArr = array('downloads' => $row["downloads"],"trackID" => $row["trackID"],"trackName" => $row["trackName"],"musicSource" => $row["musicSource"]);
				$list[] = $valArr;
				
			}
		}
		$oStmt->closeCursor();

		return $list;
	}
	
	
	
	
	/**
	 * Returns an array of objects of mofilmUserMusicLicense based on the userID
	 *
	 * @param integer $inUserID
	 * @return array
	 * @static
	 */
	public static function listOfObjectsByUserID($inUserID) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, trackID, licenseID, userID, trackName, status, expiryDate, musicSource, createDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLicenses';

		$where = array();
		$where[] = ' userID = :userID ';
		$values[':userID'] = $inUserID;
		$query .= ' WHERE '.implode(' AND ', $where);
	
		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUserMusicLicense();
				$oObject->loadFromArray($row);
				$list[(string)$oObject->getLicenseID()] = $oObject;
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
			SELECT ID, trackID, licenseID, userID, trackName, status, expiryDate, musicSource, createDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLicenses';

		$where = array();

		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}

		if ( $this->_LicenseID !== '' ) {
			$where[] = ' licenseID = :LicenseID ';
			$values[':LicenseID'] = $this->getLicenseID();
		}

		if ( $this->_UserID !== 0 ) {
			$where[] = ' userID = :userID ';
			$values[':userID'] = $this->getUserID();			
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
		$this->setTrackID($inArray['trackID']);
		$this->setLicenseID($inArray['licenseID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setTrackName($inArray['trackName']);
		$this->setStatus((int)$inArray['status']);
		$this->setExpiryDate($inArray['expiryDate']);
		$this->setMusicSource($inArray['musicSource']);
		$this->setCreateDate($inArray['createDate']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userLicenses
					( ID, trackID, licenseID, userID, trackName, status, expiryDate, musicSource, createDate )
				VALUES
					( :ID, :trackID, :LicenseID, :UserID, :TrackName, :Status, :ExpiryDate, :MusicSource, :CreateDate )
				ON DUPLICATE KEY UPDATE
					ID=VALUES(ID),
					trackID=VALUES(trackID),
					userID=VALUES(userID),
					trackName=VALUES(trackName),
					status=VALUES(status),
					expiryDate=VALUES(expiryDate),				
					musicSource=VALUES(musicSource), 
					createDate=VALUES(createDate)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':trackID', $this->getTrackID());
				$oStmt->bindValue(':LicenseID', $this->getLicenseID());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':TrackName', $this->getTrackName());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':ExpiryDate', $this->getExpiryDate());
				$oStmt->bindValue(':MusicSource', $this->getMusicSource());
				$oStmt->bindValue(':CreateDate', $this->getCreateDate());


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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLicenses
			WHERE
				licenseID = :LicenseID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':LicenseID', $this->getLicenseID());

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
	 * @return mofilmUserMusicLicense
	 */
	function reset() {
		$this->_ID = 0;
		$this->_LicenseID = '';
		$this->_UserID = 0;
		$this->_TrackName = '';
		$this->_Status = 0;
		$this->_ExpiryDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_MusicSource = null;
		$this->_CreateDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());		
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
	 * @return mofilmUserMusicLicense
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
			'_LicenseID' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_UserID' => array(
				'number' => array(),
			),
			'_Status' => array(
				'number' => array(),
			),
			'_ExpiryDate' => array(
				'dateTime' => array(),
			),
			'_CreateDate' => array(
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
	 * @return mofilmUserMusicLicense
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
		return $this->_LicenseID;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * mofilmUserMusicLicense::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmUserMusicLicense
  	 */
	function setPrimaryKey($inKey) {
		list($licenseID) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setLicenseID($licenseID);
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
	 * @return mofilmUserMusicLicense
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_LicenseID
	 *
	 * @return string
 	 */
	function getLicenseID() {
		return $this->_LicenseID;
	}

	/**
	 * Set the object property _LicenseID to $inLicenseID
	 *
	 * @param string $inLicenseID
	 * @return mofilmUserMusicLicense
	 */
	function setLicenseID($inLicenseID) {
		if ( $inLicenseID !== $this->_LicenseID ) {
			$this->_LicenseID = $inLicenseID;
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
	 * @return mofilmUserMusicLicense
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_TrackID
	 *
	 * @return integer
 	 */
	function getTrackID() {
		return $this->_TrackID;
	}

	/**
	 * Set the object property _UserID to $inTrackID
	 *
	 * @param integer $inTrackID
	 * @return mofilmUserMusicLicense
	 */
	function setTrackID($inTrackID) {
		if ( $inTrackID !== $this->_TrackID ) {
			$this->_TrackID = $inTrackID;
			$this->setModified();
		}
		return $this;
	}
	
	
	/**
	 * Return the current value of the property $_TrackName
	 *
	 * @return string
 	 */
	function getTrackName() {
		return $this->_TrackName;
	}

	/**
	 * Set the object property _TrackName to $inTrackName
	 *
	 * @param string $inTrackName
	 * @return mofilmUserMusicLicense
	 */
	function setTrackName($inTrackName) {
		if ( $inTrackName !== $this->_TrackName ) {
			$this->_TrackName = $inTrackName;
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
	 * @return mofilmUserMusicLicense
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_MusicSource
	 *
	 * @return string
 	 */
	function getMusicSource() {
		return $this->_MusicSource;
	}

	/**
	 * Set the object property _MusicSource to $inStatus
	 *
	 * @param integer $inMusicSource
	 * @return mofilmUserMusicLicense
	 */
	function setMusicSource($inMusicSource) {
		if ( $inMusicSource !== $this->_MusicSource ) {
			$this->_MusicSource = $inMusicSource;
			$this->setModified();
		}
		return $this;
	}

	
	
	/**
	 * Return the current value of the property $_ExpiryDate
	 *
	 * @return systemDateTime
 	 */
	function getExpiryDate() {
		return $this->_ExpiryDate;
	}

	/**
	 * Set the object property _ExpiryDate to $inExpiryDate
	 *
	 * @param systemDateTime $inExpiryDate
	 * @return mofilmUserMusicLicense
	 */
	function setExpiryDate($inExpiryDate) {
		if ( $inExpiryDate !== $this->_ExpiryDate ) {
			if ( !$inExpiryDate instanceof DateTime ) {
				$inExpiryDate = new systemDateTime($inExpiryDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_ExpiryDate = $inExpiryDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_CreateDate
	 *
	 * @return systemDateTime
 	 */
	function getCreateDate() {
		return $this->_CreateDate;
	}

	/**
	 * Set the object property _CreateDate to $inCreateDate
	 *
	 * @param systemDateTime $inCreateDate
	 * @return mofilmUserMusicLicensess
	 */
	function setCreateDate($inCreateDate) {
		if ( $inCreateDate !== $this->_CreateDate ) {
			if ( !$inCreateDate instanceof DateTime ) {
				$inCreateDate = new systemDateTime($inCreateDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_CreateDate = $inCreateDate;
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
	 * @return mofilmUserMusicLicense
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	/**
	 * Checks if the license is valid or expired based on the date
	 * 
	 * @return boolean 
	 */
	function isValidLicense() {
		$diff = (strtotime($this->getExpiryDate()) - time());
		if ( $diff > 0 ) {
			$this->setStatus(self::VALID_LICENSE);
			return true;
		} else {
			$this->setStatus(self::INVALID_LICENSE);
			return false;
		}
	}
}