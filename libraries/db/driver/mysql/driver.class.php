<?php
/**
 * dbDriverMySql.class.php
 * 
 * Contains management system for database connections
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbDriverMySql
 * @version $Rev: 650 $
 */


/**
 * dbDriverMySql Class
 * 
 * Provides MySQL customised extensions for the dbDriver system.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbDriverMySql
 */
class dbDriverMySql extends dbDriver {
	
	/**
	 * @see dbDriver::$_DriverName
	 */
	protected $_DriverName				= 'dbDriverMySql';
	/**
	 * @see dbDriver::$_IdentifierQuotes
	 */
	protected $_IdentifierQuotes = array(
		"start" => '`',
		"end"   => '`'
	);
	
	
	
	/**
	 * @see dbDriver::__construct()
	 */
	function __construct(dbOptions $dbOptions) {
		if ( !$dbOptions->getDatabase() ) {
			throw new dbDriverMissingDbOption('database');
		}
		
		$dsn = 'mysql:dbname='.$dbOptions->getDatabase();
		if ( $dbOptions->getHost() ) {
			$dsn .= ';host='.$dbOptions->getHost();
		}
		if ( $dbOptions->getPort()) {
			$dsn .= ';port='.$dbOptions->getPort();
		}
		if ( $dbOptions->getParam('charset') ) {
			$dsn .= ';charset='.$dbOptions->getParam('charset');
		}
		if ( $dbOptions->getSocket() ) {
			$dsn .= ';unix_socket='.$dbOptions->getSocket();
		}
		
		$dbOptions->setDbDsn($dsn);
		parent::__construct($dbOptions);
		
		/*
		 * Enable buffered queries
		 */
		$this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		/*
		 * Enable UTF-8 connection mode
		 * Bug fix for PHP5.3.0 with mysqlnd not setting PDO::MYSQL_ATTR constants
		 * Fixed in PHP 5.3.1
		 */
		$this->exec('SET NAMES utf8');
	}
	
	/**
	 * @see dbDriver::getDbUtilities()
	 */
	function getDbUtilities() {
		if ( !$this->_DbUtilities instanceof dbUtilities ) {
			$this->_DbUtilities = new dbDriverMySqlUtilities($this);
		}
		return $this->_DbUtilities;
	}
}