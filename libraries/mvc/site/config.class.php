<?php
/**
 * mvcSiteConfig.class.php
 *
 * mvcSiteConfig class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcSiteConfig
 * @version $Rev: 821 $
 */


/**
 * mvcSiteConfig
 *
 * Holds site configuration data; this is separate to and does not override the system level settings.
 * SiteConfig is used to fetch the controller map for the current site and request.
 *
 * There are two types of parameter that exist within an mvcSiteConfig:
 * <ol>
 * <li>Site specific values</li>
 * <li>Inherited values</li>
 * </ol>
 *
 * Site specific are, as the name implies, specific to the site that is being requested. These settings
 * cannot be inherited as they govern things like the active controllers (controllerMap.xml file), whether
 * the site is active or not, the template engine in use (usually smarty) and the default Controller
 * to be called for the site if not using the globally set default.
 *
 * Inherited values can be inherited from a parent site, or a parent parents site or a parent parent p...
 * Yes, if a value cannot be located, it will be passed on and on down the chain until the chain runs out.
 * This allows for lots of very similar sites with only cosmetic differences to have the same config for
 * things like databases, plugins etc. In fact, even the theme can be shared.
 *
 * When adding additional configuration parameters it is therefore important to consider where they will
 * be used and if the site that is being built will be extended to another site in the future.
 *
 * <code>
 * $oSiteConfig = mvcSiteConfig::getInstance();
 * $oSiteConfig->load('/path/to/my/config.xml');
 * $oSiteConfig->setSitePath(dirname('/path/to/my/config.xml');
 *
 * $oSiteConfig->getTemplateEngine();
 * $oSiteConfig->getParentParam('a param that might exist in a parent site');
 * $oSiteConfig->getParam('my local site param');
 * </code>
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcSiteConfig
 */
class mvcSiteConfig extends systemConfigBase {

	/**
	 * Holds instance of the controllerMapper
	 *
	 * @var mvcControllerMapper
	 * @access protected
	 */
	protected $_ControllerMapper = false;

	/**
	 * Stores the parent siteConfig object
	 *
	 * @var mvcSiteConfig
	 * @access protected
	 */
	protected $_ParentConfig = false;

	/**
	 * Stores $_ServerName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ServerName;



	/**
	 * @see systemConfigBase::__construct()
	 *
	 * @param string $inConfigFile
	 * @param string $inServerName
	 */
	function __construct($inConfigFile = null, $inServerName = null) {
		$this->reset();
		if ( $inConfigFile !== null ) {
			$this->load($inConfigFile);
			$this->setSitePath(dirname($inConfigFile));
		}
		if ( $inServerName !== null ) {
			$this->setServerName($inServerName);
		}
	}

	/**
	 * Returns an instance of the mvcSiteConfig
	 *
	 * @return mvcSiteConfig
	 */
	static function getInstance() {
		return new mvcSiteConfig();
	}


	/**
	 * Reset config object
	 *
	 * @return boolean
	 */
	function reset() {
		$this->_ControllerMapper = false;
		$this->_ParentConfig = false;
		$this->_ServerName = null;
		parent::reset();
		return true;
	}

	/**
	 * Returns site path, the fully resolved path to the sites folder
	 *
	 * @return systemConfigParam
	 */
	function getSitePath() {
		return $this->getParam('site', 'path', '');
	}

	/**
	 * Set site path
	 *
	 * @param string $inPath
	 * @return mvcSiteConfig
	 */
	function setSitePath($inPath) {
		$this->getSection('site')->getParamSet()->addParam(
			new systemConfigParam(
				'path', $inPath, true
			)
		);
		return $this;
	}

	/**
	 * Returns the full path to $inFile in relation to the websites folder
	 *
	 * @param string $inFile
	 * @return string
	 */
	function getFilePath($inFile) {
		$file = utilityStringFunction::cleanDirSlashes($this->getSitePath() . system::getDirSeparator() . $inFile);
		if ( @file_exists($file) ) {
			return $file;
		} else {
			if ( $this->getParentConfig() ) {
				return $this->getParentConfig()->getFilePath($inFile);
			}
		}
		return false;
	}



