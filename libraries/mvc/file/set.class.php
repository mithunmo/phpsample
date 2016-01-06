<?php
/**
 * mvcFileSet.class.php
 * 
 * mvcFileSet class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileSet
 * @version $Rev: 707 $
 */


/**
 * mvcFileSet class
 * 
 * Contains a set of uploaded file objects.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileSet
 */
class mvcFileSet extends baseSet {
	
	/**
	 * Creates a new mvcFileSet
	 *
	 * @return mvcFileSet
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		parent::_resetSet();
	}
	
	/**
	 * Adds a file to the set
	 *
	 * @param mvcFileObject $inFile
	 * @return mvcFileSet
	 */
	function setFile(mvcFileObject $inFile) {
		return $this->_setValue($inFile);
	}
	
	/**
	 * Returns the file object at position $inIndex, or if null all files
	 *
	 * @param integer $inIndex
	 * @return mixed
	 */
	function getFile($inIndex = null) {
		return $this->_getItem($inIndex);
	}
	
	/**
	 * Returns first file object
	 *
	 * @return mvcFileObject
	 */
	function getFirst() {
		return $this->getFile(0);
	}
	
	/**
	 * Returns last element in set
	 *
	 * @return mvcFileObject
	 */
	function getLast() {
		return $this->getFile($this->getCount()-1);
	}
	
	/**
	 * Removes the file from the set
	 *
	 * @param mvcFileObject $inFile
	 * @return mvcFileSet
	 */
	function removeFile(mvcFileObject $inFile) {
		return $this->_removeItemWithValue($inFile);
	}
}