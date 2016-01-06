<?php
/**
 * dbBackup.class.php
 * 
 * Contains db specific backup implementations
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbBackup
 * @version $Rev: 707 $
 */


/**
 * dbBackup Class
 * 
 * dbBackup provides a driver specific backup mechanism that by default is a
 * simple file copy utility (for SQLite database files). Depending on the
 * database driver, this basic class will need extending to provide that
 * additional functionality.
 * 
 * dbBackup requires the dbDriver instance be passed to it during creation.
 * 
 * There are several options to control the default behaviour of the backup:
 * 
 * <b>dbBackup::OPTION_DB_FILE_LOCATION</b>
 * Option for file location, either physical location or optional host address
 * 
 * <b>dbBackup::OPTION_DB_FILE_EXTENSION</b>
 * Option for the file extension the database files have been saved as, default db
 * 
 * <b>dbBackup::OPTION_OUTPUT_LOCATION</b>
 * Option for where output files will be stored, defaults to /data/backup/db
 *
 * <b>dbBackup::OPTION_OUTPUT_FILE_PERMISSIONS</b>
 * Option to set permissions on generated output files, default 0400, read by creator only
 *
 * <b>dbBackup::OPTION_OUTPUT_USE_DATE</b>
 * Option to use date in filename or folder name (if using subfolders), default false
 *
 * <b>dbBackup::OPTION_OUTPUT_USE_SUBFOLDERS</b>
 * Option to use sub-folders for backup, will use date as sub-folder, default false
 *
 * <b>dbBackup::OPTION_OUTPUT_COMPRESS_FILES</b>
 * Option to compress backup files to save space will try to use ZIP/GZIP if available, default false
 * 
 * Note: if files are larger than 4GB, this optional will not work as ZIP is not capable of handling
 * files larger than this. You must use 7zip and 7z archives instead. Recommended to NOT use this
 * option as it is highly system dependent and using PHP for this task is very intensive.
 * 
 * <b>dbBackup::OPTION_OUTPUT_COMPRESS_IN_BG</b>
 * Option to compress in background possibly creating many threads compressing large files
 * 
 * If you have large database backups i.e. anything more than ~500MB you should NOT use
 * this option. COMPRESS_INBG launches the compress as a background task, immediately
 * returning to the calling PHP script which will carry on processing your database files
 * regardless of available system resources.
 * 
 * <code>
 * // a very basic SQLite example
 * $oBackup = new dbBackup(dbManager::getInstance('sqlite3:///test.db'));
 * $oBackup->setOptions(
 *     array(
 *         // set options here
 *         dbBackup::OPTION_DB_FILE_EXTENSION => 'db',
 *     )
 * );
 * $oBackup->addDatabase('test');
 * $oBackup->backup();
 * </code>
 * 
 * @package scorpio
 * @subpackage db
 * @category dbBackup
 */
class dbBackup {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_DbDriver
	 *
	 * @var dbDriver
	 * @access protected
	 */
	protected $_DbDriver;
	
	/**
	 * Stores $_BackupLocation
	 *
	 * @var string
	 * @access protected
	 */
	protected $_BackupLocation;
	
	/**
	 * Stores $_Databases
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Databases;
	
	/**
	 * Stores $_Options
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Options;
	
	/**
	 * Option for file location, either physical location or optional host address
	 *
	 * @var string
	 */
	const OPTION_DB_FILE_LOCATION = 'db.file.location';
	/**
	 * Option for the file extension the database files have been saved as, default db
	 *
	 * @var string
	 */
	const OPTION_DB_FILE_EXTENSION = 'db.file.extension';
	/**
	 * Option for where output files will be stored, defaults to /data/backup/db
	 *
	 * @var string
	 */
	const OPTION_OUTPUT_LOCATION = 'db.output.location';
	/**
	 * Option to set permissions on generated output files, default 0400, read by creator only
	 *
	 * @var integer (octal)
	 */
	const OPTION_OUTPUT_FILE_PERMISSIONS = 'db.output.permissions';
	/**
	 * Option to use date in filename or folder name (if using subfolders), default false
	 *
	 * @var boolean
	 */
	const OPTION_OUTPUT_USE_DATE = 'db.output.usedate';
	/**
	 * Option to use sub-folders for backup, will use date as sub-folder, default false
	 *
	 * @var boolean
	 */
	const OPTION_OUTPUT_USE_SUBFOLDERS = 'db.output.subfolders';
	/**
	 * Option to compress backup files to save space will try to use ZIP/GZIP if available, default false
	 * 
	 * Note: if files are larger than 4GB, this optional will not work as ZIP is not capable of handling
	 * files larger than this. You must use 7zip and 7z archives instead. Recommended to NOT use this
	 * option as it is highly system dependent and using PHP for this task is very intensive.
	 * 
	 * @var boolean
	 */
	const OPTION_OUTPUT_COMPRESS_FILES = 'db.output.compress';
	/**
	 * Option to compress in background possibly creating many threads compressing large files
	 * 
	 * If you have large database backups i.e. anything more than ~500MB you should NOT use
	 * this option. COMPRESS_INBG launches the compress as a background task, immediately
	 * returning to the calling PHP script which will carry on processing your database files
	 * regardless of available system resources.
	 *
	 * @var boolean
	 */
	const OPTION_OUTPUT_COMPRESS_IN_BG = 'db.output.compress.inbg';
	
	
	
