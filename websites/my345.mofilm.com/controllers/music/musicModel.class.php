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
class musicModel extends momusicWorks {
	
	
	
	
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
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicWorks';
		
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
			
				$this->getUserSearch()->setWhereType(baseSearch::WHERE_USING_OR);
				$this->_SearchResult = $this->getUserSearch()->search();
			
				//$this->getUserSearch()->setWhereType(baseSearch::WHERE_USING_AND);
				//$this->_SearchResult = $this->getUserSearch()->search();
				
				
		} else {
			$this->_SearchResult = $this->getUserSearch()->search();
		}	
		
		systemLog::message($this->_SearchResult);
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
	
	
}