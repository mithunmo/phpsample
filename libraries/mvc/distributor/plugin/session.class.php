<?php
/**
 * session.class.php
 * 
 * mvcDistributorPluginSession class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginSession
 * @version $Rev: 707 $
 */


/**
 * mvcDistributorPluginSession class
 * 
 * Handles registering a session on distributor start-up. Expects that a 
 * class named "mvcSession" can be loaded from the sites libraries folder.
 * This mvcSession class should define the session name or the session
 * save methods etc.
 * 
 * The session is registered with the request once created.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginSession
 */
class mvcDistributorPluginSession extends mvcDistributorPlugin {
	
	/**
	 * Registers a session pre-dispatch
	 *
	 * @return void
	 */
	function executeOnDispatcherInitialise() {
		$this->getRequest()->setSession(new mvcSession($this->getRequest()));
	}
}