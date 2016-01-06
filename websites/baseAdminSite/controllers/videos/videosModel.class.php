<?php
/**
 * videosModel.class.php
 * 
 * videosModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category videosModel
 * @version $Rev: 326 $
 */


/**
 * videosModel class
 * 
 * Provides the "videos" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category videosModel
 */
class videosModel extends mvcModelBase {
	
	/**
	 * Stores $_CurrentUser
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_CurrentUser;
	
	/**
	 * Stores an instance of mofilmMovieSearch
	 * 
	 * @var mofilmMovieSearch
	 * @access protected
	 */
	protected $_VideoSearch;

	/**
	 * Stores an instance of mofilmUserSearch
	 *
	 * @var mofilmUserSearch
	 * @access protected
	 */
	protected $_UserSearch;
	
	/**
	 * Stores the last run search result
	 * 
	 * @var mofilmMovieSearchResult
	 * @access protected
	 */
	protected $_SearchResult;

	/**
	 * Stores $_SwitchUser
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_SwitchUserID;

	/**
	 * Stores the user object we are switching to
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_SwitchUser;
	
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
	 * Stores Best of clients awards
	 *
	 * @var mofilmMovie
	 * @access protected
	 */
	protected $_BocAward;
	
	/**
	 * Stores stats object
	 * 
	 * @var mofilmMovieStats
	 * @access protected
	 */
	protected $_Stats;
		
	
	protected $_VideoResults;
	
	
	protected $_SolrSearch;
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
		
