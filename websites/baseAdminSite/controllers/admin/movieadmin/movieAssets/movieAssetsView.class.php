<?php
/**
 * movieAssetsView.class.php
 * 
 * movieAssetsView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieAssetsView
 * @version $Rev: 11 $
 */


/**
 * movieAssetsView class
 * 
 * Provides the "movieAssetsView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieAssetsView
 */
class movieAssetsView extends mvcDaoView {

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
		return $this->getTpl('movieAssetsList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('movieAssetsForm');
	}
}