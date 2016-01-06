<?php
/**
 * mvcSession.class.php
 *
 * mvcSession class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm.com
 * @subpackage websites_mofilm.com_libraries
 * @category mvcSession
 */


/**
 * mvcSession
 *
 * Main site mvcSession implementation, holds base directives and defaults for the site
 *
 * @package mofilm.com
 * @subpackage websites_mofilm.com_libraries
 * @category mvcSession
 */
class mvcSession extends mvcSessionBase {

	const SESSION_NAME = 'MOFILM_Session';
	
	const MOFILM_USER_ID = 'mofilm.userID';
	const MOFILM_LOGGED_IN = 'mofilm.connected';
	const MOFILM_FB_LOGGED_IN = 'mofilm.fb.connected';
	const MOFILM_MESSAGE = 'mofilm.message';
	const MOFILM_MESSAGE_LEVEL = 'mofilm.message.level';

	const MESSAGE_OK = 0;
	const MESSAGE_CRITICAL = 1;
	const MESSAGE_ERROR = 2;
	const MESSAGE_WARNING = 4;
	const MESSAGE_INFO = 8;

	/**
	 * Holds instance of mofilmUser
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_MofilmUser = false;
	
	/**
	 * Holds an instance of the login hash record
	 * 
	 * @var mofilmUserLoginHash
	 * @access protected
	 */
	protected $_MofilmUserLoginHash = null;


	
	/**
	 * @see mvcSessionBase::_preInitialise()
	 */
	protected function _preInitialise() {
		 $this->setSessionName(self::SESSION_NAME);
		 
		 /*
		  * This allows the session to be auto-started from the var name, used for ajax requests
		  */
		 if ( isset($_POST[self::SESSION_NAME]) && strlen($_POST[self::SESSION_NAME]) > 0 ) {
		 	session_id($_POST[self::SESSION_NAME]);
		 }
	}
	
	/**
	 * @see mvcSessionBase::_postInitialise()
	 */
	protected function _postInitialise() {
		/*
		 * Intercept cookies to see if we have a valid user, validate users id and email
		 */
		
		/*
		if (
			isset($_COOKIE[mofilmConstants::COOKIE_USER_ID]) && isset($_COOKIE[mofilmConstants::COOKIE_EMAIL_ADDRESS]) &&
			is_numeric($_COOKIE[mofilmConstants::COOKIE_USER_ID]) && $_COOKIE[mofilmConstants::COOKIE_USER_ID] > 0 &&
			is_string($_COOKIE[mofilmConstants::COOKIE_EMAIL_ADDRESS]) && strlen($_COOKIE[mofilmConstants::COOKIE_EMAIL_ADDRESS]) > 0
		) {
			try {
				$oUser = mofilmUserManager::getInstanceByID($_COOKIE[mofilmConstants::COOKIE_USER_ID]);
				if (
					$oUser instanceof mofilmUserBase && $oUser->getID() == $_COOKIE[mofilmConstants::COOKIE_USER_ID] &&
					$oUser->getEmail() == $_COOKIE[mofilmConstants::COOKIE_EMAIL_ADDRESS]
				) {
					$this->setParam(self::MOFILM_USER_ID, $oUser->getID());
					$this->setParam(self::MOFILM_LOGGED_IN, true);
				}
			} catch ( Exception $e ) {
			*/	
				/*
				 * DR 2010-10-04:
				 * We don't care if the above fails, it just means people don't get logged in
				 * automagically and it is better to fail than go: "ok, come on in".
				 */
		/*		
			}
		}
		*/
	}



	/**
	 * Returns the current user object
	 *
	 * @return mofilmUser
	 */
	function getUser() {
		if ( $this->_MofilmUser === false ) {
			$userID = $this->getParam(self::MOFILM_USER_ID);
			if ( $userID ) {
				$oUser = mofilmUserManager::getInstanceByID($userID);
				if ( is_object($oUser) && $oUser->getID() > 0 ) {
					$this->_MofilmUser = $oUser;
				}
			}
		}
		return $this->_MofilmUser;
	}

