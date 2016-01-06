<?php
/**
 * ods-php a library to read and write ods files from php.
 * 
 * This library has been forked from eyeOS project and licended under the LGPL3
 * terms available at: http://www.gnu.org/licenses/lgpl-3.0.txt (relicenced
 * with permission of the copyright holders)
 * 
 * Copyright: Juan Lao Tebar (juanlao@eyeos.org) and Jose Carlos Norte (jose@eyeos.org) - 2008
 * 
 * https://sourceforge.net/projects/ods-php/
 */


/**
 * ods
 * 
 * Class for reading and writing OpenDocument Spreadsheet files.
 * 
 * Example code to create an ODS file:
 * <code>
 * $oOds = new ods();
 * $oOds->addCell(0,0,0,1,'float'); //add a cell to sheet 0, row 0, cell 0, with value 1 and type float
 * $oOds->addCell(0,0,1,2,'float'); //add a cell to sheet 0, row 0, cell 1, with value 1 and type float
 * $oOds->addCell(0,1,0,1,'float'); //add a cell to sheet 0, row 1, cell 0, with value 1 and type float
 * $oOds->addCell(0,1,1,2,'float'); //add a cell to sheet 0, row 1, cell 1, with value 1 and type float
 * $oOds->save('/path/to/file.ods', 'en-US');
 * </code>
 * 
 * Example code to read a previously created ODS file:
 * <code>
 * $oOds = new ods();
 * $oOds->loadFromFile('/path/to/file.ods');
 * // do stuff with data
 * </code>
 * 
 * Changes made by Dave Redfern:
 * 
 * <ul>
 *   <li>Better PHP5 compatibility</li>
 *   <li>Moved save/load into class</li>
 *   <li>Added loadFromFile / loadFromString methods</li>
 *   <li>Added docblock comments</li>
 *   <li>Re-built save mechanism to use ZipArchive extension</li>
 *   <li>Re-formatted XML blocks for clarity</li>
 *   <li>Changed the Spanish defaults to en-US</li>
 *   <li>Replaced Spanish names with English</li>
 *   <li>Made parsing and XML methods private to clean-up interface</li>
 * </ul>
 * 
 * @copyright Juan Lao Tebar (juanlao@eyeos.org) and Jose Carlos Norte (jose@eyeos.org) - 2008
 * @author Juan Lao Tebar (juanlao@eyeos.org
 * @author Jose Carlos Norte (jose@eyeos.org)
 * @author Dave Redfern 
 * @package ods
 * @subpackage ods
 */
class ods {
	
	/**
	 * Stores $_Filename
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Filename;
	
	/**
	 * Stores $_Language
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Language;
	
	/**
	 * An array of font definitions
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_fonts;
	
	/**
	 * An array of style information
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_styles;
	
	/**
	 * An array of sheets in the current document
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_sheets;
	
	/**
	 * Last element processed
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_lastElement;
	
	/**
	 * Current sheet reference
	 * 
	 * @var integer
	 * @access protected
	 */
	protected $_currentSheet;
	
	/**
	 * Current row in the sheet
	 * 
	 * @var integer
	 * @access protected
	 */
	protected $_currentRow;
	
	/**
	 * Current cell in the sheet
	 * 
	 * @var integer
	 * @access protected
	 */
	protected $_currentCell;
	
	/**
	 * Last row attribute
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_lastRowAtt;
	
	/**
	 * How many repeats for the current cell (merged cells?)
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_repeat;
	
	
	
	/**
	 * Creates a new ODS object, if $inFilename is specified and exists it will be loaded
	 * 
	 * @param string $inFilename Filename to open / save to
	 * @param string $inLanguage Default language to use in ODS file
	 * @return ods
	 */
	function __construct($inFilename = null, $inLanguage = 'en-US') {
		if ( !extension_loaded('zip') ) {
			throw new Exception('ods requires ZIP extension');
		}
		
		$this->_styles = array();
		$this->_fonts = array();
		$this->_sheets = array();
		$this->_currentRow = 0;
		$this->_currentSheet = 0;
		$this->_currentCell = 0;
		$this->_repeat = 0;
		$this->_Filename = null;
		$this->_Language = null;
		
		if ( $inFilename !== null ) {
			$this->setFilename($inFilename);
		}
		if ( $inLanguage !== null ) {
			$this->setLanguage($inLanguage);
		}
		if ( is_readable($this->getFilename()) ) {
			$this->loadFromFile($this->getFilename());
		}
	}
	
	
	
