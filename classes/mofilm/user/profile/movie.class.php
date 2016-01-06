<?php
/**
 * mofilmUserProfileMovie
 *
 * Stored in mofilmUserProfileMovie.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserProfileMovie
 * @category mofilmUserProfileMovie
 * @version $Rev: 98 $
 */


/**
 * mofilmUserProfileMovie Class
 *
 * Provides access to records in mofilm_content.userProfileMovies
 *
 * Creating a new record:
 * <code>
 * $oMofilmUserProfileMovie = new mofilmUserProfileMovie();
 * $oMofilmUserProfileMovie->setID($inID);
 * $oMofilmUserProfileMovie->setUserID($inUserID);
 * $oMofilmUserProfileMovie->setMovieID($inMovieID);
 * $oMofilmUserProfileMovie->setPosition($inPosition);
 * $oMofilmUserProfileMovie->setTitle($inTitle);
 * $oMofilmUserProfileMovie->setSummary($inSummary);
 * $oMofilmUserProfileMovie->setCreateDate($inCreateDate);
 * $oMofilmUserProfileMovie->setUpdateDate($inUpdateDate);
 * $oMofilmUserProfileMovie->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserProfileMovie = new mofilmUserProfileMovie($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmUserProfileMovie = new mofilmUserProfileMovie();
 * $oMofilmUserProfileMovie->setID($inID);
 * $oMofilmUserProfileMovie->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserProfileMovie = mofilmUserProfileMovie::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmUserProfileMovie
 * @category mofilmUserProfileMovie
 */
