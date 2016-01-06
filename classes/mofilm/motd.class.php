<?php
/**
 * mofilmMotd
 *
 * Stored in mofilmMotd.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMotd
 * @category mofilmMotd
 * @version $Rev: 10 $
 */


/**
 * mofilmMotd Class
 *
 * Provides access to records in system.motd
 *
 * Creating a new record:
 * <code>
 * $oMofilmMotd = new mofilmMotd();
 * $oMofilmMotd->setMotdID($inMotdID);
 * $oMofilmMotd->setUserID($inUserID);
 * $oMofilmMotd->setLastEditedBy($inLastEditedBy);
 * $oMofilmMotd->setTitle($inTitle);
 * $oMofilmMotd->setContent($inContent);
 * $oMofilmMotd->setCreateDate($inCreateDate);
 * $oMofilmMotd->setUpdateDate($inUpdateDate);
 * $oMofilmMotd->setActive($inActive);
 * $oMofilmMotd->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMotd = new mofilmMotd($inMotdID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmMotd = new mofilmMotd();
 * $oMofilmMotd->setMotdID($inMotdID);
 * $oMofilmMotd->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmMotd = mofilmMotd::getInstance($inMotdID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmMotd
 * @category mofilmMotd
 */
class mofilmMotd implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmMotd
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
	 * Stores $_MotdID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MotdID;

	/**
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_LastEditedBy
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_LastEditedBy;

	/**
	 * Stores $_Title
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Title;

	/**
	 * Stores $_Content
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Content;

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
	 * Stores $_Active
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Active;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmMotd
	 *
	 * @param integer $inMotdID
	 * @return mofilmMotd
	 */
	function __construct($inMotdID = null) {
		$this->reset();
		if ( $inMotdID !== null ) {
			$this->setMotdID($inMotdID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new mofilmMotd containing non-unique properties
	 *
	 * @param integer $inUserID
	 * @param integer $inLastEditedBy
	 * @param string $inTitle
	 * @param string $inContent
	 * @param datetime $inCreateDate
	 * @param datetime $inUpdateDate
	 * @param integer $inActive
	 * @return mofilmMotd
	 * @static
	 */
	public static function factory($inUserID = null, $inLastEditedBy = null, $inTitle = null, $inContent = null, $inCreateDate = null, $inUpdateDate = null, $inActive = null) {
		$oObject = new mofilmMotd;
		if ( $inUserID !== null ) {
			$oObject->setUserID($inUserID);
		}
		if ( $inLastEditedBy !== null ) {
			$oObject->setLastEditedBy($inLastEditedBy);
		}
		if ( $inTitle !== null ) {
			$oObject->setTitle($inTitle);
		}
		if ( $inContent !== null ) {
			$oObject->setContent($inContent);
		}
		if ( $inCreateDate !== null ) {
			$oObject->setCreateDate($inCreateDate);
		}
		if ( $inUpdateDate !== null ) {
			$oObject->setUpdateDate($inUpdateDate);
		}
		if ( $inActive !== null ) {
			$oObject->setActive($inActive);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmMotd by primary key
	 *
	 * @param integer $inMotdID
	 * @return mofilmMotd
	 * @static
	 */
	public static function getInstance($inMotdID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMotdID]) ) {
			return self::$_Instances[$inMotdID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmMotd();
		$oObject->setMotdID($inMotdID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMotdID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Gets the currently active MOTD
	 *
	 * @return mofilmMotd
	 * @static
	 */
	public static function getCurrentMotd() {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('system').'.motd WHERE active = 1 LIMIT 1';
		
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				$res = $oStmt->fetchAll();
				if ( count($res) > 0 && isset($res[0]) ) {
					$oObject = new mofilmMotd();
					$oObject->loadFromArray($res[0]);
					return $oObject;
				}
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return false;
	}

	/**
	 * Returns an array of objects of mofilmMotd
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('system').'.motd ORDER BY createDate DESC';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMotd();
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
			SELECT motdID, userID, lastEditedBy, title, content, createDate, updateDate, active
			  FROM '.system::getConfig()->getDatabase('system').'.motd';

		$where = array();
		if ( $this->_MotdID !== 0 ) {
			$where[] = ' motdID = :MotdID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_MotdID !== 0 ) {
				$oStmt->bindValue(':MotdID', $this->_MotdID);
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
		$this->setMotdID((int)$inArray['motdID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setLastEditedBy((int)$inArray['lastEditedBy']);
		$this->setTitle($inArray['title']);
		$this->setContent($inArray['content']);
		$this->setCreateDate($inArray['createDate']);
		$this->setUpdateDate($inArray['updateDate']);
		$this->setActive((int)$inArray['active']);
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
				INSERT INTO '.system::getConfig()->getDatabase('system').'.motd
					( motdID, userID, lastEditedBy, title, content, createDate, updateDate, active)
				VALUES
					(:MotdID, :UserID, :LastEditedBy, :Title, :Content, :CreateDate, :UpdateDate, :Active)
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					lastEditedBy=VALUES(lastEditedBy),
					title=VALUES(title),
					content=VALUES(content),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate),
					active=VALUES(active)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MotdID', $this->_MotdID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':LastEditedBy', $this->_LastEditedBy);
					$oStmt->bindValue(':Title', $this->_Title);
					$oStmt->bindValue(':Content', $this->_Content);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':UpdateDate', $this->_UpdateDate);
					$oStmt->bindValue(':Active', $this->_Active);

					if ( $oStmt->execute() ) {
						if ( !$this->getMotdID() ) {
							$this->setMotdID($oDB->lastInsertId());
						}
						$this->setModified(false);
						$return = true;
					}
				} catch ( Exception $e ) {
					systemLog::error($e->getMessage());
					throw $e;
				}
			}
			
			if ( $this->getActive() ) {
				dbManager::getInstance()->exec('UPDATE '.system::getConfig()->getDatabase('system').'.motd SET active = 0 WHERE motdID != '.$this->getMotdID());
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
			DELETE FROM '.system::getConfig()->getDatabase('system').'.motd
			WHERE
				motdID = :MotdID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MotdID', $this->_MotdID);

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
	 * @return mofilmMotd
	 */
	function reset() {
		$this->_MotdID = 0;
		$this->_UserID = 0;
		$this->_LastEditedBy = 0;
		$this->_Title = '';
		$this->_Content = '';
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_UpdateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Active = 0;
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
		$string .= " MotdID[$this->_MotdID] $newLine";
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " LastEditedBy[$this->_LastEditedBy] $newLine";
		$string .= " Title[$this->_Title] $newLine";
		$string .= " Content[$this->_Content] $newLine";
		$string .= " CreateDate[$this->_CreateDate] $newLine";
		$string .= " UpdateDate[$this->_UpdateDate] $newLine";
		$string .= " Active[$this->_Active] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmMotd';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"MotdID\" value=\"$this->_MotdID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"LastEditedBy\" value=\"$this->_LastEditedBy\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Title\" value=\"$this->_Title\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Content\" value=\"$this->_Content\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"CreateDate\" value=\"$this->_CreateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"UpdateDate\" value=\"$this->_UpdateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Active\" value=\"$this->_Active\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkMotdID($message);
		}
		if ( $valid ) {
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkLastEditedBy($message);
		}
		if ( $valid ) {
			$valid = $this->checkTitle($message);
		}
		if ( $valid ) {
			$valid = $this->checkContent($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkActive($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_MotdID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMotdID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MotdID) && $this->_MotdID !== 0 ) {
			$inMessage .= "{$this->_MotdID} is not a valid value for MotdID";
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
	 * Checks that $_LastEditedBy has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkLastEditedBy(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_LastEditedBy) && $this->_LastEditedBy !== 0 ) {
			$inMessage .= "{$this->_LastEditedBy} is not a valid value for LastEditedBy";
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
	 * Checks that $_Content has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkContent(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Content) && $this->_Content !== '' ) {
			$inMessage .= "{$this->_Content} is not a valid value for Content";
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
	 * Checks that $_Active has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkActive(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Active) && $this->_Active !== 0 ) {
			$inMessage .= "{$this->_Active} is not a valid value for Active";
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
	 * @return mofilmMotd
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
		return $this->_MotdID;
	}

	/**
	 * Return value of $_MotdID
	 *
	 * @return integer
	 * @access public
	 */
	function getMotdID() {
		return $this->_MotdID;
	}

	/**
	 * Set $_MotdID to MotdID
	 *
	 * @param integer $inMotdID
	 * @return mofilmMotd
	 * @access public
	 */
	function setMotdID($inMotdID) {
		if ( $inMotdID !== $this->_MotdID ) {
			$this->_MotdID = $inMotdID;
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
	 * Set $_UserID to UserID
	 *
	 * @param integer $inUserID
	 * @return mofilmMotd
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
	 * Return value of $_LastEditedBy
	 *
	 * @return integer
	 * @access public
	 */
	function getLastEditedBy() {
		return $this->_LastEditedBy;
	}

	/**
	 * Set $_LastEditedBy to LastEditedBy
	 *
	 * @param integer $inLastEditedBy
	 * @return mofilmMotd
	 * @access public
	 */
	function setLastEditedBy($inLastEditedBy) {
		if ( $inLastEditedBy !== $this->_LastEditedBy ) {
			$this->_LastEditedBy = $inLastEditedBy;
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
	 * @return mofilmMotd
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
	 * Return value of $_Content
	 *
	 * @return string
	 * @access public
	 */
	function getContent() {
		return $this->_Content;
	}

	/**
	 * Set $_Content to Content
	 *
	 * @param string $inContent
	 * @return mofilmMotd
	 * @access public
	 */
	function setContent($inContent) {
		if ( $inContent !== $this->_Content ) {
			$this->_Content = $inContent;
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
	 * @return mofilmMotd
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
	 * @return mofilmMotd
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
	 * Return value of $_Active
	 *
	 * @return integer
	 * @access public
	 */
	function getActive() {
		return $this->_Active;
	}

	/**
	 * Set $_Active to Active
	 *
	 * @param integer $inActive
	 * @return mofilmMotd
	 * @access public
	 */
	function setActive($inActive) {
		if ( $inActive !== $this->_Active ) {
			$this->_Active = $inActive;
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
	 * @return mofilmMotd
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}