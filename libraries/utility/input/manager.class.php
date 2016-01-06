<?php
/**
 * utilityInputManager Class
 * 
 * Stored in inputManager.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityInputManager
 * @version $Rev: 844 $
 */


/**
 * utilityInputManager Class
 * 
 * The input manager aggregates a collection of {@link utilityInputFilter} instances
 * into a single interface. It is used to filter GET and POST data and returns an
 * array of filtered data. It relies on the PHP {@link http://ca.php.net/filter filter}
 * extension that is included by default in PHP >5.2.0.
 * 
 * The input manager can access data from GET, POST and data that is passed into the
 * filtering method. The data source is set by calling {@link utilityInputManager::setLookupGlobals()}.
 * For convenience, the various data types are available as class constants.
 * 
 * The filters can accept any flags as supported by the PHP filter extension.
 * 
 * The filtered data will always contain array keys matching the registered filters
 * regardless of whether there was any data to filter.
 * 
 * Please note that in Scorpio data filtering is a separate step to data validation.
 * While some of the filters can validate, the results should not be relied on. See
 * {@link utilityValidator} for the validation solution.
 * 
 * Examples to filter $_GET data
 * <code>
 * $oManger = new utilityInputManager(utilityInputManager::LOOKUPGLOBALS_GET);
 * $oManger->addFilter("goto", utilityInputFilter::filterInt());
 * $filteredData = $oManger->doFilter();
 * </code>
 * 
 * Example to filter arbitrary data
 * <code>
 * $testData = array(
 * 		'integer' => 123,
 * 		'integerFalse' => 'adsf',
 * 		'string' => 'asdf',
 * 		'ip' => '192.168.0.212',
 * 		'ipFalse' => '125.126.2.266',
 * 		'nothing' => 'nothing',
 * );
 * $oManger = new utilityInputManager(utilityInputManager::LOOKUPGLOBALS_DATA);
 * $oManger->addFilter("integer", utilityInputFilter::filterInt());
 * $oManger->addFilter("integerFalse", utilityInputFilter::filterInt());
 * $oManger->addFilter("email", utilityInputFilter::filterValidateEmail());
 * $oManger->addFilter("emailFalse", utilityInputFilter::filterValidateEmail());
 * $oManger->addFilter("ip", utilityInputFilter::filterValidateIp());
 * $oManger->addFilter("ipFalse", utilityInputFilter::filterValidateIp());
 * $oManger->setData($testData);
 * $filteredData = $oManger->doFilter();
 * </code>
 *
 * If you are filtering data that has come from an XML encoded string, you can optionally
 * set the property {@link utilityInputManager->setFilterAsXml()} to run the post-filtered
 * data through the {@link utilityStringFunction::xmlStringToUtf8()} to return the original
 * character instead of the XML encoded entity. Note: this may revert some standard entities
 * e.g. &gt; &lt;.
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityInputManager
 */
class utilityInputManager implements utilityFilterInterface {

	/**
	 * Alias of LOOKUPGLOBALS_DATA
	 */
	const DATA_SOURCE_ARRAY = 0;
	/**
	 * Alias of LOOKUPGLOBALS_GET
	 */
	const DATA_SOURCE_GET = 1;
	/**
	 * Alias of LOOKUPGLOBALS_POST
	 */
	const DATA_SOURCE_POST = 2;
	/**
	 * Alias of LOOKUPGLOBALS_COOKIE
	 */
	const DATA_SOURCE_COOKIE = 3;

	/**
	 * Set the data source to be a defined array outside of the PHP
	 * super global arrays
	 *
	 * @var integer
	 */
	const LOOKUPGLOBALS_DATA = 0;
	/**
	 * Set the data source as the _GET super global array
	 *
	 * @var integer
	 */
	const LOOKUPGLOBALS_GET = 1;
	/**
	 * Set the data source as the _POST super global array
	 *
	 * @var integer
	 */
	const LOOKUPGLOBALS_POST = 2;
	/**
	 * Set the data source as the _COOKIE super global array
	 *
	 * @var integer
	 */
	const LOOKUPGLOBALS_COOKIE = 3;
	
