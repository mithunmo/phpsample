<?php
/**
 * reportWriterPdf
 * 
 * Stored in reportWriterPdf.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportWriterPdf
 * @version $Rev: 771 $
 */


/**
 * reportWriterPdf
 * 
 * Converts the reportData object into an Adobe PDF document via fPDF.
 * 
 * @package scorpio
 * @subpackage report
 * @category reportWriterPdf
 */
class reportWriterPdf extends reportWriterBase {
	
	/**
	 * @see reportWriterBase::initialise()
	 */
	function initialise() {
		$this->setExtension('pdf');
		$this->setMimeType('application/pdf');
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
		
		$oStyle = $this->getReport()->getReportStyle();
		
		/*
		 * Create FPDF instance and set defaults
		 */
		$oPdf = new reportWriterPdfFpdf(
			$oStyle->getStyleAttribute(reportStyle::SECTION_PAGE, reportStyle::STYLE_ATTRIBUTE_PAGE_ORIENTATION, 'L'),
			'mm',
			$oStyle->getStyleAttribute(reportStyle::SECTION_PAGE, reportStyle::STYLE_ATTRIBUTE_PAGE_SIZE, 'letter')
		);
		$oPdf->Open($this->getFullPathToOutputFile());
		
		$oPdf->setReportStyle($oStyle);
		$oPdf->setReportTitle($this->getReport()->getReportName());
		$oPdf->setReportDescription($this->getReport()->getReportDescription());
		$oPdf->SetFont(
			$oStyle->getStyleAttribute(reportStyle::SECTION_PAGE, reportStyle::STYLE_ATTRIBUTE_FONT, 'Arial'),
			$oPdf->getFpdfFontFormatting($oStyle->getStyles(reportStyle::SECTION_PAGE)),
			$oStyle->getStyleAttribute(reportStyle::SECTION_PAGE, reportStyle::STYLE_ATTRIBUTE_FONT_SIZE, 8)
		);
		
		foreach ( $reports as $oReport ) {
			/*
			 * Set page title to current report
			 */
			$oPdf->setReportTitle($oReport->getReportName());
			$oPdf->setReportDescription($oReport->getReportDescription());
			$oPdf->AddPage();
			
			$cols = $oReport->getDisplayColumns();
			$dataCols = $oReport->getDataColumns();
			
			/*
			 * Get column widths based on data
			 */
			$w = $this->getWidths($oPdf, $oReport);
			
			/*
			 * Set column header styles
			 */
			$bg = $oStyle->getStyleColourAsRgb(reportStyle::SECTION_HEADING, reportStyle::STYLE_ATTRIBUTE_BACKGROUND_COLOUR, '6699cc');
			$fc = $oStyle->getStyleColourAsRgb(reportStyle::SECTION_HEADING, reportStyle::STYLE_ATTRIBUTE_COLOUR, 'ffffff');
			
			$oPdf->SetFillColor($bg['r'], $bg['g'], $bg['b']);
			$oPdf->SetTextColor($fc['r'], $fc['g'], $fc['b']);
			$oPdf->SetDrawColor(0);
			$oPdf->SetLineWidth(.3);
			$oPdf->SetFont(
				$oStyle->getStyleAttribute(reportStyle::SECTION_HEADING, reportStyle::STYLE_ATTRIBUTE_FONT, 'Arial'),
				$oPdf->getFpdfFontFormatting($oStyle->getStyles(reportStyle::SECTION_HEADING)),
				$oStyle->getStyleAttribute(reportStyle::SECTION_HEADING, reportStyle::STYLE_ATTRIBUTE_FONT_SIZE, 10)
			);
			
			/*
			 * Attach column headers
			 */
			for ($i=0; $i<count($cols); $i++ ) {
				$oPdf->Cell($w[$i],7,$cols[$i],1,0,'C',true);
			}
			$oPdf->Ln();
			
			/*
			 * Set data styles
			 */
			$altBg = $oStyle->getStyleColourAsRgb(reportStyle::SECTION_DATA, reportStyle::STYLE_ATTRIBUTE_ALT_BACKGROUND_COLOUR, 'cdcdcd');
			$dtCol = $oStyle->getStyleColourAsRgb(reportStyle::SECTION_DATA, reportStyle::STYLE_ATTRIBUTE_COLOUR, '000000');
			$oPdf->SetFillColor($altBg['r'], $altBg['g'], $altBg['b']);
			$oPdf->SetTextColor($dtCol['r'], $dtCol['g'], $dtCol['b']);
			$oPdf->SetFont(
				$oStyle->getStyleAttribute(reportStyle::SECTION_DATA, reportStyle::STYLE_ATTRIBUTE_FONT, 'Arial'),
				$oPdf->getFpdfFontFormatting($oStyle->getStyles(reportStyle::SECTION_DATA)),
				$oStyle->getStyleAttribute(reportStyle::SECTION_DATA, reportStyle::STYLE_ATTRIBUTE_FONT_SIZE, 8)
			);
			
			/*
			 * And now loop over the data and assign it, truncating if too long
			 */
			$fill = false;
			foreach ( $oReport->getReportData() as $row ) {
				foreach ( $dataCols as $field ) {
					$key = array_search($field, $dataCols);
					if ( array_key_exists($field, $row) ) {
						if ( is_numeric($row[$field]) ) {
							$oPdf->Cell($w[$key], 5, $row[$field], 'LR', 0, 'C', $fill);
						} else {
							if ( round($oPdf->GetStringWidth($row[$field]), 0) >= $w[$key] ) {
								$oPdf->Cell($w[$key], 5, substr($row[$field], 0, strlen($cols[$key])).'..', 'LR', 0, 'L', $fill);
							} else {
								$oPdf->Cell($w[$key], 5, $row[$field], 'LR', 0, 'L', $fill);
							}
						}
					}
				}
				$oPdf->Ln();
				$fill = !$fill;
			}
			$oPdf->Cell(array_sum($w), 0, '', 'T');
		}
		
		$oPdf->Output();
		return true;
	}
	
