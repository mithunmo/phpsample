<?php
/**
 * commsOutboundMessageTracker
 *
 * Stored in commsOutboundMessageTracker.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageTracker
 * @version $Rev: 10 $
 */


/**
 * commsOutboundMessageTracker Class
 *
 * Provides access to records in comms.outboundMessagesTracking
 *
 * Creating a new record:
 * <code>
 * $oCommsOutboundMessageTracker = new commsOutboundMessageTracker();
 * $oCommsOutboundMessageTracker->setMessageID($inMessageID);
 * $oCommsOutboundMessageTracker->setGatewayID($inGatewayID);
 * $oCommsOutboundMessageTracker->setReference($inReference);
 * $oCommsOutboundMessageTracker->setParts($inParts);
 * $oCommsOutboundMessageTracker->setRemaining($inRemaining);
 * $oCommsOutboundMessageTracker->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsOutboundMessageTracker = new commsOutboundMessageTracker($inGatewayID, $inReference);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsOutboundMessageTracker = new commsOutboundMessageTracker();
 * $oCommsOutboundMessageTracker->setGatewayID($inGatewayID);
 * $oCommsOutboundMessageTracker->setReference($inReference);
 * $oCommsOutboundMessageTracker->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsOutboundMessageTracker = commsOutboundMessageTracker::getInstance($inGatewayID, $inReference);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageTracker
 */
