<?php
/**
 * commsGatewayAdaptorBase
 *
 * Stored in commsGatewayAdaptorBase.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage gateway
 * @category commsGatewayAdaptorBase
 * @version $Rev: 10 $
 */


/**
 * commsGatewayAdaptorBase
 * 
 * Provides the base components for actually sending the messages via
 * the gateway provided transport mechanism. This class requires extending
 * to provide the necessary methods and data for the message type.
 * 
 * The transport is initialised in the following steps:
 * 
 * <ol>
 *   <li>Setting of the message</li>
 *   <li>Loading of gateway and gateway account</li>
 *   <li>Creation of credentials</li>
 *   <li>preProcess() call</li>
 *   <li>Transport send</li>
 *   <li>Response trapped</li>
 *   <li>postProcess() call</li>
 *   <li>cleanUp() call</li>
 *   <li>Destruction of transport and response</li>
 * </ol>
 * 
 * preProcess() and postProcess() are provided as general methods called
 * before and after the transport has been initiated. Any further setup
 * should be provided by an intermediary class as required.
 * 
 * preProcess would be used to further transform the message body or to
 * compile HTTP headers / bodies or to encode messages, provide encryption
 * or any other logical transformation before the transport is called.
 * 
 * postProcess() is used to handle the response, log the response, update
 * stats, provide queue maintenance etc.
 *
 * @package comms
 * @subpackage gateway
 * @category commsGatewayAdaptorBase
 */
abstract class commsGatewayAdaptorBase {
	
	/**
	 * Stores $_Message
	 *
	 * @var commsOutboundMessage
	 * @access protected
	 */
	protected $_Message;
	
	/**
	 * Stores $_Gateway
	 *
	 * @var commsGateway
	 * @access protected
	 */
	protected $_Gateway;
	
	/**
	 * Stores $_GatewayAccount
	 *
	 * @var commsGatewayAccount
	 * @access protected
	 */
	protected $_GatewayAccount;
	
	/**
	 * Stores $_Credentials
	 *
	 * @var transportCredentials
	 * @access protected
	 */
	protected $_Credentials;
	
	/**
	 * Stores $_Sent
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Sent;
	
	
	
	/**
	 * Creates a new gateway adaptor instance
	 * 
	 * @param commsOutboundMessage $inMessage
	 */
	function __construct(commsOutboundMessage $inMessage) {
		$this->reset();
		$this->setMessage($inMessage);
		$this->setGateway(commsGateway::getInstance($inMessage->getGatewayID()));
		$this->setGatewayAccount(commsGatewayAccount::getInstance($inMessage->getGatewayAccountID()));
	}
	
	/**
	 * Ensures that clean up is always performed
	 * 
	 * @return void
	 */
	function __destruct() {
		$this->_cleanUp();
	}
	
	/**
	 * Reset the object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_Message = null;
		$this->_Gateway = null;
		$this->_GatewayAccount = null;
		$this->_Credentials = null;
		$this->_Sent = false;
	}
	
	/**
	 * Send the message
	 * 
	 * @return boolean
	 */
	function send() {
		$this->_buildTransportCredentials();
		$this->_preProcess();
		
		/*
		 * Auto-add unknown transports
		 */
		if ( !transportManager::isValidTransport($this->getGateway()->getTransportClass()) ) {
			transportManager::addTransport($this->getGateway()->getTransportClass());
		}
		
		$oTransport = transportManager::getInstance($this->getGateway()->getTransportClass(), $this->getCredentials());
		if ( $oTransport->send() ) {
			$this->setSent(true);
		} else {
			$this->setSent(false);
		}
		$oResponse = $oTransport->getResponse();
		
		$this->_postProcess($oResponse);
		$this->_cleanUp();
		
		$oTransport = null;
		$oResponse = null;
		unset($oTransport, $oResponse);
	}
	
	/**
	 * Preprocesses the transport credentials before they are sent via the transport
	 * 
	 * @return void
	 */
	abstract protected function _preProcess();
	
