<?php
/**
 * userActivityLogView.class.php
 * 
 * userActivityLogView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userActivityLogView
 * @version $Rev: 11 $
 */


/**
 * userActivityLogView class
 * 
 * Provides the "userActivityLogView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userActivityLogView
 */
class userActivityLogView extends mvcDaoView {

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
		return $this->getTpl('userActivityLogList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('userActivityLogForm');
	}
}