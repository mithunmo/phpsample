<?php
/**
 * translateAdaptorQt class
 * 
 * Stored in translateAdaptorQt.class.php
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorQt
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 650 $
 */


/**
 * translateAdaptorQt
 *
 * Handles translated data in QT format
 * 
 * Taken from Zend Framework, Zend_Translate package with only minor changes.
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorQt
 */
class translateAdaptorQt extends translateAdaptor {

	// Internal variables
	private $_file = false;

	private $_cleared = array();

	private $_transunit = null;

	private $_source = null;

	private $_target = null;

	private $_scontent = null;

	private $_tcontent = null;

	private $_stag = false;

	private $_ttag = true;

	
	
	/**
	 * Generates the Qt adapter
	 * This adapter reads with php's xml_parser
	 *
	 * @param string $inData Translation data (file or directory)
	 * @param string $inLocale (optional) Locale/Language to set
	 * @param array $inOptions optional) Options to set
	 * @param cacheController $inCache
	 */
	public function __construct($inData, $inLocale = null, array $inOptions = array(), $inCache = null) {
		parent::__construct($inData, $inLocale, $inOptions, $inCache);
	}

	/**
	 * Load translation data (QT file reader)
	 *
	 * @param string $filename  QT file to add, full path must be given for access
	 * @param string $locale
	 * @param array $option (optional)
	 * @throws translateException
	 */
	protected function _loadTranslationData($filename, $locale, array $options = array()) {
		$options = $options + $this->_Options;
		
		if ( $options['clear'] || !isset($this->_TranslationTable[$locale]) ) {
			$this->_TranslationTable[$locale] = array();
		}
		
		if ( !is_readable($filename) ) {
			throw new translateException('Translation file \'' . $filename . '\' is not readable.');
		}
		
		$this->_target = $locale;
		
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

	private function _startElement($file, $name, $attrib) {
		switch ( strtolower($name) ) {
			case 'message' :
				$this->_source = null;
				$this->_stag = false;
				$this->_ttag = false;
				$this->_scontent = null;
				$this->_tcontent = null;
				break;
			case 'source' :
				$this->_stag = true;
				break;
			case 'translation' :
				$this->_ttag = true;
				break;
			default :
				break;
		}
	}

	private function _endElement($file, $name) {
		switch ( strtolower($name) ) {
			case 'source' :
				$this->_stag = false;
				break;
			
			case 'translation' :
				if ( !empty($this->_scontent) and !empty($this->_tcontent) or (isset($this->_TranslationTable[$this->_target][$this->_scontent]) === false) ) {
					$this->_TranslationTable[$this->_target][$this->_scontent] = $this->_tcontent;
				}
				$this->_ttag = false;
				break;
			
			default :
				break;
		}
	}

	private function _contentElement($file, $data) {
		if ( $this->_stag === true ) {
			$this->_scontent .= $data;
		}
		
		if ( $this->_ttag === true ) {
			$this->_tcontent .= $data;
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
		return "Qt";
	}
}