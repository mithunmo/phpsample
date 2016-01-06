<?php
/**
 * uploadModel.class.php
 * 
 * uploadModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category uploadModel
 * @version $Rev: 393 $
 */


/**
 * uploadModel class
 * 
 * Handles uploads and sorting out all the backend business logic for uploads.
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category uploadModel
 */
class uploadModel extends mvcModelBase {

	/**
	 * Stores $_FileName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_FileName;

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_FileType
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_FileType;

	/**
	 * Stores $_EventID
	 * 
	 * @var integer
	 * @access protected 
	 */
	protected $_EventID;

	/**
	 * Stores $_SourceID
	 * 
	 * @var integer
	 * @access protected 
	 */
	protected $_SourceID;
	
	/**
	 * Stores $_UploaderType
	 * 
	 * @var integer
	 * @access protected 
	 */
	protected $_UploaderType;

	/**
	 * Stores $_Language
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Language;
	
	/**
	 * Stores $_Request
	 *
	 * @var mvcRequest
	 * @access protected
	 */
	protected $_Request;
	
	
	/**
	 * Stores $_AutocompleteSearchTag
	 * 
	 * @var string
	 * @access protected
	 */
	protected $_AutocompleteSearchTag;
	
	/**
	 * Stores $_Tag
	 * 
	 * @var string
	 * @access protected
	 */
	protected $_Tag;


	const UPLOADER_HTML5_UPLOADER = 1;
	const UPLOADER_PL_UPLOAD = 2;

	
	
