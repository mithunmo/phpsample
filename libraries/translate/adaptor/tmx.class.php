<?php
/**
 * translateAdaptorTmx class
 * 
 * Stored in translateAdaptorTmx.class.php
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorTmx
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 650 $
 */


/**
 * translateAdaptorTmx
 *
 * Handles translated data in TMX format
 * 
 * Taken from Zend Framework, Zend_Translate package with only minor changes.
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorTmx
 */
class translateAdaptorTmx extends translateAdaptor {

	// Internal variables
	private $_file = false;

	private $_cleared = array();

	private $_tu = null;

	private $_tuv = null;

	private $_seg = null;

	private $_content = null;
	
	
	
	/**
	 * Generates the tmx adapter
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
	 * Load translation data (TMX file reader)
	 *
	 * @param string $filename
	 * @param string $locale
	 * @param array $option (optional)
	 * @throws translateException
	 */
	protected function _loadTranslationData($filename, $locale, array $options = array()) {
		$options = $this->_Options + $options;
		
		if ( $options['clear'] ) {
			$this->_TranslationTable = array();
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

	private function _startElement($file, $name, $attrib) {
		if ( $this->_seg !== null ) {
			$this->_content .= "<" . $name;
			foreach ( $attrib as $key => $value ) {
				$this->_content .= " $key=\"$value\"";
			}
			$this->_content .= ">";
		} else {
			switch ( strtolower($name) ) {
				case 'tu' :
					if ( isset($attrib['tuid']) === true ) {
						$this->_tu = $attrib['tuid'];
					} else {
						$this->_tu = true;
					}
					break;
				case 'tuv' :
					if ( isset($attrib['xml:lang']) === true ) {
						$this->_tuv = $attrib['xml:lang'];
						if ( isset($this->_TranslationTable[$this->_tuv]) === false ) {
							$this->_TranslationTable[$this->_tuv] = array();
						}
					}
					break;
				case 'seg' :
					$this->_seg = true;
					$this->_content = null;
					break;
				default :
					break;
			}
		}
	}

	private function _endElement($file, $name) {
		if ( ($this->_seg !== null) and ($name !== 'seg') ) {
			$this->_content .= "</" . $name . ">";
		} else {
			switch ( strtolower($name) ) {
				case 'tu' :
					$this->_tu = null;
					break;
				case 'tuv' :
					$this->_tuv = null;
					break;
				case 'seg' :
					$this->_seg = null;
					if ( empty($this->_tu) || $this->_tu === true ) {
						$this->_tu = $this->_content;
					}
					if ( !empty($this->_content) or (isset($this->_TranslationTable[$this->_tuv][$this->_tu]) === false) ) {
						$this->_TranslationTable[$this->_tuv][$this->_tu] = $this->_content;
					}
					break;
				default :
					break;
			}
		}
	}

	private function _contentElement($file, $data) {
		if ( ($this->_seg !== null) and ($this->_tu !== null) and ($this->_tuv !== null) ) {
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
		return "Tmx";
	}
}