<?php
/**
 * mofilmUserMovieGrants
 *
 * Stored in mofilmUserMovieGrants.class.php
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserMovieGrants
 * @category mofilmUserMovieGrants
 * @version $Rev: 840 $
 */


/**
 * mofilmUserMovieGrants Class
 *
 * Provides access to records in mofilm_content.userMovieGrants
 *
 * Creating a new record:
 * <code>
 * $oMofilmUserMovieGrant = new mofilmUserMovieGrants();
 * $oMofilmUserMovieGrant->setID($inID);
 * $oMofilmUserMovieGrant->setUserID($inUserID);
 * $oMofilmUserMovieGrant->setGrantID($inGrantID);
 * $oMofilmUserMovieGrant->setMovieID($inMovieID);
 * $oMofilmUserMovieGrant->setFilmConcept($inFilmConcept);
 * $oMofilmUserMovieGrant->setFilmTitle($inFilmTitle);
 * $oMofilmUserMovieGrant->setDuration($inDuration);
 * $oMofilmUserMovieGrant->setUsageOfGrants($inUsageOfGrants);
 * $oMofilmUserMovieGrant->setRequestedAmount($inRequestedAmount);
 * $oMofilmUserMovieGrant->setScript($inScript);
 * $oMofilmUserMovieGrant->setCreated($inCreated);
 * $oMofilmUserMovieGrant->setModerated($inModerated);
 * $oMofilmUserMovieGrant->setModeratorID($inModeratorID);
 * $oMofilmUserMovieGrant->setModeratorComments($inModeratorComments);
 * $oMofilmUserMovieGrant->setMessagesToFilmmaker($inMessagesToFilmmaker);
 * $oMofilmUserMovieGrant->setStatus($inStatus);
 * $oMofilmUserMovieGrant->setGrantedAmount($inGrantedAmount);
 * $oMofilmUserMovieGrant->sethash($inHash);
 * $oMofilmUserMovieGrant->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserMovieGrant = new mofilmUserMovieGrants($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmUserMovieGrant = new mofilmUserMovieGrants();
 * $oMofilmUserMovieGrant->setID($inID);
 * $oMofilmUserMovieGrant->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserMovieGrant = mofilmUserMovieGrants::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmUserMovieGrants
 * @category mofilmUserMovieGrants
 */
class mofilmUserMovieGrants implements systemDaoInterface, systemDaoValidatorInterface {

	const PARAM_SCRIPT_WRITER = 'ScriptWriter';
	const PARAM_PRODUCER = 'Producer';
	const PARAM_DIRECTOR = 'Director';
	const PARAM_TALENT = 'Talent';
	const PARAM_DOP = 'DoP';
	const PARAM_EDITOR = 'Editor';
	const PARAM_TALENT_EXPENSES = 'TalentExpenses';
	const PARAM_PRODUCTION_STAFF = 'ProductionStaff';
	const PARAM_PROPS = 'Props';
	const PARAM_SPECIAL_EFFECTS = 'SpecialEffects';
	const PARAM_WARDROBE = 'Wardrobe';
	const PARAM_HAIR_MAKEUP = 'HairMakeUp';
	const PARAM_CAMERA_RENTAL = 'CameraRental';
	const PARAM_SOUND = 'Sound';
	const PARAM_LIGHTING = 'Lighting';
	const PARAM_TRANSPORTATION = 'Transportation';
	const PARAM_CREW_EXPENSES = 'CrewExpenses';
	const PARAM_LOCATION = 'Location';
	const PARAM_OTHERS = 'Others';
	const PARAM_SHOWREELURL = 'ShowReelURL';
	
	const PARAM_DOCUMENT_AGREEMENT_PATH = 'DocumentAgreementPath';
	const PARAM_DOCUMENT_BANK_DETAILS_PATH = 'DocumentBankDetailsPath';
	const PARAM_DOCUMENT_IDPROOF_PATH = 'DocumentIdProofPath';
	const PARAM_DOCUMENT_RECEIPTS_PATH = 'DocumentReceiptsPath';
	const PARAM_GRANT_ASSETS_PATH = 'GrantAssetsPath';
	
