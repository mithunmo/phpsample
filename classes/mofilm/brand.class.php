<?php
/**
 * mofilmBrand
 *
 * Stored in mofilmBrand.class.php
 *
 * @author Poulami Chakraborty
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmBrand
 * @category mofilmBrand
 * @version $Rev: 840 $
 */


/**
 * mofilmBrand Class
 *
 * Provides access to records in mofilm_content.brands
 *
 * Creating a new record:
 * <code>
 * $oMofilmBrand = new mofilmBrand();
 * $oMofilmBrand->setID($inID);
 * $oMofilmBrand->setName($inName);
 * $oMofilmBrand->setCompany($inCompany);
 * $oMofilmBrand->setCorporateID($inCorporateID);
 * $oMofilmBrand->setIndustryID($inIndustryID);
 * $oMofilmBrand->setFirstname($inFirstname);
 * $oMofilmBrand->setLastname($inLastname);
 * $oMofilmBrand->setAddress($inAddress);
 * $oMofilmBrand->setCity($inCity);
 * $oMofilmBrand->setCountry($inCountry);
 * $oMofilmBrand->setEmail($inEmail);
 * $oMofilmBrand->setPhone($inPhone);
 * $oMofilmBrand->setStatus($inStatus);
 * $oMofilmBrand->setCreateDate($inCreateDate);
 * $oMofilmBrand->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmBrand = new mofilmBrand($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmBrand = new mofilmBrand();
 * $oMofilmBrand->setID($inID);
 * $oMofilmBrand->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmBrand = mofilmBrand::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmBrand
 * @category mofilmBrand
 */
