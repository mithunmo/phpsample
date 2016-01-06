<?php
/**
 * mofilmSourceBudget
 *
 * Stored in mofilmSourceBudget.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSourceBudget
 * @category mofilmSourceBudget
 * @version $Rev: 840 $
 */


/**
 * mofilmSourceBudget Class
 *
 * Provides access to records in mofilm_content.sourceBudget
 *
 * Creating a new record:
 * <code>
 * $oMofilmSourceBudget = new mofilmSourceBudget();
 * $oMofilmSourceBudget->setID($inID);
 * $oMofilmSourceBudget->setSourceID($inSourceID);
 * $oMofilmSourceBudget->setUserID($inUserID);
 * $oMofilmSourceBudget->setGrantBuffer($inGrantBuffer);
 * $oMofilmSourceBudget->setOther($inOther);
 * $oMofilmSourceBudget->setModifiedTime($inModifiedTime);
 * $oMofilmSourceBudget->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmSourceBudget = new mofilmSourceBudget($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmSourceBudget = new mofilmSourceBudget();
 * $oMofilmSourceBudget->setID($inID);
 * $oMofilmSourceBudget->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmSourceBudget = mofilmSourceBudget::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmSourceBudget
 * @category mofilmSourceBudget
 */
class mofilmSourceBudget implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmSourceBudget
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
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_GrantBuffer
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GrantBuffer;

	/**
	 * Stores $_Other
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Other;

	/**
	 * Stores $_ModifiedTime
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_ModifiedTime;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmSourceBudget
	 *
	 * @param integer $inID
	 * @return mofilmSourceBudget
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
	 * Get an instance of mofilmSourceBudget by primary key
	 *
	 * @param integer $inID
	 * @return mofilmSourceBudget
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
		$oObject = new mofilmSourceBudget();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmSourceBudget
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
			SELECT ID, sourceID, userID, grantBuffer, other, modifiedTime
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourceBudget
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmSourceBudget();
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
			SELECT ID, sourceID, userID, grantBuffer, other, modifiedTime
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourceBudget';

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
		$this->setSourceID((int)$inArray['sourceID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setGrantBuffer((int)$inArray['grantBuffer']);
		$this->setOther((int)$inArray['other']);
		$this->setModifiedTime($inArray['modifiedTime']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.sourceBudget
					( ID, sourceID, userID, grantBuffer, other, modifiedTime )
				VALUES
					( :ID, :SourceID, :UserID, :GrantBuffer, :Other, :ModifiedTime )
				ON DUPLICATE KEY UPDATE
					sourceID=VALUES(sourceID),
					userID=VALUES(userID),
					grantBuffer=VALUES(grantBuffer),
					other=VALUES(other),
					modifiedTime=VALUES(modifiedTime)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':SourceID', $this->getSourceID());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':GrantBuffer', $this->getGrantBuffer());
				$oStmt->bindValue(':Other', $this->getOther());
				$oStmt->bindValue(':ModifiedTime', $this->getModifiedTime());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourceBudget
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
	 * @return mofilmSourceBudget
	 */
	function reset() {
		$this->_ID = 0;
		$this->_SourceID = 0;
		$this->_UserID = 0;
		$this->_GrantBuffer = 0;
		$this->_Other = 0;
		$this->_ModifiedTime = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
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
	 * @return mofilmSourceBudget
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
			'_UserID' => array(
				'number' => array(),
			),
			'_GrantBuffer' => array(
				'number' => array(),
			),
			'_Other' => array(
				'number' => array(),
			),
			'_ModifiedTime' => array(
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
	 * @return mofilmSourceBudget
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
	 * mofilmSourceBudget::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmSourceBudget
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
	 * @return mofilmSourceBudget
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
	 * Set the object property _SourceID to $inSourceID
	 *
	 * @param integer $inSourceID
	 * @return mofilmSourceBudget
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
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
	 * @return mofilmSourceBudget
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_GrantBuffer
	 *
	 * @return integer
 	 */
	function getGrantBuffer() {
		return $this->_GrantBuffer;
	}

	/**
	 * Set the object property _GrantBuffer to $inGrantBuffer
	 *
	 * @param integer $inGrantBuffer
	 * @return mofilmSourceBudget
	 */
	function setGrantBuffer($inGrantBuffer) {
		if ( $inGrantBuffer !== $this->_GrantBuffer ) {
			$this->_GrantBuffer = $inGrantBuffer;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Other
	 *
	 * @return integer
 	 */
	function getOther() {
		return $this->_Other;
	}

	/**
	 * Set the object property _Other to $inOther
	 *
	 * @param integer $inOther
	 * @return mofilmSourceBudget
	 */
	function setOther($inOther) {
		if ( $inOther !== $this->_Other ) {
			$this->_Other = $inOther;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ModifiedTime
	 *
	 * @return systemDateTime
 	 */
	function getModifiedTime() {
		return $this->_ModifiedTime;
	}

	/**
	 * Set the object property _ModifiedTime to $inModifiedTime
	 *
	 * @param systemDateTime $inModifiedTime
	 * @return mofilmSourceBudget
	 */
	function setModifiedTime($inModifiedTime) {
		if ( $inModifiedTime !== $this->_ModifiedTime ) {
			if ( !$inModifiedTime instanceof DateTime ) {
				$inModifiedTime = new systemDateTime($inModifiedTime, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_ModifiedTime = $inModifiedTime;
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
	 * @return mofilmSourceBudget
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
        
        public function checkIfBudgetExists($sourceID) {
            $query = '
            SELECT ID FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourceBudget
            WHERE
                sourceID='.$sourceID;

            $oStmt = dbManager::getInstance()->prepare($query);
        
            if ( $oStmt->execute() ) {
                foreach ($oStmt as $row) {
                $oObject = new mofilmSourceBudget();
                $oObject->loadFromArray($row);
                $list = $oObject->getID();
            }
            }
            $oStmt->closeCursor();
            return $list;
        }
        
        public function getBudget($sourceID) {
            $query = '
            SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourceBudget
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