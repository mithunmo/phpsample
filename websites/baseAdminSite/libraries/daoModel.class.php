<?php
/**
 * mvcDaoModelInterface.class.php
 * 
 * mvcDaoModelInterface class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage websites_baseAdminSite_libraries
 * @category mvcDaoModelInterface
 */


/**
 * mvcDaoModelInterface
 * 
 * DAO model interface
 *
 * @package scorpio
 * @subpackage websites_baseAdminSite_libraries
 * @category mvcDaoModelInterface
 */
interface mvcDaoModelInterface {
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30);
	
	/**
	 * Returns the object primary key value
	 *
	 * @return string
	 */
	function getPrimaryKey();
	
	/**
	 * Returns a new blank object
	 *
	 * @return systemDaoInterface
	 */
	function getNewObject();
	
	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey);
}