<?php
/**
 * fileObject
 * 
 * Stored in file.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage file
 * @category fileObject
 * @version $Rev: 770 $
 */


/**
 * fileObject Class
 * 
 * Object wrapper for PHPs file handling functions. Has implementation for file_get/file_put contents,
 * fopen/fread/write/fclose and other properties. Class will also set file properties (owner, group
 * permissions etc) on file load.
 * 
 * <code>
 * $oFile = new fileObject('/path/to/file.extn');
 * $oFile->get();
 * </code>
 * 
 * Contains a parseDir() method to recursively fetch files from a folder. Found files are returned
 * as file objects.
 * 
 * <code>
 * $files = fileObject::parseDir('/home/user/public_html');
 * foreach ( $files as $oFile ) {
 *     echo $oFile->getFilename()."\n";
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage file
 * @category fileObject
 */
class fileObject {
	
	/**
	 * File resource when a file has been open()'d
	 *
	 * @var resource
	 * @access protected
	 */
	protected $_Handle			= false;
	/**
	 * Flag for if the file is on the filesystem
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_FileExists		= false;
	/**
	 * Original filename supplied before it has been cleaned
	 *
	 * @var string
	 * @access protected
	 */
	protected $_OriginalName	= false;
	/**
	 * Full path to the file
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Path			= false;
	/**
	 * Filename minus path
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Filename		= false;
	/**
	 * Filesize in bytes
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Filesize		= 0;
	/**
	 * Timestamp the file was last modified
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_LastModified	= false;
	/**
	 * Current file owner or file owner to be assigned
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_Group			= false;
	/**
	 * File owner
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_Owner			= false;
	/**
	 * File permissions in octal format (default: 0664 === o+rw, g+rw, a+r)
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Permissions		= '0664';
	/**
	 * Data read from / to be written to the file
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Data			= '';
	
	
	
	/**
	 * Returns a new file object, if $filename is specifed a check is made to see if the file exists,
	 * if so the file properties are loaded
	 *
	 * @param string $inFilename
	 */
	function __construct($inFilename = false) {
		clearstatcache();
		$this->reset();
		if ( $inFilename ) {
			$this->_OriginalName = $inFilename;
			$this->_Filename = basename($inFilename);
			$this->_Path = dirname($inFilename);
			$this->setFileProperties();
		}
	}
	
	
	
	/**
	 * Static Methods
	 */
	
	/**
	 * Returns an instance of fileObject for the supplied $inFilename
	 *
	 * @param string $inFilename
	 * @return fileObject
	 * @static
	 */
	public static function getInstance($inFilename) {
		return new fileObject($inFilename);
	}
	
	/**
	 * Removes all illegal characters from $inFilename leaving only a-zA-Z0-9 and _-. as legal characters
	 *
	 * @param string $inFilename
	 * @return string
	 * @access public
	 * @static 
	 */
	public static function cleanFileName($inFilename) {
		$inFilename = str_replace(array('\\','/'), system::getDirSeparator(), $inFilename);
		return preg_replace('/[^[:space:]a-zA-Z0-9_.-]/i', '', $inFilename);
	}
	
	/**
	 * Parses $inDir for files and returns an array of fileObjects, if $inRecurse is true (Default) $inDir is recursed
	 *
	 * @param string $inDir
	 * @param boolean $inRecurse
	 * @return array
	 * @access public
	 * @static 
	 */
	public static function parseDir($inDir, $inRecurse = true) {
		$files = array();
		foreach (new DirectoryIterator($inDir) as $file ) {
			if ( !$file->isDot() ) {
				if ( $file->isDir() && $inRecurse ) {
					$tmp_files = self::parseDir($file->getPathname());
					$files = array_merge($files, $tmp_files);
				} else {
					$files[] = new fileObject($file->getPathname());
				}
			}
		}
		return $files;
	}
	
	/**
	 * Returns the file or directory count of $inDir
	 * 
	 * $inType is either "file" or "dir" to fetch only files or directory counts.
	 * This method does not include the dot folders (. & ..) from Unix filesystems.
	 * 
	 * @param string $inDir
	 * @param string $inType Either file or dir
	 * @return integer
	 * @static
	 */
	public static function countFilesInDir($inDir, $inType = 'file') {
		$file_count = 0;
		$dir_count = 0;
		
		foreach (new DirectoryIterator($inDir) as $file ) {
			if ( !$file->isDot() ) {
				if ( $file->isDir() ) {
					$dir_count++;
				} else {
					$file_count++;
				}
			}
		}
		
		return $inType == 'file' ? $file_count : $dir_count;
	}
	
	
	
	/**
	 * Main Methods
	 */
	
