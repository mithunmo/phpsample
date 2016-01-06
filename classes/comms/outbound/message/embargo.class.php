<?php
/**
 * commsOutboundMessageEmbargo
 *
 * Stored in commsOutboundMessageEmbargo.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageEmbargo
 * @version $Rev: 10 $
 */


/**
 * commsOutboundMessageEmbargo Class
 *
 * Provides access to records in comms.outboundMessagesEmbargo
 *
 * Creating a new record:
 * <code>
 * $oCommsOutboundMessageEmbargo = new commsOutboundMessageEmbargo();
 * $oCommsOutboundMessageEmbargo->setMessageID($inMessageID);
 * $oCommsOutboundMessageEmbargo->setRecipient($inRecipient);
 * $oCommsOutboundMessageEmbargo->setState($inState);
 * $oCommsOutboundMessageEmbargo->setExpires($inExpires);
 * $oCommsOutboundMessageEmbargo->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsOutboundMessageEmbargo = new commsOutboundMessageEmbargo($inRecipient);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsOutboundMessageEmbargo = new commsOutboundMessageEmbargo();
 * $oCommsOutboundMessageEmbargo->setRecipient($inRecipient);
 * $oCommsOutboundMessageEmbargo->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsOutboundMessageEmbargo = commsOutboundMessageEmbargo::getInstance($inRecipient);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageEmbargo
 */
class commsOutboundMessageEmbargo implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsOutboundMessageEmbargo
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
	 * Stores $_Recipient
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Recipient;

	/**
	 * Stores $_State
	 *
	 * @var string (STATE_QUEUED,STATE_INPROCESS,)
	 * @access protected
	 */
	protected $_State;
	const STATE_QUEUED = 'Queued';
	const STATE_INPROCESS = 'InProcess';

	/**
	 * Stores $_Expires
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_Expires;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of commsOutboundMessageEmbargo
	 *
	 * @param string $inRecipient
	 * @return commsOutboundMessageEmbargo
	 */
	function __construct($inRecipient = null) {
		$this->reset();
		if ( $inRecipient !== null ) {
			$this->setRecipient($inRecipient);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsOutboundMessageEmbargo containing non-unique properties
	 *
	 * @param integer $inMessageID
	 * @param string $inState
	 * @param datetime $inExpires
	 * @return commsOutboundMessageEmbargo
	 * @static
	 */
	public static function factory($inMessageID = null, $inState = null, $inExpires = null) {
		$oObject = new commsOutboundMessageEmbargo;
		if ( $inMessageID !== null ) {
			$oObject->setMessageID($inMessageID);
		}
		if ( $inState !== null ) {
			$oObject->setState($inState);
		}
		if ( $inExpires !== null ) {
			$oObject->setExpires($inExpires);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsOutboundMessageEmbargo by primary key
	 *
	 * @param string $inRecipient
	 * @return commsOutboundMessageEmbargo
	 * @static
	 */
	public static function getInstance($inRecipient) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inRecipient]) ) {
			return self::$_Instances[$inRecipient];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsOutboundMessageEmbargo();
		$oObject->setRecipient($inRecipient);
		if ( $oObject->load() ) {
			self::$_Instances[$inRecipient] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsOutboundMessageEmbargo
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesEmbargo';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsOutboundMessageEmbargo();
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
			SELECT messageID, recipient, state, expires
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesEmbargo';

		$where = array();
		if ( $this->_Recipient !== '' ) {
			$where[] = ' recipient = :Recipient ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_Recipient !== '' ) {
				$oStmt->bindValue(':Recipient', $this->_Recipient);
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
		$this->setRecipient($inArray['recipient']);
		$this->setState($inArray['state']);
		$this->setExpires($inArray['expires']);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.outboundMessagesEmbargo
					( messageID, recipient, state, expires)
				VALUES
					(:MessageID, :Recipient, :State, :Expires)
				ON DUPLICATE KEY UPDATE
					messageID=VALUES(messageID),
					state=VALUES(state),
					expires=VALUES(expires)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MessageID', $this->_MessageID);
					$oStmt->bindValue(':Recipient', $this->_Recipient);
					$oStmt->bindValue(':State', $this->_State);
					$oStmt->bindValue(':Expires', $this->_Expires);

					if ( $oStmt->execute() ) {
						if ( !$this->getRecipient() ) {
							$this->setRecipient($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesEmbargo
			WHERE
				recipient = :Recipient
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':Recipient', $this->_Recipient);

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
	 * @return commsOutboundMessageEmbargo
	 */
	function reset() {
		$this->_MessageID = 0;
		$this->_Recipient = '';
		$this->_State = 'Queued';
		$this->_Expires = '0000-00-00 00:00:00';
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
		$string .= " Recipient[$this->_Recipient] $newLine";
		$string .= " State[$this->_State] $newLine";
		$string .= " Expires[$this->_Expires] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsOutboundMessageEmbargo';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"MessageID\" value=\"$this->_MessageID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Recipient\" value=\"$this->_Recipient\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"State\" value=\"$this->_State\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Expires\" value=\"$this->_Expires\" type=\"datetime\" /> $newLine";
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
			$valid = $this->checkRecipient($message);
		}
		if ( $valid ) {
			$valid = $this->checkState($message);
		}
		if ( $valid ) {
			$valid = $this->checkExpires($message);
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
	 * Checks that $_Recipient has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkRecipient(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Recipient) && $this->_Recipient !== '' ) {
			$inMessage .= "{$this->_Recipient} is not a valid value for Recipient";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Recipient) > 255 ) {
			$inMessage .= "Recipient cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Recipient) <= 1 ) {
			$inMessage .= "Recipient must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_State has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkState(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_State) && $this->_State !== '' ) {
			$inMessage .= "{$this->_State} is not a valid value for State";
			$isValid = false;
		}
		if ( $isValid && $this->_State != '' && !in_array($this->_State, array(self::STATE_QUEUED, self::STATE_INPROCESS)) ) {
			$inMessage .= "State must be one of STATE_QUEUED, STATE_INPROCESS";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Expires has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkExpires(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Expires) && $this->_Expires !== '' ) {
			$inMessage .= "{$this->_Expires} is not a valid value for Expires";
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
	 * @return commsOutboundMessageEmbargo
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
		return $this->_Recipient;
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
	 * @return commsOutboundMessageEmbargo
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
	 * Return value of $_Recipient
	 *
	 * @return string
	 * @access public
	 */
	function getRecipient() {
		return $this->_Recipient;
	}

	/**
	 * Set $_Recipient to Recipient
	 *
	 * @param string $inRecipient
	 * @return commsOutboundMessageEmbargo
	 * @access public
	 */
	function setRecipient($inRecipient) {
		if ( $inRecipient !== $this->_Recipient ) {
			$this->_Recipient = $inRecipient;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_State
	 *
	 * @return string
	 * @access public
	 */
	function getState() {
		return $this->_State;
	}

	/**
	 * Set $_State to State
	 *
	 * @param string $inState
	 * @return commsOutboundMessageEmbargo
	 * @access public
	 */
	function setState($inState) {
		if ( $inState !== $this->_State ) {
			$this->_State = $inState;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Expires
	 *
	 * @return datetime
	 * @access public
	 */
	function getExpires() {
		return $this->_Expires;
	}

	/**
	 * Set $_Expires to Expires
	 *
	 * @param datetime $inExpires
	 * @return commsOutboundMessageEmbargo
	 * @access public
	 */
	function setExpires($inExpires) {
		if ( $inExpires !== $this->_Expires ) {
			$this->_Expires = $inExpires;
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
	 * @return commsOutboundMessageEmbargo
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}