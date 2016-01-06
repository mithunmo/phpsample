<?php
/**
 * linkView.class.php
 * 
 * linkView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category linkView
 * @version $Rev: 11 $
 */


/**
 * linkView class
 * 
 * Provides the "linkView" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category linkView
 */
class linkView extends mvcView {

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
	 * Shows the linkView page
	 *
	 * @return void
	 */
	function showLinkPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('link'));
	}
}