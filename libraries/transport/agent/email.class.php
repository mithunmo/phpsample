<?php
/**
 * transportAgentEmail class
 * 
 * Stored in transportAgentEmail.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage transport
 * @category transportAgentEmail
 * @version $Rev: 743 $
 */


/**
 * transportAgentEmail class
 * 
 * Handles sending emails via the default mailer: PHPMailer. Emails will be encoded
 * with both a plain text and HTML body if the message is flagged as being a HTML
 * mail.
 * 
 * This mail sender does not support embedding of images in email messages or other
 * attachments. If you require attachments or images, then consider replacing this
 * transport agent with a custom solution that can embed images on the fly during
 * the sending process.
 * 
 * For better speed you can replace this agent with one that uses SMTP.
 * 
 * @package scorpio
 * @subpackage transport
 * @category transportAgentEmail
 */
class transportAgentEmail extends transportAgentBase {
	
	/**
	 * Stores TransportName
	 * 
	 * @var string
	 */
	protected $_TransportName			= 'Email Transport';
	
	
	
	/**
	 * Main methods
	 */
	
	/**
	 * @see transportAgentBase::_process()
	 */
	protected function _process() {
		$oEmail = new utilityMail();
		$oEmail->AddAddress($this->getEmailRecipient());
		if ( !$this->getEmailSender() ) {
			$oEmail->From = system::getConfig()->getSystemFromAddress()->getParamValue();
			$oEmail->FromName = system::getConfig()->getSystemFromAddress()->getParamValue();
		} else {
			$oEmail->From = $this->getEmailSender();
			$oEmail->FromName = $this->getEmailSender();
		}
		
		$oEmail->Subject = $this->getEmailSubject();
		$oEmail->Body = $this->getEmailBody();
		if ( $this->isEmailHtml() ) {
			$oEmail->IsHTML(true);
			$oEmail->AltBody = trim(
				strip_tags(
					preg_replace(
						'/<\s*style.+?<\s*\/\s*style.*?>/si',
						'',
						str_replace(
							array('</p>', '<br />', '<br>'),
							array("</p>\r\n", "<br />\r\n", "<br>\r\n"),
							$this->getEmailBody()
						)
					)
				)
			);
		}
		
		$oResponse = new transportResponse();
		$oResponse->setRequest($this->getTransportCredentials());
		if ( $oEmail->Send() ) {
			$oResponse->setResponse('sent');
			$return = true;
		} else {
			$oResponse->setResponse($oEmail->ErrorInfo);
			$return = false;
		}
		$this->setResponse($oResponse);
		return $return;
	}
	
	/**
	 * @see transportAgentBase::_reset()
	 */
	protected function _reset() {
	}
	
	
	
	/**
	 * Helper methods for convenience and legibility
	 */
	
	/**
	 * Returns true if message is html
	 *
	 * @return boolean
	 */
	function isEmailHtml() {
		if ( $this->getTransportCredentials()->getParam(transportCredentials::PARAM_EMAIL_BODY_TYPE) == transportCredentials::PARAM_EMAIL_HTML ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Return the message header that matches the message body
	 *
	 * @return string
	 */
	function getEmailSubject() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_MESSAGE_SUBJECT);
	}
	
	/**
	 * Return the message body to be sent
	 *
	 * @return string
	 */
	function getEmailBody() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_MESSAGE_BODY);
	}
	
	/**
	 * Return email recipient
	 *
	 * @return string
	 */
	function getEmailRecipient() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_MESSAGE_RECIPIENT);
	}
	
	/**
	 * Return email sender
	 *
	 * @return string
	 */
	function getEmailSender() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_MESSAGE_SENDER);
	}
}