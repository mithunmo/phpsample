<?php
/**
 * userManagerView.class.php
 * 
 * userManagerView.class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userManagerView
 * @version $Rev: 11 $
 */


/**
 * userManagerView.class
 * 
 * Provides the "userManagerView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userManagerView
 */
class userManagerView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->addJavascriptResource(new mvcViewJavascript('shiftCheckbox', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jquery.shiftcheckbox.js'));
		
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('properties', utilityOutputWrapper::wrap(mofilmUserManager::getProperties()));
		
		$this->getEngine()->assign('permissions', utilityOutputWrapper::wrap(mofilmPermission::listOfObjects(null, null, '%Controller%', mofilmPermission::MATCH_NOT_LIKE)));
		$this->getEngine()->assign('oControllerMap', utilityOutputWrapper::wrap($this->getRequest()->getDistributor()->getSiteConfig()->getControllerMapper()));
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('usersList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('usersForm');
	}
	
	/**
	 * Shows the new user intial form
	 * 
	 * @return void
	 */
	function showNewUserForm() {
		if ( !$this->isCached($this->getTpl('newUserForm')) ) {
			$this->getEngine()->assign('formAction', $this->buildUriPath(userManagerController::ACTION_NEW));
		}
		$this->render($this->getTpl('newUserForm'));
	}
	
	/**
	 * Shows the new user settings form
	 * 
	 * @return void
	 */
	function showNewUserSettingsForm() {
		if ( !$this->isCached($this->getTpl('newUserFormSettings')) ) {
			
		}
		$this->render($this->getTpl('newUserFormSettings'));
	}
	
	/**
	 * Shows the new user error page
	 * 
	 * @return void
	 */
	function showNewUserError() {
		if ( !$this->isCached($this->getTpl('newUserError')) ) {
			
		}
		$this->render($this->getTpl('newUserError'));
	}
}