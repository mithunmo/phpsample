<?php
/**
 * mofilmMovieChannel
 *
 * Stored in mofilmMovieChannel.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMovieChannel
 * @category mofilmMovieChannel
 * @version $Rev: 840 $
 */


/**
 * mofilmMovieChannel Class
 *
 * Provides access to records in mofilm_content.movieChannel
 *
 * Creating a new record:
 * <code>
 * $oMofilmMovieChannel = new mofilmMovieChannel();
 * $oMofilmMovieChannel->setID($inID);
 * $oMofilmMovieChannel->setMovieID($inMovieID);
 * $oMofilmMovieChannel->setChannelID($inChannelID);
 * $oMofilmMovieChannel->setDistributionID($inDistributionID);
 * $oMofilmMovieChannel->setWidth($inWidth);
 * $oMofilmMovieChannel->setHeight($inHeight);
 * $oMofilmMovieChannel->setBitrate($inBitrate);
 * $oMofilmMovieChannel->setDuration($inDuration);
 * $oMofilmMovieChannel->setSize($inSize);
 * $oMofilmMovieChannel->setStatus($inStatus);
 * $oMofilmMovieChannel->setCreateDate($inCreateDate);
 * $oMofilmMovieChannel->setAction($inAction);
 * $oMofilmMovieChannel->setCategory($inCategory);
 * $oMofilmMovieChannel->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMovieChannel = new mofilmMovieChannel($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmMovieChannel = new mofilmMovieChannel();
 * $oMofilmMovieChannel->setID($inID);
 * $oMofilmMovieChannel->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmMovieChannel = mofilmMovieChannel::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmMovieChannel
 * @category mofilmMovieChannel
 */
