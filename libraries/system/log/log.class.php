<?php
/**
 * systemLog class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLog
 * @version $Rev: 722 $
 */


/**
 * systemLog Class
 * 
 * Main system log handling class. This is used for all logging with in the
 * framework. systemLog supports any number of systemLogWriters. The only
 * requirement is that each writer have a unique target or log name. Certain
 * log writers can only be used once ({@link systemLogWriterScreen screen} and
 * {@link systemLogWriterCli cli}).
 * 
 * systemLog is a static object with only a single instance ever used.
 * 
 * If called directly: systemLog::getInstance()->log() a default log file is
 * created in the current log folder with a name and path based on the current
 * script. By default messages are filtered from ALWAYS to WARNING.
 * 
 * Additional static methods are included to make it more convenient to log
 * messages at a specific level.
 * 
 * Finally, a configurable source can be defined that will be prepended to all
 * log messages. The source is an object containing various key or key value
 * pairs of data. See {@link systemLogSource} for more details.
 * 
 * <code>
 * // log a message
 * systemLog::message('this will always be logged');
 * 
 * // log a warning
 * systemLog::warning('this is a warning');
 * 
 * // set-up a custom log writer and then write to it
 * $oWriter = new systemLogWriterFile(
 * 		'my/log/file/inlogs/folder.log',
 * 		new systemLogFilter(
 * 			systemLogLevel::ALWAYS, systemLogLevel::WARNING
 * 		)
 * );
 * systemLog::getInstance($oWriter)->setLogLevel(systemLogLevel::WARNING);
 * systemLog::message('this will now go to the custom file');
 * </code>
 * 
 * As each writer can have a custom filter and log file it is possible to set
 * up multiple writers to capture specific error messages or events. An example
 * would be capturing all critical errors and emailing them to a set of devs
 * who are charged with bug-fixing.
 * 
 * The global log level is set within this class by calling setLogLevel to one
 * of the defined constants in {@link systemLogLevel}.
 * 
 * Note:
 * It is possible to end up in a situation where no log information can be
 * recorded as the log writer is failing. In a production environment this will
 * cause the app to fail silently with possible errors being generated in the 
 * PHP error log (if it has been enabled!). In development (or non-production)
 * environments the specific exception will be caught and displayed along with
 * a trace.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLog
 */
class systemLog extends baseSet {
	
	/**
	 * Sets the maximum line width of error messages before the line wraps
	 *
	 * @var integer
	 */
	const MAX_LINE_WIDTH = 110;
	
	/**
	 * Instance of systemLog class
	 *
	 * @var systemLog object
	 * @access private
	 * @static 
	 */
	private static $_Instance = false;
	
	/**
	 * Current logging level
	 *
	 * @var integer
	 * @access private
	 */
	private $_LogLevel = 0;

	/**
	 * Stores $_Source
	 *
	 * @var systemLogSource
	 * @access private
	 */
	private $_Source;

	/**
	 * If true, the exception handler will provide the backtrace as
	 * variables to the errorHandler giving a (very) large amount of
	 * debug data. Default is false.
	 *
	 * @var boolean
	 */
	private $_UseExtendedExceptionData = false;
	
	
	
	/**
	 * Returns new instance of systemLog
	 *
	 * @return systemLog
	 */
	private function __construct() {
		$this->resetWriters();
	}
	
	
	
	/**
	 * Static Methods
	 */
	
