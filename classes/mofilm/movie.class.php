<?php

/**
 * mofilmMovie
 * 
 * Stored in mofilmMovie.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovie
 * @version $Rev: 326 $
 */

/**
 * mofilmMovie Class
 * 
 * An aggregate object that pulls together all the movie data into a single
 * object allowing it to be manipulated as a single entity. This class makes
 * extensive use of lazy loading to only pull the data when it is needed.
 * 
 * While movie objects can be accessed via this class, it is recommended to
 * use the main {@link mofilmMovieManager} class that also includes additional
 * loading mechanisms to completely populate the movie metadata in a reduced
 * number of queries.
 * 
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovie
 */
class mofilmMovie extends mofilmMovieBase {

    /**
     * Stores an instance of mofilmMovieAssetSet
     *
     * @var mofilmMovieAssetSet
     * @access protected
     */
    protected $_AssetSet;

    /**
     * Stores an instance of mofilmMovieAwardSet
     *
     * @var mofilmMovieAwardSet
     * @access protected
     */
    protected $_AwardSet;

    /**
     * Stores an instance of mofilmMovieCategorySet
     *
     * @var mofilmMovieCategorySet
     * @access protected
     */
    protected $_CategorySet;

    /**
     * Stores an instance of mofilmMovieCommentSet
     *
     * @var mofilmMovieCommentSet
     * @access protected
     */
    protected $_CommentSet;

    /**
     * Stores an instance of mofilmMovieContributorSet
     *
     * @var mofilmMovieContributorSet
     * @access protected
     */
    protected $_ContributorSet;

    /**
     * Stores the movie data set
     * 
     * @var mofilmMovieDataSet
     * @access protected
     */
    protected $_DataSet;

    /**
     * Stores the movie License set
     * 
     * @var mofilmMovieMusicLicenseSet
     * @access protected 
     */
    protected $_LicenseSet;

    /**
     * Stores an instance of mofilmMovieHistorySet
     *
     * @var mofilmMovieHistorySet
     * @access protected
     */
    protected $_HistorySet;

    /**
     * Stores an instance of mofilmMovieSourceSet
     *
     * @var mofilmMovieSourceSet
     * @access protected
     */
    protected $_SourceSet;

    /**
     * Stores an instance of mofilmMovieTagSet
     *
     * @var mofilmMovieTagSet
     * @access protected
     */
    protected $_TagSet;

    /**
     * Stores an instance of mofilmMovieTrackSet
     *
     * @var mofilmMovieTrackSet
     * @access protected
     */
    protected $_TrackSet;

    /**
     * Stores an instance of mofilmMovieMessageSet
     * 
     * @var mofilmMovieMessageSet
     * @access protected
     */
    protected $_MessageSet;

    /**
     * Stores an instance of mofilmMovieRatingSet
     * 
     * @var mofilmMovieRatingSet
     * @access protected
     */
    protected $_RatingSet;

    /**
     * Stores the movie License set
     * 
     * @var mofilmMovieBroadcastSet
     * @access protected 
     */
    protected $_BroadcastSet;

