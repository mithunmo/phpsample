<?php
/**
 * apiUserView.class.php
 * 
 * apiUserView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category apiUserView
 * @version $Rev: 624 $
 */


/**
 * apiUserView class
 * 
 * Provides the "apiUserView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category apiUserView
 */
class apiUserView extends mvcDaoView {

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
		return $this->getTpl('apiUserList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('apiUserForm');
	}
}