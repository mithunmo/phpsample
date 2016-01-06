<?php
/**
 * mofilmException
 *
 * Stored in exception.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package scorpio
 * @subpackage mofilm
 * @category momusicException
 * @version $Rev: 10 $
 */


/**
 * momusicException
 *
 * momusicException class
 * 
 * @package scorpio
 * @subpackage mofilm
 * @category momusicException
 */
class momusicException extends systemException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $message
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}