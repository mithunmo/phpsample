<?php
/**
 * translateAdaptorCsv class
 * 
 * Stored in translateAdaptorCsv.class.php
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorCsv
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 650 $
 */


/**
 * translateAdaptorCsv
 *
 * Handles translated data from a CSV file
 * 
 * Taken from Zend Framework, Zend_Translate package with only minor changes.
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorCsv
 */
class translateAdaptorCsv extends translateAdaptor {

	/**
	 * Generates the adapter
	 *
	 * @param string $inData Translation data (file or directory)
	 * @param string $inLocale (optional) Locale/Language to set
	 * @param array $inOptions optional) Options to set
	 * @param cacheController $inCache
	 */
	public function __construct($inData, $inLocale = null, array $inOptions = array(), $inCache = null) {
		$this->_Options['delimiter'] = ";";
		$this->_Options['length'] = 0;
		$this->_Options['enclosure'] = '"';
		
		parent::__construct($inData, $inLocale, $inOptions, $inCache);
	}

	/**
	 * Load translation data
	 *
	 * @param string $filename Filename and full path to the translation source
	 * @param string $locale Locale/Language to add data for
	 * @param array $option (optional)
	 * @throws translateException
	 */
	protected function _loadTranslationData($filename, $locale, array $options = array()) {
		$options = $options + $this->_Options;
		
		if ( $options['clear'] || !isset($this->_TranslationTable[$locale]) ) {
			$this->_TranslationTable[$locale] = array();
		}
		
		$this->_file = @fopen($filename, 'rb');
		if ( !$this->_file ) {
			throw new translateException('Error opening translation file \'' . $filename . '\'.');
		}
		
		while ( ($data = fgetcsv($this->_file, $options['length'], $options['delimiter'], $options['enclosure'])) !== false ) {
			if ( substr($data[0], 0, 1) === '#' ) {
				continue;
			}
			
			if ( isset($data[1]) !== true ) {
				continue;
			}
			
			$this->_TranslationTable[$locale][$data[0]] = $data[1];
		}
	}

	/**
	 * returns the adaptor name
	 *
	 * @return string
	 */
	public function getAdaptorName() {
		return "Csv";
	}
}