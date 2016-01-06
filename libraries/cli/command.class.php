<?php
/**
 * cliCommand Class
 * 
 * Stored in command.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommand
 * @version $Rev: 650 $
 */


/**
 * cliCommand class
 * 
 * Represents a command to be fired by the CLI application. This class
 * should be extended to implement the execute() method. All commands
 * support command chains - a set of commands that this command can
 * execute (e.g. {@link cliCommandNew}).
 * 
 * Each commend must receive an instance of the cliRequest and should
 * have a command pattern. The command pattern is the keyword required
 * from the command line to execute it. This keyword may be a value of
 * another command (see {@link cliCommandNew}).
 * 
 * Each command can have a number of aliases - these are usually a
 * single character that can be used to execute the command e.g. the log
 * command has v and V as aliases to enable verbose or very verbose
 * logging.
 * 
 * Additional properties are help text and if the command requires a
 * value to be executed. This information will be used by the
 * {@link cliCommandHelp cliCommandHelp} command which formats help
 * information about the current application.
 * 
 * Lastly: commands can redirect to other commands. The redirect can be
 * to a command within the current command chain, or if not in that set,
 * the application command chain will be searched.
 * 
 * <code>
 * // short example of a command
 * class myCommand extends cliCommand {
 * 
 *     function __construct($inRequest) {
 *         parent::__construct($inRequest, 'mycomm');
 * 
 *         $this->setCommandHelp('This is my command example');
 *         // un-comment the next line to force a value for this command
 *         //$this->setCommandRequiresValue(true);
 *     }
 * 
 *     function execute() {
 *         echo "Hello world\n";
 *     }
 * }
 * </code>
 * 
 * By componentising commands and keeping them to a single purpose it is
 * possible to re-use huge chunks of code e.g. the 'new', help and log
 * commands can all be re-used easily.
 *
 * Lastly: to fire events from a command, reference the request and fetch
 * the application instance and then call {@link cliApplication::notify() notify()}
 * and pass in a {@link cliApplicationEvent}.
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliCommand
 */
abstract class cliCommand {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_CommandPattern
	 *
	 * @var string
	 * @access protected
	 */
	protected $_CommandPattern;
	
	/**
	 * Stores $_CommandPatternAlias
	 *
	 * @var cliCommandAliases
	 * @access protected
	 */
	protected $_CommandAliases;
	
	/**
	 * Stores $_CommandChain
	 *
	 * @var cliCommandChain
	 * @access protected
	 */
	protected $_CommandChain;
	
	/**
	 * Stores $_CommandHelp
	 *
	 * @var string
	 * @access protected
	 */
	protected $_CommandHelp;
	
	/**
	 * Stores $_CommandRequiresValue
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_CommandRequiresValue;
	
	/**
	 * Stores $_CommandIsSwitch
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_CommandIsSwitch;
	
	/**
	 * Stores $_CommandIsOptional
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_CommandIsOptional;
	
	/**
	 * Stores $_HaltAppAfterExecute, default true
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_HaltAppAfterExecute;
	
	/**
	 * Stores $_Request
	 *
	 * @var cliRequest
	 * @access protected
	 */
	protected $_Request;
	
	
	
	/**
	 * Creates a new cliCommand
	 * 
	 * cliCommand always requires the current cliRequest, however all other constructor
	 * parameters are optional. For $inCommandChain, a cliCommandChain object must always
	 * be set, however $inCommandAliases can be either a single character string, array of
	 * characters or a cliCommandAliases object.
	 *
	 * @param cliRequest $inRequest
	 * @param string $inCommandPattern
	 * @param cliCommandChain $inCommandChain
	 * @param cliCommandAliases $inCommandAliases
	 */
	function __construct(cliRequest $inRequest, $inCommandPattern = null, $inCommandChain = null, $inCommandAliases = null) {
		$this->reset();
		$this->setRequest($inRequest);
		if ( $inCommandPattern !== null ) {
			$this->setCommandPattern($inCommandPattern);
		}
		if ( $inCommandChain !== null && $inCommandChain instanceof cliCommandChain ) {
			$this->setCommandChain($inCommandChain);
		}
		if ( $inCommandAliases !== null ) {
			if ( is_string($inCommandAliases) || is_array($inCommandAliases) ) {
				$inCommandAliases = new cliCommandAliases((array) $inCommandAliases);
			}
			if ( $inCommandAliases instanceof cliCommandAliases ) {
				$this->setCommandAliases($inCommandAliases);
			}
		}
	}
	
	
	
