<?php
/**
 * systemLog Class Exception
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLogException
 * @version $Rev: 650 $
 */


/**
 * systemLog Class Exception
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLogException
 */
class systemLogException extends systemException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($inMessage, $inCode = null) {
		parent::__construct($inMessage, $inCode);
	}
}



/**
 * systemLogNoLogFileSpecified exception
 *
 * @package scorpio
 * @subpackage system
 * @category systemLogNoLogFileSpecified
 */
class systemLogNoLogFileSpecified extends systemLogException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($inLogFile) {
		parent::__construct('No log file specified ('.$inLogFile.') for file writer');
	}
}



/**
 * systemLogWritingToFileFailed exception
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLogWritingToFileFailed
 */
class systemLogWritingToFileFailed extends systemLogException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($inLogFile) {
		parent::__construct('Unable to write to log file ('.$inLogFile.'), please check permissions');
	}
}