	/**
	 * Returns the current active instance of systemLog or creates a new one
	 *
	 * @param systemLogWriter $inWriter
	 * @return systemLog
	 * @access public
	 * @static 
	 */
	public static function getInstance($inWriter = false) {
		if ( !self::$_Instance ) {
			self::$_Instance = new self();
			self::$_Instance->setUseExtendedExceptionData(system::getConfig()->getSystemLogUseExtendedExceptionData()->getParamValue());
		}
		if ( $inWriter && is_object($inWriter) && $inWriter instanceof systemLogWriter ) {
			self::$_Instance->setWriter($inWriter);
		}
		
		if (self::$_Instance->countWriters() == 0) {
			/*
			 * Set log path to the current file since nothing is specified
			 */
			if ( !defined('LOGFILE_NAME') ) {
				if ( system::getScriptFilename() ) {
					$logFile =
						system::getConfig()->getPathLogs()->getParamValue().system::getDirSeparator().
						preg_replace("/.php$/i", '', system::getScriptRelativePath().system::getDirSeparator().system::getScriptFilename()).
						'.log';
				} else {
					$logFile =
						system::getConfig()->getPathLogs()->getParamValue().system::getDirSeparator().
						'error.log';
				}
			} else {
				$logFile = LOGFILE_NAME;
			}
			
			$inWriter = system::getConfig()->getSystemLogType()->getParamValue();
			if ( class_exists($inWriter) ) {
				$inWriter = new $inWriter(
					$logFile,
					new systemLogFilter(
						systemLogLevel::ALWAYS,
						system::getConfig()->getSystemLogLevel()->getParamValue()
					)
				);
				self::$_Instance->setWriter($inWriter);
			} else {
				trigger_error(__CLASS__.'::'.__FUNCTION__."() must be passed a valid systemLogWriter class name. $inWriter is not a valid class");
			}
		}
		if ( self::$_Instance->getLogLevel() == 0 ) {
			self::$_Instance->setLogLevel(system::getConfig()->getSystemLogLevel()->getParamValue());
		}
		return self::$_Instance;
	}
	
