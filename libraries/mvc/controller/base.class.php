<?php
/**
 * mvcControllerBase.class.php
 * 
 * mvcControllerBase class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerBase
 * @version $Rev: 764 $
 */


/**
 * mvcControllerBase class
 * 
 * Abstract mvcControllerBase class that all controllers inherit from. Provides base
 * functionality for the MVC system. This class allows shared logic to be implemented
 * at the very base level of a site e.g. authentication.
 * 
 * The class requires a concrete implementation, but usually has at least one
 * intermediary class before a final controller e.g.
 * 
 * mvcControllerBase -> mvcController -> myController
 * 
 * Where mvcController is a site specific super-type controller that implements the
 * main initialisation and authentication logic.
 * 
 * Scorpio uses a {@link mvcControllerBase::launch() launch} method for handling the
 * request processing. This makes the controllers more like PageControllers than a
 * typical web controller / command; however you are free to implement method handling
 * if you wish. The {@link mvcControllerBase::isValidAction()} is abstract for this
 * very reason - how you dispatch the actions is up to you.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerBase
 */
abstract class mvcControllerBase {
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access private
	 */
	private $_Modified;
	
	/**
	 * Stores $_Action
	 * 
	 * @var string
	 * @access private
	 */
	private $_Action;
	
	/**
	 * Stores $_DefaultAction
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DefaultAction;
	
	/**
	 * Stores $_InputManager
	 * 
	 * @var utilityInputManager
	 * @access private
	 */
	private $_InputManager;
	
	/**
	 * Stores $_ControllerActions
	 * 
	 * @var mvcControllerActions
	 * @access private
	 */
	private $_ControllerActions;
	
	/**
	 * Stores $_ControllerViews
	 *
	 * @var mvcControllerViews
	 * @access private
	 */
	private $_ControllerViews;
	
	/**
	 * Stores $_RequiresAuthentication
	 *
	 * @var boolean
	 * @access private
	 */
	private $_RequiresAuthentication;
	
	/**
	 * Stores $_Model
	 *
	 * @var mvcModelBase
	 * @access private
	 */
	private $_Model;
	
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
	 * Returns new mvcControllerBase instance
	 *
	 * If overloading __construct, ensure that the first argument is always an
	 * instance of mvcRequest. Additional arguments can be added after this.
	 * Place custom configuration code inside the {@link mvcControllerBase::initialise()}
	 * method. Initialise is always called after the request has been set to the
	 * controller object.
	 *
	 * @param mvcRequest $inRequest
	 * @param mvcResponse $inResponse
	 * @return mvcControllerBase
	 */
	function __construct(mvcRequest $inRequest, $inResponse = null) {
		$this->reset();
		$this->setRequest($inRequest);
		if ( $inResponse instanceof mvcResponse ) {
			$this->setResponse($inResponse);
		}
		$this->initialise();
	}
	
	/**
	 * Rests object to defaults
	 * 
	 * @return void
	 */
	function reset() {
		$this->_Action = null;
		$this->_DefaultAction = '';
		$this->_InputManager = null;
		$this->_ControllerActions = null;
		$this->_ControllerViews = null;
		$this->_MvcRequest = null;
		$this->_MvcResponse = null;
		$this->_Model = null;
		$this->_RequiresAuthentication = true;
		$this->setModified(false);
	}
	
	
	
	/**
	 * Perform pre-launch set-up for this controller, including setting the default action
	 *
	 * @return void
	 * @abstract 
	 */
	abstract function initialise();

	/**
	 * Returns true if the parsed action is valid for this controller
	 *
	 * @return boolean
	 * @abstract 
	 */
	abstract function isValidAction();
	
	/**
	 * Returns true if the requested view can be used with this controller
	 *
	 * @param string $inView
	 * @return boolean
	 * @abstract 
	 */
	abstract function isValidView($inView);
	
	/**
	 * Returns true if requester is authorised for this controller
	 *
	 * @return boolean
	 * @abstract 
	 */
	abstract function isAuthorised();
	
	/**
	 * Handle the authorisation requests or redirect to component that will
	 *
	 * @return void
	 * @abstract 
	 */
	abstract function authorise();
	
