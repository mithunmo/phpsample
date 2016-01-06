<?php
/**
 * cacheWriterFile class
 * 
 * Stored in file.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterFile
 * @version $Rev: 744 $
 */


/**
 * cacheWriterFile
 * 
 * Provides a file level storage system for the cache controller.
 * Requires that the filesystem support file creation and modification times.
 * 
 * This writer supports building folder trees of cache data based on the cacheId
 * specified. The folder path can be as deep as you like, however it is dependent
 * on having sufficient key space in the cacheId. Sub-folders should improve I/O
 * performance as it splits lots of small files into more manageable chunks.
 * 
 * The depth and length are configurable, however the same settings should be used
 * to retrieve the cached object.
 * 
 * The cacheId is used as the file name and to build the folder structure when
 * UseSubFolders is true. The full cacheID is still used as the final file cache
 * name (this differs from earlier revisions of the cacheWriterFile class).
 * 
 * <code> 
 * // UseSubFolders = false;
 * $cacheId = 'MyCachedObject';
 * $fileName = FILESTORE/MyCachedObject.cache;
 * 
 * // UseSubFolders = true, & FolderDepth = 4
 * $cacheId = 'MyCachedObject';
 * $fileName = FILESTORE/M/y/C/a/MyCachedObject.cache;
 * </code>
 * 
 * Some examples of using the file writer storage backend:
 *  
 * <code>
 * // store an item using defaults
 * $oWriter = new cacheWriterFile();
 * $oWriter->setCacheId('MyCachedObject');
 * $oWriter->setData(new stdClass());
 * $oWriter->save();
 * 
 * // check if cached and fetch the previous item
 * $oWriter = new cacheWriterFile();
 * $oWriter->setCacheId('MyCachedObject');
 * if ( $oWriter->isCached() ) {
 *     $oWriter->load();
 *     $oWriter->getData();
 *     //...
 * }
 * 
 * // clear the cache
 * $oWriter = new cacheWriterFile();
 * $oWriter->setCacheId('MyCachedObject');
 * $oWriter->delete();
 * if ( $oWriter->isCached() ) {
 *     // this should not execute...
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterFile
 */
class cacheWriterFile extends cacheWriter {
	
	/**
	 * Stores $_FileStore, the main file store for this cache
	 *
	 * @var string
	 * @access private
	 */
	private $_FileStore;
	
	/**
	 * Stores $_FileExtension, the cache file extension
	 *
	 * @var string
	 * @access private
	 */
	private $_FileExtension;
	
	/**
	 * Stores $_UseSubFolders
	 * 
	 * Sets using sub-folders which should improve IO performance reducing the volume
	 * of files in a single folder, it is enabled by default
	 *
	 * @var boolean
	 * @access private
	 */
	private $_UseSubFolders;
	
	/**
	 * Stores $_FolderDepth
	 * 
	 * Sets how many sub-folders should be created for each cacheId.
	 * Sub-folders use the first X number of characters from the cacheId, default 3.
	 *
	 * @var integer
	 * @access private
	 */
	private $_FolderDepth;
	
	/**
	 * Stores $_SubFolderLength
	 * 
	 * Sets how long each sub-folder name should be. This is pulled from the cacheId,
	 * if it is too long you will run out of characters, default 1.
	 *
	 * @var integer
	 * @access private
	 */
	private $_SubFolderLength;
	
	/**
	 * Stores $_CacheFolderPermissionsMask, default 0700 - should be an octal number
	 *
	 * @var integer
	 * @access private
	 */
	private $_CacheFolderPermissionsMask;
	
	/**
	 * Stores $_CacheFilePermissionsMask, default 0600 - should be an octal number
	 *
	 * @var integer
	 * @access private
	 */
	private $_CacheFilePermissionsMask;
	
	
	
	/**
	 * Returns a new file writer for the cache controller
	 *
	 * @param string $inFileStore
	 * @param string $inCacheId
	 * @return cacheWriterFile
	 */
	function __construct($inFileStore = null, $inCacheId = null) {
		parent::__construct($inCacheId);
		if ( $inFileStore !== null ) {
			$this->setFileStore($inFileStore);
		}
	}
	
	/**
	 * Removes all cache files in $inFileStore folder, recursing if set, returns the number of files removed
	 *
	 * @param string $inFileStore
	 * @param string $inExtension
	 * @param boolean $inRecurse
	 * @return integer
	 * @throws cacheWriterFileStoreDoesNotExistException
	 * @throws cacheWriterFileStoreNotWritableException
	 * @static
	 */
	static function clearCache($inFileStore, $inExtension = 'cache', $inRecurse = true) {
		if ( !@file_exists($inFileStore) ) {
			throw new cacheWriterFileStoreDoesNotExistException($inFileStore);
		}
		if ( !@is_writable($inFileStore) ) {
			throw new cacheWriterFileStoreNotWritableException($inFileStore);
		}
		
		$fileCount = 0;
		foreach (new DirectoryIterator($inFileStore) as $file ) {
			if ( !$file->isDot() ) {
				if ( $file->isDir() && $inRecurse ) {
					$fileCount += self::clearCache($file->getPathname(), $inExtension, $inRecurse);
				} else {
					$arr = explode('.', $file->getPathname());
					$extension = array_pop($arr);
					if ( $extension == $inExtension ) {
						if ( @unlink($file->getPathname()) ) {
							$fileCount++;
						}
					}
				}
			}
		}
		$files = glob($inFileStore.system::getDirSeparator().'*.'.$inExtension);
		if ( count($files) == 0 ) {
			if ( !@rmdir($inFileStore) ) {
				systemLog::error("Tried to remove directory: $inFileStore but failed");
			}
		}
		return $fileCount;
	}
	
	
	
