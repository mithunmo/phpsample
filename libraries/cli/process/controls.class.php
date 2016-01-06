<?php
/**
 * cliProcessControls Class
 * 
 * Stored in controls.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliProcessControls
 * @version $Rev: 741 $
 */


/**
 * cliProcessControls Class
 * 
 * Provides POSIX control functions for daemon processes. This set of methods is for
 * forking processes. It will create PID files (a text file containing the process ID)
 * on successful launch of the process.
 * 
 * WARNING: Under no circumstance should this class be extended or the methods used in
 * an object oriented manner. {@link cliProcessControls::daemonise()} must be called
 * exactly as typed otherwise the fork process will not complete correctly. At the time
 * of writing, making these class functions does not re-map the memory properly or
 * at least causes problems where the child process and the parent process are not
 * properly separated.
 * 
 * WARNING: to be able to daemonise in this system you must have root access to the server
 * as the daemonising code attempts to reset the session leader to another user - the 
 * forked processes do NOT run as root but as the framework configured user, usually
 * a system account or user account with no shell access and that has no group etc.
 * 
 * These methods should be used in the following manner:
 * 
 * <code>
 * // example for PHP <5.3
 * require_once(dirname(dirname(__FILE__)).'/libraries/system.inc');
 * declare(ticks=1);
 * 
 * class myDaemon extends cliDaemon {
 *     // my daemon code
 *     function execute() {
 *         do {
 *             // myDaemon does something interesting / cool here
 *         } while ( $loop === true ); 
 *     }
 * }
 * 
 * // get cli params, required for app
 * $oRequest = cliRequest::getInstance();
 *
 * // Initialise process control
 * cliProcessControls::initialise($oRequest, 'myDaemon');
 * 
 * //Attempt to daemonise process
 * cliProcessControls::daemonise();
 * 
 * $oDaemon = new myDaemon();
 * $oDaemon->setPosixId(cliProcessControls::getPosixId());
 * $oDaemon->setPidFile(cliProcessControls::getPidFile());
 * $oDaemon->setPosixUser(system::getConfig()->getSystemUser());
 * $oDaemon->setPosixGroup(system::getConfig()->getSystemGroup());
 * $oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM);
 * $oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
 * $oDaemon->execute();
 * </code>
 * 
 * While PHP in web processes has pretty good resource usage (memory leaks are rare), long
 * running processes can have a lot of issues. It is vital that before any production use
 * a daemon is tested in a staging environment under load for at least a few days to make
 * sure there are no hidden leaks.
 * 
 * Things that can help reduce memory leaks:
 * <ul>
 * <li>launch processing as separate applications from the daemon and use the daemon to
 * control the app launch instead of cron tasks,</li>
 * <li>always dispose of objects by setting to 'null' first (as in PHP NULL constant NOT 
 * the string 'null') and then unset($object),</li>
 * <li>avoid looping SimpleXML structures in foreach (has memory leaks)</li>
 * <li>avoid lazy loading lots and lots of classes and consider a single static class
 * that is reloaded with new data,</li>
 * <li>periodically pause to do clean-up</li>
 * </ul>
 * 
 * With careful coding there is no reason why PHP cannot be used in this manner. From my
 * own experience, I have had PHP daemon processes running for months without any issues
 * at all.
 * 
 * Additional Notes:
 * PHP 5.3+ deprecates the use of declare(ticks=1); This means that the above example
 * will not function correctly as the signal handler can only be installed with ticks.
 * The PHP docs have not been updated yet to provide instructions on a solution.
 * Fortunately a post to the bug list:
 * {@link http://bugs.php.net/bug.php?id=47198 pcntl_signal needs declare(ticks) which is deprecated since 5.3}
 * offers a solution. Instead of looping: while ( $loop === true ); it needs to be
 * modified to add a call to: {@link http://www.php.net/manual/en/function.pcntl-signal-dispatch.php pcntl_signal_dispatch}.
 * This will then fire all attached signal handlers.
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliProcessControls
 * @static 
 */
final class cliProcessControls {
	
	/**
	 * Current cliRequest object
	 *
	 * @var cliRequest
	 * @access protected
	 * @static
	 */
	protected static $_CliRequest			= null;
	
