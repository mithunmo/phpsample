<?php
/**
 * musicLicenseModel.class.php
 * 
 * musicLicenseModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category musicLicenseModel
 * @version $Rev: 623 $
 */


/**
 * musicLicenseModel class
 * 
 * Provides the "musicLicense" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category musicLicenseModel
 */
class musicLicenseModel extends mvcModelBase {
		
	/**
	 * Stores all the licenseID that needs to be validated
	 * 
	 * @var array
	 * @access protected  
	 */
	protected $_LicenseID = array();
		
	/**
	 * Stores the movieID
	 * 
	 * @var integer
	 * @access protected 
	 */
	protected $_MovieID;

	/**
	 * Stores a mofilmMovie instance
	 *
	 * @var mofilmMovie
	 * @access protected
	 */
	protected $_Movie;
	
	

	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * sets the array of licenseID that needs to be validated
	 * 
	 * @param array $inLicenseID
	 * @return musicLicenseModel 
	 */
	function setLicenseID($inLicenseID) {
		if ( $inLicenseID !== $this->_LicenseID) {
			$this->_LicenseID = $inLicenseID;
		}
		return $this;
		
	}
	
	/**
	 * Returns the array of licenseID
	 * 
	 * @return array
	 */
	function getLicenseID() {
		return $this->_LicenseID;
	}
	
	/**
	 * Sets the movieID
	 * 
	 * @param integer $inMovieID
	 * @return musicLicenseModel 
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID) {
			$this->_MovieID = $inMovieID;
		}
		return $this;
		
	}
	
	/**
	 * Returns the movieID
	 * 
	 * @return integer 
	 */
	function getMovieID() {
		return $this->_MovieID;
	}
	
	/**
	 * Validates a list of licenes and associates the valid ones with the movieID
	 * 
	 * @return array 
	 */
	function getValidLicense() {
		$movieLicenseList = array();
		$oMovie = $this->getMovie();
		$oList = "";
		
		if ( !$oMovie instanceof mofilmMovie || !$oMovie->getID() ) {
			throw new mofilmException("MovieID not found");
		}
		
		foreach ( $this->getLicenseID() as $inLicense ) {
			if ( $this->isValidMofilmMovieLicense($inLicense) ) {
				systemLog::notice("Found in  movie DB");
				
				$movieLicenseList[] = $oMovie->getLicenseSet()->getObjectByID($inLicense);
			} elseif ( $this->validMofilmUserMusicLicese($inLicense,$this->getMovie()->getUserID()) ) {
				systemLog::notice("Found in  user DB");
				$oMofilmUserMusicLicense = mofilmUserMusicLicense::getInstance($inLicense);
				$oMovie->getLicenseSet()->setObject($oMofilmUserMusicLicense);

				$movieLicenseList[] = $oMofilmUserMusicLicense;
			} else {
				if ( $oList == "" ) {
					systemLog::notice("Calling the mofilm music API for license ".$inLicense);	
					$oMofilmMusicManager = new mofilmMusicManager();
					$oList = $oMofilmMusicManager->getUserLicense($this->getMovieID());
					unset($oMofilmMusicManager);
				}

				if ( array_key_exists($inLicense, $oList) ) {
				if ( $oMovie->getLicenseSet()->hasLicense($inLicense) ) {
						
					} else {
						$oMovie->getLicenseSet()->setObject($oList[$inLicense]);
					}
					$movieLicenseList[] = $oList[$inLicense];
				} else {
					$oMofilmUserMusicLicense = new mofilmUserMusicLicense();
					$oMofilmUserMusicLicense->setLicenseID($inLicense);
					$oMofilmUserMusicLicense->setTrackName("Null");
					$oMofilmUserMusicLicense->setStatus(mofilmUserMusicLicense::INVALID_LICENSE);
					$movieLicenseList[] = $oMofilmUserMusicLicense;
				}
			}
		}

		if ( $oMovie->isModified() ) {
			$oMovie->save();
		}
		
		$oMovie->getLicenseSet()->reset();
		return $movieLicenseList;
	}
	
	/**
	 * Checks if the license is a valid mofilmserMusicLicense object
	 * 
	 * @param string $inLicense
	 * @param integer $inUserID
	 * @return boolean 
	 */
	function validMofilmUserMusicLicese($inLicense,$inUserID) {
		$oMofilmUserMusicLicene = mofilmUserMusicLicense::getInstanceByLicenseAndUserID($inUserID, $inLicense);
		if ( $oMofilmUserMusicLicene instanceof mofilmUserMusicLicense ) {
			if ( $oMofilmUserMusicLicene->isValidLicense() ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Checks if the movieLicense is valid 
	 * 
	 * @param string $inLicense
	 * @return boolean 
	 */
	function isValidMofilmMovieLicense($inLicense) {
		$oMovie = $this->getMovie();
		if ( $oMovie->getLicenseSet()->hasLicense($inLicense) && $this->validMofilmUserMusicLicese($inLicense, $oMovie->getUserID()) ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Gets the list of valid licenses per movie
	 *  
	 * @return array 
	 */
	function getMovieDetails() {
		$oMovie = $this->getMovie();
		if ( !$oMovie instanceof mofilmMovie || !$oMovie->getID() ) {
			throw new mofilmException("Invalid MovieID");
		}
		
		return $oMovie->getLicenseSet();
	}
	
	/**
	 * Attempts to load a movie instance from the movieID
	 *
	 * @return mofilmMovie
	 */
	function getMovie() {
		if ( !$this->_Movie instanceof mofilmMovie ) {
			$this->_Movie = mofilmMovieManager::getInstanceByID($this->getMovieID());
		}
		return $this->_Movie;
	}
}
