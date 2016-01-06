<?php
/**
 * userModel.class.php
 * 
 * userModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category userModel
 * @version $Rev: 259 $
 */


/**
 * userModel class
 * 
 * Provides the "user" page
 * 
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category userModel
 */
class userModel extends mvcModelBase {
	
	/**
	 * Stores $_ProfileName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ProfileName;
	
	/**
	 * Stores $_User
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_User;

	/**
	 * Stores $_Country
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Country;

	/**
	 * Stores $_Page
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Page;

	/**
	 * Stores $_LeaderboardSearch
	 *
	 * @var mofilmLeaderboard
	 * @access protected
	 */
	protected $_LeaderboardSearch;

	/**
	 * Stores $_Leaderboard
	 *
	 * @var mofilmLeaderboardResult
	 * @access protected
	 */
	protected $_Leaderboard;

	/**
	 * Stores $_Keyword
	 *
	 * @var mofilmLeaderboardResult
	 * @access protected
	 */	
	protected $_Keyword;
	
	
	protected $_CrewResults;
	
	
	protected $_SolrSearch;
	
	protected $_Location;
	
	protected $_Skill;

	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * Resets the object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_ProfileName = null;
		$this->_User = null;
		$this->_Country = null;
		$this->_Page = 1;
		$this->_LeaderboardSearch = null;
		$this->_Leaderboard = null;
		$this->setModified(false);
	}
	
	
	
	/**
	 * Returns $_ProfileName
	 *
	 * @return string
	 */
	function getProfileName() {
		return $this->_ProfileName;
	}
	
	/**
	 * Set $_ProfileName to $inProfileName
	 *
	 * @param string $inProfileName
	 * @return userModel
	 */
	function setProfileName($inProfileName) {
		if ( $inProfileName !== $this->_ProfileName ) {
			$this->_ProfileName = $inProfileName;
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
		if ( !$this->_User instanceof mofilmUser ) {
			$this->_User = mofilmUserManager::getInstanceByProfileName($this->getProfileName());
			if ( !$this->_User instanceof mofilmUser ) {
				throw new mvcDistributorInvalidRequestException($this->getProfileName());
			}
		}
		return $this->_User;
	}
	
	/**
	 * Set $_User to $inUser
	 *
	 * @param mofilmUser $inUser
	 * @return userModel
	 */
	function setUser($inUser) {
		if ( $inUser !== $this->_User ) {
			$this->_User = $inUser;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Keyword
	 *
	 * @return string
	 */
	function getKeyword() {
		return $this->_Keyword;
	}
	
	/**
	 * Set $_Keyword to $inKeyword
	 *
	 * @param string $inKeyword
	 * @return userModel
	 */
	function setKeyword($inKeyword) {
		if ( $inKeyword !== $this->_Keyword ) {
			$this->_Keyword = $inKeyword;
			$this->setModified();
		}
		return $this;
	}
	
	
	/**
	 * Returns an array of countries with winners
	 *
	 * @return array
	 */
	function getCountryList() {
		return mofilmLeaderboardUtilities::getCountriesWithWinners();
	}

	/**
	 * Returns the value of $_Country
	 *
	 * @return string
	 */
	function getCountry() {
		return $this->_Country;
	}

	/**
	 * Returns the territory object from the country code
	 *
	 * @return mofilmTerritory
	 */
	function getTerritory() {
		return mofilmTerritory::getInstanceByShortName($this->getCountry());
	}

	/**
	 * Set $_Country to $inCountry
	 *
	 * @param string $inCountry
	 * @return userModel
	 */
	function setCountry($inCountry) {
		if ( $inCountry !== $this->_Country ) {
			$this->_Country = $inCountry;
			$this->setModified();
		}
		return $this;
	}



	/**
	 * Returns the value of $_Page
	 *
	 * @return integer
	 */
	function getPage() {
		return $this->_Page;
	}

	/**
	 * Returns the last page of the results
	 *
	 * @return integer
	 */
	function getLastPage() {
		$maxResults = $this->getLeaderboard()->getTotalResults();

		if ( $maxResults > 0 ) {
			return (int) ceil($maxResults/$this->getLeaderboardSearch()->getLimit());
		} else {
			return 1;
		}
	}

	/**
	 * Returns the value of $_Offset
	 *
	 * @return integer
	 */
	function getOffset() {
		if ( $this->getPage() && $this->getPage() > 0 ) {
			$offset = $this->getLeaderboardSearch()->getLimit()*($this->getPage()-1);
		} else {
			$offset = 0;
		}

		return $offset;
	}

	/**
	 * Set $_Page to $inPage
	 *
	 * @param integer $inPage
	 * @return userModel
	 */
	function setPage($inPage) {
		if ( $inPage !== $this->_Page ) {
			$this->_Page = $inPage;
			$this->setModified();
		}
		return $this;
	}



	/**
	 * Returns the current leaderboard page
	 *
	 * @return array(mofilmUser)
	 */
	function getAllTimeLeaderboard() {
		$this->getLeaderboardSearch()->setAllTimeUsers(true);
		$this->getLeaderboardSearch()->setOffset($this->getOffset());
		$this->setLeaderboard($this->getLeaderboardSearch()->search());
		return $this->getLeaderboard();
	}


	/**
	 * Returns the results page
	 *
	 * @return array(mofilmUser)
	 */
	function getSearchLeaderboard() {
		if ( strlen( $this->getKeyword() ) >= 3 ) {
			$this->getLeaderboardSearch()->setKeywords($this->getKeyword());
			$this->getLeaderboardSearch()->setOffset($this->getOffset());
			$this->setLeaderboard($this->getLeaderboardSearch()->search());
			return $this->getLeaderboard();
		}
	}
	
	
	
	/**
	 * Returns the specified country leaderboard
	 *
	 * @return mofilmLeaderboardResult
	 */
	function getCountryLeaderboard() {
		$this->getLeaderboardSearch()->setAllTimeUsers(true);
		$this->getLeaderboardSearch()->setTerritoryID($this->getTerritory()->getID());
		$this->getLeaderboardSearch()->setOffset($this->getOffset());

		$this->setLeaderboard($this->getLeaderboardSearch()->search());
		return $this->getLeaderboard();
	}

	/**
	 * Returns the value of $_Leaderboard
	 *
	 * @return mofilmLeaderboard
	 */
	function getLeaderboardSearch() {
		if ( !$this->_LeaderboardSearch instanceof mofilmLeaderboard ) {
			$this->_LeaderboardSearch = new mofilmLeaderboard();
			$this->_LeaderboardSearch->setOffset(0);
			$this->_LeaderboardSearch->setLimit(20);
		}

		return $this->_LeaderboardSearch;
	}

	/**
	 * Set $_Leaderboard to $inLeaderboard
	 *
	 * @param mofilmLeaderboard $inLeaderboard
	 * @return userModel
	 */
	function setLeaderboardSearch($inLeaderboard) {
		if ( $inLeaderboard !== $this->_LeaderboardSearch ) {
			$this->_LeaderboardSearch = $inLeaderboard;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_Leaderboard
	 *
	 * @return mofilmLeaderboardResult
	 */
	function getLeaderboard() {
		if ( !$this->_Leaderboard instanceof mofilmLeaderboardResult ) {
			$this->getLeaderboardSearch()->setOffset($this->getOffset());
			$this->setLeaderboard($this->getLeaderboardSearch()->search());
		}

		return $this->_Leaderboard;
	}

	/**
	 * Set $_Leaderboard to $inLeaderboard
	 *
	 * @param mofilmLeaderboardResult $inLeaderboard
	 * @return userModel
	 */
	function setLeaderboard($inLeaderboard) {
		if ( $inLeaderboard !== $this->_Leaderboard ) {
			$this->_Leaderboard = $inLeaderboard;
			$this->setModified();
		}
		return $this;
	}
		
	
	/**
	 * Set $_VideoResults to $inResult
	 *
	 * @param array $inResult
	 * @return 
	 */
	function setSolrResults($inResult) {
		if ( $inResult !== $this->_CrewResults ) {
			$this->_CrewResults = $inResult;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns solr results
	 *
	 * @return array
	 */
	function getSolrResults() {
		return $this->_CrewResults;
	}
	
	/**
	 * Gets the last page offset
	 * 
	 * @param type $inLimit
	 * @return type int
	 */
	function getSolrLastPageOffset($inLimit) {
		$total = $this->getSolrResults()->getTotalResults();
		if ( $inLimit > 0 ) {
			$val =  ceil($total/$inLimit);			
			return $val;
		} else {
			return 0;
		}
	}

	
	/**
	 * Gets the last page offset
	 * 
	 * @param type $inLimit
	 * @return type int
	 */
	function getSolrTotal() {
		return $total = $this->getSolrResults()->getTotalResults();
	}
	
	
	/**
	 * get the video search class object
	 * 
	 * @return type mofilmVideoSearch
	 */
	function getSolrCrewSearch() {
		if ( !$this->_SolrSearch instanceof mofilmCrewSearch ) {
			$this->_SolrSearch = new mofilmCrewSearch();
		}
		return $this->_SolrSearch;
	}
	
	/**
	 * Performs the video search using solr
	 * 
	 * @return type array
	 */
	function doSolrCrewSearch() {
		$this->getSolrCrewSearch()->setStart($this->getSolrOffset());
		if ( $this->getLocation() || $this->getSkill() ) {
			
			if ( $this->getSkill() ) {
				$this->getSolrCrewSearch()->setSkill($this->getSkill());
			}
			if ($this->getLocation() ) {
				$this->getSolrCrewSearch()->setLocation($this->getLocation());
			}
		} else {
			$this->getSolrCrewSearch()->setKeyword($this->getKeyword());
		}
		$this->getSolrCrewSearch()->search();
		$this->_CrewResults = $this->getSolrCrewSearch()->getResultList();		
		return $this->_CrewResults;
	}
	
	
	/**
	 * Returns the last page of the results
	 *
	 * @return integer
	 */
	function getSolrLastPage() {
		
		$maxResults = $this->getSolrResults();

		if ( $maxResults > 0 ) {
			return (int) ceil($maxResults/$this->getLeaderboardSearch()->getLimit());
		} else {
			return 1;
		}
	}

	/**
	 * Returns the value of $_Offset
	 *
	 * @return integer
	 */
	function getSolrOffset() {
		if ( $this->getPage() && $this->getPage() > 0 ) {
			$offset = 30 *($this->getPage()-1);
		} else {
			$offset = 0;
		}

		return $offset;
	}
	/** 
	 * Gets the user from userID
	 * 
	 * @param type $inUserID
	 * @return type 
	 */
	function getUserProfile($inUserID){
		return mofilmUserManager::getInstanceByID($inUserID);
	}

	
	/**
	 * Returns $_Location
	 *
	 * @return string
	 */
	function getLocation() {
		return $this->_Location;
	}
	
	/**
	 * Set $_Location to $inLocation
	 *
	 * @param string $inLocation
	 * @return userModel
	 */
	function setLocation($inLocation) {
		if ( $inLocation !== $this->_Location ) {
			$this->_Location = $inLocation;
			$this->setModified();
		}
		return $this;
	}
	

	/**
	 * Returns $_Location
	 *
	 * @return string
	 */
	function getSkill() {
		return $this->_Skill;
	}
	
	/**
	 * Set $_Skill to $inSkill
	 *
	 * @param string $inSkill
	 * @return userModel
	 */
	function setSkill($inSkill) {
		if ( $inSkill !== $this->_Skill) {
			$this->_Skill = $inSkill;
			$this->setModified();
		}
		return $this;
	}	
}