	/**
	 * Process ID from the OS
	 *
	 * @var integer
	 * @access protected
	 * @static 
	 */
	protected static $_PosixId				= false;
	
	/**
	 * Location of pid file for daemon
	 *
	 * @var string
	 * @access protected
	 * @static 
	 */
	protected static $_PidFile				= false;
	
	/**
	 * Array of children processes
	 *
	 * @var array
	 * @access protected
	 * @static 
	 */
	protected static $_Children				= array();
	
	
	
	/**
	 * Throw exception, preventing class being instantiated
	 *
	 * @throws cliException
	 */
	private function __construct() {
		throw new cliException('cliProcessTools can not be instantiated');
	}
	
	

	/**
	 * Returns the instance of cliRequest
	 *
	 * @return cliRequest
	 * @static
	 */
	public static function getCliRequest() {
		return self::$_CliRequest;
	}
	
	/**
	 * Sets the instance of cliRequest
	 *
	 * @param cliRequest $inRequest
	 * @return void
	 */
	public static function setCliRequest(cliRequest $inRequest) {
		self::$_CliRequest = $inRequest;
	}
	
	/**
	 * Return the current pidFile
	 *
	 * @return string
	 * @static 
	 */
	public static function getPidFile() {
		if ( self::$_PidFile === false || strlen(self::$_PidFile) < 8 ) {
			self::setPidFile(
				system::getConfig()->getPathTemp()->getParamValue().
				system::getDirSeparator().
				basename(system::getScriptFilename(), '.php').
				".pid"
			);
		}
		return self::$_PidFile;
	}
	
	/**
	 * Set pidFile to static vars
	 *
	 * @param string $inPidFile
	 * @static 
	 */
	public static function setPidFile($inPidFile) {
		systemLog::info('Setting pidFile to: '.$inPidFile);
		if ( $inPidFile !== self::$_PidFile ) {
			systemLog::info('pidFile set');
			self::$_PidFile = $inPidFile;
		}
	}

	/**
	 * Return the current posix process ID
	 *
	 * @return integer
	 * @static 
	 */
	public static function getPosixId() {
		return self::$_PosixId;
	}
	
	/**
	 * Set posixId to static vars
	 *
	 * @param integer $inPosixId
	 * @static 
	 */
	public static function setPosixId($inPosixId) {
		self::$_PosixId = $inPosixId;
	}
	
	/**
	 * Returns array of child process IDs
	 *
	 * @return array
	 * @static 
	 */
	public static function getChildProcesses() {
		return self::$_Children;
	}
	
	
	
	/**
	 * Installs signal handlers for $inClassname, should be called before daemonising
	 *
	 * @param cliRequest $inCliRequest
	 * @param string $inClassname
	 */
	public static function initialise($inCliRequest, $inClassname) {
		self::setCliRequest($inCliRequest);
		if ( $inClassname && strlen($inClassname) > 1 && class_exists($inClassname) ) {
			pcntl_signal(SIGTERM, array($inClassname, 'signalHandler')); # kill signals
			pcntl_signal(SIGINT,  array($inClassname, 'signalHandler')); # Ctrl+C signals
			pcntl_signal(SIGHUP,  array($inClassname, 'signalHandler')); # -HUP restart signals
			self::getPidFile();
		} else {
			trigger_error('Missing daemon classname, cannot initialise daemon environment', E_USER_ERROR);
		}
	}
	
