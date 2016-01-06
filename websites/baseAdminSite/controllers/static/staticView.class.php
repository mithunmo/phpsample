<?php
/**
 * staticView.class.php
 *
 * staticView class
 *
 * @author Dave Redfern
 * @copyright Mofilm Ltd. (c) 2009-2010
 * @package mofilm
 * @subpackage websites_baseAdminSite_controllers
 * @category staticView
 */


/**
 * staticView class
 *
 * Provides the "staticView" page
 *
 * @package mofilm
 * @subpackage websites_baseAdminSite_controllers
 * @category staticView
 */
class staticView extends mvcView {

	/**
	 * Shows the staticView page
	 *
	 * @return void
	 */
	function showPage() {
		$page = $this->getModel()->getPageName();
		if ( $this->getTpl($page) == false ) {
			$page = 'static';
		} else {
			systemLog::info("Using static page ($page) as render target");
		}

		$cacheID = $this->getModel()->getPageName();

		if ( $this->getController()->getRequest()->getDistributor()->getSiteConfig()->getParentParam('site','cacheStaticPages', true)->getParamValue() ) {
			$this->setCacheLevelHigh();
		} else {
			$this->setCacheLevelNone();
		}
		if ( !$this->isCached($this->getTpl($page), $cacheID) ) {
			$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		}
		$this->render($this->getTpl($page), $cacheID);
	}
}