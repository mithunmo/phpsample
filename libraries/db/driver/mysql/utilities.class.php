<?php
/**
 * dbDriverMySqlUtilities.class.php
 * 
 * Contains db specific utility implementations
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbDriverMySqlUtilities
 * @version $Rev: 835 $
 */


/**
 * dbDriverMySqlUtilities Class
 * 
 * Provides MySQL customised extensions for the dbUtilities system.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbDriverMySqlUtilities
 */
class dbDriverMySqlUtilities extends dbUtilities {
	
	/**
	 * @see dbUtilities::showDatabases()
	 */
	function showDatabases() {
		$results = array();

		$oResults = $this->getDbDriver()->query("SHOW DATABASES");
		foreach ( $oResults as $row ) {
			$results[] = $row['Database'];
		}
		
		return $results;
	}
	
	/**
	 * @see dbUtilities::showTables()
	 */
	function showTables($inDatabase) {
		$results = array();

		$this->getDbDriver()->exec("USE ".$this->getDbDriver()->quoteIdentifier($inDatabase));
		
		$oResults = $this->getDbDriver()->query("SHOW TABLES");
		foreach ( $oResults as $row ) {
			$results[] = $row['Tables_in_'.$inDatabase];
		}

		return $results;
	}
	
	/**
	 * @see dbUtilities::getTableProperties()
	 * 
	 * Portions borrowed from Creole
	 * @author    Hans Lellelid <hans@xmpl.org>
	 * @version   $Revision: 835 $
	 */
	function getTableProperties($inDatabase, $inTable) {
		$results = array();

		$oRes = $this->getDbDriver()
			->query(
				"SHOW COLUMNS FROM ".$this->getDbDriver()->quoteIdentifier($inDatabase).'.'.$this->getDbDriver()->quoteIdentifier($inTable)
			);

		foreach ( $oRes as $row ) {
			$oDef = new dbMapperFieldDefinition();
			$oDef->setField($row['Field']);

			$matches = array();
			if (preg_match('/^(\w+)[\(]?([\d,]*)[\)]?( |$)/', $row['Type'], $matches)) {
				// taken from Creole MySQL layer
				// colname[1] size/precision[2]
				$nativeType = $matches[1];
				if ($matches[2]) {
					if ( ($cpos = strpos($matches[2], ',')) !== false) {
						$oDef->setSize((int) substr($matches[2], 0, $cpos));
						$oDef->setPrecision((int) substr($matches[2], $cpos + 1));
					} else {
						$oDef->setSize((int) $matches[2]);
					}
				}
			} elseif (preg_match('/^(\w+)\(/', $row['Type'], $matches)) {
				$nativeType = $matches[1];
				// checks for enum style fields with values
				if ( strpos($row['Type'], '\'') !== false ) {
					$oDef->setValues(explode(",", str_ireplace(array($nativeType,'(',')','\''), '', $row['Type'])));
				}
			} else {
				$nativeType = $row['Type'];
			}

			// set default value
			$default = $row['Default'];
			if ( empty($default) || $default == 'CURRENT_TIMESTAMP' ) {
				if ( strtoupper($row['Null']) == 'YES' ) {
					$default = 'null';
				} else {
					$default = dbDriverMySqlTypes::getDefaultPhpValue($nativeType);
				}
			}

			$oDef->setType($nativeType);
			$oDef->setIsNull((strtoupper($row['Null']) == 'YES' ? true : false));
			$oDef->setIsPrimaryKey((strtoupper($row['Key']) == 'PRI' ? true : false));
			$oDef->setKey($row['Key']);
			$oDef->setDefault($default);
			$oDef->setExtra($row['Extra']);
			$oDef->setPhpType(dbDriverMySqlTypes::getPhpName(dbDriverMySqlTypes::getPhpType($nativeType)));
			$results[$row['Field']] = $oDef;
		}

		return $results;
	}
	
	/**
	 * @see dbUtilities::getTableIndexes()
	 */
	function getTableIndexes($inDatabase, $inTable) {
		$results = array();

		$oRes = $this->getDbDriver()
			->query(
				"SHOW INDEXES FROM ".$this->getDbDriver()->quoteIdentifier($inDatabase).'.'.$this->getDbDriver()->quoteIdentifier($inTable)
			);

		foreach ( $oRes as $row ) {
			$oDef = new dbMapperIndexDefinition();
			$oDef->setTable($inTable);
			$oDef->setKeyname($row['Key_name']);
			$oDef->setSequenceInIndex($row['Seq_in_index']);
			$oDef->setColumnName($row['Column_name']);
			$oDef->setUnique(($row['Non_unique'] == 0 ? 1 : 0));
			$results[] = $oDef;
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
		$results = array();

		$oRes = $this->getDbDriver()->query(
			"SHOW CREATE TABLE ".$this->getDbDriver()->quoteIdentifier($inDatabase).'.'.$this->getDbDriver()->quoteIdentifier($inTable)
		);

		$sql = $oRes->fetchColumn(1);
		$lines = explode("\n", $sql);
		
		foreach ( $lines as $line ) {
			if ( strpos($line, 'CONSTRAINT') !== false ) {
				$oConstraint = new dbMapperConstraintDefinition();

				$matches = array();
				preg_match(
					'/CONSTRAINT \`(?P<constraint>\w+)\` FOREIGN KEY \(\`(?P<index>\w+)\`\) REFERENCES \`(?P<table>\w+)\` \(\`(?<column>\w+)\`\)/',
					$line,
					$matches
				);

				$oConstraint
					->setDatabase($inDatabase)
					->setTable($inTable)
					->setConstraintName($matches['constraint'])
					->setKeyName($matches['index'])
					->setReferenceDatabase($inDatabase)
					->setReferenceTable($matches['table'])
					->setReferenceColumn($matches['column']);

				if ( preg_match('/ON DELETE (?P<delete>\w+)/', $sql, $matches) ) {
					$oConstraint->setOnDelete($matches['delete']);
				}

				if ( preg_match('/ON UPDATE (?P<update>\w+)/', $sql, $matches) ) {
					$oConstraint->setOnUpdate($matches['update']);
				}

				$results[] = $oConstraint;
			}
		}

		return $results;
	}

	/**
	 * @see dbUtilities::getDbBackup()
	 */
	function getDbBackup() {
		return new dbDriverMySqlBackup($this->getDbDriver());
	}
}