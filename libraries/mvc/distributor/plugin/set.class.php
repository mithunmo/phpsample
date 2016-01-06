<?php
/**
 * set.class.php
 * 
 * mvcDistributorPluginSet class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginSet
 * @version $Rev: 650 $
 */


/**
 * mvcDistributorPluginSet class
 * 
 * Stores the registered distributor plugins
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginSet
 */
class mvcDistributorPluginSet extends baseSet {
	
	/**
	 * Stores $_Request
	 *
	 * @var mvcRequest
	 * @access protected
	 */
	protected $_Request;
	
	
	
	/**
	 * Creates a new plugin set
	 *
	 * @param mvcRequest $inMvcRequest
	 */
	function __construct(mvcRequest $inMvcRequest) {
		$this->reset();
		$this->setRequest($inMvcRequest);
	}
	
	/**
	 * Loads the registered plugins from the site config
	 *
	 * @return boolean
	 */
	function load() {
		$plugins = $this->getRequest()->getDistributor()->getSiteConfig()->getParentSection('distributorPlugins');
		if ( $plugins instanceof systemConfigSection && $plugins->getParamSet()->getCount() > 0 ) {
			foreach ( $plugins->getParamSet() as $oParam ) {
				$class = $oParam->getParamName();
				$register = $oParam->getParamValue();
				if ( $register ) {
					$this->registerPlugin(new $class($this->getRequest()));
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_Request = null;
		parent::_resetSet();
		$this->setModified(false);
	}
	
	/**
	 * Register a new plugin into the set, optionally with $inPriority which
	 * affects the position in the plugin set
	 *
	 * @param mvcDistributorPlugin $inPlugin
	 * @param integer $inPriority
	 * @return mvcDistributorPluginSet
	 */
	function registerPlugin(mvcDistributorPlugin $inPlugin, $inPriority = null) {
		if ( $this->isPluginRegistered($inPlugin) ) {
			throw new mvcDistributorException('Plugin '.get_class($inPlugin).' has already been registered');
		}
		if ( $inPriority !== null && $this->_itemKeyExists($inPriority) ) {
			throw new mvcDistributorException('Plugin has already been assigned with priority '.$inPriority);
		}
		
		if ( !$inPlugin->getRequest() ) {
			$inPlugin->setRequest($this->getRequest());
		}
		
		if ( $inPriority === null ) {
			if ( $this->getCount() == 0 ) {
				$inPriority = 1;
			} else {
				$inPriority = max(array_keys($this->_getItem()))+1;
			}
		}
		
		return $this->_setItem($inPriority, $inPlugin);
	}
	
	/**
	 * Removes the plugin from the set
	 *
	 * @param mvcDistributorPlugin $inPlugin
	 * @return mvcDistributorPluginSet
	 */
	function unregisterPlugin($inPlugin) {
		if ( $this->getCount() > 0 ) {
			if ( $inPlugin instanceof mvcDistributorPlugin ) {
				$inPlugin = get_class($inPlugin);
			}
			foreach ( $this as $key => $oPlugin ) {
				if ( get_class($oPlugin) == $inPlugin ) {
					$this->_removeItem($key);
					break;
				}
			}
		}
		return $this;
	}
	
	/**
	 * Returns true if the plugin named $inPluginName has been registered
	 *
	 * @param string $inPluginName
	 * @return boolean
	 */
	function isPluginRegistered($inPluginName) {
		if ( $this->getCount() > 0 ) {
			if ( $inPluginName instanceof mvcDistributorPlugin ) {
				$inPluginName = get_class($inPluginName);
			}
			foreach ( $this as $key => $oPlugin ) {
				if ( get_class($oPlugin) == $inPluginName ) {
					return true;
				}
			}
		}
		return false;
	}
	
	
	
	/**
	 * Executes the plugins at dispatcher initialisation
	 *
	 * @return void
	 */
	function executeOnDispatcherInitialise() {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oPlugin = new mvcDistributorPlugin;
			foreach ( $this as $oPlugin ) {
				$oPlugin->executeOnDispatcherInitialise();
			}
		}
	}
	
	/**
	 * Executes the plugins in pre-dispatch phase
	 *
	 * @return void
	 */
	function executePreDispatch() {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oPlugin = new mvcDistributorPlugin;
			foreach ( $this as $oPlugin ) {
				$oPlugin->executePreDispatch();
			}
		}
	}
	
	/**
	 * Executes the plugins in post-dispatch phase
	 *
	 * @return void
	 */
	function executePostDispatch() {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oPlugin = new mvcDistributorPlugin;
			foreach ( $this as $oPlugin ) {
				$oPlugin->executePostDispatch();
			}
		}
	}

	/**
	 * Executes the plugins in shutdown phase
	 *
	 * @return void
	 */
	function executeOnShutdown() {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oPlugin = new mvcDistributorPlugin;
			foreach ( $this as $oPlugin ) {
				$oPlugin->executeOnShutdown();
			}
		}
	}
	
	

	/**
	 * Returns $_Request
	 *
	 * @return mvcRequest
	 */
	function getRequest() {
		return $this->_Request;
	}
	
	/**
	 * Set $_Request to $inRequest
	 *
	 * @param mvcRequest $inRequest
	 * @return mvcDistributorPluginSet
	 */
	function setRequest($inRequest) {
		if ( $inRequest !== $this->_Request ) {
			$this->_Request = $inRequest;
			$this->setModified();
		}
		return $this;
	}
}