<?php
/**
 * mofilmCommsTransportEmail class
 * 
 * Stored in mofilmCommsTransportEmail.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage comms
 * @category mofilmCommsTransportEmail
 * @version $Rev: 292 $
 */

/**
 * mofilmCommsTransportEmail class
 * 
 * Custom MOFILM email transport that will embed linked images into the email
 * before they are sent.
 * 
 * @package mofilm
 * @subpackage comms
 * @category mofilmCommsTransportEmail
 */
class mofilmCommsTransportEmail extends mofilmCommsTransportBaseEmail {
	
	/**
	 * Stores TransportName
	 * 
	 * @var string
	 */
	protected $_TransportName = 'MOFILM Email Transport';
	
	
	
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
			$senderInfo = preg_split("/:/",$this->getEmailSender());
			$oEmail->SetFrom($senderInfo[1],$senderInfo[0]);
		}
		
		$oEmail->Subject = $this->getEmailSubject();
		if ( $this->isEmailHtml() ) {
			if ( stripos($this->getEmailBody(), '<img ') !== false ) {
				$matches = array();
				preg_match_all('/src="(\S+)"/i', $this->getEmailBody(), $matches);
				if ( count($matches[1]) > 0 ) {
					foreach ( $matches[1] as $match ) {
						if ( stripos($match, 'http') === false ) {
							$path = utilityStringFunction::cleanDirSlashes(system::getConfig()->getPathWebsites().'/base/'.$match);
							if ( !$oEmail->AddEmbeddedImage($path, pathinfo($match, PATHINFO_FILENAME),pathinfo($match, PATHINFO_FILENAME).".".$this->get_file_extension($path), "base64", "image/".$this->get_file_extension($path)) ) {
								systemLog::error($oEmail->ErrorInfo);
							}
						}
					}
					$this->getTransportCredentials()->setMessageBody(
						preg_replace_callback('/src="(\S+)"/i', array($this, 'replaceImageTags'), $this->getEmailBody())
					);
				}
			}
			
			$oEmail->IsHTML(true);
			$oEmail->AltBody = $this->getEmailBodyText();
		}
		
		$oEmail->Body = $this->getEmailBody();
		
		$oResponse = new transportResponse();
		$oResponse->setRequest($this->getTransportCredentials());
		if ( $this->getEmailAttachment() != "" ) {
			$oEmail->AddAttachment($this->getEmailAttachment());
		}	

		if ( $oEmail->Send() ) {
			$oResponse->setResponse('Sent via mail()');
			$return = true;
		} else {
			$oResponse->setResponse($oEmail->ErrorInfo);
			$return = false;
		}
		$this->setResponse($oResponse);
		return $return;
	}
	
}