<?php
/**
 * mvcSessionBase.class.php
 * 
 * mvcSessionBase class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcSessionBase
 * @version $Rev: 776 $
 */


/**
 * mvcSessionBase class
 * 
 * Wraps the PHP Session super global array for the MVC system. This class
 * can be extended for a specific site with additional methods for convenience
 * e.g. to fetch the user object or other objects by key instead of trying
 * to store an object within the session.
 * 
 * The session name can only be set by extending this class.
 * 
 * Sessions are initiated by using the {@link mvcDistributorPluginSession}
 * which is set in the site config.xml file.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcSessionBase
 */
class mvcSessionBase {
	
	const FORM_TOKEN_NAME = 'form.token';
	const FORM_TOKEN_GEN_TIME = 'form.token.gen.time';
	
	/**
	 * Tracks whether the session has been modified or not
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_SessionName; must be set before instantiating class
	 *
	 * @var string
	 * @access protected
	 */
	protected $_SessionName = 'SCORPIOSESSID';
	
	/**
	 * Stores $_SessionID
	 *
	 * @var string
	 * @access protected
	 */
	protected $_SessionID = false;
	
	/**
	 * Stores $_FormTokenLifetime; length of time token is valid for in seconds
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_FormTokenLifetime = 600;
	
	/**
	 * Stores $_Request
	 *
	 * @var mvcRequest
	 * @access protected
	 */
	protected $_Request = null;
	
	
	
	/**
	 * Starts the session and sets up vars
	 *
	 * @param mvcRequest $inMvcRequest
	 * @return mvcSessionBase
	 */
	function __construct($inMvcRequest = null) {
		static $sessionStarted = false;
		if ( $inMvcRequest instanceof mvcRequest ) {
			$this->setRequest($inMvcRequest);
		}
		if ( !$sessionStarted ) {
			$this->initialise();
		}
	}
	
	/**
	 * Performs any initialisation of the session
	 *
	 * @return void
	 */
	function initialise() {
		$this->_preInitialise();
		
		session_name($this->getSessionName());
		@session_start();
		$sessionStarted = true;
		$this->setSessionID(session_id());
		
		$this->_postInitialise();
		
		$this->setModified(false);
	}
	
	/**
	 * Performs any pre-initialisation e.g. setting session cookie parameters
	 *
	 * @return void
	 * @access protected
	 */
	protected function _preInitialise() {
		 
	}
	
	/**
	 * Performs any post-initialisation, e.g. any one time actions such as
	 * stats logging or handset detection
	 *
	 * @return void
	 * @access protected
	 */
	protected function _postInitialise() {
		
	}
	
	/**
	 * Destroys the currently started session, setting all session parameters to null first
	 * 
	 * @return boolean
	 */
	function destroy() {
		if ( count($_SESSION) > 0 ) {
			foreach ( array_keys($_SESSION) as $key ) {
				$_SESSION[$key] = null;
				unset($_SESSION[$key]);
			}
		}
		
		$_SESSION = array();
		return session_destroy();
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
	 * Set object status
	 *
	 * @param boolean $inStatus
	 * @return mvcSessionBase
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
		return $this;
	}
	
	/**
	 * Returns session param $inParamName
	 *
	 * @param string $inParamName
	 * @return mixed
	 */
	function getParam($inParamName) {
		if ( isset($_SESSION[$inParamName]) ) {
			return $_SESSION[$inParamName];
		}
		return false;
	}
	
	/**
	 * Returns all session params
	 *
	 * @return array
	 */
	function getParams() {
		if ( isset($_SESSION) ) {
			return $_SESSION;
		} else {
			return array();
		}
	}
	
