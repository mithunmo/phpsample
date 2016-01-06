<?php
/**
 * mvcSiteTools
 *
 * Stored in mvcSiteTools.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcSiteTools
 * @version $Rev: 707 $
 */


/**
 * mvcSiteTools Class
 *
 * Site tools are used to get site properties. It wraps around the site config
 * and {@link mvcSiteBuilder} for creating new sites. The other purpose is to
 * provide access to a cache clearing method for the sites compiled templates.
 *
 * <code>
 * $oSite = new mvcSiteTools('example.com');
 * $oSite->getSiteConfig();
 * </code>
 * 
 * The domain name is the distributor name i.e. the folder name that is used in
 * the websites folder.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcSiteTools
 */
class mvcSiteTools {
	
	const SYSTEM_BASESITE_NAME = 'base';
	const SYSTEM_BASEADMINSITE_NAME = 'baseAdminSite';

	/**
	 * Container for static instances of mvcSiteTools
	 *
	 * @var array
	 * @access protected
	 * @static
	 */
	protected static $_Instances = array();

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Type
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Type;

	const TYPE_ADMIN = 'Admin';
	const TYPE_SITE = 'Site';

	/**
	 * Stores $_DomainName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DomainName;
	
	/**
	 * Stores parent site, used during site construction
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ParentSite;

	/**
	 * Stores $_SiteConfig
	 *
	 * @var mvcSiteConfig
	 * @access protected
	 */
	protected $_SiteConfig;
	
	/**
	 * Stores $_BuildFiles
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_BuildFiles;
	


	/**
	 * Returns a new instance of mvcSiteTools
	 *
	 * @param string $inDomainName
	 * @return mvcSiteTools
	 */
	function __construct($inDomainName = null) {
		$this->reset();
		if ( $inDomainName !== null ) {
			$this->setDomainName($inDomainName);
			$this->load();
		}
		return $this;
	}

