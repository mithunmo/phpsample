<?php
/**
 * myVideoModel.class.php
 * 
 * myVideoModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category myVideoModel
 * @version $Rev: 623 $
 */


/**
 * myVideoModel class
 * 
 * Provides the "myVideo" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category myVideoModel
 */
class myVideoModel extends mvcModelBase {
	
	
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
	 * Stores the userID
	 * 
	 * @var integer
	 * @access protected 
	 */
	protected $_UserID;
	
	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	/**
	 * Stores $_Tag
	 * 
	 * @var string
	 * @access protected
	 */
	protected $_Tag;
	
	/**
	 * Stores $_Request
	 *
	 * @var mvcRequest
	 * @access protected
	 */
	protected $_Request;
			
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
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
	 * Creates an returns a mofilmMovieSearch object
	 *
	 * @return mofilmMovieSearchResult 
	 */
	function doEncodeSearch() {
		$oVideoSearch = new mofilmMovieSearch();
		$oVideoSearch->setLoadMovieData(true);
		$oVideoSearch->setUserID($this->getUserID());
		$oVideoSearch->setUser(mofilmUserManager::getInstanceByID($this->getUserID()));
		$oVideoSearch->setEnforceStatusRestrictions(false);
		$oUser = mofilmUserManager::getInstanceByID($this->getUserID());
		if ( $oUser->getAutoCommitStatus() == mofilmUserBase::AUTO_COMMIT_STATUS_DISABLED ) {
			$oVideoSearch->addStatus(mofilmMovieBase::STATUS_ENCODING);
		}
		$oVideoSearch->setOnlyActiveMovies(false);
		return $oVideoSearch->search();
	}
	
	
	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		$total = $this->getSearchResult()->getTotalResults();
		
		if ( $inLimit > 0 ) {
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
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
	 * Returns the Fullname from emailadress
	 * 
	 * 
	 * @param string $inEmail
	 * @return string 
	 */
	function getUserName($inEmail) {
		return mofilmUserManager::getInstanceByUsername($inEmail)->getFullname();
	}
	
	/**
	 * Checks if its a valid user or not based on the email address
	 * 
	 * @param string $inEmail
	 * @return boolean 
	 */
	function getValidUser($inEmail) {
		if ( mofilmUserManager::getInstanceByUsername($inEmail) ) {
			return true;
		} else {
			return false;
		}	
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
	 * @return accountModel
	 */
	function setRequest($inRequest) {
		if ( $inRequest !== $this->_Request ) {
			$this->_Request = $inRequest;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Saves the movie and its assoiated records
	 * 
	 * @param array $inData
	 * @param integer $inUserID 
	 */
	function saveMovie($inData, $inUserID) {
		$oMovie = mofilmMovieManager::getInstanceByID($inData["MovieID"]);
		
		$inData['Tags'][] = mofilmTag::getInstanceByTagAndType($oMovie->getSource()->getEvent()->getName(), mofilmTag::TYPE_CATEGORY)->getID();
		$sourceTagID = mofilmTag::getInstanceByTagAndType($oMovie->getSource()->getName(), mofilmTag::TYPE_CATEGORY)->getID();
					
		if (!(in_array($sourceTagID, $inData['Tags']))) {
			$inData['Tags'][] = $sourceTagID;
		}
					
		$inData['Tags'][] = mofilmTag::getInstanceByTagAndType(date('Y', strtotime($oMovie->getUploadDate())), mofilmTag::TYPE_CATEGORY)->getID();
			
		if ( is_array($inData['Tags']) ) {
			$oMovie->getTagSet()->reset();
			
			foreach ( $inData['Tags'] as $tagID ) {
				$oMovie->getTagSet()->setObject(mofilmTag::getInstance($tagID));
			}
		}
		
//		if ( $inData['mofilmMovieTags'] ) {
//			$this->saveMovieTags($inData['MovieID'], $inData['mofilmMovieTags']);
//		}
		
		$oMovie->setContributorInputData($inData, $oMovie, $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue());
		$oMovie->save();
	}
	
	/**
	 * Saves user entered movie tags and auto generated categories
	 * 
	 * @return boolean
	 */
	function saveMovieTags($inMovieID = NULL, $inMovieTags = NULL) {
		$subject = $inMovieTags;
		$search = ", ";
		$replace = ",";
		$oMovieTagIDs = null;
		
		$string = str_replace($search, $replace, $subject);
		
		$tags = explode(",", $string);
		$unique_tags = $this->array_iunique($tags);

		foreach ( $unique_tags as $tag ) {
			if ( trim($tag) ) {
				$oTagID = mofilmTag::getInstanceByTag(trim($tag))->getID();
				if ( $oTagID == 0 ) {
					$oTag = new mofilmTag();
					$oTag->setName(trim($tag));
					$oTag->setType(mofilmTag::TYPE_TAG);
					$oTag->save();
					
					$oTagID = mofilmTag::getInstanceByTag(trim($tag))->getID();
					$oMovieTagIDs[] = mofilmTag::getInstance($oTagID);
				} else {
					$return = mofilmMovieTagSet::checkForMovieIDAndTagID($inMovieID, $oTagID);
					if ( $return != 1 ) {
						$oTagID = mofilmTag::getInstanceByTag(trim($tag))->getID();
						$oMovieTagIDs[] = mofilmTag::getInstance($oTagID);
					}
				}
			}
		}

		if ( is_array($oMovieTagIDs) ) {
			$oMovieTag = new mofilmMovieTagSet($inMovieID);
			$oMovieTag->setObjects($oMovieTagIDs);
			$oMovieTag->save();
		}
		return true;
	}
	
	function array_iunique($array) {
		return array_intersect_key($array,array_unique(array_map(strtolower,$array)));
	}
}