class mofilmUserProfileMovie implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmUserProfileMovie
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
	 * Stores $_Position
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Position;

	/**
	 * Stores $_Title
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Title;

	/**
	 * Stores $_Summary
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Summary;

	/**
	 * Stores $_CreateDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_CreateDate;

	/**
	 * Stores $_UpdateDate
	 *
	 * @var datetime 
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
	 * Stores the mofilmMovie object
	 *
	 * @var mofilmMovie
	 * @access protected
	 */
	protected $_Movie;



	/**
	 * Returns a new instance of mofilmUserProfileMovie
	 *
	 * @param integer $inID
	 * @return mofilmUserProfileMovie
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
	 * Creates a new mofilmUserProfileMovie containing non-unique properties
	 *
	 * @param integer $inUserID
	 * @param integer $inMovieID
	 * @param integer $inPosition
	 * @param string $inTitle
	 * @param string $inSummary
	 * @param datetime $inCreateDate
	 * @param datetime $inUpdateDate
	 * @return mofilmUserProfileMovie
	 * @static
	 */
	public static function factory($inUserID = null, $inMovieID = null, $inPosition = null, $inTitle = null, $inSummary = null, $inCreateDate = null, $inUpdateDate = null) {
		$oObject = new mofilmUserProfileMovie;
		if ( $inUserID !== null ) {
			$oObject->setUserID($inUserID);
		}
		if ( $inMovieID !== null ) {
			$oObject->setMovieID($inMovieID);
		}
		if ( $inPosition !== null ) {
			$oObject->setPosition($inPosition);
		}
		if ( $inTitle !== null ) {
			$oObject->setTitle($inTitle);
		}
		if ( $inSummary !== null ) {
			$oObject->setSummary($inSummary);
		}
		if ( $inCreateDate !== null ) {
			$oObject->setCreateDate($inCreateDate);
		}
		if ( $inUpdateDate !== null ) {
			$oObject->setUpdateDate($inUpdateDate);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmUserProfileMovie by primary key
	 *
	 * @param integer $inID
	 * @return mofilmUserProfileMovie
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
		$oObject = new mofilmUserProfileMovie();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmUserProfileMovie
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inUserID
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inUserID = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userProfileMovies';
		if ( $inUserID !== null ) {
			$query .= ' WHERE userID = '.dbManager::getInstance()->quote($inUserID).' ORDER BY position ASC ';
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserProfileMovie();
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
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT id, userID, movieID, position, title, summary, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userProfileMovies';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' id = :ID ';
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
		$this->setPosition((int)$inArray['position']);
		$this->setTitle($inArray['title']);
		$this->setSummary($inArray['summary']);
		$this->setCreateDate($inArray['createDate']);
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
			$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userProfileMovies
					( ID, userID, movieID, position, title, summary, createDate, updateDate)
				VALUES
					(:ID, :UserID, :MovieID, :Position, :Title, :Summary, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					movieID=VALUES(movieID),
					position=VALUES(position),
					title=VALUES(title),
					summary=VALUES(summary),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':MovieID', $this->_MovieID);
					$oStmt->bindValue(':Position', $this->_Position);
					$oStmt->bindValue(':Title', $this->_Title);
					$oStmt->bindValue(':Summary', $this->_Summary);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':UpdateDate', $this->_UpdateDate);

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userProfileMovies
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
	 * @return mofilmUserProfileMovie
	 */
	function reset() {
		$this->_ID = 0;
		$this->_UserID = 0;
		$this->_MovieID = 0;
		$this->_Position = 0;
		$this->_Title = '';
		$this->_Summary = '';
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_UpdateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
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
		$string .= " Position[$this->_Position] $newLine";
		$string .= " Title[$this->_Title] $newLine";
		$string .= " Summary[$this->_Summary] $newLine";
		$string .= " CreateDate[$this->_CreateDate] $newLine";
		$string .= " UpdateDate[$this->_UpdateDate] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmUserProfileMovie';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"MovieID\" value=\"$this->_MovieID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Position\" value=\"$this->_Position\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Title\" value=\"$this->_Title\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Summary\" value=\"$this->_Summary\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"CreateDate\" value=\"$this->_CreateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"UpdateDate\" value=\"$this->_UpdateDate\" type=\"datetime\" /> $newLine";
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
			$valid = $this->checkPosition($message);
		}
		if ( $valid ) {
			$valid = $this->checkTitle($message);
		}
		if ( $valid ) {
			$valid = $this->checkSummary($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateDate($message);
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
	 * Checks that $_Title has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkTitle(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Title) && $this->_Title !== '' ) {
			$inMessage .= "{$this->_Title} is not a valid value for Title";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Title) > 255 ) {
			$inMessage .= "Title cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Title) <= 1 ) {
			$inMessage .= "Title must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Summary has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkSummary(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Summary) && $this->_Summary !== '' ) {
			$inMessage .= "{$this->_Summary} is not a valid value for Summary";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_CreateDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCreateDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_CreateDate) && $this->_CreateDate !== '' ) {
			$inMessage .= "{$this->_CreateDate} is not a valid value for CreateDate";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_UpdateDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkUpdateDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_UpdateDate) && $this->_UpdateDate !== '' ) {
			$inMessage .= "{$this->_UpdateDate} is not a valid value for UpdateDate";
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
	 * @return mofilmUserProfileMovie
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
	 * @return mofilmUserProfileMovie
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
	 * Return value of $_UserID
	 *
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserProfileMovie
	 * @access public
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
	 * @return mofilmUserProfileMovie
	 * @access public
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Movie = null;
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
	 * @return mofilmUserProfileMovie
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
	 * Return value of $_Title
	 *
	 * @return string
	 * @access public
	 */
	function getTitle() {
		return $this->_Title;
	}

	/**
	 * Set $_Title to Title
	 *
	 * @param string $inTitle
	 * @return mofilmUserProfileMovie
	 * @access public
	 */
	function setTitle($inTitle) {
		if ( $inTitle !== $this->_Title ) {
			$this->_Title = $inTitle;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Summary
	 *
	 * @return string
	 * @access public
	 */
	function getSummary() {
		return $this->_Summary;
	}

	/**
	 * Set $_Summary to Summary
	 *
	 * @param string $inSummary
	 * @return mofilmUserProfileMovie
	 * @access public
	 */
	function setSummary($inSummary) {
		if ( $inSummary !== $this->_Summary ) {
			$this->_Summary = $inSummary;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_CreateDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getCreateDate() {
		return $this->_CreateDate;
	}

	/**
	 * Set $_CreateDate to CreateDate
	 *
	 * @param datetime $inCreateDate
	 * @return mofilmUserProfileMovie
	 * @access public
	 */
	function setCreateDate($inCreateDate) {
		if ( $inCreateDate !== $this->_CreateDate ) {
			$this->_CreateDate = $inCreateDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_UpdateDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getUpdateDate() {
		return $this->_UpdateDate;
	}

	/**
	 * Set $_UpdateDate to UpdateDate
	 *
	 * @param datetime $inUpdateDate
	 * @return mofilmUserProfileMovie
	 * @access public
	 */
	function setUpdateDate($inUpdateDate) {
		if ( $inUpdateDate !== $this->_UpdateDate ) {
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
	 * @return mofilmUserProfileMovie
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}


	/**
	 * Returns the mofilmMovie object, loading it as needed
	 *
	 * @return mofilmMovie
	 */
	function getMovie() {
		if ( !$this->_Movie instanceof mofilmMovie ) {
			$this->_Movie = mofilmMovieManager::getInstanceByID($this->getMovieID());
		}
		return $this->_Movie;
	}
}
