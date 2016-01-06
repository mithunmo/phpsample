<?php
/**
 * mvcControllerViews.class.php
 * 
 * mvcControllerViews class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerViews
 * @version $Rev: 650 $
 */


/**
 * mvcControllerViews
 * 
 * Stores "views" that can be used "standalone" via the Smarty includeView
 * function or via the {@link mvcViewBase::getControllerView()} method.
 * A standalone view is a snapshot from the controller that can be included
 * inside other views. This class contains all the views that are allowed to
 * be called standalone on a controller.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerViews
 */
class mvcControllerViews extends baseSet {
	
	/**
	 * Returns new instance of mvcControllerViews
	 *
	 * @return mvcControllerViews
	 */
	function __construct() {
		$this->reset();
	}
	
	
	
	/**
	 * Resets object to defaults
	 *
	 * @return void
	 */
	function reset() {
		$this->_resetSet();
	}
	
	/**
	 * Adds the view to the allowed views list
	 *
	 * @param string $inView
	 * @return mvcControllerViews
	 */
	function addView($inView) {
		return $this->_setValue($inView);
	}
	
	/**
	 * Removes the view from allowed views list
	 *
	 * @param string $inView
	 * @return mvcControllerViews
	 */
	function removeView($inView) {
		return $this->_removeItemWithValue($inView);
	}
	
	/**
	 * Returns true if $inView is a valid view
	 *
	 * @param string $inView
	 * @return boolean
	 */
	function isValidView($inView) {
		return $this->_itemValueInSet($inView);
	}
	
	/**
	 * Returns the number of views that have been assigned
	 *
	 * @return integer
	 */
	function getViewCount() {
		return $this->_itemCount();
	}
	
	/**
	 * Returns all allowed views
	 *
	 * @return array
	 */
	function getAllowedViews() {
		return $this->_getItem();
	}
}