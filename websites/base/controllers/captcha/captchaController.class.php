<?php
/**
 * captchaController
 *
 * Stored in captchaController.class.php
 * 
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category captchaController
 * @version $Rev: 1 $
 */


/**
 * captchaController
 *
 * captchaController class
 * 
 * @package websites_base
 * @subpackage controllers
 * @category captchaController
 */
class captchaController extends mvcController {
	
	const ACTION_GENERATE_CAPTCHA = 'captcha';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_GENERATE_CAPTCHA);
		$this->setRequiresAuthentication(FALSE);
		
		$this->getControllerActions()->addAction(self::ACTION_GENERATE_CAPTCHA);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		if ( $this->getAction() == self::ACTION_GENERATE_CAPTCHA ) {
			$this->generateCaptcha();
		} else {
			$this->redirect("/account/register");
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
	
	function generateCaptcha() {
	    $this->setRequiresAuthentication(false);
	    $_SESSION['mofilm_captcha_reg_number']=rand(1000,99999);
	    if ( isset ($_SESSION['mofilm_captcha_reg_number']) ) {
		systemLog::message('asdfdsfsfsf');
		    	/*
				we create out image from the existing jpg image.
				You can replace that image with another of the 
				same size.
			*/
			$img=imagecreatefromjpeg("resources/captcha/texture.jpg");
			/*
				defines the text we use in our image,
				in our case the security number defined
				in index.php
			*/
			$security_number = empty($_SESSION['mofilm_captcha_reg_number']) ? 'error' : $_SESSION['mofilm_captcha_reg_number'];
			$image_text=$security_number;
			
			/*
				we define 3 random numbers that will
				eventually create our text color code (RGB)
			*/
			$red=rand(100,255); 
			$green=rand(100,255);
			$blue=rand(100,255);
			/*
				in order to have different color for our text, 
				we substract from the maximum 255 the random
				number generated above
			*/
			$text_color=imagecolorallocate($img,255-$red,255-$green,255-$blue);

			/*
				this adds the text stored in $image_text to our 
				capcha image
			*/
			$text=imagettftext($img,16,rand(-10,10),rand(10,30),rand(25,35),$text_color,"themes/mofilm/css/fonts/nwgthc.ttf",$image_text);
			/*
				we tell the browser that he's dealing
				with a jpg image, although that's not true,
				he will have to belive us
			*/
			header("Content-type:image/jpeg");
			header("Content-Disposition:inline ; filename=secure.jpg");	
			imagejpeg($img);
	    }
	}
	
	/**
	 * Fetches the model
	 *
	 * @return momusicModel
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
		$oModel = new captchaModel();
		$this->setModel($oModel);
	}
}