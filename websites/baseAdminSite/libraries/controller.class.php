<?php
/**
 * mvcController.class.php
 *
 * mvcController class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package baseAdminSite
 * @subpackage websites_baseAdminSite_libraries
 * @category mvcController
 * @version $Rev: 172 $
 */


/**
 * mvcController
 *
 * Main site mvcController implementation, holds base directives and defaults for the site
 *
 * @package baseAdminSite
 * @subpackage websites_baseAdminSite_libraries
 * @category mvcController
 */
abstract class mvcController extends mvcControllerBase {

	/**
	 * @see mvcControllerBase::authorise()
	 */
	function authorise() {
		$this->controllerRedirect('account', 'authorise', '/account');
	}

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		if ( $this->getRequest()->getDistributor()->getSiteConfig()->getParam('site', 'requireSSL', true)->getParamValue() ) {
			if ( !$this->getRequest()->getServerSsl() ) {
				$redirect = 'https://'.$this->getRequest()->getServerName();
				if ( $this->getRequest()->getServerPort() != 443 && !in_array($this->getRequest()->getServerPort(), array(80, 8080)) ) {
					$redirect .= ':'.$this->getRequest()->getServerPort();
				}
				$redirect .= $this->getRequest()->getRequestUri();

				if ( strlen($_SERVER['QUERY_STRING']) > 1 ) {
					$redirect .= '?'.$_SERVER['QUERY_STRING'];
				}

				systemLog::info('Redirecting to SSL: '.$redirect);
				$this->redirect($redirect);
				return;
			}
		}
		
		$this->setRequiresAuthentication(true);
		
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
			$activity = $this->getActivity();
			if ( $activity ) {
				$oActivity = mofilmPermission::getInstanceByPermission($activity);
				$oActivity->setName($activity);
				$oActivity->save();

				$oUser = $this->getRequest()->getSession()->getUser();
				$auth = $auth && ($oUser !== false) && $oUser->isAuthorised($oActivity);

				if ( !$auth && $oUser !== false ) {
					systemLog::warning("Permission denied for {$oUser->getEmail()} ({$oUser->getID()}) for {$oActivity->getName()}");
				}
			}
		}
		return $auth;
	}

	/**
	 * Returns the current activity based on the request and class
	 *
	 * @return string
	 */
	function getActivity() {
		$action = $this->getAction();
		
		$activity = get_class($this).($action != '' ? '.'.$action : '');
		
		return $this->_checkNamespace($activity);
	}

	/**
	 * @see mvcControllerBase::hasAuthority()
	 */
	function hasAuthority($inActivity) {
		$inActivity = $this->_checkNamespace($inActivity);
		
		$oActivity = mofilmPermission::getInstanceByPermission($inActivity);
		$oActivity->setName($inActivity);
		$oActivity->save();

		return $this->getRequest()->getSession()->getUser()->isAuthorised($oActivity);
	}

	/**
	 * Checks the activity for a namespace, if permissions namespaces are in use
	 *
	 * @param string $inActivity
	 * @return string
	 * @access protected
	 */
	protected function _checkNamespace($inActivity) {
		if ( $this->getRequest()->getDistributor()->getSiteConfig()->getParam('permissions', 'useNamespace', false)->getParamValue() ) {
			$namespace = $this->getRequest()->getDistributor()->getSiteConfig()->getParam('permissions', 'namespace', false)->getParamValue();

			if ( $namespace && strpos($inActivity, $namespace) !== 0 && strtolower($inActivity) != 'root' ) {
				$inActivity = $namespace.'.'.$inActivity;
			}
		}

		return $inActivity;
	}
}