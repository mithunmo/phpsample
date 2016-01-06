<?php
/**
 * cliCommandSerialise Class
 * 
 * Stored in cliCommandSerialise.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandSerialise
 * @version $Rev: 707 $
 */


/**
 * cliCommandSerialise class
 * 
 * Utility command that simple passes the input into the PHP serialize
 * function and outputs it. Useful if you have to hack a database record
 * that is a serialised string of text for instance.
 * 
 * A specific example: the baseTableParamSet stores all properties as
 * serialised strings by default. This command can be used to replace an
 * existing entry in a param set.
 * 
 * <code>
 * // just serialises whatever you pass on the CLI
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(new cliCommandSerialise($oRequest))
 * $oApp->execute($oRequest);
 * </code>
 *
 * @package scorpio
 * @subpackage cli
 * @category cliCommandSerialise
 */
class cliCommandSerialise extends cliCommand {
	
	const COMMAND = 'serialise';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND);
		
		$this->setCommandHelp('Serialises the command line string, useful for updating strings in the database');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$this->getRequest()->getApplication()->getResponse()
			->addResponse("Serialising: ",$this->getRequest()->getParam(self::COMMAND))
			->addResponse(serialize($this->getRequest()->getParam(self::COMMAND)));
	}
}