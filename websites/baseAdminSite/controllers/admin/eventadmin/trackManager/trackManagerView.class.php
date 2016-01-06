<?php
/**
 * trackManagerView.class.php
 * 
 * trackManagerView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category trackManagerView
 * @version $Rev: 11 $
 */


/**
 * trackManagerView class
 * 
 * Provides the "trackManagerView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category trackManagerView
 */
class trackManagerView extends mvcDaoView {

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
		return $this->getTpl('trackManagerList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('trackManagerForm');
	}
}