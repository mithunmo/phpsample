<?php
/**
 * mofilmMovieSearch
 *
 * Stored in search.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMovieSearch
 * @category mofilmMovieSearch
 * @version $Rev: 371 $
 */


/**
 * mofilmMovieSearch Class
 *
 * The main movie search system. This class handles searching for movies by
 * a number of criteria including: keywords, user details, movie details,
 * status, userID, event / source etc.
 * 
 * The class depends on a valid mofilmUser object - even if it is empty, it
 * is still required. It needs this for various permission checks that have
 * to be performed on the data before it is used. Perhaps it should be inferred
 * that this has already been vetted, however this ensures our permissions
 * are correct.
 * 
 * Filtering by status has an additional setting to override the user based
 * permission lookups. This is to allow the front-end none-admin sites to be
 * able to search on any status without permission lookups.
 *
 * @package mofilm
 * @subpackage mofilmMovieSearch
 * @category mofilmMovieSearch
 */
class mofilmMovieSearch extends baseSearch {

	const ORDERBY_RATING = 'movies.avgRating';
	const ORDERBY_DATE = 'movies.uploaded';
        const ORDERBY_FMNAME = 'movies.fmname';
        const ORDERBY_TITLE = 'movies.title';
        const ORDERBY_AWARD = 'movies.award';
        

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_UserEmailAddress
	 *
	 * @var string
	 * @access protected
	 */
	protected $_UserEmailAddress;

	/**
	 * Stores $_Status
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Status;

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;

	/**
	 * Stores $_Events
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Events;

	/**
	 * Stores $_Sources
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Sources;
	
	/**
	 * Stores $_MovieData
	 *
	 * @var array
	 * @access protected
	 */
	protected $_MovieData;
	
	/**
	 * Stores $_UserData
	 *
	 * @var array
	 * @access protected
	 */
	protected $_UserData;
	
	/**
	 * An array of IDs to exclude from search results
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_ExcludedMovies;

	/**
	 * Stores $_AwardType
	 *
	 * @var string
	 * @access protected
	 */
	protected $_AwardType;
	
	/**
	 * Stores $_OnlyFinalists
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_OnlyFinalists;
	
	/**
	 * Stores $_OnlyUnratedMovies
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_OnlyUnratedMovies;
	
	/**
	 * Stores $_OnlyFavourites
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_OnlyFavourites;
	
	/**
	 * Stores $_OnlyTitles
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_OnlyTitles;

	/**
	 * Stores $_OnlyTags
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_OnlyTags;

	/**
	 * Stores $_OnlyActiveMovies
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_OnlyActiveMovies;
	
	/**
	 * Stores $_LoadMovieData
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_LoadMovieData;
	
	/**
	 * Stores $_EnforceStatusRestrictions
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_EnforceStatusRestrictions;
	
	/**
	 * Stores $_User
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_User;



	/**
	 * @see baseSearch::reset()
	 */
	function reset() {
		parent::reset();
		$this->_UserID = null;
		$this->_UserEmailAddress = null;
		$this->_MovieID = null;
		$this->_AwardType = null;
		$this->_OnlyFinalists = false;
		$this->_OnlyUnratedMovies = false;
		$this->_OnlyFavourites = false;
		$this->_OnlyTitles = false;
		$this->_OnlyActiveMovies = false;
		$this->_LoadMovieData = false;
		$this->_EnforceStatusRestrictions = true;
		$this->_User = null;
		
		$this->_Status = array();
		$this->_Events = array();
		$this->_Sources = array();
		$this->_MovieData = array();
		$this->_UserData = array();
		$this->_ExcludedMovies = array();
		
		$this->_OrderBy = self::ORDERBY_DATE;
		$this->_AllowedOrderBy = array(self::ORDERBY_TITLE, self::ORDERBY_FMNAME, self::ORDERBY_AWARD, self::ORDERBY_DATE, self::ORDERBY_RATING);
	}

	/**
	 * @see baseSearch::initialise()
	 */
	function initialise() {
		parent::initialise();

		$this->addAllowedOrderBy('');
	}

