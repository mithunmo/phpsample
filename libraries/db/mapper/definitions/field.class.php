<?php
/**
 * dbMapperFieldDefinition.class.php
 * 
 * Holds properties about a field including information for error checking
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbMapperFieldDefinition
 * @version $Rev: 707 $
 */


/**
 * dbMapperFieldDefinition Class
 * 
 * Holds properties about a field including information for error checking.
 * This is mostly based on MySQLs field information with some additional properties.
 * In keeping with the other definitions, field definition supports method
 * chaining.
 * 
 * <code>
 * $oFieldDef = new dbMapperFieldDefinition();
 * $oFieldDef
 *     ->setField()
 *     ->setType()
 *     ->setPhpType();
 * </code>
 * 
 * @package scorpio
 * @subpackage db
 * @category dbMapperFieldDefinition
 */
class dbMapperFieldDefinition {
	
	/**
	 * Stores modification status
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Field
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Field = '';
	
	/**
	 * Stores $_Type
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Type = '';
	
	/**
	 * Stores $_Key
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Key = '';
	
	/**
	 * Stores $_Default
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Default = '';
	
	/**
	 * Stores $_Size
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Size = 0;
	
	/**
	 * Stores $_Extra
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Extra = '';
	
	/**
	 * Stores $_Values
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Values = array();
	
	/**
	 * Stores $_IsNull
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_IsNull = false;
	
	/**
	 * Stores $_IsPrimaryKey
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_IsPrimaryKey = false;
	
	/**
	 * Stores $_PhpType
	 *
	 * @var string
	 * @access protected
	 */
	protected $_PhpType = '';
	
	/**
	 * Stores $_Precision
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Precision = '';
	
	
	
	/**
	 * Returns an instance of dbMapperFieldDefinition
	 *
	 * @return dbMapperFieldDefinition
	 */
	static function getInstance() {
		return new self();
	}
	
	
	
	/**
	 * Returns Precision
	 *
	 * @return integer
	 */
	function getPrecision() {
		return $this->_Precision;
	}
	
	/**
	 * Set Precision property
	 *
	 * @param integer $inPrecision
	 * @return dbMapperFieldDefinition
	 */
	function setPrecision($inPrecision) {
		if ( $inPrecision !== $this->_Precision ) {
			$this->_Precision = $inPrecision;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns PhpType
	 *
	 * @return string
	 */
	function getPhpType() {
		return $this->_PhpType;
	}
	
	/**
	 * Set PhpType property
	 *
	 * @param string $inPhpType
	 * @return dbMapperFieldDefinition
	 */
	function setPhpType($inPhpType) {
		if ( $inPhpType !== $this->_PhpType ) {
			$this->_PhpType = $inPhpType;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns Values
	 *
	 * @return array
	 */
	function getValues() {
		return $this->_Values;
	}
	
	/**
	 * Set Values property
	 *
	 * @param array $inValues
	 * @return dbMapperFieldDefinition
	 */
	function setValues($inValues) {
		if ( $inValues !== $this->_Values ) {
			$this->_Values = $inValues;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns Size
	 *
	 * @return integer
	 */
	function getSize() {
		return $this->_Size;
	}
	
	/**
	 * Set Size property
	 *
	 * @param integer $inSize
	 * @return dbMapperFieldDefinition
	 */
	function setSize($inSize) {
		if ( $inSize !== $this->_Size ) {
			$this->_Size = $inSize;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns IsPrimaryKey
	 *
	 * @return boolean
	 */
	function getIsPrimaryKey() {
		return $this->_IsPrimaryKey;
	}
	
	/**
	 * Set IsPrimaryKey property
	 *
	 * @param boolean $inIsPrimaryKey
	 * @return dbMapperFieldDefinition
	 */
	function setIsPrimaryKey($inIsPrimaryKey) {
		if ( $inIsPrimaryKey !== $this->_IsPrimaryKey ) {
			$this->_IsPrimaryKey = $inIsPrimaryKey;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns IsNull
	 *
	 * @return boolean
	 */
	function getIsNull() {
		return $this->_IsNull;
	}
	
	/**
	 * Set IsNull property
	 *
	 * @param boolean $inIsNull
	 * @return dbMapperFieldDefinition
	 */
	function setIsNull($inIsNull) {
		if ( $inIsNull !== $this->_IsNull ) {
			$this->_IsNull = $inIsNull;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns Extra
	 *
	 * @return string
	 */
	function getExtra() {
		return $this->_Extra;
	}
	
	/**
	 * Set Extra property
	 *
	 * @param string $inExtra
	 * @return dbMapperFieldDefinition
	 */
	function setExtra($inExtra) {
		if ( $inExtra !== $this->_Extra ) {
			$this->_Extra = $inExtra;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns Default
	 *
	 * @return string
	 */
	function getDefault() {
		return $this->_Default;
	}
	
	/**
	 * Set Default property
	 *
	 * @param string $inDefault
	 * @return dbMapperFieldDefinition
	 */
	function setDefault($inDefault) {
		if ( $inDefault !== $this->_Default ) {
			$this->_Default = $inDefault;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns Key
	 *
	 * @return string
	 */
	function getKey() {
		return $this->_Key;
	}
	
	/**
	 * Set Key property
	 *
	 * @param string $inKey
	 * @return dbMapperFieldDefinition
	 */
	function setKey($inKey) {
		if ( $inKey !== $this->_Key ) {
			$this->_Key = $inKey;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns Type
	 *
	 * @return string
	 */
	function getType() {
		return $this->_Type;
	}
	
	/**
	 * Set Type property
	 *
	 * @param string $inType
	 * @return dbMapperFieldDefinition
	 */
	function setType($inType) {
		if ( $inType !== $this->_Type ) {
			$this->_Type = $inType;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns Field
	 *
	 * @return string
	 */
	function getField() {
		return $this->_Field;
	}
	
	/**
	 * Set Field property
	 *
	 * @param string $inField
	 * @return dbMapperFieldDefinition
	 */
	function setField($inField) {
		if ( $inField !== $this->_Field ) {
			$this->_Field = $inField;
			$this->_Modified = true;
		}
		return $this;
	}
}