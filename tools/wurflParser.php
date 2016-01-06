<?php
/**
 * wurflParser.php
 * 
 * Parses WURFL XML files into the database, allows user agents to be tested and
 * will import CSV files describing the WURFL capabilities. WURFL XML can also be
 * downloaded using the download command.
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage tools
 * @category wurflParser
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
 * wurflParser
 * 
 * Parses WURFL XML files into the database, allows user agents to be tested and
 * will import CSV files describing the WURFL capabilities. WURFL XML can also be
 * downloaded using the download command.
 * 
 * @package scorpio
 * @subpackage tools
 * @category wurflParser
 */
$oApp = new cliApplication('WURFL Parser', 'Parses WURFL XML files into the database. These can be individual files given by a directory or full WURFL XML file.');
$oRequest = cliRequest::getInstance()->setApplication($oApp);
$oApp->getCommandChain()
	->addCommand(new cliCommandLog($oRequest))
	->addCommand(new cliCommandLogToConsole($oRequest))
	->addCommand(new cliCommandHelp($oRequest))
	->addCommand(new cliCommandSwitch($oRequest, 'c', 'Set that the resource has custom data and should be parsed as override values to the WURFL data'))
	->addCommand(new cliCommandSwitch($oRequest, 'r', 'Recurse folders'))
	->addCommand(new cliCommandNull($oRequest, 'ext', 'By default this script looks for .xml files when a directory is given; add file extensions using this option: e.g.: ext=xslt;xhtml;wml', true))
	->addCommand(new wurflCommandRebuild($oRequest))
	->addCommand(new wurflCommandDownload($oRequest))
	->addCommand(new wurflCommandParser($oRequest))
	->addCommand(new wurflCommandTest($oRequest))
	->addCommand(new wurflCommandCapabilityParser($oRequest));
$oApp->getListeners()->attachListener(new cliApplicationListenerLog());
$oApp->execute($oRequest);