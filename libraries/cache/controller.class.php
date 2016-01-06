<?php
/**
 * cacheController class
 *
 * Stored in controller.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cache
 * @category cacheController
 * @version $Rev: 650 $
 */


/**
 * cacheController
 * 
 * Provides an interface to a caching mechanism with multiple backends.
 * Used to cache data to a file or database table (dependent on writer).
 * 
 * The controller is the primary interface to the cache layer. It requires
 * a cacheWriter object, defaulting to the file writer if none is
 * specified. The controller will pass calls into the writer to check
 * if an item is cached or not.
 * 
 * It is a good idea to not rely on the generatedCacheId as this is only
 * known after data has been stored and unless you store this separately
 * there will be no way of retrieving the cache information. The generated
 * cacheId is an SHA1 key of the serialised data.
 * 
 * As all writers serialise data before storing it, whatever you wish to
 * store should be serialisable first. If your object requires connections
 * then you should implement __sleep and __wakeup to remove / set these.
 * Alternatively, you should implement the serializable interface.
 * 
 * Garbage Collection is handled by runGc(). This is based on a random number
 * between 1 and $_GcInterval; if 1 is produced, GC is run. Therefore, if
 * $_GcInterval is 100, there is a 1 in 100 chance of the GC running. This
 * should be configured based on the application load (number of requests
 * per second), too low and the cache will always be cleared, too high
 * and it will never be cleared.
 * 
 * Some examples of usage:
 * 
 * <code>
 * // adding to the cache
 * $oObject = new stdClass();
 * $oObject->id = 'some id string';
 * 
 * $oCacheCtrl = cacheController::getInstance(
 *     new cacheWriterFile()
 * );
 * $oCacheCtrl->setCacheId(get_class($oObject).'_'.$oObject->id);
 * $oCacheCtrl->setData($oObject);
 * $oCacheCtrl->cache();
 * 
 * // checking the cache
 * $oCacheCtrl = cacheController::getInstance(
 *     new cacheWriterFile()
 * );
 * $oCacheCtrl->setCacheId('stdClass_some id string');
 * if ( $oCacheCtrl->isCached() ) {
 *     // do something
 * }
 * 
 * $oCacheCtrl = cacheController::getInstance(
 *     new cacheWriterFile()
 * );
 * $oCacheCtrl->setCacheId('stdClass_some id string');
 * if ( $oCacheCtrl->isCached() ) {
 *     $oCacheCtrl->clearCache();
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage cache
 * @category cacheController
 */
class cacheController {
	
	/**
	 * Holds a static instance of the cache controller
	 *
	 * @var cacheController
	 * @access private
	 * @static 
	 */
	private static $_Instance;
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Writer
	 *
	 * @var cacheWriter
	 * @access private
	 */
	private $_Writer;
	/**
	 * Stores $_Lifetime
	 *
	 * @var integer
	 * @access private
	 */
	private $_Lifetime;
	/**
	 * Stores $_GcInterval, the garbage collection interval
	 *
	 * @var integer
	 * @access private
	 */
	private $_GcInterval;
	
	
	
	/**
	 * Returns new instance of cache controller
	 * 
	 * @param cacheWriter $inWriter
	 * @param string $inCacheId
	 * @return cacheController
	 */
	function __construct(cacheWriter $inWriter, $inCacheId = null) {
		$this->reset();
		if ( $inWriter !== null && $inWriter instanceof cacheWriter ) {
			$this->setWriter($inWriter);
		}
		if ( $inCacheId !== null ) {
			$this->getWriter()->setCacheId($inCacheId);
		}
	}
	
	/**
	 * Returns the instance of the cache controller, if no writer is specified default to file writer
	 *
	 * @param cacheWriter $inWriter
	 * @param string $inCacheId
	 * @return cacheController
	 * @static
	 */
	static function getInstance($inWriter = null, $inCacheId = null) {
		if ( $inWriter === null ) {
			$inWriter = new cacheWriterFile();
		}
		
		if ( self::$_Instance instanceof cacheController ) {
			self::$_Instance->setWriter($inWriter);
			if ( $inCacheId !== null ) {
				self::$_Instance->setCacheId($inCacheId);
			}
		} else {
			self::$_Instance = new self($inWriter, $inCacheId);
		}
		
		return self::$_Instance;
	}
	
