<?php
/**
 * imapServerView.class.php
 * 
 * imapServerView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category imapServerView
 * @version $Rev: 624 $
 */


/**
 * imapServerView class
 * 
 * Provides the "imapServerView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category imapServerView
 */
class imapServerView extends mvcDaoView {

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
		return $this->getTpl('imapServerList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('imapServerForm');
	}
}