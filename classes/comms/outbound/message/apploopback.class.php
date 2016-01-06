<?php
/**
 * commsOutboundMessageAppLoopBack
 *
 * Stored in commsOutboundMessageAppLoopBack.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageAppLoopBack
 * @version $Rev: 10 $
 */


/**
 * commsOutboundMessageAppLoopBack Class
 *
 * Custom class for App Loop Back messages
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageAppLoopBack
 */
class commsOutboundMessageAppLoopBack extends commsOutboundMessage {

	/**
	 * Returns the gateway id
	 * 
	 * @return integer
	 */
	function getGatewayID() {
		return commsGateway::GW_APP_LOOP_BACK;
	}
	
	/**
	 * Returns the gateway account id
	 * 
	 * @return integer
	 */
	function getGatewayAccountID() {
		return commsGatewayAccount::GW_ACC_APP_LOOP_BACK;
	}
}