<?php
/**
 * translateAdaptorArray class
 * 
 * Stored in translateAdaptorArray.class.php
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorArray
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 650 $
 */


/**
 * translateAdaptorArray
 *
 * Handles arrays of translated data
 * 
 * Taken from Zend Framework, Zend_Translate package with only minor changes.
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorArray
 */
class translateAdaptorArray extends translateAdaptor {

	/**
	 * Generates the adaptor
	 *
	 * @param string|array $inData Translation data (file or directory or array of key => values)
	 * @param string $inLocale (optional) Locale/Language to set
	 * @param array $inOptions (optional) Options to set
	 * @param cacheController $inCache
	 */
	public function __construct($inData, $inLocale = null, array $inOptions = array(), $inCache = null) {
		parent::__construct($inData, $inLocale, $inOptions, $inCache);
	}

	/**
	 * Load translation data
	 *
	 * @param string|array $inData
	 * @param string $inLocale 
	 * @param array $inOptions (optional)
	 * @throws translateException
	 */
	protected function _loadTranslationData($inData, $inLocale, array $inOptions = array()) {
		if ( !is_array($inData) ) {
			if ( file_exists($inData) ) {
				ob_start();
				$inData = include ($inData);
				ob_end_clean();
			}
		}
		if ( !is_array($inData) ) {
			throw new translateException("Error including array or file '" . $inData . "'");
		}
		
		$inOptions = $inOptions + $this->_Options;
		if ( ($inOptions['clear'] == true) || !isset($this->_TranslationTable[$inLocale]) ) {
			$this->_TranslationTable[$inLocale] = array();
		}
		
		$this->_TranslationTable[$inLocale] = $inData + $this->_TranslationTable[$inLocale];
	}

	/**
	 * Returns the adaptor name
	 *
	 * @return string
	 */
	public function getAdaptorName() {
		return "Array";
	}
}