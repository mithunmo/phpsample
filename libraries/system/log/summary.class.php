<?php
/**
 * systemLogSummary class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLogSummary
 * @version $Rev: 736 $
 */


/**
 * systemLogSummary
 * 
 * Parses the log files for errors and critical notices and compiles
 * the data into a string for display or email.
 *
 * @package scorpio
 * @subpackage system
 * @category systemLogSummary
 */
class systemLogSummary implements systemReporterInterface {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_LogLevels
	 *
	 * @var array
	 * @access protected
	 */
	protected $_LogLevels;
	
	/**
	 * Stores $_Dates
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Dates;
	
	
	
	/**
	 * Creates a new log summary instance
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * Collates and returns the log information
	 * 
	 * @return string
	 */
	function getData() {
		return $this->_getDataFromLogs();
	}
	
	/**
	 * Resets the object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_LogLevels = array(systemLogLevel::CRITICAL, systemLogLevel::ERROR, systemLogLevel::EXCEPTION);
		$this->_Dates = array();
		$this->setModified(false);
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
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return systemLogSummary
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns $_LogLevels
	 *
	 * @return array
	 */
	function getLogLevels() {
		return $this->_LogLevels;
	}
	
	/**
	 * Returns the log levels as strings for grep
	 * 
	 * @return array
	 */
	function getLogLevelsAsStrings() {
		$return = array();
		foreach ( $this->_LogLevels as $level ) {
			$return[] = systemLogLevel::convertLogLevelToString($level);
		}
		return $return;
	}
	
	/**
	 * Adds a log level to report on
	 * 
	 * @param integer $inLogLevel
	 * @return systemLogSummary
	 */
	function addLogLevel($inLogLevel) {
		if ( !in_array($inLogLevel, $this->_LogLevels) ) {
			$this->_LogLevels[] = $inLogLevel;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Set $_LogLevels to $inLogLevels
	 *
	 * @param array $inLogLevels
	 * @return systemLogSummary
	 */
	function setLogLevels(array $inLogLevels) {
		if ( $inLogLevels !== $this->_LogLevels ) {
			$this->_LogLevels = $inLogLevels;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Dates
	 *
	 * @return array
	 */
	function getDates() {
		return $this->_Dates;
	}
	
	/**
	 * Adds a date to report on
	 * 
	 * @param date $inDate
	 * @return systemLogSummary
	 */
	function addDate($inDate) {
		if ( !in_array($inDate, $this->_Dates) ) {
			$this->_Dates[] = $inDate;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Set $_Dates to $inDates
	 *
	 * @param array $inDates
	 * @return systemLogSummary
	 */
	function setDates(array $inDates) {
		if ( $inDates !== $this->_Dates ) {
			$this->_Dates = $inDates;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Locates all .log files within the system Logs folder
	 * 
	 * @return array(fileObject)
	 * @access private
	 */
	private function _getLogFiles() {
		$return = array();
		$files = fileObject::parseDir(system::getConfig()->getPathLogs());
		if ( count($files) > 0 ) {
			foreach ( $files as $oFile ) {
				if ( $oFile->getExtension() == 'log' ) {
					$return[] = $oFile;
				}
			}
		}
		
		return $return;
	}
	
	/**
	 * Compiles log information from all available logs
	 * 
	 * @return string
	 * @access private
	 */
	private function _getDataFromLogs() {
		$str = "";
		$files = $this->_getLogFiles();
		$files = array_reverse($files);
		$days = '('.implode(')|(',$this->getDates()).')';
		$errorTypes = $this->getLogLevelsAsStrings();
		
		foreach ( $files as $oFile ) {
			foreach ( $errorTypes as $errorType ) {
				$grepString = 'grep -E "^'.$days.'" @@filename@@ | grep "\['.$errorType.'\]"';
				$errorString = $this->_grepLogfile($oFile->getOriginalFilename(), $grepString);
				
				if ( strlen($errorString) > 0 ) {
					$str .= "------------------------------------------\n";
					$str .= "$errorType for {$oFile->getOriginalFilename()} \n";
					$str .= "------------------------------------------\n";
					$str .= $errorString;
					$str .= "\n";
					$str .= "\n";
				}
			}
		}
		return $str;
	}

	/**
	 * Calls the CLI grep command to pull information from the log file
	 *
	 * @param string $inLogFileName
	 * @param string $inRegex
	 * @return string
	 * @access private
	 */
	private function _grepLogfile($inLogFileName, $inRegex) {
		$data = '';
		
		$inLogFileName = escapeshellarg($inLogFileName);
		if ( !preg_match('/@@filename@@/', $inRegex) ) {
			$grep = "grep {$inRegex} {$inLogFileName}";
		} else {
			$grep = str_replace('@@filename@@', $inLogFileName, $inRegex);
		}
		
		systemLog::info("$grep");
		$data .= shell_exec($grep);
		
		return $data;
	}
}