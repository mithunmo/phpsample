<?php
/**
 * cacheWriterApc class
 * 
 * Stored in apc.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterApc
 * @version $Rev: 706 $
 */


/**
 * cacheWriterApc
 * 
 * Provides an interface into the APC caching system. Requires the APC extension
 * be loaded and configured correctly.
 * 
 * <code>
 * // store an item using defaults
 * $oWriter = new cacheWriterApc();
 * $oWriter->setCacheId('MyCachedObject');
 * $oWriter->setData(new stdClass());
 * $oWriter->save();
 * 
 * // check if cached and fetch the previous item
 * $oWriter = new cacheWriterApc();
 * $oWriter->setCacheId('MyCachedObject');
 * if ( $oWriter->isCached() ) {
 *     $oWriter->load();
 *     $oWriter->getData();
 *     //...
 * }
 * 
 * // clear the cache
 * $oWriter = new cacheWriterApc();
 * $oWriter->setCacheId('MyCachedObject');
 * $oWriter->delete();
 * if ( $oWriter->isCached() ) {
 *     // this should not execute...
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterApc
 */
class cacheWriterApc extends cacheWriter {
	
	/**
	 * Returns a new apc writer for the cache controller
	 * 
	 * @param string $inCacheId
	 * @return cacheWriterApc
	 */
	function __construct($inCacheId = null) {
		if ( !extension_loaded('apc') ) {
			throw new cacheWriterExtensionNotLoadedException('apc');
		}
		parent::__construct($inCacheId);
	}

	/**
	 * Removes all cached records from apc user space
	 *
	 * @return integer
	 * @static
	 */
	static function clearCache() {
		$oWriter = new cacheWriterApc();
		return $oWriter->runGc();
	}
	
	
	
	/**
	 * @see cacheWriter::isCached()
	 */
	function isCached() {
		$ret = apc_fetch($this->getCacheId());
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
			return apc_delete($this->getCacheId());
		}
		return false;
	}
	
	/**
	 * @see cacheWriter::load()
	 */
	function load() {
		$return = false;
		if ( $this->getCacheId() ) {
			$ret = apc_fetch($this->getCacheId());
			if ( is_array($ret) && count($ret) == 2 ) {
				$this->setSerialiseData($ret[0]);
				$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat(), $ret[1]));
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
				/*
				 * In ZDE 6.1.1 there is no auto-complete for APC
				 * 
				 * apc_store(key, var, ttl);
				 */
				$return = apc_store($this->getCacheId(), array($this->getSerialiseData(), time()), $this->getLifetime());
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
		return apc_clear_cache('user');
	}
}