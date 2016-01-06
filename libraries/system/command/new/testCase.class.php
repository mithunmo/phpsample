<?php
/**
 * systemCommandNewTestCase Class
 * 
 * Stored in systemCommandNewTestCase.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandNewTestCase
 * @version $Rev: 650 $
 */


/**
 * systemCommandNewTestCase class
 * 
 * Creates test cases for the specified class. These test cases are generated 
 * from the class itself. You can optionally specifying a package and sub-package
 * that the test should belong to.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandNewTestCase
 */
class systemCommandNewTestCase extends cliCommand {
	
	const COMMAND = 'test';
	const COMMAND_CLASSNAME = '<classname>';
	const COMMAND_TEMPLATE = 'template';
	const COMMAND_PACKAGE = 'package';
	const COMMAND_SUB_PACKAGE = 'subpackage';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND,
			new cliCommandChain(
				array(
					new cliCommandNull($inRequest, self::COMMAND_CLASSNAME, 'Build a test case skeleton for this class', false, false, false),
					new cliCommandNull($inRequest, self::COMMAND_PACKAGE, 'Build test case using this package name', true),
					new cliCommandNull($inRequest, self::COMMAND_SUB_PACKAGE, 'Build test case using this sub-package name', true),
					new cliCommandNull($inRequest, self::COMMAND_TEMPLATE, 'Use this template to build test case, should be located in userTemplates ('.system::getConfig()->getGeneratorUserTemplatePath().')', true),
				)
			)
		);
		
		$this->setCommandHelp(
			'Creates test cases for the specified class. These test cases are generated '.
			'from the class itself. You can optionally specifying a package and sub-package '.
			'that the test should belong to.'
		);
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$classname = $package = $subpackage = false;
		if ( $this->getRequest()->getParam(self::COMMAND) ) {
			if ( strlen($this->getRequest()->getParam(self::COMMAND)) > 5 ) {
				$classname = $this->getRequest()->getParam(self::COMMAND);
			} else {
				throw new cliApplicationCommandException($this, "Classname supplied but with no or short value ({$this->getRequest()->getParam(self::COMMAND)})");
			}
		}
		if ( $this->getRequest()->getParam(self::COMMAND_PACKAGE) ) {
			if ( strlen($this->getRequest()->getParam(self::COMMAND_PACKAGE)) > 3 ) {
				$package = $this->getRequest()->getParam(self::COMMAND_PACKAGE);
			} else {
				throw new cliApplicationCommandException($this, "Package supplied but with no or short value ({$this->getRequest()->getParam(self::COMMAND_PACKAGE)})");
			}
		}
		if ( $this->getRequest()->getParam(self::COMMAND_SUB_PACKAGE) ) {
			if ( strlen($this->getRequest()->getParam(self::COMMAND_SUB_PACKAGE)) > 3 ) {
				$subpackage = $this->getRequest()->getParam(self::COMMAND_SUB_PACKAGE);
			} else {
				throw new cliApplicationCommandException($this, "Package supplied but with no or short value ({$this->getRequest()->getParam(self::COMMAND_SUB_PACKAGE)})");
			}
		}
		
		try {
			$oGen = new generatorTestCase();
			$oGen->setClass($classname);
			$oGen->setPackage($package);
			$oGen->setSubPackage($subpackage);
			if ( $this->getRequest()->getParam(self::COMMAND_TEMPLATE) ) {
				$oGen->setTemplate($this->getRequest()->getParam(self::COMMAND_TEMPLATE));
			}
			$oGen->buildDataSource();
			$oGen->build();
			$data = $oGen->getGeneratedContent();
			$classData = $data['testcase'];
			
			$dir = system::getConfig()->getPathData().system::getDirSeparator().'tests'.system::getDirSeparator();
			
			if ( !@file_exists($dir.$oGen->getSubPackage()) ) {
				@mkdir($dir.$oGen->getSubPackage(), 0775, true);
			}
			
			$filename = $dir.$oGen->getSubPackage().system::getDirSeparator().$oGen->getCategory().'.class.php';
			
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_INFORMATIONAL,
					"Attempting to write file $filename"
				)
			);
			
			if ( !@file_exists($filename) ) {
				$bytes = @file_put_contents($filename, $classData);
				$this->getRequest()->getApplication()->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						"Wrote $bytes bytes to the file systems for testCase file"
					)
				);
				$this->getRequest()->getApplication()->getResponse()->addResponse("Created ".basename($filename)." and stored it at ".dirname($filename));
			} else {
				$this->getRequest()->getApplication()->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_WARNING,
						"$filename already exists not updating"
					)
				);
			}
			
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_OK,
					"Class generator completed without error, check log for warnings",
					null,
					array(cliApplicationEvent::OPTION_LOG_SOURCE => 'Done')
				)
			);
		} catch ( Exception $e ) {
			throw new cliApplicationCommandException($this, $e->getMessage());
		}
	}
}