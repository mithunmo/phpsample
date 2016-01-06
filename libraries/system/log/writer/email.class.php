<?php
/**
 * systemLogWriterEmail class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLogWriterEmail
 * @version $Rev: 650 $
 */


/**
 * systemLogWriterEmail Class
 * 
 * Sends log messages to an email address once the system shuts down through
 * either failure or natural end of execution. This writer will cache the message
 * until it reaches a certain size, rather than sending an individual email
 * for every message logged.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLogWriterEmail
 */
class systemLogWriterEmail extends systemLogWriter {
	
	/**
	 * Stores $_ErrorMessage
	 *
	 * @var string
	 * @access private
	 */
	private $_ErrorMessage;
	/**
	 * Stores $_Recipients
	 *
	 * @var array
	 * @access private
	 */
	private $_Recipients;
	
	
	
	/**
	 * @see systemLogWriter::__construct()
	 * 
	 * @param systemLogFilter $inLogFilter
	 * @return systemLogWriter
	 */
	function __construct($inLogFilter) {
		parent::__construct('email', $inLogFilter);
		
		register_shutdown_function(array($this, "sendEmail"));
	}
	
	
	
	/**
	 * Main Methods
	 */
	
	/**
	 * Writer specific options for putting log information
	 *
	 * @param string $inMessage
	 * @param string $inSource
	 * @return void
	 */
	protected function _put($inMessage, $inSource) {
		$inMessage = nl2br(htmlspecialchars($inMessage));
		$inMessage = (($inSource) ? " $inSource" : '')." $inMessage";
		$inMessage = date($this->getDateMask())." $inMessage";
		$inMessage = "<span style=\"color: #f00;\">$inMessage</span>\n";
		
		/*
		 * Force thrown an exception to get debug data
		 */
		try {
			throw new Exception("asdfas");
		} catch (Exception $oException) {
			$trace = $oException->getTrace();
			$line = 1;
			if (count($trace) >= $line) {					
				$inMessage = $inMessage."File:".$trace[$line]['file']."#".$trace[$line]['line']."<br />";
			}
		}
		
		$this->setErrorMessage($this->getErrorMessage().$inMessage);
		if (strlen($this->getErrorMessage()) > 1024*1024) {
			$this->sendEmail();
		}
	}
	
	/**
	 * Send message to set email addresses
	 *
	 * @return boolean
	 */
	public function sendEmail() {
		$return  = false;
		if ($this->getErrorMessage() != '') {
			if ( count($this->getRecipients()) > 0 ) {
			$mail = new utilityMail() ;
				$mail->IsHTML(true) ;
				$mail->Subject = "Error log from  " . system::getConfig()->getSystemHostName() . " " . date($this->getDateMask()) ;
				// HTML body
				$body = "<h3>System errors</h3><p>" ;
				$body .= $this->getErrorMessage() . '</p>' ;
				$mail->Body = $body ;
				
				foreach ( $this->getRecipients() as $email ) {
					$mail->AddAddress($email, $email) ;
				}
				if ( ! $mail->Send() ) {
					$oLog = systemLog::getInstance() ;
					$oLog->log("Could not send email  ({$mail->ErrorInfo})", systemLogLevel::ERROR) ;
				} else {
					$return = true ;
					$this->setErrorMessage("");
				}
				
				// Clear all addresses and attachments for next loop
				$mail->ClearAddresses() ;
				$mail->ClearAttachments() ;
			}
		}
		return $return;
	}
	
	
	
	/**
	 * Get / Set Methods
	 */

	/**
	 * Returns $_ErrorMessage
	 *
	 * @return string
	 */
	function getErrorMessage() {
		return $this->_ErrorMessage;
	}
	 
	/**
	 * Sets $_ErrorMessage to $inErrorMessage
	 *
	 * @param string $inErrorMessage
	 * @return systemLogWriterEmail
	 */
	function setErrorMessage($inErrorMessage) {
		if ( $this->_ErrorMessage !== $inErrorMessage ) {
			$this->_ErrorMessage = $inErrorMessage;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns $_Recipients
	 *
	 * @return array
	 */
	function getRecipients() {
		return $this->_Recipients;
	}
	 
	/**
	 * Sets $_Recipients to $inRecipients
	 *
	 * @param array $inRecipients
	 * @return systemLogWriterEmail
	 */
	function setRecipients($inRecipients) {
		if ( is_array($inRecipients) ) {
			$this->_Recipients = $inRecipients;
		} elseif ( !in_array($inRecipients, $this->_Recipients) || (isset($this->_Recipients[$inRecipients]) && $this->_Recipients[$inRecipients] !== $inRecipients) ) {
			$this->_Recipients[] = $inRecipients;
			$this->_Modified = true;
		}
		return $this;
	}
}