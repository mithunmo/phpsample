<?php
/**
 * mvcViewException.class.php
 * 
 * mvcViewException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewException
 * @version $Rev: 760 $
 */


/**
 * mvcViewException
 * 
 * mvcViewException class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewException
 */
class mvcViewException extends mvcException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}

/**
 * mvcViewInvalidTemplateException
 * 
 * mvcViewInvalidTemplateException class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewInvalidTemplateException
 */
class mvcViewInvalidTemplateException extends mvcViewException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($inTemplate, $inPath = null, $inController = '', $inView = '') {
		parent::__construct(
			"Failed to locate template: $inTemplate".($inPath !== null ? " in path $inPath" : '').
			" from controller: $inController and view: $inView"
		);
	}
}