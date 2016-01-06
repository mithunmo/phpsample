<?php
/**
 * downloadsView.class.php
 * 
 * downloadsView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category downloadsView
 * @version $Rev: 624 $
 */


/**
 * downloadsView class
 * 
 * Provides the "downloadsView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category downloadsView
 */
class downloadsView extends mvcDaoView {

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
		return $this->getTpl('downloadsList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('downloadsForm');
	}
}