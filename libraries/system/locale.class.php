<?php
/**
 * systemLocale class
 * 
 * Stored in systemLocale.class.php
 * 
 * Contains components and code from Zend Framework (ZF). Where noted, ZF code is
 * used under the terms of the ZF licence.
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLocale
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 760 $
 */


/**
 * systemLocale
 *
 * Holds data about a specific locale and provides look-up methods to attempt
 * to guess the best locale from the current browser data. Some bits of this
 * class are taken from {@link http://framework.zend.com/manual/en/zend.locale.html Zend_Locale}.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLocale
 */
class systemLocale {

	/**
	 * Array of valid locales, taken from Zend_Locale
	 *
	 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
	 * @license http://framework.zend.com/license/new-bsd     New BSD License
	 * @version $Id: Locale.php 12869 2008-11-26 11:07:02Z thomas $
	 * @var array
	 * @access private
	 * @static 
	 */
	private static $_LocaleData = array(
		'aa_DJ' => true, 'aa_ER' => true, 'aa_ET' => true, 'aa'    => true,
		'af_NA' => true, 'af_ZA' => true, 'af'    => true, 'ak_GH' => true, 'ak'    => true,
		'am_ET' => true, 'am'    => true, 'ar_AE' => true, 'ar_BH' => true, 'ar_DZ' => true,
		'ar_EG' => true, 'ar_IQ' => true, 'ar_JO' => true, 'ar_KW' => true, 'ar_LB' => true,
		'ar_LY' => true, 'ar_MA' => true, 'ar_OM' => true, 'ar_QA' => true, 'ar_SA' => true,
		'ar_SD' => true, 'ar_SY' => true, 'ar_TN' => true, 'ar_YE' => true, 'ar'    => true,
		'as_IN' => true, 'as'    => true, 'az_AZ' => true, 'az'    => true, 'be_BY' => true,
		'be'    => true, 'bg_BG' => true, 'bg'    => true, 'bn_BD' => true, 'bn_IN' => true,
		'bn'    => true, 'bo_CN' => true, 'bo_IN' => true, 'bo'    => true, 'bs_BA' => true,
		'bs'    => true, 'byn_ER'=> true, 'byn'   => true, 'ca_ES' => true, 'ca'    => true,
		'cch_NG'=> true, 'cch'   => true, 'cop_EG'=> true, 'cop_US'=> true, 'cop'   => true,
		'cs_CZ' => true, 'cs'    => true, 'cy_GB' => true, 'cy'    => true, 'da_DK' => true,
		'da'    => true, 'de_AT' => true, 'de_BE' => true, 'de_CH' => true, 'de_DE' => true,
		'de_LI' => true, 'de_LU' => true, 'de'    => true, 'dv_MV' => true, 'dv'    => true,
		'dz_BT' => true, 'dz'    => true, 'ee_GH' => true, 'ee_TG' => true, 'ee'    => true,
		'el_CY' => true, 'el_GR' => true, 'el'    => true, 'en_AS' => true, 'en_AU' => true,
		'en_BE' => true, 'en_BW' => true, 'en_BZ' => true, 'en_CA' => true, 'en_GB' => true,
		'en_GU' => true, 'en_HK' => true, 'en_IE' => true, 'en_IN' => true, 'en_JM' => true,
		'en_MH' => true, 'en_MP' => true, 'en_MT' => true, 'en_NZ' => true, 'en_PH' => true,
		'en_PK' => true, 'en_SG' => true, 'en_TT' => true, 'en_UM' => true, 'en_US' => true,
		'en_VI' => true, 'en_ZA' => true, 'en_ZW' => true, 'en'    => true, 'eo'    => true,
		'es_AR' => true, 'es_BO' => true, 'es_CL' => true, 'es_CO' => true, 'es_CR' => true,
		'es_DO' => true, 'es_EC' => true, 'es_ES' => true, 'es_GT' => true, 'es_HN' => true,
		'es_MX' => true, 'es_NI' => true, 'es_PA' => true, 'es_PE' => true, 'es_PR' => true,
		'es_PY' => true, 'es_SV' => true, 'es_US' => true, 'es_UY' => true, 'es_VE' => true,
		'es'    => true, 'et_EE' => true, 'et'    => true, 'eu_ES' => true, 'eu'    => true,
		'fa_AF' => true, 'fa_IR' => true, 'fa'    => true, 'fi_FI' => true, 'fi'    => true,
		'fil'   => true, 'fo_FO' => true, 'fo'    => true, 'fr_BE' => true, 'fr_CA' => true,
		'fr_CH' => true, 'fr_FR' => true, 'fr_LU' => true, 'fr_MC' => true, 'fr'    => true,
		'fur_IT'=> true, 'fur'   => true, 'ga_IE' => true, 'ga'    => true, 'gaa_GH'=> true,
		'gaa'   => true, 'gez_ER'=> true, 'gez_ET'=> true, 'gez'   => true, 'gl_ES' => true,
		'gl'    => true, 'gu_IN' => true, 'gu'    => true, 'gv_GB' => true, 'gv'    => true,
		'ha_GH' => true, 'ha_NE' => true, 'ha_NG' => true, 'ha'    => true, 'haw_US'=> true,
		'haw'   => true, 'he_IL' => true, 'he'    => true, 'hi_IN' => true, 'hi'    => true,
		'hr_HR' => true, 'hr'    => true, 'hu_HU' => true, 'hu'    => true, 'hy_AM' => true,
		'hy'    => true, 'ia'    => true, 'id_ID' => true, 'id'    => true, 'ig_NG' => true,
		'ig'    => true, 'ii_CN' => true, 'ii'    => true, 'is_IS' => true, 'is'    => true,
		'it_CH' => true, 'it_IT' => true, 'it'    => true, 'iu'    => true, 'ja_JP' => true,
		'ja'    => true, 'ka_GE' => true, 'ka'    => true, 'kaj_NG'=> true, 'kaj'   => true,
		'kam_KE'=> true, 'kam'   => true, 'kcg_NG'=> true, 'kcg'   => true, 'kfo_NG'=> true,
		'kfo'   => true, 'kk_KZ' => true, 'kk'    => true, 'kl_GL' => true, 'kl'    => true,
		'km_KH' => true, 'km'    => true, 'kn_IN' => true, 'kn'    => true, 'ko_KR' => true,
		'ko'    => true, 'kok_IN'=> true, 'kok'   => true, 'kpe_GN'=> true, 'kpe_LR'=> true,
		'kpe'   => true, 'ku_IQ' => true, 'ku_IR' => true, 'ku_SY' => true, 'ku_TR' => true,
		'ku'    => true, 'kw_GB' => true, 'kw'    => true, 'ky_KG' => true, 'ky'    => true,
		'ln_CD' => true, 'ln_CG' => true, 'ln'    => true, 'lo_LA' => true, 'lo'    => true,
		'lt_LT' => true, 'lt'    => true, 'lv_LV' => true, 'lv'    => true, 'mk_MK' => true,
		'mk'    => true, 'ml_IN' => true, 'ml'    => true, 'mn_MN' => true, 'mn'    => true,
		'mr_IN' => true, 'mr'    => true, 'ms_BN' => true, 'ms_MY' => true, 'ms'    => true,
		'mt_MT' => true, 'mt'    => true, 'my_MM' => true, 'my'    => true, 'nb_NO' => true,
		'nb'    => true, 'ne_NP' => true, 'ne'    => true, 'nl_BE' => true, 'nl_NL' => true,
		'nl'    => true, 'nn_NO' => true, 'nn'    => true, 'nr_ZA' => true, 'nr'    => true,
		'nso_ZA'=> true, 'nso'   => true, 'ny_MW' => true, 'ny'    => true, 'om_ET' => true,
		'om_KE' => true, 'om'    => true, 'or_IN' => true, 'or'    => true, 'pa_IN' => true,
		'pa_PK' => true, 'pa'    => true, 'pl_PL' => true, 'pl'    => true, 'ps_AF' => true,
		'ps'    => true, 'pt_BR' => true, 'pt_PT' => true, 'pt'    => true, 'ro_RO' => true,
		'ro'    => true, 'ru_RU' => true, 'ru_UA' => true, 'ru'    => true, 'rw_RW' => true,
		'rw'    => true, 'sa_IN' => true, 'sa'    => true, 'se_FI' => true, 'se_NO' => true,
		'se'    => true, 'sh_BA' => true, 'sh_CS' => true, 'sh_YU' => true, 'sh'    => true,
		'sid_ET'=> true, 'sid'   => true, 'sk_SK' => true, 'sk'    => true, 'sl_SI' => true,
		'sl'    => true, 'so_DJ' => true, 'so_ET' => true, 'so_KE' => true, 'so_SO' => true,
		'so'    => true, 'sq_AL' => true, 'sq'    => true, 'sr_BA' => true, 'sr_CS' => true,
		'sr_ME' => true, 'sr_RS' => true, 'sr_YU' => true, 'sr'    => true, 'ss_ZA' => true,
		'ss'    => true, 'ssy'   => true, 'st_ZA' => true, 'st'    => true, 'sv_FI' => true,
		'sv_SE' => true, 'sv'    => true, 'sw_KE' => true, 'sw_TZ' => true, 'sw'    => true,
		'syr_SY'=> true, 'syr'   => true, 'ta_IN' => true, 'ta'    => true, 'te_IN' => true,
		'te'    => true, 'tg_TJ' => true, 'tg'    => true, 'th_TH' => true, 'th'    => true,
		'ti_ER' => true, 'ti_ET' => true, 'ti'    => true, 'tig_ER'=> true, 'tig'   => true,
		'tn_ZA' => true, 'tn'    => true, 'to_TO' => true, 'to'    => true, 'tr_TR' => true,
		'tr'    => true, 'ts_ZA' => true, 'ts'    => true, 'tt_RU' => true, 'tt'    => true,
		'ug'    => true, 'uk_UA' => true, 'uk'    => true, 'und_ZZ'=> true, 'und'   => true,
		'ur_IN' => true, 'ur_PK' => true, 'ur'    => true, 'uz_AF' => true, 'uz_UZ' => true,
		'uz'    => true, 've_ZA' => true, 've'    => true, 'vi_VN' => true, 'vi'    => true,
		'wal_ET'=> true, 'wal'   => true, 'wo_SN' => true, 'wo'    => true, 'xh_ZA' => true,
		'xh'    => true, 'yo_NG' => true, 'yo'    => true, 'zh_CN' => true, 'zh_HK' => true,
		'zh_MO' => true, 'zh_SG' => true, 'zh_TW' => true, 'zh'    => true, 'zu_ZA' => true,
		'zu'    => true
	);

