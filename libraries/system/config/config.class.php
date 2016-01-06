<?php
/**
 * systemConfig.class.php
 *
 * System config class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemConfig
 * @version $Rev: 722 $
 */


/**
 * systemConfig
 *
 * systemConfig class extends systemConfigBase and adds specific methods to fetch
 * frequently used parameters. These include the database DSN which will be populated
 * with the username, database etc if they are set, timezone, users, paths etc.
 * 
 * Note: in most cases these methods return a systemConfigParam object, but in the
 * case of isProduction and registerExceptionHandler / registerErrorHandler the 
 * boolean value is returned.
 *
 * @package scorpio
 * @subpackage system
 * @category systemConfig
 */
class systemConfig extends systemConfigBase {

	/**
	 * Returns new systemConfig
	 *
	 * @return systemConfig
	 */
	function __construct() {
		parent::__construct();
	}



	/**
	 * Returns true if the system is in production, default is true (more secure)
	 *
	 * @return boolean
	 */
	function isProduction() {
		return $this->getParam('system', 'isProduction', true)->getParamValue();
	}
	
	/**
	 * Returns true if the built-in errorHandler should be registered with PHP
	 *
	 * @return boolean
	 */
	function registerErrorHandler() {
		return $this->getParam('system', 'registerErrorHandler', true)->getParamValue();
	}
	
	/**
	 * Returns true if the built-in exceptionHandler should be registered with PHP
	 *
	 * @return boolean
	 */
	function registerExceptionHandler() {
		return $this->getParam('system', 'registerExceptionHandler', true)->getParamValue();
	}
	
	/**
	 * If true, a default DSN will be built and assigned to the dbManager.
	 * This causes the dbManager to be loaded - but not connected.
	 *
	 * @return boolean
	 */
	function registerDefaultDatabaseDsn() {
		return $this->getParam('system', 'registerDefaultDatabaseDsn', true)->getParamValue();
	}
	
	/**
	 * Returns true if the temporary and log folders should be checked on each invocation;
	 * Should be disabled on production servers.
	 *
	 * @return boolean
	 */
	function checkFolderPermissions() {
		return $this->getParam('system', 'checkFolderPermissions', true)->getParamValue();
	}

	/**
	 * Returns the system locale, default is 'en'
	 *
	 * @return systemConfigParam
	 */
	function getSystemLocale() {
		return $this->getParam('system', 'locale', 'en');
	}
	
	/**
	 * Returns the system timezone, default is UTC (GMT0)
	 *
	 * @link http://www.php.net/manual/en/timezones.php
	 * @link http://www.php.net/manual/en/function.date-default-timezone-set.php
	 * @return systemConfigParam
	 */
	function getSystemTimeZone() {
		return $this->getParam('system', 'timezone', 'UTC');
	}
	
	/**
	 * Returns the system from email address
	 *
	 * @return systemConfigParam
	 */
	function getSystemFromAddress() {
		return $this->getParam('system', 'fromAddress', 'webmaster@your.domain.com');
	}

	/**
	 * Returns the system hostname
	 *
	 * @return systemConfigParam
	 */
	function getSystemHostname() {
		return $this->getParam('system', 'hostname', 'www.yourdomain.com');
	}

	/**
	 * Returns the default system group
	 *
	 * @return systemConfigParam
	 */
	function getSystemGroup() {
		return $this->getParam('system', 'group', 'scorpioPlatform');
	}

	/**
	 * Returns the Posix ID for the current group
	 *
	 * @return integer
	 * @throws systemConfigException
	 */
	function getSystemGroupGid() {
		$info = posix_getgrnam($this->getSystemGroup()->getParamValue());
		if ( is_array($info) ) {
			return $info['gid'];
		} else {
			throw new systemConfigException('Unable to locate group information for '.$this->getSystemGroup());
		}
	}

	/**
	 * Returns the default system user
	 *
	 * @return systemConfigParam
	 */
	function getSystemUser() {
		return $this->getParam('system', 'user', 'scorpio');
	}

