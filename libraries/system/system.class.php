<?php
/**
 * system.class.php
 * 
 * Scorpio Framework System File
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @version $Rev: 845 $
 */


/*
 * Load dependencies
 */
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'exception.class.php');


/**
 * system
 * 
 * The main system class is responsible for creating the environment within which
 * the Scorpio framework runs. This configures all the low-level details such as
 * logging, reading the core config file, setting up the autoloader and ensures
 * that any critical actions are performed.
 * 
 * This class is referenced in system.inc and should only ever be called once.
 * 
 * <code>
 * // init system
 * system::init();
 * 
 * // code path
 * system::getScriptPath();
 * 
 * // check if we are on CLI
 * system::getIsCli()
 * 
 * // fetch a config param
 * $oParam = system::getConfig()->getDatabase('system');
 * </code>
 * 
 * Certain functionality can be disabled via config params e.g.: the Scorpio error
 * and exception handlers can be prevented from being installed by setting the config
 * options 'system', 'registerErrorHandler' and 'system', 'registerExceptionHandler'
 * to false (string false, no or int 0).
 * 
 * @package scorpio
 * @subpackage system
 * @final
 */
final class system {
	
	/**
	 * Global version constant for scorpio
	 *
	 * @var string
	 */
	const SCORPIO_VERSION = '0.6.0';
	
	/*
	 * Constants for the core objects stored in the registry object
	 */
	const REGISTRY_CONFIG = 'Config';
	const REGISTRY_LOCALE = 'Locale';
	const REGISTRY_EVENT_DISPATCHER = 'Dispatcher';
	
	/*
	 * Constants for the init options
	 */
	
	/**
	 * The absolute base path where the Scorpio folders are located
	 * 
	 * @var string
	 */
	const INIT_OPTION_BASE_PATH = 'init.basePath';
	/**
	 * Full path to the master config file
	 * 
	 * @var string
	 */
	const INIT_OPTION_CONFIG_FILE = 'init.configFile';
	
	/**
	 * System directory separator
	 *
	 * @var string
	 * @access private
	 * @static 
	 */
	private static $_DirSeparator		= false;
	
	/**
	 * Constant true if in CLI
	 *
	 * @var boolean
	 * @access private
	 */
	private static $_IsCli				= false;
	
	/**
	 * Current scripts path
	 *
	 * @var string
	 * @access private
	 */
	private static $_ScriptPath			= '';
	
	/**
	 * Relative path of current script to base
	 *
	 * @var string
	 * @access private
	 */
	private static $_ScriptRelativePath	= '';
	
	/**
	 * Current scripts file name
	 *
	 * @var string
	 * @access private
	 */
	private static $_ScriptFilename		= '';
	
	/**
	 * Contains registry object system
	 *
	 * @var systemRegistry
	 * @access private
	 * @static 
	 */
	private static $_Registry			= false;
	
	
	
	/**
	 * Returns system instance
	 * 
	 * @return system
	 * @access private
	 */
	private function __construct () {
		throw new systemCannotBeInstantiated();
	}
	
	
	
	/**
	 * Initialise the base system components ready for use
	 * 
	 * Takes an array of options for setting basepath and central
	 * config location. If not specified, defaults will be used.
	 *
	 * @param array $inOptions
	 * @return void
	 * @static
	 */
	final static function init(array $inOptions = array()) {
		/*
		 * Set up global static vars
		 */
		self::setIsCli();
		self::setScriptFilename();
		self::setScriptPath();
		
		/*
		 * Initialise autoload system
		 */
		self::registerAutoloader('systemAutoload::autoload');
		
		/*
		 * Initialise object registry system
		 */
		self::initRegistry($inOptions);
		systemAutoload::addPath(self::getConfig()->getPathLibraries());
		systemAutoload::addPath(self::getConfig()->getPathClasses());
		
		/*
		 * Must come after config is loaded 
		 */
		self::setScriptRelativePath();
		
		/*
		 * Check that the temp and log folders can be written to; if required
		 */
		if ( self::getConfig()->checkFolderPermissions() ) {
			self::checkPermissions();
		}
		
		/*
		 * Set-up CLI environment, if required
		 */
		if ( self::getIsCli() ) {
			/*
			 * Include patch files if pcntl or posix is missing regardless of platform
			 */
			if ( !extension_loaded('pcntl') ) {
				require_once(self::getConfig()->getPathLibraries().DIRECTORY_SEPARATOR.'pcntl_patch.php');
			}
			if ( !extension_loaded('posix') ) {
				require_once(self::getConfig()->getPathLibraries().DIRECTORY_SEPARATOR.'posix_patch.php');
			}
			
			/*
			 * This protects stdout from getting duplicate messages from CHILD_PROCESS processes
			 */
			@ob_end_flush();
			set_time_limit(0);
			error_reporting(E_ALL);
		}
		
		/*
		 * Set new error handler to our systemLog class
		 */
		if ( self::getConfig()->registerErrorHandler() ) {
			set_error_handler(array('systemLog', 'errorHandler'));
		}
		
		/*
		 * Set a global exception handler
		 */
		if ( self::getConfig()->registerExceptionHandler() ) {
			set_exception_handler(array('systemLog', 'exceptionHandler'));
		}
		
		/*
		 * Set a default DSN, if required
		 */
		if ( self::getConfig()->registerDefaultDatabaseDsn() ) {
			try {
				dbManager::setDefaultDsn(
					dbOptions::getInstance(
						self::getConfig()->getDatabaseDsn()->getParamValue()
					)
				);
			} catch ( Exception $e ) {
				systemLog::info("Attempted to set system default DSN but failed: ".$e->getMessage());
			}
		}
		
		/*
		 * Set default timezone - will raise an E_NOTICE if invalid
		 */
		if ( self::getConfig()->getSystemTimeZone()->getParamValue() ) {
			@date_default_timezone_set(self::getConfig()->getSystemTimeZone()->getParamValue());
		}
	}
	
