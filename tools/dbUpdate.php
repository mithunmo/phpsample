<?php
/**
 * dbUpdate.php
 * 
 * dbUpdate is a CLI process that applies database updates via dbUpdate classes.
 * The databases that are under control are located in /data/dbUpdates.
 * 
 * This utility allows the current status to be viewed, test runs to be made and the
 * final changes to be pushed out. For sites that have multiple databases defined,
 * the relevant config file can be loaded from the site to override the default
 * database settings thus applying the updates to those databases. e.g. if the system
 * database is re-used for separate sites, any updates can be applied individually.
 *
 * A log is kept of all applied updates and what the result was of running the update.
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage tools
 * @category dbUpdate
 * @version $Rev: 9 $
 */

/*
 * Load dependencies
 */
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'system.inc');

/**
 * dbUpdate
 * 
 * dbUpdate is a CLI process that applies database updates via dbUpdate classes.
 * The databases that are under control are located in /data/dbUpdates.
 * 
 * This utility allows the current status to be viewed, test runs to be made and the
 * final changes to be pushed out. For sites that have multiple databases defined,
 * the relevant config file can be loaded from the site to override the default
 * database settings thus applying the updates to those databases. e.g. if the system
 * database is re-used for separate sites, any updates can be applied individually.
 *
 * A log is kept of all applied updates and what the result was of running the update.
 * 
 * @package scorpio
 * @subpackage tools
 * @category dbUpdate
 */
$oApp = new cliApplication('Database Update Utility', 'Provides a system for applying database updates and keeping track of what has been applied and the state of the update.');
$oRequest = cliRequest::getInstance()->setApplication($oApp);
$oApp->getCommandChain()
	->addCommand(new cliCommandLog($oRequest))
	->addCommand(new cliCommandLogToConsole($oRequest))
	->addCommand(new cliCommandHelp($oRequest))
	->addCommand(new cliCommandConfig($oRequest))
	->addCommand(new cliCommandNull($oRequest, 'disable-halt-onerror', 'Disables "Halt On Error" running all updates regardless of the previous result. Errors will only appear in the log. (Not recommended)'))
	->addCommand(new dbUpdateCommandStatus($oRequest))
	->addCommand(new dbUpdateCommandUpdate($oRequest))
	->addCommand(new dbUpdateCommandBackup($oRequest));
$oApp->getListeners()->attachListener(new cliApplicationListenerLog());
$oApp->execute($oRequest);