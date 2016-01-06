<?php
/**
 * cliCommandSwitch Class
 * 
 * Stored in cliCommandSwitch.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandSwitch
 * @version $Rev: 707 $
 */


/**
 * cliCommandSwitch class
 * 
 * Allows switches to be defined with help text in an application. The
 * cliCommandSwitch like cliCommandNull is not executed and has an empty
 * execute path. Use when you have un-documented aliases or switches
 * that your app uses but you do not want to create a concrete command
 * for.
 * 
 * For example: if in your processing command you allow specific additional
 * features to be enabled, but you do not want to have to create individual
 * commands for these switches, use this class to act as an identifier and
 * for documenting what these switches do.
 * 
 * <code>
 * // a poor example of adding a switch doc.
 * // w would be used in the app some how
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(new cliCommandSwitch($oRequest, 'w', 'Does something'))
 * $oApp->execute($oRequest);
 * </code>
 *
 * @package scorpio
 * @subpackage cli
 * @category cliCommandSwitch
 */
class cliCommandSwitch extends cliCommand {
	
	/**
	 * Creates a new switch only command that has no execute body.
	 * 
	 * Use this generic command for any switches you want documented in your
	 * application. Specify the alias letter and help text. The command will 
	 * not execute any code nor cause the app to halt. In fact any switch
	 * commands will be processed before any other commands as switches are
	 * always handled before commands.
	 *
	 * @param cliRequest $inRequest
	 * @param string $inSwitch
	 * @param string $inHelpText
	 */
	function __construct(cliRequest $inRequest, $inSwitch, $inHelpText) {
		parent::__construct($inRequest, null, null, $inSwitch);
		
		$this->setCommandHelp($inHelpText);
		$this->setCommandRequiresValue(false);
		$this->setCommandIsSwitch(true);
		$this->setCommandIsOptional(true);
		$this->setHaltAppAfterExecute(false);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {}
}