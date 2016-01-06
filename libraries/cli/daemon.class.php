<?php
/**
 * cliDaemon Class
 * 
 * Stored in daemon.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliDaemon
 * @version $Rev: 707 $
 */


/**
 * cliDaemon Class
 * 
 * The cliDaemon class contains the necessary logic for building a PHP daemon. Process
 * controls are required from {@link cliProcessControls} as the posix functions can
 * not be classed and must be called explicitly.
 * 
 * A single method requires implementation: execute() and this takes an instance of the
 * cliRequest (in keeping with the cliApplication interface). An optional method
 * terminate(), can be overloaded to provide additional shutdown instructions or to
 * handle shutdown tasks beyond the defaults. Terminate is called from the destructor()
 * method which is called on daemon kills.
 * 
 * All daemons that inherit this class should NOT replace the constructor, or if this
 * is required, the parent constructor must be called as this installs the process
 * handling features, shutdown function and loads base defaults.
 * 
 * For an example daemon see {@link loggingDaemon} located in /daemons.
 * 
 * <code>
 * // very basic example of a daemon
 * require_once(dirname(dirname(__FILE__)).'/libraries/system.inc');
 * // PHP <5.3.0 we need to declare ticks to install signal handling
 * declare(ticks=1);
 * 
 * // setup some logging
 * systemLog::getInstance()->setSource('StartUp');
 * systemLog::message('--------------------------------------------------');
 * systemLog::message('Initialising Logging Daemon');
 * 
 * class myDaemon extends cliDaemon {
 * 
 *     function __construct() {
 *         parent::__construct('myDaemon', 'Example Daemon');
 *     }
 * 
 *     function execute() {
 *         $this->setStatusParam('Status', 'Running');
 *         $this->updateStatus();
 * 
 *         $loop = true;
 *         do {
 *             if ( $this->signalTrapped() ) {
 *                 $loop = false;
 *             }
 *             
 *             // do something cool in here that takes ages or processes
 *             // lots of data or whatever. Then have a little rest.
 *             
 *             sleep(2);
 *         } while ( $loop === true );
 *    }
 *    
 *    function terminate() {
 *        $this->setStatusParam('Status', 'Stopped');
 *        $this->updateStatus();
 *        return true;
 *    }
 * }
 * 
 * // get request, initialise cli and daemonise
 * $oRequest = cliRequest::getInstance();
 * cliProcessControls::initialise($oRequest, 'loggingDaemon');
 * cliProcessControls::daemonise();
 * 
 * // now start up the daemon
 * $oDaemon = new myDaemon();
 * $oDaemon->setPosixId(cliProcessControls::getPosixId());
 * $oDaemon->setPidFile(cliProcessControls::getPidFile());
 * $oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
 * $oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
 * $oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM); // exit on these signals
 * $oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
 * $oDaemon->execute();
 * </code>
 * 
 * Note: you need to set-up your own signal handling to capture signals from other 
 * processes. While this is installed by cliApplication, YOU have to check for trapped
 * signals within your main process loop. Failure to do so will mean that any kill -9 
 * won't be handled gracefully and you may suffer data loss.
 * 
 * Note 2: signal handling changed in PHP5.3+ see {@link cliProcessControls} for examples
 * of setting up signal handling.
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliDaemon
 * @abstract 
 */
abstract class cliDaemon extends cliApplication {
	
	/**
	 * Process ID from the OS
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_PosixId					= false;
	/**
	 * Posix userId to run this daemon as
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_PosixUserId				= false;
	/**
	 * Posix groupId to run this daemon as
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_PosixGroupId			= false;
	/**
	 * Location of pid file for daemon
	 *
	 * @var string
	 * @access protected
	 */
	protected $_PidFile					= false;
	/**
	 * Timestamp of last time status update was recorded
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_LastStatusUpdate		= 0;
	/**
	 * Associative array of parameters to log to status file
	 *
	 * @var array
	 * @access protected
	 */
	protected $_DaemonStatus			= array();
	
	
	
	/**
	 * Returns a new cliDaemon object
	 * 
	 * @return cliDaemon
	 */
	function __construct($inAppName = null, $inAppDescription = null) {
		parent::__construct($inAppName, $inAppDescription);
		
		/*
		 * Register the destructor method, class destructor (__destruct()) is not
		 * reliably called, or can be called out of context, this ensures we have
		 * a defined method of cleaning up as our daemon exits.
		 */
		register_shutdown_function(array($this, "destructor"));
		
		/*
		 * Set last update status to now
		 */
		$this->updateStatus();
	}

	/**
	 * Executes the application stack
	 *
	 * @param cliRequest $inRequest
	 * @return void
	 * @abstract 
	 */
	function execute(cliRequest $inRequest) {
		throw new cliApplicationException('Missing execute implementation in daemon class '.__CLASS__);
	}
	
	
	
	/**
	 * Returns the time of the last update status
	 *
	 * @return integer
	 */
	function getLastStatusUpdate() {
		return $this->_LastStatusUpdate;
	}
	
	/**
	 * Set last status update to now
	 *
	 * @return void
	 */
	function updateStatus() {
		$this->_LastStatusUpdate = time();
		$this->writeStatus();
	}
	
