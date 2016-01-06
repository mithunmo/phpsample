<?php
/**
 * transportResponseHttp class
 * 
 * Stored in transportResponseHttp.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage transport
 * @category transportResponseHttp
 * @version $Rev: 650 $
 */


/**
 * transportResponseHttp class
 * 
 * Contains a HTTP response containing the headers and message body
 * of the HTTP request.
 * 
 * @package scorpio
 * @subpackage transport
 * @category transportResponseHttp
 */
class transportResponseHttp extends transportResponse {
	
	/**
	 * Stores $_HttpHeader
	 *
	 * @var string
	 * @access protected
	 */
	protected $_HttpHeader;
	
	
	
	/**
	 * @see transportResponse::reset()
	 */
	function reset() {
		parent::reset();
		$this->_HttpHeader = null;
	}
	
	
	
	/**
	 * Returns $_HttpHeader
	 *
	 * @return string
	 * @access public
	 */
	function getHttpHeader() {
		return $this->_HttpHeader;
	}
	
	/**
	 * Set $_HttpHeader to $inHttpHeader
	 *
	 * @param string $inHttpHeader
	 * @return transportResponseHttp
	 * @access public
	 */
	function setHttpHeader($inHttpHeader) {
		if ( $this->_HttpHeader !== $inHttpHeader ) {
			$this->_HttpHeader = $inHttpHeader;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the body of the response
	 *
	 * @return string
	 * @access public
	 */
	function getHttpBody() {
		return $this->getResponse();
	}
}