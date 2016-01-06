<?php
/**
 * mofilmMusicManager
 *
 * Stored in api.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMusicManager
 * @category mofilmMusicManager
 * 
 */
class mofilmMusicManager {
	
	/**
	 * Stores the controller of the mofilmmusic API
	 * 
	 * @var string
	 * @access protected 
	 */
	protected $_Source;
	
	const PARAM_MELDODY = 0;
	const PARAM_AUDIOSOCKET = 1; 
	
	
	/**
	 * This calls the get_user_license mofilmmusic API
	 * 
	 * @param integer $inMovieID
	 * @return array 
	 */
	function getUserLicense($inMovieID) {
		$oMovie = mofilmMovieManager::getInstanceByID($inMovieID);
		if ( $oMovie instanceof mofilmMovie ) {
			$inUserID = $oMovie->getUserID();
			$oMofilmMusicApi = new mofilmMusicApi();
			$oMofilmMusicApi->setUserID($inUserID);
			$oMofilmMusicApi->setApiAction("get_user_licences");
			$oMofilmMusicApi->setApiController("api");
			$oList = $oMofilmMusicApi->getUserLicenseList();
			return $oList;
		} else {
			throw new mofilmException("Invalid movieID");
		}		
	}
	
	/**
	 * This calls the get_licence_details mofilmmusic API
	 * 
	 * @param string $inLicense
	 * @return boolean
	 */
	function isValidLicense($inLicense) {
		/*
		 * @todo DR: redundant code to be removed?
		 */
		//$inUserID = $oMovie->getUserID();

		$oMofilmMusicApi = new mofilmMusicApi();
		$oMofilmMusicApi->setLicenseID($inLicense);
		$oMofilmMusicApi->setApiAction("get_licence_details");
		$oMofilmMusicApi->setApiController("api");
		return $oMofilmMusicApi->isValidLicense();
	}
	
	/**
	 * Gets the list of userLicense by userID
	 * 
	 * @param integer $inUserID
	 * @return array 
	 */
	function getUserLicenseByUserID($inUserID) {
		
		$oList = array();
		
		if ( $this->getSource() == self::PARAM_AUDIOSOCKET ) {
			$oMofilmMusicApisso = new mofilmMusicApisso();
			$oMofilmMusicApisso->setUserID($inUserID);
			$oList = $oMofilmMusicApisso->getUserLicenseList();
			
		} else {
			
			$oMofilmMusicApi = new mofilmMusicApi();
			$oMofilmMusicApi->setUserID($inUserID);
			$oMofilmMusicApi->setApiAction("get_user_licences");
			$oMofilmMusicApi->setApiController("api");
			$oList = $oMofilmMusicApi->getUserLicenseList();
			foreach ( $oList as $key => $oMofilmUserMusicLicense ) {
				if ( !$oMofilmUserMusicLicense->isValidLicense() ) {
					unset($oList[$key]);
				}
			}
		}	
			return $oList;
	}
	

	/**
	 * sets the controller of the mofilmmusic API
	 * 
	 * @param string $inSource
	 * @return string 
	 */
	function setSource($inSource) {
		if ( $inSource !== $this->_Source) {
			$this->_Source = $inSource;
		}
		return $this;
		
	}
	
	/**
	 * Returns the controller of the mofilmmusic API
	 * 
	 * @return string
	 */
	function getSource() {
		return $this->_Source;
	}
	
	
	
}
