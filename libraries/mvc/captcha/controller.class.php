<?php
/**
 * controller.class.php
 * 
 * mvcCaptchaController class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcCaptchaController
 * @version $Rev: 732 $
 */


/**
 * mvcCaptchaController class
 * 
 * Handles generating captcha images for the Scorpio framework. The controller
 * calls into the {@link mvcCaptchModel} for actual generation of the captcha.
 * This component is dependent on {@link mvcSessionBase} and {@link http://www.php.net/gd gd}.
 * GD requires that TTF font support be available.
 * 
 * Usage of this controller is the same as that for the {@link mvcImageController}.
 * Simply add a path to your controllerMap.xml file and set the class path to use
 * mvcCaptchaController.class.php:
 * 
 * <code>
 * <controller name="captcha" description="Captcha Generator" path="mvcCaptchaController.class.php" />
 * </code>
 * 
 * You must ensure that the session module is loaded either via expressly including
 * it during the dispatcher setup or via the config file. If using the config file
 * you must add the {@link mvcDistributorPluginSession} to the distributorPlugins sections.
 * 
 * Finally: in your views, simply link an image to the path you placed the captcha
 * controller on e.g. www.example.com/download/captcha and that's it. No extension
 * required.
 * 
 * If you wish to have an extension on the link, then ensure that the action
 * "generate" is set immediately after the controller name, for example:
 * http://www.example.com/download/captcha/generate/image.jpeg
 * 
 * This controller always returns a JPEG image unless the image cannot be created.
 * If the session has not been logged, a message will be written to the site log
 * file.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcCaptchaController
 */
class mvcCaptchaController extends mvcController {
	
	const ACTION_GENERATE = 'generate';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setRequiresAuthentication(false);
		$this->setDefaultAction(self::ACTION_GENERATE);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$width = $this->getRequest()->getDistributor()->getSiteConfig()->getParentParam('captcha', 'width');
		if ( !$width ) {
			$width = new systemConfigParam('width', 120, true);
		}
		$height = $this->getRequest()->getDistributor()->getSiteConfig()->getParentParam('captcha', 'height');
		if ( !$height ) {
			$height = new systemConfigParam('height', 40, true);
		}
		$length = $this->getRequest()->getDistributor()->getSiteConfig()->getParentParam('captcha', 'length');
		if ( !$length ) {
			$length = new systemConfigParam('length', 6, true);
		}
		
		$oModel = new mvcCaptchaModel();
		$code = $oModel->captchaSecurityImage($width->getParamValue(), $height->getParamValue(), $length->getParamValue());
		if ( $this->getRequest()->getSession() instanceof mvcSessionBase ) {
			$this->getRequest()->getSession()->setParam('captcha.code', $code);
		} else {
			systemLog::critical(__CLASS__.' called without active session; update config.xml file and enable: distributorPlugin -> mvcDistributorPluginSession');
		}
		
		header("Content-Type: image/jpeg");
		$oModel->outputImage();
		$oModel->destroyImage();
	}
}