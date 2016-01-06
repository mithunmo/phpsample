<?php
/**
 * commsOutboundManager class
 * 
 * Stored in commsOutboundManager.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundManager
 * @version $Rev: 280 $
 */


/**
 * commsOutboundManager class
 * 
 * Manager for the outbound system. Provides interfaces for creating messages,
 * setting up message stacks etc.
 * 
 * @package comms
 * @subpackage outbound
 * @category commsOutboundManager
 */
class commsOutboundManager {
	
	/**
	 * Stores the instance of the OB Manager
	 * 
	 * @var commsOutboundManager
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
	 * Creates a new commsOutboundManager instance
	 * 
	 * @return commsOutboundManager
	 */
	function __construct() {
		$this->_LoadObjectDetails = false;
	}
	
	
	
	/**
	 * Returns a single instance of the outbound manager
	 * 
	 * @return commsOutboundManager
	 * @static
	 */
	static function getInstance() {
		if ( !self::$_Instance instanceof commsOutboundManager ) {
			self::$_Instance = new self();
		}
		
		return self::$_Instance;
	}

	/**
	 * Fetches the next message due to be sent from the message queue
	 *
	 * @return commsOutboundMessage
	 * @static
	 */
	static function getNextMessage() {
		return self::getInstance()->getNextQueuedMessage();
	}
	
	/**
	 * Returns a message matching $inMessageID
	 * 
	 * @param integer $inMessageID
	 * @return commsOutboundMessage
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
	 * $inType can be either a {@link commsOutboundType} T_ constants or an
	 * {@link commsApplicationMessage}. App messages will be imported into
	 * message objects.
	 * 
	 * @param mixed $inType Integer or commsApplicationMessage
	 * @param commsTrigger $inTrigger
	 * @return commsOutboundMessage
	 * @static
	 */
	static function newMessage($inType = commsOutboundType::T_EMAIL, $inTrigger = null) {
		$oMessage = false;
		if ( is_numeric($inType) ) {
			$oType = commsOutboundType::getInstance($inType);
			$class = $oType->getClassName();
			
			$oMessage = new $class;
			$oMessage->setOutboundTypeID($oType->getOutboundTypeID());
		} elseif ( $inType instanceof commsApplicationMessage ) {
			$oMessage = self::newMessage($inType->getOutboundTypeID());
			$oMessage->importFromApplicationMessage($inType);
		}
		
		if ( $inTrigger instanceof commsTrigger && $oMessage ) {
			$oMessage->setOriginator($inTrigger->getPrs());
		}
		return $oMessage;
	}
	
	/**
	 * Creates an outbound queue and pre-populates with application messages from $inMessageGroupID
	 * 
	 * @param integer $inApplicationID
	 * @param integer $inMessageGroupID
	 * @param string $inLanguage
	 * @return commsOutboundQueue
	 * @static
	 */
	static function newQueueFromApplicationMessageGroup($inApplicationID, $inMessageGroupID, $inLanguage = 'en') {
		$appMsgs = commsApplicationMessage::getApplicationMessagesByGroup($inApplicationID, $inMessageGroupID, $inLanguage);
		
		$oQueue = commsOutboundQueue::getInstance();
		if ( count($appMsgs) > 0 ) {
			foreach ( $appMsgs as $oMessage ) {
				$oQueue->addToStack(self::newMessage($oMessage));
			}
		}
		return $oQueue;
	}
	
	/**
	 * Creates an outbound queue and pre-populates with message
	 *
	 * @param string $inSubject
	 * @param string $inBody
	 * @param string $inLanguage
	 * @param string $inEmailSender
	 * @param string $inEmailSenderName
	 * @param string $inBodyText
	 * @param integer $isHtml
	 *
	 * @return commsOutboundQueue
	 * @static
	 */
	static function newQueueForNormalMessage($inSubject, $inBody, $inLanguage = 'en',$inEmailSender,$inEmailSenderName,$inBodyText,$isHtml,$inAttach = "") {
		$oQueue = commsOutboundQueue::getInstance();
		$newMessg = self::newMessage();
		$newMessg->setMessageBody($inBody);
		$newMessg->setMessageSubject($inSubject);
		$newMessg->setMessageBodyText($inBodyText);
		$newMessg->setOriginator($inEmailSenderName.":".$inEmailSender);
		$newMessg->setIsHtml($isHtml);
		if ( $inAttach != "" ) {
			$newMessg->setMessageAttachement($inAttach);
		}
		$oQueue->addToStack($newMessg);
		return $oQueue;
	}



