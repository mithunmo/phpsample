<?php
/**
 * mvcDistributorBase.class.php
 * 
 * mvcDistributorBase class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorBase
 * @version $Rev: 821 $
 */


/**
 * mvcDistributorBase class
 * 
 * Abstract mvcDistributorBase class. Provides base functionality for the MVC system.
 * This is the main Front Controller class that will dispatch a request to an appropriate
 * controller within the site hierarchy.
 * 
 * The main functions of this class are to prepare the request, create or continue a
 * session, determine the request type (or context if you prefer) and to then dispatch
 * the request to the most appropriate controller as defined in the controllerMap XML
 * config file.
 * 
 * A concrete implementation is required of the dispatch() method.
 * 
 * The basic process flow of mvcDistributorBase is as follows:
 * 
 * <ol>
 * <li>Request passed to new distributor instance</li>
 * <li>mvcAutoload registered to spl_autoload for handling controllers</li>
 * <li>Resolve the site and hence the site config</li>
 * <li>Execute plugins {@link mvcDistributorPlugin::executeOnDispatcherInitialise()} event</li>
 * <li>Execute main dispatch method</li>
 * <li>Execute plugins {@link mvcDistributorPlugin::executePreDispatch()} event</li>
 * <li>Resolve the path to the most appropriate controller</li>
 * <li>Load the controller object</li>
 * <li>Validate request action against controller</li>
 * <li>Authorise request action against controller</li>
 * <li>Launch controller</li>
 * <li>Execute plugins {@link mvcDistributorPlugin::executePostDispatch()} event</li>
 * <li>Shutdown</li>
 * <li>Execute plugins {@link mvcDistributorPlugin::executeOnShutdown()} event</li>
 * </ol>
 * 
 * Scorpio is designed to work with multiple sites with a shared code-base. Each site can
 * inherit from a base set of functionality with specific components being overridden.
 * Which site to use is determined by {@link mvcDistributorBase::resolveSitePath()}.
 * This method will hunt through the /websites folder attempting to match the request
 * domain to a folder entry, first by specific domain e.g. www.madagasgar.com and 
 * then by component separated by each . e.g. madagasgar.com. In this way, all requests
 * for pages can be directed to a domain, and then content switched on a sub-domain.
 * For example: all requests to wap.madagasgar.com would be serviced using the 
 * madagasgar.com site, but wap. would result in WAP content being returned to the user.
 * 
 * This basic functionality can always be overridden by appending /as.TYPE to the URI
 * request. These requests will then be filtered out and the alternative type returned.
 * Supported out-put types include: HTML (XHTML for web), XHTML (for mobile), WML (for mobile),
 * RSS, XML, ATOM, JSON and JavaScript. WAP requests are auto-negotiated for MXHTML or
 * WML by accept-type look-up and user-agent negotiation. All XHTML Mobile requests are
 * served using application/xhtml+xml as this is the mandated content-type by the network 3.
 * 
 * Once resolved, the site config.xml file is loaded into the mvcSiteConfig object. This
 * contains theme and custom library information. This is processed before the request as
 * the specific session and view classes must be pre-loaded before dispatch can continue.
 * 
 * For request routing please see {@link mvcControllerMapper}.
 * 
 * Some additional parameters of the distributor can be configured by defining constants
 * before the base class is included. These constants control where the site folder is,
 * what the controllers folder is called, the name of the custom libraries folder, views
 * and the default action to be fired when no action can be located in the request e.g.
 * launching /home or /index when the request is simply /.
 * 
 * The loading of the various controllers is handled via a custom spl_autoload class
 * {@link mvcAutoload}. This will pre-load and fetch controllers, models and views for
 * the request based on the site controllerMap.xml file.
 * 
 * The distributor has several options that can be specified during instantiation:
 * 
 * <b>mvcDistributorBase::OPTION_DISTRIBUTOR_WEBSITES_FOLDER</b>
 * The full path to the main websites folder, defaults to /websites.
 * 
 * <b>mvcDistributorBase::OPTION_DISTRIBUTOR_WEBSITE_CONTROLLERS_FOLDER</b>
 * The name of the controllers folder, defaults to "controllers". This is where
 * the controllers can be located.
 * 
 * <b>mvcDistributorBase::OPTION_DISTRIBUTOR_WEBSITE_DEFAULT_CONTROLLER</b>
 * The name of the default controller, defaults to "home". If no request is
 * specified then this controller will be used.
 * 
 * <b>mvcDistributorBase::OPTION_DISTRIBUTOR_WEBSITE_LIBRARIES_FOLDER</b>
 * The name of the libraries folder, defaults to "libraries". The libraries folder
 * contains the mvcController, mvcSession and mvcView classes along with other
 * custom classes for the site.
 * 
 * <b>mvcDistributorBase::OPTION_DISTRIBUTOR_WEBSITE_VIEWS_FOLDER</b>
 * The name of the views folder, defaults to "views". The views are the templates
 * to be used for the site.
 * 
 * <b>mvcDistributorBase::OPTION_DISTRIBUTOR_ERROR_CONTROLLER</b>
 * Name of class implementing {@link mvcErrorInterface}, defaults to mvcErrorController.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorBase
 */
