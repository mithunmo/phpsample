<?php
/**
 * systemCommandExtractMvcAutoload Class
 * 
 * Stored in systemCommandExtractMvcAutoload.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandExtractMvcAutoload
 * @version $Rev: 829 $
 */


/**
 * systemCommandExtractMvcAutoload class
 * 
 * Given a site domain, will locate all class files and create the autoload cache file.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandExtractMvcAutoload
 */
class systemCommandExtractMvcAutoload extends cliCommand {
	
	const COMMAND = 'mvcautoload';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND, new cliCommandChain(
				array(
					new cliCommandNull($inRequest, '<domain.name>', 'The site domain name as defined in the websites folder.', true, false, false),
				)
			)
		);
		
		$this->setCommandHelp(
			'Given a site domain, will locate all class files and create the autoload cache file. '.
			'The site must be located in the websites folder and must have a correctly configured '.
			'config file and controllerMap. Note that the mvcAutoload data uses absolute file paths '.
			'you should therefore only run it if deploying to the same OS with the same folder structure.'
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
			throw new cliApplicationCommandException($this, 'Missing a domain to process, '.$domain.' is not valid'); 
		}
		if ( !is_dir(system::getConfig()->getPathWebsites().system::getDirSeparator().$domain) ) {
			throw new cliApplicationCommandException($this, 'Domain ('.$domain.') was not found in the websites folder ('.system::getConfig()->getPathWebsites().')');
		}
		
		$this->getRequest()->getApplication()->getResponse()
			->addResponse("Generating cache file for $domain for OS: ".PHP_OS);
		
		$oSiteConfig = new mvcSiteConfig(
			system::getConfig()->getPathWebsites().system::getDirSeparator().$domain.system::getDirSeparator().'config.xml',
			$domain
		);
		
		$controllers = $oSiteConfig->getControllerMapper()->getMapAsControllers();
		$map = array();
		foreach ( $oSiteConfig->getSiteClasses()->getParamSet() as $oParam ) {
			$class = $oParam->getParamName();
			$filename = $oParam->getParamValue();
			
			$map[$class] = $oSiteConfig->getFilePath('libraries'.system::getDirSeparator().$filename);
		}
		$map = $this->buildAutoloadData($oSiteConfig, $controllers, $map);
		
		$data = '<?php /* Auto-generated at '.date(DATE_COOKIE).' by '.__CLASS__.' */ return '.var_export($map, true).';';
		
		$file = mvcAutoload::getCacheFolder().system::getDirSeparator().$domain.mvcAutoload::AUTOLOAD_CACHE_FILE;
		$this->getRequest()->getApplication()->notify(
			new cliApplicationEvent(
				cliApplicationEvent::EVENT_INFORMATIONAL,
				'Attempting to write cache file to: '.$file
			)
		);
		
		$bytes = file_put_contents($file, $data);
		if ( $bytes > 0 ) {
			$this->getRequest()->getApplication()->getResponse()
				->addResponse("Created $file successfully");
		} else {
			$this->getRequest()->getApplication()->getResponse()
				->addResponse("Failed to create $file, is the folder writable?");
		}
	}
	
	
	/**
	 * Returns an array of mapped files to locations
	 *
	 * @param mvcSiteConfig $inSiteConfig
	 * @param array $inControllers
	 * @param array $inAutoloadMap
	 * @return array
	 */
	protected function buildAutoloadData(mvcSiteConfig $inSiteConfig, array $inControllers = array(), array $inAutoloadMap = array(), $inLevel = 0) {
		if ( count($inControllers) > 0 ) {
			if ( false ) $oController = new mvcControllerMap();
			foreach ( $inControllers as $oController ) {
				$classPrefix = $oController->getName();
				$filePath = $inSiteConfig->getFilePath('controllers'.system::getDirSeparator().$oController->getFilePath());
				$inAutoloadMap[$classPrefix.'Controller'] = $filePath;
				$inAutoloadMap[$classPrefix.'Model'] = str_replace('Controller.class', 'Model.class', $filePath);
				$inAutoloadMap[$classPrefix.'View'] = str_replace('Controller.class', 'View.class', $filePath);
				
				if ( $oController->hasSubControllers() ) {
					$oController->addControllerToPath($oController->getController(), $inLevel);
					$inAutoloadMap = array_merge($inAutoloadMap, $this->buildAutoloadData($inSiteConfig, $oController->getSubControllers(), $inAutoloadMap, $inLevel+1));
				}
			}
		}
		return $inAutoloadMap;
	}
}