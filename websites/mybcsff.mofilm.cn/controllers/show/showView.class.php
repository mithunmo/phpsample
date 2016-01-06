<?php
/**
 * showView.class.php
 * 
 * showView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_bcsff.mofilm.cn
 * @subpackage controllers
 * @category showView
 * @version $Rev: 634 $
 */


/**
 * showView class
 * 
 * Provides the "showView" page
 * 
 * @package websites_bcsff.mofilm.cn
 * @subpackage controllers
 * @category showView
 */
class showView extends mvcView {

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
	 * Shows the showView page
	 *
	 * @return void
	 */
	function showShowPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('show'));
	}
	
	function showCompPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('comp'));
	}
	

	function showProcessPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('process'));
	}

	function showSuccessPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('success'));
	}
	
	
	function showEventPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('event'));
	}
	
	function showReebokPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('reebok'));
	}
	
	function showIntroPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('intro'));
	}
	
	
}