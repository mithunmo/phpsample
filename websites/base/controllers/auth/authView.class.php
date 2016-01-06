<?php
/**
 * authView.class.php
 * 
 * authView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category authView
 * @version $Rev: 634 $
 */


/**
 * authView class
 * 
 * Provides the "authView" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category authView
 */
class authView extends mvcView {

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
	 * Shows the authView page
	 *
	 * @return void
	 */
	function showAuthPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('auth'));
	}
}