	/**
	 * Stores $_LookupGlobals
	 *
	 * @var integer
	 * @access protected
	 */ 
	protected $_LookupGlobals;

	/**
	* Stores an array of filter objects indexed by key
	* 
	* @var array(utilityInputFilter)
	* @access protected
	*/ 
	protected $_Filters = array();

	/**
	 * Stores $_Data
	 *
	 * @var array
	 * @access protected
	 */ 
	protected $_Data;

	/**
	 * Stores $_FilterAsXml
	 *
	 * @var boolean
	 * @access protected
	 */ 
	protected $_FilterAsXml;
	
	
	
	/**
	 * Returns new utilityInputManager
	 *
	 * @param integer $inLookupGlobals (defaults to POST)
	 * @return utilityInputManager
	 */
	function __construct($inLookupGlobals = self::LOOKUPGLOBALS_POST) {
		$this->_LookupGlobals = $inLookupGlobals;
		$this->_FilterAsXml = false;
	}
	
	
	
	/**
	 * Returns data if current request is for LOOKUPGLOBALS_DATA
	 *
	 * @return array
	 * @access private
	 */
	private function getLookupArray() {
		if ($this->_LookupGlobals == self::LOOKUPGLOBALS_DATA) {
			return $this->_Data;
		}
		return array();
	}
	
	/**
	 * filter filters the lookup globals or data
	 *
	 * @param mixed $inData Array of data to filter
	 * @return mixed
	 * @access public
	 */
	public function doFilter($inData = null) {
		if ( $inData != null ) {
			$this->_Data = $inData;
		}
		$filteredData = array();
		
		if ( $this->_LookupGlobals == self::LOOKUPGLOBALS_DATA ) {
			if ( is_array($this->_Data) ) {
				foreach ($this->_Data as $key => $value) {
					if ( $this->hasFilter($key) ) {
						$filteredData[$key] = $this->getFilter($key)->doFilter($value);
					} else {
						$filteredData[$key] = null;
					}
				}
			}
		} elseif ( in_array($this->_LookupGlobals, array(self::LOOKUPGLOBALS_COOKIE, self::LOOKUPGLOBALS_GET, self::LOOKUPGLOBALS_POST)) ) {
			switch ( $this->_LookupGlobals ) {
				case self::LOOKUPGLOBALS_COOKIE: $connection = INPUT_COOKIE;break;
				case self::LOOKUPGLOBALS_GET:    $connection = INPUT_GET;	break;
				case self::LOOKUPGLOBALS_POST:   $connection = INPUT_POST;	break;
				default:
					$connection = INPUT_POST;
			}

			/* @var utilityFilterInterface $oFilter */
			foreach ( $this->_Filters as $key => $oFilter ) {
				$filteredData[$key] = $oFilter->doGetFilter($connection, $key);
			}
		}

		if ( $this->_FilterAsXml ) {
			$this->_doFilterXml($filteredData);
		}

		return $filteredData;
	}

	/**
	 * Returns filtered data from the specified global, usually GET or POST
	 *
	 * @param integer $inGroup INPUT_ constant
	 * @param string $inField Name of the field to filter
	 * @return mixed
	 */
	public function doGetFilter($inGroup, $inField) {
		$filteredData = null;

		if ( $this->hasFilter($inField) ) {
			$filteredData = $this->getFilter($inField)->doGetFilter($inGroup, $inField);
		}

		return $filteredData;
	}
	
	/**
	 * Post-filters the data to decode XML encoded strings back to UTF-8 characters
	 *
	 * @param array $inData
	 * @access private
	 */
	private function _doFilterXml(&$inData) {
		foreach ( $inData as $key => $value ) {
			if ( $value !== null ) {
				if ( !is_array($value) ) {
					$inData[$key] = utilityStringFunction::xmlStringToUtf8($value);
				}
			}
		}
	}
	
	
	
	/**
	 * Returns the current value of lookupGlobals
	 *
	 * @return integer
	 */
	function getLookupGlobals() {
		return $this->_LookupGlobals;
	}
	
