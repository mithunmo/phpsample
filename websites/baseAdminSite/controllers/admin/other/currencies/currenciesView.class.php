<?php
/**
 * currenciesView.class.php
 * 
 * currenciesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category currenciesView
 * @version $Rev: 11 $
 */


/**
 * currenciesView class
 * 
 * Provides the "currenciesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category currenciesView
 */
class currenciesView extends mvcDaoView {

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
		return $this->getTpl('currenciesList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('currenciesForm');
	}
}