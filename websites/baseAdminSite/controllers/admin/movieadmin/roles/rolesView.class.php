<?php
/**
 * rolesView.class.php
 * 
 * rolesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category rolesView
 * @version $Rev: 11 $
 */


/**
 * rolesView class
 * 
 * Provides the "rolesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category rolesView
 */
class rolesView extends mvcDaoView {

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
		return $this->getTpl('rolesList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('rolesForm');
	}
}