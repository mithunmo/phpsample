<?php
/**
 * mofilmUserSignupCode
 *
 * Stored in mofilmUserSignupCode.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserSignupCode
 * @category mofilmUserSignupCode
 * @version $Rev: 10 $
 */


/**
 * mofilmUserSignupCode Class
 *
 * Provides access to records in mofilm_content.userSignupCodes
 *
 * Creating a new record:
 * <code>
 * $oMofilmUserSignupCode = new mofilmUserSignupCode();
 * $oMofilmUserSignupCode->setID($inID);
 * $oMofilmUserSignupCode->setCode($inCode);
 * $oMofilmUserSignupCode->setDescription($inDescription);
 * $oMofilmUserSignupCode->setLocation($inLocation);
 * $oMofilmUserSignupCode->setStartDate($inStartDate);
 * $oMofilmUserSignupCode->setEndDate($inEndDate);
 * $oMofilmUserSignupCode->setCreateDate($inCreateDate);
 * $oMofilmUserSignupCode->setUpdateDate($inUpdateDate);
 * $oMofilmUserSignupCode->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserSignupCode = new mofilmUserSignupCode($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmUserSignupCode = new mofilmUserSignupCode();
 * $oMofilmUserSignupCode->setID($inID);
 * $oMofilmUserSignupCode->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserSignupCode = mofilmUserSignupCode::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmUserSignupCode
 * @category mofilmUserSignupCode
 */
