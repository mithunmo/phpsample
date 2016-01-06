<?php
/**
 * featuredArtistView.class.php
 * 
 * featuredArtistView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category featuredArtistView
 * @version $Rev: 624 $
 */


/**
 * featuredArtistView class
 * 
 * Provides the "featuredArtistView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category featuredArtistView
 */
class featuredArtistView extends mvcDaoView {

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
		return $this->getTpl('featuredArtistList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('featuredArtistForm');
	}
        
	function showUploadPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('upload'));
	}
        
}