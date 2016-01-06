<?php
/**
 * systemCommandListSites Class
 * 
 * Stored in systemCommandListSites.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandListSites
 * @version $Rev: 650 $
 */


/**
 * systemCommandListSites class
 * 
 * Lists the currently configured sites on the CLI.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandListSites
 */
class systemCommandListSites extends cliCommand {
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, 'sites');
		
		$this->setCommandHelp('Lists the currently defined sites in the system');
		$this->setCommandRequiresValue(false);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$sites = mvcSiteTools::listOfObjects();
		$this->getRequest()->getApplication()->getResponse()
			->addResponse("\nThe following sites are available on the system:\n");
		
		if ( false ) $oSite = new mvcSiteTools();
		$array = array();
		foreach ( $sites as $oSite ) {
			$array[] = array(
				'Domain Name' => $oSite->getDomainName(),
				'Type' => $oSite->getType(),
				'Parent' => $oSite->getSiteConfig()->getParentSite(),
				'Active' => ($oSite->getSiteConfig()->isActive() ? 'Yes' : 'No'),
			);
		}
		
		$this->getRequest()->getApplication()->getResponse()
			->addResponse(cliConsoleTools::cliDataPrint($array, null, cliConstants::CONSOLE_WIDTH));
	}
}