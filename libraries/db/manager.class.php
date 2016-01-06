<?php
/**
 * dbManager.class.php
 * 
 * Contains management system for database connections
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbManager
 * @version $Rev: 707 $
 */


/**
 * dbManager Class
 * 
 * Contains management system for database connections. dbManager can handle multiple
 * separate connection instances simply by using alternative connection credentials.
 * 
 * dbManager is a static class and should always be called via {@link dbManager::getInstance()}.
 * It can be called without any parameters but only if either: a default DSN has already
 * been set OR if there is a system configured DSN in {@link systemConfig::getDatabaseDsn()}.
 * 
 * dbManager can be instanatied with either a fully qualified DSN string in a supported form
 * (see {@link dbOptions::parseDsn()}) or a pre-built dbOptions object.
 * 
 * <code>
 * // most simple usage, using system defaults
 * $oDb = dbManager::getInstance();
 * 
 * // with custom DSN
 * $oDb = dbManager::getInstance('sqlite3:///somedb.db');
 * 
 * // using dbOptions
 * $oDb = dbManager::getInstance(new dbOptions());
 * </code>
 * 
 * dbManager will cache the first requested instance as the default DSN for all further
 * requests. To change this you can at any time use {@link dbManager::setDefaultDsn()}. This
 * takes a dbOptions object.
 * 
 * <code>
 * // example setting default dsn
 * $dbOptions = new dbOptions();
 * // set options ...
 * 
 * // now register as default
 * dbManager::setDefaultDsn($dbOptions);
 * 
 * // uses new default dsn
 * $oDb = dbManager::getInstance();
 * </code>
 * 
 * @see dbOptions
 * @package scorpio
 * @subpackage db
 * @category dbManager
 */
final class dbManager {
	
	/**
	 * Database instances array
	 *
	 * @var array
	 * @access private
	 * @static 
	 */
	private static $_Instances				= array();
	/**
	 * Default DB connection
	 *
	 * @var dbOptions
	 * @access private
	 * @static 
	 */
	private static $_DefaultDsn				= false;
	/**
	 * Array of pre-installed database type mappings to drivers
	 *
	 * @var array
	 * @access private
	 * @static 
	 */
	private static $_Drivers				= array(
		'mysql'		=> 'dbDriverMySql',
		'sqlite'	=> 'dbDriverSqlite',
		'pgsql'		=> 'dbDriverPgSql',
	);
	
	
	/**
	 * Prevent dbManager being instantiated
	 */
	private function __construct() {
		throw new dbManagerCannotBeInstantiated();
	}
	
	
	
	/**
	 * Connects to a database instance with the supplied DSN string or dbOptions object.
	 * DB objects are cached using the properties of the dbOptions object, if no DSN is specified
	 * and the defaultDsn has been set, this connection is immediately returned.
	 *
	 * @param string $dsn
	 * @return dbDriver
	 * @throws dbManagerMissingDsnException
	 * @throws dbManagerDsnToDbOptionsParseFailedException
	 * @throws dbManagerInvalidHandlerException
	 * @throws dbManagerException
	 */
	public static function getInstance($inDsn = false) {
		if ( count(self::$_Instances) < 1 ) {
			if ( !$inDsn || (is_string($inDsn) && strlen($inDsn) < 1) || (is_object($inDsn) && (!$inDsn instanceof dbOptions || !$inDsn->getDsn())) ) {
				try {
					$inDsn = system::getConfig()->getDatabaseDsn()->getParamValue();
				} catch ( Exception $e ) {
					systemLog::error($e->getMessage());
					throw new dbManagerMissingDsn();
				}
			}
		} else {
			if ( !$inDsn && self::getDefaultDsn() ) {
				$inDsn = self::getDefaultDsn();
			}
		}
		
		if ( is_string($inDsn) ) {
			try {
				$inDsn = dbOptions::getInstance($inDsn);
			} catch ( Exception $e ) {
				throw new dbManagerDsnToDbOptionsParseFailed($e->getMessage());
			}
		}
		
		if ( !self::getDefaultDsn() && count(self::$_Instances) == 0 ) {
			self::setDefaultDsn($inDsn);
		}
		
		if ( $inDsn instanceof dbOptions ) {
			$instanceName = $inDsn->getDbType().$inDsn->getHost().$inDsn->getPort().$inDsn->getProtocol().$inDsn->getUser().$inDsn->getPassword().$inDsn->getDatabase();
			if ( array_key_exists($instanceName, self::$_Instances) ) {
				return self::$_Instances[$instanceName];
			}
			
			$dbType = $inDsn->getDbType();
			if ( !$dbType || $dbType === false || !array_key_exists($dbType, self::$_Drivers) ) {
				throw new dbManagerInvalidDriver($dbType, array_keys(self::$_Drivers));
			}
			
			$dbDriver = self::$_Drivers[$dbType];
			$oDbDriver = new $dbDriver($inDsn);
			
			self::$_Instances[$instanceName] = $oDbDriver;
			return $oDbDriver;
		} else {
			throw new dbManagerMissingDsn();
		}
	}
	
	/**
	 * Set the dbOptions to be the default connection
	 *
	 * @param dbOptions $oDbOptions
	 * @static 
	 */
	public static function setDefaultDsn(dbOptions $oDbOptions) {
		if ( $oDbOptions instanceof dbOptions ) {
			self::$_DefaultDsn = $oDbOptions;
		}
	}
	
	/**
	 * Returns the current default DSN options object, or false if not set
	 *
	 * @return dbOptions|false
	 * @static 
	 */
	public static function getDefaultDsn() {
		return self::$_DefaultDsn;
	}
	
	/**
	 * Removes all active instance from the container, returns number removed
	 *
	 * @return integer
	 * @static 
	 */
	public static function clearInstances() {
		$instances = 0;
		if ( count(self::$_Instances) > 0 ) {
			$instCnt = count(self::$_Instances);
			for ( $i=0; $i<$instCnt; $i++ ) {
				self::$_Instances[$i] = null;
				unset(self::$_Instances[$i]);
				$instances++;
			}
		}
		return $instances;
	}
}