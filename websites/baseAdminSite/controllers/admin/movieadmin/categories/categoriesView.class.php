<?php
/**
 * categoriesView.class.php
 * 
 * categoriesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category categoriesView
 * @version $Rev: 11 $
 */


/**
 * categoriesView class
 * 
 * Provides the "categoriesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category categoriesView
 */
class categoriesView extends mvcDaoView {

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
		return $this->getTpl('categoriesList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('categoriesForm');
	}
}