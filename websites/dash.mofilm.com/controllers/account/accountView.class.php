<?php
/**
 * accountView.class.php
 * 
 * accountView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category accountView
 * @version $Rev: 11 $
 */


/**
 * accountView class
 * 
 * Provides the "accountView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category accountView
 */
class accountView extends mvcView {

	/**
	 * Assigns some default values to template engine that are always needed
	 *
	 * @return void
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		$this->getEngine()->assign('doLoginUri', $this->getController()->buildUriPath(accountController::ACTION_DO_LOGIN));
		$this->getEngine()->assign('doLogoutUri', $this->getController()->buildUriPath(accountController::ACTION_DO_LOGOUT));
		$this->getEngine()->assign('loginUri', $this->getController()->buildUriPath(accountController::ACTION_LOGIN));
		$this->getEngine()->assign('logoutUri', $this->getController()->buildUriPath(accountController::ACTION_LOGOUT));
		$this->getEngine()->assign('redirect', $this->getModel()->getRedirect());
	}
	
	/**
	 * Shows the accountView page
	 *
	 * @return void
	 */
	function showLoginPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('formSessionKey', $this->getRequest()->getSession()->getFormToken());
		
		$this->render($this->getTpl('login'));
	}

	/**
	 * Displays the logged in / authorised page
	 */
	function showLoggedInPage() {
		$this->setCacheLevelNone();
		$this->render($this->getTpl('loggedIn'));
	}

	/**
	 * Displays the logout confirmation page
	 *
	 * @return void
	 */
	function showLogoutPage() {
		$this->setCacheLevelNone();
		$this->render($this->getTpl('logout'));
	}

	/**
	 * Displays the not authorised page
	 *
	 * @return void
	 */
	function showNotAuthorisedPage() {
		$this->setCacheLevelNone();
		$this->render($this->getTpl('notAuthorised'));
	}

	/**
	 * Displays the lost password page
	 *
	 * @return void
	 */
	function showLostPasswordPage() {
		$this->setCacheLevelNone();
		$this->render($this->getTpl('lostPassword'));
	}
}