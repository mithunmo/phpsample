<?php
/**
 * mvcDistributorPluginErrorHandler.class.php
 *
 * mvcDistributorPluginErrorHandler class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package baseAdminSite
 * @subpackage websites_baseAdminSite_libraries
 * @category mvcDistributorPluginErrorHandler
 * @version $Rev: 11 $
 */


/**
 * mvcDistributorPluginErrorHandler class
 *
 * Registers the mofilmErrorController with the distributor.
 * 
 * @todo DR: make this generic and load from config and port back to Scorpio.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginErrorHandler
 */
class mvcDistributorPluginErrorHandler extends mvcDistributorPlugin {

	/**
	 * Registers a translation adaptor pre-dispatch
	 *
	 * @return void
	 */
	function executeOnDispatcherInitialise() {
		$this->getRequest()->getDistributor()->setOptions(
			array(
				mvcDistributorBase::OPTION_DISTRIBUTOR_ERROR_CONTROLLER => 'mofilmErrorController',
			)
		);
	}
}