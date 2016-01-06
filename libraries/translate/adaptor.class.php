<?php
/**
 * translateAdaptor class
 * 
 * Stored in translateAdaptor.class.php
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptor
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 722 $
 */


/**
 * translateAdaptor
 *
 * translateAdaptor provides basic mechanics for each translation source adaptor.
 * It is modelled after the Zend Framework package {@link http://framework.zend.com/manual/en/zend.translate.html Zend_Translate}
 * but with modifications and simplifications for Scorpio. In theory, existing ZF
 * translation data should work with Scorpio without modification.
 * 
 * @todo DR: implement cacheController into loading step.
 * 
 * Portions are:
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2498 2006-12-23 22:13:38Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptor
 */
abstract class translateAdaptor {
	
	/**
	 * Scans for the locale within the name of the directory
	 * 
	 * @constant string
	 */
	const SEARCH_LOCALE_IN_DIRECTORY = 'directory';

	/**
	 * Scans for the locale within the name of the file
	 * 
	 * @constant string
	 */
	const SEARCH_LOCALE_IN_FILENAME = 'filename';

	/**
	 * Array with all options, each adaptor can have its own additional options
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
	 * @var array
	 * @access protected
	 */
	protected $_Options = array(
		'clear' => false,
		'scan' => null,
		'locale' => 'auto',
		'ignore' => '.',
		'disableNotices' => false
	);

	/**
	 * Translation table
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_TranslationTable = array();
	
	/**
     * Flag for if locale detection is in automatic mode
     * 
     * @var boolean
     * @access private
     */
    private $_Automatic = true;
    
    /**
     * Stores instance of cacheController
     *
     * @var cacheController
     * @access private
     */
    private $_Cache = null;
	
	
	
	/**
	 * Returns a new adaptor instance
	 *
	 * @param string|array $inData Translation data or filename for this adaptor
	 * @param string $inLocale (optional) Locale/Language to set
	 * @param array $inOptions (optional) Options for the adaptor
	 * @param cacheController $inCache (optional) cacheController instance
	 * @throws translateException
	 * @return void
	 */
	public function __construct($inData, $inLocale = null, array $inOptions = array(), $inCache = null) {
		if ( ($inLocale === "auto") || ($inLocale === null) ) {
			$this->_Automatic = true;
		} else {
			$this->_Automatic = false;
		}
		
		$this->setCacheController($inCache);
		$this->addTranslation($inData, $inLocale, $inOptions);
		$this->setLocale($inLocale);
	}
	
	
	