	/**
	 * Some generic text mappings for locale information
	 *
	 * @var array
	 * @static
	 * @access protected
	 */
	protected static $_LocaleTranslations = array(
		'Australia' => 'AU', 'Austria' => 'AT', 'Belgium' => 'BE', 'Brazil' => 'BR',
		'Canada' => 'CA', 'China' => 'CN', 'Czech Republic' => 'CZ', 'Denmark' => 'DK',
		'Finland' => 'FI', 'France' => 'FR', 'Germany' => 'DE', 'Greece' => 'GR',
		'Hong Kong SAR' => 'HK', 'Hungary' => 'HU', 'Iceland' => 'IS', 'Ireland' => 'IE',
		'Italy' => 'IT', 'Japan' => 'JP', 'Korea' => 'KP', 'Mexiko' => 'MX',
		'The Netherlands' => 'NL', 'New Zealand' => 'NZ', 'Norway' => 'NO',
		'Poland' => 'PL', 'Portugal' => 'PT', 'Russia' => 'RU', 'Singapore' => 'SG',
		'Slovakia' => 'SK', 'Spain' => 'ES', 'Sweden' => 'SE', 'Taiwan' => 'TW',
		'Turkey' => 'TR', 'United Kingdom' => 'GB', 'United States' => 'US', 

		'Chinese' => 'zh', 'Czech' => 'cs', 'Danish' => 'da', 'Dutch' => 'nl', 'English' => 'en',
		'Finnish' => 'fi', 'French' => 'fr', 'German' => 'de', 'Greek' => 'el', 'Hungarian' => 'hu',
		'Icelandic' => 'is', 'Italian' => 'it', 'Japanese' => 'ja', 'Korean' => 'ko',
		'Norwegian' => 'no', 'Polish' => 'pl', 'Portuguese' => 'pt', 'Russian' => 'ru', 'Slovak' => 'sk',
		'Spanish' => 'es', 'Swedish' => 'sv', 'Turkish' => 'tr'
	);
	
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_DefaultLocale
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DefaultLocale;
	