    /**
     * Saves changes to the object
     *
     * @return boolean
     */
    function save() {
        $return = true;
        if ($this->isModified()) {
            $return = parent::save() && $return;

            if ($this->_DataSet instanceof mofilmMovieDataSet) {
                $this->_DataSet->setMovieID($this->getID());
                $this->_DataSet->save();
            }
            if ($this->_LicenseSet instanceof mofilmMovieMusicLicenseSet) {
                $this->_LicenseSet->setMovieID($this->getID());
                $this->_LicenseSet->save();
            }
            if ($this->_TagSet instanceof mofilmMovieTagSet) {
                $this->_TagSet->setMovieID($this->getID());
                $this->_TagSet->save();
            }
            if ($this->_SourceSet instanceof mofilmMovieSourceSet) {
                $this->_SourceSet->setMovieID($this->getID());
                $this->_SourceSet->save();
            }
            if ($this->_AssetSet instanceof mofilmMovieAssetSet) {
                $this->_AssetSet->setMovieID($this->getID());
                $this->_AssetSet->save();
            }
            if ($this->_AwardSet instanceof mofilmMovieAwardSet) {
                $this->_AwardSet->setMovieID($this->getID());
                $this->_AwardSet->setUserID($this->getUserID());
                $this->_AwardSet->save();
            }
            if ($this->_CommentSet instanceof mofilmMovieCommentSet) {
                $this->_CommentSet->setMovieID($this->getID());
                $this->_CommentSet->save();
            }
            if ($this->_ContributorSet instanceof mofilmMovieContributorSet) {
                $this->_ContributorSet->setMovieID($this->getID());
                $this->_ContributorSet->save();
            }
            if ($this->_HistorySet instanceof mofilmMovieHistorySet) {
                $this->_HistorySet->setMovieID($this->getID());
                $this->_HistorySet->save();
            }
            if ($this->_TrackSet instanceof mofilmMovieTrackSet) {
                $this->_TrackSet->setMovieID($this->getID());
                $this->_TrackSet->save();
            }
            if ($this->_CategorySet instanceof mofilmMovieCategorySet) {
                $this->_CategorySet->setMovieID($this->getID());
                $this->_CategorySet->save();
            }
            if ($this->_MessageSet instanceof mofilmMovieMessageSet) {
                $this->_MessageSet->setMovieID($this->getID());
                $this->_MessageSet->save();
            }
            if ($this->_BroadcastSet instanceof mofilmMovieBroadcastSet) {
                $this->_BroadcastSet->setMovieID($this->getID());
                $this->_BroadcastSet->save();
            }
            $return = true;
        }
        return $return;
    }

    /**
     * Deletes the object and all related records
     *
     * @return boolean
     */
    function delete() {
        $return = false;
        if ($this->getID()) {
            $this->getDataSet()->delete();
            $this->getLicenseSet()->delete();
            $this->getTagSet()->delete();
            $this->getSourceSet()->delete();
            $this->getAssetSet()->delete();
            $this->getCommentSet()->delete();
            $this->getContributorSet()->delete();
            $this->getHistorySet()->delete();
            $this->getTrackSet()->delete();
            $this->getCategorySet()->delete();
            $this->getAwardSet()->delete();
            $this->getMessageSet()->delete();
            $this->getRatingSet()->delete();
            $this->getBroadcastSet()->delete();
            $return = parent::delete();
        }
        return $return;
    }

    /**
     * Reset object
     *
     * @return void
     */
    function reset() {
        $this->_AssetSet = null;
        $this->_AwardSet = null;
        $this->_CommentSet = null;
        $this->_ContributorSet = null;
        $this->_DataSet = null;
        $this->_LicenseSet = null;
        $this->_HistorySet = null;
        $this->_SourceSet = null;
        $this->_TagSet = null;
        $this->_TrackSet = null;
        $this->_CategorySet = null;
        $this->_MessageSet = null;
        $this->_RatingSet = null;
        $this->_BroadcastSet = null;
        parent::reset();
    }

    /**
     * Custom destructor to ensure all sub-objects are unloaded
     *
     * @return void
     */
    function __destruct() {
        
    }

    /**
     * Returns true if object, or sub-objects have been modified
     *
     * @return boolean
     */
    function isModified() {
        $modified = parent::isModified();
        if (!$modified && $this->_DataSet !== null) {
            $modified = $modified || $this->_DataSet->isModified();
        }
        if (!$modified && $this->_LicenseSet !== null) {
            $modified = $modified || $this->_LicenseSet->isModified();
        }
        if (!$modified && $this->_TagSet !== null) {
            $modified = $modified || $this->_TagSet->isModified();
        }
        if (!$modified && $this->_SourceSet !== null) {
            $modified = $modified || $this->_SourceSet->isModified();
        }
        if (!$modified && $this->_AssetSet !== null) {
            $modified = $modified || $this->_AssetSet->isModified();
        }
        if (!$modified && $this->_AwardSet !== null) {
            $modified = $modified || $this->_AwardSet->isModified();
        }
        if (!$modified && $this->_CommentSet !== null) {
            $modified = $modified || $this->_CommentSet->isModified();
        }
        if (!$modified && $this->_ContributorSet !== null) {
            $modified = $modified || $this->_ContributorSet->isModified();
        }
        if (!$modified && $this->_HistorySet !== null) {
            $modified = $modified || $this->_HistorySet->isModified();
        }
        if (!$modified && $this->_TrackSet !== null) {
            $modified = $modified || $this->_TrackSet->isModified();
        }
        if (!$modified && $this->_CategorySet !== null) {
            $modified = $modified || $this->_CategorySet->isModified();
        }
        if (!$modified && $this->_MessageSet !== null) {
            $modified = $modified || $this->_MessageSet->isModified();
        }
        if (!$modified && $this->_BroadcastSet !== null) {
            $modified = $modified || $this->_BroadcastSet->isModified();
        }
        return $modified;
    }

