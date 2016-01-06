<?php
/**
 * dbUpdateManager.class.php
 * 
 * Provides a system for applying database updates
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbUpdateManager
 * @version $Rev: 707 $
 */


/**
 * dbUpdateManager Class
 * 
 * Provides a system for applying database updates. Updates extend the dbUpdateDefinition
 * class and add a series of SQL or method calls to be applied as updates. These individual
 * updates are aggregated by this manager class that will then apply the updates.
 * 
 * When specifying database names, be sure to use the system::getConfig()->getDatabase()
 * method. This ensures that the database can be portable.
 * 
 * Log entries are created for every action performed. Test runs are not saved (commit is
 * false) but the log information will be returned in a report for each database. This
 * can be queried either for potential issues when run in a test mode or to review the
 * update process.
 * 
 * The manager can generate an array of data representating the current update status
 * which can be displayed either on the CLI or via a web page.
 * 
 * Note: that to successfully run, the user must have permissions to modify the database
 * in question. Database creation CANNOT be handled by this system (you should not be using
 * root user!).
 * 
 * To set a new database user / password, implement this class in a calling system and 
 * then set the database details manually and re-request the database connection. This
 * will override the default connection.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbUpdateManager
 */
class dbUpdateManager extends baseSet {
	
	/**
	 * Holds the instance of dbUpdateManager
	 *
	 * @var dbUpdateManager
	 * @access private
	 * @static
	 */
	private static $_Instance;
	
	/**
	 * Holds the associative array of db update classes
	 *
	 * @var array
	 * @access private
	 * @static 
	 */
	private static $_DbUpdateClasses = array();
	
	/**
	 * Stores $_HaltOnError
	 *
	 * @var boolean
	 * @access private
	 */
	private $_HaltOnError;
	
	/**
	 * Stores $_UpdateReport
	 *
	 * @var dbUpdateReportSet
	 * @access private
	 */
	private $_UpdateReport;
	
	
	
	/**
	 * Creates a new dbUpdate manager instance
	 *
	 * @return void
	 */
	function __construct() {
		$this->reset();
		$this->setUpdateReport(new dbUpdateReportSet());
	}
	
	
	
	/**
	 * Returns a static instance of dbUpdateManager
	 *
	 * @return dbUpdateManager
	 * @static 
	 */
	static function getInstance() {
		if ( !self::$_Instance instanceof dbUpdateManager ) {
			self::$_Instance = new dbUpdateManager();
			self::$_Instance->buildDbUpdateManager();
		}
		return self::$_Instance;
	}
	
	/**
	 * Locates all database update classes in /data/dbUpdates and returns them
	 * as an associative array of classname => file location
	 *
	 * @return array
	 * @static
	 */
	static function fetchDatabaseUpdateComponents() {
		if ( !is_array(self::$_DbUpdateClasses) || count(self::$_DbUpdateClasses) == 0 ) {
			self::$_DbUpdateClasses = array();
			$files = fileObject::parseDir(system::getConfig()->getPathData().system::getDirSeparator().'dbUpdates', false);
			if ( count($files) > 0 ) {
				if ( false ) $oFile = new fileObject();
				foreach ( $files as $oFile ) {
					if ( strpos($oFile->getFilename(), '.class.php') !== false ) {
						if ( $oFile->exists() && $oFile->isReadable() ) {
							$classname = 'dbUpdate'.ucwords(str_replace('.class.php', '', $oFile->getFilename()));
							self::$_DbUpdateClasses[$classname] = $oFile->getOriginalFilename();
						} else {
							systemLog::error("Unable to read {$oFile->getOriginalFilename()}");
						}
					}
				}
			}
			systemLog::debug(print_r(self::$_DbUpdateClasses,1));
		}
		return self::$_DbUpdateClasses;
	}
	
	
	
	/**
	 * Resets object to defaults
	 *
	 * @return void
	 */
	function reset() {
		$this->_HaltOnError = true;
		$this->_UpdateReport = null;
		$this->_resetSet();
	}

	/**
	 * Returns an instance of the database update manager with all update classes assigned
	 *
	 * @return dbUpdateManager
	 * @throws dbException
	 */
	protected function buildDbUpdateManager() {
		try {
			foreach ( self::fetchDatabaseUpdateComponents() as $class => $file ) {
				@include_once($file);
				$this->addUpdate(new $class());
			}
		} catch ( Exception $e ) {
			throw new dbException("Fatal Error! Cannot continue!\n".$e->getMessage()."\n\nException Trace:\n".$e->getTraceAsString()."\n");
		}
	}
	
