<?php
/**
 * baseOptionsSet
 * 
 * Stored in optionsSet.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage base
 * @category baseOptionsSet
 * @version $Rev: 650 $
 */


/**
 * baseOptionsSet
 * 
 * A generic holder for a set of options built on top of baseSet. Allows for
 * objects to implement an options system easily and quickly. Options are an
 * associative array of option.name => value pairs. Options can be set via
 * either the constructor at creation, or later via setOptions.
 * 
 * <code>
 * $oOptions = new baseOptionsSet(
 *     array(
 *         'option1' => true,
 *         'option.2' => 'a string',
 *         'some.other.value' => 123456,
 *     )
 * );
 * 
 * $val = $oOptions->getOptions('option1');
 * echo $val; // outputs 1 (true)
 * </code>
 * 
 * @package scorpio
 * @subpackage base
 * @category baseOptionsSet
 */
class baseOptionsSet extends baseSet {
	
	/**
	 * Creates a new options set
	 *
	 * @param array $inOptions (optional) Associative array of options
	 * @return baseOptionsSet
	 */
	function __construct(array $inOptions = array()) {
		$this->reset();
		if ( count($inOptions) > 0 ) {
			$this->setOptions($inOptions);
		}
	}
	
	/**
	 * Reset object
	 *
	 * @return void
	 */
	function reset() {
		parent::_resetSet();
	}
	
	
	
	/**
	 * Returns options or a specific option, null if not found 
	 *
	 * @param string $inOption (optional) The option to get, null for all
	 * @param mixed $inDefault (optional) Default value returned if $inOption is not set
	 * @return mixed
	 */
	function getOptions($inOption = null, $inDefault = null) {
		if ( $inOption === null ) {
			return parent::_getItem();
		}
		
		if ( parent::_itemKeyExists($inOption) ) {
			return $this->_getItem($inOption);
		}
		
		if ( $inDefault !== null ) {
			return $inDefault;
		} else {
			return null;
		}
	}
	
	/**
	 * Set options to $inOptions
	 * 
	 * $inOptions should be an array containing the key value pairs of options.
	 *
	 * @param array $inOptions
	 * @return baseOptionsSet
	 */
	function setOptions(array $inOptions = array()) {
		if ( count($inOptions) > 0 ) {
			foreach ( $inOptions as $key => $option ) {
				$this->_setItem($key, $option);
			}
		}
		return $this;
	}
	
	/**
	 * Removes specified options from set
	 * 
	 * $inOptions should be an array containing the keys only
	 *
	 * @param array $inOptions
	 * @return baseOptionsSet
	 */
	function removeOptions(array $inOptions = array()) {
		if ( count($inOptions) > 0 ) {
			foreach ( $inOptions as $option ) {
				$this->_removeItem($option);
			}
		}
		return $this;
	}
}