    /**
     * Returns an instance of mofilmMovieAssetSet, which is lazy loaded upon request
     *
     * @return mofilmMovieAssetSet
     */
    function getAssetSet() {
        if (!$this->_AssetSet instanceof mofilmMovieAssetSet) {
            $this->_AssetSet = new mofilmMovieAssetSet($this->getID());
        }
        return $this->_AssetSet;
    }

    /**
     * Set the pre-loaded object to the class
     *
     * @param mofilmMovieAssetSet $inObject
     * @return mofilmMovie
     */
    function setAssetSet(mofilmMovieAssetSet $inObject) {
        $this->_AssetSet = $inObject;
        return $this;
    }

    /**
     * Returns an instance of mofilmMovieAwardSet, which is lazy loaded upon request
     *
     * @param integer $inEventID (optional) Load only awards from this event
     * @return mofilmMovieAwardSet
     */
    function getAwardSet($inEventID = null) {
        if (!$this->_AwardSet instanceof mofilmMovieAwardSet) {
            $this->_AwardSet = new mofilmMovieAwardSet($this->getID(), $inEventID);
        }
        return $this->_AwardSet;
    }

    /**
     * Set the pre-loaded object to the class
     *
     * @param mofilmMovieAwardSet $inObject
     * @return mofilmMovie
     */
    function setAwardSet(mofilmMovieAwardSet $inObject) {
        $this->_AwardSet = $inObject;
        return $this;
    }

    /**
     * Returns an instance of mofilmMovieCategorySet, which is lazy loaded upon request
     *
     * @return mofilmMovieCategorySet
     */
    function getCategorySet() {
        if (!$this->_CategorySet instanceof mofilmMovieCategorySet) {
            $this->_CategorySet = new mofilmMovieCategorySet($this->getID());
        }
        return $this->_CategorySet;
    }

    /**
     * Set the pre-loaded object to the class
     *
     * @param mofilmMovieCategorySet $inObject
     * @return mofilmMovie
     */
    function setCategorySet(mofilmMovieCategorySet $inObject) {
        $this->_CategorySet = $inObject;
        return $this;
    }

    /**
     * Returns an instance of mofilmMovieCommentSet, which is lazy loaded upon request
     *
     * @return mofilmMovieCommentSet
     */
    function getCommentSet() {
        if (!$this->_CommentSet instanceof mofilmMovieCommentSet) {
            $this->_CommentSet = new mofilmMovieCommentSet($this->getID());
        }
        return $this->_CommentSet;
    }

    /**
     * Set the pre-loaded object to the class
     *
     * @param mofilmMovieCommentSet $inObject
     * @return mofilmMovie
     */
    function setCommentSet(mofilmMovieCommentSet $inObject) {
        $this->_CommentSet = $inObject;
        return $this;
    }

    /**
     * Returns an instance of mofilmMovieContributorSet, which is lazy loaded upon request
     *
     * @return mofilmMovieContributorSet
     */
    function getContributorSet() {
        if (!$this->_ContributorSet instanceof mofilmMovieContributorSet) {
            $this->_ContributorSet = new mofilmMovieContributorSet($this->getID());
            if ($this->getID() > 0) {
                $this->_ContributorSet->load();
            }
        }
        return $this->_ContributorSet;
    }

    /**
     * Set the pre-loaded object to the class
     *
     * @param mofilmMovieContributorSet $inObject
     * @return mofilmMovie
     */
    function setContributorSet(mofilmMovieContributorSet $inObject) {
        $this->_ContributorSet = $inObject;
        return $this;
    }

    /**
     * Returns the movie data parameters
     * 
     * @return mofilmMovieDataSet
     */
    function getDataSet() {
        if (!$this->_DataSet instanceof mofilmMovieDataSet) {
            $this->_DataSet = new mofilmMovieDataSet($this->getID());
        }
        return $this->_DataSet;
    }

