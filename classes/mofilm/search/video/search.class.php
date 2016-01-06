<?php

/**
 * mofilmVideoSearch
 *
 * Stored in search.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmVideoSearch
 * @category mofilmVideoSearch
 * @version $Rev: 209 $
 */

/**
 * mofilmVideoSearch Class
 *
 * The main user search system.
 *
 * @package mofilm
 * @subpackage mofilmVideoSearch
 * @category mofilmVideoSearch
 */
class mofilmVideoSearch {
	const ORDERBY_ID = 'ID';
	const ORDERBY_DATE = 'registered';
	const ORDERBY_EMAIL = 'email';
	const ORDERBY_FULLNAME = 'fullname';

	const ORDER_ASC = 1;
	const ORDER_DESC = 2;
	const ORDER_BY_UPLOADED = "movies.uploaded";
	const ORDER_BY_RATING = "movies.avgRating";
        const ORDER_BY_FMNAME = "movies.fmname";
        const ORDER_BY_TITLE = "movies.title";
        const ORDER_BY_AWARD = "movies.award";
        
	protected $_Result;
	protected $_TotalResult;
	protected $_Start = 0;
	protected $_Row;
	protected $_Keyword;
	protected $_MovieID;
	protected $_EventID;
	protected $_Status;
	protected $_SourceID;
	protected $_SourceName;
	protected $_UserID;
	protected $_Type;
	protected $_Tags;
	protected $_Titles;
	protected $_Favorites;
	protected $_Params;
	protected $_OrderBy;
	protected $_OrderDirection;
        protected $_CorporateID;
        protected $_BrandID;

	function __construct() {
		$this->reset();
		$this->initialise();
	}

	/**
	 * @see baseSearch::reset()
	 */
	function reset() {
		//parent::reset();
		$this->_Start = 0;
		$this->_TotalResult = 0;
		$this->_Keyword = '*:*';
	}

	/**
	 * @see baseSearch::initialise()
	 */
	function initialise() {
		//parent::initialise();
	}

