<?php
/**
 * systemLogSource class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLogSource
 * @version $Rev: 650 $
 */


/**
 * systemLogSource Class
 * 
 * Holds attributes to be applied to the log file when a message is written.
 * These attributes can be current values such as process information, or customer
 * reference or product id etc.
 * 
 * Items can be added as either keys or key => value pairs. A handful of constants
 * are pre-defined for use within the Scorpio framework. Adding additional values
 * here is discouraged as they will be overwritten with any updates.
 * 
 * Example:
 * <code>
 * $oSource = systemLogSource::getInstance('Controller', 'View');
 * $oSource->setSource('Template','file.tpl');
 * </code>
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLogSource
 */
class systemLogSource extends baseSet {
	
	const DESC_SYSTEM_USER_ID = 'UserID';
	const DESC_SYSTEM_USERNAME = 'Username';
	
	const DESC_CONTROLLER = 'Ctrlr';
	const DESC_CONTROLLER_ACTION = 'Act';
	const DESC_CONTROLLER_VIEW = 'View';
	
	const DESC_WURFL_ID = 'WurflID';
	const DESC_USER_AGENT = 'UA';
	
	/**
	 * Compiled source components as a string
	 *
	 * @var string
	 */
	protected $_SourceStr = null;
	
	
	
	/**
	 * Returns a new instance of the systemLogSource
	 *
	 * @return systemLogSource
	 */
	function __construct() {
		$this->resetSource();
	}
	
	
	
	/**
	 * Static Methods
	 */
	
	/**
	 * Returns a single instance of systemLogSource
	 *
	 * @param mixed $inSource
	 * @param mixed $inSourceFlags
	 * @return systemLogSource
	 */
	static function getInstance($inSource, $inSourceFlags = null) {
		$oSource = new systemLogSource();
		$oSource->setSource($inSource, $inSourceFlags);
		return $oSource;
	}
	
	
	
	/**
	 * Main Methods
	 */
	
	/**
	 * Returns source as a string, if $inLogLevel is set, prepends log level as a string
	 *
	 * @param integer $inLogLevel
	 * @return string
	 */
	function getSourceString($inLogLevel = null) {
		if ( $inLogLevel !== null ) {
			$inLogLevel = '['.systemLogLevel::convertLogLevelToString($inLogLevel).']';
		}
		if ( $this->_SourceStr == null ) {
			if ( $this->countEntries() > 0 ) {
				$str = array();
				foreach ( $this as $source => $value ) {
					if ( !is_null($value) && $value ) {
						$str[] = "$source=$value";
					} else {
						$str[] = $source;
					}
				}
				$this->_SourceStr = '['.implode('][', $str).']';
			}
		}
		return $inLogLevel.$this->_SourceStr;
	}
	
	
	
	/**
	 * List Methods
	 */
	
	/**
	 * Return source or entire array of sources
	 *
	 * @param string $inSource
	 * @return mixed
	 */
	function getSource($inSource = null) {
		return $this->_getItem($inSource);
	}
	
	/**
	 * Set the log source
	 *
	 * @param string $inSource
	 * @param mixed $inValue
	 * @return systemLogSource
	 */
	function setSource($inSource, $inValue = null) {
		if ( is_array($inSource) && $inValue === null ) {
			$this->_setItem($inSource);
			$this->_SourceStr = null;
		} else {
			if ( $this->getSource($inSource) ) {
				if ( $this->getSource($inSource) !== $inValue ) {
					$this->_setItem($inSource, $inValue);
					$this->_SourceStr = null;
				}
			} else {
				$this->_setItem($inSource, $inValue);
				$this->_SourceStr = null;
			}
		}
		return $this;
	}
	
	/**
	 * Removes the item from the source index
	 *
	 * @param string $inSource
	 * @return systemLogSource
	 */
	function removeSource($inSource) {
		return $this->_removeItem($inSource);
	}
	
	/**
	 * Returns the number of items attached to the source
	 *
	 * @return integer
	 */
	function countEntries() {
		return $this->_itemCount();
	}
	
	/**
	 * Resets source
	 *
	 * @return systemLogSource
	 */
	function resetSource() {
		$this->_SourceStr = null;
		return $this->_resetSet();
	}
}