	const PARAM_DOCUMENT_AGREEMENT = 'DocumentAgreement';
	const PARAM_DOCUMENT_BANK_DETAILS = 'DocumentBankDetails';
	const PARAM_DOCUMENT_IDPROOF = 'DocumentIdProof';
	const PARAM_DOCUMENT_RECEIPTS = 'DocumentReceipts';
	
	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmUserMovieGrants
	 *
	 * @var array
	 * @access protected
	 * @static
	 */
	protected static $_Instances = array();

	/**
	 * Stores an instance of baseTableParamSet
	 *
	 * @var baseTableParamSet
	 * @access protected
	 */
	protected $_ParamSet;
	
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
	 * Stores $_GrantID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GrantID;

	/**
	 * Stores $_MovieID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;

	/**
	 * Stores $_FilmConcept
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_FilmConcept;

	/**
	 * Stores $_FilmTitle
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_FilmTitle;

	/**
	 * Stores $_Duration
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Duration;

	/**
	 * Stores $_UsageOfGrants
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_UsageOfGrants;

	/**
	 * Stores $_RequestedAmount
	 *
	 * @var float 
	 * @access protected
	 */
	protected $_RequestedAmount;

	/**
	 * Stores $_Script
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Script;

	/**
	 * Stores $_Created
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_Created;

	/**
	 * Stores $_Moderated
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_Moderated;

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
	 * Stores $_MessagesToFilmmaker
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_MessagesToFilmmaker;

	/**
	 * Stores $_Status
	 *
	 * @var string (STATUS_PENDING,STATUS_APPROVED,STATUS_REJECTED,)
	 * @access protected
	 */
	protected $_Status;
	const STATUS_PENDING = 'Pending';
	const STATUS_APPROVED = 'Approved';
	const STATUS_REJECTED = 'Rejected';

	/**
	 * Stores $_GrantedAmount
	 *
	 * @var float 
	 * @access protected
	 */
	protected $_GrantedAmount;
	
