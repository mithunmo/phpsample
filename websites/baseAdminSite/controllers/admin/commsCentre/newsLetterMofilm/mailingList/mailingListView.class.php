<?php
/**
 * mailingListView.class.php
 * 
 * mailingListView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category mailingListView
 * @version $Rev: 624 $
 */


/**
 * mailingListView class
 * 
 * Provides the "mailingListView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category mailingListView
 */
class mailingListView extends mvcDaoView {

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
		return $this->getTpl('mailingListList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('mailingListForm');
	}
}