<?php
/**
 * sourceManagerController
 *
 * Stored in sourceManagerController.class.php
 * 
 * @author Mithun MOhan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sourceManagerController
 * @version $Rev: 220 $
 */


/**
 * sourceManagerController
 *
 * sourceManagerController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sourceManagerController
 */
class sourceManagerController extends mvcDaoController {
	
	const ACTION_DO_AJAX_DELETE_PRIZE = 'doAjaxDeletePrize';
	const ACTION_UPLOAD_SOURCE_LOGO ='doUploadSourceLogo';
	const ACTION_UPLOAD_SOURCE_BANNER ='doUploadSourceBanner';
	const ACTION_UPLOAD_SOURCE_FILLER ='doUploadSourceFiller';
        
        public $_Corporate = '';

	/**
	 * @see mvcControllerBase::initialise()		echo 'pranky';
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('sourceManagerView');    
                systemLog::message("id".$this->getSearchParameter("EventID"));


                if (isset($_GET["EventID"])){
                    $path = $this->buildUriPath(self::ACTION_NEW)."?eventID=".$_GET["EventID"];
                } else {
                    $path =  $this->buildUriPath(self::ACTION_NEW);
                }
                
                $this->getMenuItems()->getItem(self::ACTION_VIEW)->reset();
                //$oMenuItem->reset();

		$this->getMenuItems()->getItem(self::ACTION_VIEW)->addItem(
			new mvcControllerMenuItem(
				$path, 'Add Brand to Project', self::IMAGE_ACTION_NEW, 'Create a new record', false, mvcControllerMenuItem::PATH_TYPE_URI
			)
		);
                
		$this->getMenuItems()->getItem(self::ACTION_VIEW)->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_SEARCH), 'Search', self::IMAGE_ACTION_SEARCH, 'Search', false, mvcControllerMenuItem::PATH_TYPE_URI, true
			)
		);
		$this->getControllerActions()
			->addAction(self::ACTION_DO_AJAX_DELETE_PRIZE)
			->addAction(self::ACTION_UPLOAD_SOURCE_LOGO)
			->addAction(self::ACTION_UPLOAD_SOURCE_BANNER)
			->addAction(self::ACTION_UPLOAD_SOURCE_FILLER);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		parent::launch();
		switch ( $this->getAction() ) {
			case self::ACTION_DO_AJAX_DELETE_PRIZE: $this->doDeletePrize();   break;
			case self::ACTION_UPLOAD_SOURCE_LOGO: $this->actionUploadSourceLogo();   break;
			case self::ACTION_UPLOAD_SOURCE_BANNER: $this->actionUploadSourceBanner();   break;
			case self::ACTION_UPLOAD_SOURCE_FILLER: $this->actionUploadSourceFiller();;   break;
		}
	}
        
        function actionDoNew() {
            $data = $this->getInputManager()->doFilter();
            
            if(!isset($data['Custom']))
                $data['Custom'] = "N";
            $this->addInputToModel($data, $this->getModel());
            $this->getModel()->save();
            
            
            $inSourceID = $this->getModel()->getID();
            $oSourceData = mofilmSource::getInstance($inSourceID);
            $grantsDeadline = $oSourceData->getEndDate();
            $inEventID = $oSourceData->getEventID();
            
            $ProdEvent = mofilmEvent::getInstance($inEventID);
            $PID = $ProdEvent->getProductID();
            
            if($grantsDeadline === NULL)
            {
                $oEventData = mofilmEvent::getInstance($inEventID);
                $grantsDeadline = $oEventData->getEndDate();
            }
            
            
            $varFloat = '0.00';
            if($data['GrantsDescription'])
                $desc = $data['GrantsDescription'];
            else
                $desc = "No grants Available";
            
            if($PID == "3") {
            $oMofilmGrant = new mofilmGrants();
            $oMofilmGrant->setSourceID($inSourceID);
            $oMofilmGrant->setEndDate($grantsDeadline);
            $oMofilmGrant->setCurrencySymbol("$");
            $oMofilmGrant->setTotalGrants(floatval($varFloat));
            $oMofilmGrant->setDescription($desc);
            $oMofilmGrant->save();
            
            }
            
            $oGrantDataID = mofilmGrants::getInstanceBySourceID($inSourceID);
            $GID = $oGrantDataID->getID();
            
            if(!isset($GID)){
                $GID = $oMofilmGrant->getID();
            }
            
            if($PID == "5")
            {
                if($data["CustomGrant"] == "Y")
                {
                    unset($QuestionVal);
                    $QuestionVal = trim($data["CustomGrantQuestion"]);
                    if(strlen($QuestionVal) > 3){
                    $oGrantdatum = new grantdata();
                    $oGrantdatum->setGrantID($GID);
                    $oGrantdatum->setParamName("Question");
                    $oGrantdatum->setParamValue($QuestionVal);
                    $oGrantdatum->save();
                    }
                }
            }
            
           if($PID == "3")
            {
                if(isset($data["ideaSubmissions"]))
                {
                    unset($SubmissionVal);
                    $SubmissionVal = $data["ideaSubmissions"];
                    if(isset($SubmissionVal)){
                    $oGrantdatum = new grantdata();
                    $oGrantdatum->setGrantID($GID);
                    $oGrantdatum->setParamName("IdeaSubmission");
                    $oGrantdatum->setParamValue($SubmissionVal);
                    $oGrantdatum->save();
                    }
                }
            }
            
            if($data["AllUser"] == "on")
            {
                $oMofilmUserSourcePermission = new mofilmUserSourcePermissions();
                $oMofilmUserSourcePermission->setEventID($data['EventID']);
                $oMofilmUserSourcePermission->setSourceID($this->getModel()->getID());
                $oMofilmUserSourcePermission->setHasEvent("1");
                $oMofilmUserSourcePermission->setUserID(63064);
                $oMofilmUserSourcePermission->save();
            }
            else
            {
                if(isset($data["FM"]))
                {
                foreach ($data["FM"] as $value)
                {
                    if($value > 0 )
                    {    
                        $oMofilmUserSourcePermission = new mofilmUserSourcePermissions();
                        $oMofilmUserSourcePermission->setEventID($data['EventID']);
                        $oMofilmUserSourcePermission->setSourceID($this->getModel()->getID());
                        $oMofilmUserSourcePermission->setHasEvent("0");
                        $oMofilmUserSourcePermission->setUserID($value);
                        $oMofilmUserSourcePermission->save();
                    }
                }
                }
            }
            $this->redirect($this->buildUriPath(self::ACTION_VIEW));
        }
        
           
        
        function actionDoEdit() { 
                    try {
                        
                        $GrantsAvailability=$_POST['GrantsAvailability'];
                        if($GrantsAvailability=='N'){
                            $inGrantID = $_POST['GrantID'];
                            $grantObj=mofilmGrants::getInstance($inGrantID)->delete();
                        }
                    
                        $primaryKey = $this->getActionFromRequest(false, 1);
			$this->setPrimaryKey($primaryKey);
			$this->getExistingObject();

			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_POST);
			$data = $this->getInputManager()->doFilter();
                        
                        $oSession = $this->getRequest()->getSession();
                        $inUserID = $oSession->getUser()->getID();
                        
                        $oMofilmSourceBudget = new mofilmSourceBudget();
                        
                        $checkBudget = $oMofilmSourceBudget->checkIfBudgetExists($primaryKey);
                        if($checkBudget){
                            $oMofilmSourceBudget = mofilmSourceBudget::getInstance($checkBudget);
                            $content = "Buffer: ".$oMofilmSourceBudget->getGrantBuffer()." Other".$oMofilmSourceBudget->getOther()." TimeStamp: ".$oMofilmSourceBudget->getModifiedTime()." User".$oMofilmSourceBudget->getUserID();
                            $oMofilmSourceBudgetLog = new mofilmSourceBudgetLog();
                            $oMofilmSourceBudgetLog->setSrcID($primaryKey);
                            $oMofilmSourceBudgetLog->setChangeLog($content);
                            $oMofilmSourceBudgetLog->setModifiedTime(date("Y-m-d H:i:s"));
                            $oMofilmSourceBudgetLog->save();
                        }else{
                            $oMofilmSourceBudget = new mofilmSourceBudget();
                            $oMofilmSourceBudget->setSourceID($primaryKey);
                        }
                        $oMofilmSourceBudget->setUserID($inUserID);
                        if(isset($data['GrantBuffer']))
                            $oMofilmSourceBudget->setGrantBuffer($data['GrantBuffer']);
                        else
                            $oMofilmSourceBudget->setGrantBuffer(0);
                        if(isset($data['BudgetOther']))
                            $oMofilmSourceBudget->setOther($data['BudgetOther']);
                        else
                            $oMofilmSourceBudget->setOther(0);
                        $oMofilmSourceBudget->setModifiedTime(date("Y-m-d H:i:s"));
                        $oMofilmSourceBudget->save();
                    
                        $oSourceData = mofilmSource::getInstance($primaryKey);
                        $inEventID = $oSourceData->getEventID();
                        $inStatus  = $oSourceData->getStatus();

                        $ProdEvent = mofilmEvent::getInstance($inEventID);
                        $PID = $ProdEvent->getProductID();
                        
                        if($PID == "5")
                        {    
                            $oGrantDataID = mofilmGrants::getInstanceBySourceID($primaryKey);
                            $GID = $oGrantDataID->getID();
                            $inGrantID = $GID;
                            $oGrantData = new grantdata();
                                if(isset($inGrantID))
                                {    
                                    $grantUpdate = $oGrantData->getValue($inGrantID,"Question");
                                    if(isset($grantUpdate))
                                    $QuesID = $grantUpdate->getID();
                                    if(isset($QuesID))
                                    {
                                        $oDataGrant = grantdata::getInstance($QuesID)->delete();
                                    }
                                    if($data['CustomGrant'] == "Y")
                                    {
                                        unset($QuestionVal);
                                        $QuestionVal = trim($data["CustomGrantQuestion"]);
                                        if(strlen($QuestionVal) > 3){
                                            $oGrantdatum = new grantdata();
                                            $oGrantdatum->setGrantID($inGrantID);
                                            $oGrantdatum->setParamName("Question");
                                            $oGrantdatum->setParamValue($QuestionVal);
                                            $oGrantdatum->save();
                                        }
                                    }
                                }
                        }
                        
                        if($PID == "3")
                        {    
                            $oGrantDataID = mofilmGrants::getInstanceBySourceID($primaryKey);
                            $GID = $oGrantDataID->getID();
                            $inGrantID = $GID;
                            $oGrantData = new grantdata();
                                if(isset($inGrantID))
                                {    
                                    $grantUpdate = $oGrantData->getValue($inGrantID,"IdeaSubmission");
                                    if(isset($grantUpdate))
                                        $IdeaID = $grantUpdate->getID();
                                    if(isset($IdeaID))
                                    {
                                        $oDataGrant = grantdata::getInstance($IdeaID)->delete();
                                    }
                                    if(isset($data['ideaSubmissions']))
                                    {
                                        unset($SubmissionVal);
                                            $SubmissionVal = $data["ideaSubmissions"];
                                        systemLog::message($GID."productiD".$SubmissionVal);
                                        if(isset($SubmissionVal)){
                                        $oGrantda = new grantdata();
                                        $oGrantda->setGrantID($GID);
                                        $oGrantda->setParamName("IdeaSubmission");
                                        $oGrantda->setParamValue($SubmissionVal);
                                        $oGrantda->save();
                                        }
                                    }
                                }
                        }
                        
                        if(!isset($data['Custom']))
                            $data['Custom'] = "N";
                        
                        
			$this->addInputToModel($data, $this->getModel());
			$oModel = new sourceManagerModel();
			$this->getModel()->save();
            $sessionUserID = $this->getRequest()->getSession()->getUser()->getID();
			$oModel->updateSourceStatusLog($primaryKey,$inStatus,$data['Status'],$sessionUserID);
                        
                        $oEventData = mofilmEvent::getInstance($data['EventID']);
                        $ProductID = $oEventData->getProductID();
                        
                        
                        
                        if($ProductID == 3)
                        {
                            $inGID = $data['GrantID'];
                            systemLog::message($inGID."--".$inSrcID);
                            $oGrant = mofilmGrants::getInstance($inGID);
                            if(isset($data['GrantsDeadline']))
                            $grantsDeadline = $data['GrantsDeadline']." 23:50:00";
                            if(!isset($grantsDeadline))
                            {
                                $grantsDeadline = $oSourceData->getEndDate();
                                if(!isset($grantsDeadline))
                                    $grantsDeadline = $oEventData->getEndDate();
                            }
                            $oGrant->setEndDate($grantsDeadline);
                            $oGrant->setTotalGrants(floatval('0.00'));
                            $oGrant->save();
                        }
                        
                        $oMofilmDelUserPermissions = new mofilmUserSourcePermissions();
                        $oMofilmDelUserPermissions->deleteAllSources($this->getModel()->getID());
                        if($data["AllUser"] == "on")
                        {
                            $oMofilmUserSourcePermission = new mofilmUserSourcePermissions();
                            $oMofilmUserSourcePermission->setEventID($data['EventID']);
                            $oMofilmUserSourcePermission->setSourceID($this->getModel()->getID());
                            $oMofilmUserSourcePermission->setUserID(63064);
                            $oMofilmUserSourcePermission->setHasEvent(1);
                            $oMofilmUserSourcePermission->save();
                        
                        }
                        else
                        {
                            if(isset($data["FM"]))
                            {    
                                foreach ($data["FM"] as $value)
                                { 
                                    if($value > 0 )
                                    {
                                        $oMofilmUserSourcePermission = new mofilmUserSourcePermissions();
                                        $oMofilmUserSourcePermission->setEventID($data['EventID']);
                                        $oMofilmUserSourcePermission->setSourceID($this->getModel()->getID());
                                        $oMofilmUserSourcePermission->setUserID($value);
                                        $oMofilmUserSourcePermission->setHasEvent(0);
                                        $oMofilmUserSourcePermission->save();
                                    }
                                }
                            }
                        }
                        $msg = get_class($this->getModel()).' with ID '.$this->getModel()->getPrimaryKey().' successfully updated';
			$this->buildActivityLog($msg)->save();
			
			systemLog::notice($msg);
			$this->getRequest()->getSession()->setStatusMessage($msg, mvcSession::MESSAGE_OK);

			$this->redirect($this->buildUriPath(self::ACTION_VIEW));

		} catch (Exception $e) {
			systemLog::error(__CLASS__.':'.__FUNCTION__.' '.$e->getMessage());
			$this->setAction(self::ACTION_EDIT);
			$this->buildActivityLog(
				$this->getRequest()->getSession()->getUser()->getUsername().' tried to update object but it failed with error: '.$e->getMessage()
			)->save();

			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);

			$oView = new $this->_ControllerView($this);
			$oView->showDaoPage();
		}
        }
       
        /**
	 * Handles a new object
	 * 
	 * @return void
	 */
	function actionNew() {
		$this->buildModel();
		$msg = 'User is creating a new '.get_class($this->getModel());
		$this->buildActivityLog($msg)->save();
                $this->getModel()->setEventID($_GET["eventID"]);

		$oView = new $this->_ControllerView($this);
		$oView->showDaoPage();
	}

