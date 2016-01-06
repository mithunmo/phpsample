<?php

/**
 * videosController
 *
 * Stored in videosController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category videosController
 * @version $Rev: 326 $
 */

/**
 * videosController
 *
 * videosController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category videosController
 */
class videosController extends mvcController {

    const ACTION_VIEW = 'view';
    const ACTION_SEARCH = 'doSearch';
    const ACTION_SOLR_SEARCH = 'doSolrSearch';
    const ACTION_RATE = 'rate';
    const ACTION_STATUS = 'status';
    const ACTION_WATCH = 'watch';
    const ACTION_REVIEW = 'review';
    const ACTION_EDIT = 'edit';
    const ACTION_DO_EDIT = 'doEdit';
    const ACTION_DO_MOD_COMMENT = 'doModComment';
    const ACTION_DO_COMMENT = 'doComment';
    const ACTION_DO_AWARD_UPDATE = 'doAwardUpdate';
    const ACTION_CHANGE_USER = 'changeUser';
    const ACTION_DO_CHANGE_USER = 'doChangeUser';
    const ACTION_DO_PHOTO_DOWNLOAD = 'doPhotoDownload';
    const ACTION_AWARD_LIST = 'awardList';
    const ACTION_COMMENT_LIST = 'commentList';
    const VIEW_MOVIE_STATS = 'movieStats';
    const VIEW_MOVIE_REVIEW_COUNT = 'movieReviewCount';
    const ACTION_DELETE_MOVIE_TAG = 'deleteMovieTag';
    const ACTION_ADD_MOVIE_TAG = 'addMovieTag';
    const VIEW_HD_VIDEO = 'HDVideo';

    /**
     * Stores $_SearchQuery
     *
     * @var array
     * @access protected
     */
    protected $_SearchQuery;
    protected $_CorporateSerachQuery;
    protected $_BrandSerachQuery;
    protected $_ProductSearchQuery;

    function getCorporateQuery() {
        return $this->_CorporateSerachQuery;
    }

    function setCorporateQuery($corporateQuery) {
        $this->_CorporateSerachQuery = $corporateQuery;
    }

    function getBrandQuery() {
        return $this->_BrandSerachQuery;
    }

    function setBrandQuery($brandQuery) {
        $this->_BrandSerachQuery = $brandQuery;
    }

    function getProductQuery() {
        return $this->_ProductSearchQuery;
    }

    function setProductQuery($productQuery) {
        $this->_ProductSearchQuery = $productQuery;
    }

    /**
     * @see mvcControllerBase::initialise()
     */
    function initialise() {
        parent::initialise();
        $this->setDefaultAction(self::ACTION_VIEW);
        $this->getControllerActions()
                ->addAction(self::ACTION_VIEW)
                ->addAction(self::ACTION_SEARCH)
                ->addAction(self::ACTION_RATE)
                ->addAction(self::ACTION_STATUS)
                ->addAction(self::ACTION_DO_EDIT)
                ->addAction(self::ACTION_EDIT)
                ->addAction(self::ACTION_REVIEW)
                ->addAction(self::ACTION_WATCH)
                ->addAction(self::ACTION_DO_MOD_COMMENT)
                ->addAction(self::ACTION_DO_COMMENT)
                ->addAction(self::ACTION_DO_AWARD_UPDATE)
                ->addAction(self::ACTION_CHANGE_USER)
                ->addAction(self::ACTION_DO_CHANGE_USER)
                ->addAction(self::ACTION_DELETE_MOVIE_TAG)
                ->addAction(self::ACTION_SOLR_SEARCH)
                ->addAction(self::ACTION_ADD_MOVIE_TAG)
                ->addAction(self::ACTION_DO_PHOTO_DOWNLOAD);

        /*
         * Add ajax helper actions
         */
        $this->getControllerActions()
                ->addAction(self::ACTION_AWARD_LIST)
                ->addAction(self::ACTION_COMMENT_LIST);

        $this->getControllerViews()
                ->addView(self::VIEW_MOVIE_STATS)
                ->addView(self::VIEW_MOVIE_REVIEW_COUNT)
                ->addView(self::VIEW_HD_VIDEO);

        $this->setSearchQuery(array());
    }

