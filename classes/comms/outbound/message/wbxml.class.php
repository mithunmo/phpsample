<?php
/**
 * commsOutboundMessageWbxml
 *
 * Stored in commsOutboundMessageWbxml.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageWbxml
 * @version $Rev: 10 $
 */


/**
 * commsOutboundMessageWbxml Class
 *
 * Custom class for WBXML Rights Object type messages.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageWbxml
 */
class commsOutboundMessageWbxml extends commsOutboundMessageSms {
	
	/**
	 * Override charge, WXML messages cannot be billed
	 * 
	 * @param float $inCharge
	 * @return commsOutboundMessageWbxml
	 */
	function setCharge($inCharge) {
		return $this;
	}
}