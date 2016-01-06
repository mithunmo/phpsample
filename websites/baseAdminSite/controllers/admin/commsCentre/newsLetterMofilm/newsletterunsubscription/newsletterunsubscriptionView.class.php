<?php
/**
 * newsletterunsubscriptionView.class.php
 * 
 * newsletterunsubscriptionView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterunsubscriptionView
 * @version $Rev: 624 $
 */


/**
 * newsletterunsubscriptionView class
 * 
 * Provides the "newsletterunsubscriptionView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterunsubscriptionView
 */
class newsletterunsubscriptionView extends mvcDaoView {

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
		return $this->getTpl('newsletterunsubscriptionList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('newsletterunsubscriptionForm');
	}
}