<?php
/**
 * cliApplicationListenerMemUsage Class
 * 
 * Stored in memUsage.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationListenerMemUsage
 * @version $Rev: 707 $
 */


/**
 * cliApplicationListenerMemUsage
 *
 * Attaches a listener that monitors cliApplication memory usage. Memory usage
 * is logged on EXECUTE_END and EXECUTE_TRIGGER. Memory usage is logged to the
 * current logfile as defined in the application.
 * 
 * This listener is used for tracking memory leaks during long running
 * processes.
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationListenerMemUsage
 */
class cliApplicationListenerMemUsage implements cliApplicationListener {
	
	/**
	 * Instance of utilityMemUsage
	 *
	 * @var utilityMemUsage
	 * @access protected
	 */
	protected $_MemUsage = null;
	
	/**
	 * Returns a new memUsage listener
	 *
	 * @return cliApplicationListenerMemUsage
	 */
	function __construct() {
		$this->_MemUsage = new utilityMemUsage();
		$this->_MemUsage->takeReading();
	}
	
	/**
	 * Process the notification, implementation of the interface
	 *
	 * @param cliApplicationEvent $inEvent
	 */
	function notify(cliApplicationEvent $inEvent) {
		switch ( $inEvent->getEventCode() ) {
			case cliApplicationEvent::EVENT_EXECUTE_END:
			case cliApplicationEvent::EVENT_TRIGGER:
				$leakage = $this->_MemUsage->takeReading();
				systemLog::message('Memory leakage: '.utilityStringFunction::humanReadableSize($leakage).' after '.$inEvent->getOption(cliApplicationEvent::OPTION_TRIGGER_LEVEL).' loops');
			break;
		}
	}
}