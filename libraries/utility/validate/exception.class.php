<?php
/**
 * utilityValidateException.class.php
 * 
 * utilityValidateException
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateException
 * @version $Rev: 650 $
 */


/**
 * utilityValidateException
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateException
 */
class utilityValidateException extends systemException {
	
}

/**
 * utilityValidateOptionException
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateOptionException
 */
class utilityValidateOptionException extends utilityValidateException {
	
	/**
	 * @see systemException::__construct()
	 *
	 * @param string $inValidator
	 * @param string $inOption
	 */
	function __construct($inValidator, $inOption) {
		parent::__construct("$inValidator requires the option ($inOption) be set");
	}
}