<?php
/**
 * datanamesView.class.php
 * 
 * datanamesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category datanamesView
 * @version $Rev: 11 $
 */


/**
 * datanamesView class
 * 
 * Provides the "datanamesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category datanamesView
 */
class datanamesView extends mvcDaoView {

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
		return $this->getTpl('datanamesList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('datanamesForm');
	}
}