	/**
	 * Sets the internal object properties for the file
	 *
	 * @return void
	 * @access public
	 */
	public function setFileProperties() {
		if ( $this->exists() ) {
			$this->_FileExists = true;
			$this->_LastModified = filemtime($this->_Path.system::getDirSeparator().$this->_Filename);
			$this->_Filesize = filesize($this->_Path.system::getDirSeparator().$this->_Filename);
			$this->_Group = filegroup($this->_Path.system::getDirSeparator().$this->_Filename);
			$this->_Owner = fileowner($this->_Path.system::getDirSeparator().$this->_Filename);
			$this->_Permissions = substr(sprintf('%o', fileperms($this->_Path.system::getDirSeparator().$this->_Filename)), -4);
		}
	}
	
	/**
	 * Returns the original filename
	 *
	 * @return string
	 * @access public
	 */
	public function getOriginalFilename() {
		return $this->_OriginalName;
	}
	
	/**
	 * Returns the file path
	 *
	 * @return string
	 * @access public
	 */
	public function getPath() {
		return $this->_Path;
	}
	
	/**
	 * Returns cleaned filename
	 *
	 * @return string
	 * @access public
	 */
	public function getFilename() {
		return $this->_Filename;
	}
	
	/**
	 * Returns the last part of the filename that should be the extension
	 *
	 * @return string
	 * @access public
	 */
	public function getExtension() {
		return substr($this->_Filename, strrpos($this->_Filename, '.')+1);
	}
	
	/**
	 * Returns filesize in bytes
	 *
	 * @return integer
	 * @access public
	 */
	public function getFilesize() {
		return $this->_Filesize;
	}
	
	/**
	 * Returns last modified timestamp
	 *
	 * @return timestamp
	 * @access public
	 */
	public function lastModified() {
		return $this->_LastModified;
	}
	
	/**
	 * Returns current owner
	 *
	 * @return mixed
	 * @access public
	 */
	public function getOwner() {
		return $this->_Owner;
	}
	
	/**
	 * Returns current group
	 *
	 * @return mixed
	 * @access public
	 */
	public function getGroup() {
		return $this->_Group;
	}
	
	/**
	 * Returns permissions for file
	 *
	 * @return integer
	 * @access public
	 */
	public function getPermissions() {
		return $this->_Permissions;
	}
	
	/**
	 * Returns file data from the object
	 *
	 * @return string
	 * @access public
	 */
	public function getData() {
		return $this->_Data;
	}
	
	
	
	/**
	 * Returns if the file is readable
	 *
	 * @return boolean
	 * @access public
	 */
	public function isReadable() {
		return is_readable($this->_Path.system::getDirSeparator().$this->_Filename);
	}
	
	/**
	 * Returns if the file is writable
	 *
	 * @return boolean
	 * @access public
	 */
	public function isWritable() {
		return is_writable($this->_Path.system::getDirSeparator().$this->_Filename);
	}
	
	/**
	 * Returns if the file is executable
	 *
	 * @return boolean
	 * @access public
	 */
	public function isExecutable() {
		return is_executable($this->_Path.system::getDirSeparator().$this->_Filename);
	}
	
	/**
	 * Returns if the file is a directory
	 *
	 * @return boolean
	 * @access public
	 */
	public function isDir() {
		return is_dir($this->_Path.system::getDirSeparator().$this->_Filename);
	}
	
	/**
	 * Returns if the file exists
	 *
	 * @return boolean
	 * @access public
	 */
	public function exists() {
		return file_exists($this->_Path.system::getDirSeparator().$this->_Filename);
	}
	
	
	
	/**
	 * Opens a file as a resource for reading / writing / streaming; $mode is any valid value for fopen() mode
	 *
	 * @param string $inMode
	 * @return boolean
	 * @throws fileObjectException
	 * @access public
	 */
	public function open($inMode = 'r') {
		if ( $this->_Handle ) {
			$this->close();
		}
		$this->_Handle = @fopen($this->_Path.system::getDirSeparator().$this->_Filename, $inMode);
		if ( $this->_Handle ) {
			$this->setFileProperties();
			return true;
		} else {
			throw new fileObjectException("Unable to open {$this->_Path}/{$this->_Filename}, is not a valid file or resource");
		}
	}
	
	/**
	 * Reads data from an open file resource in $bytes chunks
	 *
	 * @param integer $inBytes
	 * @return string
	 * @throws fileObjectException
	 * @access public
	 */
	public function read($inBytes = 1024) {
		if ( $this->_Handle ) {
			$data = @fread($this->_Handle, $inBytes);
			$this->_Data .= $data;
			return $data;
		}
		throw new fileObjectException('No file has been open()d for reading');
	}
	
	/**
	 * Reads an open file resource as a stream via stream_get_contents() in $bytes chunks where -1 is everything
	 *
	 * @param integer $inBytes
	 * @return string
	 * @throws fileObjectException
	 * @access public
	 */
	public function readStream($inBytes = -1) {
		if ( $this->_Handle ) {
			$data = @stream_get_contents($this->_Handle, $inBytes);
			$this->_Data .= $data;
			return $data;
		}
		throw new fileObjectException('No file has been open()d for streaming');
	}
	
