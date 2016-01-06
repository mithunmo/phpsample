<?php
/**
 * systemCommandExtractAutoload Class
 * 
 * Stored in systemCommandExtractAutoload.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandExtractAutoload
 * @version $Rev: 832 $
 */


/**
 * systemCommandExtractAutoload class
 * 
 * Given a site domain, will locate all class files and create the autoload cache file.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandExtractAutoload
 */
class systemCommandExtractAutoload extends cliCommand {
	
	const COMMAND = 'autoload';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND, new cliCommandChain(
				array()
			)
		);
		
		$this->setCommandHelp(
			'Parses all files in the custom classes folder ('.system::getConfig()->getPathClasses().')'.
			' and attempts to create _autoload files within the /autoload folder. This will create'.
			' package level _autoload files and will overwrite existing cache files.'
		);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$this->getRequest()->getApplication()->getResponse()
			->addResponse("Locating classes in: ".system::getConfig()->getPathClasses());

		$autoload = array();
		$files = fileObject::parseDir(system::getConfig()->getPathClasses(), true);

		/* @var fileObject $oFile */
		foreach ( $files as $oFile ) {
			if ( stripos($oFile->getFilename(), '_autoload') !== false ) {
				continue;	
			}
			if ( stripos($oFile->getOriginalFilename(), '.svn') !== false ) {
				continue;
			}
			if ( stripos($oFile->getOriginalFilename(), '.git') !== false ) {
				continue;
			}
			if ( stripos($oFile->getOriginalFilename(), '.cvs') !== false ) {
				continue;
			}


			$contents = $oFile->get();

			$matches = array();
			preg_match('/\n(abstract class|class|interface) (\w+)/', $contents, $matches);

			if ( isset($matches[2]) ) {
				if ( strpos($matches[2], '_') === false ) {
					$package = utilityStringFunction::convertCapitalizedString($matches[2], '_');
				} else {
					$package = $matches[2];
				}

				$package = substr($package, 0, strpos($package, '_'));
				$autoload[$package][$matches[2]] = str_replace(system::getConfig()->getPathClasses().DIRECTORY_SEPARATOR, '', $oFile->getOriginalFilename());
			}
		}

		foreach ( $autoload as $package => $data ) {
			$fdata = "<?php
/**
 * system Autoload component
 *
 * @author ".system::getConfig()->getParam('app', 'author', 'Scorpio Generator')."
 * @copyright ".system::getConfig()->getParam('app', 'copyright', 'Scorpio Framework (c) 2007-'.date('Y'))."
 * @package scorpio
 * @subpackage system
 * @category systemAutoload
 */
return array(\n";

			foreach ( $data as $class => $path ) {
				$fdata .= "\t'$class' => '$path',\n";
			}

			$fdata .= ");\n";

			$file = system::getConfig()->getPathClasses().'/autoload/'.strtolower($package).'_autoload.php';

			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_INFORMATIONAL,
					'Attempting to write cache file to: '.$file
				)
			);

			$bytes = file_put_contents($file, $fdata);
			if ( $bytes > 0 ) {
				$this->getRequest()->getApplication()->getResponse()
					->addResponse("Created $file successfully");
			} else {
				$this->getRequest()->getApplication()->getResponse()
					->addResponse("Failed to create $file, is the folder writable?");
			}
		}
	}
}