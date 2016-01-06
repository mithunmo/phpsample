<?php
/**
 * systemLogWriterFile class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLogWriterFile
 * @version $Rev: 707 $
 */


/**
 * systemLogWriterFile Class
 * 
 * Writes log messages to a file. By default all logs are created in the
 * dedicated logs folder in the framework root folder. This path can be
 * changed when the writer is instantiated. Only a single file writer
 * may write to a log file at any one time, but you can have multiple
 * file writers pointing to different locations.
 * 
 * Log files are appended to and writes try to use an exclusive lock. This
 * will work reasonably well on single server machines, however if you wish
 * to log the output from multiple web-servers to the same master log, there
 * may be concurrency issues and you should consider using the
 * {@link systemLogWriterDb database} writer instead.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLogWriterFile
 */
class systemLogWriterFile extends systemLogWriter {
	
	/**
	 * @see systemLogWriter::__construct()
	 */
	function __construct($inLogFile, $inLogFilter) {
		parent::__construct($inLogFile, $inLogFilter);
		
		if ( strlen($this->getLogLocation()) < 1 ) {
			throw new systemLogNoLogFileSpecified($this->getLogLocation());
		}
		$this->checkLogFile();
	}
	
	
	
	/**
	 * Main Methods
	 */
	
	/**
	 * Applies some generic formatting to the message
	 *
	 * @param string $inMessage
	 * @param string $inSource
	 * @return string
	 */
	protected function formatMessage($inMessage, $inSource) {
		/*
		 * Remove any carriage returns (ascii 13)
		 */
		$inMessage = preg_replace("/\r/", '', $inMessage);
		
		/*
		 * Prefix with a timestamp and the $source if set, using the mask set in the writer for the date
		 */
		$datestamp = date($this->getDateMask());
		
		/*
		 * Strip new lines and reformat with timestamp
		 */
		$inMessage = preg_replace("/\n/", "\n$datestamp".(($inSource) ? " $inSource " : ''), $inMessage);
		$inMessage = $datestamp.(($inSource) ? " $inSource" : '')." $inMessage\n";
		
		return $inMessage;
	}
	
	/**
	 * @see systemLogWriter::_put()
	 */
	protected function _put($inMessage, $inSource) {
		$inMessage = $this->formatMessage($inMessage, $inSource);
		
		if (!file_exists(dirname($this->getLogLocation()))) {
			@mkdir(dirname($this->getLogLocation()), system::getConfig()->getSystemLogFolderPermissions()->getParamValue(), true);
			@chgrp(dirname($this->getLogLocation()), system::getConfig()->getSystemGroup()->getParamValue());
		}
		if (!file_exists($this->getLogLocation())) {
			@touch($this->getLogLocation());
			@chmod($this->getLogLocation(), system::getConfig()->getSystemLogFilePermissions()->getParamValue());
			@chgrp($this->getLogLocation(), system::getConfig()->getSystemGroup()->getParamValue());
		}
		
		/*
		 * Output to log file
		 */
		$bytes = @file_put_contents($this->getLogLocation(), $inMessage, FILE_APPEND|LOCK_EX);
		if ( !$bytes ) {
			throw new systemLogWritingToFileFailed($this->getLogLocation());
		}
	}
	
	/**
	 * @see systemLogWriter::getUniqueId()
	 */
	function getUniqueId() {
		return get_class($this).'_'.$this->getLogLocation();
	}
	
	/**
	 * Checks that the logFile Base Path exists in the file to be written
	 *
	 * @return string
	 * @access protected
	 */
	protected function checkLogFile() {
		$logFile = utilityStringFunction::cleanDirSlashes($this->getLogLocation());
		$logPath = utilityStringFunction::cleanDirSlashes(system::getConfig()->getPathLogs()->getParamValue());
		
		if ( strlen($logFile) > 1 && strpos($logFile, $logPath) === false ) {
			$this->setLogLocation($logPath.system::getDirSeparator().$logFile);
		}
	}
}