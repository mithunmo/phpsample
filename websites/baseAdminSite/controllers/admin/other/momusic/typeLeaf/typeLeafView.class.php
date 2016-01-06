<?php
/**
 * typeLeafView.class.php
 * 
 * typeLeafView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category typeLeafView
 * @version $Rev: 624 $
 */


/**
 * typeLeafView class
 * 
 * Provides the "typeLeafView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category typeLeafView
 */
class typeLeafView extends mvcDaoView {

	
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
		$this->getEngine()->assign('oTypeList', momusicType::listOfObjects());
		$this->getEngine()->assign('oTypeLeafList', momusicTypeLeaf::listOfObjects());
		$this->getEngine()->assign('oType', momusicType::$type);
		//$this->getEngine()->assign('oModel', $this->getModel());
		
		
	}

	
	/**
	 * Gets the types correspoding to the root node
	 * 
	 * @return void
	 */
	function showgetRootList($inRootID) {
		//systemLog::message("view".$this->getModel()->setRootID($inRootID));
		$this->getModel()->setRootID($inRootID);
		$this->render($this->getTpl('typeList'));
	}
	
/*
	function showgetTypeList($inTypeID) {
		$this->getModel()->setRootID($inTypeID);
		$this->render($this->getTpl('typeList'));
	}
*/	
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('typeLeafList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('typeLeafForm');
	}
}