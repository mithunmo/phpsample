<?php
/**
 * cacheWriterMemcache class
 * 
 * Stored in memcache.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterMemcache
 * @version $Rev: 650 $
 */


/**
 * cacheWriterMemcache
 * 
 * Provides an interface into the memcached caching system. Requires the PHP
 * memcache extension to be loaded and memcache to be configured and running
 * on appropriate hosts. See: {@link http://ca.php.net/memcache memcache} for
 * detail so on the PHP extension and {@link http://www.danga.com/memcached/}
 * for the memcache server.
 * 
 * <code>
 * // example single server
 * $inOptions = array(
 *     cacheWriterMemcache::OPTION_SERVER_HOST => '192.168.10.1',
 *     cacheWriterMemcache::OPTION_SERVER_PORT => '12345',
 * );
 * $oCacheWriter = new cacheWriterMemcache($inOptions);
 * 
 * // example of single server and use compression
 * $inOptions = array(
 *     cacheWriterMemcache::OPTION_SERVERS => array(
 *         0 => array(
 *             cacheWriterMemcache::OPTION_SERVER_HOST => '192.168.10.1',
 *             cacheWriterMemcache::OPTION_SERVER_PORT => '12345',
 *         ),
 *     ),
 *     cacheWriterMemcache::OPTION_USE_COMPRESSION => true
 * );
 * $oCacheWriter = new cacheWriterMemcache($inOptions);
 * 
 * // example of multiple servers using defaults
 * $inOptions = array(
 *     cacheWriterMemcache::OPTION_SERVERS => array(
 *         0 => array(
 *             cacheWriterMemcache::OPTION_SERVER_HOST => '192.168.10.1',
 *         ),
 *         1 => array(
 *             cacheWriterMemcache::OPTION_SERVER_HOST => '192.168.10.2',
 *         ),
 *         2 => array(
 *             cacheWriterMemcache::OPTION_SERVER_HOST => '192.168.10.3',
 *         ),
 *         3 => array(
 *             cacheWriterMemcache::OPTION_SERVER_HOST => '192.168.10.4',
 *         ),
 *     ),
 * );
 * $oCacheWriter = new cacheWriterMemcache($inOptions);
 * </code>
 * 
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterMemcache
 */
class cacheWriterMemcache extends cacheWriter {

	/**
	 * Stores $_Memcache
	 *
	 * @var Memcache
	 * @access protected
	 */
	protected $_Memcache;
	/**
	 * Stores $_OptionsSet
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_OptionsSet;
	
	/*
	 * Option param names
	 */
	const OPTION_SERVERS = 'servers';
	const OPTION_USE_COMPRESSION = 'use.compression';
	const OPTION_SERVER_HOST = 'server.host';
	const OPTION_SERVER_PORT = 'server.port';
	const OPTION_SERVER_PERSISTENT = 'server.persistent';
	const OPTION_SERVER_WEIGHT = 'server.weight';
	const OPTION_SERVER_TIMEOUT = 'server.timeout';
	const OPTION_SERVER_RETRY = 'server.retry';
	const OPTION_SERVER_STATUS = 'server.status';

	/*
	 * Default values for a server
	 */
	const DEFAULT_HOST = '127.0.0.1';
	const DEFAULT_PORT = 11211;
	const DEFAULT_PERSISTENT = true;
	const DEFAULT_WEIGHT = 1;
	const DEFAULT_TIMEOUT = 3;
	const DEFAULT_RETRY_INTERVAL = 15;
	const DEFAULT_STATUS = true;
	
	/**
	 * Mappings of options to default values
	 *
	 * @var array
	 * @access protected
	 */
	protected static $_DefaultMappings = array(
		self::OPTION_SERVER_HOST => self::DEFAULT_HOST,
		self::OPTION_SERVER_PERSISTENT => self::DEFAULT_PERSISTENT,
		self::OPTION_SERVER_PORT => self::DEFAULT_PORT,
		self::OPTION_SERVER_RETRY => self::DEFAULT_RETRY_INTERVAL,
		self::OPTION_SERVER_STATUS => self::DEFAULT_STATUS,
		self::OPTION_SERVER_TIMEOUT => self::DEFAULT_TIMEOUT,
		self::OPTION_SERVER_WEIGHT => self::DEFAULT_WEIGHT,
	);
	
	
	