	/**
	 * Static method to be used with set_error_handler
	 *
	 * @param integer $inErrno
	 * @param string $inErrmsg
	 * @param string $inFilename
	 * @param integer $inLinenum
	 * @param array $inVars
	 * @access public
	 * @static
	 */
	public static function errorHandler($inErrno, $inErrmsg, $inFilename, $inLinenum, $inVars) {
		$inErrno = $inErrno & error_reporting();
    	if ($inErrno == 0) {
    		return;
    	}

		if (isset($_SERVER['SERVER_NAME'])) {
			$inErrmsg .= "\nWebpage: http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
			if (count($_POST) > 0) {
				$inErrmsg .= "\nPost: ".http_build_query($_POST);
			}
		}
		
		/*
		 * Ignore some errors that are too generic or not very interesting
		 */
		if ($inErrno == 8 || $inErrno == 0 || $inErrno == 2048 ) {
			return;
		}
		
		/*
		 * If the error is in a library we want to know the calling script
		 */
		$basePath = system::getConfig()->getBasePath();
		$inFilename = str_replace($basePath, '', $inFilename);
		if ( preg_match("/libraries/", $inFilename) || preg_match('/.class.php$/', $inFilename) ) {
			if ( system::getScriptPath() ) {
				$scriptPath = str_replace(
					$basePath, '', system::getScriptPath().system::getDirSeparator().system::getScriptFilename()
				);
				$inFilename = "at line $inLinenum in $inFilename used by script $scriptPath";
			} else {
				$inFilename = "at line $inLinenum in $inFilename used by webscript ".system::getScriptFilename();
			}
		} else {
			$inFilename = "at line $inLinenum in script $inFilename";
		}
		
		$err  = str_repeat('*', self::MAX_LINE_WIDTH)."\n";
		$err .= systemLogLevel::convertErrorNoToString($inErrno) ." (ERRNO=$inErrno)\n";
		$err .= 'Unhandled '.($inErrno == systemLogLevel::EXCEPTION ? 'exception' : 'error')." $inFilename.\n\n";
		$err .= "ERROR MESSAGE:\n$inErrmsg";
		
		if ( $inErrno != systemLogLevel::EXCEPTION ) {
			$err .= "\n\nSTACK TRACE:\n";
			$err .= implode("\n", self::_buildStackTrace(debug_backtrace()));
		}
		$err .= "\n\nBASE PATH:\n$basePath";
		
		if ( isset($inVars) && count($inVars) > 0 && $inErrno != systemLogLevel::EXCEPTION && self::getInstance()->getUseExtendedExceptionData() ) {
			$err .= "\n\nVARIABLES = ".print_r($inVars,1);
		}
		
		/*
		 * Convert PHP error no into a Scorpio logLevel; do this to filter off
		 * errors that are trivial or that can be ignored safely e.g. user notice
		 * or deprecated (PHP >5.3
		 */
		switch ( $inErrno ) {
			case 8192:
			case 16384:
			case 2048:
				$logLevel = systemLogLevel::INFO;
			break;
			
			case E_NOTICE:
			case E_USER_NOTICE:
				$logLevel = systemLogLevel::NOTICE;
			break;
			
			case E_USER_WARNING:
			case E_WARNING:
				$logLevel = systemLogLevel::WARNING;
			break;
			
			default:
				$logLevel = systemLogLevel::CRITICAL;
		}
		
		/*
		 * Get systemLog instance and write out message to the current log, whatever that happens to be
		 */
		$oLog = systemLog::getInstance();
		$oLog->log($err, $logLevel);
		
		/*
		 * Capture old writers and write event to platform error.log
		 */
		$oldWriters = $oLog->getWriters();
		$oLog->resetWriters();
		$oLog->setWriter(
			new systemLogWriterFile(
				system::getConfig()->getPathLogs()->getParamValue().system::getDirSeparator().'error.log',
				new systemLogFilter(systemLogLevel::CRITICAL, systemLogLevel::WARNING)
			)
		);
		
		try {
			$oLog->log($err, $logLevel);
		} catch ( Exception $e ) {
			echo "Error writing to central error.log: ".$e->getMessage();
			exit;
		}
		
		/*
		 * Kill process on these error numbers
		 */
		$exitCond = array(1, 16, 32, 64, 128, 256, 512, 4096);
		if ( in_array($inErrno, $exitCond) ) {
			$oLog->log(str_repeat('*', self::MAX_LINE_WIDTH), systemLogLevel::CRITICAL);
			$oLog->log(system::getScriptFilename().' cannot continue, exiting', systemLogLevel::CRITICAL);
			if ( system::getIsCli() ) {
				echo wordwrap($err, self::MAX_LINE_WIDTH)."\n";
			} elseif ( ini_get('display_errors') ) {
				echo "<pre>$err</pre>";
			}
			exit;
		}
		
		/*
		 * reset old writers
		 */
		$oLog->resetWriters();
		foreach ( $oldWriters as $writer ) {
			$oLog->setWriter($writer);
		}
	}
	
	/**
	 * Exception handler to be used with set_exception_handler()
	 *
	 * @param Exception $inException
	 * @access public
	 * @static
	 */
	public static function exceptionHandler($inException) {
		/*
		 * Assign vars from exception
		 */
		$errno = $inException->getCode();
		if ( $errno == 0 ) {
			$errno = systemLogLevel::EXCEPTION;
		}
		
		$errmsg = $inException->getMessage();
		$filename = $inException->getFile();
		$linenum = $inException->getLine();
		$vars = $inException->getTrace();
		
		$errmsg .= "\n\nEXCEPTION STACK TRACE:\n".get_class($inException).' thrown in:'."\n";
		$errmsg .= implode("\n", self::_buildStackTrace($vars));

		if ( !systemLog::getInstance()->getUseExtendedExceptionData() ) {
			$vars = array();
		}
		
		self::errorHandler($errno, $errmsg, $filename, $linenum, $vars);
	}
	
