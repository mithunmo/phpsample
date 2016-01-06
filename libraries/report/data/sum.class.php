<?php
/**
 * reportDataSum
 * 
 * Stored in reportDataSum.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportDataSum
 * @version $Rev: 754 $
 */


/**
 * reportDataSum
 * 
 * Sums either a column or row of data by inserting the Excel function =SUM().
 * This requires the data be contiguous and be numeric. Excel will ignore strings
 * with this function, it is suggested to use the column value to set the column
 * and or row to sum.
 * 
 * This formatter / function can sum both a row and a column. To sum a rows data,
 * set the column to be summed. This should be the column index from the
 * {@link reportData} object. This value starts at 1, so the first column is 1, the
 * second 2, third 3 etc. Keep this in mind when using this class.
 * 
 * If the row is set only the specified row will be used. This number should be
 * the row number in the {@link reportData} set and NOT the anticipated row in the
 * report itself. The first row is 1, the second 2 etc.
 * 
 * Example usage:
 * <code>
 * // This examples adds totals as the last row of reportData
 * // simple test class with other methods ignored
 * class testReport extends reportBase {
 * 
 * 		function _run() {
 * 			// insert other rows above, now add some sums to the columns
 *			// our reportData contains, col1,2...4 and a total
 * 			$this->getReportData()->addRow(
 * 				array(
 * 					'col3' => new reportDataSum(0, 3, $this->getReportData()->sumColumn('col3')), // sum only col3
 * 					'col4' => new reportDataSum(0, 4, $this->getReportData()->sumColumn('col4')), // sum only col4
 * 					'total' => new reportDataSum(0, 5, 0) // sum only col5
 * 				)
 * 			);
 * 		}
 * 	}
 * </code>
 * 
 * @package scorpio
 * @subpackage report
 * @category reportDataSum
 */
class reportDataSum extends reportDataAbstract {
	
	/**
	 * Creates a new instance
	 * 
	 * @param integer $inRow Row number to act on
	 * @param string $inColumn Column name to act on
	 * @param string $inStringValue string representation for writers that cannot use formulas
	 * @param string $inStringFormat sprintf formatting for $inStringValue
	 * @param array $inOptions (optional) array of options
	 */
	function __construct($inRow = null, $inColumn = null, $inStringValue = 0, $inStringFormat = null, array $inOptions = array()) {
		parent::__construct($inRow, $inColumn, $inStringValue, $inStringFormat, $inOptions);
	}

	/**
	 * Resets the object to defaults
	 * 
	 * @return void
	 */
	function reset() {
		parent::reset();
		$this->setStringValue(0);
		$this->setModified(false);
	}
	
	/**
	 * Returns the sum formula in Excel format
	 * 
	 * @param integer $startRow
	 * @param string $startCol
	 * @param integer $endRow
	 * @param string $endCol
	 * @param integer $sheetRowStart
	 * @return string
	 */
	function render($startRow, $startCol, $endRow, $endCol, $sheetRowStart = 5) {
		if ( $this->getColumn() ) {
			$startRow = $sheetRowStart;
			--$endRow;
			$startCol = PHPExcel_Cell::stringFromColumnIndex($this->getColumn()-1);
			$endCol = PHPExcel_Cell::stringFromColumnIndex($this->getColumn()-1);
		}
		if ( $this->getRow() ) {
			$startRow = $this->getRow()+$sheetRowStart;
			$endRow = $this->getRow()+$sheetRowStart;
		}
		
		return  sprintf('=SUM(%s%s:%s%s)', $startCol, $startRow, $endCol, $endRow);
	}
}