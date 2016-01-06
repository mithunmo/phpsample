<?php
/**
 * mvcFileObject.class.php
 * 
 * mvcFileObject class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileObject
 * @version $Rev: 841 $
 */


/**
 * mvcFileObject class
 * 
 * Represents an uploaded file, can also contain the raw uploaded data.
 * 
 * <code>
 * $oFile = new mvcFileObject();
 * $oFile->setName()->setMimeType();
 * </code>
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileObject
 */
class mvcFileObject {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Name
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Name;
	
	/**
	 * Stores $_MimeType
	 *
	 * @var string
	 * @access protected
	 */
	protected $_MimeType;
	
	/**
	 * Stores $_FileSize
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_FileSize;
	
	/**
	 * Stores $_TmpName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_TmpName;
	
	/**
	 * Stores $_Extension
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Extension;
	
	/**
	 * Stores $_RawFileData
	 *
	 * @var string
	 * @access protected
	 */
	protected $_RawFileData;
	
	/**
	 * Stores $_OriginalName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_OriginalName;
	
	/**
	 * Stores $_FilePath
	 *
	 * @var string
	 * @access protected
	 */
	protected $_FilePath;

	/**
	 * Stores $_UploadKey
	 *
	 * @var string
	 * @access protected
	 */
	protected $_UploadKey;



	/**
	 * Creates a new file object
	 *
	 * @return mvcFileObject
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
		$this->_Name = null;
		$this->_MimeType = 'application/octet-stream';
		$this->_FileSize = 0;
		$this->_TmpName = null;
		$this->_Extension = null;
		$this->_RawFileData = null;
		$this->_OriginalName = null;
		$this->_FilePath = null;
		$this->_UploadKey = null;
		$this->setModified(false);
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
	 * @return mvcFileObject
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns $_Name
	 *
	 * @return string
	 */
	function getName() {
		return $this->_Name;
	}
	
	/**
	 * Set $_Name to $inName
	 *
	 * @param string $inName
	 * @return mvcFileObject
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_MimeType
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
	 * @return mvcFileObject
	 */
	function setMimeType($inMimeType) {
		if ( $inMimeType !== $this->_MimeType ) {
			$this->_MimeType = $inMimeType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_FileSize
	 *
	 * @return integer
	 */
	function getFileSize() {
		return $this->_FileSize;
	}
	
	/**
	 * Set $_FileSize to $inFileSize
	 *
	 * @param integer $inFileSize
	 * @return mvcFileObject
	 */
	function setFileSize($inFileSize) {
		if ( $inFileSize !== $this->_FileSize ) {
			$this->_FileSize = $inFileSize;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_TmpName
	 *
	 * @return string
	 */
	function getTmpName() {
		return $this->_TmpName;
	}
	
	/**
	 * Set $_TmpName to $inTmpName
	 *
	 * @param string $inTmpName
	 * @return mvcFileObject
	 */
	function setTmpName($inTmpName) {
		if ( $inTmpName !== $this->_TmpName ) {
			$this->_TmpName = $inTmpName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Extension
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
	 * @return mvcFileObject
	 */
	function setExtension($inExtension) {
		if ( $inExtension !== $this->_Extension ) {
			$this->_Extension = $inExtension;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_RawFileData
	 *
	 * @return string
	 */
	function getRawFileData() {
		return $this->_RawFileData;
	}
	
	/**
	 * Set $_RawFileData to $inRawFileData
	 *
	 * @param string $inRawFileData
	 * @return mvcFileObject
	 */
	function setRawFileData($inRawFileData) {
		if ( $inRawFileData !== $this->_RawFileData ) {
			$this->_RawFileData = $inRawFileData;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_OriginalName
	 *
	 * @return string
	 */
	function getOriginalName() {
		return $this->_OriginalName;
	}
	
	/**
	 * Set $_OriginalName to $inOriginalName
	 *
	 * @param string $inOriginalName
	 * @return mvcFileObject
	 */
	function setOriginalName($inOriginalName) {
		if ( $inOriginalName !== $this->_OriginalName ) {
			$this->_OriginalName = $inOriginalName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_FilePath
	 *
	 * @return string
	 */
	function getFilePath() {
		return $this->_FilePath;
	}
	
	/**
	 * Set $_FilePath to $inFilePath
	 *
	 * @param string $inFilePath
	 * @return mvcFileObject
	 */
	function setFilePath($inFilePath) {
		if ( $inFilePath !== $this->_FilePath ) {
			$this->_FilePath = $inFilePath;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns a URI path that is relative to the supplied base directory
	 * 
	 * For example, the file is uploaded to (full-path):
	 * /home/user/websites/base/themes/shared/assets/app/media/folder/file.extn
	 * 
	 * The base to the web accessible folder is:
	 * /home/users/websites/base
	 * 
	 * This method will return:
	 * /themes/shared/assets/app/media/folder/file.extn
	 *
	 * @param string $inBaseDirectory
	 * @return string
	 */
	function getRelativeUriPath($inBaseDirectory) {
		return str_replace(
			'\\', '/', str_replace(utilityStringFunction::cleanDirSlashes($inBaseDirectory), '', $this->getFilePath().'/'.$this->getName())
		);
	}

	/**
	 * Returns the value of $_UploadKey; the original key assigned to this file
	 *
	 * @return string
	 */
	function getUploadKey() {
		return $this->_UploadKey;
	}

	/**
	 * Set $_UploadKey to $inUploadKey
	 *
	 * @param string $inUploadKey
	 * @return mvcFileObject
	 */
	function setUploadKey($inUploadKey) {
		if ( $inUploadKey !== $this->_UploadKey ) {
			$this->_UploadKey = $inUploadKey;
			$this->setModified();
		}
		return $this;
	}
}