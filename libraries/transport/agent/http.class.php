<?php
/**
 * transportAgentHttp class
 * 
 * Stored in transportAgentHttp.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage transport
 * @category transportAgentHttp
 * @version $Rev: 707 $
 */


/**
 * transportAgentHttp class
 * 
 * Provides the base functions for various transport mechanism. A "transport" is a
 * connection to a remote system for sending messages / events etc. This is the
 * HTTP agent for making GET and POST requests. Connections are made using fsockopen
 * and the response handling will detect chunked responses.
 * 
 * The agent will attempt to re-connect several times before failing.
 * 
 * @package scorpio
 * @subpackage transport
 * @category transportAgentHttp
 */
class transportAgentHttp extends transportAgentBase {
	
	/**
	 * Stores TransportName
	 * 
	 * @var string
	 */
	protected $_TransportName			= 'HTTP Transport';
	
	
	
	/**
	 * Http Properties
	 */
	
	/**
	 * HTTP Connection resource handle
	 *
	 * @var resource
	 */
	protected $_HttpConnection			= false;
	
	
	
	/**
	 * Main methods
	 */
	
	/**
	 * @see transportAgentBase::_process()
	 */
	protected function _process() {
		$this->httpConnect();
		$this->buildHeader();
		return $this->sendMessage();
	}
	
	/**
	 * @see transportAgentBase::_reset()
	 */
	protected function _reset() {
		if ( $this->isConnectionActive() ) {
			@fclose($this->_HttpConnection);
		}
		$this->_HttpConnection = false;
	}
	
	
	
	/**
	 * Http connection methods
	 */
	
	/**
	 * Returns the http resource, or re-connects if not there
	 *
	 * @return resource
	 */
	protected function getConnection() {
		if ( !$this->isConnectionActive() ) {
			$this->httpConnect();
		}
		return $this->_HttpConnection;
	}
	
	/**
	 * Connects to a remote server via HTTP
	 * 
	 * @param boolean $forceConnect
	 * @return boolean
	 */
	protected function httpConnect($forceConnect = false) {
		if ( $this->isConnectionActive() == false || $this->getHttpPersistent() == false || $forceConnect) {
			systemLog::info("Connecting to ".$this->getHost().":".$this->getPort().' - timeout='.$this->getHttpTimeout());
			
			$errno = $errstr = false;
			$this->_HttpConnection = @fsockopen($this->getHost(), $this->getPort(), $errno, $errstr, $this->getHttpTimeout());
			if ($this->_HttpConnection) {
				if ($this->getHttpPersistent() == true) {
					systemLog::info("Persistant Connection to ".$this->getHost().":".$this->getPort()." made");
					stream_set_timeout($this->_HttpConnection, $this->getHttpPersistentTimeout());
				} else {
					systemLog::info('Connection to '.$this->getHost().':'.$this->getPort().' made');
					stream_set_timeout($this->_HttpConnection, $this->getHttpTimeout());
				}
				return true;
			}
			return false;
		}
		return true;
	}
	
	/**
	 * Returns true if the http connection resource is still connected
	 * Only relevant for persistant connections
	 * 
	 * @return boolean
	 */
	protected function isConnectionActive() {
		if ( !$this->_HttpConnection ) {
			return false;
		}
		
		$aStatus = stream_get_meta_data($this->_HttpConnection);
		if ($aStatus['timed_out']) {
			return false;
		}
		return true;
	}
	
	/**
	 * Builds the header of the http request
	 * 
	 * @return void
	 */
	function buildHeader() {
		if ( $this->getHttpPersistent() ) {
			$httpVersion = "HTTP/1.1";
		} else {
			$httpVersion = "HTTP/1.0";
		}
		
		$abs_url = $this->getHttpProtocol().'://'.$this->getHost().":".$this->getPort().$this->getPath();
		
		/*
		 * Check if post request
		 */
		if ( strtoupper($this->getHttpMethod()) == 'POST' ) {
			// Sending a POST request
			$httpHeader = 
				"POST ".$this->getPath()." ".$httpVersion."\r\n".
				"Content-Type: application/x-www-form-urlencoded\r\n".
				"Host: ".$this->getHost().":".$this->getPort()."\r\n";
					  
			// Add on any http auth headers
			if ( $this->getHttpAuth() ) {
				$httpHeader .=
					"Authorization: Basic ".base64_encode($this->getUsername().":".$this->getPassword())."\r\n";
			}
			// Add the Content-Length
			$httpHeader .= "Content-Length: ".strlen($this->getHttpBody())."\r\n\r\n";
		} else {
			// Sending a GET request - so the data is part of the header
			$httpHeader = "GET ".$abs_url."?".$this->getHttpBody().' '.$httpVersion."\r\n";
			
			// Add on any http auth headers
			if ( $this->getHttpAuth() ) {
				$httpHeader .=
					"Authorization: Basic ".base64_encode($this->getUsername().":".$this->getPassword())."\r\n";
			}
			$httpHeader .= "Host: ".$this->getHost()."\r\n\r\n";
		}
		$this->getTransportCredentials()->setParam(transportCredentials::PARAM_MESSAGE_HEADER, $httpHeader);
	}
	
