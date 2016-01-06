<?php
/**
 * uploadFilesModel.class.php
 * 
 * uploadFilesModel class
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category uploadFilesModel
 * @version $Rev: 1 $
 */


/**
 * uploadFilesModel class
 * 
 * Provides the "uploadFiles" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category uploadFilesModel
 */
class uploadFilesModel extends mofilmUploadedFiles {
    
        /**
	 * Stores an instance of mofilmUploadedFilesSearch
	 * 
	 * @var mofilmUploadedFilesSearch
	 * @access protected
	 */
	protected $_UploadedFilesSearch;
	
    	/**
	 * Stores an instance of mofilmUploadedFilesSearchResult
	 * 
	 * @var mofilmUploadedFilesSearchResult
	 * @access protected
	 */
	protected $_UploadedFilesSearchResult;
    
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		return mofilmUploadedFiles::listOfObjects($inOffset, $inLimit);
	}
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects() {
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.uploadedFiles';
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		$total = $this->getTotalObjects();
		
		if ( $inLimit > 0 ) {
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
	}


	/**
	 * Returns a new blank object
	 *
	 * @return systemDaoInterface
	 */
	function getNewObject() {
		return new mofilmUploadedFiles();
	}
	
	/**
	 * Creates an returns a mofilmUploadedFilesSearch object
	 * 
	 * @return mofilmUploadedFilesSearch
	 */
	function getUploadedFilesSearch() {
		if ( !$this->_UploadedFilesSearch instanceof mofilmUploadedFilesSearch ) {
			$this->_UploadedFilesSearch = new mofilmUploadedFilesSearch();
		}
		return $this->_UploadedFilesSearch;
	}
	
	/**
	 * Returns the search result object, or null if no search has been run
	 * 
	 * @return mofilmUploadedFilesSearchResult
	 */
	function getSearchResult() {
		return $this->_UploadedFilesSearchResult;
	}
	
	/**
	 * Runs the search with provided parameters
	 * 
	 * @return mofilmUploadedFilesSearchResult
	 */
	function doSearch() {
		$this->_UploadedFilesSearchResult = $this->getUploadedFilesSearch()->search();
		return $this->_UploadedFilesSearchResult;
	}
	
	/**
	 * 
	 * @param type $inData array
	 */
	function adminModeration($inData=null) {
		try {
			if ( is_array($inData) ) {
				if ( $inData['Status'] == 'ReSend' ) {
					$oObject = new mofilmUploadedFiles($inData['fileID']);
					$this->sendApprovalEmail($oObject->getUserID(), $oObject->getSourceID(), $oObject->getPreferredLanguage());
					return "Email Re-Sent";
				} else {
					if ( $inData['Status'] == 'Approve' ) {
						$status = mofilmUploadedFiles::STATUS_APPROVED;
					} elseif ( $inData['Status'] == 'Reject' ) {
						$status = mofilmUploadedFiles::STATUS_REJECTED;
					}

					$oObject = new mofilmUploadedFiles($inData['fileID']);
					$oObject->setModeratorID($inData['userID']);
					$oObject->setModerated(new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue()));
					$oObject->setStatus($status);
					$ret = $oObject->save();
					if ( $ret ) {
						if ( $status == mofilmUploadedFiles::STATUS_APPROVED ) {
							$this->sendApprovalEmail($oObject->getUserID(), $oObject->getSourceID(), $oObject->getPreferredLanguage());
							return "Approved and Email Sent";
						} elseif ( $status == mofilmUploadedFiles::STATUS_REJECTED ) {
							$this->sendRejectedEmail($oObject->getUserID(), $oObject->getSourceID(), $oObject->getPreferredLanguage());
							return "Rejected and Email Sent";
						}

					} else {
						return false;
					}
				}
			}
		} catch (Exception $e) {
			systemLog::message($e);
		}
		return false;
	}

	/**
	 * 
	 * @param type $inData array
	 */
	function saveUploadedFileDetails($data=null) {
		try {
			$this->setUserID($data['userID']);
			$this->setSourceID($data['SourceID']);
			$this->setFileName($data['fileName']);
			$this->setUploadType(mofilmUploadedFiles::UPLOADTYPE_NDA);
			$this->setStatus(mofilmUploadedFiles::STATUS_PENDING);
			$this->setPreferredLanguage($data['preferredLanguage']);
			$return = $this->save();
			if ( $return ) {
				return true;
			}
		} catch (Exception $e) {
			systemLog::message($e);
		}
		return false;
	}
	
	/**
	 * 
	 * Sends an automated email to user after admin approves the NDA document
	 */
	function sendApprovalEmail($inUserID, $inSourceID, $inLanguage="en") {
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_UPLOADED_FILES_APPROVED, $inLanguage
		);

		$oUser = mofilmUserManager::getInstanceByID($inUserID);
		$oObject = mofilmSource::getInstance($inSourceID);
		
		$oDownloadFiles = mofilmDownloadFile::listOfObjects(NULL, NULL, $inSourceID, mofilmDownloadFile::FILETYPE_BRIEF);
		foreach ($oDownloadFiles as $oDownloadFile) {
		    $oSourceSets = $oDownloadFile->getSourceSet()->getIterator()->getArrayCopy();
		    foreach ($oSourceSets as $oSourceSet) {
			    $inDownloadHash = $oSourceSet->getDownloadHash();
		    }
		}
		
		if ( $inLanguage == "zh" ) {
			$downloadLink = "http://my.mofilm.cn/dl/".$inDownloadHash;
		} else {
			$downloadLink = "http://mofilm.com/brief/".$inDownloadHash;
		}

		commsOutboundManager::setCustomerInMessageStack($oQueue, $oUser->getID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $oUser->getEmail());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%mofilm.username%', '%mofilm.brand%', '%mofilm.event%', '%mofilm.downloadlink%'), array($oUser->getFullname(), $oObject->getName(), $oObject->getEvent()->getName(), $downloadLink));
		return $oQueue->send();
	}
	
	/**
	 * 
	 * Sends an automated email to user after admin rejects the NDA document
	 */
	function sendRejectedEmail($inUserID, $inSourceID, $inLanguage="en") {
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_UPLOADED_FILES_REJECTED, $inLanguage
		);

		$oUser = mofilmUserManager::getInstanceByID($inUserID);
		$oObject = mofilmSource::getInstance($inSourceID);

		commsOutboundManager::setCustomerInMessageStack($oQueue, $oUser->getID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $oUser->getEmail());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%mofilm.username%', '%mofilm.brand%', '%mofilm.event%'), array($oUser->getFullname(), $oObject->getName(), $oObject->getEvent()->getName()));
		return $oQueue->send();
	}	
}