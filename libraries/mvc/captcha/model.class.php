<?php
/**
 * model.class.php
 * 
 * mvcCaptchaModel class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcCaptchaModel
 * @version $Rev: 661 $
 */


/**
 * File: CaptchaSecurityImages.php
 * Author: Simon Jarvis
 * Copyright: 2006 Simon Jarvis
 * Date: 03/08/06
 * Updated: 07/02/07
 * Requirements: PHP 4/5 with GD and FreeType libraries
 * Link: http://www.white-hat-web-design.co.uk/articles/php-captcha.php
 * 
 * This program is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation; either version 2 
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
 * GNU General Public License for more details: 
 * http://www.gnu.org/licenses/gpl.html
 */


/**
 * mvcCaptchaModel class
 * 
 * Provides the "captcha" page model based on the above script, but modified for use here
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcCaptchaModel
 */
class mvcCaptchaModel extends mvcModelBase {

	/**
	 * Holds font name, which should map to a font in the same folder as the model and controller
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Font = 'monofont.ttf';
	
	/**
	 * Stores $_CaptchaImage
	 *
	 * @var string
	 * @access protected
	 */
	protected $_CaptchaImage;
	
	
	
	/**
	 * Returns new mvcCaptchaModel
	 *
	 * @return mvcCaptchaModel
	 */
	function __construct() {
		$this->_CaptchaImage = null;
		$this->_Font = dirname(__FILE__).system::getDirSeparator().$this->_Font;
	}
	
	
	
	/**
	 * Returns $_CaptchaImage
	 *
	 * @return string
	 * @access public
	 */
	function getCaptchaImage() {
		return $this->_CaptchaImage;
	}
	
	/**
	 * Set $_CaptchaImage to $inCaptchaImage
	 *
	 * @param string $inCaptchaImage
	 * @return mvcCaptchaModel
	 * @access public
	 */
	function setCaptchaImage($inCaptchaImage) {
		if ( $this->_CaptchaImage !== $inCaptchaImage ) {
			$this->_CaptchaImage = $inCaptchaImage;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Generates a captcha code of $inCharacters long
	 *
	 * @param integer $inCharacters
	 * @return string
	 * @access protected
	 */
	protected function _generateCode($inCharacters) {
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		$code = '';
		$i = 0;
		while ($i < $inCharacters) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}
	
	/**
	 * Generates the captcha image and stores it internally for use later, returns the generated code
	 *
	 * @param integer $inWidth Width in pixels
	 * @param integer $inHeight Height in pixels
	 * @param integer $inCharacters Number of characters in the captcha code
	 * @return string
	 */
	function captchaSecurityImage($inWidth = 120, $inHeight = 40, $inCharacters = 6) {
		$code = $this->_generateCode($inCharacters);
		/* font size will be 75% of the image height */
		$font_size = $inHeight * 0.85;
		
		if ( extension_loaded('gd') ) {
			$image = @imagecreate($inWidth, $inHeight);
			/* set the colours */
			$background_color = imagecolorallocate($image, 255, 255, 255);
			$text_color = imagecolorallocate($image, 20, 40, 100);
			$noise_color = imagecolorallocate($image, 100, 120, 180);
			/* generate random dots in background */
			for( $i=0; $i<($inWidth*$inHeight)/3; $i++ ) {
				imagefilledellipse($image, mt_rand(0,$inWidth), mt_rand(0,$inHeight), 1, 1, $noise_color);
			}
			/* generate random lines in background */
			for( $i=0; $i<($inWidth*$inHeight)/150; $i++ ) {
				imageline($image, mt_rand(0,$inWidth), mt_rand(0,$inHeight), mt_rand(0,$inWidth), mt_rand(0,$inHeight), $noise_color);
			}
			/* create textbox and add text */
			$textbox = imagettfbbox($font_size, 0, $this->_Font, $code);
			$x = ($inWidth - $textbox[4])/2;
			$y = ($inHeight - $textbox[5])/2;
			imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->_Font , $code);
			
			/* store image reference internally */
			$this->setCaptchaImage($image);
			return $code;
		}
		return '';
	}
	
	/**
	 * Exports the image stream as a jpeg
	 *
	 * @return void
	 */
	function outputImage() {
		imagejpeg($this->getCaptchaImage());
	}
	
	/**
	 * Destroys the crearted image
	 *
	 * @return void
	 */
	function destroyImage() {
		imagedestroy($this->getCaptchaImage());
	}
}