#!/usr/bin/env php
<?php
/**
 * Apps - ReportGenerator - Start
 *
 * ReportGenerator is the CLI component of the reporting system. This CLI app
 * is what actually builds the reports. This allows reports to be constructed
 * offline and not be a drain on system resources.
 * 
 * This App is designed to be run via the reportd.php daemon system, but it
 * can be used as a normal CLI application by running it as a CLI app. It
 * supports help, logging and CLI log output.
 * 
 * Output from this is designed for the daemon and not to be human readable.
 *
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage apps
 * @category ReportGenerator
 * @version $Rev: 16 $
 */


/*
 * Load dependencies
 */
require_once(dirname(dirname(dirname(__FILE__))).'/libraries/system.inc');


/**
 * Instance cliApplication and configure with commands
 */
$oApp = new cliApplication('ReportGenerator', 'Builds reports for the reporting system.');
$oRequest = cliRequest::getInstance()->setApplication($oApp);
$oApp->getCommandChain()
	->addCommand(new cliCommandLog($oRequest))
	->addCommand(new cliCommandLogToConsole($oRequest))
	->addCommand(new cliCommandHelp($oRequest))
	->addCommand(new reportCentreGenerator($oRequest));
$oApp->getListeners()->attachListener(new cliApplicationListenerLog());
$oApp->execute($oRequest);