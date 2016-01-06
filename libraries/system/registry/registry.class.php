<?php
/**
 * systemRegistry.class.php
 * 
 * System registry class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemRegistry
 * @version $Rev: 707 $
 */


/**
 * systemRegistry
 * 
 * Provides central object / variable store. The registry is a static object that 
 * can be used to store single instances of other objects. Notably it is used to
 * hold the autoload and main config objects for the framework.
 * 
 * The registry is instantiated via the {@link system} object, therefore further
 * instances are discouraged.
 * 
 * <code>
 * // create a registry and add 'myObject'
 * $oRegistry = new systemRegistry();
 * $oRegistry->set('myObject', new stdClass());
 * </code>
 * 
 * @package scorpio
 * @subpackage system
 * @category systemRegistry
 */
class systemRegistry extends baseSet {
	
	/**
	 * Store a new instance in the registry
	 *
	 * @param string $inKey
	 * @param mixed $inObject
	 * @return systemRegistry
	 */
	function set($inKey, $inObject) {
		return $this->_setItem($inKey, $inObject);
	}
	
	/**
	 * Removes an object from the registry and from memory
	 *
	 * @param string $inKey
	 * @return systemRegistry
	 */
	function remove($inKey) {
		return $this->_removeItem($inKey);
	}
	
	/**
	 * Returns a previously stored instance, throws exception if not found
	 *
	 * @param string $inKey
	 * @return mixed
	 * @throws systemRegistryInstanceNotFound
	 * @throws systemRegistryKeyWasNull
	 */
	function get($inKey) {
		if ( !is_null($inKey) ) {
			$oObject = $this->_getItem($inKey);
			
			if ( is_object($oObject) ) {
				return $oObject;
			} else {
				throw new systemRegistryInstanceNotFound($inKey);
			}
		} else {
			throw new systemRegistryKeyWasNull();
		}
	}
	
	/**
	 * Completely resets the registry, removing all objects
	 *
	 * @return systemRegistry
	 */
	function reset() {
		return $this->_resetSet();
	}
	
	/**
	 * Returns the count of instances in the registry
	 *
	 * @return integer
	 */
	function countInstances() {
		return parent::_itemCount();
	}
}