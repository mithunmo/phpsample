<?php
/**
 * cliApplication Class
 * 
 * Stored in application.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliApplication
 * @version $Rev: 711 $
 */


/**
 * cliApplication class
 *
 * Provides the necessary infra-structure to run a CLI application.
 * This includes command chaining, listeners for handling application
 * events during runtime, signal handling and more.
 * 
 * cliApplication can be extended for different application types as
 * is the case with cliDaemon, however it does not need to be as all
 * the commands are specified at runtime by creating a command chain
 * for the current application. In this manner you can swap and change
 * individual commands in any application.
 * 
 * Some commands are shared e.g. help, new, logging etc. this means
 * all you have to do is build your specific commands for your app.
 * The application associates itself with the cliRequest so you can
 * always access the base application context during execution.
 * 
 * cliApplication allows listeners to be attached and notified during
 * execution. A specific cliApplicationEvent can be created and 
 * dispatched to the listeners.
 * 
 * <code>
 * // a really basic example of an app in /temp called test.php
 * require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'system.inc');
 * 
 * $oApp = new cliApplication('testApp', 'This is a test CLI application to see if the ideas will work');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 * 	->addCommand(new cliCommandLog($oRequest))
 * 	->addCommand(new cliCommandLogToConsole($oRequest))
 * 	->addCommand(new cliCommandHelp($oRequest))
 * );
 * 
 * // to add signal interception - your command will have to check signals
 * // by using: if ( $this->getRequest()->getApplication()->signalTrapped() )
 * // $oApp->trapSignal(SIGINT, SIGHUP, SIGTERM);
 * $oApp->execute($oRequest);
 * </code>
 * 
 * Note: the command chain ORDER is very important with respect to
 * parameters (arguments). Switches are always processed first, followed
 * by commands. The first matching command is executed by cliApplication
 * after which it is up to the commands to re-route to additional logic.
 * If left alone, cliApplication terminates after the first argument is
 * matched and fired.
 * 
 * Note 2: execute() is wrapped in a try {} catch {} set but will only
 * capture cliApplicationCommandExceptions thrown from the commands. This
 * information will be pretty printed (to a point) on the command line.
 * The exception trace can be obtained by adding the switch -V to enable
 * very verbose mode, but only if the help command is in the current
 * command chain.
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliApplication
 */
class cliApplication {
	
	/**
	 * The default folder name that additional commands may be located in
	 *
	 * @var string
	 */
	const DEFAULT_COMMAND_FOLDER = 'commands';
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	/**
	 * Stores $_ApplicationName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ApplicationName;
	
	/**
	 * Stores $_ApplicationDescription
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ApplicationDescription;
	
	/**
	 * Stores $_CommandChain
	 *
	 * @var cliCommandChain
	 * @access private
	 */
	private $_CommandChain;
	
	/**
	 * Stores $_ApplicationListeners
	 *
	 * @var cliApplicationListeners
	 * @access private
	 */
	private $_ApplicationListeners;
	
	/**
	 * Array of signals to trap
	 *
	 * @var array
	 * @access private
	 */
	private $_TrapSignals;
	
	/**
	 * Array of caught signals
	 *
	 * @var array
	 * @access private
	 */
	private $_CaughtSignals;
	
	/**
	 * Stores $_Response
	 *
	 * @var cliResponse
	 * @access private
	 */
	private $_Response;
	
	
	
	/**
	 * Creates a new cliApplication instance
	 *
	 * @param string $inAppName
	 * @param string $inAppDescription
	 */
	function __construct($inAppName = null, $inAppDescription = null, $inCliResponse = null) {
		$this->reset();
		if ( $inAppName !== null ) {
			$this->setApplicationName($inAppName);
		}
		if ( $inAppDescription !== null ) {
			$this->setApplicationDescription($inAppDescription);
		}
		if ( $inCliResponse !== null && $inCliResponse instanceof cliResponse ) {
			$this->setResponse($inCliResponse);
		}
		
		/*
		 * Register our signal handlers
		 */
		pcntl_signal(SIGTERM, array($this, 'signalHandler')); # kill signals
		pcntl_signal(SIGINT,  array($this, 'signalHandler')); # Ctrl+C signals
		pcntl_signal(SIGHUP,  array($this, 'signalHandler')); # -HUP restart signals
	}
	
