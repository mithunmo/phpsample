<?php
/**
 * cliApplicationListenerLog Class
 * 
 * Stored in log.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationListenerLog
 * @version $Rev: 707 $
 */


/**
 * cliApplicationListenerLog
 *
 * Attaches a listener that maps events to systemLog calls allowing
 * alternative logging systems to be used in the cliApplication. This
 * maps to systemLog specifically.
 * 
 * The log listener allows a custom source to be specified via the event.
 * Simply add log.source to the options array on the event. If not set
 * the log listener will use a source of App - Command - Event.
 * 
 * <code>
 * $oApp = new cliApplication();
 * $oApp->getListeners()->attachListener(new cliApplicationListenerLog());
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationListenerLog
 */
class cliApplicationListenerLog implements cliApplicationListener {
	
	/**
	 * Returns a new log listener
	 *
	 * @return cliApplicationListenerLog
	 */
	function __construct() {
		
	}
	
	/**
	 * Process the notification, implementation of the interface
	 *
	 * @param cliApplicationEvent $inEvent
	 */
	function notify(cliApplicationEvent $inEvent) {
		switch ( $inEvent->getEventCode() ) {
			case cliApplicationEvent::EVENT_OK:
			case cliApplicationEvent::EVENT_EXECUTE_START:
			case cliApplicationEvent::EVENT_EXECUTE_END:
				$systemLogLevel = systemLogLevel::ALWAYS;
				break;
				
			case cliApplicationEvent::EVENT_ERROR:
			case cliApplicationEvent::EVENT_EXCEPTION:
			case cliApplicationEvent::EVENT_REDIRECT_FAILURE:	
				$systemLogLevel = systemLogLevel::ERROR;
				break;
				
			case cliApplicationEvent::EVENT_REDIRECT_SUCCESS:
			case cliApplicationEvent::EVENT_TRIGGER:
				$systemLogLevel = systemLogLevel::NOTICE;
			break;
			
			case cliApplicationEvent::EVENT_PROCESS_START:
			case cliApplicationEvent::EVENT_PROCESS_END:
				$systemLogLevel = systemLogLevel::INFO;
			break;
			
			case cliApplicationEvent::EVENT_APPLICATION_TERMINATED:
				$systemLogLevel = systemLogLevel::CRITICAL;
			break;
			
			case cliApplicationEvent::EVENT_INFORMATIONAL:
				$systemLogLevel = systemLogLevel::ALWAYS;
			break;
			
			case cliApplicationEvent::EVENT_REGISTERED_SIGNAL_TRAPPED:
			case cliApplicationEvent::EVENT_UNREGISTERED_SIGNAL_TRAPPED:
				$systemLogLevel = systemLogLevel::WARNING;
			break;
			
			case cliApplicationEvent::EVENT_WARNING:
				$systemLogLevel = systemLogLevel::WARNING;
			break;
			
			default:
				$systemLogLevel = system::getConfig()->getSystemLogLevel()->getParamValue();
			break;
		}
		
		if ( count($inEvent->getOptions()) > 0 ) {
			if ( $inEvent->getOption(cliApplicationEvent::OPTION_APP_NAME) && $inEvent->getOption(cliApplicationEvent::OPTION_APP_COMMAND)) {
				systemLog::getInstance()->getSource()->setSource(
					array(
						'App' => $inEvent->getOption(cliApplicationEvent::OPTION_APP_NAME),
						'Command' => $inEvent->getOption(cliApplicationEvent::OPTION_APP_COMMAND),
						'Event' => $inEvent->getEventCode()
					)
				);
			} elseif( $inEvent->getOption(cliApplicationEvent::OPTION_LOG_SOURCE) ) {
				systemLog::getInstance()->setSource($inEvent->getOption(cliApplicationEvent::OPTION_LOG_SOURCE));
			}
		}
		systemLog::getInstance()->log($inEvent->getEventMessage(), $systemLogLevel);
	}
}