	/**
	 * Runs the search using the supplied data
	 *
	 * @return mofilmVideoSearchResult
	 */
	function search() {

                $host = system::getConfig()->getParam("solr", "video")->getParamValue();
		$curl_handle = curl_init();

                if ( $this->getCorporateID() ) {
                    
                    $name = mofilmCorporate::getInstance($this->getCorporateID())->getName();
                    //$name = preg_replace('/\s/','+', $name);
                    $url = $host.'select/?q=s_corpid:' . $this->getCorporateID();
                    
                    if ( $this->getBrandID() ) {
                            $name = mofilmBrand::getInstance($this->getBrandID())->getName();
                            $url = $url . '+AND+s_brandid:' . $this->getBrandID();
                    }
                    
                    if ( $this->getEventID() ) {
                            $url = $url . "+AND+s_eventid:" . $this->getEventID();
                    }

                    if ( $this->getStatus() ) {
                            $url = $url . "+AND+s_status:" . urlencode($this->getStatus());
                    }
                    
                    if ( $this->getProductID() ) {
                            $url = $url . "+AND+s_productid:" . urlencode($this->getProductID());
                    }

                    if ( urldecode($this->getKeyword()) != "*:*" ) {
                        systemLog::message($this->getKeyword());
                        $keyword = preg_replace("/\+/","+AND+",trim($this->getKeyword()));
			$url = $url . "+AND+q=" . $keyword;
                    }
                    
                    
                    if ( $this->getType() ) {
                            $url = $url . "+AND+s_type:" . urlencode($this->getType());
                    }

                    if ( $this->getMovieID() ) {
                            $url = $url . "+AND+s_id=" . $this->getMovieID();
                    }
                    $url .= '&wt=json&start=' . $this->getStart() . '&rows=30';

                } 
                else if ( $this->getBrandID() ) {
                    
                    $name = mofilmBrand::getInstance($this->getBrandID())->getName();
                    //$name = preg_replace('/\s/','+', $name);
                    $url = $host.'select/?q=s_brandid:' . $this->getBrandID();
                                        
                    if ( $this->getEventID() ) {
                            $url = $url . "+AND+s_eventid:" . $this->getEventID();
                    }
                    
                    if ( $this->getProductID() ) {
                            $url = $url . "+AND+s_productid:" . urlencode($this->getProductID());
                    }

                    if ( $this->getStatus() ) {
                            $url = $url . "+AND+s_status:" . urlencode($this->getStatus());
                    }

                    if ( urldecode($this->getKeyword()) != "*:*" ) {
                        $keyword = preg_replace("/\+/","+AND+",trim($this->getKeyword()));
			$url = $url . "+AND+q=" . $keyword;
                    }
                    
                    
                    if ( $this->getType() ) {
                            $url = $url . "+AND+s_type:" . urlencode($this->getType());
                    }

                    if ( $this->getMovieID() ) {
                            $url = $url . "+AND+s_id=" . $this->getMovieID();
                    }
                    $url .= '&wt=json&start=' . $this->getStart() . '&rows=30';

                } else if ( $this->getTags() && urldecode($this->getKeyword()) != "*:*" ) {

			//$keyword = preg_replace("/ /","+AND+",trim(urldecode($this->getKeyword())));                              
                        $keyword = trim($this->getKeyword());
                        systemLog::message("tag".urldecode($keyword));
                        systemLog::message("tag".$keyword);
                        systemLog::message("tag".urlencode($keyword));
			systemLog::message("tag".urlencode('"'.$keyword.'"'));
			$url = $host.'select/?q=s_genre:"' . $keyword. '"';
			
			if ( $this->getEventID() ) {
				$url = $url . "+AND+s_eventid:" . $this->getEventID();
			}
                        if ( $this->getProductID() ) {
                            $url = $url . "+AND+s_productid:" . urlencode($this->getProductID());
                        }
                        
			if ( $this->getStatus() ) {
				$url = $url . "+AND+s_status:" . urlencode($this->getStatus());
			}

			if ( $this->getType() ) {
				$url = $url . "+AND+s_type:" . urlencode($this->getType());
			}

			if ( $this->getMovieID() ) {
				$url = $url . "+AND+s_id=" . $this->getMovieID();
			}

			$url .= '&wt=json&start=' . $this->getStart() . '&rows=30';



		} else if ( $this->getEventID() ) {

			if ( $this->getEventID() ) {
				$url = $host.'select/?q=s_eventid:' . $this->getEventID();
			}
                        if ( $this->getProductID() ) {
                            $url = $url . "+AND+s_productid:" . urlencode($this->getProductID());
                        }
                        
			if ( $this->getStatus() ) {
				$url = $url . "+AND+s_status:" . urlencode($this->getStatus());
			}

                        if ( urldecode($this->getKeyword()) != "*:*" ) {
                            $keyword = preg_replace("/\+/","+AND+",trim($this->getKeyword()));
                            $url = $url . "+AND+q=" . $keyword;
                        }
/*                        
			if ( urldecode($this->getKeyword()) != "*:*" ) {
				$url = $url . "+AND+q=" . urlencode($this->getKeyword());
			}
*/
			if ( $this->getType() ) {
				$url = $url . "+AND+s_type:" . urlencode($this->getType());
			}
			
			if ( $this->getMovieID() ) {
				$url = $url . "+AND+s_id=" . $this->getMovieID();
			}
		
			$url .= '&wt=json&start=' . $this->getStart() . '&rows=30';
		} else if ( $this->getUserID() ) {
                        
			$url = $host.'select/?q=s_userid:' . $this->getUserID() . '&wt=json&start=' . $this->getStart() . '&rows=30';
		} else if ( $this->getStatus() ) {
			$url = $host.'select/?q=s_status:' . urlencode($this->getStatus()) . '&wt=json&start=' . $this->getStart() . '&rows=30';
		} else if ( $this->getProductID() ) {
                            $url = $host . "select/?q=s_productid:" . $this->getProductID();
                            
                 	if ( $this->getType() ) {
				$url = $url . "+AND+s_type:" . urlencode($this->getType());
			}
                        
		        $url .= '&wt=json&start=' . $this->getStart() . '&rows=30';
   
                } else if ( $this->getType() ) {
                        $url = $host.'select/?q=s_type:' . urlencode($this->getType());
			if ( urldecode($this->getKeyword()) == "*:*" ) {
				$url = $url ;
			} else {
				$url = $url . '+AND+s_tagname:'. $this->getKeyword();
			}   
                        if ( $this->getProductID() ) {
                            $url = $url . "+AND+s_productid:" . urlencode($this->getProductID());
                        }
                        $url .= '&wt=json&start=' . $this->getStart() . '&rows=30';
                        
		} else if ( $this->getMovieID() ) {
			$url = $host. 'select/?q=s_id:' . $this->getMovieID() . '&wt=json&start=' . $this->getStart() . '&rows=30';
		}  else {
                        systemLog::message("keyword" . $this->getKeyword());
                        
                        //$keyword = preg_replace("/\+/","+AND+",trim($this->getKeyword()));
                        $keyword = trim(urldecode($this->getKeyword()));
                        $keyword = preg_replace("/\+/","+AND+",  $keyword);    
                        if ($keyword != "*:*"){
                            systemLog::message("here".$keyword);
                            $keyword = preg_replace("/:/","\:", $keyword);
                            $keyword = urlencode('"'.$keyword.'"');
                        }
                        
			//$url = $url . "+AND+q=" . url$keyword;                        
			$url = $host. 'select/?q=' . $keyword . '&wt=json&start=' . $this->getStart() . '&rows=30';
		}

		if ( $this->getOrderDirection() == 2 && $this->getOrderBy() == self::ORDER_BY_RATING ) {
			$url.= "&sort=s_avgrating+desc";
		} else if ( $this->getOrderDirection() == 1 && $this->getOrderBy() == self::ORDER_BY_RATING ) {
			$url.= "&sort=s_avgrating+asc";
                } else if ( $this->getOrderDirection() == 2 && $this->getOrderBy() == self::ORDER_BY_UPLOADED ){
                    $url.= "&sort=s_uploaded+desc";
                } else if ( $this->getOrderDirection() == 1 && $this->getOrderBy() == self::ORDER_BY_UPLOADED ) {
                    $url.= "&sort=s_uploaded+asc";
                } else if ( $this->getOrderDirection() == 2 && $this->getOrderBy() == self::ORDER_BY_FMNAME ){
                    $url.= "&sort=s_name+desc";
                } else if ( $this->getOrderDirection() == 1 && $this->getOrderBy() == self::ORDER_BY_FMNAME ) {
                    $url.= "&sort=s_name+asc";
                } else if ( $this->getOrderDirection() == 2 && $this->getOrderBy() == self::ORDER_BY_TITLE ){
                    $url.= "&sort=s_title+desc";
                } else if ( $this->getOrderDirection() == 1 && $this->getOrderBy() == self::ORDER_BY_TITLE ) {
                    $url.= "&sort=s_title+asc";
                } else if ( $this->getOrderDirection() == 1 && $this->getOrderBy() == self::ORDER_BY_AWARD ){
                    $url.= "&sort=s_award+desc";
                } else if ( $this->getOrderDirection() == 2 && $this->getOrderBy() == self::ORDER_BY_AWARD ) {
                    $url.= "&sort=s_award+asc";
                }    

		systemLog::message($url);
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		$jsonResponse = curl_exec($curl_handle);


		curl_close($curl_handle);
		$this->setResponse(json_decode($jsonResponse));
	}