	/**
	 * Set $_FileName to $inFileName
	 *
	 * @param string $inFileName
	 * @return uploadModel
	 */
	function setFileName($inFileName) {
		if ( $inFileName !== $this->_FileName ) {
			$this->_FileName = $inFileName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_FileName
	 *
	 * @return string
	 */
	function getFileName() {
		return $this->_FileName;
	}

	/**
	 * Set $_EventID to $inEventID
	 *
	 * @param integer $inEventID
	 * @return uploadModel
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_EventID
	 *
	 * @return integer
	 */
	function getEventID() {
		return $this->_EventID;
	}

	/**
	 * Set $_SourceID to $inSourceID
	 *
	 * @param integer $inSourceID
	 * @return uploadModel
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_SourceID
	 *
	 * @return integer
	 */
	function getSourceID() {
		return $this->_SourceID;
	}
	
	/**
	 * Set $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return uploadModel
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_UserID
	 *
	 * @return integer
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Returns $_Request
	 *
	 * @return mvcRequest
	 */
	function getRequest() {
		return $this->_Request;
	}
	
	/**
	 * Set $_Request to $inRequest
	 *
	 * @param mvcRequest $inRequest
	 * @return accountModel
	 */
	function setRequest($inRequest) {
		if ( $inRequest !== $this->_Request ) {
			$this->_Request = $inRequest;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Set $_FileType to $inFiletype
	 *
	 * @param string $inFiletype
	 * @return uploadModel
	 */
	function setFiletype($inFiletype) {
		if ( $inFiletype !== $this->_FileType ) {
			$this->_FileType = $inFiletype;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_FileType
	 *
	 * @return string
	 */
	function getFiletype() {
		return $this->_FileType;
	}


	/**
	 * Set $_UploaderType to $inUploaderType
	 *
	 * @param integer $inUploaderType
	 * @return uploadModel
	 */
	function setUploaderType($inUploaderType) {
		if ( $inUploaderType !== $this->_UploaderType ) {
			$this->_UploaderType = $inUploaderType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_UploaderType
	 *
	 * @return integer
	 */
	function getUploaderType() {
		return $this->_UploaderType;
	}

	/**
	 * Returns $_Language
	 *
	 * @return string
	 */
	function getLanguage() {
		return $this->_Language;
	}

	/**
	 * Set $_AutocompleteSearchTag to $inAutocompleteSearchTag
	 *
	 * @param string $inAutocompleteSearchTag
	 * @return string
	 */
	function setAutocompleteSearhTag($inAutocompleteSearchTag) {
		if ( $inAutocompleteSearchTag !== $this->_AutocompleteSearchTag ) {
			$this->_AutocompleteSearchTag = $inAutocompleteSearchTag;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_AutocompleteSearchTag
	 *
	 * @return string
	 */
	function getAutocompleteSearchTag() {
		return $this->_AutocompleteSearchTag;
	}
	
	/**
	 * Set $_Tag to $inTag
	 *
	 * @param string $inTag
	 * @return string
	 */
	function setMovieTag($inTag) {
		if ( $inTag !== $this->_Tag ) {
			$this->_Tag = $inTag;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Tag
	 *
	 * @return string
	 */
	function getMovieTag() {
		return $this->_Tag;
	}
	
	/**
	 * Set $_Language to $inLanguage
	 *
	 * @param string $inLanguage
	 * @return accountModel
	 */
	function setLanguage($inLanguage) {
		if ( $inLanguage !== $this->_Language ) {
			$this->_Language = $inLanguage;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Merges all the video parts into a proper file and deletes the parts
	 * 
	 * @return void
	 */
	function mergeVideoFile() {
		$files = fileObject::parseDir(mofilmConstants::getVideoFolder() . "/", false);
		$mergeList = array();
		$pattern = '';

		if ( $this->getUploaderType() == self::UPLOADER_HTML5_UPLOADER ) {
			$pattern = '/' . $this->getUserID() . addcslashes($this->getFileName(), "(,),$,#,',\,/") . ".*Part([0-9]*)\./";
		} else {
			$pattern = "/" . $this->getUserID() . addcslashes($this->getFileName(), "(,),$,#,',\,/") . ".*Part([0-9]*)\.[a-z|A-Z]*/";
		}


		foreach ( $files as $oFile ) {
			if ( preg_match($pattern, $oFile->getFilename(), $matches) ) {
				$mergeList[$matches[1]] = $oFile->getFilename();
			}
		}
		ksort($mergeList);
		$path = mofilmConstants::getVideoFolder() . "/";
		for ( $i = 0; $i < count($mergeList); $i++ ) {
			file_put_contents($path . $this->getFileName(), file_get_contents($path . $mergeList[$i]), FILE_APPEND);
			unlink($path . $mergeList[$i]);
		}
	}

	/**
	 * Gets the list of license per userID
	 * 
	 * @param integer $inUserID
	 * @return array 
	 */
	function getLicenseList($inUserID) {
		try {
			$oMofilmMusicManager = new mofilmMusicManager();
			$oMofilmMusicManager->setSource(mofilmMusicManager::PARAM_AUDIOSOCKET);
			return $oMofilmMusicManager->getUserLicenseByUserID($inUserID);
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
		}
	}

	/**
	 * Saves the movie and its assoiated records
	 * 
	 * @param array $inData
	 * @param integer $inUserID 
	 */
	function saveMovie($inData, $inUserID) {

		$oUserTerms = new mofilmUserTerms();
		$oUserTerms->setUserID($inUserID);
		$oUserTerms->setTermsID(0);
		$oUserTerms->save();

		$oMovie = new mofilmMovie();
		$oMovie->setUserID($inUserID);
		$oMovie->setLongDesc($inData["Description"]);
		$oMovie->setShortDesc($inData["Title"]);

		if ( !empty($inData["Duration"]) ) {
			$oMovie->setRuntime($inData["Duration"]);
		}

		$oMovie->setCredits("No credit");

		if ( mofilmUserManager::getInstanceByID($inUserID)->getAutoCommitStatus() == mofilmUserBase::AUTO_COMMIT_STATUS_DISABLED ) {
			$oMovie->setActive(mofilmMovie::ACTIVE_Y);
		} else {
			$oMovie->setActive(mofilmMovie::ACTIVE_Y);
		}
		$oMovie->save();
		
		$oMovie->getSourceSet()->setObject(mofilmSource::getInstance($inData["sourceID"]));

		$oMofilmMovieAsset = new mofilmMovieAsset();
		$oMofilmMovieAsset->setMovieID($oMovie->getID());
		$originalPath = mofilmConstants::getVideoFolder()."/".$inData["fileName"];
		$finalPath = "/share/content/_platform"."/".$oMovie->getID()."/".$inData["fileName"];
		mkdir("/share/content/_platform"."/".$oMovie->getID(), 0755, true);
		copy($originalPath,$finalPath);
		unlink($originalPath);
		$oMofilmMovieAsset->setFilename($finalPath);
		$oMofilmMovieAsset->setType(mofilmMovieAsset::TYPE_SOURCE);
		$oMofilmMovieAsset->setHeight(0);
		$oMofilmMovieAsset->setWidth(0);
		$path_parts = pathinfo($inData["fileName"]);
		$oMofilmMovieAsset->setExt("mov");
		$oMofilmMovieAsset->setDescription("source");
		$oMovie->getAssetSet()->setObject($oMofilmMovieAsset);

		$oMovie->getDataSet()->setMovieID($oMovie->getID());
		$oMovie->getDataSet()->setProperty(mofilmDataname::DATA_USER_IP, $_SERVER["REMOTE_ADDR"]);
		$oMovie->getDataSet()->setProperty(mofilmDataname::DATA_USER_COUNTRY_CODE, $oMovie->getDataSet()->getUserCountryCode());
		$oMovie->getDataSet()->setProperty(mofilmDataname::DATA_MOVIE_LICENSEID, $inData["customLicense"]);


		$oMovie->getLicenseSet()->setMovieID($oMovie->getID());
		$oLicenseSet = new mofilmMovieMusicLicenseSet();
		$licenseArray = $inData["LicenseID"];
		for ( $i = 0; $i < count($licenseArray); $i++ ) {
			$oMovie->getLicenseSet()->setObject(mofilmUserMusicLicense::getInstance($licenseArray[$i]));
		}

		$oMovie->save();

		$oMovie->setContributorInputData($inData, $oMovie, $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue());		
		$oMovie->save();
		
		$inData['Tags'][] = mofilmTag::getInstanceByTagAndType(mofilmEvent::getInstance($inData['EventID'])->getName(), mofilmTag::TYPE_CATEGORY)->getID();
		$sourceTagID = mofilmTag::getInstanceByTagAndType(mofilmSource::getInstance($inData['sourceID'])->getName(), mofilmTag::TYPE_CATEGORY)->getID();
					
		if (!(in_array($sourceTagID, $inData['Tags']))) {
			$inData['Tags'][] = $sourceTagID;
		}
					
		$inData['Tags'][] = mofilmTag::getInstanceByTagAndType(date('Y', strtotime($oMovie->getUploadDate())), mofilmTag::TYPE_CATEGORY)->getID();

		if ( is_array($inData['Tags']) ) {
			$oMovie->getTagSet()->reset();
			
			foreach ( $inData['Tags'] as $tagID ) {
				$oMovie->getTagSet()->setObject(mofilmTag::getInstance($tagID));
			}
		}
		$oMovie->save();
				
		//$this->saveMovieTags($oMovie->getID(), $inData["Title"]); 
		
		$this->linkUserMovieGrants($inData["sourceID"], $inUserID, $oMovie->getID());

		$oUploadQueue = new mofilmUploadQueue();
		$oUploadQueue->setMovieID($oMovie->getID());
		$oUploadQueue->setUserID($oMovie->getUserID());
		$oUploadQueue->setStatus(mofilmUploadQueue::STATUS_QUEUED);
		$oUploadQueue->save();


		$this->sendEmail($inUserID, $oMovie->getID(),$oMovie->getSource()->getEvent()->getName(),$oMovie->getSource()->getName());
		return $oMovie->getID();
	}
	
	/**
	 * Saves the photo and its assoiated records
	 * 
	 * @param array $inData
	 * @param integer $inUserID 
	 */
	function savePhoto($inData, $inUserID) {

		$oUserTerms = new mofilmUserTerms();
		$oUserTerms->setUserID($inUserID);
		$oUserTerms->setTermsID(0);
		$oUserTerms->save();

		$oMovie = new mofilmMovie();
		$oMovie->setUserID($inUserID);
		$oMovie->setLongDesc($inData["Description"]);
		$oMovie->setShortDesc($inData["Title"]);
		$oMovie->setCredits("No credit");
		$oMovie->setActive(mofilmMovie::ACTIVE_Y);
		$oMovie->setStatus(mofilmMovie::STATUS_PENDING);
		$oMovie->save();
		
		$oMovie->getSourceSet()->setObject(mofilmSource::getInstance($inData["sourceID"]));

		$oMovie->getDataSet()->setMovieID($oMovie->getID());
		$oMovie->getDataSet()->setProperty(mofilmDataname::DATA_USER_IP, $_SERVER["REMOTE_ADDR"]);
		$oMovie->getDataSet()->setProperty(mofilmDataname::DATA_USER_COUNTRY_CODE, $oMovie->getDataSet()->getUserCountryCode());
		$oMovie->getDataSet()->setProperty(mofilmDataname::DATA_MOVIE_LICENSEID, 'Photo Upload');
		$oMovie->save();

		$oUploadQueue = new mofilmUploadQueue();
		$oUploadQueue->setMovieID($oMovie->getID());
		$oUploadQueue->setUserID($oMovie->getUserID());
		$oUploadQueue->setStatus(mofilmUploadQueue::STATUS_SENT);
		$oUploadQueue->save();
		
		$oUploadStatus = new mofilmUploadStatus();
		$oUploadStatus->setMovieID($oMovie->getID());
		$oUploadStatus->setVideoCloudID(0);
		$oUploadStatus->setStatus(mofilmUploadStatus::STATUS_SUCCESS);
		$oUploadStatus->setUpdateDate(date('Y-m-d H:i:s'));
		$oUploadStatus->save();

		$this->sendEmailPhoto($inUserID, $oMovie->getID(),$oMovie->getSource()->getEvent()->getName(),$oMovie->getSource()->getName());
		return $oMovie->getID();
	}	

	/**
	 * Gets the event
	 * 
	 * @return mofilmEvent
	 */
	function getEvent() {
		return mofilmEvent::getInstance($this->getEventID());
	}
	
	/**
	 * Gets the source for a event
	 * 
	 * @return mofilmEventSource
	 */
	function getSource() {
		return mofilmSource::getInstance($this->getSourceID());
	}
	
	/**
	 * Gets the list of sources for a event
	 * 
	 * @return mofilmEventSourceSet 
	 */
	function getEventSources() {
		return mofilmEvent::getInstance($this->getEventID())->getSourceSet();
	}

	/**
	 * Sends the mail when a video is uploaded
	 * 
	 * @param integer $inUserID
	 * @param integer $inMovieID
	 * @param string $inEvent
	 * @param string $inBrand
	 * @return boolean
	 */
	function sendEmailPhoto($inUserID, $inMovieID, $inEvent, $inBrand) {
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
				0, mofilmMessages::MSG_GRP_MOPHOTO, $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue()
		);
		
		$oUser = mofilmUserManager::getInstanceByID($inUserID);
		commsOutboundManager::setCustomerInMessageStack($oQueue, $inUserID);
		commsOutboundManager::setRecipientInMessageStack($oQueue, $oUser->getUsername());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%MOVIE_ID%','%EVENT_NAME%','%BRAND_NAME%', '%mofilm.username%'), array($inMovieID,$inEvent,$inBrand,$oUser->getFullname()));
		return $oQueue->send();
	}
        
        
	/**
	 * Sends the mail when a video is uploaded
	 * 
	 * @param integer $inUserID
	 * @param integer $inMovieID
	 * @param string $inEvent
	 * @param string $inBrand
	 * @return boolean
	 */
	function sendEmail($inUserID, $inMovieID, $inEvent, $inBrand) {
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
				0, mofilmMessages::MSG_GRP_USR_UPLOADED_VIDEO, $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue()
		);
		
		$oUser = mofilmUserManager::getInstanceByID($inUserID);
		commsOutboundManager::setCustomerInMessageStack($oQueue, $inUserID);
		commsOutboundManager::setRecipientInMessageStack($oQueue, $oUser->getUsername());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%MOVIE_ID%','%EVENT_NAME%','%BRAND_NAME%', '%mofilm.username%'), array($inMovieID,$inEvent,$inBrand,$oUser->getFullname()));
		return $oQueue->send();
	}

	/**
	 * Search tag for autocompleteSearch
	 * 
	 * @return array
	 */
	function autocompleteSearchTag() {
		$oTag = new mofilmTag();
		$oResult = $oTag->searchAutocompleteTag($this->getAutocompleteSearchTag());
		return $oResult;
	}
	
	/**
	 * Saves user entered movie tags and auto generated categories
	 * 
	 * @return boolean
	 */
	function saveMovieTags($inMovieID, $inTitle=NULL) {
		try {
			$subject = $this->getMovieTag();
			$search = ", ";
			$replace = ",";

			$string = str_replace($search, $replace, $subject);

			$tags = explode(",", $string);
			$tags[] = mofilmEvent::getInstance($this->getEventID())->getName();
			$tags[] = mofilmSource::getInstance($this->getSourceID())->getName();
			$tags[] = date("Y");
			$tags[] = $inTitle;
			$unique_tags = $this->array_iunique($tags);
			foreach ( $unique_tags as $tag ) {
				if ( trim($tag) ) {
					$oTagID = mofilmTag::getInstanceByTag(trim($tag))->getID();
					if ( $oTagID == 0 ) {
						$oTag = new mofilmTag();
						$oTag->setName(substr(trim($tag),0,40));
						$oTag->setType(mofilmTag::TYPE_TAG);
						$oTag->save();

						$oTagID = mofilmTag::getInstanceByTag(trim($tag))->getID();
					}

					if ( $oTagID ) {
						$oMovieTagSet[] = mofilmTag::getInstance($oTagID);
					}
				}
			}

			$oMovieTag = new mofilmMovieTagSet($inMovieID);
			$oMovieTag->setObjects($oMovieTagSet);
			$oMovieTag->save();
		} catch (Exception $e) {
			systemLog::message($e);
			systemLog::message($tags);
		}
		return true;
	}
	
	function array_iunique($array) {
		return array_intersect_key($array,array_unique(array_map(strtolower,$array)));
	}
	
	/**
	 * Links the uploading movie to grants if the user has an approved grants for that event brand combination
	 * 
	 * @return boolean
	 */
	function linkUserMovieGrants($inSourceID = null, $inUserID = null, $inMovieID = null) {
		if ( $inSourceID && $inUserID && $inMovieID ) {
			$oUserMovieGrans = mofilmUserMovieGrants::userMovieGrantsObject($inUserID, $inSourceID, mofilmUserMovieGrants::STATUS_APPROVED);
			systemLog::message($oUserMovieGrans);
			if ( $oUserMovieGrans instanceof mofilmUserMovieGrants ) {
				$oUserMovieGrans->setMovieID($inMovieID);
				$oUserMovieGrans->save();
			}
		}
		return true;
	}
}
