<?php
/**
 * gatewaysView.class.php
 * 
 * gatewaysView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category gatewaysView
 * @version $Rev: 11 $
 */


/**
 * gatewaysView class
 * 
 * Provides the "gatewaysView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category gatewaysView
 */
class gatewaysView extends mvcDaoView {

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
		return $this->getTpl('gatewaysList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('gatewaysForm');
	}
}