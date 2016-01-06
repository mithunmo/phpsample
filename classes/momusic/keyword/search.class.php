<?php
/**
 * momusicKeywordSearch
 *
 * Stored in search.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage momusicKeywordSearch
 * @category momusicKeywordSearch
 * @version $Rev: 209 $
 */


/**
 * momusicKeywordSearch Class
 *
 * The main user search system.
 *
 * @package mofilm
 * @subpackage momusicKeywordSearch
 * @category momusicKeywordSearch
 */
class momusicKeywordSearch {

	const ORDERBY_PRIORITY = 's_priority';
	const FORMAT_JSON  = "json";
			
	protected $_Result;
	
	protected $_TotalResult; 
	
	protected $_Start;
	
	protected $_Row;
	
	protected $_Keyword;
	
	protected $_Artist;
	
	protected  $_Genre;
	
	protected $_Mood;

	protected $_Instrument;
	
	protected $_Style;

	protected $_Tempo;

	protected $_Params;
	
	protected $_Format = "json";
	
	protected $_Filter;
	
	protected $_Category;
        
        protected $_Catalog;

        protected $_BrandMusicID;
        
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
	 * @return momusicKeywordSearchResult
	 */
	function search() {
		
     
     		$url = "";
                if ($this->getCatalog() == "mozayic" ){
//                    /$url = "http://localhost:8080/solr/core4" . '/select/?indent=true&start=' . $this->getStart() . '&rows=20';
                    $url = system::getConfig()->getParam('solr', 'mosaic', 'http://localhost:8080/solr/core4') . '/select/?indent=true&start=' . $this->getStart() . '&rows=20';
                } else {
                    $url = system::getConfig()->getParam('solr', 'momusic', 'http://localhost:8080/solr/core1') . '/select/?indent=true&start=' . $this->getStart() . '&rows=20';;
                }
       
                $curl_handle = curl_init();
		
		if ( $this->getArtist() ) {
			$url = $url . '&q='.$this->getArtist().'&qf=s_artist';
		} elseif ( $this->getGenre() ) {
			$url = $url . '&q=s_genre:'.$this->getGenre();
		} elseif ( $this->getMood() ) {
			$url = $url . '&q=s_mood:'.$this->getMood();
		} elseif ( $this->getInstrument() ) {
			$url = $url . '&q='.$this->getInstrument().'&qf=s_instrument';;
		} elseif ( $this->getStyle() ) {
			$url = $url . '&q=s_style:'.$this->getStyle();
		} elseif ( $this->getTempo() ) {
			$url = $url . '&q=s_description:'.$this->getTempo();
		} else {
                        systemLog::message($this->getKeyword());
                        $keyword = preg_replace("/\+/","+AND+",trim($this->getKeyword()));                    
			$url = $url . '&q='.$keyword."&qf=text";
		}
		
		$url .= "&facet=true&facet.field=s_mood&facet.field=s_genre&facet.mincount=1";
                
		//$format = "&fl=*+score&wt=json&sort=score+desc,s_priority+desc&defType=dismax&bq=s_source:Getty^0.3";
		$format = "&fl=*+score&wt=json&sort=score+desc,s_priority+desc&defType=dismax&bq=s_source:Getty^0.4";
                
		$url = $url.$format;
		systemLog::message($url);
				
		curl_setopt($curl_handle, CURLOPT_URL, $url); 
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		$jsonResponse = curl_exec($curl_handle);		
                //systemLog::message($jsonResponse);
		curl_close($curl_handle);
		$this->setResponse(json_decode($jsonResponse));
		
	}
        /**
         * provides the music listing for a brief
         * 
         * 
         */
        function briefMusicSearch(){
     		$url = "";
                if ($this->getCatalog() == "mozayic" ){
                    $url = system::getConfig()->getParam('solr', 'mosaic', 'http://localhost:8080/solr/core4') . '/select/?indent=true&wt=json&start=' . $this->getStart() . '&rows=20';                    
                    //$url = "http://localhost:8080/solr/core4" . '/select/?wt=json&start=' . $this->getStart() . '&rows=20';;
                } else {
                    $url = $url = system::getConfig()->getParam('solr', 'momusic', 'http://localhost:8080/solr/core1') . '/select/?wt=json&start=' . $this->getStart() . '&rows=20';;
                }       
                $curl_handle = curl_init();
                $trackList = momusicBrandmusic::getInstance($this->getBrandMusicID())->getTrackList();                
                $trackList = preg_replace("/ /", "", $trackList);
                $trackList = preg_replace("/,/", "+s_id:", $trackList);                
                $url = $url . "&q=s_id:" . $trackList;                
		systemLog::message($url);				
		curl_setopt($curl_handle, CURLOPT_URL, $url); 
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		$jsonResponse = curl_exec($curl_handle);		
		curl_close($curl_handle);
		$this->setResponse(json_decode($jsonResponse));
            
        }
        
	
	/**
	 * Runs the facet search using the data
	 *
	 * @return momusicKeywordSearchResult
	 */
	function searchFacetQuery() {
		
     		$url = "";
                if ($this->getCatalog() == "mozayic" ){
                    //$url = "http://localhost:8080/solr/core4" . '/select/?indent=true&start=' . $this->getStart() . '&rows=20';;
                    $url = system::getConfig()->getParam('solr', 'mosaic', 'http://localhost:8080/solr/core4') . '/select/?indent=true&start=' . $this->getStart() . '&rows=20';                    
                } else {
                    $url = $url = system::getConfig()->getParam('solr', 'momusic', 'http://localhost:8080/solr/core1') . '/select/?indent=true&start=' . $this->getStart() . '&rows=20';;
                }
		
		$curl_handle = curl_init();
		
		if ( $this->getKeyword() && $this->getCategory() && $this->getFilter() ) {
                    
                        $keyword = preg_replace("/\+/","+AND+",trim($this->getKeyword()));                    
			$url = $url . '&q='.$keyword;
		}	
		
		$catArr = preg_split("/,/", urldecode($this->getCategory()));
		$filterArr = preg_split("/,/", urldecode($this->getFilter()));
		
		for ( $i = 0 ;$i<count($catArr); $i++) {
			$url .="&fq=".$catArr[$i].':'.$filterArr[$i];	
		}
		
		
		$url .= "&facet=true&facet.field=s_mood&facet.field=s_genre&facet.mincount=1";		
		//$format = "&fl=*+score&wt=json&sort=score+desc,s_priority+desc&defType=dismax&bq=s_source:Getty^0.3";
                $format = "&fl=*+score&wt=json&sort=score+desc,s_priority+desc&defType=dismax&bq=s_source:Getty^0.4&qf=text";
		$url = $url.$format;
		systemLog::message($url);
				
		curl_setopt($curl_handle, CURLOPT_URL, $url); 
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		$jsonResponse = curl_exec($curl_handle);		
		curl_close($curl_handle);
		$this->setResponse(json_decode($jsonResponse));
		
	}
	

