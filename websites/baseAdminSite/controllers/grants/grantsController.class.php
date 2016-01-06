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
    const ACTION_SEARCH = 'doSearch';
    const ACTION_SEND_EMAIL = 'sendEmail';
    const ACTION_DO_SEND_EMAIL = 'doSendEmail';
    const ACTION_GENERATE_PDF = 'generatePdf';
    const ACTION_DO_DOCS_UPLOAD = 'doDocsUpload';
    const ACTION_RATE = 'rate';
    

    /**
     * Stores $_SearchQuery
     *
     * @var array
     * @access protected
     */
    protected $_SearchQuery;

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
                ->addAction(self::ACTION_EDIT)
                ->addAction(self::ACTION_DO_EDIT)
                ->addAction(self::ACTION_SEARCH)
                ->addAction(self::ACTION_VIEW)
                ->addAction(self::ACTION_RATE)
                ->addAction(self::ACTION_SEND_EMAIL)
                ->addAction(self::ACTION_DO_SEND_EMAIL)
                ->addAction(self::ACTION_GENERATE_PDF)
                ->addAction(self::ACTION_DO_DOCS_UPLOAD);



        $this->setSearchQuery(array());

        $this->addInputFilters();
    }

    /**
     * @see mvcControllerBase::launch()
     */
    function launch() {
        switch ($this->getAction()) {
            case self::ACTION_APPLY: $this->applyForGrants();
                break;
            case self::ACTION_RATE: $this->rate();
                break;
            case self::ACTION_DO_APPLY: $this->doSaveApplyForm();
                break;
            case self::ACTION_EDIT: $this->editGrant();
                break;
            case self::ACTION_DO_EDIT: $this->doEditGrant();
                break;
            case self::ACTION_VIEW: $this->viewGrant();
                break;
            case self::ACTION_SEND_EMAIL: $this->sendemail();
                break;
            case self::ACTION_DO_SEND_EMAIL: $this->doSendemail();
                break;
            case self::ACTION_GENERATE_PDF: $this->generatePDF();
                break;
            case self::ACTION_DO_DOCS_UPLOAD: $this->uploadDocs();
                break;
            case self::ACTION_LIST:

            default:
                $this->search();
                break;
        }
    }

    /**
     * 
     */
    function listGrants() {
        $oView = new grantsView($this);
        $oView->getObjectListView();
    }

    /**
     * 
     */
    protected function doSaveApplyForm() {
        try {
            $this->addInputToModel($this->getInputManager()->doFilter(), $this->getModel());
            $this->getModel()->save();
            $this->getModel()->sendGrantsReciptEmail();
        } catch (Exception $error) {
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
        $oView->showApplyForGrants($sourceID);
    }
    /**
     * 
     * Rating the grant/idea
     * 
     */
    function rate() {
        systemLog::message("here 1");
        $currentUser = $this->getRequest()->getSession()->getUser()->getID();
        $data = $this->getInputManager()->doFilter();
        $this->addInputToModel($data, $this->getModel());

        try {
            $this->getModel()->rateGrant($currentUser,$data);
            $message = 'Your rating has been recorded successfully.';
            $level = mvcSession::MESSAGE_OK;
        } catch (Exception $e) {
            systemLog::error($e->getMessage());
            $message = $e->getMessage();
            $level = mvcSession::MESSAGE_ERROR;
        }

        //if ($this->getRequest()->isAjaxRequest()) {
        //    $oView = new grantsView($this);
        //    $oView->sendRatingResult();
       // } else {
            //$this->getRequest()->getSession()->setStatusMessage($message, $level);
            //$this->redirect("www.google.com");
        //}
    }

    /*
     * Generates an PDF using TCPDF and forces browser to download PDF file
     * 
     * @param userMovieGrant ID
     * 
     * @return PDF file
     */

    function generatePDF() {
        $inGrantID = (int) $this->getActionFromRequest(false, 1);
        $AssetsFile = $this->getModel()->getGrantAssets($inGrantID);

        $dirPath = mofilmConstants::getTcpdfFolder();
        $filename = $this->getModel()->generatePDF($inGrantID, $dirPath);
        if ($AssetsFile) {
            $GrantsArr = array($inGrantID);
            $return = $this->generateMultiplePDF($GrantsArr);
            return TRUE;
        }
        if (file_exists($dirPath . $filename)) {
            $this->sendFile($filename, $dirPath);
        }

        return TRUE;
    }

    /**
     * Generates multiple PDF using TCPDF, zips all the requested PDF file and forces browser to download zip file
     * 
     * @param userMovieGrant IDs
     * 
     * @return zip file
     */
    function generateMultiplePDF($grantIds) {
        $zipname = time();
        $dirname = mofilmConstants::getTcpdfFolder() . $zipname . DIRECTORY_SEPARATOR;
        mkdir($dirname, 0777);
        systemLog::message($dirname);
        $CountofGrants = count($grantIds);

        $zip = new ZipArchive();

        if ($zip->open($dirname . $zipname . '.zip', ZipArchive::CREATE) == TRUE) {
            foreach ($grantIds as $inGrantID) {
                $AssetsFile = $this->getModel()->getGrantAssets($inGrantID);
                $filename = $this->getModel()->generatePDF($inGrantID, $dirname);
                if ($AssetsFile) {
                    $Basename = basename($AssetsFile);
                    $Subzip = new ZipArchive();
                    $filenameArr = explode(".", $filename);
                    $Subzip->open($dirname . $filenameArr[0] . '.zip', ZipArchive::CREATE);
                    $Subzip->addFile($dirname . $filename, $filename);
                    $Subzip->addFile($AssetsFile, $Basename);
                    $Subzip->close();
                    if ($CountofGrants == "1") {
                        $this->sendFile($filenameArr[0] . '.zip', $dirname);
                        return TRUE;
                    }
                    $zip->addFile($dirname . $filenameArr[0] . '.zip', $filename . '.zip');
                } else {
                    if ($CountofGrants == "1") {
                        if (file_exists($filename))
                            $this->sendFile($filename, $dirname);
                    }
                    $zip->addFile($dirname . $filename, $filename);
                }
            }
            $zip->close();

            if (file_exists($dirname . $zipname . '.zip')) {
                $this->sendFile($zipname . '.zip', $dirname);
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * 
     */

    function viewGrant() {
        $inGrantID = (int) $this->getActionFromRequest(false, 1);
        $grantProductID = $this->getModel()->getProductID($inGrantID);
        $oView = new grantsView($this);
        $oView->grantView($grantProductID, $inGrantID);
    }

    /*
     * 
     */

    function sendemail() {
        $inGrantID = (int) $this->getActionFromRequest(false, 1);
        $oView = new grantsView($this);
        $oView->sendEmail($inGrantID);
    }

    /*
     * 
     */

    function doSendemail() {
        try {
            $data = $this->getInputManager()->doFilter();
            $return = $this->getModel()->sendEmailCommunication($data, $this->getRequest()->getSession()->getUser()->getID());

            if ($return == true) {
                $message = 'The grants has been saved successfully.';
                $level = mvcSession::MESSAGE_OK;
            } else {
                $message = $return . 'Please try again.';
                $level = mvcSession::MESSAGE_ERROR;
            }
        } catch (Exception $e) {
            systemLog::error($e->getMessage());
            $message = $e->getMessage();
            $level = mvcSession::MESSAGE_ERROR;
        }

        $oView = new grantsView($this);
        $oView->sendJsonResult($message, $level);
    }

    /*
     * 
     */

    function editGrant() {
        $inGrantID = (int) $this->getActionFromRequest(false, 1);

        $oUserMovieGrants = mofilmUserMovieGrants::getInstance($inGrantID);
        $this->getModel()->getGrantsSearch()->addEvent($oUserMovieGrants->getGrants()->getSource()->getEventID());
        $this->getModel()->getGrantsSearch()->setUserID($oUserMovieGrants->getUserID());
        $this->getModel()->getGrantsSearch()->setOffset(0);
        $this->getModel()->getGrantsSearch()->setLimit(30);
        $grantProductID = $this->getModel()->getProductID($inGrantID);
        $oView = new grantsView($this);
        $oView->grantEdit($grantProductID, $inGrantID);
    }

    /*
     * 
     */

    function doEditGrant() {
        try {
            $data = $this->getInputManager()->doFilter();
            systemLog::message($data);
            systemLog::message($_POST);
            $return = $this->getModel()->approvalProcess($data, $this->getRequest()->getSession()->getUser()->getID());

            if ($return == true) {
                $message = 'The grants has been saved successfully.';
                $level = mvcSession::MESSAGE_OK;
            } else {
                $message = $return . 'Please try again.';
                $level = mvcSession::MESSAGE_ERROR;
            }
        } catch (Exception $e) {
            systemLog::error($e->getMessage());
            $message = $e->getMessage();
            $level = mvcSession::MESSAGE_ERROR;
        }

        $oView = new grantsView($this);
        $oView->sendJsonResult($message, $level);
    }

    /*
     * 
     */

    function uploadDocs($inUserMovieGrantID = null) {
        $this->addInputFilters();
        $data = $this->getInputManager()->doFilter();
        //print_r($data);exit;


        $inUserID = $this->getModel()->getGrantUser($data['GrantID']);
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
        } catch (mvcFileUploadNoFileUploadedException $e) {
            systemLog::warning($e->getMessage());
            return null;
        } catch (mvcFileUploadException $e) {
            systemLog::warning($e->getMessage());
            if ($oFileUpload->getUploadedFiles()->getCount() == 0) {
                $return = null;
            } else {
                $return = false;
            }
        }

        $oFiles = $oFileUpload->getUploadedFiles();

        if ($oFiles->getCount() > 0) {
            /* @var mvcFileObject $oFile */
            foreach ($oFiles as $oFile) {
                systemLog::message('Uploading File for ' . $oFile->getUploadKey());
                $path = mofilmConstants::getGrantDocsFolder() . $inUserID . system::getDirSeparator() . $inUserMovieGrantID . system::getDirSeparator() . $oFile->getUploadKey() . system::getDirSeparator();
                $finalPath = $path . $oFile->getName();
                if (!file_exists($path)) {
                    mkdir($path, 0777, TRUE);
                }
                $bytes = file_put_contents($finalPath, $oFile->getRawFileData());
                systemLog::notice("Wrote $bytes bytes to the file system for grantDocs " . $oFile->getName());

                if ($oFile->getUploadKey() == 'UploadGrantApprovalForm') {
                    $UploadGrantApprovalForm = $finalPath;
                } elseif ($oFile->getUploadKey() == 'UploadBankDetails') {
                    $UploadBankDetails = $finalPath;
                } elseif ($oFile->getUploadKey() == 'UploadPhotoIDProof') {
                    $UploadPhotoIDProof = $finalPath;
                } elseif ($oFile->getUploadKey() == 'UploadReceipts') {
                    $UploadReceipts = $finalPath;
                } elseif ($oFile->getUploadKey() == 'ApplicationAssets') {
                    $ApplicationAssets = $finalPath;
                }
            }
        }

        $oUserMovieGrants = new mofilmUserMovieGrants($data['GrantID']);
        if (isset($UploadGrantApprovalForm)) {
            $oUserMovieGrants->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOCUMENT_AGREEMENT_PATH, $UploadGrantApprovalForm);
        }
        if (isset($UploadBankDetails)) {
            $oUserMovieGrants->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOCUMENT_BANK_DETAILS_PATH, $UploadBankDetails);
        }
        if (isset($UploadPhotoIDProof)) {
            $oUserMovieGrants->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOCUMENT_IDPROOF_PATH, $UploadPhotoIDProof);
        }
        if (isset($UploadReceipts)) {
            $oUserMovieGrants->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_DOCUMENT_RECEIPTS_PATH, $UploadReceipts);
        }
        if (isset($ApplicationAssets)) {
            $oUserMovieGrants->getParamSet()->setParam(mofilmUserMovieGrants::PARAM_GRANT_ASSETS_PATH, $ApplicationAssets);
        }

        $oUserMovieGrants->getParamSet()->save();

        $this->getRequest()->getSession()->setStatusMessage('Files Uploaded', mvcSession::MESSAGE_OK);
        $this->redirect(self::ACTION_EDIT . '/' . $data['GrantID']);
    }

    /*
     * 
     */

    function search() {
        $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
        $data = $this->getInputManager()->doFilter();
        $this->addInputToModel($data, $this->getModel());
        if ($data['buttonname'] == 'PDF') {
            if (is_array($data['selectedpdfs'])) {
                $return = $this->generateMultiplePDF($data['selectedpdfs']);
                if ($return == FALSE) {
                    $this->getRequest()->getSession()->setStatusMessage('An error occured while processing, please try again.');
                }
            } else {
                $this->getRequest()->getSession()->setStatusMessage('Select atleast one application to generate PDF');
            }
        }
        /*
         *  Code Added to Reject Grants Applications
         */
        if ($data['buttonname'] == 'REJECT') {
            if (is_array($data['selectedpdfs'])) {
                $return = $this->rejectGrantApplication($data['selectedpdfs']);
                if ($return == FALSE) {
                    $this->getRequest()->getSession()->setStatusMessage('An error occured while processing, please try again.');
                }
            } else {
                $this->getRequest()->getSession()->setStatusMessage('Select atleast one application to Reject');
            }
        }

        if ($data['buttonname'] == 'Email') {
            if (is_array($data['selectedpdfs'])) {
                foreach ($data['selectedpdfs'] as $inGrantID) {
                    $return = $this->getModel()->sendApprovalEmail($inGrantID);
                    if (!$return) {
                        $notSent[] = $inGrantID;
                    } else {
                        $oGrant = mofilmUserMovieGrants::getInstance($inGrantID);
                        $grantAction = 'Approved';
                        $params = array(
                            'http' => array(
                                'method' => 'POST',
                                'header' => 'Content-type: application/x-www-form-urlencoded',
                                'content' => 'userID=' . $oGrant->getUserID() . '&grantID=' . $inGrantID . '&grantAction=' . $grantAction . '&approvalFile=' . $return
                            )
                        );

                        $context = stream_context_create($params);
                        $emailUrl = system::getConfig()->getParam('mofilm', 'emailMofilmUri')->getParamValue() . '/grant/?';
                        $result = file_get_contents($emailUrl, false, $context);
                    }
                }

                if (is_array($notSent)) {
                    $this->getRequest()->getSession()->setStatusMessage('Not sent to the following Grant IDs ' . implode(', ', $notSent));
                    systemLog::message('Not sent to the following Grant IDs ' . implode(', ', $notSent));
                }
            } else {
                $this->getRequest()->getSession()->setStatusMessage('Select atleast one application to send Acceptance Email');
            }
        }

        $oView = new grantsView($this);
        $oView->getObjectListView();
    }

    /**
     * @see mvcControllerBase::addInputFilters()
     */
    function addInputFilters() {
        $this->getInputManager()->addFilter('Rating', utilityInputFilter::filterInt());
        $this->getInputManager()->addFilter('GrantID', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('GrantedAmount', utilityInputFilter::filterFloat());
        $this->getInputManager()->addFilter('GrantedStatus', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('ModeratorComments', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('buttonname', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('selectedpdfs', utilityInputFilter::filterStringArray());
        $this->getInputManager()->addFilter('MovieID', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('DocumentAgreement', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('DocumentBankDetails', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('DocumentIdProof', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('DocumentReceipts', utilityInputFilter::filterString());

        if ($this->getAction() == self::ACTION_SEARCH) {
            $this->getInputManager()->addFilter('Status', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('Offset', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('Limit', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('UserID', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('OrderBy', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('OrderDir', utilityInputFilter::filterInt());
        }

        if ($this->getAction() == self::ACTION_DO_SEND_EMAIL) {
            $this->getInputManager()->addFilter('FilmMakerID', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('EmailMessage', utilityInputFilter::filterString());
        }
    }

    /**
     * @see mvcControllerBase::addInputToModel()
     */
    function addInputToModel($inData, $inModel) {
        if (!$inData['Limit'] || $inData['Limit'] > 30) {
            $inData['Limit'] = 30;
        }
        if (!$inData['Offset'] || $inData['Offset'] < 0) {
            $inData['Offset'] = 0;
        }
        if ($this->getAction() == self::ACTION_SEARCH || $this->getAction() == self::ACTION_LIST) {
            /*
             * Restrict search to only events / sources user can see unless they can search
             */
            $inModel->getGrantsSearch()->addEvent($inData['EventID']);

            if ($inData['EventID'] == 0) {
                $res = mofilmSource::listOfDistinctSourceIDsByName($inData['SourceID']);
                foreach ($res as $re) {
                    $inModel->getGrantsSearch()->addSource($re);
                }
            } else {
                $inModel->getGrantsSearch()->addSource($inData['SourceID']);
            }

            if (in_array($inData['Status'], mofilmUserMovieGrants::getAvailableGrantsStatus())) {
                $inModel->getGrantsSearch()->setStatus($inData['Status']);
            }

            if (isset($inData['UserID']) && ( $inData['UserID'] > 0 )) {
                $inModel->getGrantsSearch()->setUserID($inData['UserID']);
            }

            $inModel->getGrantsSearch()->setOffset($inData['Offset']);
            $inModel->getGrantsSearch()->setLimit($inData['Limit']);

            if (array_key_exists('OrderBy', $inData) && strlen($inData['OrderBy']) > 1) {
                $inModel->getGrantsSearch()->setOrderBy($inData['OrderBy']);
            }
            if (array_key_exists('OrderDir', $inData) && is_numeric($inData['OrderDir'])) {
                $inModel->getGrantsSearch()->setOrderDirection($inData['OrderDir']);
            }
            unset($inData['Offset'], $inData['Limit'], $inData['OrderDir'], $inData['OrderBy']);
            $this->setSearchQuery($inData);
        }
    }

    /**
     * Fetches the model
     *
     * @return profileModel
     */
    function getModel() {
        if (!parent::getModel()) {
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

    /**
     * Returns the search query parameters as an array
     *
     * @return array
     */
    function getSearchQuery() {
        return $this->_SearchQuery;
    }

    /**
     * Returns the search query as a string
     * 
     * @return string
     */
    function getSearchQueryAsString() {
        return http_build_query($this->getSearchQuery());
    }

    /**
     * Set $_SearchQuery to $inSearchQuery
     *
     * @param array $inSearchQuery
     * @return usersController
     */
    function setSearchQuery($inSearchQuery) {
        if ($inSearchQuery !== $this->_SearchQuery) {
            $this->_SearchQuery = $inSearchQuery;
            $this->setModified();
        }
        return $this;
    }

    function rejectGrantApplication($grantIds) {
        foreach ($grantIds as $inGrantID) {
            $status = $this->getModel()->GrantRejectionEmail($inGrantID);
            $oUserMovieGrants = mofilmUserMovieGrants::getInstance($inGrantID);
            $oUserMovieGrants->setStatus('Rejected');
        }
        return;
    }

    /**
     * Send the zip file to the user via x-sendfile
     * 
     * @param file name and file path
     * @return void 
     */
    private function sendFile($filename, $filepath) {
        $name = basename($filename);

        /*
         * IE is rubbish and doesn't like multiple . (dots) in the file name
         */
        if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
            $name = preg_replace('/\./', '%2e', $name, substr_count($name, '.') - 1);
        }

        $filesize = filesize($filepath . DIRECTORY_SEPARATOR . $name);

        /*
         * Fetch mime-type from actual file lookup
         */
        $file = escapeshellarg($filepath . DIRECTORY_SEPARATOR . $name);
        $mime = shell_exec("file -bi " . $file);

        if (!preg_match('/\//', $mime)) {
            $mime = "application/octet-stream";
        }

        /*
         * Send the file to the browser via X-Sendfile (apache module) if supported
         */
        header("HTTP/1.0 200 OK");
        header("Content-Description: File Transfer");
        header("Content-Type: $mime");
        header("Content-Length: $filesize");
        header("Content-Disposition: attachment; filename=\"$name\"");
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");

        if (function_exists('apache_get_modules') && in_array('mod_xsendfile', apache_get_modules())) {
            header("X-Sendfile: " . $filepath . DIRECTORY_SEPARATOR . $name);
        } else {
            /*
             * Ensure output buffer is clean and stopped
             */
            ob_clean();
            flush();

            //readfile($inFile->getFileLocation());
            $file = @fopen($filepath . $name, "rb");
            while (!feof($file)) {
                print(@fread($file, 1024 * 8));
                ob_flush();
                flush();
            }
        }

        chmod($filepath . $filename, 0777);
        unlink($filepath . $filename);
    }

}
