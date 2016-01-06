<?php
/**
 * tagsView.class.php
 * 
 * tagsView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category tagsView
 * @version $Rev: 11 $
 */


/**
 * tagsView class
 * 
 * Provides the "tagsView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category tagsView
 */
class tagsView extends mvcDaoView {

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
		return $this->getTpl('tagsList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('tagsForm');
	}
}