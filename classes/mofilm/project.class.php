<?php
/**
 * mofilmProject
 *
 * Stored in mofilmProject.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmProject
 * @category mofilmProject
 * @version $Rev: 840 $
 */


/**
 * mofilmProject Class
 *
 * Provides access to records in mofilm_content.projects
 *
 * Creating a new record:
 * <code>
 * $oMofilmProject = new mofilmProject();
 * $oMofilmProject->setId($inId);
 * $oMofilmProject->setName($inName);
 * $oMofilmProject->setDesc($inDesc);
 * $oMofilmProject->setBrandid($inBrandid);
 * $oMofilmProject->setStartdate($inStartdate);
 * $oMofilmProject->setEnddate($inEnddate);
 * $oMofilmProject->setStatus($inStatus);
 * $oMofilmProject->setLength($inLength);
 * $oMofilmProject->setBudget($inBudget);
 * $oMofilmProject->setType($inType);
 * $oMofilmProject->setHash($inHash);
 * $oMofilmProject->setCreateDate($inCreateDate);
 * $oMofilmProject->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmProject = new mofilmProject($inId);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmProject = new mofilmProject();
 * $oMofilmProject->setId($inId);
 * $oMofilmProject->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmProject = mofilmProject::getInstance($inId);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmProject
 * @category mofilmProject
 */
