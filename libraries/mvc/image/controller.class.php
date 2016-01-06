<?php
/**
 * mvcImageController
 *
 * Stored in mvcImageController.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcImageController
 * @version $Rev: 650 $
 */


/**
 * mvcImageController
 *
 * mvcImageController allows images to be programmatically generated, fetched or manipulated.
 * Each type of image requires a model component named 'typeModel.class.php'. Everything
 * before 'Model.class.php' will be treated as the action and will be used for routing the
 * request. For example: /download/image/funkyImage/IDENTIFIER/100x100/myimage.jpeg will be
 * routed to the 'funkyImageModel' class stored in 'funkyImageModel.class.php'.
 * 
 * Image processing is handled via the model, this includes caching and passthru. Once the
 * request is made, the model should dispatch it as fast as possible to increase response.
 * Ideally this system should not be used heavily as it may increase application rendering
 * time.
 * 
 * As with any controller, the image controller requires it be added to the controllerMap
 * file before requests will be routed to it. In your controllerMap file be sure to add the
 * following to the download controllers section:
 * <code>
 * <controller name="image" description="Image Generator" path="mvcImageController.class.php" />
 * </code>
 * 
 * The URI is interpreted by the following:
 * /download/image/MODEL/PRIMARY IDENTIFIER/DIMENSIONS/URINAME.EXTN
 * 
 * Where:
 * /download/image is the route to the central mvcImageController,
 * MODEL is the name of the model to be used for rendering,
 * PRIMARY IDENTIFIER is how the image should be referenced,
 * DIMENSIONS are the width and height to apply,
 * URINAME is the friendly URI name
 * EXTN is the extension
 * 
 * The PRIMARY IDENTIFIER is what is used to actually locate the image. This MUST be
 * added to the URI request and must always occur after the MODEL name. Dimensions are
 * optional, it is up to the model whether or not they are used. URINAME is simply an
 * additional string with extension to make the URI look like an actual file. This is
 * important for mobile devices that may use extensions for mime information. All these
 * parameters are parsed out and passed to the model. It is up to the implementation
 * if DIMENSIONS should be honoured.
 * 
 * The model itself should implement the mvcImageProcessor interface and (optionally)
 * extend the {@link mvcImageModel} class. Place your mvcImageProcessor models in an
 * "images" folder inside the libraries folder of the site. These can be shared as
 * the the site config is used to build a path to the library.
 * 
 * Note: in previous versions of Scorpio, mvcImageProcessor had to be loaded via
 * the config file. This is now no longer the case. Ensure that this has been removed
 * from your config files if moving from Scorpio 0.2.X to 0.3+
 *  
 * Note: by default all images are served statically via a mod-rewrite rule. i.e. calls to
 * something like, /download/image/X/Y/Z.jpg will cause a JPEG file named Z in the path
 * /download/image/X/Y to be served, generating a 404 error if the Z.jpg does not exist.
 * This can be changed by either:
 *  1. using an alternative extension on the file request (e.g. .jpeg, .giff etc)
 *  2. add a mod-rewrite exception for all files (or selection of files) in /download/image
 *  3. run actual static images from a physically separate path (not /web/base/)
 * 
 * While option 1 is fine for JPEGs, it is not recommended for other file-types, especially
 * if serving images to mobile devices.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcImageController
 */
class mvcImageController extends mvcController {
	
	const ACTION_VIEW = 'view';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setRequiresAuthentication(false);
		$this->setDefaultAction(self::ACTION_VIEW);
		
		if ( !interface_exists('mvcImageProcessor') ) {
			throw new mvcControllerException("Failed to load mvcImageProcessor Interface, check autoload cache files");
		}
	}
	
	/**
	 * Override site set isValid() and always return true
	 * 
	 * @see data/tests/mvc/mvcController#isValidAction()
	 * @return boolean
	 */
	function isValidAction() {
		return true;
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		/*
		 * fetch vars from URI request
		 */
		$params = array();
		$params[mvcImageModel::OPTION_IMAGE_MODEL] = $this->getAction();
		$params[mvcImageModel::OPTION_IMAGE_IDENTIFIER] = $this->getActionFromRequest(false, 1);
		$params[mvcImageModel::OPTION_IMAGE_DIMENSIONS] = $this->getActionFromRequest(false, 2);
		$params[mvcImageModel::OPTION_IMAGE_URINAME] = $this->getActionFromRequest(false, 3);
		$params[mvcImageModel::OPTION_SITE_IMAGES_PATH] = utilityStringFunction::cleanDirSlashes(
			system::getConfig()->getPathWebsites().'/base/themes/'.
			$this->getRequest()->getDistributor()->getSiteConfig()->getTheme()->getParamValue().'/images'
		);
		
		/*
		 * Attempt to load class
		 */
		$classname = $params[mvcImageModel::OPTION_IMAGE_MODEL].'Model';
		$file = $this->getRequest()->getDistributor()->getLibraryFile($classname.'.class.php', 'images');
		if ( !file_exists($file) || !is_readable($file) ) {
			systemLog::error('Failed to load class '.$classname.' from path ['.dirname($file).'] to handle image request');
			header('HTTP/1.0 404 Not Found');
			exit;
		}
		
		if ( system::getConfig()->isProduction() ) {
			@include($file);
		} else {
			include($file);
		}

		try {
			systemLog::getInstance()->getSource()->setSource('Image', $classname);
			$oModel = new $classname;
			$oModel->setOptions($params);
			$oModel->validateOptions();
			$oModel->render();
			
			/*
			 * Unlike other PHP scripts, we WANT the image to be cached by the client
			 */
			header('HTTP/1.1 200 OK');
			header('Content-Type: '.$oModel->getImageMimeType());
			header('Expires: '.date('D, d M Y H:i:s T', strtotime('+3 months')));
			header('Cache-Control: public, post-check=1, pre-check=1');
			header('Pragma: public');
			header('Content-Length: '.filesize($oModel->getImageLocation()));
			ob_clean();
			flush();
			readfile($oModel->getImageLocation());
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			header('HTTP/1.0 404 Not Found');
		}
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}
	
	/**
	 * Fetches the model
	 *
	 * @return mvcImageProcessor
	 */
	function getModel() {
		return parent::getModel();
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		
	}
}