<?php
/**
 * accountModel.class.php
 *
 * accountModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category accountModel
 * @version $Rev: 100 $
 */


/**
 * accountModel class
 *
 * Provides the "account" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category accountModel
 */
class accountModel extends mvcModelBase {

	/**
	 * Stores $_Request
	 *
	 * @var mvcRequest
	 * @access protected
	 */
	protected $_Request;

	/**
	 * Stores $_Username
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Username;

	/**
	 * Stores $_Password
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Password;

	/**
	 * Stores $_FormSessionToken
	 *
	 * @var string
	 * @access protected
	 */
	protected $_FormSessionToken;

	/**
	 * Stores $_Redirect
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Redirect;

	/**
	 * Stores $_Message
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Message;

	/**
	 * Stores $_User
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_User;

	/**
	 * Stores $_Updated
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Updated;

	/**
	 * Stores $_Language
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Language;


	/**
	 * Creates a new account model object
	 */
	function __construct() {
		$this->reset();
	}

	/**
	 * Resets the model
	 *
	 * @return void
	 */
	function reset() {
		$this->_Request = null;
		$this->_Username = null;
		$this->_Password = null;
		$this->_FormSessionToken = null;
		$this->_Redirect = null;
		$this->_Message = null;
		$this->_User = null;
		$this->_Updated = false;
		$this->_Language = 'en';
		$this->setModified(false);
	}

	/**
	 * Authenticates the user
	 *
	 * @return boolean
	 */
	function authenticate() {
		systemLog::getInstance()->getSource()->setSource('Username', $this->getUsername());

		if ( !$this->getRequest()->getSession()->isValidFormToken($this->getFormSessionToken()) ) {
			systemLog::warning('Form session key mis-match - potential form spoofing from IP: ' . $_SERVER['REMOTE_ADDR'] . ' (Ref: ' . $_SERVER['HTTP_REFERER'] . ')');
			return false;
		}

		try {
			$oUser = mofilmUserManager::getInstanceByUserLogin($this->getUsername(), $this->getPassword());
		} catch ( mofilmException $e ) {
			systemLog::error($e->getMessage());
		}

		if ( isset($oUser) && $oUser instanceof mofilmUser && $oUser->getID() > 0 && $oUser->isAuthorised('admin.canLogin') ) {
			$oLog = new mofilmUserLog();
			$oLog->setType(mofilmUserLog::TYPE_LOGIN);

			$oUser->getLogSet()->setObject($oLog);
			$oUser->save();

			$this->setUser($oUser);
			return true;
		}

		return false;
	}

	/**
	 * Resets a users password and emails it to them
	 *
	 * @return boolean
	 */
	function resetPassword() {
		systemLog::getInstance()->getSource()->setSource('Username', $this->getUsername());

		try {
			$oUser = mofilmUserManager::getInstanceByUsername($this->getUsername());
		} catch ( mofilmException $e ) {
			systemLog::error($e->getMessage());
		}

		if ( isset($oUser) && $oUser instanceof mofilmUser && $oUser->getID() > 0 ) {
			$oLog = new mofilmUserLog();
			$oLog->setType(mofilmUserLog::TYPE_OTHER);
			$oLog->setDescription('User requested password reset');

			$oUser->getLogSet()->setObject($oLog);

			$pass = $this->generateNewPassword();

			$oUser->setPassword($pass);
			$oUser->save();

			$this->_sendPasswordResetEmail($pass, $oUser->getID());
			return true;
		}

		return false;
	}

	/**
	 * Updates the user profile
	 *
	 * @return boolean
	 */
	function update() {
		if ( $this->getUser()->isModified() ) {
			try {
				$res = $this->getUser()->save();
				if ( $res ) {
					$this->setMessage('Profile updated successfully');
					$this->setUpdated(true);
				} else {
					$this->setMessage('Profile update failed');
				}
			} catch ( Exception $e ) {
				$this->setMessage('Profile update failed: ' . $e->getMessage());
			}
		} else {
			$this->setMessage('No updates to make to profile');
			$this->setUpdated(0);
		}
	}

