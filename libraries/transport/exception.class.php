<?php
/**
 * transportException
 *
 * Stored in transportException.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage transport
 * @category transportException
 * @version $Rev: 650 $
 */


/**
 * transportException
 *
 * transportException class
 *
 * @package scorpio
 * @subpackage transport
 * @category transportException
 */
class transportException extends systemException {

	/**
	 * Exception constructor
	 *
	 * @param string $message
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}