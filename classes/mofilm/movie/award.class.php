<?php
/**
 * mofilmMovieAward
 *
 * Stored in mofilmMovieAward.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMovieAward
 * @category mofilmMovieAward
 * @version $Rev: 241 $
 */


/**
 * mofilmMovieAward Class
 *
 * Provides access to records in mofilm_content.movieAwards
 *
 * Creating a new record:
 * <code>
 * $oMofilmMovieAward = new mofilmMovieAward();
 * $oMofilmMovieAward->setID($inID);
 * $oMofilmMovieAward->setUserID($inMovieID);
 * $oMofilmMovieAward->setMovieID($inMovieID);
 * $oMofilmMovieAward->setEventID($inEventID);
 * $oMofilmMovieAward->setSourceID($inSourceID);
 * $oMofilmMovieAward->setPosition($inPosition);
 * $oMofilmMovieAward->setType($inType);
 * $oMofilmMovieAward->setName($inName);
 * $oMofilmMovieAward->setYear($inYear);
 * $oMofilmMovieAward->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMovieAward = new mofilmMovieAward($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmMovieAward = new mofilmMovieAward();
 * $oMofilmMovieAward->setID($inID);
 * $oMofilmMovieAward->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmMovieAward = mofilmMovieAward::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmMovieAward
 * @category mofilmMovieAward
 */