	/**
	 * Converts a back trace into an array of strings
	 *
	 * @param array $inTrace
	 * @return array
	 * @access private
	 * @static
	 */
	private static function _buildStackTrace($inTrace) {
		$cnt = count($inTrace);
		$traceErrors = array();
		
		for ( $i=0; $i<$cnt; $i++ ) {
			if ( isset($inTrace[$i]['class']) ) {
				$string = '#'.$i.' '.$inTrace[$i]['class'].$inTrace[$i]['type'].$inTrace[$i]['function'].'(';
			} else {
				$string = '#'.$i.' '.$inTrace[$i]['function'].'(';
			}
			
			if ( isset($inTrace[$i]['args']) && count($inTrace[$i]['args']) > 0 ) {
				$args = array();
				foreach ( $inTrace[$i]['args'] as $arg ) {
					if ( is_array($arg) || is_object($arg) ) {
						$arg = gettype($arg);
					}
					if ( strlen($arg) > 10 ) {
						$arg = substr($arg, 0, 10).'...';
					}
					$args[] = $arg;
				}
				$string .= '\''.implode("', '", $args).'\'';
			}
			$string .= ')';
			
			if ( isset($inTrace[$i]['file']) ) {
				$string .= ' in '.str_replace(system::getConfig()->getBasePath(), '', $inTrace[$i]['file']);
				$string .= '@'.$inTrace[$i]['line'];
			}
			
			$traceErrors[] = $string;
		}
		return $traceErrors;
	}
	
	/**
	 * Log a message to the current log instance
	 *
	 * @param string $inMessage
	 * @static
	 */
	public static function message($inMessage) {
		self::always($inMessage);
	}
	
	/**
	 * Log a message to the current log instance
	 *
	 * @param string $inMessage
	 * @static
	 */
	public static function always($inMessage) {
		self::getInstance()->log($inMessage, systemLogLevel::ALWAYS);
	}
	
	/**
	 * Log a message to the current log instance
	 *
	 * @param string $inMessage
	 * @static
	 */
	public static function critical($inMessage) {
		self::getInstance()->log($inMessage, systemLogLevel::CRITICAL);
	}
	
	/**
	 * Log a message to the current log instance
	 *
	 * @param string $inMessage
	 * @static
	 */
	public static function debug($inMessage) {
		self::getInstance()->log($inMessage, systemLogLevel::DEBUG);
	}
	
	/**
	 * Log a message to the current log instance
	 *
	 * @param string $inMessage
	 * @static
	 */
	public static function error($inMessage) {
		self::getInstance()->log($inMessage, systemLogLevel::ERROR);
	}
	
	/**
	 * Log a message to the current log instance
	 *
	 * @param string $inMessage
	 * @static
	 */
	public static function info($inMessage) {
		self::getInstance()->log($inMessage, systemLogLevel::INFO);
	}
	
	/**
	 * Log a message to the current log instance
	 *
	 * @param string $inMessage
	 * @static
	 */
	public static function notice($inMessage) {
		self::getInstance()->log($inMessage, systemLogLevel::NOTICE);
	}
	
	/**
	 * Log a message to the current log instance
	 *
	 * @param string $inMessage
	 * @static
	 */
	public static function warning($inMessage) {
		self::getInstance()->log($inMessage, systemLogLevel::WARNING);
	}

	/**
	 * Log a message to the current log instance
	 *
	 * @param string $inMessage
	 * @static
	 */
	public static function auditSuccess($inMessage) {
		self::getInstance()->log($inMessage, systemLogLevel::AUDIT_SUCCESS);
	}

	/**
	 * Log a message to the current log instance
	 *
	 * @param string $inMessage
	 * @static
	 */
	public static function auditFailure($inMessage) {
		self::getInstance()->log($inMessage, systemLogLevel::AUDIT_FAILURE);
	}
	
	
	
	/**
	 * Main Methods
	 */
	
	/**
	 * Sends the message to the writers for logging.
	 *
	 * @param mixed $inMessage The message to log
	 * @param integer $inLogLevel The level to log at, if null uses system default loglevel
	 * @return void
	 */
	function log($inMessage, $inLogLevel = null) {
		if ( $inLogLevel === null ) {
			$inLogLevel = $this->getLogLevel();
		}
		if ( is_array($inMessage) || is_object($inMessage) ) {
			$inMessage = print_r($inMessage, 1);
		}
		
		if ( $this->countWriters() > 0 ) {
			if ( false ) $oWriter = new systemLogWriter();
			foreach ( $this as $oWriter ) {
				try {
					$oWriter->put($inMessage, $this->getSource()->getSourceString($inLogLevel), $inLogLevel);
				} catch ( Exception $e ) {
					if ( !system::getConfig()->isProduction() ) {
						if ( system::getIsCli() ) {
							echo $e->getMessage(), "\n", $e->getTraceAsString();
						} else {
							echo $e->getMessage(), '<br />', nl2br($e->getTraceAsString());
						}
						exit;
					}
				}
			}
		}
	}
	
