#!/usr/bin/php
<?php
/**
 * loggingd
 *
 * Stored in loggingd.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage daemons
 * @category loggingd
 * @version $Rev: 5 $
 */


/*
 * Load dependencies
 */
require_once(dirname(dirname(__FILE__)).'/libraries/system.inc');

/*
 * Declare ticks to allow signal handling to be registered
 */
declare(ticks=1);

/*
 * Set our logging
 */
systemLog::getInstance()->setSource('StartUp');
systemLog::message('--------------------------------------------------');
systemLog::message('Initialising Logging Daemon');

/**
 * loggingd
 *
 * The scorpio logging daemon. Reads entries from the logging database into files
 *
 * @package scorpio
 * @subpackage daemons
 * @category loggingd
 */
class loggingDaemon extends cliDaemon {
	
	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('loggingd', 'Scorpio logging daemon');
	}
	
	/**
	 * @see cliDaemon::execute()
	 */
	function execute() {
		$this->notify(
			new cliApplicationEvent(
				cliApplicationEvent::EVENT_INFORMATIONAL,
				'Entering main process loop',
				null,
				array(
					'log.source' => 'Process'
				)
			)
		);
		$this->setStatusParam('Status', 'Running');
		$this->getQueueStats();
		$this->updateStatus();
		
		/**
		 * Main daemon loop
		 */
		$loop = true;
		do {
			if ( time() - $this->getLastStatusUpdate() > 60 ) {
				$this->getQueueStats();
				$this->updateStatus();
			}
			
			if ( $this->signalTrapped() ) {
				$loop = false;
			}
			
			$arrLogs = systemLogQueue::listOfObjects(100);
			if (false) $oSystemLog = new systemLogQueue();
			foreach ( $arrLogs as $oSystemLog ) {
				$oSystemLog->storeLogFile();
				unset($oSystemLog);
			}
			
			if ( $this->signalTrapped() ) {
				$loop = false;
			}
			sleep(2);
			
		} while ( $loop === true );
	}
	
	/**
	 * Gets the number of log messages in the queue, and sets to daemon params
	 *
	 * @return void
	 */
	private function getQueueStats() {
		$this->setStatusParam('QueueMsgs', systemLogQueue::getQueueCount());
	}
	
	/**
	 * @see cliDaemon::terminate()
	 */
	function terminate() {
		$this->notify(
			new cliApplicationEvent(
				cliApplicationEvent::EVENT_INFORMATIONAL,
				'Shutting down logging daemon...'
			)
		);
		$this->getQueueStats();
		$this->setStatusParam('Status', 'Stopped');
		$this->updateStatus();
		return true;
	}
}


$oRequest = cliRequest::getInstance();

/**
 * Initialise process controls
 */
cliProcessControls::initialise($oRequest, 'loggingDaemon');

/**
 * Attempt to daemonise process
 */
cliProcessControls::daemonise();

/**
 * @var loggingDaemon $oDaemon
 */
$oDaemon = new loggingDaemon();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM);
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();