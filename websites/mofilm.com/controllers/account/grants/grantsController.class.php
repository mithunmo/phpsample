<?php
/**
 * grantsController
 *
 * Stored in grantsController.class.php
 * 
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category grantsController
 * @version $Rev: 835 $
 */


/**
 * grantsController
 *
 * grantsController class
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category grantsController
 */
class grantsController extends mvcController {
	
	const ACTION_LIST = 'grantsList';
	const ACTION_APPLY = 'apply';
	const ACTION_DO_APPLY = 'doApply';
	const ACTION_VIEW = 'view';
	const ACTION_EDIT = 'edit';
	const ACTION_DO_EDIT = 'doEdit';
	const ACTION_DO_DOCS_UPLOAD = 'doDocsUpload';


	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();

		$this->setDefaultAction(self::ACTION_LIST);
		$this->setRequiresAuthentication(true);
		$this->getControllerActions()
				->addAction(self::ACTION_LIST)
				->addAction(self::ACTION_APPLY)
				->addAction(self::ACTION_DO_APPLY)
				->addAction(self::ACTION_VIEW)
				->addAction(self::ACTION_EDIT)
				->addAction(self::ACTION_DO_EDIT)
				->addAction(self::ACTION_DO_DOCS_UPLOAD);
		
		$this->addInputFilters();
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_APPLY: $this->applyForGrants();   break;
			case self::ACTION_DO_EDIT: $this->doSaveApplyForm();   break;
			case self::ACTION_DO_APPLY: $this->doSaveApplyForm();   break;
			case self::ACTION_VIEW: $this->viewGrant();	break;
			case self::ACTION_EDIT: $this->editGrant();	break;
			case self::ACTION_DO_DOCS_UPLOAD: $this->uploadDocs();	break;
			default:
				$this->listGrants();
			break;
		}
	}
	
	/**
	 * 
	 */
	function listGrants() {
		$this->getModel()->setUserID($this->getRequest()->getSession()->getUser()->getID());
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
			
		if ( !$data['Offset'] || $data['Offset'] < 0 ) {
			$data['Offset'] = 0;
		}

		$this->getModel()->setOffset($data['Offset']);
		
		$oView = new grantsView($this);
		$oView->getObjectListView();
	}
	
	/**
	 * 
	 */
	protected function doSaveApplyForm( ) {
		try {
			$this->addInputToModel($this->getInputManager()->doFilter(), $this->getModel());
			$this->getModel()->save();
			if ($this->getAction() == self::ACTION_DO_APPLY) {
				$this->getModel()->sendGrantsReciptEmail();
			} elseif ($this->getAction() == self::ACTION_DO_EDIT) {
				$this->getModel()->sendGrantsResubmitEmail();
			}
			
			if ( isset ($_FILES['GrantFile']['name']['ApplicationAssets']) && $_FILES['GrantFile']['error']['ApplicationAssets'] != 4 ) {
				$this->uploadDocs($this->getModel()->getID());
			}
			
		} catch (Exception $error){
			throw new mofilmException('Your request could not be processed');
		}
		$this->redirect($this->buildUriPath(self::ACTION_LIST));
	}

	/**
	 * 
	 */
	function applyForGrants() {
		$sourceID = (int) $this->getActionFromRequest(false, 1);

		$oView = new grantsView($this);
		$oView->showApplyForGrants($sourceID, $this->getRequest()->getSession()->getUser()->getID());
	}

	/*
	 * 
	 */
	function viewGrant() {
		$inUserID = $this->getRequest()->getSession()->getUser()->getID();
		$inGrantID = (int) $this->getActionFromRequest(false, 1);
		
		$oView = new grantsView($this);
		$oView->grantView($inGrantID, $inUserID);
	}
	
	/*
	 * 
	 */
	function editGrant() {
		$inUserID = $this->getRequest()->getSession()->getUser()->getID();
		$inGrantID = (int) $this->getActionFromRequest(false, 1);
		
		$oView = new grantsView($this);
		$oView->grantEdit($inGrantID, $inUserID);
	}
	
	/*
	 * 
	 */
	function uploadDocs($inUserMovieGrantID=null) {
		$this->addInputFilters();
		$data = $this->getInputManager()->doFilter();
		
		if ( isset ($data['UserMovieGrantID']) ) {
			$inUserMovieGrantID = $data['UserMovieGrantID'];
		}

		$inUserID = $this->getRequest()->getSession()->getUser()->getID();
		$oFileUpload = new mvcFileUpload(
			array(
				mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
				mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
				mvcFileUpload::OPTION_FIELD_NAME => 'GrantFile',
				mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
				mvcFileUpload::OPTION_WRITE_IMMEDIATE => false,
				mvcFileUpload::OPTION_STORE_RAW_DATA => true,
			)
		);

		try {
			$oFileUpload->initialise();
			$oFileUpload->process();
		} catch ( mvcFileUploadNoFileUploadedException $e ) {
			systemLog::warning($e->getMessage());
			return null;
		} catch ( mvcFileUploadException $e ) {
			systemLog::warning($e->getMessage());
			if ( $oFileUpload->getUploadedFiles()->getCount() == 0 ) {
				$return = null;
			} else {
				$return = false;
			}
		}
		
		$oFiles = $oFileUpload->getUploadedFiles();
		if ( $oFiles->getCount() > 0 ) {
			/* @var mvcFileObject $oFile */
			foreach ( $oFiles as $oFile ) {
				systemLog::message('Uploading File for '.$oFile->getUploadKey());
				$path = mofilmConstants::getGrantDocsFolder().$inUserID.system::getDirSeparator().$inUserMovieGrantID.system::getDirSeparator().$oFile->getUploadKey().system::getDirSeparator();
				$finalPath = $path.$oFile->getName();
				if ( !file_exists($path) ) {
					mkdir($path, 0777, TRUE);
				}
				$bytes = file_put_contents($finalPath, $oFile->getRawFileData());
				systemLog::notice("Wrote $bytes bytes to the file system for grantDocs ".$oFile->getName());
				
				if ( $oFile->getUploadKey() == 'UploadGrantApprovalForm' ) {
					$UploadGrantApprovalForm = $finalPath;
				} elseif ( $oFile->getUploadKey() == 'UploadBankDetails' ) {
					$UploadBankDetails = $finalPath;
				} elseif ( $oFile->getUploadKey() == 'UploadPhotoIDProof' ) {
					$UploadPhotoIDProof = $finalPath;
				} elseif ( $oFile->getUploadKey() == 'UploadReceipts' ) {
					$UploadReceipts = $finalPath;
				} elseif ( $oFile->getUploadKey() == 'ApplicationAssets' ) {
					$ApplicationAssets = $finalPath;
				}
			}
		}
		
		$oUserMovieGrants = new mofilmUserMovieGrants($inUserMovieGrantID);
		if ( isset ($UploadGrantApprovalForm) ) {
			$oUserMovieGrants->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOCUMENT_AGREEMENT_PATH, $UploadGrantApprovalForm);
		}
		if ( isset ($UploadBankDetails) ) {
			$oUserMovieGrants->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOCUMENT_BANK_DETAILS_PATH, $UploadBankDetails);
		}
		if ( isset ($UploadPhotoIDProof) ) {
			$oUserMovieGrants->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOCUMENT_IDPROOF_PATH, $UploadPhotoIDProof);
		}
		if ( isset ($UploadReceipts) ) {
			$oUserMovieGrants->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOCUMENT_RECEIPTS_PATH, $UploadReceipts);
		}
		if ( isset ($ApplicationAssets) ) {
			$oUserMovieGrants->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_GRANT_ASSETS_PATH, $ApplicationAssets);
		}
		
		$oUserMovieGrants->getParamSet()->save();
		
		$this->getRequest()->getSession()->setStatusMessage('Files Uploaded', mvcSession::MESSAGE_OK);
		$this->redirect(self::ACTION_VIEW.'/'.$inUserMovieGrantID);
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('UserMovieGrantID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('GrantID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('FilmConcept', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('FilmTitle', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Duration', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('UsageOfGrants', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ShowReelURL', utilityInputFilter::filterString());
		//$this->getInputManager()->addFilter('RequestedAmount', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Script', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Offset', utilityInputFilter::filterString());
		
		$this->getInputManager()->addFilter('ScriptWriterAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('ProducerAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('DirectorAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('TalentAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('DoPAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('EditorAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('TalentExpensesAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('ProductionStaffAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('PropsAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('SpecialEffectsAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('WardrobeAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('HairMakeUpAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('CameraRentalAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('SoundAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('LightingAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('TransportationAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('CrewExpensesAmount', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('LocationAmount', utilityInputFilter::filterfloat());
		$this->getInputManager()->addFilter('OthersAmount', utilityInputFilter::filterFloat());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {

		if ( $this->getAction() == self::ACTION_DO_EDIT ) {
			$inModel->setID($inData['UserMovieGrantID']);
			$inModel->setCreated(mofilmUserMovieGrants::getInstance($inData['UserMovieGrantID'])->getCreated());
		}
		
		$inModel->setUserID($this->getRequest()->getSession()->getUser()->getID());
		$inModel->setGrantID($inData['GrantID']);
		$inModel->setFilmConcept(trim($inData['FilmConcept']));
		$inModel->setFilmTitle(trim($inData['FilmTitle']));
		$inModel->setDuration($inData['Duration']);
		$inModel->setUsageOfGrants(trim($inData['UsageOfGrants']));
		$inModel->setScript(trim($inData['Script']));
		$inModel->setStatus(mofilmUserMovieGrants::STATUS_PENDING);

		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_SCRIPT_WRITER, (float)$inData['ScriptWriterAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_PRODUCER, (float)$inData['ProducerAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DIRECTOR, (float)$inData['DirectorAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_TALENT, (float)$inData['TalentAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOP, (float)$inData['DoPAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_EDITOR, (float)$inData['EditorAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_TALENT_EXPENSES, (float)$inData['TalentExpensesAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_PRODUCTION_STAFF, (float)$inData['ProductionStaffAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_PROPS, (float)$inData['PropsAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_SPECIAL_EFFECTS, (float)$inData['SpecialEffectsAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_WARDROBE, (float)$inData['WardrobeAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_HAIR_MAKEUP, (float)$inData['HairMakeUpAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_CAMERA_RENTAL, (float)$inData['CameraRentalAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_SOUND, (float)$inData['SoundAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_LIGHTING, (float)$inData['LightingAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_TRANSPORTATION, (float)$inData['TransportationAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_CREW_EXPENSES, (float)$inData['CrewExpensesAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_LOCATION, (float)$inData['LocationAmount']);
		$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_OTHERS, (float)$inData['OthersAmount']);
		
		$requestedAmount = 0;
		$requestedAmount = (float)$inData['ScriptWriterAmount'] + (float)$inData['ProducerAmount'] + (float)$inData['DirectorAmount'] +
				   (float)$inData['TalentAmount'] + (float)$inData['DoPAmount'] + (float)$inData['EditorAmount'] +
				   (float)$inData['TalentExpensesAmount'] + (float)$inData['ProductionStaffAmount'] + (float)$inData['PropsAmount'] +
				   (float)$inData['SpecialEffectsAmount'] + (float)$inData['WardrobeAmount'] + (float)$inData['HairMakeUpAmount'] +
				   (float)$inData['CameraRentalAmount'] + (float)$inData['SoundAmount'] + (float)$inData['LightingAmount'] +
				   (float)$inData['TransportationAmount'] + (float)$inData['CrewExpensesAmount'] + (float)$inData['LocationAmount'] +
				   (float)$inData['OthersAmount'];
		
		$inModel->setRequestedAmount((float)$requestedAmount);
		$inModel->setHash(mofilmUtilities::buildMiniHash(date('now'), 6));
		
		if ( isset ($inData['ShowReelURL']) && $inData['ShowReelURL'] != 'N/A') {
			$inModel->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_SHOWREELURL, trim($inData['ShowReelURL']));
			$inModel->saveShowReel(trim($inData['ShowReelURL']));
		}
	}

	/**
	 * Fetches the model
	 *
	 * @return profileModel
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
		$oModel = new grantsModel();
		$this->setModel($oModel);
	}

}