	/**
	 * Calculates the widths of strings
	 *
	 * @param FPDF $inFPDF
	 * @param reportBase $inReport
	 * @return array
	 */
	private function getWidths(FPDF $inFPDF, reportBase $inReport) {
		$return = array();
		$maxWidth = $this->getPageMaxWidth();
		
		$cols = $inReport->getDisplayColumns();
		$dataCols = $inReport->getDataColumns();
		$oStyle = $inReport->getReportStyle();
		
		/*
		 * Calculate default column widths from title styles
		 */
		$inFPDF->SetFont(
			$oStyle->getStyleAttribute(reportStyle::SECTION_HEADING, reportStyle::STYLE_ATTRIBUTE_FONT, 'Arial'),
			$inFPDF->getFpdfFontFormatting($oStyle->getStyles(reportStyle::SECTION_HEADING)),
			$oStyle->getStyleAttribute(reportStyle::SECTION_HEADING, reportStyle::STYLE_ATTRIBUTE_FONT_SIZE, 10)
		);
		foreach ( $cols as $col ) {
			$return[] = round($inFPDF->GetStringWidth($col), 0)+4;
		}
		
		/*
		 * Already reached max width, exit early
		 */
		if ( array_sum($return) >= $maxWidth ) {
			return $return;
		}
		
		/*
		 * Calculate the maximum width for each column based on the data
		 */
		$inFPDF->SetFont(
			$oStyle->getStyleAttribute(reportStyle::SECTION_DATA, reportStyle::STYLE_ATTRIBUTE_FONT, 'Arial'),
			$inFPDF->getFpdfFontFormatting($oStyle->getStyles(reportStyle::SECTION_DATA)),
			$oStyle->getStyleAttribute(reportStyle::SECTION_DATA, reportStyle::STYLE_ATTRIBUTE_FONT_SIZE, 8)
		);
		foreach ( $inReport->getReportData() as $row ) {
			foreach ( $dataCols as $field ) {
				$key = array_search($field, $dataCols);
				if ( array_key_exists($field, $row) ) {
					$width = round($inFPDF->GetStringWidth($row[$field]), 0)+4;
					
					if ( $return[$key] < $width && (array_sum($return)-$return[$key]+$width) < $maxWidth ) {
						$return[$key] = $width;
					}
				}
			}
		}
		
		return $return;
	}
	