class mofilmUserSignupCode implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmUserSignupCode
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
	 * Stores $_Code
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Code;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_Location
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Location;
	
	/**
	 * Stores $_TerritoryID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TerritoryID;
	
	/**
	 * Stores $_StartDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_StartDate;

	/**
	 * Stores $_EndDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_EndDate;

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
	 * Returns a new instance of mofilmUserSignupCode
	 *
	 * @param integer $inID
	 * @return mofilmUserSignupCode
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
	 * Creates a new mofilmUserSignupCode containing non-unique properties
	 *
	 * @param string $inDescription
	 * @param string $inLocation
	 * @param datetime $inStartDate
	 * @param datetime $inEndDate
	 * @param datetime $inCreateDate
	 * @param datetime $inUpdateDate
	 * @return mofilmUserSignupCode
	 * @static
	 */
	public static function factory($inDescription = null, $inLocation = null, $inStartDate = null, $inEndDate = null, $inCreateDate = null, $inUpdateDate = null) {
		$oObject = new mofilmUserSignupCode;
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inLocation !== null ) {
			$oObject->setLocation($inLocation);
		}
		if ( $inStartDate !== null ) {
			$oObject->setStartDate($inStartDate);
		}
		if ( $inEndDate !== null ) {
			$oObject->setEndDate($inEndDate);
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
	 * Get an instance of mofilmUserSignupCode by primary key
	 *
	 * @param integer $inID
	 * @return mofilmUserSignupCode
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
		$oObject = new mofilmUserSignupCode();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of mofilmUserSignupCode by unique key (code)
	 *
	 * @param string $inCode
	 * @return mofilmUserSignupCode
	 * @static
	 */
	public static function getInstanceByCode($inCode) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inCode]) ) {
			return self::$_Instances[$inCode];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserSignupCode();
		$oObject->setCode($inCode);
		if ( $oObject->load() ) {
			self::$_Instances[$inCode] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmUserSignupCode
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inActiveOnly = FALSE) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userSignupCodes';
		
		if ( $inActiveOnly == TRUE ) {
			$now = dbManager::getInstance()->quote(date(system::getConfig()->getDatabaseDatetimeFormat()));
			$where[] = '(startDate <= '.$now.') AND (endDate > '.$now.')';
		}
		
		if ( count($where) > 0 ) {
			$query .= ' WHERE '.implode(' AND ', $where);
		}
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserSignupCode();
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
			SELECT ID, code, description, location, territoryID, startDate, endDate, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userSignupCodes';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		if ( $this->_Code !== '' ) {
			$where[] = ' code = :Code ';
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
			if ( $this->_Code !== '' ) {
				$oStmt->bindValue(':Code', $this->_Code);
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
		$this->setCode($inArray['code']);
		$this->setDescription($inArray['description']);
		$this->setLocation($inArray['location']);
		$this->setTerritoryID((int)$inArray['territoryID']);
		$this->setStartDate($inArray['startDate']);
		$this->setEndDate($inArray['endDate']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userSignupCodes
					( ID, code, description, location, territoryID, startDate, endDate, createDate, updateDate)
				VALUES
					(:ID, :Code, :Description, :Location, :TerritoryID, :StartDate, :EndDate, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					description=VALUES(description),
					location=VALUES(location),
					territoryID=VALUES(territoryID),
					startDate=VALUES(startDate),
					endDate=VALUES(endDate),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Code', $this->_Code);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':Location', $this->_Location);
					$oStmt->bindValue(':TerritoryID', $this->_TerritoryID);
					$oStmt->bindValue(':StartDate', $this->_StartDate);
					$oStmt->bindValue(':EndDate', $this->_EndDate);
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userSignupCodes
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
	 * @return mofilmUserSignupCode
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Code = '';
		$this->_Description = '';
		$this->_Location = '';
		$this->_TerritoryID = 232;
		$this->_StartDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_EndDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
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
		$string .= " Code[$this->_Code] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " Location[$this->_Location] $newLine";
		$string .= " TerritoryID[$this->_TerritoryID] $newLine";
		$string .= " StartDate[$this->_StartDate] $newLine";
		$string .= " EndDate[$this->_EndDate] $newLine";
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
		$className = 'mofilmUserSignupCode';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Code\" value=\"$this->_Code\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Location\" value=\"$this->_Location\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"TerritoryID\" value=\"$this->_TerritoryID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"StartDate\" value=\"$this->_StartDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"EndDate\" value=\"$this->_EndDate\" type=\"datetime\" /> $newLine";
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
			$valid = $this->checkCode($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkLocation($message);
		}
		if ( $valid ) {
			$valid = $this->checkStartDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkEndDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateDate($message);
		}
		if ( $valid && !$this->getID() ) {
			$oObject = self::getInstanceByCode($this->getCode());
			if ( $oObject->getID() > 0 ) {
				$valid = false;
				$message = "An event with code {$this->getCode()} already exists";
			}
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
	 * Checks that $_Code has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCode(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Code) && $this->_Code !== '' ) {
			$inMessage .= "{$this->_Code} is not a valid value for Code";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Code) > 20 ) {
			$inMessage .= "Code cannot be more than 20 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Code) <= 1 ) {
			$inMessage .= "Code must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Description has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkDescription(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Description) && $this->_Description !== '' ) {
			$inMessage .= "{$this->_Description} is not a valid value for Description";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) > 255 ) {
			$inMessage .= "Description cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Location has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkLocation(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Location) && $this->_Location !== '' ) {
			$inMessage .= "{$this->_Location} is not a valid value for Location";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Location) > 255 ) {
			$inMessage .= "Location cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Location) <= 1 ) {
			$inMessage .= "Location must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_StartDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkStartDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_StartDate) && $this->_StartDate !== '' ) {
			$inMessage .= "{$this->_StartDate} is not a valid value for StartDate";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_EndDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkEndDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_EndDate) && $this->_EndDate !== '' ) {
			$inMessage .= "{$this->_EndDate} is not a valid value for EndDate";
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
	 * @return mofilmUserSignupCode
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
	 * @return mofilmUserSignupCode
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
	 * Return value of $_Code
	 *
	 * @return string
	 * @access public
	 */
	function getCode() {
		return $this->_Code;
	}

	/**
	 * Set $_Code to Code
	 *
	 * @param string $inCode
	 * @return mofilmUserSignupCode
	 * @access public
	 */
	function setCode($inCode) {
		if ( $inCode !== $this->_Code ) {
			$this->_Code = $inCode;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Description
	 *
	 * @return string
	 * @access public
	 */
	function getDescription() {
		return $this->_Description;
	}

	/**
	 * Set $_Description to Description
	 *
	 * @param string $inDescription
	 * @return mofilmUserSignupCode
	 * @access public
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Location
	 *
	 * @return string
	 * @access public
	 */
	function getLocation() {
		return $this->_Location;
	}

	/**
	 * Set $_Location to Location
	 *
	 * @param string $inLocation
	 * @return mofilmUserSignupCode
	 * @access public
	 */
	function setLocation($inLocation) {
		if ( $inLocation !== $this->_Location ) {
			$this->_Location = $inLocation;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_TerritoryID
	 *
	 * @return integer
	 */
	function getTerritoryID() {
		return $this->_TerritoryID;
	}
	
	/**
	 * Returns the territory object
	 * 
	 * @return mofilmTerritory
	 */
	function getTerritory() {
		return mofilmTerritory::getInstance($this->getTerritoryID());
	}
	
	/**
	 * Set $_TerritoryID to $inTerritoryID
	 *
	 * @param integer $inTerritoryID
	 * @return mofilmUserSignupCode
	 */
	function setTerritoryID($inTerritoryID) {
		if ( $inTerritoryID !== $this->_TerritoryID ) {
			$this->_TerritoryID = $inTerritoryID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_StartDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getStartDate() {
		return $this->_StartDate;
	}

	/**
	 * Set $_StartDate to StartDate
	 *
	 * @param datetime $inStartDate
	 * @return mofilmUserSignupCode
	 * @access public
	 */
	function setStartDate($inStartDate) {
		if ( $inStartDate !== $this->_StartDate ) {
			$this->_StartDate = $inStartDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_EndDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getEndDate() {
		return $this->_EndDate;
	}

	/**
	 * Set $_EndDate to EndDate
	 *
	 * @param datetime $inEndDate
	 * @return mofilmUserSignupCode
	 * @access public
	 */
	function setEndDate($inEndDate) {
		if ( $inEndDate !== $this->_EndDate ) {
			$this->_EndDate = $inEndDate;
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
	 * @return mofilmUserSignupCode
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
	 * @return mofilmUserSignupCode
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
	 * @return mofilmUserSignupCode
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}