<?php
/**
 * locale.class.php
 * 
 * mvcDistributorPluginLocale class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginLocale
 * @version $Rev: 707 $
 */


/**
 * mvcDistributorPluginLocale class
 * 
 * Sets the locale for the current site attempting to auto-locate it.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginLocale
 */
class mvcDistributorPluginLocale extends mvcDistributorPlugin {
	
	/**
	 * Sets up locale
	 *
	 * @return void
	 */
	function executeOnDispatcherInitialise() {
		$this->getRequest()->setLocale();
	}
}