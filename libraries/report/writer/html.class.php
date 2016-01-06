<?php
/**
 * reportWriterHtml
 * 
 * Stored in reportWriterHtml.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportWriterHtml
 * @version $Rev: 711 $
 */


/**
 * reportWriterHtml
 * 
 * Converts the reportData object into a HTML page with CSS markup. HTML output
 * is XHTML and in tabular format.
 * 
 * @package scorpio
 * @subpackage report
 * @category reportWriterHtml
 */
class reportWriterHtml extends reportWriterBase {
	
	/**
	 * @see reportWriterBase::initialise()
	 */
	function initialise() {
		$this->setExtension('html');
		$this->setMimeType('text/html');
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
		
		$fhndl = fopen($this->getFullPathToOutputFile(), 'wb');
		if ( !$fhndl ) {
			throw new reportWriterOutputFileNotWritableException($this->getFullPathToOutputFile());
		}
		fwrite($fhndl, $this->getHeader());
		
		foreach ( $reports as $oReport ) {
			$cols = $oReport->getDisplayColumns();
			$dataCols = $oReport->getDataColumns();
		
			array_walk($cols, array($this, 'escape'));
		
			fwrite($fhndl, $this->getReportHeader($oReport));
			fwrite($fhndl, '<table cellspacing="0" class="report reportData"><thead><tr><th>'.implode('</th><th>', $cols).'</th></tr></thead>');
			fwrite($fhndl, '<tbody>');
			
			$i = 0;
			foreach ( $oReport->getReportData() as $row ) {
				fwrite($fhndl, '<tr'.($i % 2 != 0 ? ' class="alt"' : '').'>');
				
				foreach ( $dataCols as $field ) {
					if ( array_key_exists($field, $row) ) {
						fwrite($fhndl, '<td>'.htmlentities($row[$field], ENT_COMPAT, 'UTF-8').'</td>');
					}
				}
				
				fwrite($fhndl, '</tr>');
				++$i;
			}
			fwrite($fhndl, '</tbody></table><hr />');
		}
		fwrite($fhndl, $this->getFooter());
		fclose($fhndl);
		return true;
	}
	
	
	
	/**
	 * Escapes text using htmlentities for display
	 *
	 * @param string &$value
	 * @param mixed $key
	 * @param string $prefix
	 * @access private
	 */
	private function escape(&$value, $key, $prefix = '') {
		$value = htmlentities($value, ENT_COMPAT, 'UTF-8');
	}
	
