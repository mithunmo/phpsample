<?php
/**
 * mofilmException
 *
 * Stored in exception.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package scorpio
 * @subpackage mofilm
 * @category mofilmException
 * @version $Rev: 10 $
 */


/**
 * mofilmException
 *
 * mofilmException class
 * 
 * @package scorpio
 * @subpackage mofilm
 * @category mofilmException
 */
class mofilmException extends systemException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $message
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}