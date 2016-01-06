<?php
/**
 * commsOutboundMessageTransaction
 *
 * Stored in commsOutboundMessageTransaction.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageTransaction
 * @version $Rev: 10 $
 */


/**
 * commsOutboundMessageTransaction Class
 *
 * Provides access to records in comms.outboundMessagesTransactions
 *
 * Creating a new record:
 * <code>
 * $oCommsOutboundMessageTransaction = new commsOutboundMessageTransaction();
 * $oCommsOutboundMessageTransaction->setMessageID($inMessageID);
 * $oCommsOutboundMessageTransaction->setTransactionID($inTransactionID);
 * $oCommsOutboundMessageTransaction->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsOutboundMessageTransaction = new commsOutboundMessageTransaction($inMessageID, $inTransactionID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsOutboundMessageTransaction = new commsOutboundMessageTransaction();
 * $oCommsOutboundMessageTransaction->setMessageID($inMessageID);
 * $oCommsOutboundMessageTransaction->setTransactionID($inTransactionID);
 * $oCommsOutboundMessageTransaction->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsOutboundMessageTransaction = commsOutboundMessageTransaction::getInstance($inMessageID, $inTransactionID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageTransaction
 */
class commsOutboundMessageTransaction implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsOutboundMessageTransaction
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
	 * Returns a new instance of commsOutboundMessageTransaction
	 *
	 * @param integer $inMessageID
	 * @param integer $inTransactionID
	 * @return commsOutboundMessageTransaction
	 */
	function __construct($inMessageID = null, $inTransactionID = null) {
		$this->reset();
		if ( $inMessageID !== null ) {
			$this->setMessageID($inMessageID);
		}
		if ( $inTransactionID !== null ) {
			$this->setTransactionID($inTransactionID);
		}
		$this->load();
	}

	/**
	 * Creates a new commsOutboundMessageTransaction containing non-unique properties
	 *
	 * @return commsOutboundMessageTransaction
	 * @static
	 */
	public static function factory() {
		$oObject = new commsOutboundMessageTransaction;
		return $oObject;
	}

	/**
	 * Get an instance of commsOutboundMessageTransaction by primary key
	 *
	 * @param integer $inMessageID
	 * @param integer $inTransactionID
	 * @return commsOutboundMessageTransaction
	 * @static
	 */
	public static function getInstance($inMessageID, $inTransactionID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMessageID.'.'.$inTransactionID]) ) {
			return self::$_Instances[$inMessageID.'.'.$inTransactionID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsOutboundMessageTransaction();
		$oObject->setMessageID($inMessageID);
		$oObject->setTransactionID($inTransactionID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMessageID.'.'.$inTransactionID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of commsOutboundMessageTransaction by unique key (messageID)
	 *
	 * @param integer $inMessageID
	 * @param integer $inTransactionID
	 * @return commsOutboundMessageTransaction
	 * @static
	 */
	public static function getInstanceByMessageID($inMessageID, $inTransactionID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMessageID.'.'.$inTransactionID]) ) {
			return self::$_Instances[$inMessageID.'.'.$inTransactionID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsOutboundMessageTransaction();
		$oObject->setMessageID($inMessageID);
		$oObject->setTransactionID($inTransactionID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMessageID.'.'.$inTransactionID] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsOutboundMessageTransaction
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesTransactions';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsOutboundMessageTransaction();
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
			SELECT messageID, transactionID
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesTransactions';

		$where = array();
		if ( $this->_MessageID !== 0 ) {
			$where[] = ' messageID = :MessageID ';
		}
		if ( $this->_TransactionID !== 0 ) {
			$where[] = ' transactionID = :TransactionID ';
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
			if ( $this->_TransactionID !== 0 ) {
				$oStmt->bindValue(':TransactionID', $this->_TransactionID);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.outboundMessagesTransactions
					( messageID, transactionID)
				VALUES
					(:MessageID, :TransactionID)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MessageID', $this->_MessageID);
					$oStmt->bindValue(':TransactionID', $this->_TransactionID);

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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesTransactions
			WHERE
				messageID = :MessageID AND
				transactionID = :TransactionID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MessageID', $this->_MessageID);
			$oStmt->bindValue(':TransactionID', $this->_TransactionID);

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
	 * @return commsOutboundMessageTransaction
	 */
	function reset() {
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
		$className = 'commsOutboundMessageTransaction';
		$xml  = "<$className>$newLine";
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
			$valid = $this->checkMessageID($message);
		}
		if ( $valid ) {
			$valid = $this->checkTransactionID($message);
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
	 * @return commsOutboundMessageTransaction
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
		return $this->_MessageID.'.'.$this->_TransactionID;
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
	 * @return commsOutboundMessageTransaction
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
	 * @return commsOutboundMessageTransaction
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
	 * @return commsOutboundMessageTransaction
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}