<?php
/**
 * trackView.class.php
 * 
 * trackView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category trackView
 * @version $Rev: 634 $
 */


/**
 * trackView class
 * 
 * Provides the "trackView" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category trackView
 */
class trackView extends mvcView {

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
	 * Shows the trackView page
	 *
	 * @return void
	 */
	function showTrackPage() {
		$this->setCacheLevelNone();		
		$this->render($this->getTpl('track'));
	}

	/**
	 * Shows the unsubscription page
	 *
	 * @return void
	 */
	function showUnsubscriptionPage() {
		$this->render($this->getTpl('unsubscribe'));
	}
}