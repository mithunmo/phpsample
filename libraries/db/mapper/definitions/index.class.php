<?php
/**
 * dbMapperIndexDefinition.class.php
 * 
 * Holds properties about an index on the table
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbMapperIndexDefinition
 * @version $Rev: 722 $
 */


/**
 * dbMapperIndexDefinition
 * 
 * Holds properties about an index on the table; largely based on MySQLs index definition.
 * In keeping with the other definition objects, the index definition supports method
 * chaining.
 * 
 * <code>
 * $oIndexDef = new dbMapperIndexDefinition();
 * $oIndexDef
 *     ->setTable()
 *     ->setKeyName()
 *     ->setSequenceInIndex();
 * </code>
 * 
 * @package scorpio
 * @subpackage db
 * @category dbMapperIndexDefinition
 */
class dbMapperIndexDefinition {
	
	/**
	 * Stores modification status
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Table
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Table = '';
	
	/**
	 * Stores $_KeyName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_KeyName = '';
	
	/**
	 * Stores $_SequenceInIndex
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_SequenceInIndex = 1;
	
	/**
	 * Stores $_ColumnName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ColumnName = '';
	
	/**
	 * Stores $_Unique
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Unique = false;
	
	
	
	/**
	 * Returns an instance of dbMapperIndexDefinition
	 *
	 * @return dbMapperIndexDefinition
	 */
	static function getInstance() {
		return new self();
	}
	
	
	
	/**
	 * Returns Unique
	 *
	 * @return boolean
	 */
	function getUnique() {
		return $this->_Unique;
	}
	
	/**
	 * Set Unique property
	 *
	 * @param boolean $inUnique
	 * @return dbMapperIndexDefinition
	 */
	function setUnique($inUnique) {
		if ( $inUnique !== $this->_Unique ) {
			$this->_Unique = $inUnique;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns ColumnName
	 *
	 * @return string
	 */
	function getColumnName() {
		return $this->_ColumnName;
	}
	
	/**
	 * Set ColumnName property
	 *
	 * @param string $inColumnName
	 * @return dbMapperIndexDefinition
	 */
	function setColumnName($inColumnName) {
		if ( $inColumnName !== $this->_ColumnName ) {
			$this->_ColumnName = $inColumnName;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns SequenceInIndex
	 *
	 * @return integer
	 */
	function getSequenceInIndex() {
		return $this->_SequenceInIndex;
	}
	
	/**
	 * Set SequenceInIndex property
	 *
	 * @param integer $inSequenceInIndex
	 * @return dbMapperIndexDefinition
	 */
	function setSequenceInIndex($inSequenceInIndex) {
		if ( $inSequenceInIndex !== $this->_SequenceInIndex ) {
			$this->_SequenceInIndex = $inSequenceInIndex;
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
	 * @return dbMapperIndexDefinition
	 */
	function setKeyName($inKeyName) {
		if ( $inKeyName !== $this->_KeyName ) {
			$this->_KeyName = $inKeyName;
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
	 * @return dbMapperIndexDefinition
	 */
	function setTable($inTable) {
		if ( $inTable !== $this->_Table ) {
			$this->_Table = $inTable;
			$this->_Modified = true;
		}
		return $this;
	}
}