<?php
/**
 * detectDevice.class.php
 * 
 * mvcDistributorPluginDetectDevice class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginDetectDevice
 * @version $Rev: 650 $
 */


/**
 * mvcDistributorPluginDetectDevice class
 * 
 * Handles device look-ups and will log the deviceID into the session.
 * Requires that a session already be started.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginDetectDevice
 */
class mvcDistributorPluginDetectDevice extends mvcDistributorPlugin {
	
	/**
	 * Registers a deviceID pre-dispatch
	 *
	 * @return void
	 */
	function executeOnDispatcherInitialise() {
		if ( !class_exists('mvcDistributorPluginSession') ) {
			throw new mvcDistributorException('detectDevice requires session to be loaded before device plugin');
		}
		
		if ( !$this->getRequest()->getSession()->getParam('request.device') || isset($_REQUEST['userAgent']) ) {
			if ( isset($_REQUEST['userAgent']) ) {
				$userAgent = strip_tags(trim(stripslashes(urldecode($_REQUEST['userAgent']))));
			} else {
				$userAgent = false;
				if ( isset($_SERVER['X-OperaMini-Phone-UA']) ) {
					// WAP downloaded Opera Mini instance
					$userAgent = strip_tags(trim(stripslashes(urldecode($_SERVER['X-OperaMini-Phone-UA']))));
				}
				if ( !$userAgent && isset($_SERVER['HTTP_X_DEVICE_USER_AGENT']) ) {
					// Novarra forwarded user-agent e.g. un-whitelisted Vodafone handset
					$userAgent = strip_tags(trim(stripslashes(urldecode($_SERVER['HTTP_X_DEVICE_USER_AGENT']))));
				}
				if ( !$userAgent && isset($_SERVER['HTTP_USER_AGENT']) ) {
					// Normal UA, hopefully not modified
					$userAgent = strip_tags(trim(stripslashes(urldecode($_SERVER['HTTP_USER_AGENT']))));
				}
			}
			
			$oDevice = wurflManager::getInstanceByUserAgent($userAgent);
			if ( $oDevice->getDeviceID() > 0 ) {
				$this->getRequest()->getSession()->setParam('request.device', $oDevice->getDeviceID());
			}
		}
	}
}