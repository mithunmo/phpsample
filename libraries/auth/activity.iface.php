<?php
/**
 * authActivityInterface
 * 
 * Stored in authActivity.iface.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage auth
 * @category authActivityInterface
 * @version $Rev: 650 $
 */


/**
 * authActivityInterface Class
 * 
 * Interface for an activity object used in the auth system.
 * 
 * @package scorpio
 * @subpackage auth
 * @category authActivityInterface
 */
interface authActivityInterface {
	
	/**
	 * Return the activity resource id
	 * 
	 * @return string
	 * @access public
	 */
	function getActivityID();
}