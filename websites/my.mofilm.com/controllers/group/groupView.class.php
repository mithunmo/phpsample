<?php
/**
 * groupView.class.php
 * 
 * groupView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category groupView
 * @version $Rev: 11 $
 */


/**
 * groupView class
 * 
 * Provides the "groupView" page
 * 
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category groupView
 */
class groupView extends mvcView {

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
	 * Shows the groupView page
	 *
	 * @return void
	 */
	function showGroupPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('group'));
	}
}