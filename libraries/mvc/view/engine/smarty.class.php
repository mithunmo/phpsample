<?php
/**
 * mvcViewEngineSmarty.class.php
 * 
 * mvcViewEngineSmarty class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEngineSmarty
 * @version $Rev: 650 $
 */


/**
 * mvcViewEngineSmarty class
 * 
 * Interface to smarty for the MVC view system. This is the default
 * view engine and does not require any specific configuration.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEngineSmarty
 */
class mvcViewEngineSmarty extends mvcViewEngineBase {
	
	/**
	 * @see mvcViewEngineBase::initialise()
	 */
	function initialise() {
		$this->_Engine = new systemSmartyBase();
		$this->_Engine->loadFilter('pre','translate');
	    $this->_Engine->setCacheModifiedCheck(true);
	    if ( !system::getConfig()->isProduction() ) {
	   		$this->_Engine->setCompileCheck(true);
	   		$this->_Engine->setForceCompile(true);
	    }
	}
	
	/**
	 * @see mvcViewEngineBase::setCompileCheck()
	 */
	function setCompileCheck($inStatus) {
		$this->getEngine()->setCompileCheck($inStatus);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCaching()
	 */
	function setCaching($inStatus) {
		$this->getEngine()->setCaching($inStatus);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCacheLifetime()
	 */
	function setCacheLifetime($inLifetime) {
		$this->getEngine()->setCacheLifetime($inLifetime);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setTemplateDir()
	 */
	function setTemplateDir($inTemplateDir) {
		$this->getEngine()->setTemplateDir($inTemplateDir);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setConfigDir()
	 */
	function setConfigDir($inConfigDir) {
		$this->getEngine()->setConfigDir($inConfigDir);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCompileDir()
	 */
	function setCompileDir($inCompileDir) {
		$this->getEngine()->setCompileDir($inCompileDir);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCacheDir()
	 */
	function setCacheDir($inCacheDir) {
		$this->getEngine()->setCacheDir($inCacheDir);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setUseSubDirs()
	 */
	function setUseSubDirs($inStatus) {
		$this->getEngine()->setUseSubDirs($inStatus);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::assign()
	 */
	function assign($inVarName, $inVar = null) {
		$this->getEngine()->assign($inVarName, $inVar);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::getTemplateVar()
	 */
	function getTemplateVar($inVarName) {
		return $this->getEngine()->getTemplateVars($inVarName);
	}
	
	/**
	 * @see mvcViewEngineBase::isCached()
	 */
	function isCached($inTemplate, $inCacheID = null, $inCompileID = null) {
		return $this->getEngine()->isCached($inTemplate, $inCacheID , $inCompileID);
	}
	
	/**
	 * @see mvcViewEngineBase::clearCache()
	 */
	function clearCache() {
		$res = $this->getEngine()->cache->clearAll();
		if ( $res ) {
			$res = $this->getEngine()->utility->clearCompiledTemplate() && $res;
		}
		return $res;
	}
	
	
	
	/**
	 * @see mvcViewEngineBase::compile()
	 */
	function compile($inTemplate, $inCacheID = null, $inCompileID = null) {
		return $this->getEngine()->fetch($inTemplate, $inCacheID , $inCompileID);
	}
	
	/**
	 * @see mvcViewEngineBase::render()
	 */
	function render($inTemplate, $inCacheID = null, $inCompileID = null) {
		$this->getEngine()->display($inTemplate, $inCacheID , $inCompileID);
	}
}