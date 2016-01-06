<?php
/**
 * commsTrigger
 *
 * Stored in commsTrigger.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsTrigger
 * @category commsTrigger
 * @version $Rev: 10 $
 */


/**
 * commsTrigger Class
 *
 * Provides access to records in comms.triggers
 *
 * Creating a new record:
 * <code>
 * $oCommsTrigger = new commsTrigger();
 * $oCommsTrigger->setTriggerID($inTriggerID);
 * $oCommsTrigger->setInboundTypeID($inInboundTypeID);
 * $oCommsTrigger->setTriggerField($inTriggerField);
 * $oCommsTrigger->setTriggerValue($inTriggerValue);
 * $oCommsTrigger->setComment($inComment);
 * $oCommsTrigger->setExternalReference($inExternalReference);
 * $oCommsTrigger->setActive($inActive);
 * $oCommsTrigger->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsTrigger = new commsTrigger($inTriggerID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsTrigger = new commsTrigger();
 * $oCommsTrigger->setTriggerID($inTriggerID);
 * $oCommsTrigger->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsTrigger = commsTrigger::getInstance($inTriggerID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage commsTrigger
 * @category commsTrigger
 */
class commsTrigger implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsTrigger
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
	 * Stores $_TriggerID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_TriggerID;

	/**
	 * Stores $_InboundTypeID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_InboundTypeID;

	/**
	 * Stores $_TriggerField
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_TriggerField;

	/**
	 * Stores $_TriggerValue
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_TriggerValue;
	
	/**
	 * Stores $_ApplicationID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_ApplicationID;
	
	/**
	 * Stores $_Comment
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Comment;

	/**
	 * Stores $_ExternalReference
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ExternalReference;

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
	 * Returns a new instance of commsTrigger
	 *
	 * @param integer $inTriggerID
	 * @return commsTrigger
	 */
	function __construct($inTriggerID = null) {
		$this->reset();
		if ( $inTriggerID !== null ) {
			$this->setTriggerID($inTriggerID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsTrigger containing non-unique properties
	 *
	 * @param integer $inInboundTypeID
	 * @param string $inTriggerField
	 * @param string $inTriggerValue
	 * @param integer $inApplicationID
	 * @param string $inComment
	 * @param string $inExternalReference
	 * @param integer $inActive
	 * @return commsTrigger
	 * @static
	 */
	public static function factory($inInboundTypeID = null, $inTriggerField = null, $inTriggerValue = null, $inApplicationID = null, $inComment = null, $inExternalReference = null, $inActive = null) {
		$oObject = new commsTrigger;
		if ( $inInboundTypeID !== null ) {
			$oObject->setInboundTypeID($inInboundTypeID);
		}
		if ( $inTriggerField !== null ) {
			$oObject->setTriggerField($inTriggerField);
		}
		if ( $inTriggerValue !== null ) {
			$oObject->setTriggerValue($inTriggerValue);
		}
		if ( $inApplicationID !== null ) {
			$oObject->setApplicationID($inApplicationID);
		}
		if ( $inComment !== null ) {
			$oObject->setComment($inComment);
		}
		if ( $inExternalReference !== null ) {
			$oObject->setExternalReference($inExternalReference);
		}
		if ( $inActive !== null ) {
			$oObject->setActive($inActive);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsTrigger by primary key
	 *
	 * @param integer $inTriggerID
	 * @return commsTrigger
	 * @static
	 */
	public static function getInstance($inTriggerID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inTriggerID]) ) {
			return self::$_Instances[$inTriggerID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsTrigger();
		$oObject->setTriggerID($inTriggerID);
		if ( $oObject->load() ) {
			self::$_Instances[$inTriggerID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of commsTrigger by unique key (triggerValue)
	 *
	 * @param string $inTriggerValue
	 * @return commsTrigger
	 * @static
	 */
	public static function getInstanceByTriggerValue($inTriggerValue) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inTriggerValue]) ) {
			return self::$_Instances[$inTriggerValue];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsTrigger();
		$oObject->setTriggerValue($inTriggerValue);
		if ( $oObject->load() ) {
			self::$_Instances[$inTriggerValue] = $oObject;
		}
		return $oObject;
	}
	
	/**
	 * Attempts to find a triggerID based on the supplied prs|trigger pair, returns valid trigger on success
	 *
	 * @param string $inTriggerField
	 * @param string $inTriggerValue
	 * @param boolean $inActive
	 * @return commsTrigger
	 * @static
	 */
	public static function find($inTriggerField, $inTriggerValue, $inActive = true) {
		$return = false;
		if ( strlen($inTriggerField) > 1 && strlen($inTriggerValue) > 1 ) {
			$query = '
				SELECT triggers.*
				  FROM '.system::getConfig()->getDatabase('comms').'.triggers
				 WHERE triggerField = :TriggerField AND triggerValue = :TriggerValue';
			if ( $inActive === true ) {
				$query .= ' AND active = :Active';
			}
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':TriggerField', $inTriggerField);
			$oStmt->bindValue(':TriggerValue', $inTriggerValue);
			$oStmt->bindValue(':Active', $inActive);
			if ( $oStmt->execute() ) {
				$res = $oStmt->fetchAll();
				if ( is_array($res) && count($res) == 1 ) {
					$return = new commsTrigger();
					$return->loadFromArray($res[0]);
				}
			}
			$oStmt->closeCursor();
		}
		return $return;
	}

	/**
	 * Returns an array of objects of commsTrigger
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.triggers';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsTrigger();
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
			SELECT triggerID, inboundTypeID, triggerField, triggerValue, applicationID, comment, externalReference, active
			  FROM '.system::getConfig()->getDatabase('comms').'.triggers';

		$where = array();
		if ( $this->_TriggerID !== 0 ) {
			$where[] = ' triggerID = :TriggerID ';
		}
		if ( $this->_TriggerValue !== '' ) {
			$where[] = ' triggerValue = :TriggerValue ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_TriggerID !== 0 ) {
				$oStmt->bindValue(':TriggerID', $this->_TriggerID);
			}
			if ( $this->_TriggerValue !== '' ) {
				$oStmt->bindValue(':TriggerValue', $this->_TriggerValue);
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
		$this->setTriggerID((int)$inArray['triggerID']);
		$this->setInboundTypeID((int)$inArray['inboundTypeID']);
		$this->setTriggerField($inArray['triggerField']);
		$this->setTriggerValue($inArray['triggerValue']);
		$this->setApplicationID((int)$inArray['applicationID']);
		$this->setComment($inArray['comment']);
		$this->setExternalReference($inArray['externalReference']);
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
				throw new commsException($message);
			}
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.triggers
					( triggerID, inboundTypeID, triggerField, triggerValue, applicationID, comment, externalReference, active)
				VALUES
					(:TriggerID, :InboundTypeID, :TriggerField, :TriggerValue, :ApplicationID, :Comment, :ExternalReference, :Active)
				ON DUPLICATE KEY UPDATE
					inboundTypeID=VALUES(inboundTypeID),
					triggerField=VALUES(triggerField),
					triggerValue=VALUES(triggerValue),
					applicationID=VALUES(applicationID),
					comment=VALUES(comment),
					externalReference=VALUES(externalReference),
					active=VALUES(active)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':TriggerID', $this->_TriggerID);
					$oStmt->bindValue(':InboundTypeID', $this->_InboundTypeID);
					$oStmt->bindValue(':TriggerField', $this->_TriggerField);
					$oStmt->bindValue(':TriggerValue', $this->_TriggerValue);
					$oStmt->bindValue(':ApplicationID', $this->_ApplicationID);
					$oStmt->bindValue(':Comment', $this->_Comment);
					$oStmt->bindValue(':ExternalReference', $this->_ExternalReference);
					$oStmt->bindValue(':Active', $this->_Active);

					if ( $oStmt->execute() ) {
						if ( !$this->getTriggerID() ) {
							$this->setTriggerID($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.triggers
			WHERE
				triggerID = :TriggerID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':TriggerID', $this->_TriggerID);

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
	 * @return commsTrigger
	 */
	function reset() {
		$this->_TriggerID = 0;
		$this->_InboundTypeID = 0;
		$this->_TriggerField = '';
		$this->_TriggerValue = '';
		$this->_ApplicationID = 0;
		$this->_Comment = null;
		$this->_ExternalReference = null;
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
		$string .= " TriggerID[$this->_TriggerID] $newLine";
		$string .= " InboundTypeID[$this->_InboundTypeID] $newLine";
		$string .= " TriggerField[$this->_TriggerField] $newLine";
		$string .= " TriggerValue[$this->_TriggerValue] $newLine";
		$string .= " ApplicationID[$this->_ApplicationID] $newLine";
		$string .= " Comment[$this->_Comment] $newLine";
		$string .= " ExternalReference[$this->_ExternalReference] $newLine";
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
		$className = 'commsTrigger';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"TriggerID\" value=\"$this->_TriggerID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"InboundTypeID\" value=\"$this->_InboundTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"TriggerField\" value=\"$this->_TriggerField\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"TriggerValue\" value=\"$this->_TriggerValue\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"ApplicationID\" value=\"$this->_ApplicationID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Comment\" value=\"$this->_Comment\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"ExternalReference\" value=\"$this->_ExternalReference\" type=\"string\" /> $newLine";
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
			$valid = $this->checkTriggerID($message);
		}
		if ( $valid ) {
			$valid = $this->checkInboundTypeID($message);
		}
		if ( $valid ) {
			$valid = $this->checkTriggerField($message);
		}
		if ( $valid ) {
			$valid = $this->checkTriggerValue($message);
		}
		if ( $valid ) {
			$valid = $this->checkComment($message);
		}
		if ( $valid ) {
			$valid = $this->checkExternalReference($message);
		}
		if ( $valid ) {
			$valid = $this->checkActive($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_TriggerID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkTriggerID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_TriggerID) && $this->_TriggerID !== 0 ) {
			$inMessage .= "{$this->_TriggerID} is not a valid value for TriggerID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_InboundTypeID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkInboundTypeID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_InboundTypeID) && $this->_InboundTypeID !== 0 ) {
			$inMessage .= "{$this->_InboundTypeID} is not a valid value for InboundTypeID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_TriggerField has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkTriggerField(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_TriggerField) && $this->_TriggerField !== '' ) {
			$inMessage .= "{$this->_TriggerField} is not a valid value for TriggerField";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_TriggerField) > 40 ) {
			$inMessage .= "TriggerField cannot be more than 40 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_TriggerField) <= 1 ) {
			$inMessage .= "TriggerField must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_TriggerValue has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkTriggerValue(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_TriggerValue) && $this->_TriggerValue !== '' ) {
			$inMessage .= "{$this->_TriggerValue} is not a valid value for TriggerValue";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_TriggerValue) > 100 ) {
			$inMessage .= "TriggerValue cannot be more than 100 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_TriggerValue) <= 1 ) {
			$inMessage .= "TriggerValue must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Comment has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkComment(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Comment) && $this->_Comment !== null && $this->_Comment !== '' ) {
			$inMessage .= "{$this->_Comment} is not a valid value for Comment";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Comment) > 50 ) {
			$inMessage .= "Comment cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Comment) <= 1 ) {
			$inMessage .= "Comment must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_ExternalReference has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkExternalReference(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ExternalReference) && $this->_ExternalReference !== null && $this->_ExternalReference !== '' ) {
			$inMessage .= "{$this->_ExternalReference} is not a valid value for ExternalReference";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_ExternalReference) > 50 ) {
			$inMessage .= "ExternalReference cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_ExternalReference) <= 1 ) {
			$inMessage .= "ExternalReference must be more than 1 character";
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
	 * @return commsTrigger
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
		return $this->_TriggerID;
	}

	/**
	 * Return value of $_TriggerID
	 *
	 * @return integer
	 * @access public
	 */
	function getTriggerID() {
		return $this->_TriggerID;
	}

	/**
	 * Set $_TriggerID to TriggerID
	 *
	 * @param integer $inTriggerID
	 * @return commsTrigger
	 * @access public
	 */
	function setTriggerID($inTriggerID) {
		if ( $inTriggerID !== $this->_TriggerID ) {
			$this->_TriggerID = $inTriggerID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_InboundTypeID
	 *
	 * @return integer
	 * @access public
	 */
	function getInboundTypeID() {
		return $this->_InboundTypeID;
	}

	/**
	 * Set $_InboundTypeID to InboundTypeID
	 *
	 * @param integer $inInboundTypeID
	 * @return commsTrigger
	 * @access public
	 */
	function setInboundTypeID($inInboundTypeID) {
		if ( $inInboundTypeID !== $this->_InboundTypeID ) {
			$this->_InboundTypeID = $inInboundTypeID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_TriggerField
	 *
	 * @return string
	 * @access public
	 */
	function getTriggerField() {
		return $this->_TriggerField;
	}

	/**
	 * Set $_TriggerField to TriggerField
	 *
	 * @param string $inTriggerField
	 * @return commsTrigger
	 * @access public
	 */
	function setTriggerField($inTriggerField) {
		if ( $inTriggerField !== $this->_TriggerField ) {
			$this->_TriggerField = $inTriggerField;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the trigger value
	 *
	 * @return string
	 * @access public
	 */
	function getTriggerValue() {
		return $this->_TriggerValue;
	}

	/**
	 * Trigger value is a pipe (|) separated string of usually PRS|Keyword
	 * 
	 * For example: PRS could be a long number 1234567890 or an SMS shortcode
	 * 12345. The keyword is the text value that will be looked for in a message
	 * e.g. order, help etc. Note: stop and variations should not be used as
	 * they are typically reserved as stop keywords.
	 *
	 * @param string $inTriggerValue
	 * @return commsTrigger
	 * @access public
	 */
	function setTriggerValue($inTriggerValue) {
		if ( $inTriggerValue !== $this->_TriggerValue ) {
			$this->_TriggerValue = $inTriggerValue;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_ApplicationID
	 *
	 * @return integer
	 */
	function getApplicationID() {
		return $this->_ApplicationID;
	}
	
	/**
	 * Set $_ApplicationID to $inApplicationID
	 *
	 * @param integer $inApplicationID
	 * @return commsTrigger
	 */
	function setApplicationID($inApplicationID) {
		if ( $inApplicationID !== $this->_ApplicationID ) {
			$this->_ApplicationID = $inApplicationID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Comment
	 *
	 * @return string
	 * @access public
	 */
	function getComment() {
		return $this->_Comment;
	}

	/**
	 * Set $_Comment to Comment
	 *
	 * @param string $inComment
	 * @return commsTrigger
	 * @access public
	 */
	function setComment($inComment) {
		if ( $inComment !== $this->_Comment ) {
			$this->_Comment = $inComment;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_ExternalReference
	 *
	 * @return string
	 * @access public
	 */
	function getExternalReference() {
		return $this->_ExternalReference;
	}

	/**
	 * Set $_ExternalReference to ExternalReference
	 *
	 * @param string $inExternalReference
	 * @return commsTrigger
	 * @access public
	 */
	function setExternalReference($inExternalReference) {
		if ( $inExternalReference !== $this->_ExternalReference ) {
			$this->_ExternalReference = $inExternalReference;
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
	 * @return commsTrigger
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
	 * Returns the PRS component of the triggerValue, or first component of the | separated value
	 *
	 * @return false|string
	 */
	function getPrs() {
		return $this->_splitTriggerValue('prs');
	}
	
	/**
	 * Returns the keyword trigger from the triggerValue (SMS triggers), or the second component of the | separated value
	 *
	 * @return false|string
	 */
	function getTrigger() {
		return $this->_splitTriggerValue('trigger');
	}
	
	/**
	 * Splits the triggerValue property and returns a specific component
	 *
	 * @param enum(prs,trigger) $type
	 * @return false|string
	 * @access protected
	 */
	protected function _splitTriggerValue($type = 'prs') {
		if ( $this->_TriggerValue ) {
			$prs = $trigger = false;
			list($prs, $trigger) = explode('|', $this->_TriggerValue, 2);
			switch ( $type ) {
				case 'prs': $res = $prs; break;
				case 'trigger': $res = $trigger; break;
			}
			return $res;
		}
		return false;
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
	 * @return commsTrigger
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}