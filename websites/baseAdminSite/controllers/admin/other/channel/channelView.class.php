<?php
/**
 * channelView.class.php
 * 
 * channelView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category channelView
 * @version $Rev: 624 $
 */


/**
 * channelView class
 * 
 * Provides the "channelView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category channelView
 */
class channelView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		/**
		 * @todo set these parameters
		 */
		$this->getEngine()->assign('parentController', 'admin');
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('channelList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('channelForm');
	}
}