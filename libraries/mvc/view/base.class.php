<?php
/**
 * mvcViewBase.class.php
 * 
 * mvcViewBase class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewBase
 * @version $Rev: 844 $
 */


/**
 * mvcViewBase class
 * 
 * mvcViewBase class that all views inherit from. Includes the rendering engine setup routines.
 * This class is extended by all views within the mvc system.
 * 
 * The template engine can be exchange so long as an appropriate adaptor is created to route
 * the various requests to the template engine. Two engines are included: Smarty and a generic
 * PHP system. The engine to be used can be defined in the site config.xml file. Generally
 * once an engine has been decided upon it cannot be easily changed due to vast differences
 * in template code e.g. Smarty vs. straight PHP vs. PHPTal etc.
 * 
 * When passing variables through to the view system, they should be encapsulated using the 
 * output wrapper. This blocks attempts to modify objects and helps to protect the models
 * from being changed. See {@link utilityOutputWrapper} for more details.
 * 
 * mvcViewBase supports "resources". These are additional files to be used with the templates.
 * Currently this is limited to CSS and Javascript. During the render phase, additional CSS
 * and Javascript files or code can be added and injected into the template. This requires
 * that the template be set-up to output this information.
 * 
 * Views can be executed via two methods:
 * 
 * <ol>
 *   <li>Direct rendering</li>
 *   <li>Compilation</li>
 * </ol>
 * 
 * Direct rendering, as the name implies will compile and render to the current requestor
 * any response. This is the default response from a view. Headers will be sent during this
 * method call, including any headers defined by the view engine.
 * 
 * Compilation uses the template engine to compile the response, but it is returned instead
 * of being displayed. This allows the response to be further filtered or to be included in
 * other output.
 * 
 * Compilation is used for rendering controller views.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewBase
 */
class mvcViewBase {
	
	/**
	 * Array of loaded templates from the various controllers / views
	 *
	 * @var array
	 * @access protected
	 * @static 
	 */
	protected static $_Tpls = array();
	
	/**
	 * Stores $_Engine
	 *
	 * @var mvcViewEngineBase
	 * @access protected
	 */ 
	protected $_Engine;
	
	/**
	 * Stores $_Controller
	 *
	 * @var mvcControllerBase
	 * @access protected
	 */ 
	protected $_Controller;
	
	/**
	 * Array of HTTP headers to send with response
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_Headers;
	
	/**
	 * Stores an array of additional resources, either CSS or Javascript
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_Resources;
	
	const HEADER_CONTENT_DISPOSITION = 'Content-Disposition';
	const HEADER_CONTENT_TYPE = 'Content-Type';
	const HEADER_CONTENT_LENGTH = 'Content-Length';
	const HEADER_EXPIRES = 'Expires';
	const HEADER_LAST_MODIFIED = 'Last-Modified';
	const HEADER_LOCATION = 'Location';
	const HEADER_STATUS = 'Status';
	
	const HEADER_STATUS_200 = '200 OK';
	
	const HEADER_STATUS_300 = '300 Multiple Choices';
	const HEADER_STATUS_301 = '301 Moved Permanently';
	const HEADER_STATUS_302 = '302 Found';
	const HEADER_STATUS_303 = '303 See Other';
	const HEADER_STATUS_304 = '304 Not Modified';
	
	const HEADER_STATUS_400 = '400 Bad Request';
	const HEADER_STATUS_401 = '401 Unauthorized';
	const HEADER_STATUS_403 = '403 Forbidden';
	const HEADER_STATUS_404 = '404 Not Found';
	
	const HEADER_STATUS_500 = '500 Internal Server Error';
	const HEADER_STATUS_501 = '501 Not Implemented';
	const HEADER_STATUS_503 = '503 Service Unavailable';
	
	/**
	 * Holds an instance of the mvcViewHelperFactory class
	 *
	 * @var mvcViewHelperFactory
	 * @access protected
	 */
	protected $_ViewHelperFactory;
	
	
	
	/**
	 * Returns new mvcViewBase instance
	 *
	 * @param mvcControllerBase $inController
	 * @return mvcViewBase
	 */
	function __construct(mvcControllerBase $inController) {
		$this->_Resources = array('css' => array(), 'js' => array(), 'meta' => array());
		$this->setController($inController);
		$this->setupEngine();
		$this->setupInitialVars();
	}

