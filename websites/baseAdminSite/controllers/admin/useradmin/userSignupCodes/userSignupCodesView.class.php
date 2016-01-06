<?php
/**
 * userSignupCodesView.class.php
 * 
 * userSignupCodesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userSignupCodesView
 * @version $Rev: 11 $
 */


/**
 * userSignupCodesView class
 * 
 * Provides the "userSignupCodesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userSignupCodesView
 */
class userSignupCodesView extends mvcDaoView {

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
		return $this->getTpl('userSignupCodesList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('userSignupCodesForm');
	}
}