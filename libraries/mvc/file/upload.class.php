<?php
/**
 * mvcFileUpload.class.php
 * 
 * mvcFileUpload class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileUpload
 * @version $Rev: 843 $
 */


/**
 * mvcFileUpload class
 * 
 * Provides an interface to the PHP _FILES array to help manipulate file uploads
 * in a consistent manner. Can handle arrays of files as well as single items.
 * File arrays must be uploaded as FieldName[].
 * 
 * As the files are processed, they are converted into individual mvcFileObjects.
 * These are added to a set, and this is returned by the call to process(). This
 * set can be used to obtain the file data.
 * 
 * All file uploads are screened and filenames automatically checked. The original
 * name can be preserved (within limits), otherwise it will be used with the time
 * to create a MD5 hash.
 * 
 * Various options can be set to control how this object handles the uploads. These
 * include: deferring actual file copying which will automatically cause the file
 * data to be placed in the mvcFileObject instead, specifying the file store AND
 * sub-folder pattern for the store, checking folder permissions at runtime etc.
 * 
 * This class has an extensive exception model covering most incidents of bad
 * configuration or problems. When uploading multiple files, the upload will
 * process all files in the batch and then throw an exception with the details
 * of all the files that failed.
 * 
 * <code>
 * // set options via constructor
 * $oFileUpload = new mvcFileUpload(
 *     array(
 *         mvcFileUpload::OPTION_FILE_STORE => system::getConfig()->getPathTemp().'/appName/fileStore',
 *         mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => date('/Y/m'),
 *         mvcFileUpload::OPTION_FIELD_NAME => 'FileUploadFieldNameInHTMLForm',
 *     )
 * );
 * // NOTE: initialise() must always be called BEFORE process();
 * $oFileUpload->initialise();
 * $oFileSet = $oFileUpload->process();
 * foreach ( $oFileSet as $oFile ) {
 *     // now do something with the files
 *     // each file as an instance of mvcFileObject
 * }
 * 
 * // set options via methods
 * $oFileUpload = new mvcFileUpload();
 * $oFileUpload->setFileStore(system::getConfig()->getPathTemp().'/appName/fileStore');
 * $oFileUpload->setSubFolderFormat(date('/Y/m'));
 * $oFileUpload->setFieldName('FileUploadFieldNameInHTMLForm');
 * $oFileUpload->initialise();
 * $oFileSet = $oFileUpload->process();
 * foreach ( $oFileSet as $oFile ) {
 *     // now do something with the files
 *     // each file is an instance of mvcFileObject
 * }
 * 
 * // options can be set via a semi-fluent interface
 * $oFileUpload = new mvcFileUpload();
 * $oFileUpload->setFileStore(
 *     system::getConfig()->getPathTemp().'/appName/fileStore'
 * )->setSubFolderFormat(
 *     date('/Y/m')
 * )->setFieldName(
 *     'FileUploadFieldNameInHTMLForm'
 * );
 * </code>
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileUpload
 */
class mvcFileUpload {

	/**
	 * Should the filestore be automatically created, default true
	 *
	 * @var boolean
	 */
	const OPTION_AUTO_CREATE_FILESTORE = 'file.store.auto-create';
	/**
	 * Check if the folder exists and is writable, default true
	 *
	 * @var boolean
	 */
	const OPTION_CHECK_PERMISSIONS = 'file.store.check-permissions';
	/**
	 * The form field name to look for in _FILES, default FileUpload
	 *
	 * @var string
	 */
	const OPTION_FIELD_NAME = 'field.name';
	/**
	 * The full path to the file store, default /data/filestore
	 *
	 * @var string
	 */
	const OPTION_FILE_STORE = 'file.store';
	/**
	 * Store the raw file data in the mvcFileUpload objects, default false
	 * 
	 * @var boolean
	 */
	const OPTION_STORE_RAW_DATA = 'file.store-raw-data';
	/**
	 * A sub-folder to store the files in, default /YYYY/MM/DD
	 * requires leading /
	 *
	 * @var string
	 */
	const OPTION_SUB_FOLDER_FORMAT = 'file.store.sub-folder-format';
	/**
	 * Keep original filename, or hash it, default false
	 *
	 * @var boolean
	 */
	const OPTION_USE_ORIGINAL_NAME = 'file.use-original-name';
	/**
	 * Should upload files be moved immediately, default true
	 * Setting to false automatically enables internal storage
	 *
	 * @var boolean
	 */
	const OPTION_WRITE_IMMEDIATE = 'file.store.write-immediately';

