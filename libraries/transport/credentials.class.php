<?php
/**
 * transportCredentials class
 * 
 * Stored in transportCredentials.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage transport
 * @category transportCredentials
 * @version $Rev: 819 $
 */


/**
 * transportCredentials class
 * 
 * Holds details for connecting via a transport mechanism including server,
 * port username and password etc. Any number of params can be used, however
 * some have been pre-defined for use in the existing agents.
 * 
 * <code>
 * // set multiple options at once - don't forget the "null" as the second arg.
 * $oCredentials = new transportCredentials();
 * $oCredentials->setParam(
 *     array(
 *         transportCredentials::PARAM_PROTOCOL => 'http',
 *         transportCredentials::PARAM_HOST => 'www.example.com',
 *         transportCredentials::PARAM_PORT => '8080',
 *         transportCredentials::PARAM_PATH => '/server/side/path.cgi',
 *         
 *         transportCredentials::PARAM_HTTP_TIMEOUT => 60,
 *         transportCredentials::PARAM_HTTP_PERSISTENT_TIMEOUT => 60,
 *         transportCredentials::PARAM_HTTP_METHOD => 'post',
 *         
 *         transportCredentials::PARAM_MESSAGE_BODY => 'encode_post_vars',
 *     ), null
 * );
 * 
 * // set individual properties
 * $oCredentials->setParam(transportCredentials::PARAM_HTTP_METHOD, 'get');
 * </code>
 * 
 * @package scorpio
 * @subpackage transport
 * @category transportCredentials
 */
class transportCredentials extends baseSet {
	
	/*
	 * Param names for default params required by all transports
	 */
	const PARAM_PROTOCOL = 'transport.protocol';
	const PARAM_HOST = 'transport.host';
	const PARAM_PORT = 'transport.port';
	const PARAM_PATH = 'transport.path';
	const PARAM_USERNAME = 'transport.username';
	const PARAM_PASSWORD = 'transport.password';
	
	/*
	 * Param names for app connections
	 */
	const PARAM_APP_RESPONSE_OK = 'app.response.ok';
	const PARAM_APP_BACKGROUND = 'app.background';
	
	/*
	 * Param names for email connections
	 */
	const PARAM_EMAIL_CONNECTION_TYPE = 'email.connection.type';
	const PARAM_EMAIL_BODY_TYPE = 'email.body.type';
	const PARAM_EMAIL_HTML = 'html';
	const PARAM_EMAIL_TEXT = 'text';
	
	/*
	 * Param names for http connections
	 */
	const PARAM_HTTP_PERSISTENT = 'http.connection.persistent';
	const PARAM_HTTP_TIMEOUT = 'http.connection.timeout';
	const PARAM_HTTP_PERSISTENT_TIMEOUT = 'http.connection.persistent.timeout';
	const PARAM_HTTP_METHOD = 'http.connection.method';
	const PARAM_HTTP_AUTH = 'http.connection.httpAuthentication';
	
	/*
	 * Default values for app connections
	 */
	const DEFAULT_APP_RESPONSE_OK = '0';
	const DEFAULT_APP_BACKGROUND = false;
	
	/*
	 * Default values for email connections
	 */
	const DEFAULT_EMAIL_CONNECTION_TYPE = 'mail';
	const DEFAULT_EMAIL_BODY_TYPE = self::PARAM_EMAIL_HTML;
	
	/*
	 * Default values for HTTP connections
	 */
	const DEFAULT_HTTP_PROTOCOL = 'http';
	const DEFAULT_HTTP_TIMEOUT = '10';
	const DEFAULT_HTTP_PERSISTENT_TIMEOUT = '60';
	const DEFAULT_HTTP_METHOD = 'POST';
	const DEFAULT_HTTP_AUTH = false;
	const DEFAULT_HTTP_PERSISTENT = false;
	
	/*
	 * Param names for message components
	 */
	const PARAM_MESSAGE_HEADER = 'transport.message.header';
	const PARAM_MESSAGE_SUBJECT = 'transport.message.subject';
	const PARAM_MESSAGE_BODY = 'transport.message.body';
	const PARAM_MESSAGE_BODY_TEXT = 'transport.message.text';
	const PARAM_MESSAGE_RECIPIENT = 'transport.message.recipient';
	const PARAM_MESSAGE_SENDER = 'transport.message.sender';
	
	
	
	/**
	 * Constructor
	 */
	
	/**
	 * Returns new instance of transportCredentials
	 *
	 * @return transportCredentials
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * Resets object to defaults
	 *
	 * @return void
	 */
	function reset() {
		parent::_resetSet();
	}
	
	
	
	/**
	 * Get / Set Methods
	 */
	
	/**
	 * Get param named $inParamName, if $inDefault is set, returns this value if param does note exist
	 *
	 * @param string $inParamName
	 * @param mixed $inDefault
	 * @return mixed
	 */
	function getParam($inParamName, $inDefault = null) {
		$return = $this->_getItem($inParamName);
		if ( $return === false && $inDefault !== null ) {
			$return = $inDefault;
		}
		return $return;
	}
	