    /**
     * @see mvcControllerBase::launch()
     */
    function launch() {
        switch ($this->getAction()) {
            case self::ACTION_REVIEW: $this->review();
                break;
            case self::ACTION_RATE: $this->rate();
                break;
            case self::ACTION_STATUS: $this->status();
                break;
            case self::ACTION_DO_MOD_COMMENT: $this->moderationComment();
                break;
            case self::ACTION_DO_COMMENT: $this->doComment();
                break;
            case self::ACTION_DO_AWARD_UPDATE: $this->doAwardUpdate();
                break;

            case self::ACTION_CHANGE_USER: $this->changeUserAction();
                break;
            case self::ACTION_DO_CHANGE_USER: $this->doChangeUserAction();
                break;

            case self::ACTION_AWARD_LIST: $this->awardListAction();
                break;
            case self::ACTION_COMMENT_LIST: $this->commentListAction();
                break;
            case self::ACTION_EDIT:
            case self::ACTION_WATCH:
                $movieID = $this->getActionFromRequest(false, 1);
                $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
                $data = $this->getInputManager()->doFilter();

                $this->addInputToModel($data, $this->getModel());
                $this->getModel()->setMovieID($movieID);

                if ($this->getModel()->getMovie()) {
                    $oView = new videosView($this);
                    if ($this->getAction() == self::ACTION_EDIT && $this->hasAuthority('videosController.edit')) {
                        systemLog::message('User is editing movie: ' . $movieID);
                        $oView->showEditPage();
                    } else {
                        $oView->showWatchPage();
                    }
                } else {
                    $this->getRequest()->getSession()->setStatusMessage('Failed to locate a movie with ID ' . $movieID, mvcSession::MESSAGE_ERROR);
                    $this->redirect($this->buildUriPath(self::ACTION_VIEW));
                }
                break;

            case self::ACTION_DO_EDIT:
                //  print_r($_POST); exit;
                $brandStrArray = explode('-', $_POST['BrandID']);
                $data = $this->getInputManager()->doFilter();
                $sourceDetails = $this->getModel()->getSourceByBrandID($brandStrArray[0], $data['EventID']);
                $data['SourceID'] = $sourceDetails['SourceID'];
                try {
                    $this->addInputToModel($data, $this->getModel());
                    //$this->getModel()->switchMovieTag();
                    $this->getModel()->getUpdateIndtag($brandStrArray[0],$_POST['Indtags'][0]);
                    
                    if (mofilmMovieManager::getInstanceByID($data['MovieID'])->getStatus() == 'Pending') {
                        $send = TRUE;
                    }

                    $this->getModel()->getMovie()->save();

                    if ($this->getModel()->getMovie()->getStatus() == "Approved" || $this->getModel()->getMovie()->getStatus() == "Pending") {
                        $this->getModel()->linkUserMovieGrants($data['SourceID'], $this->getModel()->getMovie()->getUserID(), $data['MovieID']);
                    }

                    if ($send && $data['Status'] == "Approved") {
                        systemLog::message('Send Approved Email to movieID : ' . $data['MovieID']);
                        $this->getModel()->sendApprovedEmail();
                    }

                    if (isset($_FILES['ccaFile']['name']) && $_FILES['ccaFile']['error'] == 0) {
                        $this->uploadCcaFile($data['MovieID']);
                    }

                    systemLog::message('User changed movie ' . $data['MovieID'] . ' successfully');

                    $this->getRequest()->getSession()->setStatusMessage('Changes saved successfully', mvcSession::MESSAGE_OK);
                } catch (Exception $e) {
                    systemLog::error($e->getMessage());
                    systemLog::error($e->getTraceAsString());
                    $this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
                }
                $this->redirect($this->buildUriPath(self::ACTION_EDIT, $this->getModel()->getMovieID()));
                break;

            case self::ACTION_DELETE_MOVIE_TAG: $this->deleteMovieTag();
                break;

            case self::ACTION_ADD_MOVIE_TAG: $this->addMovieTag();
                break;
            case self::ACTION_SOLR_SEARCH: $this->videoSolrSearch();
                break;
            case self::ACTION_DO_PHOTO_DOWNLOAD: $this->doPhotoDownload();
                break;
            case self::ACTION_SEARCH:

                $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
                $data = $this->getInputManager()->doFilter();
                $brandStrArray = explode('-', $data['BrandID']);
                $this->setBrandQuery($brandStrArray[0]);
                $this->setCorporateQuery($data['CorporateID']);
            default:
                $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
                $data = $this->getInputManager()->doFilter();

                $this->addInputToModel($data, $this->getModel());

                $oView = new videosView($this);
                $oView->showVideosPage();
        }
    }

