<?php
/**
 * searchView.class.php
 *
 * searchView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category searchView
 * @version $Rev: 11 $
 */


/**
 * searchView class
 *
 * Provides the "searchView" page
 *
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category searchView
 */
class searchView extends mvcView {

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
	 * Shows the searchView page
	 *
	 * @return void
	 */
	function showSearchPage() {
		$this->setCacheLevelNone();

		$this->render($this->getTpl('search'));
	}
	
	/**
	 * Shows the search results page
	 * 
	 * @return void
	 */
	function showSearchResults() {
		//$this->setCacheLevelLow();
		$this->setCacheLevelNone();
		
		$hash = md5(serialize($this->getModel()));
		if ( !$this->isCached($this->getTpl('results'), $hash) ) {
			$this->getEngine()->assign('jsonCallback', $this->getModel()->getJsonCallback());
			$this->getEngine()->assign('oResults', utilityOutputWrapper::wrap($this->getModel()->search()));
		}
		
		$this->render($this->getTpl('results'), $hash);
	}
}