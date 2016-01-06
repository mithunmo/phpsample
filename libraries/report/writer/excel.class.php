<?php
/**
 * reportWriterExcel
 * 
 * Stored in reportWriterExcel.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportWriterExcel
 * @version $Rev: 771 $
 */


/**
 * reportWriterExcel
 * 
 * A shared writer that coverts the report data into a PHPExcel object
 * that can then be output by additional writers that inherit this class.
 * 
 * @package scorpio
 * @subpackage report
 * @category reportWriterExcel
 */
abstract class reportWriterExcel extends reportWriterBase {
	
	/**
	 * @see reportWriterBase::_compile()
	 */
	function _compile() {
		/*
		 * Set PHPExcel to use a disk cache to reduce memory usage
		 */
		PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_discISAM);
		
		$sheet = 1;
		$oXls = new PHPExcel();
		$oXls->getProperties()->setCompany(system::getConfig()->getParam('app', 'company', 'Scorpio Framework')->getParamValue());
		$oXls->getProperties()->setCreator(system::getConfig()->getParam('app', 'author', 'Scorpio Framework')->getParamValue());
		$oXls->getProperties()->setDescription($this->getReport()->getReportDescription());
		$oXls->getProperties()->setTitle($this->getReport()->getReportName());
		$oXls->getProperties()->setSubject($this->getReport()->getReportName());
		
		if ( $this->getReport() instanceof reportCollectionBase ) {
			$reports = $this->getReport()->getReports();
		} else {
			$reports = array($this->getReport());
		}
		
		foreach ( $reports as $oReport ) {
			$this->createReportOutput($oXls, $oReport, $sheet);
			++$sheet;
		}
		
		$oXls->setActiveSheetIndex(0);
		