	/**
	 * Returns a new empty ODS instance
	 * 
	 * @param string $inFilename Optional filename to load / set
	 * @return ods
	 * @static
	 */
	static function getInstance($inFilename = null) {
		$content = '<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" 
	xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" 
	xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
	xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" 
	xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" 
	xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"
	xmlns:xlink="http://www.w3.org/1999/xlink" 
	xmlns:dc="http://purl.org/dc/elements/1.1/" 
	xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" 
	xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"
	xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" 
	xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" 
	xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0"
	xmlns:math="http://www.w3.org/1998/Math/MathML" 
	xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" 
	xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" 
	xmlns:ooo="http://openoffice.org/2004/office"
	xmlns:ooow="http://openoffice.org/2004/writer" 
	xmlns:oooc="http://openoffice.org/2004/calc" 
	xmlns:dom="http://www.w3.org/2001/xml-events" 
	xmlns:xforms="http://www.w3.org/2002/xforms" 
	xmlns:xsd="http://www.w3.org/2001/XMLSchema"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0">
	<office:scripts />
	<office:font-face-decls>
		<style:font-face style:name="Liberation Sans" svg:font-family="&apos;Liberation Sans&apos;" style:font-family-generic="swiss" style:font-pitch="variable" />
		<style:font-face style:name="DejaVu Sans" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="system" style:font-pitch="variable" />
	</office:font-face-decls>
	<office:automatic-styles>
		<style:style style:name="co1" style:family="table-column">
			<style:table-column-properties fo:break-before="auto" style:column-width="2.267cm" />
		</style:style>
		<style:style style:name="ro1" style:family="table-row">
			<style:table-row-properties style:row-height="0.453cm" fo:break-before="auto" style:use-optimal-row-height="true" />
		</style:style>
		<style:style style:name="ta1" style:family="table" style:master-page-name="Default">
			<style:table-properties table:display="true" style:writing-mode="lr-tb" />
		</style:style>
	</office:automatic-styles>
	<office:body>
		<office:spreadsheet>
			<table:table table:name="Sheet1" table:style-name="ta1" table:print="false">
				<office:forms form:automatic-focus="false" form:apply-design-mode="false" />
				<table:table-column table:style-name="co1" table:default-cell-style-name="Default" />
				<table:table-row table:style-name="ro1">
					<table:table-cell />
				</table:table-row>
			</table:table>
			<table:table table:name="Sheet2" table:style-name="ta1" table:print="false">
				<table:table-column table:style-name="co1" table:default-cell-style-name="Default" />
				<table:table-row table:style-name="ro1">
					<table:table-cell />
				</table:table-row>
			</table:table>
			<table:table table:name="Sheet3" table:style-name="ta1" table:print="false">
				<table:table-column table:style-name="co1" table:default-cell-style-name="Default" />
				<table:table-row table:style-name="ro1">
					<table:table-cell />
				</table:table-row>
			</table:table>
		</office:spreadsheet>
	</office:body>
</office:document-content>';
		
		$obj = new ods($inFilename);
		if ( $inFilename === null ) {
			$obj->loadFromString($content);
		}	
		return $obj;
	}
	
	
	
	/**
	 * Loads the data from $inFile into the class, requires zip:// fopen wrapper
	 * 
	 * @param string $inFile Path to the file to open
	 * @return ods
	 */
	function loadFromFile($inFile) {
		$this->_parse(file_get_contents('zip://' . $inFile . '#content.xml'));
	}
	
	/**
	 * Loads the data from $inXmlString into the class
	 * 
	 * @param string $inXmlString XML string to load into ODS object
	 * @return ods
	 */
	function loadFromString($inXmlString) {
		$this->_parse($inXmlString);
	}
	
	/**
	 * Saves the current data to $inFile location
	 * 
	 * @param string $inFile Alternative file location if not already set
	 * @param string $inLanguage Alternative locale string for the meta data e.g. en-GB
	 * @return boolean
	 * @throws Exception
	 */
	function save($inFile = null, $inLanguage = null) {
		if ( $inLanguage !== null ) {
			$this->setLanguage($inLanguage);
		}
		if ( $inFile === null && $this->getFilename() === null ) {
			throw new Exception('ods::save requires a filename to save to');
		}
		if ( $inFile === null && $this->getFilename() ) {
			$inFile = $this->getFilename();
		}
		
		$charset = ini_get('default_charset');
		ini_set('default_charset', 'UTF-8');
		
		$oZIP = new ZipArchive();
		if ( $oZIP->open($inFile, ZipArchive::OVERWRITE) !== true ) {
			throw new Exception("ZipArchive: Failed to open $inFile for writing");
		}
		$oZIP->addFromString('content.xml', $this->_arrayToOds());
		$oZIP->addFromString('mimetype', 'application/vnd.oasis.opendocument.spreadsheet');
		$oZIP->addFromString('meta.xml', $this->_getMeta($this->getLanguage()));
		$oZIP->addFromString('styles.xml', $this->_getStyle());
		$oZIP->addFromString('settings.xml', $this->_getSettings());
		
		$oZIP->addEmptyDir('META-INF');
		$oZIP->addEmptyDir('Configurations2');
		$oZIP->addEmptyDir('Configurations2/acceleator');
		$oZIP->addEmptyDir('Configurations2/images');
		$oZIP->addEmptyDir('Configurations2/popupmenu');
		$oZIP->addEmptyDir('Configurations2/statusbar');
		$oZIP->addEmptyDir('Configurations2/floater');
		$oZIP->addEmptyDir('Configurations2/menubar');
		$oZIP->addEmptyDir('Configurations2/progressbar');
		$oZIP->addEmptyDir('Configurations2/toolbar');
		
		$oZIP->addFromString('META-INF/manifest.xml', $this->_getManifest());
		$res = $oZIP->close();
		
		ini_set('default_charset',$charset);
		
		return $res;
	}

