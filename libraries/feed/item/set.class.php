<?php
/**
 * feedItemSet
 * 
 * Stored in feedItemSet
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage feed
 * @category feedItemSet
 * @version $Rev: 707 $
 */


/**
 * feedItemSet
 * 
 * Stores a set of links from a feed.
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedItemSet
 */
class feedItemSet extends baseSet {
	
	/**
	 * Returns true if object or sub-objects have been modified
	 * 
	 * @see baseSet::isModified()
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oItem ) {
				$modified = $oItem->isModified() || $modified;
			}
		}
		return $modified;
	}
	
	/**
	 * Add a feedItem to the set
	 * 
	 * @param feedItem $inItem
	 * @return feedItemSet
	 */
	function addItem(feedItem $inItem) {
		return $this->_setValue($inItem);
	}
	
	/**
	 * Returns the item at position $inKey in the set
	 * 
	 * @param integer $inKey
	 * @return false|feedItem
	 */
	function getItemByKey($inKey) {
		return $this->_getItem($inKey);
	}
	
	/**
	 * Removes $inItem from the set
	 * 
	 * @param feedItem $inItem
	 * @return feedItemSet
	 */
	function removeItem(feedItem $inItem) {
		return $this->_removeItemWithValue($inItem);
	}
	
	/**
	 * Removes all items from set
	 * 
	 * @return feedItemSet
	 */
	function clear() {
		return $this->_resetSet();
	}
}