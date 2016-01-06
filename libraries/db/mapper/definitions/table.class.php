<?php
/**
 * dbMapperTableDefinition.class.php
 * 
 * Holds properties about a table
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbMapperTableDefinition
 * @version $Rev: 835 $
 */


/**
 * dbMapperTableDefinition Class
 * 
 * Holds properties about a table; largely based on MySQL table definition.
 * A table definition contains {@link dbMapperFieldSet} and {@link dbMapperIndexSet}
 * which in-turn hold the information about the fields and the indexes that
 * make up the table.
 * 
 * All of these properties can be specified manually by chaining together
 * the method calls. An example of this can be seen in the test cases for
 * testing these classes. Under normal circumstances they are auto-populated
 * via the dbDrivers utility class.
 * 
 * <code>
 * $oTable = new dbMapperTableDefinition('testdb', 'test');
 * $oTable
 *     ->setIndexes(
 *         dbMapperIndexSet::getInstance()
 *             ->addIndexDefinition(new dbMapperIndexDefinition())
 *             ->addIndexDefinition(new dbMapperIndexDefinition())
 *     )
 *     ->setFields(
 *         dbMapperFieldSet::getInstance()
 *             ->addFieldDefinition(new dbMapperFieldDefinition())
 *             ->addFieldDefinition(new dbMapperFieldDefinition())
 *     );
 * </code>
 * 
 * @package scorpio
 * @subpackage db
 * @category dbMapperTableDefinition
 */
class dbMapperTableDefinition {
	
	/**
	 * Stores modification status
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Database
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Database = '';
	
	/**
	 * Stores $_TableName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_TableName = '';
	
	/**
	 * Stores $_Fields
	 *
	 * @var dbMapperFieldSet
	 * @access protected
	 */
	protected $_Fields = false;
	
	/**
	 * Stores $_Indexes
	 *
	 * @var dbMapperIndexSet
	 * @access protected
	 */
	protected $_Indexes = false;

	/**
	 * Stores $_Constraints
	 *
	 * @var dbMapperConstraintSet
	 * @access protected
	 */
	protected $_Constraints = false;
	
	
	
	/**
	 * Returns a new instance of dbMapperTableDefinition
	 *
	 * @param string $inDatabase
	 * @param string $inTableName
	 * @return dbMapperTableDefinition
	 */
	static function getInstance($inDatabase, $inTableName) {
		return new self($inDatabase, $inTableName);
	}
	
	
	
	/**
	 * Returns a new table definition
	 *
	 * @param string $database
	 * @param string $tableName
	 */
	function __construct($inDatabase = null, $inTableName = null) {
		if ( !is_null($inDatabase) ) {
			$this->setDatabase($inDatabase);
		}
		if ( !is_null($inTableName) ) {
			$this->setTableName($inTableName);
		}
	}
	
	
	
	/**
	 * Returns Indexes
	 *
	 * @return dbMapperIndexSet
	 */
	function getIndexes() {
		if ( !$this->_Indexes instanceof dbMapperIndexSet ) {
			$this->_Indexes = new dbMapperIndexSet();
		}
		return $this->_Indexes;
	}
	
	/**
	 * Set Indexes property
	 *
	 * @param dbMapperIndexSet $Indexes
	 * @return dbMapperTableDefinition
	 */
	function setIndexes(dbMapperIndexSet $dbIndexSet) {
		if ( $dbIndexSet !== $this->_Indexes ) {
			$this->_Indexes = $dbIndexSet;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns Fields
	 *
	 * @return dbMapperFieldSet
	 */
	function getFields() {
		if ( !$this->_Fields instanceof dbMapperFieldSet ) {
			$this->_Fields = new dbMapperFieldSet();
		}
		return $this->_Fields;
	}
	
	/**
	 * Set Fields property
	 *
	 * @param dbMapperFieldSet $Fields
	 * @return dbMapperTableDefinition
	 */
	function setFields(dbMapperFieldSet $dbFieldSet) {
		if ( $dbFieldSet !== $this->_Fields ) {
			$this->_Fields = $dbFieldSet;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the Constraint set
	 *
	 * @return dbMapperConstraintSet
	 */
	function getConstraints() {
		if ( !$this->_Constraints instanceof dbMapperConstraintSet ) {
			$this->_Constraints = new dbMapperConstraintSet();
		}
		return $this->_Constraints;
	}

	/**
	 * Set the constraints property
	 *
	 * @param dbMapperConstraintSet $dbConstraintSet
	 * @return dbMapperTableDefinition
	 */
	function setConstraints(dbMapperConstraintSet $dbConstraintSet) {
		if ( $dbConstraintSet !== $this->_Constraints ) {
			$this->_Constraints = $dbConstraintSet;
			$this->_Modified = true;
		}
		return $this;
	}



	/**
	 * Returns TableName
	 *
	 * @return string
	 */
	function getTableName() {
		return $this->_TableName;
	}
	
	/**
	 * Set TableName property
	 *
	 * @param string $inTableName
	 * @return dbMapperTableDefinition
	 */
	function setTableName($inTableName) {
		if ( $inTableName !== $this->_TableName ) {
			$this->_TableName = $inTableName;
			$this->_Modified = true;
		}
		return $this;
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
	 * @param string $Database
	 * @return dbMapperTableDefinition
	 */
	function setDatabase($inDatabase) {
		if ( $inDatabase !== $this->_Database ) {
			$this->_Database = $inDatabase;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns a field named $inFieldName, or false if not found
	 *
	 * @param string $inFieldName
	 * @return dbFieldDefinition
	 */
	function getField($inFieldName) {
		return $this->getFields()->getFieldDefinition($inFieldName);
	}
	
	/**
	 * Returns fields that are NOT in a primary or unique key
	 *
	 * @return array
	 */
	function getNonUniqueFields() {
		$res = array();
		foreach ( $this->getFields() as $field ) {
			$add = true;
			foreach ( $this->getIndexes()->getFieldsInUniqueKey() as $indexName => $fields ) {
				foreach ( $fields as $seqID => $indexField ) {
					if ( $field->getField() == $indexField->getColumnName() ) {
						$add = false;
						break;
					}
				}
			}
			
			if ( $add === true ) {
				$res[] = $field;
			}
		}
		return $res;
	}
}