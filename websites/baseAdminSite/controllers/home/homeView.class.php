<?php
/**
 * homeView.class.php
 * 
 * homeView class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage websites_base_controllers
 * @category homeView
 */


/**
 * homeView class
 * 
 * Provides the "home" page defaults
 * 
 * @package scorpio
 * @subpackage websites_base_controllers
 * @category homeView
 */
class homeView extends mvcView {

	/**
	 * @see mvcViewBase::__construct()
	 */
	function __construct($inController) {
		parent::__construct($inController) ;
	
	}
	
	/**
	 * Shows the home page
	 *
	 * @return void
	 */
	function showHomePage() {
		if ( system::getConfig()->isProduction() ) {
			$this->setCacheLevelLow();
		} else {
			$this->setCacheLevelNone();
		}
		
		$cacheId = 'home';
		if ( !$this->isCached($this->getTpl('home', '/home'), $cacheId) ) {
			$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		}
		$this->render($this->getTpl('home', '/home'), $cacheId);
	}
}