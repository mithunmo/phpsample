<?php
/**
 * reportDataAbstract
 * 
 * Stored in reportDataAbstract.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportDataAbstract
 * @version $Rev: 778 $
 */


/**
 * reportDataAbstract
 * 
 * reportDataAbstract is an interface to various dynamic functions that can be
 * inserted into reports by the writers that support them. This allows for
 * more advance control of especially spreadsheets - but allows this control to
 * still be used by the more basic {@link reportWriterBase reportWriter} components.
 * 
 * This abstract class provides the shared logic that all reportData functions will
 * use. All sub-classes need to implement the {@link reportDataAbstract::render()}
 * method.
 * 
 * In most cases these functions will operate on the last set of data e.g. if the
 * writer is at row 10, column 5, then the function will receive row 10 column 4.
 * For summing rows, the render() method will still receive the current row, from
 * which 1 should be substracted (totals are always last).
 * 
 * reportDataAbstract inherits from baseOptionsSet allowing options to be set on
 * a per class basis for additional control. What these options are will depend
 * on the implemented class.
 * 
 * @package scorpio
 * @subpackage report
 * @category reportDataAbstract
 */
 abstract class reportDataAbstract extends baseOptionsSet {
	
	/**
	 * Stores $_StringValue
	 *
	 * @var string
	 * @access protected
	 */
	protected $_StringValue;
	
	/**
	 * Stores $_StringFormat
	 *
	 * @var string
	 * @access protected
	 */
	protected $_StringFormat;
	
	/**
	 * Stores $_Row
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Row;
	
	/**
	 * Stores $_Column
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Column;
	
	
	
	/**
	 * Creates a new instance of the function
	 * 
	 * The constructor should be overloaded to provide suitable defaults for the
	 * data type the class is for. For example: sum and average set the default
	 * string value to 0 (zero) instead of null.
	 * 
	 * If overloading, be sure to call into the parent __construct():
	 * 
	 * <code>
	 * class myFunction extends reportDataAbstract {
	 * 
	 * 		function __construct($inRow = 1, $inColumn = 1, $inStringValue = 'My String', array $inOptions = array()) {
	 * 			parent::__construct($inRow, $inColumn, $inStringValue, $inOptions);
	 * 		}
	 * }
	 * </code>
	 * 
	 * @param integer $inRow Row number to act on
	 * @param string $inColumn Column name to act on
	 * @param string $inStringValue (optional) string value of this formula
	 * @param string $inStringFormat (optional) sprintf compatible formatting instructions
	 * @param array $inOptions (optional) an array of options to be used during render
	 */
	function __construct($inRow = null, $inColumn = null, $inStringValue = null, $inStringFormat = null, array $inOptions = array()) {
		$this->reset();
		$this->setRow($inRow);
		$this->setColumn($inColumn);
		$this->setStringValue($inStringValue);
		$this->setStringFormat($inStringFormat);
		$this->setOptions($inOptions);
	}
	
	/**
	 * Returns a string if this class is used as a string
	 * 
	 * @return string
	 */
	function __toString() {
		if ( $this->getStringFormat() !== null ) {
			return (string) sprintf($this->getStringFormat(), $this->getStringValue());
		} else {
			return (string) $this->getStringValue();
		}
	}
	
	/**
	 * Resets the object to defaults
	 * 
	 * @return void
	 */
	function reset() {
		$this->_StringValue = null;
		$this->_StringFormat = null;
		$this->_Row = null;
		$this->_Column = null;
		parent::reset();
	}
	
	/**
	 * Returns control as a string using the provided data as the current data points
	 * 
	 * @param integer $startRow The current row of data from the reportData
	 * @param string $startCol The first column of data in Excel column format (e.g. A, AA, B etc)
	 * @param integer $endRow The current row of data from the reportData
	 * @param string $endCol Last column of data in Excel column format (e.g. A, AA, B etc)
	 * @param integer $sheetRowStart (optional) the row the report starts default 5 for the Excel writers
	 * @return string
	 */
	abstract function render($startRow, $startCol, $endRow, $endCol, $sheetRowStart = 5);
	
	
	
	/**
	 * Returns the string to use for writers that cannot use formulae
	 *
	 * @return string
	 */
	function getStringValue() {
		return $this->_StringValue;
	}
	
	/**
	 * Set the string value (can be integer, or float as well)
	 *
	 * @param string $inStringValue
	 * @return reportDataAbstract
	 */
	function setStringValue($inStringValue) {
		if ( $inStringValue !== $this->_StringValue ) {
			$this->_StringValue = $inStringValue;
			$this->setModified();
		}
		return $this;
	}
 
	/**
	 * Returns the string formatting controls
	 *
	 * @return string
	 */
	function getStringFormat() {
		return $this->_StringFormat;
	}
	
	/**
	 * Set the string formatting options for the string value
	 * 
	 * This is passed into the PHP function {@link sprintf} - any valid format
	 * that sprintf can take can be used here. Note: the only value that will
	 * be passed is the {@link reportDataAbstract::$_StringValue StringValue}.
	 * No other data points can be used unless the data formatter is customised
	 * to permit it via the options array.
	 *
	 * @param string $inStringFormat
	 * @return reportDataAbstract
	 */
	function setStringFormat($inStringFormat) {
		if ( $inStringFormat !== $this->_StringFormat ) {
			$this->_StringFormat = $inStringFormat;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the specific row to operate on
	 *
	 * @return integer
	 */
	function getRow() {
		return $this->_Row;
	}
	
	/**
	 * Set the specific row to operate on, rather than the row the writer is currently processing
	 *
	 * @param integer $inRow
	 * @return reportDataAbstract
	 */
	function setRow($inRow) {
		if ( $inRow !== $this->_Row ) {
			$this->_Row = $inRow;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns a specific column to be operated on
	 *
	 * @return integer
	 */
	function getColumn() {
		return $this->_Column;
	}
	
	/**
	 * Set a specific column to operate on, this should be the numeric index starting at 1
	 *
	 * @param integer $inColumn
	 * @return reportDataAbstract
	 */
	function setColumn($inColumn) {
		if ( $inColumn !== $this->_Column ) {
			$this->_Column = $inColumn;
			$this->setModified();
		}
		return $this;
	}
 }