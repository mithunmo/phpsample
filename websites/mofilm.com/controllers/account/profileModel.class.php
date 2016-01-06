<?php
/**
 * profileModel.class.php
 * 
 * profileModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category profileModel
 * @version $Rev: 11 $
 */


/**
 * profileModel class
 * 
 * Provides the "account" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category profileModel
 */
class profileModel extends mvcModelBase {
	
	/**
	 * Stores $_Request
	 *
	 * @var mvcRequest
	 * @access protected
	 */
	protected $_Request;

	/**
	 * Stores $_Message
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Message;
	
	/**
	 * Stores $_User
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_User;

	/**
	 * Stores $_Updated
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Updated;
	
	/**
	 * Stores $_Language
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Language;



	/**
	 * Creates a new account model object
	 */
	function __construct() {
		$this->reset();
	}

	/**
	 * Resets the model
	 *
	 * @return void
	 */
	function reset() {
		$this->_Request = null;
		$this->_Message = null;
		$this->_User = null;
		$this->_Updated = false;
		$this->_Language = 'en';
		$this->setModified(false);
	}
	
	/**
	 * Looks up the visiting users country and returns a territory object
	 * 
	 * @return mofilmTerritory
	 */
	function getGeoLocatedCountry() {
		$isoCn = mofilmUtilities::getCountryFromIpAddress();
		if ( !$isoCn ) {
			$isoCn = 'GB';
		}
		return mofilmTerritory::getInstanceByShortName($isoCn);
	}

	/**
	 * Searches for only approved movies, returning the search result set
	 *
	 * By using limit and offset you can page through a users videos.
	 * Note: this method always returns a mofilmMovieSearchResult and
	 * this is always a new instance.
	 * 
	 * @return mofilmMovieSearchResult
	 */
	function getApprovedMovies() {
		$oSearch = new mofilmMovieSearch();
		$oSearch->setEnforceStatusRestrictions(false);
		$oSearch->setStatus(mofilmMovieBase::STATUS_APPROVED);
		$oSearch->setUser($this->getUser());
		$oSearch->setUserID($this->getUser()->getID());
		$oSearch->setOnlyActiveMovies(true);
		$oSearch->setOffset(0);
		$oSearch->setLimit(200);
		$oSearch->setOrderBy(mofilmMovieSearch::ORDERBY_DATE);
		return $oSearch->search();
	}

	/**
	 * Updates the user profile
	 *
	 * @return boolean
	 */
	function update() {
		if ( $this->getUser()->isModified() ) {
			try {
				$res = $this->getUser()->save();
				if ( $res ) {
					$this->setMessage('Profile updated successfully');
					$this->setUpdated(true);
				} else {
					$this->setMessage('Profile update failed');
				}
			} catch ( Exception $e ) {
				$this->setMessage('Profile update failed: '.$e->getMessage());
			}
		} else {
			$this->setMessage('No updates to make to profile');
			$this->setUpdated(0);
		}
	}
	
	/**
	 * Ensures that a password is between bounds
	 * 
	 * @param string $inPassword
	 * @param string $inConfirmedPassword
	 * @return boolean
	 * @throws mofilmException
	 */
	function checkPassword($inPassword, $inConfirmedPassword) {
		if ( $inPassword != $inConfirmedPassword ) {
			throw new mofilmException("Please confirm your password by re-entering it in the Confirm Password box");
		}
		if ( strlen($inPassword) < 6 ) {
			throw new mofilmException("Your password must be at least 6 characters");
		}
		return true;
	}
	
	
	
	/**
	 * Returns $_Request
	 *
	 * @return mvcRequest
	 */
	function getRequest() {
		return $this->_Request;
	}
	
	/**
	 * Set $_Request to $inRequest
	 *
	 * @param mvcRequest $inRequest
	 * @return profileModel
	 */
	function setRequest($inRequest) {
		if ( $inRequest !== $this->_Request ) {
			$this->_Request = $inRequest;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Message
	 *
	 * @return string
	 */
	function getMessage() {
		return $this->_Message;
	}

	/**
	 * Set $_Message to $inMessage
	 *
	 * @param string $inMessage
	 * @return profileModel
	 */
	function setMessage($inMessage) {
		if ( $inMessage !== $this->_Message ) {
			$this->_Message = $inMessage;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_User
	 *
	 * @return mofilmUser
	 */
	function getUser() {
		return $this->_User;
	}

	/**
	 * Set $_User to $inUser
	 *
	 * @param mofilmUser $inUser
	 * @return profileModel
	 */
	function setUser(mofilmUser $inUser) {
		if ( $inUser !== $this->_User ) {
			$this->_User = $inUser;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Updated
	 *
	 * @return boolean
	 * @access public
	 */
	function isUpdated() {
		return $this->_Updated;
	}

	/**
	 * Set $_Updated to $inUpdated
	 *
	 * @param boolean $inUpdated
	 * @return profileModel
	 * @access public
	 */
	function setUpdated($inUpdated) {
		if ( $inUpdated !== $this->_Updated ) {
			$this->_Updated = $inUpdated;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Language
	 *
	 * @return string
	 */
	function getLanguage() {
		return $this->_Language;
	}
	
	/**
	 * Set $_Language to $inLanguage
	 *
	 * @param string $inLanguage
	 * @return profileModel
	 */
	function setLanguage($inLanguage) {
		if ( $inLanguage !== $this->_Language ) {
			$this->_Language = $inLanguage;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 *
	 * Checks if role is present or not
	 * 
	 * 
	 * @param string $inRole 
	 */
	function getSkillData($inRole) {
		
		
		$str = $this->getUser()->getParamSet()->getParam('Skills');
		$arr = explode(", ", $str);
		return in_array($inRole, $arr);
		
	}
}