	/**
	 * Adds a cell to the specified sheet
	 * 
	 * @param integer $sheet Sheet number in the ODS workbook
	 * @param integer $row Row number
	 * @param integer $cell Cell number
	 * @param mixed $value The value for the cell
	 * @param mixed $type The cell format type (e.g. string, integer, float)
	 * @return ods
	 */
	function addCell($sheet, $row, $cell, $value, $type) {
		$this->_sheets[$sheet]['rows'][$row][$cell]['attrs'] = array('OFFICE:VALUE-TYPE'=>$type,'OFFICE:VALUE'=>$value);
		$this->_sheets[$sheet]['rows'][$row][$cell]['value'] = $value;
		return $this;
	}
	
	/**
	 * Edit the cell contents, must be same format as loaded cell
	 * 
	 * @param integer $sheet Sheet number in the ODS workbook
	 * @param integer $row Row number
	 * @param integer $cell Cell number
	 * @param mixed $value The value for the cell
	 * @return ods
	 */
	function editCell($sheet, $row, $cell, $value) {
		$this->_sheets[$sheet]['rows'][$row][$cell]['attrs']['OFFICE:VALUE'] = $value;
		$this->_sheets[$sheet]['rows'][$row][$cell]['value'] = $value;
		return $this;
	}
	
	/**
	 * Removes the cell from the specified sheet at row and cell.
	 * 
	 * @param integer $sheet Sheet number in the ODS workbook
	 * @param integer $row Row number
	 * @param integer $cell Cell number
	 * @return ods
	 */
	function removeCell($sheet, $row, $cell) {
		if ( isset($this->_sheets[$sheet]['rows'][$row][$cell]) ) {
			unset($this->_sheets[$sheet]['rows'][$row][$cell]);
		}
		return $this;
	}
	
	/**
	 * Returns $_Filename
	 *
	 * @return string
	 */
	function getFilename() {
		return $this->_Filename;
	}
	
	/**
	 * Set $_Filename to $inFilename
	 *
	 * @param string $inFilename
	 * @return ods
	 */
	function setFilename($inFilename) {
		if ( $inFilename !== $this->_Filename ) {
			$this->_Filename = $inFilename;
		}
		return $this;
	}

	/**
	 * Returns $_Language
	 *
	 * @return string
	 */
	function getLanguage() {
		return $this->_Language;
	}
	
	/**
	 * Set language for file, language should be specified as xx-YY e.g. en-GB, es-ES
	 *
	 * @param string $inLanguage
	 * @return ods
	 */
	function setLanguage($inLanguage) {
		if ( $inLanguage !== $this->_Language ) {
			$this->_Language = str_replace('_', '-', $inLanguage);
		}
		return $this;
	}
	
	
	
