<?php
/**
 * HandpickedMusicView.class.php
 * 
 * HandpickedMusicView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category HandpickedMusicView
 * @version $Rev: 624 $
 */


/**
 * HandpickedMusicView class
 * 
 * Provides the "HandpickedMusicView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category HandpickedMusicView
 */
class HandpickedMusicView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		/**
		 * @todo set these parameters
		 */
		$this->getEngine()->assign('parentController', 'admin');
                $this->getEngine()->assign('imageList', momusicCoverimage::listOfObjects());
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('HandpickedMusicList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('HandpickedMusicForm');
	}
}