	/**
	 * Redirects unknown calls to the view helper system
	 *
	 * @param string $name Helper function name
	 * @param array $arguments Array of passed arguments (if any)
	 * @return string
	 */
	function __call ($name, $arguments) {
		// locate, fetch and load the helper
		$oHelper = $this->getViewHelperFactory()->getHelper($name);
		if ( method_exists($oHelper, 'setView') ) {
			$oHelper->setView($this);
		}
		
		// call the helper method
		return call_user_func_array(array($oHelper, $name), $arguments);
	}
	
	
	
	/**
	 * Setups up the Engine environment for us
	 *
	 * @return void
	 */
	function setupEngine() {
		/*
		 * setup the engine details
		 */
		$this->_Engine = mvcViewEngineFactory::getEngine(
			$this->getRequest()->getDistributor()->getSiteConfig()->getTemplateEngine()
		);
		$this->_Engine->setCaching(false);
		
		/*
		 * build site name
		 */
		$site_name = str_replace('.','_',$this->getRequest()->getDistributorServerName());
		
		$this->_Engine->setTemplateDir($this->getRequest()->getDistributor()->getDistributorWebsitesFolder());
		$this->_Engine->setConfigDir($this->getRequest()->getDistributor()->getDistributorWebsitesFolder());
	    $this->_Engine->setCompileDir($site_name);
	    $this->_Engine->setCacheDir($site_name);
	    $this->_Engine->setUseSubDirs(true);
	}
	
	/**
	 * Assigns some default values to template engine that are always needed
	 *
	 * @return void
	 */
	function setupInitialVars() {
		$this->getEngine()->assign("oRequest", utilityOutputWrapper::wrap($this->getRequest()));
		$this->getEngine()->assign("oSession", utilityOutputWrapper::wrap($this->getRequest()->getSession()));
	    $this->getEngine()->assign("oDistributor", utilityOutputWrapper::wrap($this->getRequest()->getDistributor()));
	    $this->getEngine()->assign('oView', utilityOutputWrapper::wrap($this, array('buildUriPath')));
	    $this->getEngine()->assign("oMap", utilityOutputWrapper::wrap($this->getRequest()->getControllerMap()));
	    $this->getEngine()->assign("isProduction", system::getConfig()->isProduction());
	    $this->getEngine()->assign("appAuthor", system::getConfig()->getParam('app','author'));
	    $this->getEngine()->assign("appCopyright", system::getConfig()->getParam('app','copyright'));
	    $this->getEngine()->assign("appVersion", system::getConfig()->getParam('app','version','1.0'));
	    
		/*
		 * Set some common theme vars
		 */
	    if ( $this->getRequest()->getDistributor()->getSiteConfig()->getTheme() ) {
			$themeName = $this->getRequest()->getDistributor()->getSiteConfig()->getTheme()->getParamValue();
			$this->getEngine()->assign('themename', $themeName);
			$this->getEngine()->assign('themefolder', '/themes/'.$themeName);
			$this->getEngine()->assign('themeimages', '/themes/'.$themeName.'/images');
			$this->getEngine()->assign('themeicons', '/themes/'.$themeName.'/images/icons');
	    }
	}
	
	
	
	/**
	 * Used to disable template caching
	 *
	 * @access public
	 */
	function disableTemplateCache() {
		$this->getEngine()->setCaching(false);
	}
	
	/**
	 * Disable template caching completely
	 *
	 * @return void
	 */
	function setCacheLevelNone() {
		$this->disableTemplateCache();
	}
	
	/**
	 * Set the template cache time to "low" (10 minutes)
	 *
	 * @return void
	 */
	function setCacheLevelLow() {
		$this->getEngine()->setCaching(true);
		$this->getEngine()->setCacheLifetime(600);
	}
	
	/**
	 * Set the template cache to a medium level (1 hour)
	 *
	 * @return void
	 */
	function setCacheLevelMedium() {
		$this->getEngine()->setCaching(true);
		$this->getEngine()->setCacheLifetime(3600);
	}
	
	/**
	 * Set the template cache to a high level (5 hours)
	 * 
	 * @return void
	 */
	function setCacheLevelHigh() {
		$this->getEngine()->setCaching(true);
		$this->getEngine()->setCacheLifetime(5 * 3600);
	}
	
	/**
	 * Essentially persist the template (caches for 1 whole day)
	 *
	 * @return void
	 */
	function setCacheLevelPersist() {
		$this->getEngine()->setCaching(true);
		$this->getEngine()->setCacheLifetime(24 * 3600);
	}
	
	
	