	/**
	 * Add translation data
	 *
	 * It may be a new language or additional data for existing language
	 * If $clear parameter is true, then translation data for specified
	 * language is replaced and added otherwise
	 *
	 * @param array|string $inData
	 * @param string $inLocale
	 * @param array $inOptions
	 * @throws translateException
	 * @return translateAdaptor
	 */
	public function addTranslation($inData, $inLocale = null, array $inOptions = array()) {
		try {
			$oLocale = new systemLocale($inLocale);
			$inLocale = $oLocale->getLocale();
		} catch ( systemException $e ) {
			throw new translateException($e->getMessage());
		}
		
		$originate = (string) $inLocale;
		$this->setOptions($inOptions);
		
		if ( is_string($inData) && is_dir($inData) ) {
			$inData = realpath($inData);
			$prev = '';
			foreach ( new RecursiveIteratorIterator(new RecursiveDirectoryIterator($inData, RecursiveDirectoryIterator::KEY_AS_PATHNAME), RecursiveIteratorIterator::SELF_FIRST) as $directory => $info ) {
				$file = $info->getFilename();
				if ( strpos($directory, DIRECTORY_SEPARATOR . $this->_Options['ignore']) !== false ) {
					// ignore files matching first characters from option 'ignore' and all files below
					continue;
				}
				
				if ( $info->isDir() ) {
					// pathname as locale
					if ( ($this->_Options['scan'] === self::SEARCH_LOCALE_IN_DIRECTORY) && (systemLocale::isValidLocale($file, false)) ) {
						if ( strlen($prev) <= strlen($file) ) {
							$inLocale = $file;
							$prev = (string) $inLocale;
						}
					}
				} elseif ( $info->isFile() ) {
					// filename as locale
					if ( $this->_Options['scan'] === self::SEARCH_LOCALE_IN_FILENAME ) {
						$filename = explode('.', $file);
						array_pop($filename);
						$filename = implode('.', $filename);
						if ( systemLocale::isValidLocale((string) $filename, false) ) {
							$inLocale = (string) $filename;
						} else {
							$parts = explode('.', $file);
							$parts2 = array();
							foreach ( $parts as $token ) {
								$parts2 += explode('_', $token);
							}
							$parts = array_merge($parts, $parts2);
							$parts2 = array();
							foreach ( $parts as $token ) {
								$parts2 += explode('-', $token);
							}
							$parts = array_merge($parts, $parts2);
							$parts = array_unique($parts);
							$prev = '';
							foreach ( $parts as $token ) {
								if ( systemLocale::isValidLocale($token, false) ) {
									if ( strlen($prev) <= strlen($token) ) {
										$inLocale = $token;
										$prev = $token;
									}
								}
							}
						}
					}
					
					try {
						$this->_addTranslationData($info->getPathname(), (string) $inLocale, $this->_Options);
						if ( (isset($this->_TranslationTable[(string) $inLocale]) === true) && (count($this->_TranslationTable[(string) $inLocale]) > 0) ) {
							$this->setLocale($inLocale);
						}
					} catch ( translateException $e ) {
						// ignore failed sources while scanning
					}
				}
			}
		} else {
			$this->_addTranslationData($inData, (string) $inLocale, $this->_Options);
			if ( (isset($this->_TranslationTable[(string) $inLocale]) === true) && (count($this->_TranslationTable[(string) $inLocale]) > 0) ) {
				$this->setLocale($inLocale);
			}
		}
		
		if ( (isset($this->_TranslationTable[$originate]) === true) && (count($this->_TranslationTable[$originate]) > 0) ) {
			$this->setLocale($originate);
		}
		
		return $this;
	}

	/**
	 * Sets new adaptor options
	 *
	 * @param  array $inOptions
	 * @throws translateException
	 * @return translateAdaptor
	 */
	public function setOptions(array $inOptions = array()) {
		$change = false;
		foreach ( $inOptions as $key => $option ) {
			if ( $key == 'locale' ) {
				$this->setLocale($option);
			} elseif ( (isset($this->_Options[$key]) && ($this->_Options[$key] != $option)) || !isset($this->_Options[$key]) ) {
				$this->_Options[$key] = $option;
				$change = true;
			}
		}
		return $this;
	}

	/**
	 * Returns adaptor options if no option specified, otherwise the specific option
	 * 
	 * Global options:
	 * 'clear'  => clears already loaded data when adding new files
	 * 'scan'   => searches for translation files using the SEARCH_LOCALE constants
	 * 'locale' => the actual set locale to use
	 * 'ignore' => ignore files with 
	 * 'disableNotices' => disable trigger notices if no translation found
	 *
	 * @param  string $inOption
	 * @return mixed
	 */
	public function getOptions($inOption = null) {
		if ( $inOption === null ) {
			return $this->_Options;
		}
		
		if ( isset($this->_Options[$inOption]) === true ) {
			return $this->_Options[$inOption];
		}
		
		return null;
	}

	/**
	 * Returns the current locale
	 *
	 * @return string
	 */
	public function getLocale() {
		return $this->_Options['locale'];
	}