	/**
	 * Sets a param to monitor with a value to record, these are written to a status file
	 *
	 * @param string $inParamName
	 * @param mixed $inParamValue
	 */
	function setStatusParam($inParamName, $inParamValue) {
		$this->_DaemonStatus[$inParamName] = $inParamValue;
	}
	
	/**
	 * Remove a param from the daemonStatus array
	 *
	 * @param string $inParamName
	 */
	function unsetStatusParam($inParamName) {
		if ( array_key_exists($inParamName, $this->_DaemonStatus) ) {
			unset($this->_DaemonStatus[$inParamName]);
		}
	}
	
	/**
	 * Removes all current status params, resetting to an empty array
	 *
	 * @return void
	 */
	function resetStatusParams() {
		$this->_DaemonStatus = array();
	}
	
	/**
	 * Writes out the daemonStatus params to a file
	 *
	 * @param string $status
	 * @return boolean
	 */
	function writeStatus() {
		$fileData  = "Daemon: {$this->getApplicationName()}\n";
		$fileData .= 'LastUpdated: '.date('H:i:s d-m-Y', $this->_LastStatusUpdate)."\n";
		
		if ( is_array($this->_DaemonStatus) && count($this->_DaemonStatus) > 0 ) {
			foreach ( $this->_DaemonStatus as $key => $value ) {
				if ( is_array($value) ) {
					$value = implode('|', $value);
				}
				$fileData .= ucwords($key).': '.$value."\n";
			}
		} else {
			$fileData .= "Status: Running\n";
		}
		
		$statusFolder = system::getConfig()->getPathTemp().'/status/';
		if ( !file_exists($statusFolder) ) {
			if ( !mkdir($statusFolder, 0777, true) ) {
				$this->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_ERROR,
						'Failed to create status folder!'
					)
				);
				return false;
			}
		}
		$statusFile = basename(system::getScriptFilename(), '.php').'.log';
		
		$bytes = @file_put_contents($statusFolder.$statusFile, $fileData);
		if ( substr(sprintf('%o', fileperms($statusFolder.$statusFile)), -4) !== '0666' ) {
			@chmod($statusFolder.$statusFile, 0666);
		}
		if ( $bytes > 0 ) {
			return true;
		} else {
			return false;
		}
	}
	
	
	
	/**
	 * Set the process ID
	 *
	 * @param integer $inPosixId
	 * @return cliDaemon
	 */
	public function setPosixId($inPosixId) {
		if ( is_numeric($inPosixId) ) {
			$this->_PosixId = $inPosixId;
		}
		return $this;
	}
	
	/**
	 * Set the posix userId
	 *
	 * @param integer $inUserID
	 * @return cliDaemon
	 */
	public function setPosixUser($inUserID) {
		if ( is_numeric($inUserID) ) {
			$this->_PosixUserId = $inUserID;
		}
		return $this;
	}
	
	/**
	 * Set the posix groupId
	 *
	 * @param integer $inGroupID
	 * @return cliDaemon
	 */
	public function setPosixGroup($inGroupID) {
		if ( is_numeric($inGroupID) ) {
			$this->_PosixGroupId = $inGroupID;
		}
		return $this;
	}
	
	/**
	 * Set the PID file
	 *
	 * @param string $inPidFile
	 * @return cliDaemon
	 */
	public function setPidFile($inPidFile) {
		if ( strlen($inPidFile) > 0 ) {
			$this->_PidFile = $inPidFile;
		}
		return $this;
	}
	
	
	
	/**
	 * Global daemon destructor, calls the terminate() method if it has been defined in an extended class
	 *
	 * @access public
	 */
	public function destructor() {
		$oEvent = new cliApplicationEvent(cliApplicationEvent::EVENT_INFORMATIONAL, '', null, array('log.source' => 'Shutdown'));
		
		if ( defined('DAEMON_PROCESS') ) {
			if ( defined("CHILD_PROCESS") ) {
				cliProcessControls::deletePidFile();
			}
		} else {
			if ( defined("CHILD_PROCESS") ) {
				$oEvent->setEventMessage("Child PID=".posix_getpid()." Destructor called");
			} else {
				$oEvent->setEventMessage("Parent PID=".posix_getpid()." Destructor called");
			}
		}
		$this->notify($oEvent);
		
		$this->terminate();
		$this->setStatusParam('Status', 'Stopped');
		$this->updateStatus();
		
		$this->notify(
			new cliApplicationEvent(
				cliApplicationEvent::EVENT_APPLICATION_TERMINATED,
				$this->getApplicationName()." shutdown",
				null,
				array(
					cliApplicationEvent::OPTION_LOG_SOURCE => 'Shutdown'
				)
			)
		);
		exit;
	}
	
	/**
	 * Performs custom clean-up on daemon shutdown
	 *
	 * @return void
	 */
	public function terminate() {
		
	}
	
	/**
	 * @see cli::signalTrapped()
	 */
	public function signalTrapped($inSignal = false) {
		$res = parent::signalTrapped($inSignal);
		if ( $res > 0 ) {
			$this->setStatusParam('Status', 'Shutdown signal received');
			$this->updateStatus();
		}
		return $res;
	}
}