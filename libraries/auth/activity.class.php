<?php
/**
 * authActivity
 * 
 * Stored in authActivity.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage auth
 * @category authActivity
 * @version $Rev: 650 $
 */


/**
 * authActivity Class
 * 
 * Represents an activity that can be performed by an authorised user.
 * Includes a description to describe what the activity is for.
 * 
 * @package scorpio
 * @subpackage auth
 * @category authActivity
 */
class authActivity implements authActivityInterface {
	
	const ALLOW = 'Allow';
	const DENY = 'Deny';
	const GROUP = 'Group';
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
			
	/**
	 * Stores $_ActivityID
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_ActivityID;
	
	
	
	/**
	 * Creates a new activity
	 * 
	 * @param string $inActivityID
	 * @return authActivity
	 */
	function __construct($inActivityID = null) {
		if ( $inActivityID !== null ) {
			$this->setActivityID($inActivityID);
		}
	}
	
	/**
	 * Returns a new authActivity object
	 *
	 * @param string $inActivity
	 * @return authActivity
	 * @static
	 */
	public static function factory($inActivity) {
		return new authActivity($inActivity);
	}
	
	
	
	/**
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return authActivity
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Return value of $_ActivityID
	 * 
	 * @return string
	 * @access public
	 */
	function getActivityID() {
		return $this->_ActivityID;
	}
	
	/**
	 * Set the activity ID
	 * 
	 * @param string $inActivityID
	 * @return authActivity
	 * @access public
	 */
	function setActivityID($inActivityID) {
		if ( $inActivityID !== $this->_ActivityID ) {
			$this->_ActivityID = $inActivityID;
			$this->setModified();
		}
		return $this;
	}
}