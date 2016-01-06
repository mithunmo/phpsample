<?php
/**
 * reportCentreException
 *
 * Stored in exception.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package scorpio
 * @subpackage reportCentre
 * @category reportCentreException
 * @version $Rev: 10 $
 */


/**
 * reportCentreException
 *
 * reportCentreException class
 * 
 * @package scorpio
 * @subpackage reportCentre
 * @category reportCentreException
 */
class reportCentreException extends systemException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $message
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}