	/**
	 * Stores $_Hash
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Hash;
        
        /**
 
         * @var type        * Stores $_Rating
         * 
         * @var int
         * @access protected
         */
        protected $_Rating;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmUserMovieGrants
	 *
	 * @param integer $inID
	 * @return mofilmUserMovieGrants
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
	 * Get an instance of mofilmUserMovieGrants by primary key
	 *
	 * @param integer $inID
	 * @return mofilmUserMovieGrants
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
		$oObject = new mofilmUserMovieGrants();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmUserMovieGrants
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inUserID = null, $inOffset = null, $inLimit = 30) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT id AS ID, userID,rating, grantID, movieID, filmConcept, filmTitle, duration, usageOfGrants, requestedAmount, script, created, moderated, moderatorID, moderatorComments, messagesToFilmmaker, status, grantedAmount, hash
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants';

		if ( $inUserID !== null ) {
			$query .= ' WHERE userID = '.dbManager::getInstance()->quote($inUserID);
		}

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);

		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUserMovieGrants();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}
	
	/**
	 * Returns an array of objects of mofilmUserMovieGrants
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function userMovieGrantsObjectByMovieID($inMovieID = null) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT id AS ID, userID,rating, grantID, movieID, filmConcept, filmTitle, duration, usageOfGrants, requestedAmount, script, created, moderated, moderatorID, moderatorComments, messagesToFilmmaker, status, grantedAmount, hash
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants';

		if ( $inMovieID !== null ) {
			$query .= ' WHERE movieID = '.dbManager::getInstance()->quote($inMovieID);
		}

		$res = array();

		$oStmt = dbManager::getInstance()->prepare($query);

		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUserMovieGrants();
				$oObject->loadFromArray($row);
				$res = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $res;
	}

	/**
	 * Returns an array of objects of mofilmUserMovieGrants
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function userMovieGrantsObject($inUserID = null, $inSourceID = null, $inStatus = null) {
		 
		 if ( $inUserID && $inSourceID ) {

			$query = '
				SELECT userMovieGrants.id AS ID, grantID,rating, userID, movieID, filmConcept, filmTitle, duration, usageOfGrants, requestedAmount, script, created, moderated, moderatorID, moderatorComments, messagesToFilmmaker, status, grantedAmount, hash
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants';
			
			$query .= ' INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.grants ON (grantID = grants.ID)';

			$query .= ' WHERE userID = '.dbManager::getInstance()->quote($inUserID);
			
			$query .= ' AND grants.sourceID = '.dbManager::getInstance()->quote($inSourceID);

			if ( $inStatus ) {
				$query .= ' AND status = '.dbManager::getInstance()->quote($inStatus);
			}

			$oStmt = dbManager::getInstance()->prepare($query);

			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserMovieGrants();
					$oObject->loadFromArray($row);
				}
			}
			$oStmt->closeCursor();

			return $oObject;
			
		 } else {
			return false;
		 }
	}
	
	/**
	 * Returns an integer of total grants disbursed
	 *
	 * @param integer $inSourceID
	 * @return integer
	 * @static
	 */
	public static function totalGrantsDisbursed($inSourceID) {

		$query = 'SELECT sum(grantedAmount) FROM '.
			    system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants INNER JOIN mofilm_content.grants ON (grantID = grants.ID) '.
			    'WHERE  grants.sourceID IN ('.dbManager::getInstance()->quote($inSourceID).') AND  status in '.
			    '( '.dbManager::getInstance()->quote(mofilmUserMovieGrants::STATUS_APPROVED).' )';
			$oStmt = dbManager::getInstance()->prepare($query);

			if ( $oStmt->execute() ) {
				$row = $oStmt->fetch();
				if ( $row !== false && is_array($row) ) {
					return $row[0];
				}
			}
			$oStmt->closeCursor();
	
		return 0;
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
			SELECT id AS ID, userID,rating, grantID, movieID, filmConcept, filmTitle, duration, usageOfGrants, requestedAmount, script, created, moderated, moderatorID, moderatorComments, messagesToFilmmaker, status, grantedAmount, hash
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' id = :ID ';
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
		$this->setGrantID((int)$inArray['grantID']);
		$this->setMovieID((int)$inArray['movieID']);
		$this->setFilmConcept($inArray['filmConcept']);
		$this->setFilmTitle($inArray['filmTitle']);
		$this->setDuration((int)$inArray['duration']);
                $this->setRating((int)$inArray["rating"]);
		$this->setUsageOfGrants($inArray['usageOfGrants']);
		$this->setRequestedAmount($inArray['requestedAmount']);
		$this->setScript($inArray['script']);
		$this->setCreated($inArray['created']);
		$this->setModerated($inArray['moderated']);
		$this->setModeratorID((int)$inArray['moderatorID']);
		$this->setModeratorComments($inArray['moderatorComments']);
		$this->setMessagesToFilmmaker($inArray['messagesToFilmmaker']);
		$this->setStatus($inArray['status']);
		$this->setGrantedAmount($inArray['grantedAmount']);
		$this->setHash($inArray['hash']);
		$this->setModified(false);
	}

	/**
	 * Saves object to the table
	 *
	 * @return boolean
	 */
	function save() {
	    systemLog::message('--insave');
		$return = false;
		if ( $this->isModified() ) {
			$message = '';
			if ( !$this->isValid($message) ) {
				throw new mofilmException($message);
			}

			if ( $this->_Modified ) {	
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants
					( id, userID, grantID, movieID, filmConcept, filmTitle, duration, usageOfGrants, requestedAmount, script, created, moderated, moderatorID, moderatorComments, messagesToFilmmaker, status, grantedAmount, hash, rating)
				VALUES
					( :ID, :UserID, :GrantID, :MovieID, :FilmConcept, :FilmTitle, :Duration, :UsageOfGrants, :RequestedAmount, :Script, :Created, :Moderated, :ModeratorID, :ModeratorComments, :MessagesToFilmmaker, :Status, :GrantedAmount, :Hash, :rating )
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					grantID=VALUES(grantID),
					movieID=VALUES(movieID),
					filmConcept=VALUES(filmConcept),
					filmTitle=VALUES(filmTitle),
					duration=VALUES(duration),
					usageOfGrants=VALUES(usageOfGrants),
					requestedAmount=VALUES(requestedAmount),
					script=VALUES(script),
					created=VALUES(created),
					moderated=VALUES(moderated),
					moderatorID=VALUES(moderatorID),
					moderatorComments=VALUES(moderatorComments),
					messagesToFilmmaker=VALUES(messagesToFilmmaker),
					status=VALUES(status),
					grantedAmount=VALUES(grantedAmount),
					hash=VALUES(hash),
                                        rating=VALUES(rating)
                                        ';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':GrantID', $this->getGrantID());
				$oStmt->bindValue(':MovieID', $this->getMovieID());
				$oStmt->bindValue(':FilmConcept', $this->getFilmConcept());
				$oStmt->bindValue(':FilmTitle', $this->getFilmTitle());
				$oStmt->bindValue(':Duration', $this->getDuration());
				$oStmt->bindValue(':UsageOfGrants', $this->getUsageOfGrants());
				$oStmt->bindValue(':RequestedAmount', $this->getRequestedAmount());
				$oStmt->bindValue(':Script', $this->getScript());
				$oStmt->bindValue(':Created', $this->getCreated());
				$oStmt->bindValue(':Moderated', $this->getModerated());
				$oStmt->bindValue(':ModeratorID', $this->getModeratorID());
				$oStmt->bindValue(':ModeratorComments', $this->getModeratorComments());
				$oStmt->bindValue(':MessagesToFilmmaker', $this->getMessagesToFilmmaker());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':GrantedAmount', $this->getGrantedAmount());
				$oStmt->bindValue(':Hash', $this->getHash());
                                $oStmt->bindValue(':rating', $this->getRating());

				if ( $oStmt->execute() ) {
					if ( !$this->getID() ) {
						$this->setID($oDB->lastInsertId());
					}
					$this->setModified(false);
					$return = true;
				}
				
				if ( $this->_ParamSet instanceof baseTableParamSet ) {
				    $this->_ParamSet->setIndexID($this->getID());
				    $this->_ParamSet->save();
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants
			WHERE
				id = :ID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':ID', $this->getID());

		if ( $oStmt->execute() ) {
			$oStmt->closeCursor();
			$this->reset();
			$this->getParamSet()->deleteAll();
			return true;
		}

		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mofilmUserMovieGrants
	 */
	function reset() {
		$this->_ID = 0;
		$this->_UserID = 0;
		$this->_GrantID = 0;
		$this->_MovieID = null;
		$this->_FilmConcept = '';
		$this->_FilmTitle = '';
		$this->_Duration = null;
		$this->_UsageOfGrants = null;
		$this->_RequestedAmount = 0.00;
		$this->_Script = '';
		$this->_Created = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Moderated = null;
		$this->_ModeratorID = null;
		$this->_ModeratorComments = null;
		$this->_MessagesToFilmmaker = null;
		$this->_Status = null;
		$this->_GrantedAmount = null;
		$this->_Hash = null;
                $this->_Rating = 0;
		$this->_Validator = null;
		$this->_ParamSet = null;
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
	 * @return mofilmUserMovieGrants
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
			'_GrantID' => array(
				'number' => array(),
			),
			'_FilmConcept' => array(
				'string' => array(),
			),
			'_FilmTitle' => array(
				'string' => array(),
			),
			'_UsageOfGrants' => array(
				'string' => array(),
			),
			'_RequestedAmount' => array(
				'float' => array(),
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

		if ( !$modified && $this->_ParamSet instanceof baseTableParamSet ) {
			$modified = $this->_ParamSet->isModified() || $modified;
		}
		
		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmUserMovieGrants
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
	 * mofilmUserMovieGrants::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmUserMovieGrants
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
	 * @return mofilmUserMovieGrants
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
	 * @return mofilmUserMovieGrants
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_GrantID
	 *
	 * @return integer
 	 */
	function getGrantID() {
		return $this->_GrantID;
	}

	/**
	 * Set the object property _GrantID to $inGrantID
	 *
	 * @param integer $inGrantID
	 * @return mofilmUserMovieGrants
	 */
	function setGrantID($inGrantID) {
		if ( $inGrantID !== $this->_GrantID ) {
			$this->_GrantID = $inGrantID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the Mofilm Grants Object
	 * 
	 * @return mofilmGrant
	 */
	function getGrants(){
	    return mofilmGrants::getInstance($this->getGrantID());
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
	 * @return mofilmUserMovieGrants
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the Mofilm Movie Object
	 * 
	 * @return mofilmMovie
	 */
	function getMovie(){
		return mofilmMovieManager::getInstanceByID($this->getMovieID());
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
	 * Return the current value of the property $_FilmConcept
	 *
	 * @return string
 	 */
	function getFilmConcept() {
		return $this->_FilmConcept;
	}

	/**
	 * Set the object property _FilmConcept to $inFilmConcept
	 *
	 * @param string $inFilmConcept
	 * @return mofilmUserMovieGrants
	 */
	function setFilmConcept($inFilmConcept) {
		if ( $inFilmConcept !== $this->_FilmConcept ) {
			$this->_FilmConcept = $inFilmConcept;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_FilmTitle
	 *
	 * @return string
 	 */
	function getFilmTitle() {
		return $this->_FilmTitle;
	}

	/**
	 * Set the object property _FilmTitle to $inFilmTitle
	 *
	 * @param string $inFilmTitle
	 * @return mofilmUserMovieGrants
	 */
	function setFilmTitle($inFilmTitle) {
		if ( $inFilmTitle !== $this->_FilmTitle ) {
			$this->_FilmTitle = $inFilmTitle;
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
	 * @return mofilmUserMovieGrants
	 */
	function setDuration($inDuration) {
		if ( $inDuration !== $this->_Duration ) {
			$this->_Duration = $inDuration;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_UsageOfGrants
	 *
	 * @return string
 	 */
	function getUsageOfGrants() {
		return $this->_UsageOfGrants;
	}

	/**
	 * Set the object property _UsageOfGrants to $inUsageOfGrants
	 *
	 * @param string $inUsageOfGrants
	 * @return mofilmUserMovieGrants
	 */
	function setUsageOfGrants($inUsageOfGrants) {
		if ( $inUsageOfGrants !== $this->_UsageOfGrants ) {
			$this->_UsageOfGrants = $inUsageOfGrants;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_RequestedAmount
	 *
	 * @return float
 	 */
	function getRequestedAmount() {
		return $this->_RequestedAmount;
	}

	/**
	 * Set the object property _RequestedAmount to $inRequestedAmount
	 *
	 * @param float $inRequestedAmount
	 * @return mofilmUserMovieGrants
	 */
	function setRequestedAmount($inRequestedAmount) {
		if ( $inRequestedAmount !== $this->_RequestedAmount ) {
			$this->_RequestedAmount = floatval($inRequestedAmount);
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Script
	 *
	 * @return string
 	 */
	function getScript() {
		return $this->_Script;
	}

	/**
	 * Set the object property _Script to $inScript
	 *
	 * @param string $inScript
	 * @return mofilmUserMovieGrants
	 */
	function setScript($inScript) {
		if ( $inScript !== $this->_Script ) {
			$this->_Script = $inScript;
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
	 * @return mofilmUserMovieGrants
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
	 * @return mofilmUserMovieGrants
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
	 * @return mofilmUserMovieGrants
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
	 * @return mofilmUserMovieGrants
	 */
	function setModeratorComments($inModeratorComments) {
		if ( $inModeratorComments !== $this->_ModeratorComments ) {
			$this->_ModeratorComments = $inModeratorComments;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return the current value of the property $_MessagesToFilmmaker
	 *
	 * @return string
 	 */
	function getMessagesToFilmmaker() {
		return $this->_MessagesToFilmmaker;
	}

	/**
	 * Set the object property _MessagesToFilmmaker to $inMessagesToFilmmaker
	 *
	 * @param string $inMessagesToFilmmaker
	 * @return mofilmUserMovieGrants
	 */
	function setMessagesToFilmmaker($inMessagesToFilmmaker) {
		if ( $inMessagesToFilmmaker !== $this->_MessagesToFilmmaker ) {
			$this->_MessagesToFilmmaker = $inMessagesToFilmmaker;
			$this->setModified();
		}
		return $this;
	}	
	
	/**
	 * Returns the Moderator user object
	 * 
	 * @return mofilmUser
	 */
	function getModerator(){
	    if ( $this->getModeratorID() > 0 ) {
		    return mofilmUserManager::getInstanceByID($this->getModeratorID());
	    }
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
	 * @return mofilmUserMovieGrants
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_GrantedAmount
	 *
	 * @return float
 	 */
	function getGrantedAmount() {
		return $this->_GrantedAmount;
	}

	/**
	 * Set the object property _GrantedAmount to $inGrantedAmount
	 *
	 * @param float $inGrantedAmount
	 * @return mofilmUserMovieGrants
	 */
	function setGrantedAmount($inGrantedAmount) {
		if ( $inGrantedAmount !== $this->_GrantedAmount ) {
			$this->_GrantedAmount = $inGrantedAmount;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return the current value of the property $_Hash
	 *
	 * @return float
 	 */
	function getHash() {
		return $this->_Hash;
	}

	/**
	 * Set the object property _Hash to $inHash
	 *
	 * @param string $inHash
	 * @return mofilmUserMovieGrants
	 */
	function setHash($inHash) {
		if ( $inHash !== $this->_Hash ) {
			$this->_Hash = $inHash;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Rating
	 *
	 * @return float
 	 */
	function getRating() {
		return $this->_Rating;
	}

	/**
	 * Set the object property _Rating to $inRating
	 *
	 * @param string $inRating
	 * @return mofilmUserMovieGrants
	 */
	function setRating($inRating) {
		if ( $inRating !== $this->_Rating ) {
			$this->_Rating = $inRating;
			$this->setModified();
		}
		return $this;
	}
        
        
	/**
	 * Returns the parameters object, loading it if not already loaded
	 *
	 * @return baseTableParamSet
	 */
	function getParamSet() {
		if ( !$this->_ParamSet instanceof baseTableParamSet ) {
			$this->_ParamSet = new baseTableParamSet(
				system::getConfig()->getDatabase('mofilm_content'), 'userMovieGrantsData', 'userMovieGrantsID', 'paramName', 'paramValue', $this->getID(), false
			);
		}
		return $this->_ParamSet;
	}

	/**
	 * Sets an pre-built param set to the object
	 *
	 * @param baseTableParamSet $inParamSet
	 * @return mofilmUser
	 */
	function setParamSet(baseTableParamSet $inParamSet) {
		$this->_ParamSet = $inParamSet;
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
	 * @return mofilmUserMovieGrants
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	/**
	 * Returns true if applied before deadline else returns false
	 * 
	 * @return boolean
	 */
	function getApplicationAppliedStatus() {
		$applied_date = $this->getCreated();
		$deadline = $this->getGrants()->getEndDate();
		
		if ( $deadline > $applied_date ) {
		    return true;
		} else {
		    return false;
		}
	}

	/**
	 * Returns true if file exists else returns false
	 * 
	 * @return boolean
	 */
	function isFileExists($key=null) {
		$filename = $this->getParamSet()->getParam($key);
		if ( file_exists($filename) && is_readable($filename) ) {
			return true;
		}
		return false;
	}
	
        function editGrantsStatus($GID) {

        $query = 'UPDATE ' . system::getConfig()->getDatabase('mofilm_content') . '.userMovieGrants
			 SET  Status = "Rejected"
                         WHERE
                            id = '.$GID;
        $oStmt = dbManager::getInstance()->prepare($query);
        
        if ($oStmt->execute()) 
        {
            $oStmt->closeCursor();        
            return true;
        }

        return false;
    }

	/**
	 * Returns an array of available userMovieGrantsStatus
	 *
	 * @return array
	 * @static
	 */
	static function getAvailableGrantsStatus() {
		return array(
			self::STATUS_APPROVED,
			self::STATUS_PENDING,
			self::STATUS_REJECTED,
		);
	}
}