<?php
/**
 * brandView.class.php
 * 
 * brandView class
 *
 * @author Poulami Chakraborty
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category brandView
 * @version $Rev: 624 $
 */


/**
 * brandView class
 * 
 * Provides the "brandView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category brandView
 */
class brandView extends mvcDaoView {

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
		return $this->getTpl('brandList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('brandForm');
	}
        
 
}