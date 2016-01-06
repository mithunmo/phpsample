<?php
/**
 * mofilmUserProfile
 *
 * Stored in mofilmUserProfile.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserProfile
 * @category mofilmUserProfile
 * @version $Rev: 10 $
 */


/**
 * mofilmUserProfile Class
 *
 * Provides access to records in mofilm_content.userProfiles
 *
 * Creating a new record:
 * <code>
 * $oMofilmUserProfile = new mofilmUserProfile();
 * $oMofilmUserProfile->setID($inID);
 * $oMofilmUserProfile->setUserID($inUserID);
 * $oMofilmUserProfile->setProfileName($inProfileName);
 * $oMofilmUserProfile->setActive($inActive);
 * $oMofilmUserProfile->setCreateDate($inCreateDate);
 * $oMofilmUserProfile->setUpdateDate($inUpdateDate);
 * $oMofilmUserProfile->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserProfile = new mofilmUserProfile($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmUserProfile = new mofilmUserProfile();
 * $oMofilmUserProfile->setID($inID);
 * $oMofilmUserProfile->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserProfile = mofilmUserProfile::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmUserProfile
 * @category mofilmUserProfile
 */
class mofilmUserProfile implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmUserProfile
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
	 * Stores $_ProfileName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ProfileName;

	/**
	 * Stores $_Active
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Active;
	
	const PROFILE_ACTIVE = 1;
	const PROFILE_DISABLED = 0;

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
	 * Returns a new instance of mofilmUserProfile
	 *
	 * @param integer $inID
	 * @return mofilmUserProfile
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
	 * Creates a new mofilmUserProfile containing non-unique properties
	 *
	 * @param integer $inUserID
	 * @param integer $inActive
	 * @param datetime $inCreateDate
	 * @param datetime $inUpdateDate
	 * @return mofilmUserProfile
	 * @static
	 */
	public static function factory($inUserID = null, $inActive = null, $inCreateDate = null, $inUpdateDate = null) {
		$oObject = new mofilmUserProfile;
		if ( $inUserID !== null ) {
			$oObject->setUserID($inUserID);
		}
		if ( $inActive !== null ) {
			$oObject->setActive($inActive);
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
	 * Get an instance of mofilmUserProfile by primary key
	 *
	 * @param integer $inID
	 * @return mofilmUserProfile
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
		$oObject = new mofilmUserProfile();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of mofilmUserProfile by unique key (profileName)
	 *
	 * @param string $inProfileName
	 * @return mofilmUserProfile
	 * @static
	 */
	public static function getInstanceByProfileName($inProfileName) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inProfileName]) ) {
			return self::$_Instances[$inProfileName];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserProfile();
		$oObject->setProfileName($inProfileName);
		if ( $oObject->load() ) {
			self::$_Instances[$inProfileName] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Gets the specified users active profile
	 *
	 * @param integer $inUserID
	 * @return mofilmUserProfile
	 * @static
	 */
	public static function getUserActiveProfile($inUserID) {
		$oObject = new mofilmUserProfile();
		$oObject->setUserID($inUserID);
		$oObject->setActive(self::PROFILE_ACTIVE);
		$oObject->load();
		return $oObject;
	}

	/**
	 * Gets the most recent profile record for the user, always returns a mofilmUserProfile object
	 *
	 * @param integer $inUserID
	 * @return mofilmUserProfile
	 * @static
	 */
	public static function getMostRecentUserProfile($inUserID) {
		$oObject = new mofilmUserProfile();
		$query = '
			SELECT userProfiles.*
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userProfiles
			 WHERE userProfiles.userID = :UserID
			 ORDER BY userProfiles.ID DESC, userProfiles.active DESC
			 LIMIT 1';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':UserID', $inUserID);
		if ( $oStmt->execute() ) {
			$res = $oStmt->fetchAll();
			if ( is_array($res) && count($res) > 0 ) {
				$oObject->loadFromArray($res[0]);
			}
		}
		$oStmt->closeCursor();
		
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmUserProfile
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userProfiles';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserProfile();
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
			SELECT ID, userID, profileName, active, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userProfiles';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		if ( $this->_ProfileName !== '' ) {
			$where[] = ' profileName = :ProfileName ';
		}
		if ( $this->_UserID !== 0 ) {
			$where[] = ' userID = :UserID ';
		}
		if ( $this->_Active !== null ) {
			$where[] = ' active = :Active ';
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
			if ( $this->_ProfileName !== '' ) {
				$oStmt->bindValue(':ProfileName', $this->_ProfileName);
			}
			if ( $this->_UserID !== 0 ) {
				$oStmt->bindValue(':UserID', $this->_UserID);
			}
			if ( $this->_Active !== null ) {
				$oStmt->bindValue(':Active', $this->_Active);
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
		$this->setProfileName($inArray['profileName']);
		$this->setActive((int)$inArray['active']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userProfiles
					( ID, userID, profileName, active, createDate, updateDate)
				VALUES
					(:ID, :UserID, :ProfileName, :Active, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					active=VALUES(active),
					updateDate=VALUES(updateDate)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':ProfileName', $this->_ProfileName);
					$oStmt->bindValue(':Active', $this->_Active);
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
	 * Marks the profile as disabled
	 *
	 * @return boolean
	 */
	function delete() {
		if ( $this->getID() > 0 ) {
			$this->setActive(self::PROFILE_DISABLED);
			return $this->save();
		}
		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mofilmUserProfile
	 */
	function reset() {
		$this->_ID = 0;
		$this->_UserID = 0;
		$this->_ProfileName = '';
		$this->_Active = NULL;
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
		$string .= " ProfileName[$this->_ProfileName] $newLine";
		$string .= " Active[$this->_Active] $newLine";
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
		$className = 'mofilmUserProfile';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ProfileName\" value=\"$this->_ProfileName\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Active\" value=\"$this->_Active\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkProfileName($message);
		}
		if ( $valid ) {
			$valid = $this->checkActive($message);
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
	 * Checks that $_ProfileName has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkProfileName(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ProfileName) && $this->_ProfileName !== '' ) {
			$inMessage .= "{$this->_ProfileName} is not a valid value for ProfileName";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_ProfileName) > 50 ) {
			$inMessage .= "ProfileName cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_ProfileName) <= 1 ) {
			$inMessage .= "ProfileName must be more than 1 character";
			$isValid = false;
		}
		if ( $isValid ) {
			$oProfile = self::getInstanceByProfileName($this->getProfileName());
			if ( $oProfile->getID() > 0 && $oProfile->getUserID() != $this->getUserID() ) {
				$isValid = false;
				$inMessage = "The profile name {$this->getProfileName()} is already in use by another user";
			}
			$oProfile = null;
			unset($oProfile);
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
	 * @return mofilmUserProfile
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns true if profile is active
	 * 
	 * @return boolean
	 */
	function isActive() {
		return ($this->getActive() == self::PROFILE_ACTIVE);
	}
	
	/**
	 * Returns true if profile is disabled
	 * 
	 * @return boolean
	 */
	function isDisabled() {
		return ($this->getActive() == self::PROFILE_DISABLED);
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
	 * @return mofilmUserProfile
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
	 * Set $_UserID to UserID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserProfile
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
	 * Return value of $_ProfileName
	 *
	 * @return string
	 * @access public
	 */
	function getProfileName() {
		return $this->_ProfileName;
	}

	/**
	 * Set $_ProfileName to ProfileName
	 *
	 * @param string $inProfileName
	 * @return mofilmUserProfile
	 * @access public
	 */
	function setProfileName($inProfileName) {
		if ( $inProfileName !== $this->_ProfileName ) {
			$this->_ProfileName = $inProfileName;
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
	 * @return mofilmUserProfile
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
	 * @return mofilmUserProfile
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
	 * @return mofilmUserProfile
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
	 * @return mofilmUserProfile
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}