<?php
/**
 * reportWriterOds
 * 
 * Stored in reportWriterOds.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportWriterOds
 * @version $Rev: 749 $
 */


/**
 * reportWriterOds
 * 
 * Converts the reportData object into an ODS format. The ods object only
 * supports a small part of the full specification and the original ods-php
 * class is rather light on docs.
 * 
 * @todo DR: add formatting support for ODS files.
 * 
 * @package scorpio
 * @subpackage report
 * @category reportWriterOds
 */
class reportWriterOds extends reportWriterBase {
	
	/**
	 * @see reportWriterBase::initialise()
	 */
	function initialise() {
		$this->setExtension('ods');
		$this->setMimeType('application/vnd.oasis.opendocument.spreadsheet');
	}
	
	/**
	 * @see reportWriterBase::_compile()
	 */
	function _compile() {
		if ( $this->getReport() instanceof reportCollectionBase ) {
			$reports = $this->getReport()->getReports();
		} else {
			$reports = array($this->getReport());
		}
		
		$oOds = new ods();
		$sheet = 1;
		
		foreach ( $reports as $oReport ) {
			$dataCols = $oReport->getDataColumns();
			$row = 5;
			
			/*
			 * add title and description
			 */
			$this->_buildSheetTitle($oOds, $oReport, $sheet);
			
			/*
			 * add row data
			 */
			foreach ( $oReport->getReportData() as $id => $rowData ) {
				$col = 0;
				foreach ( $dataCols as $field ) {
					if ( array_key_exists($field, $rowData) ) {
						$value = $rowData[$field];
						$format = 'string';
						
						if ( $rowData[$field] instanceof reportDataAbstract ) {
							$value = $rowData[$field]->__toString();
						}
						if ( is_numeric($value) ) {
							$format = 'float';
						}
						
						$oOds->addCell($sheet, $row, $col++, $value, $format);
					}
				}
				++$row;
				
				if ( $row == 65535 ) {
					$this->_buildSheetTitle($oOds, $oReport, ++$sheet);
					$row = 5;
				}
			}
			++$sheet;
		}
		
		$oOds->save($this->getFullPathToOutputFile(), system::getLocale()->getLocale());
		return true;
	}
	
	
	/**
	 * Creates a new sheet references and injects the titles
	 * 
	 * @param ods $inOds
	 * @param reportBase $inReport
	 * @param integer $inSheetNum
	 */
	private function _buildSheetTitle(ods $inOds, reportBase $inReport, $inSheetNum = 0) {
		$cols = $inReport->getDisplayColumns();
		
		$inOds->addCell($inSheetNum, 1, 0, system::getConfig()->getParam('app', 'title', 'Scorpio Framework').': '.$inReport->getReportName(), 'string');
		$inOds->addCell($inSheetNum, 2, 0, $inReport->getReportDescription(), 'string');
		$inOds->addCell($inSheetNum, 3, 0, '', 'string');
		
		$col = 0;
		// add column headers
		foreach ( $cols as $colTitle ) {
			$inOds->addCell($inSheetNum, 4, $col++, $colTitle, 'string');
		}
	}
}