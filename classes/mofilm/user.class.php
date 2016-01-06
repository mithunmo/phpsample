<?php
/**
 * mofilmUser
 * 
 * Stored in user.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUser
 * @category mofilmUser
 * @version $Rev: 336 $
 */


/**
 * mofilmUser Class
 * 
 * The main mofilm User class.
 * 
 * @package mofilm
 * @subpackage mofilmUser
 * @category mofilmUser
 */
class mofilmUser extends mofilmUserBase {
	
	const PARAM_ADDRESS1 = 'Address1';
	const PARAM_ADDRESS2 = 'Address2';
	const PARAM_CITY = 'City';
	const PARAM_COMPANY = 'Company';
	const PARAM_COUNTY = 'County';
	const PARAM_DATE_OF_BIRTH = 'DateOfBirth';
	const PARAM_DESCRIPTION = 'Description';
	const PARAM_FLAG_UNRATED_MOVIES = 'FlagUnratedMovies';
	const PARAM_LANGUAGE = 'Language';
	const PARAM_OCCUPATION = 'Occupation';
	const PARAM_MOBILE_PHONE = 'MobilePhone';
	const PARAM_PHONE = 'Phone';
	const PARAM_POSTCODE = 'Postcode';
	const PARAM_PROFILE_TEXT = 'ProfileText';
	const PARAM_PRIVATE_MESSAGE_ALERTS = 'PrivateMessageAlerts';
	const PARAM_SIGNUP_CODE = 'SignupCode';
	const PARAM_SKILLS = 'Skills';
	const PARAM_SKYPE = 'Skype';
	const PARAM_UNIVERSITY = 'University';
	const PARAM_WEBSITE = 'Website';
	const PARAM_SHOW_REEL_URL = 'ShowReelURL';
	const PARAM_REGISTRATION_SOURCE = 'RegistrationSource';
	const PARAM_MOMUSIC_SOURCE = 'audiosocketID';
	const PARAM_LAT = "lat";
	const PARAM_LONG = "long";
	const PARAM_MOFILMMAKER = "MM";
	const PARAM_REFERRED = "Refer";
	const PARAM_SCHOOL_NAME = "SchoolName";
        const PARAM_SHOWREEL_URL = "ShowReelURL";
	
	
	/**
	 * Stores the current permission namespace
	 *
	 * @var string
	 * @access protected
	 */
	protected $_PermissionsNamespace;

	/**
	 * Stores an instance of baseTableParamSet
	 *
	 * @var baseTableParamSet
	 * @access protected
	 */
	protected $_ParamSet;

	/**
	 * Stores an instance of mofilmUserAffiliate
	 *
	 * @var mofilmUserAffiliate
	 * @access protected
	 */
	protected $_Affiliate;

	/**
	 * Stores an instance of mofilmUserAvatar
	 *
	 * @var mofilmUserAvatar
	 * @access protected
	 */
	protected $_Avatar;

	/**
	 * Stores an instance of mofilmUserAwardSet
	 *
	 * @var mofilmUserAwardSet
	 * @access protected
	 */
	protected $_AwardSet;
	
	/**
	 * Stores an instance of mofilmUserCreditsAwardSet
	 *
	 * @var mofilmUserCreditsAwardSet
	 * @access protected
	 */
	protected $_CreditAwardSet;

	/**
	 * Stores an instance of mofilmUserDownloadSet
	 *
	 * @var mofilmUserDownloadSet
	 * @access protected
	 */
	protected $_DownloadSet;

	/**
	 * Stores an instance of mofilmUserLogSet
	 *
	 * @var mofilmUserLogSet
	 * @access protected
	 */
	protected $_LogSet;
	
	/**
	 * Stores an instance of mofilmPermissionGroup
	 * 
	 * @var mofilmPermissionGroup
	 * @access protected
	 */
	protected $_PermissionGroup;

	/**
	 * Stores an instance of mofilmUserPermissions
	 *
	 * @var mofilmUserPermissions
	 * @access protected
	 */
	protected $_Permissions;

	/**
	 * Stores an instance of mofilmUserPoints
	 *
	 * @var mofilmUserPoints
	 * @access protected
	 */
	protected $_Points;

