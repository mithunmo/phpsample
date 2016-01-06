<?php
/**
 * transportAgentBase class
 * 
 * Stored in transportAgentBase.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage transport
 * @category transportAgentBase
 * @version $Rev: 650 $
 */

/**
 * transportAgentBase class
 * 
 * Provides the base functions for various transport mechanism. A "transport" is a
 * connection to a remote system for sending messages / events etc.
 * 
 * @package scorpio
 * @subpackage transport
 * @category transportAgentBase
 * @abstract 
 */
abstract class transportAgentBase {
	
	/**
	 * Stores TransportName
	 * 
	 * @var string
	 * @abstract 
	 */
	protected $_TransportName			= 'Base Transport';
	
	/**
	 * Stores TransportCredentials
	 * 
	 * @var transportCredentials
	 */
	protected $_TransportCredentials	= null;
		
	/**
	 * Stores Response
	 * 
	 * @var transportResponse
	 */
	protected $_Response				= null;
	
	
	
	/**
	 * Constructor
	 */
	
	/**
	 * Returns new instance of transportAgentBase
	 *
	 * @param transportCredentials $oCredentials
	 * @return transportAgentBase
	 */
	final function __construct(transportCredentials $oCredentials) {
		$this->setTransportCredentials($oCredentials);
	}
	
	
	
	/**
	 * Main methods
	 */
	
	/**
	 * Makes the transport request
	 *
	 * @return boolean
	 * @throws utilityTransportError
	 */
	final function send() {
		return $this->_process();
	}
	
	/**
	 * Abstract implementation for process()
	 *
	 * @return boolean
	 * @abstract 
	 */
	abstract protected function _process();
	
	/**
	 * Reset transport properties
	 *
	 * @return boolean
	 */
	final function reset() {
		$this->_Response = null;
		$this->_TransportCredentials = null;
		$this->_reset();
		return true;
	}
	
	/**
	 * Performs custom reset of properties
	 *
	 * @return void
	 * @abstract 
	 */
	abstract protected function _reset();
	
	
	
	/**
	 * Get / Set Methods
	 */
	
	/**
	 * Return TransportName
	 * 
	 * @return string
	 */
	function getTransportName() {
		return $this->_TransportName;
	}
	
	/**
	 * Set TransportName to $inTransportName
	 * 
	 * @param string $inTransportName
	 * @return transportAgentBase
	 */
	function setTransportName($inTransportName) {
		if ( $inTransportName !== $this->_TransportName ) {
			$this->_TransportName = $inTransportName;
			$this->_changed = true;
		}
		return $this;
	}
	
	/**
	 * Return TransportCredentials
	 * 
	 * @return transportCredentials
	 */
	function getTransportCredentials() {
		return $this->_TransportCredentials;
	}
	
	/**
	 * Set TransportCredentials to $inTransportCredentials
	 * 
	 * @param transportCredentials $inTransportCredentials
	 * @return transportAgentBase
	 */
	function setTransportCredentials(transportCredentials $inTransportCredentials) {
		if ( $inTransportCredentials !== $this->_TransportCredentials ) {
			$this->_TransportCredentials = $inTransportCredentials;
			$this->_changed = true;
		}
		return $this;
	}
	
	/**
	 * Return Response
	 * 
	 * @return transportResponse
	 */
	function getResponse() {
		return $this->_Response;
	}
	
	/**
	 * Set Response to Response
	 * 
	 * @param transportResponse $inResponse
	 * @return transportAgentBase
	 */
	function setResponse(transportResponse $inResponse) {
		if ( $inResponse !== $this->_Response ) {
			$this->_Response = $inResponse;
			$this->_changed = true;
		}
		return $this;
	}
	
	
	
	/**
	 * Methods providing quick access to some common properties
	 */
	
	/**
	 * Return host
	 *
	 * @return string
	 */
	function getHost() {
		return $this->getTransportCredentials()->getHost();
	}
	
	/**
	 * Return port
	 *
	 * @return integer
	 */
	function getPort() {
		return $this->getTransportCredentials()->getPort();
	}
	
	/**
	 * Return path
	 *
	 * @return string
	 */
	function getPath() {
		return $this->getTransportCredentials()->getPath();
	}
	
	/**
	 * Return username
	 *
	 * @return string
	 */
	function getUsername() {
		return $this->getTransportCredentials()->getUsername();
	}
	
	/**
	 * Return password
	 *
	 * @return string
	 */
	function getPassword() {
		return $this->getTransportCredentials()->getPassword();
	}
}