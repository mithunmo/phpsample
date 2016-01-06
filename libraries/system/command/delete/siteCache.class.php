<?php
/**
 * systemCommandDeleteSiteCache Class
 * 
 * Stored in systemCommandDeleteSiteCache.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandDeleteSiteCache
 * @version $Rev: 650 $
 */


/**
 * systemCommandDeleteSiteCache class
 * 
 * Removes all cache files including cached template files from the specified site.
 * If a site has changed controllers or parents it is highly recommended to perform
 * this action.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandDeleteSiteCache
 */
class systemCommandDeleteSiteCache extends cliCommand {
	
	const COMMAND = 'sitecache';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND,
			new cliCommandChain(
				array(
					new cliCommandNull($inRequest, '<domain.name>', 'The site domain name as it has been registered with the system', false, false, false),
				)
			)
		);
		
		$this->setCommandHelp(
			'Removes all cache files including cached template files from the specified site. '.
			'If a site has changed controllers or parents it is highly recommended to perform '.
			'this action.'
		);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$domain = $this->getRequest()->getParam(self::COMMAND);
		if ( strlen($domain) < 5 ) {
			throw new cliApplicationCommandException($this, "Please specify the domain name, ($domain) is too short");
		}
		$oSite = mvcSiteTools::getInstance($domain);
		if ( !file_exists($oSite->getSitePath()) ) {
			throw new cliApplicationCommandException($this, "Domain ($domain) is not registered in the system; use 'list sites' if available");
		}
		
		$oSite->clearSiteCacheFiles();
		
		$this->getRequest()->getApplication()->getResponse()
			->addResponse("Process complete, all cache files should be removed");
	}
}