<?php
/**
 * mvcViewEngineSavant3.class.php
 * 
 * mvcViewEngineSavant3 class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEngineSavant3
 * @version $Rev: 650 $
 */


/**
 * mvcViewEngineSavant3 class
 * 
 * Interface to Savant3 for the MVC view system, requires Savant3 be setup
 * and the appropriate autoload cache file created.
 *
 * To enable it, set the config option templateEngine to savant3 in the
 * site section of the websites config.xml file.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEngineSavant3
 */
class mvcViewEngineSavant3 extends mvcViewEngineBase {
	
	/**
	 * @see mvcViewEngineBase::initialise()
	 */
	function initialise() {
		$this->_Engine = new Savant3();
		if ( !system::getConfig()->isProduction() ) {
			$this->_Engine->setExceptions(true);
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
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setTemplateDir()
	 */
	function setTemplateDir($inTemplateDir) {
		$this->getEngine()->setPath('template', $inTemplateDir);
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
		return $this->getEngine()->$inVarName;
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
		return $this->getEngine()->fetch($this->_stripTemplateResource($inTemplate));
	}
	
	/**
	 * @see mvcViewEngineBase::render()
	 */
	function render($inTemplate, $inCacheID = null, $inCompileID = null) {
		$this->getEngine()->display($this->_stripTemplateResource($inTemplate));
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
		$res = $this->getEngine()->getConfig('template_path');
		if ( is_array($res) && count($res) > 0 && $res[0] != './' ) {
			$inTemplate = str_replace($res[0], '', $inTemplate);
		}
		return $inTemplate;
	}
}