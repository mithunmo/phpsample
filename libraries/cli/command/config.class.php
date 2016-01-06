<?php
/**
 * cliCommandConfig Class
 * 
 * Stored in cliCommandConfig.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandConfig
 * @version $Rev: 707 $
 */


/**
 * cliCommandConfig class
 * 
 * Allows alternative config files to be read and imported into the systemConfig.
 * This command MUST come before other commands that need the modified config data. 
 * 
 * <code>
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(
 *         new cliCommandConfig($oRequest)
 *     )
 *     ->addCommand(new myCommandThatUsesConfigData())
 * $oApp->execute($oRequest);
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliCommandConfig
 */
class cliCommandConfig extends cliCommand {
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, 'config');
		
		$this->setCommandHelp(
			'Allows you to specify an alternative config file that will be loaded ',
			'into the systemConfig. Must be specified before any commands e.g. app',
			'.php --config=.. <command> are run'
		);
		$this->setCommandRequiresValue(true);
		$this->setCommandIsOptional(true);
		$this->setCommandIsSwitch(true);
		$this->setHaltAppAfterExecute(false);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$configFile = $this->getRequest()->getParam('config');
		if ( $configFile && strlen($configFile) > 5 ) {
			if ( !file_exists($configFile) ) {
				throw new cliApplicationCommandException($this, 'File at ('.$configFile.') does not exist');
			}
			if ( !is_readable($configFile) ) {
				throw new cliApplicationCommandException($this, 'File at ('.$configFile.') cannot be read');
			}
			try {
				system::getConfig()->load($configFile);
				$this->getRequest()->getApplication()->notify(
					new cliApplicationEvent(cliApplicationEvent::EVENT_OK, 'Successfully loaded config file @ '.$configFile)
				);
			} catch ( Exception $e ) {
				throw new cliApplicationCommandException($this, $e->getMessage());
			}
		} else {
			throw new cliApplicationCommandException($this, 'No config file was specified, please give the path to the config file.');
		}
	}
}