<?php
/**
 * baseSet.class.php
 * 
 * System base class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage base
 * @category baseSet
 * @version $Rev: 650 $
 */


/**
 * baseSet
 * 
 * This is one of the most heavily used classes within the framework. baseSet is a wrapper
 * around an array with a set of methods to manipulate it. baseSet can be used directly
 * or extended and made into more complex objects. It is used extensively internally
 * whenever an array of objects needs to be moved around or maintained.
 * 
 * baseSet implements IteratorAggregate allowing it to be easily iterated via foreach.
 * 
 * @package scorpio
 * @subpackage base
 * @category baseSet
 */
class baseSet implements IteratorAggregate, Countable {
	
	/**
	 * Flag showing status of list
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified		= false;
	
	/**
	 * The set itself
	 *
	 * @var array
	 * @access private
	 */
	private $_Set				= array();
	
	
	
	/**
	 * Returns true if the list has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set modified to $status
	 *
	 * @param boolean $status
	 * @return baseSet 
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
		return $this;
	}
	
	/**
	 * Implementation of count for Countable interface
	 * 
	 * @return integer
	 */
	function count() {
		return $this->_itemCount();
	}
	
	/**
	 * Returns the item count
	 *
	 * @return integer
	 */
	function getCount() {
		return $this->_itemCount();
	}
	
	/**
	 * Returns item at index $key, or if null the whole list; returns false if item is not found
	 *
	 * @param mixed $inKey
	 * @return mixed
	 * @final 
	 */
	final protected function _getItem($inKey = null) {
		if ( $inKey === null ) {
			return $this->_Set;
		} else {
			if ( array_key_exists($inKey, $this->_Set) ) {
				return $this->_Set[$inKey];
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Add item(s) to the set
	 *
	 * @param mixed $inKey
	 * @param mixed $inItem
	 * @return baseSet
	 * @final 
	 */
	final protected function _setItem($inKey, $inItem = null) {
		if ( is_array($inKey) && $inItem === null ) {
			$this->_Set = $inKey;
			$this->setModified();
		} else {
			if ( isset($this->_Set[$inKey]) ) {
				if ( $this->_Set[$inKey] !== $inItem ) {
					$this->_Set[$inKey] = $inItem;
					$this->setModified();
				}
			} else {
				$this->_Set[$inKey] = $inItem;
				$this->setModified();
			}
		}
		return $this;
	}
	
	/**
	 * Adds the item to the Set if not already present
	 *
	 * @param mixed $inItem
	 * @return baseSet
	 * @final 
	 */
	final protected function _setValue($inItem) {
		if ( !$this->_itemValueInSet($inItem) ) {
			$this->_Set[] = $inItem;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Remove an item from the set
	 *
	 * @param mixed $inKey
	 * @return baseSet
	 * @final 
	 */
	final protected function _removeItem($inKey) {
		if ( isset($this->_Set[$inKey]) ) {
			unset($this->_Set[$inKey]);
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Searches set for an item with value of $item and removes it
	 *
	 * @param mixed $inItem
	 * @return baseSet
	 * @final 
	 */
	final protected function _removeItemWithValue($inItem) {
		$inKey = $this->_findItem($inItem);
		if ( $inKey !== false ) {
			$this->_removeItem($inKey);
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Clears all set values and resets set to an empty array
	 *
	 * @return baseSet
	 * @final 
	 */
	final protected function _resetSet() {
		if ( $this->_itemCount() > 0 ) {
			$cnt = count($this->_Set);
			for ( $i=0; $i<$cnt; $i++ ) {
				$this->_Set[$i] = null;
				unset($this->_Set[$i]);
			}
			$this->_Set = array();
			$this->setModified(false);
		}
		return $this;
	}
	
	/**
	 * Attempts to locate $inItem in the set and returns index if located
	 *
	 * @param mixed $inItem
	 * @return mixed
	 * @final 
	 */
	final protected function _findItem($inItem) {
		return array_search($inItem, $this->_Set, (is_object($inItem) ? true : null));
	}
	
	/**
	 * Returns true if $inKey already exists in set
	 *
	 * @param mixed $inKey
	 * @return boolean
	 */
	final protected function _itemKeyExists($inKey) {
		return array_key_exists($inKey, $this->_Set);
	}
	
	/**
	 * Returns true if $inItem is in the set
	 *
	 * @param mixed $inItem
	 * @return boolean
	 * @final 
	 */
	final protected function _itemValueInSet($inItem) {
		return in_array($inItem, $this->_Set, (is_object($inItem) ? true : null));
	}
	
	/**
	 * Returns the item count from the set
	 *
	 * @return integer
	 * @final 
	 */
	final protected function _itemCount() {
		return count($this->_Set);
	}
	
	/**
	 * Merges the supplied array into the current set
	 * Note: should only be used with sets of the same data, may cause strange results
	 *
	 * @param array $inArray
	 * @return baseSet
	 */
	final protected function _mergeSets(array $inArray) {
		$this->_Set = array_merge($this->_Set, $inArray);
		$this->setModified();
		return $this;
	}
	
	/**
	 * Reverses the data in the set maintaining any keys
	 *
	 * @return baseSet
	 */
	final protected function _reverseSet() {
		$this->_Set = array_reverse($this->_Set, true);
		$this->setModified();
		return $this;
	}
	
	/**
	 * Returns the array when iterating the object
	 *
	 * @return ArrayIterator
	 */
	function getIterator() {
		return new ArrayIterator($this->_Set);
	}
}