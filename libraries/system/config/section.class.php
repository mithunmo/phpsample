<?php
/**
 * systemConfigSection.class.php
 * 
 * System config section class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemConfigSection
 * @version $Rev: 650 $
 */


/**
 * systemConfigSection
 * 
 * systemConfigSection is a param that can contain other params. This is a 
 * section from the config file and holds other parameters. Like a param it
 * can be marked as allowing overrides or not.
 * 
 * <code>
 * // create a new section
 * $oSection = new systemConfigSection('sectionName', true, true);
 * 
 * // make a new param
 * $oParam = new systemConfigParam('param','value',false);
 * 
 * // attach the param
 * $oSection->getParamSet()->addParam($oParam);
 * </code>
 * 
 * @package scorpio
 * @subpackage system
 * @category systemConfigSection
 */
class systemConfigSection extends systemConfigParam {
	
	/**
	 * Stores $_ParamSet
	 *
	 * @var systemConfigParamSet
	 * @access protected
	 */
	protected $_ParamSet			= false;
	
	
	
	/**
	 * Creates a new systemConfigSection
	 * 
	 * $inParamName and $inCanBeOverridden are required
	 * 
	 * @see systemConfigParam::__construct()
	 */
	function __construct($inParamName = null, $inParamValue = null, $inCanBeOverridden = null) {
		parent::__construct($inParamName, $inParamValue, $inCanBeOverridden);
	}
	
	/**
	 * @see systemConfigParam::reset()
	 */
	function reset() {
		$this->_ParamSet = null;
		parent::reset();
	}
	
	
	
	/**
	 * Returns systemConfigParamSet
	 *
	 * @return systemConfigParamSet
	 */
	function getParamSet() {
		if ( !$this->_ParamSet instanceof  systemConfigParamSet ) {
			$this->_ParamSet = new systemConfigParamSet($this->_ParamName);
			$this->_Modified = true;
		}
		return $this->_ParamSet;
	}
	
	/**
	 * Set ParamSet property
	 *
	 * @param systemConfigParamSet $inSystemConfigParamSet
	 * @return systemConfigSection
	 */
	function setParamSet($inSystemConfigParamSet) {
		if ( $inSystemConfigParamSet !== $this->_ParamSet ) {
			$this->_ParamSet = $inSystemConfigParamSet;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns true if the section has been modified
	 *
	 * @return boolean
	 */
	function getModified() {
		$modified = $this->_Modified;
		if ( $this->_ParamSet !== false ) {
			$modified = $modified || $this->getParamSet()->getModified();
		}
		return $modified;
	}
}