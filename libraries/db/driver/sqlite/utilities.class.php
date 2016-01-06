<?php
/**
 * dbDriverSqliteUtilities.class.php
 * 
 * Contains db specific utility implementations
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbDriverSqliteUtilities
 * @version $Rev: 835 $
 */


/**
 * dbDriverSqliteUtilities Class
 * 
 * Provides SQLite customised extensions for the dbUtilities system.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbDriverSqliteUtilities
 */
class dbDriverSqliteUtilities extends dbUtilities {
	
	/**
	 * @see dbUtilities::showDatabases()
	 */
	function showDatabases() {
		throw new dbUtilitiesException('SQLite does not support multiple databases');
	}
	
	/*
	 * For SQLite data we need to query the sqlite_master table that is always
	 * created inside an SQLite database file. The structure is:
	 * 
	 * CREATE TABLE sqlite_master (
	 *   type TEXT,
	 *   name TEXT,
	 *   tbl_name TEXT,
	 *   rootpage INTEGER,
	 *   sql TEXT
	 * );
	 * 
	 * Type is one of: table, index
	 */
	
	/**
	 * @see dbUtilities::showTables()
	 */
	function showTables($inDatabase) {
		$results = array();
		try {
			$oResults = $this->getDbDriver()->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
			foreach ( $oResults as $row ) {
				$results[] = $row['name'];
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
	 * Borrowed from Creole package
	 * @author    Hans Lellelid <hans@xmpl.org>
	 * @version   $Revision: 835 $
	 */
	function getTableProperties($inDatabase, $inTable) {
		$results = array();
		try {
			$oRes = $this->getDbDriver()->query('PRAGMA table_info('.$inTable.')');
			foreach ( $oRes as $row ) {
				$oDef = new dbMapperFieldDefinition();
				$oDef->setField($row['name']);
	            
				/*
				 * Taken from Creole::SQLiteTableInfo class
				 */
	            $matches = array();
	            if (preg_match('/^([^\(]+)\(\s*(\d+)\s*,\s*(\d+)\s*\)$/', $row['type'], $matches)) {
	                $nativeType = $matches[1];
	                $oDef->setSize($matches[2]);
	                $oDef->setPrecision($matches[3]);    
	            } elseif (preg_match('/^([^\(]+)\(\s*(\d+)\s*\)$/', $row['type'], $matches)) {
	                $nativeType = $matches[1];
	                $oDef->setSize($matches[2]);
	            } else {
	                $nativeType = $row['type'];
	            }
	            
				// set default value
				$default = $row['dflt_value'];
				if ( empty($default) ) {
					if ( !$row['notnull'] ) {
						$default = 'null';
					} else {
						$default = dbDriverSqliteTypes::getDefaultPhpValue($nativeType);
					}
				}
	            
	            $oDef->setType($nativeType);
				$oDef->setIsNull((!$row['notnull'] ? true : false));
				$oDef->setIsPrimaryKey(($row['pk'] == 1 || (strtolower($row['type']) == 'integer primary key') ? true : false));
				$oDef->setDefault($default);
				$oDef->setPhpType(dbDriverSqliteTypes::getPhpName(dbDriverMySqlTypes::getPhpType($nativeType)));
	            $results[$row['name']] = $oDef;
			}
			return $results;
		} catch ( Exception $e ) {
			throw $e;
		}
		return $results;
	}
	
	/**
	 * @see dbUtilities::getTableIndexes()
	 * 
	 * Borrowed from Creole package
	 * @author    Hans Lellelid <hans@xmpl.org>
	 * @version   $Revision: 835 $
	 */
	function getTableIndexes($inDatabase, $inTable) {
		$results = array();
		try {
			$oIndexList = $this->getDbDriver()->query('PRAGMA index_list('.$inTable.')');
			foreach ( $oIndexList as $index ) {
				$oFieldList = $this->getDbDriver()->query('PRAGMA index_info('.$index['name'].')');
				foreach ( $oFieldList as $field ) {
					$oDef = new dbMapperIndexDefinition();
					$oDef->setTable($inTable);
					$oDef->setKeyname($index['name']);
					$oDef->setSequenceInIndex($field['seqno']);
					$oDef->setColumnName($field['name']);
					$oDef->setUnique(($index['unique'] == 1 ? 1 : 0));
					$results[] = $oDef;
				}
			}
			return $results;
		} catch ( Exception $e ) {
			throw $e;
		}
		return $results;
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
		return new dbBackup($this->getDbDriver());
	}
}