    /**
     * 
     * Uploads CCa Files and saves the path in movieAsset table
     * 
     */
    function uploadCcaFile($inMovieID = Null) {
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
            if ($oFile instanceof mvcFileObject) {

                $oMovie = mofilmMovieManager::getInstanceByID($inMovieID);
                $oMovieAsset = $oMovie->getAssetSet()->getObjectByAssetType(mofilmMovieAsset::TYPE_CCA)->getFirst();
                $inUserID = $oMovie->getUserID();
                systemLog::message($oFile);
                systemLog::message('Uploading File for ' . $oFile->getUploadKey());
                $path = mofilmConstants::getCcaDocsFolder() . $inUserID . system::getDirSeparator() . $inMovieID . system::getDirSeparator();
                $finalPath = $path . $oFile->getName();
                if (!file_exists($path)) {
                    mkdir($path, 0777, TRUE);
                }
                $bytes = file_put_contents($finalPath, $oFile->getRawFileData());
                systemLog::notice("Wrote $bytes bytes to the file system for grantDocs " . $oFile->getName());

                if ($oMovieAsset instanceof mofilmMovieAsset && $oMovieAsset->getID() > 0) {
                    $oMovieAsset->setFilename($finalPath);
                    $oMovieAsset->setExt(pathinfo($finalPath, PATHINFO_EXTENSION));
                } else {
                    unset($oMovieAsset);
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
        } catch (mvcFileUploadNoFileUploadedException $e) {
            systemLog::warning($e->getMessage());
            return null;
        } catch (mvcFileUploadException $e) {
            systemLog::warning($e->getMessage());
            return null;
        }
    }

    /**
     * Handles standalone view requests into the movies controller
     * 
     * @param array $params
     * @return string
     */
    function fetchStandaloneView($params = array()) {
        switch ($params['view']) {
            case self::VIEW_MOVIE_STATS:
                $oView = new videosView($this);
                return $oView->getMovieStatsView();
                break;

            case self::VIEW_MOVIE_REVIEW_COUNT:
                $this->getModel()->setSearchForReview();

                $oView = new videosView($this);
                return $oView->getMovieReviewCountView();
                break;

            case self::VIEW_HD_VIDEO:
                $oView = new videosView($this);
                return $oView->playHDVideo();
                break;
        }
    }

    /**
     * Handles the review action
     * 
     * @return void
     */
    function review() {
        $movieID = $this->getActionFromRequest(false, 1);
        $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
        $data = $this->getInputManager()->doFilter();

        $this->addInputToModel($data, $this->getModel());
        $this->getModel()->setMovieID($movieID);
        $this->getModel()->setSearchForReview();

        $oView = new videosView($this);
        $oView->showReviewPage();
    }

    /**
     * Handles recording of ratings
     * 
     * @return void
     */
    function rate() {
        $data = $this->getInputManager()->doFilter();
        $this->addInputToModel($data, $this->getModel());

        try {
            $this->getModel()->rateMovie($data);
            $message = 'Your rating has been recorded successfully.';
            $level = mvcSession::MESSAGE_OK;
        } catch (Exception $e) {
            systemLog::error($e->getMessage());
            $message = $e->getMessage();
            $level = mvcSession::MESSAGE_ERROR;
        }

        if ($this->getRequest()->isAjaxRequest()) {
            $oView = new videosView($this);
            $oView->sendRatingResult();
        } else {
            $this->getRequest()->getSession()->setStatusMessage($message, $level);
            $this->redirect($this->buildUriPath(self::ACTION_WATCH, $this->getModel()->getMovieID()));
        }
    }

    /**
     * Handles changing the movie status
     * 
     * @return void
     */
    function status() {
        $data = array(
            'MovieID' => $this->getActionFromRequest(false, 1),
            'Status' => $this->getActionFromRequest(false, 2)
        );

        $email = $this->getActionFromRequest(false, 3);

        $this->addInputToModel($data, $this->getModel());

        try {
            if ($email == 'sendEmail') {
                systemLog::message('Send Approved Email to movieID : ' . $data['MovieID']);
                $this->getModel()->sendApprovedEmail();
            }

            $this->getModel()->setStatus($data);
            systemLog::message('User changed movie ' . $data['MovieID'] . ' status to ' . $data['Status']);

            $message = 'Movie status changed successfully.';
            $level = mvcSession::MESSAGE_OK;
        } catch (Exception $e) {
            systemLog::error($e->getMessage());
            $message = $e->getMessage();
            $level = mvcSession::MESSAGE_ERROR;
        }

        if ($this->getRequest()->isAjaxRequest()) {
            $oView = new videosView($this);
            $oView->sendJsonResult($message, $level);
        } else {
            $this->getRequest()->getSession()->setStatusMessage($message, $level);
            $this->redirect($this->buildUriPath(self::ACTION_WATCH, $this->getModel()->getMovieID()));
        }
    }

    /**
     * Handles setting the moderation comment
     * 
     * @return void
     */
    function moderationComment() {
        $data = $this->getInputManager()->doFilter();
        $this->addInputToModel($data, $this->getModel());

        try {
            $this->getModel()->setModerationComment($data['ModComment']);
            systemLog::message('User added moderation comment to movie ' . $data['MovieID']);

            $message = 'Moderation comment set successfully.';
            $level = mvcSession::MESSAGE_OK;
        } catch (Exception $e) {
            systemLog::error($e->getMessage());
            $message = $e->getMessage();
            $level = mvcSession::MESSAGE_ERROR;
        }

        if ($this->getRequest()->isAjaxRequest()) {
            $oView = new videosView($this);
            $oView->sendJsonResult($message, $level);
        } else {
            $this->getRequest()->getSession()->setStatusMessage($message, $level);
            $this->redirect($this->buildUriPath(self::ACTION_REVIEW, $this->getModel()->getMovieID()));
        }
    }

    /**
     * Handles posting a reviewer / moderator comment
     * 
     * @return void
     */
    function doComment() {
        $data = $this->getInputManager()->doFilter();
        $this->addInputToModel($data, $this->getModel());

        try {
            $this->getModel()->addCommentToMovie($data['Comment']);
            systemLog::message('User posted comment to movie ' . $data['MovieID']);

            $message = 'Your comment has been added successfully.';
            $level = mvcSession::MESSAGE_OK;
        } catch (Exception $e) {
            systemLog::error($e->getMessage());
            $message = $e->getMessage();
            $level = mvcSession::MESSAGE_ERROR;
        }

        if ($this->getRequest()->isAjaxRequest()) {
            $oView = new videosView($this);
            $oView->sendJsonResult($message, $level);
        } else {
            $this->getRequest()->getSession()->setStatusMessage($message, $level);
            $this->redirect($this->buildUriPath(self::ACTION_EDIT, $this->getModel()->getMovieID()));
        }
    }

    /**
     * Handles adding an award to the movie
     * 
     * @return void
     */
    function doAwardUpdate() {
        $data = $this->getInputManager()->doFilter();
        $this->addInputToModel($data, $this->getModel());

        if (isset($data['bocAward']) && $data['bocAward'] == 'BestOfClients' && $this->getModel()->checkBestOfClientExist($data)) {
            try {

                $this->getModel()->addBestClientAwardToMovie($data);
                systemLog::message('User added award ' . $data['Type'] . ' to movie ' . $data['MovieID']);

                $message = 'The award has been recorded successfully.';
                $level = mvcSession::MESSAGE_OK;
            } catch (Exception $e) {
                systemLog::error($e->getMessage());
                $message = $e->getMessage();
                $level = mvcSession::MESSAGE_ERROR;
            }
        } else {
            if (!$this->getModel()->checkBestOfClientExist($data) && $data['bocAward'] == '') {
                $this->getModel()->removeBestOfClient($data);
                $message = 'The Best Of Client award has been deleted successfully.';
                $level = mvcSession::MESSAGE_OK;
            } else {

                try {

                    $this->getModel()->addAwardToMovie($data);
                    systemLog::message('User added award ' . $data['Type'] . ' to movie ' . $data['MovieID']);

                    $message = 'The award has been recorded successfully.';
                    $level = mvcSession::MESSAGE_OK;
                } catch (Exception $e) {
                    systemLog::error($e->getMessage());
                    $message = $e->getMessage();
                    $level = mvcSession::MESSAGE_ERROR;
                }
            }
        }

        if ($this->getRequest()->isAjaxRequest()) {
            $oView = new videosView($this);
            $oView->sendJsonResult($message, $level);
        } else {
            $this->getRequest()->getSession()->setStatusMessage($message, $level);
            $this->redirect($this->buildUriPath(self::ACTION_EDIT, $this->getModel()->getMovieID()));
        }
    }

    /**
     * Handles the change user actions including searching for a new user
     *
     * @return void
     */
    function changeUserAction() {
        $movieID = $this->getActionFromRequest(false, 1);
        $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
        $data = $this->getInputManager()->doFilter();

        $this->addInputToModel($data, $this->getModel());
        $this->getModel()->setMovieID($movieID);

        if ($this->getModel()->getMovie()) {
            $selectedUser = $this->getActionFromRequest(false, 2);
            $this->getModel()->setSwitchUserID($selectedUser);

            $oView = new videosView($this);
            if ($selectedUser && $this->getModel()->getSwitchUser()) {
                $oView->showChangeUserConfirmationPage();
            } else {
                $oView->showChangeUserPage();
            }
        } else {
            $this->getRequest()->getSession()->setStatusMessage(
                    'Invalid or missing movieID. You must select a movie before you can change the user.', mvcSession::MESSAGE_ERROR
            );
            $this->redirect($this->buildUriPath(self::ACTION_VIEW));
        }
    }

    /**
     * Handles actually committing the new user to the movie
     *
     * @return void
     */
    function doChangeUserAction() {
        $movieID = $this->getActionFromRequest(false, 1);
        $data = $this->getInputManager()->doFilter();

        $this->addInputToModel($data, $this->getModel());
        $this->getModel()->setMovieID($movieID);

        try {
            if ($this->getModel()->getMovie()) {
                $this->getModel()->switchUser();
                systemLog::message('Switched movie user successfully to ' . $this->getModel()->getMovie()->getUserID());

                $this->redirect($this->buildUriPath(self::ACTION_EDIT, $this->getModel()->getMovieID()));
            } else {
                $this->getRequest()->getSession()->setStatusMessage(
                        'Invalid or missing movieID. You must select a movie before you can change the user.', mvcSession::MESSAGE_ERROR
                );
                $this->redirect($this->buildUriPath(self::ACTION_VIEW));
            }
        } catch (Exception $e) {
            systemLog::error($e->getMessage());
            $this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);

            $this->redirect($this->buildUriPath(self::ACTION_EDIT, $this->getModel()->getMovieID()));
        }
    }