	/**
	 * Converts the internal arrays into an ODS data stream
	 * 
	 * @return string
	 * @access private
	 */
	private function _arrayToOds() {
		$fontArray = $this->_fonts;
		$styleArray = $this->_styles;
		$sheetArray = $this->_sheets;
		// Header
		$string  = '<?xml version="1.0" encoding="UTF-8"?><office:document-content ';
		$string .= 'xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" ';
		$string .= 'xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" ';
		$string .= 'xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" ';
		$string .= 'xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" ';
		$string .= 'xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" ';
		$string .= 'xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" ';
		$string .= 'xmlns:xlink="http://www.w3.org/1999/xlink" ';
		$string .= 'xmlns:dc="http://purl.org/dc/elements/1.1/" ';
		$string .= 'xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" ';
		$string .= 'xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" ';
		$string .= 'xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" ';
		$string .= 'xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" ';
		$string .= 'xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" ';
		$string .= 'xmlns:math="http://www.w3.org/1998/Math/MathML" ';
		$string .= 'xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" ';
		$string .= 'xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" ';
		$string .= 'xmlns:ooo="http://openoffice.org/2004/office" ';
		$string .= 'xmlns:ooow="http://openoffice.org/2004/writer" ';
		$string .= 'xmlns:oooc="http://openoffice.org/2004/calc" ';
		$string .= 'xmlns:dom="http://www.w3.org/2001/xml-events" ';
		$string .= 'xmlns:xforms="http://www.w3.org/2002/xforms" ';
		$string .= 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ';
		$string .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0">';
		
		// ToDo: scripts
		$string .= '<office:scripts/>';
		
		// Fonts
		$string .= '<office:font-face-decls>';
		foreach ($fontArray as $fontName => $fontAttribs) {
			$string .= '<style:font-face ';
			foreach ($fontAttribs as $attrName => $attrValue) {
				$string .= strtolower($attrName) . '="' . $attrValue . '" ';
			}
			$string .= '/>';
		}
		$string .= '</office:font-face-decls>';
		
		// Styles
		$string .= '<office:automatic-styles>';
		foreach ($styleArray as $styleName => $styleAttribs) {
			$string .= '<style:style ';
			foreach ($styleAttribs['attrs'] as $attrName => $attrValue) {
				$string .= strtolower($attrName) . '="' . $attrValue . '" ';
			}
			$string .= '>';
			
			// Subnodes
			foreach ($styleAttribs['styles'] as $nodeName => $nodeTree) {
				$string .= '<' . $nodeName . ' ';
				foreach ($nodeTree as $attrName => $attrValue) {
					$string .= strtolower($attrName) . '="' . $attrValue . '" ';
				}
				$string .= '/>';
			}
			
			$string .= '</style:style>';
		}
		$string .= '</office:automatic-styles>';
		
		// Body
		$string .= '<office:body>';
		$string .= '<office:spreadsheet>';
		foreach ($sheetArray as $tableIndex => $tableContent) {
			$string .= '<table:table table:name="Sheet ' . $tableIndex . '" table:print="false">';
			
			foreach ($tableContent['rows'] as $rowIndex => $rowContent) {
				$string .= '<table:table-row>';
				
				foreach($rowContent as $cellIndex => $cellContent) {
					$string .= '<table:table-cell ';
					foreach ($cellContent['attrs'] as $attrName => $attrValue) {
						$string .= strtolower($attrName) . '="' . $attrValue . '" ';
					}
					$string .= '>';
					
					if (isset($cellContent['value'])) {
						$string .= '<text:p>' . $cellContent['value'] . '</text:p>';
					}
					
					$string .= '</table:table-cell>';
				}
				
				$string .= '</table:table-row>';
			}
			
			$string .= '</table:table>';
		}
		
		$string .= '</office:spreadsheet>';
		$string .= '</office:body>';
		
		// Footer
		$string .= '</office:document-content>';
		
		return $string;
	}
	
	
	
	/**
	 * Parses the ODS XML data into a structure that can be manipulated by PHP
	 * 
	 * @param string $data
	 * @return void
	 * @access private
	 */
	private function _parse($data) {
		$xml_parser = xml_parser_create(); 
		xml_set_object($xml_parser, $this);
		xml_set_element_handler($xml_parser, '_startElement', '_endElement');
		xml_set_character_data_handler($xml_parser, '_characterData');

		xml_parse($xml_parser, $data, strlen($data));

		xml_parser_free($xml_parser);
	}
	
