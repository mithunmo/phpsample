<?php
/**
 * commsOutboundMessageSms
 *
 * Stored in commsOutboundMessageSms.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageSms
 * @version $Rev: 10 $
 */


/**
 * commsOutboundMessageSms Class
 *
 * Custom class for SMS type messages.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageSms
 */
class commsOutboundMessageSms extends commsOutboundMessage {
	
	const PARAM_REQUIRES_ACK = 'message.sms.requiresAck';
	
	/**
	 * @see commsOutboundMessage::importFromApplicationMessage()
	 * 
	 * @param commsApplicationMessage $inMessage
	 * @return commsOutboundMessage
	 */
	function importFromApplicationMessage(commsApplicationMessage $inMessage) {
		
		return parent::importFromApplicationMessage($inMessage);
	}

	/**
	 * @see commsOutboundMessageBase::checkRecipient
	 * 
	 * @param string $inMessage
	 */
	protected function checkRecipient(&$inMessage = '') {
		$isValid = parent::checkRecipient($inMessage);
		if ( $isValid ) {
			$oValidator = new utilityValidateNumber();
			if ( !$oValidator->isValid($this->getRecipient()) ) {
				$isValid = false;
				$inMessage .= implode("\n", $oValidator->getMessages());
			}
			$oValidator = null;
		}
		return $isValid;
	}
	
	/**
	 * Returns true if the message requires an Acknowledgement
	 * 
	 * @return boolean
	 */
	function getRequiresAck() {
		return $this->getParamSet()->getParam(self::PARAM_REQUIRES_ACK);
	}
	
	/**
	 * Set if the message requires an acknowledgement
	 * 
	 * @param boolean $inAck
	 * @return commsOutboundMessageSms
	 */
	function setRequiresAck($inAck) {
		$this->getParamSet()->setParam(self::PARAM_REQUIRES_ACK, ($inAck === true ? 1 : 0));
		return $this;
	}
}