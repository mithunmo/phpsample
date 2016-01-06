<?php
/**
 * videoPlatformView.class.php
 * 
 * videoPlatformView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_my.mofilm.in
 * @subpackage controllers
 * @category videoPlatformView
 * @version $Rev: 634 $
 */


/**
 * videoPlatformView class
 * 
 * Provides the "videoPlatformView" page
 * 
 * @package websites_my.mofilm.in
 * @subpackage controllers
 * @category videoPlatformView
 */
class videoPlatformView extends mvcView {

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
	 * Shows the videoPlatformView page
	 *
	 * @return void
	 */
	function showVideoPlatformPage() {
		$this->setCacheLevelNone();
		$this->getEngine()->assign('oResult', $this->getModel()->getMovieDetails());
		$this->render($this->getTpl('videoPlatform'));
	}
}