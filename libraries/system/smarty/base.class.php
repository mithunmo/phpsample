<?php
/**
 * systemSmartyBase.class.php
 * 
 * Scorpio Framework System File
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemSmartyBase
 * @version $Rev: 844 $
 */


/**
 * systemSmartyBase
 * 
 * systemSmartyBase class provides some useful additional functions for Smarty and
 * setup routines to ensure that the cache and compile folders are always created
 * in a consistent manner. Any application using Smarty should run through this
 * adaptor.
 * 
 * To use; simply instantiate and pass the name of the folder you want the cache
 * files to be created in. This is usually the application or website domain name.
 * 
 * <code>
 * $oSmarty = new systemSmartyBase();
 * $oSmarty->setTemplateDir('/path/to/websites');
 * $oSmarty->setConfigDir('/path/to/config/folder');
 * $oSmarty->setCompileDir('myApp');
 * $oSmarty->setCacheDir('myApp');
 * $oSmarty->setUseSubDirs(true);
 * $oSmarty->assign('something','somevalue');
 * $oSmarty->display('template.tpl');
 * </code>
 * 
 * @package scorpio
 * @subpackage system
 * @category systemSmartyBase
 */
class systemSmartyBase extends Smarty {
	
	/**
	 * Create Smarty environment
	 *
	 * Note: we have to completely override the Smarty constructor to prevent
	 * it from calling setCacheDir and setCompileDir. Scorpio takes care of
	 * these folders and automatically sets them up in the create place when
	 * set by the calling code. Previously Smarty set the class properties
	 * directly, but since 3.1 these are handled via set methods.
	 *
	 * @return systemSmartyBase
	 */
	function __construct() {
		// selfpointer needed by some other class methods
		$this->smarty = $this;
		if ( is_callable('mb_internal_encoding') ) {
			mb_internal_encoding(SMARTY_RESOURCE_CHAR_SET);
		}
		$this->start_time = microtime(true);
		// set default dirs
		$this->setTemplateDir('.' . DS . 'templates' . DS)
			->setPluginsDir(SMARTY_PLUGINS_DIR)
			->setConfigDir('.' . DS . 'configs' . DS);

		$this->debug_tpl = 'file:' . SMARTY_DIR . 'debug.tpl';
		if ( isset($_SERVER['SCRIPT_NAME']) ) {
			$this->assignGlobal('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);
		}
		
		$this->compile_dir = false;
		$this->cache_dir = false;
		$this->addPluginsDir(
			system::getConfig()->getPathLibraries().
			system::getDirSeparator().
			'system'.
			system::getDirSeparator().
			'smarty'.
			system::getDirSeparator().
			'plugins'
		);
	}


	
	/**
	 * Sets the compile dir, this should be the name of the application and NOT a full path
	 * 
	 * @param string $compile_dir
	 * @return systemSmartyBase
	 */
	public function setCompileDir($compile_dir = null) {
		if ( is_null($compile_dir) ) {
			$compile_dir = system::getScriptFilename();
		}
		$this->checkFolderForPath($compile_dir);
		
		$compile_dir = system::getConfig()->getPathTemplateCompile().system::getDirSeparator().$compile_dir.system::getDirSeparator();
		if ( !@file_exists($compile_dir) ) {
			systemLog::warning("$compile_dir does not exist");
			$this->createFolder($compile_dir);
		}
		$this->compile_dir = $compile_dir;

		if (!isset(Smarty::$_muted_directories[$this->compile_dir])) {
			//Smarty::$_muted_directories[$this->compile_dir] = null;
		}

		return $this;
	}

	/**
	 * Set cache dir, this should be the name of the application and NOT a full path
	 * 
	 * @param string $cache_dir
	 * @return systemSmartyBase
	 */
	public function setCacheDir($cache_dir = null) {
		if ( is_null($cache_dir) ) {
			$cache_dir = system::getScriptFilename();
		}
		$this->checkFolderForPath($cache_dir);
		
		$cache_dir = system::getConfig()->getPathTemplateCache().system::getDirSeparator().$cache_dir.system::getDirSeparator();
		if ( !@file_exists($cache_dir) ) {
			systemLog::warning("$cache_dir does not exist");
			$this->createFolder($cache_dir);
		}
		$this->cache_dir = $cache_dir;

		if (!isset(Smarty::$_muted_directories[$this->cache_dir])) {
			//Smarty::$_muted_directories[$this->cache_dir] = null;
		}

		return $this;
	}
	
	/**
	 * Override Smarty default fetch to implement enforced compile/cache locations
	 *
	 * @see Smarty::fetch()
	 * @param string $template
	 * @param string $cache_id (optional)
	 * @param string $compile_id (optional)
	 * @param string $parent (optional)
	 * @param boolean $display (optional) default false
	 * @return string
	 */
	public function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false) {
		if ( !$this->compile_dir ) {
			$this->setCompileDir();
		}
		if ( !$this->cache_dir && $this->caching ) {
			$this->setCacheDir();
		}
		
		return parent::fetch($template, $cache_id, $compile_id, $parent, $display);
	}
	
	
	
	/**
	 * Checks if the folder is a path, returning the last item if it is
	 *
	 * @param string &$inFolder
	 */
	protected function checkFolderForPath(&$inFolder) {
		if ( stripos($inFolder, system::getDirSeparator()) !== false ) {
			systemLog::warning("Compile dir contains path information, stripping ($inFolder)");

			$pieces = array_reverse(
				explode(
					system::getDirSeparator(), preg_replace('/[.]{2,}/', '.', $inFolder)
				)
			);

			foreach ( $pieces as $piece ) {
				if ( strlen(trim($piece)) > 0 && $piece != '.' && $piece != '..' ) {
					$inFolder = $piece;
					break;
				}
			}

			systemLog::warning("Compile dir is now: $inFolder");
		}
	}
	
	/**
	 * Creates the folder $inFolder
	 *
	 * @param string $inFolder
	 */
	protected function createFolder($inFolder) {
		if ( !@file_exists($inFolder) ) {
			systemLog::message("Creating smarty folder: $inFolder");
			if ( !@mkdir($inFolder, 0775, true) ) {
				 throw new systemException("Unable to create folder ($inFolder)");
			}
		}
	}
}