	/**
	 * Post-processes the response after the message has been sent
	 * 
	 * @param transportResponse $inResponse
	 * @return void
	 */
	abstract protected function _postProcess(transportResponse $inResponse);
	
	/**
	 * Cleans up the objects and ensures they are unloaded from memory
	 * 
	 * @return void
	 */
	protected function _cleanUp() {
		$this->reset();
	}
	
	/**
	 * Logs a sent message
	 * 
	 * @return void
	 * @access protected
	 */
	protected function _logSent() {
		$oLog = commsOutboundLog::getInstance(
			$this->getMessage()->getOutboundTypeID(), $this->getMessage()->getGatewayID(),
			$this->getMessage()->getGatewayAccountID(), $this->getMessage()->getNetworkID()
		);
		$oLog->incrementSent()->save();
		$oLog = null;
		unset($oLog);
	}
	
	/**
	 * Builds the transport parameters from the gateway account and message
	 * 
	 * @return transportCredentials
	 */
	protected function _buildTransportCredentials() {
		$params = array_merge(
			$this->getGatewayAccount()->getParamSet()->getParam(),
			$this->getMessage()->getTransportCredentials()
		);
		
		$oCredentials = $this->getCredentials();
		$oCredentials->reset();
		$oCredentials->setParam($params, null);
		
		return $oCredentials;
	}
	
	

	/**
	 * Returns $_Message
	 *
	 * @return commsOutboundMessage
	 */
	function getMessage() {
		return $this->_Message;
	}
	
	/**
	 * Set $_Message to $inMessage
	 *
	 * @param commsOutboundMessage $inMessage
	 * @return commsGatewayAdaptorBase
	 */
	function setMessage($inMessage) {
		if ( $inMessage !== $this->_Message ) {
			$this->_Message = $inMessage;
		}
		return $this;
	}
	
	/**
	 * Returns $_Gateway
	 *
	 * @return commsGateway
	 */
	function getGateway() {
		return $this->_Gateway;
	}
	
	/**
	 * Set $_Gateway to $inGateway
	 *
	 * @param commsGateway $inGateway
	 * @return commsGatewayAdaptorBase
	 */
	function setGateway($inGateway) {
		if ( $inGateway !== $this->_Gateway ) {
			$this->_Gateway = $inGateway;
		}
		return $this;
	}

	/**
	 * Returns $_GatewayAccount
	 *
	 * @return commsGatewayAccount
	 */
	function getGatewayAccount() {
		return $this->_GatewayAccount;
	}
	
	/**
	 * Set $_GatewayAccount to $inGatewayAccount
	 *
	 * @param commsGatewayAccount $inGatewayAccount
	 * @return commsGatewayAdaptorBase
	 */
	function setGatewayAccount($inGatewayAccount) {
		if ( $inGatewayAccount !== $this->_GatewayAccount ) {
			$this->_GatewayAccount = $inGatewayAccount;
		}
		return $this;
	}

	/**
	 * Returns $_Credentials
	 *
	 * @return transportCredentials
	 */
	function getCredentials() {
		if ( !$this->_Credentials instanceof transportCredentials ) {
			$this->_Credentials = new transportCredentials();
		}
		return $this->_Credentials;
	}
	
	/**
	 * Set $_Credentials to $inCredentials
	 *
	 * @param transportCredentials $inCredentials
	 * @return commsGatewayAdaptorBase
	 */
	function setCredentials($inCredentials) {
		if ( $inCredentials !== $this->_Credentials ) {
			$this->_Credentials = $inCredentials;
		}
		return $this;
	}

	/**
	 * Returns $_Sent
	 *
	 * @return boolean
	 */
	function getSent() {
		return $this->_Sent;
	}
	
	/**
	 * Set $_Sent to $inSent
	 *
	 * @param boolean $inSent
	 * @return commsGatewayAdaptorBase
	 */
	function setSent($inSent) {
		if ( $inSent !== $this->_Sent ) {
			$this->_Sent = $inSent;
		}
		return $this;
	}
}