abstract class mvcDistributorBase {
	
	/**
	 * Stores $_Options
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_Options;
	
	/**
	 * The full path to the main websites folder, defaults to /websites
	 * 
	 * @var string
	 */
	const OPTION_DISTRIBUTOR_WEBSITES_FOLDER = 'distributor.websites.folder';
	/**
	 * The name of the controllers folder, default controllers
	 * 
	 * @var string
	 */
	const OPTION_DISTRIBUTOR_WEBSITE_CONTROLLERS_FOLDER = 'distributor.website.constrollers.folder';
	/**
	 * The name of the default controller, default home
	 * 
	 * @var string
	 */
	const OPTION_DISTRIBUTOR_WEBSITE_DEFAULT_CONTROLLER = 'distributor.website.default.controller';
	/**
	 * The name of the libraries folder, default libraries
	 * 
	 * @var string
	 */
	const OPTION_DISTRIBUTOR_WEBSITE_LIBRARIES_FOLDER = 'distributor.website.libraries.folder';
	/**
	 * The name of the views folder, default views
	 * 
	 * @var string
	 */
	const OPTION_DISTRIBUTOR_WEBSITE_VIEWS_FOLDER = 'distributor.website.views.folder';
	/**
	 * Name of class implementing {@link mvcErrorInterface}, default mvcErrorController
	 * 
	 * @var string
	 */
	const OPTION_DISTRIBUTOR_ERROR_CONTROLLER = 'distributor.error.controller';
	
	/**
	 * Stores $_MvcRequest
	 *
	 * @var mvcRequest
	 * @access protected
	 */
	protected $_MvcRequest;
	
	/**
	 * Stores $_MvcResponse
	 *
	 * @var mvcResponse
	 * @access protected
	 */
	protected $_MvcResponse;
	
	/**
	 * Stores $_SiteConfig instance
	 *
	 * @var mvcSiteConfig
	 * @access protected
	 */ 
	protected $_SiteConfig;
	
	/**
	 * Stores the mvcDistributorPluginSet object
	 *
	 * @var mvcDistributorPluginSet
	 * @access protected
	 */
	protected $_PluginSet;
	
	
	
	/**
	 * Returns new mvcDistributorBase instance
	 *
	 * @param mvcRequest $inRequest
	 * @param array $inOptions
	 * @return mvcDistributorBase
	 */
	function __construct(mvcRequest $inRequest, array $inOptions = array()) {
		$this->reset();
		$this->setRequest($inRequest);
		if ( count($inOptions) > 0 ) {
			$this->setOptions($inOptions);
		}
	}
	
	/**
	 * Resets the object to defaults
	 *
	 * @return void
	 */
	function reset() {
		$this->setOptions(
			array(
				self::OPTION_DISTRIBUTOR_WEBSITES_FOLDER => system::getConfig()->getPathWebsites()->getParamValue(),
				self::OPTION_DISTRIBUTOR_WEBSITE_CONTROLLERS_FOLDER => 'controllers',
				self::OPTION_DISTRIBUTOR_WEBSITE_DEFAULT_CONTROLLER => 'home',
				self::OPTION_DISTRIBUTOR_WEBSITE_LIBRARIES_FOLDER => 'libraries',
				self::OPTION_DISTRIBUTOR_WEBSITE_VIEWS_FOLDER => 'views',
				self::OPTION_DISTRIBUTOR_ERROR_CONTROLLER => 'mvcErrorController',
			)
		);
		$this->_MvcRequest = null;
		$this->_SiteConfig = null;
		$this->_PluginSet = null;
	}
	
	/**
	 * Initialises the distributor prior to request dispatch
	 *
	 * @return void
	 */
	function initialise() {
		$this->getRequest()->setDistributor($this);
		
		/*
		 * Point us to the right folder based on the request
		 */
		$this->resolveSitePath();
		
		/*
		 * Initialise mvcAutoloader
		 */
		new mvcAutoload(
			$this->getRequest(), $this->getSiteConfig(), array(
				mvcAutoload::FOLDER_CONTROLLERS => $this->getDistributorControllersFolder(),
				mvcAutoload::FOLDER_LIBRARIES => $this->getDistributorLibrariesFolder()
			)
		);
		
		/*
		 * Fire the registered plugins ondispatcher methods
		 */
		$this->getPluginSet()->executeOnDispatcherInitialise();
	}
	
