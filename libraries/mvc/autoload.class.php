<?php
/**
 * mvcAutoload.class.php
 * 
 * mvcAutoload class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcAutoload
 * @version $Rev: 810 $
 */


/**
 * mvcAutoload class
 * 
 * mvcAutoload is an spl_autoload registerable class that provides autoloading
 * functionality for the MVC system. It is set by mvcDistributorBase when it is
 * included.
 * 
 * mvcAutoload works on Controller classes, attempting to locate via the controllerMap
 * the location of the Controller.class.php file within the site structure. By
 * loading the Controller, the View and Model and pre-assigned as the controllerName
 * plus either Model.class.php or View.class.php. Whether they are actually ever used
 * is irrelevant as the autoload only creates a file map and does not actually 
 * create the objects. In this way it is similar to the systemAutoload except there
 * are no cache files.
 * 
 * mvcAutoload has another feature: the ability to pre-load classes from the site
 * configuration (config.xml). Any classes specified will be pre-assigned to the
 * autoload array making them available within the sites MVC framework. In this manner
 * it is possible to create custom library files and not have to worry when instantiating
 * the objects.
 * 
 * As of Scorpio 0.3.0.5; mvcAutoload now builds the entire autoload map for the whole
 * site, including any custom classes.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcAutoload
 */
class mvcAutoload extends baseSet {
	
	/**
	 * Name of the autoload cache folder
	 *
	 * @var string
	 */
	const AUTOLOAD_CACHE_FOLDER = 'mvcAutoloadCache';
	
	/**
	 * Name of the autoload cache file
	 *
	 * @var string
	 */
	const AUTOLOAD_CACHE_FILE = '_autoload_cache.php';
	
	/**
	 * Name of the controllers folder, from the distributor
	 *  
	 * @var string
	 */
	const FOLDER_CONTROLLERS = 'controllers';
	
	/**
	 * The name of the libraries folder, from the distributor
	 * 
	 * @var string
	 */
	const FOLDER_LIBRARIES = 'libraries';
	
	/**
	 * Stores if class map has been built or not
	 * 
	 * @var boolean
	 */
	const MAP_BUILT = 'map.built';
	
	/**
	 * Stores an instance of mvcAutoload
	 *
	 * @var mvcAutoload
	 */
	private static $_Instance = false;
	
	/**
	 * Stores $_Request
	 *
	 * @var mvcRequest
	 * @access protected
	 */
	protected $_Request;
	
	/**
	 * Stores $_SiteConfig
	 *
	 * @var mvcSiteConfig
	 * @access protected
	 */
	protected $_SiteConfig;
	
	/**
	 * Stores $_OptionsSet
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_OptionsSet;
	
	
	
	/**
	 * Returns new mvcAutoload instance
	 *
	 * @param mvcRequest $inRequest
	 * @param mvcSiteConfig $inSiteConfig
	 * @param array $inOptions Array of options
	 * @return mvcAutoload
	 */
	function __construct(mvcRequest $inRequest, mvcSiteConfig $inSiteConfig, array $inOptions = array()) {
		$this->reset();
		$this->setRequest($inRequest);
		$this->setSiteConfig($inSiteConfig);
		$this->setOptions($inOptions);
		$this->initialise();
	}
	
	/**
	 * Initialises autoloader
	 * 
	 * @return void
	 */
	function initialise() {
		/*
		 * Load autoload cache data if required
		 */
		if ( $this->getSiteConfig()->isAutoloadCacheEnabled() ) {
			$this->load();
		}
		
		/*
		 * Set-up auto-save if required
		 */
		if ( $this->getSiteConfig()->isAutoloadCacheAutoSaveEnabled() ) {
			register_shutdown_function(array($this, 'save'));
		}
		
		/*
		 * Set-up instance
		 */
		self::$_Instance = $this;
		
		/*
		 * Register on SPL stack
		 */
		system::registerAutoloader('mvcAutoload::autoload');
	}
	
	
	
	/**
	 * Returns an instance of mvcAutoload
	 *
	 * @return mvcAutoload
	 * @access public
	 * @static 
	 */
	public static function getInstance() {
		if ( !self::$_Instance instanceof mvcAutoload ) {
			throw new mvcAutoloadException('Fatal error: mvcAutoload system has not been initialised');
		}
		return self::$_Instance;
	}
	
	/**
	 * Convenience method for spl_autoload registration
	 *
	 * @param string $inClassname
	 * @return boolean
	 * @static
	 */
	public static function autoload($inClassname) {
		return self::getInstance()->loadClass($inClassname);
	}
	
	/**
	 * Returns the current mvc autoload cache folder
	 *
	 * @return string
	 * @static
	 */
	public static function getCacheFolder() {
		return system::getConfig()->getPathTemp().system::getDirSeparator().self::AUTOLOAD_CACHE_FOLDER;
	}
	
	
	
