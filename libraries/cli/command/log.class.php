<?php
/**
 * cliCommandLog Class
 * 
 * Stored in log.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandLog
 * @version $Rev: 707 $
 */


/**
 * cliCommandLog class
 * 
 * Sets the log level to either verbose (INFO) or very verbose (DEBUG)
 * allowing applications to be better debugged. Specifying either causes
 * a message of that level to be output to the systemLog.
 * 
 * This command is almost always used in conjunction with {@link cliCommandLogToConsole}
 * for debugging apps and daemons.
 * 
 * <code>
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(
 *         new cliCommandLog($oRequest)
 *     )
 * $oApp->execute($oRequest);
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliCommandLog
 */
class cliCommandLog extends cliCommand {
	
	/**
	 * Creates a new log command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, null, null, array('v','V'));
		
		$this->setCommandHelp('Set log level to verbose (v) or very verbose (V)');
		$this->setCommandIsOptional(true);
	}
	
	/**
	 * Sets the correct log level based on the switch
	 *
	 * @return void
	 */
	function execute() {
		if ( $this->getRequest()->getSwitch('v') ) {
			systemLog::getInstance()->setLogLevel(systemLogLevel::INFO);
			systemLog::info("WARNING :: LogLevel is now INFO");
		}
		if ( $this->getRequest()->getSwitch('V') ) {
			systemLog::getInstance()->setLogLevel(systemLogLevel::DEBUG);
			systemLog::debug("WARNING :: LogLevel is now DEBUG");
		}
	}
}