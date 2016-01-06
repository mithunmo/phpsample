<?php
/**
 * worksView.class.php
 * 
 * worksView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category worksView
 * @version $Rev: 624 $
 */


/**
 * worksView class
 * 
 * Provides the "worksView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category worksView
 */
class worksView extends mvcDaoView {

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
		return $this->getTpl('worksList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('worksForm');
	}
}