	function getResultList() {

		$oResponse = $this->getResponse();
		$this->_TotalResult = $oResponse->response->numFound;
		$docs = $oResponse->response->docs;

		return new mofilmResultSet($docs, $this->getTotalResult(), $this);
	}

	/**
	 * Sets the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @param JSON $inResponse
	 * @return mofilmMusicApisso 
	 */
	function setResponse($inResponse) {
		if ( $inResponse !== $this->_Response ) {
			$this->_Response = $inResponse;
		}
		return $this;
	}

	/**
	 * Returns the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @return JSON
	 */
	function getResponse() {
		return $this->_Response;
	}

	function setTotalResult($inTotalResult) {
		if ( $inTotalResult !== $this->_TotalResult ) {
			$this->_TotalResult = $inTotalResult;
		}
		return $this;
	}

	/**
	 * Returns the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @return integer
	 */
	function getTotalResult() {
		return $this->_TotalResult;
	}

	function setStart($inStart) {
		if ( $inStart !== $this->_Start ) {
			$this->_Start = $inStart;
		}
		return $this;
	}

	/**
	 * Returns the start of the result
	 * 
	 * @return integer
	 */
	function getStart() {
		return $this->_Start;
	}
        
        /**
         * Sets the keyword
         * 
         * @param type $inKeyword
         * @return \mofilmVideoSearch
         */
	function setKeyword($inKeyword) {
		if ( $inKeyword !== $this->_Keyword ) {
			$this->_Keyword = $inKeyword;
		}
		return $this;
	}

