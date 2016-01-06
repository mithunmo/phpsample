<?php
/**
 * corporateView.class.php
 * 
 * corporateView class
 *
 * @author Poulami Chakraborty
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category corporateView
 * @version $Rev: 624 $
 */


/**
 * corporateView class
 * 
 * Provides the "corporateView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category corporateView
 */
class corporateView extends mvcDaoView {

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
		return $this->getTpl('corporateList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('corporateForm');
	}
}