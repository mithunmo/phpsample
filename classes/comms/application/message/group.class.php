<?php
/**
 * commsApplicationMessageGroup
 *
 * Stored in commsApplicationMessageGroup.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsApplicationMessageGroup
 * @category commsApplicationMessageGroup
 * @version $Rev: 10 $
 */


/**
 * commsApplicationMessageGroup Class
 *
 * Provides access to records in comms.applicationMessageGroups
 *
 * Creating a new record:
 * <code>
 * $oCommsApplicationMessageGroup = new commsApplicationMessageGroup();
 * $oCommsApplicationMessageGroup->setMessageGroupID($inMessageGroupID);
 * $oCommsApplicationMessageGroup->setMessageType($inMessageType);
 * $oCommsApplicationMessageGroup->setDescription($inDescription);
 * $oCommsApplicationMessageGroup->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsApplicationMessageGroup = new commsApplicationMessageGroup($inMessageGroupID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsApplicationMessageGroup = new commsApplicationMessageGroup();
 * $oCommsApplicationMessageGroup->setMessageGroupID($inMessageGroupID);
 * $oCommsApplicationMessageGroup->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsApplicationMessageGroup = commsApplicationMessageGroup::getInstance($inMessageGroupID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage commsApplicationMessageGroup
 * @category commsApplicationMessageGroup
 */
class commsApplicationMessageGroup implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsApplicationMessageGroup
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
	 * Stores $_MessageGroupID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MessageGroupID;

	/**
	 * Stores $_MessageType
	 *
	 * @var string (MESSAGETYPE_SMS,MESSAGETYPE_EMAIL,)
	 * @access protected
	 */
	protected $_MessageType;
	const MESSAGETYPE_SMS = 'SMS';
	const MESSAGETYPE_EMAIL = 'Email';

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
	 * Returns a new instance of commsApplicationMessageGroup
	 *
	 * @param integer $inMessageGroupID
	 * @return commsApplicationMessageGroup
	 */
	function __construct($inMessageGroupID = null) {
		$this->reset();
		if ( $inMessageGroupID !== null ) {
			$this->setMessageGroupID($inMessageGroupID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsApplicationMessageGroup containing non-unique properties
	 *
	 * @param string $inMessageType
	 * @param string $inDescription
	 * @return commsApplicationMessageGroup
	 * @static
	 */
	public static function factory($inMessageType = null, $inDescription = null) {
		$oObject = new commsApplicationMessageGroup;
		if ( $inMessageType !== null ) {
			$oObject->setMessageType($inMessageType);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsApplicationMessageGroup by primary key
	 *
	 * @param integer $inMessageGroupID
	 * @return commsApplicationMessageGroup
	 * @static
	 */
	public static function getInstance($inMessageGroupID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMessageGroupID]) ) {
			return self::$_Instances[$inMessageGroupID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsApplicationMessageGroup();
		$oObject->setMessageGroupID($inMessageGroupID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMessageGroupID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsApplicationMessageGroup
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.applicationMessageGroups';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsApplicationMessageGroup();
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
			SELECT messageGroupID, messageType, description
			  FROM '.system::getConfig()->getDatabase('comms').'.applicationMessageGroups';

		$where = array();
		if ( $this->_MessageGroupID !== 0 ) {
			$where[] = ' messageGroupID = :MessageGroupID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_MessageGroupID !== 0 ) {
				$oStmt->bindValue(':MessageGroupID', $this->_MessageGroupID);
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
		$this->setMessageGroupID((int)$inArray['messageGroupID']);
		$this->setMessageType($inArray['messageType']);
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
				throw new commsException($message);
			}
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.applicationMessageGroups
					( messageGroupID, messageType, description)
				VALUES
					(:MessageGroupID, :MessageType, :Description)
				ON DUPLICATE KEY UPDATE
					messageType=VALUES(messageType),
					description=VALUES(description)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MessageGroupID', $this->_MessageGroupID);
					$oStmt->bindValue(':MessageType', $this->_MessageType);
					$oStmt->bindValue(':Description', $this->_Description);

					if ( $oStmt->execute() ) {
						if ( !$this->getMessageGroupID() ) {
							$this->setMessageGroupID($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.applicationMessageGroups
			WHERE
				messageGroupID = :MessageGroupID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MessageGroupID', $this->_MessageGroupID);

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
	 * @return commsApplicationMessageGroup
	 */
	function reset() {
		$this->_MessageGroupID = 0;
		$this->_MessageType = null;
		$this->_Description = '';
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
		$string .= " MessageGroupID[$this->_MessageGroupID] $newLine";
		$string .= " MessageType[$this->_MessageType] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsApplicationMessageGroup';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"MessageGroupID\" value=\"$this->_MessageGroupID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"MessageType\" value=\"$this->_MessageType\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
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
			$valid = $this->checkMessageGroupID($message);
		}
		if ( $valid ) {
			$valid = $this->checkMessageType($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_MessageGroupID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMessageGroupID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MessageGroupID) && $this->_MessageGroupID !== 0 ) {
			$inMessage .= "{$this->_MessageGroupID} is not a valid value for MessageGroupID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_MessageType has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMessageType(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_MessageType) && $this->_MessageType !== null && $this->_MessageType !== '' ) {
			$inMessage .= "{$this->_MessageType} is not a valid value for MessageType";
			$isValid = false;
		}
		if ( $isValid && $this->_MessageType != '' && !in_array($this->_MessageType, array(self::MESSAGETYPE_SMS, self::MESSAGETYPE_EMAIL)) ) {
			$inMessage .= "MessageType must be one of MESSAGETYPE_SMS, MESSAGETYPE_EMAIL";
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
		if ( $isValid && strlen($this->_Description) > 50 ) {
			$inMessage .= "Description cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
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
	 * @return commsApplicationMessageGroup
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
		return $this->_MessageGroupID;
	}

	/**
	 * Return value of $_MessageGroupID
	 *
	 * @return integer
	 * @access public
	 */
	function getMessageGroupID() {
		return $this->_MessageGroupID;
	}

	/**
	 * Set $_MessageGroupID to MessageGroupID
	 *
	 * @param integer $inMessageGroupID
	 * @return commsApplicationMessageGroup
	 * @access public
	 */
	function setMessageGroupID($inMessageGroupID) {
		if ( $inMessageGroupID !== $this->_MessageGroupID ) {
			$this->_MessageGroupID = $inMessageGroupID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_MessageType
	 *
	 * @return string
	 * @access public
	 */
	function getMessageType() {
		return $this->_MessageType;
	}

	/**
	 * Set $_MessageType to MessageType
	 *
	 * @param string $inMessageType
	 * @return commsApplicationMessageGroup
	 * @access public
	 */
	function setMessageType($inMessageType) {
		if ( $inMessageType !== $this->_MessageType ) {
			$this->_MessageType = $inMessageType;
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
	 * @return commsApplicationMessageGroup
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
	 * @return commsApplicationMessageGroup
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}