	/**
	 * Stores $_CurrentLocale
	 *
	 * @var string
	 * @access protected
	 */
	protected $_CurrentLocale;

	/**
	 * Stores the detected browser locales
	 *
	 * @var array
	 * @access protected
	 */
	protected $_BrowserLocales;

	/**
	 * Stores the environment detected locales
	 *
	 * @var array
	 * @access protected
	 */
	protected $_EnvLocales;

	/**
	 * Stores $_LocaleFromAutoDetect
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_LocaleFromAutoDetect;
	
	
	
	/**
	 * Creates a new locale using the specified locale or searching for the most
	 * appropriate
	 *
	 * @return systemLocale
	 */
	function __construct($inLocale = null) {
		$this->reset();
				
		$locale = self::cleanLocale($inLocale);
		if ( self::isValidLocale($locale) ) {
			$this->setLocale($locale);
		} else {
			$locale = $this->detectLocale($locale);
		}
		
		$this->setCurrentLocale($locale);
	}
	
	
	
	/**
	 * Cleans the locale so it is in a consistent format
	 *
	 * @param string $inLocale
	 * @return string
	 * @access protected
	 */
	public static function cleanLocale($inLocale) {
		if ( !is_string($inLocale) || is_numeric($inLocale) ) {
			return '';
		}
		$locale = trim(preg_replace('/[^0-9a-zA-Z_\-]/', '', $inLocale));
		
		if ( strpos($locale, '-') === false && strpos($locale, '_') === false && strlen($locale) > 3 ) {
			$locale = strtolower($locale);
			if ( !in_array($locale, array('environment','browser','auto')) ) {
				$locale = substr($locale, 0, 3);
			}
		}
		
		$matches = array();
		if ( preg_match('/([a-z]{1,3})([\-_]{1})([a-z]+)/i', $locale, $matches) ) {
			$locale = strtolower($matches[1]).$matches[2].strtoupper(substr($matches[3], 0, 3));
		}
		if (strpos($locale, '-') !== false) {
			$locale = strtr($locale, '-', '_');
		}
		return $locale;
	}
	