	/**
	 * @see cacheWriter::reset()
	 */
	function reset() {
		$this->_FileStore = system::getConfig()->getPathTemp().system::getDirSeparator().'cache';
		$this->_FileExtension = 'cache';
		$this->_UseSubFolders = true;
		$this->_FolderDepth = 3;
		$this->_SubFolderLength = 1;
		$this->_CacheFolderPermissionsMask = 0700;
		$this->_CacheFilePermissionsMask = 0600;
		parent::reset();
	}
	
	/**
	 * @see cacheWriter::delete()
	 */
	function delete() {
		$this->checkFileStoreLocation();
		
		$cacheFile = $this->getSubFolderPath().$this->getCacheFileName();
		if ( !@file_exists($cacheFile) ) {
			throw new cacheWriterCacheFileDoesNotExistException($this->getCacheId(), $cacheFile);
		}
		$res = @unlink($cacheFile);
		clearstatcache();
		return $res;
	}
	
	/**
	 * @see cacheWriter::isCached()
	 */
	function isCached() {
		$cacheFile = $this->getSubFolderPath().$this->getCacheFileName();
		return @file_exists($cacheFile);
	}
	
	/**
	 * @see cacheWriter::isExpired()
	 */
	function isExpired() {
		$cacheFile = $this->getSubFolderPath().$this->getCacheFileName();
		return ((time()-filemtime($cacheFile)) > $this->getLifetime());
	}
	
