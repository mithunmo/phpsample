<?php
/**
 * mofilmSystemAPIException
 *
 * Stored in apiexception.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package scorpio
 * @subpackage mofilm
 * @category mofilmSystemAPIException
 * @version $Rev: 10 $
 */


/**
 * mofilmSystemAPIException
 *
 * mofilmSystemAPIException class
 * 
 * @package mofilm
 * @subpackage mofilm
 * @category mofilmSystemAPIException
 */
class mofilmSystemAPIException extends mofilmException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $message
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}

/**
 * mofilmSystemAPITokenTimeoutException
 *
 * mofilmSystemAPITokenTimeoutException class
 *
 * @package mofilm
 * @subpackage mofilm
 * @category mofilmSystemAPITokenTimeoutException
 */
class mofilmSystemAPITokenTimeoutException extends mofilmSystemAPIException {

	/**
	 * Exception constructor
	 *
	 * @param string $inToken
	 */
	function __construct($inToken) {
		parent::__construct(sprintf('Token (%s) has expired', $inToken));
	}
}