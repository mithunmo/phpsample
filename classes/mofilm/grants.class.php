<?php
/**
 * mofilmGrants
 *
 * Stored in mofilmGrants.class.php
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmGrants
 * @category mofilmGrants
 * @version $Rev: 840 $
 */


/**
 * mofilmGrants Class
 *
 * Provides access to records in mofilm_content.grants
 *
 * Creating a new record:
 * <code>
 * $oMofilmGrant = new mofilmGrants();
 * $oMofilmGrant->setID($inID);
 * $oMofilmGrant->setSourceID($inSourceID);
 * $oMofilmGrant->setEndDate($inEndDate);
 * $oMofilmGrant->setCurrencySymbol($inCurrencySymbol);
 * $oMofilmGrant->setTotalGrants($inTotalGrants);
 * $oMofilmGrant->setDescription($inDescription);
 * $oMofilmGrant->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmGrant = new mofilmGrants($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmGrant = new mofilmGrants();
 * $oMofilmGrant->setID($inID);
 * $oMofilmGrant->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmGrant = mofilmGrants::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmGrants
 * @category mofilmGrants
 */
class mofilmGrants implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmGrants
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
	 * Stores $_SourceID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SourceID;

	/**
	 * Stores $_EndDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_EndDate;

	/**
	 * Stores $_CurrencySymbol
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_CurrencySymbol;

	/**
	 * Stores $_TotalGrants
	 *
	 * @var float 
	 * @access protected
	 */
	protected $_TotalGrants;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmGrants
	 *
	 * @param integer $inID
	 * @return mofilmGrants
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
	 * Get an instance of mofilmGrants by primary key
	 *
	 * @param integer $inID
	 * @return mofilmGrants
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
		$oObject = new mofilmGrants();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmGrants by SourceID
	 *
	 * @param integer $inSourceID
	 * @return mofilmGrants
	 * @static
	 */
	public static function getInstanceBySourceID($inSourceID) {
		$key = $inSourceID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmGrants();
		$oObject->setSourceID($inSourceID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmGrants
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
			SELECT ID, sourceID, endDate, currencySymbol, totalGrants, description
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.grants
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmGrants();
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
			SELECT ID, sourceID, endDate, currencySymbol, totalGrants, description
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.grants';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}
		
		if ( $this->_SourceID !== 0 ) {
			$where[] = ' sourceID = :sourceID ';
			$values[':sourceID'] = $this->getSourceID();
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
		$this->setSourceID((int)$inArray['sourceID']);
		$this->setEndDate($inArray['endDate']);
		$this->setCurrencySymbol($inArray['currencySymbol']);
		$this->setTotalGrants($inArray['totalGrants']);
		$this->setDescription($inArray['description']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.grants
					( ID, sourceID, endDate, currencySymbol, totalGrants, description )
				VALUES
					( :ID, :SourceID, :EndDate, :CurrencySymbol, :TotalGrants, :Description )
				ON DUPLICATE KEY UPDATE
					sourceID=VALUES(sourceID),
					endDate=VALUES(endDate),
					currencySymbol=VALUES(currencySymbol),
					totalGrants=VALUES(totalGrants),
					description=VALUES(description)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':SourceID', $this->getSourceID());
				$oStmt->bindValue(':EndDate', $this->getEndDate());
				$oStmt->bindValue(':CurrencySymbol', $this->getCurrencySymbol());
				$oStmt->bindValue(':TotalGrants', $this->getTotalGrants());
				$oStmt->bindValue(':Description', $this->getDescription());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.grants
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
	 * @return mofilmGrants
	 */
	function reset() {
		$this->_ID = 0;
		$this->_SourceID = 0;
		$this->_EndDate = new systemDateTime(date('Y-m-d 23:50:00'), system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_CurrencySymbol = '$';
		$this->_TotalGrants = 0.00;
		$this->_Description = '';
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
	 * @return mofilmGrants
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
			'_SourceID' => array(
				'number' => array(),
			),
			'_EndDate' => array(
				'dateTime' => array(),
			),
			'_CurrencySymbol' => array(
				'string' => array('min' => 1,'max' => 3,),
			),
			'_TotalGrants' => array(
				'float' => array(),
			),
			'_Description' => array(
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
	 * @return mofilmGrants
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
	 * mofilmGrants::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmGrants
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
	 * @return mofilmGrants
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SourceID
	 *
	 * @return integer
 	 */
	function getSourceID() {
		return $this->_SourceID;
	}
	
	/**
	 * Returns the Mofilm Source Object
	 * 
	 * @return mofilmSource
	 */
	function getSource(){
	    return mofilmSource::getInstance($this->getSourceID());
	}

	/**
	 * Set the object property _SourceID to $inSourceID
	 *
	 * @param integer $inSourceID
	 * @return mofilmGrants
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_EndDate
	 *
	 * @return systemDateTime
 	 */
	function getEndDate() {
		return $this->_EndDate;
	}

	/**
	 * Set the object property _EndDate to $inEndDate
	 *
	 * @param systemDateTime $inEndDate
	 * @return mofilmGrants
	 */
	function setEndDate($inEndDate) {
		if ( $inEndDate !== $this->_EndDate ) {
			if ( !$inEndDate instanceof DateTime ) {
				$inEndDate = new systemDateTime($inEndDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_EndDate = $inEndDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_CurrencySymbol
	 *
	 * @return string
 	 */
	function getCurrencySymbol() {
		return $this->_CurrencySymbol;
	}

	/**
	 * Set the object property _CurrencySymbol to $inCurrencySymbol
	 *
	 * @param string $inCurrencySymbol
	 * @return mofilmGrants
	 */
	function setCurrencySymbol($inCurrencySymbol) {
		if ( $inCurrencySymbol !== $this->_CurrencySymbol ) {
			$this->_CurrencySymbol = $inCurrencySymbol;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_TotalGrants
	 *
	 * @return float
 	 */
	function getTotalGrants() {
		return $this->_TotalGrants;
	}

	/**
	 * Set the object property _TotalGrants to $inTotalGrants
	 *
	 * @param float $inTotalGrants
	 * @return mofilmGrants
	 */
	function setTotalGrants($inTotalGrants) {
		if ( $inTotalGrants !== $this->_TotalGrants ) {
			$this->_TotalGrants = $inTotalGrants;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Description
	 *
	 * @return string
 	 */
	function getDescription() {
		return $this->_Description;
	}

	/**
	 * Set the object property _Description to $inDescription
	 *
	 * @param string $inDescription
	 * @return mofilmGrants
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
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
	 * @return mofilmGrants
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	/**
	 * Returns true if source is open
	 * 
	 * @return boolean
	 */
	function isOpen() {
		$now = date(system::getConfig()->getDatabaseDatetimeFormat());
		$end_date = $this->getEndDate();
		
		if ( $now <= $end_date ) {
			return true;
		}
	}
	
	/**
	 * Returns true if source is closed
	 * 
	 * @return boolean
	 */
	function isClosed() {
		$now = date(system::getConfig()->getDatabaseDatetimeFormat());
		$end_date = $this->getEndDate();
		
		if ( $now > $end_date ) {
			return true;
		}
	}
        
        public function getGrantID($sourceID) {
            $query = '
            SELECT ID FROM '.system::getConfig()->getDatabase('mofilm_content').'.grants
            WHERE
                sourceID='.$sourceID;

            $oStmt = dbManager::getInstance()->prepare($query);
        
            if ( $oStmt->execute() ) {
                foreach ($oStmt as $row) {
                $oObject = new mofilmSourceBudget();
                $oObject->loadFromArray($row);
                $list = $oObject;
            }
            }
            $oStmt->closeCursor();
            return $list;
        }
}