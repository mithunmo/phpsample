<?php
/**
 * systemCommandNewSite Class
 * 
 * Stored in systemCommandNewSite.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandNewSite
 * @version $Rev: 791 $
 */


/**
 * systemCommandNewSite class
 * 
 * Creates a new site entry in the system.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandNewSite
 */
class systemCommandNewSite extends cliCommand {
	
	const COMMAND = 'site';
	const COMMAND_SITE_PARENT = 'parent';
	const COMMAND_SITE_TYPE = 'type';
	const SWITCH_SITE_BUILD_FILES = 'b';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND,
			new cliCommandChain(
				array(
					new cliCommandNull($inRequest, '<domain.name>', 'The new sites domain name e.g. example.com; do NOT include sub-domains such as www. wap. or rss. these are automatically enabled by the MVC framework', false, false, false),
					new cliCommandNull($inRequest, self::COMMAND_SITE_PARENT, 'The parent site to inherit from, use a site listed in the site listings, if not set uses base', true),
					new cliCommandNull($inRequest, self::COMMAND_SITE_TYPE, 'The site type, either admin or site, default is site', true),
					new cliCommandSwitch($inRequest, self::SWITCH_SITE_BUILD_FILES, 'If set, creates default templates and classes for the site'),
				)
			)
		);
		
		$this->setCommandHelp(
			'Creates a new website in the system, creating the folders and default setup. '.
			'A site is need for the controller build process. Optionally the site type can '.
			'be specified. There are two site types in scorpio: a normal site and an admin site. '.
			'The default is all sites are normal sites.'
		);
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$domain = $this->getRequest()->getParam(self::COMMAND);
		$type = $this->getRequest()->getParam(self::COMMAND_SITE_TYPE);
		$parent = $this->getRequest()->getParam(self::COMMAND_SITE_PARENT);
		$buildFiles = $this->getRequest()->getSwitch(self::SWITCH_SITE_BUILD_FILES);
		
		if ( strlen($domain) < 3 ) {
			throw new cliApplicationCommandException($this, "Domain ($domain) is too short");
		}
		
		$oSite = mvcSiteTools::getInstance($domain);
		if ( file_exists($oSite->getSitePath()) ) {
			throw new cliApplicationCommandException($this, "Domain ($domain) is already in use and registered on the system");
		}
		
		$oSite->setDomainName($domain);
		$oSite->setParentSite(($parent ? $parent : 'base'));
		$oSite->setType(($type && $type == 'admin' ? mvcSiteTools::TYPE_ADMIN : mvcSiteTools::TYPE_SITE));
		$oSite->setBuildFiles(($buildFiles === true));
		
		try {
			$oSite->save();
			if ( file_exists($oSite->getSitePath()) ) {
				$this->getRequest()->getApplication()->getResponse()->addResponse("Created new site ($domain) successfully");
			} else {
				throw new cliApplicationCommandException($this, "Failed to store site details for new domain ($domain)");
			}
		} catch ( Exception $e ) {
			throw new cliApplicationCommandException($this, $e->getMessage());
		}
	}
}