<?php
/*
.---------------------------------------------------------------------------.
|  Software: Amazon SES Mailer - PHP email class for Amazon SES             |
|   Version: 1.0                                                            |
|   Contact: dev@geoloqi.com                                                |
|      Info: https://github.com/geoloqi/Amazon-SES-Mailer-PHP               |
| ------------------------------------------------------------------------- |
|     Admin: Aaron Parecki                                                  |
|   Authors: Aaron Parecki aaronpk@geoloqi.com                              |
| Copyright (c) 2011, Geoloqi.com                                           |
| ------------------------------------------------------------------------- |
|   License: Distributed under the Lesser General Public License (LGPL)     |
|            http://www.gnu.org/copyleft/lesser.html                        |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
'---------------------------------------------------------------------------'
*/

class mofilmAmazonSESMailer extends utilityMail {
    
	/**
	 * Stores Amazon SES Access Key
	 *
	 * @var string
	 * @public
	 */
	public $AWSAccessKeyId;

	/**
	 * Stores Amazon SES secret key
	 *
	 * @var string
	 * @public
	 */
	public $AWSSecretKey;

	/**
	 * Stores the mailer type
	 *
	 * @var string
	 * @public
	 */
	public $Mailer = 'amazonses';
	


	/**
	 * Constructs a new object of AmazonSES
	 *
	 * @param string  $id
	 * @param string $key
	 * @param Exception $exceptions
	 */
	public function __construct($id, $key, $exceptions=FALSE) {
		$this->AWSAccessKeyID = $id;
		$this->AWSSecretKey = $key;
		parent::__construct($exceptions);
	}

      /**
       * Creates message and assigns Mailer. If the message is
       * not sent successfully then it returns false.  Use the ErrorInfo
       * variable to view description of the error.
       * @return bool
       */
	public function Send() {
		$this->Mailer = "amazonses";
		$this->SingleTo = true;
		try {
			if(!$this->PreSend()) return false;
			return $this->PostSend();
		} catch (phpmailerException $e) {
			$this->SetError($e->getMessage());
			if ($this->exceptions) {
				throw $e;
			}
		return false;
		}
	}
	
	/**
	 * Calls the correct mailer function
	 *
	 * @return void
	 */
	protected function PostSend() {
		try {
			// Choose the mailer and send through it
			switch($this->Mailer) {
				case 'sendmail':
					return $this->SendmailSend($this->MIMEHeader, $this->MIMEBody);
				case 'smtp':
					return $this->SmtpSend($this->MIMEHeader, $this->MIMEBody);
				case 'amazonses':
					return $this->AmazonSESSend($this->MIMEHeader, $this->MIMEBody);
				default:
					return $this->MailSend($this->MIMEHeader, $this->MIMEBody);
			}
		} catch (phpmailerException $e) {
			$this->SetError($e->getMessage());
			if ($this->exceptions) {
				throw $e;
			}
		echo $e->getMessage()."\n";
		return false;
		}
	}


	/**
	* Sends mail using Amazon SES.
	* @param string $header The message headers
	* @param string $body The message body
	* @access protected
	* @return bool
	*/
	protected function AmazonSESSend($header, $body) {
		$ses = new AmazonSES($this->AWSAccessKeyID, $this->AWSSecretKey);
		if ($this->SingleTo === true) {
			foreach ($this->SingleToArray as $key => $val) {
				try {
					$response = $ses->send_raw_email(array(
						'Data' => base64_encode($header . "\n" . $body)
					), array(
						'Source' => $this->Sender,
						'Destinations' => $val
					));					
					return $response;
				}
				catch(Exception $e) {
					systemLog::error($e);
					systemLog::error("Amazon API not accessible");
					mail('it@mofilm.com', 'Email Daemon', 'Amazon API not accessible');
					mail($this->to[0][0], $this->Subject, strip_tags($body));
					systemLog::error("Sending Via normal mail");
					//throw new phpmailerException('Amazon API not accessible', self::STOP_CRITICAL);
					return false;
				}
			}
		} else {
			$response = $ses->send_raw_email(array(
				'Data' => base64_encode($header . "\n" . $body)
			), array(
				'Source' => $this->Sender,
				'Destinations' => $this->to
			));
			// implement call back function if it exists
			$isSent = ($response->isOK()) ? 1 : 0;
			$this->doCallback($isSent,$this->to,$this->cc,$this->bcc,$this->Subject,$body);
			if(!$isSent) {
				throw new phpmailerException('Error Sending via Amazon SES', self::STOP_CRITICAL);
			}
		}
		return true;
	}

}