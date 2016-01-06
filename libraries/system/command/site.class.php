<?php
/**
 * systemCommandSite Class
 * 
 * Stored in systemCommandSite.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandSite
 * @version $Rev: 650 $
 */


/**
 * systemCommandSite class
 * 
 * Validates if a site name matches an existing site stored in the system.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandSite
 */
class systemCommandSite extends cliCommand {
	
	const COMMAND = 'site';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND);
		
		$this->setCommandHelp('Specifies the site to perform actions on, use <list sites> to list current sites');
		$this->setCommandRequiresValue(true);
		$this->setCommandIsSwitch(true);
		$this->setHaltAppAfterExecute(false);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$site = $this->getRequest()->getParam(self::COMMAND);
		$oSite = mvcSiteTools::getInstance($site);
		if ( !is_object($oSite) || !file_exists($oSite->getSitePath()) ) {
			throw new cliApplicationCommandException($this, "Site with name ($site) could not be loaded. Please check the site has been created.");
		}
	}
}