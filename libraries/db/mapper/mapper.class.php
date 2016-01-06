<?php
/**
 * dbMapper.class.php
 * 
 * Contains db specific utility implementations
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbMapper
 * @version $Rev: 835 $
 */


/**
 * dbMapper Class
 * 
 * Generates a map of a database including all tables and indexes on those tables.
 * Each table is an object containing field and index objects. dbMapper supports
 * chained interfaces on most of the methods allowing for some pretty complex
 * looking code, but really quite elegant.
 * 
 * Examples:
 * <code>
 * // fetch everything in a database ... and we do mean EVERYTHING!
 * $oDbMapper = dbManager::getInstance()->getDbUtilities()->getDbMapper();
 * $oDbMapper->setDatabase($this->_Database);
 * $oDbMapper->buildDbMap();
 * print_r($oDbMapper); // likely not a good idea if it's a large database!
 * 
 * // fetch a single tables details and add it to the map
 * $oDbUtils = dbManager::getInstance()->getDbUtilities();
 * $oTableDefinition = dbMapperTableDefinition::getInstance(
 *     $this->getDatabase(), $this->_Table
 * )->setFields(
 *     dbMapperFieldSet::getInstance()->setFields(
 *         $oDbUtils->getTableProperties($this->getDatabase(), $this->_Table)
 *     )
 * )->setIndexes(
 *     dbMapperIndexSet::getInstance()->setIndexes(
 *         $oDbUtils->getTableIndexes($this->getDatabase(), $this->_Table)
 *     )
 * );
 * $oDbMapper->getDbMap()->addTableDefinition($oTableDefinition);
 * </code>
 * 
 * It is important to note that via either method, what should be returned is a
 * dbMapper object that CONTAINS the table definition.
 * 
 * @see generator
 * @package scorpio
 * @subpackage db
 * @category dbMapper
 */
class dbMapper {
	
	/**
	 * Database utilities to allow querying database properties
	 *
	 * @var dbUtilities
	 */
	protected $_DbUtilities				= false;

	/**
	 * Stores $_Database
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Database				= '';

	/**
	 * Stores $_DbMap
	 *
	 * @var dbTableSet
	 * @access protected
	 */
	protected $_DbMap					= false;
	
	
	
	/**
	 * Creates a new dbMapper object
	 *
	 * @param dbUtilities $dbUtilities
	 */
	function __construct(dbUtilities $dbUtilities) {
		$this->_DbUtilities = $dbUtilities;
	}
	
	
	
	/**
	 * Returns Database
	 *
	 * @return string
	 */
	function getDatabase() {
		return $this->_Database;
	}
	
	/**
	 * Set Database property
	 *
	 * @param string $inDatabase
	 * @return dbMapper
	 */
	function setDatabase($inDatabase) {
		if ( $inDatabase !== $this->_Database ) {
			$this->_Database = $inDatabase;
		}
		return $this;
	}
	
	/**
	 * Returns DbMap
	 *
	 * @return dbMapperTableSet
	 */
	function getDbMap() {
		if ( !$this->_DbMap ) {
			$this->_DbMap = new dbMapperTableSet();
		}
		return $this->_DbMap;
	}
	
	/**
	 * Set DbMap property
	 *
	 * @param dbMapperTableSet $inTableSet
	 * @return dbMapper
	 */
	function setDbMap(dbMapperTableSet $inTableSet) {
		if ( $inTableSet !== $this->_DbMap ) {
			$this->_DbMap = $inTableSet;
		}
		return $this;
	}
	
	/**
	 * Returns a list of databases that are accessible from current connection
	 *
	 * @return array
	 */
	function listDatabases() {
		return $this->getDbUtilities()->showDatabases();
	}
	
	/**
	 * Returns the current dbUtilities
	 *
	 * @return dbUtilities
	 */
	function getDbUtilities() {
		return $this->_DbUtilities;
	}
	
	/**
	 * Returns the map of the database as an object tree
	 *
	 * @return dbMapper
	 * @throws dbMapperNoDatabaseSelected
	 */
	function buildDbMap() {
		if ( $this->_Database ) {
			foreach ( $this->getDbUtilities()->showTables($this->getDatabase()) as $tableName ) {
				$this->getDbMap()->addTableDefinition(
					dbMapperTableDefinition::getInstance(
						$this->getDatabase(), $tableName
					)->setFields(
						dbMapperFieldSet::getInstance()->setFields(
							$this->getDbUtilities()->getTableProperties($this->getDatabase(), $tableName)
						)
					)->setIndexes(
						dbMapperIndexSet::getInstance()->setIndexes(
							$this->getDbUtilities()->getTableIndexes($this->getDatabase(), $tableName)
						)
					)->setConstraints(
						dbMapperConstraintSet::getInstance()->setConstraints(
							$this->getDbUtilities()->getTableForeignKeys($this->getDatabase(), $tableName)
						)
					)
				);
			}
			return $this;
		} else {
			throw new dbMapperNoDatabaseSelected();
		}
	}
}