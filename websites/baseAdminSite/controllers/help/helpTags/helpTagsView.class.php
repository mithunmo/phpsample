<?php
/**
 * helpTagsView.class.php
 * 
 * helpTagsView class
 *
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category helpTagsView
 * @version $Rev: 624 $
 */


/**
 * helpTagsView class
 * 
 * Provides the "helpTagsView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category helpTagsView
 */
class helpTagsView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'help');
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('helpTagsList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('helpTagsForm');
	}
}