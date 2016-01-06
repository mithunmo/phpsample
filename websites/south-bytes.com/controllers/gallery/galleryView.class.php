<?php
/**
 * galleryView.class.php
 * 
 * galleryView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_south-bytes.com
 * @subpackage controllers
 * @category galleryView
 * @version $Rev: 11 $
 */


/**
 * galleryView class
 * 
 * Provides the "galleryView" page
 * 
 * @package websites_south-bytes.com
 * @subpackage controllers
 * @category galleryView
 */
class galleryView extends mvcView {

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
	 * Shows the galleryView page
	 *
	 * @return void
	 */
	function showGalleryPage() {
		if ( system::getConfig()->isProduction() ) {
			$this->setCacheLevelLow();
		} else {
			$this->setCacheLevelNone();
		}

		if ( !$this->isCached($this->getTpl('gallery')) ) {
			$this->getEngine()->assign('oLatest', utilityOutputWrapper::wrap($this->getModel()->getLatestAdditions()));
			$this->getEngine()->assign('oTopRated', utilityOutputWrapper::wrap($this->getModel()->getTopRated()));
		}
		$this->render($this->getTpl('gallery'));
	}
}