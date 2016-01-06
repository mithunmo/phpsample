<?php
/**
 * mvcViewEngineGeneric
 *
 * Stored in mvcViewEngineGeneric.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEngineGeneric
 * @version $Rev: 650 $
 */


/**
 * mvcViewEngineGeneric
 *
 * A generic PHP template engine, you can assign vars and they can be accessed via
 * $this->VarName within the template. A cache layer can be added.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEngineGeneric
 */
class mvcViewEngineGeneric {
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	/**
	 * Stores $_TplVars
	 *
	 * @var array
	 * @access private
	 */
	private $_TplVars;
	
	/**
	 * Stores $_Buffer
	 *
	 * @var string
	 * @access private
	 */
	private $_Buffer;
	
	/**
	 * Stores $_TemplateDirs
	 *
	 * @var array
	 * @access private
	 */
	private $_TemplateDirs;
	
	
	
	/**
	 * Returns new mvcViewEngineGeneric
	 *
	 * @return mvcViewEngineGeneric
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * Returns true if $inVar isset in _TplVars
	 *
	 * @param string $inVar
	 * @return boolean
	 */
	function __isset($inVar) {
		return array_key_exists($inVar, $this->_TplVars);
	}
	
	/**
	 * Re-directs var fetches to the TplVars private member, returns null if not found
	 *
	 * @param string $inVar
	 * @return mixed
	 */
	function __get($inVar) {
		if ( array_key_exists($inVar, $this->_TplVars) ) {
			return $this->_TplVars[$inVar];
		} else {
			return null;
		}
	}
	
	/**
	 * Allows template vars to be set outside of the method (not recommended!)
	 *
	 * @param string $inVar
	 * @param mixed $inValue
	 * @return mvcViewEngineGeneric
	 */
	function __set($inVar, $inValue = null) {
		return $this->assign($inVar, $inValue);
	}
	
	/**
	 * Translate $inString through the translate system
	 *
	 * @param string $inString
	 * @param string $inLocale
	 * @return string
	 */
	function __($inString, $inLocale = null) {
		if ( false ) $oRequest = new mvcRequest();
		$oRequest = $this->getTemplateVar('oRequest');
		if ( $oRequest instanceof mvcRequest && $oRequest->getDistributor()->getSiteConfig()->isI18nActive() ) {
			$langLocale = $oRequest->getLocale();
			$langIdentifier = $oRequest->getDistributor()->getSiteConfig()->getI18nIndentifier()->getParamValue();
			$langAdaptor = $oRequest->getDistributor()->getSiteConfig()->getI18nAdaptor()->getParamValue();
			$langOptions = $oRequest->getDistributor()->getSiteConfig()->getI18nAdaptorOptions();
			if ( $langOptions instanceof utilityOutputWrapper ) {
				$langOptions = $langOptions->getSeed();
			}
			$langData = $oRequest->getDistributor()->getSiteConfig()->getSitePath()->getParamValue().'/libraries/lang/';
			
			$oTransAdap = translateManager::getInstance($langAdaptor, $langData, $langLocale, $langOptions);
			return $oTransAdap->translate($inString, $inLocale);
		}
		return $inString;
	}
	
	/**
	 * Escapes the string $inVar, optionally using $inCharset (default is UTF-8)
	 *
	 * @param string $inVar
	 * @param string $inCharset Valid htmlentities character set
	 * @return string
	 */
	function escape($inVar, $inCharset = 'UTF-8') {
		return htmlentities($inVar, ENT_COMPAT, $inCharset);
	}
	
	/**
	 * Resets object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_TplVars = array();
		$this->_Buffer = '';
		$this->_Modified = false;
		$this->_TemplateDirs = array();
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
	 * Set $_Modified to $inStatus
	 *
	 * @param boolean $inStatus
	 * @return mvcViewEngineGeneric
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
		return $this;
	}
	
	/**
	 * Returns $_TplVars
	 *
	 * @return array
	 * @access public
	 */
	function getTplVars() {
		return $this->_TplVars;
	}
	