	/**
	 * Locate the requested template from the file system, throws exception if not found
	 *
	 * @param string $inFileName
	 * @param string $inPath (optional)
	 * @return string
	 * @throws mvcViewInvalidTemplateException
	 */
	function getTemplateFile($inFileName, $inPath = null) {
		$fileName = $inFileName.'.'.$this->getRequest()->getOutputType().'.tpl';
		$resolvedTemplate = $this->getRequest()->getDistributor()->getTemplateFile($fileName, $inPath);
		if ( !$resolvedTemplate ) {
			throw new mvcViewInvalidTemplateException($fileName, $inPath, get_class($this->getController()), get_class($this));
		} else {
			return 'file:'.$resolvedTemplate;
		}	
	}
	
	/**
	 * Return a statically cached template or add it if it is not there
	 *
	 * @param string $inFileName
	 * @param string $inPath (optional)
	 * @return string
	 * @throws mvcViewInvalidTemplateException
	 */
	function getTpl($inFileName, $inPath = null) {
		$key = ($inPath !== null ? $inPath.'.' : '').$inFileName;
		if ( !isset(self::$_Tpls[$key]) ) {
			self::$_Tpls[$key] = $this->getTemplateFile($inFileName, $inPath);
		}
		
		return self::$_Tpls[$key];
	}
	
	/**
	 * Fetches a view from a different controller. The path is the controllerMap path (URI path)
	 * to the controller. The view must be named in the controller and be permitted by the
	 * mvcControllerViews system (see motdController.class.php for an example in baseAdminSite).
	 * $inControllerName is the controller name without the "Controller" suffix.
	 * 
	 * Use this method within your templates. It can be used in place of the Smarty function {@link smarty_function_includeView}
	 * to allow other template engines to pull in external views.
	 * 
	 * $inParams is a string of the parameters formatted like a URI request string, for example:
	 * <code>$inParams = 'param1=value1&param2=val2&3=4';</code>
	 * This string will be converted to an array and passed to the requested controllers {@link mvcControllerBase::fetchStandaloneView()}
	 * method. Additionally, the parameters will contain: controller, view, classname and path. 
	 * 
	 * <code>
	 * 	// in php templates
	 *  $oView->getControllerView('motd', '/controlPanel/systemTools/motd', 'motd');
	 *  
	 *  // smarty
	 *  {$oView->getControllerView('motd', '/controlPanel/systemTools/motd', 'motd')}
	 *  
	 *  // with additional parameters
	 *  {$oView->getControllerView('motd', '/controlPanel/systemTools/motd', 'motd', 'param1=value1&param2=value2')}
	 * </code>
	 * 
	 * @param string $inControllerName
	 * @param string $inControllerPath
	 * @param string $inView
	 * @param string $inParams
	 * @return string
	 */
	function getControllerView($inControllerName = null, $inControllerPath = null, $inView = null, $inParams = null) {
		$display = '';
		try {
			if ( $inControllerName == null ) {
				throw new mvcViewException("Missing required param 'controller'. Please set controller when calling this function");
			}
			if ( $inControllerPath == null ) {
				throw new mvcViewException("Missing required param 'path'. Please specify the path when calling this function");
			}
			if ( $inView == null ) {
				throw new mvcViewException("Missing required param 'view'. Please specify the view when calling this function");
			}
			
			/**
			 * The classname for the controller
			 * 
			 * @var string $controller
			 */
			$controller = $inControllerName.'Controller';
			
			/*
			 * Attempt to load the specified controller
			 */
			$oRequest = $this->getRequest();
			$oRequest->getDistributor()->includeControllerUnit($inControllerName, $inControllerPath);
			
			if ( false ) $oController = new mvcControllerBase($this->getRequest());
			$oController = new $controller($oRequest, $this->getResponse());
			
			if ( $oController->isValidView($inView) ) {
				$params = array(
					'controller' => $inControllerName,
					'path' => $inControllerPath,
					'view' => $inView,
					'classname' => $controller
				);
				$params = array_merge($params, utilityStringFunction::createArrayFromString($inParams));
				
				$display = utf8_encode($oController->fetchStandaloneView($params));
			} else {
				throw new mvcViewException("Failed to load view ($inView). This is not a valid view for controller $controller");
			}
		} catch (Exception $oException) {
			echo $str = "Error loading view <strong>{$inView}</strong> from controller <strong>{$inControllerName}</strong>.";
			systemLog::error(strip_tags($str));
			throw $oException;
		}
		
		return $display;
	}
	
	
	
	/**
	 * Returns mvcViewEngineBase object
	 *
	 * @return mvcViewEngineBase
	 */
	function getEngine() {
		return $this->_Engine;
	}

