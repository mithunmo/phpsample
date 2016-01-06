<?php
/**
 * reportWriterXls
 * 
 * Stored in reportWriterXls.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportWriterXls
 * @version $Rev: 650 $
 */


/**
 * reportWriterXls
 * 
 * Converts the reportData into an Excel Spreadsheet using Spreadsheet_Excel_Writer.
 * 
 * @package scorpio
 * @subpackage report
 * @category reportWriterXls
 */
class reportWriterXls extends reportWriterExcel {
	
	/**
	 * @see reportWriterBase::initialise()
	 */
	function initialise() {
		$this->setExtension('xls');
		$this->setMimeType('application/vnd.ms-excel');
	}

	/**
	 * @see reportWriterExcel::_storeFile()
	 */
	protected function _storeFile(PHPExcel $inXls) {
		$oWriter = PHPExcel_IOFactory::createWriter($inXls, 'Excel5');
		$oWriter->save($this->getFullPathToOutputFile());
	}
}