	/**
	 * Runs the facet search using the data
	 *
	 * @return momusicKeywordSearchResult
	 */
	function searchFacetQueryMusicCategory() {
		
                $url = "";
                if ($this->getCatalog() == "mozayic" ){
//                    $url = "http://localhost:8080/solr/core4" . '/select/?indent=true&start=' . $this->getStart() . '&rows=20';;
                    $url = system::getConfig()->getParam('solr', 'mosaic', 'http://localhost:8080/solr/core4') . '/select/?indent=true&start=' . $this->getStart() . '&rows=20';                                        
                } else {
                    $url = $url = system::getConfig()->getParam('solr', 'momusic', 'http://localhost:8080/solr/core1') . '/select/?wt=json&start=' . $this->getStart() . '&rows=20';;
                }
   
		$curl_handle = curl_init();
						
		if ( $this->getArtist() ) {
			$url = $url . '&q='.$this->getArtist().'&qf=s_artist';
		} elseif ( $this->getGenre() ) {
			$url = $url . '&q=s_genre:'.$this->getGenre();
		} elseif ( $this->getMood() ) {
			$url = $url . '&q=s_mood:'.$this->getMood();
		} elseif ( $this->getInstrument() ) {
			$url = $url . '&q=s_instrument:'.$this->getInstrument();
		} elseif ( $this->getStyle() ) {
			$url = $url . '&q=s_style:'.$this->getStyle();
		} elseif ( $this->getTempo() ) {
			$url = $url . '&q=s_description:'.$this->getTempo();
		} else {
                    
                        $keyword = preg_replace("/\+/","+AND+",trim($this->getKeyword()));                    
			$url = $url . '&q='.$this->getKeyword().'&qf=text';
		}
		
		$catArr = preg_split("/,/", urldecode($this->getCategory()));
		$filterArr = preg_split("/,/", urldecode($this->getFilter()));
		
		for ( $i = 0 ;$i<count($catArr); $i++) {
			$url .="&fq=".$catArr[$i].':'.$filterArr[$i];	
		}
		
		//$url .="&fq=".$this->getCategory().':'.$this->getFilter();	
		
		$url .= "&facet=true&facet.field=s_mood&facet.field=s_genre&facet.mincount=1";		
		//$format = "&fl=*+score&wt=json&sort=score+desc,s_priority+desc&defType=dismax&bq=s_source:Getty^0.3";
                $format = "&fl=*+score&wt=json&sort=score+desc,s_priority+desc&defType=dismax&bq=s_source:Getty^0.4";
		$url = $url.$format;
		systemLog::message($url);
				
		curl_setopt($curl_handle, CURLOPT_URL, $url); 
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		$jsonResponse = curl_exec($curl_handle);		
		curl_close($curl_handle);
		$this->setResponse(json_decode($jsonResponse));
		
	}
	
	
	