class mofilmMovieAward implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmMovieAward
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
	 * Stores $_MovieID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;

	/**
	 * Stores $_EventID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_EventID;

	/**
	 * Stores $_SourceID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SourceID;

	/**
	 * Stores $_Position
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Position;

	/**
	 * Stores $_Type
	 *
	 * @var string (TYPE_WINNER,TYPE_SHORTLISTED,TYPE_RUNNER_UP,TYPE_NOMINATED,TYPE_OTHER,)
	 * @access protected
	 */
	protected $_Type;
	const TYPE_WINNER = 'Winner';
	const TYPE_SHORTLISTED = 'Shortlisted';
	const TYPE_FINALIST = 'Finalist';
	const TYPE_RUNNER_UP = 'Runner Up';
	const TYPE_NOMINATED = 'Nominated';
	const TYPE_OTHER = 'Other';
        const TYPE_PRO_FINAL = 'ProFinal';
        const TYPE_PRO_SHOWCASE = 'ProShowcase';

	/**
	 * Stores $_Name
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Name;

	/**
	 * Stores $_Year
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Year;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;

	/**
	 * Stores an instance of mofilmMovie
	 *
	 * @var mofilmMovie
	 * @access protected
	 */
	protected $_Movie;



	/**
	 * Returns a new instance of mofilmMovieAward
	 *
	 * @param integer $inID
	 * @return mofilmMovieAward
	 */
	function __construct($inID = null) {
		$this->reset();
		if ( $inID !== null ) {
			$this->setID($inID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new mofilmMovieAward containing non-unique properties
	 *
	 * @param integer $inUserID
	 * @param integer $inMovieID
	 * @param integer $inEventID
	 * @param integer $inSourceID
	 * @param integer $inPosition
	 * @param string $inType
	 * @param string $inName
	 * @param string $inYear
	 * @return mofilmMovieAward
	 * @static
	 */
	public static function factory($inUserID = null, $inMovieID = null, $inEventID = null, $inSourceID = null, $inPosition = null, $inType = null, $inName = null, $inYear = null) {
		$oObject = new mofilmMovieAward;
		if ( $inUserID ) {
			$oObject->setUserID($inUserID);
		}
		if ( $inMovieID !== null ) {
			$oObject->setMovieID($inMovieID);
		}
		if ( $inEventID !== null ) {
			$oObject->setEventID($inEventID);
		}
		if ( $inSourceID !== null ) {
			$oObject->setSourceID($inSourceID);
		}
		if ( $inPosition !== null ) {
			$oObject->setPosition($inPosition);
		}
		if ( $inType !== null ) {
			$oObject->setType($inType);
		}
		if ( $inName !== null ) {
			$oObject->setName($inName);
		}
		if ( $inYear !== null ) {
			$oObject->setYear($inYear);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmMovieAward by primary key
	 *
	 * @param integer $inID
	 * @return mofilmMovieAward
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
		$oObject = new mofilmMovieAward();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
	
	/**
	 * Returns an award instance for the event and type, false if not found
	 * 
	 * @param integer $inEventID
	 * @param string $inType
	 * @return mofilmMovieAward
	 * @static
	 */
	public static function getInstanceByEventAndType($inEventID, $inType) {
		$query = '
			SELECT *
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
			 WHERE eventID = :EventID
			   AND type = :Type
			 LIMIT 1';
		
		$oObject = false;
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':EventID', $inEventID, PDO::PARAM_INT);
			$oStmt->bindValue(':Type', $inType);
			if ( $oStmt->execute() ) {
				$row = $oStmt->fetch();
				if ( $row !== false && is_array($row) ) {
					$oObject = new mofilmMovieAward();
					$oObject->loadFromArray($row);
				}
			}
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $oObject;
	}
	
	/**
	 * Returns an award instance for the movie, event and type, false if not found
	 * 
	 * @param integer $inMovieID
	 * @param integer $inEventID
	 * @param string $inType
	 * @return mofilmMovieAward
	 * @static
	 */
	public static function getInstanceByMovieEventAndType($inMovieID, $inEventID, $inType) {
		$query = '
			SELECT *
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
			 WHERE movieID = :MovieID
			   AND eventID = :EventID
			   AND type = :Type
			 LIMIT 1';
		
		$oObject = false;
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $inMovieID, PDO::PARAM_INT);
			$oStmt->bindValue(':EventID', $inEventID, PDO::PARAM_INT);
			$oStmt->bindValue(':Type', $inType);
			if ( $oStmt->execute() ) {
				$row = $oStmt->fetch();
				if ( $row !== false && is_array($row) ) {
					$oObject = new mofilmMovieAward();
					$oObject->loadFromArray($row);
				}
			}
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmMovieAward
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inMovieID
	 * @param integer $inEventID
	 * @param string $inType
	 * @param integer $inUserID
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inMovieID = null, $inEventID = null, $inType = null, $inUserID = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards WHERE 1';
		if ( $inMovieID !== null ) {
			$query .= ' AND movieID = '.dbManager::getInstance()->quote($inMovieID);
		}
		if ( $inEventID !== null && $inEventID > 0 ) {
			$query .= ' AND eventID = '.dbManager::getInstance()->quote($inEventID);
		}
		if ( $inType !== null && strlen($inType) > 0 ) {
			$query .= ' AND type = '.dbManager::getInstance()->quote($inType);
		}
		if ( $inUserID !== null && $inUserID > 0 ) {
			$query .= ' AND userID = '.dbManager::getInstance()->quote($inUserID);
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMovieAward();
					$oObject->loadFromArray($row);
					$list[] = $oObject;
				}
			}
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $list;
	}
	
	/**
	 * Returns the array of award types
	 * 
	 * @return array
	 * @static
	 */
	public static function getTypes() {
		return array(
			self::TYPE_FINALIST, self::TYPE_RUNNER_UP,
			self::TYPE_SHORTLISTED, self::TYPE_WINNER,
                        self::TYPE_PRO_FINAL, self::TYPE_PRO_SHOWCASE
		);
	}



	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT ID, userID, movieID, eventID, sourceID, position, type, name, year
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_ID !== 0 ) {
				$oStmt->bindValue(':ID', $this->_ID);
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
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $return;
	}

	/**
	 * Loads a record by array
	 *
	 * @param array $inArray
	 */
	function loadFromArray($inArray) {
		$this->setID((int)$inArray['ID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setMovieID((int)$inArray['movieID']);
		$this->setEventID((int)$inArray['eventID']);
		$this->setSourceID((int)$inArray['sourceID']);
		$this->setPosition((int)$inArray['position']);
		$this->setType($inArray['type']);
		$this->setName($inArray['name']);
		$this->setYear($inArray['year']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
					( ID, userID, movieID, eventID, sourceID, position, type, name, year)
				VALUES
					(:ID, :UserID, :MovieID, :EventID, :SourceID, :Position, :Type, :Name, :Year)
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					movieID=VALUES(movieID),
					eventID=VALUES(eventID),
					sourceID=VALUES(sourceID),
					position=VALUES(position),
					type=VALUES(type),
					name=VALUES(name),
					year=VALUES(year)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':MovieID', $this->_MovieID);
					$oStmt->bindValue(':EventID', $this->_EventID);
					$oStmt->bindValue(':SourceID', $this->_SourceID);
					$oStmt->bindValue(':Position', $this->_Position);
					$oStmt->bindValue(':Type', $this->_Type);
					$oStmt->bindValue(':Name', $this->_Name);
					$oStmt->bindValue(':Year', $this->_Year);

					if ( $oStmt->execute() ) {
						if ( !$this->getID() ) {
							$this->setID($oDB->lastInsertId());
						}
						$this->setModified(false);
						$return = true;
					}
				} catch ( Exception $e ) {
					systemLog::error($e->getMessage());
					throw $e;
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
			WHERE
				ID = :ID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ID', $this->_ID);

			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				$this->reset();
				return true;
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mofilmMovieAward
	 */
	function reset() {
		$this->_ID = 0;
		$this->_UserID = 0;
		$this->_MovieID = 0;
		$this->_EventID = 0;
		$this->_SourceID = 0;
		$this->_Position = 0;
		$this->_Type = '';
		$this->_Name = '';
		$this->_Year = '';
		$this->setModified(false);
		$this->setMarkForDeletion(false);
		return $this;
	}

	/**
	 * Returns object as a string with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toString($newLine = "\n") {
		$string  = '';
		$string .= " ID[$this->_ID] $newLine";
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " MovieID[$this->_MovieID] $newLine";
		$string .= " EventID[$this->_EventID] $newLine";
		$string .= " SourceID[$this->_SourceID] $newLine";
		$string .= " Position[$this->_Position] $newLine";
		$string .= " Type[$this->_Type] $newLine";
		$string .= " Name[$this->_Name] $newLine";
		$string .= " Year[$this->_Year] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmMovieAward';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"MovieID\" value=\"$this->_MovieID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"EventID\" value=\"$this->_EventID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"SourceID\" value=\"$this->_SourceID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Position\" value=\"$this->_Position\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Type\" value=\"$this->_Type\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Name\" value=\"$this->_Name\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Year\" value=\"$this->_Year\" type=\"string\" /> $newLine";
		$xml .= "</$className>$newLine";
		return $xml;
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
	 * Returns true if object is valid
	 *
	 * @return boolean
	 */
	function isValid(&$message = '') {
		$valid = true;
		if ( $valid ) {
			$valid = $this->checkID($message);
		}
		if ( $valid ) {
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkMovieID($message);
		}
		if ( $valid ) {
			$valid = $this->checkEventID($message);
		}
		if ( $valid ) {
			$valid = $this->checkSourceID($message);
		}
		if ( $valid ) {
			$valid = $this->checkPosition($message);
		}
		if ( $valid ) {
			$valid = $this->checkType($message);
		}
		if ( $valid ) {
			$valid = $this->checkName($message);
		}
		if ( $valid ) {
			$valid = $this->checkYear($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_ID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ID) && $this->_ID !== 0 ) {
			$inMessage .= "{$this->_ID} is not a valid value for ID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_UserID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkUserID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_UserID) && $this->_UserID !== 0 ) {
			$inMessage .= "{$this->_UserID} is not a valid value for UserID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_MovieID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMovieID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MovieID) && $this->_MovieID !== 0 ) {
			$inMessage .= "{$this->_MovieID} is not a valid value for MovieID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_EventID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkEventID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_EventID) && $this->_EventID !== 0 ) {
			$inMessage .= "{$this->_EventID} is not a valid value for EventID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_SourceID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkSourceID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_SourceID) && $this->_SourceID !== 0 ) {
			$inMessage .= "{$this->_SourceID} is not a valid value for SourceID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Position has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkPosition(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Position) && $this->_Position !== 0 ) {
			$inMessage .= "{$this->_Position} is not a valid value for Position";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Type has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkType(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Type) && $this->_Type !== '' ) {
			$inMessage .= "{$this->_Type} is not a valid value for Type";
			$isValid = false;
		}
		if ( $isValid && $this->_Type != '' && !in_array($this->_Type, array(self::TYPE_WINNER, self::TYPE_SHORTLISTED, self::TYPE_FINALIST, self::TYPE_RUNNER_UP, self::TYPE_NOMINATED, self::TYPE_OTHER,self::TYPE_PRO_FINAL, self:: TYPE_PRO_SHOWCASE)) ) {
			$inMessage .= "Type must be one of TYPE_WINNER, TYPE_SHORTLISTED, TYPE_FINALIST, TYPE_RUNNER_UP, TYPE_NOMINATED, TYPE_OTHER,TYPE_PRO_FINAL,TYPE_PRO_SHOWCASE";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Name has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkName(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Name) && $this->_Name !== '' ) {
			$inMessage .= "{$this->_Name} is not a valid value for Name";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Name) > 255 ) {
			$inMessage .= "Name cannot be more than 255 characters";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Year has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkYear(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Year) && $this->_Year !== '' ) {
			$inMessage .= "{$this->_Year} is not a valid value for Year";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Year) > 4 ) {
			$inMessage .= "Year cannot be more than 4 characters";
			$isValid = false;
		}
		return $isValid;
	}



	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmMovieAward
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the primaryKey index
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->_ID;
	}

	/**
	 * Return value of $_ID
	 *
	 * @return integer
	 * @access public
	 */
	function getID() {
		return $this->_ID;
	}

	/**
	 * Set $_ID to ID
	 *
	 * @param integer $inID
	 * @return mofilmMovieAward
	 * @access public
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_UserID
	 *
	 * @return integer
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmMovieAward
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_MovieID
	 *
	 * @return integer
	 * @access public
	 */
	function getMovieID() {
		return $this->_MovieID;
	}

	/**
	 * Set $_MovieID to MovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieAward
	 * @access public
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_EventID
	 *
	 * @return integer
	 * @access public
	 */
	function getEventID() {
		return $this->_EventID;
	}
	
	/**
	 * Returns the mofilmEvent object
	 * 
	 * @return mofilmEvent
	 */
	function getEvent() {
		return mofilmEvent::getInstance($this->getEventID());
	}

	/**
	 * Set $_EventID to EventID
	 *
	 * @param integer $inEventID
	 * @return mofilmMovieAward
	 * @access public
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_SourceID
	 *
	 * @return integer
	 * @access public
	 */
	function getSourceID() {
		return $this->_SourceID;
	}
	
	/**
	 * Returns the mofilmSource object
	 * 
	 * @return mofilmSource
	 */
	function getSource() {
		return mofilmSource::getInstance($this->getSourceID());
	}

	/**
	 * Set $_SourceID to SourceID
	 *
	 * @param integer $inSourceID
	 * @return mofilmMovieAward
	 * @access public
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Position
	 *
	 * @return integer
	 * @access public
	 */
	function getPosition() {
		return $this->_Position;
	}

	/**
	 * Set $_Position to Position
	 *
	 * @param integer $inPosition
	 * @return mofilmMovieAward
	 * @access public
	 */
	function setPosition($inPosition) {
		if ( $inPosition !== $this->_Position ) {
			$this->_Position = $inPosition;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Type
	 *
	 * @return string
	 * @access public
	 */
	function getType() {
		return $this->_Type;
	}

	/**
	 * Set $_Type to Type
	 *
	 * @param string $inType
	 * @return mofilmMovieAward
	 * @access public
	 */
	function setType($inType) {
		if ( $inType !== $this->_Type ) {
			$this->_Type = $inType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Name
	 *
	 * @return string
	 * @access public
	 */
	function getName() {
		return $this->_Name;
	}

	/**
	 * Set $_Name to Name
	 *
	 * @param string $inName
	 * @return mofilmMovieAward
	 * @access public
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Year
	 *
	 * @return string
	 * @access public
	 */
	function getYear() {
		return $this->_Year;
	}

	/**
	 * Set $_Year to Year
	 *
	 * @param string $inYear
	 * @return mofilmMovieAward
	 * @access public
	 */
	function setYear($inYear) {
		if ( $inYear !== $this->_Year ) {
			$this->_Year = $inYear;
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
	 * @return mofilmMovieAward
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	

	/**
	 * Returns a string representation of this award e.g. 1st Place at event
	 *
	 * @return string
	 */
	function getAwardTitle() {
		$return = '';
		if  ( $this->isWinner() ) {
			$return .= 'Overall Event Winner for ';
		} elseif ( $this->getEventPosition() ) {
			$return .= 'Placed '. $this->getEventPosition().' for ';
		} elseif ( $this->isRunnerUp() ) {
			$return .= 'Runner Up for ';
		} elseif ( $this->isShortlisted() ) {
			$return .= 'Shortlisted for ';
		}

		$return .= $this->getEventDescription();

		return $return;
	}

	/**
	 * Returns the event description for the award
	 * 
	 * @return string
	 */
	function getEventDescription() {
		if ( $this->getName() ) {
			return $this->getName();
		} else {
			return $this->getEvent()->getName().' for '.$this->getSource()->getName();
		}
	}
	
	/**
	 * Returns the event year for the award
	 * 
	 * @return integer
	 */
	function getEventYear() {
		if ( $this->getYear() ) {
			return $this->getYear();
		} else {
			return systemDateTime::getInstanceUtc($this->getEvent()->getEndDate())->getYear();
		}
	}
	
	/**
	 * Return a friendly name for the position
	 * 
	 * @return string
	 * @todo DR: refactor to something that is useful
	 */
	function getEventPosition() {
		switch ( $this->getPosition() ) {
			case 1:
				return '1st';
				
			case 2:
				return '2nd';
				
			case 3:
				return '3rd';
				
			case 4:
				return '4th';
				
			case 5:
				return '5th';
				
			default:
				return '';
		}
	}

	/**
	 * Returns true if award is a finalist award
	 *
	 * @return boolean
	 */
	function isFinalist() {
		return $this->getType() == self::TYPE_FINALIST;
	}

	/**
	 * Returns true if nominated
	 *
	 * @return boolean
	 */
	function isNominated() {
		return $this->getType() == self::TYPE_NOMINATED;
	}

	/**
	 * Returns true if other award
	 *
	 * @return boolean
	 */
	function isOther() {
		return $this->getType() == self::TYPE_OTHER;
	}

	/**
	 * Returns true if a runner up
	 *
	 * @return boolean
	 */
	function isRunnerUp() {
		return $this->getType() == self::TYPE_RUNNER_UP;
	}

	/**
	 * Returns true if shortlisted
	 *
	 * @return boolean
	 */
	function isShortlisted() {
		return $this->getType() == self::TYPE_SHORTLISTED;
	}

	/**
	 * Returns true if award is a winner
	 *
	 * @return boolean
	 */
	function isWinner() {
		return $this->getType() == self::TYPE_WINNER;
	}
        
        /**
	 * Returns true if award is a winner
	 *
	 * @return boolean
	 */
	function isProFinal() {
		return $this->getType() == self::TYPE_PRO_FINAL;
	}
        
        
        /**
	 * Returns true if award is a winner
	 *
	 * @return boolean
	 */
	function isProShowcase() {
		return $this->getType() == self::TYPE_PRO_SHOWCASE;
	}

	

	/**
	 * Returns the mofilmMovie object, loading it if not already set
	 *
	 * @return mofilmMovie
	 */
	function getMovie() {
		if ( !$this->_Movie instanceof mofilmMovie ) {
			$this->_Movie = mofilmMovieManager::getInstanceByID($this->getMovieID());
		}
		return $this->_Movie;
	}

	/**
	 * Set the mofilmMovie object directly
	 *
	 * @param mofilmMovie $inObject
	 * @return mofilmMovieAward
	 */
	function setMovie(mofilmMovie $inObject) {
		$this->_Movie = $inObject;
		return $this;
	}

}