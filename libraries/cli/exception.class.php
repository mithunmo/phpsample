<?php
/**
 * cliException Class
 * 
 * Stored in exception.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliException
 * @version $Rev: 650 $
 */


/**
 * cliException class
 *
 * Specific exception class for cli applications. Provides context where
 * exceptions occur.
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliException
 */
class cliException extends systemException {
	
}

/**
 * cliApplicationException class
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationException
 */
class cliApplicationException extends cliException {
	
}

/**
 * cliApplicationCommandException class
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationCommandException
 */
class cliApplicationCommandException extends cliApplicationException {
	
	/**
	 * Stores $_Command
	 *
	 * @var cliCommand
	 * @access protected
	 */
	protected $_Command;
	
	
	/**
	 * Creates a new cliApplicationCommandException
	 *
	 * @param cliCommand $inCommand
	 * @param string $inErrorMessage
	 */
	function __construct($inCommand, $inErrorMessage = '') {
		parent::__construct($inErrorMessage);
		$this->setCommand($inCommand);
	}
	
	
	
	/**
	 * Returns $_Command
	 *
	 * @return cliCommand
	 * @access public
	 */
	function getCommand() {
		return $this->_Command;
	}
	
	/**
	 * Set $_Command to $inCommand
	 *
	 * @param cliCommand $inCommand
	 * @return cliApplicationCommandException
	 * @access public
	 */
	function setCommand($inCommand) {
		if ( $this->_Command !== $inCommand ) {
			$this->_Command = $inCommand;
		}
		return $this;
	}
}