	/**
	 * Sends the data over the HTTP connection, check getResponse, getResponseHeader for data
	 * 
	 * @return boolean
	 */
	function sendMessage() {
		systemLog::info('Sending http message');
		
		$loopCount = 0;
		while ( !$this->httpConnect() && $loopCount < 10 ) {
			$this->setResponse("Connection to ".$this->getHost()." failed.");
			trigger_error("TCP/IP Connection failed", E_USER_NOTICE);
			sleep(3);
			$loopCount++;
		}
		systemLog::info("TCP/IP Connection established");
		$retry = false;
		
		do {
			if ( $retry ) {
				systemLog::message("Connection re-established, resending data");
			}
			$retry = false;
			// Send the http message data
			if ( strtoupper($this->getHttpMethod()) == 'POST' ) {
				systemLog::info("Sending data down connection");
				
				@fputs($this->getConnection(), $this->getHttpHeader());
				@fputs($this->getConnection(), $this->getHttpBody());
				
				systemLog::debug($this->getHttpHeader());
				systemLog::info($this->getHttpBody());
			} else {
				@fputs($this->getConnection(), $this->getHttpHeader());
				systemLog::info($this->getHttpHeader());
			}
			
			// Now retrieve the server's response
			systemLog::debug('Waiting for HTTP Response');
			
			$response = '';
			$header = '';
			// Header first
			$count = 0;
			
			do {
				$token = @fread($this->getConnection(), 1);
				$header.=$token;
				if ( ord($token) == 0 ) {
					$count++;
					systemLog::notice("CONNECTION TIMED OUT, RECONNECTING");
					// Reconnect
					if ( !$this->httpConnect(TRUE) ) {
						// Can't reconnect so bail
						$count = 5;
					} else {
						sleep(1);
						systemLog::notice("Reconnected, resending data");
						// Resend the data
						$retry = true;
					}
				}
			} while (!preg_match('/\\r\\n\\r\\n$/',$header) && $count < 5 && !$retry);
			
			// Our little count above tells us that the connection has died and failed to reconnect
			if ( $count >= 5 ) {
				trigger_error("TCP/IP Connection failed", E_USER_NOTICE);
				$this->setResponse('Connection to gateway failed. Remote server not responding.');
				@fclose($this->_HttpConnection);
				$this->_HttpConnection = false;
				return false;
			}
		} while ($retry);
		
		// We have a response - so we must parse it
		if ( preg_match('/Transfer\\-Encoding:\\s+chunked\\r\\n/', $header) ) {
			systemLog::debug("CHUNKED ENCODED RESPONSE DETECTED");
			
			do {
				$byte = '';
				$chunk_size = '';
				do {
					$chunk_size .= $byte;
					$byte = fread($this->getConnection(),1);
				} while ($byte!="\r");
				
				@fread($this->getConnection(), 1);
				$chunk_size=hexdec($chunk_size); // convert to real number
				if ( $chunk_size > 0 ) {
					$response.=fread($this->getConnection(), $chunk_size);
				}
				@fread($this->getConnection(), 2);       // ditch the CRLF that trails the chunk
			} while ($chunk_size && !feof($this->getConnection()));
		} else {
			// check for specified content length
			$matches = array();
			if (preg_match('/Content\\-Length:\\s+([0-9]*)\\r\\n/', $header, $matches)) {
				$response = @fread($this->getConnection(), $matches[1]);
			} else {
				// not a nice way to do it (may also result in extra CRLF which trails the real content???)
				while ( !feof($this->getConnection()) ) {
					$response .= @fread($this->getConnection(), 4096);
				}
			}
		}
		
		if ( stristr($header, 'Connection: Close') ) {
			systemLog::debug("CONNECTION CLOSE RECEIVED");
			@fclose($this->_HttpConnection);
			$this->_HttpConnection = false;
		} else {
			systemLog::debug("HTTP HEADERS\n".$header);
			// Close the tcp connection
			if ( $this->getHttpPersistent() == false ) {
				@fclose($this->_HttpConnection);
			}
		}
		
		systemLog::debug("HTTP RESPONSE\n".$response);
		$oResponse = new transportResponseHttp();
		$oResponse->setHttpHeader($header);
		$oResponse->setResponse($response);
		$this->setResponse($oResponse);
		return true;
	}
	
	
	
	/**
	 * Helper methods for convenience and legibility
	 */
	
	/**
	 * Return protocol
	 *
	 * @return string
	 */
	function getHttpProtocol() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_PROTOCOL, transportCredentials::DEFAULT_HTTP_PROTOCOL);
	}
	
	/**
	 * Return http method
	 *
	 * @return string
	 */
	function getHttpMethod() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_HTTP_METHOD, transportCredentials::DEFAULT_HTTP_METHOD);
	}
	
	/**
	 * Return is persistent connection
	 *
	 * @return boolean
	 */
	function getHttpPersistent() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_HTTP_PERSISTENT, transportCredentials::DEFAULT_HTTP_PERSISTENT);
	}
	
	/**
	 * Return timeout
	 *
	 * @return integer
	 */
	function getHttpTimeout() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_HTTP_TIMEOUT, transportCredentials::DEFAULT_HTTP_TIMEOUT);
	}
	
	/**
	 * Return persistent connection timeout
	 *
	 * @return integer
	 */
	function getHttpPersistentTimeout() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_HTTP_PERSISTENT_TIMEOUT, transportCredentials::DEFAULT_HTTP_PERSISTENT_TIMEOUT);
	}
	
	/**
	 * Return http authentication
	 *
	 * @return boolean
	 */
	function getHttpAuth() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_HTTP_AUTH, transportCredentials::DEFAULT_HTTP_AUTH);
	}
	
	/**
	 * Return the message header that matches the message body
	 *
	 * @return string
	 */
	function getHttpHeader() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_MESSAGE_HEADER);
	}
	
	/**
	 * Return the message body to be sent
	 *
	 * @return string
	 */
	function getHttpBody() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_MESSAGE_BODY);
	}
}