<?php
/**
 * dbMapperTableSet.class.php
 * 
 * Holds a set of table definitions
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbMapperTableSet
 * @version $Rev: 707 $
 */


/**
 * dbMapperTableSet Class
 * 
 * Holds a set of table definitions. This is used by the dbMapper
 * when building a map of the database properties. It can be used
 * standalone to simulate a database.
 * 
 * <code>
 * $oTableSet = new dbMapperTableSet();
 * $oTableSet->addTableDefinition(new dbMapperTableDefinition());
 * $oTableSet->addTableDefinition(new dbMapperTableDefinition());
 * </code>
 * 
 * @see dbMapperTableDefinition
 * @package scorpio
 * @subpackage db
 * @category dbMapperTableSet
 */
class dbMapperTableSet extends baseSet {
	
	/**
	 * Returns a single instance of the base set
	 *
	 * @return dbMapperTableSet
	 */
	static function getInstance() {
		return new self();
	}
	
	/**
	 * Adds a table definition to the set
	 *
	 * @param dbMapperTableDefinition $oTable
	 * @return dbMapperTableSet
	 */
	function addTableDefinition(dbMapperTableDefinition $oTable) {
		return parent::_setItem($oTable->getTableName(), $oTable);
	}
	
	/**
	 * Sets an array of tables
	 *
	 * @param array $inTables
	 * @return dbMapperTableSet
	 */
	function setTables($inTables) {
		return parent::_setItem($inTables);
	}
	
	/**
	 * Returns a table definition
	 *
	 * @param string $inTableName
	 * @return dbMapperTableDefinition
	 */
	function getTableDefinition($inTableName) {
		return parent::_getItem($inTableName);
	}
	
	/**
	 * Removes a table definition
	 *
	 * @param dbMapperTableDefinition $oTable
	 * @return dbMapperTableSet
	 */
	function removeTableDefinition(dbMapperTableDefinition $oTable) {
		return parent::_removeItem($oTable->getTableName());
	}
	
	/**
	 * Removes all tables
	 *
	 * @return dbMapperTableSet
	 */
	function removeTables() {
		return parent::_resetSet();
	}
	
	/**
	 * Returns number of tables in set
	 *
	 * @return integer
	 */
	function countTables() {
		return parent::_itemCount();
	}
}