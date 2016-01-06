<?php
/**
 * transportManager class
 * 
 * Stored in transportManager.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage transport
 * @category transportManager
 * @version $Rev: 707 $
 */

/**
 * transportManager class
 * 
 * Provides instancing and constants for the transport mechanisms. A transport
 * is a mechanism for relaying data or for performing remote actions within an
 * application. Primarily this is for making HTTP requests in a controlled
 * manner through a defined interface but it can be used to send emails,
 * contact other applications etc. Additional agents can be implemented for
 * additional services not provided by the basic clients.
 * 
 * transportManager requires the agent type and the credentials object.
 * 
 * Example usage, connect to www.example.com to a CGI script and post some
 * encoded vars:
 * 
 * <code>
 * $oCredentials = new transportCredentials();
 * $oCredentials->setParam(
 *     array(
 *         transportCredentials::PARAM_PROTOCOL => 'http',
 *         transportCredentials::PARAM_HOST => 'www.example.com',
 *         transportCredentials::PARAM_PORT => '8080',
 *         transportCredentials::PARAM_PATH => '/server/side/path.cgi',
 *         
 *         transportCredentials::PARAM_HTTP_TIMEOUT => 60,
 *         transportCredentials::PARAM_HTTP_PERSISTENT_TIMEOUT => 60,
 *         transportCredentials::PARAM_HTTP_METHOD => 'post',
 *         
 *         transportCredentials::PARAM_MESSAGE_BODY => 'encode_post_vars',
 *     ), null
 * );
 * 
 * $oTransport = transportManager::getInstance(
 *     transportManager::TRANSPORT_AGENT_HTTP,
 *     $oCredentials
 * );
 * if ( $oTransport->send() ) {
 *     // sent successfully
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage transport
 * @category transportManager
 */
class transportManager {
	
	const TRANSPORT_AGENT_APPLICATION = 'transportAgentApplication';
	const TRANSPORT_AGENT_EMAIL = 'transportAgentEmail';
	const TRANSPORT_AGENT_HTTP = 'transportAgentHttp';
	
	/**
	 * Array of valid transport classes
	 *
	 * @var array
	 * @static 
	 */
	static private $_AvailableTransports = array(
		self::TRANSPORT_AGENT_APPLICATION,
		self::TRANSPORT_AGENT_EMAIL,
		self::TRANSPORT_AGENT_HTTP 
	);
	
	/**
	 * Holds an instance of each transport
	 *
	 * @var array
	 * @static 
	 */
	static private $_Instances = array();
	
	/**
	 * Returns array of available transports
	 *
	 * @return array
	 * @static 
	 */
	static function getAvailableTransports() {
		return self::$_AvailableTransports;
	}
	
	/**
	 * Add a new transport to the list of allowed transports (class name only)
	 * 
	 * @param string $inTransport
	 * @return void
	 * @static
	 */
	static function addTransport($inTransport) {
		if ( !in_array($inTransport, self::$_AvailableTransports) ) {
			self::$_AvailableTransports[] = $inTransport;
		}
	}
	
	/**
	 * Removes a new transport from the list of allowed transports (class name only)
	 * 
	 * @param string $inTransport
	 * @return void
	 * @static
	 */
	static function removeTransport($inTransport) {
		$key = array_search($inTransport, self::$_AvailableTransports);
		if ( $key !== false ) {
			unset(self::$_AvailableTransports[$key]);
		}
	}
	
	/**
	 * Returns true if $inTransport is a valid transport
	 *
	 * @param string $inTransport
	 * @return boolean
	 * @static 
	 */
	static function isValidTransport($inTransport) {
		return in_array($inTransport, self::$_AvailableTransports);
	}
	
	/**
	 * Returns an instance of the transport
	 *
	 * @return transportAgentBase
	 * @throws transportException
	 * @static 
	 */
	static function getInstance($inTransport, transportCredentials $oCredentials) {
		if ( isset(self::$_Instances[$inTransport]) ) {
			$oTransport = self::$_Instances[$inTransport];
			$oTransport->reset();
			$oTransport->setTransportCredentials($oCredentials);
			return $oTransport;
		}
		
		if ( self::isValidTransport($inTransport) ) {
			$oTransport = new $inTransport($oCredentials);
			if ( $oTransport instanceof transportAgentBase ) {
				$oTransport->setTransportCredentials($oCredentials);
				self::$_Instances[$inTransport] = $oTransport;
				return $oTransport;
			} else {
				throw new transportException("Failed to create instance of transport");
			}
		} else {
			throw new transportException("$inTransport is not a valid transport");
		}
	}
}