	/**
	 * Returns true if the user has authority to access/use $inActivity
	 *
	 * @param string $inActivity
	 * @return boolean
	 */
	abstract function hasAuthority($inActivity);
	
	/**
	 * Launch the actions of the controller
	 *
	 * @return boolean
	 * @abstract 
	 */
	abstract function launch();
	
	
	
	/**
	 * Redirects the browser to $inLocation in a semi-intelligent fashion. If headers are already sent
	 * displays the static template page "redirect.XYZ" where XYZ is the current output type e.g. xml, html, wml
	 *
	 * The redirect template should contain %redirect.location% as a link for the user to follow. It will
	 * be replaced with whatever link was passed into redirect().
	 * 
	 * @param string $inLocation
	 * @return void
	 */
	function redirect($inLocation) {
		session_write_close();
		if ( headers_sent() ) {
			$file = $this->getRequest()->getDistributor()->getTemplateFile('redirect.'.$this->getRequest()->getOutputType(), '');
			if ( $file ) {
				header("Content-Type: ".$this->getRequest()->getMimeType());
				echo str_replace('%redirect.location%', $inLocation, @file_get_contents($file));
				exit;
			}
		}
		header("Location: $inLocation");
		exit;
	}
	
	/**
	 * Redirects to a specific controller in the request controller map
	 * 
	 * This method will forward a request to another controller within the same
	 * request context i.e. without any additional HTTP overheads. This requires 
	 * an amount of setting up as the controller must reside within the controller
	 * map and the mvcRequest object requires updating with any additional data
	 * to forward to the called controller.
	 * 
	 * An example usage would be to forward requests to a login controller when a
	 * request has not been verified, or if additional processing is required on
	 * a previously successful action.
	 * 
	 * The basics are to call the controller (without suffix e.g. loginController 
	 * is called as "login"), with the required action (that must be valid on that
	 * controller) and the path to this controller as it would be requested via
	 * the normal route. The path is important as it will re-configure the
	 * mvcRequest data structures for this new request and cause all subsequent
	 * links to resolve correctly (similar to {@link mvcViewBase::getControllerView})
	 * 
	 * <code>
	 * class myController extends mvcController {
	 *     
	 *     function launch() {
	 *        // do something and process some stuff through the model
	 *        // now instead of forwarding to a view, redirect
	 * 
	 *        $this->controllerRedirect('another', 'doMoreProcessing', '/path/to/another');
	 *        // no more code will be executed after this point; the request will be processed
	 *        // by "anotherController" located in the controllers folder in /path/to/another/
	 *     }
	 * }
	 * </code>
	 *
	 * @param string $inController
	 * @param string $inAction
	 * @param string $inPath
	 * @return void
	 * @throws mvcControllerException
	 */
	function controllerRedirect($inController, $inAction, $inPath) {
		$this->getRequest()->getDistributor()->includeControllerUnit($inController, $inPath);
		/*
		 * Reset the map to the new request; need this to ensure links will resolve
		 */
		$this->getRequest()->setControllerMap(
			$this->getRequest()
				->getDistributor()
					->getSiteConfig()
						->getControllerMapper()
							->getController($inPath)
		);
		
		$controller = $inController.'Controller';
		if ( false ) $oController = new mvcControllerBase();
		$oController = new $controller($this->getRequest(), $this->getResponse());
		$oController->setAction($inAction);
		if ( $oController->isValidAction() ) {
			$oController->launch();
			return;
		} else {
			throw new mvcControllerException("Action ($inAction) is not valid for controller ($inController)");
		}
	}
	
	
	
	/**
	 * Binds input filters to the input manager; called in initialise()
	 * 
	 * <code>
	 * class controller extends mvcController {
	 *     
	 *     // bind input filters
	 *     function addInputFilters() {
	 *         $this->getInputManager()->addInputFilter('someVar', utilityInputFilter::filterString());
	 *     }
	 * }
	 * </code>
	 * 
	 * @return void
	 * @abstract 
	 */
	function addInputFilters() {}
	
