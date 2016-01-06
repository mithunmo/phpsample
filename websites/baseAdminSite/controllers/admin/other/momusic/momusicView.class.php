<?php
/**
 * momusicView.class.php
 * 
 * momusicView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category momusicView
 * @version $Rev: 634 $
 */


/**
 * momusicView class
 * 
 * Provides the "momusicView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category momusicView
 */
class momusicView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		/*
		 * Add any further custom setup for the view that is needed on every request
		 */
	}
	
	/**
	 * Shows the momusicView page
	 *
	 * @return void
	 */
	function showMomusicPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('momusic'));
	}
}