	/**
	 * Routes the request launching any controller that is needed
	 *
	 * @return boolean
	 */
	abstract function dispatch();
	
	
	
	/**
	 * Finds the site path in the websites folder based on the server name
	 * 
	 * @return mvcDistributorBase
	 * @throws mvcDistributorException
	 */
	function resolveSitePath() {
		$serverName = $this->getRequest()->getServerName();
		
		while ( strpos($serverName, '.') !== false ) {
			if ( is_dir($this->getDistributorWebsitesFolder().system::getDirSeparator().$serverName) ) {
				$this->getRequest()->setDistributorSitePath($this->getDistributorWebsitesFolder().system::getDirSeparator().$serverName);
				$this->getRequest()->setDistributorServerName($serverName);
				return $this;
			}
			$serverName = substr($serverName, strpos($serverName,".")+1);
		}
		
		throw new mvcDistributorException("Failed to find site path for $serverName; configuration error");
	}
	
	/**
	 * Parses the request URI for the controller, returning controllerMap object
	 * 
	 * @return void
	 * @throws mvcException
	 */
	function resolveController() {
		if ( $this->getRequest()->getRequestUri() == "/" || $this->getRequest()->getRequestUri() == '' ) {
			if ( $this->getSiteConfig() instanceof mvcSiteConfig ) {
				$controller = $this->getSiteConfig()->getDefaultController();
			} else {
				$controller = $this->getDistributorDefaultController();
			}
		} else {
			$controller = $this->getRequest()->getRequestUri();
		}
		
		$this->getRequest()->setControllerMap(
			$this->getSiteConfig()->getControllerMapper()->findController($controller)
		);
	}
	
	/**
	 * Loads the controller from the request controllerMap
	 *
	 * @return mvcControllerBase
	 */
	function loadController() {
		$oMap = $this->getRequest()->getControllerMap();
		if ( $oMap->getController() instanceof SimpleXMLElement ) {
			if ( $oMap->getPath() && strlen($oMap->getPath()) > 1 ) {
				$controller = basename($oMap->getFilePath(), '.class.php');
			} else {
				$controller = $oMap->getName().'Controller';
			}
			systemLog::debug("Attempting to load and run ($controller)");
			return new $controller($this->getRequest(), $this->getResponse());
		}
		return false;
	}
	
	
	
	/**
	 * Returns the mvcRequest object
	 *
	 * @return mvcRequest
	 */
	function getRequest() {
		return $this->_MvcRequest;
	}
	
	/**
	 * Set $_MvcRequest to $inMvcRequest
	 *
	 * @param mvcRequest $inMvcRequest
	 * @return mvcDistributorBase
	 */
	function setRequest(mvcRequest $inMvcRequest) {
		if ( $inMvcRequest !== $this->_MvcRequest ) {
			$this->_MvcRequest = $inMvcRequest;
		}
		return $this;
	}

	/**
	 * Returns the mvcResponse object, creating a default if not set
	 *
	 * @return mvcResponse
	 */
	function getResponse() {
		if ( !$this->_MvcResponse instanceof mvcResponse ) {
			$this->_MvcResponse = new mvcResponse($this->getRequest());
			$this->_MvcResponse->addHeader(mvcResponse::HEADER_STATUS, mvcResponse::HEADER_STATUS_200);
		}
		return $this->_MvcResponse;
	}
	
	/**
	 * Set $_MvcResponse to $inMvcResponse
	 *
	 * @param mvcResponse $inMvcResponse
	 * @return mvcControllerBase
	 */
	function setResponse(mvcResponse $inMvcResponse) {
		if ( $inMvcResponse !== $this->_MvcResponse ) {
			$this->_MvcResponse = $inMvcResponse;
		}
		return $this;
	}
	
	/**
	 * Returns the site config object by lazy load
	 *
	 * @return mvcSiteConfig
	 */
	function getSiteConfig() {
		if ( !$this->_SiteConfig instanceof mvcSiteConfig ) {
			$this->_SiteConfig = new mvcSiteConfig(
				$this->getRequest()->getDistributorSitePath().system::getDirSeparator().'config.xml',
				$this->getRequest()->getServerName()
			);
			
			/*
			 * Import settings into global config
			 */
			system::getConfig()->load(
				$this->getRequest()->getDistributorSitePath().system::getDirSeparator().'config.xml'
			);
		}
		return $this->_SiteConfig;
	}
	