	/**
	 * Returns the max width for the current page type and orientation
	 * 
	 * @return integer
	 */
	private function getPageMaxWidth() {
		$widths = array(
			'a3' => array(
				'P' => 270,
				'L' => 390
			),
			'a4' => array(
				'P' => 190,
				'L' => 270
			),
			'a5' => array(
				'P' => 120,
				'L' => 190
			),
			'letter' => array(
				'P' => 190,
				'L' => 260
			),
			'legal' => array(
				'P' => 190,
				'L' => 340
			)
		);
		
		$pagesize = strtolower(
			$this->getReport()
				->getReportStyle()
					->getStyleAttribute(reportStyle::SECTION_PAGE, reportStyle::STYLE_ATTRIBUTE_PAGE_SIZE, 'letter')
		);
		$pageori = strtoupper(
			$this->getReport()
				->getReportStyle()
					->getStyleAttribute(reportStyle::SECTION_PAGE, reportStyle::STYLE_ATTRIBUTE_PAGE_ORIENTATION, 'L')
		);
		
		if ( isset($widths[$pagesize]) && isset($widths[$pagesize][$pageori]) ) {
			return $widths[$pagesize][$pageori];
		} else {
			return 190;
		}
	}
}



/**
 * reportWriterPdfFpdf
 * 
 * Custom implementation of FPDF to handle headers and footers.
 *
 * @package scorpio
 * @subpackage report
 * @category reportWriterPdfFpdf
 */
class reportWriterPdfFpdf extends FPDF {
	
	/**
	 * Stores $_ReportStyle
	 *
	 * @var reportStyle
	 * @access protected
	 */
	protected $_ReportStyle;
	
	/**
	 * Stores $_ReportTitle
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ReportTitle;
	
	/**
	 * Stores $_ReportDescription
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ReportDescription;
	
	
	/**
	 * Writes the header and description on each page automagically
	 * 
	 * @see FPDF::Header()
	 */
	function Header() {
		$this->SetFont(
			$this->getReportStyle()->getStyleAttribute(reportStyle::SECTION_TITLE, reportStyle::STYLE_ATTRIBUTE_FONT, 'Arial'),
			$this->getFpdfFontFormatting($this->getReportStyle()->getStyles(reportStyle::SECTION_TITLE)),
			$this->getReportStyle()->getStyleAttribute(reportStyle::SECTION_TITLE, reportStyle::STYLE_ATTRIBUTE_FONT_SIZE, 16)
		);
		$rgb = $this->getReportStyle()->getStyleColourAsRgb(reportStyle::SECTION_TITLE, reportStyle::STYLE_ATTRIBUTE_COLOUR, '000000');
		$this->SetTextColor($rgb['r'], $rgb['g'], $rgb['b']);
		$this->Cell(0, 10, system::getConfig()->getParam('app', 'title', 'Scorpio Framework').': '.$this->getReportTitle(), 0, 0, 'L');
		$this->Ln(5);
		
		$this->SetFont(
			$this->getReportStyle()->getStyleAttribute(reportStyle::SECTION_DESC, reportStyle::STYLE_ATTRIBUTE_FONT, 'Arial'),
			$this->getFpdfFontFormatting($this->getReportStyle()->getStyles(reportStyle::SECTION_DESC)),
			$this->getReportStyle()->getStyleAttribute(reportStyle::SECTION_DESC, reportStyle::STYLE_ATTRIBUTE_FONT_SIZE, 12)
		);
		$rgb = $this->getReportStyle()->getStyleColourAsRgb(reportStyle::SECTION_DESC, reportStyle::STYLE_ATTRIBUTE_COLOUR, '000000');
		$this->SetTextColor($rgb['r'], $rgb['g'], $rgb['b']);
		
		if ( strlen($this->getReportDescription()) < 130 ) {
			$this->Cell(0, 10, $this->getReportDescription(), 0, 0, 'L');
		} else {
			$text = explode("\n", wordwrap($this->getReportDescription(), 120, "\n"));
			foreach ( $text as $line ) {
				$this->Cell(0, 10, trim($line), 0, 0, 'L');
				$this->Ln(5);
			}
		}
		$this->Ln(10);
	}
	
