<?php
/**
 * mofilmUserPoints
 *
 * Stored in mofilmUserPoints.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserPoints
 * @category mofilmUserPoints
 * @version $Rev: 806 $
 */


/**
 * mofilmUserPoints Class
 *
 * Provides access to records in mofilm_content.userPoints
 *
 * Creating a new record:
 * <code>
 * $oMofilmUserPoint = new mofilmUserPoints();
 * $oMofilmUserPoint->setId($inId);
 * $oMofilmUserPoint->setUserID($inUserID);
 * $oMofilmUserPoint->setScore($inScore);
 * $oMofilmUserPoint->setCreateDate($inCreateDate);
 * $oMofilmUserPoint->setUpdateDate($inUpdateDate);
 * $oMofilmUserPoint->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserPoint = new mofilmUserPoints($inId);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmUserPoint = new mofilmUserPoints();
 * $oMofilmUserPoint->setId($inId);
 * $oMofilmUserPoint->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserPoint = mofilmUserPoints::getInstance($inId);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmUserPoints
 * @category mofilmUserPoints
 */
class mofilmUserPoints implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmUserPoints
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
	 * Stores $_Id
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Id;

	/**
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_Score
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Score;

	/**
	 * Stores $_HighScore
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_HighScore;

	/**
	 * Stores $_Position
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Position;

	/**
	 * Stores $_PositionAllTime
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_PositionAllTime;

	/**
	 * Stores $_TotalUsers
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TotalUsers;

	/**
	 * Stores $_CreateDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_CreateDate;

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
	 * Returns a new instance of mofilmUserPoints
	 *
	 * @param integer $inId
	 * @return mofilmUserPoints
	 */
	function __construct($inId = null) {
		$this->reset();
		if ( $inId !== null ) {
			$this->setId($inId);
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
	 * Get an instance of mofilmUserPoints by primary key
	 *
	 * @param integer $inId
	 * @return mofilmUserPoints
	 * @static
	 */
	public static function getInstance($inId) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inId]) ) {
			return self::$_Instances[$inId];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserPoints();
		$oObject->setId($inId);
		if ( $oObject->load() ) {
			self::$_Instances[$inId] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get instance of mofilmUserPoints by unique key (userID)
	 *
	 * @param integer $inUserID
	 * @return mofilmUserPoints
	 * @static
	 */
	public static function getInstanceByUserID($inUserID) {
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserPoints();
		$oObject->setUserID($inUserID);
		$oObject->load();

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmUserPoints
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
			SELECT id, userID, score, highScore, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPoints
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUserPoints();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

	/**
	 * Loads an array of mofilmUser objects with mofilmUserPoint objects
	 *
	 * @param array $inUsers
	 * @return boolean
	 * @static
	 */
	public static function loadArrayOfUsersWithProperties(array $inUsers) {
		$return = false;
		$properties = array();
		if ( count($inUsers) > 0 ) {
			$userIDs = implode(', ', array_keys($inUsers));

			$query = '
				SELECT userPoints.*,
				       (SELECT COUNT(*) FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPoints) AS totalUsers,
				       (
				        SELECT position
					      FROM (
						        SELECT userID, @i:=@i+1 AS position
							      FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPoints
							     ORDER BY score DESC
						       ) t
					     WHERE userID = userPoints.userID
				       ) AS position,
				       (
				        SELECT position
					      FROM (
						        SELECT userID, @j:=@j+1 AS position
							      FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPoints
							     ORDER BY highScore DESC
						       ) t
					     WHERE userID = userPoints.userID
				       ) AS positionAllTime
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPoints
				 WHERE userID IN ('.$userIDs.')';

			$oDb = dbManager::getInstance();
			$oDb->exec('SET @i := 0, @j := 0');
			$oStmt = $oDb->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$properties[$row['userID']] = $row;
				}
			}
			$oStmt->closeCursor();

			if ( false ) $oUser = new mofilmUser();
			foreach ( $inUsers as $oUser ) {
				if ( $oUser instanceof mofilmUser ) {
					if ( array_key_exists($oUser->getID(), $properties) ) {
						$oObject = new mofilmUserPoints();
						$oObject->loadFromArray($properties[$oUser->getID()]);

						$oUser->setPoints($oObject);
						$return = true;
					}
				}
			}
		}

		return $return;
	}



	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;

		$query = '
			SELECT id, userID, score, highScore, createDate, updateDate,
			      (SELECT COUNT(*) FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPoints) AS totalUsers,
			      (
			       SELECT position
			         FROM (
			               SELECT userID, @i:=@i+1 AS position
			                 FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPoints
			                ORDER BY score DESC
			              ) t
			        WHERE userID = userPoints.userID
			      ) AS position,
			      (
			       SELECT position
			         FROM (
			               SELECT userID, @j:=@j+1 AS position
			                 FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPoints
			                ORDER BY highScore DESC
			              ) t
			        WHERE userID = userPoints.userID
			      ) AS positionAllTime
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPoints';

		$where = array();
		if ( $this->_Id !== 0 ) {
			$where[] = ' id = :Id ';
		}
		if ( $this->_UserID !== 0 ) {
			$where[] = ' userID = :UserID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oDb = dbManager::getInstance();
		$oDb->exec('SET @i := 0, @j := 0');
		$oStmt = $oDb->prepare($query);
		if ( $this->_Id !== 0 ) {
			$oStmt->bindValue(':Id', $this->getId());
		}
		if ( $this->_UserID !== 0 ) {
			$oStmt->bindValue(':UserID', $this->getUserID());
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
		$this->setId((int)$inArray['id']);
		$this->setUserID((int)$inArray['userID']);
		$this->setScore((int)$inArray['score']);
		$this->setHighScore((int)$inArray['highScore']);
		$this->setCreateDate($inArray['createDate']);
		$this->setUpdateDate($inArray['updateDate']);
		if ( array_key_exists('position', $inArray) ) {
			$this->setPosition($inArray['position']);
		}
		if ( array_key_exists('totalUsers', $inArray) ) {
			$this->setTotalUsers($inArray['totalUsers']);
		}
		if ( array_key_exists('positionAllTime', $inArray) ) {
			$this->setPositionAllTime($inArray['positionAllTime']);
		}
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

			$this->getUpdateDate()->setTimestamp(time());
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userPoints
					( id, userID, score, highScore, createDate, updateDate)
				VALUES
					(:Id, :UserID, :Score, :HighScore, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					score=VALUES(score),
					highScore=VALUES(highScore),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':Id', $this->getId());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':Score', $this->getScore());
				$oStmt->bindValue(':HighScore', $this->getHighScore());
				$oStmt->bindValue(':CreateDate', $this->getCreateDate());
				$oStmt->bindValue(':UpdateDate', $this->getUpdateDate());

				if ( $oStmt->execute() ) {
					if ( !$this->getId() ) {
						$this->setId($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPoints
			WHERE
				id = :Id
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Id', $this->getId());

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
	 * @return mofilmUserPoints
	 */
	function reset() {
		$this->_Id = 0;
		$this->_UserID = 0;
		$this->_Score = 0;
		$this->_HighScore = 0;
		$this->_Position = 0;
		$this->_PositionAllTime = 0;
		$this->_TotalUsers = 0;
		$this->_CreateDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_UpdateDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
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
	 * @return mofilmUserPoints
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
			'_Id' => array(
				'number' => array(),
			),
			'_UserID' => array(
				'number' => array(),
			),
			'_Score' => array(
				'number' => array(),
			),
			'_CreateDate' => array(
				'dateTime' => array(),
			),
			'_UpdateDate' => array(
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
		if ( !$modified && $this->getUpdateDate() ) {
			$this->_Modified = $this->getUpdateDate()->isModified() || $modified;
		}

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmUserPoints
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
		return $this->_Id;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * {@link mofilmUserPoints::PRIMARY_KEY_SEPARATOR}.
 	 *
	 * @param string $inKey
	 * @return mofilmUserPoints
  	 */
	function setPrimaryKey($inKey) {
		list($id) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setId($id);
	}

	/**
	 * Return the current value of the property $_Id
	 *
	 * @return integer
 	 */
	function getId() {
		return $this->_Id;
	}

	/**
	 * Set the object property _Id to $inId
	 *
	 * @param integer $inId
	 * @return mofilmUserPoints
	 */
	function setId($inId) {
		if ( $inId !== $this->_Id ) {
			$this->_Id = $inId;
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
	 * @return mofilmUserPoints
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Score
	 *
	 * @return integer
 	 */
	function getScore() {
		return $this->_Score;
	}

	/**
	 * Set the object property _Score to $inScore
	 *
	 * @param integer $inScore
	 * @return mofilmUserPoints
	 */
	function setScore($inScore) {
		if ( $inScore !== $this->_Score ) {
			$this->_Score = $inScore;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_HighScore
	 *
	 * @return integer
	 */
	function getHighScore() {
		return $this->_HighScore;
	}

	/**
	 * Set $_HighScore to $inHighScore
	 *
	 * @param integer $inHighScore
	 * @return mofilmUserPoints
	 */
	function setHighScore($inHighScore) {
		if ( $inHighScore !== $this->_HighScore ) {
			$this->_HighScore = $inHighScore;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_Position
	 *
	 * @return integer
	 */
	function getPosition() {
		return $this->_Position;
	}

	/**
	 * Set $_Position to $inPosition
	 *
	 * @param integer $inPosition
	 * @return mofilmUserPoints
	 */
	function setPosition($inPosition) {
		if ( $inPosition !== $this->_Position ) {
			$this->_Position = $inPosition;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_PositionAllTime
	 *
	 * @return integer
	 */
	function getPositionAllTime() {
		return $this->_PositionAllTime;
	}

	/**
	 * Set $_PositionAllTime to $inPositionAllTime
	 *
	 * @param integer $inPositionAllTime
	 * @return mofilmUserPoints
	 */
	function setPositionAllTime($inPositionAllTime) {
		if ( $inPositionAllTime !== $this->_PositionAllTime ) {
			$this->_PositionAllTime = $inPositionAllTime;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_TotalUsers
	 *
	 * @return integer
	 */
	function getTotalUsers() {
		return $this->_TotalUsers;
	}

	/**
	 * Set $_TotalUsers to $inTotalUsers
	 *
	 * @param integer $inTotalUsers
	 * @return mofilmUserPoints
	 */
	function setTotalUsers($inTotalUsers) {
		if ( $inTotalUsers !== $this->_TotalUsers ) {
			$this->_TotalUsers = $inTotalUsers;
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
	 * @return mofilmUserPoints
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
	 * @return mofilmUserPoints
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
	 * @return mofilmUserPoints
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}