<?php
/**
 * cliCommandNew Class
 * 
 * Stored in new.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandNew
 * @version $Rev: 707 $
 */


/**
 * cliCommandNew class
 * 
 * Handles the keyword 'new' with a variable number of 'new'
 * objects assigned at runtime. Allows for the handling to be
 * consistent and re-used but the object types to change. This
 * is similar to the following commands:
 * 
 * <ul>
 *   <li>{@link cliCommandDelete delete}</li>
 *   <li>{@link cliCommandList list}</li>
 *   <li>{@link cliCommandUpdate update}</li>
 * </ul>
 * 
 * <code>
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(
 *         new cliCommandNew(
 *             $oRequest, new cliCommandChain(
 *                 array(
 *                     // your commands that create "new" things
 *                 )
 *             )
 *         )
 *     )
 * $oApp->execute($oRequest);
 * </code>
 *
 * @package scorpio
 * @subpackage cli
 * @category cliCommandNew
 */
class cliCommandNew extends cliCommand {
	
	/**
	 * Creates the new command
	 *
	 * @param cliRequest $inRequest
	 * @param cliCommandChain $inCommandChain
	 */
	function __construct($inRequest, $inCommandChain = null) {
		parent::__construct($inRequest, 'new', $inCommandChain);
		
		$this->setCommandHelp('Creates new objects in the system');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the new command, directing to a the appropriate command
	 *
	 * @return void
	 * @throws cliApplicationCommandException
	 */
	function execute() {
		$oCommand = $this->getCommandChain()->getCommand($this->getRequest()->getParam('new'));
		if ( $oCommand instanceof cliCommand ) {
			$oCommand->execute();
		} else {
			throw new cliApplicationCommandException($this, 'New object of type '.$this->getRequest()->getParam('new').' is not valid');
		}
	}
}