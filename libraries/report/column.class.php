<?php
/**
 * reportColumn
 * 
 * Stored in reportColumn.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportColumn
 * @version $Rev: 771 $
 */


/**
 * reportColumn
 * 
 * The reportColumn maps a data column to a display name. This allows
 * specific columns to be specified in the output but additional data
 * points to be retrieved for use in calculations without impacting
 * on the display of the data.
 * 
 * Additionally reportColumns can have a width set that will cause the
 * data to be truncated during compilation in to the various formats
 * (if the reportWriter has been configured to do so).
 * 
 * Finally: a column can have a datatype set. This is used in the
 * {@link reportData} object to build the SQLite database structure.
 * You should use the constants in this class to specify the type of
 * the data you are storing. If it is not specified a straight "text"
 * field will be created.
 * 
 * <code>
 * // a column named "fieldName"
 * $oCol = new reportColumn('fieldName', 'Display as Field Name', 30, 'text');
 * </code>
 * 
 * @package scorpio
 * @subpackage report
 * @category reportColumn
 */
class reportColumn {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_FieldName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_FieldName;
	
	/**
	 * Stores $_DisplayName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DisplayName;
	
	/**
	 * Stores $_MaxWidth
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MaxWidth;
	
	/**
	 * Stores $_FieldType
	 *
	 * @var string
	 * @access protected
	 */
	protected $_FieldType;
	
	const FIELD_DATE = 'date';
	const FIELD_DATETIME = 'datetime';
	const FIELD_DECIMAL = 'decimal';
	const FIELD_FLOAT = 'float';
	const FIELD_INTEGER = 'integer';
	const FIELD_STRING = 'text';
	const FIELD_TIME = 'time';
	
	
	
	/**
	 * Creates a new column object, mapping a result index to a display name
	 * 
	 * $inMaxWidth sets the maximum width of content for this column in the writer.
	 *
	 * @param string $inFieldName
	 * @param string $inDisplayName
	 * @param integer $inMaxWidth
	 * @return reportColumn
	 */
	function __construct($inFieldName, $inDisplayName, $inMaxWidth = 30, $inFieldType = self::FIELD_STRING) {
		$this->reset();
		$this->setFieldName($inFieldName);
		$this->setDisplayName($inDisplayName);
		$this->setMaxWidth($inMaxWidth);
		$this->setFieldType($inFieldType);
	}
	
	/**
	 * Reset object
	 *
	 * @return void
	 */
	function reset() {
		$this->_FieldName = null;
		$this->_DisplayName = null;
		$this->_MaxWidth = 30;
		$this->_FieldType = self::FIELD_STRING;
		$this->setModified(false);
	}
	
	/**
	 * Returns an array of number types
	 * 
	 * @return array
	 * @static
	 */
	static function getNumericTypes() {
		return array(
			self::FIELD_DECIMAL, self::FIELD_FLOAT, self::FIELD_INTEGER,
		);
	}
	
	
	
	/**
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return reportColumn
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns $_FieldName
	 *
	 * @return string
	 */
	function getFieldName() {
		return $this->_FieldName;
	}
	
	/**
	 * Set $_FieldName to $inFieldName
	 *
	 * @param string $inFieldName
	 * @return reportColumn
	 */
	function setFieldName($inFieldName) {
		if ( $inFieldName !== $this->_FieldName ) {
			$this->_FieldName = $inFieldName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_DisplayName
	 *
	 * @return string
	 */
	function getDisplayName() {
		return $this->_DisplayName;
	}
	
	/**
	 * Set $_DisplayName to $inDisplayName
	 *
	 * @param string $inDisplayName
	 * @return reportColumn
	 */
	function setDisplayName($inDisplayName) {
		if ( $inDisplayName !== $this->_DisplayName ) {
			$this->_DisplayName = $inDisplayName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_MaxWidth
	 *
	 * @return integer
	 */
	function getMaxWidth() {
		return $this->_MaxWidth;
	}
	
	/**
	 * Set $_MaxWidth to $inMaxWidth
	 *
	 * @param integer $inMaxWidth
	 * @return reportColumn
	 */
	function setMaxWidth($inMaxWidth) {
		if ( $inMaxWidth !== $this->_MaxWidth ) {
			$this->_MaxWidth = $inMaxWidth;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_FieldType
	 *
	 * @return string
	 */
	function getFieldType() {
		return $this->_FieldType;
	}
	
	/**
	 * Set $_FieldType to $inFieldType
	 *
	 * @param string $inFieldType
	 * @return reportColumn
	 */
	function setFieldType($inFieldType) {
		if ( $inFieldType !== $this->_FieldType ) {
			$this->_FieldType = $inFieldType;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns true if the field type is numeric
	 * 
	 * @return boolean
	 */
	function isNumeric() {
		return in_array($this->getFieldType(), self::getNumericTypes());
	}
}