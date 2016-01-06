<?php
/**
 * brandMusicView.class.php
 * 
 * brandMusicView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category brandMusicView
 * @version $Rev: 624 $
 */


/**
 * brandMusicView class
 * 
 * Provides the "brandMusicView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category brandMusicView
 */
class brandMusicView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		/**
		 * @todo set these parameters
		 */
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('eventsall', mofilmEvent::listOfObjects(null, null, true));            
                
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('brandMusicList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('brandMusicForm');
	}
}