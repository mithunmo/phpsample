<?php
/**
 * cliApplicationListenerEmail Class
 * 
 * Stored in email.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationListenerEmail
 * @version $Rev: 707 $
 */


/**
 * cliApplicationListenerEmail
 *
 * Allows people to be emailed when certain events have been triggered.
 * This could be on daemon termination, or application completion.
 * 
 * Email notices are triggered by the following events:
 * <ul>
 *   <li>EVENT_OK</li>
 *   <li>EVENT_EXECUTE_END</li>
 *   <li>EVENT_ERROR</li>
 *   <li>EVENT_EXCEPTION</li>
 *   <li>EVENT_APPLICATION_TERMINATED</li>
 * </ul>
 * 
 * Recipients must be set before attaching the listener to the application.
 * Recipients are emailed as a BCC list.
 * 
 * <code>
 * // add a few recipients
 * $oMailListener = new cliApplicationListenerEmail();
 * $oMailListener->addRecipient('me@example.com');
 * $oMailListener->addRecipient('someone@example.com');
 * 
 * // bulk add from array
 * $oMailListener->setRecipients(array('me@example.com', 'someone@example.com'));
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationListenerEmail
 */
class cliApplicationListenerEmail implements cliApplicationListener {
	
	/**
	 * Stores $_Recipients
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Recipients;
	
	
	
	/**
	 * Returns a new email listener
	 *
	 * @return cliApplicationListenerEmail
	 */
	function __construct() {
		$this->_Recipients = array();
	}
	
	/**
	 * Process the notification, implementation of the interface
	 *
	 * @param cliApplicationEvent $inEvent
	 * @return void
	 */
	function notify(cliApplicationEvent $inEvent) {
		$mail = false;
		switch ( $inEvent->getEventCode() ) {
			case cliApplicationEvent::EVENT_OK:
			case cliApplicationEvent::EVENT_EXECUTE_END:
				$title = system::getScriptFilename().' finished successfully';
				$mail = true;
			break;
				
			case cliApplicationEvent::EVENT_ERROR:
			case cliApplicationEvent::EVENT_EXCEPTION:	
				$title = system::getScriptFilename().' encountered errors during execution';
				$debug = "
DEBUG Data:
Event Source:
{$inEvent->getEventSource()}
Event Options:
".print_r($inEvent->getOptions(), 1);
				$mail = true;
			break;
			
			case cliApplicationEvent::EVENT_APPLICATION_TERMINATED:
				$title = system::getScriptFilename().' terminated!';
				$mail = true;
			break;
		}
		
		if ( $mail && count($this->getRecipients()) > 0 ) {
			$oMailer = new utilityMail();
			$oMailer->set('exceptions', true);
			$oMailer->SetFrom(system::getConfig()->getSystemFromAddress(), system::getConfig()->getSystemHostname());
			
			$body = "CLI App Event Notification

This is a server generated notification that you have been subscribed to.

The application: ".system::getScriptFilename()." has finished processing with
status: {$inEvent->getEventCodeAsString()}.

The event message (if any) was:
{$inEvent->getEventMessage()}";

			if ( isset($debug) ) {
				$body .= $debug;
			}

			$body .= "\n\nPlease do not reply to this message.";
			
			$oMailer->Subject = $title;
			$oMailer->Body = $body;
			
			foreach ( $this->getRecipients() as $recipient ) {
				$oMailer->AddBCC($recipient);
			}
			try {
				$oMailer->Send();
			} catch ( Exception $e ) {
				// avoid recursive exception errors
				systemLog::critical($e->getMessage());
			}
		}
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
	 * Add a recipient to be notified
	 *
	 * @param string $inEmailAddress
	 * @return cliApplicationListenerEmail
	 */
	function addRecipient($inEmailAddress) {
		if ( !in_array($inEmailAddress, $this->_Recipients) ) {
			$this->_Recipients[] = $inEmailAddress;
		}
		return $this;
	}
	
	/**
	 * Removes a recipient
	 *
	 * @param string $inEmailAddress
	 * @return cliApplicationListenerEmail
	 */
	function removeRecipient($inEmailAddress) {
		$key = array_search($inEmailAddress, $this->_Recipients);
		if ( $key !== false ) {
			unset($this->_Recipients[$key]);
		}
		return $this;
	}
	
	/**
	 * Set an array of recipients
	 *
	 * @param array $inRecipients
	 * @return cliApplicationListenerEmail
	 */
	function setRecipients(array $inRecipients = array()) {
		if ( $inRecipients !== $this->_Recipients ) {
			$this->_Recipients = $inRecipients;
		}
		return $this;
	}
}