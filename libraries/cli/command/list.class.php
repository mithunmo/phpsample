<?php
/**
 * cliCommandList Class
 * 
 * Stored in cliCommandList.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandList
 * @version $Rev: 707 $
 */


/**
 * cliCommandList class
 * 
 * Will list items of the type, where type is defined in the command chain.
 * e.g. list could be for databases, tables, sites or users etc. This is
 * similar to the following commands:
 * 
 * <ul>
 *   <li>{@link cliCommandDelete delete}</li>
 *   <li>{@link cliCommandNew new}</li>
 *   <li>{@link cliCommandUpdate update}</li>
 * </ul>
 * 
 * <code>
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(
 *         new cliCommandList(
 *             $oRequest, new cliCommandChain(
 *                 array(
 *                     // your list commands
 *                 )
 *             )
 *         )
 *     )
 * $oApp->execute($oRequest);
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliCommandList
 */
class cliCommandList extends cliCommand {
	
	/**
	 * Creates a new list command
	 *
	 * @param cliRequest $inRequest
	 * @param cliCommandChain $inChain
	 */
	function __construct(cliRequest $inRequest, cliCommandChain $inChain) {
		parent::__construct($inRequest, 'list', $inChain);
		
		$this->setCommandHelp('Lists objects or items of the specified type');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$oCommand = $this->getCommandChain()->getCommand($this->getRequest()->getParam('list'));
		if ( $oCommand instanceof cliCommand ) {
			$oCommand->execute();
		} else {
			throw new cliApplicationCommandException($this, "Unable to list items of type: ".$this->getRequest()->getParam('list'));
		}
	}
}