<?php
/**
 * systemCommandNewController Class
 * 
 * Stored in systemCommandNewController.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandNewController
 * @version $Rev: 805 $
 */


/**
 * systemCommandNewController class
 * 
 * Creates new controllers in the defined sites and attaches them to a component (plugin).
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandNewController
 */
class systemCommandNewController extends cliCommand {
	
	const COMMAND = 'controller';
	const COMMAND_DESC = 'desc';
	const COMMAND_DAO = 'dao';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND,
			new cliCommandChain(
				array(
					new cliCommandNull($inRequest, '<path/to/controller>', 'The full path to the controller including the controller itself but without the Controller suffix (e.g. controlPanel/user/login)', false, false, false),
					new systemCommandSite($inRequest),
					new cliCommandNull($inRequest, self::COMMAND_DESC, 'A short description for the controller that can be used as a title', true),
					new cliCommandNull($inRequest, self::COMMAND_DAO, 'If set, will build a controller for manipulating the Data Access Object, usually reserved for an admin site.', true),
				)
			)
		);
		
		$this->setCommandHelp(
			'Creates a new controller in the site and component specified. If any controllers are missing '.
			'from the path, these will be created as part of the process. Custom site templates can be '.
			'created once the site exists. They should be added to /data/templates/mvcGenerator. If any '.
			'files are missing from the path, they will be automatically generated. This includes default '.
			'controller, model, view and template files.'
		);
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		foreach ( $this->getCommandChain() as $oCommand ) {
			$oCommand->execute();
		}
		
		if ( !$this->getRequest()->getParam(self::COMMAND) || strlen($this->getRequest()->getParam(self::COMMAND)) < 2 ) {
			throw new cliApplicationCommandException($this, "No controller path has been provided or it is too short ({$this->getRequest()->getParam(self::COMMAND)})");
		}
		
		$this->buildFolderStructure();
		$controllers = $this->analyseControllerPath();
		
		if ( is_array($controllers) && count($controllers) > 0 ) {
			foreach ( $controllers as $data ) {
				$this->buildController($data);
			}
		}
	}
	
	
	
	/**
	 * Returns the system site from the specified domain
	 *
	 * @return mvcSiteTools
	 */
	function getSite() {
		return mvcSiteTools::getInstance($this->getRequest()->getParam(systemCommandSite::COMMAND));
	}
	
	/**
	 * Returns true if $inFile exists in the file system
	 *
	 * @param string $inFile
	 * @return boolean
	 */
	function doesFileExist($inFile) {
		if ( @file_exists($inFile) ) {
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_INFORMATIONAL,
					"Controller ($inFile) already exists"
				)
			);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Attempts to build the folder structure for the generated classes, returns the name of the singular database on success
	 *
	 * @return void
	 */
	function buildFolderStructure() {
		$structure = utilityStringFunction::cleanDirSlashes($this->getRequest()->getParam(self::COMMAND));
						
		$folder = system::getConfig()->getPathWebsites()
			.system::getDirSeparator()
			.$this->getSite()->getDomainName()
			.system::getDirSeparator().'controllers'.system::getDirSeparator().$structure;
		
		if ( @file_exists($folder) ) {
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_WARNING,
					"$folder already exists, continuing anyway",
					null,
					array(cliApplicationEvent::OPTION_LOG_SOURCE => 'buildFolderStructure')
				)
			);
			return;
		}
		if ( @mkdir($folder, 0775, true) ) {
			$view = system::getConfig()->getPathWebsites().system::getDirSeparator().$this->getSite()->getDomainName().system::getDirSeparator().'views'.system::getDirSeparator().$structure;
			if ( !file_exists($view) ) {
				if ( !@mkdir($view, 0775, true) ) {
					$this->getRequest()->getApplication()->notify(
						cliApplicationEvent::EVENT_WARNING,
						"Failed to make view folder, continuing anyway",
						null,
						array(cliApplicationEvent::OPTION_LOG_SOURCE => 'buildFolderStructure')
					);
					$this->getRequest()->getApplication()->getResponse()->addResponse("Failed to make view folder ($view), continuing anyway...");
				}
			}
		} else {
			throw new cliApplicationCommandException($this, "Failed to create folder structure for controller ({$this->getRequest()->getParam(self::COMMAND)}) at ($folder)");
		}
	}
	
	/**
	 * Builds the path to the controller if not already there, these are database records
	 * Returns the array of controller names and paths to be built
	 *
	 * @return array
	 */
	function analyseControllerPath() {
		$controllers = array();
		if ( strpos($this->getRequest()->getParam(self::COMMAND), '/') === 0 ) {
			$path = substr($this->getRequest()->getParam(self::COMMAND), 1);
		} else {
			$path = $this->getRequest()->getParam(self::COMMAND);
		}
		$path = explode("/", $path);
		if ( count($path) < 1 ) {
			throw new cliApplicationCommandException($this, "Unable to continue, site path ({$this->getRequest()->getParam(self::COMMAND)}) contains no information");
		}
		
		/*
		 * set our default path
		 */
		$baseFolder = system::getConfig()->getPathWebsites().system::getDirSeparator().$this->getSite()->getDomainName().system::getDirSeparator();
		$controllerFolder = $baseFolder.'controllers';
		$templateFolder = $baseFolder.'views';
		
		/*
		 * check that each component exists, otherwise we have to make it
		 */
		$controllerPath = '';
		$i = 0;
		foreach ( $path as $controller ) {
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_INFORMATIONAL,
					'Beginning build process for '.$controller,
					null,
					array('log.source' => 'Controller='.$controller)
				)
			);
			
			$i++;
			if ( $controller && $controller != '' ) {
				$controllerPath .= "/$controller";
				
				/*
				 * Add data entry to our internal array of controllers to build
				 */
				$addController = array(
					'name' => $controller,
					'path' => $controllerPath,
				);
				
				$files = array(
					'controllerFile' => $controllerFolder.$controllerPath.system::getDirSeparator().$controller.'Controller.class.php',
					'modelFile' => $controllerFolder.$controllerPath.system::getDirSeparator().$controller.'Model.class.php',
					'viewFile' => $controllerFolder.$controllerPath.system::getDirSeparator().$controller.'View.class.php',
				);
				if ( $this->getRequest()->getParam(self::COMMAND_DAO) && count($path) == $i ) {
					$addController['daoController'] = true;
					$files['templateFileForm'] = $templateFolder.$controllerPath.system::getDirSeparator().$controller.'Form.html.tpl';
					$files['templateFileList'] = $templateFolder.$controllerPath.system::getDirSeparator().$controller.'List.html.tpl';
				} else {
					$addController['daoController'] = false;
					$files['templateFile'] = $templateFolder.$controllerPath.system::getDirSeparator().$controller.'.html.tpl';
				}
				foreach ( $files as $option => $file ) {
					if ( !$this->doesFileExist($file) ) {
						$addController[$option] = $file;
					}
				}
				$controllers[] = $addController;
			}
		}
		return $controllers;
	}
	
	/**
	 * Builds a new controller component and class files from the supplied data array
	 * Data array should be generated by analyseControllerPath().
	 *
	 * @param array $inData
	 * @return void
	 */
	function buildController(array $inData = array()) {
		if ( !isset($inData['name']) ) {
			throw new cliApplicationCommandException($this, "Tried to build Controller but data array had no 'name'");
		}
		$oGen = new generatorController();
		$oGen->setSiteID($this->getSite()->getDomainName());
		if ( $inData['daoController'] == true ) {
			$oGen->setDaoObject($this->getRequest()->getParam(self::COMMAND_DAO));
		}
		
		$this->getRequest()->getApplication()->getResponse()->addResponse("\t-> Building controller files for {$inData['name']}");
		
		$oGen->buildControllerData($inData['name']);
		$oGen->buildDataSource();
		$oGen->build();
		
		$data = $oGen->getGeneratedContent();
		
		foreach ( $inData as $key => $value ) {
			$code = false;
			if ( array_key_exists($key, $data) ) {
				$code = $data[$key];
			}
			if ( $code ) {
				if ( @file_put_contents($value, $code) ) {
					$this->getRequest()->getApplication()->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_INFORMATIONAL,
							"Stored $value to the filesystem successfully"
						)
					);
				} else {
					throw new cliApplicationCommandException($this, "Failed to create file ($value) for controller");
				}
			}
		}
	}
}