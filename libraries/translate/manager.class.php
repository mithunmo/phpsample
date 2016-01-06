<?php
/**
 * translateManager class
 * 
 * Stored in translateManager.class.php
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateManager
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 697 $
 */


/**
 * translateManager
 *
 * translateManager provides the main interface into the translation system.
 * It is modelled after the Zend Framework package {@link http://framework.zend.com/manual/en/zend.translate.html Zend_Translate}
 * but with modifications and simplifications for Scorpio. In theory, existing ZF
 * translation data should work with Scorpio without modification.
 * 
 * Example:
 * <code>
 * $oManager = translateManager::getInstance(translateManager::ADAPTOR_CSV, '/path/to/lang.csv', 'en_GB', array());
 * $oManager->translate('string');
 * //or
 * $oManager->__('string');
 * </code>
 * 
 * Portions are:
 * @ copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @ version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @ license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateManager
 */
class translateManager {
	
	/**
	 * Static instance of translateManager
	 *
	 * @var translateManager
	 * @access private
	 * @static
	 */
	private static $_Instance = null;
	
	/**
	 * Caching engine instance
	 *
	 * @var cacheController
	 * @access private
	 * @static 
	 */
	private static $_Cache = null;

	/*
	 * Adapter name constants
	 */
	const ADAPTOR_ARRAY = 'Array';
	const ADAPTOR_CSV = 'Csv';
	const ADAPTOR_GETTEXT = 'Gettext';
	const ADAPTOR_INI = 'Ini';
	const ADAPTOR_QT = 'Qt';
	const ADAPTOR_TBX = 'Tbx';
	const ADAPTOR_TMX = 'Tmx';
	const ADAPTOR_XLIFF = 'Xliff';
	const ADAPTOR_XMLTM = 'XmlTm';
	
	/**
	 * An array of permitted adaptor implementations
	 *
	 * @var array
	 * @access private
	 * @static
	 */
	private static $_Adaptors = array(
		self::ADAPTOR_ARRAY, self::ADAPTOR_CSV, self::ADAPTOR_GETTEXT,
		self::ADAPTOR_INI, self::ADAPTOR_QT, self::ADAPTOR_TBX, self::ADAPTOR_TMX,
		self::ADAPTOR_XLIFF, self::ADAPTOR_XMLTM
	);

	/**
	 * Current adaptor instance
	 *
	 * @var translateAdaptor
	 * @access private
	 */
	private $_Adaptor;
	
	
	
	/**
	 * Generates the standard translation object
	 * 
	 * <code>
	 * $array = array(
	 *   'clear'  => clears already loaded data when adding new files
	 *   'scan'   => searches for translation files using the SEARCH_LOCALE constants
	 *   'locale' => the actual set locale to use
	 *   'ignore' => ignore files with 
	 *   'disableNotices' => disable trigger notices if no translation found
	 * );
	 * </code>
	 *
	 * @param  string $inAdaptor Adapter to use
	 * @param  string|array $inData Translation source data for the adaptor
	 * @param  string $inLocale OPTIONAL locale to use
	 * @param  array  $inOptions OPTIONAL options for the adaptor
	 * @throws translateException
	 */
	public function __construct($inAdaptor, $inData, $inLocale = null, array $inOptions = array()) {
		$this->setAdapter($inAdaptor, $inData, $inLocale, $inOptions);
	}
	
	/**
	 * Returns a single instance of the translate manager
	 *
	 * <code>
	 * $array = array(
	 *   'clear'  => clears already loaded data when adding new files
	 *   'scan'   => searches for translation files using the SEARCH_LOCALE constants
	 *   'locale' => the actual set locale to use
	 *   'ignore' => ignore files with 
	 *   'disableNotices' => disable trigger notices if no translation found
	 * );
	 * </code>
	 * 
	 * @param string $inAdaptor
	 * @param string|array $inData
	 * @param string $inLocale
	 * @param array $inOptions
	 * @return translateManager
	 * @throws translateException
	 * @static
	 */
	public static function getInstance($inAdaptor = null, $inData = null, $inLocale = null, array $inOptions = array()) {
		if ( !self::$_Instance instanceof translateManager ) {
			self::$_Instance = new translateManager($inAdaptor, $inData, $inLocale, $inOptions);
		}
		
		return self::$_Instance;
	}
	
	/**
	 * Returns true if $inAdaptor is a valid adaptor
	 *
	 * @param string $inAdaptor
	 * @return boolean
	 * @static
	 */
	public static function isValidAdaptor($inAdaptor) {
		return in_array(ucfirst($inAdaptor), self::$_Adaptors);
	}
	
	/**
	 * Add a new adaptor implementation to list of allowed adaptors
	 *
	 * @param string $inAdaptor
	 * @return void
	 * @static
	 */
	public static function addAdaptor($inAdaptor) {
		if ( !in_array($inAdaptor, self::$_Adaptors) ) {
			self::$_Adaptors[] = $inAdaptor;
		}
	}
	
	/**
	 * Remove an adaptor from list of allowed adaptors
	 *
	 * @param string $inAdaptor
	 * @return void
	 * @static
	 */
	public static function removeAdaptor($inAdaptor) {
		$key = array_search($inAdaptor, self::$_Adaptors);
		if ( $key !== false ) {
			unset(self::$_Adaptors[$key]);
		}
	}

	/**
	 * Returns the set cache
	 *
	 * @return cacheController
	 */
	public static function getCache() {
		return self::$_Cache;
	}

	/**
	 * Sets a cache for all instances of translateManager
	 *
	 * @param  cacheController $inCache
	 * @return void
	 */
	public static function setCache(cacheController $inCache) {
		self::$_Cache = $inCache;
	}

	/**
	 * Returns true when a cache is set
	 *
	 * @return boolean
	 */
	public static function hasCache() {
		if ( self::$_Cache !== null ) {
			return true;
		}
		
		return false;
	}

	/**
	 * Removes any set cache
	 *
	 * @return void
	 */
	public static function removeCache() {
		self::$_Cache = null;
	}

	/**
	 * Clears all set cache data
	 *
	 * @return void
	 */
	public static function clearCache() {
		self::$_Cache->clearCache();
	}
	
	
	
	/**
	 * Returns the current translation adaptor
	 *
	 * @return translateAdaptor
	 */
	public function getAdaptor() {
		return $this->_Adaptor;
	}
	
	/**
	 * Sets a new translation adaptor
	 *
	 * @param string $inAdaptor
	 * @param string|array $inData
	 * @param string $inLocale
	 * @param array $inOptions
	 * @throws translateException
	 */
	public function setAdapter($inAdaptor, $inData, $inLocale = null, array $inOptions = array()) {
		if ( !self::isValidAdaptor($inAdaptor) ) {
			throw new translateException("Adaptor $inAdaptor is not a valid adaptor implementation");
		}
		$adaptor = 'translateAdaptor'.ucfirst($inAdaptor);
		
		$this->_Adaptor = new $adaptor($inData, $inLocale, $inOptions, self::getCache());
		if ( !$this->_Adaptor instanceof translateAdaptor ) {
			throw new translateException("Adapter " . $adaptor . " does not extend translateAdaptor");
		}
	}
	
	
	
	/*
	 * Re-route calls for other methods into the adaptor
	 */
	public function __call($method, array $options) {
		if ( method_exists($this->_Adaptor, $method) ) {
			return call_user_func_array(array($this->_Adaptor, $method), $options);
		}
		throw new translateException("Unknown method '" . $method . "' called on adaptor!");
	}
}