	/**
	 * Add this to the beginning of any uploaded file when creating it e.g. for chunked uploads
	 *
	 * @var string
	 */
	const OPTION_ADD_FILENAME_PREFIX = 'file.prefix';

	/**
	 * Add this to the end of any uploaded file when creating it e.g. for chunked uploads
	 *
	 * @var string
	 */
	const OPTION_ADD_FILENAME_SUFFIX = 'file.suffix';

	
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;

	/**
	 * Stores $_OptionsSet
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_OptionsSet;
	
	/**
	 * Stores $_UploadedFiles
	 *
	 * @var mvcFileSet
	 * @access protected
	 */
	protected $_UploadedFiles;
	
	
	
	/**
	 * Creates a new mvcFileUpload object; optionally sets up options
	 *
	 * @param array $inOptions
	 * @return mvcFileUpload
	 */
	function __construct(array $inOptions = array()) {
		$this->reset();
		if ( count($inOptions) > 0 ) {
			$this->setOptions($inOptions);
		}
	}
	
	
	
	/**
	 * Resets object to defaults
	 *
	 * @return void
	 */
	function reset() {
		$this->_OptionsSet = null;
		$this->_UploadedFiles = new mvcFileSet();
		$this->getOptionsSet()->setOptions(
			array(
				self::OPTION_AUTO_CREATE_FILESTORE => true,
				self::OPTION_CHECK_PERMISSIONS => true,
				self::OPTION_FIELD_NAME => 'FileUpload',
				self::OPTION_FILE_STORE => system::getConfig()->getPathData().system::getDirSeparator().'fileStore',
				self::OPTION_STORE_RAW_DATA => false,
				self::OPTION_SUB_FOLDER_FORMAT => date('/Y/m/d'),
				self::OPTION_USE_ORIGINAL_NAME => false,
				self::OPTION_WRITE_IMMEDIATE => true,
			)
		);
		$this->getOptionsSet()->setModified(false);
		$this->setModified(false);
	}
	
	/**
	 * Initialises the mvcFileUpload object
	 * 
	 * @return mvcFileUpload
	 * @throws mvcFileException
	 * @deprecated Use {@link mvcFileUpload::initialise()}
	 * @since 2010-01-08
	 */
	function init() {
		return $this->initialise();
	}

	/**
	 * Initialises the mvcFileUpload object
	 *
	 * @return mvcFileUpload
	 * @throws mvcFileException
	 */
	function initialise() {
	  if ( $this->getCheckFolderPermissions() ) { 
			try {
				$this->checkFolderPermissions();
			} catch ( mvcFileFileStoreDoesNotExistException $e ) {
				if ( $this->getAutoCreateFileStore() ) {
					$this->buildFileStore();
				} else {
					throw $e;
				}
			}
		}
		if ( $this->getAutoCreateFileStore() ) {
			$this->buildSubFolders();
		}
		return $this;
	}
	
	/**
	 * Processes the _FILES array of data
	 *
	 * @return mvcFileSet
	 * @throws mvcFileException
	 */
	function process() {
		if ( isset($_FILES) && count($_FILES) > 0 ) {
			if ( is_array($_FILES[$this->getFieldName()]['name']) ) {
				$this->_processArray();
			} else {
				$this->_processSingleFile();
			}
		}
		return $this->getUploadedFiles();
	}
	
	/**
	 * Handles a single uploaded file field
	 *
	 * @return void
	 * @throws mvcFileException
	 */
	protected function _processSingleFile() {
		$this->checkUploadedFile($_FILES[$this->getFieldName()]['error'], $_FILES[$this->getFieldName()]['name']);
		$this->checkIsValidUploadedFile($_FILES[$this->getFieldName()]['tmp_name'], $_FILES[$this->getFieldName()]['name']);
		
		$oFile = $this->createUploadFile(
			$_FILES[$this->getFieldName()]['name'],
			$_FILES[$this->getFieldName()]['type'],
			$_FILES[$this->getFieldName()]['size'],
			$_FILES[$this->getFieldName()]['tmp_name']
		);
		
		$this->getUploadedFiles()->setFile($oFile);
	}
	