		$this->_storeFile($oXls);
		return true;
	}

	/**
	 * Write out the file to the file system
	 *
	 * @param PHPExcel $inXls
	 * @return void
	 * @abstract
	 */
	abstract protected function _storeFile(PHPExcel $inXls);
	
	/**
	 * Creates appropriate sheets for the report
	 * 
	 * @param PHPExcel $inXls
	 * @param reportBase $inReport
	 * @param integer &$inSheet
	 * @return void
	 * @abstract
	 */
	private function createReportOutput(PHPExcel $inXls, reportBase $inReport, &$sheet) {
		$row = $sheetRowStart = 5;
		$cols = $inReport->getDisplayColumns();
		$dataCols = $inReport->getDataColumns();
		$oStyle = $inReport->getReportStyle();
		
		/*
		 * Create report format instructions
		 */
		$arrFmtReportTitle = $this->createFormat($inXls, $oStyle, reportStyle::SECTION_TITLE);
		$arrFmtReportDesc = $this->createFormat($inXls, $oStyle, reportStyle::SECTION_DESC);
		$arrFmtReportColHeader = $this->createFormat($inXls, $oStyle, reportStyle::SECTION_HEADING);
		$arrFmtReportColData = $this->createFormat($inXls, $oStyle, reportStyle::SECTION_DATA);
		$arrFmtReportColDataAlt = $this->createFormat($inXls, $oStyle, reportStyle::SECTION_DATA);
		$arrFmtReportColDataAlt['fill']['color']['rgb']
			= $oStyle->getStyleAttribute(
				reportStyle::SECTION_DATA, reportStyle::STYLE_ATTRIBUTE_ALT_BACKGROUND_COLOUR, 'cdcdcd'
			);
		
		
		/*
		 * Counter just for the sheets in this report
		 * @var integer
		 */
		$setSheetNum = 1;
		
		/*
		 * Create sheet for workbook
		 */
		$oSheet = $this->createWorksheet($inXls, $inReport, $sheet, $arrFmtReportTitle, $arrFmtReportDesc, $arrFmtReportColHeader, $setSheetNum);
		
		/*
		 * Add column data
		 */
		foreach ( $inReport->getReportData() as $rowData ) {
			$col = 0;
			foreach ( $dataCols as $field ) {
				if ( array_key_exists($field, $rowData) ) {
					$value = $rowData[$field];
					if ( $value instanceof reportDataAbstract ) {
						$value = $value->render($row, $this->_getColumnLetter(0), $row, $this->_getColumnLetter($col-1), $sheetRowStart);
					}
					
					$oSheet->getCellByColumnAndRow($col, $row)->setValue($value);
					$oSheet->getStyleByColumnAndRow($col, $row)->applyFromArray(($row % 2 == 0 ? $arrFmtReportColDataAlt : $arrFmtReportColData));
					$col++;
				}
			}
			++$row;
			
			/*
			 * Excel allows a maximum of 65535 rows
			 */
			if ( $row == 65535 ) {
				$oSheet = $this->createWorksheet($inXls, ++$sheet, $arrFmtReportTitle, $arrFmtReportDesc, $arrFmtReportColHeader, $setSheetNum);
				$row = $sheetRowStart;
				++$setSheetNum;
			}
		}
	}
	
	/**
	 * Creates a format for the specified section
	 * 
	 * @param PHPExcel $inXls
	 * @param reportStyle $inStyle
	 * @param string $inSection
	 * @return array
	 */
	private function createFormat(PHPExcel $inXls, reportStyle $inStyle, $inSection) {
		$style = array(
			'font'    => array(
				'name'      => $inStyle->getStyleAttribute($inSection, reportStyle::STYLE_ATTRIBUTE_FONT, 'Arial'),
				'size'      => $inStyle->getStyleAttribute($inSection, reportStyle::STYLE_ATTRIBUTE_FONT_SIZE, 10),
				'bold'      => $inStyle->getStyleAttribute($inSection, reportStyle::STYLE_ATTRIBUTE_FONT_BOLD, false),
				'italic'    => $inStyle->getStyleAttribute($inSection, reportStyle::STYLE_ATTRIBUTE_FONT_ITALIC, false),
				'underline' => ($inStyle->getStyleAttribute($inSection, reportStyle::STYLE_ATTRIBUTE_FONT_UNDERLINE, false) ? PHPExcel_Style_Font::UNDERLINE_SINGLE : PHPExcel_Style_Font::UNDERLINE_NONE),
				'strike'    => false,
				'color'     => array(
					'rgb' => $inStyle->getStyleAttribute($inSection, reportStyle::STYLE_ATTRIBUTE_COLOUR, '000000')
				)
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array(
					'rgb' => $inStyle->getStyleAttribute($inSection, reportStyle::STYLE_ATTRIBUTE_BACKGROUND_COLOUR, 'ffffff')
				)
			),
		);

		if ( $inStyle->getStyleAttribute($inSection, reportStyle::STYLE_ATTRIBUTE_BORDER, '') ) {
			$border = strtoupper($inStyle->getStyleAttribute($inSection, reportStyle::STYLE_ATTRIBUTE_BORDER, ''));
			$width = $inStyle->getStyleAttribute($inSection, reportStyle::STYLE_ATTRIBUTE_BORDER_SIZE, 1);
			if ( $width > 2 ) {
				$width = 2;
			}
			
			$sharedStyles = array(
				'style' => ($width == 2 ? PHPExcel_Style_Border::BORDER_THICK : PHPExcel_Style_Border::BORDER_THIN),
				'color' => array(
					'rgb' => $inStyle->getStyleAttribute($inSection, reportStyle::STYLE_ATTRIBUTE_BORDER_COLOUR, 'cdcdcd')
				)
			);

			if ( $border == 'A' ) {
				$borders = array(
					'borders' => array(
						'allborders' => $sharedStyles
					)
				);
			} else {
				$borders = array('borders');
				$applyBorders = str_split($border);
				foreach ( $applyBorders as $borderSide ) {
					switch ( $borderSide ) {
						case 'L': $borders['borders']['left']   = $sharedStyles; break;
						case 'R': $borders['borders']['right']  = $sharedStyles; break;
						case 'T': $borders['borders']['top']    = $sharedStyles; break;
						case 'B': $borders['borders']['bottom'] = $sharedStyles; break;
					}
				}
			}
			$style[] = $borders;
		}
		
		return $style;
	}
	
	/**
	 * Creates a new worksheet on the specified Workbook
	 * 
	 * Also appends the title, description and columns and styling.
	 * 
	 * @param PHPExcel $inXls
	 * @param reportBase $inReport
	 * @param integer $inSheetNum
	 * @param array $inTitleFormat
	 * @param array $inDescFormat
	 * @param array $inHeadingFormat
	 * @param integer $inSetSheetNum
	 * @return PHPExcel_Worksheet
	 */
	private function createWorksheet(PHPExcel $inXls, reportBase $inReport, $inSheetNum, array $inTitleFormat, array $inDescFormat, array $inHeadingFormat, $inSetSheetNum = 1) {
		$row = 1;
		$cols = $inReport->getDisplayColumns();
		
		if ( $inSheetNum == 1 ) {
			$oSheet = $inXls->getSheet(0);
		} else {
			$oSheet = $inXls->createSheet($inSheetNum);
		}
		$oSheet->setTitle($inReport->getReportName().' S'.$inSetSheetNum);
		$oSheet->setPrintGridlines(false);
		
		/*
		 * Set column widths
		 */
		$i = 0;
		foreach ( $this->getWidths($inReport) as $colWidth ) {
			$oSheet->getColumnDimensionByColumn($i)->setWidth($colWidth);
			$i++;
		}
		
		/*
		 * Add title
		 */
		$oSheet->getCellByColumnAndRow(0, $row)->setValue(system::getConfig()->getParam('app', 'title', 'Scorpio Framework').': '.$inReport->getReportName());
		$oSheet->getStyle('A1:'.$this->_getColumnLetter(count($cols)).'1')->applyFromArray($inTitleFormat);
		++$row;

		/*
		 * Add description
		 */
		$oSheet->getCellByColumnAndRow(0, $row)->setValue($inReport->getReportDescription());
		$oSheet->getStyle('A2:'.$this->_getColumnLetter(count($cols)).'2')->applyFromArray($inDescFormat);
		++$row;
		++$row;
		
		/*
		 * Add columns
		 */
		$col = 0;
		$oSheet->getStyle('A4:'.$this->_getColumnLetter(count($cols)-1).'4')->applyFromArray($inHeadingFormat);
		foreach ( $cols as $colTitle ) {
			$oSheet->getCellByColumnAndRow($col, $row)->setValue($colTitle);
			$col++;
		}
		return $oSheet;
	}

	/**
	 * Calculates the widths of strings
	 *
	 * @param reportBase $inReport
	 * @return array
	 * @access private
	 */
	private function getWidths(reportBase $inReport) {
		$return = array();
		$cols = $inReport->getDisplayColumns();
		$dataCols = $inReport->getDataColumns();
		
		foreach ( $cols as $col ) {
			$return[] = strlen($col)+2;
		}
		
		/*
		 *  find longest string and calculate width of column
		 */
		foreach ( $inReport->getReportData() as $row ) {
			foreach ( $row as $field => $value ) {
				$key = array_search($field, $dataCols);
				if ( $key !== false ) {
					$width = strlen($value)+2;
					
					if ( $return[$key] < $width ) {
						$return[$key] = $width;
					}
				}
			}
		}
		
		return $return;
	}

	/**
	 * Converts column number to letter format used by Excel
	 *
	 * @param integer $inNumber
	 * @return string
	 */
	private function _getColumnLetter($inNumber) {
		return PHPExcel_Cell::stringFromColumnIndex($inNumber);
	}
}