    /**
     * Sets the data set object to the movie
     * 
     * @param mofilmMovieDataSet $inObject
     * @return mofilmMovie
     */
    function setDataSet(mofilmMovieDataSet $inObject) {
        $this->_DataSet = $inObject;
        return $this;
    }

    /**
     * Returns the movie data parameters
     * 
     * @return mofilmMovieMusicLicenseSet
     */
    function getLicenseSet() {
        if (!$this->_LicenseSet instanceof mofilmMovieMusicLicenseSet) {
            $this->_LicenseSet = new mofilmMovieMusicLicenseSet($this->getID());
        }
        return $this->_LicenseSet;
    }

    /**
     * Sets the license set object to the movie
     * 
     * @param mofilmMovieMusicLicenseSet $inObject
     * @return mofilmMovie
     */
    function setLicenseSet(mofilmMovieMusicLicenseSet $inObject) {
        $this->_LicenseSet = $inObject;
        return $this;
    }

    /**
     * Returns an instance of mofilmMovieHistorySet, which is lazy loaded upon request
     *
     * @return mofilmMovieHistorySet
     */
    function getHistorySet() {
        if (!$this->_HistorySet instanceof mofilmMovieHistorySet) {
            $this->_HistorySet = new mofilmMovieHistorySet($this->getID());
        }
        return $this->_HistorySet;
    }

    /**
     * Set the pre-loaded object to the class
     *
     * @param mofilmMovieHistorySet $inObject
     * @return mofilmMovie
     */
    function setHistorySet(mofilmMovieHistorySet $inObject) {
        $this->_HistorySet = $inObject;
        return $this;
    }

    /**
     * Returns an instance of mofilmMovieSourceSet, which is lazy loaded upon request
     *
     * @return mofilmMovieSourceSet
     */
    function getSourceSet() {
        if (!$this->_SourceSet instanceof mofilmMovieSourceSet) {
            $this->_SourceSet = new mofilmMovieSourceSet($this->getID());
        }
        return $this->_SourceSet;
    }

    /**
     * Returns the first source object from the set
     * 
     * @return mofilmSource
     */
    function getSource() {
        $oObject = $this->getSourceSet()->getObjectByIndex(0);
        if ($oObject instanceof mofilmSource) {
            return $oObject;
        } else {
            return new mofilmSource();
        }
    }

    /**
     * Set the pre-loaded object to the class
     *
     * @param mofilmMovieSourceSet $inObject
     * @return mofilmMovie
     */
    function setSourceSet(mofilmMovieSourceSet $inObject) {
        $this->_SourceSet = $inObject;
        return $this;
    }

    /**
     * Returns an instance of mofilmMovieTagSet, which is lazy loaded upon request
     *
     * @return mofilmMovieTagSet
     */
    function getTagSet() {
        if (!$this->_TagSet instanceof mofilmMovieTagSet) {
            $this->_TagSet = new mofilmMovieTagSet($this->getID());
        }
        return $this->_TagSet;
    }

    /**
     * Sets the tag set object to the movie
     * 
     * @param mofilmMovieTagSet $inObject
     * @return mofilmMovie
     */
    function setTagSet(mofilmMovieTagSet $inObject) {
        $this->_TagSet = $inObject;
        return $this;
    }

    /**
     * Returns an instance of mofilmMovieTrackSet, which is lazy loaded upon request
     *
     * @return mofilmMovieTrackSet
     */
    function getTrackSet() {
        if (!$this->_TrackSet instanceof mofilmMovieTrackSet) {
            $this->_TrackSet = new mofilmMovieTrackSet($this->getID());
        }
        return $this->_TrackSet;
    }

    /**
     * Sets the track set object to the movie
     *
     * @param mofilmMovieTrackSet $inObject
     * @return mofilmMovie
     */
    function setTrackSet(mofilmMovieTrackSet $inObject) {
        $this->_TrackSet = $inObject;
        return $this;
    }

