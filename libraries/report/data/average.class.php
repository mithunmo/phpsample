<?php
/**
 * reportDataAverage
 * 
 * Stored in reportDataAverage.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportDataAverage
 * @version $Rev: 754 $
 */


/**
 * reportDataAverage
 * 
 * Calculates the average value of a row or column of data. Note that in an ideal
 * world the column / row will only contain numerical data. Either way, Excel will
 * allow strings - they should (in theory) be ignored. Either that or they get
 * counted as 1 (integer one).
 * 
 * Example usage:
 * <code>
 * // This examples adds an average as the last row of reportData
 * // simple test class with other methods ignored
 * class testReport extends reportBase {
 * 
 * 		function _run() {
 * 			// insert other rows above, now add some sums to the columns
 *			// our reportData contains, col1,2...4 and a total
 * 			$this->getReportData()->addRow(
 * 				array(
 * 					'col3' => new reportDataAverage(0, 3, $this->getReportData()->sumColumn('col3')/$this->getReportData()->getCount()), // avg only col3
 * 				)
 * 			);
 * 		}
 * 	}
 * </code>
 * 
 * @package scorpio
 * @subpackage report
 * @category reportDataAverage
 */
class reportDataAverage extends reportDataAbstract {

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
		
		return  sprintf('=AVERAGE(%s%s:%s%s)', $startCol, $startRow, $endCol, $endRow);
	}
}