	/**
	 * Sets current locale to $inLocale
	 *
	 * @param  string $inLocale
	 * @throws translateException
	 * @return translateAdaptor
	 */
	public function setLocale($inLocale) {
		if ( ($inLocale === "auto") || ($inLocale === null) ) {
			$this->_Automatic = true;
		} else {
			$this->_Automatic = false;
		}
		
		if ( !systemLocale::isValidLocale($inLocale, true, false) ) {
			if ( !systemLocale::isValidLocale($inLocale, false, false) ) {
				throw new translateException("The given Language ({$inLocale}) does not exist");
			}
			
			$inLocale = new systemLocale($inLocale);
		}
		
		$inLocale = (string) $inLocale;
		if ( !isset($this->_TranslationTable[$inLocale]) ) {
			$temp = explode('_', $inLocale);
			if ( !isset($this->_TranslationTable[$temp[0]]) && !isset($this->_TranslationTable[$inLocale]) ) {
				// Should we suppress notices ?
				if ( $this->_Options['disableNotices'] === false ) {
					// throwing a notice due to possible problems on locale setting
					throw new translateAdaptorRequestedLanguageNotAvailableException($inLocale);
				}
			}
			
			$inLocale = $temp[0];
		}
		
		if ( empty($this->_TranslationTable[$inLocale]) ) {
			// Should we suppress notices ?
			if ( $this->_Options['disableNotices'] === false ) {
				// throwing a notice due to possible problems on locale setting
				throw new translateAdaptorTranslationNotAvailableException($inLocale);
			}
		}
		
		if ( $this->_Options['locale'] != $inLocale ) {
			$this->_Options['locale'] = $inLocale;
		}
		
		return $this;
	}

	/**
	 * Returns the available languages from this adaptor
	 *
	 * @return array
	 */
	public function getList() {
		$list = array_keys($this->_TranslationTable);
		$result = null;
		foreach ( $list as $value ) {
			if ( !empty($this->_TranslationTable[$value]) ) {
				$result[$value] = $value;
			}
		}
		return $result;
	}

	/**
	 * Returns all available message ids from this adaptor
	 * If no locale is given, the actual language will be used
	 *
	 * @param  string $locale (optional) Language to return the message ids from
	 * @return array
	 */
	public function getMessageIds($inLocale = null) {
		if ( empty($inLocale) || !$this->isAvailable($inLocale) ) {
			$inLocale = $this->_Options['locale'];
		}
		
		return array_keys($this->_TranslationTable[(string) $inLocale]);
	}

	/**
	 * Returns all available translations from this adaptor
	 * If no locale is given, the actual language will be used
	 * If 'all' is given the complete translation dictionary will be returned
	 *
	 * @param  string $locale (optional) Language to return the messages from
	 * @return array
	 */
	public function getMessages($inLocale = null) {
		if ( $inLocale === 'all' ) {
			return $this->_TranslationTable;
		}
		
		if ( (empty($inLocale) === true) || ($this->isAvailable($inLocale) === false) ) {
			$inLocale = $this->_Options['locale'];
		}
		
		return $this->_TranslationTable[(string) $inLocale];
	}

	/**
	 * Is the wished language available ?
	 * 
	 * @param  string $locale
	 * @return boolean
	 */
	public function isAvailable($inLocale) {
		return isset($this->_TranslationTable[(string) $inLocale]);
	}

	/**
	 * Load translation data
	 *
	 * @param mixed $inData
	 * @param string $inLocale
	 * @param array $inOptions (optional)
	 * @return void
	 * @abstract 
	 */
	abstract protected function _loadTranslationData($inData, $inLocale, array $inOptions = array());

	/**
	 * Internal function for adding translation data
	 *
	 * It may be a new language or additional data for existing language
	 * If $clear parameter is true, then translation data for specified
	 * language is replaced and added otherwise
	 *
	 * @param array|string $data
	 * @param string $locale
	 * @param array $options
	 * @throws translateException
	 * @return translateAdaptor
	 */
	private function _addTranslationData($inData, $inLocale, array $inOptions = array()) {
		if ( !systemLocale::isValidLocale($inLocale, false) ) {
			if ( !systemLocale::isValidLocale($inLocale) ) {
				throw new translateException("The given Language ({$inLocale}) does not exist");
			}
			$inLocale = new systemLocale($inLocale);
		}
		
		$locale = (string) $inLocale;
		if ( isset($this->_TranslationTable[$locale]) === false ) {
			$this->_TranslationTable[$locale] = array();
		}
		
		$read = true;
		if ( $read ) {
			$this->_loadTranslationData($inData, $locale, $inOptions);
		}
		
		if ( $this->_Automatic === true ) {
			$find = new systemLocale($locale);
			$browser = $find->getEnvLocales() + $find->getBrowserLocales();
			arsort($browser);
			foreach ( $browser as $language => $quality ) {
				if ( isset($this->_TranslationTable[$language]) === true ) {
					$this->_Options['locale'] = $language;
					break;
				}
			}
		}
		
		return $this;
	}

