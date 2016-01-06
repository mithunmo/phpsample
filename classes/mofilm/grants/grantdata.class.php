<?php
/**
 * grantdata
 *
 * Stored in grantdata.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm/grants
 * @subpackage grantdata
 * @category grantdata
 * @version $Rev: 840 $
 */


/**
 * grantdata Class
 *
 * Provides access to records in mofilm_content.grantsData
 *
 * Creating a new record:
 * <code>
 * $oGrantdatum = new grantdata();
 * $oGrantdatum->setID($inID);
 * $oGrantdatum->setGrantID($inGrantID);
 * $oGrantdatum->setParamName($inParamName);
 * $oGrantdatum->setParamValue($inParamValue);
 * $oGrantdatum->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oGrantdatum = new grantdata($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oGrantdatum = new grantdata();
 * $oGrantdatum->setID($inID);
 * $oGrantdatum->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oGrantdatum = grantdata::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm/grants
 * @subpackage grantdata
 * @category grantdata
 */
class grantdata implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of grantdata
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
	 * Stores $_GrantID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GrantID;

	/**
	 * Stores $_ParamName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ParamName;

	/**
	 * Stores $_ParamValue
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ParamValue;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of grantdata
	 *
	 * @param integer $inID
	 * @return grantdata
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
	 * Get an instance of grantdata by primary key
	 *
	 * @param integer $inID
	 * @return grantdata
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
		$oObject = new grantdata();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of grantdata
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
			SELECT ID, grantID, paramName, paramValue
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.grantsData
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new grantdata();
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
			SELECT ID, grantID, paramName, paramValue
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.grantsData';

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
		$this->setGrantID((int)$inArray['grantID']);
		$this->setParamName($inArray['paramName']);
		$this->setParamValue($inArray['paramValue']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.grantsData
					( ID, grantID, paramName, paramValue )
				VALUES
					( :ID, :GrantID, :ParamName, :ParamValue )
				ON DUPLICATE KEY UPDATE
					grantID=VALUES(grantID),
					paramName=VALUES(paramName),
					paramValue=VALUES(paramValue)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':GrantID', $this->getGrantID());
				$oStmt->bindValue(':ParamName', $this->getParamName());
				$oStmt->bindValue(':ParamValue', $this->getParamValue());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.grantsData
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
	 * @return grantdata
	 */
	function reset() {
		$this->_ID = 0;
		$this->_GrantID = 0;
		$this->_ParamName = '';
		$this->_ParamValue = '';
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
	 * @return grantdata
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
			'_GrantID' => array(
				'number' => array(),
			),
			'_ParamName' => array(
				'string' => array('min' => 1,'max' => 100,),
			),
			'_ParamValue' => array(
				'string' => array('min' => 1,'max' => 200,),
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
	 * @return grantdata
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
	 * grantdata::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return grantdata
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
	 * @return grantdata
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_GrantID
	 *
	 * @return integer
 	 */
	function getGrantID() {
		return $this->_GrantID;
	}

	/**
	 * Set the object property _GrantID to $inGrantID
	 *
	 * @param integer $inGrantID
	 * @return grantdata
	 */
	function setGrantID($inGrantID) {
		if ( $inGrantID !== $this->_GrantID ) {
			$this->_GrantID = $inGrantID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ParamName
	 *
	 * @return string
 	 */
	function getParamName() {
		return $this->_ParamName;
	}

	/**
	 * Set the object property _ParamName to $inParamName
	 *
	 * @param string $inParamName
	 * @return grantdata
	 */
	function setParamName($inParamName) {
		if ( $inParamName !== $this->_ParamName ) {
			$this->_ParamName = $inParamName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ParamValue
	 *
	 * @return string
 	 */
	function getParamValue() {
		return $this->_ParamValue;
	}

	/**
	 * Set the object property _ParamValue to $inParamValue
	 *
	 * @param string $inParamValue
	 * @return grantdata
	 */
	function setParamValue($inParamValue) {
		if ( $inParamValue !== $this->_ParamValue ) {
			$this->_ParamValue = $inParamValue;
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
	 * @return grantdata
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
        
        public function getValue($GrantID,$Param) {
            $query = '
            SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.grantsData
            WHERE
                grantID='.$GrantID.' AND paramName="'.$Param.'"';

            $oStmt = dbManager::getInstance()->prepare($query);
        
            if ( $oStmt->execute() ) {
                foreach ($oStmt as $row) {
                $oObject = new grantdata();
                $oObject->loadFromArray($row);
                $list = $oObject;
            }
            }
            $oStmt->closeCursor();
            return $list;
        }
        
        
}