	/**
	 * Returns the Posix ID for the current user
	 *
	 * @return integer
	 * @throws systemConfigException
	 */
	function getSystemUserId() {
		$info = posix_getpwnam($this->getSystemUser()->getParamValue());
		if ( is_array($info) ) {
			return $info['uid'];
		} else {
			throw new systemConfigException('Unable to locate user information for '.$this->getSystemUser());
		}
	}
	
	/**
	 * Returns the URI separator to use in URI strings
	 *
	 * @return systemConfigParam
	 */
	function getSystemUriSeparator() {
		return $this->getParam('system', 'uriSeparator', '_');
	}
	
	
	
	/**
	 * Returns the current logging type, this is the name of a valid systemLogWriter object
	 *
	 * @return systemConfigParam
	 */
	function getSystemLogType() {
		return $this->getParam('system', 'logType', 'systemLogWriterFile');
	}
	
	/**
	 * Returns the permissions to apply to created log folders
	 *
	 * @return systemConfigParam
	 */
	function getSystemLogFolderPermissions() {
		return $this->getParam('system', 'logFolderPermissions', 0755);
	}
	
	/**
	 * Returns the permissions to apply to created log files
	 *
	 * @return systemConfigParam
	 */
	function getSystemLogFilePermissions() {
		return $this->getParam('system', 'logFilePermissions', 0644);
	}

	/**
	 * Returns the current log level (as defined in systemLogLevel)
	 *
	 * @return systemConfigParam
	 */
	function getSystemLogLevel() {
		return $this->getParam('system', 'logLevel', systemLogLevel::WARNING);
	}

	/**
	 * Returns the date format to use on log entries
	 *
	 * @return systemConfigParam
	 */
	function getSystemLogDateFormat() {
		return $this->getParam('system', 'logDateFormat', 'd/m/Y H:i:s');
	}

	/**
	 * Returns the date format to use on log entries
	 *
	 * @return systemConfigParam
	 */
	function getSystemLogUseExtendedExceptionData() {
		return $this->getParam('system', 'logUseExtendedExceptionData', true);
	}



	/**
	 * Returns the system default DSN string
	 *
	 * @return systemConfigParam
	 */
	function getDatabaseDsn() {
		$oParam = $this->getParam('database', 'dsn', '%TYPE%://%USER%:%PASSWORD%@%HOST%/%DATABASE%');

		$replace = array(
			'%USER%' => self::getDatabaseUser(),
			'%PASSWORD%' => self::getDatabasePassword(),
			'%DATABASE%' => self::getDatabaseDefault(),
			'%TYPE%' => self::getDatabaseType(),
			'%HOST%' => self::getDatabaseHost(),
			'%PORT%' => self::getDatabasePort(),
		);

		$oNewParam = new systemConfigParam('dsn', strtr($oParam->getParamValue(), $replace), true);
		$oNewParam->setModified();
		return $oNewParam;
	}

	/**
	 * Returns the system default database TYPE
	 *
	 * @return systemConfigParam
	 */
	function getDatabaseType() {
		return $this->getParam('database', 'type', 'mysql');
	}

	/**
	 * Returns the database host
	 *
	 * @return systemConfigParam
	 */
	function getDatabaseHost() {
		return $this->getParam('database', 'host', 'localhost');
	}

	/**
	 * Returns the database port
	 *
	 * @return systemConfigParam
	 */
	function getDatabasePort() {
		return $this->getParam('database', 'port', '3306');
	}

	/**
	 * Returns the system default database user
	 *
	 * @return systemConfigParam
	 */
	function getDatabaseUser() {
		if ( stripos(system::getScriptPath(), self::getPathDaemons()->getParamValue()) !== false ) {
			$oParam = self::getDatabaseUserDaemon();
		} elseif ( stripos(system::getScriptPath(), self::getPathWebsites()->getParamValue()) !== false ) {
			$oParam = self::getDatabaseUserWeb();
		} else {
			$oParam = self::getDatabaseUserScript();
		}
		return $oParam;
	}

