<?php
/**
 * errorHandler.class.php
 *
 * mvcDistributorPluginErrorHandler class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2011
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginErrorHandler
 * @version $Rev: 707 $
 */


/**
 * mvcDistributorPluginErrorHandler class
 *
 * Registers a site configured error Controller to the distributor.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginErrorHandler
 */
class mvcDistributorPluginErrorHandler extends mvcDistributorPlugin {

	/**
	 * Registers an alternative error controller for the site
	 *
	 * @return void
	 */
	function executeOnDispatcherInitialise() {
		if ( $this->getRequest()->getDistributor()->getSiteConfig()->getErrorController() ) {
			$controller = $this->getRequest()->getDistributor()->getSiteConfig()->getErrorController()->getParamValue();
		} else {
			$controller = 'mvcErrorController';
		}

		$this->getRequest()->getDistributor()->setOptions(
			array(
				mvcDistributorBase::OPTION_DISTRIBUTOR_ERROR_CONTROLLER => $controller,
			)
		);
	}
}