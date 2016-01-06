<?php
/**
 * testCommandListTestCases Class
 * 
 * Stored in testCommandListTestCases.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category testCommandListTestCases
 * @version $Rev: 650 $
 */


/**
 * testCommandListTestCases class
 * 
 * Lists all the tests cases in the tests folder.
 *
 * @package scorpio
 * @subpackage cli
 * @category testCommandListTestCases
 */
class testCommandListTestCases extends cliCommand {
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, 'list', null, 'l');
		
		$this->setCommandHelp('Lists all available packages for testing');
		$this->setCommandRequiresValue(false);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$oResponse = $this->getRequest()->getApplication()->getResponse();
		$oResponse->addResponse("\nApplication Help System for {$this->getRequest()->getApplication()->getApplicationName()}");
		$oResponse->addResponse(cliConsoleTools::drawSeparator());
		$oResponse->addResponse("Available packages for testing:");
		
		$oPackages = testSuitePackages::getInstance();
		if ( false ) $oPackage = new testSuitePackage();
		foreach ( $oPackages as $package => $oPackage ) {
			$oResponse->addResponse("\nPackage >> $package");
			$oResponse->addResponse(cliConsoleTools::drawSeparator());
			$subpackages = $oPackage->getPackageSet();
			foreach ( $subpackages as $subpackage => $subPackageO ) {
				$oResponse->addResponse("\t ---> $subpackage");
			}
		}
	}
}