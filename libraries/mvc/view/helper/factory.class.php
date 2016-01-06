<?php
/**
 * mvcViewHelperFactory.class.php
 * 
 * mvcViewHelperFactory class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewHelperFactory
 * @version $Rev: 821 $
 */


/**
 * mvcViewHelperFactory class
 * 
 * mvcViewHelperFactory handles requests for view helpers, locates and loads
 * the helper objects and general provides house keeping for the instantiated
 * objects.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewHelperFactory
 */
class mvcViewHelperFactory {
	
	/**
	 * Array of search paths for available helper objects
	 *
	 * @var array
	 * @access protected
	 */
	protected $_HelperPaths = array();
	
	/**
	 * Array of mappings of helper class names to paths
	 *
	 * @var array
	 * @access protected
	 */
	protected $_HelperMap = array();
	
	/**
	 * Array of instances of the helpers
	 *
	 * @var array
	 * @access protected
	 */
	protected $_HelperInstances = array();
	
	
	
	/**
	 * Creates a new helper factory
	 *
	 * @return mvcViewHelperFactory
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
		$this->_HelperPaths = array(
			dirname(__FILE__)
		);
		$this->_HelperInstances = array();
		$this->_HelperMap = array();
	}
	
	
	
	/**
	 * Returns the requested helper instance, creating one if it does not already exist
	 *
	 * $inHelper should be the name of the helper, either the template function call
	 * or the classname. If the template function is given, it will be converted to
	 * mvcViewHelper[A-Z]... and a file will be located for the function name with
	 * .class.php appended. Only the {@link mvcViewHelperFactory::$_HelperPaths} will
	 * be searched.
	 * 
	 * When naming your view helper, be sure to follow the convention, otherwise it may
	 * not be loaded.
	 * 
	 * @param string $inHelper
	 * @param string $inHelperPrefix Prefix used for view helpers
	 * @return mvcViewHelperInterface
	 */
	function getHelper($inHelper, $inHelperPrefix = 'mvcViewHelper') {
		$helperClass = $inHelper;
		if ( strpos($helperClass, $inHelperPrefix) === false ) {
			$helperClass = $inHelperPrefix.ucfirst($helperClass);
		}
		
		$oHelper = $this->getInstance($helperClass);
		if ( $oHelper instanceof mvcViewHelperInterface ) {
			return $oHelper;
		}

		include_once $this->getHelperPath($helperClass, $inHelperPrefix);
		$oHelper = new $helperClass();
		$this->addInstance($helperClass, $oHelper);
		return $oHelper;
	}
	
	/**
	 * Returns the trailing stub of the class from the helper
	 *
	 * @param string $inHelperClass
	 * @param string $inHelperPrefix Prefix used for view helpers
	 * @return string
	 */
	function getHelperClassStub($inHelperClass, $inHelperPrefix = 'mvcViewHelper') {
		$filestub = str_replace($inHelperPrefix, '', $inHelperClass);
		$stub = strtolower(substr($filestub, 0, 1)).substr($filestub, 1);
		return $stub;
	}
	
	/**
	 * Adds a search path for the helpers, paths are searched in the order they are added 
	 *
	 * @param string $inPath
	 * @return mvcViewHelperFactory
	 */
	function addHelperPath($inPath) {
		if ( !in_array($inPath, $this->_HelperPaths) ) {
			$this->_HelperPaths[] = $inPath;
		}
		return $this;
	}
	
	/**
	 * Returns the path to the view helper
	 *
	 * @param string $inHelperClass
	 * @param string $inHelperPrefix Prefix used with view helpers
	 * @return string
	 * @throws mvcViewException
	 */
	function getHelperPath($inHelperClass, $inHelperPrefix = 'mvcViewHelper') {
		if ( $this->getMapping($inHelperClass) ) {
			return $this->getMapping($inHelperClass);
		}
		
		$filename = $this->getHelperClassStub($inHelperClass, $inHelperPrefix).'.class.php';
		foreach ( $this->_HelperPaths as $path ) {
			$filepath = $path.DIRECTORY_SEPARATOR.$filename;
			if ( file_exists($filepath) && is_readable($filepath) ) {
				$this->addMapping($inHelperClass, $filepath);
				return $filepath;
			}
		}
		
		throw new mvcViewException("Failed to locate a file for $inHelperClass in helper paths");
	}
	
	/**
	 * Removes the search path
	 *
	 * @param string $inPath
	 * @return mvcViewHelperFactory
	 */
	function removeHelperPath($inPath) {
		$key = array_search($inPath, $this->_HelperPaths);
		if ( $key !== false ) {
			unset($this->_HelperPaths[$key]);
		}
		return $this;
	}
	
	/**
	 * Adds a helper class mapping to a path
	 *
	 * @param string $inHelperClass
	 * @param string $inPath
	 * @return mvcViewHelperFactory
	 */
	function addMapping($inHelperClass, $inPath) {
		if ( !array_key_exists($inHelperClass, $this->_HelperMap) ) {
			$this->_HelperMap[$inHelperClass] = $inPath;
		}
		return $this;
	}
	
	/**
	 * Returns the mapped path to the helper class, false if not found
	 *
	 * @param string $inHelperClass
	 * @return string|false
	 */
	function getMapping($inHelperClass) {
		if ( array_key_exists($inHelperClass, $this->_HelperMap) ) {
			return $this->_HelperMap[$inHelperClass];
		}
		return false;
	}
	
	/**
	 * Removes the class mapping
	 *
	 * @param string $inHelperClass
	 * @return mvcViewHelperFactory
	 */
	function removeMapping($inHelperClass) {
		if ( array_key_exists($inHelperClass, $this->_HelperMap) ) {
			unset($this->_HelperMap[$inHelperClass]);
		}
		return $this;
	}
	
	/**
	 * Adds the instance of the helper class to the pool
	 *
	 * @param string $inHelperClass
	 * @param mvcViewHelperInterface $inObject
	 * @return mvcViewHelperFactory
	 */
	function addInstance($inHelperClass, $inObject) {
		if ( !array_key_exists($inHelperClass, $this->_HelperInstances) ) {
			$this->_HelperInstances[$inHelperClass] = $inObject;
		}
		return $this;
	}
	
	/**
	 * Returns the existing instance of view helper, or null if not set
	 *
	 * @param string $inHelperClass
	 * @return mvcViewHelperInterface|null
	 */
	function getInstance($inHelperClass) {
		if ( array_key_exists($inHelperClass, $this->_HelperInstances) ) {
			return $this->_HelperInstances[$inHelperClass];
		}
		return null;
	}
	
	/**
	 * Removes an instance of the view helper
	 *
	 * @param string $inHelperClass
	 * @return mvcViewHelperFactory
	 */
	function removeInstance($inHelperClass) {
		if ( array_key_exists($inHelperClass, $this->_HelperInstances) ) {
			$this->_HelperInstances[$inHelperClass] = null;
			unset($this->_HelperInstances[$inHelperClass]);
		}
		return $this;
	}
}