		$this->_MovieID = null;
		$this->_Movie = null;
		$this->_Stats = null;
		$this->_VideoSearch = null;
		$this->_UserSearch = null;
		$this->_SwitchUserID = null;
		$this->_SwitchUser = null;
	}
	
	

	/**
	 * Returns $_CurrentUser
	 *
	 * @return mofilmUser
	 */
	function getCurrentUser() {
		return $this->_CurrentUser;
	}
	
	/**
	 * Set $_CurrentUser to $inCurrentUser
	 *
	 * @param mofilmUser $inCurrentUser
	 * @return usersModel
	 */
	function setCurrentUser($inCurrentUser) {
		if ( $inCurrentUser !== $this->_CurrentUser ) {
			$this->_CurrentUser = $inCurrentUser;
			$this->setModified();
		}
		return $this;
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
			$this->setModified();
		}
		return $this;
	}
        
        
       	/**
	 * Returns the value of $_SwitchUser
	 *
	 * @return integer
	 */
	function getSwitchUserID() {
		return $this->_SwitchUserID;
	}

	/**
	 * Returns the user object we are switching to
	 *
	 * @return mofilmUser
	 */
	function getSwitchUser() {
		if ( !$this->_SwitchUser instanceof mofilmUser ) {
			$this->_SwitchUser = mofilmUserManager::getInstanceByID($this->getSwitchUserID());
		}
		return $this->_SwitchUser;
	}

	/**
	 * Set $_SwitchUser to $inSwitchUser
	 *
	 * @param integer $inSwitchUser
	 * @return videosModel
	 */
	function setSwitchUserID($inSwitchUser) {
		if ( $inSwitchUser !== $this->_SwitchUserID ) {
			$this->_SwitchUserID = $inSwitchUser;
			$this->setModified();
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
				$this->_Movie = mofilmMovieManager::getInstanceByID($this->getMovieID());
			} elseif ( $this->getSearchResult() ) {
				$this->_Movie = $this->getSearchResult()->getFirstResult();
			}
		}
		return $this->_Movie;
	}
	
	/**
	 * Set $_Movie to $inMovie
	 *
	 * @param mofilmMovie $inMovie
	 * @return videosModel
	 */
	function setMovie($inMovie) {
		if ( $inMovie !== $this->_Movie ) {
			$this->_Movie = $inMovie;
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
		}
		return $this->_VideoSearch;
	}

	/**
	 * Creates and returns a user search object
	 *
	 * @return mofilmUserSearch
	 */
	function getUserSearch() {
		if ( !$this->_UserSearch instanceof mofilmUserSearch ) {
			$this->_UserSearch = new mofilmUserSearch();
			$this->_UserSearch->setUser($this->getCurrentUser());
			$this->_UserSearch->setOnlyActiveUsers(true);
			$this->_UserSearch->setEnabled(mofilmUser::ENABLED_Y);
		}
		return $this->_UserSearch;
	}

	/**
	 * Runs the user search returning the result set
	 *
	 * @return mofilmMovieSearchResult
	 */
	function doUserSearch() {
		$this->_SearchResult = $this->getUserSearch()->search();

		return $this->_SearchResult;
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
	function doSearch($corporateID = null,$brandID = null) {
            $this->_SearchResult = $this->getVideoSearch()->search($corporateID,$brandID);
            return $this->_SearchResult;
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
	 * Sets up the video search object for the review mode
	 * 
	 *  @return void
	 */
	function setSearchForReview() {
		$this->getVideoSearch()->setOnlyUnratedMovies(true);
		$this->getVideoSearch()->setLoadMovieData(false);
		$this->getVideoSearch()->setOrderBy(mofilmMovieSearch::ORDERBY_DATE);
		$this->getVideoSearch()->setOrderDirection(mofilmMovieSearch::ORDER_ASC);
		
		if ( $this->getCurrentUser()->getEventFilter()->getCount() > 0 ) {
			$this->getVideoSearch()->setEvents($this->getCurrentUser()->getEventFilter()->toArray());
		}
	}
	
	/**
	 * Rates the current movie, expects an array of data:
	 * 
	 * <code>
	 * $inData = array(
	 *     'Rating' => User Rating
	 * );
	 * </code>
	 * 
	 * @param array $inData
	 * @return boolean
	 * @throws mvcModelException
	 */
	function rateMovie(array $inData = array()) {
		if ( !$this->getMovie() instanceof mofilmMovie ) {
			throw new mvcModelException('Expected an instance of mofilmMovie, none found');
		}
		if ( !isset($inData['Rating']) || !is_numeric($inData['Rating']) || $inData['Rating'] == 0 ) {
			throw new mvcModelException('Missing a rating value, or is not numeric');
		}
		
		$oRating = mofilmMovieRating::getInstance($this->getMovie()->getID(), $this->getCurrentUser()->getID());
		$oRating->setMovieID($this->getMovie()->getID());
		$oRating->setRating($inData['Rating']);
		$oRating->setUserID($this->getCurrentUser()->getID());
		$oRating->save();
		
		mofilmMovieRating::updateMovieRating($this->getMovie()->getID());
		
		/*
		 * Reload movie data
		 */
		$this->getMovie()->load();
		
		return true;
	}
	
	/**
	 * Updates the movie status
	 * 
	 * @param array $inData
	 * @return boolean
	 * @throws mvcModelException
	 */
	function setStatus(array $inData = array()) {
		if ( !$this->getMovie() instanceof mofilmMovie ) {
			throw new mvcModelException('Expected an instance of mofilmMovie, none found');
		}
		if ( !isset($inData['Status']) || strlen($inData['Status']) < 3 ) {
			throw new mvcModelException('Missing a status value');
		}
		if ( !in_array($inData['Status'], mofilmMovieManager::getAvailableMovieStatuses()) ) {
			throw new mvcModelException('Invalid status, please use one of: '.implode(', ', mofilmMovieManager::getAvailableMovieStatuses()));
		}
		
		$this->getMovie()->setStatus($inData['Status']);
		if ( in_array($inData['Status'], array(mofilmMovieBase::STATUS_APPROVED, mofilmMovieBase::STATUS_REJECTED)) ) {
			if ( !$this->getMovie()->getModerated() || !$this->getMovie()->getModeratorID() ) {
				$this
					->getMovie()
						->setModerated(date(system::getConfig()->getDatabaseDatetimeFormat()))
						->setModeratorID($this->getCurrentUser()->getID());
			}
		}
		$this->getMovie()->save();
		
		/*
		 * Reload movie data
		 */
		$this->getMovie()->load();
		
		return true;
	}
	
	/**
	 * Sets the moderation comment
	 * 
	 * @param string $inComment
	 * @return boolean
	 * @throws mvcModelException
	 */
	function setModerationComment($inComment) {
		if ( !$this->getMovie() instanceof mofilmMovie ) {
			throw new mvcModelException('Expected an instance of mofilmMovie, none found');
		}
		if ( $this->getMovie()->getModeratorComments() ) {
			throw new mvcModelException('This movie has already been moderated');
		}
		if ( strlen($inComment) < 1 ) {
			throw new mvcModelException('Missing a comment value');
		}
		
		if ( !$this->getMovie()->getModeratorID() ) {
			$this->getMovie()->setModeratorID($this->getCurrentUser()->getID());
			$this->getMovie()->setModerated(date(system::getConfig()->getDatabaseDatetimeFormat()));
		}
		$this->getMovie()->setModeratorComments($inComment);
		$this->getMovie()->save();
		
		/*
		 * Reload movie data
		 */
		$this->getMovie()->load();
		
		return true;
	}
	
	/**
	 * Sets the moderation comment
	 * 
	 * @param string $inComment
	 * @return boolean
	 * @throws mvcModelException
	 */
	function addCommentToMovie($inComment) {
		if ( !$this->getMovie() instanceof mofilmMovie ) {
			throw new mvcModelException('Expected an instance of mofilmMovie, none found');
		}
		if ( strlen($inComment) < 1 ) {
			throw new mvcModelException('Missing a comment value');
		}
		
		$oComment = new mofilmMovieComment();
		$oComment->setComment($inComment);
		$oComment->setUserID($this->getCurrentUser()->getID());
		
		$this->getMovie()->getCommentSet()->setObject($oComment);
		$this->getMovie()->save();
		
		return true;
	}
	
	/**
	 * Handles adding the award to the movie
	 * 
	 * @param array $inData
	 * @return boolean
	 * @throws mvcModelException
	 */
	function addAwardToMovie(array $inData = array()) {
            
		if ( !$this->getMovie() instanceof mofilmMovie ) {
			throw new mvcModelException('Expected an instance of mofilmMovie, none found');
		}
		if ( !isset($inData['Award']) || strlen($inData['Award']) < 1 ) {
			throw new mvcModelException('Missing an award to assign, please select one or remove to remove all awards from this movie.');
		}
		if ( !in_array($inData['Award'], array('Winner', 'Shortlisted', 'Finalist', 'Runner Up', 'remove','ProFinal', 'ProShowcase')) ) {
			throw new mvcModelException('Invalid value for the award. It can only be one of the preset values.');
		                   
                }
                
		
		if ( $inData['Award'] == 'remove' ) {
			$oSet = $this->getMovie()->getAwardSet($this->getMovie()->getSource()->getEventID());
			$i = 0;
			foreach ( $oSet as $oAward ) {
				if ( $oAward->getEventID() == $this->getMovie()->getSource()->getEventID() && $oAward->getMovieID() == $this->getMovie()->getID() ) {
					$oAward->delete();
					++$i;
				}
			}
			systemLog::message("Removed $i awards from movie {$this->getMovie()->getID()}");
			return true;
		} else {
                    
			$oAward = new mofilmMovieAward();
			$oAward->setEventID($this->getMovie()->getSource()->getEventID());
			$oAward->setSourceID($this->getMovie()->getSource()->getID());
			$oAward->setType($inData['Award']);
			if ( $oAward->getType() == mofilmMovieAward::TYPE_FINALIST ) {
				$oAward->setPosition($inData['Position']);
			}
			
			$this->getMovie()->getAwardSet()->setObject($oAward);
			$this->getMovie()->save();
			return true;
		}
            
             
	}
        
         /**
	 * Handles to fetch the type of the event
	 * 
	 * @param array $data
	 * @return array with product type
	 * @throws mvcModelException
	 */
	
        
        function getEventType($data){
             $query = 
			"SELECT events.productID
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".movies
                              JOIN ".system::getConfig()->getDatabase('mofilm_content').".movieSources
                                  ON movies.ID = movieSources.movieID
                              JOIN ".system::getConfig()->getDatabase('mofilm_content').".sources
                                  ON movieSources.sourceID = sources.ID
                                 JOIN ".system::getConfig()->getDatabase('mofilm_content').".events
                                  ON sources.eventID = events.ID
                        WHERE  movieID = ".$data['MovieID'];
     
                $oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();

                if(count($res) > 0){
                    return $res; 
                    
                }else{
                   return false; 
                }
        }
        
        /**
	 * Handles adding the Best Of Clients award to the movie
	 * 
	 * @param array $inData
	 * @return boolean
	 * @throws mvcModelException
	 */
	
        function addBestClientAwardToMovie($inData){
            
                if($inData['bocAward'] == 'BestOfClients'){
                    $inData['EventID']  = $this->getMovie()->getSource()->getEventID();
                    $inData['SourceID'] = $this->getMovie()->getSource()->getID();
                    $inData['MovieID']  = $this->getMovie()->getID();
                    $inData['UserID']   = $this->getMovie()->getUserID();


                    if($this->getBestAwardCountForClient($inData) >= 10){
                        throw new mvcModelException('Best of Client Award could not be added because client quota exceeded. Please remove award from another client video in order to proceed.');
                    }else{
                       if($this->saveBestOfClient($inData)){
                           systemLog::message('Best of Client award added for this video.');
                       }else{
                           throw new mvcModelException('Problem in saving the record of Best Of Client.');
                       }
                    }  
                }	else{
                     throw new mvcModelException('Best Of Client data for this video was not found.');
                }

        }
        
        
         /**
	 * Handles saving the Best Of Clients award to the db
	 * 
	 * @param array $data
	 * @return boolean
	 * @throws mvcModelException
	 */
        function saveBestOfClient($data){
            
            if($this->checkBestOfClientExist($data)){
                $query = "INSERT INTO ".system::getConfig()->getDatabase('mofilm_content').".movieAwards 
                         (userID, movieID, eventID, sourceID, type, name, year) VALUES (".$data['UserID'].",
                         ".$data['MovieID'].",".$data['EventID'].",".$data['SourceID'].",'BestOfClients','','') ";

                $oStmt = dbManager::getInstance()->prepare($query);

                if ($oStmt->execute()) {
                    return true;
                }else{
                    throw new mvcModelException('Problem in saving the record of Best Of Clients');  
                }
                return true;
            }else{
               throw new mvcModelException('Best Of Client Award is already present for this video.');  
            }
         
        }
        
         /**
	 * Handles deleting the Best Of Clients award from the db
	 * 
	 * @param array $data
	 * @return boolean
	 * @throws mvcModelException
	 */
        
        function removeBestOfClient($data){
 
                $query = "DELETE FROM ".system::getConfig()->getDatabase('mofilm_content').".movieAwards 
                         WHERE type='BestOfClients' AND movieID = ".$data['MovieID'];
                $oStmt = dbManager::getInstance()->prepare($query);

                try {
                    $oStmt->execute();
                }catch ( Exception $e ) {
                     throw $e;
                }

    
        }
         /**
	 * Handles checking the Best Of Clients award to the movie exists or not in the db 
	 * 
	 * @param array $data
	 * @return boolean
	 * @throws mvcModelException
	 */
        function checkBestOfClientExist($data){
             $query = 
			"SELECT count(*) as total
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".movieAwards
                        WHERE movieAwards.type =  'BestOfClients' AND movieID = ".$data['MovieID'];
     
                $oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();

              
                if($res['total'] == 0){
                    return true;        
                }else{
                   return false; 
                }
               
                
        }
         /**
	 * Handles fetching the total count for Best Of Client for partucular events 
	 * 
	 * @param array $data
	 * @return Cout as integer
	 * @throws mvcModelException
	 */
        
        function getBestAwardCountForClient($data){
                $getCorporate = 
			"SELECT brands.corporateID as CorporateID
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".movies
                          JOIN   ".system::getConfig()->getDatabase('mofilm_content').".movieSources
                          ON movieSources.movieID = movies.ID
                          JOIN   ".system::getConfig()->getDatabase('mofilm_content').".sources
                          ON sources.ID = movieSources.sourceID
                          JOIN ".system::getConfig()->getDatabase('mofilm_content').".brands
                              ON brands.ID = sources.brandID
                                  
                        WHERE movies.ID =".$data["MovieID"]." LIMIT 1";

      
                $oRes = dbManager::getInstance()->query($getCorporate);
		$res = $oRes->fetch();
              
                $query = 
			"SELECT count(*) as total
			    FROM ".system::getConfig()->getDatabase('mofilm_content').".movieAwards
                            JOIN   ".system::getConfig()->getDatabase('mofilm_content').".sources
                            ON sources.ID = movieAwards.sourceID
                            JOIN ".system::getConfig()->getDatabase('mofilm_content').".brands
                            ON brands.ID = sources.brandID             
                            WHERE movieAwards.type  = 'BestOfClients' 
                            AND brands.corporateID = ".$res['CorporateID'];
                $oCount = dbManager::getInstance()->query($query);
          	$bocCount = $oCount->fetch();
              
      
                if($bocCount['total'] !=  '0'){
                    return  $bocCount['total'];
                }else{
                    return 0;
                }
  
        }
        
        function getUpdateIndtag($brandId,$inId)
        {
            if(!empty($brandId)){
                $getBrandupdQuery="update ".system::getConfig()->getDatabase('mofilm_content').".brands set brands.industryId='$inId' Where brands.ID='$brandId'";
                dbManager::getInstance()->query($getBrandupdQuery);
               }
        }
        
        function getBrand($movieID){
               $getBrandQuery = 
                            "SELECT brands.ID as BrandID, brands.name as BrandName,brands.industryId as industryid
                             FROM   ".system::getConfig()->getDatabase('mofilm_content').".movieSources
                             JOIN   ".system::getConfig()->getDatabase('mofilm_content').".sources
                             ON sources.ID = movieSources.sourceID
                             JOIN ".system::getConfig()->getDatabase('mofilm_content').".brands
                             ON brands.ID = sources.brandID WHERE movieSources.movieID =".$movieID;
                $resBrand = dbManager::getInstance()->query($getBrandQuery);
          	$brandDetails = $resBrand->fetch();
                return $brandDetails;
        }
        
        function getSourceByBrandID($brandID, $eventID){
                $getSourceQuery = 
                              "SELECT sources.ID as SourceID FROM ".system::getConfig()->getDatabase('mofilm_content').".sources
                              WHERE sources.brandID =".$brandID." AND sources.eventID = ".$eventID;
                $resSource = dbManager::getInstance()->query($getSourceQuery);
          	$sourceDetails = $resSource->fetch();
                return $sourceDetails;
        }
        
        
        function getMovieAwards($data){
                
            if($this ->getEventType($data) == 5){
                    $query = 
			"SELECT type
			 FROM ".system::getConfig()->getDatabase('mofilm_content').".movieAwards
                         WHERE movieID = ".$data['MovieID'];
                $oRes = dbManager::getInstance()->query($query);
                $movieHighestAwardType = '';
                
                foreach($oRes as $key => $value){
                    if($value['type'] == 'Pro Final' && $movieHighestAwardType == 'Pro Showcase' ){
                        continue;
                    }else{
                        $movieHighestAwardType = $value['type'];
                    }
                }
            }else{
                $array1  = array('Winner','Finalist','Runner Up');
                $array2  = array('Winner','Finalist');
                $array3  = array('Winner');
                $query = 
			"SELECT type
			 FROM ".system::getConfig()->getDatabase('mofilm_content').".movieAwards
                         WHERE movieID = ".$data['MovieID'];
                $oRes = dbManager::getInstance()->query($query);
                $movieHighestAwardType = '';
                
                foreach($oRes as $key => $value){
                    if($value['type'] == 'Shortlisted' && in_array($movieHighestAwardType, $array1) ){
                        continue;
                    }
                    if($value['type'] == 'Runner Up' && in_array($movieHighestAwardType, $array2) ){
                        continue;
                    }
                     if($value['type'] == 'Finalist' && in_array($movieHighestAwardType, $array3) ){
                        continue;
                    }
                    if($value['type'] != 'BestOfClients'){
                        $movieHighestAwardType = $value['type'];
                    }
                    
                }
            }
                return $movieHighestAwardType;
            
        }
	/**
	 * Loads the movie stats using the current user
	 * 
	 * @return mofilmMovieStats
	 */
	function getMovieStats() {
		if ( !$this->_Stats instanceof mofilmMovieStats ) {
			$this->_Stats = new mofilmMovieStats($this->getCurrentUser());
			$this->_Stats->load();
		}
		return $this->_Stats;
	}

	/**
	 * Moves the movie from the original user to the new user
	 *
	 * @return void
	 * @throws mvcModelException
	 */
	function switchUser() {
		if ( !$this->getSwitchUser() instanceof mofilmUser || $this->getSwitchUser()->getID() < 1 ) {
			throw new mvcModelException('Missing a user to switch the movie to');
		}

		$origUser = $this->getMovie()->getUserID();

		/* @var mofilmMovieAward $oAward */
		foreach ( $this->getMovie()->getAwardSet() as $oAward ) {
			$oAward->setUserID($this->getSwitchUser()->getID());
		}

		$this->getMovie()->getLicenseSet()->delete();
		$this->getMovie()->setUserID($this->getSwitchUser()->getID());
		$this->getMovie()->save();

		$oLog = new mofilmUserLog();
		$oLog->setUserID($this->getCurrentUser()->getID());
		$oLog->setDescription(sprintf('Switched user from %d to %d', $origUser, $this->getSwitchUserID()));
		$oLog->setType(mofilmUserLog::TYPE_OTHER);
		$oLog->save();
	}
	
	/**
	 * Returs the user full name based on the email address
	 * 
	 * @param string $inEmail
	 * @return string 
	 */
	function getUserName($inEmail) {
		return mofilmUserManager::getInstanceByUsername($inEmail)->getFullname();
	}
	
	/**
	 * Checks if the user is a valid or not
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
	
	/*
	 * Changing the Automated tag at the time of Event and Source changing
	 */
	function switchMovieTag() {
		$inNewEventID = $this->getMovie()->getSource()->getEventID();
		$inNewSourceID = $this->getMovie()->getSource()->getID();
		$inMovieID = $this->getMovieID();
		$inMovieYear = date('Y', strtotime($this->getMovie()->getUploadDate()));

		$deleteAutomaticTags = mofilmTag::getTagsByMovieID($inMovieID, mofilmTag::TYPE_CATEGORY);

		$oMovieTag = new mofilmMovieTagSet();
		$oMovieTag->setMovieID($inMovieID);

		foreach ( $deleteAutomaticTags as $deleteAutomaticTag ) {
			$oMovieTag->setTagID($deleteAutomaticTag->getID());
			$oMovieTag->deleteByTagAndMovieID();
		}

		$inEventTagID = mofilmTag::getInstanceByTagAndType(mofilmEvent::getInstance($inNewEventID)->getName(), mofilmTag::TYPE_CATEGORY)->getID();
		$oMovieTag->setTagID($inEventTagID);
		$oMovieTag->save();
		
		$inSourceTagID = mofilmTag::getInstanceByTagAndType(mofilmSource::getInstance($inNewSourceID)->getName(), mofilmTag::TYPE_CATEGORY)->getID();
		if ( $inEventTagID !== $inSourceTagID ) {
			$oMovieTag->setTagID($inSourceTagID);
			$oMovieTag->save();
		}
		
		$inYearTagID = mofilmTag::getInstanceByTagAndType($inMovieYear, mofilmTag::TYPE_CATEGORY)->getID();
		$oMovieTag->setTagID($inYearTagID);
		$oMovieTag->save();
		return true;
	}
	
	function BCMovieAssets() {	    
		$oUploadStatus = mofilmUploadStatus::getInstanceByMovieID($this->getMovieID());
		
		if ( $oUploadStatus ) {
			$readAPI = 'Ekg-LmhL4QrFPEdtjwJlyX2Zi4l6mgdiPnWGP0bKIyKKT_94PTKHrw..';
			$bc = new BCMAPI($readAPI);
			try {
				$oVideoRenditions = $bc->find('find_video_by_id', array('video_id' => $oUploadStatus->getVideoCloudID(), 'video_fields' => 'videoStillURL,thumbnailURL,renditions', 'media_delivery' => 'http'));

				$oVideoAssets = $this->getMovie()->getAssetSet()->getObjectByAssetType(mofilmMovieAsset::TYPE_THUMBNAIL)->getIterator();
				foreach ( $oVideoAssets as $oVideoAsset ) {
					$assetParams = $oVideoAsset->toArray();

					if ( $assetParams['_Description'] == 'ThumbNail_640x340' && $assetParams['_CdnURL'] !== strstr($oVideoRenditions->videoStillURL, '?', true) ) {
						systemLog::message('VideoStill Modified -- '.$assetParams['_ID']);
						$assets = new mofilmMovieAsset($assetParams['_ID']);
						$assets->setCdnURL(strstr($oVideoRenditions->videoStillURL, '?', true));
						$assets->save();
					}

					if ( $assetParams['_Description'] == 'ThumbNail_150x84' && $assetParams['_CdnURL'] !== strstr($oVideoRenditions->thumbnailURL, '?', true) ) {
						systemLog::message('Thumbnail Modified -- '.$assetParams['_ID']);
						$assets = new mofilmMovieAsset($assetParams['_ID']);
						$assets->setCdnURL(strstr($oVideoRenditions->thumbnailURL, '?', true));
						$assets->save();
					}
				}

				return $oVideoRenditions;
			} catch (Exception $error) {
				return false;
			}
		}
	}
	
	/**
	 * Sends the mail when a video is Approved
	 * 
	 * @return boolean
	 */	
	function sendApprovedEmail() {
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
				0, mofilmMessages::MSG_GRP_ADMIN_MOVIE_APPROVED, 'en'
		);
		
		$oUser = mofilmUserManager::getInstanceByID($this->getMovie()->getUserID());

		commsOutboundManager::setCustomerInMessageStack($oQueue, $oUser->getID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $oUser->getUsername());
		return $oQueue->send();	    
	}
		

	/**
	 * Set $_VideoResults to $inResult
	 *
	 * @param array $inResult
	 * @return 
	 */
	function setSolrResults($inResult) {
		if ( $inResult !== $this->_VideoResults ) {
			$this->_VideoResults = $inResult;
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
		return $this->_VideoResults;
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
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
	}
	
	
	/**
	 * Gets the movie object using ID
	 * 
	 * @param type $inMovieID
	 * @return type mofilmMovie
	 */
	function getMovieByID($inMovieID) {
		$this->_Movie = mofilmMovieManager::getInstanceByID($inMovieID);
		return $this->_Movie;
	}

	/**
	 * get the video search class object
	 * 
	 * @return type mofilmVideoSearch
	 */
	function getSolrVideoSearch() {
		if ( !$this->_SolrSearch instanceof mofilmVideoSearch ) {
			$this->_SolrSearch = new mofilmVideoSearch();
		}
		return $this->_SolrSearch;
	}
	
	/**
	 * Performs the video search using solr
	 * 
	 * @return type array
	 */
	function doSolrVideoSearch() {
		$this->getSolrVideoSearch()->search();
		$this->_VideoResults = $this->getSolrVideoSearch()->getResultList();		
		return $this->_VideoResults;
	}
	
	function linkUserMovieGrants($inSourceID = null, $inUserID = null, $inMovieID = null) {
		if ( $inSourceID && $inUserID && $inMovieID ) {
			$oUserMovieGrans = mofilmUserMovieGrants::userMovieGrantsObject($inUserID, $inSourceID, mofilmUserMovieGrants::STATUS_APPROVED);
			systemLog::message($oUserMovieGrans);
			if ( $oUserMovieGrans instanceof mofilmUserMovieGrants ) {
				$oUserMovieGrans->setMovieID($inMovieID);
				$oUserMovieGrans->save();
			}
		}
		return true;
	}
	
}