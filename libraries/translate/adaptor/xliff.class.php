<?php
/**
 * translateAdaptorXliff class
 * 
 * Stored in translateAdaptorXliff.class.php
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorXliff
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 650 $
 */


/**
 * translateAdaptorXliff
 *
 * Handles translated data in the Xliff format
 * 
 * Taken from Zend Framework, Zend_Translate package with only minor changes.
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorXliff
 */
class translateAdaptorXliff extends translateAdaptor {

	// Internal variables
	private $_file = false;

	private $_cleared = array();

	private $_transunit = null;

	private $_source = null;

	private $_target = null;

	private $_scontent = null;

	private $_tcontent = null;

	private $_stag = false;

	private $_ttag = false;
	
	
	
	/**
	 * Generates the xliff adapter
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
	 * Load translation data (XLIFF file reader)
	 *
	 * @param string $filename
	 * @param string $locale
	 * @param array $option (optional)
	 * @throws translateException
	 */
	protected function _loadTranslationData($filename, $locale, array $options = array()) {
		$options = $options + $this->_Options;
		
		if ( $options['clear'] ) {
			$this->_TranslationTable = array();
		}
		
		if ( !is_readable($filename) ) {
			throw new translateException('Translation file \'' . $filename . '\' is not readable.');
		}
		
		$encoding = $this->_findEncoding($filename);
		$this->_target = $locale;
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
		if ( $this->_stag === true ) {
			$this->_scontent .= "<" . $name;
			foreach ( $attrib as $key => $value ) {
				$this->_scontent .= " $key=\"$value\"";
			}
			$this->_scontent .= ">";
		} else if ( $this->_ttag === true ) {
			$this->_tcontent .= "<" . $name;
			foreach ( $attrib as $key => $value ) {
				$this->_tcontent .= " $key=\"$value\"";
			}
			$this->_tcontent .= ">";
		} else {
			switch ( strtolower($name) ) {
				case 'file' :
					$this->_source = $attrib['source-language'];
					if ( isset($attrib['target-language']) ) {
						$this->_target = $attrib['target-language'];
					}
					
					$this->_TranslationTable[$this->_source] = array();
					$this->_TranslationTable[$this->_target] = array();
					break;
				case 'trans-unit' :
					$this->_transunit = true;
					break;
				case 'source' :
					if ( $this->_transunit === true ) {
						$this->_scontent = null;
						$this->_stag = true;
						$this->_ttag = false;
					}
					break;
				case 'target' :
					if ( $this->_transunit === true ) {
						$this->_tcontent = null;
						$this->_ttag = true;
						$this->_stag = false;
					}
					break;
				default :
					break;
			}
		}
	}

	private function _endElement($file, $name) {
		if ( ($this->_stag === true) and ($name !== 'source') ) {
			$this->_scontent .= "</" . $name . ">";
		} else if ( ($this->_ttag === true) and ($name !== 'target') ) {
			$this->_tcontent .= "</" . $name . ">";
		} else {
			switch ( strtolower($name) ) {
				case 'trans-unit' :
					$this->_transunit = null;
					$this->_scontent = null;
					$this->_tcontent = null;
					break;
				case 'source' :
					if ( !empty($this->_scontent) and !empty($this->_tcontent) or (isset($this->_TranslationTable[$this->_source][$this->_scontent]) === false) ) {
						$this->_TranslationTable[$this->_source][$this->_scontent] = $this->_scontent;
					}
					$this->_stag = false;
					break;
				case 'target' :
					if ( !empty($this->_scontent) and !empty($this->_tcontent) or (isset($this->_TranslationTable[$this->_source][$this->_scontent]) === false) ) {
						$this->_TranslationTable[$this->_target][$this->_scontent] = $this->_tcontent;
					}
					$this->_ttag = false;
					break;
				default :
					break;
			}
		}
	}

	private function _contentElement($file, $data) {
		if ( ($this->_transunit !== null) and ($this->_source !== null) and ($this->_stag === true) ) {
			$this->_scontent .= $data;
		}
		
		if ( ($this->_transunit !== null) and ($this->_target !== null) and ($this->_ttag === true) ) {
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
		return "Xliff";
	}
}