	/**
	 * Returns true if databases are up-to-date, false if any require an update
	 *
	 * @return boolean
	 */
	function isUpToDate() {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oUpdate = new dbUpdateDefinition();
			foreach ( $this as $oUpdate ) {
				if ( !$oUpdate->isUpToDate() ) {
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * Returns an array detailing information about each database update and the current state
	 *
	 * @return array
	 */
	function getUpdateStatus() {
		$return = array();
		if ( $this->getCount() ) {
			if ( false ) $oUpdate = new dbUpdateDefinition();
			foreach ( $this as $oUpdate ) {
				$return[] = array(
					'Database' => $oUpdate->getDbName(),
					'UpToDate' => ($oUpdate->isUpToDate() ? 'Yes':'No'),
					'Ver' => $oUpdate->getVersion(),
					'Latest' => $oUpdate->getCount(),
					'Updates' => $oUpdate->getUpdateCount(),
					'LastRun' => $oUpdate->getUpdateDate(),
					'LastResult' => $oUpdate->getLastUpdateLogEntry()->getUpdateResult()
				);
			}
		}
		return $return;
	}
	
	/**
	 * Applies any outstanding updates to the database(s), generates a report accessible via {@link dbUpdateManager::getUpdateReport()}
	 *
	 * @param boolean $inCommit
	 * @return boolean
	 */
	function update($inCommit = false) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oUpdate = new dbUpdateDefinition();
			foreach ( $this as $oUpdate ) {
				$oReport = new dbUpdateReport($oUpdate->getDbName());
				$oUpdate->update($inCommit, $this->getHaltOnError(), $oReport);
				$this->getUpdateReport()->addReport($oReport);
			}
			if ( !$this->getUpdateReport()->hasError() ) {
				return true;
			}
		}
		return false;
	}
	
	
	
	/**
	 * Returns $_HaltOnError
	 *
	 * @return boolean
	 * @access public
	 */
	function getHaltOnError() {
		return $this->_HaltOnError;
	}
	
	/**
	 * Set $_HaltOnError to $inHaltOnError
	 *
	 * @param boolean $inHaltOnError
	 * @return dbUpdateManager
	 * @access public
	 */
	function setHaltOnError($inHaltOnError) {
		if ( $this->_HaltOnError !== $inHaltOnError ) {
			$this->_HaltOnError = $inHaltOnError;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_UpdateReport
	 *
	 * @return dbUpdateReportSet
	 * @access public
	 */
	function getUpdateReport() {
		return $this->_UpdateReport;
	}
	
	/**
	 * Set $_UpdateReport to $inUpdateReport
	 *
	 * @param dbUpdateReportSet $inUpdateReport
	 * @return dbUpdateManager
	 * @access public
	 */
	function setUpdateReport($inUpdateReport) {
		if ( $this->_UpdateReport !== $inUpdateReport ) {
			$this->_UpdateReport = $inUpdateReport;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Adds a new database definition to apply updates to
	 *
	 * @param dbUpdateDefinition $inDefinition
	 * @return dbUpdateManager 
	 */
	function addUpdate(dbUpdateDefinition $inDefinition) {
		return $this->_setValue($inDefinition);
	}
	
	/**
	 * Removes the definition from the set
	 *
	 * @param dbUpdateDefinition $inDefinition
	 * @return dbUpdateManager
	 */
	function removeUpdate(dbUpdateDefinition $inDefinition) {
		return $this->_removeItemWithValue($inDefinition);
	}
	
	/**
	 * Returns the update by $inKey
	 *
	 * @param integer $inKey
	 * @return dbUpdateDefinition
	 */
	function getUpdate($inKey) {
		return $this->_getItem($inKey);
	}
	
	/**
	 * Returns the update definition object for $inDatabase, false on failure
	 *
	 * @param string $inDatabase
	 * @return dbUpdateDefinition
	 */
	function getUpdateByDatabaseName($inDatabase) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oUpdate = new dbUpdateDefinition();
			foreach ( $this as $oUpdate ) {
				if ( $oUpdate->getDbName() == $inDatabase ) {
					return $oUpdate;
				}
			}
		}
		return false;
	}
	
	/**
	 * Returns the item count
	 *
	 * @return integer
	 */
	function getCount() {
		return $this->_itemCount();
	}
}