	/**
	 * Runs the search using the supplied data
	 *
	 * @return mofilmMovieSearchResult
	 */
	function search() {
		if ( $this->canSearchRun() ) {
			if ( $this->getMovieID() ) {
				return new mofilmMovieSearchResult(array(mofilmMovieManager::getInstanceByID($this->getMovieID())), 1, $this);
			}

			$query = '';
			$this->buildSelect($query);
			$this->buildWhere($query);
			
			if ( $this->getKeywords() && $this->getSearchTextType() == self::SEARCH_TEXT_MATCH || $this->getSearchTextType() == self::SEARCH_TEXT_MATCH_BOOLEAN ) {
				/*
				 * DR 2010-09-15: "can we also search on users name?" well means doing a UNION ALL
				 * but using the exact same query as the one against movies. So we cheat and simply
				 * str_replace the bits that need changing. This produces 2 result sets inside the
				 * result set, that then has to be ordered to get the results into relevancy order.
				 * 
				 * Yes this can be expensive but it is still easier than either trying to do 2 FT
				 * matches in one normal SELECT or running 2 completely separate queries - which
				 * would just be a mess.
				 */
				$union = str_replace(
					array(
						'SQL_CALC_FOUND_ROWS',
						'FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies',
						'movies.shortDesc',
						'INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieTags',
						'ON (movieTags.movieID = movies.ID)',
						'INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.tags',
						'ON (movieTags.tagID = tags.ID)',
						'|| MATCH (tags.name) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).')',
					),
					array(
						'',
						'FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.users ON (movies.userID = users.ID)',
						'users.firstname, users.surname',
						'',
						'',
						'',
						'',
						'',
					),
					$query
				);

				/*
				 * DR 2011-02-25: This fixes bugs in the UNION ALL causing duplicate results
				 * even though we are GROUP BY movies.ID. So instead we create a derived table
				 * (movies) and GROUP BY movies.ID on that. Just how messy can this search get.
				 * Um, very by the looks of things :S
				 */
				$query = '
					SELECT SQL_CALC_FOUND_ROWS movies.ID, movies.uploaded, movies.avgRating, Score
					  FROM ('.str_replace('SQL_CALC_FOUND_ROWS', '', $query).' UNION ALL '.$union.') AS movies';
			}

			/*
			 * Always try to exclude duplicates
			 */
			$query .= ' GROUP BY movies.ID ';
			
			$this->buildOrderBy($query);
			$this->buildLimit($query);

			$count = 0;
			$list = array();

			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				$tmp = array();
				foreach ( $oStmt as $row ) {
					$tmp[] = $row['ID'];
				}

				$count = dbManager::getInstance()->query('SELECT FOUND_ROWS() AS Results')->fetchColumn();
				if ( count($tmp) > 0 ) {
					$oManager = mofilmMovieManager::getInstance();
					$oManager->setLoadMovieDetails($this->getLoadMovieData());
					$oManager->setLoadOnlyActive($this->getOnlyActiveMovies());
					$list = $oManager->loadMoviesByArray($tmp);
				}
			}
			$oStmt->closeCursor();

