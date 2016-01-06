<?php
/**
 * systemCommandExtractBootstrap Class
 * 
 * Stored in systemCommandExtractBootstrap.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern 2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandExtractBootstrap
 * @version $Rev: 670 $
 */


/**
 * systemCommandExtractBootstrap class
 * 
 * Creates bootstrap include files containing all the resources in a single
 * file for a particular project. Bootstraps require a config file that details
 * all the classes that should be loaded and the order they should be loaded in.
 * The config file will also dictate where the bootstrap file should be located.
 *
 * The config file is an XML file with the following structure:
 *
 * <code>
 * <bootstrap>
 *     <application></application>
 *     <filename></filename>
 *     <fileLocation></fileLocation>
 *     <classes>
 *         <class></class>
 *     </classes>
 * </bootstrap>
 * </code>
 * 
 * Application is an idenfitier for that particular config file e.g. a project name
 * or the application name. It should be unique for each config file.
 * 
 * Filename is the name of the .php file that will be generated (e.g. bootstrap). This
 * should be just the filename without any extension.
 * 
 * FileLocation is the relative path in the current scorpio folder to export the
 * bootstrap data to. e.g. "temp" will output to %BASEPATH%/temp
 * 
 * Classes contains the actual class data. Each class should be marked inside a class
 * tag. Order is important. You should list your classes in the order they are needed
 * from most basic to most specific. If this is not done, compile errors may result.
 * Further your classes should not contain multiple defines, or use require/include(_once)
 * otherwise they will fail. In fact require/include(_once) will be stripped from the
 * files as they are processed.
 *
 * You should test the compiled file thoroughly before deploying it.
 * 
 * Finally: if using APC or another opcode cache, you may have to reset the max file
 * limit. The core Scorpio files require nearly 1MB in APC, anything beyond these
 * core files will hit the default file size limit of APC.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandExtractBootstrap
 */
class systemCommandExtractBootstrap extends cliCommand {
	
	const COMMAND = 'bootstrap';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND, new cliCommandChain(
				array(
					new cliCommandNull($inRequest, '<config.file>', 'The config file to use to build the bootstrap file. This can be located within Scorpio or somewhere on the filesystem.', false, false, false),
				)
			)
		);
		
		$this->setCommandHelp('Creates bootstrap include files containing all the resources in a single file for a particular project.');
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$configFile = $this->getRequest()->getParam(self::COMMAND);
		
		if ( !file_exists($configFile) || !is_readable($configFile) ) {
			throw new cliApplicationCommandException($this, 'Config file ('.$configFile.') is not readable or does not exist');
		}
		
		$oXML = simplexml_load_file($configFile);
		if ( !$oXML instanceof SimpleXMLElement ) {
			throw new cliApplicationCommandException($this, 'Invalid XML file ('.$configFile.')');
		}
		
		$classes = $this->convertToArray($oXML, 'classes/class');
		if ( count($classes) == 0 ) {
			throw new cliApplicationCommandException($this, 'No classes were found in config file. Please define classes in a <classes><class /></classes> tag set.');
		}
		
		if ( !isset($oXML->fileLocation) || strlen((string) $oXML->fileLocation) == 0 ) {
			throw new cliApplicationCommandException($this, 'No fileLocation specified in config file');
		}
		if ( !isset($oXML->filename) || strlen((string) $oXML->filename) == 0 ) {
			throw new cliApplicationCommandException($this, 'No filename specified in config file');
		}
		
		$output = (string) $oXML->fileLocation;
		if ( strpos($output, system::getDirSeparator()) !== 0 ) {
			$output = system::getDirSeparator().$output;
		}
		
		$output = system::getConfig()->getBasePath().$output;
		if ( !file_exists($output) ) {
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_INFORMATIONAL,
					'Attempting to create output folder'
				)
			);
			if ( !mkdir($output, 0755, true) ) {
				throw new cliApplicationCommandException($this, 'Unable to create output folder ('.$output.') check permissions');
			}
		}
		if ( !is_writable($output) ) {
			throw new cliApplicationCommandException($this, 'Unable to write to output folder ('.$output.') check permissions');
		}
		
		$filename = (string) $oXML->filename;
		
		systemPackager::package($classes, $output, $filename, true);
		
		$this->getRequest()->getApplication()->getResponse()->addResponse('Successfully created files in ('.$output.') folder');
	}
	
	/**
	 * Converts $inXML to a standard PHP array using $inTag which is an xPath query
	 * 
	 * @param SimpleXMLElement $inXML
	 * @param string $inTag xPath query
	 * @return array
	 */
	function convertToArray(SimpleXMLElement $inXML, $inTag) {
		$return = array();
		
		$res = $inXML->xpath($inTag);
		
		foreach ( $res as $oXml ) {
			$return[] = (string) $oXml;
		}
		
		return $return;
	}
}