	/**
	 * Returns a new memcache writer for the cache controller
	 * 
	 * $inOptions is an associative array of server details and options. It supports 2 formats:
	 * 1. Single Server
	 * 2. Multi Server
	 * 
	 * <code>
	 * // example single server
	 * $inOptions = array(
	 *     cacheWriterMemcache::OPTION_SERVER_HOST => '192.168.10.1',
	 *     cacheWriterMemcache::OPTION_SERVER_PORT => '12345',
	 * );
	 * 
	 * // example of single server and use compression
	 * $inOptions = array(
	 *     cacheWriterMemcache::OPTION_SERVERS => array(
	 *         0 => array(
	 *             cacheWriterMemcache::OPTION_SERVER_HOST => '192.168.10.1',
	 *             cacheWriterMemcache::OPTION_SERVER_PORT => '12345',
	 *         ),
	 *     ),
	 *     cacheWriterMemcache::OPTION_USE_COMPRESSION => true
	 * );
	 * 
	 * // example of multiple servers using defaults
	 * $inOptions = array(
	 *     cacheWriterMemcache::OPTION_SERVERS => array(
	 *         0 => array(
	 *             cacheWriterMemcache::OPTION_SERVER_HOST => '192.168.10.1',
	 *         ),
	 *         1 => array(
	 *             cacheWriterMemcache::OPTION_SERVER_HOST => '192.168.10.2',
	 *         ),
	 *         2 => array(
	 *             cacheWriterMemcache::OPTION_SERVER_HOST => '192.168.10.3',
	 *         ),
	 *         3 => array(
	 *             cacheWriterMemcache::OPTION_SERVER_HOST => '192.168.10.4',
	 *         ),
	 *     ),
	 * );
	 * </code>
	 * 
	 * @param array $inOptions
	 * @param string $inCacheId
	 * @return cacheWriterMemcache
	 */
	function __construct(array $inOptions = array(), $inCacheId = null) {
		if ( !extension_loaded('memcache') ) {
			throw new cacheWriterExtensionNotLoadedException('memcache');
		}
		parent::__construct($inCacheId);
		
		$this->parseOptions($inOptions);
	}

	/**
	 * Removes all cached records from memcache instances
	 *
	 * @param array $inOptions
	 * @return integer
	 * @static
	 */
	static function clearCache(array $inOptions = array()) {
		$oWriter = new cacheWriterMemcache($inOptions);
		return $oWriter->runGc();
	}
	
	
	
	/**
	 * Parses the options assigning as appropriate
	 *
	 * @param array $inOptions
	 * @return void
	 */
	function parseOptions(array $inOptions = array()) {
		if ( !isset($inOptions[self::OPTION_SERVERS]) && isset($inOptions[self::OPTION_SERVER_HOST]) ) {
			$inOptions = array(
				'servers' => array(
					0 => $inOptions
				),
				self::OPTION_USE_COMPRESSION => (isset($inOptions[self::OPTION_USE_COMPRESSION]) ? $inOptions[self::OPTION_USE_COMPRESSION] : false)
			);
		}
		
		if ( count($inOptions) > 0 ) {
			foreach ( $inOptions[self::OPTION_SERVERS] as &$server ) {
				/*
				 * Ensure all options are set
				 */
				$server = array_merge(self::$_DefaultMappings, $server);
				
				/*
				 * In ZDE 6.1.1, addServer is not in autocomplete, order of params is:
				 * Memcache::addServer(host, port, persistent, weight, timeout, retry_interval, status);
				 */
				$this->getMemcache()->addServer(
					$server[self::OPTION_SERVER_HOST], $server[self::OPTION_SERVER_PORT], $server[self::OPTION_SERVER_PERSISTENT],
					$server[self::OPTION_SERVER_WEIGHT], $server[self::OPTION_SERVER_TIMEOUT], $server[self::OPTION_SERVER_RETRY],
					$server[self::OPTION_SERVER_STATUS]
				);
			}
			$this->getOptionsSet()->setOptions($inOptions);
		}
	}
	
