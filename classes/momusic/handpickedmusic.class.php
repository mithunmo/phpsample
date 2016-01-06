<?php
/**
 * momusicHandpickedmusic
 *
 * Stored in momusicHandpickedmusic.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package momusic
 * @subpackage momusicHandpickedmusic
 * @category momusicHandpickedmusic
 * @version $Rev: 840 $
 */


/**
 * momusicHandpickedmusic Class
 *
 * Provides access to records in momusic_content.handpickedMusic
 *
 * Creating a new record:
 * <code>
 * $oMomusicHandpickedmusic = new momusicHandpickedmusic();
 * $oMomusicHandpickedmusic->setID($inID);
 * $oMomusicHandpickedmusic->setCoverImageID($inCoverImageID);
 * $oMomusicHandpickedmusic->setTrackID($inTrackID);
 * $oMomusicHandpickedmusic->setStatus($inStatus);
 * $oMomusicHandpickedmusic->setCreateDate($inCreateDate);
 * $oMomusicHandpickedmusic->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMomusicHandpickedmusic = new momusicHandpickedmusic($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMomusicHandpickedmusic = new momusicHandpickedmusic();
 * $oMomusicHandpickedmusic->setID($inID);
 * $oMomusicHandpickedmusic->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMomusicHandpickedmusic = momusicHandpickedmusic::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package momusic
 * @subpackage momusicHandpickedmusic
 * @category momusicHandpickedmusic
 */
class momusicHandpickedmusic implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of momusicHandpickedmusic
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
	 * Stores $_CoverImageID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_CoverImageID;

	/**
	 * Stores $_TrackID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_TrackID;

	/**
	 * Stores $_Status
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Status;

	/**
	 * Stores $_Rank
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Rank;

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



	/**
	 * Returns a new instance of momusicHandpickedmusic
	 *
	 * @param integer $inID
	 * @return momusicHandpickedmusic
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
	 * Get an instance of momusicHandpickedmusic by primary key
	 *
	 * @param integer $inID
	 * @return momusicHandpickedmusic
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
		$oObject = new momusicHandpickedmusic();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of momusicHandpickedmusic
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
			SELECT ID, coverImageID, trackID, status, rank, createDate
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.handpickedMusic
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicHandpickedmusic();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

	/**
	 * Returns an array of objects of momusicHandpickedmusic
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjectsByRank() {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, coverImageID, trackID, status, rank, createDate
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.handpickedMusic
			 ORDER BY rank desc';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicHandpickedmusic();
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
			SELECT ID, coverImageID, trackID, status, rank, createDate
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.handpickedMusic';

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
		$this->setCoverImageID((int)$inArray['coverImageID']);
		$this->setTrackID((int)$inArray['trackID']);
		$this->setStatus((int)$inArray['status']);
		$this->setRank((int)$inArray['rank']);
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
				throw new momusicException($message);
			}

			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('momusic_content').'.handpickedMusic
					( ID, coverImageID, trackID, status, rank, createDate )
				VALUES
					( :ID, :CoverImageID, :TrackID, :Status, :Rank, :CreateDate )
				ON DUPLICATE KEY UPDATE
					coverImageID=VALUES(coverImageID),
					trackID=VALUES(trackID),
					status=VALUES(status),
					rank=VALUES(rank),
					createDate=VALUES(createDate)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':CoverImageID', $this->getCoverImageID());
				$oStmt->bindValue(':TrackID', $this->getTrackID());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':Rank', $this->getRank());
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
			DELETE FROM '.system::getConfig()->getDatabase('momusic_content').'.handpickedMusic
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
	 * @return momusicHandpickedmusic
	 */
	function reset() {
		$this->_ID = 0;
		$this->_CoverImageID = 0;
		$this->_TrackID = 0;
		$this->_Status = 0;
		$this->_Rank = 0;
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
	 * @return momusicHandpickedmusic
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
			'_CoverImageID' => array(
				'number' => array(),
			),
			'_TrackID' => array(
				'number' => array(),
			),
			'_Status' => array(
				'number' => array(),
			),
			'_Rank' => array(
				'number' => array(),
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
	 * @return momusicHandpickedmusic
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
	 * momusicHandpickedmusic::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return momusicHandpickedmusic
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
	 * @return momusicHandpickedmusic
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_CoverImageID
	 *
	 * @return integer
 	 */
	function getCoverImageID() {
		return $this->_CoverImageID;
	}

	/**
	 * Set the object property _CoverImageID to $inCoverImageID
	 *
	 * @param integer $inCoverImageID
	 * @return momusicHandpickedmusic
	 */
	function setCoverImageID($inCoverImageID) {
		if ( $inCoverImageID !== $this->_CoverImageID ) {
			$this->_CoverImageID = $inCoverImageID;
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
	 * Set the object property _TrackID to $inTrackID
	 *
	 * @param integer $inTrackID
	 * @return momusicHandpickedmusic
	 */
	function setTrackID($inTrackID) {
		if ( $inTrackID !== $this->_TrackID ) {
			$this->_TrackID = $inTrackID;
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
	 * @return momusicHandpickedmusic
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Rank
	 *
	 * @return integer
 	 */
	function getRank() {
		return $this->_Rank;
	}

	/**
	 * Set the object property _Rank to $inRank
	 *
	 * @param integer $inRank
	 * @return momusichm
	 */
	function setRank($inRank) {
		if ( $inRank !== $this->_Rank ) {
			$this->_Rank = $inRank;
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
	 * @return momusicHandpickedmusic
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
	 * @return momusicHandpickedmusic
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}