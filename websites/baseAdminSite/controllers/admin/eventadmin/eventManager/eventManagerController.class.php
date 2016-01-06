<?php
/**
 * eventManagerController
 *
 * Stored in eventManagerController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventManagerController
 * @version $Rev: 11 $
 */

/**
 * eventManagerController
 *
 * eventManagerController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventManagerController
 */
class eventManagerController extends mvcDaoController {

    const VIEW_EVENTS = 'events';
    const ACTION_EVENT_SOURCE_STATS = 'sourceStats';
    const ACTION_EVENT_GRANT_STATS = 'grantStats';
    const ACTION_UPLOAD_EVENT_LOGO = 'doUploadEventLogo';
    const ACTION_UPLOAD_EVENT_BANNER = 'doUploadEventBanner';
    const ACTION_UPLOAD_EVENT_FILLER = 'doUploadEventFiller';
    const ACTION_NEW_PROJECT = "newProject";
    const ACTION_ADD_BRAND = "addBrand";

    /**
     * @see mvcControllerBase::launch()
     */
    function launch() {
        if ($this->getAction() == self::ACTION_EVENT_SOURCE_STATS) {
            $this->actionEventSourceStats();
            return;
        }
        if ($this->getAction() == self::ACTION_EVENT_GRANT_STATS) {
            $this->actionEventGrantStats();
            return;
        }
        if ($this->getAction() == self::ACTION_UPLOAD_EVENT_LOGO) {
            $this->actionUploadEventLogo();
        }
        if ($this->getAction() == self::ACTION_UPLOAD_EVENT_BANNER) {
            $this->actionUploadEventBanner();
        }
        if ($this->getAction() == self::ACTION_UPLOAD_EVENT_FILLER) {
            $this->actionUploadEventFiller();
        }

        if ($this->getAction() == self::ACTION_ADD_BRAND) {
            $this->actionAddBrand();
        }

        /*
          if ($this->getAction() == self::ACTION_NEW) {
          $this->actionNew();
          exit();
          } else {
          parent::launch();
          exit();
          }
         * 
         */
        /*
          if ($this->getAction() == self::ACTION_NEW_PROJECT) {
          $this->actionCreateProject();
          }
         * 
         */
        parent::launch();
    }

    function actionAddBrand() {
        $this->redirect("/admin/eventadmin/sourceManager/viewObjects?Search=Search&EventID=".$_GET["eventID"]);
        
        
    }

    function actionNew() {

        
        $oMenuItem = $this->getMenuItems()->getItem(self::ACTION_NEW);
        $oMenuItem->reset();
        
        $oMenuItem->addItem(
                new mvcControllerMenuItem(
                "/admin/eventadmin/sourceManager", 'Save + Add Brand', self::IMAGE_ACTION_NEW, 'Next', false, mvcControllerMenuItem::PATH_TYPE_URI,true
                )
        );
        
        $oMenuItem->addItem(
                new mvcControllerMenuItem(
                $this->buildUriPath(self::ACTION_NEW), 'Save', self::IMAGE_ACTION_NEW, 'Next', true, mvcControllerMenuItem::PATH_TYPE_URI, true
                )
        );
        $oMenuItem->addItem(
                new mvcControllerMenuItem(
                $this->buildUriPath(self::ACTION_VIEW), 'Cancel', self::IMAGE_ACTION_CANCEL, 'Cancel new record', false, mvcControllerMenuItem::PATH_TYPE_URI
                )
        );
        
        parent::actionNew();
    }

    function getUserPermissionList($eventID)
    {
        $oMofilmUserEventPermission = mofilmUserEventPermissions::listOfObjects(0,$eventID);
        foreach($oMofilmUserEventPermission as $oMofilmEventPer)
        {
            $UserArr[]['id'] =  $oMofilmEventPer->getUserID();
            
        }
        
        return $UserArr;
    }
    
