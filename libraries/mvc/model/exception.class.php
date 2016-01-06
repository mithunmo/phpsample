<?php
/**
 * mvcModelException.class.php
 * 
 * mvcModelException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcModelException
 * @version $Rev: 650 $
 */


/**
 * mvcModelException
 * 
 * mvcModelException class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcModelException
 */
class mvcModelException extends mvcException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}