	/**
	 * Returns the template engine to be used by this site
	 *
	 * @return systemConfigParam
	 */
	function getTemplateEngine() {
		return $this->getParam('site', 'templateEngine', 'smarty');
	}

	/**
	 * Returns the filename for the controllerMap file; returns a STRING and NOT the param object
	 *
	 * @return string
	 */
	function getControllerMapFilename() {
		return $this->getParam('site', 'controllerMapFilename', 'controllerMap.xml')->getParamValue();
	}

	/**
	 * Returns the controller map file
	 *
	 * @return systemConfigParam
	 */
	function getControllerMapFile() {
		return $this->getParam('site', 'controllerMap', $this->getSitePath() . system::getDirSeparator() . $this->getControllerMapFilename());
	}

	/**
	 * Returns the default controller for the site
	 *
	 * @return systemConfigParam
	 */
	function getDefaultController() {
		return $this->getParam('site', 'defaultController', 'home');
	}

	/**
	 * Returns the site specific loglevel
	 *
	 * @return systemConfigParam
	 */
	function getSiteLogLevel() {
		return $this->getParam('site', 'logLevel', systemLogLevel::WARNING);
	}

	/**
	 * Returns if the site is active or not (from the config file), defaults to false
	 *
	 * @return boolean
	 */
	function isActive() {
		return $this->getParam('site', 'active', false)->getParamValue();
	}

	/**
	 * Returns the site configured error controller, or the parents error controller
	 *
	 * @return systemConfigParam
	 */
	function getErrorController() {
		return $this->getParentParam('site', 'errorController', 'mvcErrorController');
	}

	/**
	 * Returns parent site name if set
	 *
	 * @return systemConfigParam
	 */
	function getParentSite() {
		return $this->getParentParam('site', 'parent');
	}

	/**
	 * Returns theme to apply to site
	 *
	 * @return systemConfigParam
	 */
	function getTheme() {
		return $this->getParentParam('site', 'theme');
	}

	/**
	 * Returns the character to use to separate a text string e.g. "this is the text" => "this_is_the_text"
	 *
	 * @return systemConfigParam
	 */
	function getUriTextSeparator() {
		return $this->getParentParam('site', 'uriTextSeparator');
	}



	/**
	 * Returns true if the Captcha system should be used for logins
	 *
	 * @return boolean
	 */
	function isCaptchaRequiredOnLogin() {
		return $this->getParam('login', 'useCaptcha', true)->getParamValue();
	}



	/**
	 * Returns if the autoload cache should auto-loaded on mvcAutoload init, default true
	 *
	 * @return boolean
	 */
	function isAutoloadCacheEnabled() {
		return $this->getParam('autoload', 'useCache', true)->getParamValue();
	}

	/**
	 * Returns if the autoload cache should auto-save on script exit, default true
	 *
	 * @return boolean
	 */
	function isAutoloadCacheAutoSaveEnabled() {
		return $this->getParam('autoload', 'autoSave', true)->getParamValue();
	}

	/**
	 * Returns if the site classes should always be parsed by mvcAutoload, default true
	 *
	 * @return boolean
	 */
	function getAlwaysLoadSiteClasses() {
		return $this->getParam('autoload', 'preloadSiteClasses', true)->getParamValue();
	}



	/**
	 * Returns true if multi-languages are enabled
	 *
	 * @return boolean
	 */
	function isI18nActive() {
		return $this->getParam('i18n', 'active', false)->getParamValue();
	}

	/**
	 * Returns the identifier for marking up / identifying text to be translated
	 *
	 * @return systemConfigParam
	 */
	function getI18nIndentifier() {
		return $this->getParam('i18n', 'identifier', 't');
	}

	/**
	 * Returns the default language to be used if none is specified
	 *
	 * @return systemConfigParam
	 */
	function getI18nDefaultLanguage() {
		return $this->getParam('i18n', 'defaultLanguage', 'en');
	}