	/**
	 * Translates the given string, returns the translation
	 *
	 * @param string $inMessageId
	 * @param string $inLocale (optional) Locale/Language to use
	 * @return string
	 */
	public function translate($inMessageId, $inLocale = null) {
		if ( $inLocale === null ) {
			$inLocale = $this->_Options['locale'];
		}
		if ( !systemLocale::isValidLocale($inLocale, false) ) {
			if ( !systemLocale::isValidLocale($inLocale) ) {
				// language does not exist, return original string
				return $inMessageId;
			}
			$inLocale = new systemLocale($inLocale);
		}
		
		$locale = (string) $inLocale;
		if ( isset($this->_TranslationTable[$locale][$inMessageId]) === true ) {
			// return original translation
			return $this->_TranslationTable[$locale][$inMessageId];
		} else if ( strlen($locale) != 2 ) {
			// faster than creating a new locale and separate the leading part
			$locale = substr($locale, 0, -strlen(strrchr($locale, '_')));
			
			if ( isset($this->_TranslationTable[$locale][$inMessageId]) === true ) {
				// return regionless translation (en_US -> en)
				return $this->_TranslationTable[$locale][$inMessageId];
			}
		}
		
		// no translation found, return original
		return $inMessageId;
	}

	/**
	 * Translates the given string, returns the translation
	 *
	 * @param string $inMessageId
	 * @param string $inLocale (optional)
	 * @return string
	 */
	public function __($inMessageId, $inLocale = null) {
		return $this->translate($inMessageId, $inLocale);
	}

	/**
	 * Checks if a string is translated within the source or not
	 * returns boolean
	 *
	 * @param string $inMessageId
	 * @param boolean $inOriginal (optional) Allow translation only for original language
	 *                                       when true, a translation for 'en_US' would give false when it can
	 *                                       be translated with 'en' only
	 * @param string $locale (optional)
	 * @return boolean
	 */
	public function isTranslated($inMessageId, $inOriginal = false, $inLocale = null) {
		if ( ($inOriginal !== false) && ($inOriginal !== true) ) {
			$inLocale = $inOriginal;
			$inOriginal = false;
		}
		
		if ( $inLocale === null ) {
			$inLocale = $this->_Options['locale'];
		}
		
		if ( !systemLocale::isValidLocale($inLocale, false) ) {
			if ( !systemLocale::isValidLocale($inLocale) ) {
				// language does not exist, return original string
				return false;
			}
			
			$inLocale = new systemLocale();
		}
		
		$inLocale = (string) $inLocale;
		if ( isset($this->_TranslationTable[$inLocale][$inMessageId]) === true ) {
			// return original translation
			return true;
		} elseif ( (strlen($inLocale) != 2) && ($inOriginal === false) ) {
			// faster than creating a new locale and separate the leading part
			$inLocale = substr($inLocale, 0, -strlen(strrchr($inLocale, '_')));
			
			if ( isset($this->_TranslationTable[$inLocale][$inMessageId]) === true ) {
				// return regionless translation (en_US -> en)
				return true;
			}
		}
		
		// No translation found, return original
		return false;
	}
	
	/**
	 * Returns the current cacheController
	 *
	 * @return cacheController
	 */
	function getCache() {
		return $this->_Cache;
	}
	
	/**
	 * Set $_Cache to $inCacheController
	 *
	 * @param cacheController $inCacheController
	 * @return translateAdaptor
	 */
	function setCacheController($inCacheController) {
		if ( $inCacheController !== $this->_Cache ) {
			$this->_Cache = $inCacheController;
		}
		return $this;
	}
	
    /**
     * Returns the adaptor name
     *
     * @return string
     */
    abstract public function getAdaptorName();
}