    /**
     * Returns the specified movies award list
     *
     * @return void
     */
    function awardListAction() {
        $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
        $data = $this->getInputManager()->doFilter();
        $this->addInputToModel($data, $this->getModel());

        if ($this->getRequest()->isAjaxRequest()) {
            $oView = new videosView($this);
            $oView->getAwardList();
        } else {
            $this->redirect($this->buildUriPath(self::ACTION_EDIT, $data['MovieID']));
        }
    }

    /**
     * Returns the specified movies comment list
     *
     * @return void
     */
    function commentListAction() {
        $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
        $data = $this->getInputManager()->doFilter();
        $this->addInputToModel($data, $this->getModel());

        if ($this->getRequest()->isAjaxRequest()) {
            $oView = new videosView($this);
            $oView->getCommentList();
        } else {
            $this->redirect($this->buildUriPath(self::ACTION_EDIT, $data['MovieID']));
        }
    }

    /**
     * Deletes the movie Tag / Genres
     * 
     * @param int $data['MovieID']
     * @param int $data['tagID']
     * @param int $data['tagCategory']
     * 
     * @return json data
     */
    function deleteMovieTag() {
        if ($this->getRequest()->isAjaxRequest()) {
            $data = $this->getInputManager()->doFilter();
            $oMovieTag = new mofilmMovieTagSet();
            $oMovieTag->setMovieID($data['MovieID']);
            $oMovieTag->setTagID($data['tagID']);
            $res = $oMovieTag->deleteByTagAndMovieID();

            if ($data['tagCategory'] == 'adminGenres') {
                $ret['id'] = mofilmTag::getInstance($data['tagID'])->getID();
                $ret['name'] = mofilmTag::getInstance($data['tagID'])->getName();
                $ret['status'] = 1;
            } else {
                $ret['status'] = 0;
            }

            echo json_encode($ret);
        }
    }

    /*
     * Adding Genres to movie
     * 
     * @param int $data['MovieID']
     * @param int $data['tagID']
     * 
     * @return json data
     */

    function addMovieTag() {
        if ($this->getRequest()->isAjaxRequest()) {
            $data = $this->getInputManager()->doFilter();
            $oMovieTag = new mofilmMovieTagSet();
            $oMovieTag->setTagID($data['tagID']);
            $oMovieTag->setMovieID($data['MovieID']);
            $res = $oMovieTag->save();

            if ($res) {
                $ret['id'] = mofilmTag::getInstance($data['tagID'])->getID();
                $ret['name'] = mofilmTag::getInstance($data['tagID'])->getName();
                $ret['status'] = 1;
            } else {
                $ret['status'] = 0;
            }

            echo json_encode($ret);
        }
    }

    /**
     * Performs the video search through solr
     * 
     */
    function videoSolrSearch() {

        $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
        $data = $this->getInputManager()->doFilter();
        systemLog::message("ddd".$data['Offset']);
        if(isset($data['Offset'])){
            $this->getModel()->getSolrVideoSearch()->setStart($data['Offset']);
        } else {
            $this->getModel()->getSolrVideoSearch()->setStart(0);
        }

        if (is_numeric($data['Keywords'])) {
            $data['MovieID'] = (int) $data['Keywords'];
            $data["Keywords"] = null;
            $this->getModel()->getSolrVideoSearch()->setMovieID($data["MovieID"]);
        } else if (isset($data["Keywords"]) && strlen($data["Keywords"]) > 2 && $data["Keywords"] != "Search by keywords") {
            $this->getModel()->getSolrVideoSearch()->setKeyword(urlencode($_GET["Keywords"]));
        } else {
            $this->getModel()->getSolrVideoSearch()->setKeyword("*:*");
        }


        $this->getModel()->getSolrVideoSearch()->setFavorites(($data['Favourites'] == 1 ? true : false));
        $this->getModel()->getSolrVideoSearch()->setTags(($data['Tags'] == 1 ? true : false));


        $this->getModel()->getSolrVideoSearch()->setTitles(($data['onlyTitles'] == 1 ? true : false));

        if (isset($data["EventID"])) {

            $this->getModel()->getSolrVideoSearch()->setEventID($data["EventID"]);
        }


        if (isset($data["CorporateID"])) {
            $this->setCorporateQuery($data['CorporateID']);
            $this->getModel()->getSolrVideoSearch()->setCorporateID($data["CorporateID"]);
        }

        if (isset($data["BrandID"])) {
            $brandArray = explode('-', $data["BrandID"]);
            $data["BrandID"] = $brandArray[0];
            $this->setBrandQuery($brandArray[0]);
            $this->getModel()->getSolrVideoSearch()->setBrandID($data["BrandID"]);
        }

        if (isset($data["ProductID"])) {
            $this->setProductQuery($data['ProductID']);
            $this->getModel()->getSolrVideoSearch()->setProductID($data["ProductID"]);
        }

        if (isset($data["Status"])) {
            $this->getModel()->getSolrVideoSearch()->setStatus($data["Status"]);
        }

        if (isset($data["SourceID"])) {

            if (is_numeric($data["SourceID"])) {
                $this->getModel()->getSolrVideoSearch()->setSourceID($data["SourceID"]);
            } else {
                $this->getModel()->getSolrVideoSearch()->setSourceName($data["SourceID"]);
            }
        }

        if (isset($data["Award"])) {
            $this->getModel()->getSolrVideoSearch()->setType($data["Award"]);
        }

        if (isset($data["UserID"])) {
            $this->getModel()->getSolrVideoSearch()->setUserID($data["UserID"]);
        }

        if (array_key_exists('OrderBy', $data) && strlen($data['OrderBy']) > 1) {
            $this->getModel()->getSolrVideoSearch()->setOrderBy($data['OrderBy']);
        }
        if (array_key_exists('OrderDir', $data) && is_numeric($data['OrderDir'])) {
            $this->getModel()->getSolrVideoSearch()->setOrderDirection($data['OrderDir']);
        }

        unset($data['Offset'], $data['Limit'], $data['OrderBy'], $data['OrderDir']);
        $data["Keywords"] = $_GET["Keywords"];
        $this->setSearchQuery($data);

        $oView = new videosView($this);
        $oView->showSearchVideosPage();
    }