	/**
	 * Writes the footer on each page automagically
	 * 
	 * @see FPDF::Footer()
	 */
	function Footer() {
		$this->SetY(-10);
		$this->SetFont('Arial','B',9);
		$text = 'CONFIDENTIAL';
		$this->Cell($this->GetStringWidth($text), 10, $text, 0, 0, 'L');
		
		$this->SetFont('Arial','I',8);
		$text = 'Generated at '.date('H:i:s \o\n d M Y').' | Copyright '.system::getConfig()->getParam('app', 'copyright', 'Scorpio Framework (C) '.date('Y'));
		$this->Cell(0, 10, $text, 0, 0, 'C');
		
		$this->Cell(0, 10, 'Page '.$this->PageNo(),0,0,'R');
	}

	/**
	 * Returns a text string for FPDF font formatting
	 * 
	 * @param array $inStyles
	 * @return string
	 */
	function getFpdfFontFormatting(array $inStyles = array()) {
		$return = '';
		if ( isset($inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_BOLD]) && $inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_BOLD] == true ) {
			$return .= 'B';
		}
		if ( isset($inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_ITALIC]) && $inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_ITALIC] == true ) {
			$return .= 'I';
		}
		if ( isset($inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_UNDERLINE]) && $inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_UNDERLINE] == true ) {
			$return .= 'U';
		}
		return $return;
	}
	
	
	
	/**
	 * Returns $_ReportStyle
	 *
	 * @return reportStyle
	 * @access public
	 */
	function getReportStyle() {
		return $this->_ReportStyle;
	}
	
	/**
	 * Set $_ReportStyle to $inReportStyle
	 *
	 * @param reportStyle $inReportStyle
	 * @return reportWriterPdfFpdf
	 * @access public
	 */
	function setReportStyle($inReportStyle) {
		if ( $this->_ReportStyle !== $inReportStyle ) {
			$this->_ReportStyle = $inReportStyle;
		}
		return $this;
	}

	/**
	 * Returns $_ReportTitle
	 *
	 * @return string
	 */
	function getReportTitle() {
		return $this->_ReportTitle;
	}
	
	/**
	 * Set $_ReportTitle to $inReportTitle
	 *
	 * @param string $inReportTitle
	 * @return reportWriterPdfFpdf
	 */
	function setReportTitle($inReportTitle) {
		if ( $inReportTitle !== $this->_ReportTitle ) {
			$this->_ReportTitle = $inReportTitle;
		}
		return $this;
	}

	/**
	 * Returns $_ReportDescription
	 *
	 * @return string
	 */
	function getReportDescription() {
		return $this->_ReportDescription;
	}
	
	/**
	 * Set $_ReportDescription to $inReportDescription
	 *
	 * @param string $inReportDescription
	 * @return reportWriterPdfFpdf
	 */
	function setReportDescription($inReportDescription) {
		if ( $inReportDescription !== $this->_ReportDescription ) {
			$this->_ReportDescription = $inReportDescription;
		}
		return $this;
	}
	
	
	
	/**
	 * FPDF page-by-page output extension
	 * @author Oliver <oliver@fpdf.org>
	 * @license FPDF
	 */
	
	public $f;

	function Open($file) {
		if ( FPDF_VERSION < '1.6' )
			$this->Error('Version 1.6 or above is required by this extension');
		$this->f = fopen($file, 'wb');
		if ( !$this->f )
			$this->Error('Unable to create output file: ' . $file);
		parent::Open();
		$this->_putheader();
	}

	function Image($file, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '') {
		if ( !isset($this->images[$file]) ) {
			//Retrieve only meta-information
			$a = getimagesize($file);
			if ( $a === false )
				$this->Error('Missing or incorrect image file: ' . $file);
			$this->images[$file] = array('w' => $a[0], 'h' => $a[1], 'type' => $a[2], 'i' => count($this->images) + 1);
		}
		parent::Image($file, $x, $y, $w, $h, $type, $link);
	}

	function Output() {
		if ( $this->state < 3 )
			$this->Close();
	}

	function _endpage() {
		parent::_endpage();
		//Write page to file
		$filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
		$p = ($this->compress) ? gzcompress($this->buffer) : $this->buffer;
		$this->_newobj();
		$this->_out('<<' . $filter . '/Length ' . strlen($p) . '>>');
		$this->_putstream($p);
		$this->_out('endobj');
		$this->buffer = '';
	}

	function _newobj() {
		$this->n++;
		$this->offsets[$this->n] = ftell($this->f);
		$this->_out($this->n . ' 0 obj');
	}

	function _out($s) {
		if ( $this->state == 2 )
			$this->buffer .= $s . "\n";
		else
			fwrite($this->f, $s . "\n", strlen($s) + 1);
	}

	function _putimages() {
		$filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
		reset($this->images);
		while ( list($file, $info) = each($this->images) ) {
			//Load image
			if ( $info['type'] == 1 )
				$info = $this->_parsegif($file);
			elseif ( $info['type'] == 2 )
				$info = $this->_parsejpg($file);
			elseif ( $info['type'] == 3 )
				$info = $this->_parsepng($file);
			else
				$this->Error('Unsupported image type: ' . $file);
			
		//Put it into file
			$this->_newobj();
			$this->images[$file]['n'] = $this->n;
			$this->_out('<</Type /XObject');
			$this->_out('/Subtype /Image');
			$this->_out('/Width ' . $info['w']);
			$this->_out('/Height ' . $info['h']);
			if ( $info['cs'] == 'Indexed' )
				$this->_out('/ColorSpace [/Indexed /DeviceRGB ' . (strlen($info['pal']) / 3 - 1) . ' ' . ($this->n + 1) . ' 0 R]');
			else {
				$this->_out('/ColorSpace /' . $info['cs']);
				if ( $info['cs'] == 'DeviceCMYK' )
					$this->_out('/Decode [1 0 1 0 1 0 1 0]');
			}
			$this->_out('/BitsPerComponent ' . $info['bpc']);
			if ( isset($info['f']) )
				$this->_out('/Filter /' . $info['f']);
			if ( isset($info['parms']) )
				$this->_out($info['parms']);
			if ( isset($info['trns']) && is_array($info['trns']) ) {
				$trns = '';
				for ( $i = 0; $i < count($info['trns']); $i++ )
					$trns .= $info['trns'][$i] . ' ' . $info['trns'][$i] . ' ';
				$this->_out('/Mask [' . $trns . ']');
			}
			$this->_out('/Length ' . strlen($info['data']) . '>>');
			$this->_putstream($info['data']);
			unset($info['data']);
			$this->_out('endobj');
			//Palette
			if ( $info['cs'] == 'Indexed' ) {
				$this->_newobj();
				$pal = ($this->compress) ? gzcompress($info['pal']) : $info['pal'];
				$this->_out('<<' . $filter . '/Length ' . strlen($pal) . '>>');
				$this->_putstream($pal);
				$this->_out('endobj');
			}
		}
	}

	function _putpages() {
		$nb = $this->page;
		if ( $this->DefOrientation == 'P' ) {
			$wPt = $this->DefPageFormat[0] * $this->k;
			$hPt = $this->DefPageFormat[1] * $this->k;
		} else {
			$wPt = $this->DefPageFormat[1] * $this->k;
			$hPt = $this->DefPageFormat[0] * $this->k;
		}
		//Page objects
		for ( $n = 1; $n <= $nb; $n++ ) {
			$this->_newobj();
			$this->_out('<</Type /Page');
			$this->_out('/Parent 1 0 R');
			if ( isset($this->PageSizes[$n]) )
				$this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]', $this->PageSizes[$n][0], $this->PageSizes[$n][1]));
			$this->_out('/Resources 2 0 R');
			if ( isset($this->PageLinks[$n]) ) {
				//Links
				$annots = '/Annots [';
				foreach ( $this->PageLinks[$n] as $pl ) {
					$rect = sprintf('%.2F %.2F %.2F %.2F', $pl[0], $pl[1], $pl[0] + $pl[2], $pl[1] - $pl[3]);
					$annots .= '<</Type /Annot /Subtype /Link /Rect [' . $rect . '] /Border [0 0 0] ';
					if ( is_string($pl[4]) )
						$annots .= '/A <</S /URI /URI ' . $this->_textstring($pl[4]) . '>>>>';
					else {
						$l = $this->links[$pl[4]];
						$h = isset($this->PageSizes[$l[0]]) ? $this->PageSizes[$l[0]][1] : $hPt;
						$annots .= sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]>>', 2 + $nb + $l[0], $h - $l[1] * $this->k);
					}
				}
				$this->_out($annots . ']');
			}
			$this->_out('/Contents ' . (2 + $n) . ' 0 R>>');
			$this->_out('endobj');
		}
		//Pages root
		$this->offsets[1] = ftell($this->f);
		$this->_out('1 0 obj');
		$this->_out('<</Type /Pages');
		$kids = '/Kids [';
		for ( $n = 1; $n <= $nb; $n++ )
			$kids .= (2 + $nb + $n) . ' 0 R ';
		$this->_out($kids . ']');
		$this->_out('/Count ' . $nb);
		$this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]', $wPt, $hPt));
		$this->_out('>>');
		$this->_out('endobj');
	}

	function _putresources() {
		$this->_putfonts();
		$this->_putimages();
		//Resource dictionary
		$this->offsets[2] = ftell($this->f);
		$this->_out('2 0 obj');
		$this->_out('<<');
		$this->_putresourcedict();
		$this->_out('>>');
		$this->_out('endobj');
	}

	function _putcatalog() {
		$this->_out('/Type /Catalog');
		$this->_out('/Pages 1 0 R');
		$n = 3 + $this->page;
		if ( $this->ZoomMode == 'fullpage' )
			$this->_out('/OpenAction [' . $n . ' 0 R /Fit]');
		elseif ( $this->ZoomMode == 'fullwidth' )
			$this->_out('/OpenAction [' . $n . ' 0 R /FitH null]');
		elseif ( $this->ZoomMode == 'real' )
			$this->_out('/OpenAction [' . $n . ' 0 R /XYZ null null 1]');
		elseif ( !is_string($this->ZoomMode) )
			$this->_out('/OpenAction [' . $n . ' 0 R /XYZ null null ' . ($this->ZoomMode / 100) . ']');
		if ( $this->LayoutMode == 'single' )
			$this->_out('/PageLayout /SinglePage');
		elseif ( $this->LayoutMode == 'continuous' )
			$this->_out('/PageLayout /OneColumn');
		elseif ( $this->LayoutMode == 'two' )
			$this->_out('/PageLayout /TwoColumnLeft');
	}

	function _enddoc() {
		$this->_putpages();
		$this->_putresources();
		//Info
		$this->_newobj();
		$this->_out('<<');
		$this->_putinfo();
		$this->_out('>>');
		$this->_out('endobj');
		//Catalog
		$this->_newobj();
		$this->_out('<<');
		$this->_putcatalog();
		$this->_out('>>');
		$this->_out('endobj');
		//Cross-ref
		$o = ftell($this->f);
		$this->_out('xref');
		$this->_out('0 ' . ($this->n + 1));
		$this->_out('0000000000 65535 f ');
		for ( $i = 1; $i <= $this->n; $i++ )
			$this->_out(sprintf('%010d 00000 n ', $this->offsets[$i]));
		
		//Trailer
		$this->_out('trailer');
		$this->_out('<<');
		$this->_puttrailer();
		$this->_out('>>');
		$this->_out('startxref');
		$this->_out($o);
		$this->_out('%%EOF');
		$this->state = 3;
		fclose($this->f);
	}
}