	/**
	 * @see cacheWriter::load()
	 */
	function load() {
		$this->checkFileStoreLocation();
		
		$cacheFile = $this->getSubFolderPath().$this->getCacheFileName();
		if ( !@file_exists($cacheFile) ) {
			throw new cacheWriterCacheFileDoesNotExistException($this->getCacheId(), $cacheFile);
		}
		if ( !@is_readable($cacheFile) ) {
			throw new cacheWriterCacheFileNotReadableException($this->getCacheId(), $cacheFile);
		}
		$this->setSerialiseData(@file_get_contents($cacheFile));
		$this->setCreateDate(date(system::getConfig()->getDatabaseDatetimeFormat(), filectime($cacheFile))); // filectime is change time, not create but will have to do
		$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat(), filemtime($cacheFile)));
		$this->setModified(false);
		return true;
	}
	
	/**
	 * @see cacheWriter::save()
	 */
	function save() {
		if ( $this->isModified() ) {
			if ( $this->_Modified ) {
				try {
					$this->checkFileStoreLocation();
				} catch ( cacheWriterFileStoreDoesNotExistException $e ) {
					$this->createFileStoreLocation();
				}
				
				$bytes = file_put_contents($this->getSubFolderPath().$this->getCacheFileName(), $this->getSerialiseData(), LOCK_EX);
				@chmod($this->getSubFolderPath().$this->getCacheFileName(), $this->getCacheFilePermissionsMask());
				$this->setModified(false);
				return ($bytes > 0);
			}
		}
		return false;
	}
	
	/**
	 * Runs garbage collection of the current cache folder, will recurse if their are additional folders
	 * 
	 * @see cacheWriter->runGc()
	 */
	function runGc() {
		return (self::clearCache($this->getSubFolderPath(), $this->getFileExtension(), true)>0);
	}
	
	/**
	 * Checks that the file store exists and can be accessed / written to
	 *
	 * @return void
	 * @throws cacheWriterFileException
	 * @throws cacheWriterFileStoreDoesNotExistException
	 * @throws cacheWriterFileStoreNotReadableException
	 * @throws cacheWriterFileStoreNotWritableException
	 */
	function checkFileStoreLocation() {
		if ( !@file_exists($this->getSubFolderPath()) ) {
			throw new cacheWriterFileStoreDoesNotExistException($this->getSubFolderPath());
		}
		if ( !@is_readable($this->getSubFolderPath()) ) {
			throw new cacheWriterFileStoreNotReadableException($this->getSubFolderPath());
		}
		if ( !@is_writable($this->getSubFolderPath()) ) {
			throw new cacheWriterFileStoreNotWritableException($this->getSubFolderPath());
		}
	}
	
	/**
	 * Creates the cache file location if it does not exist
	 *
	 * @return void
	 * @throws cacheWriterFileException
	 */
	function createFileStoreLocation() {
		$path = $this->getSubFolderPath();
		if ( !@mkdir($path, $this->getCacheFolderPermissionsMask(), true) ) {
			throw new cacheWriterFileException("Failed to create file store path ($path), check permissions");
		}
	}
	
	
	
	/**
	 * Returns the sub-folder path
	 *
	 * @return string
	 * @throws cacheWriterFileException
	 */
	function getSubFolderPath() {
		if ( !$this->getCacheId() ) {
			throw new cacheWriterFileException('No cacheId set, unable to build folder path');
		}
		
		$path = array($this->getFileStore());
		if ( $this->getUseSubFolders() ) {
			for ( $i=0; $i<$this->getFolderDepth(); $i++ ) {
				$path[] = strtolower(substr($this->getCacheId(), $i*$this->getSubFolderLength(), $this->getSubFolderLength()));
			}
		}
		return implode(system::getDirSeparator(), $path).system::getDirSeparator();
	}
	
	/**
	 * Returns the filename for the cached object
	 *
	 * @return string
	 * @throws cacheWriterFileException
	 */
	function getCacheFileName() {
		if ( !$this->getCacheId() ) {
			throw new cacheWriterFileException('No cacheId set, unable to create cache file name');
		}
		return $this->getCacheId().'.'.$this->getFileExtension();
	}
	
	/**
	 * Returns $_FileStore
	 *
	 * @return string
	 */
	function getFileStore() {
		return $this->_FileStore;
	}
	
	/**
	 * Set $_FileStore to $inFileStore
	 *
	 * @param string $inFileStore
	 * @return cacheWriterFile
	 */
	function setFileStore($inFileStore) {
		if ( $inFileStore !== $this->_FileStore ) {
			$this->_FileStore = $inFileStore;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_FileExtension
	 *
	 * @return string
	 */
	function getFileExtension() {
		return $this->_FileExtension;
	}
	
	/**
	 * Set $_FileExtension to $inFileExtension
	 *
	 * @param string $inFileExtension
	 * @return cacheWriterFile
	 */
	function setFileExtension($inFileExtension) {
		if ( $inFileExtension !== $this->_FileExtension ) {
			$this->_FileExtension = $inFileExtension;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_UseSubFolders
	 *
	 * @return boolean
	 */
	function getUseSubFolders() {
		return $this->_UseSubFolders;
	}
	
	/**
	 * Set $_UseSubFolders to $inUseSubFolders
	 *
	 * @param boolean $inUseSubFolders
	 * @return cacheWriterFile
	 */
	function setUseSubFolders($inUseSubFolders) {
		if ( $inUseSubFolders !== $this->_UseSubFolders ) {
			$this->_UseSubFolders = $inUseSubFolders;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_FolderDepth
	 *
	 * @return integer
	 */
	function getFolderDepth() {
		return $this->_FolderDepth;
	}
	
	/**
	 * Set $_FolderDepth to $inFolderDepth
	 *
	 * @param integer $inFolderDepth
	 * @return cacheWriterFile
	 */
	function setFolderDepth($inFolderDepth) {
		if ( $inFolderDepth !== $this->_FolderDepth ) {
			$this->_FolderDepth = $inFolderDepth;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_SubFolderLength
	 *
	 * @return integer
	 */
	function getSubFolderLength() {
		return $this->_SubFolderLength;
	}
	
	/**
	 * Set $_SubFolderLength to $inSubFolderLength
	 *
	 * @param integer $inSubFolderLength
	 * @return cacheWriterFile
	 */
	function setSubFolderLength($inSubFolderLength) {
		if ( $inSubFolderLength !== $this->_SubFolderLength ) {
			$this->_SubFolderLength = $inSubFolderLength;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_CacheFolderPermissionsMask
	 *
	 * @return integer
	 */
	function getCacheFolderPermissionsMask() {
		return $this->_CacheFolderPermissionsMask;
	}
	
	/**
	 * Set $_CacheFolderPermissionsMask to $inCacheFolderPermissionsMask
	 *
	 * @param integer $inCacheFolderPermissionsMask
	 * @return cacheWriterFile
	 */
	function setCacheFolderPermissionsMask($inCacheFolderPermissionsMask) {
		if ( $inCacheFolderPermissionsMask !== $this->_CacheFolderPermissionsMask ) {
			$this->_CacheFolderPermissionsMask = $inCacheFolderPermissionsMask;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_CacheFilePermissionsMask
	 *
	 * @return integer
	 */
	function getCacheFilePermissionsMask() {
		return $this->_CacheFilePermissionsMask;
	}
	
	/**
	 * Set $_CacheFilePermissionsMask to $inCacheFilePermissionsMask
	 *
	 * @param integer $inCacheFilePermissionsMask
	 * @return cacheWriterFile
	 */
	function setCacheFilePermissionsMask($inCacheFilePermissionsMask) {
		if ( $inCacheFilePermissionsMask !== $this->_CacheFilePermissionsMask ) {
			$this->_CacheFilePermissionsMask = $inCacheFilePermissionsMask;
			$this->setModified();
		}
		return $this;
	}
}