	/**
	 * @see cacheWriter::reset()
	 */
	function reset() {
		$this->_Memcache = null;
		$this->_OptionsSet = null;
		parent::reset();
	}
	
	/**
	 * @see cacheWriter::isCached()
	 */
	function isCached() {
		$ret = $this->getMemcache()->get($this->getCacheId());
		if ( $ret !== false ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * @see cacheWriter::isExpired()
	 */
	function isExpired() {
		return (!$this->isCached());
	}
	
	
	
	/**
	 * @see cacheWriter::delete()
	 */
	function delete() {
		if ( $this->getCacheId() ) {
			return $this->getMemcache()->delete($this->getCacheId(), 0);
		}
		return false;
	}
	
	/**
	 * @see cacheWriter::load()
	 */
	function load() {
		$return = false;
		if ( $this->getCacheId() ) {
			$ret = $this->getMemcache()->get($this->getCacheId());
			if ( $ret !== false ) {
				$this->setSerialiseData($ret);
				$this->setModified(false);
				$return = true;
			}
		}
		return $return;
	}
	
	/**
	 * @see cacheWriter::save()
	 */
	function save() {
		$return = false;
		if ( $this->isModified() ) {
			if ( $this->_Modified ) {
				if ( $this->getUseCompression() ) {
					$flag = MEMCACHE_COMPRESSED;
				} else {
					$flag = 0;
				}
				
				/*
				 * Storing requires either add/replace or set/replace, needs two operations
				 */
				if ( $this->isCached() ) {
					$return = $this->getMemcache()->replace($this->getCacheId(), $this->getSerialiseData(), $flag, $this->getLifetime());
				} else {
					$return = $this->getMemcache()->add($this->getCacheId(), $this->getSerialiseData(), $flag, $this->getLifetime());
				}
				$this->setModified(false);
			}
		}
		return $return;
	}
	
	/**
	 * Runs garbage collection
	 * 
	 * @see cacheWriter->runGc()
	 */
	function runGc() {
		return $this->getMemcache()->flush();
	}
	
	
	
	/**
	 * Override getLifetime to add in additional checks for memcache
	 *
	 * @return integer
	 */
	function getLifetime() {
		$lifetime = parent::getLifetime();
		if ( is_null($lifetime) ) {
			$lifetime = 0;
		}
		if ( $lifetime > 2592000 ) {
			$lifetime = 2592000;
		}
		return $lifetime;
	}
	
	/**
	 * Returns $_Memcache
	 *
	 * @return Memcache
	 */
	function getMemcache() {
		if ( !$this->_Memcache instanceof Memcache ) {
			$this->_Memcache = new Memcache();
		}
		return $this->_Memcache;
	}
	
	/**
	 * Set $_Memcache to $inMemcache
	 *
	 * @param Memcache $inMemcache
	 * @return cacheWriterMemcache
	 */
	function setMemcache($inMemcache) {
		if ( $inMemcache !== $this->_Memcache ) {
			$this->_Memcache = $inMemcache;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns true if compression should be used
	 *
	 * @return boolean
	 */
	function getUseCompression() {
		return $this->getOptionsSet()->getOptions(self::OPTION_USE_COMPRESSION, false);
	}

	/**
	 * Returns $_OptionsSet
	 *
	 * @return baseOptionsSet
	 */
	function getOptionsSet() {
		if ( !$this->_OptionsSet instanceof baseOptionsSet ) {
			$this->_OptionsSet = new baseOptionsSet();
		}
		return $this->_OptionsSet;
	}
	
	/**
	 * Set $_OptionsSet to $inOptionsSet
	 *
	 * @param baseOptionsSet $inOptionsSet
	 * @return cacheWriterMemcache
	 */
	function setOptionsSet($inOptionsSet) {
		if ( $inOptionsSet !== $this->_OptionsSet ) {
			$this->_OptionsSet = $inOptionsSet;
			$this->setModified();
		}
		return $this;
	}
}