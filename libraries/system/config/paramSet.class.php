<?php
/**
 * systemConfigParamSet.class.php
 * 
 * systemConfigParamSet class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemConfigParamSet
 * @version $Rev: 650 $
 */


/**
 * systemConfigParamSet
 * 
 * Handles a set of params and is used internally by the {@link systemConfigSection}
 * object.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemConfigParamSet
 */
class systemConfigParamSet extends baseSet {
 	
 	/**
 	 * Stores $_Section
 	 *
 	 * @var string
 	 * @access protected
 	 */
 	protected $_Section			= '';
 	
 	
 	
 	/**
 	 * Returns a new systemConfigParamSet
 	 *
 	 * @param systemConfigSection $inSection
 	 * @return systemConfigParamSet
 	 */
 	function __construct($inSection = null) {
 		if ( $inSection !== null ) {
 			$this->setSection($inSection);
 		}
 	}
 	
 	
 	
 	/**
 	 * Adds the param to the set but only if it can be overridden
 	 *
 	 * @param systemConfigParam $oParam
 	 * @throws systemConfigParamCannotBeOverridden
 	 */
 	function addParam(systemConfigParam $oParam) {
 		$key = false;
 		foreach ( $this as $oExParam ) {
 			if ( $oExParam->getParamName() == $oParam->getParamName() ) {
 				$key = $this->_findItem($oExParam);
 				break;
 			}
 		}
 		if ( $key !== false ) {
 			if ( $this->_getItem($key)->getCanBeOverridden() && $this->_getItem($key) !== $oParam ) {
 				return $this->_setItem($key, $oParam);
 			} else {
 				throw new systemConfigParamCannotBeOverridden($oParam);
 			}
 		} else {
 			return $this->_setValue($oParam);
 		}
 	}
 	
 	/**
 	 * Returns the param with name $inParamName; will create new param if not found and set value to $inDefault
 	 * if no $inParamName set returns false.
 	 *
 	 * @param string $inParamName
 	 * @param mixed $inDefault
 	 * @return mixed
 	 */
 	function getParam($inParamName = false, $inDefault = false) {
 		if ( $inParamName ) {
 			if ( $this->paramCount() > 0 ) {
	 			foreach ( $this as $oParam ) {
	 				if ( $oParam->getParamName() == $inParamName ) {
	 					return $oParam;
	 				}
	 			}
 			}
 			$oParam = new systemConfigParam($inParamName, $inDefault, true);
 			$oParam->setModified();
 			$this->addParam($oParam);
 			return $oParam;
 		}
 		return false;
 	}
 	
 	/**
 	 * Removes a param from the set
 	 *
 	 * @param systemConfigParam $oParam
 	 * @return systemConfigParamSet
 	 */
 	function removeParam(systemConfigParam $oParam) {
 		return $this->_removeItemWithValue($oParam);
 	}
 	
 	/**
 	 * Removes all params from the set
 	 *
 	 * @return systemConfigParamSet
 	 */
 	function clearParams() {
 		return $this->_resetSet();
 	}
 	
 	/**
 	 * Returns the number of params in the set
 	 *
 	 * @return integer
 	 */
 	function paramCount() {
 		return $this->_itemCount();
 	}
 	
 	
 	
 	/**
 	 * Returns Section
 	 *
 	 * @return string
 	 */
 	function getSection() {
 		return $this->_Section;
 	}
 	
 	/**
 	 * Set Section property
 	 *
 	 * @param string $inSection
 	 * @return systemConfigParamSet
 	 */
 	function setSection($inSection) {
 		if ( $inSection !== $this->_Section ) {
 			$this->_Section = $inSection;
 			$this->_Modified = true;
 		}
 		return $this;
 	}
 	
 	/**
 	 * Returns true if any part of the set has changed
 	 *
 	 * @return boolean
 	 */
 	function getModified() {
 		$modified = $this->_Modified;
 		if ( $this->_itemCount() > 0 ) {
	 		foreach ( $this as $oParam ) {
	 			$modified = $modified || $oParam->getModified();
	 		}
 		}
 		return $modified;
 	}
}