<?php

/**
 * userModel.class.php
 * 
 * userModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_api.mofilm.com
 * @subpackage controllers
 * @category userModel
 * @version $Rev: 623 $
 */

/**
 * userModel class
 * 
 * Provides the "user" page
 * 
 * @package websites_api.mofilm.com
 * @subpackage controllers
 * @category userModel
 */
class userModel extends mvcModelBase {

    /**
     * Stores $_Username
     *
     * @var string
     * @access protected
     */
    protected $_Username;

    /**
     * Stores $_Password
     *
     * @var string
     * @access protected
     */
    protected $_Password;

    /**
     * stores $_UnixTimestamp
     *
     * @var integer
     * @access protected
     */
    protected $_UnixTimestamp;

    /**
     * Stores $_apiKey
     *
     * @var string
     * @access protected
     */
    protected $_ApiKey;

    /**
     * stores $_Hash
     *
     * @var string
     * @access protected
     */
    protected $_Hash;

    /**
     *  Stores $_user
     *
     * @var mofilmUser
     * @access protected
     */
    protected $_User;

    /**
     * Stores $_UserID
     * 
     * @var integer
     * @access protected
     */
    protected $_UserID;

    /**
     * Stores $_RequestToken
     *
     * @var string
     * @access protected
     */
    protected $_RequestToken;

    /**
     * @see mvcModelBase::__construct()
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Sets the hash which  for the request
     *
     * @param string $inHash
     * @return userModel
     */
    function setHash($inHash) {
        $this->_Hash = $inHash;
        return $this;
    }

    /**
     * Returns the hash for a request
     *
     * @return string
     */
    function getHash() {
        return $this->_Hash;
    }

    /**
     * Sets the public API key for the request
     *
     * @param string $inApikey
     * @return userModel
     */
    function setApiKey($inApikey) {
        $this->_ApiKey = $inApikey;
        return $this;
    }

    /**
     * Returns the public API key
     *
     * @return string
     */
    function getApikey() {
        return $this->_ApiKey;
    }

    /**
     * Sets the username for the request
     *
     * @param string $inUsername
     * @return userModel
     */
    function setUsername($inUsername) {
        $this->_Username = $inUsername;
        return $this;
    }

    /**
     * Returns the password for the request
     *
     * @return string
     */
    function getUsername() {
        return $this->_Username;
    }

    /**
     * Sets the password for the request
     *
     * @param string $inPassword
     * @return userModel
     */
    function setPassword($inPassword) {
        $this->_Password = $inPassword;
        return $this;
    }

    /**
     * Returns the password for the request
     *
     * @return string
     */
    function getPassword() {
        return $this->_Password;
    }

    /**
     * Sets the timestamp of the request
     *
     * @param integer $inTime
     * @return userModel
     */
    function setTimestamp($inTime) {
        $this->_UnixTimestamp = $inTime;
        return $this;
    }

    /**
     * Returns the timestamp for the request
     * @return <integer>
     */
    function getTimestamp() {
        return $this->_UnixTimestamp;
    }

    /**
     * Sets the user object
     *
     * @param mofilmUser $oUser
     * @return userModel
     */
    function setUser(mofilmUser $oUser) {
        $this->_User = $oUser;
        return $this;
    }

    /**
     * Gets the user object for the request
     *
     * @return mofilmUser
     */
    function getUser() {
        return $this->_User;
    }

    /**
     * Gets the userID
     * @return integer
     */
    function getUserID() {
        return $this->_UserID;
    }

    /**
     * Stores the userID
     *
     * @param integer $inUserID
     * @return userModel
     */
    function setUserID($inUserID) {
        $this->_UserID = $inUserID;
        return $this;
    }

    /**
     * Sets the request token
     * 
     * @param string $inRequestToken
     * @return userModel
     */
    function setRequestToken($inRequestToken) {
        $this->_RequestToken = $inRequestToken;
        return $this;
    }

    /**
     * Gets the Token
     * @return string
     */
    function getRequestToken() {
        return $this->_RequestToken;
    }

