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
class mofilmCommsTransportAmazonSESEmail extends mofilmCommsTransportBaseEmail {

	/**
	 * Stores TransportName
	 *
	 * @var string
	 */
	protected $_TransportName = 'MOFILM/Amazon SES Email Transport';


	/**
	 * Main methods
	 */

	/**
	 * @see transportAgentBase::_process()
	 */
	protected function _process() {
		$accessKey = system::getConfig()->getParam("amazonses", "accesskey");
		$secretKey = system::getConfig()->getParam("amazonses", "secretkey");
		
		$oSesMessage = new mofilmAmazonSESMailer($accessKey,$secretKey);
		$oSesMessage->AddAddress($this->getEmailRecipient());
		//$senderInfo = preg_split("/:/",$this->getEmailSender());
		/*
		if ( !$this->getEmailSender() ) {
			$oSesMessage->SetFrom(system::getConfig()->getSystemFromAddress()->getParamValue(), "MOFILM");
		} else {
			$senderInfo = preg_split("/:/",$this->getEmailSender());
			$oSesMessage->SetFrom($senderInfo[1],$senderInfo[0]);
		}
		*/
		$oSesMessage->SetFrom(system::getConfig()->getSystemFromAddress()->getParamValue(), "MOFILM");
		$oSesMessage->Subject = $this->getEmailSubject();
		if ( $this->isEmailHtml() ) {
			if ( stripos($this->getEmailBody(), '<img ') !== false ) {
				$matches = array();
				preg_match_all('/src="(\S+)"/i', $this->getEmailBody(), $matches);
				if ( count($matches[1]) > 0 ) {
					foreach ( $matches[1] as $match ) {
						if ( stripos($match, 'http') === false ) {					
							$path = utilityStringFunction::cleanDirSlashes(system::getConfig()->getPathWebsites().'/base/'.$match);
							if ( !$oSesMessage->AddEmbeddedImage($path, pathinfo($match, PATHINFO_FILENAME),pathinfo($match, PATHINFO_FILENAME).".".$this->get_file_extension($path), "base64", "image/".$this->get_file_extension($path)) ) {
								systemLog::error($oSesMessage->ErrorInfo);
							}
						}
					}
					$this->getTransportCredentials()->setMessageBody(
						preg_replace_callback('/src="(\S+)"/i', array($this, 'replaceImageTags'), $this->getEmailBody())
					);
				}
			}

			$oSesMessage->IsHTML(true);
		}
		$oSesMessage->Body = $this->getEmailBody();
		$oSesMessage->AltBody = $this->getEmailBodyText();

		$oResponse = new transportResponse();
		$oResponse->setRequest($this->getTransportCredentials());
		if ( $this->getEmailAttachment() != "" ) {
			$oSesMessage->AddAttachment($this->getEmailAttachment());
		}
		$amazonSESResponse = $oSesMessage->Send();
		if ( $amazonSESResponse != false ) {
			$oResponse->setResponse($amazonSESResponse->body->SendRawEmailResult->MessageId);
			$return = ($amazonSESResponse->isOK()) ? 1 : 0;
			if ($return) {
				$return = true;
			} else {
				$return = false;
			}
			$this->setResponse($oResponse);
			return $return;
		} else {
			$oResponse->setResponse("failed");
			$this->setResponse($oResponse);
			return false;
		}
	}


}