	/**
	 * Attempts to autoload the class named $inClassname
	 *
	 * @param string $inClassname
	 * @return boolean
	 */
	function loadClass($inClassname) {
		/*
		 * Exclude Smarty files, they cause an error
		 */
		if ( !$inClassname || strpos($inClassname, 'Smarty') !== false ) {
			return false;
		}
				
		/*
		 * check autoload array first and load as required
		 */
		if ( array_key_exists($inClassname, $this->getClasses()) ) {
			return $this->_includeFile($inClassname);
		}
		
		if ( !array_key_exists(self::MAP_BUILT, $this->getClasses()) || !system::getConfig()->isProduction() ) {
			systemLog::info('Building class map for '.$this->getRequest()->getDistributorServerName());
			$map = $this->_generateClassMap();
			
			foreach ( $map as $class => $path ) {
				$this->setClassFile($class, $path);
			}
			
			$this->setClassFile(self::MAP_BUILT, true);

			return $this->_includeFile($inClassname);
		}
		
		/*
		 * Log an error only if there are 2 or less autoloaders which should be
		 * systemAutoload and mvcAutoload
		 */
		if ( count(spl_autoload_functions()) <= 2 ) {
			systemLog::critical(
				"Failed to locate $inClassname in mvc system from request: (".
				$this->getRequest()->getServerName().
				':'.
				$this->getRequest()->getRequestUri().')'
			);
		}

		return false;
	}
	
	/**
	 * Generates an array containing all classes required by the current site
	 * 
	 * The array is indexed by classname and contains the path to the class
	 * relative to the current websites folder.
	 * 
	 * @return array
	 */
	protected function _generateClassMap() {
		/*
		 * Attach custom library files
		 */
		$map = $this->_buildLibraryData();

		/*
		 * Now get controller data
		 */
		$map = $this->_buildAutoloadData(
			$this->getSiteConfig()->getControllerMapper()->getMapAsControllers(), $map
		);
		
		return $map;
	}
	
	/**
	 * Returns an array of mapped library files
	 * 
	 * @param array $inAutoloadMap
	 * @return array
	 */
	protected function _buildLibraryData(array $inAutoloadMap = array()) {
		foreach ( $this->getSiteConfig()->getSiteClasses()->getParamSet() as $oParam ) {
			$class = $oParam->getParamName();
			$filename = $oParam->getParamValue();
			
			$inAutoloadMap[$class] = $this->getSiteConfig()->getFilePath(
				$this->getLibrariesFolder().system::getDirSeparator().$filename
			);
		}
		return $inAutoloadMap;
	}
	
	/**
	 * Returns an array of mapped files to locations
	 *
	 * @param array $inControllers
	 * @param array $inAutoloadMap
	 * @param integer $inLevel
	 * @return array
	 */
	protected function _buildAutoloadData(array $inControllers = array(), array $inAutoloadMap = array(), $inLevel = 0) {
		if ( count($inControllers) > 0 ) {
			if ( false ) $oController = new mvcControllerMap();
			foreach ( $inControllers as $oController ) {
				$classPrefix = $oController->getName();
				$filePath = $this->getSiteConfig()->getFilePath(
					$this->getControllersFolder().system::getDirSeparator().$oController->getFilePath()
				);
				
				$inAutoloadMap[$classPrefix.'Controller'] = $filePath;
				$inAutoloadMap[$classPrefix.'Model'] = str_replace('Controller.class', 'Model.class', $filePath);
				$inAutoloadMap[$classPrefix.'View'] = str_replace('Controller.class', 'View.class', $filePath);
				
				if ( $oController->hasSubControllers() ) {
					$oController->addControllerToPath($oController->getController(), $inLevel);
					
					$inAutoloadMap = array_merge(
						$inAutoloadMap,
						$this->_buildAutoloadData(
							$oController->getSubControllers(), $inAutoloadMap, $inLevel+1
						)
					);
				}
			}
		}
		return $inAutoloadMap;
	}
	
	/**
	 * Includes the file from the autoload cache
	 *
	 * @param string $inClassname
	 * @return boolean
	 */
	protected function _includeFile($inClassname) {
		$filePath = $this->getClassFile($inClassname);

		if ( $filePath ) {
			if ( system::getConfig()->isProduction() ) {
				$res = @include_once($filePath);
			} else {
				$res = include_once($filePath);
			}
		} else {
			$res = false;
		}

		if ( $res ) {
			return true;
		} else {
			return false;
		}
	}
	
	
	