	/**
	 *  gets the music search results
	 * 
	 * @return momusicSearchResult 
	 */
	function getResultList() {
		
		$oResponse = $this->getResponse();		
		$this->_TotalResult = $oResponse->response->numFound;
		$docs = $oResponse->response->docs; 		
		return new momusicSearchResult($docs, $this->getTotalResult(), $this);
		
	}
	
	/**
	 * Gets the facet list
	 * 
	 * 
	 */
	function getFacet() {
		$oResponse = $this->getResponse();
		$facet = $oResponse->facet_counts->facet_fields;
		$facetList = array();
		foreach ( $facet as $type => $inValue ) {
			//$facetList[] = $type;
			$facetItem = array();
			$k =0;
			for ($i = 0;$i<count($inValue); ) {
				$k = $i+1;
				$facetItem[$inValue[$i]] = $inValue[$k];
				$i = $i+2;
			}
			$facetList[$type] = $facetItem;
		}
		
		return $facetList;
	}

	/**
	 * Sets the response from the mofilmmusic-Audiosocket SSO API
	 * 
	 * @param JSON $inResponse
	 * @return JSON 
	 */
	function setResponse($inResponse) {
		if ( $inResponse !== $this->_Response) {
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
		if ( $inTotalResult !== $this->_TotalResult) {
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
		if ( $inStart !== $this->_Start) {
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
		if ( $inKeyword !== $this->_Keyword) {
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
	
	
	/**
	 * Set $_Artist to $inArtist
	 *
	 * @param string $inArtist
	 * @return string
	 */
	function setArtist($inArtist) {
		if ( $inArtist !== $this->_Artist ) {
			$this->_Artist = $inArtist;
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
	

	function setInstrument($inInst) {
		if ( $inInst !== $this->_Instrument ) {
			$this->_Instrument = $inInst;
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
	function setCategory($inCategory) {
		if ( $inCategory !== $this->_Category ) {
			$this->_Category = $inCategory;
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
	
        /**
	 * Set $_Catalog to $inCatalog
	 *
	 * @param string $inCatalog
	 * @return string
	 */
	function setCatalog($inCatalog) {
		if ( $inCatalog !== $this->_Catalog ) {
			$this->_Catalog = $inCatalog;
		}
		return $this;
	}

	/**
	 * Returns $_Catalog
	 *
	 * @return string
	 */
	function getCatalog() {
		return $this->_Catalog;
	}
        
	/**
	 * Set $_BrandMusicID to $inBrandMusicID
	 *
	 * @param int $inBrandMusicID
	 * 
	 */
	function setBrandMusiciD($inBrandMusicID) {
		if ( $inBrandMusicID !== $this->_BrandMusicID ) {
			$this->_BrandMusicID = $inBrandMusicID;
		}
		return $this;
	}

	/**
	 * Returns $_BrandMusicID
	 *
	 * @return int
	 */
	function getBrandMusicID() {
		return $this->_BrandMusicID;
	}
	
}	

	
