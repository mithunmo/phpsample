<?php
/**
 * mofilmMusicApi
 *
 * Stored in api.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMusicApi
 * @category mofilmMusicApi
 * 
 */
class mofilmMusicApi {
	
	/**
	 * Stores the controller of the mofilmmusic API
	 * 
	 * @var string
	 * @access protected 
	 */
	protected $_Controller;
	
	/**
	 * Stores the action of the mofilmmusic API
	 * 
	 * @var string
	 * @access protected 
	 */
	protected $_Action;
	
	/**
	 * Stores the LicenseID
	 * 
	 * @var string
	 * @access protected  
	 */
	protected $_LicenseID;
	
	/**
	 * Stores the reponse XML from the mofilmmusic API
	 * 
	 * @var XML
	 * @access protected 
	 */
	protected $_ResponseXML;
	
	/**
	 * Stores the mofilm UserID
	 * 
	 * @var integer
	 * @access protected 
	 */
	protected $_UserID;
	
	
	
	/**
	 * Sets the LicenseID to be validated
	 * 
	 * @param string $inLicenseID
	 * @return mofilmMusicApi 
	 */
	function setLicenseID($inLicenseID) {
		if ( $inLicenseID !== $this->_LicenseID) {
			$this->_LicenseID = $inLicenseID;
		}
		return $this;
		
	}
	
	/**
	 * Returns the licenseID
	 * 
	 * @return string 
	 */
	function getLicenseID() {
		return $this->_LicenseID;
	}
	
	/**
	 * sets the controller of the mofilmmusic API
	 * 
	 * @param string $inController
	 * @return mofilmMusicApi 
	 */
	function setApiController($inController) {
		if ( $inController !== $this->_Controller) {
			$this->_Controller = $inController;
		}
		return $this;
		
	}
	
	/**
	 * Returns the controller of the mofilmmusic API
	 * 
	 * @return string
	 */
	function getApiController() {
		return $this->_Controller;
	}
	
	/**
	 * Sets the action of the mofilmmusic API
	 *  
	 * @param string $inAction
	 * @return mofilmMusicApi 
	 */
	function setApiAction($inAction) {
		if ( $inAction !== $this->_Action) {
			$this->_Action = $inAction;
		}
		return $this;
		
	}
	
	/**
	 * Returns the API action
	 * 
	 * @return string 
	 */
	function getApiAction() {
		return $this->_Action;
	}
	
	/**
	 * Sets the response XML from the mofilmmusic API
	 * 
	 * @param XML $inResponseXML
	 * @return mofilmMusicApi 
	 */
	function setResponseXML($inResponseXML) {
		if ( $inResponseXML !== $this->_ResponseXML) {
			$this->_ResponseXML = $inResponseXML;
		}
		return $this;
		
	}
	
	/**
	 * Returns the XML response from the mofilmmusic API
	 * 
	 * @return XML 
	 */
	function getResponseXML() {
		return $this->_ResponseXML;
	}

	/**
	 * Sets the mofilm userID
	 * 
	 * @param integer $inUserID
	 * @return mofilmMusicApi 
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID) {
			$this->_UserID = $inUserID;
		}
		return $this;
	}
	
	/**
	 * Returns the mofilm UserID
	 * 
	 * @return integer 
	 */
	function getUserID() {
		return $this->_UserID;
	}
	
