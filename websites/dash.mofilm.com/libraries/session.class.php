<?php
/**
 * mvcSession.class.php
 *
 * mvcSession class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package baseAdminSite
 * @subpackage websites_dash.mofilm.com_libraries
 * @category mvcSession
 * @version $Rev: 11 $
 */


/**
 * mvcSession
 *
 * Main site mvcSession implementation, holds base directives and defaults for the site
 *
 * @package baseAdminSite
 * @subpackage websites_dash.mofilm.com_libraries
 * @category mvcSession
 */
class mvcSession extends mvcSessionBase {
	
	const SESSION_NAME = 'MOFILM_MADM_Session';
	
	const MOFILM_USER_ID = 'mofilm.userID';
	const MOFILM_LOGGED_IN = 'mofilm.connected';
	const MOFILM_MESSAGE = 'mofilm.message';
	const MOFILM_MESSAGE_LEVEL = 'mofilm.message.level';
	const MOFILM_LOGIN_HASH = 'mofilmIdentity';

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
		 * Intercept cookies to see if we have a valid user
		 */
		if (
			isset($_COOKIE[self::MOFILM_LOGIN_HASH]) && is_string($_COOKIE[self::MOFILM_LOGIN_HASH]) && strlen($_COOKIE[self::MOFILM_LOGIN_HASH]) > 0
		) {
			try {
				$key = file_get_contents(system::getConfig()->getPathData().'/dash.session.key');
				$details = utilityEncrypt::factory($key)->decrypt(utilityEncrypt::fromUriString($_COOKIE[self::MOFILM_LOGIN_HASH]));
				unset($key);

				if ( strlen($details) > 0 ) {
					$details = unserialize($details);
				}
				
				if ( is_array($details) ) {
					if ( $details['expiry'] > time() ) {
						$oUser = mofilmUserManager::getInstanceByID($details['id']);
						if ( $oUser instanceof mofilmUserBase && $oUser->getID() ) {
							$this->setParam(self::MOFILM_USER_ID, $oUser->getID());
							$this->setParam(self::MOFILM_LOGGED_IN, true);
						}
					}
				}
			} catch ( Exception $e ) {
				/*
				 * DR 2010-10-04:
				 * We don't care if the above fails, it just means people don't get logged in
				 * automagically and it is better to fail than go: "ok, come on in".
				 */
			}
		}
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
				if ( $oUser->getID() > 0 ) {
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