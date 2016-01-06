<?php
/**
 * authActivitySet
 * 
 * Stored in authActivitySet.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage auth
 * @category authActivitySet
 * @version $Rev: 697 $
 */


/**
 * authActivitySet
 * 
 * Holds a set of activities that an authorising object has access to.
 *
 * @package scorpio
 * @subpackage auth
 * @category authActivitySet
 */
class authActivitySet extends baseSet implements authInterface {
	
	/**
	 * Returns new authActivitySet object
	 * 
	 * @return authActivitySet
	 */
	function __construct() {
		$this->reset();
	}

	/**
	 * Loads the set with activities, if the array contains strings, they are
	 * converted to authActivity objects
	 * 
	 * @param array $inArray
	 * @return boolean
	 * @throws authActivitySetException
	 */
	function loadFromArray(array $inArray = array()) {
		if ( count($inArray) > 0 ) {
			foreach ( $inArray as $oActivity ) {
				if ( !$oActivity instanceof authActivity ) {
					$oActivity = authActivity::factory($oActivity);
				}
				$this->addActivity($oActivity);
			}
			$this->setModified(false);
			return true;
		}
		return false;
	}

	/**
	 * Reset object to defaults
	 * 
	 * @return authActivitySet
	 */
	function reset() {
		$this->_resetSet();
		return $this;
	}
	
	
	
	/**
	 * Returns true if group has authority for this activity
	 *
	 * @param authActivity $inActivity
	 * @return boolean
	 */
	function isAuthorised($inActivity) {
		if ( !$inActivity instanceof authActivity ) {
			$inActivity = new authActivity($inActivity);
		}
		return is_object($this->_getItem($inActivity->getActivityID()));
	}
	
	/**
	 * Returns true if this groups contains $inActivity
	 * 
	 * @param mixed $inActivity
	 * @return boolean
	 */
	function hasActivity($inActivity) {
		if ( !$inActivity instanceof authActivity ) {
			$inActivity = new authActivity($inActivity);
		}
		return is_object($this->_getItem($inActivity->getActivityID()));
	}
	
	
	
	/**
	 * Returns true if modified of objects modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( !$modified && $this->_itemCount() > 0 ) {
			if ( false ) $oActivity = new authActivity();
			foreach ( $this as $oActivity ) {
				$modified = $modified || $oActivity->isModified();
			}
		}
		return $modified;
	}
	
	/**
	 * Returns activity with ID $inActivity
	 *
	 * @param string $inActivity
	 * @return authActivity
	 */
	function getActivity($inActivity = null) {
		return $this->_getItem($inActivity);
	}
	
	/**
	 * Add activity to set
	 *
	 * @param authActivity $inActivity
	 * @return authActivitySet
	 */
	function addActivity(authActivity $inActivity) {
		if ( $inActivity instanceof authActivity ) {
			return $this->_setItem($inActivity->getActivityID(), $inActivity);
		} else {
			throw new authActivitySetException("Expected activity to be instance of authActivity");
		}
	}
	
	/**
	 * Removes activity from set
	 *
	 * @param authActivity $inActivity
	 * @return authActivitySet
	 */
	function unsetActivity(authActivity $inActivity) {
		return $this->_removeItemWithValue($inActivity);
	}
}