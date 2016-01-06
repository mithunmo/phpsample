<?php
/**
 * commsOutboundQueue class
 * 
 * Stored in commsOutboundQueue.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundQueue
 * @version $Rev: 53 $
 */


/**
 * commsOutboundQueue class
 * 
 * Outbound queue provides an interface to the outbound system. It handles adding
 * messages to the queue and ensuring that they are valid before being queued.
 * The outbounnd queue to handle any valid outbound message type.
 * 
 * Messages are handled on a FIFO basis - so the first message in the stack is the
 * first handled and the first sent - unless a message delay has been set.
 * 
 * <code>
 * $oMsgQueue = commsOutboundQueue::getInstance();
 * 
 * // create a new message
 * $oMessage = commsOutboundManager::newMessage(commsOutboundType::T_EMAIL);
 * $oMessage->setFromAddress();
 * $oMessage->setRecipient();
 * $oMessage->setSubject();
 * 
 * // attach message
 * $oMsgQueue->addToStack($oMessage);
 * 
 * // send messages
 * $oMsgQueue->send();
 * </code>
 * 
 * The outbound system allows a "transactionID" to be set.
 * 
 * A transactionID in this case is a unique identifier that can be used to track
 * this batch of messages together with other platform events e.g. an order. It
 * should be numeric only.
 * 
 * Errors are logged automatically via systemLog and include the method the error
 * was raised in. Some are critical e.g. unable to match to the outbound type.
 * 
 * @package comms
 * @subpackage outbound
 * @category commsOutboundQueue
 */
class commsOutboundQueue {
	
	/**
	 * Maintains instance of commsOutboundQueue
	 *
	 * @var commsOutboundQueue
	 * @access private
	 * @static
	 */
	private static $_Instance = false;
	
	/**
	 * Array of message types index by keyword
	 *
	 * @var array(ID => commsOutboundType)
	 * @access private
	 * @static
	 */
	private static $_MessageTypes = array();
	
	/**
	 * Message stack to send
	 *
	 * @var array(commsOutboundMessage)
	 * @access protected
	 */
	protected $_Stack = array();
	
	/**
	 * Number of messages in stack
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_StackCount = 0;
	
	/**
	 * True if processing current stack
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Processing = false;
	
	/**
	 * Last thrown exception from outbound system
	 *
	 * @var Exception
	 * @access protected
	 */
	protected $_Exception = null;
	
	/**
	 * Stores $_TransactionID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TransactionID = 0;
	
	
	
	/**
	 * Creates a new commsOutboundQueue instance and attempts to connect to the queue
	 *
	 * @return commsOutboundQueue
	 */
	private function __construct() {
		
	}
	
	
	
	/**
	 * Gets a new instance of the commsOutboundQueue
	 * 
	 * @return commsOutboundQueue
	 * @static
	 */
	public static function getInstance() {
		if ( !self::$_Instance || !self::$_Instance instanceof commsOutboundQueue ) {
			self::$_Instance = new self();
		}
		
		return self::$_Instance;
	}
	
	/**
	 * Remove the current commsOutboundQueue instance
	 *
	 * @return void
	 * @static 
	 */
	public static function reset() {
		self::$_Instance = null;
	}

