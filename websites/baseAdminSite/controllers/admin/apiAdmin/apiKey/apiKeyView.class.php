<?php
/**
 * apiKeyView.class.php
 * 
 * apiKeyView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category apiKeyView
 * @version $Rev: 624 $
 */


/**
 * apiKeyView class
 * 
 * Provides the "apiKeyView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category apiKeyView
 */
class apiKeyView extends mvcDaoView {

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
		return $this->getTpl('apiKeyList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('apiKeyForm');
	}
}