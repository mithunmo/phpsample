<?php
/**
 * cliCommandPassword Class
 * 
 * Stored in logToConsole.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandPassword
 * @version $Rev: 809 $
 */


/**
 * cliCommandPassword class
 * 
 * Encodes the passed string into a hashed string via the passwordHash object.
 * This is useful for generating replacement passwords that can be inserted into
 * the database (if you use {@link passwordHash}).
 * 
 * <code>
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(new cliCommandPassword($oRequest))
 * $oApp->execute($oRequest);
 * </code>
 *
 * @package scorpio
 * @subpackage cli
 * @category cliCommandPassword
 */
class cliCommandPassword extends cliCommand {
	
	const COMMAND = 'password';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND);
		
		$this->setCommandHelp('Encode a string with passwordHash to use as a password hash');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		if ( strlen($this->getRequest()->getParam(self::COMMAND)) <= 1 ) {
			$this->getRequest()->getUserInput(self::COMMAND);
		}

		$oPwHash = new passwordHash(8, true);

		$this->getRequest()->getApplication()->getResponse()
			->addResponse("Hashing password: ".$this->getRequest()->getParam(self::COMMAND))
			->addResponse($oPwHash->HashPassword($this->getRequest()->getParam(self::COMMAND)));
	}
}