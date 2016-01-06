<?php
/**
 * cliCommandLogToConsole Class
 * 
 * Stored in logToConsole.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandLogToConsole
 * @version $Rev: 707 $
 */


/**
 * cliCommandLogToConsole class
 * 
 * Adds a systemLogWriterCli object to the systemLog instance allowing
 * all log messages from ALWAYS to current log level to be output on 
 * the command line as the application is executing. This is mainly for
 * debugging.
 * 
 * Note: if using with cliCommandLog, this option should come LAST on the
 * cli switch stack e.g. -oV will use the system default log level and
 * NOT systemLogLevel::DEBUG as set by V. The correct order is -Vo.
 * 
 * <code>
 * // add output logging to app
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(
 *         new cliCommandLogToConsole($oRequest)
 *     )
 * $oApp->execute($oRequest);
 * 
 * // add verbose logging and output to console
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(new cliCommandLog($oRequest))
 *     ->addCommand(new cliCommandLogToConsole($oRequest))
 * $oApp->execute($oRequest);
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliCommandLogToConsole
 */
class cliCommandLogToConsole extends cliCommand {
	
	/**
	 * Creates a new console logger switch
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, null, null, 'o');
		
		$this->setCommandHelp('Outputs systemLog messages to the console');
		$this->setCommandIsOptional(true);
	}
	
	/**
	 * Executes the command adding the output logger
	 *
	 * @return void
	 */
	function execute() {
		if ( $this->getRequest()->getSwitch('o') ) {
			systemLog::getInstance()->setWriter(
				new systemLogWriterCli(
					new systemLogFilter(
						systemLogLevel::ALWAYS, systemLog::getInstance()->getLogLevel()
					)
				)
			);
		}
	}
}