    /**
     * Returns an instance of mofilmMovieMessageSet, which is lazy loaded upon request
     *
     * @return mofilmMovieMessageSet
     */
    function getMessageSet() {
        if (!$this->_MessageSet instanceof mofilmMovieMessageSet) {
            $this->_MessageSet = new mofilmMovieMessageSet($this->getID());
        }
        return $this->_MessageSet;
    }

    /**
     * Sets the message set object to the movie
     *
     * @param mofilmMovieMessageSet $inObject
     * @return mofilmMovie
     */
    function setMessageSet(mofilmMovieMessageSet $inObject) {
        $this->_MessageSet = $inObject;
        return $this;
    }

    /**
     * Returns a string for the movie credits
     * 
     * @return string
     */
    function getCreditText() {
        if ($this->getCredits() && strlen($this->getCredits()) > 0) {
            return $this->getCredits();
        } else {
            return $this->getUser()->getFullname();
        }
    }

    /**
     * Returns a short URI for this, optionally customised for the user
     * 
     * If $inFullUri is true, then a fully qualified URI is returned.
     * 
     * @param integer $inUserID
     * @param boolean $inFullUri
     * @return string
     */
    function getShortUri($inUserID = 0, $inFullUri = false) {
        $oObject = mofilmMovieLink::getInstanceByUnqSenderMovie($inUserID, $this->getID());
        if ($oObject->getID() == 0 || !$oObject->getHash()) {
            $oObject->setMovieID($this->getID());
            $oObject->setSenderID($inUserID);
            $oObject->setHash(mofilmUtilities::buildMiniHash($oObject, 6));
            $oObject->save();
        }

        if ($inFullUri) {
            return system::getConfig()->getParam('mofilm', 'screeningUrl') . '/' . $oObject->getHash();
        } else {
            return $oObject->getHash();
        }
    }

    /**
     * Alias of getRatingSet, for API compatibility
     * 
     * @return mofilmMovieRatingSet
     */
    function getRatings() {
        return $this->getRatingSet();
    }

    /**
     * Returns an array of rating objects
     * 
     * @return mofilmMovieRatingSet
     */
    function getRatingSet() {
        if (!$this->_RatingSet instanceof mofilmMovieRatingSet) {
            $this->_RatingSet = new mofilmMovieRatingSet($this->getID());
        }
        return $this->_RatingSet;
    }

    /**
     * Sets a pre-built rating set to the object
     * 
     * @param mofilmMovieRatingSet $inSet
     * @return mofilmMovie
     */
    function setRatingSet(mofilmMovieRatingSet $inSet) {
        $this->_RatingSet = $inSet;
        return $this;
    }

    /**
     * Returns the users rating for this movie
     * 
     * @param integer $inUserID
     * @return mofilmMovieRating
     */
    function getUserRating($inUserID) {
        return mofilmMovieRating::getInstance($this->getID(), $inUserID);
    }

    /**
     * Returns other movies by this user on the current event
     * 
     * @param integer $inOffset
     * @param integer $inLimit
     * @param mofilmUser $inUser
     * @return mofilmMovieSearchResult
     */
    function getOtherEventMovies($inOffset = 0, $inLimit = 5, $inUser = null) {
        if (!$inUser instanceof mofilmUser) {
            $inUser = $this->getUser();
        }
        $oSearch = new mofilmMovieSearch();
        $oSearch->setUser($inUser);
        $oSearch->setUserID($this->getUserID());
        $oSearch->addStatus(mofilmMovie::STATUS_APPROVED);
        $oSearch->addStatus(mofilmMovie::STATUS_PENDING);
        $oSearch->addEvent($this->getSource()->getEvent());
        $oSearch->excludeMovie($this);
        $oSearch->setOffset($inOffset);
        $oSearch->setLimit($inLimit);
        return $oSearch->search();
    }

    /**
     * Returns other movies by this user on the current event that are either waiting for approval or are approved
     * 
     * @param integer $inOffset
     * @param integer $inLimit
     * @param mofilmUser $inUser
     * @return mofilmMovieSearchResult
     */
    function getOtherEventMoviesForReview($inOffset = 0, $inLimit = 5, $inUser = null) {
        if (!$inUser instanceof mofilmUser) {
            $inUser = $this->getUser();
        }

        $oSearch = new mofilmMovieSearch();
        $oSearch->setUser($inUser);
        $oSearch->setUserID($this->getUserID());
        $oSearch->addStatus(mofilmMovie::STATUS_APPROVED);
        $oSearch->addStatus(mofilmMovie::STATUS_PENDING);
        $oSearch->addEvent($this->getSource()->getEvent());
        $oSearch->excludeMovie($this);
        $oSearch->setOrderBy(mofilmMovieSearch::ORDERBY_DATE);
        $oSearch->setOrderDirection(mofilmMovieSearch::ORDER_DESC);
        $oSearch->setOffset($inOffset);
        $oSearch->setLimit($inLimit);
        return $oSearch->search();
    }

