<?php
/**
 * cliCommandDelete Class
 * 
 * Stored in delete.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandDelete
 * @version $Rev: 707 $
 */


/**
 * cliCommandDelete class
 * 
 * Handles the keyword 'delete' with a variable number of 'delete'
 * objects assigned at runtime. Allows for the handling to be
 * consistent and re-used but the object types to change.
 * 
 * This command is similar to the following:
 * 
 * <ul>
 *   <li>{@link cliCommandList list}</li>
 *   <li>{@link cliCommandNew new}</li>
 *   <li>{@link cliCommandUpdate update}</li>
 * </ul>
 * 
 * <code>
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(
 *         new cliCommandDelete(
 *             $oRequest, new cliCommandChain(
 *                 array(
 *                     // commands for deleting things
 *                 )
 *             )
 *         )
 *     )
 * $oApp->execute($oRequest);
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliCommandDelete
 */
class cliCommandDelete extends cliCommand {
	
	/**
	 * Creates the delete command
	 *
	 * @param cliRequest $inRequest
	 * @param cliCommandChain $inCommandChain
	 */
	function __construct($inRequest, $inCommandChain = null) {
		parent::__construct($inRequest, 'delete', $inCommandChain);
		
		$this->setCommandHelp('Removes objects or items from the system');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the delete command, directing to a the appropriate command
	 *
	 * @return void
	 * @throws cliApplicationCommandException
	 */
	function execute() {
		$oCommand = $this->getCommandChain()->getCommand($this->getRequest()->getParam('delete'));
		if ( $oCommand instanceof cliCommand ) {
			$oCommand->execute();
		} else {
			throw new cliApplicationCommandException($this, 'Object of type '.$this->getRequest()->getParam('delete').' is not supported');
		}
	}
}