	/**
	 * For each message in the current queue, $inSearchFields are replaced with $inReplaceFields
	 * 
	 * This method is used to replace dynamic data vars in the message templates injected from
	 * the application messages. You can build up an array of values for each to customise the
	 * messages.
	 * 
	 * @param commsOutboundQueue $inOutboundQueue
	 * @param array $inSearchFields
	 * @param array $inReplaceFields
	 * @return void
	 * @static
	 */
	static function replaceDataInMessageStack(commsOutboundQueue $inOutboundQueue, array $inSearchFields, array $inReplaceFields) {
		if ( $inOutboundQueue->getMessageCount() > 0 ) {
			foreach ( $inOutboundQueue->getMessageStack() as $oMessage ) {
				$oMessage->setMessageBody(str_replace($inSearchFields, $inReplaceFields, $oMessage->getMessageBody()));
			}
		}
	}

	/**
	 * For each message in the current queue, set customerID to $inCustomerID
	 * 
	 * @param commsOutboundQueue $inOutboundQueue
	 * @param integer $inCustomerID
	 * @return void
	 * @static
	 */
	static function setCustomerInMessageStack(commsOutboundQueue $inOutboundQueue, $inCustomerID) {
		if ( $inOutboundQueue->getMessageCount() > 0 ) {
			foreach ( $inOutboundQueue->getMessageStack() as $oMessage ) {
				$oMessage->setCustomerID($inCustomerID);
			}
		}
	}

	
	/**
	 * For each message in the current queue, set originator to $inOriginator
	 * 
	 * @param commsOutboundQueue $inOutboundQueue
	 * @param string $inOriginator
	 * @return void
	 * @static
	 */
	static function setOriginatorInMessageStack(commsOutboundQueue $inOutboundQueue, $inOriginator) {
		if ( $inOutboundQueue->getMessageCount() > 0 ) {
			foreach ( $inOutboundQueue->getMessageStack() as $oMessage ) {
				$oMessage->setOriginator($inOriginator);
			}
		}
	}
	
	/**
	 * For each message in the current queue, set recipient to $inRecipient
	 * 
	 * @param commsOutboundQueue $inOutboundQueue
	 * @param string $inRecipient
	 * @return void
	 * @static
	 */
	static function setRecipientInMessageStack(commsOutboundQueue $inOutboundQueue, $inRecipient) {
		if ( $inOutboundQueue->getMessageCount() > 0 ) {
			foreach ( $inOutboundQueue->getMessageStack() as $oMessage ) {
				$oMessage->setRecipient($inRecipient);
			}
		}
	}

	/**
	 * For each message in the current queue, merge $inParams to the message parameters
	 * 
	 * @param commsOutboundQueue $inOutboundQueue
	 * @param array $inParams
	 * @return void
	 * @static
	 */
	static function mergeParamsInMessageStack(commsOutboundQueue $inOutboundQueue, array $inParams) {
		if ( $inOutboundQueue->getMessageCount() > 0 ) {
			foreach ( $inOutboundQueue->getMessageStack() as $oMessage ) {
				$oMessage->getParamSet()->mergeParams($inParams);
			}
		}
	}
	
	
	
	/**
	 * Loading methods
	 */
	
	/**
	 * Returns the next queued message from the outbound queue
	 * 
	 * @return commsOutboundMessage
	 */
	function getNextQueuedMessage() {
		$query = '
			SELECT outboundMessages.*
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesQueue
			  	   INNER JOIN '.system::getConfig()->getDatabase('comms').'.outboundMessages USING (messageID)
			       LEFT JOIN '.system::getConfig()->getDatabase('comms').'.outboundMessagesEmbargo ON (
			           outboundMessages.recipient = outboundMessagesEmbargo.recipient
			           AND
			           outboundMessages.outboundTypeID IN ('.commsOutboundType::T_SMS.','.commsOutboundType::T_APP_LOOP_BACK.')
			       )
			 WHERE outboundMessagesQueue.scheduled <= '.dbManager::getInstance()->quote(date(system::getConfig()->getDatabaseDatetimeFormat())).'
			   AND outboundMessagesEmbargo.messageID IS NULL
			 ORDER BY outboundMessagesQueue.messageID ASC
			 LIMIT 1';
		
		return $this->_executeSqlQuery($query, false);
	}
	