	/**
	 * Handles arrays of uploaded files, note that while this method
	 * does throw an exception, it only does so AFTER the array of
	 * files has been processed. If any Exception is throw, these will
	 * not be handled.
	 *
	 * @return void
	 * @throws mvcFileUploadArrayException
	 * @throws mvcFileUploadException
	 * @throws mvcFileException
	 */
	protected function _processArray() {
		$failed = array();
		foreach ( $_FILES[$this->getFieldName()]['error'] as $key => $errorCode ) {
			try {
				$this->checkUploadedFile($errorCode, $_FILES[$this->getFieldName()]['name'][$key]);
				$this->checkIsValidUploadedFile($_FILES[$this->getFieldName()]['tmp_name'][$key], $_FILES[$this->getFieldName()]['name'][$key]);
				
				$oFile = $this->createUploadFile(
					$_FILES[$this->getFieldName()]['name'][$key],
					$_FILES[$this->getFieldName()]['type'][$key],
					$_FILES[$this->getFieldName()]['size'][$key],
					$_FILES[$this->getFieldName()]['tmp_name'][$key],
					$key
				);
				
				$this->getUploadedFiles()->setFile($oFile);
			} catch ( mvcFileException $e ) {
				$failed[$_FILES[$this->getFieldName()]['name'][$key]] = $e->getMessage();
			}
		}
		
		if ( count($failed) > 0 ) {
			$messages = array("Errors during file upload:");
			foreach ( $failed as $name => $reason ) {
				$messages[] = "$name : $reason";
			}
			throw new mvcFileUploadArrayException(implode("\n", $messages));
		}
	}
	
	/**
	 * Checks if the file upload completed successfully
	 *
	 * @param integer $inErrorCode
	 * @param string $inOriginalName
	 * @return void
	 */
	protected function checkUploadedFile($inErrorCode, $inOriginalName) {
		if ( !$inErrorCode == UPLOAD_ERR_OK ) {
			if ( $inErrorCode == UPLOAD_ERR_NO_FILE ) {
				throw new mvcFileUploadNoFileUploadedException($this->translateErrorCode($inErrorCode).' ('.$inOriginalName.')');
			} else {
				throw new mvcFileUploadException($this->translateErrorCode($inErrorCode).' ('.$inOriginalName.')');
			}
		}
	}
	
	/**
	 * Checks if the file ($inOriginalFileName) is a valid uploaded file, requires the temporary file name
	 *
	 * @param string $inTemporaryFileName
	 * @param string $inOriginalFileName
	 */
	protected function checkIsValidUploadedFile($inTemporaryFileName, $inOriginalFileName) {
		if ( !is_uploaded_file($inTemporaryFileName) ) {
			throw new mvcFileUploadInvalidException('Upload file named ('.$inOriginalFileName.') illegally uploaded');
		}
	}
	
	/**
	 * Creates a target file name from the original filename, or builds an automatic name if set
	 * 
	 * Note: original filename will be sanitised for URI safety.
	 * Note 2: the optional prefix/suffix will be added if set.
	 *
	 * @param string $inOriginalFilename
	 * @return string
	 */
	protected function createUploadFilename($inOriginalFilename) {
		$extn = $this->getUploadedFileExtension($inOriginalFilename);
		if ( $this->getUseOriginalFileName() ) {
			$filename = utilityStringFunction::normaliseStringCharactersForUri(
				str_ireplace('.'.$extn, '', basename($inOriginalFilename)), '_'
			);
		} else {
			$filename = md5($inOriginalFilename.'.'.microtime(true));
		}

		if ( $this->getAddFilenamePrefix() && strlen($this->getAddFilenamePrefix()) > 0 ) {
			$filename = $this->getAddFilenamePrefix().$filename;
		}
		if ( $this->getAddFilenameSuffix() && strlen($this->getAddFilenameSuffix()) > 0 ) {
			$filename .= $this->getAddFilenameSuffix();
		}

		return $filename.'.'.$extn;
	}
	
