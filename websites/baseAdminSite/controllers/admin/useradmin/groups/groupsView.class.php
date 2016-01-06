<?php
/**
 * groupsView.class.php
 * 
 * groupsView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category groupsView
 * @version $Rev: 11 $
 */


/**
 * groupsView class
 * 
 * Provides the "groupsView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category groupsView
 */
class groupsView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->addJavascriptResource(new mvcViewJavascript('shiftCheckbox', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jquery.shiftcheckbox.js'));
		
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('groups', utilityOutputWrapper::wrap(mofilmPermissionGroup::listOfObjects()));
		
		$this->getEngine()->assign('permissions', utilityOutputWrapper::wrap(mofilmPermission::listOfObjects(null, null, '%Controller%', mofilmPermission::MATCH_NOT_LIKE)));
		$this->getEngine()->assign('oControllerMap', utilityOutputWrapper::wrap($this->getRequest()->getDistributor()->getSiteConfig()->getControllerMapper()));
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('groupsList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('groupsForm');
	}
}