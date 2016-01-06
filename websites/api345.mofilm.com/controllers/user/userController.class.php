<?php

/**
 * userController
 *
 * Stored in userController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_api.mofilm.com
 * @subpackage controllers
 * @category userController
 * @version $Rev: 736 $
 */

/**
 * userController
 *
 * userController class
 * 
 * @package websites_api.mofilm.com
 * @subpackage controllers
 * @category userController
 */
class userController extends mvcController {

    const ACTION_AUTHENTICATE = 'authenticate';
    const ACTION_ACCOUNT_DETAIL = "accountDetail";
    const ACTION_BRIEF = "brief";
    const ACTION_SAVETOKEN = "pushtoken";
    const ACTION_SENDPUSH = "sendpush";
    const ACTION_SAVE = "save";

    /**
     * @see mvcControllerBase::initialise()
     */
    function initialise() {
        parent::initialise();

        $this->setDefaultAction(self::ACTION_AUTHENTICATE);

        $this->getControllerActions()
                ->addAction(self::ACTION_AUTHENTICATE)
                ->addAction(self::ACTION_ACCOUNT_DETAIL)
                ->addAction(self::ACTION_SAVETOKEN)
                ->addAction(self::ACTION_SAVE)
                ->addAction(self::ACTION_BRIEF);

        $this->addControllerParameter(self::ACTION_AUTHENTICATE, 'username');
        $this->addControllerParameter(self::ACTION_AUTHENTICATE, 'password');

        $this->addControllerParameter(self::ACTION_ACCOUNT_DETAIL, 'userID');
        $this->addControllerParameter(self::ACTION_ACCOUNT_DETAIL, 'token');
    }

    /**
     * @see mvcControllerBase::launch()
     */
    function launch() {
        switch ($this->getAction()) {
            case self::ACTION_AUTHENTICATE: $this->authenticateAction();
                break;
            case self::ACTION_ACCOUNT_DETAIL: $this->accountDetailAction();
                break;
            case self::ACTION_BRIEF: $this->downloadAction();
                break;
            case self::ACTION_SAVE: $this->saveUser();
                break;
            case self::ACTION_SAVETOKEN: $this->saveToken();
                break;
            default:
                throw new mvcDistributorInvalidRequestException(sprintf('Unhandled action specified by requestor'));
                break;
        }
    }

    /**
     * 
     * Saves the userID and push token from  mobile device 
     * 
     * @return void
     */
    function saveToken() {

        systemLog::message($_POST);
        $userID = $_POST["userID"];
        $token = $_POST["token"];

        $oUserMobile = new mofilmUserMobile();
        $oUserMobile->setUserID($userID);
        $oUserMobile->setToken($token);
        $oUserMobile->setType($_POST["type"]);
        $oUserMobile->save();
    }

    function saveUser() {

        $data = $this->getInputManager()->doFilter();
        $this->addInputToModel($data, $this->getModel());


        try {
            if ($this->getModel()->saveUser($data)) {
                $oView = new userView($this);
                $oView->showRegisterPage();
            }
        } catch (Exception $ex) {
            $oView = new userView($this);
            $oView->showInvalidRegister($ex->getMessage());
        }
    }

    /**
     * Handle the download request
     * 
     * @return void
     */
    protected function downloadAction() {
        //$oView = new downloadView($this);
        $hash = $this->getActionFromRequest(false, 1);
        $oFile = $this->getModel()->getFile($hash);
        systemLog::message($oFile);
        if ($this->getActionFromRequest(false, 1)) {
            //  $link = "http://api.mofilm.com/resources/downloads/competitions/".$oFile->getFilename();
            // $arr["link"] =  $link;
            //   echo json_encode($arr);
            $this->sendFile($oFile);
        }
    }

    private function sendFile(mofilmDownloadFile $inFile) {
        $name = basename($inFile->getFilename());

        /*
         * IE is rubbish and doesn't like multiple . (dots) in the file name
         */
        if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
            $name = preg_replace('/\./', '%2e', $name, substr_count($name, '.') - 1);
        }

        systemLog::info("Delivering file {$name}");
        $filesize = filesize($inFile->getFileLocation());

        /*
         * Fetch mime-type from actual file lookup
         */
        $file = escapeshellarg($inFile->getFileLocation());
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
        //header("Content-Transfer-Encoding: binary");
        //header("Expires: 0");
        //header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        //header("Pragma: public");

        /*
         * Ensure output buffer is clean and stopped
         */
        //ob_clean();
        //flush();

        readfile($inFile->getFileLocation());
        /*
          $file = @fopen($inFile->getFileLocation(),"rb");
          while(!feof($file))
          {
          print(@fread($file, 1024*8));
          ob_flush();
          flush();
          }
         */
    }

    /**
     * Authenticates an API MOFILM user
     *
     * @return void
     */
    protected function authenticateAction() {
        $data = $this->getInputManager()->doFilter();
        $this->addInputToModel($data, $this->getModel());

        if ($this->getModel()->authenticateUser()) {
            $oView = new userView($this);
            $oView->showUserPage();
        } else {
            $oView = new userView($this);
            $oView->showInvalidUser();
        }
    }

    /**
     * Fetches an account details result for the specified user
     *
     * @return void
     */
    protected function accountDetailAction() {
        $data = $this->getInputManager()->doFilter();
        $this->addInputToModel($data, $this->getModel());

        $oView = new userView($this);
        try {
            if ($this->getModel()->getAccountDetail()) {
                $oView->showUserDetailPage();
            } else {
                $oView->showInvalidUser();
            }
        } catch (mofilmSystemAPITokenTimeoutException $e) {
            $oView->showApiTokenTimeout();
        }
    }

    /**
     * @see mvcControllerBase::addInputFilters()
     */
    function addInputFilters() {
        $this->getInputManager()->addFilter('username', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('firstname', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('surname', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('email', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('password', utilityInputFilter::filterString());
        $this->getInputManager()->addFilter('userID', utilityInputFilter::filterInt());
        $this->getInputManager()->addFilter('token', utilityInputFilter::filterString());
    }

    /**
     * @see mvcControllerBase::addInputToModel()
     */
    function addInputToModel($inData, $inModel) {
        $this->getModel()->setHash($inData['hash']);
        $this->getModel()->setApiKey($inData['apiKey']);
        $this->getModel()->setPassword($inData['password']);
        $this->getModel()->setUsername($inData['username']);
        $this->getModel()->setTimestamp($inData['time']);
        $this->getModel()->setUserID($inData['userID']);
        $this->getModel()->setRequestToken($inData['token']);
    }

    /**
     * Fetches the model
     *
     * @return userModel
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
        $oModel = new userModel();
        $this->setModel($oModel);
    }

}
