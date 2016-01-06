<?php
/**
 * reportTypesView.class.php
 * 
 * reportTypesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportTypesView
 * @version $Rev: 11 $
 */


/**
 * reportTypesView class
 * 
 * Provides the "reportTypesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportTypesView
 */
class reportTypesView extends mvcDaoView {

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
		return $this->getTpl('reportTypesList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('reportTypesForm');
	}
}