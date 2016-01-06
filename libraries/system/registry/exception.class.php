<?php
/**
 * systemRegistryException.class.php
 * 
 * systemRegistryException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemRegistryException
 * @version $Rev: 650 $
 */


/**
 * systemRegistry exception
 *
 * @package scorpio
 * @subpackage system
 * @category systemRegistryException
 */
class systemRegistryException extends systemException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($inMessage, $inCode = null) {
		parent::__construct($inMessage, $inCode);
	}
}



/**
 * systemRegistryInstanceNotFound exception
 *
 * @package scorpio
 * @subpackage system
 * @category systemRegistryInstanceNotFound
 */
class systemRegistryInstanceNotFound extends systemRegistryException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($inKey) {
		parent::__construct("$inKey has no instance in the registry");
	}
}



/**
 * systemRegistryKeyWasNull exception
 * 
 * @package scorpio
 * @subpackage system
 * @category systemRegistryKeyWasNull
 */
class systemRegistryKeyWasNull extends systemRegistryException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct() {
		parent::__construct('Supplied key was null or had no value');
	}
}