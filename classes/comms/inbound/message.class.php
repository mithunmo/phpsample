<?php
/**
 * commsInboundMessage
 *
 * Stored in message.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage inbound
 * @category commsInboundMessage
 * @version $Rev: 10 $
 */


/**
 * commsInboundMessage Class
 *
 * This is the main composite outbound message class from which all outbound message types
 * inherit. This class provides the parameters and additional shared object methods that
 * the message types use.
 * 
 * This class should never be directly instantiated.
 *
 * @package comms
 * @subpackage inbound
 * @category commsInboundMessage
 */
class commsInboundMessage extends commsInboundMessageBase {
	
	const PARAM_MESSAGE_BODY = 'message.body';
	const PARAM_MESSAGE_SUBJECT = 'message.subject';
	const PARAM_PRS = 'message.prs';
	
	/**
	 * Stores an instance of baseTableParamSet
	 *
	 * @var baseTableParamSet
	 * @access protected
	 */
	protected $_ParamSet;
	
	/**
	 * Stores an instance of commsInboundMessageQueue
	 *
	 * @var commsInboundMessageQueue
	 * @access protected
	 */
	protected $_Queue;
	
	/**
	 * Stores an instance of commsInboundMessageTransaction
	 *
	 * @var commsInboundMessageTransaction
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
			if ( $this->_Queue instanceof commsInboundMessageQueue ) {
				$this->_Queue->setMessageID($this->getMessageID());
				$this->_Queue->save();
			}
			if ( $this->_TransactionMap instanceof commsInboundMessageTransaction ) {
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
		if ( $this->getProductID() ) {
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
			$this->_ParamSet = new baseTableParamSet(system::getConfig()->getDatabase('comms'), 'inboundMessagesParams', 'messageID', 'paramName', 'paramValue', $this->getMessageID(), false);
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
	 * @return commsInboundMessage
	 */
	function setParamSet(baseTableParamSet $inObject) {
		$this->_ParamSet = $inObject;
		return $this;
	}

	/**
	 * Returns an instance of commsInboundMessageQueue, which is lazy loaded upon request
	 *
	 * @return commsInboundMessageQueue
	 */
	function getQueue() {
		if ( !$this->_Queue instanceof commsInboundMessageQueue ) {
			$this->_Queue = new commsInboundMessageQueue($this->getMessageID());
			if ( $this->getMessageID() > 0 ) {
				$this->_Queue->load();
			}
		}
		return $this->_Queue;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param commsInboundMessageQueue $inObject
	 * @return commsInboundMessage
	 */
	function setQueue(commsInboundMessageQueue $inObject) {
		$this->_Queue = $inObject;
		return $this;
	}

	/**
	 * Returns an instance of commsInboundMessageTransaction, which is lazy loaded upon request
	 *
	 * @return commsInboundMessageTransaction
	 */
	function getTransactionMap() {
		if ( !$this->_TransactionMap instanceof commsInboundMessageTransaction ) {
			$this->_TransactionMap = new commsInboundMessageTransaction($this->getMessageID());
			if ( $this->getMessageID() > 0 ) {
				$this->_TransactionMap->load();
			}
		}
		return $this->_TransactionMap;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param commsInboundMessageTransaction $inObject
	 * @return commsInboundMessage
	 */
	function setTransactionMap(commsInboundMessageTransaction $inObject) {
		$this->_TransactionMap = $inObject;
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
	 * @return commsInboundMessage
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
	 * @return commsInboundMessage
	 */
	function setMessageBody($inString) {
		$this->getParamSet()->setParam(self::PARAM_MESSAGE_BODY, $inString);
		return $this;
	}
	
	/**
	 * Returns the PRS the message came from
	 * 
	 * @return string
	 */
	function getPRS() {
		return $this->getParamSet()->getParam(self::PARAM_PRS);
	}
	
	/**
	 * Sets the PRS the message came from
	 * 
	 * @param string $inPRS
	 * @return commsInboundMessage
	 */
	function setPRS($inPRS) {
		$this->getParamSet()->setParam(self::PARAM_PRS, $inPRS);
		return $this;
	}
}