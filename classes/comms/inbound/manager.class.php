<?php
/**
 * commsInboundManager class
 * 
 * Stored in commsInboundManager.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage inbound
 * @category commsInboundManager
 * @version $Rev: 10 $
 */


/**
 * commsInboundManager class
 * 
 * Manager for the inbound system. Provides interfaces for creating messages,
 * setting up message stacks etc.
 * 
 * @package comms
 * @subpackage inbound
 * @category commsInboundManager
 */
class commsInboundManager {
	
	/**
	 * Stores the instance of the inbound manager
	 * 
	 * @var commsInboundManager
	 * @access private
	 * @static
	 */
	private static $_Instance;
	
	/**
	 * Stores $_LoadObjectDetails
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_LoadObjectDetails = false;
	
	
	
	/**
	 * Creates a new commsInboundManager instance
	 * 
	 * @return commsInboundManager
	 */
	function __construct() {
		$this->_LoadObjectDetails = false;
	}
	
	
	
	/**
	 * Returns a single instance of the outbound manager
	 * 
	 * @return commsInboundManager
	 * @static
	 */
	static function getInstance() {
		if ( !self::$_Instance instanceof commsInboundManager ) {
			self::$_Instance = new self();
		}
		
		return self::$_Instance;
	}

	/**
	 * Fetches the next message due to be sent from the message queue
	 *
	 * @return commsInboundMessage
	 * @static
	 */
	static function getNextMessage() {
		return self::getInstance()->getNextQueuedMessage();
	}
	
	/**
	 * Returns a message matching $inMessageID
	 * 
	 * @param integer $inMessageID
	 * @return commsInboundMessage
	 * @static
	 */
	static function getInstanceByID($inMessageID) {
		return self::getInstance()->getMessageByID($inMessageID);
	}
	
	/**
	 * Returns an array of messages loaded from $inArray
	 * 
	 * @param array $inArray
	 * @return array
	 * @static
	 */
	static function loadInstancesByArray(array $inArray = array()) {
		return self::getInstance()->loadMessagesByArray($inArray);
	}
	
	/**
	 * Fetches all messages associated with $inTransactionID and returns in scheduled order
	 * 
	 * @param integer $inTransactionID
	 * @return array
	 * @static
	 */
	static function getMessagesByTransactionID($inTransactionID) {
		return self::getInstance()->loadMessagesByTransactionID($inTransactionID);
	}
	
	

	/**
	 * Creates a new message from the type
	 * 
	 * $inType is a {@link commsInboundType} T_ constant.
	 * 
	 * @param integer $inType commsInboundType constant
	 * @param commsTrigger $inTrigger
	 * @return commsInboundMessage
	 * @static
	 */
	static function newMessage($inType = commsInboundType::T_EMAIL, $inTrigger = null) {
		$oMessage = false;
		if ( is_numeric($inType) ) {
			$oType = commsInboundType::getInstance($inType);
			$class = $oType->getClassName();
			if ( !$class ) {
				throw new commsInboundException("Invalid type ($inType) specified for inbound message");
			}
			
			$oMessage = new $class;
			$oMessage = new commsInboundMessage();
			$oMessage->setInboundTypeID($oType->getInboundTypeID());
		} else {
			throw new commsInboundException("Invalid value supplied for type ($inType)");
		}
		
		if ( $inTrigger instanceof commsTrigger && $oMessage ) {
			$oMessage->setPRS($inTrigger->getPrs());
		}
		return $oMessage;
	}
	
	
	
	/**
	 * Loading methods
	 */
	
	/**
	 * Returns the next queued message from the inbound queue
	 * 
	 * @return commsInboundMessage
	 */
	function getNextQueuedMessage() {
		$query = '
			SELECT inboundMessages.*
			  FROM '.system::getConfig()->getDatabase('comms').'.inboundMessagesQueue
			  	   INNER JOIN '.system::getConfig()->getDatabase('comms').'.inboundMessages USING (messageID)
			 WHERE inboundMessagesQueue.scheduled <= '.dbManager::getInstance()->quote(date(system::getConfig()->getDatabaseDatetimeFormat())).'
			 ORDER BY inboundMessagesQueue.messageID ASC
			 LIMIT 1';
		
		return $this->_executeSqlQuery($query, false);
	}
	
	/**
	 * Loads a message based on the supplied ID
	 *
	 * @param integer $inMessageID
	 * @return commsInboundMessage
	 * @throws commsInboundException
	 */
	function getMessageByID($inMessageID) {
		if ( empty($inMessageID) || strlen($inMessageID) < 1 ) {
			throw new commsInboundException('Expected message ID, nothing given');
		}
		if ( !is_numeric($inMessageID) ) {
			throw new commsInboundException('Expected message ID to be numeric');
		}
		
		$query = '
			SELECT inboundMessages.*
			  FROM '.system::getConfig()->getDatabase('comms').'.inboundMessages
			 WHERE inboundMessages.messageID = '.dbManager::getInstance()->quote($inMessageID);
		
		return $this->_executeSqlQuery($query, false);
	}