			return new mofilmMovieSearchResult($list, $count, $this);
		}
		/*
		 * Always return empty result set
		 */
		return new mofilmMovieSearchResult(array(), 0, $this);
	}
	
	/**
	 * @see baseSearchInterface::canSearchRun()
	 */
	function canSearchRun() {
		$return = true;
		if ( !$this->getUser() instanceof mofilmUser ) {
			systemLog::warning('Missing mofilmUser object for search');
			$return = false;
		}
		if (
			!$this->getKeywords() && !$this->getMovieID() && !$this->getStatusCount() && !$this->getUserID() &&
			(
				!$this->getEventCount() && !$this->getSourceCount() &&
				(!$this->getUser()->getPermissions()->isRoot() && $this->getUser()->getClientID() != mofilmClient::MOFILM)
			)
			&&
			!$this->getUserEmailAddress() && !$this->getOnlyFinalists() &&
			!$this->getMovieDataCount() && !$this->getUserDataCount()
		) {
			$return = false;
		}

		/*
		 * DR 2011-02-17: check for none-MOFILM users so that open-ended searches can
		 * still run, auto-sets the permitted sources from the current user.
		 */
		if ( false === $return && $this->getUser()->getClientID() != mofilmClient::MOFILM ) {
			if ( $this->getUser()->getSourceSet()->getCount() ) {
				$this->setSources($this->getUser()->getSourceSet()->getObjectIDs());
				$return = true;
			}
		}
		return $return;
	}

	/**
	 * @see baseSearchInterface::buildSelect()
	 */
	function buildSelect(&$inQuery) {
		$inQuery = 'SELECT SQL_CALC_FOUND_ROWS movies.ID, movies.uploaded, movies.avgRating ';
		if ( $this->getSearchTextType() && strlen($this->getKeywords()) > 2 ) {
			switch ( $this->getSearchTextType() ) {
				case self::SEARCH_TEXT_MATCH:
				case self::SEARCH_TEXT_MATCH_BOOLEAN:
					$inQuery .= ', MATCH (movies.shortDesc) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).') AS Score';
				break;
			}
		}
		$inQuery .= ' FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies ';
		
		if ( $this->getKeywords() && !$this->getOnlyTitles() ) {
			$inQuery .= '
				INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieTags
				   ON (movieTags.movieID = movies.ID)
				   INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.tags
				   ON (movieTags.tagID = tags.ID)';
		}
		
		if ( $this->getEventCount() > 0 || $this->getSourceCount() > 0 ) {
			$inQuery .= '
				INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources
				   ON (movieSources.movieID = movies.ID AND movieSources.sourceID)';
		}

		if ( $this->getUserEmailAddress() && strlen($this->getUserEmailAddress()) > 2 ) {
			$inQuery .= '
				INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.users
				   ON (movies.userID = users.ID)';
		}
		
		if ( $this->getOnlyFinalists() || $this->getAwardType() ) {
			$inQuery .= '
				INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
				   ON (movies.ID = movieAwards.movieID)';
		}
		
		if ( $this->getOnlyUnratedMovies() ) {
			if ( $this->getUser()->getID() > 0 ) {
				$inQuery .= '
					LEFT JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieRatings
					   ON (movies.ID = movieRatings.movieID AND movieRatings.userID = '.$this->getUser()->getID().')';
			}
		}
		
		if ( $this->getOnlyFavourites() ) {
			$inQuery .= '
				INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.userFavourites
				   ON (movies.ID = userFavourites.movieID)';
		}
		
		if ( $this->getMovieDataCount() > 0 ) {
			$inQuery .= '
				INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieData
				   ON (movies.ID = movieData.movieID)';
		}
		
		if ( $this->getUserDataCount() > 0 ) {
			$inQuery .= '
				INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.userData
				   ON (movies.userID = userData.userID)
			';
		}
	}

	/**
	 * @see baseSearchInterface::buildWhere()
	 */
	function buildWhere(&$inQuery) {
		$where = array();
		
		$where[] = 'movies.private != 2';
		
		if ( $this->getUserID() > 0 ) {
			$where[] = 'movies.userID = '.$this->getUserID();
		}
		if ( $this->getUserEmailAddress() && strlen($this->getUserEmailAddress()) > 2 ) {
			$where[] = 'users.email LIKE '.dbManager::getInstance()->quote('%'.$this->getUserEmailAddress().'%');
		}

		if ( $this->getKeywords() ) {
			/*
			 * DB is configured with min word length of 4 chars, find max length, reset to LIKE if less than 4
			 */
			$keywords = explode(' ', $this->getKeywords());
			$maxLength = 0;
			foreach ( $keywords as $keyword ) {
				$strLen = strlen(trim($keyword));
				if ( $strLen > $maxLength ) {
					$maxLength = $strLen;
				}
			}
			if ( $maxLength < 4 ) {
				$this->setSearchTextType(self::SEARCH_TEXT_LIKE);
			}

			switch ( $this->getSearchTextType() ) {
				case self::SEARCH_TEXT_EXACT:
					$search_keyword = ' ( movies.shortDesc = '.dbManager::getInstance()->quote($this->getKeywords());
					if ( !( $this->getOnlyTitles() ) ) {
					    $search_keyword .= ' || tags.name = '.dbManager::getInstance()->quote($this->getKeywords());
					}
					$search_keyword .= ' ) ';
				break;

				case self::SEARCH_TEXT_LIKE:
					$search_keyword = ' ( movies.shortDesc LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ','%', $this->getKeywords()).'%');
				    	if ( !( $this->getOnlyTitles() ) ) {
					    $search_keyword .= ' || tags.name LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ','%', $this->getKeywords()).'%');
					}
					$search_keyword .= ' ) ';
				break;

				case self::SEARCH_TEXT_MATCH:
					$search_keyword = ' ( MATCH (movies.shortDesc) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).')';
					if ( !( $this->getOnlyTitles() ) ) {
					    $search_keyword .= ' || MATCH (tags.name) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).')';
					}
					$search_keyword .= ' ) ';
				break;

				case self::SEARCH_TEXT_MATCH_BOOLEAN:
					$search_keyword = ' ( MATCH (movies.shortDesc) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).' IN BOOLEAN MODE)';
				    	if ( !( $this->getOnlyTitles() ) ) {
					    $search_keyword .= ' || MATCH (tags.name) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).' IN BOOLEAN MODE)';
					}
					$search_keyword .= ' ) ';
				break;
			}
			
			$where[] = $search_keyword;
		}

		/*
		 * Everyone can see approved movies
		 */
		$status = array(mofilmMovieBase::STATUS_APPROVED);
		
		if ( $this->getUser() instanceof mofilmUser && $this->getEnforceStatusRestrictions() ) {
			$oPerms = $this->getUser()->getPermissions();
			$namespace = $oPerms->getNamespace() ? $oPerms->getNamespace().'.' : '';
			
			if ( $this->getStatusCount() > 0 ) {
				/*
				 * Resets status array so we can search for specific ranges of status as we need
				 */
				$status = array();
				foreach ( $this->getStatus() as $searchStatus ) {
					if ( $oPerms->isAuthorised($namespace.sprintf("see%sVideos", preg_replace('/[^a-zA-Z]/', '', $searchStatus))) ) {
						$status[] = $searchStatus;
					} elseif ( $searchStatus == mofilmMovieBase::STATUS_APPROVED ) {
						$status[] = $searchStatus;
					}
				}
			} else {
				if ( $this->getOnlyUnratedMovies() ) {
					if ( $oPerms->isAuthorised($namespace.'seePendingVideos') ) {
						$status[] = mofilmMovieBase::STATUS_PENDING;
					}
				} else {
					if ( $oPerms->isAuthorised($namespace.'seeEncodingVideos') ) {
						$status[] = mofilmMovieBase::STATUS_ENCODING;
					}
					if ( $oPerms->isAuthorised($namespace.'seePendingVideos') ) {
						$status[] = mofilmMovieBase::STATUS_PENDING;
					}
					if ( $oPerms->isAuthorised($namespace.'seeRemovedVideos') ) {
						$status[] = mofilmMovieBase::STATUS_REMOVED;
					}
					if ( $oPerms->isAuthorised($namespace.'seeRejectedVideos') ) {
						$status[] = mofilmMovieBase::STATUS_REJECTED;
					}
					if ( $oPerms->isAuthorised($namespace.'seeDisputedVideos') ) {
						$status[] = mofilmMovieBase::STATUS_DISPUTED;
					}
					if ( $oPerms->isAuthorised($namespace.'seeFailedVideos') ) {
						$status[] = mofilmMovieBase::STATUS_FAILED_ENCODING;
					}
				}
			}
		} else {
			if ( $this->getStatusCount() > 0 ) {
				$status = array();
				foreach ( $this->getStatus() as $searchStatus ) {
					$status[] = $searchStatus;
				}
			}
		}
		if ( count($status) > 0 ) {
			$where[] = '(movies.status = "'.implode('" OR movies.status = "', $status).'")';
		}

		if ( $this->getSourceCount() > 0 || $this->getEventCount() > 0 ) {
			if ( $this->getSourceCount() == 0 && $this->getEventCount() > 0 ) {
				foreach ( $this->getEvents() as $eventID ) {
					$oEvent = mofilmEvent::getInstance($eventID);
					foreach ( $oEvent->getSourceSet() as $oSource ) {
						if ( !$oSource->isHidden() || $this->getUser()->getPermissions()->isRoot() ) {
							$this->addSource($oSource);
						}
					}
				}
			}

			if ( $this->getSourceCount() > 0 ) {
				if ( $this->getUser()->getClientID() > 1 && !$this->getUser()->getPermissions()->isRoot() ) {
					$oSources = $this->getUser()->getSourceSet();
					foreach ( $this->getSources() as $sourceId ) {
						if ( !$oSources->getObjectByID($sourceId) ) {
							$this->removeSource($sourceId);
						}
					}
				}
				if ( $this->getSourceCount() == 0 ) {
					$this->setSources($oSources->getObjectIDs());
				}

				$where[] = 'movieSources.sourceID IN ('.implode(',', $this->getSources()).')';
			}
		}
		
		/*
		 * @todo DR: redo the entire search system to remove the permissions checking internally.
		 * This should all be done externally in models / business logic - search should not be
		 * dependent on the user object.
		 */
		
		if ( $this->getOnlyFinalists() ) {
			$where[] = '(movieAwards.movieID IS NOT NULL AND movieAwards.type = '.dbManager::getInstance()->quote(mofilmMovieAward::TYPE_FINALIST).')';
		}

		if ( $this->getAwardType() ) {
			$where[] = '(movieAwards.movieID IS NOT NULL AND movieAwards.type = '.dbManager::getInstance()->quote($this->getAwardType()).')';
		}
		
		if ( $this->getOnlyUnratedMovies() ) {
			if ( $this->getUser()->getID() > 0 ) {
				$where[] = '(movieRatings.userID IS NULL)';
			} else {
				$where[] = '(movies.avgRating = 0 || movies.avgRating IS NULL)';
			}
		}
		
		if ( $this->getOnlyFavourites() ) {
			$where[] = 'userFavourites.userID = '.$this->getUser()->getID();
		}
					
		if ( $this->getOnlyActiveMovies() ) {
			$where[] = 'movies.active = "Y"';
		}

		if ( $this->getOrderBy() == self::ORDERBY_RATING ) {
			$where[] = '(movies.ratingCount >= 1)';
		}
		
		if ( $this->getMovieDataCount() > 0 ) {
			$tmp = array();
			foreach ( $this->getMovieData() as $dataID => $value ) {
				$tmp[] = '(movieData.datanameID = '.dbManager::getInstance()->quote($dataID).' AND movieData.value = '.dbManager::getInstance()->quote($value).')';
			}
			$where[] = '( '.implode(' '.$this->getWhereType().' ', $tmp).' )';
		}
		
		if ( $this->getUserDataCount() > 0 ) {
			$data = array();
			foreach ( $this->getUserData() as $dataID => $value ) {
				$data[] = '(userData.paramName = '.dbManager::getInstance()->quote($dataID).' AND userData.paramValue = '.dbManager::getInstance()->quote($value).')';
			}
			$where[] = implode(' '.$this->getWhereType().' ', $data);
		}
		
		if ( $this->getExcludedMoviesCount() > 0 ) {
			$where[] = 'movies.ID NOT IN ('.implode(',', $this->getExcludedMovies()).')';
		}

		if ( count($where) > 0 ) {
			$join = $this->getWhereType() == self::WHERE_USING_OR ? ' OR ' : ' AND ';
			$inQuery .= ' WHERE '.implode($join, $where);
		}
	}

	/**
	 * Adds the order by clause to the query
	 *
	 * @param string &$inQuery
	 */
	function buildOrderBy(&$inQuery) {
		$dir = $this->getOrderDirection() == self::ORDER_ASC ? 'ASC' : 'DESC';
		if ( !$this->getKeywords() && $this->getOrderBy() ) {
			$inQuery .= ' ORDER BY '.$this->getOrderBy().' '.$dir;
		} elseif ( $this->getKeywords() ) {
			/*
			 * DR 2010-09-15: we have to strip 'movies.' from the orderby because the keyword
			 * search uses a UNION ALL and movies.uploaded even though selected, apparently
			 * does not exist. uploaded does though.
			 * 
			 * Oh yeah and here we do have to order by score first, because we are basically
			 * lumping 2 completely different full text searches together and those results are
			 * not in a sensible order. Blame CN for the insanity ;)
			 * 
			 * DR 2010-09-16: accuracy is being dropped in favour of date ordering. Hmm, not
			 * sure on that one. Anyway, to restore technically accurate results, order by: 
			 * SCORE DESC first.
			 */
			$inQuery .= ' ORDER BY '.str_replace('movies.', '', $this->getOrderBy()).' '.$dir;
		}
	}
	
	
	
	/**
	 * Return value of $_UserID
	 *
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmMovieSearch
	 * @access public
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_UserEmailAddress
	 *
	 * @return string
	 * @access public
	 */
	function getUserEmailAddress() {
		return $this->_UserEmailAddress;
	}

	/**
	 * Set $_UserEmailAddress to $inUserEmailAddress
	 *
	 * @param string $inUserEmailAddress
	 * @return mofilmMovieSearch
	 * @access public
	 */
	function setUserEmailAddress($inUserEmailAddress) {
		if ( $inUserEmailAddress !== $this->_UserEmailAddress ) {
			$this->_UserEmailAddress = $inUserEmailAddress;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Status
	 *
	 * @return array
	 * @access public
	 */
	function getStatus() {
		return $this->_Status;
	}
	
	/**
	 * Returns the number of statuses in the search criteria
	 * 
	 * @return integer
	 */
	function getStatusCount() {
		return count($this->_Status);
	}
	
	/**
	 * Adds a status to the search criteria
	 *
	 * @param string $inStatus mofilmMovieBase::STATUS_ constant
	 * @return mofilmMovieSearch
	 */
	function addStatus($inStatus) {
		if ( strlen($inStatus) > 0 && !in_array($inStatus, $this->_Status) ) {
			$this->_Status[] = $inStatus;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Set $_Status to $inStatus
	 *
	 * @param string $inStatus either a single string status or an array
	 * @return mofilmMovieSearch
	 * @access public
	 */
	function setStatus($inStatus) {
		if ( !is_array($inStatus) ) {
			$inStatus = array($inStatus);
		}
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Removes $inStatus from the search criteria
	 * 
	 * @param string $inStatus
	 * @return mofilmMovieSearch
	 */
	function removeStatus($inStatus) {
		$key = array_search($inStatus, $this->_Status);
		if ( $key !== false ) {
			$this->setModified();
			unset($this->_Status[$key]);
		}
		return $this;
	}

	/**
	 * Return value of $_MovieID
	 *
	 * @return integer
	 * @access public
	 */
	function getMovieID() {
		return $this->_MovieID;
	}

	/**
	 * Set $_MovieID to $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieSearch
	 * @access public
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Events
	 *
	 * @return array
	 * @access public
	 */
	function getEvents() {
		return $this->_Events;
	}

	/**
	 * Returns the number of events in search criteria
	 *
	 * @return integer
	 */
	function getEventCount() {
		return count($this->_Events);
	}

	/**
	 * Adds an event to the search criteria
	 *
	 * @param mixed $inEvent Either id or mofilmEvent object
	 * @return mofilmMovieSearch
	 */
	function addEvent($inEvent) {
		$event = $inEvent;
		if ( $inEvent instanceof mofilmEvent ) {
			$event = $inEvent->getID();
		}

		if ( is_numeric($event) && $event > 0 && !in_array($event, $this->_Events) ) {
			$this->_Events[] = $event;
		}
		return $this;
	}

	/**
	 * Removes the event from the search criteria
	 *
	 * @param mixed $inEvent Either id or mofilmEvent object
	 * @return mofilmMovieSearch
	 */
	function removeEvent($inEvent) {
		$event = $inEvent;
		if ( $inEvent instanceof mofilmEvent ) {
			$event = $inEvent->getID();
		}

		$key = array_search($event, $this->_Events);
		if ( $key !== false ) {
			unset($this->_Events[$key]);
		}
		return $this;
	}

	/**
	 * Set an array of event ids to the search criteria
	 *
	 * @param array $inEvents
	 * @return mofilmMovieSearch
	 * @access public
	 */
	function setEvents(array $inEvents = array()) {
		if ( $inEvents !== $this->_Events ) {
			$this->_Events = $inEvents;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Sources
	 *
	 * @return array
	 * @access public
	 */
	function getSources() {
		return $this->_Sources;
	}

	/**
	 * Returns the number of sources in search criteria
	 *
	 * @return integer
	 */
	function getSourceCount() {
		return count($this->_Sources);
	}

	/**
	 * Adds a source to the search criteria
	 *
	 * @param mixed $inSource Either id or mofilmSource object
	 * @return mofilmMovieSearch
	 */
	function addSource($inSource) {
		$source = $inSource;
		if ( $inSource instanceof mofilmSourceBase ) {
			$source = $inSource->getID();
		}

		if ( is_numeric($source) && $source > 0 && !in_array($source, $this->_Sources) ) {
			$this->_Sources[] = $source;
		}
		return $this;
	}

	/**
	 * Removes the source from the search criteria
	 *
	 * @param mixed $inSource Either id or mofilmSource object
	 * @return mofilmMovieSearch
	 */
	function removeSource($inSource) {
		$source = $inSource;
		if ( $inSource instanceof mofilmSourceBase ) {
			$source = $inSource->getID();
		}

		$key = array_search($source, $this->_Sources);
		if ( $key !== false ) {
			unset($this->_Sources[$key]);
		}
		return $this;
	}

	/**
	 * Set $_Sources to $inSources
	 *
	 * @param array $inSources
	 * @return mofilmMovieSearch
	 * @access public
	 */
	function setSources($inSources) {
		if ( $inSources !== $this->_Sources ) {
			$this->_Sources = $inSources;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the data point at $inParamName, null if not set, or all data points
	 *
	 * @param mixed $inParamName
	 * @return array
	 */
	function getMovieData($inParamName = null) {
		if ( $inParamName !== null ) {
			if ( !is_numeric($inParamName) ) {
				$inParamName = mofilmDataname::getInstanceByDataname($inParamName)->getID();
			}
			
			if ( array_key_exists($inParamName, $this->_MovieData) ) {
				return $this->_MovieData[$inParamName];
			} else {
				return null;
			}
		}
		return $this->_MovieData;
	}
	
	/**
	 * Returns the number of movie data params set for search
	 * 
	 * @return integer
	 */
	function getMovieDataCount() {
		return count($this->_MovieData);
	}
	
	/**
	 * Adds the movie param to the search set overriding any existing value
	 * 
	 * @param string $inParamName
	 * @param mixed $inParamValue
	 * @return mofilmMovieSearch
	 */
	function addMovieData($inParamName, $inParamValue) {
		if ( !is_numeric($inParamName) ) {
			$inParamName = mofilmDataname::getInstanceByDataname($inParamName)->getID();
		}
		if ( is_numeric($inParamName) && $inParamName > 0 ) {
			$this->_MovieData[$inParamName] = $inParamValue;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Removes the data param from the search
	 * 
	 * @param string $inParamName
	 * @return mofilmMovieSearch
	 */
	function removeMovieData($inParamName) {
		if ( !is_numeric($inParamName) ) {
			$inParamName = mofilmDataname::getInstanceByDataname($inParamName)->getID();
		}
		if ( array_key_exists($inParamName, $this->_MovieData) ) {
			$this->_MovieData[$inParamName] = null;
			unset($this->_MovieData[$inParamName]);
		}
		return $this;
	}
	
	/**
	 * Set $_MovieData to $inMovieData
	 *
	 * @param array $inMovieData
	 * @return mofilmMovieSearch
	 */
	function setMovieData(array $inMovieData = array()) {
		if ( $inMovieData !== $this->_MovieData ) {
			$this->_MovieData = $inMovieData;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returs the number of parameters in UserData
	 *
	 * @return integer
	 */
	function getUserDataCount() {
		return count($this->_UserData);
	}
	
	/**
	 * Returns the value of $_UserData, or just the param named $inParamName
	 * 
	 * @param string $inParamName
	 * @return array|mixed
	 */
	function getUserData($inParamName = null) {
	    if ( $inParamName !== null ) {
			if ( array_key_exists($inParamName, $this->_UserData) ) {
				return $this->_UserData[$inParamName];
			} else {
				return null;
			}
		}
		return $this->_UserData;
	}
	
	/**
	 * Set value of $_UserData
	 *
	 * @param array $inUserData
	 * @return Mofilm_Movie_Search
	 */
	function setUserData($inUserData) {
	    if ( $inUserData !== $this->_UserData ) {
	        $this->_UserData = $inUserData;
	        $this->setModified();
	    }
	    return $this;
	}
	
	/**
	 * Adds a user data parameter to search on
	 *
	 * @param mixed $inDataName
	 * @param mixed $inDataValue
	 * @return Mofilm_Movie_Search
	 */
	function addUserData($inDataName, $inDataValue) {
		if ( strlen($inDataName) > 0 ) {
			$this->_UserData[$inDataName] = $inDataValue;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Exclude the specified movie from the result set
	 * 
	 * @param mixed $inMovie Either movieID or instanceof mofilmMovie
	 * @return mofilmMovieSearch
	 */
	function excludeMovie($inMovie) {
		$movieID = $inMovie;
		if ( $inMovie instanceof mofilmMovie ) {
			$movieID = $inMovie->getID();
		}
		
		if ( !in_array($movieID, $this->_ExcludedMovies) ) {
			$this->_ExcludedMovies[] = $movieID;
			$this->setModified();
		}
		
		return $this;
	}
	
	/**
	 * Returns the number of movies to be excluded
	 * 
	 * @return integer
	 */
	function getExcludedMoviesCount() {
		return count($this->_ExcludedMovies);
	}
	
	/**
	 * Returns the excluded movies array
	 * 
	 * @return array
	 */
	function getExcludedMovies() {
		return $this->_ExcludedMovies;
	}
	
	/**
	 * Sets an array of movies to exclude from results, overriding any existing array
	 * 
	 * @param array $inMovies Array of movie IDs
	 * @return mofilmMovieSearch
	 */
	function setExcludedMovies(array $inMovies) {
		if ( $inMovies !== $this->_ExcludedMovies ) {
			$this->_ExcludedMovies = $inMovies;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the type of award to search for
	 *
	 * @return string
	 */
	function getAwardType() {
		return $this->_AwardType;
	}

	/**
	 * Set the award type to search for
	 *
	 * @param string $inAwardType
	 * @return mofilmMovieSearch
	 */
	function setAwardType($inAwardType) {
		if ( $inAwardType !== $this->_AwardType ) {
			$this->_AwardType = $inAwardType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_OnlyFinalists
	 *
	 * @return boolean
	 */
	function getOnlyFinalists() {
		return $this->_OnlyFinalists;
	}
	
	/**
	 * Set $_OnlyFinalists to $inOnlyFinalists
	 *
	 * @param boolean $inOnlyFinalists
	 * @return mofilmMovieSearch
	 */
	function setOnlyFinalists($inOnlyFinalists) {
		if ( $inOnlyFinalists !== $this->_OnlyFinalists ) {
			$this->_OnlyFinalists = $inOnlyFinalists;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_OnlyUnratedMovies
	 *
	 * @return boolean
	 */
	function getOnlyUnratedMovies() {
		return $this->_OnlyUnratedMovies;
	}
	
	/**
	 * Set $_OnlyUnratedMovies to $inOnlyUnratedMovies
	 *
	 * @param boolean $inOnlyUnratedMovies
	 * @return mofilmMovieSearch
	 */
	function setOnlyUnratedMovies($inOnlyUnratedMovies) {
		if ( $inOnlyUnratedMovies !== $this->_OnlyUnratedMovies ) {
			$this->_OnlyUnratedMovies = $inOnlyUnratedMovies;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_OnlyFavourites
	 *
	 * @return boolean
	 */
	function getOnlyFavourites() {
		return $this->_OnlyFavourites;
	}
	
	/**
	 * Set $_OnlyFavourites to $inOnlyFavourites
	 *
	 * @param boolean $inOnlyFavourites
	 * @return mofilmMovieSearch
	 */
	function setOnlyFavourites($inOnlyFavourites) {
		if ( $inOnlyFavourites !== $this->_OnlyFavourites ) {
			$this->_OnlyFavourites = $inOnlyFavourites;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_OnlyTitles
	 *
	 * @return boolean
	 */
	function getOnlyTitles() {
		return $this->_OnlyTitles;
	}
	
	/**
	 * Set $_OnlyTitles to $inOnlyTitles
	 *
	 * @param boolean $inOnlyTitles
	 * @return mofilmMovieSearch
	 */
	function setOnlyTitles($inOnlyTitles) {
		if ( $inOnlyTitles !== $this->_OnlyTitles ) {
			$this->_OnlyTitles = $inOnlyTitles;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_OnlyTitles
	 *
	 * @return boolean
	 */
	function getOnlyTags() {
		return $this->_OnlyTags;
	}

	/**
	 * Set $_OnlyTags to $inOnlyTags
	 *
	 * @param boolean $inOnlyTags
	 * @return mofilmMovieSearch
	 */
	function setOnlyTags($inOnlyTags) {
		if ( $inOnlyTags !== $this->_OnlyTags ) {
			$this->_OnlyTags = $inOnlyTags;
			$this->setModified();
		}
		return $this;
	}
	

	/**
	 * Returns $_OnlyActiveMovies
	 *
	 * @return boolean
	 */
	function getOnlyActiveMovies() {
		return $this->_OnlyActiveMovies;
	}
	
	/**
	 * Set $_OnlyActiveMovies to $inOnlyActiveMovies
	 *
	 * @param boolean $inOnlyActiveMovies
	 * @return mofilmMovieSearch
	 */
	function setOnlyActiveMovies($inOnlyActiveMovies) {
		if ( $inOnlyActiveMovies !== $this->_OnlyActiveMovies ) {
			$this->_OnlyActiveMovies = $inOnlyActiveMovies;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_LoadMovieData
	 *
	 * @return boolean
	 */
	function getLoadMovieData() {
		return $this->_LoadMovieData;
	}
	
	/**
	 * Set $_LoadMovieData to $inLoadMovieData
	 *
	 * @param boolean $inLoadMovieData
	 * @return mofilmMovieSearch
	 */
	function setLoadMovieData($inLoadMovieData) {
		if ( $inLoadMovieData !== $this->_LoadMovieData ) {
			$this->_LoadMovieData = $inLoadMovieData;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_EnforceStatusRestrictions
	 *
	 * @return boolean
	 */
	function getEnforceStatusRestrictions() {
		return $this->_EnforceStatusRestrictions;
	}
	
	/**
	 * Set $_EnforceStatusRestrictions to $inEnforceStatusRestrictions
	 *
	 * @param boolean $inEnforceStatusRestrictions
	 * @return mofilmMovieSearch
	 */
	function setEnforceStatusRestrictions($inEnforceStatusRestrictions) {
		if ( $inEnforceStatusRestrictions !== $this->_EnforceStatusRestrictions ) {
			$this->_EnforceStatusRestrictions = $inEnforceStatusRestrictions;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_User
	 *
	 * @return mofilmUser
	 * @access public
	 */
	function getUser() {
		return $this->_User;
	}

	/**
	 * Set $_User to $inUser
	 *
	 * @param mofilmUser $inUser
	 * @return mofilmMovieSearch
	 * @access public
	 */
	function setUser(mofilmUser $inUser) {
		if ( $inUser !== $this->_User ) {
			$this->_User = $inUser;
			$this->setModified();
		}
		return $this;
	}
}