	/**
	 * Performs additional validation on the input data, returns true if OK, or an error string
	 *
	 * @param array $inData
	 * @return boolean
	 * @abstract 
	 */
	function validateInputData($inData) {}
	
	/**
	 * Maps filtered input data to the controller model
	 *
	 * @param array $inData
	 * @param mvcModelBase $inModel
	 * @return void
	 * @abstract 
	 */
	function addInputToModel($inData, $inModel) {}
	
	/**
	 * Returns the standalone view based on the params.
	 * 
	 * This method is called from within a view (usually from {@link mvcViewBase::getControllerView()}).
	 * The array of parameters includes:
	 * 
	 * <ul>
	 *   <li>controller - the requested controller</li>
	 *   <li>path - the path to the controller</li>
	 *   <li>view - the specific view, as defined in {@link getControllerViews}</li>
	 *   <li>classname - the controller class name</li>
	 * </ul>
	 *
	 * $params will also contain any passed parameters that were defined in the view.
	 *
	 * @param array $params
	 * @return string
	 */
	function fetchStandaloneView($params = array()) {}
	
	
	
	/**
	 * Returns $_Modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set $_Modified to $inModified
	 *
	 * @param boolean $inModified
	 * @return mvcControllerBase
	 */
	function setModified($inModified = true) {
		if ( $inModified !== $this->_Modified ) {
			$this->_Modified = $inModified;
		}
		return $this;
	}
	
	/**
	 * Returns the current action that is being requested
	 * 
	 * @return string
	 */
	function getAction() {
		if ( !$this->_Action ) {
			$this->_Action = $this->getActionFromRequest($this->getDefaultAction());
		}
		return $this->_Action;
	}
	
