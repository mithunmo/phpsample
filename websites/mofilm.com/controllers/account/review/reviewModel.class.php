<?php
/**
 * reviewModel.class.php
 * 
 * reviewModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category reviewModel
 * @version $Rev: 623 $
 */


/**
 * reviewModel class
 * 
 * Provides the "review" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category reviewModel
 */
class reviewModel extends mvcModelBase {
	

	
	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	/**
	 * Stores $_Movie
	 *
	 * @var mofilmMovie
	 * @access protected
	 */
	protected $_Movie;

	/**
	 * Stores the last run search result
	 * 
	 * @var mofilmMovieSearchResult
	 * @access protected
	 */
	protected $_SearchResult;

	/**
	 * Stores an instance of mofilmMovieSearch
	 * 
	 * @var mofilmMovieSearch
	 * @access protected
	 */
	protected $_VideoSearch;
		
	
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Returns $_MovieID
	 *
	 * @return integer
	 */
	function getMovieID() {
		return $this->_MovieID;
	}
	
	/**
	 * Set $_MovieID to $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return videosModel
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
		}
		return $this;
	}
	
	
	
	/**
	 * Returns a mofilmMovie object if a movieID is present
	 *
	 * @return mofilmMovie
	 */
	function getMovie() {
		if ( !$this->_Movie instanceof mofilmMovie ) {
			if ( $this->getMovieID() ) {
				$this->_Movie = mofilmMovieManager::getInstance()->setLoadOnlyActive(false)->getMovieByID($this->getMovieID());
			} elseif ( $this->getSearchResult() ) {
				$this->_Movie = $this->getSearchResult()->getFirstResult();
			}
		}
		return $this->_Movie;
	}
	
	/**
	 * Commits the user movie record
	 * 
	 * @return void
	 */
	function commitUserMovie() {
		$this->getMovie()->setActive(mofilmMovieBase::ACTIVE_Y);
		$this->getMovie()->save();
	}
	
	/**
	 * Rejects the user movie record
	 * 
	 * @return void
	 */
	function rejectUserMovie() {
		$this->getMovie()->setStatus(mofilmMovieBase::STATUS_REJECTED);
		$this->getMovie()->save();
	}

	
	/**
	 * Checks if the movie has been encoded and waitint for the user approval
	 * 
	 * @return boolean
	 */
	function isCommitReady() {
		if ( $this->getMovie()->getStatus() ==  mofilmMovieBase::STATUS_PENDING && $this->getMovie()->getActive() == mofilmMovieBase::ACTIVE_N) {
			return true;
		} else {
			return false;
		}
		
	}
	

	/**
	 * Creates an returns a mofilmMovieSearch object
	 * 
	 * @return mofilmMovieSearch
	 */
	function getVideoSearch() {
		if ( !$this->_VideoSearch instanceof mofilmMovieSearch ) {
			$this->_VideoSearch = new mofilmMovieSearch();
			$this->_VideoSearch->setLoadMovieData(true);
			$this->_VideoSearch->setUserID($this->getUserID());
			$this->_VideoSearch->setUser(mofilmUserManager::getInstanceByID($this->getUserID()));
			$this->_VideoSearch->setEnforceStatusRestrictions(false);
			$oUser = mofilmUserManager::getInstanceByID($this->getUserID());
			if ( $oUser->getAutoCommitStatus() == mofilmUserBase::AUTO_COMMIT_STATUS_DISABLED ) {
				$this->_VideoSearch->addStatus(mofilmMovieBase::STATUS_ENCODING);
			}
			$this->_VideoSearch->addStatus(mofilmMovieBase::STATUS_APPROVED);
			$this->_VideoSearch->addStatus(mofilmMovieBase::STATUS_PENDING);
			$this->_VideoSearch->addAllowedOrderBy(mofilmMovieSearch::ORDERBY_DATE);
			$this->_VideoSearch->setOnlyActiveMovies(false);
		}
		return $this->_VideoSearch;
	}

	/**
	 * Returns the search result object, or null if no search has been run
	 * 
	 * @return mofilmMovieSearchResult
	 */
	function getSearchResult() {
		return $this->_SearchResult;
	}
	
	/**
	 * Runs the search with whatever parameters are in it
	 * 
	 * @return mofilmMovieSearchResult
	 */
	function doSearch() {
		$this->_SearchResult = $this->getVideoSearch()->search();
		return $this->_SearchResult;
	}	

	/**
	 * Returns $_UserID
	 *
	 * @return integer
	 */
	function getUserID() {
		return $this->_UserID;
	}
	
	/**
	 * Set $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return myVideoModel
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}
	
	
}