    /**
     * Returns a commsOutboundSearchResult of all messages associated with this movie in order sent DESC
     * 
     * @param integer $inOffset
     * @param integer $inLimit
     * @return commsOutboundSearchResult
     */
    function getMessagesAttachedToMovie($inOffset = 0, $inLimit = 30) {
        $oSearch = new commsOutboundSearch();
        $oSearch->addParam(mofilmMessages::MSG_PARAM_MOVIE_ID, $this->getID());
        $oSearch->setOnlySentMessages(true);
        $oSearch->setOrderBy(commsOutboundSearch::ORDERBY_DATE_CREATED);
        $oSearch->setOrderDirection(commsOutboundSearch::ORDER_DESC);
        return $oSearch->search();
    }

    /**
     * Returns a thumbnail URI for this movie
     * 
     * @param string $inSize Size of thumbnail, s - small, m - medium, l - large
     * @return string
     */
    function getThumbnailUri($inSize = 's') {

        /*
         * PG : After migrating to brightcove only s - small and l - large thumbnails will be available
         */
        if (!in_array($inSize, array('s', 'm', 'l'))) {
            $inSize = 's';
        }

        switch ($inSize) {
            case 'l': $type = 'ThumbNail_604x340';
                break;
            case 'm': $type = 'ThumbNail_640x340';
                break;
            case 's':
            default:
                $type = 'ThumbNail_150x84';
                break;
        }

        if ($this->getStatus() == mofilmMovie::STATUS_ENCODING) {
            return "/resources/video/thumb_500x281.png";
        }

        $return = $this->getAssetSet()->getObjectByDescription($type)->getFirst()->getCdnURL();
        if (!$return) {
            //	$return = "http://platform.mofilm.com/images/thumb.php?m={$this->getID()}&s=$inSize";
            $return = "http://omedia.vo.llnwd.net/o10/mofilms/platform/" . $this->getID() . "/thumb_500x281.jpg";
        }
        return $return;
    }

    /**
     * Returns a grants object if grants exists or returns false
     * 
     * @return grants object if true else false
     */
    function getGrantsData() {
        $oGrant = mofilmUserMovieGrants::userMovieGrantsObjectByMovieID($this->getID());

        if ($oGrant instanceof mofilmUserMovieGrants) {
            return $oGrant;
        } else {
            return false;
        }
    }

    /**
     * Returns a uploadStatus object
     * 
     * @return uploadStatus object if true else false
     */
    function getUploadStatusSet() {
        $oUploadStatus = mofilmUploadStatus::getInstanceByMovieID($this->getID());

        if ($oUploadStatus instanceof mofilmUploadStatus) {
            return $oUploadStatus;
        } else {
            return false;
        }
    }

    /**
     * Returns a contributor role of the movie user combination
     * 
     * @return role if true else false
     */
    function getContributorRole($inProfileName = NULL) {
        if (!is_null($inProfileName)) {
            $inMovieID = $this->getID();
            $oContributors = mofilmContributor::getArrayOfInstancesByEmail($inProfileName);

            foreach ($oContributors as $oContributor) {
                $inContributorsID[] = $oContributor->getID();
            }

            $return = false;
            $query = 'SELECT roles.description FROM ' . system::getConfig()->getDatabase('mofilm_content') .
                    '.movieContributors INNER JOIN ' . system::getConfig()->getDatabase('mofilm_content') .
                    '.roles ON ( roles.ID = movieContributors.roleID ) ';

            $where = array();
            if ($inMovieID !== 0) {
                $where[] = ' movieID = :MovieID ';
            }
            if ($inContributorID !== 0) {
                $where[] = ' contributorID in (' . implode(', ', $inContributorsID) . ') ';
            }

            if (count($where) == 0) {
                return false;
            }

            $query .= ' WHERE ' . implode(' AND ', $where);

            try {
                $oStmt = dbManager::getInstance()->prepare($query);
                if ($inMovieID !== 0) {
                    $oStmt->bindValue(':MovieID', $inMovieID);
                }

                $list = array();

                if ($oStmt->execute()) {

                    foreach ($oStmt as $row) {
                        $list[] = $row['description'];
                    }
                }
                $oStmt->closeCursor();
            } catch (Exception $e) {
                systemLog::error($e->getMessage());
                throw $e;
            }
            return implode(', ', $list);
        } else {
            return false;
        }
    }

