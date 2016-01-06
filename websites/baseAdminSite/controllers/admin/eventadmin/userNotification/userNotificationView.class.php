<?php
/**
 * userNotificationView.class.php
 * 
 * userNotificationView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userNotificationView
 * @version $Rev: 624 $
 */


/**
 * userNotificationView class
 * 
 * Provides the "userNotificationView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userNotificationView
 */
class userNotificationView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		/**
		 * @todo set these parameters
		 */
		$this->getEngine()->assign('eventsall', utilityOutputWrapper::wrap(mofilmEvent::listOfObjects(null, null, true)));                        
		$this->getEngine()->assign('parentController', 'admin');
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('userNotificationList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
            
		return $this->getTpl('userNotificationForm');
	}
}