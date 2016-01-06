<?php
/**
 * cliUtils.php
 * 
 * A collection of useful utilities for use on the command line.
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage tools
 * @category cliUtils
 * @version $Rev: 9 $
 */

/*
 * Load dependencies
 */
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'system.inc');

/**
 * cliUtils
 * 
 * A collection of useful utilities for use on the command line.
 * 
 * @package scorpio
 * @subpackage tools
 * @category cliUtils
 */
$oApp = new cliApplication('cliUtils', 'A simple script file that has some useful functions without the whole application framework behind it.');
$oRequest = cliRequest::getInstance()->setApplication($oApp);
$oApp->getCommandChain()
	->addCommand(new cliCommandHelp($oRequest))
	->addCommand(new cliCommandPassword($oRequest))
	->addCommand(new cliCommandSerialise($oRequest));
$oApp->getListeners()->attachListener(new cliApplicationListenerLog());
$oApp->execute($oRequest);