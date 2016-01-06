<?php
/**
 * processLog.php
 * 
 * Logs process information for the specified process from ps -waxl. The logging frequency
 * can be set using --freq=X where x is the interval in seconds.
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage tools
 * @category processLog
 * @version $Rev: 9 $
 */

/*
 * Load dependencies
 */
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'system.inc');

/*
 * Allow signal handler to be installed
 */
declare(ticks=1);

/**
 * processLog
 * 
 * Logs process information for the specified process from ps -waxl. The logging frequency
 * can be set using --freq=X where x is the interval in seconds.
 *
 * @package scorpio
 * @subpackage tools
 * @category processLog
 */
$oApp = new cliApplication('processLog', 'A script to log out process information using the command ps. Information is logged to a file in the logs folder called process');
$oRequest = cliRequest::getInstance()->setApplication($oApp);
$oApp->getCommandChain()
	->addCommand(new cliCommandLog($oRequest))
	->addCommand(new cliCommandLogToConsole($oRequest))
	->addCommand(new cliCommandHelp($oRequest))
	->addCommand(new cliCommandNull($oRequest, 'freq', 'Frequency in seconds to capture data, default is 5 seconds', true))
	->addCommand(new cliCommandProcessLogger($oRequest));
$oApp->getListeners()->attachListener(new cliApplicationListenerLog());
$oApp->execute($oRequest);