	/**
	 * Sends any set headers to the browser, only if headers have not already been sent
	 *
	 * @return void
	 */
	function sendHeaders() {
		if ( !headers_sent() ) {
			foreach ( $this->getHeaders() as $type => $value ) {
				if ( $type == self::HEADER_STATUS ) {
					header($this->getRequest()->getServerProtocol().' '.$value);
				} else {
					header("$type: $value");
				}
			}
		}
	}
	
	/**
     * Displays the view output generated from the request via the template layer
     *
     * @param string $inTemplate
     * @param string $inCacheID
     * @param string $inCompileID
     * @return void
     */
	function render($inTemplate, $inCacheID = null, $inCompileID = null) {
		$this->sendHeaders();
		
		$inCacheID = $this->getCacheId($inCacheID);
		$inCompileID = $this->getCacheId($inCompileID);
        $this->getEngine()->render($inTemplate, $inCacheID, $inCompileID);
    }
    
	/**
     * Executes and compiles the response, but returns rather displays the results
     *
     * @param string $inTemplate
     * @param string $inCacheID
     * @param string $inCompileID
     * @return string
     */
    function compile($inTemplate, $inCacheID = null, $inCompileID = null) {
    	$inCacheID = $this->getCacheId($inCacheID);
    	$inCompileID = $this->getCacheId($inCompileID);

    	return $this->getEngine()->compile($inTemplate, $inCacheID , $inCompileID);
    }
    
    /**
     * test to see if valid cache exists for this template
     *
     * @param string $inTemplate name of template file
     * @param string $inCacheID
     * @param string $inCompileID
     * @return string|false
     */
    function isCached($inTemplate, $inCacheID = null, $inCompileID = null) {
    	$inCacheID = $this->getCacheId($inCacheID);
    	$inCompileID = $this->getCacheId($inCompileID);

		return $this->getEngine()->isCached($inTemplate, $inCacheID , $inCompileID);
    }
    
    /**
     * Adjusts $inCacheId to reflect the request locale, port and mobile device output type
     *
     * @param string $inCacheId
     * @return string
     */
    function getCacheId($inCacheId) {
    	if ( $this->getRequest()->getDistributor()->getSiteConfig()->isI18nActive() ) {
    		$inCacheId = $this->getRequest()->getLocale().'_'.$inCacheId;
    	}
    	if ( $this->getRequest()->getServerPort() != 80 ) {
			$inCacheId = $this->getRequest()->getServerPort().'_'.$inCacheId;
		}
		if ( in_array($this->getRequest()->getOutputType(), array(mvcRequest::OUTPUT_WML, mvcRequest::OUTPUT_XHTML))  ) {
			if ( $this->getRequest()->getSession() instanceof mvcSessionBase && $this->getRequest()->getSession()->getParam('request.device') ) {
				$deviceID = $this->getRequest()->getSession()->getParam('request.device');
				$inCacheId = ($deviceID ? $deviceID : 0).'_'.$inCacheId;
			}
		}

		return $inCacheId;
    }
	
    
    
	/**
	 * Returns the current controller for this view	
	 *
	 * @return mvcControllerBase
	 */
	function getController() {
		return $this->_Controller;
	}
	
	/**
	 * Set the controller object
	 *
	 * @param mvcControllerBase $inController
	 * @return mvcViewBase
	 */
	function setController($inController){
		if ( $this->_Controller !== $inController ) {
			$this->_Controller = $inController;
		}
		return $this;
	}
	
	/**
	 * Returns the mvcRequest object
	 *
	 * @return mvcRequest
	 */
	function getRequest() {
		return $this->getController()->getRequest();
	}
	
	/**
	 * Returns the mvcResponse object
	 * 
	 * @return mvcResponse
	 */
	function getResponse() {
		return $this->getController()->getResponse();
	}
	
	/**
	 * Returns the model
	 *
	 * @return mvcModelBase
	 */
	function getModel() {
		return $this->getController()->getModel();
	}
	
	/**
	 * Returns the view helper factory instance
	 *
	 * @return mvcViewHelperFactory
	 */
	function getViewHelperFactory() {
		if ( !$this->_ViewHelperFactory instanceof mvcViewHelperFactory ) {
			$this->_ViewHelperFactory = new mvcViewHelperFactory();
		}
		return $this->_ViewHelperFactory;
	}
	
