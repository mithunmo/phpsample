<?php
/**
 * cacheWriter class
 *
 * Stored in writer.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cache
 * @category cacheWriter
 * @version $Rev: 697 $
 */


/**
 * cacheWriter class
 * 
 * Provides the shared abstract writer interface for the caching system.
 * This encompasses all of the methods required for writing cache data
 * to a variety of sources. cacheWriter instances are used within the 
 * main {@link cacheController} object.
 * 
 * This class requires a concrete implementation for example {@link cacheWriterFile}.
 * 
 * @package scorpio
 * @subpackage cache
 * @category cacheWriter
 * @abstract
 */
abstract class cacheWriter {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_CacheId
	 *
	 * @var string
	 * @access private
	 */
	private $_CacheId;
	/**
	 * Stores $_Data
	 *
	 * @var mixed
	 * @access private
	 */
	private $_Data;
	/**
	 * Stores $_CreateDate
	 *
	 * @var datetime
	 * @access private
	 */
	private $_CreateDate;
	/**
	 * Stores $_UpdateDate
	 *
	 * @var datetime
	 * @access private
	 */
	private $_UpdateDate;
	/**
	 * Stores $_Lifetime
	 *
	 * @var integer
	 * @access private
	 */
	private $_Lifetime;
	
	
	
	/**
	 * Returns new instance of cache writer
	 * 
	 * @param string $inCacheId
	 * @return cacheWriter
	 */
	function __construct($inCacheId = null) {
		$this->reset();
		if ( $inCacheId !== null ) {
			$this->setCacheId($inCacheId);
		}
	}
	
	/**
	 * Reset controller to defaults
	 *
	 * @return void;
	 */
	function reset() {
		$this->_CacheId = null;
		$this->_Data = null;
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat());
		$this->_UpdateDate = date(system::getConfig()->getDatabaseDatetimeFormat());
		$this->_Lifetime = 3600; // one hour
	}
	
	/**
	 * Returns true if the requested object is cached already
	 *
	 * @return boolean
	 * @abstract 
	 */
	abstract function isCached();
	
	/**
	 * Returns true if the cache has reached or exceeded the lifetime
	 *
	 * @return boolean
	 * @abstract
	 */
	abstract function isExpired();
	
	/**
	 * Loads the cache object into the writer
	 *
	 * @return boolean
	 * @abstract
	 */
	abstract function load();
	
	/**
	 * Saves the data to the data destination
	 *
	 * @return boolean
	 * @abstract 
	 */
	abstract function save();
	
	/**
	 * Deletes the cache data
	 *
	 * @return boolean
	 * @abstract
	 */
	abstract function delete();
	
	/**
	 * Executes garbage collection to flush the cache of the specified resource
	 *
	 * @return boolean
	 * @abstract
	 */
	abstract function runGc();
	
	
	
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
	 * @return cacheWriter
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns $_CacheId
	 *
	 * @return string
	 */
	function getCacheId() {
		return $this->_CacheId;
	}
	
	/**
	 * Set $_CacheId to $inCacheId
	 *
	 * @param string $inCacheId
	 * @return cacheWriter
	 */
	function setCacheId($inCacheId) {
		if ( $inCacheId !== $this->_CacheId ) {
			$this->_CacheId = $inCacheId;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_Data and unserialises it first
	 *
	 * @return mixed
	 */
	function getData() {
		if ( !$this->_Data ) {
			$this->load();
		}
		return unserialize($this->_Data);
	}
	
	/**
	 * Returns the data as the internal serialised string
	 *
	 * @return string
	 */
	function getSerialiseData() {
		return $this->_Data;
	}
	
	/**
	 * Set $_Data to $inData, all data is serialised first
	 *
	 * @param mixed $inData
	 * @return cacheWriter
	 */
	function setData($inData) {
		$inData = serialize($inData);
		if ( $inData !== $this->_Data ) {
			$this->_Data = $inData;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Sets already serialised data into the object
	 *
	 * @param string $inData
	 * @return cacheWriter
	 */
	function setSerialiseData($inData) {
		if ( $inData !== $this->_Data ) {
			$this->_Data = $inData;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_CreateDate
	 *
	 * @return datetime
	 */
	function getCreateDate() {
		return $this->_CreateDate;
	}
	
	/**
	 * Set $_CreateDate to $inCreateDate
	 *
	 * @param datetime $inCreateDate
	 * @return cacheWriter
	 */
	function setCreateDate($inCreateDate) {
		if ( $inCreateDate !== $this->_CreateDate ) {
			$this->_CreateDate = $inCreateDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_UpdateDate
	 *
	 * @return datetime
	 */
	function getUpdateDate() {
		return $this->_UpdateDate;
	}
	
	/**
	 * Set $_UpdateDate to $inUpdateDate
	 *
	 * @param datetime $inUpdateDate
	 * @return cacheWriter
	 */
	function setUpdateDate($inUpdateDate) {
		if ( $inUpdateDate !== $this->_UpdateDate ) {
			$this->_UpdateDate = $inUpdateDate;
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
	 * @return cacheWriter
	 */
	function setLifetime($inLifetime) {
		if ( $inLifetime !== $this->_Lifetime ) {
			$this->_Lifetime = $inLifetime;
			$this->setModified();
		}
		return $this;
	}
}