	/**
	 * Set $_TplVars to $inTplVars
	 *
	 * @param array $inTplVars
	 * @return mvcViewEngineGeneric
	 * @access public
	 */
	function setTplVars($inTplVars) {
		if ( $this->_TplVars !== $inTplVars ) {
			$this->_TplVars = $inTplVars;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Buffer
	 *
	 * @return string
	 * @access public
	 */
	function getBuffer() {
		return $this->_Buffer;
	}
	
	/**
	 * Set $_Buffer to $inBuffer
	 *
	 * @param string $inBuffer
	 * @return mvcViewEngineGeneric
	 * @access public
	 */
	function setBuffer($inBuffer) {
		if ( $this->_Buffer !== $inBuffer ) {
			$this->_Buffer = $inBuffer;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_TemplateDirs
	 *
	 * @return array
	 * @access public
	 */
	function getTemplateDirs() {
		return $this->_TemplateDirs;
	}
	
	/**
	 * Either add or set template dir to $inTemplateDir, can be either a string or array of dirs
	 *
	 * @param string|array $inTemplateDir
	 * @return mvcViewEngineGeneric
	 * @access public
	 */
	function setTemplateDir($inTemplateDir) {
		if ( is_string($inTemplateDir) ) {
			if ( !in_array($inTemplateDir, $this->_TemplateDirs) ) {
				$this->_TemplateDirs[] = $inTemplateDir;
				$this->setModified();
			}
		} elseif ( is_array($inTemplateDir) ) {
			$this->_TemplateDirs = $inTemplateDir;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Assign item to template variables
	 *
	 * @param string|array $inVarName
	 * @param mixed $inValue
	 * @return mvcViewEngineGeneric
	 */
	function assign($inVarName, $inValue = null) {
		if ( is_array($inVarName) && is_null($inValue) ) {
			foreach ( $inVarName as $var => $value ) {
				$this->_TplVars[$var] = $value;
			}
			$this->setModified();
		} elseif ( is_string($inVarName) ) {
			$this->_TplVars[$inVarName] = $inValue;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns a previously assigned template var, null if not found
	 *
	 * @param string $inVarName
	 * @return mixed
	 */
	function getTemplateVar($inVarName) {
		if ( array_key_exists($inVarName, $this->_TplVars) ) {
			return $this->_TplVars[$inVarName];
		}
		return null;
	}

	/**
	 * Returns a previously assigned template var by reference null if not found
	 *
	 * @param string $inVarName
	 * @return mixed
	 */
	function &getTemplateVarByRef($inVarName) {
		if ( array_key_exists($inVarName, $this->_TplVars) ) {
			return $this->_TplVars[$inVarName];
		}
		return null;
	}
	
	/**
	 * Assign item to template variables by reference
	 *
	 * @param string $inVarName
	 * @param mixed &$inValue
	 * @return mvcViewEngineGeneric
	 */
	function assignByRef($inVarName, &$inValue = null) {
		if ( is_array($inVarName) && is_null($inValue) ) {
			foreach ( $inVarName as $var => $value ) {
				$this->_TplVars[$var] = &$value;
			}
			$this->setModified();
		} elseif ( is_string($inVarName) ) {
			$this->_TplVars[$inVarName] = &$inValue;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the number of vars assigned to the engine
	 *
	 * @return integer
	 */
	function getTplVarCount() {
		return count($this->_TplVars);
	}
	
	/**
	 * Unsets all vars assigned to the engine
	 *
	 * @return mvcViewEngineGeneric
	 */
	function clearTplVars() {
		if ( $this->getTplVarCount() > 0 ) {
			foreach ( $this->_TplVars as $var => $value ) {
				unset($this->_TplVars[$var]);
			}
		}
		return $this;
	}
	
	
	
	/**
	 * Compiles and either returns or displays the result
	 *
	 * @param string $inTemplate
	 * @param string $inCacheID
	 * @param string $inCompileID
	 * @param boolean $inDisplay
	 * @return string
	 */
	function compile($inTemplate, $inCacheID = null, $inCompileID = null, $inDisplay = false) {
		if ( stripos($inTemplate, 'file:') === 0 ) {
			$inTemplate = str_replace('file:', '', $inTemplate);
		}
		ob_start();
		include_once $inTemplate;
		$this->setBuffer(ob_get_clean());
		
		if ( $inDisplay ) {
			echo $this->getBuffer();
		} else {
			return $this->getBuffer();
		}
	}
	
	/**
	 * Executes the template and immediately displays the results
	 *
	 * @param string $inTemplate
	 * @param string $inCacheID
	 * @param string $inCompileID
	 * @return void
	 */
	function display($inTemplate, $inCacheID = null, $inCompileID = null) {
		$this->compile($inTemplate, $inCacheID, $inCompileID, true);
	}
}