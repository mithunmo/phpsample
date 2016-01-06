<?php
/**
 * commsOutboundMessageEmail
 *
 * Stored in commsOutboundMessageEmail.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageEmail
 * @version $Rev: 278 $
 */


/**
 * commsOutboundMessageEmail Class
 *
 * Custom class for email messages.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageEmail
 */
class commsOutboundMessageEmail extends commsOutboundMessage {
	
	const PARAM_FROM_ADDRESS = 'message.email.sender';
	const PARAM_FROM_ADDRESS_DISPLAY_TEXT = 'message.email.senderDisplayText';
	const PARAM_IS_HTML = 'message.email.isHtml';
	const PARAM_ATTACH = 'message.attachment';
	
	/**
	 * @see commsOutboundMessage::importFromApplicationMessage()
	 * 
	 * @param commsApplicationMessage $inMessage
	 * @return commsOutboundMessage
	 */
	function importFromApplicationMessage(commsApplicationMessage $inMessage) {
		parent::importFromApplicationMessage($inMessage);
		
		$this->setIsHtml($inMessage->getIsHtml());
		$this->setMessageSubject($inMessage->getMessageHeader());
		/*
		 * Add \r\n to the body to stop mail client issues
		 */
		$body = $this->getMessageBody();
		$body = str_replace("/p>","/p>\r\n", $body);
		$body = str_replace('br>',"br>\r\n", $body);
		$body = str_replace('br />',"br />\r\n", $body);
		$this->setMessageBody($body);
		
		return $this;
	}
	
	/**
	 * @see commsOutboundMessage::_getTransportCredentials()
	 * 
	 * @param array $inCredentials
	 */
	protected function _getTransportCredentials(array &$inCredentials) {
		$inCredentials[transportCredentials::PARAM_EMAIL_BODY_TYPE]
			= $this->getIsHtml() ? transportCredentials::PARAM_EMAIL_HTML : transportCredentials::PARAM_EMAIL_TEXT;
		$inCredentials[self::PARAM_ATTACH] = $this->getMessageAttachment();

	}
	
	/**
	 * @see commsOutboundMessageBase::checkRecipient
	 * 
	 * @param string $inMessage
	 */
	protected function checkRecipient(&$inMessage = '') {
		$isValid = parent::checkRecipient($inMessage);
		if ( $isValid ) {
			$oValidator = new utilityValidateEmailAddress();
			if ( !$oValidator->isValid($this->getRecipient()) ) {
				$isValid = false;
				$inMessage .= implode("\n", $oValidator->getMessages());
			}
			$oValidator = null;
		}
		return $isValid;
	}
	
	/**
	 * Returns the email gateway id
	 * 
	 * @return integer
	 */
	function getGatewayID() {
		return commsGateway::GW_EMAIL;
	}
	
	/**
	 * Returns the email gateway account id
	 * 
	 * @return integer
	 */
	function getGatewayAccountID() {
		return commsGatewayAccount::GW_ACC_EMAIL;
	}
		
	/**
	 * Returns the sender email address
	 * 
	 * @return string
	 */
	function getFromAddress() {
		return $this->getParamSet()->getParam(self::PARAM_FROM_ADDRESS);
	}
	
	/**
	 * Sets the sender email address
	 * 
	 * @param string $inEmail
	 * @return commsOutboundMessageEmail
	 */
	function setFromAddress($inEmail) {
		$this->getParamSet()->setParam(self::PARAM_FROM_ADDRESS, $inEmail);
		return $this;
	}
		
	/**
	 * Returns the sender display text
	 * 
	 * @return string
	 */
	function getFromAddressDisplayText() {
		return $this->getParamSet()->getParam(self::PARAM_FROM_ADDRESS_DISPLAY_TEXT);
	}
	
	/**
	 * Sets the senders display text
	 * 
	 * @param string $inDisplayText
	 * @return commsOutboundMessageEmail
	 */
	function setFromAddressDisplayText($inDisplayText) {
		$this->getParamSet()->setParam(self::PARAM_FROM_ADDRESS_DISPLAY_TEXT, $inDisplayText);
		return $this;
	}
		
	/**
	 * Returns true if the message contains HTML
	 * 
	 * @return boolean
	 */
	function getIsHtml() {
		return $this->getParamSet()->getParam(self::PARAM_IS_HTML) == 1;
	}
	
	/**
	 * Sets whether the email is HTML or not
	 * 
	 * @param boolean $inStat
	 * @return commsOutboundMessageEmail
	 */
	function setIsHtml($inStat) {
		$this->getParamSet()->setParam(self::PARAM_IS_HTML, ($inStat == true ? 1 : 0));
		return $this;
	}
	
	/**
	 * Sets the path of the attachement 
	 * 
	 * @param string $inPath
	 * @return commsOutboundMessageEmail 
	 */
	function setMessageAttachement($inPath) {
		$this->getParamSet()->setParam(self::PARAM_ATTACH, $inPath);
		return $this;
	}
	
	/**
	 * Gets the path of the attachement 
	 * 
	 * @return string 
	 */
	function getMessageAttachment() {
		return $this->getParamSet()->getParam(self::PARAM_ATTACH);
	}
}