	/**
	 * Checks the permissions on variable folders, stops framework if an error is encountered
	 * 
	 * @return void
	 */
	static function checkPermissions() {
		if ( !@file_exists(self::getConfig()->getPathLogs()) || !@is_writable(self::getConfig()->getPathLogs()) ) {
			exit("The LOGS folder cannot be written to by the current process: ".self::getConfig()->getPathLogs());
		}
		if ( !@file_exists(self::getConfig()->getPathTemp()) || !@is_writable(self::getConfig()->getPathTemp()) ) {
			exit("The TEMP folder cannot be written to by the current process: ".self::getConfig()->getPathTemp());
		}
	}
	
	/**
	 * Get the current system directory separator
	 *
	 * @return string
	 */
	static function getDirSeparator() {
		if ( !self::$_DirSeparator ) {
			self::$_DirSeparator = DIRECTORY_SEPARATOR;
		}
		return self::$_DirSeparator;
	}
	
	/**
	 * Returns the current script path
	 *
	 * @return string
	 */
	static function getScriptPath() {
		return self::$_ScriptPath;
	}
	
	/**
	 * Set the script path
	 *
	 * @return void
	 * @access private
	 * @static
	 */
	private static function setScriptPath() {
		if ( self::getIsCli() ) {
			if ( substr($_SERVER['SCRIPT_NAME'],0,1) == self::getDirSeparator() ) {
				self::$_ScriptPath = dirname($_SERVER['SCRIPT_NAME']);
			} else {
				if ( isset($_SERVER['PWD']) && strpos($_SERVER['PWD'], '/cygdrive') === 0 ) {
					// if running under cygwin, the path is from the cygwin terminal
					$pwd = preg_replace('/\/cygdrive\/([A-Z]{1})\//i', '\1:/', $_SERVER['PWD']);
					$pwd = ucfirst(str_replace(array('\\', '/'), self::getDirSeparator(), $pwd));
					$apath = explode(self::getDirSeparator(), $pwd);
				} else {
					if ( isset($_SERVER['PWD']) ) {
						$apath = explode(self::getDirSeparator(), substr($_SERVER['PWD'],1));
					} else {
						$apath = explode(self::getDirSeparator(), substr(dirname(dirname(dirname(__FILE__))),1));
					}
				}
				
				$pos = count($apath)-1;
				$ascript = explode(self::getDirSeparator(), $_SERVER['SCRIPT_NAME']);
				foreach ($ascript as $val) {
					if ($val == '.') continue;
					if ($val == '..') {
						$pos--;
						continue;
					}
					if ($pos < -1) {
						break;
					}
					$apath[++$pos] = $val;
				}
				
				if (  isset($_SERVER['PWD']) && strpos($_SERVER['PWD'], '/cygdrive') === 0 ) {
					self::$_ScriptPath = trim(dirname(implode(self::getDirSeparator(), $apath)));
				} else {
					self::$_ScriptPath = trim(dirname(self::getDirSeparator().implode(self::getDirSeparator(), $apath)));
				}
			}
		} else {
			self::$_ScriptPath = str_replace(
				array('\\', '/'), self::getDirSeparator(), dirname($_SERVER['SCRIPT_FILENAME'])
			);
		}
	}
	
