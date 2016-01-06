<?php
/**
 * scaffoldException
 *
 * Stored in scaffoldException.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage scaffold
 * @category scaffoldException
 * @version $Rev: 650 $
 */


/**
 * scaffoldException
 *
 * scaffoldException class
 *
 * @package scorpio
 * @subpackage scaffold
 * @category scaffoldException
 */
class scaffoldException extends systemException {

	/**
	 * Exception constructor
	 *
	 * @param string $message
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}