	/**
	 * Set the mofilm user, or if not set, removes the user
	 *
	 * @param mixed $inUser
	 * @return mvcSession
	 */
	function setUser($inUser = null) {
		if ( $inUser === null || ($inUser instanceof mofilmUser && $inUser->getID() == 0) ) {
			$this->setParam(self::MOFILM_USER_ID, false);
		} else {
			$userID = $inUser;
			if ( $inUser instanceof mofilmUser ) {
				$userID = $inUser->getID();
			}
			$this->setParam(self::MOFILM_USER_ID, $userID);
		}
		return $this;
	}

	/**
	 * Returns true if the current session is logged in
	 *
	 * @return boolean
	 */
	function isLoggedIn() {
		/*
		$return = true;
		if (
			!isset($_COOKIE[mofilmConstants::COOKIE_USER_ID]) || !isset($_COOKIE[mofilmConstants::COOKIE_EMAIL_ADDRESS]) ||
			!is_numeric($_COOKIE[mofilmConstants::COOKIE_USER_ID]) || strlen($_COOKIE[mofilmConstants::COOKIE_EMAIL_ADDRESS]) < 1
		) {
			$return = false;
		}
		
		if ( $this->getRequest()->isAjaxRequest() ) {
			$return = true;
		}
		*/
		return ($this->getParam(self::MOFILM_USER_ID) && $this->getParam(self::MOFILM_LOGGED_IN));
	}

	/**
	 * Sets the logged in status
	 *
	 * @param boolean $inStatus
	 * @return mvcSession
	 */
	function setLoggedIn($inStatus = false) {
		$this->setParam(self::MOFILM_LOGGED_IN, $inStatus);
		return $this;
	}
	
	/**
	 * Sets the logged in status
	 *
	 * @param boolean $inStatus
	 * @return mvcSession
	 */
	function setFBLoggedIn($inStatus = false) {
	    systemLog::message('setFBLoggedIn');
		$this->setParam(self::MOFILM_FB_LOGGED_IN, $inStatus);
		return $this;
	}
	
	/**
	 * Return true if the current session id logged in using facebook account
	 * 
	 * @return boolean
	 */
	
	function isFBLoggedIn() {
	    systemLog::message('isFBLoggedIn');
		return ($this->getParam(self::MOFILM_USER_ID) && $this->getParam(self::MOFILM_FB_LOGGED_IN));
	}
	
	/**
	 * Returns the login hash record
	 * 
	 * @return mofilmUserLoginHash
	 */
	/*
	function getLoginHash() {
		if ( !$this->_MofilmUserLoginHash instanceof mofilmUserLoginHash ) {
			if ( isset($_COOKIE[mofilmConstants::COOKIE_LOGIN_HASH]) && strlen($_COOKIE[mofilmConstants::COOKIE_LOGIN_HASH]) > 0 ) {
				$this->_MofilmUserLoginHash = mofilmUserLoginHash::getInstance($_COOKIE[mofilmConstants::COOKIE_LOGIN_HASH]);
			} else {
				$this->_MofilmUserLoginHash = mofilmUserLoginHash::factoryFromUser($this->getUser());
			}
		}
		return $this->_MofilmUserLoginHash;
	}
	*/



	/**
	 * Returns the current set status message, removing it once retrieved
	 *
	 * @return array(message, level)
	 * @access public
	 */
	function getStatusMessage() {
		$msg = $this->getParam(self::MOFILM_MESSAGE);
		$level = $this->getParam(self::MOFILM_MESSAGE_LEVEL);

		$this->setParam(self::MOFILM_MESSAGE, false);
		$this->setParam(self::MOFILM_MESSAGE_LEVEL, false);
		return array('message' => $msg, 'level' => $level);
	}

	/**
	 * Sets a status message to be displayed on the next page load
	 *
	 * @param string $inStatusMessage
	 * @param integer $inLevel One of the constants MESSAGE_XX
	 * @return mvcSession
	 * @access public
	 */
	function setStatusMessage($inStatusMessage, $inLevel = self::MESSAGE_OK) {
		$this->setParam(self::MOFILM_MESSAGE, $inStatusMessage);
		$this->setParam(self::MOFILM_MESSAGE_LEVEL, $inLevel);
		return $this;
	}
}