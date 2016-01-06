<?php
/**
 * mvcException.class.php
 * 
 * mvcException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcException
 * @version $Rev: 650 $
 */


/**
 * mvcException
 * 
 * mvcException class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcException
 */
class mvcException extends systemException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}





/**
 * mvcAutoloadException
 * 
 * mvcAutoloadException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcAutoloadException
 */
class mvcAutoloadException extends mvcException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}

/**
 * mvcAutoloadClassCouldNotBeLoaded
 *
 * mvcAutoloadClassCouldNotBeLoaded class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcAutoloadClassCouldNotBeLoaded
 */
class mvcAutoloadClassCouldNotBeLoaded extends mvcAutoloadException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($inClassname) {
		parent::__construct("$inClassname could not be located by the mvcAutoload system");
	}
}