	/**
	 * Loads a message based on the supplied ID
	 *
	 * @param integer $inMessageID
	 * @return commsOutboundMessage
	 * @throws commsOutboundException
	 */
	function getMessageByID($inMessageID) {
		if ( empty($inMessageID) || strlen($inMessageID) < 1 ) {
			throw new commsOutboundException('Expected message ID, nothing given');
		}
		if ( !is_numeric($inMessageID) ) {
			throw new commsOutboundException('Expected message ID to be numeric');
		}
		
		$query = '
			SELECT outboundMessages.*
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessages
			 WHERE outboundMessages.messageID = '.dbManager::getInstance()->quote($inMessageID);
		
		return $this->_executeSqlQuery($query, false);
	}

	/**
	 * Fetches an array of messages populating them in one go preventing multiple SQL queries
	 *
	 * @param array $inArray
	 * @return array(commsOutboundMessage)
	 * @throws commsOutboundException
	 */
	function loadMessagesByArray(array $inArray = array()) {
		if ( count($inArray) < 1 ) {
			throw new commsOutboundException('Array contains no messages to load');
		}
		
		$query = '
			SELECT outboundMessages.*
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessages
			 WHERE outboundMessages.messageID IN ('.implode(', ', $inArray).')';
		
		$query .= ' ORDER BY FIELD(messageID, '.implode(',', $inArray).')';
		
		return $this->_executeSqlQuery($query, true);
	}
	
	/**
	 * Fetches all messages associated with $inTransactionID, returning in scheduled order
	 * 
	 * @param integer $inTransactionID
	 * @return array(commsOutboundMessage)
	 * @throws commsOutboundException
	 */
	function loadMessagesByTransactionID($inTransactionID) {
		if ( empty($inTransactionID) || strlen($inTransactionID) < 1 ) {
			throw new commsOutboundException('Expected transaction ID, nothing given');
		}
		if ( !is_numeric($inTransactionID) ) {
			throw new commsOutboundException('Expected transaction ID to be numeric');
		}
		
		$query = '
			SELECT outboundMessages.*
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessages
			       INNER JOIN '.system::getConfig()->getDatabase('comms').'.outboundMessagesTransactions USING (messageID)
			 WHERE outboundMessagesTransactions.transactionID = '.dbManager::getInstance()->quote($inTransactionID).'
			 ORDER BY outboundMessages.scheduledDate ASC';
		
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
				$oObject = self::newMessage($row['outboundTypeID']);
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
				  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesParams
				 WHERE messageID IN ('.implode(', ', array_keys($inArray)).')';
			
			$tmp = array();
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->execute();
			foreach ( $oStmt as $row ) {
				$tmp[$row['messageID']][$row['paramName']] = $row['paramValue'];
			}
			$oStmt->closeCursor();
			
			if ( false ) $oObject = new commsOutboundMessage();
			foreach ( $inArray as $oObject ) {
				if ( array_key_exists($oObject->getMessageID(), $tmp) ) {
					$oParams = new baseTableParamSet(
						system::getConfig()->getDatabase('comms'), 'outboundMessagesParams', 'messageID', 'paramName', 'paramValue', $oObject->getMessageID(), false
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
	 * @throws commsOutboundException
	 */
	private function _createIndexedArray(array $inArray = array()) {
		$return = array();
		foreach ( $inArray as $oObject ) {
			if ( $oObject instanceof commsOutboundMessage ) {
				$return[$oObject->getMessageID()] = $oObject;
			} else {
				throw new commsOutboundException(__CLASS__.'::'.__METHOD__.' Operation failed on a none commsOutboundMessage object');
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
	 * @return commsOutboundManager
	 */
	function setLoadObjectDetails($inLoadObjectDetails) {
		if ( $inLoadObjectDetails !== $this->_LoadObjectDetails ) {
			$this->_LoadObjectDetails = $inLoadObjectDetails;
		}
		return $this;
	}
}