	/**
	 * Set data type to filter on, a class constant matching:
	 *
	 * * 0 - a data array
	 * * 1 - GET data
	 * * 2 - POST data
	 * * 3 - COOKIE data
	 *
	 * @param integer $inLookupGlobals
	 * @return utilityInputManager
	 */
	function setLookupGlobals($inLookupGlobals){
		if ( $inLookupGlobals !== $this->_LookupGlobals )	{
			$this->_LookupGlobals = $inLookupGlobals;
		}
		return $this;
	}

	/**
	 * Returns true if $inKey has an assigned filter instance
	 *
	 * @param string $inKey
	 * @return boolean
	 */
	function hasFilter($inKey) {
		return array_key_exists($inKey, $this->_Filters) && $this->_Filters[$inKey] instanceof utilityFilterInterface;
	}

	/**
	 * Returns a filter for $inKey or false if none found, or all filters if null
	 *
	 * @param string $inKey
	 * @return utilityFilterInterface
	 */
	function getFilter($inKey = null) {
		if ( $inKey !== null ) {
			if ( isset($this->_Filters[$inKey]) ) {
				return $this->_Filters[$inKey];
			} else {
				return false;
			}
		} else {
			return  $this->_Filters;
		}
	}
	
	/**
	 * Add a filter for the key $inKey to the manager, or many filters if $inKey is an array
	 *
	 * @param string $inKey
	 * @param utilityFilterInterface $inFilter
	 * @return utilityInputManager
	 */
	function setFilter($inKey, utilityFilterInterface $inFilter){
		if ( is_array($inKey) && $inFilter === null ) {
			$this->_Filters = $inKey;
		} elseif ( isset($this->_Filters[$inKey]) || $inFilter != null ) {
			if ( isset($this->_Filters[$inKey]) ) {
				if ( $this->_Filters[$inKey] !== $inFilter ) {
					$this->_Filters[$inKey] = $inFilter;
				}
			} else {
				$this->_Filters[$inKey] = $inFilter;
			}
		}
		return $this;
	}
	
	/**
	 * Add filter for $inKey
	 *
	 * @param string $inKey
	 * @param utilityFilterInterface $inFilter
	 * @return utilityInputManager
	 */
	function addFilter($inKey, utilityFilterInterface $inFilter) {
		return $this->setFilter($inKey, $inFilter);
	}
	
	/**
	 * Remove filter for $inKey
	 *
	 * @param string $inKey
	 * @return utilityInputManager
	 */
	function removeFilter($inKey){
		if ( array_key_exists($inKey, $this->_Filters) ) {
			unset($this->_Filters[$inKey]);
		}
		return $this;
	}
	
	/**
	 * Removes all filters, resetting internal array
	 *
	 * @return utilityInputManager
	 */
	function clearFilters(){
		if ( count($this->_Filters) > 0 ) {
			$this->_Filters = array();
		}
		return $this;
	}

	/**
	 * Returns the number of filters currently in the manager
	 *
	 * @return integer
	 */
	function getCount() {
		return count($this->_Filters);
	}

	/**
	 * Alias of getCount()
	 *
	 * @return integer
	 */
	function getFilterCount(){
		return $this->getCount();
	}
	
	/**
	 * Returns the data
	 *
	 * @return mixed
	 */
	function getData() {
		return $this->_Data;
	}
	
	/**
	 * Set data source
	 *
	 * @param mixed $inData
	 * @return utilityInputManager
	 */
	function setData($inData){
		if ($inData !== $this->_Data )	{
			$this->_Data = $inData;
		}
		return $this;
	}
	
	/**
	 * Returns true if the data should be post-filtered and converted from XML to UTF-8 characters
	 *
	 * @return boolean
	 */
	function getFilterAsXml() {
		return $this->_FilterAsXml;
	}
	
	/**
	 * Set whether to post-decode XML encoded data after filtering (true) or not (false)
	 *
	 * @param boolean $inFilterXml
	 * @return utilityInputManager
	 */
	function setFilterAsXml($inFilterXml){
		if ($this->_FilterAsXml !== $inFilterXml) {
			$this->_FilterAsXml = $inFilterXml;
		}
		return $this;
	}
}