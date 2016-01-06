<?php
/**
 * systemException.class.php
 * 
 * systemException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemException
 * @version $Rev: 650 $
 */


/**
 * systemException class
 * 
 * Contains the main system exception that all other exceptions inherit from
 * 
 * @package scorpio
 * @subpackage system
 * @category systemException
 */
class systemException extends Exception {
	
	/**
	 * Copy of original supplied error message
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ErrorMessage		= false;
	
	
	
	/**
	 * Exception constructor
	 *
	 * @param string $inMessage
	 * @param integer $inCode
	 */
	function __construct($inMessage, $inCode = null) {
		$this->_ErrorMessage = $inMessage;
		if ( php_sapi_name() == 'cli' ) {
			parent::__construct($inMessage, $inCode);
		} else {
			parent::__construct(htmlspecialchars($inMessage), $inCode);
		}
	}
	
	/**
	 * Returns the un-modified error string
	 *
	 * @return string
	 */
	function getOriginalErrorMessage() {
		return $this->_ErrorMessage;
	}
}



/**
 * systemCannotBeInstantiated exception
 *
 * @package scorpio
 * @subpackage system
 * @category systemCannotBeInstantiated
 */
class systemCannotBeInstantiated extends systemException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct() {
		parent::__construct(__CLASS__." cannot be instantiated");
	}
}