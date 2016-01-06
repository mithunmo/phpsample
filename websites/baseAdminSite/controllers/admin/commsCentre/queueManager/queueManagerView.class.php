<?php
/**
 * queueManagerView.class.php
 * 
 * queueManagerView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category queueManagerView
 * @version $Rev: 11 $
 */


/**
 * queueManagerView class
 * 
 * Provides the "queueManagerView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category queueManagerView
 */
class queueManagerView extends mvcDaoView {

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
		return $this->getTpl('queueManagerList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('queueManagerForm');
	}
}