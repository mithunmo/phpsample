<?php
/**
 * mofilmUserSourcePermissions
 *
 * Stored in mofilmUserSourcePermissions.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserSourcePermissions
 * @category mofilmUserSourcePermissions
 * @version $Rev: 840 $
 */


/**
 * mofilmUserSourcePermissions Class
 *
 * Provides access to records in mofilm_content.userSourcePermissions
 *
 * Creating a new record:
 * <code>
 * $oMofilmUserSourcePermission = new mofilmUserSourcePermissions();
 * $oMofilmUserSourcePermission->setID($inID);
 * $oMofilmUserSourcePermission->setEventID($inEventID);
 * $oMofilmUserSourcePermission->setSourceID($inSourceID);
 * $oMofilmUserSourcePermission->setUserID($inUserID);
 * $oMofilmUserSourcePermission->setHasEvent($inHasEvent);
 * $oMofilmUserSourcePermission->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserSourcePermission = new mofilmUserSourcePermissions($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmUserSourcePermission = new mofilmUserSourcePermissions();
 * $oMofilmUserSourcePermission->setID($inID);
 * $oMofilmUserSourcePermission->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserSourcePermission = mofilmUserSourcePermissions::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmUserSourcePermissions
 * @category mofilmUserSourcePermissions
 */
class mofilmUserSourcePermissions implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmUserSourcePermissions
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
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_HasEvent
	 *
	 * @var string (HASEVENT_0,HASEVENT_1,)
	 * @access protected
	 */
	protected $_HasEvent;
	const HASEVENT_0 = '0';
	const HASEVENT_1 = '1';

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmUserSourcePermissions
	 *
	 * @param integer $inID
	 * @return mofilmUserSourcePermissions
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
	 * Get an instance of mofilmUserSourcePermissions by primary key
	 *
	 * @param integer $inID
	 * @return mofilmUserSourcePermissions
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
		$oObject = new mofilmUserSourcePermissions();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmUserSourcePermissions
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
			SELECT ID, EventID, SourceID, UserID, hasEvent
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userSourcePermissions
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUserSourcePermissions();
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
			SELECT ID, EventID, SourceID, UserID, hasEvent
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userSourcePermissions';

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
		$this->setEventID((int)$inArray['EventID']);
		$this->setSourceID((int)$inArray['SourceID']);
		$this->setUserID((int)$inArray['UserID']);
		$this->setHasEvent($inArray['hasEvent']);
		$this->setModified(false);
	}
        
        function getAllFlag($sourceID)
        {
            $list = array();
            if(isset($sourceID))
            {
                if($sourceID > 0 )
                {
            $query = '
			SELECT hasEvent
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userSourcePermissions
			 WHERE SourceID ='.$sourceID .' LIMIT 1' ;

		

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUserSourcePermissions();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();
                }
		return $list;
            }
        }

        function getUserListID($sourceID)
        {
            if(isset($sourceID))
            {
                $list = array();
                if($sourceID > 0)
                {
            $query = '
			SELECT *
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userSourcePermissions
			 WHERE SourceID ='.$sourceID .' ORDER BY ID';

		

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUserSourcePermissions();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();
            }
		return $list;
            }
        }
        
        function deleteAllSources($sourceID)
        {
            $query = '
            DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userSourcePermissions
            WHERE
                SourceID='.$sourceID;

            $oStmt = dbManager::getInstance()->prepare($query);
        
            if ( $oStmt->execute() ) {
            $oStmt->closeCursor();
            $this->reset();
            return true;
            }

            return false;
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userSourcePermissions
					( ID, EventID, SourceID, UserID, hasEvent )
				VALUES
					( :ID, :EventID, :SourceID, :UserID, :HasEvent )
				ON DUPLICATE KEY UPDATE
					EventID=VALUES(EventID),
					SourceID=VALUES(SourceID),
					UserID=VALUES(UserID),
					hasEvent=VALUES(hasEvent)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':EventID', $this->getEventID());
				$oStmt->bindValue(':SourceID', $this->getSourceID());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':HasEvent', $this->getHasEvent());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userSourcePermissions
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
	 * @return mofilmUserSourcePermissions
	 */
	function reset() {
		$this->_ID = 0;
		$this->_EventID = 0;
		$this->_SourceID = 0;
		$this->_UserID = null;
		$this->_HasEvent = '';
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
	 * @return mofilmUserSourcePermissions
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
			'_EventID' => array(
				'number' => array(),
			),
			'_SourceID' => array(
				'number' => array(),
			),
			'_UserID' => array(
				'number' => array(),
			),
			'_HasEvent' => array(
				'inArray' => array('values' => array(self::HASEVENT_0, self::HASEVENT_1),),
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
	 * @return mofilmUserSourcePermissions
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
	 * mofilmUserSourcePermissions::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmUserSourcePermissions
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
	 * @return mofilmUserSourcePermissions
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_EventID
	 *
	 * @return integer
 	 */
	function getEventID() {
		return $this->_EventID;
	}

	/**
	 * Set the object property _EventID to $inEventID
	 *
	 * @param integer $inEventID
	 * @return mofilmUserSourcePermissions
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
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
	 * @return mofilmUserSourcePermissions
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
	 * @return mofilmUserSourcePermissions
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_HasEvent
	 *
	 * @return string
 	 */
	function getHasEvent() {
		return $this->_HasEvent;
	}

	/**
	 * Set the object property _HasEvent to $inHasEvent
	 *
	 * @param string $inHasEvent
	 * @return mofilmUserSourcePermissions
	 */
	function setHasEvent($inHasEvent) {
		if ( $inHasEvent !== $this->_HasEvent ) {
			$this->_HasEvent = $inHasEvent;
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
	 * @return mofilmUserSourcePermissions
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}