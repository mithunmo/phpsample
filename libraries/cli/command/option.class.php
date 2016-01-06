<?php
/**
 * cliCommandOption Class
 * 
 * Stored in cliCommandOption.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandOption
 * @version $Rev: 707 $
 */


/**
 * cliCommandOption class
 * 
 * Creates a command that requires one value out of several possibilities. For example:
 * you may want to limit the CLI command to only allow certain databases or tables, or
 * that the command requires a "mode" switch that can be only one of a handful of values.
 * 
 * <code>
 * // example where a table is needed
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(
 *         new cliCommandOption(
 *             $oRequest, 'table', array('table1', 'table2', 'table3), false, 'Select table to use'
 *         )
 *     )
 * $oApp->execute($oRequest);
 * </code>
 *
 * @package scorpio
 * @subpackage cli
 * @category cliCommandOption
 */
class cliCommandOption extends cliCommand {
	
	const COMMAND = 'option';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 * @param string $inCommandName
	 * @param array $inAvailableOptions Array of valid entries for the command
	 * @param boolean $inIsOptional Is the option mandatory? Default false
	 * @param string $inCommandHelp Optional help text for the option
	 */
	function __construct(cliRequest $inRequest, $inCommandName = 'option', array $inAvailableOptions = array(), $inIsOptional = false, $inCommandHelp = null) {
		parent::__construct($inRequest, $inCommandName);
		
		if ( count($inAvailableOptions) > 0 ) {
			foreach ( $inAvailableOptions as $option ) {
				$this->getCommandChain()->addCommand(
					new cliCommandNull($inRequest, $option, 'A possible value for '.$inCommandName, false, false, false)
				);
			}
		}
		
		$this->setCommandHelp(($inCommandHelp !== null ? $inCommandHelp : 'Creates a command that requires one of several defined possibilities.'));
		$this->setCommandRequiresValue(true);
		$this->setCommandIsOptional($inIsOptional);
		$this->setHaltAppAfterExecute(false);
		$this->setCommandIsSwitch(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$oCommand = $this->getCommandChain()->getCommand($this->getRequest()->getParam($this->getCommandPattern()));
		if ( !$oCommand instanceof cliCommand ) {
			throw new cliApplicationCommandException($this, 'Option '.$this->getRequest()->getParam($this->getCommandPattern()).' is not valid');
		}
	}
}