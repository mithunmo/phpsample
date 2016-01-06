<?php
/**
 * reportCentreReportSet Class
 * 
 * Stored in reportCentreReportSet.class.php
 *
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package reportCentre
 * @subpackage reportCentre
 * @category reportCentreReportSet
 * @version $Rev: 10 $
 */


/**
 * reportCentreReportSet Class
 * 
 * Holds a set of reports allowing them to be iterated.
 * 
 * @package reportCentre
 * @subpackage reportCentre
 * @category reportCentreReportSet
 */
class reportCentreReportSet implements IteratorAggregate {
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
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
	 * Stores $_Limit
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Limit;
	
	/**
	 * Stores $_Offset
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Offset;
	
	
	
	/**
	 * Returns a new reportCentreReportSet
	 *
	 * @param array $inResults
	 * @param integer $inTotalResults
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return reportCentreReportSet
	 */
	function __construct(array $inResults, $inTotalResults = 0, $inOffset = 0, $inLimit = 30) {
		$this->_Modified = false;
		$this->setResults($inResults);
		$this->setTotalResults($inTotalResults);
		$this->setOffset($inOffset);
		$this->setLimit($inLimit);
	}
	
	
	
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
	 * @return reportCentreReportSet
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
	 * @return reportCentreReportSet
	 */
	function setTotalResults($inTotalResults) {
		if ( $inTotalResults !== $this->_TotalResults ) {
			$this->_TotalResults = $inTotalResults;
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
	 * @return reportCentreReportSet
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
	 * @return reportCentreReportSet
	 */
	function setLimit($inLimit) {
		if ( $inLimit !== $this->_Limit ) {
			$this->_Limit = $inLimit;
			$this->setModified();
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
		$page = $this->getOffset()-$this->getLimit();
		return $page >= 0 ? $page : 0;
	}
	
	/**
	 * Return the offset to the next page, only if it is less than the total results
	 *
	 * @return integer
	 */
	function getNextPage() {
		$page = $this->getOffset()+$this->getLimit();
		return $page >= $this->getTotalResults() ? false : $page;
	}
	
	/**
	 * Return the offset to the last page of results
	 *
	 * @return integer
	 */
	function getLastPage() {
		if ( $this->getTotalResults() > 0 && $this->getLimit() > 0 ) {
			if ( $this->getLimit() == 1 ) {
				return (floor($this->getTotalResults()/$this->getLimit())*$this->getLimit())-1;
			} else {
				return floor($this->getTotalResults()/$this->getLimit())*$this->getLimit();
			}
		} else {
			return 0;
		}
	}
}