	/**
	 * Removes expired messages from the queue, returns number of rows affected
	 *
	 * @return integer
	 * @static
	 */
	public static function cleanupQueue() {
		$i = 0;
		$tsNow = date(system::getConfig()->getDatabaseDateTimeFormat());
		$query = '
			SELECT outboundMessages.*
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessages
			       LEFT JOIN '.system::getConfig()->getDatabase('comms').'.outboundMessagesEmbargo USING (recipient)
			 WHERE outboundMessagesEmbargo.state = :State
			   AND outboundMessagesEmbargo.expires < :TsNow
			   AND outboundMessages.outboundTypeID = :OutboundTypeID
			   AND outboundMessages.charge > 0
			 ORDER BY outboundMessagesEmbargo.expires ASC, outboundMessages.messageID ASC
			 LIMIT 200';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':State', 'InProcess');
		$oStmt->bindValue(':TsNow', $tsNow);
		$oStmt->bindValue(':OutboundTypeID', commsOutboundType::T_SMS);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				try {
					$oMessage = commsOutboundManager::newMessage($row['outboundTypeID']);
					$oMessage->loadFromArray($row);
					
					self::expireMessage($oMessage);
					++$i;
				} catch ( Exception $e ) {
					systemLog::warning($e->getMessage());
				}
			}
		}
		$oStmt->closeCursor();
		return $i;
	}
	
	/**
	 * Expires a message object, returns rows affected
	 * 
	 * If a transaction has been mapped to this message; all other messages
	 * associated with the transaction will be failed and removed from the
	 * queue.
	 *
	 * @param commsOutboundMessage $inMessage
	 * @return integer
	 * @static
	 */
	public static function expireMessage(commsOutboundMessage $inMessage) {
		systemLog::getInstance()->setSource(
			array(
				'MsgID' => $inMessage->getMessageID(),
				'TypID' => $inMessage->getOutboundTypeID(),
				'To' => $inMessage->getRecipient(),
				'TransID' => $inMessage->getTransactionMap()->getTransactionID()
			)
		);
		$res = 0;
		if ( $inMessage instanceof commsOutboundMessage && $inMessage->getMessageID() > 0 ) {
			if ( $inMessage->getCharge() > 0.00 ) {
				systemLog::notice('Expiring charged message ('.$inMessage->getMessageID().'), charged at '.$inMessage->getCharge());
				$inMessage->setStatusID(commsOutboundStatus::S_EXPIRED);
				$inMessage->setComment('Message expired');
				$inMessage->setAcknowledgedDate(date(system::getConfig()->getDatabaseDateTimeFormat()));
				if ( $inMessage->save() ) {
					$res++;
				}
				$res += self::failMessagesByTransactionId($inMessage);
				self::purgeMessagesByTransactionId($inMessage);
			}
			
			/*
			 * Delete records in embargo for this recipient
			 */
			$oEmbargo = commsOutboundMessageEmbargo::getInstance($inMessage->getRecipient());
			$oEmbargo->delete();
		}
		systemLog::info('Expired message '.$inMessage->getMessageID().':'.$inMessage->getOutboundTypeID().' and '.$res.' other messages');
		return $res;
	}

	/**
	 * Fails a message object, returns true on success
	 *
	 * @param commsOutboundMessage $inMessage
	 * @return boolean
	 * @static
	 */
	public static function failMessage(commsOutboundMessage $inMessage) {
		systemLog::getInstance()->setSource(
			array(
				'MsgID' => $inMessage->getMessageID(),
				'TypID' => $inMessage->getOutboundTypeID(),
				'To' => $inMessage->getRecipient(),
				'TransID' => $inMessage->getTransactionMap()->getTransactionID()
			)
		);
		
		if ( $inMessage instanceof commsOutboundMessage && $inMessage->getMessageID() > 0 ) {
			$inMessage->setStatusID(commsOutboundStatus::S_FAILED);
			$inMessage->setComment('Message failed by request');
			$inMessage->setSentDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
			$inMessage->save();
			
			/*
			 * Delete records in embargo for this recipient
			 */
			$oEmbargo = commsOutboundMessageEmbargo::getInstance($inMessage->getRecipient());
			$oEmbargo->delete();
		}
		systemLog::info('Failed message '.$inMessage->getMessageID().':'.$inMessage->getOutboundTypeID());
		return true;
	}
	
	/**
	 * Fails all messages associated to $inMessage's transactionID except for appLoopBacks
	 *
	 * @param commsOutboundMessage $inMessage
	 * @return integer
	 * @static
	 */
	public static function failMessagesByTransactionId(commsOutboundMessage $inMessage) {
		$res = 0;
		if ( $inMessage->getTransactionMap()->getTransactionID() ) {
			$list = commsOutboundManager::getMessagesByTransactionID($inMessage->getTransactionMap()->getTransactionID());
			
			foreach ( $list as $oObject ) {
				if ( $oObject->getOutboundTypeID() != commsOutboundType::T_APP_LOOP_BACK ) {
					$oObject->setStatusID(commsOutboundStatus::S_DROPPED);
					$oObject->setSentDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
					$oObject->setComment('Sending failed from previous message failure');
					$oObject->save();
					$res++;
				}
			}
		}
		return $res;
	}
	
	/**
	 * Purges all messages assocaited to $inMessage's transactionID, except for appLoopBacks
	 *
	 * @param commsOutboundMessage $inMessage
	 * @return integer
	 * @static 
	 */
	public static function purgeMessagesByTransactionId(commsOutboundMessage $inMessage) {
		$res = 0;
		if ( $inMessage->getTransactionMap()->getTransactionID() ) {
			self::failMessagesByTransactionId($inMessage);
			
			$query = '
				DELETE '.system::getConfig()->getDatabase('comms').'.outboundMessagesQueue
				  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesQueue
				       INNER JOIN '.system::getConfig()->getDatabase('comms').'.outboundMessagesTransactions USING (messageID)
				       INNER JOIN '.system::getConfig()->getDatabase('comms').'.outboundMessages USING (messageID)
				 WHERE outboundMessagesTransactions.transactionID = :TransactionID
				   AND outboundMessages.outboundTypeID != :OutboundTypeID';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':TransactionID', $inMessage->getTransactionMap()->getTransactionID(), PDO::PARAM_INT);
			$oStmt->bindValue(':OutboundTypeID', commsOutboundType::T_APP_LOOP_BACK, PDO::PARAM_INT);
			$oStmt->execute();
			$res = $oStmt->rowCount();
		}
		return $res;
	}
	
	/**
	 * Returns the number of messages by type in the outbound queue
	 * 
	 * @return array
	 * @static
	 */
	public static function getQueueStats() {
		$return = array();
		$query = '
			SELECT outboundTypes.description, COUNT(*) AS count
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesQueue
			       INNER JOIN '.system::getConfig()->getDatabase('comms').'.outboundMessages USING (messageID)
			       INNER JOIN '.system::getConfig()->getDatabase('comms').'.outboundTypes USING (outboundTypeID)
			 GROUP BY outboundMessages.outboundTypeID
			 ORDER BY outboundTypes.description ASC';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$return[$row['description']] = $row['count'];
			}
		}
		return $return;
	}
	
	
	
	/**
	 * Returns true if process() has been called
	 *
	 * @return boolean
	 */
	public function isProcessing() {
		return $this->_Processing;
	}
	
	/**
	 * Attach a new messageStack to queue
	 *
	 * @return commsOutboundQueue
	 */
	public function resetStack() {
		$this->_Stack = array();
		$this->_StackCount = 0;
		return $this;
	}
	
	/**
	 * Return current messages stack
	 *
	 * @return array
	 */
	public function getMessageStack() {
		return $this->_Stack;
	}
	
	/**
	 * Return message count in stack
	 *
	 * @return integer
	 */
	public function getMessageCount() {
		return $this->_StackCount;
	}
	
	/**
	 * Pushes another message onto the current stack (only possible before calling process() )
	 *
	 * @param commsOutboundMessage $message
	 * @return commsOutboundQueue
	 */
	public function addToStack(commsOutboundMessage $message) {
		if ( !$this->isProcessing() ) {
			$this->_Stack[] = $message;
			$this->_StackCount++;
		}
		return $this;
	}
	
	/**
	 * Remove message from stack (only possible before calling process() )
	 *
	 * @param commsOutboundMessage $message
	 * @return commsOutboundQueue
	 */
	public function removeFromStack(commsOutboundMessage $message) {
		if ( !$this->isProcessing() ) {
			$msgs = $this->getMessageStack();
			$this->resetStack();
			foreach ( $msgs as $oMessage ) {
				if ( $oMessage != $message ) {
					$this->_Stack[] = $oMessage;
				}
			}
			$this->_StackCount = count($this->_Stack);
		}
		return $this;
	}
	
	/**
	 * Sets the recipient on all messages in this stack
	 * 
	 * Recipient will be either an email address for email messages
	 * or a fully qualified MSISDN for SMS and SMS related messages.
	 * 
	 * @param string $inRecipient
	 * @return commsOutboundQueue
	 */
	public function setRecipient($inRecipient) {
		if ( $this->getMessageCount() > 0 ) {
			foreach ( $this->getMessageStack() as $oMessage ) {
				$oMessage->setRecipient($inRecipient);
			}
		}
		return $this;
	}
	
	
	
	/**
	 * Send array of messages down queue
	 *
	 * @return boolean
	 */
	public function send() {
		try  {
			$this->_process();
			systemLog::message("Sent {$this->getMessageCount()} messages down queue");
			$this->resetStack();
			return true;
		} catch ( Exception $e ) {
			$this->setException($e);
			systemLog::error($e->getMessage());
			return false;
		}
	}
	
	/**
	 * Processes the messageStack
	 *
	 * @return boolean
	 * @throws commsOutboundException
	 * @access private
	 */
	private function _process() {
		if ( !$this->isProcessing() ) {
			$this->_Processing = true;
			systemLog::message('Preparing message stack for sending');
			
			if ( false ) $oMessage = new commsOutboundMessage();
			foreach ( $this->_Stack as $oMessage ) {
				if ( $this->getTransactionID() ) {
					systemLog::info('Binding transaction id: '.$this->getTransactionID().' to message');
					$oMessage->getTransactionMap()->setTransactionID($this->getTransactionID());
				}
				$oMessage
					->getQueue()
						->setScheduled($oMessage->getScheduledDate())
						->setTransactionID($this->getTransactionID());
				$oMessage->save();
			}
			$this->_Processing = false;
			
		} else {
			throw new commsOutboundException(__CLASS__.'::'.__METHOD__.'() called while already processing');
		}
	}
	
	
	
	/**
	 * Returns true if an exception has been raised
	 *
	 * @return boolean
	 */
	function hasExceptionBeenThrown() {
		return ($this->_Exception instanceof Exception) ? true : false;
	}
	
	/**
	 * Returns the last thrown exception from the outbound system
	 *
	 * @return false|Exception
	 */
	function getLastException() {
		return $this->_Exception;
	}
	
	/**
	 * Set an exception object
	 *
	 * @param Exception $e
	 */
	protected function setException(Exception $e) {
		$this->_Exception = $e;
	}

	/**
	 * Returns $_TransactionID
	 *
	 * @return integer
	 */
	function getTransactionID() {
		return $this->_TransactionID;
	}
	
	/**
	 * Set $_TransactionID to $inTransactionID
	 *
	 * @param integer $inTransactionID
	 * @return commsOutboundQueue
	 */
	function setTransactionID($inTransactionID) {
		if ( $inTransactionID !== $this->_TransactionID ) {
			$this->_TransactionID = $inTransactionID;
		}
		return $this;
	}
}