    function actionEdit() {
            
        $oMenuItem = $this->getMenuItems()->getItem(self::ACTION_EDIT);
        $oMenuItem->reset();
        $oMenuItem->addItem(
                new mvcControllerMenuItem(
                "/admin/eventadmin/sourceManager", 'Save + Add Brand', self::IMAGE_ACTION_NEW, 'Next', true, mvcControllerMenuItem::PATH_TYPE_URI,true
                )
        );
        
        $oMenuItem->addItem(
                new mvcControllerMenuItem(
                $this->buildUriPath(self::ACTION_NEW), 'Save', self::IMAGE_ACTION_NEW, 'Next', true, mvcControllerMenuItem::PATH_TYPE_URI, true
                )
        );
        $oMenuItem->addItem(
                new mvcControllerMenuItem(
                $this->buildUriPath(self::ACTION_VIEW), 'Cancel', self::IMAGE_ACTION_CANCEL, 'Cancel new record', false, mvcControllerMenuItem::PATH_TYPE_URI
                )
        );
        ///admin/eventadmin/sourceManager

            parent::actionEdit();
    }
    
    
    
	/**
	 * Handles creating a new object and storing it
	 * 
	 * @return void
	 */
	function actionDoNew() {
                
		try {
			$this->buildModel();

			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_POST);
			$data = $this->getInputManager()->doFilter();
                        if($data['ProductID'] == 3)
                        {
                            $data['Sitecopy'] = $data['Name'];
                            $data['DisplayName'] = $data['Name'];
                        }
                        if($data["AllUser"] != "on")
                        {
                        $UserCnt = 0;
                        foreach ($data["FM"] as $value) {
                                    if($value > 0 ) $UserCnt = $UserCnt+1;
                        }  
                        
                        }
                        systemLog::message($data);
                        $this->addInputToModel($data, $this->getModel());
			$this->getModel()->save();

			$msg = 'New '.get_class($this->getModel()).' with ID '.$this->getModel()->getPrimaryKey().' created successfully';
			$this->buildActivityLog($msg)->save();
                        
                        if($this->getModel()->getID())
                        {
                            if($data["AllUser"] != "on")
                            {
                                
                                foreach ($data["FM"] as $value)
                                {
                                    if($value > 0 )
                                    {
                                    $oMofilmUserEventPermission = new mofilmUserEventPermissions();
                                    $oMofilmUserEventPermission->setEventID($this->getModel()->getID());
                                    $oMofilmUserEventPermission->setUserID($value);
                                    $oMofilmUserEventPermission->save();
                                    }
                                }
                            }
                        }
                        systemLog::notice($msg);
			$this->getRequest()->getSession()->setStatusMessage($msg, mvcSession::MESSAGE_OK);
                        if(isset($_POST["Save_+_Add_Brand"])){
                            $this->redirect($this->buildUriPath(self::ACTION_ADD_BRAND)."?eventID=".$this->getModel()->getID());
                        } else {
                            $this->redirect($this->buildUriPath(self::ACTION_VIEW));
                        }
                        //$this->redirect("/admin/eventadmin/sourceManager/newObject");

		} catch (Exception $e) {
			systemLog::error(__CLASS__.'::'.__FUNCTION__.' '.$e->getMessage());
			$this->buildActivityLog(
				$this->getRequest()->getSession()->getUser()->getUsername().' tried store a new object but it failed with error: '.$e->getMessage()
			)->save();

			$this->setAction(self::ACTION_NEW);
			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);

