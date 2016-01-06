<?php
/**
 * dbDriverMySqlBackup.class.php
 * 
 * Contains db specific backup implementations
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbDriverMySqlBackup
 * @version $Rev: 650 $
 */


/**
 * dbDriverMySqlBackup Class
 * 
 * Provides MySQL customised extensions for the dbBackup system.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbDriverMySqlBackup
 */
class dbDriverMySqlBackup extends dbBackup {
	
	/**
	 * Mysql specfic option: aggregates specified databases into one single sql dump file, default false
	 *
	 * @var boolean
	 */
	const OPTION_MYSQL_AGGREGATE_DATABASES = 'db.mysql.aggregate';
	/**
	 * Mysql specific option: the flags to use with mysqldump, default --opt
	 *
	 * @var string
	 */
	const OPTION_MYSQL_OPTIONS = 'db.mysql.options';
	
	

	/**
	 * Performs adaptor specific validation
	 *
	 * @return void
	 * @throws dbException
	 */
	protected function _validateOptions() {
		
	}
	
	/**
	 * Backs up MySQL databases to files, uses mysqldump to do the work
	 *
	 * @return boolean
	 * @throws dbException
	 */
	function backup() {
		$this->validateOptions();
		$backupLocation = $this->getBackupLocation();
		
		if ( $this->getOptions(self::OPTION_MYSQL_AGGREGATE_DATABASES) ) {
			$dbs = array(implode(' ', $this->getDatabases()));
		} else {
			$dbs = $this->getDatabases();
		}
		
		foreach ( $dbs as $database ) {
			$bkfile = $backupLocation.system::getDirSeparator().str_replace(' ', '_', $database);
			if ( $this->getOptions(self::OPTION_OUTPUT_USE_DATE) ) {
				$bkfile .= '_'.date('Y-m-d_Hi');
			}
			$bkfile .= '.sql';
			
			if ( $this->getOptions(self::OPTION_MYSQL_OPTIONS) ) {
				$options = $this->getOptions(self::OPTION_MYSQL_OPTIONS);
			} else {
				$options = '--opt';
			}
			
			$proc = 
				'mysqldump '.$options.' --databases '.$database.
				' -u '.escapeshellarg(system::getConfig()->getDatabaseUserScript()->getParamValue()).
				' --password='.escapeshellarg(system::getConfig()->getDatabasePassword()->getParamValue()).' > '.escapeshellarg($bkfile);
			
			systemLog::message('Running: '.preg_replace(array('/ -u (\S)* /i', '/ --password\=(\S)* /i'), array(' ', ' '), $proc));
			`$proc`;
			
			$bkfile = $this->compressFile($bkfile);
			
			chmod($bkfile, $this->getOptions(self::OPTION_OUTPUT_FILE_PERMISSIONS));
		}
		return true;
	}
}