class mofilmBrand implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmBrand
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
	 * Stores $_Name
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Name;

	/**
	 * Stores $_Company
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Company;

	/**
	 * Stores $_CorporateID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_CorporateID;

	/**
	 * Stores $_IndustryID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_IndustryID;

	/**
	 * Stores $_Firstname
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Firstname;

	/**
	 * Stores $_Lastname
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Lastname;

	/**
	 * Stores $_Address
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Address;

	/**
	 * Stores $_City
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_City;

	/**
	 * Stores $_Country
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Country;

	/**
	 * Stores $_Email
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Email;

	/**
	 * Stores $_Phone
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Phone;

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
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmBrand
	 *
	 * @param integer $inID
	 * @return mofilmBrand
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
	 * Get an instance of mofilmBrand by primary key
	 *
	 * @param integer $inID
	 * @return mofilmBrand
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
		$oObject = new mofilmBrand();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get instance of mofilmBrand by unique key (ID)
	 *
	 * @param integer $inID
	 * @return mofilmBrand
	 * @static
	 */
	public static function getInstanceByID($inID) {
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
		$oObject = new mofilmBrand();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmBrand
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $params = null) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();
                if(isset($params['CorporateID']) &&  $params['CorporateID']!= 0){
                         $query = '
                            SELECT ID, name, company, corporateID, firstname, lastname, address, city, country, email, phone, status, createDate
                              FROM '.system::getConfig()->getDatabase('mofilm_content').'.brands
                             WHERE 1';
                       if(isset($params['CorporateID']) &&  $params['CorporateID']!== 0){
                        $query .= ' AND corporateID = '.$params['CorporateID'];
                    }
                }elseif($params['EventID'] && $params['EventID'] != 0){
                    $query = '
			SELECT brands.*
			FROM '.system::getConfig()->getDatabase('mofilm_content').'.brands JOIN 
                        '.system::getConfig()->getDatabase('mofilm_content').'.sources ON sources.brandID = brands.ID JOIN     
                        '.system::getConfig()->getDatabase('mofilm_content').'.events ON sources.eventID = events.ID			 
                        WHERE events.ID = '.$params['EventID'];
                }else{
                    $query = '
			SELECT ID, name, company, corporateID,industryID, firstname, lastname, address, city, country, email, phone, status, createDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.brands
			 WHERE 1';
                }
                
                $query .= ' ORDER BY brands.name ASC ';
                
		if ( $inOffset != null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmBrand();
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
			SELECT ID, name, company, corporateID,industryID, firstname, lastname, address, city, country, email, phone, status, createDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.brands';

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
		$this->setName($inArray['name']);
		$this->setCompany($inArray['company']);
		$this->setCorporateID((int)$inArray['corporateID']);
                $this->setIndustryID((int)$inArray['industryID']);
		$this->setFirstname($inArray['firstname']);
		$this->setLastname($inArray['lastname']);
		$this->setAddress($inArray['address']);
		$this->setCity($inArray['city']);
		$this->setCountry($inArray['country']);
		$this->setEmail($inArray['email']);
		$this->setPhone($inArray['phone']);
		$this->setStatus((int)$inArray['status']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.brands
					( ID, name, company, corporateID,industryID, firstname, lastname, address, city, country, email, phone, status, createDate )
				VALUES
					( :ID, :Name, :Company, :CorporateID, :IndustryID, :Firstname, :Lastname, :Address, :City, :Country, :Email, :Phone, :Status, :CreateDate )
				ON DUPLICATE KEY UPDATE
					name=VALUES(name),
					company=VALUES(company),
					corporateID=VALUES(corporateID),
                                        industryID=VALUES(industryID),
					firstname=VALUES(firstname),
					lastname=VALUES(lastname),
					address=VALUES(address),
					city=VALUES(city),
					country=VALUES(country),
					email=VALUES(email),
					phone=VALUES(phone),
					status=VALUES(status),
					createDate=VALUES(createDate)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':Name', $this->getName());
				$oStmt->bindValue(':Company', $this->getCompany());
				$oStmt->bindValue(':CorporateID', $this->getCorporateID());
                                $oStmt->bindValue(':IndustryID', $this->getIndustryID());
				$oStmt->bindValue(':Firstname', $this->getFirstname());
				$oStmt->bindValue(':Lastname', $this->getLastname());
				$oStmt->bindValue(':Address', $this->getAddress());
				$oStmt->bindValue(':City', $this->getCity());
				$oStmt->bindValue(':Country', $this->getCountry());
				$oStmt->bindValue(':Email', $this->getEmail());
				$oStmt->bindValue(':Phone', $this->getPhone());
				$oStmt->bindValue(':Status', $this->getStatus());
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.brands
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
	 * @return mofilmBrand
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Name = '';
		$this->_Company = '';
		$this->_CorporateID = 0;
                $this->_IndustryID = 0;
		$this->_Firstname = '';
		$this->_Lastname = '';
		$this->_Address = '';
		$this->_City = '';
		$this->_Country = '';
		$this->_Email = '';
		$this->_Phone = '';
		$this->_Status = 0;
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
	 * @return mofilmBrand
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
                        '_Name' => array(
                                'string' => array('min' => 1,'max' => 32,),
                        ),
                        '_CorporateID' => array(
                                'number' => array(),
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
	 * @return mofilmBrand
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
	 * mofilmBrand::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmBrand
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
	 * @return mofilmBrand
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
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
	 * @return mofilmBrand
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Company
	 *
	 * @return string
 	 */
	function getCompany() {
		return $this->_Company;
	}

	/**
	 * Set the object property _Company to $inCompany
	 *
	 * @param string $inCompany
	 * @return mofilmBrand
	 */
	function setCompany($inCompany) {
		if ( $inCompany !== $this->_Company ) {
			$this->_Company = $inCompany;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_CorporateID
	 *
	 * @return integer
 	 */
	function getCorporateID() {
		return $this->_CorporateID;
	}

	/**
	 * Set the object property _CorporateID to $inCorporateID
	 *
	 * @param integer $inCorporateID
	 * @return mofilmBrand
	 */
	function setCorporateID($inCorporateID) {
		if ( $inCorporateID !== $this->_CorporateID ) {
			$this->_CorporateID = $inCorporateID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_IndustryID
	 *
	 * @return integer
 	 */
	function getIndustryID() {
		return $this->_IndustryID;
	}

	/**
	 * Set the object property _IndustryID to $inIndustryID
	 *
	 * @param integer $inIndustryID
	 * @return mofilmBrand
	 */
	function setIndustryID($inIndustryID) {
		if ( $inIndustryID !== $this->_IndustryID ) {
			$this->_IndustryID = $inIndustryID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Firstname
	 *
	 * @return string
 	 */
	function getFirstname() {
		return $this->_Firstname;
	}

	/**
	 * Set the object property _Firstname to $inFirstname
	 *
	 * @param string $inFirstname
	 * @return mofilmBrand
	 */
	function setFirstname($inFirstname) {
		if ( $inFirstname !== $this->_Firstname ) {
			$this->_Firstname = $inFirstname;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Lastname
	 *
	 * @return string
 	 */
	function getLastname() {
		return $this->_Lastname;
	}

	/**
	 * Set the object property _Lastname to $inLastname
	 *
	 * @param string $inLastname
	 * @return mofilmBrand
	 */
	function setLastname($inLastname) {
		if ( $inLastname !== $this->_Lastname ) {
			$this->_Lastname = $inLastname;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Address
	 *
	 * @return string
 	 */
	function getAddress() {
		return $this->_Address;
	}

	/**
	 * Set the object property _Address to $inAddress
	 *
	 * @param string $inAddress
	 * @return mofilmBrand
	 */
	function setAddress($inAddress) {
		if ( $inAddress !== $this->_Address ) {
			$this->_Address = $inAddress;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_City
	 *
	 * @return string
 	 */
	function getCity() {
		return $this->_City;
	}

	/**
	 * Set the object property _City to $inCity
	 *
	 * @param string $inCity
	 * @return mofilmBrand
	 */
	function setCity($inCity) {
		if ( $inCity !== $this->_City ) {
			$this->_City = $inCity;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Country
	 *
	 * @return string
 	 */
	function getCountry() {
		return $this->_Country;
	}

	/**
	 * Set the object property _Country to $inCountry
	 *
	 * @param string $inCountry
	 * @return mofilmBrand
	 */
	function setCountry($inCountry) {
		if ( $inCountry !== $this->_Country ) {
			$this->_Country = $inCountry;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Email
	 *
	 * @return string
 	 */
	function getEmail() {
		return $this->_Email;
	}

	/**
	 * Set the object property _Email to $inEmail
	 *
	 * @param string $inEmail
	 * @return mofilmBrand
	 */
	function setEmail($inEmail) {
		if ( $inEmail !== $this->_Email ) {
			$this->_Email = $inEmail;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Phone
	 *
	 * @return string
 	 */
	function getPhone() {
		return $this->_Phone;
	}

	/**
	 * Set the object property _Phone to $inPhone
	 *
	 * @param string $inPhone
	 * @return mofilmBrand
	 */
	function setPhone($inPhone) {
		if ( $inPhone !== $this->_Phone ) {
			$this->_Phone = $inPhone;
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
	 * @return mofilmBrand
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
	 * @return mofilmBrand
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
	 * @return mofilmBrand
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}