	/**
	 * Returns the original files extension, lower cased
	 *
	 * @param string $inOriginalFilename
	 * @return string
	 */
	protected function getUploadedFileExtension($inOriginalFilename) {
		return strtolower(substr(strrchr($inOriginalFilename, '.'), 1));
	}
	
	/**
	 * Creates an mvcFileObject to represent the uploaded file
	 *
	 * @param string $inFileName
	 * @param string $inFileType
	 * @param integer $inFileSize
	 * @param string $inTmpName
	 * @param string $inUploadKey Array key that identifies this file
	 * @return mvcFileObject 
	 */
	protected function createUploadFile($inFileName, $inFileType, $inFileSize, $inTmpName, $inUploadKey = null) {
		$target = $this->getUploadTarget().system::getDirSeparator().$this->createUploadFilename($inFileName);
		
		$oFile = new mvcFileObject();
		$oFile->setExtension($this->getUploadedFileExtension($inFileName));
		$oFile->setFilePath(dirname($target));
		$oFile->setFileSize($inFileSize);
		$oFile->setMimeType($inFileType);
		$oFile->setName(basename($target));
		$oFile->setOriginalName($inFileName);
		$oFile->setTmpName($inTmpName);
		$oFile->setUploadKey($inUploadKey);
		
		if ( $this->getWriteFilesImmediately() ) {
			if ( move_uploaded_file($inTmpName, $target) ) {
				chmod($target, 0644);
			} else {
				throw new mvcFileUploadMoveUploadedFileException("Failed to create target file ($target)");
			}
		}
		if ( $this->getStoreRawFileDataInResult() ) {
			$oFile->setRawFileData(file_get_contents($inTmpName));
		}
		return $oFile;
	}
	
	/**
	 * Checks if the target folder can be written to by the current process, throws exceptions on error
	 *
	 * @return void
	 * @throws mvcFileFileStoreDoesNotExistException
	 * @throws mvcFileFileStroreNotReadableException
	 * @throws mvcFileFileStoreNotWritableException
	 */
	protected function checkFolderPermissions() {
		if ( !file_exists($this->getFileStore()) ) {
			throw new mvcFileFileStoreDoesNotExistException($this->getFileStore());
		}
		if ( !is_readable($this->getFileStore())) {
			throw new mvcFileFileStoreNotReadableException($this->getFileStore());
		}
		if ( !is_writable($this->getFileStore()) ) {
			throw new mvcFileFileStoreNotWritableException($this->getFileStore());
		}
	}
	
	/**
	 * Attempts to create the file store folder
	 *
	 * @return void
	 * @throws mvcFileFileStoreBuildFailedException
	 */
	protected function buildFileStore() {
		if ( !mkdir(utilityStringFunction::cleanDirSlashes($this->getFileStore()), 0755, true) ) {
			throw new mvcFileFileStoreBuildFailedException($this->getFileStore());
		}
	}
	
	/**
	 * Attempts to create the sub folder structure in the file store
	 *
	 * @return void
	 * @throws mvcFileFileStoreBuildSubFoldersFailedException
	 */
	protected function buildSubFolders() {
		if ( $this->getSubFolderFormat() ) {
			if ( !file_exists($this->getUploadTarget()) ) {
				if ( !mkdir($this->getUploadTarget(), 0755, true) ) {
					throw new mvcFileFileStoreBuildSubFoldersFailedException($this->getSubFolderFormat(), $this->getFileStore());
				}
			}
		}
	}
	
	/**
	 * Translates the PHP upload error code into a string
	 *
	 * @param integer $inErrorCode
	 * @return string
	 */
	protected function translateErrorCode($inErrorCode) {
		$codes = array(
			UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
			UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
			UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive specified in form',
			UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
			UPLOAD_ERR_NO_FILE => 'No file was uploaded',
			UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
			UPLOAD_ERR_OK => 'File uploaded successfully',
			UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
		);
		
		if ( array_key_exists($inErrorCode, $codes) ) {
			return $codes[$inErrorCode];
		} else {
			return 'Unknown error occured';
		}
	}
	
	

