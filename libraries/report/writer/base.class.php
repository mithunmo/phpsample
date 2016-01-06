<?php
/**
 * reportWriterBase
 * 
 * Stored in reportWriterBase.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportWriterBase
 * @version $Rev: 771 $
 */


/**
 * reportWriterBase
 * 
 * The report writer component converts the reportData object into an
 * output format for display. This data can itself be cached (depending
 * on the report useCache setting).
 * 
 * Each writer can implement whatever additional methods are required
 * to aid in producing the output format.
 * 
 * @package scorpio
 * @subpackage report
 * @category reportWriterBase
 */
abstract class reportWriterBase {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Report
	 *
	 * @var reportBase
	 * @access protected
	 */
	protected $_Report;
	
	/**
	 * Stores $_FileStore
	 *
	 * @var string
	 * @access protected
	 */
	protected $_FileStore;
	
	/**
	 * Stores $_Extension
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Extension;
	
	/**
	 * Stores $_MimeType
	 *
	 * @var string
	 * @access protected
	 */
	protected $_MimeType;
	
	
	
	/**
	 * Creates a new writer instance
	 *
	 * @param reportBase $inReport
	 */
	function __construct(reportBase $inReport) {
		$this->reset();
		$this->setReport($inReport);
		$this->initialise();
	}
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_Report = null;
		$this->_FileStore = null;
		$this->_Extension = null;
		$this->_MimeType = 'application/octet-stream';
		$this->setModified(false);
	}
	
	/**
	 * Initialises the object by setting extension, mimetype and other settings
	 *
	 * @return void
	 * @abstract 
	 */
	function initialise() {
		
	}
	
	/**
	 * Compiles the report into the specified ouput
	 *
	 * @return reportWriterBase 
	 */
	function compile() {
		$this->buildFileStore();
		
		$file = $this->getFullPathToOutputFile();
		if ( $this->getReport()->useCache() ) {
			if ( file_exists($file) && is_readable($file) ) {
				if ( (time()-filemtime($file)) <= $this->getReport()->getCacheLifetime() ) {
					return true;
				} else {
					// remove cache and rebuild
					unlink($file);
				}
			}
		}
		
		$this->_compile();
		
		return $this;
	}
	
	/**
	 * Does the custom compile work into the specified format
	 * 
	 * @return void
	 * @throws reportWriterException
	 * @abstract 
	 */
	abstract function _compile();
	
	/**
	 * Builds the file store if it does not exist
	 *
	 * @return boolean, null if already exists
	 */
	function buildFileStore() {
		if ( !file_exists($this->getFileStore()) ) {
			return mkdir($this->getFileStore(), 0755, true);
		}
		return null;
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
	 * @return reportWriterBase
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns the current report instance
	 *
	 * @return reportBase
	 */
	function getReport() {
		return $this->_Report;
	}
	
	/**
	 * Set the report instance
	 *
	 * @param reportBase $inReport
	 * @return reportWriterBase
	 */
	function setReport(reportBase $inReport) {
		if ( $inReport !== $this->_Report ) {
			$this->_Report = $inReport;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns a filename
	 *
	 * @return string
	 */
	function getFilename() {
		return $this->getReport()->getCacheId().($this->getExtension() ? '.'.$this->getExtension() : '');
	}

	/**
	 * Returns the file store to use, generating one if not set from the cache
	 *
	 * @return string
	 */
	function getFileStore() {
		if ( !$this->_FileStore ) {
			$this->_FileStore = reportManager::buildFileStorePath($this->getReport());
		}
		return $this->_FileStore;
	}
	
	/**
	 * Returns the full path to the output file
	 *
	 * @return string
	 */
	function getFullPathToOutputFile() {
		return $this->getFileStore().system::getDirSeparator().$this->getFilename();
	}
	
	/**
	 * Set the active filestore for caching writer files
	 *
	 * @param string $inFileStore
	 * @return reportWriterBase
	 */
	function setFileStore($inFileStore) {
		if ( $inFileStore !== $this->_FileStore ) {
			$this->_FileStore = $inFileStore;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the file extension used by this writer
	 *
	 * @return string
	 */
	function getExtension() {
		return $this->_Extension;
	}
	
	/**
	 * Set $_Extension to $inExtension
	 *
	 * @param string $inExtension
	 * @return reportWriterBase
	 */
	function setExtension($inExtension) {
		if ( $inExtension !== $this->_Extension ) {
			$this->_Extension = $inExtension;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the mimetype for this report writer
	 *
	 * @return string
	 */
	function getMimeType() {
		return $this->_MimeType;
	}
	
	/**
	 * Set $_MimeType to $inMimeType
	 *
	 * @param string $inMimeType
	 * @return reportWriterBase
	 */
	function setMimeType($inMimeType) {
		if ( $inMimeType !== $this->_MimeType ) {
			$this->_MimeType = $inMimeType;
			$this->setModified();
		}
		return $this;
	}
}