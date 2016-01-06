<?php
/**
 * reportQueueView.class.php
 * 
 * reportQueueView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportQueueView
 * @version $Rev: 11 $
 */


/**
 * reportQueueView class
 * 
 * Provides the "reportQueueView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportQueueView
 */
class reportQueueView extends mvcDaoView {

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
		return $this->getTpl('reportQueueList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('reportQueueForm');
	}
}