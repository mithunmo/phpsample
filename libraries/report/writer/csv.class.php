<?php
/**
 * reportWriterCsv
 * 
 * Stored in reportWriterCsv.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportWriterCsv
 * @version $Rev: 650 $
 */


/**
 * reportWriterCsv
 * 
 * Converts the reportData object into a CSV file in the standard UNIX
 * format, i.e. data points separated by a comma, escaped using the double
 * quote, with a UNIX newline (\n).
 * 
 * Unlike other report formats, CSV data is written entirely to disk and
 * is never held in memory. For very large data sets, you should use CSV
 * format.
 * 
 * @package scorpio
 * @subpackage report
 * @category reportWriterCsv
 */
class reportWriterCsv extends reportWriterBase {
	
	/**
	 * @see reportWriterBase::initialise()
	 */
	function initialise() {
		$this->setExtension('csv');
		$this->setMimeType('text/csv');
	}
	
	/**
	 * @see reportWriterBase::_compile()
	 */
	function _compile() {
		$fhndl = fopen($this->getFullPathToOutputFile(), 'wb');
		if ( !$fhndl ) {
			throw new reportWriterOutputFileNotWritableException($this->getFullPathToOutputFile());
		}
		
		fputcsv($fhndl, $this->getReport()->getDisplayColumns(), ',', '"');
		
		$dataCols = $this->getReport()->getDataColumns();
		foreach ( $this->getReport()->getReportData() as $row ) {
			$data = array();
			foreach ( $dataCols as $field ) {
				if ( array_key_exists($field, $row) ) {
					$data[$field] = $row[$field];
				}
			}
			fputcsv($fhndl, $data, ',', '"');
		}
		fclose($fhndl);
	}
}