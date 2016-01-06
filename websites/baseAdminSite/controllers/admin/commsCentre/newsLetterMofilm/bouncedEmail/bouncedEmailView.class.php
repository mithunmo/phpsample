<?php
/**
 * bouncedEmailView.class.php
 * 
 * bouncedEmailView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category bouncedEmailView
 * @version $Rev: 624 $
 */


/**
 * bouncedEmailView class
 * 
 * Provides the "bouncedEmailView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category bouncedEmailView
 */
class bouncedEmailView extends mvcDaoView {

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
		return $this->getTpl('bouncedEmailList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('bouncedEmailForm');
	}
}