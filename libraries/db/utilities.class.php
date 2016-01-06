<?php
/**
 * dbUtilities.class.php
 * 
 * Contains db specific utility implementations
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbUtilities
 * @version $Rev: 835 $
 */


/**
 * dbUtilities Class
 * 
 * dbUtilities provides a set of convenience methods for extracting database information.
 * This includes converting database tables and fields into objects for the class generator
 * as well as utility methods for listing tables and databases etc.
 * 
 * Requires specific implementation for each driver.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbUtilities
 * @abstract 
 */
abstract class dbUtilities {
	
	/**
	 * Database connection to allow querying
	 *
	 * @var dbDriver
	 */
	protected $_DbDriver				= false;
	
	
	
	/**
	 * Creates a new dbUtilities object
	 *
	 * @param dbDriver $inDbDriver
	 */
	function __construct(dbDriver $inDbDriver) {
		$this->_DbDriver = $inDbDriver;
	}
	
	
	
	/**
	 * Returns the current dbDriver
	 *
	 * @return dbDriver
	 */
	function getDbDriver() {
		return $this->_DbDriver;
	}
	
	/**
	 * Returns a new dbMapper object
	 *
	 * @return dbMapper
	 */
	function getDbMapper() {
		return new dbMapper($this);
	}
	
	/**
	 * Returns an array of all databases or the currently connected database
	 *
	 * @return array
	 * @abstract 
	 */
	abstract function showDatabases();
	
	/**
	 * Returns an array of tables in the specified database
	 *
	 * @param string $inDatabase
	 * @return array
	 * @abstract 
	 */
	abstract function showTables($inDatabase);
	
	/**
	 * Returns an array of table field definitions
	 *
	 * @param string $inDatabase
	 * @param string $inTable
	 * @return array(dbFieldDefinition)
	 * @abstract 
	 */
	abstract function getTableProperties($inDatabase, $inTable);
	
	/**
	 * Returns an array of table index definitions
	 *
	 * @param string $inDatabase
	 * @param string $inTable
	 * @return array(dbTableIndexes)
	 * @abstract 
	 */
	abstract function getTableIndexes($inDatabase, $inTable);

	/**
	 * Returns an array of table foreign key definitions
	 *
	 * @param string $inDatabase
	 * @param string $inTable
	 * @return array(dbTableForeignKeys)
	 * @abstract
	 */
	abstract function getTableForeignKeys($inDatabase, $inTable);
	
	/**
	 * Returns the database driver backup object
	 *
	 * @return dbBackup
	 */
	abstract function getDbBackup();
}