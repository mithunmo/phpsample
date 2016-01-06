<?php
/**
 * commsOutboundMessage
 *
 * Stored in message.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessage
 * @version $Rev: 80 $
 */


/**
 * commsOutboundMessage Class
 *
 * This is the main composite outbound message class from which all outbound message types
 * inherit. This class provides the parameters and additional shared object methods that
 * the message types use.
 * 
 * This class should never be directly instantiated.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessage
 */
class commsOutboundMessage extends commsOutboundMessageBase {
	
	const PARAM_MESSAGE_BODY = 'message.body';
	const PARAM_MESSAGE_SUBJECT = 'message.subject';
	const PARAM_MESSAGE_NETWORK_ID = 'message.networkID';
	const PARAM_MESSAGE_COMMENT = 'message.comment';
	const PARAM_MESSAGE_BODY_TEXT = 'message.text';
	
	/**
	 * Stores an instance of baseTableParamSet
	 *
	 * @var baseTableParamSet
	 * @access protected
	 */
	protected $_ParamSet;
	
	/**
	 * Stores an instance of commsOutboundMessageQueue
	 *
	 * @var commsOutboundMessageQueue
	 * @access protected
	 */
	protected $_Queue;
	
	/**
	 * Stores an instance of commsOutboundMessageTransaction
	 *
	 * @var commsOutboundMessageTransaction
	 * @access protected
	 */
	protected $_TransactionMap;
	
	
	
	/**
	 * Saves changes to the object
	 *
	 * @return boolean
	 */
	function save() {
		$return = true;
		if ( $this->isModified() ) {
			$return = parent::save() && $return;
		
			if ( $this->_ParamSet instanceof baseTableParamSet ) {
				$this->_ParamSet->setIndexID($this->getMessageID());
				$return = $this->_ParamSet->save() && $return;
			}
			if ( $this->_Queue instanceof commsOutboundMessageQueue ) {
				$this->_Queue->setMessageID($this->getMessageID());
				$this->_Queue->save();
			}
			if ( $this->_TransactionMap instanceof commsOutboundMessageTransaction ) {
				$this->_TransactionMap->setMessageID($this->getMessageID());
				$this->_TransactionMap->save();
			}
		}
		return $return;
	}
	
	/**
	 * Deletes the object and all related records
	 *
	 * @return boolean
	 */
	function delete() {
		$return = false;
		if ( $this->getMessageID() ) {
			$this->getParamSet()->deleteAll();
			$this->getQueue()->delete();
			$this->getTransactionMap()->delete();
			$return = parent::delete();
		}
		return $return;
	}
	
	/**
	 * Reset object
	 *
	 * @return void
	 */
	function reset() {
		$this->_ParamSet = null;
		$this->_Queue = null;
		$this->_TransactionMap = null;
		
		parent::reset();
	}
	
	/**
	 * Imports custom properties from the application message
	 * 
	 * @param commsApplicationMessage $inMessage
	 * @return commsOutboundMessage
	 * @abstract
	 */
	function importFromApplicationMessage(commsApplicationMessage $inMessage) {
		$this->setCharge($inMessage->getCharge());
		$this->setCurrencyID($inMessage->getCurrencyID());
		$this->setMessageBody($inMessage->getMessageBody());
		$this->setScheduledDate(date(system::getConfig()->getDatabaseDatetimeFormat(), time()+(3600*$inMessage->getDelay())));
		return $this;
	}
	
	/**
	 * Gets the message properties as an array of transport credentials
	 * 
	 * @return array
	 */
	function getTransportCredentials() {
		$credentials = array(
			transportCredentials::PARAM_MESSAGE_BODY => $this->getMessageBody(),
			transportCredentials::PARAM_MESSAGE_RECIPIENT => $this->getRecipient(),
			transportCredentials::PARAM_MESSAGE_SENDER => $this->getOriginator(),
			transportCredentials::PARAM_MESSAGE_SUBJECT => $this->getMessageSubject(),
			transportCredentials::PARAM_MESSAGE_BODY_TEXT => $this->getMessageBodyText()
		);
		
		/*
		 * Import custom credentials
		 */
		$this->_getTransportCredentials($credentials);
		
		return $credentials;
	}
	
	/**
	 * Maps custom transport credentials based on the message type
	 * 
	 * @param array $inCredentials Passed as a reference
	 * @return void
	 * @access protected
	 * @abstract
	 */
	protected function _getTransportCredentials(array &$inCredentials) {
		
	}
	
	