    /**
     * Returns a FLV URI for this movie
     * 
     * @param 
     * @return string
     */
    function getFLVUri() {
        $return = $this->getAssetSet()->getObjectByAssetAndFileType('File', 'FLV')->getFirst()->getCdnURL();
        if (isset($return)) {
            return $return;
        } else {
            return false;
        }
    }

    /**
     * Get instance of mofilmContributor by unique key (email)
     *
     * @param string $inEmail
     * @return mofilmContributor
     * @static
     */
    function getIphoneVideo() {
        $query = 'SELECT cdnURL FROM ' . system::getConfig()->getDatabase('mofilm_content') . '.movieAssets 
				 LEFT JOIN ' . system::getConfig()->getDatabase('mofilm_content') . '.movies ON (movieAssets.movieID = movies.ID)
			     LEFT JOIN ' . system::getConfig()->getDatabase('mofilm_content') . '.users ON (users.ID = movies.userID)
				 WHERE type= "File"
				 AND (ext="MP4" or ext="M4V" or ext="MOV")
				 AND width<="480" AND height<="320" 
				 AND cdnURL LIKE "%%iphone%%" 
				 AND movieID =' . $this->getID();


        $cdnUrl;
        try {
            $oStmt = dbManager::getInstance()->prepare($query);

            if ($oStmt->execute()) {
                foreach ($oStmt as $row) {
                    $cdnUrl = $row["cdnURL"];
                }
            }
            $oStmt->closeCursor();
        } catch (Exception $e) {
            systemLog::error($e->getMessage());
            throw $e;
        }
        return $cdnUrl;
    }