			$oView = new $this->_ControllerView($this);
			$oView->showDaoPage();
		}
	}

        
	function actionDoEdit() {
                
		try {
			$this->buildModel();

			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_POST);
			$data = $this->getInputManager()->doFilter();
                        if($data['ProductID'] == 3)
                        {
                            $data['Sitecopy'] = $data['Name'];
                            $data['DisplayName'] = $data['Name'];
                        }
                        $this->addInputToModel($data, $this->getModel());
			$this->getModel()->save();

			$msg = 'New '.get_class($this->getModel()).' with ID '.$this->getModel()->getPrimaryKey().' created successfully';
			$this->buildActivityLog($msg)->save();
                        $oMofilmDelUserPermissions = new mofilmUserEventPermissions();
                        $oMofilmDelUserPermissions->deleteAllEvents($this->getModel()->getID());
                        if($data["AllUser"] == "on")
                        {
                                                    
                        }
                        else
                        {
                            if(isset($data["FM"]))
                            {    
                                foreach ($data["FM"] as $value)
                                { 
                                    if($value > 0 )
                                    {
                                        $oMofilmUserEventPermission = new mofilmUserEventPermissions();
                                        $oMofilmUserEventPermission->setEventID($this->getModel()->getID());
                                        $oMofilmUserEventPermission->setUserID($value);
                                        $oMofilmUserEventPermission->save();
                                    
                                    }
                                }
                            }
                        }
                        
                        
                                              
                        systemLog::notice($msg);
			$this->getRequest()->getSession()->setStatusMessage($msg, mvcSession::MESSAGE_OK);

			//$this->redirect($this->buildUriPath(self::ACTION_ADD_BRAND)."?eventID=".$this->getModel()->getID());
                        //$this->redirect("/admin/eventadmin/sourceManager/newObject");
                        if(isset($_POST["Save_+_Add_Brand"])){
                            $this->redirect($this->buildUriPath(self::ACTION_ADD_BRAND)."?eventID=".$this->getModel()->getID());
                        } else {
                            $this->redirect($this->buildUriPath(self::ACTION_VIEW));
                        }


		} catch (Exception $e) {
			systemLog::error(__CLASS__.'::'.__FUNCTION__.' '.$e->getMessage());
			$this->buildActivityLog(
				$this->getRequest()->getSession()->getUser()->getUsername().' tried store a new object but it failed with error: '.$e->getMessage()
			)->save();

			$this->setAction(self::ACTION_NEW);
			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);

			$oView = new $this->_ControllerView($this);
			$oView->showDaoPage();
		}
	}
        
    /**
     * Returns source stats for the specified event
     * 
     * @return void
     */
    function actionEventSourceStats() {
        if (!$this->getRequest()->isAjaxRequest()) {
            throw new mvcControllerException('Source statistics can only be accessed via ajax requests.');
        }

        $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
        $this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
        $data = $this->getInputManager()->doFilter();

        if (array_key_exists('EventID', $data)) {
            $this->setPrimaryKey($data['EventID']);
        }

        $oView = new eventManagerView($this);
        $oView->showSourceStats();
    }

    function actionCreateProject() {
        $oView = new eventManagerView($this);
        $oView->showCreateProject();
    }

    /**
     * Returns source stats for the specified event
     * 
     * @return void
     */
    function actionEventGrantStats() {
        if (!$this->getRequest()->isAjaxRequest()) {
            throw new mvcControllerException('Grant statistics can only be accessed via ajax requests.');
        }

        $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
        $this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
        $data = $this->getInputManager()->doFilter();

        if (array_key_exists('EventID', $data)) {
            $this->setPrimaryKey($data['EventID']);
        }

        $oView = new eventManagerView($this);
        $oView->showGrantStats();
    }

    /**
     * Handles listing objects and search options
     * 
     * @return void
     */
    function actionView() {
        $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
        $this->getInputManager()->addFilter('Active', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('CorporateID', utilityInputFilter::filterInt());
        $this->getInputManager()->addFilter('BrandID', utilityInputFilter::filterInt());
        $this->getInputManager()->addFilter('ProductID', utilityInputFilter::filterInt());
        $data = $this->getInputManager()->doFilter();

        $this->setSearchOptionFromRequestData($data, 'Active');
        $this->setSearchOptionFromRequestData($data, 'CorporateID');
        $this->setSearchOptionFromRequestData($data, 'BrandID');
        $this->setSearchOptionFromRequestData($data, 'ProductID');

        parent::actionView();
    }

    /**
     * Returns the standalone view based on the params
     *
     * @param array $params
     * @return string
     */
    function fetchStandaloneView($params = array()) {
        switch ($params['view']) {
            case self::VIEW_EVENTS:
                $oView = new eventManagerView($this);
                return $oView->getEventListView($params);
                break;
        }
    }

    /**
     * @see mvcControllerBase::initialise()
     */
    function initialise() {
        parent::initialise();

        $this->setControllerView('eventManagerView');


        $this->getMenuItems()->getItem(self::ACTION_VIEW)->addItem(
                new mvcControllerMenuItem(
                $this->buildUriPath(self::ACTION_NEW), 'New project', self::IMAGE_ACTION_NEW, 'Create a new record', false, mvcControllerMenuItem::PATH_TYPE_URI
                )
        );


        /*
          $this->getMenuItems()->getItem(self::ACTION_VIEW)->removeItem(
          new mvcControllerMenuItem(
          $this->buildUriPath(self::ACTION_NEW), 'New project', self::IMAGE_ACTION_NEW, 'Create a new record', false, mvcControllerMenuItem::PATH_TYPE_URI
          )
          );
         * 
         */



        $this->getMenuItems()->getItem(self::ACTION_VIEW)->addItem(
                new mvcControllerMenuItem(
                $this->buildUriPath(self::ACTION_SEARCH), 'Search', self::IMAGE_ACTION_SEARCH, 'Search', false, mvcControllerMenuItem::PATH_TYPE_URI, true
                )
        );


        $this->getControllerViews()->addView(self::VIEW_EVENTS);

        $this->getControllerActions()->addAction(self::ACTION_EVENT_SOURCE_STATS);
        $this->getControllerActions()->addAction(self::ACTION_EVENT_GRANT_STATS);
        $this->getControllerActions()->addAction(self::ACTION_UPLOAD_EVENT_LOGO);
        $this->getControllerActions()->addAction(self::ACTION_UPLOAD_EVENT_BANNER);
        $this->getControllerActions()->addAction(self::ACTION_UPLOAD_EVENT_FILLER);
        $this->getControllerActions()->addAction(self::ACTION_NEW_PROJECT);
        $this->getControllerActions()->addAction(self::ACTION_ADD_BRAND);
    }

    /**
     * @see mvcControllerBase::addInputFilters()
     */
    function addInputFilters() {
        $this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
        $this->getInputManager()->addFilter('EventDataSetID', utilityInputFilter::filterInt());
        $this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('DisplayName', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('Hidden', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('Startdate', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('Enddate', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('AwardStartdate', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('AwardEnddate', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('TermsID', utilityInputFilter::filterInt());
        $this->getInputManager()->addFilter('Instructions', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('EventBgcolor', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('Sitecopy', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('Custom', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('ProductID', utilityInputFilter::filterInt());
        $this->getInputManager()->addFilter('Tba', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('Hash', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('Email', utilityInputFilter::filterString());

        $this->getInputManager()->addFilter('StartdateTime', utilityInputFilter::filterStringArray());
        $this->getInputManager()->addFilter('EnddateTime', utilityInputFilter::filterStringArray());
        $this->getInputManager()->addFilter('AwardStartdateTime', utilityInputFilter::filterStringArray());
        $this->getInputManager()->addFilter('AwardEnddateTime', utilityInputFilter::filterStringArray());
        
        $this->getInputManager()->addFilter('Contributors', utilityInputFilter::filterStringArray());
        $this->getInputManager()->addFilter('AllUser', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('FM', utilityInputFilter::filterStringArray());
    }

    /**
     * @see mvcControllerBase::addInputToModel()
     *
     * @param array $inData
     * @param eventManagerModel $inModel
     */
    function addInputToModel($inData, $inModel) {
        $inModel->setID($inData['PrimaryKey']);
        $inModel->setName($inData['Name']);
        $inModel->setWebpath('/competitions/event/' . str_replace(' ', '-', trim($inData['Name'])));
        $inModel->setHidden($inData['Hidden']);
        $inModel->setTermsID($inData['TermsID']);
        $inModel->setInstructions(stripslashes(trim($_POST['Instructions'])));
        $inModel->setBgcolor(trim($inData['EventBgcolor']));
        $inModel->setStartDate(mofilmUtilities::buildDate($inData, 'Startdate', 'StartdateTime'));
        $inModel->setEndDate(mofilmUtilities::buildDate($inData, 'Enddate', 'EnddateTime'));
        $inModel->setAwardStartDate(mofilmUtilities::buildDate($inData, 'AwardStartdate', 'AwardStartdateTime'));
        $inModel->setAwardEndDate(mofilmUtilities::buildDate($inData, 'AwardEnddate', 'AwardEnddateTime'));
        $inModel->setCustom($inData['Custom']);
        $inModel->setProductID($inData['ProductID']);
        $inModel->setTba($inData['Tba']);
        $inModel->setStatus($inData['Status']);       
        $oEventDataSet = new mofilmEventDataSet();
        $oEventDataSet->setID($inData['EventDataSetID']);
        $oEventDataSet->setName($inData['DisplayName']);
        $oEventDataSet->setDescription(stripslashes(trim($_POST['Sitecopy'])));

        if ($this->getAction() == self::ACTION_DO_NEW || $inData['Hash'] == 0) {
            $oEventDataSet->setHash(mofilmUtilities::buildMiniHash(null, 8));
        } else {
            $oEventDataSet->setHash($inData['Hash']);
        }

        $oEventDataSet->setLang('en');
        $inModel->setEventDataSet($oEventDataSet);

        if ($this->getAction() == self::ACTION_DO_NEW) {
            $inModel->addEventTag();
        }

        if ($this->getAction() == self::ACTION_DO_EDIT) {
            $inModel->editEventTag();
        }
    }

    /**
     * Processes the event logo image
     *
     * @return boolean
     */
    protected function actionUploadEventLogo() {
        $inEventID = (int) $this->getActionFromRequest(false, 1);
        $oEvent = new mofilmEvent($inEventID);

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
            if ($oFiles->getCount() > 0) {
                systemLog::message('Adding logo images for ' . $oEvent->getName());
                $oImageConv = new imageConvertor(
                        array(
                    imageConvertor::OPTION_OUTPUT_FILENAME => $oEvent->getLogoName() . '.gif',
                    imageConvertor::OPTION_OUTPUT_OVERWRITE_FILES => true,
                        )
                );

                $images = array(
                    'client' => array(
                        imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getClientEventsLogoFolder(),
                        imageConvertor::OPTION_OUTPUT_FORMAT => 'gif',
                        imageConvertor::OPTION_OUTPUT_QUALITY => 90,
                        imageConvertor::OPTION_OUTPUT_WIDTH => 164,
                        imageConvertor::OPTION_OUTPUT_HEIGHT => 152,
                        imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
                        imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
                    ),
                    'admin' => array(
                        imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getAdminEventsFolder(),
                        imageConvertor::OPTION_OUTPUT_FORMAT => 'gif',
                        imageConvertor::OPTION_OUTPUT_WIDTH => 50,
                        imageConvertor::OPTION_OUTPUT_HEIGHT => 28,
                        imageConvertor::OPTION_OUTPUT_QUALITY => 90,
                        imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
                        imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
                    ),
                    'mofilm' => array(
                        imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getClientEventsLogoFolder(),
                        imageConvertor::OPTION_OUTPUT_FORMAT => 'png',
                        imageConvertor::OPTION_OUTPUT_QUALITY => 90,
                        imageConvertor::OPTION_OUTPUT_WIDTH => 262,
                        imageConvertor::OPTION_OUTPUT_HEIGHT => 243,
                        imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
                        imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
                    )
                );

                foreach ($images as $type => $options) {
                    systemLog::message("Creating $type event image");
                    $oImageConv->setOptions($options);
                    $oImageConv->process($oFiles->getFirst()->getRawFileData());
                }
            }
        } catch (mvcFileUploadNoFileUploadedException $e) {
            systemLog::warning($e->getMessage());
        } catch (mvcFileUploadException $e) {
            systemLog::error($e->getMessage());
        }
        return true;
    }

    /**
     * Processes the event banner image
     *
     * @return boolean
     */
    function actionUploadEventBanner() {
        $inEventID = (int) $this->getActionFromRequest(false, 1);
        $oEvent = new mofilmEvent($inEventID);

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
            if ($oFile instanceof mvcFileObject) {
                systemLog::info('Checking file target location');
                $fileloc = mofilmConstants::getClientEventsBannerFolder() . system::getDirSeparator() . $oEvent->getLogoName() . '.png';
                if (!file_exists(dirname($fileloc))) {
                    mkdir(dirname($fileloc), 0755, true);
                }

                $bytes = file_put_contents($fileloc, $oFile->getRawFileData());
                systemLog::notice("Wrote $bytes bytes to the file system for banner ");
            }
        } catch (mvcFileUploadException $e) {
            systemLog::error($e->getMessage());
        }
        return true;
    }

    /**
     * Processes the event banner filler image (if any)
     *
     * @return boolean
     */
    function actionUploadEventFiller() {
        $inEventID = (int) $this->getActionFromRequest(false, 1);
        $oEvent = new mofilmEvent($inEventID);

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
            if ($oFile instanceof mvcFileObject) {
                systemLog::info('Checking file target location');
                $fileloc = mofilmConstants::getClientEventsFillerFolder() . system::getDirSeparator() . $oEvent->getLogoName() . '.jpg';
                if (!file_exists(dirname($fileloc))) {
                    mkdir(dirname($fileloc), 0755, true);
                }

                $bytes = file_put_contents($fileloc, $oFile->getRawFileData());
                systemLog::notice("Wrote $bytes bytes to the file system for filler ");
            }
        } catch (mvcFileUploadException $e) {
            systemLog::error($e->getMessage());
        }
        return true;
    }

    /**
     * Builds the model
     *
     * @return void
     */
    function buildModel() {
        $oModel = new eventManagerModel();
        $oModel->setCurrentUser($this->getRequest()->getSession()->getUser());
        $this->setModel($oModel);
    }

}
