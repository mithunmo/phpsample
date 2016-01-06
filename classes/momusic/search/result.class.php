<?php
/**
 * momusicResultSet Class
 * 
 * Stored in momusicResultSet.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage base
 * @category momusicResultSet
 * @version $Rev: 650 $
 */


/**
 * momusicResultSet Class
 * 
 * A momusicResultSet is a wrapper around a resultSet. It takes a set of ID's and allows
 * them to be iterated and instantiated via a getInstance() method. This class needs to
 * be extended into the result set.
 * 
 * Alternatively, a pre-populated result set can be passed as the array with getInstance
 * simply retrieving the object.
 * 
 * This class includes methods for building next/previous navigation and handling pages.
 * It provides the basics for a quick deployment and is used in the wurflSearchResultSet.
 * 
 * @package scorpio
 * @subpackage base
 * @category momusicResultSet
 */
class momusicResultSet implements IteratorAggregate {
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	/**
	 * Stores $_SearchInterface
	 *
	 * @var baseSearchInterface
	 * @access protected
	 */
	protected $_SearchInterface;
	
	/**
	 * Array of results to iterate
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Results			= array();
	
	/**
	 * Current item
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_Current			= 0;
	
	/**
	 * Total results from query
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TotalResults	= 0;
	
	
	
	/**
	 * Returns a new resultObject
	 *
	 * @param array $inResults
	 * @param integer $inTotalResults
	 * @param baseSearchInterface $inSearch
	 * @return resultObject
	 */
	function __construct(array $inResults, $inTotalResults ,  $inSearch) {
		$this->_Modified = false;
		$this->setResults($inResults);
		$this->setTotalResults($inTotalResults);
		$this->setSearchInterface($inSearch);
		return $this;
	}
	
	
	
	/**
	 * Returns an instance of an object, using $keyId to create it
	 *
	 * @param integer $inKeyId
	 * @return object
	 * @abstract 
	 */
	// abstract function getInstance($inKeyId);
	
	
	/**
	 * Returns $_Modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set $_Modified to $inModified
	 *
	 * @param boolean $inModified
	 * @return momusicResultSet
	 */
	function setModified($inModified = true) {
		if ( $inModified !== $this->_Modified ) {
			$this->_Modified = $inModified;
		}
		return $this;
	}
	
	/**
	 * Returns an iterator for foreaching over the object
	 *
	 * @return ArrayIterator
	 */
	function getIterator() {
		return new ArrayIterator($this->_Results);
	}
	
	/**
	 * Set array of results
	 *
	 * @param array $inResults
	 */
	function setResults($inResults) {
		if ( is_array($inResults) ) {
			$this->_Results = $inResults;
		}
	}
	
	/**
	 * Return array of results
	 *
	 * @return array
	 */
	function getResults() {
		return $this->_Results;
	}
	
	/**
	 * Returns number of items in result set
	 *
	 * @return integer
	 */
	function getResultCount() {
		return count($this->_Results);
	}
	
	/**
	 * Returns true if we have a result set
	 *
	 * @return boolean
	 */
	function hasResults() {
		return (count($this->_Results) > 0) ? true : false;
	}
	
	/**
	 * Returns TotalResults
	 *
	 * @return integer
	 */
	function getTotalResults() {
		return $this->_TotalResults;
	}
	
	/**
	 * Set TotalResults property
	 *
	 * @param integer $inTotalResults
	 * @return momusicResultSet
	 */
	function setTotalResults($inTotalResults) {
		if ( $inTotalResults !== $this->_TotalResults ) {
			$this->_TotalResults = $inTotalResults;
		}
		return $this;
	}
	
	/**
	 * Return the offset to the first page of results
	 *
	 * @return integer
	 */
	function getFirstPage() {
		return 0;
	}
	
	/**
	 * Return the offset to the previous page of results
	 *
	 * @return integer
	 */
	function getPreviousPage() {
		$page = $this->getSearchInterface()->getOffset()-$this->getSearchInterface()->getLimit();
		return $page >= 0 ? $page : 0;
	}
	
	/**
	 * Return the offset to the next page, only if it is less than the total results
	 *
	 * @return integer
	 */
	function getNextPage() {
		$page = $this->getSearchInterface()->getOffset()+$this->getSearchInterface()->getLimit();
		return $page >= $this->getTotalResults() ? false : $page;
	}
	
	/**
	 * Return the offset to the last page of results
	 *
	 * @return integer
	 */
	function getLastPage() {
		if ( $this->getTotalResults() > 0 && $this->getSearchInterface()->getLimit() > 0 ) {
			if ( $this->getSearchInterface()->getLimit() == 1 ) {
				return (floor($this->getTotalResults()/$this->getSearchInterface()->getLimit())*$this->getSearchInterface()->getLimit())-1;
			} else {
				return floor($this->getTotalResults()/$this->getSearchInterface()->getLimit())*$this->getSearchInterface()->getLimit();
			}
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns $_SearchInterface
	 *
	 * @return baseSearchInterface
	 */
	function getSearchInterface() {
		return $this->_SearchInterface;
	}
	
	/**
	 * Set $_SearchInterface to $inSearchInterface
	 *
	 * @param baseSearchInterface $inSearchInterface
	 * @return momusicResultSet
	 */
	function setSearchInterface( $inSearchInterface) {
		if ( $inSearchInterface !== $this->_SearchInterface ) {
			$this->_SearchInterface = $inSearchInterface;
			$this->setModified();
		}
		return $this;
	}
}