<?php
/**
 * cliApplicationEvent Class
 * 
 * Stored in event.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationEvent
 * @version $Rev: 650 $
 */


/**
 * cliApplicationEvent class
 *
 * Holds the event details that are passed to application listeners to act on.
 * Events have an event code and message at the most basic level, however the
 * source object and an array of options can be supplied to give further
 * information to the listener.
 * 
 * Event codes are defined as bit constants from OK 0 to WARNING 65536.
 * Other codes may be added in time.
 * 
 * Various OPTION_ constants are available for some frequently used parameters.
 * These can be passed into the event options and will be picked up by various
 * components including the default listeners. For example:
 * 
 * <code>
 * $oEvent = new cliApplicationEvent(
 *     $inEventCode, $inEventMessage, null, array(
 *         cliApplicationEvent::OPTION_TRIGGER_LEVEL => 30,
 *         cliApplicationEvent::OPTION_LOOP_PROCESS_ID => 'loop5400',
 *     )
 * );
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationEvent
 */
class cliApplicationEvent {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_EventCode
	 *
	 * @var integer
	 * @access private
	 */
	private $_EventCode;
	
	/**
	 * Status, all OK
	 *
	 * @var integer
	 */
	const EVENT_OK = 0;
	/**
	 * Status, error occured NOT exception
	 *
	 * @var integer
	 */
	const EVENT_ERROR = 1;
	/**
	 * Status, exception caught / thrown
	 *
	 * @var integer
	 */
	const EVENT_EXCEPTION = 2;
	/**
	 * Status, internal re-direct was successfull
	 *
	 * @var integer
	 */
	const EVENT_REDIRECT_SUCCESS = 4;
	/**
	 * Status, internal re-direct failed
	 *
	 * @var integer
	 */
	const EVENT_REDIRECT_FAILURE = 8;
	/**
	 * Status, a registered signal was trapped
	 *
	 * @var integer
	 */
	const EVENT_REGISTERED_SIGNAL_TRAPPED = 16;
	/**
	 * Status, a terminating (not trapped signal) was caught
	 *
	 * @var integer
	 */
	const EVENT_UNREGISTERED_SIGNAL_TRAPPED = 32;
	/**
	 * Status, application (un)expectedly terminated
	 *
	 * @var integer
	 */
	const EVENT_APPLICATION_TERMINATED = 64;
	
	/**
	 * Status, the start of the main execute() cycle
	 *
	 * @var itneger
	 */
	const EVENT_EXECUTE_START = 128;
	/**
	 * Status, the end of the last execute() cycle
	 *
	 * @var integer
	 */
	const EVENT_EXECUTE_END = 256;
	/**
	 * Status, the start of a process loop
	 *
	 * @var itneger
	 */
	const EVENT_PROCESS_START = 512;
	/**
	 * Status, the end of an open process loop
	 *
	 * @var integer
	 */
	const EVENT_PROCESS_END = 1024;
	/**
	 * Status, notification of a pre-determined trigger event
	 *
	 * @var integer
	 */
	const EVENT_TRIGGER = 2048;
	
	/**
	 * Status, an information notice
	 *
	 * @var integer
	 */
	const EVENT_INFORMATIONAL = 32768;
	/**
	 * Status, a warning notice
	 *
	 * @var integer
	 */
	const EVENT_WARNING = 65536;
	
	
	
	/**
	 * Stores $_EventMessage
	 *
	 * @var string
	 * @access private
	 */
	private $_EventMessage;
	
	/**
	 * Stores $_EventSource
	 *
	 * @var mixed
	 * @access private
	 */
	private $_EventSource;
	
	/**
	 * Stores $_Options
	 *
	 * @var array
	 * @access private
	 */
	private $_Options;
	
	const OPTION_APP_NAME = 'app.name';
	const OPTION_APP_COMMAND = 'app.command';
	const OPTION_LOG_SOURCE = 'log.source';
	/**
	 * Caught posix signal option parameter
	 *
	 * @var string
	 */
	const OPTION_POSIX_SIGNAL = 'posix.signal';
	/**
	 * The trigger level option parameter (usually a loop count e.g. 500 loops in an execute block)
	 *
	 * @var string
	 */
	const OPTION_TRIGGER_LEVEL = 'trigger.level';
	/**
	 * An identifier for the process start and end block (usually a loop ID)
	 *
	 * @var string
	 */
	const OPTION_LOOP_PROCESS_ID = 'process.id';
	
	
	
