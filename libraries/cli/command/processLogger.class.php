<?php
/**
 * cliCommandProcessLogger Class
 * 
 * Stored in cliCommandProcessLogger.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandProcessLogger
 * @version $Rev: 707 $
 */


/**
 * cliCommandProcessLogger class
 * 
 * Captures process information from ps -waxl and writes it to a log file
 * named after the process. Can have a variable sleep time between readings.
 * 
 * <code>
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(new cliCommandProcessLogger($oRequest))
 * $oApp->execute($oRequest);
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliCommandProcessLogger
 */
class cliCommandProcessLogger extends cliCommand {
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, 'log');
		
		$this->setCommandHelp('The name of the process to log data on');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		if ( $this->getRequest()->getParam('freq') && is_numeric($this->getRequest()->getParam('freq')) && $this->getRequest()->getParam('freq') > 0 ) {
			$sleep = $this->getRequest()->getParam('freq');
		} else {
			$sleep = 5;
		}
		
		if ( !$this->getRequest()->getParam('log') || strlen($this->getRequest()->getParam('log')) < 2 ) {
			throw new cliApplicationCommandException($this, 'No process specified for monitoring');
		}
		
		$process = $this->getRequest()->getParam('log');
		$logFile = system::getConfig()->getPathLogs().system::getDirSeparator().'processLogs'.system::getDirSeparator().$process.'_process.log';
		if ( !file_exists(dirname($logFile)) ) {
			@mkdir(dirname($logFile), 0775, true);
		}
		
		do {
			$cmd = "ps waxl | grep $process";
			$res = `$cmd`;
		
			$lines = explode("\n", $res);
			if ( count($lines) > 0 ) {
				foreach ( $lines as $line ) {
					if ( stripos($line, $process) !== false && stripos($line, 'processLog.php') === false && stripos($line, 'tail -f') === false && stripos($line, 'sh -c ps') === false && stripos($line, 'grep') === false ) {
						@file_put_contents($logFile, date('Y-m-d H:i:s').' '.$line."\n", FILE_APPEND);
					}
				}
			}
			
			sleep($sleep);
		} while ( $this->getRequest()->getApplication()->signalTrapped() == 0 );
	}
}