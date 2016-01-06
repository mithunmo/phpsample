<?php
/**
 * staticView.class.php
 *
 * staticView class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage websites_base_controllers
 * @category staticView
 */


/**
 * staticView class
 *
 * Provides the "staticView" page
 *
 * @package scorpio
 * @subpackage websites_base_controllers
 * @category staticView
 */
class staticView extends mvcView {

	/**
	 * @see mvcViewBase::__construct()
	 */
	function __construct($inController) {
		parent::__construct($inController);
	}

	/**
	 * Shows the staticView page
	 *
	 * @return void
	 */
	function showPage() {
		$page = $this->getModel()->getPageName();
		try {
			$this->getTpl($page, '/static');
			systemLog::info("Using static page ($page) as render target");
		} catch ( mvcViewInvalidTemplateException $e ) {
			$page = 'static';
		}

		$cacheID = $this->getModel()->getPageName();

		if ( $this->getController()->getRequest()->getDistributor()->getSiteConfig()->getParentParam('site','cacheStaticPages', true)->getParamValue() ) {
			$this->setCacheLevelHigh();
		} else {
			$this->setCacheLevelNone();
		}
		if ( !$this->isCached($this->getTpl($page, '/static'), $cacheID) ) {
			$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		}
		$this->render($this->getTpl($page, '/static'), $cacheID);
	}
}