	/**
	 * Returns username for a web script
	 *
	 * @return systemConfigParam
	 */
	function getDatabaseUserWeb() {
		return $this->getParam('database', 'userWeb', 'web');
	}

	/**
	 * Returns username for a CLI script
	 *
	 * @return systemConfigParam
	 */
	function getDatabaseUserScript() {
		return $this->getParam('database', 'userScript', 'script');
	}

	/**
	 * Returns username for a daemon process
	 *
	 * @return systemConfigParam
	 */
	function getDatabaseUserDaemon() {
		return $this->getParam('database', 'userDaemon', 'daemon');
	}

	/**
	 * Returns the system default database password
	 *
	 * @return systemConfigParam
	 */
	function getDatabasePassword() {
		return $this->getParam('database', 'userPassword', 'yourdatabasepassword');
	}

	/**
	 * Returns the configured database name for the friendly name
	 *
	 * @param string $inDatabase
	 * @return systemConfigParam
	 */
	function getDatabase($inDatabase) {
		return $this->getParam('database', $inDatabase, $this->getDatabaseDefault());
	}

	/**
	 * Returns a default database to connect to
	 *
	 * @return systemConfigParam
	 */
	function getDatabaseDefault() {
		return $this->getParam('database', 'default', 'scorpio_system');
	}
	
	/**
	 * Returns the date() string format for the databases datetime field
	 *
	 * @return systemConfigParam
	 */
	function getDatabaseDatetimeFormat() {
		return $this->getParam('database', 'datetime', 'Y-m-d H:i:s');
	}
        
        /**
	 * Returns the date() string format for the databases datetime field with default time as 12:00
	 *
	 * @return systemConfigParam
	 */
	function getDatabaseEndDatetimeFormat() {

		return $this->getParam('database', 'datetimeend', 'Y-m-d 23:50:00');
	}

	/**
	 * Returns the date() string format for the databases date field
	 *
	 * @return systemConfigParam
	 */
	function getDatabaseDateFormat() {
		return $this->getParam('database', 'date', 'Y-m-d');
	}

	/**
	 * Returns the date() string format for the databases time field
	 *
	 * @return systemConfigParam
	 */
	function getDatabaseTimeFormat() {
		return $this->getParam('database', 'time', 'H:i:s');
	}



	/**
	 * Returns the path to the libraries
	 *
	 * @return systemConfigParam
	 */
	function getPathLibraries() {
		return $this->getParam('paths', 'libraries', $this->getBasePath().system::getDirSeparator().'libraries');
	}

	/**
	 * Returns the path to the apps
	 *
	 * @return systemConfigParam
	 */
	function getPathApps() {
		return $this->getParam('paths', 'apps', $this->getBasePath().system::getDirSeparator().'apps');
	}

	/**
	 * Returns the path to the data folder
	 *
	 * @return systemConfigParam
	 */
	function getPathData() {
		return $this->getParam('paths', 'data', $this->getBasePath().system::getDirSeparator().'data');
	}

	/**
	 * Returns the path to the daemons
	 *
	 * @return systemConfigParam
	 */
	function getPathDaemons() {
		return $this->getParam('paths', 'daemons', $this->getBasePath().system::getDirSeparator().'daemons');
	}

	/**
	 * Returns the path to the logs
	 *
	 * @return systemConfigParam
	 */
	function getPathLogs() {
		return $this->getParam('paths', 'logs', $this->getBasePath().system::getDirSeparator().'logs');
	}

	/**
	 * Returns the path to the temp
	 *
	 * @return systemConfigParam
	 */
	function getPathTemp() {
		return $this->getParam('paths', 'temp', $this->getBasePath().system::getDirSeparator().'temp');
	}

	/**
	 * Returns the path to the smarty temp folders
	 *
	 * @return systemConfigParam
	 */
	function getPathTemplateTemp() {
		return $this->getParam('paths', 'templateTemp', $this->getBasePath().system::getDirSeparator().'temp'.system::getDirSeparator().'templates');
	}

