<?php
/**
 * mofilmCommsTransportBaseEmail class
 * 
 * Stored in mofilmCommsTransportBaseEmail.class.php
 * 
 * @author Mithun Mohan
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage comms
 * @category mofilmCommsTransportBaseEmail
 * @version $Rev: 192 $
 */

/**
 * mofilmCommsTransportBaseEmail class
 * 
 * Custom MOFILM email transport that will embed linked images into the email
 * before they are sent.
 * 
 * @package mofilm
 * @subpackage comms
 * @category mofilmCommsTransportBaseEmail
 */
abstract class mofilmCommsTransportBaseEmail extends transportAgentBase {
	
	/**
	 * Stores TransportName
	 * 
	 * @var string
	 */
	protected $_TransportName = 'MOFILM Base Email Transport';
	
	
	
	/**
	 * Main methods
	 */
	
	/**
	 * @see transportAgentBase::_process()
	 */
	protected function _process() {
		
	}
	
	/**
	 * @see transportAgentBase::_reset()
	 */
	protected function _reset() {
	}
	
	/**
	 * Returns an image src attribute reset to use an internal cid: link
	 * 
	 * @param array $matches
	 * @return string
	 */
	function replaceImageTags($matches) {
		if ( stripos($matches[1], 'http') === false ) {
			$filestub = pathinfo($matches[1], PATHINFO_FILENAME);
			return 'src="cid:'.$filestub.'"';
		} else {
			return 'src="'.$matches[1].'"';
		}
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

	/**
	 * Returns the path of the attachement 
	 * 
	 * @return string 
	 */
	function getEmailAttachment() {
		return $this->getTransportCredentials()->getParam(commsOutboundMessageEmail::PARAM_ATTACH);
	}
	
	/**
	 * Returns text version to be sent
	 * 
	 * @return string 
	 */
	function getEmailBodyText() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_MESSAGE_BODY_TEXT);
	}

	/**
	 * Returns the extension of the file based on the path name
	 *
	 * @param string $inPath
	 * @return string
	 */
	function get_file_extension($inPath) {
		$extn = pathinfo($inPath, PATHINFO_EXTENSION);
		if ( $extn == "jpeg" || $extn == "jpg") {
			return "jpeg";
		} else {
			return $extn;
		}
	}
	
}