<?php
/**
 * cliCommandChain Class
 * 
 * Stored in chain.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandChain
 * @version $Rev: 707 $
 */


/**
 * cliCommandChain class
 *
 * Holds a set of commands to be executed within a command or application.
 * The command chain can only hold one instance of each command, unless
 * the object properties are different (e.g. different command pattern).
 * 
 * <code>
 * // example of setting up command chain
 * $oChain = new cliCommandChain(
 *     array(
 *         new cliCommandLog($cliRequest),
 *         new cliCommandLogToConsole($cliRequest),
 *         new cliCommandHelp($cliRequest)
 *     )
 * );
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliCommandChain
 */
class cliCommandChain extends baseSet {
	
	/**
	 * Creates a new cliCommandChain, $inCommands must be an array of cliCommands
	 *
	 * @param array $inCommands
	 * @return cliCommandChain
	 */
	function __construct(array $inCommands = array()) {
		$this->reset();
		if ( count($inCommands) > 0 ) {
			foreach ( $inCommands as $oCommand ) {
				$this->addCommand($oCommand);
			}
		}
	}
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		parent::_resetSet();
	}
	
	/**
	 * Returns true if $inCommand exists in this chain
	 *
	 * @param string $inCommand
	 * @return boolean
	 */
	function hasCommand($inCommand) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oCommand = new cliCommand();
			foreach ( $this as $oCommand ) {
				if ( $oCommand->getCommandPattern() === $inCommand ) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Returns a command from the chain matching $inCommand or false if no match
	 *
	 * @param string $inCommand
	 * @return cliCommand
	 */
	function getCommand($inCommand) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oCommand = new cliCommand();
			foreach ( $this as $oCommand ) {
				if ( $oCommand->getCommandPattern() === $inCommand ) {
					return $oCommand;
				}
			}
		}
		return false;
	}
	
	/**
	 * Adds a cliCommand to the chain for further processing
	 *
	 * @param cliCommand $inCommand
	 * @return cliCommandChain
	 */
	function addCommand(cliCommand $inCommand) {
		return $this->_setValue($inCommand);
	}
	
	/**
	 * Removes $inCommand from the chain
	 *
	 * @param cliCommand $inCommand
	 * @return cliCommandChain
	 */
	function removeCommand(cliCommand $inCommand) {
		return $this->_removeItemWithValue($inCommand);
	}
	
	/**
	 * Returns the command count
	 *
	 * @return integer
	 */
	function getCount() {
		return parent::_itemCount();
	}
}