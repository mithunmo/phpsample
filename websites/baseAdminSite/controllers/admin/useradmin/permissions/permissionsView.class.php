<?php
/**
 * permissionsView.class.php
 * 
 * permissionsView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category permissionsView
 * @version $Rev: 11 $
 */


/**
 * permissionsView class
 * 
 * Provides the "permissionsView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category permissionsView
 */
class permissionsView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('permissionsList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('permissionsForm');
	}
}