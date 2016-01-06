<?php
/**
 * view.class.php
 *
 * mvcStaticView class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcStaticView
 * @version $Rev: 764 $
 */


/**
 * mvcStaticView
 *
 * Handles displaying static pages and caching those pages.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcStaticView
 */
class mvcStaticView extends mvcView {
	
	/**
	 * Shows the mvcStaticView page
	 *
	 * @return void
	 */
	function showPage() {
		$page = $this->getModel()->getPageName();
		try {
			$this->getTpl($page);
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
		if ( !$this->isCached($this->getTpl($page), $cacheID) ) {
			$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		}
		$this->render($this->getTpl($page), $cacheID);
	}
}