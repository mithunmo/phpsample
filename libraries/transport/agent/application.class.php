<?php
/**
 * transportAgentApplication class
 * 
 * Stored in transportAgentApplication.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage transport
 * @category transportAgentApplication
 * @version $Rev: 650 $
 */


/**
 * transportAgentApplication class
 * 
 * Handles launching applications, these are CLI apps that are either other
 * PHP scripts or other programs available to the system. Process can be fired
 * either inline or as background processes. Background processes will always
 * return a status of true and raise a log message of notice level.
 * 
 * The application to be fired should be executable before being passed to
 * this mechanism as no additional checks will be made when calling the app.
 * 
 * @package scorpio
 * @subpackage transport
 * @category transportAgentApplication
 */
class transportAgentApplication extends transportAgentBase {
	
	/**
	 * Stores TransportName
	 * 
	 * @var string
	 */
	protected $_TransportName			= 'Application Transport';
	
	
	
	/**
	 * Main methods
	 */
	
	/**
	 * @see transportAgentBase::_process()
	 */
	protected function _process() {
		systemLog::message('Launching application');
		
		// build script
		$script = $this->getApplication();
		$script .= ' 2>&1';
		
		if ( $this->launchInBg() ) {
			$script .= ' &';
		}
		
		// launch script
		$res = `$script`;
		
		systemLog::info('Launched '.$script);
		if ( strlen($res) > 0 ) {
			systemLog::info('Response : '.$res);
		}
		
		list($statusNo, $statusMessage) = explode(':', $res);
		$oResponse = new transportResponse();
		$oResponse->setRequest($this->getTransportCredentials());
		$oResponse->setResponse($res);
		$this->setResponse($oResponse);
		
		if ( $this->launchInBg() ) {
			systemLog::notice('Launched script in background with processID ('.$res.')');
			return true;
		}
		
		if ( $statusNo != $this->getAppResponseOk() ) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * @see transportAgentBase::_reset()
	 */
	protected function _reset() {
	}
	
	
	
	/**
	 * Helper methods for convenience and legibility
	 */
	
	/**
	 * Returns true if app should be launched in background
	 *
	 * @return boolean
	 */
	function launchInBg() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_APP_BACKGROUND, transportCredentials::DEFAULT_APP_BACKGROUND);
	}
	
	/**
	 * Returns the value that is an OK app response, usually an integer of "0"
	 *
	 * @return string
	 */
	function getAppResponseOk() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_APP_RESPONSE_OK, transportCredentials::DEFAULT_APP_RESPONSE_OK);
	}
	
	/**
	 * Return the message body to be sent
	 *
	 * @return string
	 */
	function getApplication() {
		return $this->getTransportCredentials()->getParam(transportCredentials::PARAM_MESSAGE_BODY);
	}
}