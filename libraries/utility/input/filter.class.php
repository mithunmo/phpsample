<?php
/**
 * utilityInputFilter Class
 * 
 * Stored in inputFilter.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityInputFilter
 * @version $Rev: 844 $
 */


/**
 * utilityInputFilter Class
 * 
 * This class controls all input filters and allows data filtering. This is
 * a wrapper around the PHP filter extension that was introduced with PHP 5.2.0
 * and is inspired by the ez Components filter system.
 * 
 * Each filter has a variety of options (flags) that can be specified. For a
 * complete list please see the PHP.net documentation for the filter extension.
 * 
 * Example:
 * <code>
 * $options = array();
 * $options['options']['min_range'] = 1; 
 * $options['options']['max_range'] = 10;
 * $options['flags'] = FILTER_FLAG_ALLOW_OCTAL;
 * 
 * $oFilter = utilityInputFilter::filterInt();
 * $oFilter->setOptions($options);
 * $oFilter->doFilter(3);
 * </code>
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityInputFilter
 */
class utilityInputFilter implements utilityFilterInterface {
	
	/*
	 * Constants for most available filters in PHP5
	 */
	const FILTER_INT = 'int'; 
	const FILTER_BOOLEAN = 'boolean'; 
	const FILTER_FLOAT = 'float'; 
	const FILTER_VALIDATE_REGEXP = 'validate_regexp'; 
	const FILTER_VALIDATE_URL = 'validate_url'; 
	const FILTER_VALIDATE_EMAIL = 'validate_email'; 
	const FILTER_VALIDATE_IP = 'validate_ip'; 
	const FILTER_STRING = 'string'; 
	const FILTER_STRIPPED = 'stripped'; 
	const FILTER_ENCODED = 'encoded'; 
	const FILTER_SPECIAL_CHARS = 'special_chars'; 
	const FILTER_UNSAFE_RAW = 'unsafe_raw'; 
	const FILTER_EMAIL = 'email'; 
	const FILTER_URL = 'url'; 
	const FILTER_NUMBER_INT = 'number_int'; 
	const FILTER_NUMBER_FLOAT = 'number_float'; 
	const FILTER_MAGIC_QUOTES = 'magic_quotes'; 
	const FILTER_CALLBACK = 'callback'; 

	/**
	 * Stores $_Filter
	 *
	 * @var integer
	 * @access protected
	 */ 
	protected $_Filter;
	/**
	 * Stores options for the filter
	 *
	 * @var integer
	 * @access protected
	 */ 
	protected $_Options;
	
	
	
	/**
	 * Returns new utilityInputFilter
	 *
	 * @return utilityInputFilter
	 */
	function __construct() {
		$this->_Filter = FILTER_VALIDATE_INT;
		$this->_Options = null;
	}
	
	
	
	/**
	 * Return a filter to check string
	 *
	 * @return utilityInputFilter
	 * @access public
	 * @static 
	 */
	public static function filterString() {
		$oObject = new utilityInputFilter();
		$oObject->setFilter(self::FILTER_STRING);
		return $oObject;
		
	}
	
	/**
	 * Return a filter to check string
	 *
	 * @return utilityInputFilter
	 * @access public
	 * @static 
	 */
	public static function filterUnsafeRaw() {
		$oObject = new utilityInputFilter();
		$oObject->setFilter(self::FILTER_UNSAFE_RAW);
		return $oObject;
	}
	 
	/**
	 * Return a filter to check string
	 *
	 * @return utilityInputFilter
	 * @access public
	 * @static 
	 */
	public static function filterUnsafeRawArray() {
		$oObject = new utilityInputFilter();
		$oObject->setFilter(self::FILTER_UNSAFE_RAW);
		$oObject->setOptions(array('flags' => FILTER_REQUIRE_ARRAY));
		return $oObject;
	}
	
	/**
	 * Return a filter to check string
	 *
	 * @return utilityInputFilter
	 * @access public
	 * @static 
	 */
	public static function filterStringArray() {
		$oObject = new utilityInputFilter();
		$oObject->setFilter(self::FILTER_STRING);
		$oObject->setOptions(array('flags' => FILTER_REQUIRE_ARRAY));
		return $oObject;
	}
	
	/**
	 * Return a filter to check int
	 *
	 * @return utilityInputFilter
	 * @access public
	 * @static 
	 */
	public static function filterInt() {
		$oObject = new utilityInputFilter();
		$oObject->setFilter(self::FILTER_INT);
		return $oObject;
	}
	