	/**
	 * Set the current action for the request
	 * 
	 * @param string $inAction
	 * @return mvcControllerBase
	 */
	function setAction($inAction) {
		if ( $inAction !== $this->_Action ) {
			$this->_Action = $inAction;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns a default action
	 *
	 * @return string
	 */
	function getDefaultAction() {
		return $this->_DefaultAction;
	}
	
	/**
	 * Set the default action for the controller, usually defined in the initialise call
	 *
	 * @param string $inDefaultAction
	 * @return mvcControllerBase
	 */
	function setDefaultAction($inDefaultAction) {
		if ( $inDefaultAction !== $this->_DefaultAction ) {
			$this->_DefaultAction = $inDefaultAction;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Fetches data from the request URI string
	 * 
	 * This method allows data to be encoded in the URI string and then used during
	 * the request processing, allowing for SEO friendly URIs. $inDefault specifies
	 * a value to use if the section cannot be found and $inLevel is how far up the
	 * URI string to fetch the value from. The level is offset by the current
	 * controller level as determined by the {@link mvcControllerMapper}.
	 * 
	 * As an example: /product/view/123456/My_Product_Name
	 * <code>
	 * function launch() {
	 *     $prodID = $this->getActionFromRequest(false, 1);
	 *     // prodID is 123456
	 * }
	 * </code>
	 *
	 * @param string $inDefault
	 * @param integer $inLevel
	 * @return string
	 */
	function getActionFromRequest($inDefault = '', $inLevel = 0) {
		$level = $this->getControllerLevel();
		$level = $level + $inLevel;
		$pattern = '/^'.str_repeat('\/[^\/]*',$level).'\/([^\/]*)[\.|\/]?(.*)/';
		$matches = array();
		
		preg_match($pattern, $this->getRequest()->getRequestUri(), $matches);
		if ( count($matches) > 0 ) {
			if ( $matches[1] != 'index.php' && $matches[1] != 'index.html' ) {
				$this->getRequest()->setControllerUri(isset($matches[2]) ? $matches[2] : '');
				return trim($matches[1]) == '' ? $inDefault : urldecode(trim($matches[1]));
			}
		}
		return $inDefault;
	}
	
	/**
	 * Returns the instance of the {@link utilityInputManager Input Manager}
	 * 
	 * @return utilityInputManager
	 */
	function getInputManager() {
		if ( !$this->_InputManager instanceof utilityInputManager ) {
			$this->_InputManager = new utilityInputManager(utilityInputManager::LOOKUPGLOBALS_POST);
		}
		return $this->_InputManager;
	}
	
	/**
	 * Set a pre-built input manager instance
	 * 
	 * @param utilityInputManager $inInputManager
	 * @return mvcControllerBase
	 */
	function setInputManager(utilityInputManager $inInputManager) {
		if ( $inInputManager !== $this->_InputManager ) {
			$this->_InputManager = $inInputManager;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Model
	 *
	 * @return mvcModelBase
	 */
	function getModel() {
		return $this->_Model;
	}
	
	/**
	 * Set $_Model to $inModel
	 *
	 * @param mvcModelBase $inModel
	 * @return mvcControllerBase
	 */
	function setModel($inModel) {
		if ( $inModel !== $this->_Model ) {
			$this->_Model = $inModel;
			$this->setModified();
		}
		return $this;
	}	

	/**
	 * Returns the current mvcRequest
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
	 * @return mvcControllerBase
	 */
	function setRequest(mvcRequest $inMvcRequest) {
		if ( $inMvcRequest !== $this->_MvcRequest ) {
			$this->_MvcRequest = $inMvcRequest;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_MvcResponse
	 *
	 * @return mvcResponse
	 */
	function getResponse() {
		return $this->_MvcResponse;
	}
	
	/**
	 * Set $_MvcResponse to $inMvcResponse
	 *
	 * @param mvcResponse $inMvcResponse
	 * @return mvcControllerBase
	 */
	function setResponse($inMvcResponse) {
		if ( $inMvcResponse !== $this->_MvcResponse ) {
			$this->_MvcResponse = $inMvcResponse;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns true if the controller requires authentication
	 *
	 * @return boolean
	 */
	function getRequiresAuthentication() {
		return $this->_RequiresAuthentication;
	}
	
	/**
	 * Set whether the controller requires authentication (true) or not (false)
	 *
	 * @param boolean $inRequiresAuthentication
	 * @return mvcControllerBase
	 */
	function setRequiresAuthentication($inRequiresAuthentication) {
		if ( $inRequiresAuthentication !== $this->_RequiresAuthentication ) {
			$this->_RequiresAuthentication = $inRequiresAuthentication;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the current class name
	 *
	 * @return string
	 */
	function getClassName() {
		return get_class($this);
	}

	/**
	 * Returns the current level this controller is at in the request; 0 is root
	 *
	 * @return integer
	 */
	function getControllerLevel() {
		if ( $this->getRequest()->getControllerMap() ) {
			return $this->getRequest()->getControllerMap()->getControllerLevel();
		}
		return 0;
	}

	/**
	 * Returns array of sub controllers for this controller
	 *
	 * @return array(mvcControllerMap)
	 */
	function getSubControllers() {
		if ( $this->getRequest()->getControllerMap() ) {
			return $this->getRequest()->getControllerMap()->getSubControllers();
		}
		return array();
	}
	
	/**
	 * Return ControllerActions
	 * 
	 * @return mvcControllerActions
	 */
	function getControllerActions() {
		if ( !$this->_ControllerActions instanceof mvcControllerActions ) {
			$this->_ControllerActions = new mvcControllerActions($this->_DefaultAction);
		}
		return $this->_ControllerActions;
	}
	
	/**
	 * Returns ControllerViews
	 *
	 * @return mvcControllerViews
	 */
	function getControllerViews() {
		if ( !$this->_ControllerViews instanceof mvcControllerViews ) {
			$this->_ControllerViews = new mvcControllerViews();
		}
		return $this->_ControllerViews;
	}
	
	/**
	 * Builds a URI for this controller with optional action and data; both should be strings
	 *
	 * @param string $inAction
	 * @param string $inData
	 * @return string
	 */
	function buildUriPath($inAction = null, $inData = null) {
		$uri = $this->getRequest()->getControllerMap()->getUriPath();
		
		if ( $inAction !== null ) {
			$uri .= '/'.$inAction;
		}
		if ( $inData !== null ) {
			$uri .= '/'.$inData;
		}
		return $uri;
	}
}