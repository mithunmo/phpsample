<?php
/**
 * musicModel.class.php
 * 
 * musicModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category musicModel
 * @version $Rev: 623 $
 */


/**
 * musicModel class
 * 
 * Provides the "music" page
 * 
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category musicModel
 */
class musicModel extends momusicWork {
	
	
	
	
	/**
	 * Stores an instance of mofilmUserSearch
	 * 
	 * @var momusicWorkSearch
	 * @access protected
	 */
	protected $_WorkSearch;
	
	/**
	 * Stores the last run search result
	 * 
	 * @var momusciWorkSearchResult
	 * @access protected
	 */
	protected $_SearchResult;
	
	/**
	 * 
	 * @var $_Keywords
	 * @access protected
	 */
	protected $_Keywords;
	
	protected $_Artist;
	
	protected $_Genre;
	
	protected $_Mood;

	protected $_Offset;
	
	protected $_Limit;
	
	protected $_SearchWord;
	
	protected $_Instrument;
	
	protected $_Style;
	
	protected $_Tempo;
	
	protected $_Facet;
	
	protected $_Category;
	
	protected $_Filter;
	
	protected $_FilterArr;

	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
	}
	
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		return momusicWorks::listOfObjects($inOffset, $inLimit);
	}
	
	/**
	 * Returns the object primary key value
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return parent::getPrimaryKey();
	}
		
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects() {
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.work';
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}

	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalActiveObjects() {
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.work where status = 0';
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		$total = $this->getTotalObjects();
		
		if ( $inLimit > 0 ) {
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
	}
		
	/**
	 * Returns a new blank object
	 *
	 * @return systemDaoInterface
	 */
	function getNewObject() {
		return new momusicWorks();
	}
	
	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey) {
		/**
		 * @todo set primary key for this object
		 */
		$this->setID($inPrimaryKey);
		$this->load();
		return $this;
	}
	
	function uploadMovie() {
		
	}
	
	/* Music search functionality    */
	
	
	/**
	 * Creates an returns a mofilmUserSearch object
	 * 
	 * @return mofilmUserSearch
	 */
	function getUserSearch() {
		if ( !$this->_WorkSearch instanceof momusicWorkSearch ) {
			$this->_WorkSearch = new momusicWorkSearch();
			//$this->_UserSearch->setLoadUserDetails(true);
			//$this->_UserSearch->setOnlyActiveUsers(true);
		}
		return $this->_WorkSearch;
	}
	
	/**
	 * Returns the search result object, or null if no search has been run
	 * 
	 * @return mofilmUserSearchResult
	 */
	function getSearchResult() {
		return $this->_SearchResult;
	}
	
	/**
	 * Runs the search with whatever parameters are in it
	 * 
	 * @return momusicWorkSearchResult
	 */
	function doSearch() {
		//systemLog::message("keyword".$this->getUserSearch()->getKeywords());
		if ( strlen($this->getUserSearch()->getKeywords()) >  1 ) { 
			
				$this->getUserSearch()->setWhereType(baseSearch::WHERE_USING_AND);
				$this->_SearchResult = $this->getUserSearch()->search();
			
				//$this->getUserSearch()->setWhereType(baseSearch::WHERE_USING_AND);
				//$this->_SearchResult = $this->getUserSearch()->search();
				
				
		} else {
			$this->getUserSearch()->setWhereType(baseSearch::WHERE_USING_AND);
			$this->_SearchResult = $this->getUserSearch()->search();
		}	
		
		//systemLog::message($this->_SearchResult);
		return $this->_SearchResult;
	}
	

	/**
	 * Set $_Tag to $inTag
	 *
	 * @param string $inTag
	 * @return string
	 */
	function setKeywords($inKeywords) {
		if ( $inKeywords !== $this->_Keywords ) {
			$this->_Keywords = $inKeywords;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Tag
	 *
	 * @return string
	 */
	function getKeywords() {
		return $this->_Keywords;
	}
	
	
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getMashObjectList($inOffset = null, $inLimit = 30) {
		return momusicMash::listOfObjectsByUserID($inUserID, $inOffset, $inLimit);
	}

	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getMashTotalObjects($inUserID) {
		$query = '
			SELECT COUNT(*) AS Count
			FROM '.system::getConfig()->getDatabase('momusic_content').'.mash  	  
			WHERE userID ='.$inUserID;
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getMashLastPageOffset($inLimit,$inUserID) {
		$total = $this->getMashTotalObjects($inUserID);
		
		if ( $inLimit > 0 ) {
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
	
	}
	
	/**
	 * Set $_Offset to $inOffset
	 *
	 * @param integer $inOffset
	 * @return integer
	 */
	function setOffset($inOffset) {
		if ( $inOffset !== $this->_Offset ) {
			$this->_Offset = $inOffset;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Offset
	 *
	 * @return integer
	 */
	function getOffset() {
		return $this->_Offset;
	}

	/**
	 * Set $_Limit to $inLimit
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function setLimit($inLimit) {
		if ( $inLimit !== $this->_Limit ) {
			$this->_Limit = $inLimit;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Limit
	 *
	 * @return integer
	 */
	function getLimit() {
		return $this->_Limit;
	}

	
	function solrSearch() {
		
			$oKeyword = new momusicKeywordSearch();
			$oKeyword->setStart($this->getOffset());
			
			
			if ( strlen($this->getKeywords()) > 1 && $this->getKeywords() !="*:*" && $this->getCategory() && $this->getFilter() ) {
				$oKeyword->setKeyword(urlencode($this->getKeywords()));
				$oKeyword->setFilter(urlencode($this->getFilter()));
				$oKeyword->setCategory(urlencode($this->getCategory()));
				$oKeyword->searchFacetQuery();				
				
			} else if ( strlen($this->getKeywords()) > 1 && $this->getKeywords() =="*:*" && $this->getCategory() && $this->getFilter() ) {
				$oKeyword->setArtist(urlencode($this->getArtist()));
				$oKeyword->setGenre(urlencode($this->getGenre()));
				$oKeyword->setMood(urlencode($this->getMood()));
				$oKeyword->setInstrument(urlencode($this->getInstrument()));
				$oKeyword->setStyle(urlencode($this->getStyle()));
				$oKeyword->setTempo(urlencode($this->getTempo()));
				
				$oKeyword->setFilter(urlencode($this->getFilter()));
				$oKeyword->setCategory(urlencode($this->getCategory()));
				$oKeyword->searchFacetQueryMusicCategory();				
				
			} else if ( strlen($this->getKeywords()) > 1 && $this->getKeywords() !="*:*" ) {
				$oKeyword->setKeyword(urlencode($this->getKeywords()));
				$oKeyword->search();
				
			} else {
				$oKeyword->setArtist(urlencode($this->getArtist()));
				$oKeyword->setGenre(urlencode($this->getGenre()));
				$oKeyword->setMood(urlencode($this->getMood()));
				$oKeyword->setInstrument(urlencode($this->getInstrument()));
				$oKeyword->setStyle(urlencode($this->getStyle()));
				$oKeyword->setTempo(urlencode($this->getTempo()));
				$oKeyword->search();

			}
			
			$oResult =  $oKeyword->getResultList();
			$this->_Facet = $oKeyword->getFacet();
			
			if ( $oResult->getTotalResults() == 0 ) {
				if ( $this->getArtist() ) {
					$oKeyword->setKeyword(urlencode($this->getArtist()));
					$oKeyword->setArtist("");
				} else if ( $this->getStyle() ) {
					$oKeyword->setKeyword(urlencode($this->getStyle()));
					$oKeyword->setStyle("");
				} else if ( $this->getInstrument() ) {
					$oKeyword->setKeyword(urlencode($this->getInstrument()));
					$oKeyword->setInstrument("");
				} else if ( $this->getGenre() ) {
					$oKeyword->setKeyword(urlencode($this->getGenre()));
					$oKeyword->setGenre("");
				}
				
				$oKeyword->setStart($this->getOffset());
				$oKeyword->search();
				$oResult =  $oKeyword->getResultList();
				
				
			}
						
			return $oResult;
	}
	
	
	/**
	 * Set $_Artist to $inArtist
	 *
	 * @param string $inArtist
	 * @return string
	 */
	function setArtist($inArtist) {
		if ( $inArtist !== $this->_Artist ) {
			$this->_Artist = $inArtist;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Artist
	 *
	 * @return string
	 */
	function getArtist() {
		return $this->_Artist;
	}
	

	
	/**
	 * Set $_Artist to $inArtist
	 *
	 * @param string $inArtist
	 * @return string
	 */
	function setGenre($inGenre) {
		if ( $inGenre !== $this->_Genre ) {
			$this->_Genre = $inGenre;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Artist
	 *
	 * @return string
	 */
	function getGenre() {
		return $this->_Genre;
	}
	

	/**
	 * Set $_Artist to $inArtist
	 *
	 * @param string $inArtist
	 * @return string
	 */
	function setMood($inMood) {
		if ( $inMood !== $this->_Mood ) {
			$this->_Mood = $inMood;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Artist
	 *
	 * @return string
	 */
	function getMood() {
		return $this->_Mood;
	}
	

	/**
	 * Set $_Artist to $inArtist
	 *
	 * @param string $inArtist
	 * @return string
	 */
	function setInstrument($inInst) {
		if ( $inInst !== $this->_Instrument ) {
			$this->_Instrument = $inInst;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Artist
	 *
	 * @return string
	 */
	function getInstrument() {
		return $this->_Instrument;
	}
	

	/**
	 * Set $_Style to $inStyle
	 *
	 * @param string $inStyle
	 * @return string
	 */
	function setStyle($inStyle) {
		if ( $inStyle !== $this->_Style ) {
			$this->_Style = $inStyle;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Artist
	 *
	 * @return string
	 */
	function getStyle() {
		return $this->_Style;
	}
	
	/**
	 * Set $_Tempo to $inTempo
	 *
	 * @param string $inTempo
	 * @return string
	 */
	function setTempo($inTempo) {
		if ( $inTempo !== $this->_Tempo ) {
			$this->_Tempo = $inTempo;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Tempo
	 *
	 * @return string
	 */
	function getTempo() {
		return $this->_Tempo;
	}
	
		
	/**
	 * Set $_Artist to $inArtist
	 *
	 * @param string $inArtist
	 * @return string
	 */
	function setSearchWord($inWord) {
		if ( $inWord !== $this->_SearchWord ) {
			$this->_SearchWord = $inWord;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Artist
	 *
	 * @return string
	 */
	function getSearchWord() {
		return $this->_SearchWord;
	}
	
	
	function getItemName($inID){
		return self::getInstance($inID)->getSongName();
	}
	
	/**
	 * Gets the list of facet
	 * 
	 * @return array
	 */
	function getFacet() {
		return $this->_Facet;
	}
	
	/**
	 * Set $_Artist to $inArtist
	 *
	 * @param string $inArtist
	 * @return string
	 */
	function setCategory($inCategory) {
		if ( $inCategory !== $this->_Category ) {
			$this->_Category = $inCategory;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Artist
	 *
	 * @return string
	 */
	function getCategory() {
		return $this->_Category;
	}
	
	/**
	 * Set $_Fiter to $inFilter
	 *
	 * @param string $inFilter
	 * @return string
	 */
	function setFilter($inFilter) {
		if ( $inFilter !== $this->_Filter ) {
			$this->_Filter = $inFilter;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Artist
	 *
	 * @return string
	 */
	function getFilter() {
		return $this->_Filter;
	}
	

	function strstr_after($haystack, $needle, $case_insensitive = false) {
		$strpos = ($case_insensitive) ? 'stripos' : 'strpos';
		$pos = $strpos($haystack, $needle);
		if (is_int($pos)) {
			return substr($haystack, $pos + strlen($needle));
		}
		// Most likely false or null
		return $pos;
	}	
	
	/**
	 * Deletes the keyword and forms the url
	 * 
	 */
	function getNewUrl(){
	
		$key = $this->strstr_after($_SERVER["REQUEST_URI"], '?');
		$keywordsplit = preg_split("/&/", $key);
		$catArr = preg_split("/,/",$this->strstr_after($keywordsplit[1], '='));
		$filArr = preg_split("/,/",$this->strstr_after($keywordsplit[2], '='));

		$link = array();
		for ($i=0;$i<count($catArr);$i++) {
			$link[$filArr[$i]] = $keywordsplit[0].$this->getJoinedString($catArr, $filArr, $i);
		}
		$this->_FilterArr = $link;
		return $link;
	}
	
	/**
	 *
	 * Gets the links to remove the filter 
	 * 
	 * @param type $catArr
	 * @param type $filArr
	 * @param type $i
	 * @return string 
	 */
	function getJoinedString($catArr,$filArr,$i) {
		$link =""; 
		$categoryArr = array();
		$filterArr = array();
		for ($j = 0 ;$j<count($catArr);$j++) {
			if ( $j != $i ) {
				$categoryArr[]= $catArr[$j];
				$filterArr[] = $filArr[$j];
			}
			
		}
		$link.="&category=".join(",", $categoryArr)."&filterq=".join(",",$filterArr);
		
		return $link;
	}
	
	/**
	 * Checks the filter selected on page
	 * 
	 * @param type $inKey
	 * @return boolean 
	 */
	function getFilterDefined($inKey) {
				
		$pieces = explode(",", $this->getFilter());
		if ( array_search($inKey, $pieces) === false ) {
			return false;
		} 
		return true;
	}
}