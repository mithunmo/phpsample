<?php
/**
 * mvcStaticController
 *
 * Stored in controller.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcStaticController
 * @version $Rev: 732 $
 */


/**
 * mvcStaticController
 *
 * Handles requests for static pages. This controller inherits the
 * default security policy of the current site. If you wish to allow
 * open access, you will have to extend the class and override the
 * requiresAuthentication property.
 * 
 * Static pages can have any page name so long as it is a valid
 * URI name. Therefore this controller will always return true for
 * a valid action.
 * 
 * To enable this controller in your site, simply add a declaration in
 * your controllerMap.xml file that points to this class. This functions
 * in the same manner as {@link mvcImageController} and {@link mvcCaptchaController}.
 * 
 * <code>
 * <controller name="static" description="Static Pages" path="mvcStaticController.class.php" />
 * </code>
 * 
 * By default, the views should be located within a folder called "static"
 * in your sites views folder. For example: in a site example.com, you would
 * need to ensure that a folder "static" exists in the "views" folder of
 * the site: /websites/example.com/views/static
 * 
 * If a specific template cannot be located, then a default "static"
 * template is used. You should at the minimum create a static.html.tpl
 * file within the static folder to handle these situations.
 * 
 * If you want to provide additional functionality beyond what the default
 * controller / model can, you should extend them into your libraries
 * folder in your site and add the classes to the site classes section
 * of your config.xml file in the site folder.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcStaticController
 */
class mvcStaticController extends mvcController {

	const ACTION_VIEW = 'view';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
	}

	/**
	 * @see mvcControllerBase::isValidAction()
	 */
	function isValidAction() {
		return true;
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$oView = new mvcStaticView($this);
		$oView->showPage();
	}

	

	/**
	 * Fetches the model
	 *
	 * @return staticModel
	 */
	function getModel() {
		if ( !parent::getModel() ) {
			$this->buildModel();
		}
		return parent::getModel();
	}

	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new mvcStaticModel();
		$oModel->setPageName($this->getAction());
		$oModel->setRequest($this->getRequest());
		$this->setModel($oModel);
	}
}