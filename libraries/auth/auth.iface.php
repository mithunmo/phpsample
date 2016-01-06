<?php
/**
 * authInterface
 * 
 * Stored in authInterface.iface.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage auth
 * @category authInterface
 * @version $Rev: 650 $
 */


/**
 * authInterface Class
 * 
 * Interface for a class supporting authorisation checks.
 * 
 * @package scorpio
 * @subpackage auth
 * @category authInterface
 */
interface authInterface {
	
	/**
	 * Returns true if the object has $inActivity, false if not
	 * 
	 * $inActivity can be either a string or instance of {@link authActivity}
	 * 
	 * @param mixed $inActivity
	 * @return boolean
	 */
	function hasActivity($inActivity);
	
	/**
	 * Returns true if object is authorised for $inActivity, false if not
	 * 
	 * $inActivity can be either a string or instance of {@link authActivity}
	 * 
	 * @param mixed $inActivity
	 * @return boolean
	 */
	function isAuthorised($inActivity);
}