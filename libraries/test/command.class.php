<?php
/**
 * testCommand Class
 * 
 * Stored in testCommand.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category testCommand
 * @version $Rev: 722 $
 */


/**
 * testCommand class
 * 
 * Executes the test cases specified. This can either be all test cases or
 * a specific package or a specific test case in a package.
 *
 * @package scorpio
 * @subpackage cli
 * @category testCommand
 */
class testCommand extends cliCommand {
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest, cliCommandChain $inCommandChain) {
		parent::__construct($inRequest, 'test', $inCommandChain);
		
		$this->setCommandHelp('Runs a test case or all test cases if set to all, use help <command> for more');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$oCommand = $this->getCommandChain()->getCommand($this->getRequest()->getParam('test'));
		if ( !$oCommand instanceof cliCommand ) {
			throw new cliApplicationCommandException($this, "Command ({$this->getRequest()->getParam('test')}) is not valid");
		}
		if ( $this->getRequest()->getParam('test') == 'package' && (!$this->getRequest()->getParam('package') || !is_string($this->getRequest()->getParam('package'))) ) {
			 throw new cliApplicationCommandException($this, "Command ({$this->getRequest()->getParam('test')}) requires a package name to run");
		}
		
		@require_once('PHPUnit/Framework.php');
		if ( !class_exists('PHPUnit_Framework_TestCase') ) {
			throw new cliApplicationCommandException($this, 'testSuite requires PHPUnit be installed and configured');
		}
		
		if ( $this->getRequest()->getParam('package') ) {
			$package = $this->getRequest()->getParam('package');
		} else {
			$package = null;
		}
		
		if ( $package && $this->getRequest()->getParam($this->getRequest()->getParam('package')) && strlen($this->getRequest()->getParam($this->getRequest()->getParam('package'))) > 1 ) {
			$testCase = $this->getRequest()->getParam($this->getRequest()->getParam('package'));
			if ( strpos($testCase, 'test') === false ) {
				$testCase = 'test'.ucfirst($testCase);
			}
		} else {
			$testCase = null;
		}
		
		$oResults = testSuitePackages::createTestSuite(
			testSuitePackages::getInstance(), $package, $testCase
		)->run();
		
		@require_once 'PHPUnit/TextUI/ResultPrinter.php';
		if ( !class_exists('PHPUnit_TextUI_ResultPrinter') ) {
			throw new cliApplicationCommandException($this, 'Failed to load PHPUnit_TextUI_ResultPrinter, please check PHPUnit is correctly installed');
		}
		$oPrinter = new PHPUnit_TextUI_ResultPrinter();
		$oPrinter->printResult($oResults);
	}
}