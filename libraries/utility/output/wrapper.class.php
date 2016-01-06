<?php
/**
 * utilityOutputWrapper Class
 * 
 * Stored in outputWrapper.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityOutputWrapper
 * @version $Rev: 842 $
 */


/**
 * utilityOutputWrapper Class
 * 
 * utilityOutputWrapper is used for pushing objects to a template layer. It prevents access to
 * methods that could change the state of the object e.g. saving or setting new data, meaning
 * that your template layer is "safe" to open up to external developers. The wrapping extends
 * to sub-objects or arrays. This means that any methods that return new objects or arrays
 * will themselves be wrapped by output wrapper.
 * 
 * Attempts to call barred methods results in an exception being thrown.
 * 
 * Wrapped objects have an __toString implementation that will display the permitted methods
 * if the object is used as a string. This is useful for debugging when you are not sure
 * what is available to be called, or if you just want to see the methods on the object.
 * 
 * Additional methods are added for certain objects. These pre-defined methods include:
 * <ul>
 *   <li>__toString</li>
 *   <li>toString</li>
 *   <li>format</li>
 *   <li>arrayKeyExists</li>
 *   <li>getArrayCount</li>
 *   <li>getArrayValue</li>
 *   <li>getSeedClassName</li>
 *   <li>IteratorAggregate</li>
 *   <li>Foreach</li>
 * </ul>
 * 
 * An additional, un-documented, method is provided for cases where you really need the
 * original object. getSeed will return the original object un-wrapped.
 * 
 * Note: if you use instanceof or {@link http://ca.php.net/is_a is_a()} you will get the
 * output wrapper class back - not the original class.
 * 
 * <code>
 * // example
 * $var = utilityOutputWrapper::wrap(new stdClass());
 * 
 * // wrap existing object with exceptions
 * $wrapped = utilityOutputWrapper::wrap(
 *     $existingObject,
 *     array('allowThis', 'allowThat'), // allowed methods
 *     array('blockThis','blockThat')   // blocked methods
 * );
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityOutputWrapper
 */
class utilityOutputWrapper implements IteratorAggregate  {
	
	/**
	 * Stores $_Seed
	 *
	 * @var mixed
	 */ 
	protected $_Seed;
	
	/**
	 * Stores $_AllowRecursiveObjects
	 *
	 * @var bool
	 */ 
	protected $_AllowRecursiveObjects = true;
	
	/**
	 * Stores $_AllowedFunctions
	 *
	 * @var array
	 */ 
	protected $_AllowedFunctions;
	
	
	/**
	 * Returns new utilityOutputWrapper wrapping $inSeed.
	 * 
	 * Optionally for objects an array of methods can be specified that are
	 * always allowed, or that are always ignored. By default get*, is*, has*
	 * allow*, can* and count* methods are allowed.
	 *
	 * @param mixed $inSeed
	 * @param array $inAlwaysAllow
	 * @param array $inAlwaysIgnore
	 */
	function __construct($inSeed, $inAlwaysAllow = null, $inAlwaysIgnore = null) {
		$this->_Seed = $inSeed;
		$this->buildAllowedFunctions();
		if ($inAlwaysAllow != null && is_array($inAlwaysAllow)) {
			$this->_AllowedFunctions = array_merge($this->_AllowedFunctions, $inAlwaysAllow);
			$this->_AllowedFunctions = array_unique($this->_AllowedFunctions);
		}
		if ($inAlwaysIgnore != null && is_array($inAlwaysIgnore)) {
			$this->_AllowedFunctions = array_diff($this->_AllowedFunctions, $inAlwaysIgnore);
		}
	}
	
	/**
	 * Function will wrap the item only allowing allowed commands to be executed
	 *
	 * @param mixed $inItem
	 * @param array $inAlwaysAllow
	 * @param array $inAlwaysIgnore
	 * @return utilityOutputWrapper
	 */
	static function wrap($inItem, $inAlwaysAllow = null, $inAlwaysIgnore = null) {
		if ( is_array($inItem) || is_object($inItem) ) {
			return new utilityOutputWrapper($inItem, $inAlwaysAllow, $inAlwaysIgnore);	
		} else {
			return $inItem;
		}
	}
	
	
	/**
	 * Overloaded call to intercept object calls
	 *
	 * @param string $function
	 * @param array $args
	 * @return utilityOutputWrapper
	 */
	public function __call($function, $args) {
		if ( $this->allowFunction($function) ) {
			if ( is_object($this->_Seed) ) {
				$value = call_user_func_array(array(&$this->_Seed, $function), $args);
				return self::wrap($value);
			} elseif ( is_array($this->_Seed) ) {
				if ( $function == 'getArrayCount' ) {
					$value = count($this->_Seed);
					return self::wrap($value);
				} elseif ( $function == 'arrayKeyExists' ) {
					$value = array_key_exists($args[0] ,$this->_Seed);
					return self::wrap($value);
				} elseif ( $function == 'getArrayValue' ) {
					$value = (isset($this->_Seed[$args[0]]) ? $this->_Seed[$args[0]] : false);
					return self::wrap($value);
				}
				$match = array();
				if ( preg_match('/^getArrayValue(.*)/', $function, $match) ) {
					$value = $this->_Seed[$match[1]];
					return self::wrap($value);
				}
			}
		} else {
			throw new systemException("Function $function not allowed to be called on ".get_class($this->_Seed)." ");
		}
		return false;
	}
	
