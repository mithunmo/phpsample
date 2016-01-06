<?php
/**
 * commsInboundMessageQueue
 *
 * Stored in commsInboundMessageQueue.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage inbound
 * @category commsInboundMessageQueue
 * @version $Rev: 10 $
 */


/**
 * commsInboundMessageQueue Class
 *
 * Provides access to records in comms.inboundMessagesQueue
 *
 * Creating a new record:
 * <code>
 * $oCommsInboundMessageQueue = new commsInboundMessageQueue();
 * $oCommsInboundMessageQueue->setReceived($inReceived);
 * $oCommsInboundMessageQueue->setMessageID($inMessageID);
 * $oCommsInboundMessageQueue->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsInboundMessageQueue = new commsInboundMessageQueue($inMessageID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsInboundMessageQueue = new commsInboundMessageQueue();
 * $oCommsInboundMessageQueue->setMessageID($inMessageID);
 * $oCommsInboundMessageQueue->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsInboundMessageQueue = commsInboundMessageQueue::getInstance($inMessageID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage inbound
 * @category commsInboundMessageQueue
 */
class commsInboundMessageQueue implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsInboundMessageQueue
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
	 * Stores $_Received
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_Received;

	/**
	 * Stores $_MessageID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MessageID;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of commsInboundMessageQueue
	 *
	 * @param integer $inMessageID
	 * @return commsInboundMessageQueue
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
	 * Creates a new commsInboundMessageQueue containing non-unique properties
	 *
	 * @param datetime $inReceived
	 * @return commsInboundMessageQueue
	 * @static
	 */
	public static function factory($inReceived = null) {
		$oObject = new commsInboundMessageQueue;
		if ( $inReceived !== null ) {
			$oObject->setReceived($inReceived);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsInboundMessageQueue by primary key
	 *
	 * @param integer $inMessageID
	 * @return commsInboundMessageQueue
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
		$oObject = new commsInboundMessageQueue();
		$oObject->setMessageID($inMessageID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMessageID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of commsInboundMessageQueue by unique key (uniqueMsg)
	 *
	 * @param integer $inMessageID
	 * @return commsInboundMessageQueue
	 * @static
	 */
	public static function getInstanceByUniqueMsg($inMessageID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMessageID]) ) {
			return self::$_Instances[$inMessageID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsInboundMessageQueue();
		$oObject->setMessageID($inMessageID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMessageID] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsInboundMessageQueue
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.inboundMessagesQueue';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsInboundMessageQueue();
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
			SELECT received, messageID
			  FROM '.system::getConfig()->getDatabase('comms').'.inboundMessagesQueue';

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
		$this->setReceived($inArray['received']);
		$this->setMessageID((int)$inArray['messageID']);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.inboundMessagesQueue
					( received, messageID)
				VALUES
					(:Received, :MessageID)
				ON DUPLICATE KEY UPDATE
					received=VALUES(received)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':Received', $this->_Received);
					$oStmt->bindValue(':MessageID', $this->_MessageID);

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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.inboundMessagesQueue
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
	 * @return commsInboundMessageQueue
	 */
	function reset() {
		$this->_Received = '0000-00-00 00:00:00';
		$this->_MessageID = 0;
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
		$string .= " Received[$this->_Received] $newLine";
		$string .= " MessageID[$this->_MessageID] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsInboundMessageQueue';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"Received\" value=\"$this->_Received\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"MessageID\" value=\"$this->_MessageID\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkReceived($message);
		}
		if ( $valid ) {
			$valid = $this->checkMessageID($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_Received has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkReceived(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Received) && $this->_Received !== '' ) {
			$inMessage .= "{$this->_Received} is not a valid value for Received";
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
	 * @return commsInboundMessageQueue
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
	 * Return value of $_Received
	 *
	 * @return datetime
	 * @access public
	 */
	function getReceived() {
		return $this->_Received;
	}

	/**
	 * Set $_Received to Received
	 *
	 * @param datetime $inReceived
	 * @return commsInboundMessageQueue
	 * @access public
	 */
	function setReceived($inReceived) {
		if ( $inReceived !== $this->_Received ) {
			$this->_Received = $inReceived;
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
	 * Set $_MessageID to MessageID
	 *
	 * @param integer $inMessageID
	 * @return commsInboundMessageQueue
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
	 * @return commsInboundMessageQueue
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}