	/**
	 * Generates a new random password
	 *
	 * Vowels and similar letters / numbers have been removed.
	 *
	 * @return string
	 */
	private function generateNewPassword() {
		return mofilmUtilities::generateRandomString(8);
	}

	/**
	 * Sends the valid user their new password
	 *
	 * @return mixed
	 * @throws Exception
	 */
	private function _sendPasswordResetEmail($inNewPass, $inUserID = 0) {
		$oUser = mofilmUserManager::getInstanceByID($inUserID);
		$inLanguage= $oUser->getParamSet()->getParam("Language");
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_CLIENT_PASSWORD_RESET, $inLanguage
		);

		commsOutboundManager::setCustomerInMessageStack($oQueue, $inUserID);
		commsOutboundManager::setRecipientInMessageStack($oQueue, $this->getUsername());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%user.password%'), array($inNewPass));

		return $oQueue->send();
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
	 * @return accountModel
	 */
	function setRequest($inRequest) {
		if ( $inRequest !== $this->_Request ) {
			$this->_Request = $inRequest;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Username
	 *
	 * @return string
	 */
	function getUsername() {
		return $this->_Username;
	}

	/**
	 * Set $_Username to $inUsername
	 *
	 * @param string $inUsername
	 * @return accountModel
	 */
	function setUsername($inUsername) {
		if ( $inUsername !== $this->_Username ) {
			$this->_Username = $inUsername;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Password
	 *
	 * @return string
	 */
	function getPassword() {
		return $this->_Password;
	}

	/**
	 * Set $_Password to $inPassword
	 *
	 * @param string $inPassword
	 * @return accountModel
	 */
	function setPassword($inPassword) {
		if ( $inPassword !== $this->_Password ) {
			$this->_Password = $inPassword;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_FormSessionToken
	 *
	 * @return string
	 */
	function getFormSessionToken() {
		return $this->_FormSessionToken;
	}

	/**
	 * Set $_FormSessionToken to $inFormSessionToken
	 *
	 * @param string $inFormSessionToken
	 * @return accountModel
	 */
	function setFormSessionToken($inFormSessionToken) {
		if ( $inFormSessionToken !== $this->_FormSessionToken ) {
			$this->_FormSessionToken = $inFormSessionToken;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Redirect
	 *
	 * @return string
	 * @access public
	 */
	function getRedirect() {
		return $this->_Redirect;
	}

	/**
	 * Set $_Redirect to $inRedirect
	 *
	 * @param string $inRedirect
	 * @return accountModel
	 * @access public
	 */
	function setRedirect($inRedirect) {
		if ( $inRedirect !== $this->_Redirect ) {
			$this->_Redirect = $inRedirect;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Message
	 *
	 * @return string
	 */
	function getMessage() {
		return $this->_Message;
	}

	/**
	 * Set $_Message to $inMessage
	 *
	 * @param string $inMessage
	 * @return accountModel
	 */
	function setMessage($inMessage) {
		if ( $inMessage !== $this->_Message ) {
			$this->_Message = $inMessage;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_User
	 *
	 * @return mofilmUser
	 */
	function getUser() {
		return $this->_User;
	}

	/**
	 * Set $_User to $inUser
	 *
	 * @param mofilmUser $inUser
	 * @return accountModel
	 */
	function setUser(mofilmUser $inUser) {
		if ( $inUser !== $this->_User ) {
			$this->_User = $inUser;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Updated
	 *
	 * @return boolean
	 * @access public
	 */
	function isUpdated() {
		return $this->_Updated;
	}

	/**
	 * Set $_Updated to $inUpdated
	 *
	 * @param boolean $inUpdated
	 * @return accountModel
	 * @access public
	 */
	function setUpdated($inUpdated) {
		if ( $inUpdated !== $this->_Updated ) {
			$this->_Updated = $inUpdated;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Language
	 *
	 * @return string
	 */
	function getLanguage() {
		return $this->_Language;
	}

	/**
	 * Set $_Language to $inLanguage
	 *
	 * @param string $inLanguage
	 * @return accountModel
	 */
	function setLanguage($inLanguage) {
		if ( $inLanguage !== $this->_Language ) {
			$this->_Language = $inLanguage;
			$this->setModified();
		}
		return $this;
	}
}