	/**
	 * XML Parser start element handler
	 * 
	 * @param XMLParser $parser XMLParser resource handle
	 * @param string $tagName
	 * @param mixed $attrs
	 * @return void
	 * @access private
	 */
	private function _startElement($parser, $tagName, $attrs) {
		$cTagName = strtolower($tagName);
		if($cTagName == 'style:font-face') {
			$this->_fonts[$attrs['STYLE:NAME']] = $attrs;
		} elseif($cTagName == 'style:style') {
			$this->_lastElement = $attrs['STYLE:NAME'];
			$this->_styles[$this->_lastElement]['attrs'] = $attrs;
		} elseif($cTagName == 'style:table-column-properties' || $cTagName == 'style:table-row-properties' 
			|| $cTagName == 'style:table-properties' || $cTagName == 'style:text-properties') {
			$this->_styles[$this->_lastElement]['styles'][$cTagName] = $attrs;
		} elseif($cTagName == 'table:table-cell') {
			$this->_lastElement = $cTagName;
			$this->_sheets[$this->_currentSheet]['rows'][$this->_currentRow][$this->_currentCell]['attrs'] = $attrs;
			if(isset($attrs['TABLE:NUMBER-COLUMNS-REPEATED'])) {
				$times = intval($attrs['TABLE:NUMBER-COLUMNS-REPEATED']);
				$times--;
				for($i=1;$i<=$times;$i++) {
					$cnum = $this->_currentCell+$i;
					$this->_sheets[$this->_currentSheet]['rows'][$this->_currentRow][$cnum]['attrs'] = $attrs;
				}
				$this->_currentCell += $times;
				$this->_repeat = $times;
			}
			if(isset($this->_lastRowAtt['TABLE:NUMBER-ROWS-REPEATED'])) {
				$times = intval($this->_lastRowAtt['TABLE:NUMBER-ROWS-REPEATED']);
				$times--;
				for($i=1;$i<=$times;$i++) {
					$cnum = $this->_currentRow+$i;
					$this->_sheets[$this->_currentSheet]['rows'][$cnum][$i-1]['attrs'] = $attrs;
				}
				$this->_currentRow += $times;
			}
		} elseif($cTagName == 'table:table-row') {
			$this->_lastRowAtt = $attrs;
		}
	}
	
	/**
	 * XML Parser end element handler
	 * 
	 * @param XMLParser $parser XMLParser resource handle
	 * @param string $tagName
	 * @return void
	 * @access private
	 */
	private function _endElement($parser, $tagName) {
		$cTagName = strtolower($tagName);
		if($cTagName == 'table:table') {
			$this->_currentSheet++;
			$this->_currentRow = 0;
		} elseif($cTagName == 'table:table-row') {
			$this->_currentRow++;
			$this->_currentCell = 0;
		} elseif($cTagName == 'table:table-cell') {
			$this->_currentCell++;
			$this->_repeat = 0;
		}
	}
	
	/**
	 * Character data handler
	 * 
	 * @param XMLParser $parser XMLParser resource handle
	 * @param mixed $data
	 * @return void
	 * @access private
	 */
	private function _characterData($parser, $data) {
		if($this->_lastElement == 'table:table-cell') {
			$this->_sheets[$this->_currentSheet]['rows'][$this->_currentRow][$this->_currentCell]['value'] .= $data;
			if($this->_repeat > 0) {
				for($i=0;$i<$this->_repeat;$i++) {
					$cnum = $this->_currentCell - ($i+1);
					$this->_sheets[$this->_currentSheet]['rows'][$this->_currentRow][$cnum]['value'] .= $data;
				}
			}
		}
	}
	
	
	
	/**
	 * Returns the meta data XML descriptor
	 * 
	 * @param string $lang
	 * @return string
	 * @access private
	 */
	private function _getMeta($lang) {
		$myDate = date('Y-m-j\TH:i:s');
		$meta = '<?xml version="1.0" encoding="UTF-8"?>
		<office:document-meta xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" 
		xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" 
		xmlns:ooo="http://openoffice.org/2004/office" office:version="1.0">
			<office:meta>
				<meta:generator>ods-php</meta:generator>
				<meta:creation-date>'.$myDate.'</meta:creation-date>
				<dc:date>'.$myDate.'</dc:date>
				<dc:language>'.$lang.'</dc:language>
				<meta:editing-cycles>2</meta:editing-cycles>
				<meta:editing-duration>PT15S</meta:editing-duration>
				<meta:user-defined meta:name="Info 1"/>
				<meta:user-defined meta:name="Info 2"/>
				<meta:user-defined meta:name="Info 3"/>
				<meta:user-defined meta:name="Info 4"/>
			</office:meta>
		</office:document-meta>';
		return $meta;
	}
	
