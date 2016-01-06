<?php
/**
 * commsInboundMessageSms
 *
 * Stored in commsInboundMessageSms.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage inbound
 * @category commsInboundMessageSms
 * @version $Rev: 10 $
 */


/**
 * commsInboundMessageSms Class
 *
 * Custom class for SMS type messages
 *
 * @package comms
 * @subpackage inbound
 * @category commsInboundMessageSms
 */
class commsInboundMessageSms extends commsInboundMessage {
	
	const PARAM_NETWORK_ID = 'message.networkID';
	
	/**
	 * Returns the mapped networkID sent by the gateway
	 * 
	 * @return integer
	 */
	function getNetworkID() {
		return $this->getParamSet()->getParam(self::PARAM_NETWORK_ID);
	}
	
	/**
	 * Sets the network ID for this message
	 * 
	 * @param integer $inNetworkID
	 * @return commsInboundMessageSms
	 */
	function setNetworkID($inNetworkID) {
		$this->getParamSet()->setParam(self::PARAM_NETWORK_ID, $inNetworkID);
		return $this;
	}
}