	/**
	 * Loads the previously cached autoload map
	 *
	 * @return boolean
	 */
	function load() {
		$cacheFile = $this->getCacheFile();
		if ( @file_exists($cacheFile) && @is_readable($cacheFile) ) {
			$cacheArray = include_once $cacheFile;
			if ( $cacheArray && is_array($cacheArray) && count($cacheArray) > 0 ) {
				$this->_setItem($cacheArray);
				$this->setModified(false);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Saves the autoload cache to the filesystem
	 *
	 * @return boolean
	 */
	function save() {
		if ( $this->isModified() ) {
			$cacheFile = $this->getCacheFile();
			$data = '<?php /* Auto-generated at '.date(DATE_COOKIE).' by '.__CLASS__.' */'.
				"\n".'return '.var_export($this->getClasses(), true).';';
			
			if ( !@file_exists(dirname($cacheFile)) ) {
				@mkdir(dirname($cacheFile), 0775, true);
			}
			
			if ( !file_exists($cacheFile) ) {
				@touch($cacheFile);
				@chmod($cacheFile, 0640);
			}
			
			if ( @is_writable($cacheFile) ) {
				$bytes = @file_put_contents($cacheFile, $data, LOCK_EX);
				systemLog::info("Updated autoload cache file ($cacheFile); wrote $bytes bytes to filesystem");
				return true;
			} else {
				systemLog::warning("mvcAutoload cache file not writable ($cacheFile)");
			}
			$this->setModified(false);
		}
		return false;
	}
	
	/**
	 * Deletes the cache record
	 *
	 * @return boolean
	 */
	function delete() {
		$cacheFile = $this->getCacheFile();
		if ( $cacheFile ) {
			if ( @unlink($cacheFile) ) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Resets the mvcAutoload array
	 *
	 * @return void
	 */
	function reset() {
		$this->_Request = null;
		$this->_SiteConfig = null;
		$this->_OptionsSet = null;
		parent::_resetSet();
	}
	
	

	/**
	 * Returns $_Request
	 *
	 * @return mvcRequest
	 * @access public
	 */
	function getRequest() {
		return $this->_Request;
	}
	
	/**
	 * Set $_Request to $inRequest
	 *
	 * @param mvcRequest $inRequest
	 * @return mvcAutoload
	 * @access public
	 */
	function setRequest($inRequest) {
		if ( $this->_Request !== $inRequest ) {
			$this->_Request = $inRequest;
		}
		return $this;
	}

	/**
	 * Returns $_SiteConfig
	 *
	 * @return mvcSiteConfig
	 */
	function getSiteConfig() {
		return $this->_SiteConfig;
	}
	
	/**
	 * Set $_SiteConfig to $inSiteConfig
	 *
	 * @param mvcSiteConfig $inSiteConfig
	 * @return mvcAutoload
	 */
	function setSiteConfig($inSiteConfig) {
		if ( $inSiteConfig !== $this->_SiteConfig ) {
			$this->_SiteConfig = $inSiteConfig;
		}
		return $this;
	}
	
	/**
	 * Returns the entire autoload array
	 *
	 * @return array
	 */
	function getClasses() {
		return $this->_getItem();
	}
	
	/**
	 * Returns the file path for $inClassname
	 *
	 * @param string $inClassname
	 * @return string
	 */
	function getClassFile($inClassname) {
		return $this->_getItem($inClassname);
	}
	
	/**
	 * Sets the class path for $inClassname
	 *
	 * @param string $inClassname
	 * @param string $inClasspath
	 * @return systemAutoload
	 */
	function setClassFile($inClassname, $inClasspath) {
		return $this->_setItem($inClassname, $inClasspath);
	}
	
	/**
	 * Returns the full path to the autoload cache file
	 *
	 * @return string
	 * @access private
	 */
	private function getCacheFile() {
		$path = self::getCacheFolder().system::getDirSeparator();
		
		$filename =
			$this->getRequest()->getDistributorServerName().
			self::AUTOLOAD_CACHE_FILE;
		
		return $path.$filename;
	}
	
	
	
	/**
	 * Returns $_OptionsSet
	 *
	 * @return baseOptionsSet
	 */
	function getOptionsSet() {
		if ( !$this->_OptionsSet instanceof baseOptionsSet ) {
			$this->_OptionsSet = new baseOptionsSet();
		}
		return $this->_OptionsSet;
	}
	
	/**
	 * Set $_OptionsSet to $inOptionsSet
	 *
	 * @param baseOptionsSet $inOptionsSet
	 * @return mvcAutoload
	 */
	function setOptionsSet(baseOptionsSet $inOptionsSet) {
		if ( $inOptionsSet !== $this->_OptionsSet ) {
			$this->_OptionsSet = $inOptionsSet;
		}
		return $this;
	}
	
	/**
	 * Returns the option value for $inOption, or $inDefault if not found
	 * 
	 * @param string $inOption
	 * @param mixed $inDefault
	 * @return mixed
	 */
	function getOption($inOption, $inDefault = null) {
		return $this->getOptionsSet()->getOptions($inOption, $inDefault);
	}
	
	/**
	 * Sets a single option $inOption to $inValue
	 * 
	 * @param string $inOption
	 * @param mixed $inValue
	 * @return mvcAutoload
	 */
	function setOption($inOption, $inValue) {
		$this->getOptionsSet()->setOptions(array($inOption => $inValue));
		return $this;
	}
	
	/**
	 * Sets an array of options
	 * 
	 * @param array $inOptions
	 * @return mvcAutoload
	 */
	function setOptions(array $inOptions) {
		$this->getOptionsSet()->setOptions($inOptions);
		return $this;
	}

	/**
	 * Returns the name of the controllers folder
	 *
	 * @return string
	 */
	function getControllersFolder() {
		return $this->getOption(self::FOLDER_CONTROLLERS, 'controllers');
	}
	
	/**
	 * Returns the name of the libraries folder
	 *
	 * @return string
	 */
	function getLibrariesFolder() {
		return $this->getOption(self::FOLDER_LIBRARIES, 'libraries');
	}
}