	/**
	 * Returns the adaptor to be used for translating text (must be a valid translateAdaptor)
	 *
	 * @return systemConfigParam
	 */
	function getI18nAdaptor() {
		return $this->getParam('i18n', 'adaptor', 'array');
	}

	/**
	 * Returns the adaptor configuration parameters
	 *
	 * Configuration parameters should be specified as key=value pairs
	 * separated with a pipe | character. e.g. disableNotices=true|scan=directory
	 * will be converted to an array containing disableNotices => true,
	 * scan => directory.
	 *
	 * @return array
	 */
	function getI18nAdaptorOptions() {
		$options = $this->getParam('i18n', 'adaptorOptions', 'disableNotices=true|scan=directory')->getParamValue();
		return utilityStringFunction::createArrayFromString($options, '|', '=');
	}



	/**
	 * Returns the param from the parent site config
	 *
	 * @param string $inParam
	 * @return systemConfigParam
	 */
	function getParentParam($inSection, $inParam) {
		$value = $this->getParam($inSection, $inParam, null);
		if ( $value instanceof systemConfigParam && $value->getParamName() == $inParam && $value->getParamValue() !== null ) {
			return $value;
		} elseif ( $this->getParentConfig() ) {
			return $this->getParentConfig()->getParentParam($inSection, $inParam);
		}
		return null;
	}

	/**
	 * Returns the configured site classes for this site, returns a SECTION and NOT a param
	 *
	 * @return systemConfigSection
	 */
	function getSiteClasses() {
		return $this->getParentSection('classes');
	}

	/**
	 * Fetches a section and populates it with the config chains values
	 *
	 * @param string $inSection
	 * @return systemConfigSection
	 */
	function getParentSection($inSection) {
		$oParentObjects = false;
		if ( $this->getParentConfig() ) {
			$oParentObjects = $this->getParentConfig()->getParentSection($inSection);
		}
		if ( $oParentObjects instanceof systemConfigSection ) {
			$oObjects = $this->getSection($inSection);
			if ( $oObjects instanceof systemConfigSection && $oObjects->getParamSet()->paramCount() > 0 ) {
				foreach ( $oObjects->getParamSet() as $oParam ) {
					$oParentObjects->getParamSet()->addParam($oParam);
				}
			}
			return $oParentObjects;
		} else {
			return $this->getSection($inSection);
		}
	}


	/**
	 * Fetches the controller map; loading it if not set
	 *
	 * @return mvcControllerMapper
	 * @throws mvcSiteConfigNoControllerMapDefined
	 */
	function getControllerMapper() {
		if ( !$this->_ControllerMapper instanceof mvcControllerMapper ) {
			$this->_ControllerMapper = new mvcControllerMapper();
			if ( $this->getControllerMapFile() ) {
				$this->_ControllerMapper->load($this->getControllerMapFile());
			} else {
				throw new mvcSiteConfigNoControllerMapDefined();
			}
		}
		return $this->_ControllerMapper;
	}

	/**
	 * Return ParentConfig, if there is one to get
	 *
	 * @return mvcSiteConfig
	 */
	function getParentConfig() {
		if ( !$this->_ParentConfig instanceof mvcSiteConfig ) {
			if ( $this->getParentSite()->getParamValue() && strlen(trim($this->getParentSite()->getParamValue())) > 0 ) {
				$this->_ParentConfig = new mvcSiteConfig(dirname($this->getSitePath()) . system::getDirSeparator() . $this->getParentSite()->getParamValue() . system::getDirSeparator() . 'config.xml');
			}
		}
		return $this->_ParentConfig;
	}

	/**
	 * Returns $_ServerName
	 *
	 * @return string
	 */
	function getServerName() {
		return $this->_ServerName;
	}

	/**
	 * Set $_ServerName to $inServerName
	 *
	 * @param string $inServerName
	 * @return mvcSiteConfig
	 */
	function setServerName($inServerName) {
		if ( $inServerName !== $this->_ServerName ) {
			$this->_ServerName = $inServerName;
			$this->setModified();
		}
		return $this;
	}
}