	/**
	 * Set the siteConfig instance
	 *
	 * @param mvcSiteConfig $inSiteConfig
	 * @return mvcDistributorBase
	 */
	function setSiteConfig($inSiteConfig){
		if ($this->_SiteConfig !== $inSiteConfig) {
			$this->_SiteConfig = $inSiteConfig;
		}
		return $this;
	}
	
	/**
	 * Returns the plugin set, lazy-loading it if the object does not exist
	 *
	 * @return mvcDistributorPluginSet
	 */
	function getPluginSet() {
		if ( !$this->_PluginSet instanceof mvcDistributorPluginSet ) {
			$this->_PluginSet = new mvcDistributorPluginSet($this->getRequest());
			$this->_PluginSet->load();
		}
		return $this->_PluginSet;
	}
	
	/**
	 * Allows the plugins to be set manually
	 *
	 * @param mvcDistributorPluginSet $inSet
	 * @return mvcDistributorBase
	 */
	function setPluginSet(mvcDistributorPluginSet $inSet) {
		$this->_PluginSet = $inSet;
		return $this;
	}
	
	
	
	/**
	 * Returns a properly formed path/to/the/thing with / set to the system dir separator
	 * 
	 * Path can be a string, array or mvcControllerMap object. If $inPath is null, the
	 * current request in the controller map is used.
	 *
	 * @param string $inPath
	 * @return string
	 */
	protected function buildPath($inPath = null) {
		$path = '';
		if ( $inPath === null ) {
			$path = $this->getRequest()->getControllerMap()->getPathAsString();
		} elseif ( is_array($inPath) && count($inPath) > 0 ) {
			$path = implode(system::getDirSeparator(), $inPath);
		} elseif ( $inPath instanceof mvcControllerMap ) {
			$path = $inPath->getPathAsString();
		} else {
			$path = utilityStringFunction::cleanDirSlashes(utilityStringFunction::cleanPath($inPath));
		}
		return $path;
	}
	
	/**
	 * Includes a file from the libraries folder of the site
	 *
	 * @param string $inLibraryFile
	 * @param mixed $inPath
	 * @return void
	 */
	function includeLibraryFile($inLibraryFile, $inPath = null) {
		if ( system::getConfig()->isProduction() ) {
			@include_once($this->getLibraryFile($inLibraryFile, $inPath));
		} else {
			include_once($this->getLibraryFile($inLibraryFile, $inPath));
		}
	}
	
	/**
	 * Includes a file from the controllers folder of the site
	 *
	 * @param string $inControllerFile
	 * @param mixed $inPath
	 * @return void
	 */
	function includeControllerFile($inControllerFile, $inPath = null) {
		if ( system::getConfig()->isProduction() ) {
			@include_once($this->getControllerFile($inControllerFile, $inPath));
		} else {
			include_once($this->getControllerFile($inControllerFile, $inPath));
		}
	}
	
	/**
	 * Includes an entire unit based on $inControllerName
	 * 
	 * e.g. $inControllerName = 'login', $inPath = '/user/login'
	 * 
	 * Loads controller filename: loginController.class.php from /user/login
	 * Loads model filename: loginModel.class.php from /user/login
	 * Loads view filename: loginView.class.php from /user/login
	 *
	 * @param string $inControllerName
	 * @param string $inPath
	 */
	function includeControllerUnit($inControllerName, $inPath = null) {
		$classname = $inControllerName.'Controller.class.php';
		$controller = $this->getControllerFile($classname, $inPath);
		if ( $controller ) {
			$this->includeControllerFile($classname, $inPath);
			$this->includeControllerFile($inControllerName.'Model.class.php', $inPath);
			$this->includeControllerFile($inControllerName.'View.class.php', $inPath);
			return true;
		} else {
			throw new mvcDistributorException("Unable to load unit ($inControllerName) from path ($inPath), controller does not exist");
		}
	}
	
	/**
	 * Returns the path to the controller file located in the sites website.controllers.folder (default controllers)
	 *
	 * @param string $inFileName
	 * @param mixed $inPath
	 * @return string
	 */
	function getControllerFile($inFileName, $inPath = null) {
		$path = $this->buildPath($inPath);
		if ( strlen($path) > 1 ) {
			$path .= system::getDirSeparator();
		}
		
		return $this->getSiteConfig()->getFilePath($this->getDistributorControllersFolder().system::getDirSeparator().$path.$inFileName);
	}
	