	/**
	 * Returns the style XML descriptor
	 * 
	 * @return string
	 * @access private
	 */
	private function _getStyle() {
		return '<?xml version="1.0" encoding="UTF-8"?>
<office:document-styles xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
	xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" 
	xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
	xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" 
	xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" 
	xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"
	xmlns:xlink="http://www.w3.org/1999/xlink" 
	xmlns:dc="http://purl.org/dc/elements/1.1/" 
	xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" 
	xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"
	xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" 
	xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" 
	xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0"
	xmlns:math="http://www.w3.org/1998/Math/MathML" 
	xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" 
	xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" 
	xmlns:ooo="http://openoffice.org/2004/office"
	xmlns:ooow="http://openoffice.org/2004/writer" 
	xmlns:oooc="http://openoffice.org/2004/calc" 
	xmlns:dom="http://www.w3.org/2001/xml-events" office:version="1.0">
	<office:font-face-decls>
		<style:font-face style:name="Liberation Sans" svg:font-family="&apos;Liberation Sans&apos;" style:font-family-generic="swiss" style:font-pitch="variable" />
		<style:font-face style:name="DejaVu Sans" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="system" style:font-pitch="variable" />
	</office:font-face-decls>
	<office:styles>
		<style:default-style style:family="table-cell">
			<style:table-cell-properties style:decimal-places="2" />
			<style:paragraph-properties style:tab-stop-distance="1.25cm" />
			<style:text-properties style:font-name="Liberation Sans" fo:language="en" fo:country="US" style:font-name-asian="DejaVu Sans" style:language-asian="zxx" style:country-asian="none"
				style:font-name-complex="DejaVu Sans" style:language-complex="zxx" style:country-complex="none" />
		</style:default-style>
		<number:number-style style:name="N0">
			<number:number number:min-integer-digits="1" />
		</number:number-style>
		<number:currency-style style:name="N103P0" style:volatile="true">
			<number:number number:decimal-places="2" number:min-integer-digits="1" number:grouping="true" />
			<number:text> </number:text>
			<number:currency-symbol number:language="en" number:country="US">$</number:currency-symbol>
		</number:currency-style>
		<number:currency-style style:name="N103">
			<style:text-properties fo:color="#ff0000" />
			<number:text>-</number:text>
			<number:number number:decimal-places="2" number:min-integer-digits="1" number:grouping="true" />
			<number:text> </number:text>
			<number:currency-symbol number:language="en" number:country="US">$</number:currency-symbol>
			<style:map style:condition="value()&gt;=0" style:apply-style-name="N103P0" />
		</number:currency-style>
		<style:style style:name="Default" style:family="table-cell" />
		<style:style style:name="Result" style:family="table-cell" style:parent-style-name="Default">
			<style:text-properties fo:font-style="italic" style:text-underline-style="solid" style:text-underline-width="auto" style:text-underline-color="font-color" fo:font-weight="bold" />
		</style:style>
		<style:style style:name="Result2" style:family="table-cell" style:parent-style-name="Result" style:data-style-name="N103" />
		<style:style style:name="Heading" style:family="table-cell" style:parent-style-name="Default">
			<style:table-cell-properties style:text-align-source="fix" style:repeat-content="false" />
			<style:paragraph-properties fo:text-align="center" />
			<style:text-properties fo:font-size="16pt" fo:font-style="italic" fo:font-weight="bold" />
		</style:style>
		<style:style style:name="Heading1" style:family="table-cell" style:parent-style-name="Heading">
			<style:table-cell-properties style:rotation-angle="90" />
		</style:style>
	</office:styles>
	<office:automatic-styles>
		<style:page-layout style:name="pm1">
			<style:page-layout-properties style:writing-mode="lr-tb" />
			<style:header-style>
				<style:header-footer-properties fo:min-height="0.751cm" fo:margin-left="0cm" fo:margin-right="0cm" fo:margin-bottom="0.25cm" />
			</style:header-style>
			<style:footer-style>
				<style:header-footer-properties fo:min-height="0.751cm" fo:margin-left="0cm" fo:margin-right="0cm" fo:margin-top="0.25cm" />
			</style:footer-style>
		</style:page-layout>
		<style:page-layout style:name="pm2">
			<style:page-layout-properties style:writing-mode="lr-tb" />
			<style:header-style>
				<style:header-footer-properties fo:min-height="0.751cm" fo:margin-left="0cm" fo:margin-right="0cm" fo:margin-bottom="0.25cm" fo:border="0.088cm solid #000000"
					fo:padding="0.018cm" fo:background-color="#c0c0c0">
					<style:background-image />
				</style:header-footer-properties>
			</style:header-style>
			<style:footer-style>
				<style:header-footer-properties fo:min-height="0.751cm" fo:margin-left="0cm" fo:margin-right="0cm" fo:margin-top="0.25cm" fo:border="0.088cm solid #000000"
					fo:padding="0.018cm" fo:background-color="#c0c0c0">
					<style:background-image />
				</style:header-footer-properties>
			</style:footer-style>
		</style:page-layout>
	</office:automatic-styles>
	<office:master-styles>
		<style:master-page style:name="Default" style:page-layout-name="pm1">
			<style:header>
				<text:p>
					Sheet <text:sheet-name>1</text:sheet-name>
				</text:p>
			</style:header>
			<style:header-left style:display="false" />
			<style:footer>
				<text:p>
					Page <text:page-number>1</text:page-number>
				</text:p>
			</style:footer>
			<style:footer-left style:display="false" />
		</style:master-page>
		<style:master-page style:name="Report" style:page-layout-name="pm2">
			<style:header>
				<style:region-left>
					<text:p>
						<text:sheet-name>1</text:sheet-name> (<text:title>Title</text:title>)
					</text:p>
				</style:region-left>
				<style:region-right>
					<text:p>
						<text:date style:data-style-name="N2" text:date-value="2008-02-18">18/02/2008</text:date>, <text:time>00:17:06</text:time>
					</text:p>
				</style:region-right>
			</style:header>
			<style:header-left style:display="false" />
			<style:footer>
				<text:p>
					Page <text:page-number>1</text:page-number> / <text:page-count>99</text:page-count>
				</text:p>
			</style:footer>
			<style:footer-left style:display="false" />
		</style:master-page>
	</office:master-styles>
</office:document-styles>';
	}
	
