<?php
/**
 * accountController
 *
 * Stored in accountController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category accountController
 * @version $Rev: 336 $
 */


/**
 * accountController
 *
 * accountController class
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category accountController
 */
class accountController extends mvcController {

	const ACTION_AUTHORISE = 'authorise';
	const ACTION_NOT_AUTHORISED = 'notAuthorised';
	const ACTION_AUTHORISED = 'authorised';
	
	const ACTION_LOGIN = 'login';
	const ACTION_LOGINCN = 'logincn';
	const ACTION_LOGOUT = 'logout';
	const ACTION_LOGGED_IN = 'loggedIn';
	const ACTION_DO_LOGIN = 'doLogin';
	const ACTION_DO_LOGOUT = 'doLogout';

	const ACTION_FORGOT_PASSWORD = 'forgotpw';
	const ACTION_FORGOT_PASSWORDCN = 'forgotpwcn';
	const ACTION_DO_FORGOT_PASSWORD = 'doForgotpw';
	
	const ACTION_REGISTER = 'register';
	const ACTION_REGISTERCN = 'registercn';
	
	const ACTION_EVENT_BASED_REGISTER = 'eventRegistercn';
	const ACTION_DO_EVENTREGISTERCN = 'docnEventRegister';
	
	const ACTION_DO_REGISTER = 'doRegister';
	const ACTION_DO_REGISTERCN = 'docnRegister';
	const ACTION_REGISTER_DONE = 'registerDone';
	const ACTION_REGISTERCN_DONE = 'registercnDone';
	