	/**
	 * Returns the array of locals
	 *
	 * @return array
	 * @static
	 */
	public static function getLocales() {
		return self::$_LocaleData;
	}
	
	/**
	 * Returns the array of local translations
	 *
	 * @return array
	 * @static
	 */
	public static function getLocaleTranslations() {
		return self::$_LocaleTranslations;
	}
	
	/**
	 * Returns a translation for $inData from the self::$_LocaleTranslations
	 *
	 * @param string $inData
	 * @return string
	 * @static
	 */
	public static function translateLocaleData($inData) {
		if ( isset(self::$_LocaleTranslations[$inData]) ) {
			return self::$_LocaleTranslations[$inData];
		}
		return false;
	}

	/**
	 * Returns true if $inLocale exists in the LocaleData
	 * 
	 * If $inCheckPartials is true then the locale is checked for an under-score (_)
	 * separator that splits the language and region and the language component will
	 * be checked against the LocaleData array. e.g. en_XYZ is not valid, but 
	 * $inCheckPartials set to true, will validate against 'en' meaning the locale
	 * will still be treated as being valid.
	 *
	 * @param string $inLocale
	 * @param boolean $inCheckPartials (optional) Check the locale stub
	 * @return boolean
	 */
	public static function isValidLocale($inLocale, $inCheckPartials = true) {
		$locale = self::cleanLocale($inLocale);
		if ( isset(self::$_LocaleData[$locale]) === true ) {
			return true;
		} else {
			if ( $inCheckPartials && strpos($locale, '_') !== false ) {
				if ( isset(self::$_LocaleData[substr($locale, 0, strpos($locale, '_'))]) === true ) {
					return true;
				}
			}
			return false;
		}
	}
	
	
	
