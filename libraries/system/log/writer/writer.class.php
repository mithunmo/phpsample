<?php
/**
 * systemLogWriter class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLogWriter
 * @version $Rev: 707 $
 */


/**
 * systemLogWriter Class
 * 
 * Base writer providing shared methods for sending log messages to a destination
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLogWriter
 */
abstract class systemLogWriter {
	
	/**
	 * Date mask to use to prefix each line with, any valid datetime mask can be used
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DateMask;
	
	/**
	 * Stores $_Filter
	 *
	 * @var systemLogFilter
	 * @access protected
	 */
	protected $_Filter;
	
	/**
	 * Stores $_LogLocation
	 *
	 * @var string
	 * @access protected
	 */
	protected $_LogLocation;
	
	/**
	 * Stores $_LastLogLevel
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_LastLogLevel;
	
	
	
	/**
	 * Returns new writer instance
	 *
	 * @param string $inLogLocation
	 * @param systemLogFilter $inLogFilter
	 * @return systemLogWriter
	 */
	function __construct($inLogLocation, $inLogFilter) {
		$this->_DateMask = system::getConfig()->getSystemLogDateFormat()->getParamValue();
		
		if ( !$inLogLocation ) {
			$inLogLocation = get_class($this);
		}
		if ( !$inLogFilter ) {
			$inLogFilter = new systemLogFilter();
		}
		$this->setLogLocation($inLogLocation);
		$this->setFilter($inLogFilter);
		return $this;
	}
	
	
	
	/**
	 * Main Methods
	 */
	
	/**
	 * Puts the log message into the destination
	 *
	 * @param string $inMessage
	 * @param string $inSource
	 * @param integer $inLogLevel
	 * @return void 
	 */
	final function put($inMessage, $inSource, $inLogLevel) {
		$this->setLastLogLevel($inLogLevel);
		if ( $this->isLoggable($inLogLevel) ) {
			$this->_put($inMessage, $inSource);
		}
	}
	
	/**
	 * Writer specific options for putting log information
	 *
	 * @param string $inMessage
	 * @param string $inSource
	 * @return void
	 */
	abstract protected function _put($inMessage, $inSource);
	
	/**
	 * Returns true if the message should be put
	 *
	 * @param integer $inLogLevel
	 * @return boolean
	 */
	function isLoggable($inLogLevel) {
		if ( $this->getFilter()->getMinLogLevel() <= $inLogLevel && $inLogLevel <= $this->getFilter()->getMaxLogLevel() ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Returns unqiue id for this writer
	 *
	 * @return string
	 */
	function getUniqueId() {
		return get_class($this);
	}
	
	
	
	/**
	 * Get / Set Methods
	 */
	
	/**
	 * Returns the date mask format
	 *
	 * @return string
	 */
	function getDateMask() {
		return $this->_DateMask;
	}
	
	/**
	 * Set $_DateMark to $inDateMask; $inDateMask is any valid date() format
	 *
	 * @param string $inDateMask
	 * @return systemLogWriter
	 */
	function setDateMask($inDateMask) {
		if ( $this->_DateMask !== $inDateMask ) {
			$this->_DateMask = $inDateMask;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns $_Filter
	 *
	 * @return systemLogFilter
	 */
	function getFilter() {
		return $this->_Filter;
	}
	 
	/**
	 * Sets $_Filter to $inFilter
	 *
	 * @param systemLogFilter $inFilter
	 * @return systemLogWriter
	 */
	function setFilter(systemLogFilter $inFilter) {
		if ( $this->_Filter !== $inFilter ) {
			$this->_Filter = $inFilter;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns $_LogLocation
	 *
	 * @return string
	 */
	function getLogLocation() {
		return $this->_LogLocation;
	}
	 
	/**
	 * Sets $_LogLocation to $inLogLocation
	 *
	 * @param string $inLogLocation
	 * @return systemLogWriter
	 */
	function setLogLocation($inLogLocation) {
		if ( $this->_LogLocation !== $inLogLocation ) {
			$this->_LogLocation = $inLogLocation;
			$this->_Modified = true;
		}
		return $this;
	}

	/**
	 * Returns $_LastLogLevel
	 *
	 * @return integer
	 */
	function getLastLogLevel() {
		return $this->_LastLogLevel;
	}
	
	/**
	 * Set $_LastLogLevel to $inLastLogLevel
	 *
	 * @param integer $inLastLogLevel
	 * @return systemLogWriter
	 */
	function setLastLogLevel($inLastLogLevel) {
		if ( $inLastLogLevel !== $this->_LastLogLevel ) {
			$this->_LastLogLevel = $inLastLogLevel;
			$this->_Modified = true;
		}
		return $this;
	}
}