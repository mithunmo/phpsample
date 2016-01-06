<?php
/**
 * cliCommandNull Class
 * 
 * Stored in cliCommandNull.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandNull
 * @version $Rev: 707 $
 */


/**
 * cliCommandNull class
 * 
 * The null command is used as a place holder when a command is required, but
 * a full class is not needed - e.g. for a long-option that is just a toggle
 * or if you want to specify specific options within another command. This
 * command is useful for creating extensive help for your cli application.
 * 
 * <code>
 * // document 'test' switch
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(
 *         new cliCommandNull($oRequest, 'test', 'Help for switch test', false, true, true)
 *     )
 * $oApp->execute($oRequest);
 * 
 * // usage within a command, in this case the testCommand from the test system:
 * new testCommand(
 *     $oRequest, new cliCommandChain(
 *         array(
 *             new cliCommandNull($oRequest, 'all', 'Run all test cases', false, false),
 *             new cliCommandNull($oRequest, 'package',
 *                 "Run test case(s) for specific package. Requires <packagename> and optionally <testcase>\ne.g. test package <packagename> <testcase>", true, false, false
 *             )
 *         )
 *     )
 * )
 * </code>
 *
 * @package scorpio
 * @subpackage cli
 * @category cliCommandNull
 */
class cliCommandNull extends cliCommand {
	
	/**
	 * Creates a new null command that has no execute body.
	 * 
	 * The command will be processed the same as any other argument, but does not cause
	 * the application to halt. Used when a long option (toggle) is wanted or for
	 * disposable commands that do not require a concrete implementation. $inIsSwitch
	 * if set to false causes the command to appear as an <arg> instead of as
	 * <--switch>. Default is as a switch. By default all null commands are "optional"
	 * which will cause them to be flagged as optional in the help messages.
	 *
	 * @param cliRequest $inRequest
	 * @param string $inCommand
	 * @param string $inHelpText
	 * @param boolean $inRequiresValue
	 * @param boolean $inIsSwitch
	 * @param boolean $inIsOptional
	 */
	function __construct(cliRequest $inRequest, $inCommand, $inHelpText, $inRequiresValue = false, $inIsSwitch = true, $inIsOptional = true) {
		parent::__construct($inRequest, $inCommand);
		
		$this->setCommandHelp($inHelpText);
		$this->setCommandRequiresValue($inRequiresValue);
		$this->setCommandIsSwitch($inIsSwitch);
		$this->setCommandIsOptional($inIsOptional);
		$this->setHaltAppAfterExecute(false);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {}
}