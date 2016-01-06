<?php
/**
 * wurflException
 *
 * Stored in wurflException.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflException
 * @version $Rev: 650 $
 */


/**
 * wurflException
 *
 * wurflException class
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflException
 */
class wurflException extends systemException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $message
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}