<?php
/**
 * systemConfigParam.class.php
 * 
 * System config class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemConfigParam
 * @version $Rev: 650 $
 */


/**
 * systemConfigParam
 * 
 * systemConfigParam class holds a params details. Used by systemConfigBase
 * when parsing the config files.
 * 
 * <code>
 * // new param that cannot be overridden
 * $oParam = new systemConfigParam('name','value', false);
 * 
 * // new param that can be overridden
 * $oParam2 = new systemConfigParam('name2','value2', true);
 * </code>
 * 
 * @package scorpio
 * @subpackage system
 * @category systemConfigParam
 */
class systemConfigParam {
	
	/**
	 * Stores $_ParamName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ParamName				= '';
	/**
	 * Stores $_ParamValue
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_ParamValue				= false;
	/**
	 * Stores $_CanBeOverridden
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_CanBeOverridden			= true;
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified				= false;
	
	
	
	/**
	 * Returns a new systemConfigParam
	 * 
	 * $inParamValue is optional but $inParamName and $inCanBeOverridden are required
	 *
	 * @param string $inParamName (required)
	 * @param mixed $inParamValue (optional)
	 * @param boolean $inCanBeOverridden (required)
	 */
	function __construct($inParamName = null, $inParamValue = null, $inCanBeOverridden = null) {
		if ( $inParamName !== null && $inCanBeOverridden !== null ) {
			$this->setParamName($inParamName);
			$this->setParamValue($inParamValue);
			$this->setCanBeOverridden($inCanBeOverridden);
			$this->setModified(false);
		}
	}
	
	/**
	 * Resets object to defaults
	 *
	 * @return void
	 */
	function reset() {
		$this->_CanBeOverridden = true;
		$this->_ParamName = '';
		$this->_ParamValue = false;
		$this->_Modified = false;
	}
	
	/**
	 * If object is used as a var, return the paramValue
	 *
	 * @return mixed
	 */
	function __toString() {
		return $this->_ParamValue;
	}
	
	
	
	/**
	 * Returns Modified
	 *
	 * @return boolean
	 */
	function getModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set Modified property
	 *
	 * @param boolean $inModified
	 * @return systemConfigParam
	 */
	function setModified($inModified = true) {
		$this->_Modified = $inModified;
		return $this;
	}
	
	/**
	 * Returns CanBeOverridden
	 *
	 * @return boolean
	 */
	function getCanBeOverridden() {
		return $this->_CanBeOverridden;
	}
	
	/**
	 * Set CanBeOverridden property
	 *
	 * @param boolean $inCanBeOverridden
	 * @return systemConfigParam
	 */
	function setCanBeOverridden($inCanBeOverridden) {
		if ( $inCanBeOverridden !== $this->_CanBeOverridden ) {
			$this->_CanBeOverridden = $inCanBeOverridden;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns ParamValue
	 *
	 * @return mixed
	 */
	function getParamValue() {
		return $this->_ParamValue;
	}
	
	/**
	 * Set ParamValue property
	 *
	 * @param mixed $inParamValue
	 * @return systemConfigParam
	 */
	function setParamValue($inParamValue) {
		if ( $this->getCanBeOverridden() && $inParamValue !== $this->_ParamValue ) {
			$this->_ParamValue = $inParamValue;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns ParamName
	 *
	 * @return string
	 */
	function getParamName() {
		return $this->_ParamName;
	}
	
	/**
	 * Set ParamName property
	 *
	 * @param string $inParamName
	 * @return systemConfigParam
	 */
	function setParamName($inParamName) {
		if ( $inParamName !== $this->_ParamName ) {
			$this->_ParamName = $inParamName;
			$this->_Modified = true;
		}
		return $this;
	}
}