	/**
	 * Builds a valid path to the the action $inAction with optional data $inData
	 * 
	 * $inAction should be an action that is valid for the current controller.
	 * $inData should be url_encoded or be in a URI friendly state.
	 *
	 * @param string $inAction
	 * @param string $inData
	 * @return string
	 */
	function buildUriPath($inAction, $inData = null) {
		return $this->getController()->buildUriPath($inAction, $inData);
	}
	
	
	
	/**
	 * Returns the current set of headers to be used during output
	 *
	 * @return baseOptionsSet
	 */
	function getHeaders() {
		if ( !$this->_Headers instanceof baseOptionsSet ) {
			$this->_Headers = new baseOptionsSet(
				array(
					self::HEADER_STATUS => self::HEADER_STATUS_200,
					self::HEADER_CONTENT_TYPE => $this->getRequest()->getMimeType(),
				)
			);
		}
		return $this->_Headers;
	}
	
	/**
	 * Add a header to the set, will overwrite an existing header
	 *
	 * @param string $inType
	 * @param string $inValue
	 * @return mvcViewBase
	 */
	function addHeader($inType, $inValue) {
		$this->getHeaders()->setOptions(array($inType => $inValue));
		return $this;
	}
	
	/**
	 * Removes the specified header from the set
	 *
	 * @param string $inType
	 * @return mvcViewBase
	 */
	function removeHeader($inType) {
		$this->getHeaders()->removeOptions(array($inType));
		return $this;
	}
	
	
	
	/**
	 * Adds a CSS resource
	 * 
	 * @param mvcViewCss $inResource
	 * @return mvcViewBase
	 */
	function addCssResource(mvcViewCss $inResource) {
		return $this->addResource($inResource);
	}

	/**
	 * Adds a Javascript resource
	 * 
	 * @param mvcViewJavascript $inResource
	 * @return mvcViewBase
	 */
	function addJavascriptResource(mvcViewJavascript $inResource) {
		return $this->addResource($inResource);
	}

	/**
	 * Adds a Meta resource
	 * 
	 * @param mvcViewMeta $inResource
	 * @return mvcViewBase
	 */
	function addMetaResource(mvcViewMeta $inResource) {
		return $this->addResource($inResource);
	}
	
	/**
	 * Adds an mvcViewResource
	 * 
	 * @param mvcViewResource $inResource
	 * @return mvcViewBase
	 */
	function addResource(mvcViewResource $inResource) {
		$type = $this->getResourceType($inResource);
		
		if ( $type && !array_key_exists($inResource->getIdentifier(), $this->_Resources[$type]) ) {
			$this->_Resources[$type][$inResource->getIdentifier()] = $inResource;
		}
		
		return $this;
	}
	
	/**
	 * Removes the resource
	 * 
	 * @param mvcViewResource $inResource
	 * @return mvcViewBase
	 */
	function removeResource(mvcViewResource $inResource) {
		$type = $this->getResourceType($inResource);
		
		if ( $type && array_key_exists($inResource->getIdentifier(), $this->_Resources[$type]) ) {
			$this->_Resources[$type][$inResource->getIdentifier()] = null;
			unset($this->_Resources[$type][$inResource->getIdentifier()]);
		}
		
		return $this;
	}
	
	/**
	 * Locates a resource by the identifier, returns null if not found
	 * 
	 * @param string $inResource
	 * @return mvcViewResource
	 */
	function getResourceByIdentifier($inResource) {
		if ( array_key_exists($inResource, $this->_Resources['css']) ) {
			return $this->_Resources['css'][$inResource];
		}
		if ( array_key_exists($inResource, $this->_Resources['js']) ) {
			return $this->_Resources['js'][$inResource];
		}
		if ( array_key_exists($inResource, $this->_Resources['meta']) ) {
			return $this->_Resources['meta'][$inResource];
		}
		return null;
	}
	
	/**
	 * Returns the set of resources by type
	 * 
	 * @param string $inType Either css, js or meta
	 * @return array
	 */
	function getResourcesByType($inType) {
		if ( array_key_exists($inType, $this->_Resources) ) {
			return $this->_Resources[$inType];
		} else {
			return array();
		}
	}
	
	/**
	 * Returns the resource type from the specified resource object
	 * 
	 * @param mvcViewResource $inResource
	 * @return string
	 */
	function getResourceType(mvcViewResource $inResource) {
		if ( $inResource instanceof mvcViewCss ) {
			$type = 'css';
		} elseif ( $inResource instanceof mvcViewJavascript ) {
			$type = 'js';
		} elseif ( $inResource instanceof mvcViewMeta ) {
			$type = 'meta';
		} else {
			$type = false;
		}
		
		return $type;
	}
}