class mofilmProject implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmProject
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
	 * Stores $_Name
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Name;

	/**
	 * Stores $_Desc
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Desc;

	/**
	 * Stores $_Brandid
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Brandid;

	/**
	 * Stores $_Startdate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_Startdate;

	/**
	 * Stores $_Enddate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_Enddate;

	/**
	 * Stores $_Status
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Status;

	/**
	 * Stores $_Length
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Length;

	/**
	 * Stores $_Budget
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Budget;

	/**
	 * Stores $_Type
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Type;

	/**
	 * Stores $_Hash
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Hash;

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
	 * Returns a new instance of mofilmProject
	 *
	 * @param integer $inId
	 * @return mofilmProject
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
	 * Get an instance of mofilmProject by primary key
	 *
	 * @param integer $inId
	 * @return mofilmProject
	 * @static
	 */
	public static function getInstance($inId) {
		$key = $inId;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmProject();
		$oObject->setId($inId);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get instance of mofilmProject by unique key (hash)
	 *
	 * @param string $inHash
	 * @return mofilmProject
	 * @static
	 */
	public static function getInstanceByHash($inHash) {
		$key = $inHash;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmProject();
		$oObject->setHash($inHash);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmProject
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
			SELECT id, name, desc, brandid, startdate, enddate, status, length, budget, type, hash, createDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.projects
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmProject();
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
			SELECT id, name, desc, brandid, startdate, enddate, status, length, budget, type, hash, createDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.projects';

		$where = array();
		if ( $this->_Id !== 0 ) {
			$where[] = ' id = :Id ';
			$values[':Id'] = $this->getId();
		}
		if ( $this->_Hash !== '' ) {
			$where[] = ' hash = :Hash ';
			$values[':Hash'] = $this->getHash();
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
		$this->setId((int)$inArray['id']);
		$this->setName($inArray['name']);
		$this->setDesc($inArray['desc']);
		$this->setBrandid((int)$inArray['brandid']);
		$this->setStartdate($inArray['startdate']);
		$this->setEnddate($inArray['enddate']);
		$this->setStatus((int)$inArray['status']);
		$this->setLength((int)$inArray['length']);
		$this->setBudget($inArray['budget']);
		$this->setType($inArray['type']);
		$this->setHash($inArray['hash']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.projects
					( id, name, desc, brandid, startdate, enddate, status, length, budget, type, hash, createDate )
				VALUES
					( :Id, :Name, :Desc, :Brandid, :Startdate, :Enddate, :Status, :Length, :Budget, :Type, :Hash, :CreateDate )
				ON DUPLICATE KEY UPDATE
					name=VALUES(name),
					desc=VALUES(desc),
					brandid=VALUES(brandid),
					startdate=VALUES(startdate),
					enddate=VALUES(enddate),
					status=VALUES(status),
					length=VALUES(length),
					budget=VALUES(budget),
					type=VALUES(type),
					createDate=VALUES(createDate)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':Id', $this->getId());
				$oStmt->bindValue(':Name', $this->getName());
				$oStmt->bindValue(':Desc', $this->getDesc());
				$oStmt->bindValue(':Brandid', $this->getBrandid());
				$oStmt->bindValue(':Startdate', $this->getStartdate());
				$oStmt->bindValue(':Enddate', $this->getEnddate());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':Length', $this->getLength());
				$oStmt->bindValue(':Budget', $this->getBudget());
				$oStmt->bindValue(':Type', $this->getType());
				$oStmt->bindValue(':Hash', $this->getHash());
				$oStmt->bindValue(':CreateDate', $this->getCreateDate());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.projects
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
	 * @return mofilmProject
	 */
	function reset() {
		$this->_Id = 0;
		$this->_Name = '';
		$this->_Desc = '';
		$this->_Brandid = 0;
		$this->_Startdate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Enddate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Status = 0;
		$this->_Length = 0;
		$this->_Budget = '';
		$this->_Type = '';
		$this->_Hash = '';
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
	 * @return mofilmProject
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
			'_Name' => array(
				'string' => array('min' => 1,'max' => 32,),
			),
			'_Desc' => array(
				'string' => array(),
			),
			'_Brandid' => array(
				'number' => array(),
			),
			'_Startdate' => array(
				'dateTime' => array(),
			),
			'_Enddate' => array(
				'dateTime' => array(),
			),
			'_Status' => array(
				'number' => array(),
			),
			'_Length' => array(
				'number' => array(),
			),
			'_Budget' => array(
				'string' => array('min' => 1,'max' => 32,),
			),
			'_Type' => array(
				'string' => array('min' => 1,'max' => 32,),
			),
			'_Hash' => array(
				'string' => array('min' => 1,'max' => 32,),
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
	 * @return mofilmProject
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
	 * mofilmProject::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmProject
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
	 * @return mofilmProject
	 */
	function setId($inId) {
		if ( $inId !== $this->_Id ) {
			$this->_Id = $inId;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Name
	 *
	 * @return string
 	 */
	function getName() {
		return $this->_Name;
	}

	/**
	 * Set the object property _Name to $inName
	 *
	 * @param string $inName
	 * @return mofilmProject
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Desc
	 *
	 * @return string
 	 */
	function getDesc() {
		return $this->_Desc;
	}

	/**
	 * Set the object property _Desc to $inDesc
	 *
	 * @param string $inDesc
	 * @return mofilmProject
	 */
	function setDesc($inDesc) {
		if ( $inDesc !== $this->_Desc ) {
			$this->_Desc = $inDesc;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Brandid
	 *
	 * @return integer
 	 */
	function getBrandid() {
		return $this->_Brandid;
	}

	/**
	 * Set the object property _Brandid to $inBrandid
	 *
	 * @param integer $inBrandid
	 * @return mofilmProject
	 */
	function setBrandid($inBrandid) {
		if ( $inBrandid !== $this->_Brandid ) {
			$this->_Brandid = $inBrandid;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Startdate
	 *
	 * @return systemDateTime
 	 */
	function getStartdate() {
		return $this->_Startdate;
	}

	/**
	 * Set the object property _Startdate to $inStartdate
	 *
	 * @param systemDateTime $inStartdate
	 * @return mofilmProject
	 */
	function setStartdate($inStartdate) {
		if ( $inStartdate !== $this->_Startdate ) {
			if ( !$inStartdate instanceof DateTime ) {
				$inStartdate = new systemDateTime($inStartdate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_Startdate = $inStartdate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Enddate
	 *
	 * @return systemDateTime
 	 */
	function getEnddate() {
		return $this->_Enddate;
	}

	/**
	 * Set the object property _Enddate to $inEnddate
	 *
	 * @param systemDateTime $inEnddate
	 * @return mofilmProject
	 */
	function setEnddate($inEnddate) {
		if ( $inEnddate !== $this->_Enddate ) {
			if ( !$inEnddate instanceof DateTime ) {
				$inEnddate = new systemDateTime($inEnddate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_Enddate = $inEnddate;
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
	 * @return mofilmProject
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Length
	 *
	 * @return integer
 	 */
	function getLength() {
		return $this->_Length;
	}

	/**
	 * Set the object property _Length to $inLength
	 *
	 * @param integer $inLength
	 * @return mofilmProject
	 */
	function setLength($inLength) {
		if ( $inLength !== $this->_Length ) {
			$this->_Length = $inLength;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Budget
	 *
	 * @return string
 	 */
	function getBudget() {
		return $this->_Budget;
	}

	/**
	 * Set the object property _Budget to $inBudget
	 *
	 * @param string $inBudget
	 * @return mofilmProject
	 */
	function setBudget($inBudget) {
		if ( $inBudget !== $this->_Budget ) {
			$this->_Budget = $inBudget;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Type
	 *
	 * @return string
 	 */
	function getType() {
		return $this->_Type;
	}

	/**
	 * Set the object property _Type to $inType
	 *
	 * @param string $inType
	 * @return mofilmProject
	 */
	function setType($inType) {
		if ( $inType !== $this->_Type ) {
			$this->_Type = $inType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Hash
	 *
	 * @return string
 	 */
	function getHash() {
		return $this->_Hash;
	}

	/**
	 * Set the object property _Hash to $inHash
	 *
	 * @param string $inHash
	 * @return mofilmProject
	 */
	function setHash($inHash) {
		if ( $inHash !== $this->_Hash ) {
			$this->_Hash = $inHash;
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
	 * @return mofilmProject
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
	 * @return mofilmProject
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}