	/**
	 * Return a filter to check boolean
	 *
	 * @return utilityInputFilter
	 * @access public
	 * @static 
	 */
	public static function filterBoolean() {
		$oObject = new utilityInputFilter();
		$oObject->setFilter(self::FILTER_BOOLEAN);
		return $oObject;
	}
	
	/**
	 * Return a filter to check float
	 *
	 * @return utilityInputFilter
	 * @access public
	 * @static 
	 */
	public static function filterFloat() {
		$oObject = new utilityInputFilter();
		$oObject->setFilter(self::FILTER_FLOAT);
		return $oObject;
	}
	
	/**
	 * Return a filter to check validate_url
	 *
	 * @return utilityInputFilter
	 * @access public
	 * @static 
	 */
	public static function filterValidateUrl() {
		$oObject = new utilityInputFilter();
		$oObject->setFilter(self::FILTER_VALIDATE_URL);
		return $oObject;
	}
	
	/**
	 * Return a filter to check validate_email
	 *
	 * @return utilityInputFilter
	 * @access public
	 * @static 
	 */
	public static function filterValidateEmail() {
		$oObject = new utilityInputFilter();
		$oObject->setFilter(self::FILTER_VALIDATE_EMAIL);
		return $oObject;
	}
	
	/**
	 * Return a filter to check validate_ip
	 *
	 * @return utilityInputFilter
	 * @access public
	 * @static 
	 */
	public static function filterValidateIp() {
		$oObject = new utilityInputFilter();
		$oObject->setFilter(self::FILTER_VALIDATE_IP);
		return $oObject;
	}
	
	/**
	 * Return a filter to check number_int
	 *
	 * @return utilityInputFilter
	 * @access public
	 * @static 
	 */
	public static function filterNumberInt() {
		$oObject = new utilityInputFilter();
		$oObject->setFilter(self::FILTER_NUMBER_INT);
		return $oObject;
	}
	
	/**
	 * Return a filter to check number_float
	 *
	 * @return utilityInputFilter
	 * @access public
	 * @static 
	 */
	public static function filterNumberFloat() {
		$oObject = new utilityInputFilter();
		$oObject->setFilter(self::FILTER_NUMBER_FLOAT);
		return $oObject;
	}
		
	
	
	/**
	 * Filters supplied data and returns the filtered output
	 *
	 * @see http://ca.php.net/filter_var
	 * @param mixed $inData The data to filter
	 * @return mixed
	 * @access public
	 */
	public function doFilter($inData = null) {
		return filter_var($inData, filter_id($this->_Filter), $this->_Options);
	}
	
	/**
	 * Returns filtered data from the specified global, usually GET or POST
	 *
	 * @see http://ca.php.net/filter_input
	 * @param integer $inGroup INPUT_ constant
	 * @param string $inField Name of the field to filter
	 * @return mixed
	 */
	public function doGetFilter($inGroup, $inField) {
		return filter_input($inGroup, $inField, filter_id($this->_Filter), $this->_Options);
	}
	
	
	
	/**
	 * Returns options, null if none set
	 *
	 * @return array
	 */
	function getOptions() {
		return $this->_Options;
	}
	
	/**
	 * Set an array of options for the filter
	 * 
	 * <code>
	 * $options = array();
	 * $options['options']['min_range'] = 1; 
	 * $options['options']['max_range'] = 10;
	 * $options['flags'] = FILTER_FLAG_ALLOW_OCTAL;
	 * 
	 * $oFilter = utilityInputFilter::filterInt();
	 * $oFilter->setOptions($options);
	 * </code>
	 *
	 * @param array $inOptions
	 * @return utilityInputFilter
	 */
	function setOptions(array $inOptions){
		if ( $inOptions !== $this->_Options )	{
			$this->_Options = $inOptions;
		}
		return $this;
	}
	
	/**
	 * Returns current filter name
	 *
	 * @return string
	 */
	function getFilter() {
		return $this->_Filter;
	}
	
	/**
	 * Set new filter
	 *
	 * @param string $inFilter
	 * @return utilityInputFilter
	 */
	function setFilter($inFilter){
		if ( $inFilter !== $this->_Filter )	{
			$this->_Filter = $inFilter;
		}
		return $this;
	}
}