	/**
	 * Returns true if object or sub-objects have been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		$modified = parent::isModified();
		if ( !$modified && $this->_ParamSet !== null ) {
			$modified = $modified || $this->_ParamSet->isModified();
		}
		if ( !$modified && $this->_Queue !== null ) {
			$modified = $modified || $this->_Queue->isModified();
		}
		if ( !$modified && $this->_TransactionMap !== null ) {
			$modified = $modified || $this->_TransactionMap->isModified();
		}
		return $modified;
	}
	
	/**
	 * Returns an instance of ParamSet, which is lazy loaded upon request
	 *
	 * @return baseTableParamSet
	 */
	function getParamSet() {
		if ( !$this->_ParamSet instanceof baseTableParamSet ) {
			$this->_ParamSet = new baseTableParamSet(system::getConfig()->getDatabase('comms'), 'outboundMessagesParams', 'messageID', 'paramName', 'paramValue', $this->getMessageID(), false);
			if ( $this->getMessageID() > 0 ) {
				$this->_ParamSet->load();
			}
		}
		return $this->_ParamSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param baseTableParamSet $inObject
	 * @return commsOutboundMessage
	 */
	function setParamSet(baseTableParamSet $inObject) {
		$this->_ParamSet = $inObject;
		return $this;
	}
	
	/**
	 * Returns an instance of commsOutboundMessageTransaction, which is lazy loaded upon request
	 *
	 * @return commsOutboundMessageTransaction
	 */
	function getTransactionMap() {
		if ( !$this->_TransactionMap instanceof commsOutboundMessageTransaction ) {
			$this->_TransactionMap = new commsOutboundMessageTransaction($this->getMessageID());
		}
		return $this->_TransactionMap;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param commsOutboundMessageTransaction $inObject
	 * @return commsOutboundMessage
	 */
	function setTransactionMap(commsOutboundMessageTransaction $inObject) {
		$this->_TransactionMap = $inObject;
		return $this;
	}

	/**
	 * Returns an instance of commsOutboundMessageQueue, which is lazy loaded upon request
	 *
	 * @return commsOutboundMessageQueue
	 */
	function getQueue() {
		if ( !$this->_Queue instanceof commsOutboundMessageQueue ) {
			$this->_Queue = new commsOutboundMessageQueue($this->getMessageID());
		}
		return $this->_Queue;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param commsOutboundMessageQueue $inObject
	 * @return commsOutboundMessage
	 */
	function setQueue(commsOutboundMessageQueue $inObject) {
		$this->_Queue = $inObject;
		return $this;
	}
	
	
		
	/**
	 * Returns the message header, if any
	 * 
	 * @return string
	 */
	function getMessageSubject() {
		return $this->getParamSet()->getParam(self::PARAM_MESSAGE_SUBJECT);
	}
	
	/**
	 * Sets the message header
	 * 
	 * @param string $inVarName
	 * @return commsOutboundMessage
	 */
	function setMessageSubject($inVarName) {
		$this->getParamSet()->setParam(self::PARAM_MESSAGE_SUBJECT, $inVarName);
		return $this;
	}
		
	/**
	 * Returns the message body
	 * 
	 * @return string
	 */
	function getMessageBody() {
		return $this->getParamSet()->getParam(self::PARAM_MESSAGE_BODY);
	}
	
	/**
	 * Sets the message body
	 * 
	 * @param string $inString
	 * @return commsOutboundMessage
	 */
	function setMessageBody($inString) {
		$this->getParamSet()->setParam(self::PARAM_MESSAGE_BODY, $inString);
		return $this;
	}

	/**
	 * Return value of $_MessageBodyText
	 *
	 * @return string
	 * @access public
	 */
	function getMessageBodyText() {
		return $this->getParamSet()->getParam(self::PARAM_MESSAGE_BODY_TEXT);
	}

	/**
	 * Set $_MessageBody to MessageBodyText
	 *
	 * @param string $inMessageBodyText
	 * @return commsApplicationMessage
	 * @access public
	 */
	function setMessageBodyText($inString) {
		$this->getParamSet()->setParam(self::PARAM_MESSAGE_BODY_TEXT, $inString);
		return $this;
	}

	
	/**
	 * Returns the network id for the recipient
	 * 
	 * @return integer
	 */
	function getNetworkID() {
		return $this->getParamSet()->getParam(self::PARAM_MESSAGE_NETWORK_ID, 0);
	}
	
	/**
	 * Sets the network id for the recipient
	 * 
	 * @param integer $inNetworkID
	 * @return commsOutboundMessage
	 */
	function setNetworkID($inNetworkID) {
		$this->getParamSet()->setParam(self::PARAM_MESSAGE_NETWORK_ID, $inNetworkID);
		return $this;
	}
		
	/**
	 * Returns the message comment if any
	 * 
	 * @return string
	 */
	function getComment() {
		return $this->getParamSet()->getParam(self::PARAM_MESSAGE_COMMENT);
	}
	
	/**
	 * Sets the message comment, this can be a status message, error message etc
	 * 
	 * @param string $inComment
	 * @return commsOutboundMessage
	 */
	function setComment($inComment) {
		$this->getParamSet()->setParam(self::PARAM_MESSAGE_COMMENT, $inComment);
		return $this;
	}
}