    /**
     * 
     */
    function doPhotoDownload() {
        $data = $this->getInputManager()->doFilter();

        if (count($data['downloadImage']) >= 1) {
            $oMovie = new mofilmMovie($data['MovieID']);
            $oMovieAssets = $oMovie->getAssetSet()->getObjectByAssetType('Source');

            $zipname = time();
            $dirname = mofilmConstants::getTcpdfFolder();
            mkdir($dirname . $zipname, 0777);

            $zip = new ZipArchive();

            if ($zip->open($dirname . $zipname . '.zip', ZipArchive::CREATE) == TRUE) {
                foreach ($oMovieAssets as $asset) {
                    if (in_array($asset->getID(), $data['downloadImage'])) {
                        $photoName = substr($asset->getFilename(), 27);
                        $zip->addFile('/share/content/_platform/' . $data['MovieID'] . DIRECTORY_SEPARATOR . $photoName, $photoName);
                    }
                }

                $zip->close();

                if (file_exists($dirname . $zipname . '.zip')) {
                    $this->sendZipFile($zipname, $dirname);
                }
            }
        }
        $this->redirect('/videos/edit/' . $data['MovieID']);
    }

    /**
     * @see mvcControllerBase::addInputFilters()
     */
    function addInputFilters() {
        $this->getInputManager()->addFilter('MovieID', utilityInputFilter::filterInt());
        $this->getInputManager()->addFilter('term', utilityInputFilter::filterString());
        if ($this->getAction() == self::ACTION_SEARCH || $this->getAction() == self::ACTION_REVIEW || $this->getAction() == self::ACTION_SOLR_SEARCH) {
            $this->getInputManager()->addFilter('Status', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('Offset', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('Limit', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('Display', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('Finalists', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('Keywords', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('Award', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('BrandID', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('CorporateID', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('ProductID', utilityInputFilter::filterString());
            //$this->getInputManager()->addFilter('DistinctSourceName', utilityInputFilter::filterString());			
            $this->getInputManager()->addFilter('UserID', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('Favourites', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('Tags', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('onlyTitles', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('OrderBy', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('OrderDir', utilityInputFilter::filterInt());
        } elseif ($this->getAction() == self::ACTION_RATE) {
            $this->getInputManager()->addFilter('Rating', utilityInputFilter::filterInt());
        } elseif ($this->getAction() == self::ACTION_DO_EDIT) {

            $this->getInputManager()->addFilter('Title', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('DescriEventIDption', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('Credits', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('Active', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('Runtime', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('ProductionYear', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('TrackID', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('Private', utilityInputFilter::filterInt());

            $this->getInputManager()->addFilter('Broadcast', utilityInputFilter::filterStringArray());

            $this->getInputManager()->addFilter('Data', utilityInputFilter::filterStringArray());
            $this->getInputManager()->addFilter('Tags', utilityInputFilter::filterStringArray());
            $this->getInputManager()->addFilter('Categories', utilityInputFilter::filterStringArray());
            $this->getInputManager()->addFilter('Contributors', utilityInputFilter::filterStringArray());

            $this->getInputManager()->addFilter('Comment', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('CcaVerified', utilityInputFilter::filterInt());

            $this->getInputManager()->addFilter('dist', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('channel', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('mrss_action', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('mrss_category', utilityInputFilter::filterString());
        } elseif ($this->getAction() == self::ACTION_DO_MOD_COMMENT) {
            $this->getInputManager()->addFilter('ModComment', utilityInputFilter::filterString());
        } elseif ($this->getAction() == self::ACTION_DO_COMMENT) {
            $this->getInputManager()->addFilter('Comment', utilityInputFilter::filterString());
        } elseif ($this->getAction() == self::ACTION_DO_AWARD_UPDATE) {
            $this->getInputManager()->addFilter('Award', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('bocAward', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('Position', utilityInputFilter::filterInt());
        } elseif ($this->getAction() == self::ACTION_CHANGE_USER) {
            $this->getInputManager()->addFilter('UserID', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('Offset', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
            $this->getInputManager()->addFilter('Email', utilityInputFilter::filterString());
        } elseif ($this->getAction() == self::ACTION_DO_CHANGE_USER) {
            $this->getInputManager()->addFilter('UserID', utilityInputFilter::filterInt());
        } elseif ($this->getAction() == self::ACTION_DELETE_MOVIE_TAG || $this->getAction() == self::ACTION_ADD_MOVIE_TAG) {
            $this->getInputManager()->addFilter('tagID', utilityInputFilter::filterInt());
            $this->getInputManager()->addFilter('tagCategory', utilityInputFilter::filterString());
        } elseif ($this->getAction() == self::ACTION_DO_PHOTO_DOWNLOAD) {
            $this->getInputManager()->addFilter('downloadImage', utilityInputFilter::filterStringArray());
        }
    }

    /**
     * @see mvcControllerBase::addInputToModel()
     * 
     * @param array $inData
     * @param videosModel $inModel
     */
    function addInputToModel($inData, $inModel) {
        if (!$inData['Limit'] || $inData['Limit'] > 30) {
            $inData['Limit'] = 20;
        }
        if (!$inData['Offset'] || $inData['Offset'] < 0) {
            $inData['Offset'] = 0;
        }
        if ($this->getAction() == self::ACTION_SEARCH) {
            /*
             * Restrict search to only events / sources user can see unless they can search
             */
            if ($this->hasAuthority('videosController.canSearchByEvent')) {
                $inModel->getVideoSearch()->addEvent($inData['EventID']);
                if ($this->hasAuthority('videosController.canSearchBySource')) {
                    if ($inData['EventID'] == 0) {
                        //$res = mofilmSource::listOfDistinctSourceIDsByName($inData['DistinctSourceName']);
                        $res = mofilmSource::listOfDistinctSourceIDsByName($inData['SourceID']);
                        foreach ($res as $re) {
                            $inModel->getVideoSearch()->addSource($re);
                        }
                        //$inModel->getVideoSearch()->addSourceName($inData['DistinctSourceName']);
                    } else {
                        $inModel->getVideoSearch()->addSource($inData['SourceID']);
                    }
                }
            } else {
                $inModel->getVideoSearch()->setEvents($this->getRequest()->getSession()->getUser()->getSourceSet()->getEventIDs());
            }

            if ($this->hasAuthority('videosController.canSearchByStatus') && in_array($inData['Status'], mofilmMovieManager::getAvailableMovieStatuses())) {
                $inModel->getVideoSearch()->setStatus($inData['Status']);
            }
            if (strlen($inData['Keywords']) > 2 && strtolower($inData['Keywords']) != 'search by keywords') {
                $matches = array();
                if (preg_match('/^user:(\d+)$/i', $inData['Keywords'], $matches)) {
                    $inData['UserID'] = (int) $matches[1];
                } elseif (preg_match('/^video:(\d+)$/i', $inData['Keywords'], $matches)) {
                    $inData['MovieID'] = (int) $matches[1];
                } elseif (is_numeric($inData['Keywords'])) {
                    $inData['MovieID'] = (int) $inData['Keywords'];
                } else {
                    $inModel->getVideoSearch()->setKeywords($inData['Keywords']);
                }
            }
            if ($this->hasAuthority('videosController.canSearchByAward') && in_array($inData['Award'], mofilmMovieAward::getTypes())) {
                $inModel->getVideoSearch()->setAwardType($inData['Award']);
            }

            $inModel->getVideoSearch()->setOnlyFavourites(($inData['Favourites'] == 1 ? true : false));
            $inModel->getVideoSearch()->setOnlyTags(($inData['Tags'] == 1 ? true : false));

            $inModel->getVideoSearch()->setOnlyTitles(($inData['onlyTitles'] == 1 ? true : false));
            $inModel->getVideoSearch()->setMovieID($inData['MovieID']);
            $inModel->getVideoSearch()->setUserID($inData['UserID']);
            $inModel->getVideoSearch()->setOffset($inData['Offset']);
            $inModel->getVideoSearch()->setLimit($inData['Limit']);

            if (array_key_exists('OrderBy', $inData) && strlen($inData['OrderBy']) > 1) {
                $inModel->getVideoSearch()->setOrderBy($inData['OrderBy']);
            }
            if (array_key_exists('OrderDir', $inData) && is_numeric($inData['OrderDir'])) {
                $inModel->getVideoSearch()->setOrderDirection($inData['OrderDir']);
            }

            unset($inData['Offset'], $inData['Limit'], $inData['OrderBy'], $inData['OrderDir']);
            $this->setSearchQuery($inData);
        } elseif ($this->getAction() == self::ACTION_REVIEW) {
            if ($this->hasAuthority('videosController.canSearchByEvent')) {
                $inModel->getVideoSearch()->addEvent($inData['EventID']);
            }

            $inModel->getVideoSearch()->setOffset($inData['Offset']);
            $inModel->getVideoSearch()->setLimit($inData['Limit']);
            unset($inData['Offset'], $inData['Limit']);
            $this->setSearchQuery($inData);
        } elseif (
                $this->getAction() == self::ACTION_RATE || $this->getAction() == self::ACTION_STATUS ||
                $this->getAction() == self::ACTION_DO_MOD_COMMENT || $this->getAction() == self::ACTION_DO_COMMENT ||
                $this->getAction() == self::ACTION_DO_AWARD_UPDATE || $this->getAction() == self::ACTION_AWARD_LIST ||
                $this->getAction() == self::ACTION_COMMENT_LIST
        ) {
            $inModel->setMovieID($inData['MovieID']);
        } elseif ($this->getAction() == self::ACTION_DO_AWARD_UPDATE) {
            $inModel->setMovieID($inData['MovieID']);
        } elseif ($this->getAction() == self::ACTION_DO_EDIT) {
            $inModel->setMovieID($inData['MovieID']);
            $inModel->getMovie()->setLongDesc(trim(strip_tags($_POST['Description'])));
            $inModel->getMovie()->setProductionYear($inData['ProductionYear']);
            $inModel->getMovie()->setRuntime($inData['Runtime']);
            $inModel->getMovie()->setShortDesc(trim(strip_tags($_POST['Title'])));

            if ($this->hasAuthority('canBroadcast')) {
                $oMofilmBroadCast = new mofilmMovieBroadcast();
                $broadCastDetails = $oMofilmBroadCast->getBroadCastDetails($inData['MovieID']);

                $i = 0;
                foreach ($broadCastDetails as $data) {
                    $broadCasts[] = $data->getCountryID();
                    $broadCastslog[$i]['CountryID'] = $data->getCountryID();
                    $broadCastslog[$i]['BroadcastDate'] = $data->getBroadCastDate()->getDate();
                    $i++;
                }
                foreach ($_POST['Broadcast'] as $broadCastDet) {
                    if ($broadCastDet['CountryID'] != 0 && $broadCastDet['date'] != '') {
                        $oMofilmBroadCast = new mofilmMovieBroadcast();
                        $oMofilmBroadCast->setMovieID($inData['MovieID']);
                        $oMofilmBroadCast->setUserID($this->getRequest()->getSession()->getUser()->getID());
                        $oMofilmBroadCast->setCountryID($broadCastDet['CountryID']);
                        $oMofilmBroadCast->setBroadCastDate($broadCastDet['date']);
                        $oMofilmBroadCast->setModified(true);
                        $countryObject = new mofilmTerritory($broadCastDet['CountryID']);
                        if (in_array($broadCastDet['CountryID'], $broadCasts)) {
                            $val = array_search($broadCastDet['CountryID'], $broadCasts);
                            unset($broadCasts[$val]);
                            if ($broadCastslog[$val]['CountryID'] != $broadCastDet['CountryID'] || $broadCastslog[$val]['BroadcastDate'] != $broadCastDet['date']) {
                                mofilmUserLog::factory($this->getRequest()->getSession()->getUser()->getID(), date('Y-m-d H:i:s'), mofilmUserLog::TYPE_OTHER, 'Movie:'.$inData['MovieID'].' Country Data of "'.$countryObject->getCountry().'" updated by '.$this->getRequest()->getSession()->getUser()->getFirstname().' '. $this->getRequest()->getSession()->getUser()->getSurname())->save();
                            }
                        } else {
                            mofilmUserLog::factory($this->getRequest()->getSession()->getUser()->getID(), date('Y-m-d H:i:s'), mofilmUserLog::TYPE_OTHER, 'Movie:'.$inData['MovieID'].' Country Data of "'.$countryObject->getCountry().'" added by '.$this->getRequest()->getSession()->getUser()->getFirstname().' '. $this->getRequest()->getSession()->getUser()->getSurname())->save();
                        }
                        
                        $inModel->getMovie()->getBroadcastSet()->setObject($oMofilmBroadCast);
                    }
                }
                
                $oMofilmBroadCastMovieData = new mofilmMovieBroadcast();
                $broadCastDetails = $oMofilmBroadCast->getBroadCastDetails($inData['MovieID']);
                if ($_POST['Data']['BroadcastDate'] != '' && $inModel->getMovie()->getDataSet()->getProperty(mofilmDataname::DATA_BROADCAST_DATE) != $_POST['Data']['BroadcastDate']) {
                    mofilmUserLog::factory($this->getRequest()->getSession()->getUser()->getID(), date('Y-m-d H:i:s'), mofilmUserLog::TYPE_OTHER, 'Movie:'.$inData['MovieID'].' Rights approved by '.$this->getRequest()->getSession()->getUser()->getFirstname().' '. $this->getRequest()->getSession()->getUser()->getSurname())->save();
                }
                if ($_POST['Data']['BroadcastNote'] != '' && $inModel->getMovie()->getDataSet()->getProperty(mofilmDataname::DATA_BROADCAST_NOTE) != '' && $inModel->getMovie()->getDataSet()->getProperty(mofilmDataname::DATA_BROADCAST_NOTE) != $_POST['Data']['BroadcastNote']) {
                    mofilmUserLog::factory($this->getRequest()->getSession()->getUser()->getID(), date('Y-m-d H:i:s'), mofilmUserLog::TYPE_OTHER, 'Movie:'.$inData['MovieID'].' Note updated by '.$this->getRequest()->getSession()->getUser()->getFirstname().' '. $this->getRequest()->getSession()->getUser()->getSurname().'<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="background-color: #F9F9F9; padding:3px;"><b>'. $_POST['Data']['BroadcastNote'].'</b></span>')->save();
                } elseif($_POST['Data']['BroadcastNote'] != ''  && $inModel->getMovie()->getDataSet()->getProperty(mofilmDataname::DATA_BROADCAST_NOTE) == '') {
                    mofilmUserLog::factory($this->getRequest()->getSession()->getUser()->getID(), date('Y-m-d H:i:s'), mofilmUserLog::TYPE_OTHER, 'Movie:'.$inData['MovieID'].' Note added by '.$this->getRequest()->getSession()->getUser()->getFirstname().' '. $this->getRequest()->getSession()->getUser()->getSurname() .'<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="background-color: #F9F9F9; padding:3px;"><b>'. $_POST['Data']['BroadcastNote'].'</b></span>')->save();
                }
                foreach ($broadCasts as $data) {
                    $oMofilmBroadCast1 = new mofilmMovieBroadcast();
                    $oMofilmBroadCast1->setMovieID($inData['MovieID']);
                    $oMofilmBroadCast1->setCountryID($data);
                    $oMofilmBroadCast1->setMarkForDeletion(true);
                    $inModel->getMovie()->getBroadcastSet()->setObject($oMofilmBroadCast1);
                    
                    $countryObject = new mofilmTerritory($data);
                    mofilmUserLog::factory($this->getRequest()->getSession()->getUser()->getID(), date('Y-m-d H:i:s'), mofilmUserLog::TYPE_OTHER, 'Movie:'.$inData['MovieID'].' Country Data of "'.$countryObject->getCountry().'" deleted by '.$this->getRequest()->getSession()->getUser()->getFirstname().' '. $this->getRequest()->getSession()->getUser()->getSurname())->save();
                }
                if ($_POST['broadcastChanged'] == 'Changed' && $_POST['Data']['BroadcastDate'] != '') {
                    $params = array(
                        'http' => array(
                            'method' => 'POST',
                            'header' => 'Content-type: application/x-www-form-urlencoded',
                            'content' => 'MovieID=' . $inData['MovieID'] . '&UserID=' . $this->getRequest()->getSession()->getUser()->getID() . '&adminUri=' . system::getConfig()->getParam('mofilm', 'adminMofilmUri')->getParamValue() . '/home'
                        )
                    );

                    $context = stream_context_create($params);
                    $emailUrl = system::getConfig()->getParam('mofilm', 'emailMofilmUri')->getParamValue() . '/broadcast/?';
                    $result = file_get_contents($emailUrl, false, $context);
                }
            }
            if (isset($inData['CcaVerified']) && $inData['CcaVerified'] == 1) {
                $inModel->getMovie()->getAssetSet()->getObjectByAssetType(mofilmMovieAsset::TYPE_CCA)->getFirst()->setNotes(mofilmMovieAsset::TYPE_CCA_VERIFIED);
            }

            if ($this->hasAuthority('setPrivate')) {
                $inModel->getMovie()->setPrivate($inData['Private']);
            }

            if ($this->hasAuthority('canChangeMovieCredits')) {
                $inModel->getMovie()->setCredits(trim(strip_tags($_POST['Credits'])));
            }

            if ($this->hasAuthority('canChangeMovieSource')) {
                if ($inData['SourceID'] > 0 && $inModel->getMovie()->getSource()->getID() != $inData['SourceID']) {
                    $inModel->getMovie()->getSourceSet()->reset();
                    $inModel->getMovie()->getSourceSet()->setObject(mofilmSource::getInstance($inData['SourceID']));
                }
            }

            if ($this->hasAuthority('setStatus')) {
                if (array_key_exists('Status', $inData) && strlen($inData['Status']) > 1) {
                    $inModel->getMovie()->setStatus($inData['Status']);
                }
                $inModel->getMovie()->setActive($inData['Active']);

                if (!$inModel->getMovie()->getModeratorID() && $inModel->getMovie()->getStatus() != mofilmMovie::STATUS_PENDING) {
                    $inModel->getMovie()->setModeratorID($this->getRequest()->getSession()->getUser()->getID());
                    $inModel->getMovie()->setModerated(date(system::getConfig()->getDatabaseDatetimeFormat()));
                }
            }

            if ($this->hasAuthority('editVideoData')) {
                if (is_array($inData['Data'])) {
                    $inModel->getMovie()->getDataSet()->reset();
                    foreach ($inData['Data'] as $param => $value) {
                        $inModel->getMovie()->getDataSet()->setProperty($param, $value);
                    }
                }
            }

            if ($this->hasAuthority('setTags')) {
                /* 				if ( is_array($inData['Categories']) ) {
                  $inModel->getMovie()->getCategorySet()->reset();
                  foreach ( $inData['Categories'] as $categoryID ) {
                  $inModel->getMovie()->getCategorySet()->setObject(mofilmCategory::getInstance($categoryID));
                  }
                  }
                 */
                $inData['Tags'][] = mofilmTag::getInstanceByTagAndType(mofilmEvent::getInstance($inData['EventID'])->getName(), mofilmTag::TYPE_CATEGORY)->getID();
                $sourceTagID = mofilmTag::getInstanceByTagAndType(mofilmSource::getInstance($inData['SourceID'])->getName(), mofilmTag::TYPE_CATEGORY)->getID();

                if (!(in_array($sourceTagID, $inData['Tags']))) {
                    $inData['Tags'][] = $sourceTagID;
                }

                $inData['Tags'][] = mofilmTag::getInstanceByTagAndType(date('Y', strtotime($inModel->getMovie()->getUploadDate())), mofilmTag::TYPE_CATEGORY)->getID();

                if (is_array($inData['Tags'])) {
                    $inModel->getMovie()->getTagSet()->reset();

                    foreach ($inData['Tags'] as $tagID) {
                        $inModel->getMovie()->getTagSet()->setObject(mofilmTag::getInstance($tagID));
                    }
                }
            }

            if ($this->hasAuthority('canChangeContributors')) {
                $inModel->getMovie()->setContributorInputData($inData, $inModel->getMovie(), $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue());
            }

            if ($this->hasAuthority('canChangeContributors')) {
                $oMovieChannel = mofilmMovieChannel::getInstanceByMovieID($inModel->getMovie()->getID());
                if ($inData["mrss_action"] == "new" || $inData["mrss_action"] == "update") {
                    $oMovieChannel->setMovieID($inModel->getMovie()->getID());
                    $oMovieChannel->setDistributionID($inData["dist"]);
                    $oMovieChannel->setChannelID($inData["channel"]);
                    $oMovieChannel->setCategory($inData["mrss_category"]);
                    $oMovieChannel->setAction($inData["mrss_action"]);
                    $oMovieChannel->setStatus(0);
                    $oMovieChannel->save();
                } else if ($inData["mrss_action"] == "delete") {
                    $oMovieChannel->setStatus(-1);
                }
            }

            if (array_key_exists('TrackID', $inData) && $inData['TrackID'] > 0) {
                $inModel->getMovie()->getTrackSet()->reset();
                $inModel->getMovie()->getTrackSet()->setObject(mofilmTrack::getInstance($inData['TrackID']));
            }

            if ($this->hasAuthority('canComment')) {
                if (array_key_exists('Comment', $inData) && strlen(trim($inData['Comment'])) > 0) {
                    $oComment = new mofilmMovieComment();
                    $oComment->setComment(trim($inData['Comment']));
                    $oComment->setUserID($this->getRequest()->getSession()->getUser()->getID());

                    $inModel->getMovie()->getCommentSet()->setObject($oComment);
                }
            }
        } elseif ($this->getAction() == self::ACTION_CHANGE_USER) {
            $inModel->getUserSearch()->setOffset($inData['Offset']);
            $inModel->getUserSearch()->setLimit(30);
            $inModel->getUserSearch()->setUserID($inData['UserID']);
            $inModel->getUserSearch()->setUserEmailAddress($inData['Email']);
            if (isset($inData['Name']) && strlen($inData['Name']) > 3) {
                $inModel->getUserSearch()->setKeywords($inData['Name']);
            }

            unset($inData['Offset'], $inData['Limit']);
            $this->setSearchQuery($inData);
        } elseif ($this->getAction() == self::ACTION_DO_CHANGE_USER) {
            $inModel->setSwitchUserID($inData['UserID']);
        }
    }

    /**
     * Fetches the model
     *
     * @return videosModel
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
        $oModel = new videosModel();
        $oModel->setCurrentUser($this->getRequest()->getSession()->getUser());
        $oModel->getVideoSearch()->setUser($this->getRequest()->getSession()->getUser());
        /*
         * DR 2010-09-28:
         * Not sure why I set the video search to include user sources by default. At any
         * rate, adding this here causes issues when none Mofilm clients search by source
         * basically they can't. So it's disabled for now, but not sure if this will break
         * something else.
         * 
         * $oModel->getVideoSearch()->setSources($this->getRequest()->getSession()->getUser()->getSourceSet()->getObjectIDs());
         */
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

    /**
     * Send the zip file to the user via x-sendfile
     * 
     * @param file name and file path
     * @return void 
     */
    private function sendZipFile($filename, $filepath) {
        $name = basename($filename . '.zip');

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
        unlink($filepath . $name);
    }

}
