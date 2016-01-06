<?php
/**
 * systemCommandNewReport Class
 * 
 * Stored in systemCommandNewReport.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandNewReport
 * @version $Rev: 805 $
 */


/**
 * systemCommandNewReport class
 * 
 * Creates a new report shell using the specified template.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandNewReport
 */
class systemCommandNewReport extends cliCommand {
	
	const COMMAND = 'report';
	const COMMAND_TEMPLATE = 'template';
	const SWITCH_IS_COLLECTION = 'c';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND,
			new cliCommandChain(
				array(
					new cliCommandSwitch($inRequest, self::SWITCH_IS_COLLECTION, 'Creates a report collection shell that can contain other reports'),
					new cliCommandNull($inRequest, '<reportClassName>', 'The class name for the report e.g. myReportClass', false, false, false),
					new cliCommandNull($inRequest, self::COMMAND_TEMPLATE, 'Use this template to build the report shell, should be located in userTemplates ('.system::getConfig()->getGeneratorUserTemplatePath().')', true),
				)
			)
		);
		
		$this->setCommandHelp(
			'Creates a report shell from the default report template, or optionally a custom template.'.
			' The report class will be built in the classes folder ('.system::getConfig()->getPathClasses().').'.
			' Once created you will need to customise the report for your needs.'
		);
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		if ( !$this->getRequest()->getParam(self::COMMAND) || strlen($this->getRequest()->getParam(self::COMMAND)) < 2 ) {
			throw new cliApplicationCommandException($this, "No classname has been provided or it is too short ({$this->getRequest()->getParam(self::COMMAND)})");
		}

		$classname = trim($this->getRequest()->getParam(self::COMMAND));

		if ( strpos($classname, '_') !== false ) {
			$filepath = strtolower(str_replace('_', system::getDirSeparator(), $classname));
		} else {
			$filepath = strtolower(utilityStringFunction::convertCapitalizedString($classname, system::getDirSeparator()));
		}
		$filepath .= '.class.php';

		$folder = $this->buildFolderStructure($filepath);

		$oGen = new generatorReport();
		$oGen->setClass($classname);
		$oGen->setIsCollection($this->getRequest()->getSwitch(self::SWITCH_IS_COLLECTION));
		$oGen->buildDataSource();
		$oGen->build();

		$data = $oGen->getGeneratedContent();


		$res = @file_put_contents($folder.system::getDirSeparator().basename($filepath), $data['report']);
		if ( $res > 0 ) {
			$response = "Created {$oGen->getClass()} and stored at ".$folder.system::getDirSeparator().basename($filepath)." successfully";
			$this->getRequest()->getApplication()->getResponse()->addResponse($response);
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_INFORMATIONAL,
					$response,
					null,
					array()
				)
			);
		} else {
			throw new cliApplicationCommandException($this, 'Unable to create file in '.$folder);
		}
	}

	/**
	 * Attempts to build the folder structure for the generated classes, returns the name of the singular database on success
	 *
	 * @return void
	 */
	function buildFolderStructure($inFilePath) {
		$folder = dirname(
			system::getConfig()->getPathClasses()
			.system::getDirSeparator()
			.$inFilePath
		);

		if ( @file_exists($folder) ) {
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_WARNING,
					"$folder already exists, continuing anyway",
					null,
					array(cliApplicationEvent::OPTION_LOG_SOURCE => 'buildFolderStructure')
				)
			);
			return $folder;
		}

		if ( !@mkdir($folder, 0755, true) ) {
			throw new cliApplicationCommandException($this, 'Unable to create folder structure ('.$folder.')');
		}

		return $folder;
	}
}