	/**
	 * Returns a new instance of dbBackup
	 *
	 * @param dbDriver $inDbDriver
	 */
	function __construct(dbDriver $inDbDriver) {
		$this->reset();
		$this->setDbDriver($inDbDriver);
	}
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_DbDriver = null;
		$this->_BackupLocation = null;
		$this->_Databases = array();
		$this->_Options = array(
			self::OPTION_DB_FILE_LOCATION => system::getConfig()->getPathData().system::getDirSeparator().'db',
			self::OPTION_DB_FILE_EXTENSION => 'db',
			self::OPTION_OUTPUT_COMPRESS_FILES => false,
			self::OPTION_OUTPUT_COMPRESS_IN_BG => false,
			self::OPTION_OUTPUT_FILE_PERMISSIONS => 0400,
			self::OPTION_OUTPUT_LOCATION =>  system::getConfig()->getPathData().system::getDirSeparator().'backup'.system::getDirSeparator().'db',
			self::OPTION_OUTPUT_USE_DATE => false,
			self::OPTION_OUTPUT_USE_SUBFOLDERS => false,
		);
	}
	
	/**
	 * Performs the backup, returning true on success, throws exception on failure
	 *
	 * @return boolean
	 * @throws dbException
	 */
	function backup() {
		$this->validateOptions();
		
		$dbLocation = $this->getOptions(self::OPTION_DB_FILE_LOCATION);
		$backupLocation = $this->getBackupLocation();
		
		foreach ( $this->_Databases as $database ) {
			$dbfile = $dbLocation.system::getDirSeparator().$database.'.'.$this->getOptions(self::OPTION_DB_FILE_EXTENSION);
			$bkfile = $backupLocation.system::getDirSeparator().$database;
			if ( $this->getOptions(self::OPTION_OUTPUT_USE_DATE) ) {
				$bkfile .= date('Y-m-d_Hi');
			}
			$bkfile .= '.'.$this->getOptions(self::OPTION_DB_FILE_EXTENSION);
			
			if ( file_exists($dbfile) ) {
				systemLog::message("Copying db file to backup location");
				if ( copy($dbfile, $bkfile) ) {
					systemLog::message("Successfully copied $dbfile to $bkfile");
					$backupLocation = $this->compressFile($backupLocation);
					
					chmod($backupLocation, $this->getOptions(self::OPTION_OUTPUT_FILE_PERMISSIONS));
				} else {
					throw new dbException("Failed to copy file $dbfile");
				}
			}
		}
		return true;
	}
	
	/**
	 * Checks the basic options and throws exceptions if there are issues
	 *
	 * @return void
	 * @throws dbException
	 */
	function validateOptions() {
		if ( count($this->getDatabases()) == 0 ) {
			throw new dbException('No databases specified for processing');
		}
		$this->_validateOptions();
	}
	
	/**
	 * Performs adaptor specific validation
	 *
	 * @return void
	 * @throws dbException
	 */
	protected function _validateOptions() {
		$dbLocation = $this->getOptions(self::OPTION_DB_FILE_LOCATION);
		if ( !file_exists($dbLocation) || !is_readable($dbLocation) || !is_dir($dbLocation) ) {
			throw new dbException("DB location ($dbLocation) is not readable, does not exist or is not a directory");
		}
	}
	
	/**
	 * Attempts to run a compression program on $inFile checks for gzip, zip and 7za (7zip)
	 * Returns the name of the compressed file
	 *
	 * @param string $inFile
	 * @return string
	 */
	function compressFile($inFile) {
		if ( strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && $this->getOptions(self::OPTION_OUTPUT_COMPRESS_FILES) ) {
			$command = false;
			$extn = '';
			
			$res = explode("\n", `which 7za`);
			if ( !$command && is_array($res) && isset($res[0]) && strlen($res[0]) > 5 && stripos($res[0], 'which: no ') === false ) {
				$command = trim($res[0]).' -a '.$inFile.'.7z'.' '.$inFile.' && rm '.$inFile;
				$extn = '.7z';
			}
			$res = explode("\n", `which gzip`);
			if ( !$command && is_array($res) && isset($res[0]) && strlen($res[0]) > 5 && stripos($res[0], 'which: no ') === false ) {
				$command = trim($res[0]).' '.$inFile;
				$extn = '.gz';
			}
			$res = explode("\n", `which zip`);
			if ( !$command && is_array($res) && isset($res[0]) && strlen($res[0]) > 5 && stripos($res[0], 'which: no ') === false ) {
				$command = trim($res[0]).' '.$inFile.'.zip'.' '.$inFile.' && rm '.$inFile;
				$extn = '.zip';
			}
			
			if ( $command ) {
				$proc = "$command 2>&1 /dev/null";
				if ( $this->getOptions(self::OPTION_OUTPUT_COMPRESS_IN_BG) ) {
					$proc .= " & ";
				}
				systemLog::message("Executing: $proc");
				`$proc`;
				
				return $inFile.$extn;
			} else {
				systemLog::error('No compression program on command line (which gzip|zip|7za)');
			}
		}
		return $inFile;
	}
	
	
	
	/**
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return dbBackup
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns $_DbDriver
	 *
	 * @return dbDriver
	 */
	function getDbDriver() {
		return $this->_DbDriver;
	}
	
	/**
	 * Set $_DbDriver to $inDbDriver
	 *
	 * @param dbDriver $inDbDriver
	 * @return dbBackup
	 */
	function setDbDriver(dbDriver $inDbDriver) {
		if ( $inDbDriver !== $this->_DbDriver ) {
			$this->_DbDriver = $inDbDriver;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_BackupLocation
	 *
	 * @return string
	 */
	function getBackupLocation() {
		if ( !$this->_BackupLocation ) {
			$this->_BackupLocation = $this->getOptions(self::OPTION_OUTPUT_LOCATION);
		}
		
		if ( !file_exists($this->_BackupLocation) || !is_readable($this->_BackupLocation) || !is_dir($this->_BackupLocation) ) {
			systemLog::error("Backup location ({$this->_BackupLocation}) is not readable, does not exist or is not a directory, creating");
		}
		if ( $this->getOptions(self::OPTION_OUTPUT_USE_SUBFOLDERS) ) {
			$this->_BackupLocation .= system::getDirSeparator().date('Y-m-d');
		}
		if ( !file_exists($this->_BackupLocation) ) {
			systemLog::message("Creating {$this->_BackupLocation}");
			mkdir($this->_BackupLocation, 0700, true);
		}
		
		return $this->_BackupLocation;
	}
	
	/**
	 * Set a specific backup location, if not set the default option will be used instead
	 *
	 * @param string $inBackupLocation
	 * @return dbBackup
	 */
	function setBackupLocation($inBackupLocation) {
		if ( $inBackupLocation !== $this->_BackupLocation ) {
			$this->_BackupLocation = $inBackupLocation;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Databases
	 *
	 * @return array
	 */
	function getDatabases() {
		return $this->_Databases;
	}
	
	/**
	 * Set $_Databases to $inDatabases
	 *
	 * @param array $inDatabases
	 * @return dbBackup
	 */
	function setDatabases(array $inDatabases = array()) {
		if ( count($inDatabases) > 0 ) {
			foreach ( $inDatabases as $db ) {
				if ( !in_array($inDatabases, $this->_Databases) ) {
					$this->_Databases[] = $db;
					$this->setModified();
				}
			}
		}
		return $this;
	}

	/**
	 * Returns the option value of $inOption, null if not set or if $inOption is null all options
	 *
	 * @return mixed
	 */
	function getOptions($inOption = null) {
		if ( $inOption !== null ) {
			if ( array_key_exists($inOption, $this->_Options) ) {
				return $this->_Options[$inOption];
			} else {
				return null;
			}
		}
		return $this->_Options;
	}
	
	/**
	 * Set the options to be used during database backup. There may be additional
	 * driver specific options beyond the defaults in dbBackup
	 * 
	 * <code>
	 * $oBackup = new dbBackup($inDriver);
	 * $oBackup->setOptions(
	 *     array(
	 *         dbBackup::OPTION_OUTPUT_COMPRESS_FILES => true,
	 *         dbBackup::OPTION_OUTPUT_COMPRESS_IN_BG => false,
	 *         dbBackup::OPTION_OUTPUT_FILE_PERMISSIONS => 0600,
	 *         dbBackup::OPTION_OUTPUT_LOCATION =>  '/mnt/backup/db',
	 *         dbBackup::OPTION_OUTPUT_USE_DATE => true,
	 *         dbBackup::OPTION_OUTPUT_USE_SUBFOLDERS => true,
	 *     )
	 * );
	 * $oBackup->backup();
	 * </code>
	 *
	 * @param array $inOptions
	 * @return dbBackup
	 */
	function setOptions(array $inOptions = array()) {
		if ( count($inOptions) > 0 ) {
			foreach ( $inOptions as $key => $option ) {
				if ( (isset($this->_Options[$key]) && ($this->_Options[$key] != $option)) || !isset($this->_Options[$key]) ) {
					$this->_Options[$key] = $option;
					$this->setModified();
				}
			}
		}
		return $this;
	}
}