	/**
	 * Creates a new application event object
	 * 
	 * Event will have status $inEventCode, message $inEventMessage, the original source
	 * object (optional) and an array of additional data via $inOptions.
	 *
	 * @param integer $inEventCode
	 * @param string $inEventMessage
	 * @param mixed $inSource
	 * @param array $inOptions
	 */
	function __construct($inEventCode, $inEventMessage, $inSource = null, array $inOptions = array()) {
		$this->reset();
		$this->setEventCode($inEventCode);
		$this->setEventMessage($inEventMessage);
		if ( $inSource !== null && is_object($inSource) ) {
			$this->setEventSource($inSource);
		}
		$this->setOptions($inOptions);
	}
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_EventCode = null;
		$this->_EventMessage = null;
		$this->_EventSource = null;
		$this->_Options = array();
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
	 * @return cliApplicationEvent
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns $_EventCode
	 *
	 * @return integer
	 */
	function getEventCode() {
		return $this->_EventCode;
	}
	
	/**
	 * Returns the event code as a string
	 *
	 * @return string
	 */
	function getEventCodeAsString() {
		$strings = array(
			self::EVENT_OK => 'Command Processed Successfully',
			self::EVENT_ERROR => 'Error occured, but not exception',
			self::EVENT_EXCEPTION => 'Exception Was Raised',
			self::EVENT_REDIRECT_SUCCESS => 'Command Redirect Successful',
			self::EVENT_REDIRECT_FAILURE => 'Command Redirect Failed',
			self::EVENT_REGISTERED_SIGNAL_TRAPPED => 'Registered Signal Trapped',
			self::EVENT_UNREGISTERED_SIGNAL_TRAPPED => 'Unregistered Signal Trapped',
			self::EVENT_APPLICATION_TERMINATED => 'Application Terminated',
			self::EVENT_INFORMATIONAL => 'Info',
			self::EVENT_WARNING => 'Warning',
			self::EVENT_PROCESS_END => 'Process Loop Ended',
			self::EVENT_PROCESS_START => 'Process Loop Started',
			self::EVENT_TRIGGER => 'Trigger Level Reached',
		);
		
		if ( array_key_exists($this->getEventCode(), $strings) ) {
			return $strings[$this->getEventCode()];
		} else {
			return 'Unknown event code';
		}
	}
	
	/**
	 * Set $_EventCode to $inEventCode
	 *
	 * @param integer $inEventCode
	 * @return cliApplicationEvent
	 */
	function setEventCode($inEventCode) {
		if ( $inEventCode !== $this->_EventCode ) {
			$this->_EventCode = $inEventCode;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_EventMessage
	 *
	 * @return string
	 */
	function getEventMessage() {
		return $this->_EventMessage;
	}
	
	/**
	 * Set $_EventMessage to $inEventMessage
	 *
	 * @param string $inEventMessage
	 * @return cliApplicationEvent
	 */
	function setEventMessage($inEventMessage) {
		if ( $inEventMessage !== $this->_EventMessage ) {
			$this->_EventMessage = $inEventMessage;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_EventSource, which is an object
	 *
	 * @return mixed
	 */
	function getEventSource() {
		return $this->_EventSource;
	}
	
	/**
	 * Set $_EventSource to $inEventSource
	 *
	 * @param mixed $inEventSource
	 * @return cliApplicationEvent
	 */
	function setEventSource($inEventSource) {
		if ( $inEventSource !== $this->_EventSource && is_object($inEventSource) ) {
			$this->_EventSource = $inEventSource;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Options
	 *
	 * @return array
	 */
	function getOptions() {
		return $this->_Options;
	}
	
	/**
	 * Returns the option named $inParamName
	 *
	 * @param string $inParamName
	 * @return mixed
	 */
	function getOption($inParamName) {
		if ( array_key_exists($inParamName, $this->_Options) ) {
			return $this->_Options[$inParamName];
		}
		return false;
	}
	
	/**
	 * Set $_Options to $inOptions
	 *
	 * @param array $inOptions
	 * @return cliApplicationEvent
	 */
	function setOptions(array $inOptions) {
		if ( $inOptions !== $this->_Options ) {
			$this->_Options = $inOptions;
			$this->setModified();
		}
		return $this;
	}
}