<?php
/**
 * mvcViewEnginePhp.class.php
 * 
 * mvcViewEnginePhp class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEnginePhp
 * @version $Rev: 650 $
 */


/**
 * mvcViewEnginePhp class
 * 
 * Interface to a simple PHP "template" engine, for people who do not like
 * template engines.
 *
 * To enable it, set the config option templateEngine to php in the
 * site section of the websites config.xml file.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEnginePhp
 */
class mvcViewEnginePhp extends mvcViewEngineBase {
	
	/**
	 * @see mvcViewEngineBase::initialise()
	 */
	function initialise() {
		$this->_Engine = new mvcViewEngineGeneric();
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
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCompileDir()
	 */
	function setCompileDir($inCompileDir) {
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
		$this->getEngine()->assign($inVarName, $inVar);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::getTemplateVar()
	 */
	function getTemplateVar($inVarName) {
		return $this->getEngine()->getTemplateVar($inVarName);
	}
	
	/**
	 * @see mvcViewEngineBase::isCached()
	 */
	function isCached($inTemplate, $inCacheID = null, $inCompileID = null) {
		return false;
	}
	
	/**
	 * @see mvcViewEngineBase::clearCache()
	 */
	function clearCache() {
		return true;
	}
	
	
	
	/**
	 * @see mvcViewEngineBase::compile()
	 */
	function compile($inTemplate, $inCacheID = null, $inCompileID = null) {
		return $this->getEngine()->compile($inTemplate, $inCacheID , $inCompileID, false);
	}
	
	/**
	 * @see mvcViewEngineBase::render()
	 */
	function render($inTemplate, $inCacheID = null, $inCompileID = null) {
		$this->getEngine()->display($inTemplate, $inCacheID , $inCompileID);
	}
}