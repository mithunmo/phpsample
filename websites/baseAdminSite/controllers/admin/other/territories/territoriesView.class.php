<?php
/**
 * territoriesView.class.php
 * 
 * territoriesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category territoriesView
 * @version $Rev: 11 $
 */


/**
 * territoriesView class
 * 
 * Provides the "territoriesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category territoriesView
 */
class territoriesView extends mvcDaoView {

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
		return $this->getTpl('territoriesList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('territoriesForm');
	}
}