<?php
/**
 * cliResponse Class
 * 
 * Stored in response.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliResponse
 * @version $Rev: 707 $
 */


/**
 * cliResponse class
 *
 * Holds messages to be sent to the client during a cliApplication execution.
 * Exceptions are handled in the {@link cliApplication} class and are not
 * needed to be caught or rendered here.
 * 
 * cliResponse implements __toString for pretty printing the output to the CLI.
 * 
 * Multiple response messages can be queued for display, or the current stack
 * can be flushed to the console. In most cases, the messages are held until
 * the cli app has finished executing. However you may wish to display status
 * information to the CLI rather than a log file, in which case you can use
 * {@link cliResponse::flushResponse()}.
 * 
 * <code>
 * // cliResponse is usually used within an application context:
 * // ... some other class code ...
 * function updateResponse() {
 *     $oResponse = $this->getRequest()->getApplication()->getResponse();
 *     $oResponse->addResponse("\nAdd some text, you can add newlines and other things.");
 *     $oResponse->addResponse("\nIf you want some data output consider:\n".cliConsoleTools::cliDataPrint());
 * }
 * // ... the rest of the class ...
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliResponse
 */
class cliResponse extends baseSet {
	
	/**
	 * Creates a new cliResponse
	 *
	 * @return cliResponse
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * Allows cliResponse to be used as a string
	 *
	 * @return string
	 */
	function __toString() {
		return implode("\n", array_values($this->_getItem()))."\n";
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
	 * Sends response to the client
	 *
	 * @return void
	 */
	function sendResponse() {
		echo $this->__toString();
	}
	
	/**
	 * Outputs any response messages and clears the response
	 *
	 * @return void
	 */
	function flushResponse() {
		echo $this->__toString();
		$this->clearResponse();
	}
	
	/**
	 * Add a message to the response, with an optional identifier 
	 *
	 * @param string $inMessage
	 * @param string $inIdent (optional)
	 * @return cliResponse
	 */
	function addResponse($inMessage, $inIdent = null) {
		return $this->_setItem($this->getIdent($inIdent), $inMessage);
	}
	
	/**
	 * Creates an ident string for the response message body, or if $inIdent is not null, uses that
	 *
	 * @param string $inIdent
	 * @return string
	 */
	function getIdent($inIdent = null) {
		if ( $inIdent !== null ) {
			return $inIdent;
		}
		
		$i=1;
		$keyID = $key = 'response.body';
		while ( $this->_getItem($keyID) ) {
			$keyID = $key.$i;
			++$i; 
		}
		return $keyID;
	}
	
	/**
	 * Returns a specific response body, or all if null
	 *
	 * @param string $inIdent
	 * @return string|array
	 */
	function getResponse($inIdent = null) {
		return $this->_getItem($inIdent);
	}
	
	/**
	 * Removes a specific response from the body
	 *
	 * @param string $inIdent
	 * @return cliResponse
	 */
	function removeResponse($inIdent = null) {
		return $this->_removeItem($inIdent);
	}
	
	/**
	 * Clears any response body
	 *
	 * @return cliResponse
	 */
	function clearResponse() {
		return $this->_resetSet();
	}
}