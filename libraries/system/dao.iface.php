<?php
/**
 * systemDaoInterface.class.php
 * 
 * System DAO Interface
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemDaoInterface
 * @version $Rev: 650 $
 */


/**
 * systemDaoInterface
 * 
 * systemDaoInterface provides standard method for all data objects
 * 
 * @package scorpio
 * @subpackage system
 * @category systemDaoInterface
 */
interface systemDaoInterface {
 	
	/**
	 * Returns true if the object has been modified
	 *
	 * @return boolean
	 */
 	function isModified();
 	
 	/**
	 * Returns properties of object as an array
	 *
	 * @return array
	 */
	function toArray();
 	
 	/**
 	 * Load the object based on properties
 	 *
 	 * @return boolean
 	 */
 	function load();
 	
 	/**
 	 * Commits the object and any changes to the database
 	 *
 	 * @return boolean
 	 */
 	function save();
 	
 	/**
 	 * Deletes the object and any sub-objects
 	 *
 	 * @return boolean
 	 */
 	function delete();
 	
 	/**
 	 * Resets object properties to defaults
 	 *
 	 * @return systemDaoInterface
 	 */
 	function reset();
 	
 	/**
 	 * Returns the value of the primary key
 	 *
 	 * @return mixed
 	 */
 	#function getPrimaryKey();
}