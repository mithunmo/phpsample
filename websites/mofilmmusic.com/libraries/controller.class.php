<?php
/**
 * mvcController.class.php
 *
 * mvcController class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm.com
 * @subpackage websites_mofilm.com_libraries
 * @category mvcController
 */


/**
 * mvcController
 *
 * Main site mvcController implementation, holds base directives and defaults for the site
 *
 * @package mofilm.com
 * @subpackage websites_mofilm.com_libraries
 * @category mvcController
 */
abstract class mvcController extends mvcControllerBase {

	/**
	 * @see mvcControllerBase::authorise()
	 */
	function authorise() {
		
		if ( $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue() == "zh") {
			$uri = system::getConfig()->getParam('mofilm', 'myMofilmCNUri', 'http://my.mofilm.cn');
			$uri .= '/account/authorise?redirect=';
		} else {
			$uri = system::getConfig()->getParam('mofilm', 'myMofilmUri', 'http://mofilm.com');
			$uri .= '/account/authorise?redirect=';
		}
		$redirect = 'http://'.$this->getRequest()->getServerName().$this->getRequest()->getRequestUri();
		if ( isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0 ) {
			$redirect .= '?'.$_SERVER['QUERY_STRING']; 
		}
		$this->redirect($uri.urlencode($redirect));
	}

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		$this->setRequiresAuthentication(false);
		
		if ( $this->getRequest()->getSession()->getUser() ) {
			$userID = $this->getRequest()->getSession()->getUser()->getID();
		} else {
			$userID = 0;
		}
		systemLog::getInstance()->setSource(
			systemLogSource::getInstance(
				array(
					systemLogSource::DESC_SYSTEM_USER_ID =>	$userID,
					systemLogSource::DESC_CONTROLLER => get_class($this),
					systemLogSource::DESC_CONTROLLER_ACTION => $this->getAction()
				)
			)
		);
		
		/*
		 * Bind input filters to input manager
		 */
		$this->addInputFilters();
	}

	/**
	 * @see mvcControllerBase::isValidAction()
	 */
	function isValidAction() {
		return $this->getControllerActions()->isValidAction($this->getAction());
	}

	/**
	 * @see mvcControllerBase::isValidView()
	 */
	function isValidView($inView) {
		return $this->getControllerViews()->isValidView($inView);
	}

	/**
	 * @see mvcControllerBase::isAuthorised()
	 */
	function isAuthorised() {
		$auth = true;
		if ( $this->getRequiresAuthentication() ) {
			$oUser = $this->getRequest()->getSession()->getUser();
			$auth = (($oUser !== false) && $oUser instanceof mofilmUser && $this->getRequest()->getSession()->isLoggedIn());
		}
		return $auth;
	}

	/**
	 * @see mvcControllerBase::hasAuthority()
	 */
	function hasAuthority($inActivity) {
		return true;
	}
}