<?php
/**
 * systemAutoload.class.php
 * 
 * systemAutoload class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemAutoload
 * @version $Rev: 699 $
 */


/*
 * Load dependencies
 */
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'exception.class.php');


/**
 * systemAutoload
 * 
 * Handles class autoloading and location. This class can be registered with
 * either __autoload or spl_autoload_register. It is preferable to use the spl
 * function.
 * 
 * The autoload system works by de-constructing the classname and attempting to
 * match it to an autoload file in the /libraries/autoload folder. This autoload
 * file is simply an associative array of classname => location relative to 
 * /libraries. If the class location is already in the internal array of locations
 * it will be immediately included without any further lookups required.
 * 
 * The autoload file is located by breaking apart the class name using either the
 * camel case breaks e.g. dbManager would first be checked on dbmanager and then
 * just db; or PEAR / ZF style and splitting on the underscore (_) e.g. Db_Manager
 * dbmanager / db. Note that all autoload cache files are lowercase.
 * 
 * If the class cannot be found in any of the cache files in any of the registered
 * paths, the autoloader will fall back to trying to map the class components to
 * a file structure. e.g. dbManager would attempt to be loaded from first:
 * 
 * %path%/db/Manager.(class.)php and then
 * %path%/db/manage.(class.)php
 * 
 * Db_Manager would be loaded from:
 * %path%/Db/Manager.(class.)php
 * 
 * Both .class.php and .php are used as extensions.
 * 
 * The autoload files should be based on the class name and split into groups. The
 * adopted standard is similar to ez Components e.g.: all system classes can be
 * loaded from system_autoload.php.
 * 
 * The autoload cache file should return an array of all classes and locations
 * that it contains:
 * 
 * <code>
 * // example cache file
 * return array(
 *     'myClass' => 'my/class.class.php',
 *     'myOtherClass' => 'my/other.class.php',
 * );
 * </code>
 * 
 * The autoloader is automatically registered by including system.inc. Otherwise you
 * can implement your own autoloader based on this.
 * 
 * Further paths can be added to the autoloader by calling {@link systemAutoload::addPath()}
 * and giving the location of the class folder. This should be a full path to the
 * folder and the folder should contain a sub-folder called "autoload". The
 * autoload sub-folder is where the autoload cache files will be read from.
 * 
 * systemAutoload will cache the resolved class paths as they are loaded and used.
 * This removes the need to have to cycle through the assigned paths looking for
 * classes and can improve performance.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemAutoload
 */
class systemAutoload {
	
	/**
	 * Holds instance of systemAutoload
	 *
	 * @var systemAutoload
	 * @access private
	 * @static 
	 */
	static private $_Instance;
	
	/**
	 * Stores an array of class locations
	 *
	 * @var array
	 * @access private
	 * @static
	 */
	static private $_ClassPaths = array();
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores the array of mapped classes
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_AutoloadArray;
	
	/**
	 * Stores $_ResolvedClasses
	 *
	 * @var array
	 * @access protected
	 */
	protected $_ResolvedClasses;
	
	
	
	/**
	 * Returns instance of systemAutoload
	 *
	 * @return systemAutoload
	 */
	private function __construct() {
		$this->_AutoloadArray = array();
		$this->_ResolvedClasses = array();
		
		/*
		 * Load fully resolved classes
		 */
		$this->load();
		
		/*
		 * Auto-update resolved classes cache file
		 */
		register_shutdown_function(array($this, 'save'));
	}
	
	/**
	 * Returns the single instance of systemAutoload
	 *
	 * @return systemAutoload
	 * @static 
	 */
	static function getInstance() {
		if ( !self::$_Instance instanceof systemAutoload ) {
			self::$_Instance = new systemAutoload();
		}
		return self::$_Instance;
	}
	
	/**
	 * Static autoload method
	 *
	 * @param string $inClassname
	 * @return boolean
	 * @static
	 */
	static function autoload($inClassname) {
		return self::getInstance()->loadClass($inClassname);
	}

	/**
	 * Returns the array of paths to search for classes in
	 *
	 * @return array
	 * @static
	 */
	static function getClassPaths() {
		if ( count(self::$_ClassPaths) == 0 ) {
			self::addPath(dirname(dirname(dirname(__FILE__))));
		}
		return self::$_ClassPaths;
	}
	
