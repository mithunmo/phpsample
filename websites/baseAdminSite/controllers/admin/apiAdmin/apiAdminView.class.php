<?php
/**
 * apiAdminView.class.php
 * 
 * apiAdminView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category apiAdminView
 * @version $Rev: 634 $
 */


/**
 * apiAdminView class
 * 
 * Provides the "apiAdminView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category apiAdminView
 */
class apiAdminView extends mvcView {

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
	 * Shows the apiAdminView page
	 *
	 * @return void
	 */
	function showApiAdminPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('apiAdmin'));
	}
}