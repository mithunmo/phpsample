<?php
/**
 * fileObjectException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage file
 * @category fileObjectException
 * @version $Rev: 650 $
 */


/**
 * fileObjectException class
 * 
 * @package scorpio
 * @subpackage file
 * @category fileObjectException
 */
class fileObjectException extends systemException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $message
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}