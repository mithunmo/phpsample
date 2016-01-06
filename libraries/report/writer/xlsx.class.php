<?php
/**
 * reportWriterXlsx
 * 
 * Stored in reportWriterXlsx.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportWriterXlsx
 * @version $Rev: 650 $
 */


/**
 * reportWriterXlsx
 * 
 * Converts the reportData into an Excel 2007 Spreadsheet using PHPExcel.
 * 
 * @package scorpio
 * @subpackage report
 * @category reportWriterXlsx
 */
class reportWriterXlsx extends reportWriterExcel {
	
	/**
	 * @see reportWriterBase::initialise()
	 */
	function initialise() {
		$this->setExtension('xlsx');
		$this->setMimeType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	}

	/**
	 * @see reportWriterExcel::_storeFile()
	 */
	protected function _storeFile(PHPExcel $inXls) {
		$oWriter = PHPExcel_IOFactory::createWriter($inXls, 'Excel2007');
		$oWriter->save($this->getFullPathToOutputFile());
	}
}