	/**
	 * Add a path to search for classes in
	 *
	 * @param string $inPath
	 * @return void
	 * @static
	 */
	static function addPath($inPath) {
		$inPath = self::cleanDirSlashes($inPath);
		if ( !in_array($inPath, self::$_ClassPaths) ) {
			self::$_ClassPaths[] = $inPath;
		}
	}
	
	/**
	 * Set $_ClassPaths to $inClassPaths
	 *
	 * @param array $inClassPaths
	 * @return void
	 * @static
	 */
	static function setClassPaths(array $inClassPaths = array()) {
		if ( $inClassPaths !== self::$_ClassPaths ) {
			self::$_ClassPaths = $inClassPaths;
		}
	}
	
	/**
	 * Removes the path from the set of class locations
	 *
	 * @param string $inPath
	 * @return void
	 * @static
	 */
	static function removePath($inPath) {
		$inPath = self::cleanDirSlashes($inPath);
		$key = array_search($inPath, self::$_ClassPaths);
		if ( $key !== false ) {
			unset(self::$_ClassPaths[$key]);
		}
	}
	
	/**
	 * Returns a string from "SomethingLikeThis" into "Something Like This"
	 * 
	 * @param string $inString
	 * @param string $inSeparator (default is a space)
	 * @return string
	 * @static 
	 */
	static function convertCapitalizedString($inString, $inSeparator = ' ') {
		$match = false;
		while (preg_match('/([a-z])([A-Z])/',$inString,$match)) {
			$inString = str_replace($match[0],$match[1].$inSeparator.$match[2],$inString);
		}
		return $inString;
	}
	
	/**
	 * Cleans the tailing slash off the path	
	 *
	 * @param string $inPath
	 * @return string
	 * @static 
	 */
	static function cleanPath($inPath) {
		return preg_replace('/\/$/','',$inPath);
	}

	/**
	 * Replaces directory slashes with the system set directory separator
	 *
	 * @param string $inPath
	 * @return string
	 * @static 
	 */
	static function cleanDirSlashes($inPath) {
		$path = str_replace(array('\\','/'), DIRECTORY_SEPARATOR, $inPath);
		$path = preg_replace("/[\\/\\\\]{2,}/", DIRECTORY_SEPARATOR, $path);
		return $path;
	}
	
	
	
	/**
	 * Attempts to autoload the class named $inClassname
	 *
	 * @param string $inClassname
	 * @return boolean
	 */
	function loadClass($inClassname) {
		if ( !$inClassname ) {
			return false;
		}
		
		/*
		 * check autoload array first and load as required
		 */
		if ( $this->getClassFile($inClassname) ) {
			return $this->_includeFile($inClassname, $this->getClassFile($inClassname));
		}
		
		/*
		 * no entry, so check the autoload folder first
		 */
		if ( stripos($inClassname, '_') !== false ) {
			$class = str_replace('_', '/', $inClassname);
		} else {
			$class = self::convertCapitalizedString($inClassname, '/');
		}
		$components = explode('/', $class);
		
		/*
		 * Attempt to locate autoload file
		 */
		if ( count($components) > 0 ) {
			while ( count($components) > 0 ) {
				$classPath = 'autoload'.DIRECTORY_SEPARATOR.strtolower(implode('', $components)).'_autoload.php';
				
				try {
				    return $this->_includeFile($inClassname, $classPath, true);
				} catch ( systemAutoloadClassDoesNotExistInAutoloadCache $e ) {
					// recoverable (class likely in another autoload cache file)
					array_pop($components);
					continue;
				} catch ( systemAutoloadInvalidCacheFile $e ) {
					// recoverable (class likely in another autoload cache file)
					array_pop($components);
					continue;
				} catch ( systemAutoloadFileDoesNotExist $e ) {
					// recoverable (actually ignore, otherwise the systemLog cannot be autoloaded)
					array_pop($components);
					continue;
				} catch ( systemAutoloadFileIsNotReadable $e ) {
					// recoverable
					array_pop($components);
					continue;
				} catch ( Exception $e ) {
					// all other conditions are fatal ONLY IF there is just this autoloader
					if ( count(spl_autoload_functions()) <= 1 ) {
						throw $e;
					} else {
						// defer to another spl_autoload system
						break;
					}
				}
			}
		}
		
		return $this->_fallback($inClassname, $class);
	}
	
