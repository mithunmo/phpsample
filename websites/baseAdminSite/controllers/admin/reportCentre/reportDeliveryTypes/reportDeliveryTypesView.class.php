<?php
/**
 * reportDeliveryTypesView.class.php
 * 
 * reportDeliveryTypesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportDeliveryTypesView
 * @version $Rev: 11 $
 */


/**
 * reportDeliveryTypesView class
 * 
 * Provides the "reportDeliveryTypesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportDeliveryTypesView
 */
class reportDeliveryTypesView extends mvcDaoView {

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
		return $this->getTpl('reportDeliveryTypesList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('reportDeliveryTypesForm');
	}
}