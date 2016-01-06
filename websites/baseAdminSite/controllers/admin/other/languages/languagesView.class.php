<?php
/**
 * languagesView.class.php
 * 
 * languagesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category languagesView
 * @version $Rev: 11 $
 */


/**
 * languagesView class
 * 
 * Provides the "languagesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category languagesView
 */
class languagesView extends mvcDaoView {

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
		return $this->getTpl('languagesList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('languagesForm');
	}
}