<?php
/**
 * mvcResponse.class.php
 * 
 * mvcResponse class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcResponse
 * @version $Rev: 707 $
 */


/**
 * mvcResponse class
 * 
 * Contains the response to the HTTP request. The response will encapsulate
 * whatever view data has been generated along with any headers etc.
 * 
 * <code>
 * $oResponse = new mvcResponse(mvcRequest::getInstance(), '<p>Hello World!</p>');
 * $oResponse->send();
 * </code>
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcResponse
 */
class mvcResponse {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Request
	 *
	 * @var mvcRequest
	 * @access protected
	 */
	protected $_Request;
	
	/**
	 * Array of HTTP headers to send with response
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_Headers;
	
	const HEADER_CONTENT_DISPOSITION = 'Content-Disposition';
	const HEADER_CONTENT_TYPE = 'Content-Type';
	const HEADER_CONTENT_LENGTH = 'Content-Length';
	const HEADER_EXPIRES = 'Expires';
	const HEADER_LAST_MODIFIED = 'Last-Modified';
	const HEADER_LOCATION = 'Location';
	const HEADER_STATUS = 'Status';
	
	const HEADER_STATUS_200 = '200 OK';
	
	const HEADER_STATUS_300 = '300 Multiple Choices';
	const HEADER_STATUS_301 = '301 Moved Permanently';
	const HEADER_STATUS_302 = '302 Found';
	const HEADER_STATUS_303 = '303 See Other';
	const HEADER_STATUS_304 = '304 Not Modified';
	
	const HEADER_STATUS_400 = '400 Bad Request';
	const HEADER_STATUS_401 = '401 Unauthorized';
	const HEADER_STATUS_403 = '403 Forbidden';
	const HEADER_STATUS_404 = '404 Not Found';
	
	const HEADER_STATUS_500 = '500 Internal Server Error';
	const HEADER_STATUS_501 = '501 Not Implemented';
	const HEADER_STATUS_503 = '503 Service Unavailable';
	
	/**
	 * Stores $_Content
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Content;
	
	
	
	/**
	 * Creates a new response
	 * 
	 * @param mvcRequest $inRequest
	 */
	function __construct(mvcRequest $inRequest, $inContent = null, $inHeaders = array()) {
		$this->setRequest($inRequest);
		$this->setContent($inContent);
		$this->getHeaders()->setOptions($inHeaders);
	}
	
	/**
	 * Converts response to a string, triggering headers to be sent if not already
	 * 
	 * @return string
	 */
	function toString() {
		return $this->__toString();
	}
	
	/**
	 * If the response is used as a string, dump the contents and headers to the requester
	 * 
	 * @return string
	 */
	function __toString() {
		$this->sendHeaders();
		
		return (string) $this->getContent();
	}
	
	/**
	 * Sends the response to the requester
	 * 
	 * @return void
	 */
	function send() {
		$this->sendHeaders();
		$this->sendContent();
	}
	
	/**
	 * Sends headers, if headers have not already been sent
	 * 
	 * @return void
	 */
	function sendHeaders() {
		if ( !headers_sent() ) {
			foreach ( $this->getHeaders() as $type => $value ) {
				if ( $type == self::HEADER_STATUS ) {
					header($this->getRequest()->getServerProtocol().' '.$value);
				} else {
					header("$type: $value");
				}
			}
		}
	}
	
	/**
	 * Sends only the content
	 * 
	 * @return void
	 */
	function sendContent() {
		echo (string) $this->getContent();
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
	 * @return mvcResponse
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns $_Request
	 *
	 * @return mvcRequest
	 */
	function getRequest() {
		return $this->_Request;
	}
	
	/**
	 * Set $_Request to $inRequest
	 *
	 * @param mvcRequest $inRequest
	 * @return mvcResponse
	 */
	function setRequest($inRequest) {
		if ( $inRequest !== $this->_Request ) {
			$this->_Request = $inRequest;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the current set of headers to be used during output
	 *
	 * @return baseOptionsSet
	 */
	function getHeaders() {
		if ( !$this->_Headers instanceof baseOptionsSet ) {
			$this->_Headers = new baseOptionsSet(
				array(
					self::HEADER_STATUS => self::HEADER_STATUS_200,
					self::HEADER_CONTENT_TYPE => $this->getRequest()->getMimeType(),
				)
			);
		}
		return $this->_Headers;
	}
	
	/**
	 * Add a header to the set, will overwrite an existing header
	 *
	 * @param string $inType
	 * @param string $inValue
	 * @return mvcViewBase
	 */
	function addHeader($inType, $inValue) {
		$this->getHeaders()->setOptions(array($inType => $inValue));
		return $this;
	}
	
	/**
	 * Removes the specified header from the set
	 *
	 * @param string $inType
	 * @return mvcViewBase
	 */
	function removeHeader($inType) {
		$this->getHeaders()->removeOptions(array($inType));
		return $this;
	}

	/**
	 * Returns current content array
	 *
	 * @return string
	 */
	function getContent() {
		return $this->_Content;
	}
	
	/**
	 * Sets or replaces the content response
	 *
	 * @param string $inContent
	 * @return mvcResponse
	 */
	function setContent($inContent) {
		if ( $inContent !== $this->_Content ) {
			$this->_Content = $inContent;
			$this->setModified();
		}
		return $this;
	}
}