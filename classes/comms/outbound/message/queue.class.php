<?php
/**
 * commsOutboundMessageQueue
 *
 * Stored in commsOutboundMessageQueue.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageQueue
 * @version $Rev: 10 $
 */


/**
 * commsOutboundMessageQueue Class
 *
 * Provides access to records in comms.outboundMessagesQueue
 *
 * Creating a new record:
 * <code>
 * $oCommsOutboundMessageQueue = new commsOutboundMessageQueue();
 * $oCommsOutboundMessageQueue->setScheduled($inScheduled);
 * $oCommsOutboundMessageQueue->setMessageID($inMessageID);
 * $oCommsOutboundMessageQueue->setTransactionID($inTransactionID);
 * $oCommsOutboundMessageQueue->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsOutboundMessageQueue = new commsOutboundMessageQueue($inMessageID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsOutboundMessageQueue = new commsOutboundMessageQueue();
 * $oCommsOutboundMessageQueue->setMessageID($inMessageID);
 * $oCommsOutboundMessageQueue->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsOutboundMessageQueue = commsOutboundMessageQueue::getInstance($inMessageID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageQueue
 */
class commsOutboundMessageQueue implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsOutboundMessageQueue
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
	 * Stores $_Scheduled
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_Scheduled;

	/**
	 * Stores $_MessageID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MessageID;

	/**
	 * Stores $_TransactionID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_TransactionID;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of commsOutboundMessageQueue
	 *
	 * @param integer $inMessageID
	 * @return commsOutboundMessageQueue
	 */
	function __construct($inMessageID = null) {
		$this->reset();
		if ( $inMessageID !== null ) {
			$this->setMessageID($inMessageID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsOutboundMessageQueue containing non-unique properties
	 *
	 * @param datetime $inScheduled
	 * @param integer $inTransactionID
	 * @return commsOutboundMessageQueue
	 * @static
	 */
	public static function factory($inScheduled = null, $inTransactionID = null) {
		$oObject = new commsOutboundMessageQueue;
		if ( $inScheduled !== null ) {
			$oObject->setScheduled($inScheduled);
		}
		if ( $inTransactionID !== null ) {
			$oObject->setTransactionID($inTransactionID);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsOutboundMessageQueue by primary key
	 *
	 * @param integer $inMessageID
	 * @return commsOutboundMessageQueue
	 * @static
	 */
	public static function getInstance($inMessageID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMessageID]) ) {
			return self::$_Instances[$inMessageID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsOutboundMessageQueue();
		$oObject->setMessageID($inMessageID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMessageID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of commsOutboundMessageQueue by unique key (messageID)
	 *
	 * @param integer $inMessageID
	 * @return commsOutboundMessageQueue
	 * @static
	 */
	public static function getInstanceByMessageID($inMessageID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMessageID]) ) {
			return self::$_Instances[$inMessageID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsOutboundMessageQueue();
		$oObject->setMessageID($inMessageID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMessageID] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsOutboundMessageQueue
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesQueue';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsOutboundMessageQueue();
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
			SELECT scheduled, messageID, transactionID
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesQueue';

		$where = array();
		if ( $this->_MessageID !== 0 ) {
			$where[] = ' messageID = :MessageID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_MessageID !== 0 ) {
				$oStmt->bindValue(':MessageID', $this->_MessageID);
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
		$this->setScheduled($inArray['scheduled']);
		$this->setMessageID((int)$inArray['messageID']);
		$this->setTransactionID((int)$inArray['transactionID']);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.outboundMessagesQueue
					( scheduled, messageID, transactionID)
				VALUES
					(:Scheduled, :MessageID, :TransactionID)
				ON DUPLICATE KEY UPDATE
					scheduled=VALUES(scheduled),
					transactionID=VALUES(transactionID)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':Scheduled', $this->_Scheduled);
					$oStmt->bindValue(':MessageID', $this->_MessageID);
					$oStmt->bindValue(':TransactionID', $this->_TransactionID);

					if ( $oStmt->execute() ) {
						if ( !$this->getMessageID() ) {
							$this->setMessageID($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesQueue
			WHERE
				messageID = :MessageID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MessageID', $this->_MessageID);

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
	 * @return commsOutboundMessageQueue
	 */
	function reset() {
		$this->_Scheduled = '0000-00-00 00:00:00';
		$this->_MessageID = 0;
		$this->_TransactionID = 0;
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
		$string .= " Scheduled[$this->_Scheduled] $newLine";
		$string .= " MessageID[$this->_MessageID] $newLine";
		$string .= " TransactionID[$this->_TransactionID] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsOutboundMessageQueue';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"Scheduled\" value=\"$this->_Scheduled\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"MessageID\" value=\"$this->_MessageID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"TransactionID\" value=\"$this->_TransactionID\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkScheduled($message);
		}
		if ( $valid ) {
			$valid = $this->checkMessageID($message);
		}
		if ( $valid ) {
			$valid = $this->checkTransactionID($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_Scheduled has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkScheduled(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Scheduled) && $this->_Scheduled !== '' ) {
			$inMessage .= "{$this->_Scheduled} is not a valid value for Scheduled";
			$isValid = false;
		}
		return $isValid;
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
	 * Checks that $_TransactionID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkTransactionID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_TransactionID) && $this->_TransactionID !== 0 ) {
			$inMessage .= "{$this->_TransactionID} is not a valid value for TransactionID";
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
	 * @return commsOutboundMessageQueue
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
		return $this->_MessageID;
	}

	/**
	 * Return value of $_Scheduled
	 *
	 * @return datetime
	 * @access public
	 */
	function getScheduled() {
		return $this->_Scheduled;
	}

	/**
	 * Set $_Scheduled to Scheduled
	 *
	 * @param datetime $inScheduled
	 * @return commsOutboundMessageQueue
	 * @access public
	 */
	function setScheduled($inScheduled) {
		if ( $inScheduled !== $this->_Scheduled ) {
			$this->_Scheduled = $inScheduled;
			$this->setModified();
		}
		return $this;
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
	 * Returns the message object
	 * 
	 * @return commsOutboundMessage
	 */
	function getMessage() {
		return commsOutboundManager::getInstanceByID($this->getMessageID());
	}

	/**
	 * Set $_MessageID to MessageID
	 *
	 * @param integer $inMessageID
	 * @return commsOutboundMessageQueue
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
	 * Return value of $_TransactionID
	 *
	 * @return integer
	 * @access public
	 */
	function getTransactionID() {
		return $this->_TransactionID;
	}

	/**
	 * Set $_TransactionID to TransactionID
	 *
	 * @param integer $inTransactionID
	 * @return commsOutboundMessageQueue
	 * @access public
	 */
	function setTransactionID($inTransactionID) {
		if ( $inTransactionID !== $this->_TransactionID ) {
			$this->_TransactionID = $inTransactionID;
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
	 * @return commsOutboundMessageQueue
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}