	/**
	 * Reset controller to defaults
	 *
	 * @return void;
	 */
	function reset() {
		$this->_Writer = null;
		$this->_Lifetime = 3600; // one hour
		$this->_GcInterval = 2500;
	}

	/**
	 * If no key has been set, creates an SHA1 hash of the current data set
	 *
	 * @return cacheController
	 * @throws cacheControllerNoDataForKeyException
	 */
	function generateCacheId() {
		if ( !$this->getWriter()->getCacheId() ) {
			if ( strlen($this->getWriter()->getSerialiseData()) == 0 ) {
				throw new cacheControllerNoDataForKeyException();
			}
			$this->getWriter()->setCacheId(sha1($this->getWriter()->getSerialiseData()));
		}
		return $this;
	}
	
	/**
	 * Returns true if the current cache id has been cached
	 *
	 * @return boolean
	 */
	function isCached() {
		$this->runGc();
		return $this->getWriter()->isCached();
	}
	
	/**
	 * Returns true if the cache needs a refresh
	 *
	 * @return boolean
	 */
	function isExpired() {
		$this->runGc();
		return $this->getWriter()->setLifetime($this->getLifetime())->isExpired();
	}
	
	/**
	 * Attempts to load the cache data from the key
	 *
	 * @return boolean
	 */
	function getCache() {
		return $this->getWriter()->load();
	}
	
	/**
	 * Caches the data currently set, or $inData if supplied
	 *
	 * @param mixed $inData
	 * @return boolean
	 */
	function cache($inData = null) {
		if ( $inData !== null ) {
			$this->getWriter()->setData($inData);
		}
		return $this->getWriter()->save();
	}
	
	/**
	 * Clears the cache for the current key
	 *
	 * @return boolean
	 */
	function clearCache() {
		return $this->getWriter()->delete();
	}
	
	/**
	 * Runs garbage collection at a random point configured by GcInterval
	 *
	 * @return void
	 */
	function runGc() {
		if ( rand(1, $this->getGcInterval()) == 1 ) {
			try {
				$this->getWriter()->runGc();
			} catch ( Exception $e ) {
				// do nothing
			}
		}
	}
	
	
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return cacheController
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns the cacheId
	 *
	 * @return string
	 */
	function getCacheId() {
		return $this->getWriter()->getCacheId();
	}
	
	/**
	 * Sets the cacheId
	 *
	 * @param string $inCacheId
	 * @return cacheController
	 */
	function setCacheId($inCacheId) {
		$this->getWriter()->setCacheId($inCacheId);
		return $this;
	}
	
	/**
	 * Returns $_Data
	 *
	 * @return mixed
	 */
	function getData() {
		return $this->getWriter()->getData();
	}
	
	/**
	 * Set $_Data to $inData
	 *
	 * @param mixed $inData
	 * @return cacheController
	 */
	function setData($inData) {
		$this->getWriter()->setData($inData);
		return $this;
	}
	
	/**
	 * Returns $_Writer
	 *
	 * @return cacheWriter
	 */
	function getWriter() {
		return $this->_Writer;
	}
	
	/**
	 * Set $_Writer to $inWriter
	 *
	 * @param cacheWriter $inWriter
	 * @return cacheController
	 */
	function setWriter($inWriter) {
		if ( $inWriter !== $this->_Writer ) {
			$this->_Writer = $inWriter;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Lifetime
	 *
	 * @return integer
	 */
	function getLifetime() {
		return $this->_Lifetime;
	}
	
	/**
	 * Set $_Lifetime to $inLifetime
	 *
	 * @param integer $inLifetime
	 * @return cacheController
	 */
	function setLifetime($inLifetime) {
		if ( $inLifetime !== $this->_Lifetime ) {
			$this->_Lifetime = $inLifetime;
			$this->getWriter()->setLifetime($this->_Lifetime);
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_GcInterval
	 *
	 * @return integer
	 */
	function getGcInterval() {
		return $this->_GcInterval;
	}
	
	/**
	 * Set $_GcInterval to $inGcInterval
	 *
	 * @param integer $inGcInterval
	 * @return cacheController
	 */
	function setGcInterval($inGcInterval) {
		if ( $inGcInterval !== $this->_GcInterval ) {
			$this->_GcInterval = $inGcInterval;
			$this->setModified();
		}
		return $this;
	}
}