    /**
     * Sets the list of contributors based on input
     * 
     * @param array $inData
     * @param MofilmMovie $oMovie 
     */
    function setContributorInputData($inData, $oMovie, $inLanguage) {

        if (is_array($inData['Contributors'])) {
            /*
              if ( count($inData['Contributors']) != $oMovie->getContributorSet()->getCount() ) {

              foreach ( $oMovie->getContributorSet() as $oMap ) {
              $oMap->setMarkForDeletion(true);
              foreach ( $inData['Contributors'] as $contributor ) {
              if ( $contributor['ID'] > 0 && $oMap->getContributorID() == $contributor['ID'] ) {
              $oMap->setMarkForDeletion(false);
              $oMap->setModified(false);
              }
              }
              }
              }
             */

            foreach ($inData['Contributors'] as $contributor) {
                if (array_key_exists('Remove', $contributor)) {
                    if ($oMovie->getContributorSet()->getMappingByContributorID($contributor['ID'])) {
                        $oMovie->getContributorSet()->getMappingByContributorID($contributor['ID'])->setMarkForDeletion(true);
                        continue;
                    }
                }

                $oRole = mofilmRole::getInstanceByDescription($contributor['Role']);
                $oRole->setDescription($contributor['Role']);
                systemLog::message($contributor);
                if (isset($contributor['ID']) && $contributor['ID'] > 0) {
                    if ($oMovie->getContributorSet()->getMappingByContributorID($contributor['ID'])) {
                        $oMovie->getContributorSet()->getMappingByContributorID($contributor['ID'])->setRoleID($oRole->getID());
                    } else {
                        //$oContributor = mofilmContributor::getInstanceByEmail(mofilmUserManager::getInstanceByID($contributor["ID"])->getEmail());
                        $oContributor = mofilmContributor::getInstanceByEmail(mofilmUserManager::getInstanceByID($contributor["ID"])->getEmail());
                        if (!$oContributor) {
                            $oContributor = new mofilmContributor();
                            $oContributor->setName(mofilmUserManager::getInstanceByID($contributor["ID"])->getEmail());
                        }
                        systemLog::message("Creating a new contributor");
                        $oMap = new mofilmMovieContributorMap();
                        $oMap->setRole($oRole);
                        $oMap->setContributor($oContributor);
                        $oMovie->getContributorSet()->setContributor($oMap);
                        $this->sendCreditEmail($oMovie->getUserID(), $oContributor->getName(), $oMovie->getTitle(), mofilmUserManager::getInstanceByID($oMovie->getUserID())->getFullname(), $oRole->getDescription(), 0, $inLanguage);
                    }
                } else {
                    if (strlen($contributor['Name']) < 1 || strlen($contributor['Role']) < 1) {
                        continue;
                    }
                    try {
                        if (preg_match("/@/", $contributor["Name"])) {

                            //$oContributor = mofilmContributor::getInstanceByEmail($contributor["Name"]);
                            $oContributor = mofilmContributor::getInstanceByEmail($contributor["Name"]);
                            if (!$oContributor) {
                                $oContributor = new mofilmContributor();
                                $oContributor->setName($contributor["Name"]);
                            }
                            $oMap = new mofilmMovieContributorMap();
                            $oMap->setRole($oRole);
                            $oMap->setContributor($oContributor);
                            $oMovie->getContributorSet()->setContributor($oMap);
                            if (!mofilmUserManager::getInstanceByUsername($contributor["Name"])) {
                                $this->sendCreditEmail($oMovie->getUserID(), $oContributor->getName(), $oMovie->getTitle(), mofilmUserManager::getInstanceByID($oMovie->getUserID())->getFullname(), $oRole->getDescription(), 1, $inLanguage);
                            } else {
                                $this->sendCreditEmail($oMovie->getUserID(), $oContributor->getName(), $oMovie->getTitle(), mofilmUserManager::getInstanceByID($oMovie->getUserID())->getFullname(), $oRole->getDescription(), 0, $inLanguage);
                            }
                        } else {
                            systemLog::message("No such user found or this is not an valid email address" . $contributor["Name"]);
                            throw new mvcModelException('No such ' . $contributor["Name"] . ' user found');
                        }
                    } catch (Exception $e) {
                        $message = $e->getMessage();
                        $level = mvcSession::MESSAGE_ERROR;
                        //throw new mvcModelException($message);
                    }
                }
            }
        }
    }

    /**
     * Sends the mail when a video is uploaded
     * 
     * @param integer $inUserID
     * @param integer $inMovieID
     * @return boolean
     */
    private function sendCreditEmail($inUserID, $inEmail, $inMovieTitle, $inName, $inRole, $inMessage, $inLanguage) {

        $link = $this->getShortUri($this->getUserID(), true);
        if ($inMessage == 0) {
            $oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(0, mofilmMessages::MSG_GRP_USR_CREDIT, $inLanguage);
        } else {
            $oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(0, mofilmMessages::MSG_GRP_USR_CREDIT_NEWREGISTER, $inLanguage);
        }
        commsOutboundManager::setCustomerInMessageStack($oQueue, $inUserID);
        commsOutboundManager::setRecipientInMessageStack($oQueue, $inEmail);
        commsOutboundManager::replaceDataInMessageStack($oQueue, array('%MOVIE_TITLE%', '%EMAIL_ADDRESS%', '%ROLE%', '%LINK%'), array($inMovieTitle, $inName, $inRole, $link));
        return $oQueue->send();
    }

    /**
     * Returns an instance of mofilmMovieBroadcastSet, which is lazy loaded upon request
     *
     * @return mofilmMovieBroadcastSet
     */
    function getBroadcastSet() {
        if (!$this->_BroadcastSet instanceof mofilmMovieBroadcastSet) {
            $this->_BroadcastSet = new mofilmMovieBroadcastSet($this->getID());
        }
        return $this->_BroadcastSet;
    }

    /**
     * Set the pre-loaded object to the class
     *
     * @param mofilmMovieBroadcastSet $inObject
     * @return mofilmMovie
     */
    function setBroadcastSet(mofilmMovieBroadcastSet $inObject) {
        $this->_BroadcastSet = $inObject;
        return $this;
    }

}