	/**
	 * Checks if this object is alled to call a function 
	 *
	 * @param string $inHasFunction
	 * @return string
	 * @access public
	 */
	public function allowFunction($inHasFunction) {
		return in_array($inHasFunction, $this->_AllowedFunctions);
	}
	
	/**
	 * Returns name of seed class
	 *
	 * @return string
	 */
	function getSeedClassName() {
		return get_class($this->_Seed);
	}
	
	/**
	 * Builds the array of allowed methods from the seed object
	 *
	 * @return void
	 */
	public function buildAllowedFunctions() {
		$allowed = array('getSeedClassName', 'toString', '__toString');
		if ( is_array($this->_Seed) ) {
			$allowed[] = "Foreach";
			$allowed[] = "getArrayCount";
			$allowed[] = "getArrayValue";
			$allowed[] = "arrayKeyExists";
		}
		if ( $this->_Seed instanceof IteratorAggregate ) {
			$allowed[] = "IteratorAggregate";
		} 
		if ( is_object($this->_Seed) ) {
			$methods = get_class_methods($this->_Seed);
			foreach ( $methods as $method ) {
				if ( preg_match('/^get.*/', $method) ) {
					$allowed[] = $method;
				}
				if ( preg_match('/^is.*/', $method) ) {
					$allowed[] = $method;
				}
				if ( preg_match('/^allow.*/', $method) ) {
					$allowed[] = $method;
				}
				if ( preg_match('/^has.*/', $method) ) {
					$allowed[] = $method;
				}
				if ( preg_match('/^count.*/', $method) ) {
					$allowed[] = $method;
				}
				if ( preg_match('/^can.*/', $method) ) {
					$allowed[] = $method;
				}
				if ( 'format' == $method ) {
					$allowed[] = $method;
				}
			}
		}
		$this->_AllowedFunctions = $allowed;
	}
	
	/**
	 * Returns an iterator of the current seed object 
	 * 
	 * @return utilityOutputWrapper
	 * @access public
	 */
	public function getIterator() {
		if ( $this->_Seed instanceof IteratorAggregate ) {
			return new utilityOutputWrapperIterator($this->_Seed->getIterator());
		} elseif ( is_array($this->_Seed) ) {
			return new utilityOutputWrapperArray($this->_Seed);
		}
		return false;
	}
	
	/**
	 * Returns true if seed can be used in a foreach loop
	 *
	 * @return boolean
	 */
	public function isForeachAble() {
		if ( $this->_Seed instanceof IteratorAggregate || $this->_Seed instanceof Iterator ) {
			return true;
		} elseif ( is_array($this->_Seed) ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Converts object to string representation showing allowed methods
	 *
	 * @return string
	 */
	public function __toString() {
		return implode(" \n", $this->_AllowedFunctions);
	}
	
	
	/**
	 * Return current seed object
	 *
	 * @return mixed
	 */
	function getSeed() {
		return $this->_Seed;
	}
	
	/**
	 * Set new seed object
	 *
	 * @param mixed $inSeed
	 * @return utilityOutputWrapper
	 */
	function setSeed($inSeed){
		if ($this->_Seed !== $inSeed) {
			$this->_Seed = $inSeed;
		}
		return $this;
	}
	
	/**
	 * Returns true if objects can be recursed
	 *
	 * @return boolean
	 */
	function getAllowRecursiveObjects() {
		return $this->_AllowRecursiveObjects;
	}
	
	/**
	 * Set if objects can be recursed
	 *
	 * @param boolean $inAllowRecursiveObjects
	 * @return utilityOutputWrapper
	 */
	function setAllowRecursiveObjects($inAllowRecursiveObjects) {
		if ( $this->_AllowRecursiveObjects !== $inAllowRecursiveObjects ) {
			$this->_AllowRecursiveObjects = $inAllowRecursiveObjects;
		}
		return $this;
	}
	
	/**
	 * Returns array of allowed methods
	 *
	 * @return array
	 */
	function getAllowedFunctions() {
		return $this->_AllowedFunctions;
	}
}