    function saveUser($data) {
        
        $email = $data["email"];
        $first = $data["username"];
        $last = $data["surname"];
        $password = $data["password"];
        
        $oUser = mofilmUserManager::getInstance()->setLoadOnlyActive(false)->getUserByUsername($email);
        if ($oUser instanceof mofilmUserBase && $oUser->getID() > 0) {
            throw new mofilmException("An account already exists for ({$inData['username']})");
        }

        $oValidator = new utilityValidateEmailAddress();
        if (!$oValidator->isValid($email)) {
            throw new mofilmException(implode(' ', $oValidator->getMessages()));
        }

        $oUser = new mofilmUser();
        $oUser->setEmail($email);
        $oUser->setPassword($password);
        $oUser->setClientID(0);
        $oUser->setEnabled(mofilmUser::ENABLED_Y);
        $oUser->setHash(mofilmUtilities::buildMiniHash($_POST, 10));
        $oUser->setAutoCommitStatus(1);
        // $oUser->setTerritoryID($inData['territory']);
        //  $oUser->setFacebookID($inData['facebookID']);
        $oUser->setFirstname($first);
        $oUser->setSurname($last);

        $oUser->save();

        $oProfile = new mofilmUserProfile();
        $oProfile->setUserID($oUser->getID());
        $oProfile->setProfileName($first . $last . time());
        $oProfile->setActive(mofilmUserProfile::PROFILE_ACTIVE);
        $oProfile->save();
        $this->setUser($oUser);
        return true;
    }

    /**
     * Authenticates the user based on username and password
     *
     * @return boolean
     */
    function authenticateUser() {
        try {
            $oUser = mofilmUserManager::getInstanceByUserLogin($this->getUsername(), $this->getPassword());
        } catch (mofilmException $e) {
            systemLog::error($e->getMessage());
        }

        if (isset($oUser) && $oUser instanceof mofilmUser && $oUser->getID() > 0) {
            $this->setUser($oUser);

            $oMofilmSystemAPIRequest = new mofilmSystemAPIRequest();
            $oMofilmSystemAPIRequest->setRequestAPIkey($this->getApikey());
            $oMofilmSystemAPIRequest->setRequestToken(uniqid());
            $oMofilmSystemAPIRequest->setRequestAction('accountDetail');
            $oMofilmSystemAPIRequest->save();

            $this->setRequestToken($oMofilmSystemAPIRequest->getRequestToken());

            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets the User details based on the userID
     *
     * @return boolean
     * @throws mofilmSystemAPITokenTimeoutException
     */
    function getAccountDetail() {
        $oMofilmAPIRequest = mofilmSystemAPIRequest::getInstanceByRequestToken($this->getRequestToken());
        $oDiff = $oMofilmAPIRequest->getTimeStamp()->diff(new systemDateTime());

        if ($oMofilmAPIRequest->getID() > 0 && systemDateTime::getDiffInSeconds($oDiff) < 60) {
            $oUser = mofilmUserManager::getInstanceByID($this->getUserID());

            if ($oUser instanceof mofilmUser && $oUser->getID() == $this->getUserID()) {
                $this->setUser($oUser);
                return true;
            } else {
                return false;
            }
        } else {
            throw new mofilmSystemAPITokenTimeoutException($this->getRequestToken());
        }
    }

    /**
     * Returns the file, fetching it if $inAction is supplied
     *
     * @param string $inFileID
     * @return mofilmDownloadFile
     */
    function getFile($inFileID = null) {
        if (!$this->_File instanceof mofilmDownloadFile || $inFileID !== null) {
            if ($inFileID !== null) {
                if (is_numeric($inFileID) && strlen($inFileID) < 5) {
                    $oFile = mofilmDownloadFile::getInstance($inFileID);
                } else {
                    $oFile = mofilmDownloadFile::getInstanceByHash($inFileID);
                }

                if ($oFile->getID() > 0) {
                    $this->_File = $oFile;
                }
            }
        }
        return $this->_File;
    }

}
