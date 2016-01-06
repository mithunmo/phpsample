<?php
/**
 * transportResponse class
 * 
 * Stored in transportResponse.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage transport
 * @category transportResponse
 * @version $Rev: 650 $
 */


/**
 * transportResponse class
 * 
 * Contains the request and response from calling the transport. If an exception was
 * raised, also contains the exception.
 * 
 * @package scorpio
 * @subpackage transport
 * @category transportResponse
 */
class transportResponse {
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	/**
	 * Stores $_Request
	 *
	 * @var transportCredentials
	 * @access protected
	 */
	protected $_Request;
	
	/**
	 * Stores $_Response
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Response;
	
	/**
	 * Stores $_TransportException
	 *
	 * @var transportException
	 * @access protected
	 */
	protected $_TransportException;
	
	

	/**
	 * Returns new instance of transportResponse
	 *
	 * @return transportResponse
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_Modified = false;
		$this->_Request = null;
		$this->_Response = null;
		$this->_TransportException = null;
	}
	
	
	
	/**
	 * Returns $_Modified
	 *
	 * @return boolean
	 * @access public
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set $_Modified to $inModified
	 *
	 * @param boolean $inModified
	 * @return transportResponse
	 * @access public
	 */
	function setModified($inModified = true) {
		if ( $this->_Modified !== $inModified ) {
			$this->_Modified = $inModified;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Request
	 *
	 * @return transportResponse
	 * @access public
	 */
	function getRequest() {
		return $this->_Request;
	}
	
	/**
	 * Set $_Request to $inRequest
	 *
	 * @param transportCredentials $inRequest
	 * @return transportResponse
	 * @access public
	 */
	function setRequest(transportCredentials $inRequest) {
		if ( $this->_Request !== $inRequest ) {
			$this->_Request = $inRequest;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Response
	 *
	 * @return string
	 * @access public
	 */
	function getResponse() {
		return $this->_Response;
	}
	
	/**
	 * Set $_Response to $inResponse
	 *
	 * @param string $inResponse
	 * @return transportResponse
	 * @access public
	 */
	function setResponse($inResponse) {
		if ( $this->_Response !== $inResponse ) {
			$this->_Response = $inResponse;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_TransportException
	 *
	 * @return transportException
	 * @access public
	 */
	function getTransportException() {
		return $this->_TransportException;
	}
	
	/**
	 * Set $_TransportException to $inTransportException
	 *
	 * @param transportException $inTransportException
	 * @return transportResponse
	 * @access public
	 */
	function setTransportException($inTransportException) {
		if ( $this->_TransportException !== $inTransportException ) {
			$this->_TransportException = $inTransportException;
			$this->setModified();
		}
		return $this;
	}
}