	/**
	 * Returns the path to the specified template file, uses current controller path if none specified
	 * template must be located in views/controller/path.
	 * Template must be located within distributor.views.folder (default views)
	 *
	 * @param string $inFileName
	 * @param mixed $inPath
	 * @return string
	 */
	function getTemplateFile($inFileName, $inPath = null) {
		$path = $this->buildPath($inPath);
		if ( strlen($path) > 1 ) {
			$path .= system::getDirSeparator();
		}
		return $this->getSiteConfig()->getFilePath($this->getDistributorViewsFolder().system::getDirSeparator().$path.$inFileName);
	}
	
	/**
	 * Returns the full path to the specified file to be included in relation to the current site
	 * and contained within the website.libraries.folder folder of that site (default libraries)
	 *
	 * @param string $inFileName
	 * @param mixed $inPath
	 * @return string
	 */
	function getLibraryFile($inFileName, $inPath = null) {
		$path = $this->buildPath($inPath);
		if ( strlen($path) > 1 ) {
			$path .= system::getDirSeparator();
		}
		
		return $this->getSiteConfig()->getFilePath($this->getDistributorLibrariesFolder().system::getDirSeparator().$path.$inFileName);
	}
	
	/**
	 * Returns the path to the specified plugin file, this method allows the plugin
	 * library files to be located outside of the site hierarchy. This will check the
	 * parent sites for a value, returns false if file does not exist or param not found.
	 *
	 * @param string $inFileName
	 * @param string $inPath
	 * @return string
	 * @deprecated Add site path to systemAutoload
	 * @since 0.3.0alpha1
	 */
	function getPluginFile($inFileName, $inPath = null) {
		throw new mvcDistributorException(__CLASS__.'::'.__METHOD__.' has been deprecated, please add the site path to the autoload system');
	}
	
	
	
	/**
	 * Returns the instance of baseOptionsSet 
	 *
	 * @return baseOptionsSet
	 */
	function getOptions() {
		if ( !$this->_Options instanceof baseOptionsSet ) {
			$this->_Options = new baseOptionsSet();
		}
		return $this->_Options;
	}
	
	/**
	 * Returns options or a specific option, null if not found 
	 *
	 * @param string $inOption (optional) The option to get, null for all
	 * @param mixed $inDefault (optional) Default value returned if $inOption is not set
	 * @return mixed
	 */
	function getOption($inOption = null, $inDefault = null) {
		return $this->getOptions()->getOptions($inOption, $inDefault);
	}
	
	/**
	 * Returns the websites folder
	 *
	 * @return string
	 */
	function getDistributorWebsitesFolder() {
		return $this->getOption(self::OPTION_DISTRIBUTOR_WEBSITES_FOLDER, system::getConfig()->getPathWebsites()->getParamValue());
	}
	
	/**
	 * Returns the default controller name
	 *
	 * @return string
	 */
	function getDistributorDefaultController() {
		return $this->getOption(self::OPTION_DISTRIBUTOR_WEBSITE_DEFAULT_CONTROLLER, 'home');
	}
	
	/**
	 * Returns the name of the controllers folder
	 *
	 * @return string
	 */
	function getDistributorControllersFolder() {
		return $this->getOption(self::OPTION_DISTRIBUTOR_WEBSITE_CONTROLLERS_FOLDER, 'controllers');
	}
	
	/**
	 * Returns the name of the libraries folder
	 *
	 * @return string
	 */
	function getDistributorLibrariesFolder() {
		return $this->getOption(self::OPTION_DISTRIBUTOR_WEBSITE_LIBRARIES_FOLDER, 'libraries');
	}
	
	/**
	 * Returns the name of the views folder
	 *
	 * @return string
	 */
	function getDistributorViewsFolder() {
		return $this->getOption(self::OPTION_DISTRIBUTOR_WEBSITE_VIEWS_FOLDER, 'views');
	}

	/**
	 * Returns the error controller that will handle errors
	 *
	 * @return string
	 */
	function getDistributorErrorController() {
		return $this->getOption(self::OPTION_DISTRIBUTOR_ERROR_CONTROLLER, 'mvcErrorController');
	}
	
	/**
	 * Set options to $inOptions
	 * 
	 * $inOptions can be either an associative array or an instance of baseOptionsSet
	 *
	 * @param mixed $inOptions
	 * @return mvcDistributorBase
	 */
	function setOptions($inOptions) {
		if ( $inOptions instanceof baseOptionsSet ) {
			$this->_Options = $inOptions;
		}
		if ( is_array($inOptions) ) {
			$this->getOptions()->setOptions($inOptions);
		}
		return $this;
	}
}