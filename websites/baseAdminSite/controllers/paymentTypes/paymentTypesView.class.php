<?php
/**
 * paymentTypesView.class.php
 * 
 * paymentTypesView class
 *
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category paymentTypesView
 * @version $Rev: 624 $
 */


/**
 * paymentTypesView class
 * 
 * Provides the "paymentTypesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category paymentTypesView
 */
class paymentTypesView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		/**
		 * @todo set these parameters
		 */
		$this->getEngine()->assign('parentController', 'admin');
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('paymentTypesList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('paymentTypesForm');
	}
}