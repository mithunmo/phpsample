<?php
/**
 * translateAdaptorIni class
 * 
 * Stored in translateAdaptorIni.class.php
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorIni
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 650 $
 */


/**
 * translateAdaptorIni
 *
 * Handles translated data from an INI file
 * 
 * Taken from Zend Framework, Zend_Translate package with only minor changes.
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorIni
 */
class translateAdaptorIni extends translateAdaptor {

	/**
	 * Generates the adaptor
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
	 * Load translation data
	 *
	 * @param string $data
	 * @param string $locale
	 * @param array $options (optional)
	 * @throws translateException
	 */
	protected function _loadTranslationData($data, $locale, array $options = array()) {
		if ( !file_exists($data) ) {
			throw new translateException("Ini file '" . $data . "' not found");
		}
		$inidata = parse_ini_file($data, false);
		
		$options = array_merge($this->_Options, $options);
		if ( ($options['clear'] == true) || !isset($this->_TranslationTable[$locale]) ) {
			$this->_TranslationTable[$locale] = array();
		}
		$this->_TranslationTable[$locale] = array_merge($this->_TranslationTable[$locale], $inidata);
	}

	/**
	 * Returns the adaptor name
	 *
	 * @return string
	 */
	public function getAdaptorName() {
		return "Ini";
	}
}