<?php
/**
 * movieAwardsView.class.php
 * 
 * movieAwardsView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieAwardsView
 * @version $Rev: 11 $
 */


/**
 * movieAwardsView class
 * 
 * Provides the "movieAwardsView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieAwardsView
 */
class movieAwardsView extends mvcDaoView {

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
		return $this->getTpl('movieAwardsList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('movieAwardsForm');
	}
}