	/**
	 * Stores an instance of mofilmUserProfile
	 *
	 * @var mofilmUserProfile
	 * @access protected
	 */
	protected $_Profile;
	
	/**
	 * Stores an instance of mofilmUserSourceSet
	 *
	 * @var mofilmUserSourceSet
	 * @access protected
	 */
	protected $_SourceSet;

	/**
	 * Stores an instance of mofilmUserTeamSet
	 *
	 * @var mofilmUserTeamSet
	 * @access protected
	 */
	protected $_TeamSet;

	/**
	 * Stores an instance of mofilmUserTermsSet
	 *
	 * @var mofilmUserTermsSet
	 * @access protected
	 */
	protected $_TermsSet;

	/**
	 * Stores an instance of mofilmUserMovieStats
	 *
	 * @var mofilmUserMovieStats
	 * @access protected
	 */
	protected $_Stats;
	
	/**
	 * Stores an instance of mofilmUserMovieGrantsStats
	 *
	 * @var mofilmUserMovieGrantsStats
	 * @access protected
	 */
	protected $_GrantsStats;
	
	/**
	 * Stores an instance of mofilmUserFavourites
	 *
	 * @var mofilmUserFavourites
	 * @access protected
	 */
	protected $_Favourites;

	/**
	 * Stores an instance of mofilmUserEventFavourites
	 *
	 * @var mofilmUserEventFavourites
	 * @access protected
	 */
	protected $_EventFavourites;

	/**
	 * Stores an instance of mofilmUserEventFilter
	 *
	 * @var mofilmUserEventFilter
	 * @access protected
	 */
	protected $_EventFilter;
	
	/**
	 * Stores an instance of mofilmUserProfileMovieSet
	 *
	 * @var mofilmUserProfileMovieSet
	 * @access protected
	 */
	protected $_ProfileMovieSet;
	
	
	