	/**
	 * Get an instance of mvcSiteTools by domain name
	 *
	 * @param string $inDomainName
	 * @return mvcSiteTools
	 * @static
	 */
	public static function getInstance($inDomainName) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances) && count(self::$_Instances) > 0 ) {
			foreach ( self::$_Instances as $oSite ) {
				if ( $oSite->getDomainName() == $inDomainName ) {
					return $oSite;
				}
			}
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mvcSiteTools();
		$oObject->setDomainName($inDomainName);
		if ( $oObject->load() ) {
			self::$_Instances[] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mvcSiteTools for all currently defined sites
	 *
	 * @return array
	 * @static
	 */
	public static function listOfObjects() {
		$list = array();
		try {
			$sites = self::getListOfSites();
			foreach ( $sites as $site ) {
				$list[] = new mvcSiteTools($site);
			}
			
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $list;
	}
	
	/**
	 * Returns an array of all sites located in the current websites folder
	 *
	 * @return array
	 * @static
	 */
	public static function getListOfSites() {
		$arrFolders = glob(system::getConfig()->getPathWebsites().system::getDirSeparator().'*', GLOB_ONLYDIR);
		$ignore = array(
			system::getConfig()->getPathWebsites().system::getDirSeparator().'controllers',
			system::getConfig()->getPathWebsites().system::getDirSeparator().'views',
			system::getConfig()->getPathWebsites().system::getDirSeparator().'libraries',
		);
		
		$return = array();
		foreach ( array_diff($arrFolders, $ignore) as $folder ) {
			$return[] = str_replace(system::getConfig()->getPathWebsites().system::getDirSeparator(), '', $folder);
		}
		return $return;
	}
	
	/**
	 * Returns the list of themes in the base themes folder
	 *
	 * @return array
	 * @static
	 */
	public static function getListOfThemes() {
		$baseRoot = system::getConfig()->getPathWebsites().system::getDirSeparator().'base'.system::getDirSeparator();
		
		$arrFolders = glob($baseRoot.'themes'.system::getDirSeparator().'*', GLOB_ONLYDIR);
		$ignore = array(
			$baseRoot.'themes'.system::getDirSeparator().'shared',
		);
		
		$return = array();
		foreach ( array_diff($arrFolders, $ignore) as $folder ) {
			$return[] = str_replace($baseRoot.'themes'.system::getDirSeparator(), '', $folder);
		}
		return $return;
	}
	
	
	
	/**
	 * Loads additional site details, for future expansion
	 *
	 * @return boolean
	 */
	function load() {
		$return = true;
		if ( $this->getDomainName() ) {
			try {
				$this->getSiteConfig();
				$this->getType();
				$this->setParentSite($this->getSiteConfig()->getParentSite()->getParamValue());
				$this->setModified(false);
			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
				$return = false;
			}
		}
		return $return;
	}

	/**
	 * Loads a record by array
	 *
	 * @param array $inArray
	 */
	function loadFromArray($inArray) {
		$this->setType($inArray['type']);
		$this->setDomainName($inArray['domainName']);
		$this->setParentSite($inArray['parentSite']);
		$this->setModified(false);
	}

	/**
	 * Saves changes to the site
	 *
	 * @return boolean
	 */
	function save() {
		$return = true;
		if ( $this->isModified() ) {
			if ( $this->_Modified ) {
				$oBuilder = new mvcSiteBuilder($this->getType(), $this->getDomainName(), $this->getParentSite(), $this->getBuildFiles());
				$return = $oBuilder->build() && $return;
			}
			
			if ( $this->_SiteConfig instanceof mvcSiteConfig ) {
				$this->_SiteConfig->save();
			}
			$this->setModified(false);
		}
		return $return;
	}

	/**
	 * Deletes the site
	 *
	 * @return boolean
	 */
	function delete() {
		throw new mvcSiteToolsException('mvcSiteTools does not support deleting sites.');
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mvcSiteTools
	 */
	function reset() {
		$this->_Type = '';
		$this->_DomainName = '';
		$this->_ParentSite = null;
		$this->_SiteConfig = null;
		$this->_BuildFiles = false;
		$this->setModified(false);
		return $this;
	}
	
	/**
	 * Returns properties of object as an array
	 *
	 * @return array
	 */
	function toArray() {
		return get_object_vars($this);
	}
	
	
	
	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( !$modified && $this->_SiteConfig instanceof mvcSiteConfig ) {
			$modified = $this->_SiteConfig->isModified() || $modified;
		}
		return $modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mvcSiteTools
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns the current site type, either an admin or normal site.
	 * 
	 * This is performed by iterating back through the site inheritance
	 * looking for a 'baseAdminSite' in any part of the site history.
	 * Therefore if you create your own system, you should ensure that
	 * there is a 'baseAdminSite' for all admin derived sites.
	 *
	 * @return string
	 * @access public
	 */
	function getType() {
		if ( !$this->_Type ) {
			$this->_Type = self::TYPE_SITE;
			if ( $this->getSiteConfig()->getParentSite()->getParamValue() == self::SYSTEM_BASEADMINSITE_NAME || $this->getDomainName() == self::SYSTEM_BASEADMINSITE_NAME ) {
				$this->_Type = self::TYPE_ADMIN;
			} else {
				$oParent = $this->getSiteConfig()->getParentConfig();
				while ( is_object($oParent) && $oParent->getParentSite()->getParamValue() ) {
					if ( $oParent->getParentSite()->getParamValue() == self::SYSTEM_BASEADMINSITE_NAME ) {
						$this->_Type = self::TYPE_ADMIN;
						break;
					}
					$oParent = $oParent->getParentConfig();
				}
			}
		}
		return $this->_Type;
	}
	
	/**
	 * Set $_Type to $inType
	 *
	 * @param string $inType
	 * @return mvcSiteTools
	 * @access public
	 */
	function setType($inType) {
		if ( $inType !== $this->_Type ) {
			$this->_Type = $inType;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the current site domain, or rather the folder name
	 *
	 * @return string
	 * @access public
	 */
	function getDomainName() {
		return $this->_DomainName;
	}

	/**
	 * Set $_DomainName to DomainName
	 *
	 * @param string $inDomainName
	 * @return mvcSiteTools
	 * @access public
	 */
	function setDomainName($inDomainName) {
		if ( $inDomainName !== $this->_DomainName ) {
			$this->_DomainName = $inDomainName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_ParentSite
	 *
	 * @return string
	 * @access public
	 */
	function getParentSite() {
		return $this->_ParentSite;
	}
	
	/**
	 * Set $_ParentSite to $inParentSite
	 *
	 * @param string $inParentSite
	 * @return mvcSiteTools
	 * @access public
	 */
	function setParentSite($inParentSite) {
		if ( $this->_ParentSite !== $inParentSite ) {
			$this->_ParentSite = $inParentSite;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_BuildFiles
	 *
	 * @return boolean
	 */
	function getBuildFiles() {
		return $this->_BuildFiles;
	}
	
	/**
	 * Set $_BuildFiles to $inBuildFiles
	 *
	 * @param boolean $inBuildFiles
	 * @return mvcSiteTools
	 */
	function setBuildFiles($inBuildFiles) {
		if ( $inBuildFiles !== $this->_BuildFiles ) {
			$this->_BuildFiles = $inBuildFiles;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the full path to the current site folder in websites
	 * 
	 * @return string
	 */
	function getSitePath() {
		return system::getConfig()->getPathWebsites().system::getDirSeparator().$this->getDomainName().system::getDirSeparator();
	}
	
	
	
	/**
	 * Loads and returns the mvcSiteConfig if not already loaded
	 *
	 * @return mvcSiteConfig
	 */
	function getSiteConfig() {
		if ( !$this->_SiteConfig instanceof mvcSiteConfig && $this->getDomainName() ) {
			$this->_SiteConfig = new mvcSiteConfig($this->getSitePath().'config.xml');
		}
		return $this->_SiteConfig;
	}
	
	/**
	 * Attemps to delete all cache and compile files for the site
	 *
	 * @return void
	 */
	function clearSiteCacheFiles() {
		$folderName = str_replace('.','_', $this->getDomainName());
		
		try {
			$oEngine = mvcViewEngineFactory::getEngine($this->getSiteConfig()->getTemplateEngine()->getParamValue());
			$oEngine->setCacheDir($folderName);
			$oEngine->setCompileDir($folderName);
			$oEngine->setUseSubDirs(true);
			$oEngine->clearCache();
			$oEngine = null;
			unset($oEngine);
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
		}
		
		if ( $this->getSiteConfig()->isAutoloadCacheEnabled() ) {
			$dir = 
				mvcAutoload::getCacheFolder().
				system::getDirSeparator().
				$this->getDomainName().mvcAutoload::AUTOLOAD_CACHE_FILE;
			
			if ( @file_exists($dir) && @is_writable($dir) ) {
				$oFile = new fileObject($dir);
				if ( !$oFile->delete() ) {
					systemLog::error('Failed to remove file ('.$oFile->getOriginalFilename().') - check permissions');
				}
			}
		}
	}
	
	/**
	 * Returns the location of the site controllerMap file
	 *
	 * @return string
	 */
	function getControllerMapFile() {
		return $this->getSiteConfig()->getControllerMapFile();
	}
}