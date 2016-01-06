<?php
/**
 * commsException
 *
 * Stored in exception.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsException
 * @category commsException
 * @version $Rev: 10 $
 */


/**
 * commsException
 *
 * commsException class
 *
 * @package comms
 * @subpackage commsException
 * @category commsException
 */
class commsException extends systemException {

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
 * commsInboundException
 *
 * commsInboundException class
 *
 * @package comms
 * @subpackage inbound
 * @category commsInboundException
 */
class commsInboundException extends commsException {
	
}



/**
 * commsOutboundException
 *
 * commsOutboundException class
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundException
 */
class commsOutboundException extends commsException {
	
}