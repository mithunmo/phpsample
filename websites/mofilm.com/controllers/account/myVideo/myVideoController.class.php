<?php
/**
 * myVideoController
 *
 * Stored in myVideoController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category myVideoController
 * @version $Rev: 736 $
 */


/**
 * myVideoController
 *
 * myVideoController class
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category myVideoController
 */
class myVideoController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_TOTAL_ENCODED_VIDEOS = "totalEncodedVideo";
	const ACTION_USEREDIT = "edit";
	const ACTION_USERDOEDIT = "doEdit";
	
	
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(true);

		$this->getControllerActions()->addAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_TOTAL_ENCODED_VIDEOS);
		$this->getControllerActions()->addAction(self::ACTION_USEREDIT);
		$this->getControllerActions()->addAction(self::ACTION_USERDOEDIT);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		if ( $this->getAction() == self::ACTION_VIEW ) {
			$this->getModel()->setUserID($this->getRequest()->getSession()->getUser()->getID());
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$data = $this->getInputManager()->doFilter();	
			$this->addInputToModel($data, $this->getModel());
			$oView = new myVideoView($this);
			$oView->showMyVideoPage();
		} elseif ( $this->getAction() == self::ACTION_TOTAL_ENCODED_VIDEOS ) {
			
			if ( $this->getRequest()->getSession()->getUser()->getAutoCommitStatus() == mofilmUserBase::AUTO_COMMIT_STATUS_DISABLED ) {
				$this->getModel()->setUserID($this->getRequest()->getSession()->getUser()->getID());
				$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
				$data = $this->getInputManager()->doFilter();	
				$this->addInputToModel($data, $this->getModel());
				$oView = new myVideoView($this);
				$oView->showTotalEncodedVideoPage();
			}
			
		} elseif ( $this->getAction() == self::ACTION_USEREDIT ) {
			$oUser =  $this->getRequest()->getSession()->getUser();
			if ( $oUser->getFirstname()!=null && $oUser->getSurname()!=null && $oUser->getProfile()->getActive()!= mofilmUserProfile::PROFILE_DISABLED ) {
			
				$movieID = (int) $this->getActionFromRequest(false, 1);
				$this->getModel()->setMovieID($movieID);
				if ( is_int($movieID) && $this->getModel()->getMovie() && $this->getModel()->getMovie()->getUserID() == $this->getRequest()->getSession()->getUser()->getID() ) {
					$this->getModel()->setMovieID($movieID);	
					$oView = new myVideoView($this);
					$oView->showEditVideoPage();
				} else {	
					throw new mvcDistributorInvalidRequestException(sprintf('Unhandled action specified by requestor'));				
				}	
			} else {
					$oView = new uploadView($this);
					$oView->showUploadCompletePage();			
			}		
		} elseif ( $this->getAction() == self::ACTION_USERDOEDIT ) {
			$this->addInputFilters();
			$data = $this->getInputManager()->doFilter();
			
			if ( isset ($_FILES['ccaFile']['name']) && $_FILES['ccaFile']['error'] == 0 ) {
				$this->uploadCcaFile($data['MovieID']);
			}

			//$this->addInputToModel($data, $this->getModel());
			$movieID = $this->getModel()->saveMovie($data, $this->getRequest()->getSession()->getUser()->getID());
			$this->getRequest()->getSession()->setStatusMessage('Invalid or missing movieID. You m', mvcSession::MESSAGE_OK );			
			$this->redirect("/account/myVideo/edit/".$data["MovieID"]);	
			
		}
	}
	
	/**
	 * 
	 * Uploads CCa Files and saves the path in movieAsset table
	 * 
	 */
	function uploadCcaFile($inMovieID=Null) {
		$oFileUpload = new mvcFileUpload(
			array(
				mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
				mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
				mvcFileUpload::OPTION_FIELD_NAME => 'ccaFile',
				mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
				mvcFileUpload::OPTION_WRITE_IMMEDIATE => false,
				mvcFileUpload::OPTION_STORE_RAW_DATA => true,
			)
		);

		try {
			$oFileUpload->initialise();
			$oFiles = $oFileUpload->process();
			
			$oFile = $oFiles->getFirst();
			if ( $oFile instanceof mvcFileObject ) {
			    
				$inUserID = $this->getRequest()->getSession()->getUser()->getID();
				
				systemLog::message('Uploading File for '.$oFile->getUploadKey());
				$path = mofilmConstants::getCcaDocsFolder().$inUserID.system::getDirSeparator().$inMovieID.system::getDirSeparator();
				$finalPath = $path.$oFile->getName();
				if ( !file_exists($path) ) {
					mkdir($path, 0777, TRUE);
				}
				$bytes = file_put_contents($finalPath, $oFile->getRawFileData());
				systemLog::notice("Wrote $bytes bytes to the file system for CCA Documents ".$oFile->getName());
				
				$oMovie = mofilmMovieManager::getInstanceByID($inMovieID);
				$oMovieAsset = $oMovie->getAssetSet()->getObjectByAssetType(mofilmMovieAsset::TYPE_CCA)->getFirst();

				if ( $oMovieAsset instanceof mofilmMovieAsset && $oMovieAsset->getID() > 0 ) {
					$oMovieAsset->setFilename($finalPath);
					$oMovieAsset->setExt(pathinfo($finalPath, PATHINFO_EXTENSION));
					
				} else {
					systemLog::message('asset add');
					unset ($oMovieAsset);
					$oMovieAsset = new mofilmMovieAsset();
					$oMovieAsset->setMovieID($inMovieID);
					$oMovieAsset->setType(mofilmMovieAsset::TYPE_CCA);
					$oMovieAsset->setFilename($finalPath);
					$oMovieAsset->setExt(pathinfo($finalPath, PATHINFO_EXTENSION));
					$oMovieAsset->setDescription('Cca File');
				}

				$oMovie->getAssetSet()->setObject($oMovieAsset);
				$oMovie->save();
				return true;
			}			
		} catch ( mvcFileUploadNoFileUploadedException $e ) {
			systemLog::warning($e->getMessage());
			return null;
		} catch ( mvcFileUploadException $e ) {
			systemLog::warning($e->getMessage());
			return null;
		}
	}
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('Offset', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Limit', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('MovieID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Contributors', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('Tags', utilityInputFilter::filterStringArray());
		
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		if ( !$inData['Limit'] || $inData['Limit'] > 30 ) {
			$inData['Limit'] = 20;
		}
		if ( !$inData['Offset'] || $inData['Offset'] < 0 ) {
			$inData['Offset'] = 0;
		}
		
		$inModel->getVideoSearch()->setOffset($inData['Offset']);
		$inModel->getVideoSearch()->setLimit($inData['Limit']);
		
	}
	
	/**
	 * Fetches the model
	 *
	 * @return myVideoModel
	 */
	function getModel() {
		if ( !parent::getModel() ) {
			$this->buildModel();
		}
		return parent::getModel();
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new myVideoModel();
		$oModel->setRequest($this->getRequest());
		$this->setModel($oModel);
	}
}