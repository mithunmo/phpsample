<?php
/**
 * cliCommandRecurse Class
 * 
 * Stored in cliCommandRecurse.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandRecurse
 * @version $Rev: 707 $
 */


/**
 * cliCommandRecurse class
 * 
 * This is a toggle switch. It represents recursing directories when searching
 * for resources. It can be replaced by using {@link cliCommandSwitch}, however
 * having a defined command just for this makes it easier to predict application
 * parameters and behaviour.
 * 
 * This command is usually used within a processing command and not directly on
 * the application instance.
 * 
 * <code>
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(new cliCommandRecurse($oRequest))
 * $oApp->execute($oRequest);
 * </code>
 *
 * @package scorpio
 * @subpackage cli
 * @category cliCommandRecurse
 */
class cliCommandRecurse extends cliCommand {
	
	const COMMAND = 'R';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, null, null, self::COMMAND);
		
		$this->setCommandHelp('Recurse directories when searching for resources');
		$this->setCommandIsOptional(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {}
}