	/**
	 * Attempts to load the specified class without using an autoload cache file
	 *
	 * @param string $inClassname
	 * @param string $inClassPath
	 * @return boolean
	 */
	protected function _fallback($inClassname, $inClassPath) {
		/*
		 * For single named class e.g. Savant3, PHPTAL etc, prefix with
		 * the class name first as they should be in a sub-folder
		 */
		if ( strpos($inClassPath, '/') === false ) {
			$inClassPath .= '/'.$inClassPath;
		}
		
		/*
		 * Array of various possible formats that the class could be found in
		 */
		$formats = array(
			$inClassPath, // default My/Class/Here
			strtolower($inClassPath), // lowecased my/class/here
			str_replace('/', '', $inClassPath), // Scorpio style MyClassHere
			str_replace('/', '_', $inClassPath) // ZF / Pear style My_Class_Here
		);
		
		foreach ( $formats as $format ) {
			try {
				try {
					return $this->_includeFile($inClassname, $format.'.class.php', false);
				} catch ( Exception $e ) {
					return $this->_includeFile($inClassname, $format.'.php', false);
				}
			} catch ( Exception $e ) {
				// do nothing
			}
		}
		
		/*
		 * Give up trying to locate the file
		 */
		return false;
	}
	
	/**
	 * File include wrapper, returns true on success, else throws exceptions
	 *
	 * @param string $inClassname
	 * @param string $classPath
	 * @return boolean
	 * @throws systemAutoloadFileDoesNotExist
	 * @throws systemAutoloadFileIsNotReadable
	 * @throws systemAutoloadClassDoesNotExistInAutoloadCache
	 * @throws systemAutoloadClassCouldNotBeLoaded
	 */
	protected function _includeFile($inClassname, $inClassPath, $isAutoloadFile = false) {
		if ( !$isAutoloadFile && array_key_exists($inClassname, $this->_ResolvedClasses) ) {
			return $this->_includeOnce($this->_ResolvedClasses[$inClassname]);
		}
		
		/*
		 * Find the file from our set of search paths
		 */
		$basePath = $this->_getBasePath($inClassname, $inClassPath);
		$classPath = $basePath.$inClassPath;
		if ( @!is_readable($classPath) ) {
			throw new systemAutoloadFileIsNotReadable($inClassname, $classPath);
		}
		
		/*
		 * If this is an autoload cache file, include and merge the mappings
		 */
		if ( $isAutoloadFile === true ) {
			$array = $this->_includeOnce($classPath);
			if ( !is_array($array) ) {
				throw new systemAutoloadInvalidCacheFile($inClassname, $classPath);
			}
			
			/*
			 * Import autoload data into main data set, regardless if the class is not
			 * actually in there (will save a lookup in future)
			 */
			$this->_AutoloadArray = array_merge($this->_AutoloadArray, $array);
			
			/*
			 * Now check that our class exists in the loaded data set
			 */
			if ( !array_key_exists($inClassname, $array) ) {
				throw new systemAutoloadClassDoesNotExistInAutoloadCache($inClassname, $classPath);
			}
			
			/*
			 * Include class file now
			 */
			if ( $this->_includeOnce($basePath.$this->getClassFile($inClassname)) ) {
				return true;
			} else {
				throw new systemAutoloadClassCouldNotBeLoaded($inClassname, $classPath);
			}
		}
		
		/*
		 * Try to include the actual file
		 */
		if ( $this->_includeOnce($classPath) ) {
			if ( !array_key_exists($inClassname, $this->_ResolvedClasses) ) {
				$this->addResolvedClass($inClassname, $classPath);
			}
			return true;
		} else {
			throw new systemAutoloadClassCouldNotBeLoaded($inClassname, $classPath);
		}
	}
	
	/**
	 * Includes the file $inFile
	 *
	 * @param string $inFile
	 * @return boolean
	 */
	protected function _includeOnce($inFile) {
		return include_once $inFile;
	}
	
