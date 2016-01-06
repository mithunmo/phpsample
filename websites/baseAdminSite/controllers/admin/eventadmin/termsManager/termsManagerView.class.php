<?php
/**
 * termsManagerView.class.php
 * 
 * termsManagerView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category termsManagerView
 * @version $Rev: 11 $
 */


/**
 * termsManagerView class
 * 
 * Provides the "termsManagerView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category termsManagerView
 */
class termsManagerView extends mvcDaoView {

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
		return $this->getTpl('termsManagerList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('termsManagerForm');
	}
}