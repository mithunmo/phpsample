<?php
/**
 * systemCommandExtract Class
 * 
 * Stored in systemCommandExtract.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandExtract
 * @version $Rev: 829 $
 */


/**
 * systemCommandExtract class
 * 
 * Extracts various data from a resource (file, directory, site etc). This could be translation
 * information or specific text resources.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandExtract
 */
class systemCommandExtract extends cliCommand {
	
	const COMMAND = 'extract';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND, new cliCommandChain(
				array(
					new systemCommandExtractAutoload($inRequest),
					new systemCommandExtractMvcAutoload($inRequest),
					new systemCommandExtractTranslation($inRequest),
					new systemCommandExtractBootstrap($inRequest),
				)
			)
		);
		
		$this->setCommandHelp('Extracts various data from a resource (file, directory, site etc). This could be translation information or specific text resources.');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$oCommand = $this->getCommandChain()->getCommand($this->getRequest()->getParam(self::COMMAND));
		if ( $oCommand instanceof cliCommand ) {
			$oCommand->execute();
		} else {
			throw new cliApplicationCommandException($this, 'Extraction of type '.$this->getRequest()->getParam(self::COMMAND).' is not supported');
		}
	}
}