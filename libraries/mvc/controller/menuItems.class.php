<?php
/**
 * mvcControllerMenuItems.class.php
 * 
 * mvcControllerMenuItems class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerMenuItems
 * @version $Rev: 650 $
 */


/**
 * mvcControllerMenuItems
 * 
 * Holds the controller menu item definitions
 *
 * Example:
 * <code>
 * $oMenuItems = new mvcControllerMenuItems();
 * 
 * $oItem = new mvcControllerMenuItem(self::ACTION_VIEW, 'View', self::ACTION_VIEW, 'View Messages');
 * $oItem->addItem(new mvcControllerMenuItem(self::ACTION_VIEW, 'Refresh', self::ACTION_VIEW, 'Refresh list'));
 * $oMenuItems->addItem($oItem);
 * 
 * $oItem2 = new mvcControllerMenuItem(self::ACTION_NEW, 'New', 'newMessage', 'Create a new message');
 * $oItem2->addItem(new mvcControllerMenuItem('/controlPanel/pms/sentItems', 'Sent Items', 'sentMessages', 'View sent messages', false, mvcControllerMenuItem::PATH_TYPE_URI));
 * $oMenuItems->addItem($oItem2);
 * </code>
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerMenuItems
 */
class mvcControllerMenuItems extends baseSet {	
	
	/**
	 * Returns new instance of mvcControllerMenuItems
	 *
	 * @return mvcControllerMenuItems
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
	 * Adds the menuItem to the list
	 *
	 * @param mvcControllerMenuItem $inItem
	 * @return mvcControllerMenuItems
	 */
	function addItem(mvcControllerMenuItem $inItem) {
		return $this->_setItem($inItem->getUriPath(), $inItem);
	}
	
	/**
	 * Returns the menuItem named $inItem
	 *
	 * @param string $inItem
	 * @return mvcControllerMenuItem
	 */
	function getItem($inItem = null) {
		return $this->_getItem($inItem);
	}
	
	/**
	 * Removes the action from allowed actions list
	 *
	 * @param mvcControllerMenuItem $inItem
	 * @return mvcControllerMenuItems
	 */
	function removeItem(mvcControllerMenuItem $inItem) {
		return $this->_removeItem($inItem->getUriPath());
	}
	
	/**
	 * Returns the number of actions that have been assigned
	 *
	 * @return integer
	 */
	function getItemCount() {
		return $this->_itemCount();
	}
}