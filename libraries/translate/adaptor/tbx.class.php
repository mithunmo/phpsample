<?php
/**
 * translateAdaptorTbx class
 * 
 * Stored in translateAdaptorTbx.class.php
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorTbx
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 650 $
 */


/**
 * translateAdaptorTbx
 *
 * Handles translated data in TBX format
 * 
 * Taken from Zend Framework, Zend_Translate package with only minor changes.
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorTbx
 */
class translateAdaptorTbx extends translateAdaptor {

	// Internal variables
	private $_file = false;

	private $_cleared = array();

	private $_langset = null;

	private $_termentry = null;

	private $_content = null;

	private $_term = null;
	
	
	
	/**
	 * Generates the tbx adapter
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
	 * Load translation data (TBX file reader)
	 *
	 * @param string $filename  TBX file to add, full path must be given for access
	 * @param string $locale    Locale has no effect for TBX because TBX defines all languages within
	 *                            the source file
	 * @param array $option    (optional) Options to use
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
		if ( $this->_term !== null ) {
			$this->_content .= "<" . $name;
			foreach ( $attrib as $key => $value ) {
				$this->_content .= " $key=\"$value\"";
			}
			$this->_content .= ">";
		} else {
			switch ( strtolower($name) ) {
				case 'termentry' :
					$this->_termentry = null;
					break;
				case 'langset' :
					if ( isset($attrib['xml:lang']) === true ) {
						$this->_langset = $attrib['xml:lang'];
						if ( isset($this->_TranslationTable[$this->_langset]) === false ) {
							$this->_TranslationTable[$this->_langset] = array();
						}
					}
					break;
				case 'term' :
					$this->_term = true;
					$this->_content = null;
					break;
				default :
					break;
			}
		}
	}

	private function _endElement($file, $name) {
		if ( ($this->_term !== null) and ($name != "term") ) {
			$this->_content .= "</" . $name . ">";
		} else {
			switch ( strtolower($name) ) {
				case 'langset' :
					$this->_langset = null;
					break;
				case 'term' :
					$this->_term = null;
					if ( empty($this->_termentry) ) {
						$this->_termentry = $this->_content;
					}
					if ( !empty($this->_content) or (isset($this->_TranslationTable[$this->_langset][$this->_termentry]) === false) ) {
						$this->_TranslationTable[$this->_langset][$this->_termentry] = $this->_content;
					}
					break;
				default :
					break;
			}
		}
	}

	private function _contentElement($file, $data) {
		if ( $this->_term !== null ) {
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
		return "Tbx";
	}
}