	/**
	 * Returns the number of writers in the system
	 *
	 * @return integer
	 */
	function countWriters() {
		return $this->_itemCount();
	}
	
	/**
	 * Returns writer with $inUniqueId
	 *
	 * @param string $inUniqueId
	 * @return systemLogWriter
	 */
	function getWriter($inUniqueId) {
		return $this->_getItem($inUniqueId);
	}
	
	/**
	 * Returns all writers
	 *
	 * @return array
	 */
	function getWriters() {
		return $this->_getItem();
	}
	
	/**
	 * Adds a new writer
	 *
	 * @param systemLogWriter $oWriter
	 * @return systemLog
	 */
	function setWriter(systemLogWriter $oWriter) {
		return $this->_setItem($oWriter->getUniqueId(), $oWriter);
	}
	
	/**
	 * Remove the writer with $inUniqueId, can also be instance of the writer
	 *
	 * @param string|systemLogWriter $inUniqueId
	 * @return systemLog
	 */
	function removeWriter($inUniqueId) {
		if ( $inUniqueId instanceof systemLogWriter ) {
			$inUniqueId = $inUniqueId->getUniqueId();
		}
		return $this->_removeItem($inUniqueId);
	}
	
	/**
	 * Removes all writers from systemLog
	 *
	 * @return systemLog
	 */
	function resetWriters() {
		return $this->_resetSet();
	}
	
	/**
	 * Return log level
	 * 
	 * @return integer
	 */
	function getLogLevel() {
		return $this->_LogLevel ;
	}
	
	/**
	 * Set log level
	 * 
	 * @param integer $_LogLevel
	 * @return systemLog
	 */
	function setLogLevel($inLogLevel) {
		if ( $this->_LogLevel !== $inLogLevel ) {
			$this->_LogLevel = $inLogLevel;
		}
		return $this;
	}
	
	/**
	 * Returns $_Source
	 *
	 * @return systemLogSource
	 */
	function getSource() {
		if ( !$this->_Source instanceof systemLogSource ) {
			$this->_Source = new systemLogSource();
		}
		return $this->_Source;
	}
	 
	/**
	 * Sets $_Source to $inSource
	 *
	 * @param systemLogSource $inSource
	 * @return systemLog
	 */
	function setSource($inSource) {
		if ( $inSource ) {
			if ( is_string($inSource) ) {
				$oSource = new systemLogSource();
				$oSource->setSource($inSource, false);
			} elseif ( $inSource instanceof systemLogSource ) {
				$oSource = $inSource;
			} elseif ( is_array($inSource) && count($inSource) > 0 ) {
				$oSource = new systemLogSource();
				foreach ( $inSource as $source => $value ) {
					$oSource->setSource($source, $value);
				}
			}
			
			if ( $oSource instanceof systemLogSource ) {
				$this->_Source = $oSource;
			}
		}
		return $this;
	}

	/**
	 * Returns true if exception handler should report extended data
	 *
	 * @return boolean
	 */
	function getUseExtendedExceptionData() {
		return $this->_UseExtendedExceptionData;
	}

	/**
	 * Sets if exception handler should report extended data (true) or not (false)
	 *
	 * @param boolean $inStatus
	 * @return systemLog
	 */
	function setUseExtendedExceptionData($inStatus = true) {
		if ( $inStatus !== $this->_UseExtendedExceptionData ) {
			$this->_UseExtendedExceptionData = (bool) $inStatus;
		}
		return $this;
	}
}