class mofilmMovieChannel implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmMovieChannel
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
	 * Stores $_ChannelID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ChannelID;

	/**
	 * Stores $_DistributionID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_DistributionID;

	/**
	 * Stores $_Width
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Width;

	/**
	 * Stores $_Height
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Height;

	/**
	 * Stores $_Bitrate
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Bitrate;

	/**
	 * Stores $_Duration
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Duration;

	/**
	 * Stores $_Size
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Size;

	/**
	 * Stores $_Status
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Status;

	/**
	 * Stores $_CreateDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_CreateDate;

	/**
	 * Stores $_Action
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Action;

	/**
	 * Stores $_Category
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Category;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmMovieChannel
	 *
	 * @param integer $inID
	 * @return mofilmMovieChannel
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
	 * Get an instance of mofilmMovieChannel by primary key
	 *
	 * @param integer $inID
	 * @return mofilmMovieChannel
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
		$oObject = new mofilmMovieChannel();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get instance of mofilmMovieChannel by unique key (movieID)
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieChannel
	 * @static
	 */
	public static function getInstanceByMovieID($inMovieID) {
		$key = $inMovieID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmMovieChannel();
		$oObject->setMovieID($inMovieID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmMovieChannel
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
			SELECT ID, movieID, channelID, distributionID, width, height, bitrate, duration, size, status, createDate, action, category
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieChannel
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmMovieChannel();
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
			SELECT ID, movieID, channelID, distributionID, width, height, bitrate, duration, size, status, createDate, action, category
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieChannel';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}
		if ( $this->_MovieID !== 0 ) {
			$where[] = ' movieID = :MovieID ';
			$values[':MovieID'] = $this->getMovieID();
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
		$this->setChannelID((int)$inArray['channelID']);
		$this->setDistributionID((int)$inArray['distributionID']);
		$this->setWidth((int)$inArray['width']);
		$this->setHeight((int)$inArray['height']);
		$this->setBitrate((int)$inArray['bitrate']);
		$this->setDuration((int)$inArray['duration']);
		$this->setSize((int)$inArray['size']);
		$this->setStatus((int)$inArray['status']);
		$this->setCreateDate($inArray['createDate']);
		$this->setAction($inArray['action']);
		$this->setCategory($inArray['category']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieChannel
					( ID, movieID, channelID, distributionID, width, height, bitrate, duration, size, status, createDate, action, category )
				VALUES
					( :ID, :MovieID, :ChannelID, :DistributionID, :Width, :Height, :Bitrate, :Duration, :Size, :Status, :CreateDate, :Action, :Category )
				ON DUPLICATE KEY UPDATE
					channelID=VALUES(channelID),
					distributionID=VALUES(distributionID),
					width=VALUES(width),
					height=VALUES(height),
					bitrate=VALUES(bitrate),
					duration=VALUES(duration),
					size=VALUES(size),
					status=VALUES(status),
					createDate=VALUES(createDate),
					action=VALUES(action),
					category=VALUES(category)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':MovieID', $this->getMovieID());
				$oStmt->bindValue(':ChannelID', $this->getChannelID());
				$oStmt->bindValue(':DistributionID', $this->getDistributionID());
				$oStmt->bindValue(':Width', $this->getWidth());
				$oStmt->bindValue(':Height', $this->getHeight());
				$oStmt->bindValue(':Bitrate', $this->getBitrate());
				$oStmt->bindValue(':Duration', $this->getDuration());
				$oStmt->bindValue(':Size', $this->getSize());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':CreateDate', $this->getCreateDate());
				$oStmt->bindValue(':Action', $this->getAction());
				$oStmt->bindValue(':Category', $this->getCategory());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieChannel
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
	 * @return mofilmMovieChannel
	 */
	function reset() {
		$this->_ID = 0;
		$this->_MovieID = 0;
		$this->_ChannelID = 0;
		$this->_DistributionID = 0;
		$this->_Width = 0;
		$this->_Height = 0;
		$this->_Bitrate = 0;
		$this->_Duration = 0;
		$this->_Size = 0;
		$this->_Status = 0;
		$this->_CreateDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Action = '';
		$this->_Category = '';
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
	 * @return mofilmMovieChannel
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
			'_ChannelID' => array(
				'number' => array(),
			),
			'_DistributionID' => array(
				'number' => array(),
			),
			'_Width' => array(
				'number' => array(),
			),
			'_Height' => array(
				'number' => array(),
			),
			'_Bitrate' => array(
				'number' => array(),
			),
			'_Duration' => array(
				'number' => array(),
			),
			'_Size' => array(
				'number' => array(),
			),
			'_Status' => array(
				'number' => array(),
			),
			'_CreateDate' => array(
				'dateTime' => array(),
			),
			'_Action' => array(
				'string' => array(),
			),
			'_Category' => array(
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
	 * @return mofilmMovieChannel
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
	 * mofilmMovieChannel::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmMovieChannel
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
	 * @return mofilmMovieChannel
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
	 * @return mofilmMovieChannel
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ChannelID
	 *
	 * @return integer
 	 */
	function getChannelID() {
		return $this->_ChannelID;
	}

	/**
	 * Set the object property _ChannelID to $inChannelID
	 *
	 * @param integer $inChannelID
	 * @return mofilmMovieChannel
	 */
	function setChannelID($inChannelID) {
		if ( $inChannelID !== $this->_ChannelID ) {
			$this->_ChannelID = $inChannelID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_DistributionID
	 *
	 * @return integer
 	 */
	function getDistributionID() {
		return $this->_DistributionID;
	}

	/**
	 * Set the object property _DistributionID to $inDistributionID
	 *
	 * @param integer $inDistributionID
	 * @return mofilmMovieChannel
	 */
	function setDistributionID($inDistributionID) {
		if ( $inDistributionID !== $this->_DistributionID ) {
			$this->_DistributionID = $inDistributionID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Width
	 *
	 * @return integer
 	 */
	function getWidth() {
		return $this->_Width;
	}

	/**
	 * Set the object property _Width to $inWidth
	 *
	 * @param integer $inWidth
	 * @return mofilmMovieChannel
	 */
	function setWidth($inWidth) {
		if ( $inWidth !== $this->_Width ) {
			$this->_Width = $inWidth;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Height
	 *
	 * @return integer
 	 */
	function getHeight() {
		return $this->_Height;
	}

	/**
	 * Set the object property _Height to $inHeight
	 *
	 * @param integer $inHeight
	 * @return mofilmMovieChannel
	 */
	function setHeight($inHeight) {
		if ( $inHeight !== $this->_Height ) {
			$this->_Height = $inHeight;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Bitrate
	 *
	 * @return integer
 	 */
	function getBitrate() {
		return $this->_Bitrate;
	}

	/**
	 * Set the object property _Bitrate to $inBitrate
	 *
	 * @param integer $inBitrate
	 * @return mofilmMovieChannel
	 */
	function setBitrate($inBitrate) {
		if ( $inBitrate !== $this->_Bitrate ) {
			$this->_Bitrate = $inBitrate;
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
	 * @return mofilmMovieChannel
	 */
	function setDuration($inDuration) {
		if ( $inDuration !== $this->_Duration ) {
			$this->_Duration = $inDuration;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Size
	 *
	 * @return integer
 	 */
	function getSize() {
		return $this->_Size;
	}

	/**
	 * Set the object property _Size to $inSize
	 *
	 * @param integer $inSize
	 * @return mofilmMovieChannel
	 */
	function setSize($inSize) {
		if ( $inSize !== $this->_Size ) {
			$this->_Size = $inSize;
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
	 * @return mofilmMovieChannel
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
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
	 * @return mofilmMovieChannel
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
	 * Return the current value of the property $_Action
	 *
	 * @return string
 	 */
	function getAction() {
		return $this->_Action;
	}

	/**
	 * Set the object property _Action to $inAction
	 *
	 * @param string $inAction
	 * @return mofilmMovieChannel
	 */
	function setAction($inAction) {
		if ( $inAction !== $this->_Action ) {
			$this->_Action = $inAction;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Category
	 *
	 * @return string
 	 */
	function getCategory() {
		return $this->_Category;
	}

	/**
	 * Set the object property _Category to $inCategory
	 *
	 * @param string $inCategory
	 * @return mofilmMovieChannel
	 */
	function setCategory($inCategory) {
		if ( $inCategory !== $this->_Category ) {
			$this->_Category = $inCategory;
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
	 * @return mofilmMovieChannel
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}