	/**
	 * Returns if end of file has been reached
	 *
	 * @return boolean
	 * @throws fileObjectException
	 * @access public
	 */
	public function eof() {
		if ( $this->_Handle ) {
			return @feof($this->_Handle);
		}
		throw new fileObjectException('No valid file has been opened');
	}
	
	/**
	 * Write $data, or already loaded data to an open file resource at $bytes chunks
	 *
	 * @param string $inData
	 * @param integer $inBytes
	 * @return integer
	 * @throws fileObjectException
	 * @access public
	 */
	public function write($inData = false, $inBytes = 1024) {
		if ( $this->_Handle ) {
			if ( $inData ) {
				$this->_Data = $inData;
			}
			return @fwrite($this->_Handle, $this->_Data, $inBytes);
		}
		throw new fileObjectException('No file has been open()d for writing');
	}
	
	/**
	 * Close open file resource
	 *
	 * @return boolean
	 * @access public
	 */
	public function close() {
		if ( $this->_Handle ) {
			return @fclose($this->_Handle);
		}
		return false;
	}
	
	
	
	/**
	 * Wrapper for file_get_contents(), fetches the whole loaded file, stores the data internally and returns it
	 *
	 * @return string
	 * @access public
	 */
	public function get() {
		$this->_Data = @file_get_contents($this->_Path.system::getDirSeparator().$this->_Filename);
		$this->setFileProperties();
		return $this->_Data;
	}
	
	/**
	 * Wrapper for file_put_contents(), writes $data or existing data to file and returns bytes written or false
	 * $flags takes any valid flag for {@link http://www.php.net/file_put_contents} e.g. LOCK_EX
	 *
	 * @param string $inData
	 * @param integer $inFlags
	 * @return integer
	 * @access public
	 */
	public function put($inData = false, $inFlags = false) {
		if ( $inData ) {
			$this->_Data = $inData;
		}
		$this->_Filesize = @file_put_contents($this->_Path.system::getDirSeparator().$this->_Filename, $this->_Data, $inFlags);
		return $this->_Filesize;
	}
	
	/**
	 * Delete loaded file from the filesystem if it exists
	 *
	 * @return boolean
	 * @access public
	 */
	public function delete() {
		if ( $this->exists() ) {
			return @unlink($this->_Path.system::getDirSeparator().$this->_Filename);
		}
		return false;
	}
	
	
	
	/**
	 * Set group for file
	 *
	 * @param mixed $inGroup
	 * @return void
	 * @access public
	 */
	public function setGroup($inGroup = false) {
		if ( $inGroup ) {
			$this->_Group = $inGroup;
		}
		if ( $this->_FileExists ) {
			@chgrp($this->_Path.system::getDirSeparator().$this->_Filename, $this->_Group);
		}
	}
	
	/**
	 * Set owner of file
	 *
	 * @param mixed $inOwner
	 * @return void
	 * @access public
	 */
	public function setOwner($inOwner = false) {
		if ( $inOwner ) {
			$this->_Owner = $inOwner;
		}
		if ( $this->_FileExists ) {
			@chown($this->_Path.system::getDirSeparator().$this->_Filename, $this->_Owner);
		}
	}
	
	/**
	 * Set permissions to assign to file on writing, or if file exists, assign permissions
	 *
	 * @param integer $inPermissions
	 * @return void
	 * @access public
	 */
	public function setPermissions($inPermissions = false) {
		if ( $inPermissions ) {
			$this->_Permissions = $inPermissions;
		}
		if ( $this->_FileExists ) {
			@chmod($this->_Path.system::getDirSeparator().$this->_Filename, $this->_Permissions);
		}
	}
	
	/**
	 * Set filename for new file
	 *
	 * @param string $inFilename
	 * @access public
	 */
	public function setFilename($inFilename) {
		$this->_OriginalName = $inFilename;
		$inFilename = self::cleanFileName($inFilename);
		$this->_Filename = basename($inFilename);
		$this->_Path = dirname($inFilename);
	}
	
	/**
	 * Set file data to write
	 *
	 * @param string $inData
	 * @access public
	 */
	public function setData($inData) {
		$this->_Data = $inData;
	}
	
	/**
	 * Resets object properties
	 *
	 * @return void
	 * @access public
	 */
	public function reset() {
		$this->_Handle			= false;
		$this->_FileExists		= false;
		$this->_OriginalName	= false;
		$this->_Path			= false;
		$this->_Filename		= false;
		$this->_Filesize		= 0;
		$this->_LastModified	= false;
		$this->_Group			= system::getConfig()->getSystemGroup();
		$this->_Owner			= system::getConfig()->getSystemUser();
		$this->_Permissions		= '0664';
		$this->_Data			= false;
	}
}