<?php
/**
 * translateAdaptorGettext class
 * 
 * Stored in translateAdaptorGettext.class.php
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorGettext
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 650 $
 */


/**
 * translateAdaptorGettext
 *
 * Handles translated data from an gettext file
 * 
 * Taken from Zend Framework, Zend_Translate package with only minor changes.
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorGettext
 */
class translateAdaptorGettext extends translateAdaptor {

	// Internal variables
	private $_bigEndian = false;

	private $_file = false;

	private $_adapterInfo = array();

	
	
	/**
	 * Generates the  adapter
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
	 * Read values from the MO file
	 *
	 * @param  string  $bytes
	 */
	private function _readMOData($bytes) {
		if ( $this->_bigEndian === false ) {
			return unpack('V' . $bytes, fread($this->_file, 4 * $bytes));
		} else {
			return unpack('N' . $bytes, fread($this->_file, 4 * $bytes));
		}
	}

	/**
	 * Load translation data (MO file reader)
	 *
	 * @param string $filename MO file to add, full path must be given for access
	 * @param string $locale New Locale/Language to set
	 * @param array $option (optional)
	 * @throws translateException
	 */
	protected function _loadTranslationData($filename, $locale, array $options = array()) {
		$this->_bigEndian = false;
		$options = $options + $this->_Options;
		
		if ( $options['clear'] || !isset($this->_TranslationTable[$locale]) ) {
			$this->_TranslationTable[$locale] = array();
		}
		
		$this->_file = @fopen($filename, 'rb');
		if ( !$this->_file ) {
			throw new translateException('Error opening translation file \'' . $filename . '\'.');
		}
		if ( @filesize($filename) < 10 ) {
			throw new translateException('\'' . $filename . '\' is not a gettext file');
		}
		
		// get Endian
		$input = $this->_readMOData(1);
		if ( strtolower(substr(dechex($input[1]), -8)) == "950412de" ) {
			$this->_bigEndian = false;
		} else if ( strtolower(substr(dechex($input[1]), -8)) == "de120495" ) {
			$this->_bigEndian = true;
		} else {
			throw new translateException('\'' . $filename . '\' is not a gettext file');
		}
		// read revision - not supported for now
		$input = $this->_readMOData(1);
		
		// number of bytes
		$input = $this->_readMOData(1);
		$total = $input[1];
		
		// number of original strings
		$input = $this->_readMOData(1);
		$OOffset = $input[1];
		
		// number of translation strings
		$input = $this->_readMOData(1);
		$TOffset = $input[1];
		
		// fill the original table
		fseek($this->_file, $OOffset);
		$origtemp = $this->_readMOData(2 * $total);
		fseek($this->_file, $TOffset);
		$transtemp = $this->_readMOData(2 * $total);
		
		for ( $count = 0; $count < $total; ++$count ) {
			if ( $origtemp[$count * 2 + 1] != 0 ) {
				fseek($this->_file, $origtemp[$count * 2 + 2]);
				$original = @fread($this->_file, $origtemp[$count * 2 + 1]);
			} else {
				$original = '';
			}
			
			if ( $transtemp[$count * 2 + 1] != 0 ) {
				fseek($this->_file, $transtemp[$count * 2 + 2]);
				$this->_TranslationTable[$locale][$original] = fread($this->_file, $transtemp[$count * 2 + 1]);
			}
		}
		
		$this->_TranslationTable[$locale][''] = trim($this->_TranslationTable[$locale]['']);
		if ( empty($this->_TranslationTable[$locale]['']) ) {
			$this->_adapterInfo[$filename] = 'No adapter information available';
		} else {
			$this->_adapterInfo[$filename] = $this->_TranslationTable[$locale][''];
		}
		
		unset($this->_TranslationTable[$locale]['']);
	}

	/**
	 * Returns the adapter informations
	 *
	 * @return array Each loaded adapter information as array value
	 */
	public function getAdapterInfo() {
		return $this->_adapterInfo;
	}

	/**
	 * Returns the adaptor name
	 *
	 * @return string
	 */
	public function getAdaptorName() {
		return "Gettext";
	}
}