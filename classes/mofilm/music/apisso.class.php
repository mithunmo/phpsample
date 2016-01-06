<?php
/**
 * mofilmMusicApisso
 *
 * Stored in api.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMusicApisso
 * @category mofilmMusicApisso
 * 
 */
class mofilmMusicApisso {
	
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
	 * Stores the reponse  from the mofilmmusic API
	 * 
	 * @var XML
	 * @access protected 
	 */
	protected $_Response;
	
	/**
	 * Stores the mofilm UserID
	 * 
	 * @var integer
	 * @access protected 
	 */
	protected $_UserID;
			
	/**
	 * sets the controller of the mofilmmusic API
	 * 
	 * @param string $inController
	 * @return mofilmMusicApisso 
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
	 * @return mofilmMusicApisso 
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
	 * Sets the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @param JSON $inResponse
	 * @return mofilmMusicApisso 
	 */
	function setResponse($inResponse) {
		if ( $inResponse !== $this->_Response) {
			$this->_Response = $inResponse;
		}
		return $this;
		
	}
	
	/**
	 * Returns the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @return JSON
	 */
	function getResponse() {
		return $this->_Response;
	}

	/**
	 * Sets the mofilm userID
	 * 
	 * @param integer $inUserID
	 * @return mofilmMusicApisso 
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
	 * Returs the track name from the XML
	 * 
	 * @return string 
	 */
	function getTrackName() {
		//return (string) $this->getResponse()->response->song_name;
	}
	
	/**
	 * Returns the username from the XML
	 * 
	 * @return string 
	 */
	function getUserName() {
		//return (string) $this->getResponse()->response->username;
	}
	
	/**
	 * Gets the list of licenses per mofilm userID
	 * 
	 * @param integer $inUserID
	 * @return array 
	 */
	function getUserLicense() {
		return false;
		try {
			$apiKey = system::getConfig()->getParam("momusic", "apiKey")->getParamValue();
			$server = system::getConfig()->getParam("momusic", "server");

			$url = $server."/".mofilmUserManager::getInstanceByID($this->getUserID())->getParamSet()->getParam(mofilmUser::PARAM_MOMUSIC_SOURCE)."/works";
			systemLog::message($url);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Audiosocket-Token:'.system::getConfig()->getParam('momusic', 'apiKey')->getParamValue()));
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
			curl_setopt($ch, CURLOPT_POST, 0);
			$jsonResponse = curl_exec($ch);
			$result = json_decode($jsonResponse);
			if ( $jsonResponse == "<h1>Not Found</h1>") {
				return false;
			} else {
				$this->setResponse($result);
				return true;
			}
		}
		catch(Exception $e) {
			throw new mofilmException("SSO API Not Reachable".$e->getMessage());
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
			$oResponse = $this->getResponse();
			foreach ( $oResponse as $value ) {
				$licenseID = $value->id;
				if ( strlen((string) $licenseID) > 0 ) {
					$oMofimMusicLicense = mofilmUserMusicLicense::getInstance((string)$licenseID);
					if ( !$oMofimMusicLicense instanceof mofilmUserMusicLicense || !$oMofimMusicLicense->getID() ) {
						$oTracks = $value->tracks;
						$trackName = array();
						foreach ( $oTracks as $inTrack ) {
							$trackName[] = $inTrack->name;
						}
						$tracks = implode(",",$trackName);
						$oMofimMusicLicense = new mofilmUserMusicLicense();
						$oMofimMusicLicense->setLicenseID((string) $licenseID);
						$oMofimMusicLicense->setTrackName($tracks);
						$oMofimMusicLicense->setExpiryDate ("2099-02-05 05:12:31");
						$oMofimMusicLicense->setUserID((string) $this->getUserID());
						$oMofimMusicLicense->setStatus(0);
						$oMofimMusicLicense->setMusicSource("AudioSocket");
						$oMofimMusicLicense->save();
					}
				}
			}
			$validLicense = mofilmUserMusicLicense::listOfObjectsByUserID($this->getUserID());
			return $validLicense;
		} else {
			$validLicense = mofilmUserMusicLicense::listOfObjectsByUserID($this->getUserID());
			if ( count($validLicense) <=0 ) {
				throw new mofilmException("UserID {$this->getUserID()} has no licenses");
			} else {
				return $validLicense;
			}
		}
	}
}