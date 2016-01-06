<?php
/**
 * dbMapperConstraintDefinition.class.php
 * 
 * Holds properties about a constraint on the table
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbMapperConstraintDefinition
 * @version $Rev: 722 $
 */


/**
 * dbMapperConstraintDefinition
 * 
 * Holds properties about a constraint (foreign key) on the table. This is largely
 * based on the information returned in the MySQL show create table query.
 * 
 * <code>
 * $oConstraint = new dbMapperConstraintDefinition();
 * $oConstraint
 *     ->setTable()
 *     ->setConstraintName()
 *     ->setKeyName()
 *     ->setReferenceTable()
 *     ->setReferenceColumn()
 *     ->setOnUpdate()
 *     ->setOnDelete();
 * </code>
 * 
 * @package scorpio
 * @subpackage db
 * @category dbMapperConstraintDefinition
 */
class dbMapperConstraintDefinition {
	
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
	 * Stores $_Table
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Table = '';

	/**
	 * Stores $_ConstraintName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ConstraintName = '';

	/**
	 * Stores $_KeyName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_KeyName = '';

	/**
	 * Stores $_ReferenceDatabase
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ReferenceDatabase = '';

	/**
	 * Stores $_ReferenceTable
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ReferenceTable = '';

	/**
	 * Stores $_ReferenceColumn
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ReferenceColumn = '';

	/**
	 * Stores $_OnDelete
	 *
	 * @var string
	 * @access protected
	 */
	protected $_OnDelete = '';

	/**
	 * Stores $_OnUpdate
	 *
	 * @var string
	 * @access protected
	 */
	protected $_OnUpdate = '';

	const ACTION_NO_ACTION = 'NO ACTION';
	const ACTION_CASCADE = 'CASCADE';
	const ACTION_SET_NULL = 'SET NULL';
	const ACTION_RESTRICT = 'RESTRICT';



	/**
	 * Returns an instance of dbMapperConstraintDefinition
	 *
	 * @return dbMapperConstraintDefinition
	 */
	static function getInstance() {
		return new self();
	}
	


	/**
	 * Returns the value of $_Database
	 *
	 * @return string
	 */
	function getDatabase() {
		return $this->_Database;
	}

	/**
	 * Set $_Database to $inDatabase
	 *
	 * @param string $inDatabase
	 * @return dbMapperConstraintDefinition
	 */
	function setDatabase($inDatabase) {
		if ( $inDatabase !== $this->_Database ) {
			$this->_Database = $inDatabase;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns Table
	 *
	 * @return string
	 */
	function getTable() {
		return $this->_Table;
	}

	/**
	 * Set Table property
	 *
	 * @param string $inTable
	 * @return dbMapperConstraintDefinition
	 */
	function setTable($inTable) {
		if ( $inTable !== $this->_Table ) {
			$this->_Table = $inTable;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the value of $_ConstraintName
	 *
	 * @return string
	 */
	function getConstraintName() {
		return $this->_ConstraintName;
	}

	/**
	 * Set $_ConstraintName to $inConstraintName
	 *
	 * @param string $inConstraintName
	 * @return dbMapperConstraintDefinition
	 */
	function setConstraintName($inConstraintName) {
		if ( $inConstraintName !== $this->_ConstraintName ) {
			$this->_ConstraintName = $inConstraintName;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns KeyName
	 *
	 * @return string
	 */
	function getKeyName() {
		return $this->_KeyName;
	}
	
	/**
	 * Set KeyName property
	 *
	 * @param string $inKeyName
	 * @return dbMapperConstraintDefinition
	 */
	function setKeyName($inKeyName) {
		if ( $inKeyName !== $this->_KeyName ) {
			$this->_KeyName = $inKeyName;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the value of $_ReferenceDatabase
	 *
	 * @return string
	 */
	function getReferenceDatabase() {
		return $this->_ReferenceDatabase;
	}

	/**
	 * Set $_ReferenceDatabase to $inReferenceDatabase
	 *
	 * @param string $inReferenceDatabase
	 * @return dbMapperConstraintDefinition
	 */
	function setReferenceDatabase($inReferenceDatabase) {
		if ( $inReferenceDatabase !== $this->_ReferenceDatabase ) {
			$this->_ReferenceDatabase = $inReferenceDatabase;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the value of $_ReferenceTable
	 *
	 * @return string
	 */
	function getReferenceTable() {
		return $this->_ReferenceTable;
	}

	/**
	 * Set $_ReferenceTable to $inReferenceTable
	 *
	 * @param string $inReferenceTable
	 * @return dbMapperConstraintDefinition
	 */
	function setReferenceTable($inReferenceTable) {
		if ( $inReferenceTable !== $this->_ReferenceTable ) {
			$this->_ReferenceTable = $inReferenceTable;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the value of $_ReferenceColumn
	 *
	 * @return string
	 */
	function getReferenceColumn() {
		return $this->_ReferenceColumn;
	}

	/**
	 * Set $_ReferenceColumn to $inReferenceColumn
	 *
	 * @param string $inReferenceColumn
	 * @return dbMapperConstraintDefinition
	 */
	function setReferenceColumn($inReferenceColumn) {
		if ( $inReferenceColumn !== $this->_ReferenceColumn ) {
			$this->_ReferenceColumn = $inReferenceColumn;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the value of $_OnDelete
	 *
	 * @return string
	 */
	function getOnDelete() {
		return $this->_OnDelete;
	}

	/**
	 * Set $_OnDelete to $inOnDelete
	 *
	 * @param string $inOnDelete
	 * @return dbMapperConstraintDefinition
	 */
	function setOnDelete($inOnDelete) {
		if ( $inOnDelete !== $this->_OnDelete ) {
			$this->_OnDelete = $inOnDelete;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns the value of $_OnUpdate
	 *
	 * @return string
	 */
	function getOnUpdate() {
		return $this->_OnUpdate;
	}

	/**
	 * Set $_OnUpdate to $inOnUpdate
	 *
	 * @param string $inOnUpdate
	 * @return dbMapperConstraintDefinition
	 */
	function setOnUpdate($inOnUpdate) {
		if ( $inOnUpdate !== $this->_OnUpdate ) {
			$this->_OnUpdate = $inOnUpdate;
			$this->_Modified = true;
		}
		return $this;
	}
}