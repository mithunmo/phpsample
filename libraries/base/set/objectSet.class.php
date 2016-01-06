<?php
/**
 * baseObjectSet.class.php
 * 
 * System baseObjectSet class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage base
 * @category baseObjectSet
 * @version $Rev: 650 $
 */


/**
 * baseObjectSet
 * 
 * baseObjectSet is designed for manipulating sets of objects that all conform to the
 * {@link systemDaoInterface}. This super class can then save, load, delete add objects.
 * It requires extending and implementing to handle a specific set of objects e.g. a
 * set of products, or a set of users.
 * 
 * In theory you can use it with any systemDaoInterface based object, but in practice
 * this is not really a good idea.
 * 
 * While the class is not "abstract" per se, it does require a concrete {@link baseObjectSet::load()}
 * method.
 * 
 * If you require finer control of your set, it is recommended to inherit directly from
 * {@link baseSet} and implement your own systemDaoInterface into that set.
 * 
 * @package scorpio
 * @subpackage base
 * @category baseObjectSet
 */
class baseObjectSet extends baseSet implements systemDaoInterface {
 	
	/**
	 * Requires an implementation
	 *
	 * @abstract 
	 */
 	function load() {
 		throw new systemException('baseObjectSet requires a load implementation');
 	}
 	
 	/**
 	 * Save all changes to objects to the database
 	 *
 	 * @return boolean
 	 */
 	function save() {
 		$return = true;
 		foreach ( $this as $oObject ) {
 			$return = $oObject->save() && $return;
 		}
 		$this->setModified(false);
 		return $return;
 	}
 	
 	/**
 	 * Deletes all objects in set
 	 *
 	 * @return boolean
 	 */
 	function delete() {
 		$return = true;
 		foreach ( $this as $oObject ) {
 			$return = $oObject->delete() && $return;
 		}
 		$this->reset();
 		$this->setModified(false);
 		return $return;
 	}
 	
 	/**
 	 * Clears set of objects
 	 *
 	 * @return baseObjectSet
 	 */
 	function reset() {
 		return parent::_resetSet();
 	}
 	
 	/**
 	 * Returns class properties
 	 *
 	 * @return array
 	 */
 	function toArray() {
 		return get_class_vars($this);
 	}
 	
 	
 	
 	/**
 	 * Returns the number of objects in the set
 	 *
 	 * @return integer
 	 */
 	function getObjectCount() {
 		return parent::_itemCount();
 	}
 	
 	/**
 	 * Returns the object
 	 *
 	 * @param mixed $inObject
 	 * @return systemDaoInterface object
 	 */
 	function getObject($inObject) {
 		return parent::_getItem($inObject);
 	}
 	
 	/**
 	 * Add an object to the set, must implement the systemDaoInterface
 	 *
 	 * @param systemDaoInterface $inObject
 	 * @return baseObjectSet
 	 */
 	function setObject(systemDaoInterface $inObject) {
 		return parent::_setValue($inObject);
 	}
 	
 	/**
 	 * Remove a single object from the set
 	 *
 	 * @param mixed $inObject
 	 * @return baseObjectSet
 	 */
 	function removeObject($inObject) {
 		return parent::_removeItemWithValue($inObject);
 	}
}