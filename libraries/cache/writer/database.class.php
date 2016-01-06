<?php
/**
 * cacheWriterDatabase class
 * 
 * Stored in database.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterDatabase
 * @version $Rev: 650 $
 */


/**
 * cacheWriterDatabase
 * 
 * Provides a database level storage system for the cache controller.
 * This class requires a table with the following schema:
 * 
 * <code>
 * CREATE TABLE `dataCache` (
 *   `cacheId` varchar(255) NOT NULL,
 *   `data` mediumtext NOT NULL,
 *   `createDate` datetime NOT NULL,
 *   `updateDate` datetime NOT NULL,
 *   `lifetime` int(10) NOT NULL default '3600',
 *   UNIQUE KEY `cacheId` (`cacheId`)
 * ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 * </code>
 * 
 * dataCache can be renamed however you wish to, but it must exist. This
 * class is abstract and requires a specific database backend implementation.
 * 
 * Sub-classes need to implement the following methods:
 * <ul>
 *     <li>{@link cacheWriter::load()}</li>
 *     <li>{@link cacheWriter::save()}</li>
 *     <li>{@link cacheWriter::delete()}</li>
 *     <li>{@link cacheWriter::runGc()}</li>
 * </ul>
 * 
 * Note: there are some small differences between this writer and the file
 * writer. Using isCached() and isExpired() will automatically trigger a load from
 * the database. If not, each would require a separate query; a select 1 from...,
 * select (now()-unix_timestamp(update_time))>lifetime from...) and a select.
 * 
 * Some examples of using the database writer storage backend:
 *  
 * <code>
 * // store an item using defaults
 * $oWriter = new cacheWriterDatabase('dbName', 'tbCacheName');
 * $oWriter->setCacheId('MyCachedObject');
 * $oWriter->setData(new stdClass());
 * $oWriter->save();
 * 
 * // check if cached and fetch the previous item
 * $oWriter = new cacheWriterDatabase('dbName', 'tbCacheName');
 * $oWriter->setCacheId('MyCachedObject');
 * if ( $oWriter->isCached() ) {
 *     $oWriter->getData();
 *     //...
 * }
 * 
 * // clear the cache
 * $oWriter = new cacheWriterDatabase('dbName', 'tbCacheName');
 * $oWriter->setCacheId('MyCachedObject');
 * $oWriter->delete();
 * if ( $oWriter->isCached() ) {
 *     // this should not execute...
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterDatabase
 */
abstract class cacheWriterDatabase extends cacheWriter {
	
	/**
	 * Stores $_CacheDatabase, the database the cache table is located in
	 *
	 * @var string
	 * @access private
	 */
	private $_CacheDatabase;
	
	/**
	 * Stores $_CacheTable, the name of the cache table
	 *
	 * @var string
	 * @access private
	 */
	private $_CacheTable;
	
	/**
	 * Stores $_Loaded
	 *
	 * @var boolean
	 * @access private
	 */
	private $_Loaded;
	
	
	
	/**
	 * Returns a new database writer for the cache controller
	 *
	 * @param string $inCacheDatabase
	 * @param string $inCacheTable
	 * @param string $inCacheId
	 * @return cacheWriterDatabase
	 */
	function __construct($inCacheDatabase = null, $inCacheTable = null, $inCacheId = null) {
		parent::__construct($inCacheId);
		if ( $inCacheDatabase !== null ) {
			$this->setCacheDatabase($inCacheDatabase);
		}
		if ( $inCacheTable !== null ) {
			$this->setCacheTable($inCacheTable);
		}
	}
	
	
	
	/**
	 * @see cacheWriter::reset()
	 */
	function reset() {
		$this->_CacheDatabase = null;
		$this->_CacheTable = 'dataCache';
		$this->_Loaded = false;
		parent::reset();
	}
	
	/**
	 * @see cacheWriter::isCached()
	 */
	function isCached() {
		if ( !$this->getLoaded() ) {
			$this->load();
		}
		return $this->getLoaded(); 
	}
	
	/**
	 * @see cacheWriter::isExpired()
	 */
	function isExpired() {
		if ( !$this->getLoaded() ) {
			$this->load();
		}
		return (time()-strtotime($this->getUpdateDate()) > $this->getLifetime());
	}
	
	

	/**
	 * Returns $_CacheDatabase
	 *
	 * @return string
	 */
	function getCacheDatabase() {
		return $this->_CacheDatabase;
	}
	
	/**
	 * Set $_CacheDatabase to $inCacheDatabase
	 *
	 * @param string $inCacheDatabase
	 * @return cacheWriterDatabase
	 */
	function setCacheDatabase($inCacheDatabase) {
		if ( $inCacheDatabase !== $this->_CacheDatabase ) {
			$this->_CacheDatabase = $inCacheDatabase;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_CacheTable
	 *
	 * @return string
	 */
	function getCacheTable() {
		return $this->_CacheTable;
	}
	
	/**
	 * Set $_CacheTable to $inCacheTable
	 *
	 * @param string $inCacheTable
	 * @return cacheWriterDatabase
	 */
	function setCacheTable($inCacheTable) {
		if ( $inCacheTable !== $this->_CacheTable ) {
			$this->_CacheTable = $inCacheTable;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Loaded
	 *
	 * @return boolean
	 */
	function getLoaded() {
		return $this->_Loaded;
	}
	
	/**
	 * Set $_Loaded to $inLoaded
	 *
	 * @param boolean $inLoaded
	 * @return cacheWriterDatabase
	 */
	function setLoaded($inLoaded) {
		if ( $inLoaded !== $this->_Loaded ) {
			$this->_Loaded = $inLoaded;
			$this->setModified();
		}
		return $this;
	}
}