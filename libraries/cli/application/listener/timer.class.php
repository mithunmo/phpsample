<?php
/**
 * cliApplicationListenerTimer Class
 * 
 * Stored in timer.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationListenerTimer
 * @version $Rev: 650 $
 */


/**
 * cliApplicationListenerTimer
 *
 * Attaches a listener that wraps the utilityStopWatch allowing application
 * execution to be timed. Useful for long running processes to track the
 * amount of time it takes to complete loops. Timer is started by the
 * EXECUTE_START event and then split points are triggered at PROCESS_START,
 * PROCESS_END, EXECUTE_END, TRIGGER and APPLICATION_TERMINATED.
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationListenerTimer
 */
class cliApplicationListenerTimer implements cliApplicationListener {
	
	/**
	 * Instance of utilityStopWatch
	 *
	 * @var utilityStopWatch
	 * @access protected
	 */
	protected $_Timer = null;
	
	
	
	/**
	 * Returns a new log listener
	 *
	 * @return cliApplicationListenerTimer
	 */
	function __construct() {
		$this->_Timer = new utilityStopWatch();
	}
	
	/**
	 * Process the notification, implementation of the interface
	 *
	 * @param cliApplicationEvent $inEvent
	 */
	function notify(cliApplicationEvent $inEvent) {
		switch ( $inEvent->getEventCode() ) {
			case cliApplicationEvent::EVENT_EXECUTE_START:
				$this->_Timer->start();
			break;
			
			case cliApplicationEvent::EVENT_PROCESS_START:
				$this->_Timer->split('ProcStart-'.$inEvent->getOption(cliApplicationEvent::OPTION_LOOP_PROCESS_ID));
			break;
			
			case cliApplicationEvent::EVENT_PROCESS_END:
				$this->_Timer->split('ProcEnd-'.$inEvent->getOption(cliApplicationEvent::OPTION_LOOP_PROCESS_ID));
			break;
			
			case cliApplicationEvent::EVENT_TRIGGER:
				$this->_Timer->split('Checkpoint');
				$elapsed = $this->_Timer->elapsed();
				systemLog::message("Average parse time per loop: ".round(($elapsed/$inEvent->getOption(cliApplicationEvent::OPTION_TRIGGER_LEVEL)), 2)." seconds");
			break;
			
			case cliApplicationEvent::EVENT_APPLICATION_TERMINATED:
			case cliApplicationEvent::EVENT_EXECUTE_END:
				$this->_Timer->stop();
				$time = utilityStringFunction::humanReadableTime($this->_Timer->elapsed());
				systemLog::message("Total execution time: $time");
			break;
		}
	}
}