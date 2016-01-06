<?php
/**
 * cliCommandVersion Class
 * 
 * Stored in cliCommandVersion.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandVersion
 * @version $Rev: 707 $
 */


/**
 * cliCommandVersion class
 * 
 * Outputs version information about the current running framework. This is
 * used by the scorpio cli tool.
 * 
 * <code>
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(new cliCommandVersion($oRequest))
 * $oApp->execute($oRequest);
 * </code>
 *
 * @package scorpio
 * @subpackage cli
 * @category cliCommandVersion
 */
class cliCommandVersion extends cliCommand {
	
	const COMMAND = 'version';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND);
		
		$this->setCommandHelp('Outputs version information about the current running framework');
		$this->setCommandRequiresValue(false);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$oResponse = $this->getRequest()->getApplication()->getResponse();
		$oResponse->addResponse("\nScorpio Framework for PHP v".system::SCORPIO_VERSION);
		$oResponse->addResponse(cliConsoleTools::drawSeparator());
		$oResponse->addResponse('* Copyright (c) 2007-'.date('Y').', Dave Redfern');
		$oResponse->addResponse('* All rights reserved.');
		$oResponse->addResponse('* Licensed under the BSD License with some exceptions');
		$oResponse->addResponse('* See: http://scorpio.madagasgar.com/static/license');
	}
}