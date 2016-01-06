<?php
/**
 * generatorException class
 * 
 * Stored in exception.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage generator
 * @category generatorException
 * @version $Rev: 650 $
 */


/**
 * generatorException
 * 
 * generatorException class
 *
 * @package scorpio
 * @subpackage generator
 * @category generatorException
 */
class generatorException extends systemException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}