<?php
/**
 * suppliersView.class.php
 * 
 * suppliersView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category suppliersView
 * @version $Rev: 11 $
 */


/**
 * suppliersView class
 * 
 * Provides the "suppliersView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category suppliersView
 */
class suppliersView extends mvcDaoView {

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
		return $this->getTpl('suppliersList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('suppliersForm');
	}
}