	/**
	 * Sets the session param $inParamName overriding the previous value
	 *
	 * @param string $inParamName
	 * @param mixed $inParamValue
	 * @return mvcSessionBase
	 */
	function setParam($inParamName, $inParamValue) {
		if ( isset($_SESSION[$inParamName]) ) {
			if ( $inParamValue !== $_SESSION[$inParamName] ) {
				$_SESSION[$inParamName] = $inParamValue;
				$this->setModified();
			}
		} else {
			$_SESSION[$inParamName] = $inParamValue;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Removes and unsets $inParamName from the session
	 * 
	 * @param string $inParamName
	 * @return mvcSessionBase
	 */
	function removeParam($inParamName) {
		if ( array_key_exists($inParamName, $_SESSION) ) {
			$_SESSION[$inParamName] = null;
			unset($_SESSION[$inParamName]);
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_SessionName
	 *
	 * @return string
	 */
	function getSessionName() {
		return $this->_SessionName;
	}
	
	/**
	 * Set $_SessionName to $inSessionName
	 *
	 * @param string $inSessionName
	 * @return mvcSessionBase
	 */
	function setSessionName($inSessionName) {
		if ( $inSessionName !== $this->_SessionName ) {
			$this->_SessionName = $inSessionName;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_SessionID
	 *
	 * @return string
	 */
	function getSessionID() {
		return $this->_SessionID;
	}
	
	/**
	 * Set $_SessionID to $inSessionID
	 *
	 * @param string $inSessionID
	 * @return mvcSessionBase
	 */
	function setSessionID($inSessionID) {
		if ( $inSessionID !== $this->_SessionID ) {
			$this->_SessionID = $inSessionID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Regenerates the session ID, returning the mvcSession object
	 *
	 * @return mvcSessionBase
	 */
	function regenerateSessionID() {
		session_regenerate_id(true);
		return $this->setSessionID(session_id());
	}
	
	
	
	/**
	 * Creates a token for use with forms and stores it and the generation time
	 * in the current session.
	 *
	 * @return string
	 */
	function getFormToken() {
		if ( !$this->getParam(self::FORM_TOKEN_NAME) ) {
			$this->setParam(self::FORM_TOKEN_NAME, md5(uniqid(rand(), true)));
			$this->setParam(self::FORM_TOKEN_GEN_TIME, time());
		}
		return $this->getParam(self::FORM_TOKEN_NAME);
	}
	
	/**
	 * Returns true if the supplied token matches the current session token
	 *
	 * @param string $inFormToken
	 * @return boolean
	 */
	function isValidFormToken($inFormToken = null) {
		if ( $inFormToken !== null ) {
			$sToken = $this->getFormToken();
			if ( $sToken ) {
				if ( (time()-$this->getParam(self::FORM_TOKEN_GEN_TIME)) < $this->getFormTokenLifetime() ) {
					if ( $sToken == $inFormToken ) {
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * Removes the current session form token but does not create a new one
	 * 
	 * @return mvcSessionBase
	 */
	function clearFormToken() {
		$this->setParam(self::FORM_TOKEN_NAME, false);
		return $this;
	}
	
	/**
	 * Creates a new form token
	 *
	 * @return string
	 */
	function newFormToken() {
		$this->setParam(self::FORM_TOKEN_NAME, false);
		return $this->getFormToken();
	}

	/**
	 * Returns $_FormTokenLifetime
	 *
	 * @return integer
	 */
	function getFormTokenLifetime() {
		return $this->_FormTokenLifetime;
	}
	
	/**
	 * Set the time that the token is valid for; valid only for the current request
	 *
	 * @param integer $inFormTokenLifetime
	 * @return mvcSessionBase
	 */
	function setFormTokenLifetime($inFormTokenLifetime) {
		if ( $inFormTokenLifetime !== $this->_FormTokenLifetime ) {
			$this->_FormTokenLifetime = $inFormTokenLifetime;
			$this->setModified();
		}
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
	 * @return mvcSessionBase
	 */
	function setRequest($inRequest) {
		if ( $inRequest !== $this->_Request ) {
			$this->_Request = $inRequest;
			$this->setModified();
		}
		return $this;
	}
}