class commsOutboundMessageTracker implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsOutboundMessageTracker
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
	 * Stores $_MessageID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MessageID;

	/**
	 * Stores $_GatewayID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GatewayID;

	/**
	 * Stores $_Reference
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Reference;

	/**
	 * Stores $_Parts
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Parts;

	/**
	 * Stores $_Remaining
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Remaining;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of commsOutboundMessageTracker
	 *
	 * @param integer $inGatewayID
	 * @param string $inReference
	 * @return commsOutboundMessageTracker
	 */
	function __construct($inGatewayID = null, $inReference = null) {
		$this->reset();
		if ( $inGatewayID !== null && $inReference !== null ) {
			$this->setGatewayID($inGatewayID);
			$this->setReference($inReference);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsOutboundMessageTracker containing non-unique properties
	 *
	 * @param integer $inMessageID
	 * @param integer $inParts
	 * @param integer $inRemaining
	 * @return commsOutboundMessageTracker
	 * @static
	 */
	public static function factory($inMessageID = null, $inParts = null, $inRemaining = null) {
		$oObject = new commsOutboundMessageTracker;
		if ( $inMessageID !== null ) {
			$oObject->setMessageID($inMessageID);
		}
		if ( $inParts !== null ) {
			$oObject->setParts($inParts);
		}
		if ( $inRemaining !== null ) {
			$oObject->setRemaining($inRemaining);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsOutboundMessageTracker by primary key
	 *
	 * @param integer $inGatewayID
	 * @param string $inReference
	 * @return commsOutboundMessageTracker
	 * @static
	 */
	public static function getInstance($inGatewayID, $inReference) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inGatewayID.'.'.$inReference]) ) {
			return self::$_Instances[$inGatewayID.'.'.$inReference];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsOutboundMessageTracker();
		$oObject->setGatewayID($inGatewayID);
		$oObject->setReference($inReference);
		if ( $oObject->load() ) {
			self::$_Instances[$inGatewayID.'.'.$inReference] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsOutboundMessageTracker
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesTracking';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsOutboundMessageTracker();
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
			SELECT messageID, gatewayID, reference, parts, remaining
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesTracking';

		$where = array();
		if ( $this->_GatewayID !== 0 ) {
			$where[] = ' gatewayID = :GatewayID ';
		}
		if ( $this->_Reference !== '' ) {
			$where[] = ' reference = :Reference ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_GatewayID !== 0 ) {
				$oStmt->bindValue(':GatewayID', $this->_GatewayID);
			}
			if ( $this->_Reference !== '' ) {
				$oStmt->bindValue(':Reference', $this->_Reference);
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
		$this->setMessageID((int)$inArray['messageID']);
		$this->setGatewayID((int)$inArray['gatewayID']);
		$this->setReference($inArray['reference']);
		$this->setParts((int)$inArray['parts']);
		$this->setRemaining((int)$inArray['remaining']);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.outboundMessagesTracking
					( messageID, gatewayID, reference, parts, remaining)
				VALUES
					(:MessageID, :GatewayID, :Reference, :Parts, :Remaining)
				ON DUPLICATE KEY UPDATE
					messageID=VALUES(messageID),
					parts=VALUES(parts),
					remaining=VALUES(remaining)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MessageID', $this->_MessageID);
					$oStmt->bindValue(':GatewayID', $this->_GatewayID);
					$oStmt->bindValue(':Reference', $this->_Reference);
					$oStmt->bindValue(':Parts', $this->_Parts);
					$oStmt->bindValue(':Remaining', $this->_Remaining);

					if ( $oStmt->execute() ) {
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesTracking
			WHERE
				gatewayID = :GatewayID AND
				reference = :Reference
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':GatewayID', $this->_GatewayID);
			$oStmt->bindValue(':Reference', $this->_Reference);

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
	 * @return commsOutboundMessageTracker
	 */
	function reset() {
		$this->_MessageID = 0;
		$this->_GatewayID = 0;
		$this->_Reference = '';
		$this->_Parts = 0;
		$this->_Remaining = 0;
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
		$string .= " MessageID[$this->_MessageID] $newLine";
		$string .= " GatewayID[$this->_GatewayID] $newLine";
		$string .= " Reference[$this->_Reference] $newLine";
		$string .= " Parts[$this->_Parts] $newLine";
		$string .= " Remaining[$this->_Remaining] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsOutboundMessageTracker';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"MessageID\" value=\"$this->_MessageID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"GatewayID\" value=\"$this->_GatewayID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Reference\" value=\"$this->_Reference\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Parts\" value=\"$this->_Parts\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Remaining\" value=\"$this->_Remaining\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkMessageID($message);
		}
		if ( $valid ) {
			$valid = $this->checkGatewayID($message);
		}
		if ( $valid ) {
			$valid = $this->checkReference($message);
		}
		if ( $valid ) {
			$valid = $this->checkParts($message);
		}
		if ( $valid ) {
			$valid = $this->checkRemaining($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_MessageID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMessageID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MessageID) && $this->_MessageID !== 0 ) {
			$inMessage .= "{$this->_MessageID} is not a valid value for MessageID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_GatewayID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkGatewayID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_GatewayID) && $this->_GatewayID !== 0 ) {
			$inMessage .= "{$this->_GatewayID} is not a valid value for GatewayID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Reference has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkReference(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Reference) && $this->_Reference !== '' ) {
			$inMessage .= "{$this->_Reference} is not a valid value for Reference";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Reference) > 100 ) {
			$inMessage .= "Reference cannot be more than 100 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Reference) <= 1 ) {
			$inMessage .= "Reference must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Parts has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkParts(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Parts) && $this->_Parts !== 0 ) {
			$inMessage .= "{$this->_Parts} is not a valid value for Parts";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Remaining has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkRemaining(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Remaining) && $this->_Remaining !== 0 ) {
			$inMessage .= "{$this->_Remaining} is not a valid value for Remaining";
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
	 * @return commsOutboundMessageTracker
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
		return $this->_GatewayID.'.'.$this->_Reference;
	}

	/**
	 * Return value of $_MessageID
	 *
	 * @return integer
	 * @access public
	 */
	function getMessageID() {
		return $this->_MessageID;
	}

	/**
	 * Set $_MessageID to MessageID
	 *
	 * @param integer $inMessageID
	 * @return commsOutboundMessageTracker
	 * @access public
	 */
	function setMessageID($inMessageID) {
		if ( $inMessageID !== $this->_MessageID ) {
			$this->_MessageID = $inMessageID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_GatewayID
	 *
	 * @return integer
	 * @access public
	 */
	function getGatewayID() {
		return $this->_GatewayID;
	}

	/**
	 * Set $_GatewayID to GatewayID
	 *
	 * @param integer $inGatewayID
	 * @return commsOutboundMessageTracker
	 * @access public
	 */
	function setGatewayID($inGatewayID) {
		if ( $inGatewayID !== $this->_GatewayID ) {
			$this->_GatewayID = $inGatewayID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Reference
	 *
	 * @return string
	 * @access public
	 */
	function getReference() {
		return $this->_Reference;
	}

	/**
	 * Set $_Reference to Reference
	 *
	 * @param string $inReference
	 * @return commsOutboundMessageTracker
	 * @access public
	 */
	function setReference($inReference) {
		if ( $inReference !== $this->_Reference ) {
			$this->_Reference = $inReference;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Parts
	 *
	 * @return integer
	 * @access public
	 */
	function getParts() {
		return $this->_Parts;
	}

	/**
	 * Set $_Parts to Parts
	 *
	 * @param integer $inParts
	 * @return commsOutboundMessageTracker
	 * @access public
	 */
	function setParts($inParts) {
		if ( $inParts !== $this->_Parts ) {
			$this->_Parts = $inParts;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Remaining
	 *
	 * @return integer
	 * @access public
	 */
	function getRemaining() {
		return $this->_Remaining;
	}

	/**
	 * Set $_Remaining to Remaining
	 *
	 * @param integer $inRemaining
	 * @return commsOutboundMessageTracker
	 * @access public
	 */
	function setRemaining($inRemaining) {
		if ( $inRemaining !== $this->_Remaining ) {
			$this->_Remaining = $inRemaining;
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
	 * @return commsOutboundMessageTracker
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}