<?php
/**
 * accountController
 *
 * Stored in accountController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category accountController
 * @version $Rev: 11 $
 */


/**
 * accountController
 *
 * accountController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category accountController
 */
class accountController extends mvcController {

	const ACTION_AUTHORISE = 'authorise';
	const ACTION_NOT_AUTHORISED = 'notAuthorised';
	const ACTION_AUTHORISED = 'authorised';
	
	const ACTION_LOGIN = 'login';
	const ACTION_LOGOUT = 'logout';
	const ACTION_LOGGED_IN = 'loggedIn';
	const ACTION_DO_LOGIN = 'doLogin';
	const ACTION_DO_LOGOUT = 'doLogout';

	
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
				->addAction(self::ACTION_DO_LOGIN)
				->addAction(self::ACTION_DO_LOGOUT)
				->addAction(self::ACTION_LOGOUT);

		$this->addInputFilters();
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
			case self::ACTION_DO_LOGOUT:			$this->doLogoutAction(); break;
			
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
	 * Handles the authorise action
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
	 * Handles actually authenticating a user 
	 * 
	 * @return void
	 */
	protected function doLoginAction() {
		$this->setAction(self::ACTION_LOGIN);
		$oSession = $this->getRequest()->getSession();

		if ( $oSession->isLoggedIn() ) {
			$this->setAction(self::ACTION_AUTHORISED);
			$this->loggedInAction();
		} else {
			$this->addInputToModel($this->getInputManager()->doFilter(), $this->getModel());
			if ( $this->getModel()->authenticate() ) {
				$oSession->regenerateSessionID();
				$oSession->setUser($this->getModel()->getUser());
				$oSession->setLoggedIn(true);

				systemLog::notice("Successfully authenticated {$this->getModel()->getUsername()} ({$this->getModel()->getUser()->getID()})");

				setcookie(
					mvcSession::MOFILM_LOGIN_HASH, $this->getModel()->getCookieData(), time()+3600000, "/", 'dash.mofilm.com', false, true
				);

				$this->setAction(self::ACTION_LOGGED_IN);
				$oSession->setStatusMessage('Successfully logged in', mvcSession::MESSAGE_OK);

				if ( $this->getModel()->getRedirect() ) {
					systemLog::notice("Attempting to redirect user to {$this->getModel()->getRedirect()}");
					$this->redirect($this->getModel()->getRedirect());
				} else {
					$this->loggedInAction();
				}
			} else {
				systemLog::error('Attempt to login as user '.$this->getModel()->getUsername().' failed');
				$oSession->clearFormToken();
				$oSession->setStatusMessage('Sorry, your username and/or password were not valid', mvcSession::MESSAGE_ERROR);
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
	 * Handles logging a user out from the system
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
		$oSession->setUser(null);
		$oSession->setLoggedIn(false);
		$oSession->regenerateSessionID();
		$oSession->destroy();
		$oSession = null;
		unset($oSession);

		setcookie(mvcSession::MOFILM_LOGIN_HASH, '', time()-7200, "/", 'dash.mofilm.com', false, true);

		$this->redirect($this->buildUriPath(self::ACTION_LOGIN));
	}
	
	/**
	 * Shows the login page
	 * 
	 * @return void
	 */
	protected function loginAction() {
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
	 * Shows the already logged in page
	 * 
	 * @return void
	 */
	protected function loggedInAction() {
		$oView = new accountView($this);
		$oView->showLoggedInPage();
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('username', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('password', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('redirect', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('_sk', utilityInputFilter::filterString());
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
		$inModel->setFormSessionToken($inData['_sk']);
		
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