	/**
	 * Fetches an array of messages populating them in one go preventing multiple SQL queries
	 *
	 * @param array $inArray
	 * @return array(commsInboundMessage)
	 * @throws commsInboundException
	 */
	function loadMessagesByArray(array $inArray = array()) {
		if ( count($inArray) < 1 ) {
			throw new commsInboundException('Array contains no messages to load');
		}
		
		$query = '
			SELECT inboundMessages.*
			  FROM '.system::getConfig()->getDatabase('comms').'.inboundMessages
			 WHERE inboundMessages.messageID IN ('.implode(', ', $inArray).')';
		
		$query .= ' ORDER BY FIELD(messageID, '.implode(',', $inArray).')';
		
		return $this->_executeSqlQuery($query, true);
	}
	
	/**
	 * Fetches all messages associated with $inTransactionID, returning in scheduled order
	 * 
	 * @param integer $inTransactionID
	 * @return array(commsInboundMessage)
	 * @throws commsInboundException
	 */
	function loadMessagesByTransactionID($inTransactionID) {
		if ( empty($inTransactionID) || strlen($inTransactionID) < 1 ) {
			throw new commsInboundException('Expected transaction ID, nothing given');
		}
		if ( !is_numeric($inTransactionID) ) {
			throw new commsInboundException('Expected transaction ID to be numeric');
		}
		
		$query = '
			SELECT inboundMessages.*
			  FROM '.system::getConfig()->getDatabase('comms').'.inboundMessages
			       INNER JOIN '.system::getConfig()->getDatabase('comms').'.inboundMessagesTransactions USING (messageID)
			 WHERE inboundMessagesTransactions.transactionID = '.dbManager::getInstance()->quote($inTransactionID).'
			 ORDER BY inboundMessages.scheduledDate ASC';
		
		return $this->_executeSqlQuery($query, true);
	}
	
	/**
	 * Executes the SQL query, populating the result as necessary, $inFetchAll controls
	 * whether one or all results are returned
	 *
	 * @param string $inSql
	 * @param boolean $inFetchAll
	 * @return mixed
	 * @access private
	 */
	private function _executeSqlQuery($inSql, $inFetchAll = false) {
		$return = array();
		$oStmt = dbManager::getInstance()->prepare($inSql);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = self::newMessage($row['inboundTypeID']);
				$oObject->loadFromArray($row);
				
				$return[] = $oObject;

				if ( $inFetchAll === false ) {
					break;
				}
			}
		}
		$oStmt->closeCursor();

		if ( $this->getLoadObjectDetails() ) {
			$this->_loadObjectDetails($return);
		}

		if ( $inFetchAll ) {
			return $return;
		} else {
			if ( isset($return[0]) && is_object($return[0]) ) {
				return $return[0];
			}
		}
		return false;
	}

	/**
	 * Pre-loads user data
	 *
	 * @param array $inArray
	 * @return void
	 * @access private
	 */
	private function _loadObjectDetails(array $inArray = array()) {
		$inArray = $this->_createIndexedArray($inArray);
		
		$this->_loadMessageParams($inArray);
	}
	
	/**
	 * Loads an array of message objects with parameters in one go
	 * 
	 * @param array $inArray
	 * @return void
	 * @access private
	 */
	private function _loadMessageParams(array $inArray = array()) {
		if ( count($inArray) > 0 ) {
			$query = '
				SELECT messageID, paramName, paramValue
				  FROM '.system::getConfig()->getDatabase('comms').'.inboundMessagesParams
				 WHERE messageID IN ('.implode(', ', array_keys($inArray)).')';
			
			$tmp = array();
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->execute();
			foreach ( $oStmt as $row ) {
				$tmp[$row['messageID']][$row['paramName']] = $row['paramValue'];
			}
			$oStmt->closeCursor();
			
			if ( false ) $oObject = new commsInboundMessage();
			foreach ( $inArray as $oObject ) {
				if ( array_key_exists($oObject->getMessageID(), $tmp) ) {
					$oParams = new baseTableParamSet(
						system::getConfig()->getDatabase('comms'), 'inboundMessagesParams', 'messageID', 'paramName', 'paramValue', $oObject->getMessageID(), false
					);
					$oParams->setParam($tmp[$oObject->getMessageID()], null);
					
					$oObject->setParamSet($oParams);
				}
			}
		}
	}

	/**
	 * Converts a simple array so that it is indexed by the user ID
	 *
	 * @param array $inArray
	 * @return array
	 * @access private
	 * @throws commsInboundException
	 */
	private function _createIndexedArray(array $inArray = array()) {
		$return = array();
		foreach ( $inArray as $oObject ) {
			if ( $oObject instanceof commsInboundMessage ) {
				$return[$oObject->getMessageID()] = $oObject;
			} else {
				throw new commsInboundException(__CLASS__.'::'.__METHOD__.' Operation failed on a none commsInboundMessage object');
			}
		}
		return $return;
	}

	/**
	 * Returns $_LoadObjectDetails
	 *
	 * @return boolean
	 */
	function getLoadObjectDetails() {
		return $this->_LoadObjectDetails;
	}
	
	/**
	 * Set $_LoadObjectDetails to $inLoadObjectDetails
	 *
	 * @param boolean $inLoadObjectDetails
	 * @return commsInboundManager
	 */
	function setLoadObjectDetails($inLoadObjectDetails) {
		if ( $inLoadObjectDetails !== $this->_LoadObjectDetails ) {
			$this->_LoadObjectDetails = $inLoadObjectDetails;
		}
		return $this;
	}
}