	/**
	 * Attempts to detect the current locale from various sources
	 * 
	 * @param string $inLocale (optional) Can be browser, environment or auto
	 * @return string
	 * @throws systemException
	 */
	function detectLocale($inLocale = null) {
		$locale = false;
		$this->setLocaleFromAutoDetect(true);
		$this->detectBrowserLocales();
		$this->detectEnvLocales();
		
		if ( $inLocale == 'browser' ) {
			$locale = $this->getBrowserLocales();
		}
		if ( $inLocale == 'environment' ) {
			$locale = $this->getEnvLocales();
		}
		if ( $inLocale == 'auto' ) {
			$locale = $this->getBrowserLocales() + $this->getEnvLocales();
		}
		if ( is_array($locale) ) {
			$locale = key($locale);
		}
		
		if ( !$this->getDefaultLocale() && system::getConfig()->getSystemLocale() ) {
			$sysLocale = system::getConfig()->getSystemLocale()->getParamValue();
			if ( self::isValidLocale($sysLocale) ) {
				$this->setDefaultLocale($sysLocale);
			} else {
				throw new systemException("Invalid system locale specified in config ($locale)");
			}
		}
		if ( !$locale ) {
			$locale = $this->getDefaultLocale();
		}
		return $locale;
	}
	
	/**
	 * Attempts to detect the current browser locale
	 *
	 * @return void
	 */
	function detectBrowserLocales() {
		/*
		 * liberally nicked from Zend_Locale::getBrowser()
		 * 
		 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
		 * @license http://framework.zend.com/license/new-bsd     New BSD License
		 * @version $Id: Locale.php 12869 2008-11-26 11:07:02Z thomas $
		 */
		//$httplanguages = getenv('HTTP_ACCEPT_LANGUAGE');
		$httplanguages = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '');
		$languages = array();
		if ( empty($httplanguages) === false ) {
			$accepted = preg_split('/,\s*/', $httplanguages);
			
			foreach ( $accepted as $accept ) {
				$match = null;
				$result = preg_match('/^([a-z]{1,8}(?:[-_][a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $accept, $match);
				
				if ( $result < 1 ) {
					continue;
				}
				
				if ( isset($match[2]) === true ) {
					$quality = (float) $match[2];
				} else {
					$quality = 1.0;
				}
				
				$countrys = explode('-', $match[1]);
				$region = array_shift($countrys);
				
				$country2 = explode('_', $region);
				$region = array_shift($country2);
				
				foreach ( $countrys as $country ) {
					$languages[$region . '_' . strtoupper($country)] = $quality;
				}
				
				foreach ( $country2 as $country ) {
					$languages[$region . '_' . strtoupper($country)] = $quality;
				}
				
				if ( (isset($languages[$region]) === false) || ($languages[$region] < $quality) ) {
					$languages[$region] = $quality;
				}
			}
		}
		$this->setBrowserLocales($languages);
	}
	
	/**
	 * Attempts to detect the current environment locale
	 *
	 * @return void
	 */
	function detectEnvLocales() {
		/*
		 * Liberally nicked from Zend_Locale::getEnvironment()
		 * 
		 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
		 * @license   http://framework.zend.com/license/new-bsd     New BSD License
		 * @version   $Id: Locale.php 12869 2008-11-26 11:07:02Z thomas $
		 */
		$language = setlocale(LC_ALL, 0);
		$languages = explode(';', $language);
		$languagearray = array();
		
		foreach ( $languages as $locale ) {
			if ( strpos($locale, '=') !== false ) {
				$language = substr($locale, strpos($locale, '='));
				$language = substr($language, 1);
			}
			
			if ( $language !== 'C' ) {
				if ( strpos($language, '.') !== false ) {
					$language = substr($language, 0, strpos($language, '.'));
				} elseif ( strpos($language, '@') !== false ) {
					$language = substr($language, 0, strpos($language, '@'));
				}
				
				if ( strpos($language, 'C/') === 0 ) {
					$language = substr($language, 2);
				}
				
				$splitted = explode('_', $language);
				$language = (string) $language;
				if ( isset(self::$_LocaleData[$language]) === true ) {
					$languagearray[$language] = 1;
					if ( strlen($language) > 4 ) {
						$languagearray[substr($language, 0, 2)] = 1;
					}
					
					continue;
				}
				
				if ( self::translateLocaleData($splitted[0]) !== false ) {
					if ( self::translateLocaleData($splitted[1]) !== false ) {
						$languagearray[self::translateLocaleData($splitted[0]).'_'.self::translateLocaleData($splitted[1])] = 1;
					}
					
					$languagearray[self::translateLocaleData($splitted[0])] = 1;
				}
			}
		}
		
		$this->setEnvLocales($languagearray);
	}