	/**
	 * Executes the command
	 *
	 * @return boolean|void
	 * @abstract
	 */
	abstract function execute();
	
	/**
	 * Redirect processing to a new command $inCommand
	 *
	 * @param string $inCommand
	 * @throws cliException
	 */
	function redirect($inCommand) {
		$oCommand = $this->_findCommand($inCommand);
		if ( $oCommand instanceof cliCommand ) {
			$oCommand->execute();
		} else {
			if ( $this->getRequest()->getApplication() == null ) {
				throw new cliApplicationCommandException($this, "Fatal error: request does not contain application");
			}

			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_REDIRECT_FAILURE,
					"$inCommand is invalid in this commands chain",
					null,
					array(
						cliApplicationEvent::OPTION_APP_NAME => $this->getRequest()->getApplication()->getApplicationName(),
						cliApplicationEvent::OPTION_APP_COMMAND => $this->getCommandPattern(),
					)
				)
			);
			throw new cliApplicationCommandException($this, "$inCommand is invalid in this commands chain");
		}
	}
	
	/**
	 * Attempts to locate $inCommand in the current chain, or in the application
	 *
	 * @param string $inCommand
	 * @return cliCommand
	 * @access protected
	 */
	protected function _findCommand($inCommand) {
		$oCommand = $this->getCommandChain()->getCommand($inCommand);
		if ( !$oCommand instanceof cliCommand ) {
			if ( $this->getRequest()->getApplication() != null ) {
				$oCommand = $this->getRequest()->getApplication()->getCommandChain()->getCommand($inCommand);
			}
		}
		return $oCommand;
	}
	
	/**
	 * Reset object
	 *
	 * @return void
	 */
	function reset() {
		$this->_CommandPattern = null;
		$this->_CommandAliases = null;
		$this->_CommandChain = null;
		$this->_CommandHelp = 'Command '.get_class($this).' has no help available';
		$this->_CommandRequiresValue = false;
		$this->_CommandIsSwitch = false;
		$this->_CommandIsOptional = false;
		$this->_HaltAppAfterExecute = true;
		$this->_Request = null;
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
	 * @return cliCommand
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns $_CommandPattern
	 *
	 * @return string
	 */
	function getCommandPattern() {
		return $this->_CommandPattern;
	}
	
	/**
	 * Set $_CommandPattern to $inCommandPattern
	 *
	 * @param string $inCommandPattern
	 * @return cliCommand
	 */
	function setCommandPattern($inCommandPattern) {
		if ( $inCommandPattern !== $this->_CommandPattern ) {
			$this->_CommandPattern = $inCommandPattern;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_CommandAliases
	 *
	 * @return cliCommandAliases
	 */
	function getCommandAliases() {
		if ( !$this->_CommandAliases instanceof cliCommandAliases ) {
			$this->_CommandAliases = new cliCommandAliases();
		}
		return $this->_CommandAliases;
	}
	
	/**
	 * Set $_CommandAliases to $inCommandAliases
	 *
	 * @param string $inCommandAliases
	 * @return cliCommand
	 */
	function setCommandAliases(cliCommandAliases $inCommandAliases) {
		if ( $inCommandAliases !== $this->_CommandAliases ) {
			$this->_CommandAliases = $inCommandAliases;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_CommandChain
	 *
	 * @return cliCommandChain
	 */
	function getCommandChain() {
		if ( !$this->_CommandChain instanceof cliCommandChain ) {
			$this->_CommandChain = new cliCommandChain();
		}
		return $this->_CommandChain;
	}
	
	/**
	 * Set $_CommandChain to $inCommandChain
	 *
	 * @param cliCommandChain $inCommandChain
	 * @return cliCommand
	 */
	function setCommandChain(cliCommandChain $inCommandChain) {
		if ( $inCommandChain !== $this->_CommandChain ) {
			$this->_CommandChain = $inCommandChain;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_CommandHelp
	 *
	 * @return string
	 */
	function getCommandHelp() {
		return $this->_CommandHelp;
	}
	
	/**
	 * Set $_CommandHelp to $inCommandHelp
	 *
	 * @param string $inCommandHelp
	 * @return cliCommand
	 */
	function setCommandHelp($inCommandHelp) {
		if ( $inCommandHelp !== $this->_CommandHelp ) {
			$this->_CommandHelp = $inCommandHelp;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Used by the help system, if true adds [=value] to help output
	 *
	 * @return boolean
	 * @access public
	 */
	function getCommandRequiresValue() {
		return $this->_CommandRequiresValue;
	}
	
	/**
	 * Set $_CommandRequiresValue to $inCommandRequiresValue
	 *
	 * @param boolean $inCommandRequiresValue
	 * @return cliCommand
	 * @access public
	 */
	function setCommandRequiresValue($inCommandRequiresValue) {
		if ( $this->_CommandRequiresValue !== $inCommandRequiresValue ) {
			$this->_CommandRequiresValue = $inCommandRequiresValue;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_CommandIsSwitch
	 *
	 * @return boolean
	 * @access public
	 */
	function getCommandIsSwitch() {
		return $this->_CommandIsSwitch;
	}
	
	/**
	 * Set $_CommandIsSwitch to $inCommandIsSwitch
	 *
	 * @param boolean $inCommandIsSwitch
	 * @return cliCommand
	 * @access public
	 */
	function setCommandIsSwitch($inCommandIsSwitch) {
		if ( $this->_CommandIsSwitch !== $inCommandIsSwitch ) {
			$this->_CommandIsSwitch = $inCommandIsSwitch;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_CommandIsOptional
	 *
	 * @return boolean
	 * @access public
	 */
	function getCommandIsOptional() {
		return $this->_CommandIsOptional;
	}
	
	/**
	 * Set $_CommandIsOptional to $inCommandIsOptional
	 *
	 * @param boolean $inCommandIsOptional
	 * @return cliCommand
	 * @access public
	 */
	function setCommandIsOptional($inCommandIsOptional) {
		if ( $this->_CommandIsOptional !== $inCommandIsOptional ) {
			$this->_CommandIsOptional = $inCommandIsOptional;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns true if the application should be halted after
	 * this command executes. Used to control initial app firing.
	 *
	 * @return boolean
	 */
	function haltAppAfterExecute() {
		return $this->_HaltAppAfterExecute;
	}
	
	/**
	 * Set $_HaltAppAfterExecute to $inHaltAppAfterExecute
	 *
	 * @param boolean $inHaltAppAfterExecute
	 * @return cliCommand
	 */
	function setHaltAppAfterExecute($inHaltAppAfterExecute) {
		if ( $inHaltAppAfterExecute !== $this->_HaltAppAfterExecute ) {
			$this->_HaltAppAfterExecute = $inHaltAppAfterExecute;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns request object
	 *
	 * @return cliRequest
	 */
	function getRequest() {
		return $this->_Request;
	}
	
	/**
	 * Set the cliRequest object
	 *
	 * @param cliRequest $inRequest
	 * @return cliCommand
	 */
	function setRequest(cliRequest $inRequest) {
		if ( $inRequest !== $this->_Request ) {
			$this->_Request = $inRequest;
			$this->setModified();
		}
		return $this;
	}
}