	/**
	 * Returns the ODS settings
	 * 
	 * @return string
	 * @access private
	 */
	private function _getSettings() {
		return '<?xml version="1.0" encoding="UTF-8"?>
<office:document-settings xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
	xmlns:xlink="http://www.w3.org/1999/xlink" 
	xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0"
	xmlns:ooo="http://openoffice.org/2004/office" office:version="1.0">
	<office:settings>
		<config:config-item-set config:name="ooo:view-settings">
			<config:config-item config:name="VisibleAreaTop" config:type="int">0</config:config-item>
			<config:config-item config:name="VisibleAreaLeft" config:type="int">0</config:config-item>
			<config:config-item config:name="VisibleAreaWidth" config:type="int">2258</config:config-item>
			<config:config-item config:name="VisibleAreaHeight" config:type="int">903</config:config-item>
			<config:config-item-map-indexed config:name="Views">
				<config:config-item-map-entry>
					<config:config-item config:name="ViewId" config:type="string">View1</config:config-item>
					<config:config-item-map-named config:name="Tables">
						<config:config-item-map-entry config:name="Sheet1">
							<config:config-item config:name="CursorPositionX" config:type="int">0</config:config-item>
							<config:config-item config:name="CursorPositionY" config:type="int">1</config:config-item>
							<config:config-item config:name="HorizontalSplitMode" config:type="short">0</config:config-item>
							<config:config-item config:name="VerticalSplitMode" config:type="short">0</config:config-item>
							<config:config-item config:name="HorizontalSplitPosition" config:type="int">0</config:config-item>
							<config:config-item config:name="VerticalSplitPosition" config:type="int">0</config:config-item>
							<config:config-item config:name="ActiveSplitRange" config:type="short">2</config:config-item>
							<config:config-item config:name="PositionLeft" config:type="int">0</config:config-item>
							<config:config-item config:name="PositionRight" config:type="int">0</config:config-item>
							<config:config-item config:name="PositionTop" config:type="int">0</config:config-item>
							<config:config-item config:name="PositionBottom" config:type="int">0</config:config-item>
						</config:config-item-map-entry>
					</config:config-item-map-named>
					<config:config-item config:name="ActiveTable" config:type="string">Sheet1</config:config-item>
					<config:config-item config:name="HorizontalScrollbarWidth" config:type="int">270</config:config-item>
					<config:config-item config:name="ZoomType" config:type="short">0</config:config-item>
					<config:config-item config:name="ZoomValue" config:type="int">100</config:config-item>
					<config:config-item config:name="PageViewZoomValue" config:type="int">60</config:config-item>
					<config:config-item config:name="ShowPageBreakPreview" config:type="boolean">false</config:config-item>
					<config:config-item config:name="ShowZeroValues" config:type="boolean">true</config:config-item>
					<config:config-item config:name="ShowNotes" config:type="boolean">true</config:config-item>
					<config:config-item config:name="ShowGrid" config:type="boolean">true</config:config-item>
					<config:config-item config:name="GridColor" config:type="long">12632256</config:config-item>
					<config:config-item config:name="ShowPageBreaks" config:type="boolean">true</config:config-item>
					<config:config-item config:name="HasColumnRowHeaders" config:type="boolean">true</config:config-item>
					<config:config-item config:name="HasSheetTabs" config:type="boolean">true</config:config-item>
					<config:config-item config:name="IsOutlineSymbolsSet" config:type="boolean">true</config:config-item>
					<config:config-item config:name="IsSnapToRaster" config:type="boolean">false</config:config-item>
					<config:config-item config:name="RasterIsVisible" config:type="boolean">false</config:config-item>
					<config:config-item config:name="RasterResolutionX" config:type="int">1000</config:config-item>
					<config:config-item config:name="RasterResolutionY" config:type="int">1000</config:config-item>
					<config:config-item config:name="RasterSubdivisionX" config:type="int">1</config:config-item>
					<config:config-item config:name="RasterSubdivisionY" config:type="int">1</config:config-item>
					<config:config-item config:name="IsRasterAxisSynchronized" config:type="boolean">true</config:config-item>
				</config:config-item-map-entry>
			</config:config-item-map-indexed>
		</config:config-item-set>
		<config:config-item-set config:name="ooo:configuration-settings">
			<config:config-item config:name="ShowZeroValues" config:type="boolean">true</config:config-item>
			<config:config-item config:name="ShowNotes" config:type="boolean">true</config:config-item>
			<config:config-item config:name="ShowGrid" config:type="boolean">true</config:config-item>
			<config:config-item config:name="GridColor" config:type="long">12632256</config:config-item>
			<config:config-item config:name="ShowPageBreaks" config:type="boolean">true</config:config-item>
			<config:config-item config:name="LinkUpdateMode" config:type="short">3</config:config-item>
			<config:config-item config:name="HasColumnRowHeaders" config:type="boolean">true</config:config-item>
			<config:config-item config:name="HasSheetTabs" config:type="boolean">true</config:config-item>
			<config:config-item config:name="IsOutlineSymbolsSet" config:type="boolean">true</config:config-item>
			<config:config-item config:name="IsSnapToRaster" config:type="boolean">false</config:config-item>
			<config:config-item config:name="RasterIsVisible" config:type="boolean">false</config:config-item>
			<config:config-item config:name="RasterResolutionX" config:type="int">1000</config:config-item>
			<config:config-item config:name="RasterResolutionY" config:type="int">1000</config:config-item>
			<config:config-item config:name="RasterSubdivisionX" config:type="int">1</config:config-item>
			<config:config-item config:name="RasterSubdivisionY" config:type="int">1</config:config-item>
			<config:config-item config:name="IsRasterAxisSynchronized" config:type="boolean">true</config:config-item>
			<config:config-item config:name="AutoCalculate" config:type="boolean">true</config:config-item>
			<config:config-item config:name="PrinterName" config:type="string">Generic Printer</config:config-item>
			<config:config-item config:name="PrinterSetup" config:type="base64Binary">
				WAH+/0dlbmVyaWMgUHJpbnRlcgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
				AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU0dFTlBSVAAAAAAAAAAAAAAAAAAAAAAA
				AAAAAAAAAAAWAAMAngAAAAAAAAAFAFZUAAAkbQAASm9iRGF0YSAxCnByaW50ZXI9R2VuZXJpYyBQcmludGVyCm9yaWVudGF0aW9uPVBv
				cnRyYWl0CmNvcGllcz0xCm1hcmdpbmRhanVzdG1lbnQ9MCwwLDAsMApjb2xvcmRlcHRoPTI0CnBzbGV2ZWw9MApjb2xvcmRldmljZT0w
				ClBQRENvbnRleERhdGEKUGFnZVNpemU6TGV0dGVyAAA=
			</config:config-item>
			<config:config-item config:name="ApplyUserData" config:type="boolean">true</config:config-item>
			<config:config-item config:name="CharacterCompressionType" config:type="short">0</config:config-item>
			<config:config-item config:name="IsKernAsianPunctuation" config:type="boolean">false</config:config-item>
			<config:config-item config:name="SaveVersionOnClose" config:type="boolean">false</config:config-item>
			<config:config-item config:name="UpdateFromTemplate" config:type="boolean">false</config:config-item>
			<config:config-item config:name="AllowPrintJobCancel" config:type="boolean">true</config:config-item>
			<config:config-item config:name="LoadReadonly" config:type="boolean">false</config:config-item>
		</config:config-item-set>
	</office:settings>
</office:document-settings>';
	}
	
	/**
	 * Returns the ODS manifest data
	 * 
	 * @return string
	 * @access private
	 */
	private function _getManifest() {
		return '<?xml version="1.0" encoding="UTF-8"?>
			<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0">
			 <manifest:file-entry manifest:media-type="application/vnd.oasis.opendocument.spreadsheet" manifest:full-path="/"/>
			 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/statusbar/"/>
			 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/accelerator/"/>
			 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/floater/"/>
			 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/popupmenu/"/>
			 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/progressbar/"/>
			 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/menubar/"/>
			 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/toolbar/"/>
			 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/images/Bitmaps/"/>
			 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/images/"/>
			 <manifest:file-entry manifest:media-type="application/vnd.sun.xml.ui.configuration" manifest:full-path="Configurations2/"/>
			 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="content.xml"/>
			 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="styles.xml"/>
			 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="meta.xml"/>
			 <manifest:file-entry manifest:media-type="" manifest:full-path="Thumbnails/"/>
			 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="settings.xml"/>
			</manifest:manifest>';
	}
}