	/**
	 * Forms the request to call the mofilmusic API
	 * 
	 * @return boolean 
	 */
	function isValidLicense() {
		try {
			$publicKey = system::getConfig()->getParam("mofilmmusic", "publickey")->getParamValue();
			$privateKey = system::getConfig()->getParam("mofilmmusic", "privatekey")->getParamValue();		
			$server = system::getConfig()->getParam("mofilmmusic", "server");

			$time = time();
			$hash = sha1(
				http_build_query(
					array(
						'apiKey' => $publicKey,
						'privateKey' => $privateKey,
						'time' => $time,
						'uri' => '/'.$this->getApiController().'/'.$this->getApiAction(),
						'licence_id' => $this->getLicenseID()
					)
				)
			);

			$uri = sprintf('%s/%s/%s', $server, $this->getApiController(), $this->getApiAction());
			$query = sprintf('apiKey=%s&hash=%s&time=%d&licence_id=%s', $publicKey, $hash, $time, $this->getLicenseID());
			utilityCurl::$options = utilityCurl::$options + array(
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_TIMEOUT => 10,
			);
			$response = utilityCurl::fetchContent($uri.'?'.$query);

			$oXML = simplexml_load_string($response);
			$this->setResponseXML($oXML);

			if ( isset($oXML->response->messages->error) ) {
				$error = (string) $oXML->response->messages->error;
			} else {
				$error = '';
			}

			if ( $error == "" ) {
				return true;
			} else {
				return false;
			}
		}
		catch(Exception $e) {
			throw new mofilmException("Mofilm Music API Not Reachable");
		}
	}
	
	/**
	 * Returs the track name from the XML
	 * 
	 * @return string 
	 */
	function getTrackName() {
		return (string) $this->getResponseXML()->response->song_name;
	}
	
	/**
	 * Returns the username from the XML
	 * 
	 * @return string 
	 */
	function getUserName() {
		return (string) $this->getResponseXML()->response->username;
	}
	
	/**
	 * Gets the list of licenses per mofilm userID
	 * 
	 * @return array 
	 */
	function getUserLicense() {
		try {
			$publicKey = system::getConfig()->getParam("mofilmmusic", "publickey")->getParamValue();
			$privateKey = system::getConfig()->getParam("mofilmmusic", "privatekey")->getParamValue();		
			$server = system::getConfig()->getParam("mofilmmusic", "server");

			$time = time();
			$hash = sha1(
				http_build_query(
					array(
						'apiKey' => $publicKey,
						'time' => $time,
						'uri' => '/'.$this->getApiController().'/'.$this->getApiAction(),
						'mofilm_user_id' => $this->getUserID(),
						'privateKey' => $privateKey,
					)
				)
			);

			$uri = sprintf('%s/%s/%s', $server, $this->getApiController(), $this->getApiAction());
			$query = sprintf('apiKey=%s&hash=%s&time=%d&mofilm_user_id=%s',$publicKey, $hash, $time, $this->getUserID());
			utilityCurl::$options = utilityCurl::$options + array( CURLOPT_SSL_VERIFYHOST => false, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_TIMEOUT => 10);
			$response = utilityCurl::fetchContent($uri.'?'.$query);

			systemLog::debug($response);
			$oXML = simplexml_load_string($response);
			$this->setResponseXML($oXML);
			return true;
		}
		catch(Exception $e) {
			throw new mofilmException("Mofilm Music API Not Reachable");
		}
	}
	
	/**
	 * Forms a list of mofilmUserMusiclicense objects
	 * 
	 * @return array 
	 */
	function getUserLicenseList() {
		$validLicense = array();
		if ( $this->getUserLicense() ) {
			foreach ( $this->getResponseXML()->response->data as $value ) {
				if ( strlen((string) $value->licence_id) > 0 ) {
					$oMofimMusicLicense = mofilmUserMusicLicense::getInstance((string)$value->licence_id);
					if ( !$oMofimMusicLicense instanceof mofilmUserMusicLicense || !$oMofimMusicLicense->getID() ) {
						$oMofimMusicLicense = new mofilmUserMusicLicense();
						$oMofimMusicLicense->setLicenseID((string) $value->licence_id);
						$oMofimMusicLicense->setTrackName((string) $value->song_name);
						$oMofimMusicLicense->setExpiryDate((string) $value->expiry_date);
						$oMofimMusicLicense->setUserID((string) $value->mofilm_user_id);
						$oMofimMusicLicense->isValidLicense();
						$oMofimMusicLicense->save();
					}

					$validLicense[(string) $value->licence_id] = $oMofimMusicLicense;
				}
			}

			return $validLicense;
		} else {
			throw new mofilmException("UserID {$this->getUserID()} has no licenses");
		}
	}
}