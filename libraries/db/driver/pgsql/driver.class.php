<?php
/**
 * dbDriverPgSql.class.php
 * 
 * Contains management system for database connections
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbDriverPgSql
 * @version $Rev: 650 $
 */


/**
 * dbDriverPgSql Class
 * 
 * Provides PgSQL customised extensions for the dbDriver system.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbDriverPgSql
 */
class dbDriverPgSql extends dbDriver {
	
	/**
	 * @see dbDriver::$_DriverName
	 */
	protected $_DriverName				= 'dbDriverPgSql';
	
	
	
	/**
	 * @see dbDriver::__construct()
	 */
	function __construct(dbOptions $dbOptions) {
		if ( !$dbOptions->getDatabase() ) {
			throw new dbDriverMissingDbOption('database');
		}
		
		$dsn = 'pgsql:dbname='.$dbOptions->getDatabase();
		if ( $dbOptions->getHost() ) {
			$dsn .= ' host='.$dbOptions->getHost();
		}
		if ( $dbOptions->getPort()) {
			$dsn .= ' port='.$dbOptions->getPort();
		}
		
		$dbOptions->setDbDsn($dsn);
		parent::__construct($dbOptions);
	}
	
	/**
	 * @see dbDriver::getDbUtilities()
	 */
	function getDbUtilities() {
		if ( !$this->_DbUtilities instanceof dbUtilities ) {
			$this->_DbUtilities = new dbDriverPgSqlUtilities($this);
		}
		return $this->_DbUtilities;
	}
}