	/**
	 * Returns the full path to the specified file, or throws an exception if it cannot be found
	 *
	 * @param string $inClassname
	 * @param string $inClassPath
	 * @return string
	 * @throws systemAutoloadFileDoesNotExist
	 */
	protected function _getBasePath($inClassname, $inClassPath) {
		foreach ( self::getClassPaths() as $basePath ) {
			$basePath = self::cleanPath($basePath);
			$classPath = self::cleanDirSlashes($basePath.DIRECTORY_SEPARATOR.$inClassPath);
			if ( @file_exists($classPath) ) {
				return $basePath.DIRECTORY_SEPARATOR;
			}
		}
		throw new systemAutoloadFileDoesNotExist($inClassname, $classPath);
	}
	
	/**
	 * Loads the previously cached autoload map
	 *
	 * @return boolean
	 */
	function load() {
		$cacheFile = $this->getCacheFile();
		if ( @file_exists($cacheFile) && @is_readable($cacheFile) ) {
			$cacheArray = include_once $cacheFile;
			if ( $cacheArray && is_array($cacheArray) && count($cacheArray) > 0 ) {
				$this->setResolvedClasses($cacheArray);
				$this->setModified(false);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Saves the autoload cache to the filesystem
	 *
	 * @return boolean
	 */
	function save() {
		if ( $this->isModified() ) {
			$cacheFile = $this->getCacheFile();
			$data = '<?php /* Auto-generated at '.date(DATE_COOKIE).' by '.__CLASS__.' */'.
				"\n".'return '.var_export($this->getResolvedClasses(), true).';';
			
			if ( !@file_exists(dirname($cacheFile)) ) {
				@mkdir(dirname($cacheFile), 0775, true);
			}
			
			if ( !file_exists($cacheFile) ) {
				@touch($cacheFile);
				@chmod($cacheFile, 0666);
			}
			
			if ( @is_writable($cacheFile) ) {
				$bytes = @file_put_contents($cacheFile, $data, LOCK_EX);
				return true;
			}
			$this->setModified(false);
		}
		return false;
	}
	
	/**
	 * Deletes the cache record
	 *
	 * @return boolean
	 */
	function delete() {
		$cacheFile = $this->getCacheFile();
		if ( $cacheFile ) {
			if ( @unlink($cacheFile) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns the full path to the autoload cache file
	 *
	 * @return string
	 * @access private
	 */
	private function getCacheFile() {
		$base  = dirname(dirname(dirname(dirname(__FILE__))));
		$base .= DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'systemAutoload.cache.php';
		return $base;
	}
	
	

	/**
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return systemAutoload
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns the entire autoload array
	 *
	 * @return array
	 */
	function getClasses() {
		return $this->_AutoloadArray;
	}
	
	/**
	 * Returns total number of classes in autoload system
	 *
	 * @return integer
	 */
	function countClasses() {
		return count($this->_AutoloadArray);
	}
	
	/**
	 * Returns the file path for $inClassname
	 *
	 * @param string $inClassname
	 * @return string
	 */
	function getClassFile($inClassname) {
		if ( $inClassname === null ) {
			return $this->_AutoloadArray;
		} else {
			if ( array_key_exists($inClassname, $this->_AutoloadArray) ) {
				return $this->_AutoloadArray[$inClassname];
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Sets the class path for $inClassname
	 *
	 * @param string $inClassname
	 * @param string $inClasspath
	 * @return systemAutoload
	 */
	function setClassFile($inClassname, $inClasspath) {
		return $this->_AutoloadArray[$inClassname] = $inClasspath;
	}

	/**
	 * Returns $_ResolvedClasses
	 *
	 * @return array
	 */
	function getResolvedClasses() {
		return $this->_ResolvedClasses;
	}
	
	/**
	 * Add fully resolved class path
	 * 
	 * @param string $inClassname
	 * @param string $inClassPath
	 * @return systemAutoload
	 */
	function addResolvedClass($inClassname, $inClassPath) {
		$this->_ResolvedClasses[$inClassname] = $inClassPath;
		$this->setModified();
		return $this;
	}
	
	/**
	 * Set $_ResolvedClasses to $inResolvedClasses
	 *
	 * @param array $inResolvedClasses
	 * @return systemAutoload
	 */
	function setResolvedClasses($inResolvedClasses) {
		if ( $inResolvedClasses !== $this->_ResolvedClasses ) {
			$this->_ResolvedClasses = $inResolvedClasses;
			$this->setModified();
		}
		return $this;
	}
}
