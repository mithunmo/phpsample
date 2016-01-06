<?php
/**
 * affiliateBannersView.class.php
 * 
 * affiliateBannersView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category affiliateBannersView
 * @version $Rev: 91 $
 */


/**
 * affiliateBannersView class
 * 
 * Provides the "affiliateBannersView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category affiliateBannersView
 */
class affiliateBannersView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('wwwMofilmUri', system::getConfig()->getParam('mofilm', 'wwwMofilmUri', 'http://www.mofilm.com')->getParamValue());
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('affiliateBannersList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('affiliateBannersForm');
	}
}