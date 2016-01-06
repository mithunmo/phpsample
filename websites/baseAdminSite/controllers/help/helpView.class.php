<?php
/**
 * helpView.class.php
 * 
 * helpView class
 *
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category helpView
 * @version $Rev: 634 $
 */


/**
 * helpView class
 * 
 * Provides the "helpView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category helpView
 */
class helpView extends mvcView {

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
	 * Shows the helpView page
	 *
	 * @return void
	 */
	function showHelpPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('help'));
	}
}