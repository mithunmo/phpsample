<?php
/**
 * termsView.class.php
 * 
 * termsView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category termsView
 * @version $Rev: 11 $
 */


/**
 * termsView class
 * 
 * Provides the "termsView" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category termsView
 */
class termsView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		if ( system::getConfig()->isProduction() ) {
			$this->setCacheLevelMedium();
		} else {
			$this->setCacheLevelNone();
		}
	}
	
	/**
	 * Shows the termsView page
	 *
	 * @return void
	 */
	function showTermsPage() {
		$cacheId = 'terms_'.$this->getModel()->getTermsID();

		if ( !$this->isCached($this->getTpl('terms'), $cacheId) ) {
			$this->getEngine()->assign('oTerms', utilityOutputWrapper::wrap($this->getModel()->getTerms()));
		}
		
		$this->render($this->getTpl('terms'), $cacheId);
	}

	/**
	 * Renders an events terms
	 *
	 * @return void
	 */
	function showEventTermsPage() {
		$cacheId = 'terms_event_'.$this->getModel()->getEventID();

		if ( !$this->isCached($this->getTpl('termsEvent'), $cacheId) ) {
			$this->getEngine()->assign('oEvent', utilityOutputWrapper::wrap($this->getModel()->getEvent()));
		}

		$this->render($this->getTpl('termsEvent'), $cacheId);
	}

	/**
	 * Renders a sources terms
	 *
	 * @return void
	 */
	function showSourceTermsPage() {
		$cacheId = 'terms_source_'.$this->getModel()->getSourceID();

		if ( !$this->isCached($this->getTpl('termsSource'), $cacheId) ) {
			$this->getEngine()->assign('oSource', utilityOutputWrapper::wrap($this->getModel()->getSource()));
		}

		$this->render($this->getTpl('termsSource'), $cacheId);
	}
}