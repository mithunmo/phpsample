<?php

/**
 * mofilmCrewSearch
 *
 * Stored in search.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCrewSearch
 * @category mofilmCrewSearch
 * @version $Rev: 209 $
 */

/**
 * mofilmCrewSearch Class
 *
 * The main user search system.
 *
 * @package mofilm
 * @subpackage mofilmCrewSearch
 * @category mofilmCrewSearch
 */
class mofilmCrewSearch {
	const ORDERBY_ID = 'ID';
	const ORDERBY_DATE = 'registered';
	const ORDERBY_EMAIL = 'email';
	const ORDERBY_FULLNAME = 'fullname';

	const ORDER_ASC = 1;
	const ORDER_DESC = 2;
	const ORDER_BY_UPLOADED = "movies.uploaded";
	const ORDER_BY_RATING = "movies.avgRating";


	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Result;
	protected $_TotalResult;
	protected $_Start;
	protected $_Row;
	protected $_Keyword;
	protected $_MovieID;
	protected $_EventID;
	protected $_Status;
	protected $_SourceID;
	protected $_SourceName;
	protected $_UserID;
	protected $_Type;
	protected $_Params;
	protected $_Skill;
	protected  $_Location;

	/**
	 * Stores $_OrderBy
	 *
	 * @var string
	 * @access protected
	 */
	protected $_OrderBy;

	/**
	 * Stores $_OrderDirection
	 *
	 * @var string
	 * @access protected
	 */
	protected $_OrderDirection;

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
		$this->_Skill = "";
		$this->_Location = "";
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


		$curl_handle = curl_init();

		$url =  'localhost:8080/solr/core3/select/?wt=json&start=' . $this->getStart() . '&rows=30';
		if ( $this->getLocation() || $this->getSkill() ) {
			
			if ( $this->getLocation() ) {
				$url = $url . '&q=s_city:' . $this->getLocation();
				
				if ( $this->getSkill() != "" ) {
						$url = $url . '+AND+(s_skill:' . $this->getSkill()."+OR+s_role:".$this->getSkill().")";
				} 		
			} else if ( $this->getSkill() ) {
				$url = $url . '&q=(s_skill:' . $this->getSkill()."+OR+s_role:".$this->getSkill().")";
				
				if ( $this->getLocation() != "" ) {
						$url = $url . '+AND+s_location:' . $this->getLocation();
				} 						
			}
			
		
		} else if ( $this->getKeyword() ) {
			$url = $url . '&q=' . $this->getKeyword();
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

		//foreach ( $docs as $value ) {			
		//	systemLog::message($value->s_artist);
		//}

		return new mofilmResultSet($docs, $this->getTotalResult(), $this);
		//return $docs;
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
	 * Returns the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @return integer
	 */
	function getStart() {
		return $this->_Start;
	}

	function setKeyword($inKeyword) {
		if ( $inKeyword !== $this->_Keyword ) {
			$this->_Keyword = $inKeyword;
		}
		return $this;
	}

	/**
	 * Returns the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @return integer
	 */
	function getKeyword() {
		return $this->_Keyword;
	}

	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
		}
		return $this;
	}

	/**
	 * Returns the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @return integer
	 */
	function getMovieID() {
		return $this->_MovieID;
	}

	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
		}
		return $this;
	}

	/**
	 * Returns the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @return integer
	 */
	function getEventID() {
		return $this->_EventID;
	}

	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
		}
		return $this;
	}

	/**
	 * Returns the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @return integer
	 */
	function getStatus() {
		return $this->_Status;
	}

	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
		}
		return $this;
	}

	/**
	 * Returns the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @return integer
	 */
	function getSourceID() {
		return $this->_SourceID;
	}

	function setSourceName($inSourceName) {
		if ( $inSourceName !== $this->_SourceName ) {
			$this->_SourceName = $inSourceName;
		}
		return $this;
	}

	/**
	 * Returns the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @return integer
	 */
	function getSourceName() {
		return $this->_SourceName;
	}

	function setType($inType) {
		if ( $inType !== $this->_Type ) {
			$this->_Type = $inType;
		}
		return $this;
	}

	/**
	 * Returns the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @return integer
	 */
	function getType() {
		return $this->_Type;
	}

	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
		}
		return $this;
	}

	/**
	 * Returns the response from the mofilmmusic-Audiosocket SSO API
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
			//if ( !in_array($inOrderBy, $this->_AllowedOrderBy) ) {
			//	throw new systemException("You can only order by one of the following fields: ".implode(', ', $this->_AllowedOrderBy));
			//}
			$this->_OrderBy = $inOrderBy;
		}
		return $this;
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
		}
		return $this;
	}		
	
}
