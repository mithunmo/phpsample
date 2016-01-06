<?php
/**
 * xmlView.class.php
 * 
 * xmlView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category xmlView
 * @version $Rev: 11 $
 */


/**
 * xmlView class
 * 
 * Provides the "xmlView" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category xmlView
 */
class xmlView extends mvcView {

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
	 * Shows the xmlView page
	 *
	 * @return void
	 */
	function showXmlPage() {
		$this->getRequest()->setOutputType(mvcRequest::OUTPUT_XML);
		
		if ( system::getConfig()->isProduction() ) {
			$this->setCacheLevelLow();
		} else {
			$this->setCacheLevelNone();
		}
		
		$key = 'xml_'.$this->getModel()->getMovieID();
		if ( !$this->isCached($this->getTpl('xml'), $key) ) {
			$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
			$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));
		}
		
		$this->render($this->getTpl('xml'), $key);
	}
}