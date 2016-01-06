<?php
/**
 * systemLogFilter class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLogFilter
 * @version $Rev: 707 $
 */


/**
 * systemLogFilter Class
 * 
 * Provides controls for when a message should be logged based on the log level.
 * Log levels are taken from {@link systemLogLevel}
 * 
 * <code>
 * $oFilter = new systemLogFilter(systemLogLevel::CRITICAL, systemLogLevel::DEBUG);
 * </code>
 * 
 * The filter is used with a {@link systemLogWriter} object.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLogFilter
 */
class systemLogFilter {
	
	/**
	 * Stores $_MinLogLevel
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MinLogLevel;
	
	/**
	 * Stores $_MaxLogLevel
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MaxLogLevel;
	
	
	
	/**
	 * Returns a new systemLogFilter
	 *
	 * @param integer $inMinLogLevel
	 * @param integer $inMaxLogLevel
	 * @return systemLogFilter
	 */
	function __construct($inMinLogLevel = null, $inMaxLogLevel = null) {
		if ( is_null($inMinLogLevel) ) {
			$inMinLogLevel = systemLogLevel::ALWAYS;
		}
		if ( is_null($inMaxLogLevel) ) {
			$inMaxLogLevel = system::getConfig()->getSystemLogLevel()->getParamValue();
		}
		$this->setMinLogLevel($inMinLogLevel);
		$this->setMaxLogLevel($inMaxLogLevel);
		return $this;
	}
	
	
	
	/**
	 * Returns $_MinLogLevel
	 *
	 * @return integer
	 */
	function getMinLogLevel() {
		return $this->_MinLogLevel;
	}
	 
	/**
	 * Sets $_MinLogLevel to $inMinLogLevel
	 *
	 * @param integer $inMinLogLevel
	 * @return systemLogFilter
	 */
	function setMinLogLevel($inMinLogLevel) {
		if ( $this->_MinLogLevel !== $inMinLogLevel ) {
			$this->_MinLogLevel = $inMinLogLevel;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns $_MaxLogLevel
	 *
	 * @return integer
	 */
	function getMaxLogLevel() {
		return $this->_MaxLogLevel;
	}
	 
	/**
	 * Sets $_MaxLogLevel to $inMaxLogLevel
	 *
	 * @param integer $inMaxLogLevel
	 * @return systemLogFilter
	 */
	function setMaxLogLevel($inMaxLogLevel) {
		if ( $this->_MaxLogLevel !== $inMaxLogLevel ) {
			$this->_MaxLogLevel = $inMaxLogLevel;
			$this->_Modified = true;
		}
		return $this;
	}
}