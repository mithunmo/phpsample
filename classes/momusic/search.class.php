<?php
/**
 * baseSolrSearch
 * 
 * Stored in baseSolrSearch.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mithun Mohan (c) 2007-2010
 * @package scorpio
 * @subpackage base
 * @category baseSolrSearch
 * @version $Rev: 650 $
 */


/**
 * baseSolrSearch Class
 * 
 * Provides the common functionality needed to create a search system. This class needs to be
 * extended to implement the query building. For an example see {@link wurflSearch}.
 * 
 * baseSolrSearch itself implements the {@link baseSolrSearchInterface} which defines an interface
 * for a search object. This is then used with the {@link baseResultSet} to have consistent
 * iterable result sets.
 * 
 * @package scorpio
 * @subpackage base
 * @category baseSolrSearch
 */
abstract class baseSolrSearch implements baseSearchInterface {
	
	const WHERE_USING_AND = 1;
	const WHERE_USING_OR = 2;
	
	const SEARCH_TEXT_LIKE = 1;
	const SEARCH_TEXT_MATCH = 2;
	const SEARCH_TEXT_EXACT = 3;
	const SEARCH_TEXT_MATCH_BOOLEAN = 4;
	
	const ORDER_ASC = 1;
	const ORDER_DESC = 2;
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	/**
	 * Stores $_Keywords
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Keywords;
	
	/**
	 * Stores $_OrderBy
	 *
	 * @var string
	 * @access protected
	 */
	protected $_OrderBy;
	
	/**
	 * Array of allowed orderBy fields
	 *
	 * @var array
	 * @access protected
	 */
	protected $_AllowedOrderBy;
	
	/**
	 * Stores $_OrderDirection
	 *
	 * @var string
	 * @access protected
	 */
	protected $_OrderDirection;
	
	/**
	 * Stores $_SearchTextType
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_SearchTextType;
	
	/**
	 * Stores $_WhereType
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_WhereType;
	
	/**
	 * Stores $_Offset
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Offset;
	
	/**
	 * Stores $_Limit
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Limit;
	
	
	
	/**
	 * Returns a new instance of baseSolrSearch
	 *
	 * @return baseSolrSearch
	 */
	function __construct() {
		$this->reset();
		$this->initialise();
	}
	
	
	
	/**
	 * Resets class to defaults
	 *
	 * @return baseSolrSearch
	 */
	function reset() {
		$this->_Keywords = false;
		$this->_AllowedOrderBy = array();
		$this->_OrderBy = null;
		$this->_OrderDirection = self::ORDER_DESC;
		$this->_SearchTextType = self::SEARCH_TEXT_MATCH;
		$this->_WhereType = self::WHERE_USING_AND;
		$this->_Offset = 0;
		$this->_Limit = 30;
		$this->_Modified = false;
		return $this;
	}
	
	/**
	 * Performs setup duties before the search runs; should be inherited to set order by groups
	 *
	 * @return void
	 * @abstract
	 */
	function initialise() {}
	