	/**
	 * Returns the keyword
	 * 
	 * @return integer
	 */
	function getKeyword() {
		return $this->_Keyword;
	}
        
        /**
         * Sets the movieID
         * 
         * @param type $inMovieID
         * @return \mofilmVideoSearch
         */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
		}
		return $this;
	}

	/**
	 * Returns the movieID
	 * 
	 * @return integer
	 */
	function getMovieID() {
		return $this->_MovieID;
	}
        
        /**
         * Sets the eventID
         * 
         * @param type $inEventID
         * @return \mofilmVideoSearch
         */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
		}
		return $this;
	}

	/**
	 * Returns the eventID
	 * 
	 * @return integer
	 */
	function getEventID() {
		return $this->_EventID;
	}
        
        /**
         *
         * Sets the status
         * 
         * @param type $inStatus
         * @return \mofilmVideoSearch
         */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
		}
		return $this;
	}

	/**
	 * Returns the status
	 * 
	 * @return integer
	 */
	function getStatus() {
		return $this->_Status;
	}
        
        /**
         * Sets the sourceID
         * 
         * @param type $inSourceID
         * @return \mofilmVideoSearch
         */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
		}
		return $this;
	}

	/**
	 * Returns the sourceID
	 * 
	 * @return integer
	 */
	function getSourceID() {
		return $this->_SourceID;
	}
        
        /**
         * 
         * Sets the source name
         * 
         * @param type $inSourceName
         * @return \mofilmVideoSearch
         */
	function setSourceName($inSourceName) {
		if ( $inSourceName !== $this->_SourceName ) {
			$this->_SourceName = $inSourceName;
		}
		return $this;
	}

	/**
	 * Returns the sourceName
	 * 
	 * @return integer
	 */
	function getSourceName() {
		return $this->_SourceName;
	}
        
        /**
         * Sets the type
         * 
         * @param type $inType
         * @return \mofilmVideoSearch
         */
	function setType($inType) {
		if ( $inType !== $this->_Type ) {
			$this->_Type = $inType;
		}
		return $this;
	}

	/**
	 * Returns the type
	 * 
	 * @return integer
	 */
	function getType() {
		return $this->_Type;
	}
        
        /**
         * Set the tags
         * 
         * @param type $inTags
         * @return \mofilmVideoSearch
         */
	function setTags($inTags) {
		if ( $inTags !== $this->_Tags ) {
			$this->_Tags = $inTags;
		}
		return $this;
	}

	/**
	 * Returns the tags
	 *
	 * @return integer
	 */
	function getFavourites() {
		return $this->_Favorites;
	}
	
        /**
         * Sets the favorites
         * 
         * @param type $inFavorites
         * @return \mofilmVideoSearch
         */
	function setFavorites($inFavorites) {
		if ( $inFavorites !== $this->_Favorites ) {
			$this->_Favorites = $inFavorites;
		}
		return $this;
	}
        
        /**
         * Sets the titles
         * 
         * @param type $inTitles
         * @return \mofilmVideoSearch
         */
	function setTitles($inTitles) {
		if ( $inTitles !== $this->_Titles ) {
			$this->_Titles = $inTitles;
		}
		return $this;
	}


	/**
	 * Returns the tags
	 *
	 * @return integer
	 */
	function getTags() {
		return $this->_Tags;
	}

	/**
	 * Returns the titles
	 *
	 * @return integer
	 */
	function getTitles() {
		return $this->_Titles;
	}
        
        
        /**
         * Sets the productID
         * 
         * @param type $inProductID
         * @return \mofilmVideoSearch
         */
	function setProductID($inProductID) {
		if ( $inProductID !== $this->_ProductID ) {
			$this->_ProductID = $inProductID;
		}
		return $this;
	}

	/**
	 * Returns the productID
	 * 
	 * @return integer
	 */
	function getProductID() {
		return $this->_ProductID;
	}
        
        
        /**
         * Sets the corporateID
         * 
         * @param type $inCorporateID
         * @return \mofilmVideoSearch
         */
	function setCorporateID($inCorporateID) {
		if ( $inCorporateID !== $this->_CorporateID ) {
			$this->_CorporateID = $inCorporateID;
		}
		return $this;
	}

	/**
	 * Returns the corporateID
	 * 
	 * @return integer
	 */
	function getCorporateID() {
		return $this->_CorporateID;
	}
        
        
         /**
         * Sets the brandID
         * 
         * @param type $inBrandID
         * @return \mofilmVideoSearch
         */
	function setBrandID($inBrandID) {
		if ( $inBrandID !== $this->_BrandID ) {
			$this->_BrandID = $inBrandID;
		}
		return $this;
	}

	/**
	 * Returns the brandID
	 * 
	 * @return integer
	 */
	function getBrandID() {
		return $this->_BrandID;
	}
        

        /**
         * Sets the userID
         * 
         * @param type $inUserID
         * @return \mofilmVideoSearch
         */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
		}
		return $this;
	}

	/**
	 * Returns the userID
	 * 
	 * @return integer
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Returns $_OrderDirection
	 *
	 * @return integer
	 */
	function getOrderDirection() {
		return $this->_OrderDirection;
	}

	/**
	 * Set $_OrderDirection to $inOrderDirection
	 *
	 * @param integer $inOrderDirection
	 * @return baseSearch
	 */
	function setOrderDirection($inOrderDirection) {
		if ( $inOrderDirection !== $this->_OrderDirection ) {
			if ( !in_array($inOrderDirection, array(self::ORDER_ASC, self::ORDER_DESC)) ) {
				throw new systemException("Order direction can only be 1 for Ascending or 2 for descending");
			}
			$this->_OrderDirection = $inOrderDirection;
		}
		return $this;
	}

	/**
	 * Returns $_OrderBy
	 *
	 * @return string
	 */
	function getOrderBy() {
		return $this->_OrderBy;
	}

	/**
	 * Set $_OrderBy to $inOrderBy
	 *
	 * @param string $inOrderBy
	 * @return baseSearch
	 */
	function setOrderBy($inOrderBy) {
		if ( $inOrderBy !== $this->_OrderBy ) {
			$this->_OrderBy = $inOrderBy;
		}
		return $this;
	}

}