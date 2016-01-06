<?php
/**
 * mvcViewEngineBase.class.php
 * 
 * mvcViewEngineBase class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEngineBase
 * @version $Rev: 650 $
 */


/**
 * mvcViewEngineBase class
 * 
 * Abstract engine interface for view layer, provides a means to make view method calls
 * standardised regardless of rendering engine. If a method is not implemented in the
 * template engine, then it should still be implemented but just return the engine
 * instead e.g. for those template layers that do not have a config directory; simply
 * return $this.
 * 
 * All engines should inherit from this class an must implement all methods whether the
 * engine actually uses them or not. The extension class must be named:
 * 
 * mvcViewEngineTYPE
 * 
 * Where TYPE is the short name for the engine e.g. Smarty has an engine class named
 * mvcViewEngineSmarty, PHPTal could be mvcViewEnginePhptal etc.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEngineBase
 */
abstract class mvcViewEngineBase {
	
	/**
	 * Holds an instance of the actual template engine (e.g. Smarty, PHPTal etc)
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_Engine;
	
	
	
	/**
	 * Returns a new instance of the mvcViewEngineBase
	 *
	 * @return mvcViewEngineBase
	 */
	function __construct() {
		$this->initialise();
	}
	
	
	
	/**
	 * Returns the template engine instance
	 *
	 * @return mvcViewEngineBase
	 */
	function getEngine() {
		return $this->_Engine;
	}
	
	/**
	 * Performs any engine specific initialisation of the engine
	 *
	 * @return void
	 */
	abstract function initialise();
	
	/**
	 * Enable or disable template compile checking
	 *
	 * @param boolean $inStatus
	 * @return mvcViewEngineBase
	 */
	abstract function setCompileCheck($inStatus);
	
	/**
	 * Enable or disable caching
	 *
	 * @param boolean $inStatus
	 * @return mvcViewEngineBase
	 */
	abstract function setCaching($inStatus);
	
	/**
	 * Set the cache lifetime in seconds
	 *
	 * @param integer $inLifetime
	 * @return mvcViewEngineBase
	 */
	abstract function setCacheLifetime($inLifetime);
	
	/**
	 * Set the template directory
	 *
	 * @param string $inTemplateDir
	 * @return mvcViewEngineBase
	 */
	abstract function setTemplateDir($inTemplateDir);
	
	/**
	 * Set the config directory for the template engine (if applicable)
	 *
	 * @param string $inConfigDir
	 * @return mvcViewEngineBase
	 */
	abstract function setConfigDir($inConfigDir);
	
	/**
	 * Set the compile directory where templates are compiled (if applicable)
	 *
	 * @param string $inCompileDir
	 * @return mvcViewEngineBase
	 */
	abstract function setCompileDir($inCompileDir);
	
	/**
	 * Set the cache directory where complete templates are cached (if applicable)
	 *
	 * @param string $inCacheDir
	 * @return mvcViewEngineBase
	 */
	abstract function setCacheDir($inCacheDir);
	
	/**
	 * Use sub folders in the cache directory (if applicable)
	 *
	 * @param boolean $inStatus
	 * @return mvcViewEngineBase
	 */
	abstract function setUseSubDirs($inStatus);
	
	/**
	 * Assign a value to the template
	 *
	 * @param string $inVarName
	 * @param mixed $inVar
	 * @return mvcViewEngineBase
	 */
	abstract function assign($inVarName, $inVar = null);
	
	/**
	 * Returns an already assigned template var
	 *
	 * @param string $inVarName
	 * @return mixed
	 */
	abstract function getTemplateVar($inVarName);
	
	/**
	 * Returns true if the template is cached
	 *
	 * @param string $inTemplate
	 * @param string $inCacheID
	 * @param string $inCompileID
	 * @return boolean
	 */
	abstract function isCached($inTemplate, $inCacheID = null, $inCompileID = null);
	
	/**
	 * Clears all cached and compiled template files for the current request
	 *
	 * @return boolean
	 */
	abstract function clearCache();
	
	
	
	/**
	 * Compiles but does not display the template, returns compiled template as a string
	 *
	 * @param string $inTemplate
	 * @param string $inCacheID
	 * @param string $inCompileID
	 * @return string
	 */
	abstract function compile($inTemplate, $inCacheID = null, $inCompileID = null);
	
	/**
	 * Compiles and displays the template to the browsing agent
	 *
	 * @param string $inTemplate
	 * @param string $inCacheID
	 * @param string $inCompileID
	 * @return void
	 */
	abstract function render($inTemplate, $inCacheID = null, $inCompileID = null);
}