	/**
	 * Executes the application stack
	 *
	 * @param cliRequest $inRequest
	 * @return void
	 */
	function execute(cliRequest $inRequest) {
		try {
			if ( $inRequest->getCount() == 0 && $inRequest->getSwitchCount() == 0 ) {
				if ( $this->getCommandChain()->hasCommand('help') ) {
					$this->getCommandChain()->getCommand('help')->execute();
				}
			}
			
			/*
			 * For auto-completion in IDE 
			 */
			if ( false ) $oCommand = new cliCommand();
			
			/*
			 * Run switches first to set application directives
			 */
			foreach ( $inRequest->getSwitches() as $switch ) {
				foreach ( $this->getCommandChain() as $oCommand ) {
					if ( $oCommand->getCommandAliases()->hasAlias($switch) ) {
						$oCommand->execute();
					}
				}
			}
			
			/*
			 * Now execute the arguments in order of the request
			 */
			foreach ( $inRequest as $arg => $value ) {
				foreach ( $this->getCommandChain() as $oCommand ) {
					if ( $oCommand->getCommandPattern() == $arg ) {
						$oCommand->execute();
						if ( $oCommand->haltAppAfterExecute() ) {
							break 2;
						}
					}
				}
			}
		} catch ( cliApplicationCommandException $e ) {
			$this->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_EXCEPTION,
					$e->getMessage(),
					$e,
					array(
						cliApplicationEvent::OPTION_APP_NAME => $this->getApplicationName(),
						cliApplicationEvent::OPTION_APP_COMMAND => $e->getCommand()->getCommandPattern()
					)
				)
			);
			