	/**
	 * Returns the current script relative path
	 *
	 * @return string
	 */
	static function getScriptRelativePath() {
		return self::$_ScriptRelativePath;
	}
	
	/**
	 * Set the script relative path
	 *
	 * @return void
	 * @access private
	 * @static
	 */
	private static function setScriptRelativePath() {
		self::$_ScriptRelativePath = str_replace(
			self::getConfig()->getBasePath().self::getDirSeparator(), '', self::getScriptPath()
		);
	}
	
	/**
	 * Returns the current script name
	 *
	 * @return string
	 */
	static function getScriptFilename() {
		return self::$_ScriptFilename;
	}
	
	/**
	 * Set the script name
	 *
	 * @return void
	 * @access private
	 * @static
	 */
	private static function setScriptFilename() {
		if ( self::getIsCli() ) {
			self::$_ScriptFilename = basename($_SERVER['SCRIPT_NAME']);
		} else {
			self::$_ScriptFilename = basename($_SERVER['SCRIPT_FILENAME']);
		}
	}
	
	/**
	 * Returns true if current running on CLI
	 *
	 * @return boolean
	 * @static 
	 */
	static function getIsCli() {
		return self::$_IsCli;
	}
	
	/**
	 * Sets the status of IsCli
	 *
	 * @return void
	 * @access private
	 * @static
	 */
	private static function setIsCli() {
		if (strtoupper(php_sapi_name()) == 'CLI') {
			self::$_IsCli = true;
		}
	}
	
	
	
	/**
	 * Registers an autoload implementation with spl_autoload_register
	 *
	 * This method accepts both strings and arrays as input. For static
	 * autoload methods, they can be assigned as a string using:
	 * ClassName::autoLoadMethod or the array syntax: array(ClassName, autoLoadMethod)
	 *
	 * @param mixed $inAutoloader
	 * @return void
	 * @static 
	 */
	static function registerAutoloader($inAutoloader) {
		spl_autoload_register($inAutoloader);
	}
	
	/**
	 * Sets up the main registry system
	 *
	 * @param array $inOptions
	 * @return void
	 * @access private
	 * @static
	 */
	private static function initRegistry(array $inOptions = array()) {
		$configFile = false;
		$oConfig = new systemConfig();
		if ( count($inOptions) > 0 ) {
			if ( array_key_exists(self::INIT_OPTION_BASE_PATH, $inOptions) ) {
				$oConfig->getBasePath()->setParamValue($inOptions[self::INIT_OPTION_BASE_PATH]);
			}
			if ( array_key_exists(self::INIT_OPTION_CONFIG_FILE, $inOptions) ) {
				$configFile = $inOptions[self::INIT_OPTION_CONFIG_FILE];
			}
		}
		$oConfig->load($configFile);
		
		$oRegistry = self::getRegistry();
		$oRegistry->set(self::REGISTRY_CONFIG, $oConfig);
		$oRegistry->set(self::REGISTRY_LOCALE, new systemLocale('auto'));
		$oRegistry->set(self::REGISTRY_EVENT_DISPATCHER, new systemEventDispatcher());
	}
	
	
	
	/**
	 * Compares the version with the current scorpio version.
	 * 
	 * Returns true if $inVersion is greater than or equal to the current version.
	 * 
	 * @param string $inVersion A string in format X.Y.Z
	 * @return boolean
	 * @static 
	 */
	static function compareVersion($inVersion) {
		return version_compare(self::SCORPIO_VERSION, $inVersion, '<=');
	}
	
	/**
	 * Returns the registry object, creating it if not set
	 *
	 * @return systemRegistry
	 * @static 
	 */
	static function getRegistry() {
		if ( self::$_Registry === false ) {
			self::$_Registry = new systemRegistry();
		}
		return self::$_Registry;
	}
	
	/**
	 * Returns the autoload system object
	 *
	 * @return systemAutoload
	 * @static 
	 */
	static function getAutoload() {
		return systemAutoload::getInstance();
	}
	
	/**
	 * Returns the system config object
	 *
	 * @return systemConfig
	 * @static 
	 */
	static function getConfig() {
		return self::getRegistry()->get(self::REGISTRY_CONFIG);
	}
	
	/**
	 * Returns the system locale object
	 *
	 * @return systemLocale
	 * @static
	 */
	static function getLocale() {
		return self::getRegistry()->get(self::REGISTRY_LOCALE);
	}
	
	/**
	 * Returns a system level event dispatcher
	 * 
	 * @return systemEventDispatcher
	 * @static
	 */
	static function getEventDispatcher() {
		return self::getRegistry()->get(self::REGISTRY_EVENT_DISPATCHER);
	}
}