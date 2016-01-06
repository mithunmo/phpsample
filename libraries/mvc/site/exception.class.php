<?php
/**
 * mvcSiteConfigException.class.php
 * 
 * mvcSiteConfigException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcSiteConfigException
 * @version $Rev: 650 $
 */


/**
 * mvcSiteException
 * 
 * mvcSiteException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcSiteException
 */
class mvcSiteException extends mvcException {
	
}



/**
 * mvcSiteToolsException
 * 
 * mvcSiteToolsException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcSiteToolsException
 */
class mvcSiteToolsException extends mvcSiteException {
	
}



/**
 * mvcSiteConfigException
 * 
 * mvcSiteConfigException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcSiteConfigException
 */
class mvcSiteConfigException extends mvcSiteException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}

/**
 * mvcSiteConfigNoControllerMapDefined
 * 
 * mvcSiteConfigNoControllerMapDefined exception class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcSiteConfigNoControllerMapDefined
 */
class mvcSiteConfigNoControllerMapDefined extends mvcSiteConfigException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct() {
		parent::__construct("Fatal error: controllerMap could not be located for site");
	}
}