<?php
/**
 * accountModel.class.php
 * 
 * accountModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category accountModel
 * @version $Rev: 336 $
 */


/**
 * accountModel class
 * 
 * Provides the "account" page
 * 
 * @package websites_mofilm.com
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
	 * Stores $_FacebookID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_FacebookID;

	/**
	 * Stores $_FacebookLoginStatus
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_FacebookLoginStatus;
	
	/**
	 * Stores $_FBAccessToken
	 * 
	 * @var string
	 * @access protected
	 */
	protected $_FBAccessToken;


	/**
	 * Stores $_FBLoginUrl
	 *
	 * @var string
	 * @access protected
	 */
	protected $_FBLoginUrl;
	
	/**
	 * Stores $_FBLogoutUrl
	 *
	 * @var string
	 * @access protected
	 */
	protected $_FBLogoutUrl;
	
	/**
	 * Stores $_City
	 *
	 * @var string
	 * @access protected
	 */
	protected $_City;
	
	/**
	 * Stores $_RegistrationSource
	 *
	 * @var string
	 * @access protected
	 */
	protected $_RegistrationSource;

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
		$this->_FacebookID = null;
		$this->_FBAccessToken = null;
		$this->_FormSessionToken = null;
		$this->_Redirect = null;
		$this->_FBLoginUrl = null;
		$this->_Message = null;
		$this->_User = null;
		$this->_Updated = false;
		$this->_Language = 'en';
		$this->_FacebookLoginStatus = false;
		$this->_City = null;
		$this->_RegistrationSource = null;
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
			systemLog::warning('Form session key mis-match - potential form spoofing from IP: '.$_SERVER['REMOTE_ADDR'].' (Ref: '.$_SERVER['HTTP_REFERER'].')');
			return false;
		}
		
		try {
			$oUser = mofilmUserManager::getInstanceByUserLogin($this->getUsername(), $this->getPassword());
		} catch ( mofilmException $e ) {
			systemLog::error($e->getMessage());
		}

		if ( isset($oUser) && $oUser instanceof mofilmUser && $oUser->getID() > 0 ) {
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
	 * Authenticates the user using facebook account
	 *
	 * @return boolean
	 */
	function authenticateFacebook() {
		if ( !$this->getRequest()->getSession()->isValidFormToken($this->getFormSessionToken()) ) {
			systemLog::warning('Form session key mis-match - potential form spoofing from IP: '.$_SERVER['REMOTE_ADDR'].' (Ref: '.$_SERVER['HTTP_REFERER'].')');
			return false;
		}
		
		try {
			$oUser = mofilmUserManager::getInstanceByFacebookLogin($this->getFacebookID());
			$this->setFacebookLoginStatus(true);
		} catch ( mofilmException $e ) {
			systemLog::error($e->getMessage());
		}

		if ( isset($oUser) && $oUser instanceof mofilmUser && $oUser->getID() > 0 ) {
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
	 * Looks up the visiting users country and returns a territory object
	 * 
	 * @return mofilmTerritory
	 */
	function getGeoLocatedCountry() {
		$isoCn = mofilmUtilities::getCountryFromIpAddress();
		if ( !$isoCn ) {
			$isoCn = 'GB';
		}
		return mofilmTerritory::getInstanceByShortName($isoCn);
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

			$this->sendEmail($pass, $oUser->getID());
			return true;
		}

		return false;
	}
	
	/**
	 * Resends the activation email to $inUsername
	 * 
	 * @param string $inUsername
	 * @return boolean
	 * @throws mofilmException
	 */
	function resendActivationEmail($inUsername) {
		try {
			$oUser = mofilmUserManager::getInstance()->setLoadOnlyActive(false)->getUserByUsername($inUsername);
			if ( !$oUser instanceof mofilmUserBase ) {
				throw new Exception();
			}
		} catch ( Exception $e ) {
			throw new mofilmException("Invalid email address ($inUsername)");
		}
		
		return $this->_sendActivationMail($oUser);
	}
	
	/**
	 * Creates a new mofilm user based on input data
	 * 
	 * @param array $inData
	 * @return boolean
	 * @throws mofilmException
	 */
	function registerUser(array $inData) {
		systemLog::message($inData);
		systemLog::message("remote".$_SERVER['REMOTE_ADDR']);		
		try {
			$tags = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.$_SERVER['REMOTE_ADDR']);
			$inData['City'] = $tags['city'];
			systemLog::message("============================="); 
			systemLog::message($tags['city']); 
		}
		catch(Exception $e) {
			systemLog::message("City from IP not accessible ");
		}
		
		$oUser = mofilmUserManager::getInstance()->setLoadOnlyActive(false)->getUserByUsername($inData['username']);
		if ( $oUser instanceof mofilmUserBase && $oUser->getID() > 0 ) {
			throw new mofilmException("An account already exists for ({$inData['username']})");
		}
		
		$oValidator = new utilityValidateEmailAddress();
		if ( !$oValidator->isValid($inData['username']) ) {
			throw new mofilmException(implode(' ', $oValidator->getMessages()));
		}
		
		if ( strtolower($inData['Firstname']) == 'undefined' ) {
			throw new mofilmException("Firstname cannot be empty.");
		}
		
		if ( strtolower($inData['Surname']) == 'undefined' ) {
			throw new mofilmException("Surname cannot be empty.");
		}
		
		if ( strlen($inData['Phone']) < 1 ) {
			throw new mofilmException("Phone Number cannot be empty.");
		}

		$this->checkPassword($inData['Password'], $inData['ConfirmPassword']);
		$this->checkUsername($inData['ProfileName']);
		
		$dob = false;
		if ( isset($inData['DateOfBirth']) && is_array($inData['DateOfBirth']) && count($inData['DateOfBirth']) == 3 ) {
			if ( checkdate($inData['DateOfBirth']['Month'], $inData['DateOfBirth']['Day'], $inData['DateOfBirth']['Year']) ) {
				$dob = $inData['DateOfBirth']['Year'].'-'.$inData['DateOfBirth']['Month'].'-'.$inData['DateOfBirth']['Day'];
				
				if ( $dob == date('Y-m-d') ) {
					$dob = false;
				}
			} else {
				throw new mofilmException('The date you entered is not valid, please re-enter it');
			}
		}
		systemLog::message("Registering user {$inData['username']}");
		
		$oUser = new mofilmUser();
		$oUser->setEmail($inData['username']);
		$oUser->setPassword($inData['Password']);
		$oUser->setClientID(0);
		$oUser->setEnabled(mofilmUser::ENABLED_N);
		$oUser->setHash(mofilmUtilities::buildMiniHash($inData, 10));
		$oUser->setTerritoryID($inData['territory']);
		$oUser->setFacebookID($inData['facebookID']);
		$oUser->setFirstname($inData['Firstname']);
		$oUser->setSurname($inData['Surname']);
		
		if ( isset($inData['Skills']) ) {
			$skills = implode(", ", $inData['Skills']);
			$oUser->getParamSet()->setParam(mofilmUser::PARAM_SKILLS, $skills);
		}
		
		$oUser->getParamSet()->setParam(mofilmUser::PARAM_PHONE, $inData['Phone']);
		$oUser->getParamSet()->setParam(mofilmUser::PARAM_DATE_OF_BIRTH, $dob);
		$oUser->getParamSet()->setParam(mofilmUser::PARAM_REGISTRATION_SOURCE, $inData['registrationSource']);
		
		if ( isset ($inData['SchoolName']) ) {
			$oUser->getParamSet()->setParam(mofilmUser::PARAM_SCHOOL_NAME, $inData['SchoolName']);
		}
		
		if ( $inData['City'] != "Limit Exceeded") {
			$oUser->getParamSet()->setParam(mofilmUser::PARAM_CITY, $inData['City']);
		}
		if ( isset($inData["referral"]) ) {
			
			$referredUser = mofilmUserManager::getInstanceByID($inData["referral"]);
			if ( $referredUser ) {
				$oUser->getParamSet()->setParam(mofilmUser::PARAM_REFERRED, $referredUser->getID());
			}
		}
		
		try {
			$oUser->getParamSet()->setParam(mofilmUser::PARAM_LAT, $tags["latitude"]);
			$oUser->getParamSet()->setParam(mofilmUser::PARAM_LONG, $tags["longitude"]);
		}
		catch(Exception $e) {
			systemLog::message("Latitude and longitude not there");
		}
		
		if ( isset($inData[mofilmUser::PARAM_SIGNUP_CODE]) && strlen($inData[mofilmUser::PARAM_SIGNUP_CODE]) > 0 ) {
			$oCode = mofilmUserSignupCode::getInstance($inData[mofilmUser::PARAM_SIGNUP_CODE]);
			if ( $oCode->getID() > 0 ) {
				$oUser->getParamSet()->setParam(mofilmUser::PARAM_SIGNUP_CODE, $inData[mofilmUser::PARAM_SIGNUP_CODE]);
			}
		}
		$oUser->save();

		$this->setUser($oUser);
		
		$oProfile = new mofilmUserProfile();
		$oProfile->setUserID($oUser->getID());
		$oProfile->setProfileName($inData['ProfileName']);
		$oProfile->setActive(mofilmUserProfile::PROFILE_DISABLED);
		$oProfile->save();
                
                if($inData['Affiliate'] != ''){
                    $affUser = new mofilmUserAffiliate();
                    $affUser->setUserID($oUser->getID());
                    $affUser->setAffiliate($inData['Affiliate']);
                    $affUser->save();
                }
			
		if ( isset($inData['optIn']) && $inData['optIn'] && $oUser->getID() > 0 ) {

			systemLog::message("Registering user {$oUser->getEmail()} for email news");
			try {
				$oCommsEmail = mofilmCommsEmail::factoryFromUser($oUser);
				$oCommsEmail->save();
				
				$oCommsSub = mofilmCommsSubscription::factoryFromCommsEmail($oCommsEmail, mofilmCommsListType::T_MOFILM_NEWS);
				$oCommsSub->save();
			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
			}
		}
		
		//if ( $inData['territory'] == 45) { 
		//	return $this->_sendcnActivationMail($oUser); 
		//} else { 
		//	return $this->_sendActivationMail($oUser); 
		//}		
		// return $this->_sendActivationMail($oUser);
		return TRUE;
	}
	

	/**
	 * Creates a new mofilm user based on input data for the new event based branding page 
	 * 
	 * @param array $inData
	 * @return boolean
	 * @throws mofilmException
	 */
	function registercnEventUser(array $inData) {
		$oUser = mofilmUserManager::getInstance()->setLoadOnlyActive(false)->getUserByUsername($inData['username']);
		if ( $oUser instanceof mofilmUserBase && $oUser->getID() > 0 ) {
			throw new mofilmException("An account already exists for ({$inData['username']})");
		}
		
		$oValidator = new utilityValidateEmailAddress();
		if ( !$oValidator->isValid($inData['username']) ) {
			throw new mofilmException(implode(' ', $oValidator->getMessages()));
		}
		
		if ( strtolower($inData['Firstname']) == 'undefined' ) {
			throw new mofilmException("Firstname cannot be empty.");
		}
		
		if ( strtolower($inData['Surname']) == 'undefined' ) {
			throw new mofilmException("Surname cannot be empty.");
		}
		
		$this->checkPassword($inData['Password'], $inData['ConfirmPassword']);
		$this->checkUsername($inData['ProfileName']);
		
		$dob = false;
		if ( isset($inData['DateOfBirth']) && is_array($inData['DateOfBirth']) && count($inData['DateOfBirth']) == 3 ) {
			if ( checkdate($inData['DateOfBirth']['Month'], $inData['DateOfBirth']['Day'], $inData['DateOfBirth']['Year']) ) {
				$dob = $inData['DateOfBirth']['Year'].'-'.$inData['DateOfBirth']['Month'].'-'.$inData['DateOfBirth']['Day'];
				
				if ( $dob == date('Y-m-d') ) {
					$dob = false;
				}
			} else {
				throw new mofilmException('The date you entered is not valid, please re-enter it');
			}
		}
		systemLog::message("Registering user {$inData['username']}");
		
		$oUser = new mofilmUser();
		$oUser->setEmail($inData['username']);
		$oUser->setPassword($inData['Password']);
		$oUser->setClientID(0);
		$oUser->setEnabled(mofilmUser::ENABLED_N);
		$oUser->setHash(mofilmUtilities::buildMiniHash($inData, 10));
		$oUser->setTerritoryID($inData['territory']);
		$oUser->setFacebookID($inData['facebookID']);
		$oUser->setFirstname($inData['Firstname']);
		$oUser->setSurname($inData['Surname']);
		$oUser->getParamSet()->setParam(mofilmUser::PARAM_DATE_OF_BIRTH, $dob);
		$oUser->getParamSet()->setParam(mofilmUser::PARAM_REGISTRATION_SOURCE, $inData['registrationSource']);
		$oUser->getParamSet()->setParam(mofilmUser::PARAM_CITY, $inData['City']);
		if ( isset($inData[mofilmUser::PARAM_SIGNUP_CODE]) && strlen($inData[mofilmUser::PARAM_SIGNUP_CODE]) > 0 ) {
			$oCode = mofilmUserSignupCode::getInstance($inData[mofilmUser::PARAM_SIGNUP_CODE]);
			if ( $oCode->getID() > 0 ) {
				$oUser->getParamSet()->setParam(mofilmUser::PARAM_SIGNUP_CODE, $inData[mofilmUser::PARAM_SIGNUP_CODE]);
			}
		}
		$oUser->save();

		$oProfile = new mofilmUserProfile();
		$oProfile->setUserID($oUser->getID());
		$oProfile->setProfileName($inData['ProfileName']);
		$oProfile->setActive(mofilmUserProfile::PROFILE_DISABLED);
		$oProfile->save();

		if ( isset($inData['optIn']) && $inData['optIn'] && $oUser->getID() > 0 ) {

			systemLog::message("Registering user {$oUser->getEmail()} for email news");
			try {
				$oCommsEmail = mofilmCommsEmail::factoryFromUser($oUser);
				$oCommsEmail->save();
				
				$oCommsSub = mofilmCommsSubscription::factoryFromCommsEmail($oCommsEmail, mofilmCommsListType::T_MOFILM_NEWS);
				$oCommsSub->save();
			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
			}
		}
		
		return $this->_sendcnEventActivationMail($oUser,$inData["campaignID"]); 
	}
	
	
	
	/**
	 * Activates a user account
	 * 
	 * @return void
	 */
	function activateUser() {
		if ( $this->getUser() instanceof mofilmUserBase ) {
			systemLog::message("Activating user {$this->getUser()->getEmail()}");
			$this->getUser()->setRegIP($_SERVER['REMOTE_ADDR']);
			$this->getUser()->setRegistered(date(system::getConfig()->getDatabaseDatetimeFormat()));
			$this->getUser()->setEnabled(mofilmUserBase::ENABLED_Y);
			$this->getUser()->save();
			
			$oProfile = mofilmUserProfile::getMostRecentUserProfile($this->getUser()->getID());
			if ( $oProfile && $oProfile->getID() > 0 ) {
				$oProfile->setActive(mofilmUserProfile::PROFILE_ACTIVE);
				$oProfile->save();
			}
			
			if ( $this->getUser()->hasPassword() ) {
				$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
					0, mofilmMessages::MSG_GRP_CLIENT_WELCOME, $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue()
				);
				
				commsOutboundManager::setCustomerInMessageStack($oQueue, $this->getUser()->getID());
				commsOutboundManager::setRecipientInMessageStack($oQueue, $this->getUser()->getEmail());
				commsOutboundManager::replaceDataInMessageStack($oQueue, array('%emailAddr%'), array($this->getUser()->getEmail()));
				
				$oQueue->send();
			}
		}
		return TRUE;
	}
	
	/**
	 * Ensures that a password is between bounds
	 * 
	 * @param string $inPassword
	 * @param string $inConfirmedPassword
	 * @return boolean
	 * @throws mofilmException
	 */
	function checkPassword($inPassword, $inConfirmedPassword) {
		if ( $inPassword != $inConfirmedPassword ) {
			throw new mofilmException("Please confirm your password by re-entering it in the Confirm Password box");
		}
		if ( strlen($inPassword) < 6 ) {
			throw new mofilmException("Your password must be at least 6 characters");
		}
		return true;
	}
	
	/**
	 * Ensures that username is not in use
	 * 
	 * @param string $inProfileName
	 * @return boolean
	 * @throws mofilmException
	 */
	function checkUsername($inProfileName) {
		if ( isset($inProfileName) && strlen($inProfileName) > 0 ) {
			$oProfileCheck = mofilmUserProfile::getInstanceByProfileName($inProfileName);
			if ( $oProfileCheck instanceof mofilmUserProfile && $oProfileCheck->getID() > 0 ) {
				if ( $oProfileCheck->getProfileName() == $inProfileName ) {
					throw new mofilmException("The Username ({$inProfileName}) is already in use by another user");
				}
			}
		}
		return true;
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
	private function sendEmail($inNewPass, $inUserID = 0) {
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_CLIENT_PASSWORD_RESET, $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue()
		);
		
		commsOutboundManager::setCustomerInMessageStack($oQueue, $inUserID);
		commsOutboundManager::setRecipientInMessageStack($oQueue, $this->getUsername());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%user.password%'), array($inNewPass));
		
		return $oQueue->send();
	}

	/**
	 * Sends the activation message to $inUser
	 * 
	 * @param mofilmUserBase $inUser
	 * @return booleran
	 * @throws mofilmException
	 */
	private function _sendActivationMail(mofilmUserBase $inUser) {
		if ( $inUser->isEnabled() ) {
			throw new mofilmException("The account for ({$inUser->getEmail()}) is already active.");
		}
		
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_CLIENT_REGISTRATION, $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue()
		);
		
		if ( $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue() == "zh") {
			//$regUri = "http://my.mofilm.cn/account/register/";
			$regUri = "http://".$this->getRequest()->getServerName()."/account/register/";
			
		} else {
			$regUri = "http://".$this->getRequest()->getServerName()."/account/register/";
			//$regUri = mofilmConstants::getRegistrationUri();
		}
		
		commsOutboundManager::setCustomerInMessageStack($oQueue, $inUser->getID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $inUser->getEmail());
		commsOutboundManager::replaceDataInMessageStack(
			$oQueue,
			array('%regUrl%', '%emailAddr%'),
			array(
				$regUri.$inUser->getHash(),
				$inUser->getEmail(),
			)
		);
		
		return $oQueue->send();
	}
	
	/**
	 * Sends the activation message to $inUser
	 * 
	 * @param mofilmUserBase $inUser
	 * @return booleran
	 * @throws mofilmException
	 */
	private function _sendcnActivationMail(mofilmUserBase $inUser) {
		if ( $inUser->isEnabled() ) {
			throw new mofilmException("The account for ({$inUser->getEmail()}) is already active.");
		}
		
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_CLIENT_REGISTRATIONCN, $this->getLanguage()
		);
		
		commsOutboundManager::setCustomerInMessageStack($oQueue, $inUser->getID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $inUser->getEmail());
		commsOutboundManager::replaceDataInMessageStack(
			$oQueue,
			array('%regUrl%', '%emailAddr%'),
			array(
				mofilmConstants::getCNRegistrationUri().$inUser->getHash(),
				$inUser->getEmail(),
			)
		);
		
		return $oQueue->send();
	}

	
	/**
	 * Sends the activation message to $inUser
	 * 
	 * @param mofilmUserBase $inUser
	 * @return booleran
	 * @throws mofilmException
	 */
	private function _sendcnEventActivationMail(mofilmUserBase $inUser,$inEventID) {
		if ( $inUser->isEnabled() ) {
			throw new mofilmException("The account for ({$inUser->getEmail()}) is already active.");
		}
		
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_CLIENT_REGISTRATIONCN, $this->getLanguage()
		);
		
		commsOutboundManager::setCustomerInMessageStack($oQueue, $inUser->getID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $inUser->getEmail());
		commsOutboundManager::replaceDataInMessageStack(
			$oQueue,
			array('%regUrl%', '%emailAddr%'),
			array(
				system::getConfig()->getParam('mofilm', 'myMofilmUri').'/account/eventRegistercn/'.$inUser->getHash()."/".$inEventID,
				$inUser->getEmail(),
			)
		);
		
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
	 * Returns $_FBAccessToken
	 *
	 * @return string
	 */
	function getFBAccessToken() {
		return $this->_FBAccessToken;
	}
	
	/**
	 * Set $_FBAccessToken to $inFBAccessToken
	 *
	 * @param string $inFBAccessToken
	 * @return accountModel
	 */
	function setFBAccessToken($inFBAccessToken) {
		if ( $inFBAccessToken !== $this->_FBAccessToken ) {
			$this->_FBAccessToken = $inFBAccessToken;
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
	 * Return value of $_FBLoginUrl
	 *
	 * @return string
	 * @access public
	 */
	function getFBLoginUrl() {
		return $this->_FBLoginUrl;
	}

	/**
	 * Set $_FBLoginUrl to $inFBLoginUrl
	 *
	 * @param string $inFBLoginUrl
	 * @return accountModel
	 * @access public
	 */
	function setFBLoginUrl($inFBLoginUrl) {
		if ( $inFBLoginUrl !== $this->_FBLoginUrl ) {
			$this->_FBLoginUrl = $inFBLoginUrl;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_FBLogoutUrl
	 *
	 * @return string
	 * @access public
	 */
	function getFBLogoutUrl() {
		return $this->_FBLogoutUrl;
	}

	/**
	 * Set $_FBLogoutUrl to $inFBLogoutUrl
	 *
	 * @param string $inFBLogoutUrl
	 * @return accountModel
	 * @access public
	 */
	function setFBLogoutUrl($inFBLogoutUrl) {
		if ( $inFBLogoutUrl !== $this->_FBLogoutUrl ) {
			$this->_FBLogoutUrl = $inFBLogoutUrl;
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
	
	/**
	 * Return value of $_FacebookID
	 *
	 * @return integer
	 * @access public
	 */
	function getFacebookID() {
		return $this->_FacebookID;
	}

	/**
	 * Set $_FacebookID to FacebookID
	 *
	 * @param integer $inFacebookID
	 * @return accountModel
	 * @access public
	 */
	function setFacebookID($inFacebookID) {
		if ( $inFacebookID !== $this->_FacebookID ) {
			$this->_FacebookID = $inFacebookID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_FacebookLoginStatus
	 *
	 * @return bool
	 * @access public
	 */
	function getFacebookLoginStatus() {
		return $this->_FacebookLoginStatus;
	}

	/**
	 * Set $_FacebookLoginStatus to FacebookLoginStatus
	 *
	 * @param integer $inFacebookLoginStatus
	 * @return accountModel
	 * @access public
	 */
	function setFacebookLoginStatus($inFacebookLoginStatus) {
		if ( $inFacebookLoginStatus !== $this->_FacebookLoginStatus ) {
			$this->_FacebookLoginStatus = $inFacebookLoginStatus;
		}
		return $this;
	}
	
	/**
	 * Return value of $_City
	 *
	 * @return string
	 * @access public
	 */
	function getCity() {
		return $this->_City;
	}

	/**
	 * Set $_City to City
	 *
	 * @param string $inCity
	 * @return accountModel
	 * @access public
	 */
	function setCity($inCity) {
		if ( $inCity !== $this->_City ) {
			$this->_City = $inCity;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_RegistrationSource
	 *
	 * @return string
	 * @access public
	 */
	function getRegistrationSource() {
		return $this->_RegistrationSource1;
	}

	/**
	 * Set $_RegistrationSource to RegistrationSource
	 *
	 * @param string $inRegistrationSource
	 * @return accountModel
	 * @access public
	 */
	function setRegistrationSource($inRegistrationSource) {
		if ( $inRegistrationSource !== $this->_RegistrationSource ) {
			$this->_RegistrationSource = $inRegistrationSource;
			$this->setModified();
			systemLog::message($inRegistrationSource);
			systemLog::message($this->_RegistrationSource);
		}
		return $this;
	}	
	
	/**
	 * Sends the Referral message to new filmmaker
	 * 
	 * @param mofilmUserBase $inUser
	 * @return booleran
	 * @throws mofilmException
	 */
	function sendReferral($inRefer, $inUser) {
		
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_REFERRAL, $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue()
		);
		
			$regUri = "http://".$this->getRequest()->getServerName()."/account/register?referral=".$inUser->getID();
		
		commsOutboundManager::setCustomerInMessageStack($oQueue, $inUser->getID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $inRefer);
		commsOutboundManager::replaceDataInMessageStack(
			$oQueue,
			array('%mofilm.userhash%', '%mofilm.user%'),
			array(
				$regUri,
				$inUser->getFullname()
			)
		);

		return $oQueue->send();
	}
	
	/**
	 * Sends the Chinese Referral message to new filmmaker
	 * 
	 * @param mofilmUserBase $inUser
	 * @return booleran
	 * @throws mofilmException
	 */
	function sendReferralCn($inRefer, $inUser) {
		
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_REFERRAL_CN, $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue()
		);
		
			$regUri = "http://".$this->getRequest()->getServerName()."/account/register?referral=".$inUser->getID();

		commsOutboundManager::setCustomerInMessageStack($oQueue, $inUser->getID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $inRefer);
		commsOutboundManager::replaceDataInMessageStack(
			$oQueue,
			array('%mofilm.userhash%', '%mofilm.user%'),
			array(
				$regUri,
				$inUser->getFullname()
			)
		);

		return $oQueue->send();
	}
	
}