	/**
	 * Returns true if the search can be run based on the inputs set
	 *
	 * @return boolean
	 * @abstract
	 */
	function canSearchRun() {
		$return = true;
		
		return $return;
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
		}
	}
	
	/**
	 * Adds the limit to the query
	 *
	 * @param string $inQuery
	 */
	function buildLimit(&$inQuery) {
		$inQuery .= ' LIMIT '.$this->getOffset().','.$this->getLimit();
	}
	
	
	
	/**
	 * Get / set methods
	 */
	
	/**
	 * Returns $_Modified
	 *
	 * @return boolean
	 * @final
	 */
	final function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set $_Modified to $inModified
	 *
	 * @param boolean $inModified
	 * @return baseSolrSearch
	 * @final
	 */
	final function setModified($inModified = true) {
		if ( $inModified !== $this->_Modified ) {
			$this->_Modified = $inModified;
		}
		return $this;
	}
	
	/**
	 * Returns $_Keywords
	 *
	 * @return string
	 */
	function getKeywords() {
		return $this->_Keywords;
	}
	
	/**
	 * Set $_Keywords to $inKeywords
	 *
	 * @param string $inKeywords
	 * @return baseSolrSearch
	 */
	function setKeywords($inKeywords) {
		if ( $inKeywords !== $this->_Keywords ) {
			if ( strlen($inKeywords) < 3 || !$inKeywords ) {
				throw new systemException("Keywords must be longer than 3 characters and cannot be empty");
			}
			$this->_Keywords = $inKeywords;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_SearchTextType
	 *
	 * @return integer
	 */
	function getSearchTextType() {
		return $this->_SearchTextType;
	}
	
	/**
	 * Set $_SearchTextType to $inSearchTextType
	 *
	 * @param integer $inSearchTextType
	 * @return baseSolrSearch
	 */
	function setSearchTextType($inSearchTextType) {
		if ( $inSearchTextType !== $this->_SearchTextType ) {
			if ( !in_array($inSearchTextType, array(self::SEARCH_TEXT_EXACT, self::SEARCH_TEXT_LIKE, self::SEARCH_TEXT_MATCH, self::SEARCH_TEXT_MATCH_BOOLEAN)) ) {
				throw new systemException("The search text modifier must be one of: 1 - Like, 2 - Match, 3 - Exact or 4 - Match Boolean");
			}
			$this->_SearchTextType = $inSearchTextType;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_WhereType
	 *
	 * @return integer
	 */
	function getWhereType() {
		return $this->_WhereType;
	}
	
	/**
	 * Set $_WhereType to $inWhereType
	 *
	 * @param integer $inWhereType
	 * @return baseSolrSearch
	 */
	function setWhereType($inWhereType) {
		if ( $inWhereType !== $this->_WhereType ) {
			if ( !in_array($inWhereType, array(self::WHERE_USING_AND, self::WHERE_USING_OR)) ) {
				throw new systemException("The where type can be only 1 - AND, or 2 - OR");
			}
			$this->_WhereType = $inWhereType;
			$this->setModified();
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
	 * @return baseSolrSearch
	 */
	function setOrderBy($inOrderBy) {
		if ( $inOrderBy !== $this->_OrderBy ) {
			if ( !in_array($inOrderBy, $this->_AllowedOrderBy) ) {
				throw new systemException("You can only order by one of the following fields: ".implode(', ', $this->_AllowedOrderBy));
			}
			$this->_OrderBy = $inOrderBy;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Adds $inOrderBy to the list of allowed order by strings
	 *
	 * @param string $inOrderBy
	 * @return baseSolrSearch
	 */
	function addAllowedOrderBy($inOrderBy) {
		if ( strlen($inOrderBy) > 0 && !in_array($inOrderBy, $this->_AllowedOrderBy) ) {
			$this->_AllowedOrderBy[] = $inOrderBy;
		}
		return $this;
	}
	
	/**
	 * Removes $inOrderBy from the list of allowed order by strings
	 *
	 * @param string $inOrderBy
	 * @return baseSolrSearch
	 */
	function removeAllowedOrderBy($inOrderBy) {
		if ( strlen($inOrderBy) > 0 && in_array($inOrderBy, $this->_AllowedOrderBy) ) {
			$key = array_search($inOrderBy, $this->_AllowedOrderBy);
			unset($this->_AllowedOrderBy[$key]);
		}
		return $this;
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
	 * @return baseSolrSearch
	 */
	function setOrderDirection($inOrderDirection) {
		if ( $inOrderDirection !== $this->_OrderDirection ) {
			if ( !in_array($inOrderDirection, array(self::ORDER_ASC, self::ORDER_DESC)) ) {
				throw new systemException("Order direction can only be 1 for Ascending or 2 for descending");
			}
			$this->_OrderDirection = $inOrderDirection;
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
	 * Set $_Offset to $inOffset
	 *
	 * @param integer $inOffset
	 * @return baseSolrSearch
	 */
	function setOffset($inOffset) {
		if ( $inOffset !== $this->_Offset ) {
			$this->_Offset = $inOffset;
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
	
	/**
	 * Set $_Limit to $inLimit
	 *
	 * @param integer $inLimit
	 * @return baseSolrSearch
	 */
	function setLimit($inLimit) {
		if ( $inLimit !== $this->_Limit ) {
			$this->_Limit = $inLimit;
			$this->setModified();
		}
		return $this;
	}
}