<?php
/**
 * distributionView.class.php
 * 
 * distributionView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category distributionView
 * @version $Rev: 624 $
 */


/**
 * distributionView class
 * 
 * Provides the "distributionView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category distributionView
 */
class distributionView extends mvcDaoView {

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
		return $this->getTpl('distributionList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('distributionForm');
	}
}