	/**
	 * Set param $inParamName with value $inParamValue
	 *
	 * @param string $inParamName
	 * @param mixed $inParamValue
	 * @return transportCredentials
	 */
	function setParam($inParamName, $inParamValue) {
		$this->_setItem($inParamName, $inParamValue);
		return $this;
	}
	
	/**
	 * Returns param count
	 *
	 * @return integer
	 */
	function countParams() {
		return $this->_itemCount();
	}
	
	
	
	/**
	 * Convenience methods
	 */
	
	/**
	 * Get Protocol
	 *
	 * @return string
	 */
	function getProtocol() {
		return $this->getParam(self::PARAM_PROTOCOL);
	}
	
	/**
	 * Set Protocol to value $inParamValue
	 *
	 * @param string $inParamValue
	 * @return transportCredentials
	 */
	function setProtocol($inParamValue) {
		return $this->setParam(self::PARAM_PROTOCOL, $inParamValue);
	}
	
	/**
	 * Get Host
	 *
	 * @return string
	 */
	function getHost() {
		return $this->getParam(self::PARAM_HOST);
	}
	
	/**
	 * Set Host to value $inParamValue
	 *
	 * @param string $inParamValue
	 * @return transportCredentials
	 */
	function setHost($inParamValue) {
		return $this->setParam(self::PARAM_HOST, $inParamValue);
	}
	
	/**
	 * Get Port
	 *
	 * @return integer
	 */
	function getPort() {
		return $this->getParam(self::PARAM_PORT);
	}
	
	/**
	 * Set Port to value $inParamValue
	 *
	 * @param integer $inParamValue
	 * @return transportCredentials
	 */
	function setPort($inParamValue) {
		return $this->setParam(self::PARAM_PORT, $inParamValue);
	}
	
	/**
	 * Get Path
	 *
	 * @return string
	 */
	function getPath() {
		return $this->getParam(self::PARAM_PATH);
	}
	
	/**
	 * Set Path to value $inParamValue
	 *
	 * @param string $inParamValue
	 * @return transportCredentials
	 */
	function setPath($inParamValue) {
		return $this->setParam(self::PARAM_PATH, $inParamValue);
	}
	
	/**
	 * Get Username
	 *
	 * @return string
	 */
	function getUsername() {
		return $this->getParam(self::PARAM_USERNAME);
	}
	
	/**
	 * Set Username to value $inParamValue
	 *
	 * @param string $inParamValue
	 * @return transportCredentials
	 */
	function setUsername($inParamValue) {
		return $this->setParam(self::PARAM_USERNAME, $inParamValue);
	}
	
	/**
	 * Get Password
	 *
	 * @return string
	 */
	function getPassword() {
		return $this->getParam(self::PARAM_PASSWORD);
	}
	
	/**
	 * Set Password to value $inParamValue
	 *
	 * @param string $inParamValue
	 * @return transportCredentials
	 */
	function setPassword($inParamValue) {
		return $this->setParam(self::PARAM_PASSWORD, $inParamValue);
	}
	
	/**
	 * Get MessageBody
	 *
	 * @return string
	 */
	function getMessageBody() {
		return $this->getParam(self::PARAM_MESSAGE_BODY);
	}
	
	/**
	 * Set MessageBody to value $inParamValue
	 *
	 * @param string $inParamValue
	 * @return transportCredentials
	 */
	function setMessageBody($inParamValue) {
		return $this->setParam(self::PARAM_MESSAGE_BODY, $inParamValue);
	}

	/**
	 * Get plain text MessageBody
	 *
	 * @return string
	 */
	function getMessageBodyText() {
		return $this->getParam(self::PARAM_MESSAGE_BODY_TEXT);
	}

	/**
	 * Set plain text MessageBody to value $inParamValue
	 *
	 * @param string $inParamValue
	 * @return transportCredentials
	 */
	function setMessageBodyText($inParamValue) {
		return $this->setParam(self::PARAM_MESSAGE_BODY_TEXT, $inParamValue);
	}
	
	/**
	 * Get MessageHeader
	 *
	 * @return string
	 */
	function getMessageHeader() {
		return $this->getParam(self::PARAM_MESSAGE_HEADER);
	}
	
	/**
	 * Set MessageHeader to value $inParamValue
	 *
	 * @param string $inParamValue
	 * @return transportCredentials
	 */
	function setMessageHeader($inParamValue) {
		return $this->setParam(self::PARAM_MESSAGE_HEADER, $inParamValue);
	}
	
	/**
	 * Get MessageSubject
	 *
	 * @return string
	 */
	function getMessageSubject() {
		return $this->getParam(self::PARAM_MESSAGE_SUBJECT);
	}
	
	/**
	 * Set MessageSubject to value $inParamValue
	 *
	 * @param string $inParamValue
	 * @return transportCredentials
	 */
	function setMessageSubject($inParamValue) {
		return $this->setParam(self::PARAM_MESSAGE_SUBJECT, $inParamValue);
	}
}