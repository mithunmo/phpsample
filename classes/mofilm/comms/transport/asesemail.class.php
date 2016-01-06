<?php
/**
 * mofilmCommsTransportEmail class
 *
 * Stored in asesmail.class.php
 *
 * @author Mithun Mohan
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage comms
 * @category mofilmCommsTransportASESEmail
 * @version $Rev: 10 $
 */

/**
 * mofilmCommsTransportASESEmail class
 *
 * Custom MOFILM email transport that will embed linked images into the email
 * before they are sent.
 *
 * @package mofilm
 * @subpackage comms
 * @category mofilmCommsTransportEmail
 */
class mofilmCommsTransportASESEmail extends transportAgentBase {

	/**
	 * Stores TransportName
	 *
	 * @var string
	 */
	protected $_TransportName = 'MOFILM/ASES Email Transport';


	/**
	 * Main methods
	 */

	/**
	 * @see transportAgentBase::_process()
	 */
	protected function _process() {

		$accessKey = system::getConfig()->getParam("amazonses", "accesskey");
		$secretKey = system::getConfig()->getParam("amazonses", "secretkey");

		$oConnection = new mofilmSimpleEmailService($accessKey, $secretKey);
		$oSesMessage = new mofilmSimpleEmailServiceMessage();
		$oSesMessage->addTo($this->getEmailRecipient());
		//$oSesMessage->setFrom('Mofilm <it@mofilm.com>');
		$senderEmail = $this->getEmailSender();
		if(isset ($senderEmail)) {
			$oSesMessage->setFrom($this->getEmailSender());
		} else {
			$oSesMessage->setFrom('Mofilm <it@mofilm.com>');
		}
		$oSesMessage->setSubject($this->getEmailSubject());

		$textVersion = '';
		/*
		if ( $this->isEmailHtml() ) {
			$textVersion = trim(
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
		 * 
		 */
		$textVersion = $this->getEmailBodyText();
		$oSesMessage->setMessageFromString($textVersion, $this->getEmailBody());

		$oResponse = new transportResponse();
		$oResponse->setRequest($this->getTransportCredentials());

		$oArray = $oConnection->sendEmail($oSesMessage);
		if ( is_array($oArray) ) {
			$oResponse->setResponse($oArray["MessageId"]);
			$return = true;
		} else {
			$oResponse->setResponse('failed');
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

	function getEmailBodyText() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_MESSAGE_BODY_TEXT);
	}
}