	/**
	 * Reset object to defaults
	 *
	 * @return void
	 */
	function reset() {
		$this->_Modified = false;
		$this->_DefaultLocale = null;
		$this->_CurrentLocale = null;
		$this->_BrowserLocales = array();
		$this->_EnvLocales = array();
		$this->_LocaleFromAutoDetect = false;
	}
	
	/**
	 * Returns the current locale, or if not set, the system default locale
	 *
	 * @return string
	 * @throws systemException
	 */
	function getLocale() {
		if ( $this->getCurrentLocale() ) {
			return $this->getCurrentLocale();
		}
		if ( $this->getDefaultLocale() ) {
			return $this->getDefaultLocale();
		}
		throw new systemException("No locale has been set");
	}
	
	/**
	 * Explicitly set the locale to $inLocale
	 *
	 * @return systemLocale
	 */
	function setLocale($inLocale) {
		if ( $this->_CurrentLocale !== $inLocale ) {
			$this->setCurrentLocale($inLocale);
			$this->setLocaleFromAutoDetect(false);
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the language part of the locale
	 *
	 * @return string
	 */
	public function getLanguage() {
		$locale = explode('_', $this->getLocale());
		return $locale[0];
	}
	
	/**
	 * Returns the region part of the locale if available
	 *
	 * @return string|false
	 */
	public function getRegion() {
		$locale = explode('_', $this->getLocale());
		if ( isset($locale[1]) === true ) {
			return $locale[1];
		}
		return false;
	}

	/**
	 * Returns a string representation of the object
	 *
	 * @return string
	 */
	public function toString() {
		return (string) $this->getLocale();
	}

	/**
	 * Returns a string representation of the object
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
	}
	
	
	
	/**
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return systemLocale
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns the system default locale (if set)
	 *
	 * @return string
	 */
	function getDefaultLocale() {
		return $this->_DefaultLocale;
	}
	
	/**
	 * Set $_DefaultLocale to $inDefaultLocale
	 *
	 * @param string $inDefaultLocale
	 * @return systemLocale
	 */
	function setDefaultLocale($inDefaultLocale) {
		if ( $inDefaultLocale !== $this->_DefaultLocale ) {
			$this->_DefaultLocale = $inDefaultLocale;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the currently assigned locale
	 *
	 * @return string
	 */
	function getCurrentLocale() {
		return $this->_CurrentLocale;
	}

	/**
	 * Set $_CurrentLocale to $inCurrentLocale
	 *
	 * @param string $inCurrentLocale
	 * @return systemLocale
	 */
	function setCurrentLocale($inCurrentLocale) {
		if ( $inCurrentLocale !== $this->_CurrentLocale ) {
			$this->_CurrentLocale = $inCurrentLocale;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the array of detected browser locales
	 *
	 * @return array
	 */
	function getBrowserLocales() {
		return $this->_BrowserLocales;
	}

	/**
	 * Set $_BrowserLocale to $inBrowserLocale
	 *
	 * @param string|array $inBrowserLocale
	 * @return systemLocale
	 */
	function setBrowserLocales($inBrowserLocale) {
		if ( $inBrowserLocale !== $this->_BrowserLocales ) {
			$this->_BrowserLocales = (array) $inBrowserLocale;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the array of detected environment locales
	 *
	 * @return array
	 */
	function getEnvLocales() {
		return $this->_EnvLocales;
	}

	/**
	 * Set $_EnvLocale to $inEnvLocale
	 *
	 * @param string|array $inEnvLocale
	 * @return __PHP_Incomplete_Class
	 */
	function setEnvLocales($inEnvLocale) {
		if ( $inEnvLocale !== $this->_EnvLocales ) {
			$this->_EnvLocales = (array) $inEnvLocale;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns true if current locale came from auto-detect
	 *
	 * @return boolean
	 */
	function isLocaleFromAutoDetect() {
		return $this->_LocaleFromAutoDetect;
	}

	/**
	 * Set $_LocaleFromAutoDetect to $inLocaleFromAutoDetect
	 *
	 * @param boolean $inLocaleFromAutoDetect
	 * @return systemLocale
	 */
	function setLocaleFromAutoDetect($inLocaleFromAutoDetect) {
		if ( $inLocaleFromAutoDetect !== $this->_LocaleFromAutoDetect ) {
			$this->_LocaleFromAutoDetect = $inLocaleFromAutoDetect;
			$this->setModified();
		}
		return $this;
	}
}