	/**
	 * Returns the path to the smarty temp compile folders
	 *
	 * @return systemConfigParam
	 */
	function getPathTemplateCompile() {
		return $this->getParam('paths', 'templateCompile', $this->getPathTemplateTemp()->getParamValue().system::getDirSeparator().'compileDir');
	}

	/**
	 * Returns the path to the smarty temp cache folders
	 *
	 * @return systemConfigParam
	 */
	function getPathTemplateCache() {
		return $this->getParam('paths', 'templateCache', $this->getPathTemplateTemp()->getParamValue().system::getDirSeparator().'cacheDir');
	}

	/**
	 * Returns the path to the tools
	 *
	 * @return systemConfigParam
	 */
	function getPathTools() {
		return $this->getParam('paths', 'tools', $this->getBasePath().system::getDirSeparator().'tools');
	}
	
	/**
	 * Returns the path to the classes folder (what was plugins) that contains non-core classes
	 *
	 * @return systemConfigParam
	 * @since 2008-09-06
	 */
	function getPathClasses() {
		return $this->getParam('paths', 'classes', $this->getBasePath().system::getDirSeparator().'classes');
	}

	/**
	 * Returns the path to the websites
	 *
	 * @return systemConfigParam
	 */
	function getPathWebsites() {
		return $this->getParam('paths', 'websites', $this->getBasePath().system::getDirSeparator().'websites');
	}

	
	
	/**
	 * Returns the URI location to the master WURFL XML file
	 *
	 * @return systemConfigParam
	 */
	function getWurflUriLocation() {
		return $this->getParam('wurfl', 'uriLocation', 'http://downloads.sourceforge.net/wurfl/wurfl-latest.zip');
	}
	
	/**
	 * Returns the maximum allowed file import size, value should be in bytes e.g. 0.5MB = 512000
	 *
	 * @return systemConfigParam
	 */
	function getWurflMaxImportSize() {
		return $this->getParam('wurfl', 'maxImportSize', 512000);
	}

	/**
	 * Returns the maximum number of devices that can be exported at any one time
	 *
	 * @return systemConfigParam
	 */
	function getWurflMaxExportSize() {
		return $this->getParam('wurfl', 'maxExportSize', 100);
	}
	
	
	
	/**
	 * Returns the maximum allowed size for an image to previewed (in bytes)
	 *
	 * @return systemConfigParam
	 */
	function getMaxImageSizeForPreview() {
		return $this->getParam('images', 'maxImageSizeForPreview', 512000);
	}
	
	/**
	 * Returns the preview width for image previews
	 *
	 * @return systemConfigParam
	 */
	function getImagePreviewWidth() {
		return $this->getParam('images', 'previewWidth', 40);
	}
	
	/**
	 * Returns the preview height for image previews
	 *
	 * @return systemConfigParam
	 */
	function getImagePreviewHeight() {
		return $this->getParam('images', 'previewHeight', 40);
	}
	
	
	
	/**
	 * Returns the default generator Data Access Object template
	 *
	 * @return systemConfigParam
	 */
	function getGeneratorDaoTemplate() {
		 return $this->getParam('generator', 'daoTemplate', 'default.tpl');
	}
	
	/**
	 * Returns the default generator Test Case template
	 *
	 * @return systemConfigParam
	 */
	function getGeneratorTestCaseTemplate() {
		return $this->getParam('generator', 'testCaseTemplate', 'testCase.tpl');
	}
	
	/**
	 * Returns the path to user generated templates for generator
	 *
	 * @return systemConfigParam
	 */
	function getGeneratorUserTemplatePath() {
		return $this->getParam('generator', 'userTemplates', self::getPathData().system::getDirSeparator().'templates'.system::getDirSeparator().'generator');
	}
	
	/**
	 * Returns the path to user generated templates for mvcGenerator
	 *
	 * @return systemConfigParam
	 */
	function getMvcGeneratorUserTemplatePath() {
		return $this->getParam('mvcGenerator', 'userTemplates', self::getPathData().system::getDirSeparator().'templates'.system::getDirSeparator().'mvcGenerator');
	}
}