	const ACTION_ACTIVATION = 'activation';
	const ACTION_ACTIVATIONCN = 'activationcn';
	const ACTION_REFERRAL = "reward";
	const ACTION_DO_REFERRAL = "doReward";
	const ACTION_REFER_DONE = "thankyou";
	const ACTION_REFER_TERMS = "referralTerms";
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_LOGIN);
		$this->setRequiresAuthentication(false);
		$this
			->getControllerActions()
				->addAction(self::ACTION_AUTHORISE)
				->addAction(self::ACTION_LOGIN)
				->addAction(self::ACTION_LOGINCN)	
				->addAction(self::ACTION_DO_FORGOT_PASSWORD)
				->addAction(self::ACTION_DO_LOGIN)
				->addAction(self::ACTION_DO_LOGOUT)
				->addAction(self::ACTION_FORGOT_PASSWORD)
				->addAction(self::ACTION_FORGOT_PASSWORDCN)	
				->addAction(self::ACTION_LOGOUT)
				->addAction(self::ACTION_ACTIVATION)
				->addAction(self::ACTION_ACTIVATIONCN)	
				->addAction(self::ACTION_REGISTER)
				->addAction(self::ACTION_REGISTERCN)	
				->addAction(self::ACTION_DO_REGISTER)
				->addAction(self::ACTION_DO_REGISTERCN)
				->addAction(self::ACTION_EVENT_BASED_REGISTER)
				->addAction(self::ACTION_DO_EVENTREGISTERCN)	
				->addAction(self::ACTION_REGISTER_DONE)
				->addAction(self::ACTION_REFERRAL)
				->addAction(self::ACTION_DO_REFERRAL)	
				->addAction(self::ACTION_REFER_DONE)	
				->addAction(self::ACTION_REFER_TERMS)	
				->addAction(self::ACTION_REGISTERCN_DONE);

		$this->addInputFilters();
		
		/*
		 * Fetch profileController for constants
		 */
		$this->getRequest()
			->getDistributor()
				->includeControllerFile('profileController.class.php', '/account');
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_AUTHORISE:			$this->authoriseAction(); break;
			case self::ACTION_NOT_AUTHORISED:		$this->notAuthorisedAction(); break;
			case self::ACTION_DO_LOGIN:				$this->doLoginAction(); break;
			case self::ACTION_LOGOUT:				$this->logoutAction(); break;
			case self::ACTION_REFERRAL:             $this->referralAction(); break;
			case self::ACTION_DO_REFERRAL:          $this->doReferralAction(); break;
			case self::ACTION_REFER_DONE:           $this->referralDoneAction(); break;
			case self::ACTION_REFER_TERMS:          $this->referralTermsAction(); break;
			case self::ACTION_DO_LOGOUT:			$this->doLogoutAction(); break;
			case self::ACTION_FORGOT_PASSWORD:		$this->forgotPasswordAction(); break;
			case self::ACTION_FORGOT_PASSWORDCN:	$this->forgotPasswordAction(); break;
			case self::ACTION_DO_FORGOT_PASSWORD:	$this->doForgotPasswordAction(); break;
			
			case self::ACTION_ACTIVATION:			$this->activationAction(); break;
			case self::ACTION_ACTIVATIONCN:			$this->activationcnAction(); break;
			case self::ACTION_REGISTER:				$this->registerAction(); break;
			case self::ACTION_LOGINCN:              $this->logincnAction(); break;
			case self::ACTION_REGISTERCN:			$this->registercnAction(); break;
			case self::ACTION_EVENT_BASED_REGISTER: $this->eventRegistercnAction(); break;
			case self::ACTION_DO_EVENTREGISTERCN:   $this->doEventRegistercnAction(); break;
			case self::ACTION_DO_REGISTER:			$this->doRegisterAction(); break;
			case self::ACTION_DO_REGISTERCN:		$this->doRegistercnAction(); break;
			case self::ACTION_REGISTER_DONE:		$this->registerDoneAction(); break;
			case self::ACTION_REGISTERCN_DONE:		$this->registerDoneAction(); break;
			
			case self::ACTION_LOGGED_IN:
			case self::ACTION_AUTHORISED:
				$this->loggedInAction();
			break;
			
			default:
				$this->loginAction();
			break;
		}
	}
	
	/**
	 * Handles authorisation requests
	 * 
	 * @return void
	 */
	protected function authoriseAction() {
		$oSession = $this->getRequest()->getSession();
		if ( !$oSession->isLoggedIn() ) {
			$this->setAction(self::ACTION_LOGIN);
			$this->getModel()->setMessage("You must login before you can view the resource");

			$oSession->setStatusMessage('You must login before you can view the resource', mvcSession::MESSAGE_ERROR);
			$this->loginAction();
			
		} else {
			$oLog = new mofilmUserLog();
			$oLog->setType(mofilmUserLog::TYPE_OTHER);
			$oLog->setDescription('User attempted to access restricted resource: '.$this->getRequest()->getRequestUri());

			$oSession->getUser()->getLogSet()->setObject($oLog);
			$oSession->getUser()->save();

			$this->getModel()->setMessage("Sorry, but you do not have permission to access the resource");
			$oSession->setStatusMessage('Sorry, but you do not have permission to access the resource', mvcSession::MESSAGE_WARNING);

			systemLog::warning(
				$oSession->getUser()->getUsername().' attempted to access a resource ('.
				$this->getRequest()->getRequestUri().') they do not have permissions for'
			);

			$this->setAction(self::ACTION_NOT_AUTHORISED);
			$this->notAuthorisedAction();
		}
	}

	/**
	 * Shows the not authorised page
	 * 
	 * @return void
	 */
	protected function notAuthorisedAction() {
		$oView = new accountView($this);
		$oView->showNotAuthorisedPage();
	}

	/**
	 * Shows the forgotten password form
	 * 
	 * @return void
	 */
	protected function forgotPasswordAction() {
		$oView = new accountView($this);
		$oView->showLostPasswordPage();
	}
	
	/**
	 * Handles requests to reset passwords
	 * 
	 * @return void
	 */
	protected function doForgotPasswordAction() {
		$oSession = $this->getRequest()->getSession();
		$this->addInputToModel($this->getInputManager()->doFilter(), $this->getModel());
		if ( $this->getModel()->resetPassword() ) {
			$oSession->setStatusMessage('Your password has been reset and sent to your email address', mvcSession::MESSAGE_OK);
			$this->redirect($this->buildUriPath(self::ACTION_LOGIN));
		} else {
			$oSession->setStatusMessage('Sorry, but you entered an invalid or incorrect email address', mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_LOGIN));
		}
	}

	/**
	 * Shows the login page
	 * 
	 * @return void
	 */
	protected function loginAction() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
		
		//if ( apc_fetch($_COOKIE["MOFILM_Session"]) ) {	
		//	$val = "/music/solrSearch?".apc_fetch($_COOKIE["MOFILM_Session"]);
		//	$this->getModel()->setRedirect($val);
		//} else
                 if ( isset($data['redirect']) ) {
			$this->getModel()->setRedirect($data['redirect']);
		}

		$oSession = $this->getRequest()->getSession();
		if ( $oSession->isLoggedIn() ) {
			$this->setAction(self::ACTION_AUTHORISED);
			$this->loggedInAction();
		} else {
			$oView = new accountView($this);
			$oView->showLoginPage();
		}
	}
	
	/**
	 * Shows the referral page
	 * 
	 */
	function referralAction(){
		if ( $this->getRequest()->getSession()->isLoggedIn() ) {
			$oView = new accountView($this);
			$oView->showReferralPage();		
		} else {
			$this->redirect("/account/login?redirect=/account/reward");
		}	
	}
	
	/**
	 * Shows the thank you page for the referral
	 */
	function referralDoneAction(){
		$oView = new accountView($this);
		$oView->showReferralThankYou();				
	}
	
	/**
	 * Shows the referral terms 
	 */
	function referralTermsAction(){
		$oView = new accountView($this);
		$oView->showReferralTerms();						
	}
	
	/**
	 * Handles the referral page
	 */
	function doReferralAction() {
		if ( $this->getRequest()->getSession()->isLoggedIn() ) {
			$oUser = $this->getRequest()->getSession()->getUser();
			$data = $this->getInputManager()->doFilter();
			$data["refer"] = trim($data["refer"]);
			if ( $data['refer'] && strlen($data['refer']) > 0 && !mofilmUserManager::getInstanceByUsername($data["refer"]) ) {
				try {
					//$this->getModel()->resendActivationEmail($data['refer']);
					
					$oLog = new mofilmUserLog();
					$oLog->setType(mofilmUserLog::TYPE_REFER);
					$oLog->setDescription($data["refer"]);
					$oUser->getLogSet()->setObject($oLog);
					$oUser->save();

					
					
					if ($this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue() == 'zh') {
						$this->getModel()->sendReferralCn($data["refer"], $oUser);
					} else {
						$this->getModel()->sendReferral($data["refer"], $oUser);
					}
					
					$message = 'An email has been sent to your friend';
					$level = mvcSession::MESSAGE_OK;
				} catch ( Exception $e ) {
					systemLog::error($e->getMessage());
					$message = $e->getMessage();
					$level = mvcSession::MESSAGE_ERROR;
				}

				$this->getRequest()->getSession()->setStatusMessage($message, $level);
				$this->redirect($this->buildUriPath(self::ACTION_REFER_DONE));
			} else {
				$message = 'Your friend is already registered';
				$level = mvcSession::MESSAGE_ERROR;
				$this->getRequest()->getSession()->setStatusMessage($message, $level);
				$this->redirect($this->buildUriPath(self::ACTION_REFERRAL));
			}
		} else {
			$this->redirect("/account/login?redirect=/account/reward");
		}
	}

	/**
	 * Shows the login page for chine
	 * 
	 * @return void
	 */
	protected function logincnAction() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
		if ( isset($data['redirect']) ) {
			$this->getModel()->setRedirect($data['redirect']);
		}

		$oSession = $this->getRequest()->getSession();
		if ( $oSession->isLoggedIn() ) {
			$this->setAction(self::ACTION_AUTHORISED);
			$this->loggedInAction();
		} else {
			$oView = new accountView($this);
			$oView->showLogincnPage();
		}
	}

	
	
	/**
	 * Shows the already logged in page
	 * 
	 * @return void
	 */
	protected function loggedInAction() {
		$oView = new accountView($this);
		$oView->showLoggedInPage();
	}
	
	/**
	 * Handles authenticating a user
	 * 
	 * @return void
	 */
	protected function doLoginAction() {
		$this->setAction(self::ACTION_LOGIN);
		$oSession = $this->getRequest()->getSession();
		
		if ( $oSession->isLoggedIn() ) {
			$this->loggedInAction();
		} else {
			$this->addInputToModel($this->getInputManager()->doFilter(), $this->getModel());
			if ( $this->getModel()->authenticate() || $this->getModel()->authenticateFacebook()) {
				$oSession->regenerateSessionID();
				$oSession->setUser($this->getModel()->getUser());
				$oSession->setLoggedIn(true);
				$oSession->setFBLoggedIn(false);
				
				if ( $this->getModel()->getFacebookLoginStatus() ) {
				    $oSession->setFBLoggedIn(true);
				}
				
				setcookie(mofilmConstants::COOKIE_USER_ID, $this->getModel()->getUser()->getID(), time()+3600000, "/", '.mofilm.com', false, true);
				//setcookie(mofilmConstants::COOKIE_EMAIL_ADDRESS, $this->getModel()->getUser()->getEmail(), time()+3600000, "/", '.mofilm.com', false, true);
				
				systemLog::notice("Successfully authenticated {$this->getModel()->getUsername()} ({$this->getModel()->getUser()->getID()})");
				
				$this->setAction(self::ACTION_LOGGED_IN);
				$oSession->setStatusMessage('Successfully logged in', mvcSession::MESSAGE_OK);
				
				if ( $this->getModel()->getRedirect() ) {
					systemLog::notice("Attempting to redirect user to {$this->getModel()->getRedirect()}");
					$this->redirect($this->getModel()->getRedirect());
				} else {
					$this->redirect($this->buildUriPath(profileController::ACTION_PROFILE));
				}
				return;
			} else {
				systemLog::error('Attempt to login as user '.$this->getModel()->getUsername().' failed');
				$oSession->clearFormToken();
				if ( $this->getModel()->getFacebookLoginStatus() ) {
					$oSession->setStatusMessage('Sorry, your facebook account login details is not valid in Mofilm', mvcSession::MESSAGE_ERROR);
				} else {
					$oSession->setStatusMessage('Sorry, your username and/or password were not valid', mvcSession::MESSAGE_ERROR);
				}
				
				$this->loginAction();
			}
		}
	}
	
	/**
	 * Shows the logout page
	 * 
	 * @return void
	 */
	protected function logoutAction() {
		$oView = new accountView($this);
		$oView->showLogoutPage();
	}
	
	/**
	 * Handles logging a user out
	 * 
	 * @return void
	 */
	protected function doLogoutAction() {
		$oSession = $this->getRequest()->getSession();
		
		if ( $oSession->getUser() ) {
			$oLog = new mofilmUserLog();
			$oLog->setType(mofilmUserLog::TYPE_OTHER);
			$oLog->setDescription('User logged out');

			$oSession->getUser()->getLogSet()->setObject($oLog);
			$oSession->getUser()->save();
			$oSession->setStatusMessage('Successfully Logged Out', mvcSession::MESSAGE_OK);
			systemLog::notice($oSession->getUser()->getEmail().' logged out');
		}
		
                setcookie(mofilmConstants::COOKIE_USER_ID, null, -1, "/", '.mofilm.com', false, true);
		$oSession->setUser(null);
		$oSession->setLoggedIn(false);
		$oSession->regenerateSessionID();
		$oSession->destroy();
		$oSession = null;
		unset($oSession);
		
		/*
		 * Remove cookies
		 */
		//setcookie(mofilmConstants::COOKIE_USER_ID, '', time()-7200, "/", '.mofilm.com', false, true);
		//setcookie(mofilmConstants::COOKIE_EMAIL_ADDRESS, '', time()-7200, "/", '.mofilm.com', false, true);
		if ( $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue() == "zh") {
			$this->redirect("http://mofilm.cn");
		} else {	
			//$this->redirect(system::getConfig()->getParam('mofilm', 'wwwMofilmUri', 'http://www.mofilm.com')->getParamValue());
			$this->redirect("http://".$this->getRequest()->getServerName());
		}	
	}
	
	/**
	 * Shows the activation page
	 * 
	 * @return void
	 */
	protected function activationAction() {
		$data = $this->getInputManager()->doFilter();
		if ( $data['username'] && strlen($data['username']) > 0 ) {
			try {
				$this->getModel()->resendActivationEmail($data['username']);
				$message = 'Your activation email has been sent';
				$level = mvcSession::MESSAGE_OK;
			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
				$message = $e->getMessage();
				$level = mvcSession::MESSAGE_ERROR;
			}
			
			$this->getRequest()->getSession()->setStatusMessage($message, $level);
			$this->redirect($this->buildUriPath(self::ACTION_LOGIN));
			
		} else {
			$oView = new accountView($this);
			$oView->showActivationPage();
		}
	}


	/**
	 * Shows the activation page
	 * 
	 * @return void
	 */
	protected function activationcnAction() {
		$data = $this->getInputManager()->doFilter();
		if ( $data['username'] && strlen($data['username']) > 0 ) {
			try {
				$this->getModel()->resendActivationEmail($data['username']);
				$message = '您的激活邮件已发送';
				$level = mvcSession::MESSAGE_OK;
			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
				$message = $e->getMessage();
				$level = mvcSession::MESSAGE_ERROR;
			}
			
			$this->getRequest()->getSession()->setStatusMessage($message, $level);
			$this->redirect($this->buildUriPath(self::ACTION_LOGIN));
			
		} else {
			$oView = new accountView($this);
			$oView->showActivationPage();
		}
	}
	
	
	
	/**
	 * Shows the registration page
	 * 
	 * @return void
	 */
	protected function registerAction() {
		$oView = new accountView($this);
		$hash = $this->getActionFromRequest(false, 1);
		$fdata = $this->getRequest()->getSession()->getParam('register.formData');
		
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();

		if ( isset ($data['utm_campaign']) ) {
			$inSignupCode = mofilmUserSignupCode::getInstanceByCode($data['utm_campaign']);
			if ( isset ($inSignupCode) && $inSignupCode->getID() > 0  ) {
				$fdata['SignupCode'] = $inSignupCode->getID();
			}
		}
                
		if ( isset($data['redirect']) ) {
                        $fdata['redirect'] = $data['redirect'];
		}
		if ( $hash ) {
			$oUser = mofilmUserManager::getInstance()->setLoadOnlyActive(false)->getUserByHash($hash);
			if ( $oUser instanceof mofilmUserBase && $oUser->getID() && !$oUser->isEnabled() ) {
				$this->getModel()->setUser($oUser);
				$this->getModel()->activateUser();
				
				/*
				 * Fake login
				 */
				//setcookie(mofilmConstants::COOKIE_USER_ID, $this->getModel()->getUser()->getID(), time()+3600000, "/", '.mofilm.com', false, true);
				//setcookie(mofilmConstants::COOKIE_EMAIL_ADDRESS, $this->getModel()->getUser()->getEmail(), time()+3600000, "/", '.mofilm.com', false, true);
				
				$oView->showWelcomePage();
				return;
			}
			
			$this->getRequest()->getSession()
				->setStatusMessage('Sorry, but that is an invalid activation hash', mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_REGISTER));
			return;
			
		} else {
			if (isset ($data["referral"]) ) {
				$oView->getEngine()->assign('doRegisterUri', $this->buildUriPath(self::ACTION_DO_REGISTER)."?referral=".$data["referral"]);
			}
			$oView->getEngine()->assign('formData', $fdata);
			$oView->showRegisterPage();
		}
	}

	/**
	 * Shows the registration page
	 * 
	 * @return void
	 */
	protected function registercnAction() {
		$oView = new accountView($this);
		$hash = $this->getActionFromRequest(false, 1);
		$fdata = $this->getRequest()->getSession()->getParam('register.formData');
		
		if ( $hash ) {
			$oUser = mofilmUserManager::getInstance()->setLoadOnlyActive(false)->getUserByHash($hash);
			if ( $oUser instanceof mofilmUserBase && $oUser->getID() && !$oUser->isEnabled() ) {
				$this->getModel()->setUser($oUser);
				$this->getModel()->activateUser();
				
				/*
				 * Fake login
				 */
				//setcookie(mofilmConstants::COOKIE_USER_ID, $this->getModel()->getUser()->getID(), time()+3600000, "/", '.mofilm.com', false, true);
				//setcookie(mofilmConstants::COOKIE_EMAIL_ADDRESS, $this->getModel()->getUser()->getEmail(), time()+3600000, "/", '.mofilm.com', false, true);
				
				$oView->showWelcomePage();
				return;
			}
			
			$this->getRequest()->getSession()
				->setStatusMessage('Sorry, but that is an invalid activation hash', mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_REGISTER));
			return;
			
		} else {
			$oView->getEngine()->assign('formData', $fdata);
			$oView->showRegistercnPage();
		}
	}
	
	/**
	 * Shows the custom registration page for Chines events
	 * 
	 */
	function eventRegistercnAction() {
		$oView = new accountView($this);
		$hash = $this->getActionFromRequest(false, 1);
		$eventID = $this->getActionFromRequest(false, 2);
		$fdata = $this->getRequest()->getSession()->getParam('register.formData');
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
				
		if ( $hash ) {
			$oUser = mofilmUserManager::getInstance()->setLoadOnlyActive(false)->getUserByHash($hash);
			if ( $oUser instanceof mofilmUserBase && $oUser->getID() && !$oUser->isEnabled() ) {
				$this->getModel()->setUser($oUser);
				$this->getModel()->activateUser();
				
				/*
				 * Fake login
				 */
				//setcookie(mofilmConstants::COOKIE_USER_ID, $this->getModel()->getUser()->getID(), time()+3600000, "/", '.mofilm.com', false, true);
				//setcookie(mofilmConstants::COOKIE_EMAIL_ADDRESS, $this->getModel()->getUser()->getEmail(), time()+3600000, "/", '.mofilm.com', false, true);
				
				//$this->redirect("http://mofilm.cn".mofilmEvent::getInstance($eventID)->getWebpath());
				$oView->showWelcomecnPage();
				return;
			}
			
			$this->getRequest()->getSession()
				->setStatusMessage('Sorry, but that is an invalid activation hash', mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_EVENT_BASED_REGISTER)."?campaignID=".$eventID);
			return;
			
		} else {
			$oView->getEngine()->assign('formData', $fdata);
			$oView->showEventRegistercnPage($data["campaignID"]);
		}
		
	}
	
	
	/**
	 * Handles the registration steps
	 * 
	 * @return void
	 */
	protected function doRegisterAction() {
		$data = $this->getInputManager()->doFilter();
		$inCaptcha = $this->getRequest()->getSession()->getParam('mofilm_captcha_reg_number');
		
		if ( $inCaptcha == $data['Captcha'] ) {
			if ( isset($_GET["referral"])){
				$data["referral"] = $_GET["referral"];
			}
			$data['dob'] = mofilmUtilities::buildDate($data, 'DateOfBirth');
			try {
				$this->getModel()->registerUser($data);

				$message = 'Your account has been created and an activation email sent. Click the link in the email to activate your account.';
				$level = mvcSession::MESSAGE_OK;
				$redirect = $this->buildUriPath(self::ACTION_REGISTER_DONE);

				$this->getRequest()->getSession()->setParam('email', $data['username']);
				$this->getRequest()->getSession()->removeParam('register.formData');
				
				$this->getModel()->activateUser();
                                if ( $data["redirect"] != "" ){
                                    $this->redirect($data["redirect"]);
                                } else {
                                    $oView = new accountView($this);
                                    $oView->showWelcomePage();
                                    return;                                    
                                }
                                    
                                
			} catch ( mofilmException $e ) {
				$message = $e->getMessage();
				$level = mvcSession::MESSAGE_ERROR;
				$redirect = $this->buildUriPath(self::ACTION_REGISTER."?".$_SERVER["QUERY_STRING"]);
				$this->getRequest()->getSession()->setParam('register.formData', $data);

			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
				$message = $e->getMessage();
				$level = mvcSession::MESSAGE_ERROR;
				$redirect = $this->buildUriPath(self::ACTION_REGISTER."?".$_SERVER["QUERY_STRING"]);
				$this->getRequest()->getSession()->setParam('register.formData', $data);
			}
		} else {
			$message = 'The Code you entered did not match the code shown on the page. Please try again.';
			$level = mvcSession::MESSAGE_ERROR;
			$redirect = $this->buildUriPath(self::ACTION_REGISTER."?".$_SERVER["QUERY_STRING"]);
			$this->getRequest()->getSession()->setParam('register.formData', $data);
		}
		
		$this->getRequest()->getSession()->setStatusMessage($message, $level);
		$this->redirect($redirect);
	}
	
	function doEventRegistercnAction() {
		$data = $this->getInputManager()->doFilter();
		$this->getRequest()->setLocale("cn");
		$data['dob'] = mofilmUtilities::buildDate($data, 'DateOfBirth');
		
		
		try {
			$this->getModel()->registercnEventUser($data);
			
			$message = '您的帐户已创建并发送一封激活邮件。点击电子邮件中的链接来激活您的帐户。';
			$level = mvcSession::MESSAGE_OK;
			$redirect = $this->buildUriPath(self::ACTION_REGISTERCN_DONE);
			
			$this->getRequest()->getSession()->setParam('email', $data['username']);
			$this->getRequest()->getSession()->removeParam('register.formData');
			
		} catch ( mofilmException $e ) {
			$message = $e->getMessage();
			$level = mvcSession::MESSAGE_ERROR;
			$redirect = $this->buildUriPath(self::ACTION_EVENT_BASED_REGISTER."?campaignID=".$data["campaignID"]);
			$this->getRequest()->getSession()->setParam('register.formData', $data);
			
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			$message = '很抱歉，有一个错误，而处理您的请求。请稍后再试。';
			$level = mvcSession::MESSAGE_ERROR;
			$redirect = $this->buildUriPath(self::ACTION_EVENT_BASED_REGISTER."?campaignID=".$data["campaignID"]);
			$this->getRequest()->getSession()->setParam('register.formData', $data);
		}
		
		$this->getRequest()->getSession()->setStatusMessage($message, $level);
		$this->redirect($redirect);
		
		
	}
	
	/**
	 * Handles the registration steps for chinese page
	 * 
	 * @return void
	 */
	protected function doRegistercnAction() {
		$data = $this->getInputManager()->doFilter();
		$this->getRequest()->setLocale("cn");
		$inCaptcha = $this->getRequest()->getSession()->getParam('mofilm_captcha_reg_number');
		
		if ( $inCaptcha == $data['Captcha'] ) {
			$data['dob'] = mofilmUtilities::buildDate($data, 'DateOfBirth');
			try {
				$this->getModel()->registerUser($data);

				$message = '您的帐户已创建并发送一封激活邮件。点击电子邮件中的链接来激活您的帐户。';
				$level = mvcSession::MESSAGE_OK;
				$redirect = $this->buildUriPath(self::ACTION_REGISTERCN_DONE);

				$this->getRequest()->getSession()->setParam('email', $data['username']);
				$this->getRequest()->getSession()->removeParam('register.formData');
				
				$this->getModel()->activateUser();
				
				$oView = new accountView($this);
				$oView->showWelcomePage();
				return;
			} catch ( mofilmException $e ) {
				$message = $e->getMessage();
				$level = mvcSession::MESSAGE_ERROR;
				$redirect = $this->buildUriPath(self::ACTION_REGISTERCN);
				$this->getRequest()->getSession()->setParam('register.formData', $data);

			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
				$message = '很抱歉，有一个错误，而处理您的请求。请稍后再试。';
				$level = mvcSession::MESSAGE_ERROR;
				$redirect = $this->buildUriPath(self::ACTION_REGISTERCN);
				$this->getRequest()->getSession()->setParam('register.formData', $data);
			}
		} else {
			$message = '您所输入验证码有误。请重新输入。';
			$level = mvcSession::MESSAGE_ERROR;
			$redirect = $this->buildUriPath(self::ACTION_REGISTERCN);
			$this->getRequest()->getSession()->setParam('register.formData', $data);
		}
		
		$this->getRequest()->getSession()->setStatusMessage($message, $level);
		$this->redirect($redirect);
	}
	
	
	
	/**
	 * Handles the post registration success page
	 * 
	 * @return void
	 */
	protected function registerDoneAction() {
		$oView = new accountView($this);
		$oView->showRegistrationDonePage();
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('username', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('refer', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('referral', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('password', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('facebookID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('accessToken', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('redirect', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('_sk', utilityInputFilter::filterString());

		$this->getInputManager()->addFilter('curPassword', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Password', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ConfirmPassword', utilityInputFilter::filterString());		
		$this->getInputManager()->addFilter('Firstname', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Surname', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ProfileName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Phone', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SignupCode', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('DateOfBirth', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('Skills', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('territory', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('optIn', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('registrationSource', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('City', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('campaignID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('utm_campaign', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Captcha', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SchoolName', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('Affiliate', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param accountModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setUsername($inData['username']);
		$inModel->setPassword($inData['password']);
		$inModel->setFacebookID($inData['facebookID']);
		$inModel->setFBAccessToken($inData['accessToken']);
		$inModel->setFormSessionToken($inData['_sk']);
		$inModel->setRegistrationSource($inData['registrationSource']);
		
		
		$inModel->setCity($inData['City']);

		$inData['redirect'] = urldecode($inData['redirect']);
		if (
			$inData['redirect'] == $this->buildUriPath(self::ACTION_LOGIN) ||
			$inData['redirect'] == $this->buildUriPath(self::ACTION_DO_LOGIN) ||
			(!$inData['redirect'] && $inModel->getRedirect() == $this->buildUriPath(self::ACTION_LOGIN)) ||
			(!$inData['redirect'] && $inModel->getRedirect() == $this->buildUriPath(self::ACTION_DO_LOGIN))
		) {
			/*
			 * Prevent recursion in redirection
			 */
			$inModel->setRedirect('');
		} else {
			/*
			if ( stripos($inData['redirect'], 'http') === 0 || strpos($inData['redirect'], '.') !== false ) {
				if ( !preg_match('/^(http|https)\:\/\/([\w\.]+).mofilm.(com|cn)\//i', $inData['redirect']) ) {
					systemLog::warning("None mofilm redirect intercepted: {$inData['redirect']} (from {$_SERVER['REMOTE_ADDR']})");
					$inData['redirect'] = null;
				}	
		}
		*/
			$inModel->setRedirect($inData['redirect']);
		}
	}
	
	/**
	 * Fetches the model
	 *
	 * @return accountModel
	 */
	function getModel() {
		if ( !parent::getModel() ) {
			$this->buildModel();
		}
		return parent::getModel();
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new accountModel();
		$oModel->setRequest($this->getRequest());
		$oModel->setRedirect($this->getRequest()->getRequestUri());
		$oModel->setLanguage($this->getRequest()->getLocale());
		$this->setModel($oModel);
	}
}