	/**
	 * Saves object to the table
	 * 
	 * @return boolean
	 */
	function save() {
		$return = true;
		if ( $this->isModified() ) {
			$return = parent::save() && $return;

			if ( $this->_ParamSet instanceof baseTableParamSet ) {
				$this->_ParamSet->setIndexID($this->getID());
				$this->_ParamSet->save();
			}
			if ( $this->_Affiliate instanceof mofilmUserAffiliate ) {
				$this->_Affiliate->setUserID($this->getID());
				$this->_Affiliate->save();
			}
			if ( $this->_Avatar instanceof mofilmUserAvatar ) {
				$this->_Avatar->setUserID($this->getID());
				$this->_Avatar->save();
			}
			if ( $this->_AwardSet instanceof mofilmUserAwardSet ) {
				$this->_AwardSet->setUserID($this->getID());
				$this->_AwardSet->save();
			}
			if ( $this->_DownloadSet instanceof mofilmUserDownloadSet ) {
				$this->_DownloadSet->setUserID($this->getID());
				$this->_DownloadSet->save();
			}
			if ( $this->_LogSet instanceof mofilmUserLogSet ) {
				$this->_LogSet->setUserID($this->getID());
				$this->_LogSet->save();
			}
			if ( $this->_Permissions instanceof mofilmUserPermissions ) {
				$this->_Permissions->setUserID($this->getID());
				$this->_Permissions->save();
			}
			if ( $this->_TeamSet instanceof mofilmUserTeamSet ) {
				$this->_TeamSet->setUserID($this->getID());
				$this->_TeamSet->save();
			}
			if ( $this->_TermsSet instanceof mofilmUserTermsSet ) {
				$this->_TermsSet->setUserID($this->getID());
				$this->_TermsSet->save();
			}
			if ( $this->_Favourites instanceof mofilmUserFavourites ) {
				$this->_Favourites->setUserID($this->getID());
				$this->_Favourites->save();
			}
			if ( $this->_EventFavourites instanceof mofilmUserEventFavourites ) {
				$this->_EventFavourites->setUserID($this->getID());
				$this->_EventFavourites->save();
			}
			if ( $this->_EventFilter instanceof mofilmUserEventFilter ) {
				$this->_EventFilter->setUserID($this->getID());
				$this->_EventFilter->save();
			}
			if ( $this->_Profile instanceof mofilmUserProfile ) {
				$this->_Profile->setUserID($this->getID());
				$this->_Profile->save();
			}
			if ( $this->_ProfileMovieSet instanceof mofilmUserProfileMovieSet ) {
				$this->_ProfileMovieSet->setUserID($this->getID());
				$this->_ProfileMovieSet->save();
			}
			if ( $this->_Points instanceof mofilmUserPoints ) {
				$this->_Points->setUserID($this->getID());
				$this->_Points->save();
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
		if ( $this->getID() ) {
			$this->getParamSet()->deleteAll();
			$this->getAffiliate()->delete();
			$this->getAvatar()->delete();
			$this->getAwardSet()->delete();
			$this->getDownloadSet()->delete();
			$this->getLogSet()->delete();
			$this->getPermissions()->delete();
			$this->getTeamSet()->delete();
			$this->getTermsSet()->delete();
			$this->getFavourites()->delete();
			$this->getEventFavourites()->delete();
			$this->getEventFilter()->delete();
			$this->getProfile()->delete();
			$this->getProfileMovieSet()->delete();

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
		$this->_ParamSet = null;
		$this->_Affiliate = null;
		$this->_Avatar = null;
		$this->_AwardSet = null;
		$this->_CreditAwardSet = null;
		$this->_DownloadSet = null;
		$this->_Favourites = null;
		$this->_EventFavourites = null;
		$this->_LogSet = null;
		$this->_PermissionGroup = null;
		$this->_Permissions = null;
		$this->_Points = null;
		$this->_SourceSet = null;
		$this->_Stats = null;
		$this->_TeamSet = null;
		$this->_TermsSet = null;
		$this->_ProfileMovieSet = null;
		parent::reset();
	}
	
	
	
	/**
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( !$modified && $this->_ParamSet instanceof baseTableParamSet ) {
			$modified = $this->_ParamSet->isModified() || $modified;
		}
		if ( !$modified && $this->_Affiliate instanceof mofilmUserAffiliate ) {
			$modified = $this->_Affiliate->isModified() || $modified;
		}
		if ( !$modified && $this->_Avatar instanceof mofilmUserAvatar ) {
			$modified = $this->_Avatar->isModified() || $modified;
		}
		if ( !$modified && $this->_AwardSet instanceof mofilmUserAwardSet ) {
			$modified = $modified || $this->_AwardSet->isModified();
		}
		if ( !$modified && $this->_DownloadSet instanceof mofilmUserDownloadSet ) {
			$modified = $this->_DownloadSet->isModified() || $modified;
		}
		if ( !$modified && $this->_LogSet instanceof mofilmUserLogSet ) {
			$modified = $this->_LogSet->isModified() || $modified;
		}
		if ( !$modified && $this->_Permissions instanceof mofilmUserPermissions ) {
			$modified = $this->_Permissions->isModified() || $modified;
		}
		if ( !$modified && $this->_TeamSet instanceof mofilmUserTeamSet ) {
			$modified = $this->_TeamSet->isModified() || $modified;
		}
		if ( !$modified && $this->_TermsSet instanceof mofilmUserTermsSet ) {
			$modified = $this->_TermsSet->isModified() || $modified;
		}
		if ( !$modified && $this->_Favourites instanceof mofilmUserFavourites ) {
			$modified = $this->_Favourites->isModified() || $modified;
		}
		if ( !$modified && $this->_EventFavourites instanceof mofilmUserEventFavourites ) {
			$modified = $modified || $this->_EventFavourites->isModified();
		}
		if ( !$modified && $this->_EventFilter instanceof mofilmUserEventFilter ) {
			$modified = $modified || $this->_EventFilter->isModified();
		}
		if ( !$modified && $this->_Profile instanceof mofilmUserProfile ) {
			$modified = $modified || $this->_Profile->isModified();
		}
		if ( !$modified && $this->_ProfileMovieSet instanceof mofilmUserProfileMovieSet ) {
			$modified = $modified || $this->_ProfileMovieSet->isModified();
		}
		if ( !$modified && $this->_Points instanceof mofilmUserPoints ) {
			$modified = $modified || $this->_Points->isModified();
		}
		return $modified;
	}

	/**
	 * Returns true if user is authorised to access $inPermission
	 *
	 * @param mixed $inPermission
	 * @return boolean
	 */
	function isAuthorised($inPermission) {
		if ( $inPermission instanceof mofilmPermission ) {
			$inPermission = $inPermission->getName();
		}
		return $this->getPermissions()->isAuthorised($inPermission);
	}
	
	/**
	 * Returns true if the user can see eventID $inEventID
	 * 
	 * @param integer $inEventID
	 * @return boolean
	 */
	function hasEvent($inEventID) {
		if ( $this->getClientID() == mofilmClient::MOFILM || $this->getPermissions()->isRoot() || in_array($inEventID, $this->getSourceSet()->getEventIDs()) ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Returns the parameters object, loading it if not already loaded
	 *
	 * @return baseTableParamSet
	 */
	function getParamSet() {
		if ( !$this->_ParamSet instanceof baseTableParamSet ) {
			$this->_ParamSet = new baseTableParamSet(
				system::getConfig()->getDatabase('mofilm_content'), 'userData', 'userID', 'paramName', 'paramValue', $this->getID(), false
			);
		}
		return $this->_ParamSet;
	}

	/**
	 * Sets an pre-built param set to the object
	 *
	 * @param baseTableParamSet $inParamSet
	 * @return mofilmUser
	 */
	function setParamSet(baseTableParamSet $inParamSet) {
		$this->_ParamSet = $inParamSet;
		return $this;
	}

	/**
	 * Returns the instance of mofilmUserAffiliate
	 *
	 * @return mofilmUserAffiliate
	 */
	function getAffiliate() {
		if ( !$this->_Affiliate instanceof mofilmUserAffiliate ) {
			$this->_Affiliate = new mofilmUserAffiliate($this->getID());
		}
		return $this->_Affiliate;
	}

	/**
	 * Sets a complete instance of mofilmUserAffiliate
	 *
	 * @param mofilmUserAffiliate $inAffiliate
	 * @return mofilmUser
	 */
	function setAffiliate(mofilmUserAffiliate $inAffiliate) {
		$this->_Affiliate = $inAffiliate;
		return $this;
	}

	/**
	 * Returns the avatar instance
	 *
	 * @return mofilmUserAvatar
	 */
	function getAvatar() {
		if ( !$this->_Avatar instanceof mofilmUserAvatar ) {
			$this->_Avatar = new mofilmUserAvatar($this->getID());
		}
		return $this->_Avatar;
	}

	/**
	 * Sets a complete instance of mofilmUserAvatar
	 *
	 * @param mofilmUserAvatar $inAvatar
	 * @return mofilmUser
	 */
	function setAvatar(mofilmUserAvatar $inAvatar) {
		$this->_Avatar = $inAvatar;
		return $this;
	}

	/**
	 * Returns the mofilmUserAwardSet object, loading it if not already set
	 *
	 * @return mofilmUserAwardSet
	 */
	function getAwardSet() {
		if ( !$this->_AwardSet instanceof mofilmUserAwardSet ) {
			$this->_AwardSet = new mofilmUserAwardSet($this->getID());
		}
		return $this->_AwardSet;
	}

	/**
	 * Set the mofilmUserAwardSet object directly
	 *
	 * @param mofilmUserAwardSet $inObject
	 * @return mofilmUser
	 */
	function setAwardSet(mofilmUserAwardSet $inObject) {
		$this->_AwardSet = $inObject;
		return $this;
	}
	
	/**
	 * Returns the mofilmUserContributorAwardSet object, loading it if not already set
	 *
	 * @return mofilmUserContributorAwardSet
	 */
	function getCreditAwardSet() {
		if ( !$this->_CreditAwardSet instanceof mofilmUserContributorAwardSet ) {
			$this->_CreditAwardSet = new mofilmUserContributorAwardSet($this->getID());
		}
		return $this->_CreditAwardSet;
	}

	/**
	 * Returns the mofilmUserDownloadSet
	 *
	 * @return mofilmUserDownloadSet
	 */
	function getDownloadSet() {
		if ( !$this->_DownloadSet instanceof mofilmUserDownloadSet ) {
			$this->_DownloadSet = new mofilmUserDownloadSet($this->getID());
		}
		return $this->_DownloadSet;
	}

	/**
	 * Sets a pre-built download set
	 *
	 * @param mofilmUserDownloadSet $inSet
	 * @return mofilmUser
	 */
	function setDownloadSet(mofilmUserDownloadSet $inSet) {
		$this->_DownloadSet = $inSet;
		return $this;
	}

	/**
	 * Returns the mofilmUserLogSet
	 *
	 * @return mofilmUserLogSet
	 */
	function getLogSet() {
		if ( !$this->_LogSet instanceof mofilmUserLogSet ) {
			$this->_LogSet = new mofilmUserLogSet($this->getID());
		}
		return $this->_LogSet;
	}

	/**
	 * Sets a pre-built log set
	 *
	 * @param mofilmUserLogSet $inSet
	 * @return mofilmUser
	 */
	function setLogSet(mofilmUserLogSet $inSet) {
		$this->_LogSet = $inSet;
		return $this;
	}

	/**
	 * Returns the mofilmUserPermissions
	 *
	 * @return mofilmUserPermissions
	 */
	function getPermissions() {
		if ( !$this->_Permissions instanceof mofilmUserPermissions ) {
			$this->_Permissions = new mofilmUserPermissions($this->getID(), $this->getPermissionsNamespace());
		}
		return $this->_Permissions;
	}

	/**
	 * Sets a pre-built permissions set
	 *
	 * @param mofilmUserPermissions $inSet
	 * @return mofilmUser
	 */
	function setPermissions(mofilmUserPermissions $inSet) {
		$this->_Permissions = $inSet;
		return $this;
	}

	/**
	 * Returns the mofilmUserPoints object, loading it if not already set
	 *
	 * @return mofilmUserPoints
	 */
	function getPoints() {
		if ( !$this->_Points instanceof mofilmUserPoints ) {
			$this->_Points = mofilmUserPoints::getInstanceByUserID($this->getID());
		}
		return $this->_Points;
	}

	/**
	 * Set the mofilmUserPoints object directly
	 *
	 * @param mofilmUserPoints $inObject
	 * @return mofilmUser
	 */
	function setPoints(mofilmUserPoints $inObject) {
		$this->_Points = $inObject;
		return $this;
	}
	
	/**
	 * Returns the current users effective permission group
	 * 
	 * @return mofilmPermissionGroup
	 */
	function getPermissionGroup() {
		if ( !$this->_PermissionGroup instanceof mofilmPermissionGroup ) {
			$this->_PermissionGroup = mofilmPermissionGroup::getPermissionGroupFromUser($this->getID());
		}
		return $this->_PermissionGroup;
	}

	/**
	 * Returns the current permissions namespace
	 *
	 * @return string
	 */
	function getPermissionsNamespace() {
		return $this->_PermissionsNamespace;
	}

	/**
	 * Sets the permissions namespace to use when loading the permissions
	 *
	 * Note: setting the namespace causes the current permissions object
	 * to be removed.
	 *
	 * @param string $inNamespace
	 * @return mofilmUser
	 */
	function setPermissionsNamespace($inNamespace) {
		if ( $this->_PermissionsNamespace !== $inNamespace ) {
			$this->_PermissionsNamespace = $inNamespace;
			$this->_Permissions = null;
		}
		return $this;
	}

	/**
	 * Returns an instance of mofilmUserProfile, which is lazy loaded upon request
	 *
	 * @return mofilmUserProfile
	 */
	function getProfile() {
		if ( !$this->_Profile instanceof mofilmUserProfile ) {
			$this->_Profile = mofilmUserProfile::getUserActiveProfile($this->getID());
			if ( !$this->_Profile->getID() ) {
				$this->_Profile = mofilmUserProfile::getMostRecentUserProfile($this->getID());
			}
		}
		return $this->_Profile;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param mofilmUserProfile $inObject
	 * @return mofilmUser
	 */
	function setProfile(mofilmUserProfile $inObject) {
		$this->_Profile = $inObject;
		return $this;
	}

	/**
	 * Returns the mofilmUserSourceSet
	 *
	 * @return mofilmUserSourceSet
	 */
	function getSourceSet() {
		if ( !$this->_SourceSet instanceof mofilmUserSourceSet ) {
			$this->_SourceSet = new mofilmUserSourceSet($this->getID());
		}
		return $this->_SourceSet;
	}

	/**
	 * Sets a pre-built source set
	 *
	 * @param mofilmUserSourceSet $inSet
	 * @return mofilmUser
	 */
	function setSourceSet(mofilmUserSourceSet $inSet) {
		$this->_SourceSet = $inSet;
		return $this;
	}

	/**
	 * Returns the user stats object
	 *
	 * @return mofilmUserMovieStats
	 */
	function getStats() {
		if ( !$this->_Stats instanceof mofilmUserMovieStats ) {
			$this->_Stats = new mofilmUserMovieStats($this->getID());
		}
		return $this->_Stats;
	}

	/**
	 * Sets a pre-built stats object
	 *
	 * @param mofilmUserMovieStats $inStats
	 * @return mofilmUser
	 */
	function setStats(mofilmUserMovieStats $inStats) {
		$this->_Stats = $inStats;
		return $this;
	}
	
	/**
	 * Returns the user grants applied stats object
	 *
	 * @return mofilmUserMovieGrantsStats
	 */
	function getGrantStats() {
		if ( !$this->_GrantsStats instanceof mofilmUserMovieGrantsStats ) {
			$this->_GrantsStats = new mofilmUserMovieGrantsStats($this->getID());
		}
		return $this->_GrantsStats;
	}

	/**
	 * Sets a pre-built stats object
	 *
	 * @param mofilmUserMovieGrantsStats $inStats
	 * @return mofilmUser
	 */
	function setGrantStats(mofilmUserMovieGrantsStats $inStats) {
		$this->_GrantsStats = $inStats;
		return $this;
	}

	/**
	 * Returns the mofilmUserTeamSet
	 *
	 * @return mofilmUserTeamSet
	 */
	function getTeamSet() {
		if ( !$this->_TeamSet instanceof mofilmUserTeamSet ) {
			$this->_TeamSet = new mofilmUserTeamSet($this->getID());
		}
		return $this->_TeamSet;
	}

	/**
	 * Sets a pre-built team set
	 *
	 * @param mofilmUserTeamSet $inSet
	 * @return mofilmUser
	 */
	function setTeamSet(mofilmUserTeamSet $inSet) {
		$this->_TeamSet = $inSet;
		return $this;
	}

	/**
	 * Returns the mofilmUserTermsSet
	 *
	 * @return mofilmUserTermsSet
	 */
	function getTermsSet() {
		if ( !$this->_TermsSet instanceof mofilmUserTermsSet ) {
			$this->_TermsSet = new mofilmUserTermsSet($this->getID());
		}
		return $this->_TermsSet;
	}

	/**
	 * Sets a pre-built terms set
	 *
	 * @param mofilmUserTermsSet $inSet
	 * @return mofilmUser
	 */
	function setTermsSet(mofilmUserTermsSet $inSet) {
		$this->_TermsSet = $inSet;
		return $this;
	}

	/**
	 * Returns an instance of mofilmUserFavourites, which is lazy loaded upon request
	 *
	 * @return mofilmUserFavourites
	 */
	function getFavourites() {
		if ( !$this->_Favourites instanceof mofilmUserFavourites ) {
			$this->_Favourites = new mofilmUserFavourites($this->getID());
		}
		return $this->_Favourites;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param mofilmUserFavourites $inObject
	 * @return mofilmUser
	 */
	function setFavourites(mofilmUserFavourites $inObject) {
		$this->_Favourites = $inObject;
		return $this;
	}

	/**
	 * Returns the mofilmUserEventFavourites object, loading it if not already set
	 *
	 * @return mofilmUserEventFavourites
	 */
	function getEventFavourites() {
		if ( !$this->_EventFavourites instanceof mofilmUserEventFavourites ) {
			$this->_EventFavourites = new mofilmUserEventFavourites($this->getID());
		}
		return $this->_EventFavourites;
	}

	/**
	 * Set the mofilmUserEventFavourites object directly
	 *
	 * @param mofilmUserEventFavourites $inObject
	 * @return mofilmUser
	 */
	function setEventFavourites(mofilmUserEventFavourites $inObject) {
		$this->_EventFavourites = $inObject;
		return $this;
	}

	/**
	 * Returns an instance of mofilmUserEventFilter, which is lazy loaded upon request
	 *
	 * @return mofilmUserEventFilter
	 */
	function getEventFilter() {
		if ( !$this->_EventFilter instanceof mofilmUserEventFilter ) {
			$this->_EventFilter = new mofilmUserEventFilter($this->getID());
		}
		return $this->_EventFilter;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param mofilmUserEventFilter $inObject
	 * @return mofilmUser
	 */
	function setEventFilter(mofilmUserEventFilter $inObject) {
		$this->_EventFilter = $inObject;
		return $this;
	}

	/**
	 * Returns an instance of mofilmUserProfileMovieSet, which is lazy loaded upon request
	 *
	 * @return mofilmUserProfileMovieSet
	 */
	function getProfileMovieSet() {
		if ( !$this->_ProfileMovieSet instanceof mofilmUserProfileMovieSet ) {
			$this->_ProfileMovieSet = new mofilmUserProfileMovieSet($this->getID());
		}
		return $this->_ProfileMovieSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param mofilmUserProfileMovieSet $inObject
	 * @return mofilmUser
	 */
	function setProfileMovieSet(mofilmUserProfileMovieSet $inObject) {
		$this->_ProfileMovieSet = $inObject;
		return $this;
	}

	

	/**
	 * Searches for and returns movies for the current user
	 *
	 * By using limit and offset you can page through a users videos.
	 * Note: this method always returns a mofilmMovieSearchResult and
	 * this is always a new instance.
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return mofilmMovieSearchResult
	 */
	function getUserMovies($inOffset = 0, $inLimit = 5) {
		$oSearch = new mofilmMovieSearch();
		$oSearch->setEnforceStatusRestrictions(false);
		$oSearch->setStatus(mofilmMovieManager::getAvailableMovieStatuses());
		$oSearch->removeStatus(mofilmMovie::STATUS_REMOVED);
		$oSearch->setUser($this);
		$oSearch->setUserID($this->getID());
		$oSearch->setOffset($inOffset);
		$oSearch->setLimit($inLimit);
		$oSearch->setOrderBy(mofilmMovieSearch::ORDERBY_DATE);
		return $oSearch->search();
	}
	
	/**
	 * Gets the users rating for $inMovieID
	 * 
	 * @param integer $inMovieID
	 * @return mofilmMovieRating
	 */
	function getMovieRating($inMovieID) {
		return mofilmMovieRating::getInstance($inMovieID, $this->getID());
	}
	
	/**
	 * Returns true if the user has read $inMotd
	 * 
	 * @param integer $inMotd
	 * @return boolean
	 */
	function hasReadMotd($inMotd) {
		$oMotdLog = mofilmMotdLog::getInstance($inMotd, $this->getID());
		if ( $oMotdLog->getUserID() > 0 && $inMotd == $oMotdLog->getMotdID() ) {
			$return = true;
		} else {
			$return = false;
		}
		unset($oMotdLog);
		return $return;
	}
	/**
	 * Gets the contributor roles for a user
	 * 
	 * @return string
	 */
	function getContributorRoles() {
		
			$query = 'SELECT group_concat( DISTINCT description ) as role
						FROM 
						'.system::getConfig()->getDatabase('mofilm_content').'.contributors, 
						'.system::getConfig()->getDatabase('mofilm_content').'.movieContributors,
						'.system::getConfig()->getDatabase('mofilm_content').'.roles
						WHERE contributors.name = "' . $this->getEmail() .'"
						AND contributors.ID = movieContributors.contributorID
						AND roles.ID = movieContributors.roleID';
				
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
				}
			}
		return $row["role"];
		
	}
}