	/**
	 * Returns a set of CSS attributes from the style properties
	 * 
	 * @param array $inStyles
	 * @return string
	 */
	private function createCssStyle(array $inStyles = array()) {
		$return = array();
		if ( isset($inStyles[reportStyle::STYLE_ATTRIBUTE_BACKGROUND_COLOUR]) ) {
			$return[] = 'background-color: #'.$inStyles[reportStyle::STYLE_ATTRIBUTE_BACKGROUND_COLOUR].';';
		}
		if ( isset($inStyles[reportStyle::STYLE_ATTRIBUTE_COLOUR]) ) {
			$return[] = 'color: #'.$inStyles[reportStyle::STYLE_ATTRIBUTE_COLOUR].';';
		}
		if ( isset($inStyles[reportStyle::STYLE_ATTRIBUTE_BORDER]) ) {
			$border = strtoupper($inStyles[reportStyle::STYLE_ATTRIBUTE_BORDER]);
			$width = $inStyles[reportStyle::STYLE_ATTRIBUTE_BORDER_SIZE];
			$colour = $inStyles[reportStyle::STYLE_ATTRIBUTE_BORDER_COLOUR];
			if ( $width > 2 ) {
				$width = 2;
			}
			
			if ( $border == 'A' ) {
				$return[] = 'border: '.$width.'px solid #'.$colour.';';
			} else {
				$borders = str_split($border);
				foreach ( $borders as $border ) {
					switch ( $border ) {
						case 'L': $return[] = 'border-left: '.$width.'px solid #'.$colour.';';   break;
						case 'R': $return[] = 'border-right: '.$width.'px solid #'.$colour.';';  break;
						case 'T': $return[] = 'border-top: '.$width.'px solid #'.$colour.';';    break;
						case 'B': $return[] = 'border-bottom: '.$width.'px solid #'.$colour.';'; break;
					}
				}
			}
		}
		if ( isset($inStyles[reportStyle::STYLE_ATTRIBUTE_FONT]) ) {
			$return[] = 'font-family: '.$inStyles[reportStyle::STYLE_ATTRIBUTE_FONT].';';
		}
		if ( isset($inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_BOLD]) && $inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_BOLD] == true ) {
			$return[] = 'font-weight: bold;';
		}
		if ( isset($inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_ITALIC]) && $inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_ITALIC] == true ) {
			$return[] = 'font-style: italic;';
		}
		if ( isset($inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_SIZE]) ) {
			$return[] = 'font-size: '.$inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_SIZE].'pt;';
		}
		if ( isset($inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_UNDERLINE]) && $inStyles[reportStyle::STYLE_ATTRIBUTE_FONT_UNDERLINE] == true ) {
			$return[] = 'text-decoration: underline;';
		}
		return implode(' ', $return);
	}
	
	/**
	 * Returns the page header text
	 *
	 * @return string
	 */
	private function getHeader() {
		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>'.system::getConfig()->getParam('app', 'title', 'Scorpio Framework').' - '.$this->getReport()->getReportName().'</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<style type="text/css">
		<!--
		html * { margin: 0; padding: 0; font-family: '.$this->getReport()->getReportStyle()->getStyleAttribute(reportStyle::SECTION_DATA, reportStyle::STYLE_ATTRIBUTE_FONT, 'Arial, Verdana, Helvetica, sans-serif').'; }
		body { margin: 10px; font-size: '.$this->getReport()->getReportStyle()->getStyleAttribute(reportStyle::SECTION_PAGE, reportStyle::STYLE_ATTRIBUTE_FONT_SIZE, 12).'px; }
		h1 { margin-bottom: 5px; '.$this->createCssStyle($this->getReport()->getReportStyle()->getStyles(reportStyle::SECTION_TITLE)).' }
		p { margin-bottom: 10px; '.$this->createCssStyle($this->getReport()->getReportStyle()->getStyles(reportStyle::SECTION_DESC)).' }
		table { border: 1px solid #000; padding: 3px; width: 100%; }
		table td { padding: 3px; }
		table thead th { padding: 3px; '.$this->createCssStyle($this->getReport()->getReportStyle()->getStyles(reportStyle::SECTION_HEADING)).' text-align: left; }
		table tbody td { '.$this->createCssStyle($this->getReport()->getReportStyle()->getStyles(reportStyle::SECTION_DATA)).' }
		table tbody tr.alt td { background-color: #'.$this->getReport()->getReportStyle()->getStyleAttribute(reportStyle::SECTION_DATA, reportStyle::STYLE_ATTRIBUTE_ALT_BACKGROUND_COLOUR, 'fcfcfc').'; }
		hr { margin: 15px 0px; padding: 0px; border: 0px; height: 1px; background-color: #000; color: #000; }
		.footer { margin-top: 10px; text-align: center; }
		.footer p { color: #999; font-sytle: italic; }
		.confidential { display: block; font-weight: bold; font-size: 14px; float: left; }
		-->
		</style>
	</head>
	<body bgcolor="#ffffff">
		<h1>'.system::getConfig()->getParam('app', 'title', 'Scorpio Framework').' - '.htmlentities($this->getReport()->getReportName(), ENT_COMPAT, 'UTF-8').'</h1>';
	}
	
	/**
	 * Creates the report header
	 * 
	 * @param reportBase $inReport
	 * @return string
	 */
	private function getReportHeader(reportBase $inReport) {
		return
			'<h2>'.system::getConfig()->getParam('app', 'title', 'Scorpio Framework').' - '.htmlentities($inReport->getReportName(), ENT_COMPAT, 'UTF-8').'</h2>
			<p>'.htmlentities($inReport->getReportDescription(), ENT_COMPAT, 'UTF-8').'</p>';
	}
	
	/**
	 * Returns the page footer text
	 *
	 * @return string
	 */
	private function getFooter() {
		return '
		<div class="footer">
			<span class="confidential">CONFIDENTIAL</span>
			<p>Generated at '.date('H:i:s \o\n d M Y').' | Copyright '.system::getConfig()->getParam('app', 'copyright', 'Scorpio Framework &copy; '.date('Y')).'</p>
		</div>
	</body>
</html>';
	}
}