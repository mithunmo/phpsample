<?php
/**
 * dbDriverSqlite.class.php
 * 
 * Contains management system for database connections
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbDriverSqlite
 * @version $Rev: 650 $
 */


/**
 * dbDriverSqlite Class
 * 
 * Provides SQLite customised extensions for the dbDriver system.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbDriverSqlite
 */
class dbDriverSqlite extends dbDriver {
	
	/**
	 * @see dbDriver::$_DriverName
	 */
	protected $_DriverName				= 'dbDriverSqlite';
	
	
	
	/**
	 * @see dbDriver::__construct()
	 */
	function __construct(dbOptions $dbOptions) {
		if ( !$dbOptions->getDatabase() ) {
			throw new dbDriverMissingDbOption('database');
		}
		if ( !$dbOptions->getParam(dbOptions::PARAM_DB_SQLITE_VERSION) ) {
			throw new dbDriverMissingDbOption('sqlite version');
		}
		switch ( $dbOptions->getParam(dbOptions::PARAM_DB_SQLITE_VERSION) ) {
			case 2:  $dsn = 'sqlite2'; break;
			default: $dsn = 'sqlite';
		}
		$dsn .= ':'.$dbOptions->getDatabase();
		$dbOptions->setDbDsn($dsn);
		
		parent::__construct($dbOptions);
	}
	
	/**
	 * @see dbDriver::getDbUtilities()
	 */
	function getDbUtilities() {
		if ( !$this->_DbUtilities instanceof dbUtilities ) {
			$this->_DbUtilities = new dbDriverSqliteUtilities($this);
		}
		return $this->_DbUtilities;
	}
}