<?php
/**
 * systemCommandNewDao Class
 * 
 * Stored in systemCommandNewDao.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandNewDao
 * @version $Rev: 715 $
 */


/**
 * systemCommandNewDao class
 * 
 * Handles creating Data Access Objects on the command line. Uses generator to build
 * and then output the class data.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandNewDao
 */
class systemCommandNewDao extends cliCommand {
	
	const COMMAND = 'dao';
	const COMMAND_PREFIX = 'prefix';
	const COMMAND_CLASSNAME = 'classname';
	const COMMAND_DAO_TEMPLATE = 'dao-template';
	
	const SWITCH_DB_PREFIX = 'd';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND,
			new cliCommandChain(
				array(
					new cliCommandSwitch($inRequest, self::SWITCH_DB_PREFIX, 'Use the database name in the class name (cannot be used with <classname>)'),
					new cliCommandNull($inRequest, '<dbname>', 'The database name to build against or <dbname.tbname> for a specific table', false, false, false),
					new cliCommandNull($inRequest, self::COMMAND_PREFIX, 'Use this prefix on the generated classes and as the package name', true),
					new cliCommandNull($inRequest, self::COMMAND_CLASSNAME, 'Use this as the full name of the class (single table only)', true),
					new cliCommandNull($inRequest, self::COMMAND_DAO_TEMPLATE, 'Use this template to build DAOs, should be located in userTemplates ('.system::getConfig()->getGeneratorUserTemplatePath().')', true),
				)
			)
		);
		
		$this->setCommandHelp(
			'Creates a new Data Access Object (DAO) from the specified database or database/table '.
			'combination. DAOs act as gateways to the database tables. Note: relations are '.
			'not modelled by this component. If a database is specified ALL tables will be '.
			'exported as DAOs. Once complete you will need to check and modify the classes. '.
			'Some tables cause issues e.g. parameter / property tables or those that are for '.
			'many-to-many relationships.'
		);
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$database = $table = $classname = $prefix = false;
		list($database, $table) = explode('.', $this->getRequest()->getParam(self::COMMAND));
		if ( !$database || strlen($database) < 2 ) {
			throw new cliApplicationCommandException($this, "No database specified or the name is too short ($database)");
		}
		
		if ( $this->getRequest()->getParam(self::COMMAND_CLASSNAME) ) {
			if ( strlen($this->getRequest()->getParam(self::COMMAND_CLASSNAME)) > 5 ) {
				$classname = $this->getRequest()->getParam(self::COMMAND_CLASSNAME);
			} else {
				throw new cliApplicationCommandException($this, "Classname supplied but with no or short value ({$this->getRequest()->getParam(self::COMMAND_CLASSNAME)})");
			}
		}
		
		if ( $this->getRequest()->getParam(self::COMMAND_PREFIX) ) {
			if ( strlen($this->getRequest()->getParam(self::COMMAND_PREFIX)) > 1 && !is_numeric($this->getRequest()->getParam(self::COMMAND_PREFIX)) ) {
				$prefix = $this->getRequest()->getParam(self::COMMAND_PREFIX);
			} else {
				throw new cliApplicationCommandException($this, "Prefix supplied but with invalid or short value ({$this->getRequest()->getParam(self::COMMAND_PREFIX)})");
			}
		}
		if ( $classname && !$table ) {
			throw new cliApplicationCommandException($this, "A table must be specified when generating a named single class");
		}
		if ( $prefix && $this->getRequest()->getSwitch(self::SWITCH_DB_PREFIX) ) {
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_ERROR,
					"Both prefix and use database prefix specified; using $prefix in place of database name"
				)
			);
		}
		
		try {
			$oGen = new generatorDao();
			$oGen->setUseDatabaseAsPrefix($this->getRequest()->getSwitch(self::SWITCH_DB_PREFIX));
			$oGen->setClassname($classname);
			$oGen->setClassPrefix($prefix);
			$oGen->setDatabase($database);
			$oGen->setTable($table);
			if ( $this->getRequest()->getParam(self::COMMAND_DAO_TEMPLATE) ) {
				$oGen->setTemplate($this->getRequest()->getParam(self::COMMAND_DAO_TEMPLATE));
			}
			$oGen->buildDataSource();
			$oGen->build();
			
			if ( count($oGen->getGeneratedContent()) > 0 ) {
				$this->buildFolderStructure($oGen);
				$this->writeAutoloadData($oGen);
				$this->writeClassData($oGen);
				
				$this->getRequest()->getApplication()->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_OK,
						"Class generator completed without error, check log for warnings",
						null,
						array(cliApplicationEvent::OPTION_LOG_SOURCE => 'Done')
					)
				);
			}
		} catch ( Exception $e ) {
			throw new cliApplicationCommandException($this, $e->getMessage());
		}
	}
	
	
	
	/**
	 * File writing methods
	 */
	
	/**
	 * Attempts to build the folder structure for the generated classes, returns the name of the singular database on success
	 *
	 * @param generatorDao $inGenerator
	 * @return void
	 * @throws cliApplicationCommandException
	 */
	function buildFolderStructure(generatorDao $inGenerator) {
		$this->getRequest()->getApplication()->notify(
			new cliApplicationEvent(
				cliApplicationEvent::EVENT_INFORMATIONAL,
				"Attempting to create folder structure for ".$inGenerator->getPackageBase(),
				null,
				array(cliApplicationEvent::OPTION_LOG_SOURCE=>'buildFolderStructure')
			)
		);
		
		if ( @is_writable(system::getConfig()->getPathClasses()) ) {
			if ( !@file_exists(system::getConfig()->getPathClasses().system::getDirSeparator().$inGenerator->getPackageBase()) ) {
				if ( @mkdir(system::getConfig()->getPathClasses().system::getDirSeparator().$inGenerator->getPackageBase(), 0775, true) ) {
					$this->getRequest()->getApplication()->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_INFORMATIONAL,
							"Folders built successfully"
						)
					);
				} else {
					throw new cliApplicationCommandException($this, 'Failed to create folder tree ('.system::getConfig()->getPathClasses().system::getDirSeparator().$inGenerator->getPackageBase().system::getDirSeparator().')');
				}
			} else {
				$this->getRequest()->getApplication()->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_ERROR,
						"Folders already exist in target location"
					)
				);
			}
		} else {
			throw new cliApplicationCommandException($this, 'Classes folder ('.system::getConfig()->getPathClasses().') is not writable');
		}
	}
	
	/**
	 * Writes out class data to file system
	 *
	 * @param generatorDao $inGenerator
	 * @return void
	 * @throws cliApplicationCommandException
	 */
	function writeClassData(generatorDao $inGenerator) {
		if ( $inGenerator->hasGeneratedContent() ) {
			$fileCount = 0;
			
			foreach ( $inGenerator->getGeneratedContent() as $className => $classData ) {
				$file = system::getConfig()->getPathClasses().system::getDirSeparator().$inGenerator->generateClassFileNameAndPath($className);
				
				$this->getRequest()->getApplication()->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						"Attempting to create $file",
						null,
						array(cliApplicationEvent::OPTION_LOG_SOURCE => 'writeClass]['.$className)
					)
				);
				
				if ( !@file_exists(dirname($file)) ) {
					$this->getRequest()->getApplication()->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_WARNING,
							"Missing folder: ".dirname($file)." making"
						)
					);
					@mkdir(dirname($file), 0775, true);
				}
				
				if ( !@file_exists($file) ) {
					$bytes = @file_put_contents($file, $classData, LOCK_EX|OVERWRITE);
					if ( $bytes > 0 ) {
						$this->getRequest()->getApplication()->notify(
							new cliApplicationEvent(
								cliApplicationEvent::EVENT_INFORMATIONAL,
								"Wrote $bytes bytes to the file system for file"
							)
						);
						$this->getRequest()->getApplication()->getResponse()->addResponse("Created $className and stored it at $file");
						$fileCount++;
					}
				} else {
					$this->getRequest()->getApplication()->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_WARNING,
							"$file already exists, and rebuild is to false, not overwriting"
						)
					);
				}
			}
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_INFORMATIONAL,
					"Successfully created $fileCount files out of ".count($inGenerator->getGeneratedContent())." generated classes",
					null,
					array(cliApplicationEvent::OPTION_LOG_SOURCE => 'writeClass')
				)
			);
		}
	}
	
	/**
	 * Creates an autoload file in autoload folder
	 *
	 * @param generatorDao $inGenerator
	 * @return void
	 */
	function writeAutoloadData(generatorDao $inGenerator) {
		if ( !$inGenerator->getTable() ) {
			$file = system::getConfig()->getPathClasses().system::getDirSeparator().'autoload'.system::getDirSeparator().strtolower($inGenerator->getPackageBase()).'_autoload.php';
			
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_INFORMATIONAL,
					"Attempting to write autoload file $file",
					null,
					array(cliApplicationEvent::OPTION_LOG_SOURCE => 'writeAutoload')
				)
			);
			
			if ( !@file_exists($file) ) {
				$bytes = @file_put_contents($file, $inGenerator->getAutoloadData());
				$this->getRequest()->getApplication()->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						"Wrote $bytes bytes to the file system for autoload file"
					)
				);
			} else {
				$this->getRequest()->getApplication()->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_WARNING,
						"$file already exists, not updating, outputting results\n{$inGenerator->getAutoloadData()}"
					)
				);
			}
		} else {
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_WARNING,
					"Table specified during build, skipping autoload file creation"
				)
			);
		}
	}
}