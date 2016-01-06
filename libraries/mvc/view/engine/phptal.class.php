<?php
/**
 * mvcViewEnginePhptal.class.php
 * 
 * mvcViewEnginePhptal class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEnginePhptal
 * @version $Rev: 650 $
 */


/**
 * mvcViewEnginePhptal class
 * 
 * Interface to PHPTAL for the MVC view system, requires PHPTAL be setup
 * and the appropriate autoload cache file created.
 *
 * To enable it, set the config option templateEngine to phptal in the
 * site section of the websites config.xml file.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEnginePhptal
 */
class mvcViewEnginePhptal extends mvcViewEngineBase {
	
	/**
	 * @see mvcViewEngineBase::initialise()
	 */
	function initialise() {
		$this->_Engine = new PHPTAL();
		if ( !system::getConfig()->isProduction() ) {
			$this->_Engine->setForceReparse(true);
		}
	}
	
	/**
	 * @see mvcViewEngineBase::setCompileCheck()
	 */
	function setCompileCheck($inStatus) {
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCaching()
	 */
	function setCaching($inStatus) {
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCacheLifetime()
	 */
	function setCacheLifetime($inLifetime) {
		// PHPTAL works in cache days, not seconds
		$this->getEngine()->setCacheLifetime(($inLifetime/86400));
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setTemplateDir()
	 */
	function setTemplateDir($inTemplateDir) {
		$this->getEngine()->setTemplateRepository($inTemplateDir);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setConfigDir()
	 */
	function setConfigDir($inConfigDir) {
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCompileDir()
	 */
	function setCompileDir($inCompileDir) {
		$compileDir = system::getConfig()->getPathTemplateCompile().system::getDirSeparator().$inCompileDir;
		
		$this->getEngine()->setPhpCodeDestination($compileDir);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCacheDir()
	 */
	function setCacheDir($inCacheDir) {
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setUseSubDirs()
	 */
	function setUseSubDirs($inStatus) {
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::assign()
	 */
	function assign($inVarName, $inVar = null) {
		$this->getEngine()->set($inVarName, $inVar);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::getTemplateVar()
	 */
	function getTemplateVar($inVarName) {
		return $this->getEngine()->$inVarName;
	}
	
	/**
	 * @see mvcViewEngineBase::isCached()
	 */
	function isCached($inTemplate, $inCacheID = null, $inCompileID = null) {
		// no way of knowing if PHPTAL has pre-compiled the template
		return false;
	}
	
	/**
	 * @see mvcViewEngineBase::clearCache()
	 */
	function clearCache() {
		// trick PHPTAL into removing everything
		$this->getEngine()->setCacheLifetime(0);
		$this->getEngine()->cleanUpGarbage();
		return true;
	}
	
	
	
	/**
	 * @see mvcViewEngineBase::compile()
	 */
	function compile($inTemplate, $inCacheID = null, $inCompileID = null) {
		$this->getEngine()->setTemplate($this->_stripTemplateResource($inTemplate));
		return $this->getEngine()->execute();
	}
	
	/**
	 * @see mvcViewEngineBase::render()
	 */
	function render($inTemplate, $inCacheID = null, $inCompileID = null) {
		$this->getEngine()->setTemplate($this->_stripTemplateResource($inTemplate));
		echo $this->getEngine()->execute();
	}
	
	/**
	 * Removes the 'file:' or 'res:' and full path prefix that templates have 
	 *
	 * @param string $inTemplate
	 * @return string
	 * @access private
	 */
	private function _stripTemplateResource($inTemplate) {
		if ( strpos($inTemplate, ':') !== false ) {
			 $inTemplate = substr($inTemplate, strpos($inTemplate, ':')+1);
		}
		$res = $this->getEngine()->getTemplateRepositories();
		if ( is_array($res) && count($res) > 0 && $res[0] != './' ) {
			$inTemplate = str_replace($res[0], '', $inTemplate);
		}
		return $inTemplate;
	}
}