	/**
	 * Turn our script into a daemon
	 *
	 * @access public
	 * @static 
	 */
	public static function daemonise() {
		systemLog::getInstance()->setSource('Daemonise');
		if (
			self::getCliRequest()->getParam('nodaemon') ||
			self::getCliRequest()->getParam('nodeamon') ||
			self::getCliRequest()->getParam('nodemon')
		) {
			systemLog::notice("DAEMONising cancelled");
			return false;
		}
		
		if ( posix_geteuid() > 0 ) {
			trigger_error('Unable to daemonize UID='.posix_geteuid().', please run as root', E_USER_ERROR);
		}
		
		/*
		 * Continue and fork only if no pidfile exists
		 */
		$pid = self::hasPidFile();
		if ( $pid ) {
			$cmd = '/bin/ps -p '.$pid;
			$res = `$cmd`;
			if ( preg_match('/'.system::getScriptFilename().'/', $res) ) {
				trigger_error("Unable to fork - Daemon already running as pid $pid", E_USER_ERROR);
			}
			unlink(self::$_PidFile);
			exit;
		}
		
		$child = self::fork();
		if ( $child > 0 ) {
			systemLog::notice("Daemon pid=$child launched");
			self::createPidFile(false, $child);
			exit; // kill parent
		} elseif ( $child == 0 ) {
			if ( !defined('DAEMON_PROCESS') ) {
				define('DAEMON_PROCESS', true);
			}
			if ( !posix_setgid(system::getConfig()->getSystemGroupGid()) ) {
				trigger_error("Unable to setgid to ".system::getConfig()->getSystemGroupGid()."!", E_USER_WARNING);
				exit;
			}	
			if ( !posix_setuid(system::getConfig()->getSystemUserId()) ) {
				trigger_error("Unable to setuid to ".system::getConfig()->getSystemUserId()."!", E_USER_WARNING);
				exit;
			}
		} else {
			trigger_error("Unable to daemonize", E_USER_ERROR);
			exit;
		}
		
		posix_setsid(); // become session leader
		umask(0); // clear umask
		
		$posixId = posix_getpid();
		self::setPosixId($posixId);
		
		sleep(3); // let our daemon settle down a bit first
		return $posixId;
	}
	
	/**
	 * Fork the current process and create a child process
	 *
	 * @return integer
	 * @access public
	 * @static 
	 */
	public static function fork() {
		if ( ($child = pcntl_fork()) == -1 ) {
			trigger_error("Unable to fork - exiting", E_USER_WARNING);
		} elseif ( $child == 0 ) {
			systemLog::notice("Child process running - pid=".posix_getpid());
			if ( !defined("CHILD_PROCESS") ) {
				define("CHILD_PROCESS", true);
			}
		} else {
			array_push(self::$_Children, $child);
		}
		return $child;
	}
	
	/**
	 * Creates a process ID file for the daemon
	 *
	 * @param string $inPidFile
	 * @param integer $inProcessID
	 * @return boolean
	 * @access public
	 * @static 
	 */
	public static function createPidFile($inPidFile = false, $inProcessID = false) {
		if ( $inPidFile === false ) {
			$inPidFile = self::getPidFile();
		}
		if ( $inProcessID !== false ) {
			self::$_PosixId = $inProcessID;
		}
		
		if ( strlen($inPidFile) < 8 ) {
			trigger_error('Missing pidFile or too short ('.$inPidFile.')', E_USER_ERROR);
			return false;
		}
		
		if ( self::$_PosixId === false ) {
			self::$_PosixId = posix_getpid();
		}
		if ( defined('CHILD_PROCESS') ) {
			trigger_error('Cannot create pidfile for a child process', E_USER_NOTICE);
			return false;
		}
		if ( is_numeric(self::hasPidFile()) ) {
			trigger_error('Unable to create pid - pidfile '.$inPidFile.' exists', E_USER_ERROR);
			return false;
		}
		
		touch($inPidFile);
		chgrp($inPidFile, system::getConfig()->getSystemGroup()->getParamValue());
		chmod($inPidFile, 0664);
		$bytes = @file_put_contents($inPidFile, self::$_PosixId);
		
		if ( $bytes > 0 ) {
			systemLog::info('pidfile ('.$inPidFile.') created');
		} else {
			trigger_error('Unable to create pidfile '.$inPidFile.', file write error', E_USER_WARNING);
			return false;
		}
		return true;
	}
	
	/**
	 * Checks to see if a PID file has been creasted already
	 *
	 * @return boolean
	 * @access public
	 * @static 
	 */
	public static function hasPidFile() {
		if ( @file_exists(self::$_PidFile) ) {
			$res = @file_get_contents(self::$_PidFile);
			return $res;
		}
		return false;
	}
	
	/**
	 * Deletes a PID file if it exists
	 *
	 * @return boolean
	 * @access public
	 * @static 
	 */
	public static function deletePidFile() {
		if ( self::hasPidFile() && defined('DAEMON_PROCESS')) {
			systemLog::info('Removing pidfile ('.self::$_PidFile.')');
			@unlink(self::$_PidFile);
			return true;
		}
		return false;
	}
}