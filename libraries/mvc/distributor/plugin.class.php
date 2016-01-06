<?php
/**
 * plugin.class.php
 * 
 * mvcDistributorPlugin class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPlugin
 * @version $Rev: 707 $
 */


/**
 * mvcDistributorPlugin class
 * 
 * Base class providing shared logic for distributor plugins. Distributor plugins
 * provide a means to hook in additional logic pre and post dispatch. The execute
 * methods are all concrete so only the ones that need an implmentation need
 * completing.
 * 
 * There are currently four events that can hooked into:
 * <ul>
 *   <li>executeOnDispatcherInitialise</li>
 *   <li>executePreDispatch</li>
 *   <li>executePostDispatch</li>
 *   <li>executeOnShutdown</li>
 * </ul>
 * 
 * <b>executeOnDispatcherInitialise</b>
 * This is executed during the distributors initialise phase. Any plugin that needs
 * to be created or to modify the environment before the request is handled should
 * implement this method.
 * 
 * For example: {@link mvcDistributorPluginSession} uses this hook so that the session
 * is available before the request is dispatched. Similarly {@link mvcDistributorPluginLog}
 * sets up per site logging.
 * 
 * <b>executePreDispatch</b>
 * This is executed immediately before the controller is resolved and launched but afer
 * the distributor is initialised. This hook is used by {@link mvcDistributorPluginDispatchTimer}
 * to time how long the request takes to process.
 * 
 * <b>executePostDispatch</b>
 * This is executed immediately after the controller has been launched but before the
 * distributor has finished. This can be used to set-up information for the next
 * request or to do immediate clean-up. {@link mvcDistributorPluginDispatchTimer} uses this
 * hook to record the time taken by the request.
 * 
 * <b>executeOnShutdown</b>
 * This is executed at the very end of the request as the distributor ends the request.
 * It is used by {@link mvcDistributorPluginMemTracker} to track the total memory usage
 * during the request.
 * 
 * 
 * <b>Using Distributor Plugins</b>
 * 
 * Plugins can be either expressly set by calling the registerPlugin method on {@link mvcDistributorPluginSet}
 * (accessed via {@link mvcDistributorBase::getPluginSet()}) or via the sites config.xml file.
 * If using the config file, then a new section called "distributorPlugins" should be
 * added and then each plugin specified in the order they should be added. Note that
 * some plugins may require others be loaded before they can be (e.g. detectDevice requires
 * the session be ready).
 * 
 * <code>
 * // example load log and session in the site
 * <section name="distributorPlugins" override="1">
 *     <option name="mvcDistributorPluginLog" value="true" override="1" />
 *     <option name="mvcDistributorPluginSession" value="true" override="1" />
 * </section>
 * </code>
 * 
 * Plugins are inherited through the site hierarchy. To disable a plugin create the
 * section in the site and set the value to "false" or "0".
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPlugin
 */
abstract class mvcDistributorPlugin {
	
	/**
	 * Stores $_Request
	 *
	 * @var mvcRequest
	 * @access protected
	 */
	protected $_Request;
	
	
	
	/**
	 * Creates a new distributor plugin, requires request and the type of plugin
	 *
	 * @param mvcRequest $inMvcRequest
	 */
	function __construct($inMvcRequest = null) {
		$this->reset();
		if ( $inMvcRequest !== null && $inMvcRequest instanceof mvcRequest ) {
			$this->setRequest($inMvcRequest);
		}
	}
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_Request = null;
	}
	
	
	
	/**
	 * Executes the plugin at dispatcher initialisation
	 *
	 * @return void
	 * @abstract 
	 */
	function executeOnDispatcherInitialise() {
		
	}
	
	/**
	 * Executes the plugin in pre-dispatch phase
	 *
	 * @return void
	 * @abstract 
	 */
	function executePreDispatch() {
		
	}
	
	/**
	 * Executes the plugin in post-dispatch phase
	 *
	 * @return void
	 * @abstract 
	 */
	function executePostDispatch() {
		
	}
	
	/**
	 * Executes the plugin in shutdown phase
	 *
	 * @return void
	 * @abstract
	 */
	function executeOnShutdown() {
		
	}
	
	
	
	/**
	 * Returns the current mvcRequest object
	 *
	 * @return mvcRequest
	 */
	function getRequest() {
		return $this->_Request;
	}
	
	/**
	 * Set the mvcRequest instance
	 *
	 * @param mvcRequest $inRequest
	 * @return mvcDistributorPlugin
	 */
	function setRequest(mvcRequest $inRequest) {
		if ( $inRequest !== $this->_Request ) {
			$this->_Request = $inRequest;
		}
		return $this;
	}
	
	/**
	 * Returns the log folder location for the current site
	 *
	 * @return string
	 * @access private
	 */
	protected function _getLogFolder() {
		return system::getConfig()->getPathLogs().
			system::getDirSeparator().
			'websites'.
			system::getDirSeparator().
			$this->getRequest()->getDistributorServerName().
			system::getDirSeparator(); 
	}
}