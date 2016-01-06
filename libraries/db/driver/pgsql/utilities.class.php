<?php
/**
 * dbDriverPgSqlUtilities.class.php
 * 
 * Contains db specific utility implementations
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbDriverPgSqlUtilities
 * @version $Rev: 835 $
 */


/**
 * dbDriverPgSqlUtilities Class
 * 
 * Provides PgSQL customised extensions for the dbUtilities system.
 * 
 * Bits of this implementation are taken from multiple packages including
 * ezComponents and Creole. Unlike other implementations this one requires
 * at least version 8 or more of Postgres so that there is information_schema
 * available.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbDriverPgSqlUtilities
 */
class dbDriverPgSqlUtilities extends dbUtilities {
	
	/**
	 * @see dbUtilities::showDatabases()
	 */
	function showDatabases() {
		$results = array();
		try {
			$oResults = $this->getDbDriver()->query("SELECT datname AS Database FROM pg_catalog.pg_database ORDER BY datname");
			foreach ( $oResults as $row ) {
				$results[] = $row['Database'];
			}
			return $results;
		} catch ( Exception $e ) {
			throw $e;
		}
		return $results;
	}
	
	/**
	 * @see dbUtilities::showTables()
	 */
	function showTables($inDatabase) {
		$results = array();
		try {
			$oResults = $this->getDbDriver()->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
			foreach ( $oResults as $row ) {
				$results[] = $row['table_name'];
			}
			return $results;
		} catch ( Exception $e ) {
			throw $e;
		}
		return $results;
	}
	
	/**
	 * @see dbUtilities::getTableProperties()
	 * 
	 * @todo DR: complete Postgres DB utilities methods
	 */
	function getTableProperties($inDatabase, $inTable) {
		$results = array();
		try {
			$oRes = $this->getDbDriver()->query("
				SELECT a.attnum, a.attname AS field, t.typname AS type,
				       format_type(a.atttypid, a.atttypmod) AS fulltype,
				       (
				         SELECT substring(d.adsrc for 128) FROM pg_catalog.pg_attrdef d 
				         WHERE d.adrelid = a.attrelid AND d.adnum = a.attnum AND a.atthasdef
				       ) AS default,
				       a.attlen AS length, a.atttypmod AS lengthvar, a.attnotnull AS notnull
				  FROM pg_class c, pg_attribute a, pg_type t 
				 WHERE c.relname = '$inTable' AND a.attnum > 0 AND a.attrelid = c.oid AND a.atttypid = t.oid 
				 ORDER BY a.attnum");
			
			throw new dbUtilitiesException('PgSQL Utilities not implemented yet');

		} catch ( Exception $e ) {
			throw $e;
		}
		
		return $results;
	}
	
	/**
	 * @see dbUtilities::getTableIndexes()
	 */
	function getTableIndexes($inDatabase, $inTable) {
		throw new dbUtilitiesException('PgSQL Utilities not implemented yet');
	}

	/**
	 * @see dbUtilities::getTableForeignKeys()
	 *
	 * @param string $inDatabase
	 * @param string $inTable
	 * @return array
	 */
	function getTableForeignKeys($inDatabase, $inTable) {
		return array();
	}

	/**
	 * @see dbUtilities::getDbBackup()
	 */
	function getDbBackup() {
		throw new dbUtilitiesException('dbBackup has not been implemented for the Postgres driver');
	}
}