			$this->getResponse()->addResponse($this->_formatException($e, $inRequest));
		}
		
		$this->getResponse()->sendResponse();
	}
	
	/**
	 * Formats an exception to be displayed on the CLI terminal
	 *
	 * @param cliApplicationCommandException $inException
	 * @param cliRequest $inRequest
	 * @return string
	 * @access protected
	 */
	protected function _formatException(cliApplicationCommandException $inException, cliRequest $inRequest) {
		$return  = '';
		$return .= "\nError in application '".$this->getApplicationName().'\' from command: '.$inException->getCommand()->getCommandPattern()."\n";
		$return .= str_repeat('-', cliConstants::CONSOLE_WIDTH)."\n";
		$return .= "\n    -> ".$inException->getMessage()."\n\n";
		if ( $inException->getCommand()->getCommandRequiresValue() ) {
			if ( $inException->getCommand()->getCommandChain()->getCount() == 0 ) {
				$return .= "    A value is required for this command\n";
			} else {
				$return .= "    Command (".$inException->getCommand()->getCommandPattern().') requires a value of: [';
				foreach ( $inException->getCommand()->getCommandChain() as $oCommand ) {
					if ( !$oCommand->getCommandIsSwitch() ) {
						$return .= $oCommand->getCommandPattern().", ";
					}
				}
				$return .= "]\n";
			}
		}
		/*
		 * only show stack trace in debug mode
		 */
		if ( $this->getCommandChain()->hasCommand('help') && $inRequest->getSwitch('V') ) { 
			$return .= "\n\nStack trace:\n";
			$return .= str_repeat('-', cliConstants::CONSOLE_WIDTH)."\n";
			$return .= $inException->getTraceAsString()."\n";
		}
		return $return;
	}

	/**
	 * Fires a notification to any attached listeners on the application
	 *
	 * @param cliApplicationEvent $inEvent
	 * @return void
	 */
	function notify(cliApplicationEvent $inEvent) {
		$this->getListeners()->notify($inEvent);
	}
	
	/**
	 * Registers a path with the autoloader that additional commands can be located in
	 *
	 * Note: this method has difficulty resolving applications running under Cygwin.
	 * In these cases, you should set this path explicitly from your scriptname.php
	 * file, rather than trying to rely on the system derived script path.
	 * 
	 * @param string $inPath Full path, if not given, uses current app name
	 * @return cliApplication
	 */
	function registerCommandPath($inPath = null) {
		if ( $inPath === null ) {
			$inPath = system::getScriptPath().system::getDirSeparator().self::DEFAULT_COMMAND_FOLDER;
		}
		
		$inPath = utilityStringFunction::cleanDirSlashes($inPath);
		
		if ( file_exists($inPath) && is_readable($inPath) ) {
			system::getAutoload()->addClassPath($inPath);
		}
		return $this;
	}
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_ApplicationName = null;
		$this->_ApplicationDescription = null;
		$this->_CommandChain = null;
		$this->_ApplicationListeners = null;
		$this->_Response = null;
		$this->_CaughtSignals = array();
		$this->_TrapSignals = array();
		$this->_Modified = false;
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
	 * Set $_Modified to $inStatus
	 *
	 * @param boolean $inStatus
	 * @return cliApplication
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
		return $this;
	}

	/**
	 * Returns $_ApplicationName
	 *
	 * @return string
	 * @access public
	 */
	function getApplicationName() {
		return $this->_ApplicationName;
	}
	
	/**
	 * Set $_ApplicationName to $inApplicationName
	 *
	 * @param string $inApplicationName
	 * @return cliApplication
	 * @access public
	 */
	function setApplicationName($inApplicationName) {
		if ( $this->_ApplicationName !== $inApplicationName ) {
			$this->_ApplicationName = $inApplicationName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_ApplicationDescription
	 *
	 * @return string
	 * @access public
	 */
	function getApplicationDescription() {
		return $this->_ApplicationDescription;
	}
	
	/**
	 * Set $_ApplicationDescription to $inApplicationDescription
	 *
	 * @param string $inApplicationDescription
	 * @return cliApplication
	 * @access public
	 */
	function setApplicationDescription($inApplicationDescription) {
		if ( $this->_ApplicationDescription !== $inApplicationDescription ) {
			$this->_ApplicationDescription = $inApplicationDescription;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Response
	 *
	 * @return cliResponse
	 */
	function getResponse() {
		if ( !$this->_Response instanceof cliResponse ) {
			$this->_Response = new cliResponse();
		}
		return $this->_Response;
	}
	
	/**
	 * Set $_Response to $inResponse
	 *
	 * @param cliResponse $inResponse
	 * @return cliApplication
	 */
	function setResponse($inResponse) {
		if ( $inResponse !== $this->_Response ) {
			$this->_Response = $inResponse;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the application command chain
	 *
	 * @return cliCommandChain
	 * @access public
	 */
	function getCommandChain() {
		if ( !$this->_CommandChain instanceof cliCommandChain ) {
			$this->_CommandChain = new cliCommandChain();
		}
		return $this->_CommandChain;
	}
	
	/**
	 * Returns the application listeners object
	 *
	 * @return cliApplicationListeners
	 */
	function getListeners() {
		if ( !$this->_ApplicationListeners instanceof cliApplicationListeners ) {
			$this->_ApplicationListeners = new cliApplicationListeners();
		}
		return $this->_ApplicationListeners;
	}
	
	
	
	/*
	 * Signal Handling
	 */
	
	/**
	 * Returns true if $inSignal has been registered with the signal handler
	 *
	 * @param integer $inSignal
	 * @return boolean
	 */
	public function isRegisteredSignal($inSignal) {
		return in_array($inSignal, $this->_TrapSignals);
	}
	
	/**
	 * Registers a set of signals to be intercepted and handled by the application.
	 * 
	 * Multiple signals can be passed in as arguments. The PHP constants SIG* should
	 * be used when specifying the signals.
	 * 
	 * <code>
	 * // example of trapping various signals
	 * $oApp = new cliApplication('app', 'description');
	 * $oApp->trapSignal(SIGCHLD, SIGINT, SIGTERM ...);
	 * </code>
	 *
	 * @param integer $arg1 [, integer $arg2 ... ]
	 * @return boolean
	 * @access public
	 */
	public function trapSignal() {
		$args = func_get_args();
		if ( count($args) == 0 ) {
			return false;
		}
		
		foreach ( $args as $signal ) {
			if ( !in_array($signal, $this->_TrapSignals) ) {
				$this->_TrapSignals[] = $signal;
			}
		}
		return true;
	}
	
	/**
	 * Checks if any or a specific signal has been caught by the signalHandler.
	 * 
	 * Returns the number of signals caught. The signal count depends on which
	 * signals are being handled by the application. If none are registered,
	 * this will always return 0.
	 *
	 * @param integer $inSignal
	 * @return integer
	 * @access public
	 */
	public function signalTrapped($inSignal = false) {
		if ( count($this->_CaughtSignals) > 0 ) {
			if ( is_numeric($inSignal) ) {
				$count = 0;
				foreach ( $this->_CaughtSignals as $signo ) {
					if ( $signo == $inSignal ) {
						$count++;
					}
					return $count;
				}
			} else {
				return count($this->_CaughtSignals);
			}
		}
		return 0;
	}
	
	/**
	 * Signal handler, records $inSignal in the internal array of caught signals.
	 * 
	 * The signals to be handled by the application are registered using
	 * {@link cliApplication::trapSignal()}. When a signal is intercepted by the
	 * signal handler, it is checked to see if it is an application controlled
	 * signal. It is, the event is logged and it is up to the application to
	 * detect and handle the signal via {@link cliApplication::signalTrapped()}.
	 * 
	 * Any unhandled signals (including kill -9) will cause immediate termination
	 * of the process - possibly resulting in un-predictable behaviour or data
	 * corruption.
	 * 
	 * Note: kill -9 should not be handled. This signal causes immediate application
	 * termination, therefore you should avoid using kill -9 daemon/application as
	 * it will terminate the process without any chance of the clean-up process
	 * firing.
	 *
	 * @param integer $signal
	 * @access public
	 */
	public function signalHandler($inSignal) {
		$aSignals = array(
			 1 => "SIGHUP",
			 2 => "SIGINT",
			 3 => "SIGQUIT",
			15 => "SIGTERM",
			17 => "SIGCHLD"
		);
		
		if ( in_array($inSignal, $this->_TrapSignals) ) {
			/*
			 * Pass these signals to daemon to handle
			 */
			$this->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_REGISTERED_SIGNAL_TRAPPED,
					$aSignals[$inSignal]." Signal Trapped",
					null,
					array(
						cliApplicationEvent::OPTION_POSIX_SIGNAL => $inSignal
					)
				)
			);
			
			$this->_CaughtSignals[] = $inSignal;
		} else {
			/*
			 * Some other signal, exit
			 */
			$this->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_UNREGISTERED_SIGNAL_TRAPPED,
					$aSignals[$inSignal]." Signal Received - Exiting",
					null,
					array(
						cliApplicationEvent::OPTION_POSIX_SIGNAL => $inSignal
					)
				)
			);
			exit;
		}
	}
}