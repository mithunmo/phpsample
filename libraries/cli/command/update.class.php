<?php
/**
 * cliCommandUpdate Class
 * 
 * Stored in update.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandUpdate
 * @version $Rev: 707 $
 */


/**
 * cliCommandUpdate class
 * 
 * Handles the keyword 'update' with a variable number of 'update'
 * objects assigned at runtime. Allows for the handling to be
 * consistent and re-used but the object types to change.
 * 
 * This command is similar to the following:
 * 
 * <ul>
 *   <li>{@link cliCommandDelete delete}</li>
 *   <li>{@link cliCommandList list}</li>
 *   <li>{@link cliCommandNew new}</li>
 * </ul>
 *
 * <code>
 * // basic example of using update command
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(
 *         new cliCommandUpdate(
 *             $oRequest,
 *             new cliCommandChain(
 *                 array(
 *                     // my update commands here
 *                 )
 *             )
 *         )
 *     )
 * $oApp->execute($oRequest);
 * </code>
 *
 * @package scorpio
 * @subpackage cli
 * @category cliCommandUpdate
 */
class cliCommandUpdate extends cliCommand {
	
	/**
	 * Creates the update command
	 *
	 * @param cliRequest $inRequest
	 * @param cliCommandChain $inCommandChain
	 */
	function __construct($inRequest, $inCommandChain = null) {
		parent::__construct($inRequest, 'update', $inCommandChain);
		
		$this->setCommandHelp('Updates objects or items in the system');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the update command, directing to a the appropriate command
	 *
	 * @return void
	 * @throws cliApplicationCommandException
	 */
	function execute() {
		$oCommand = $this->getCommandChain()->getCommand($this->getRequest()->getParam('update'));
		if ( $oCommand instanceof cliCommand ) {
			$oCommand->execute();
		} else {
			throw new cliApplicationCommandException($this, 'Object of type '.$this->getRequest()->getParam('update').' is not supported');
		}
	}
}