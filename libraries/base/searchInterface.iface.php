<?php
/**
 * baseSearchInterface
 * 
 * Stored in baseSearchInterface.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage base
 * @category baseSearchInterface
 * @version $Rev: 650 $
 */


/**
 * baseSearchInterface Class
 * 
 * Search Interface definition, defines what should be include in a search object
 * to make building search systems a little easier and more conformant. Should be
 * used via either {@link baseSearch} or as a standalone implmentation.
 * 
 * Calls to {@link baseSearchInterface::search()} expect a baseResultSet to be
 * returned.
 * 
 * @package scorpio
 * @subpackage base
 * @category baseSearchInterface
 */
interface baseSearchInterface {
	
	/**
	 * Resets the search fields to defaults
	 *
	 * @return baseSearchInterface
	 */
	function reset();
	
	/**
	 * Returns true if the search can be run, false otherwise
	 *
	 * @return boolean
	 */
	function canSearchRun();
	
	/**
	 * Runs the search using the class data, returning a resultSet
	 *
	 * @return baseResultSet
	 */
	function search();
	
	/**
	 * Builds the SELECT statement for the search query, query is passed by reference
	 *
	 * @param string &$inQuery
	 * @return void
	 */
	function buildSelect(&$inQuery);
	
	/**
	 * Builds the WHERE statement for the search query, query is passed by reference
	 *
	 * @param string &$inQuery
	 * @return void
	 */
	function buildWhere(&$inQuery);
	
	/**
	 * Builds the ORDER BY statement for the search query, query is passed by reference
	 *
	 * @param string &$inQuery
	 * @return void
	 */
	function buildOrderBy(&$inQuery);
	
	/**
	 * Builds the LIMIT statement for the search query, query is passed by reference
	 *
	 * @param string &$inQuery
	 * @return void
	 */
	function buildLimit(&$inQuery);
	
	/**
	 * Return the current offset
	 *
	 * @return integer
	 */
	function getOffset();
	
	/**
	 * Return the current limit
	 *
	 * @return integer
	 */
	function getLimit();
}