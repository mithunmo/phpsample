<?php
/**
 * typeView.class.php
 * 
 * typeView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category typeView
 * @version $Rev: 624 $
 */


/**
 * typeView class
 * 
 * Provides the "typeView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category typeView
 */
class typeView extends mvcDaoView {

	
	function __construct($inController) {
		parent::__construct($inController);

	}

	
	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		/**
		 * @todo set these parameters
		 */
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('oType', momusicType::$type);

	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('typeList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		
		return $this->getTpl('typeForm');
	}
}