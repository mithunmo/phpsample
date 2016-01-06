<?php
/**
 * translateAdaptorXmlTm class
 * 
 * Stored in translateAdaptorXmlTm.class.php
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorXmlTm
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 650 $
 */


/**
 * translateAdaptorXmlTm
 *
 * Handles translated data in XMLTM format
 * 
 * Taken from Zend Framework, Zend_Translate package with only minor changes.
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorXmlTm
 */
class translateAdaptorXmlTm extends translateAdaptor {

	/**
	 * Current file for parsing
	 *
	 * @var string
	 * @access private
	 */
	private $_file = false;
	
	/**
	 * Array of cleared data
	 *
	 * @var array
	 * @access private
	 */
	private $_cleared = array();
	
	/**
	 * Current language
	 *
	 * @var string
	 * @access private
	 */
	private $_lang = null;
	
	/**
	 * Content
	 *
	 * @var string
	 * @access private
	 */
	private $_content = null;
	
	/**
	 * Current tag
	 *
	 * @var string
	 * @access private
	 */
	private $_tag = null;
	
	
	
	/**
	 * Generates the xmltm adapter
	 * This adapter reads with php's xml_parser
	 *
	 * @param string $inData Translation data (file or directory)
	 * @param string $inLocale (optional) Locale/Language to set
	 * @param array $inOptions (optional) Options to set
	 * @param cacheController $inCache
	 */
	public function __construct($inData, $inLocale = null, array $inOptions = array(), $inCache = null) {
		parent::__construct($inData, $inLocale, $inOptions, $inCache);
	}

	/**
	 * Load translation data (XMLTM file reader)
	 *
	 * @param string $filename
	 * @param string $locale
	 * @param array $option (optional)
	 * @throws translateException
	 */
	protected function _loadTranslationData($filename, $locale, array $options = array()) {
		$options = $options + $this->_Options;
		$this->_lang = $locale;
		
		if ( $options['clear'] || !isset($this->_TranslationTable[$locale]) ) {
			$this->_TranslationTable[$locale] = array();
		}
		
		if ( !is_readable($filename) ) {
			throw new translateException('Translation file \'' . $filename . '\' is not readable.');
		}
		
		$encoding = $this->_findEncoding($filename);
		$this->_file = xml_parser_create($encoding);
		xml_set_object($this->_file, $this);
		xml_parser_set_option($this->_file, XML_OPTION_CASE_FOLDING, 0);
		xml_set_element_handler($this->_file, "_startElement", "_endElement");
		xml_set_character_data_handler($this->_file, "_contentElement");
		
		if ( !xml_parse($this->_file, file_get_contents($filename)) ) {
			$ex = sprintf('XML error: %s at line %d', xml_error_string(xml_get_error_code($this->_file)), xml_get_current_line_number($this->_file));
			xml_parser_free($this->_file);
			
			throw new translateException($ex);
		}
	}
	
	/**
	 * Handles the opening start tag in the XML parse
	 *
	 * @param string $file
	 * @param string $name
	 * @param array $attrib
	 */
	private function _startElement($file, $name, $attrib) {
		switch ( strtolower($name) ) {
			case 'tm:tu' :
				$this->_tag = $attrib['id'];
				$this->_content = null;
				break;
			default :
				break;
		}
	}
	
	/**
	 * Handles the closing tag in the XML parse
	 *
	 * @param string $file
	 * @param string $name
	 */
	private function _endElement($file, $name) {
		switch ( strtolower($name) ) {
			case 'tm:tu' :
				if ( !empty($this->_tag) and !empty($this->_content) or (isset($this->_TranslationTable[$this->_lang][$this->_tag]) === false) ) {
					$this->_TranslationTable[$this->_lang][$this->_tag] = $this->_content;
				}
				$this->_tag = null;
				$this->_content = null;
				break;
			
			default :
				break;
		}
	}
	
	/**
	 * Handles content
	 *
	 * @param string $file
	 * @param string $data
	 */
	private function _contentElement($file, $data) {
		if ( ($this->_tag !== null) ) {
			$this->_content .= $data;
		}
	}

	/**
	 * Attempts to locate the file content encoding
	 *
	 * @param string $filename
	 * @return string
	 */
	private function _findEncoding($filename) {
		$file = file_get_contents($filename, null, null, 0, 100);
		if ( strpos($file, "encoding") !== false ) {
			$encoding = substr($file, strpos($file, "encoding") + 9);
			$encoding = substr($encoding, 1, strpos($encoding, $encoding[0], 1) - 1);
			return $encoding;
		}
		return 'UTF-8';
	}

	/**
	 * Returns the adaptor name
	 *
	 * @return string
	 */
	public function getAdaptorName() {
		return "XmlTm";
	}
}