	/**
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( !$modified && $this->_OptionsSet instanceof baseOptionsSet ) {
			$modified = $this->_OptionsSet->isModified();
		}
		return $modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return mvcFileUpload
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}


	/**
	 * Returns the value of $_OptionsSet
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
	 * Allows for directly setting the baseOptionsSet
	 *
	 * @param baseOptionsSet $inOptionsSet
	 * @return mvcFileUpload
	 */
	function setOptionsSet(baseOptionsSet $inOptionsSet) {
		if ( $inOptionsSet !== $this->_OptionsSet ) {
			$this->_OptionsSet = $inOptionsSet;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the option $inOption, if not found returns $inDefault
	 *
	 * @param string $inOption
	 * @param mixed $inDefault
	 * @return mixed
	 */
	function getOption($inOption, $inDefault = null) {
		return $this->getOptionsSet()->getOptions($inOption, $inDefault);
	}

	/**
	 * Sets a single option $inOption to value $inValue
	 *
	 * @param string $inOption
	 * @param mixed $inValue
	 * @return mvcFileUpload
	 */
	function setOption($inOption, $inValue) {
		$this->getOptionsSet()->setOptions(array($inOption => $inValue));
		return $this;
	}

	/**
	 * Sets multiple options into an existing options set
	 *
	 * @param array $inOptions
	 * @return mvcFileUpload
	 */
	function setOptions(array $inOptions = array()) {
		$this->getOptionsSet()->setOptions($inOptions);
		return $this;
	}

	/**
	 * Returns $_FieldName
	 *
	 * @return string
	 */
	function getFieldName() {
		return $this->getOption(self::OPTION_FIELD_NAME);
	}
	
	/**
	 * Set the field name used to upload files from
	 *
	 * @param string $inFieldName
	 * @return mvcFileUpload
	 */
	function setFieldName($inFieldName) {
		return $this->setOption(self::OPTION_FIELD_NAME, $inFieldName);
	}

	/**
	 * Returns $_FileStore
	 *
	 * @return string
	 */
	function getFileStore() {
		return $this->getOption(self::OPTION_FILE_STORE);
	}
	
	/**
	 * Set the path to the main file store, this may be in /data or in websites/base
	 *
	 * @param string $inFileStore
	 * @return mvcFileUpload
	 */
	function setFileStore($inFileStore) {
		return $this->setOption(self::OPTION_FILE_STORE, $inFileStore);
	}
	
	/**
	 * Returns $_SubFolderFormat
	 *
	 * @return string
	 */
	function getSubFolderFormat() {
		return $this->getOption(self::OPTION_SUB_FOLDER_FORMAT);
	}
	
	/**
	 * Set the sub-folder format to use when storing the files.
	 * 
	 * This is appended to the file store path default is to use the date
	 * /YYYY/mm/dd; path MUST include leading /.
	 *
	 * @param string $inSubFolderFormat
	 * @return mvcFileUpload
	 */
	function setSubFolderFormat($inSubFolderFormat) {
		if ( substr($inSubFolderFormat, 0, 1) !== '/' ) {
			$inSubFolderFormat = '/'.$inSubFolderFormat;
		}
		return $this->setOption(self::OPTION_SUB_FOLDER_FORMAT, $inSubFolderFormat);
	}
	
	/**
	 * Returns the upload target (filestore+subfolder format)
	 *
	 * @return string
	 */
	function getUploadTarget() {
		return utilityStringFunction::cleanDirSlashes($this->getFileStore().$this->getSubFolderFormat());
	}
	
	/**
	 * Returns the upload file set
	 *
	 * @return mvcFileSet
	 */
	function getUploadedFiles() {
		return $this->_UploadedFiles;
	}
	
	/**
	 * Set the file result set
	 *
	 * @param mvcFileSet $inUploadedFiles
	 * @return mvcFileUpload
	 */
	function setUploadedFiles(mvcFileSet $inUploadedFiles) {
		if ( $inUploadedFiles !== $this->_UploadedFiles ) {
			$this->_UploadedFiles = $inUploadedFiles;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_AutoCreateFileStore
	 *
	 * @return boolean
	 */
	function getAutoCreateFileStore() {
		return $this->getOption(self::OPTION_AUTO_CREATE_FILESTORE);
	}
	
	/**
	 * Should the folder store be auto-created, default true
	 *
	 * @param boolean $inAutoCreateFileStore
	 * @return mvcFileUpload
	 */
	function setAutoCreateFileStore($inAutoCreateFileStore) {
		return $this->setOption(self::OPTION_AUTO_CREATE_FILESTORE, $inAutoCreateFileStore);
	}

	/**
	 * Returns $_CheckFolderPermissions
	 *
	 * @return boolean
	 */
	function getCheckFolderPermissions() {
		return $this->getOption(self::OPTION_CHECK_PERMISSIONS);
	}
	
	/**
	 * Should the folder permissions be checked before processing, default true
	 *
	 * @param boolean $inCheckFolderPermissions
	 * @return mvcFileUpload
	 */
	function setCheckFolderPermissions($inCheckFolderPermissions) {
		return $this->setOption(self::OPTION_CHECK_PERMISSIONS, $inCheckFolderPermissions);
	}

	/**
	 * Returns $_UseOriginalFileName
	 *
	 * @return boolean
	 */
	function getUseOriginalFileName() {
		return $this->getOption(self::OPTION_USE_ORIGINAL_NAME);
	}
	
	/**
	 * Toggles whether the original file name is kept or not, default false
	 *
	 * @param boolean $inUseOriginalFileName
	 * @return mvcFileUpload
	 */
	function setUseOriginalFileName($inUseOriginalFileName) {
		return $this->setOption(self::OPTION_USE_ORIGINAL_NAME, $inUseOriginalFileName);
	}

	/**
	 * Returns $_StoreRawFileDataInResult
	 *
	 * @return boolean
	 */
	function getStoreRawFileDataInResult() {
		return $this->getOption(self::OPTION_STORE_RAW_DATA);
	}
	
	/**
	 * Should the raw file data be returned in the result set, default false
	 *
	 * @param boolean $inStoreRawFileDataInResult
	 * @return mvcFileUpload
	 */
	function setStoreRawFileDataInResult($inStoreRawFileDataInResult) {
		return $this->setOption(self::OPTION_STORE_RAW_DATA, $inStoreRawFileDataInResult);
	}

	/**
	 * Returns $_WriteFilesImmediately
	 *
	 * @return boolean
	 */
	function getWriteFilesImmediately() {
		return $this->getOption(self::OPTION_WRITE_IMMEDIATE);
	}
	
	/**
	 * Set write mode; if not immediately written causes raw data to be placed in result, default true
	 *
	 * @param boolean $inWriteFilesImmediately
	 * @return mvcFileUpload
	 */
	function setWriteFilesImmediately($inWriteFilesImmediately) {
		if ( $inWriteFilesImmediately === false ) {
			$this->setStoreRawFileDataInResult(true);
		}
		return $this->setOption(self::OPTION_WRITE_IMMEDIATE, $inWriteFilesImmediately);
	}

	/**
	 * Returns the filename prefix
	 *
	 * @return boolean
	 */
	function getAddFilenamePrefix() {
		return $this->getOption(self::OPTION_ADD_FILENAME_PREFIX);
	}

	/**
	 * Prefix this string to all filenames
	 *
	 * @param string $inPrefix
	 * @return mvcFileUpload
	 */
	function setAddFilenamePrefix($inPrefix) {
		return $this->setOption(self::OPTION_ADD_FILENAME_PREFIX, $inPrefix);
	}

	/**
	 * Returns the filename suffix
	 *
	 * @return boolean
	 */
	function getAddFilenameSuffix() {
		return $this->getOption(self::OPTION_ADD_FILENAME_SUFFIX);
	}

	/**
	 * Suffix this string to all filenames
	 *
	 * @param string $inSuffix
	 * @return mvcFileUpload
	 */
	function setAddFilenameSuffix($inSuffix) {
		return $this->setOption(self::OPTION_ADD_FILENAME_SUFFIX, $inSuffix);
	}
}