<?php
/**
 * mvcDistributorException.class.php
 * 
 * mvcDistributorException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorException
 * @version $Rev: 650 $
 */


/**
 * mvcDistributorException
 * 
 * mvcDistributorException class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorException
 */
class mvcDistributorException extends mvcException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($inMessage) {
		parent::__construct($inMessage);
	}
}

/**
 * mvcDistributorOfflineException
 * 
 * mvcDistributorOfflineException class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorOfflineException
 */
class mvcDistributorOfflineException extends mvcException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct() {
		parent::__construct('The system or site is currently offline');
	}
}

/**
 * mvcDistributorDatabaseOfflineException
 * 
 * mvcDistributorDatabaseOfflineException class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorDatabaseOfflineException
 */
class mvcDistributorDatabaseOfflineException extends mvcException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct() {
		parent::__construct('System database is currently un-available');
	}
}

/**
 * mvcDistributorInvalidRequestException
 * 
 * mvcDistributorInvalidRequestException class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorInvalidRequestException
 */
class mvcDistributorInvalidRequestException extends mvcDistributorException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($inRequest) {
		parent::__construct('Failed to route request ('.$inRequest.') to a valid controller');
	}
}

/**
 * mvcDistributorInvalidActionException
 * 
 * mvcDistributorInvalidActionException class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorInvalidActionException
 */
class mvcDistributorInvalidActionException extends mvcDistributorException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($inRequest, $inAction) {
		parent::__construct('Specified action ('.$inAction.') is not valid for the request ('.$inRequest.')');
	}
}