    	/**
	 * Handles listing objects and search options
	 * 
	 * @return void
	 */
	function actionView() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
		$data = $this->getInputManager()->doFilter();
		$this->setSearchOptionFromRequestData($data, 'EventID');
                systemLog::message("dd".$this->getSearchParameter("EventID"));
                
                
                
                if (isset($_GET["EventID"])){
                    $path = $this->buildUriPath(self::ACTION_NEW)."?eventID=".$_GET["EventID"];
                } else if ($this->getSearchParameter("EventID")) {
                    $path = $this->buildUriPath(self::ACTION_NEW)."?eventID=".$this->getSearchParameter("EventID");
                } else {
                    $path =  $this->buildUriPath(self::ACTION_NEW);
                }
                
                $this->getMenuItems()->getItem(self::ACTION_VIEW)->reset();
                //$oMenuItem->reset();

		$this->getMenuItems()->getItem(self::ACTION_VIEW)->addItem(
			new mvcControllerMenuItem(
				$path, 'Add Brand to Project', self::IMAGE_ACTION_NEW, 'Create a new record', false, mvcControllerMenuItem::PATH_TYPE_URI
			)
		);
                
		$this->getMenuItems()->getItem(self::ACTION_VIEW)->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_SEARCH), 'Search', self::IMAGE_ACTION_SEARCH, 'Search', false, mvcControllerMenuItem::PATH_TYPE_URI, true
			)
		);
                
		parent::actionView();
                
	}
	
	/*
	 * 
	 */
	function doDeletePrize() {
		try {
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_POST);
			$data = $this->getInputManager()->doFilter();
			
			if ( isset ($data['SourcePrizeID']) && $data['SourcePrizeID'] > 0 ) {
				$return = $this->getModel()->deletePrizeByID($data['SourcePrizeID']);
			
				if ( $return == true ) {
					$message = 'Prize Deleted';
					$level = mvcSession::MESSAGE_OK;
				} else {
					$message = $return.'Please try again.';
					$level = mvcSession::MESSAGE_ERROR;
				}
			} else {
				$message = $return.'Please try again.';
				$level = mvcSession::MESSAGE_ERROR;
			}
		} catch (Exception $e) {
			systemLog::error($e->getMessage());
			$message = $e->getMessage();
			$level = mvcSession::MESSAGE_ERROR;
		}
		
		$oView = new sourceManagerView($this);
		$oView->sendJsonResult($message, $level);
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
                $this->getInputManager()->addFilter('BrandID', utilityInputFilter::filterInt());
                $this->getInputManager()->addFilter('SponsorID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SourceDataSetID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('DisplayName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Hidden', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Custom', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Startdate', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Enddate', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Sitecopy', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Terms', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Tripbudget', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Hash', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SourceBgcolor', utilityInputFilter::filterString());

		$this->getInputManager()->addFilter('UseEventStartDate', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('UseEventEndDate', utilityInputFilter::filterInt());

		//$this->getInputManager()->addFilter('TermsID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Instructions', utilityInputFilter::filterString());
		
		$this->getInputManager()->addFilter('StartdateTime', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('EnddateTime', utilityInputFilter::filterStringArray());
		
		$this->getInputManager()->addFilter('DownloadFileData', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('Prize', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('Tracks', utilityInputFilter::filterStringArray());
		
		$this->getInputManager()->addFilter('GrantID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('GrantsAvailability', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('GrantsDeadline', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('GrantsDeadlineTime', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('CurrencySymbol', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('minGrants', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('maxGrants', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('GrantsDescription', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('FM', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('AllUser', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('BudgetOther', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('GrantBuffer', utilityInputFilter::filterInt());
                $this->getInputManager()->addFilter('CustomGrantQuestion', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('CustomGrant', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('ideaSubmissions', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('ApprovedAmount', utilityInputFilter::filterInt());
		if ( $this->getAction() == self::ACTION_DO_AJAX_DELETE_PRIZE ) {
			$this->getInputManager()->addFilter('SourcePrizeID', utilityInputFilter::filterInt());
		}
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param sourceManagerModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setEventID($inData['EventID']);
                $inModel->setBrandID($inData['BrandID']);
                $inModel->setSponsorID($inData["SponsorID"]);
		$inModel->setName($inData['Name']);
		$inModel->setHidden($inData['Hidden']);
		$inModel->setCustom($inData['Custom']);
		if($inData['Status']!='CLOSED'){
			$inModel->setCloseDate('');
		}else{
			$inModel->setCloseDate(date('Y-m-d'));
		}
		if ( $this->getAction() == self::ACTION_DO_NEW ) {
			$inModel->setCreatedDate(date('Y-m-d H:m:s'));
		}
		
		//$inModel->setTermsID($inData['TermsID']);
		$inModel->setInstructions(stripslashes(trim($_POST['Instructions'])));
		$inModel->setBgcolor(trim($inData['SourceBgcolor']));
		$inModel->setTripbudget((int)$inData['Tripbudget']);
                if ( $this->getAction() == self::ACTION_DO_NEW ) {
			$inModel->setSourceStatus(mofilmSourceBase::SOURCE_STATUS_DRAFT);
		} else {
			$inModel->setSourceStatus($inData['Status']);
		}
                
                if ( $this->getAction() == self::ACTION_DO_NEW ) {
			$inModel->addSourceTag();
		}
		
		if ( $this->getAction() == self::ACTION_DO_EDIT ) {
			$inModel->editSourceTag();
		}

		if ( isset($inData['UseEventStartDate']) && $inData['UseEventStartDate'] == 1 ) {
			$inModel->setStartDate(null);
		}
		
		if ( isset($inData['UseEventEndDate']) && $inData['UseEventEndDate'] == 1 ) {
			$inModel->setEndDate(null);
		}

		if ( isset($inData['Startdate']) && strlen($inData['Startdate']) == 10 ) {
			$inModel->setStartdate(mofilmUtilities::buildDate($inData, 'Startdate', 'StartdateTime'));
		}
		
		if ( isset($inData['Enddate']) && strlen($inData['Enddate']) == 10 ) {
			$inModel->setEnddate(mofilmUtilities::buildDate($inData, 'Enddate', 'EnddateTime'));
		}
                
                /*$oEventPermissionIDS = new mofilmUserEventPermissions();
                $UserIdArr = $oEventPermissionIDS->selectUserForEvent($inData['EventID']);*/
                
                
		$oSourceDataSet = new mofilmSourceDataSet();
		$oSourceDataSet->setID($inData['SourceDataSetID']);
		$oSourceDataSet->setName($inData['DisplayName']);
		$oSourceDataSet->setDescription(stripslashes(trim($_POST['Sitecopy'])));
		$oSourceDataSet->setTerms(stripslashes(trim($_POST['Terms'])));
		
		if ( $this->getAction() == self::ACTION_DO_NEW || $inData['Hash'] == 0 ) {
			$oSourceDataSet->setHash(mofilmUtilities::buildMiniHash(null, 8));
		} else {
			$oSourceDataSet->setHash($inData['Hash']);
		}
		
		$oSourceDataSet->setLang('en');
		$inModel->setSourceDataSet($oSourceDataSet);

		if ( isset($inData['Prize']) && is_array($inData['Prize']) && count($inData['Prize']) > 0 ) {
			$inModel->getPrizeSet()->reset();
			foreach ( $inData['Prize'] as $prize) {
				if ( $prize['ID'] == 0 && strlen($prize['Position']) > 0 && strlen($prize['Amount'] > 0 && strlen($prize['Description']) > 0 ) ) {
					$oPrize = new mofilmSourcePrizeSet();
					$oPrize->setPosition($prize['Position']);
					$oPrize->setAmount($prize['Amount']);
					$oPrize->setDescription(nl2br(trim($prize['Description'])));
				}
				
				if ( $oPrize ) {
					$inModel->getPrizeSet()->setObject($oPrize);
				}
			}
		}

		if ( isset ($inData['DownloadFileData']) && is_array($inData['DownloadFileData']) && count($inData['DownloadFileData']) ) {
			$eventname = str_replace(' ', '-', mofilmEvent::getInstance($inData['EventID'])->getName());
                        $assetSet = array();
			foreach ( $inData['DownloadFileData'] as $DownloadFileData ) {
				if ( $DownloadFileData['ID'] == 0 && strlen($DownloadFileData['FileType']) > 0 && strlen($DownloadFileData['Description']) > 0 && strlen($DownloadFileData['Name']) > 0 ) {
					$oDownloadFileData = new mofilmDownloadFile();
					$oDownloadFileData->setID($DownloadFileData['ID']);
					$oDownloadFileData->setFiletype($DownloadFileData['FileType']);
					$oDownloadFileData->setDescription($DownloadFileData['Description']);
					if ( $oDownloadFileData->isExtenalLink($DownloadFileData['Name'])) {
						$oDownloadFileData->setFilename($DownloadFileData['Name']);
					} else {
						$oDownloadFileData->setFilename($eventname.DIRECTORY_SEPARATOR.$DownloadFileData['Name']);
					}
					
					$oDownloadFileData->setLang('en');
                                        $assetSet[] = $oDownloadFileData;
					
				}
			}
                        $inModel->setSourceDownloadFileSet($assetSet);
		}
		
		if ( isset($inData['Tracks']) && is_array($inData['Tracks']) && count($inData['Tracks']) > 0 ) {
			$inModel->getTrackSet()->reset();
			
			foreach ( $inData['Tracks'] as $id => $track ) {
				$oTrack = false;
				if ( isset($track['Remove']) ) {
					continue;
				}

				if ( isset($track['ID']) && $track['ID'] > 0 && $track['Hash'] && strlen($track['Hash']) > 0 ) {
					$oTrack = mofilmTrack::getInstance($track['ID']);
					$oTrack->setDownloadHash($track['Hash']);
				} elseif ( strlen($track['Title']) > 0 ) {
					$oTrack = mofilmTrack::getInstance($track['ID']);
					$oTrack->setArtist($track['Artist']);
					$oTrack->setTitle($track['Title']);
					$oTrack->setSupplierID(mofilmSupplier::getInstanceByDescription($track['Supplier'])->getID());
					$oTrack->setFiletype(mofilmTrack::FILETYPE_MUSIC);
					$oTrack->setDescription($track['Title']);
				}

				if ( $oTrack ) {
					$inModel->getTrackSet()->setObject($oTrack);
				}
			}
		}

		if ( $inData['GrantsAvailability'] == 'Y' ) {
			$oGrants['ID'] = $inData['GrantID'];
			$oGrants['EndDate'] = mofilmUtilities::buildDate($inData, 'GrantsDeadline', 'GrantsDeadlineTime');
			$oGrants['CurrencySymbol'] = $inData['CurrencySymbol'];
			$oGrants['TotalGrants'] = (float) $inData['maxGrants'];
			$oGrants['Description'] = $inData['GrantsDescription'];
			$inModel->setGrantsSet($oGrants);
		}
		
//		
	}
	
		/**
	 * Processes the source logo image (if any)
	 *
	 * @return boolean
	 */
	protected function actionUploadSourceLogo() {
		$inSourceID = (int) $this->getActionFromRequest(false, 1);
		$oSource = new mofilmSource($inSourceID);

		$oFileUpload = new mvcFileUpload(
			array(
				mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
				mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
				mvcFileUpload::OPTION_FIELD_NAME => 'FilesLogo',
				mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
				mvcFileUpload::OPTION_WRITE_IMMEDIATE => false,
				mvcFileUpload::OPTION_STORE_RAW_DATA => true,
			)
		);
		$oFileUpload->initialise();
		try {
			$oFiles = $oFileUpload->process();
			if ( $oFiles->getCount() > 0 ) {
				systemLog::message('Adding logo images for '.$oSource->getName());

				$oImageConv = new imageConvertor(
					array(
						imageConvertor::OPTION_OUTPUT_FILENAME => $oSource->getLogoName(),
						imageConvertor::OPTION_OUTPUT_OVERWRITE_FILES => true,
					)
				);

				$images = array(
					'client' => array(
						    imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getClientSourceLogoFolder(),
						    imageConvertor::OPTION_OUTPUT_FORMAT => 'gif',
						    imageConvertor::OPTION_OUTPUT_QUALITY => 90,
						    imageConvertor::OPTION_OUTPUT_WIDTH => 164,
						    imageConvertor::OPTION_OUTPUT_HEIGHT => 152,
						    imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
						    imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
					),
					'admin' => array(
						    imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getAdminSourceFolder(),
						    imageConvertor::OPTION_OUTPUT_FORMAT => 'gif',
						    imageConvertor::OPTION_OUTPUT_WIDTH => 50,
						    imageConvertor::OPTION_OUTPUT_HEIGHT => 28,
						    imageConvertor::OPTION_OUTPUT_QUALITY => 90,
						    imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
						    imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
					),
                                        'mobile' => array(
						    imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getClientSourceLogoFolder(),
						    imageConvertor::OPTION_OUTPUT_FORMAT => 'jpg',
						    imageConvertor::OPTION_OUTPUT_QUALITY => 100,
						    imageConvertor::OPTION_OUTPUT_WIDTH => 100,
						    imageConvertor::OPTION_OUTPUT_HEIGHT => 100,
						    imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
						    imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
					),
                                        'mobilelarge' => array(
						    imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getClientSourceLogoFolder(),
						    imageConvertor::OPTION_OUTPUT_FORMAT => 'jpg',
						    imageConvertor::OPTION_OUTPUT_QUALITY => 100,
						    imageConvertor::OPTION_OUTPUT_WIDTH => 400,
						    imageConvertor::OPTION_OUTPUT_HEIGHT => 400,
						    imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
						    imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
                                                    imageConvertor::OPTION_OUTPUT_FILENAME => $oSource->getLogoName()."_400"
					),
                                        'mofilm' => array(
						    imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getClientSourceLogoFolder(),
						    imageConvertor::OPTION_OUTPUT_FORMAT => 'png',
						    imageConvertor::OPTION_OUTPUT_QUALITY => 100,
						    imageConvertor::OPTION_OUTPUT_WIDTH => 129,
						    imageConvertor::OPTION_OUTPUT_HEIGHT => 101,
						    imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
						    imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
                                                    imageConvertor::OPTION_OUTPUT_FILENAME => $oSource->getLogoName()
					)                                    
                                    
				);

				foreach ( $images as $type => $options ) {
					systemLog::message("Creating $type source image");
					$oImageConv->setOptions($options);
					$oImageConv->process($oFiles->getFirst()->getRawFileData());
				}
			}
		} catch ( mvcFileUploadNoFileUploadedException $e ) {
			systemLog::warning($e->getMessage());
			$return = null;
		} catch ( mvcFileUploadException $e ) {
			systemLog::error($e->getMessage());
			$return = false;
		}

		return $return;
	}

	/**
	 * Processes the source banner image
	 *
	 * @return boolean
	 */
	function actionUploadSourceBanner() {
		$inSourceID = (int) $this->getActionFromRequest(false, 1);
		$oSource = new mofilmSource($inSourceID);
			
		$oFileUpload = new mvcFileUpload(
			array(
				mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
				mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
				mvcFileUpload::OPTION_FIELD_NAME => 'FilesBanner',
				mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
				mvcFileUpload::OPTION_WRITE_IMMEDIATE => false,
				mvcFileUpload::OPTION_STORE_RAW_DATA => true,
				mvcFileUpload::OPTION_USE_ORIGINAL_NAME => true,
			)
		);
		$oFileUpload->initialise();
		try {
			$oFiles = $oFileUpload->process();
			$oFile = $oFiles->getFirst();
			if ( $oFile instanceof mvcFileObject ) {
				systemLog::info('Checking file target location');
				$fileloc = mofilmConstants::getClientSourceBannerFolder().system::getDirSeparator().$oSource->getLogoName().'.png';
				if ( !file_exists(dirname($fileloc)) ) {
					mkdir(dirname($fileloc), 0755, true);
				}
						
				$bytes = file_put_contents($fileloc, $oFile->getRawFileData());
				systemLog::notice("Wrote $bytes bytes to the file system for banner ");
			}
		} catch ( mvcFileUploadException $e ) {
			systemLog::error($e->getMessage());
		}
	}
	
	/**
	 * Processes the source banner filler image (if any)
	 *
	 * @return boolean
	 */
	function actionUploadSourceFiller() {
		$inSourceID = (int) $this->getActionFromRequest(false, 1);
		$oSource = new mofilmSource($inSourceID);
			
		$oFileUpload = new mvcFileUpload(
			array(
				mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
				mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
				mvcFileUpload::OPTION_FIELD_NAME => 'FilesFiller',
				mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
				mvcFileUpload::OPTION_WRITE_IMMEDIATE => false,
				mvcFileUpload::OPTION_STORE_RAW_DATA => true,
				mvcFileUpload::OPTION_USE_ORIGINAL_NAME => true,
			)
		);
		$oFileUpload->initialise();
		try {
			$oFiles = $oFileUpload->process();
			$oFile = $oFiles->getFirst();
			if ( $oFile instanceof mvcFileObject ) {
				systemLog::info('Checking file target location');
				$fileloc = mofilmConstants::getClientSourceFillerFolder().system::getDirSeparator().$oSource->getLogoName().'.jpg';
				if ( !file_exists(dirname($fileloc)) ) {
					mkdir(dirname($fileloc), 0755, true);
				}
						
				$bytes = file_put_contents($fileloc, $oFile->getRawFileData());
				systemLog::notice("Wrote $bytes bytes to the file system for filler ");
			}
		} catch ( mvcFileUploadException $e ) {
			systemLog::error($e->getMessage());
		}
	}
	
        function getCorporate($brandID){
            $this->_Corporate = $this->getModel()->getCorporateModel($brandID);
            return $this->_Corporate;
        }
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new sourceManagerModel();
		$oModel->setCurrentUser($this->getRequest()->getSession()->getUser());
		$this->setModel($oModel);
	}
}
