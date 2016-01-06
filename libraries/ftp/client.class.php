<?php
/**
 * ftpClient
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpClient
 * @author Dave Redfern <dave@scorpio.madagasgar.com>
 * @author Tobias Schlitt <toby@php.net>
 * @author Jorrit Schippers <jschippers@php.net>
 * @copyright 2009 Dave Redfern
 * @copyright 1997-2008 The PHP Group
 * @license http://www.php.net/license/3_0.txt PHP License 3.0
 * @version CVS: $Id: FTP.php,v 1.69 2008/05/19 19:37:42 jschippers Exp $
 * @version $Rev: 707 $
 */


/**
 * Class for handling FTP communication
 *
 * This class provides comfortable communication with FTP-servers. You may do
 * everything enabled by the PHP-FTP-extension and further functionalities, like
 * recursive-deletion, -up- and -download. Another feature is to create directories
 * recursively.
 * 
 * This is a port of the Net_FTP PEAR class to be fully PHP5 compliant. It revises
 * the error model to throw exceptions as well as tidying up the options, constants
 * and making some small refactorings. Additionally, the all space indentation has
 * been replaced with tabs reducing overall file size by 6Kb.
 * 
 * Because this is simply a port of Net_FTP, it is released under the same license
 * as Net_FTP.
 * 
 * Example usage:
 * <code>
 * // connect to mirror service and list contents of the kubuntu directory
 * $oFtp = new ftpClient(
 *     array(
 *        ftpClient::OPTION_HOSTNAME => 'mirror.csclub.uwaterloo.ca',
 *        ftpClient::OPTION_PORT => '21',
 *        ftpClient::OPTION_TIMEOUT => 20,
 *        ftpClient::OPTION_TRANSFER_MODE => FTP_ASCII,
 *        ftpClient::OPTION_USERNAME => 'anonymous',
 *        ftpClient::OPTION_PASSWORD => 'username.example.com',
 *     )
 * );
 * if ( $oFtp->login() ) {
 *     $oFtp->cd('ubuntu-releases/kubuntu/');
 *     $res = $oFtp->ls();
 *     print_r($res);
 * }
 * $oFtp->disconnect();
 * </code>
 * 
 * To switch to/from passive mode an FTP connection must first be made. This is the
 * only option that cannot be set from the options array as setting {@link http://ca.php.net/ftp_pasv ftp_pasv}
 * requires the FTP connection resource.
 * 
 * <code>
 * // switch to passive mode
 * $oFtp = new ftpClient(// set options here);
 * $oFtp->connect();
 * $oFtp->setPassive();
 * 
 * // switch to active
 * $oFtp->setActive();
 * </code>
 * 
 * Dependencies:
 * ftpClient has the following dependencies in the Scorpio Framework:
 *   {@link baseOptionsSet}
 *   {@link ftpException} 
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpClient
 * @access public
 */
class ftpClient {
	
	/**
	 * Stores the directory permissions
	 * 
	 * @var integer
	 * @access private
	 * @static
	 */
	private static $_dir_permissions = false;
	
	/**
	 * Option to set ls return files only
	 *
	 * @var integer
	 */
	const LS_MODE_FILES_ONLY = 0;
	/**
	 * Option to set ls return dirs only
	 *
	 * @var integer
	 */
	const LS_MODE_DIR_ONLY = 1;
	/**
	 * Option to set ls return files and dirs
	 *
	 * @var integer
	 */
	const LS_MODE_DIRS_FILES = 2;
	/**
	 * Option to set ls return faw listings
	 *
	 * @var integer
	 */
	const LS_MODE_RAWLIST = 3;
	
	
	/**
	 * Option to indicate that non-blocking features should not be used in
	 * put(). This will also disable the listener functionality as a side effect.
	 *
	 * @var integer
	 */
	const PUT_BLOCKING = 1;
	/**
	 * Option to indicate that non-blocking features should be used if available in
	 * put(). This will also enable the listener functionality.
	 *
	 * @var integer
	 */
	const PUT_NONBLOCKING = 2;
	
	
	/**
	 * Stores $_OptionsSet, an instance of baseOptionsSet for storing class options
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_OptionsSet;
	
	/**
	 * Hostname to connect to, no protocol or trailing / or directory; only the hostname
	 *
	 * @var string
	 */
	const OPTION_HOSTNAME = 'ftp.hostname';
	/**
	 * Port for the ftp connection, default is 21
	 *
	 * @var integer
	 */
	const OPTION_PORT = 'ftp.port';
	/**
	 * Username for ftp login
	 *
	 * @var string
	 */
	const OPTION_USERNAME = 'ftp.username';
	/**
	 * Password for ftp login
	 *
	 * @var string
	 */
	const OPTION_PASSWORD = 'ftp.password';
	/**
	 * Transfer method, active (false) or passive (true)
	 *
	 * @var boolean
	 */
	const OPTION_TRANSFER_METHOD = 'ftp.passive';
	/**
	 * Transfer mode, either ascii or binary
	 *
	 * @var integer
	 */
	const OPTION_TRANSFER_MODE = 'ftp.transfer.mode';
	/**
	 * Connection timeout in seconds
	 *
	 * @var integer
	 */
	const OPTION_TIMEOUT = 'ftp.timeout';
	
	const TRANSFER_METHOD_PASSIVE = true;
	const TRANSFER_METHOD_ACTIVE = false;
	
	/**
	 * Resource handle when connected to a ftp server
	 *
	 * @var resource
	 * @access private
	 */
	private $_handle;
	
	/**
	 * Saves file-extensions for ascii- and binary-mode
	 *
	 * Array format is array[EXT] = FTP_ASCII | FTP_BINARY
	 *
	 * @access private
	 * @var array
	 */
	private $_file_extensions;
	
	/**
	 * Matches the ls entries against a regex and maps the resulting array to
	 * speaking names
	 *
	 * The values are set in the constructor because of line length constaints.
	 *
	 * Typical lines for the Windows format:
	 * 07-05-07  08:40AM                 4701 SomeFile.ext
	 * 04-29-07  10:28PM       <DIR>          SomeDir
	 *
	 * @access private
	 * @var array
	 */
	private $_ls_match = null;
	
	/**
	 * Stores the matcher for the current connection
	 *
	 * @access private
	 * @var array
	 */
	private $_matcher = null;
	
	/**
	 * Holds all Net_FTP_Observer objects 
	 * that wish to be notified of new messages.
	 *
	 * @var array
	 * @access private
	 */
	private $_listeners = array();
	
	
	
	/**
	 * This generates a new FTP-Object. The FTP-connection is not established.
	 * 
	 * $inOptions is an associative array of the options you wish to set at
	 * creation time. You should use the class OPTION_ constants to set
	 * these values. Alternatively you can use the setX methods.
	 *
	 * @param array $inOptions
	 * @return ftpClient
	 * @see ftpClient::setHostname(), ftpClient::setPort(), ftpClient::connect()
	 */
	function __construct(array $inOptions = array()) {
		$this->_handle = null;
		$this->getOptionsSet()->setOptions($inOptions);
		$this->_file_extensions = array(
			'php' => FTP_ASCII,
			'css' => FTP_ASCII,
			'js' => FTP_ASCII,
			'html' => FTP_ASCII,
			'htm' => FTP_ASCII,
			'phtml' => FTP_ASCII,
			'xml' => FTP_ASCII,
			'json' => FTP_ASCII,
			
			'gif' => FTP_BINARY,
			'jpg' => FTP_BINARY,
			'jpeg' => FTP_BINARY,
			'png' => FTP_BINARY,
			'ico' => FTP_BINARY,
			'zip' => FTP_BINARY,
			'gz' => FTP_BINARY,
			'iso' => FTP_BINARY,
		);
	    $this->_ls_match = array(
			'unix' => array(
				'pattern' => '/(?:(d)|.)([rwxts-]{9})\s+(\w+)\s+([\w\d-()?.]+)\s+([\w\d-()?.]+)\s+(\w+)\s+(\S+\s+\S+\s+\S+)\s+(.+)/',
				'map' => array(
					'is_dir'        => 1,
					'rights'        => 2,
					'files_inside'  => 3,
					'user'          => 4,
					'group'         => 5,
					'size'          => 6,
					'date'          => 7,
					'name'          => 8,
				)
			),
			'windows' => array(
				'pattern' => '/([0-9\-]+)\s+([0-9:APM]+)\s+((<DIR>)|\d+)\s+(.+)/',
				'map' => array(
					'date'   => 1,
					'time'   => 2,
					'size'   => 3,
					'is_dir' => 4,
					'name'   => 5,
				)
			)
		);
	}
	
	/**
	 * Ensure disconnect is always called when object is destroyed to prevent open resources
	 *
	 * @return void
	 */
	function __destruct() {
		$this->disconnect();
	}

	/**
	 * This function generates the FTP-connection. You can optionally define a
	 * hostname and/or a port. If you do so, this data is stored inside the object.
	 *
	 * @param string $host (optional) The Hostname
	 * @param int    $port (optional) The Port
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function connect($host = null, $port = null) {
		$this->_matcher = null;
		if ( $host !== null ) {
			$this->setHostname($host);
		}
		if ( $port !== null ) {
			$this->setPort($port);
		}
		$handle = ftp_connect($this->getHostname(), $this->getPort(), $this->getTimeoutOption());
		if ( !$handle ) {
			$this->_handle = false;
			throw new ftpConnectException($this->getHostname(), $this->getPort());
		} else {
			$this->_handle = $handle;
			return true;
		}
	}

	/**
	 * This function close the FTP-connection
	 *
	 * @access public
	 * @return bool
	 * @throws ftpException
	 */
	function disconnect() {
		if ( is_resource($this->_handle) ) {
			$res = @ftp_close($this->_handle);
			if ( !$res ) {
				throw new ftpDisconnectException();
			}
			$this->_handle = null;
		}
		return true;
	}

	/**
	 * This logs you into the ftp-server. You are free to specify username and
	 * password in this method. If you specify it, the values will be taken into 
	 * the corresponding attributes, if do not specify, the attributes are taken.
	 *
	 * If connect() has not been called yet, a connection will be setup
	 *
	 * @param string $username (optional) The username to use 
	 * @param string $password (optional) The password to use
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function login($username = null, $password = null) {
		if ( $this->_handle === null ) {
			$res = $this->connect();
		}
		if ( $username !== null ) {
			$this->setUsername($username);
		}
		if ( $password !== null ) {
			$this->setPassword($password);
		}
		
		$res = @ftp_login($this->_handle, $this->getUsername(), $this->getPassword());
		
		if ( !$res ) {
			throw new ftpLoginException($this->getUsername(), $this->getPassword());
		} else {
			return true;
		}
	}

	/**
	 * This changes the currently used directory. You can use either an absolute
	 * directory-path (e.g. "/home/blah") or a relative one (e.g. "../test").
	 *
	 * @param string $dir The directory to go to.
	 *
	 * @access public
	 * @return boolean
	 */
	function cd($dir) {
		$erg = @ftp_chdir($this->_handle, $dir);
		if ( !$erg ) {
			throw new ftpCdException($dir);
		} else {
			return true;
		}
	}

	/**
	 * Show's you the actual path on the server
	 * This function questions the ftp-handle for the actual selected path and
	 * returns it.
	 *
	 * @access public
	 * @return mixed
	 * @throws ftpException
	 */
	function pwd() {
		$res = @ftp_pwd($this->_handle);
		if ( !$res ) {
			throw new ftpPwdException();
		} else {
			return $res;
		}
	}

	/**
	 * This works similar to the mkdir-command on your local machine. You can either
	 * give it an absolute or relative path. The relative path will be completed
	 * with the actual selected server-path. (see: pwd())
	 *
	 * @param string $dir       Absolute or relative dir-path
	 * @param bool   $recursive (optional) Create all needed directories
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function mkdir($dir, $recursive = false) {
		$dir = $this->_constructPath($dir);
		$savedir = $this->pwd();
		$e = $this->cd($dir);
		if ( $e === true ) {
			$this->cd($savedir);
			return true;
		}
		$this->cd($savedir);
		if ( $recursive === false ) {
			$res = @ftp_mkdir($this->_handle, $dir);
			if ( !$res ) {
				throw new ftpMkdirException($dir);
			} else {
				return true;
			}
		} else {
			// do not look at the first character, as $dir is absolute,
			// it will always be a /
			if ( strpos(substr($dir, 1), '/') === false ) {
				return $this->mkdir($dir, false);
			}
			if ( substr($dir, -1) == '/' ) {
				$dir = substr($dir, 0, -1);
			}
			$parent = substr($dir, 0, strrpos($dir, '/'));
			$res = $this->mkdir($parent, true);
			if ( $res === true ) {
				$res = $this->mkdir($dir, false);
			}
			if ( $res !== true ) {
				return $res;
			}
			return true;
		}
	}

	/**
	 * This method tries executing a command on the ftp, using SITE EXEC.
	 *
	 * @param string $command The command to execute
	 *
	 * @access public
	 * @return mixed The result of the command (if successfull)
	 * @throws ftpException
	 */
	function execute($command) {
		$res = @ftp_exec($this->_handle, $command);
		if ( !$res ) {
			throw new ftpExecCommandException($command);
		} else {
			return $res;
		}
	}

	/**
	 * Execute a SITE command on the server
	 * This method tries to execute a SITE command on the ftp server.
	 *
	 * @param string $command The command with parameters to execute
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function site($command) {
		$res = @ftp_site($this->_handle, $command);
		if ( !$res ) {
			throw new ftpSiteCommandException($command);
		} else {
			return $res;
		}
	}

	/**
	 * This method will try to chmod the file specified on the server
	 * Currently, you must give a number as the the permission argument (777 or
	 * similar). The file can be either a relative or absolute path.
	 * NOTE: Some servers do not support this feature. In that case, you will
	 * get an exception thrown. If successful, the method returns true
	 *
	 * @param mixed   $target      The file or array of files to set permissions for
	 * @param integer $permissions The mode to set the file permissions to
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function chmod($target, $permissions) {
		if ( is_array($target) ) {
			for ( $i = 0; $i < count($target); $i++ ) {
				$res = $this->chmod($target[$i], $permissions);
			}
		} else {
			$res = $this->site("CHMOD " . $permissions . " " . $target);
			if ( !$res ) {
				throw new ftpChmodException($target, $permissions);
			} else {
				return $res;
			}
		}
	}

	/**
	 * This method will try to chmod a folder and all of its contents
	 * on the server. The target argument must be a folder or an array of folders
	 * and the permissions argument have to be an integer (i.e. 777).
	 * The file can be either a relative or absolute path.
	 * NOTE: Some servers do not support this feature. In that case, you
	 * will get an exception thrown. If successful, the method returns true
	 *
	 * @param mixed   $target      The folder or array of folders to
	 *                             set permissions for
	 * @param integer $permissions The mode to set the folder
	 *                             and file permissions to
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function chmodRecursive($target, $permissions) {
		if ( !self::$_dir_permissions ) {
			self::$_dir_permissions = $this->_makeDirPermissions($permissions);
		}
		
		if ( is_array($target) ) {
			for ( $i = 0; $i < count($target); $i++ ) {
				$res = $this->chmodRecursive($target[$i], $permissions);
			}
		} else {
			$remote_path = $this->_constructPath($target);
			
			// Chmod the directory itself
			$result = $this->chmod($remote_path, self::$_dir_permissions);
			
			// If $remote_path last character is not a slash, add one
			if ( substr($remote_path, strlen($remote_path) - 1) != "/" ) {
				$remote_path .= "/";
			}
			
			$dir_list = array();
			$mode = self::LS_MODE_DIR_ONLY;
			$dir_list = $this->ls($remote_path, $mode);
			foreach ( $dir_list as $dir_entry ) {
				if ( $dir_entry['name'] == '.' || $dir_entry['name'] == '..' ) {
					continue;
				}
				
				$remote_path_new = $remote_path . $dir_entry["name"] . "/";
				
				// Chmod the directory we're about to enter
				$result = $this->chmod($remote_path_new, self::$_dir_permissions);
				$result = $this->chmodRecursive($remote_path_new, $permissions);
			
			}
			
			$file_list = array();
			$mode = self::LS_MODE_FILES_ONLY;
			$file_list = $this->ls($remote_path, $mode);
			
			foreach ( $file_list as $file_entry ) {
				$remote_file = $remote_path . $file_entry["name"];
				$result = $this->chmod($remote_file, $permissions);
			}
		}
		return true;
	}

	/**
	 * Rename or move a file or a directory from the ftp-server
	 *
	 * @param string $remote_from Original directory or file name
	 * @param string $remote_to   New directory of file name
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function rename($remote_from, $remote_to) {
		$res = @ftp_rename($this->_handle, $remote_from, $remote_to);
		if ( !$res ) {
			throw new ftpRenameException($remote_from, $remote_to);
		}
		return true;
	}

	/**
	 * This will return logical permissions mask for directory.
	 * if directory has to be readable it have also be executable
	 *
	 * @param string $permissions File permissions in digits for file (i.e. 666)
	 *
	 * @access private
	 * @return string File permissions in digits for directory (i.e. 777)
	 */
	private function _makeDirPermissions($permissions) {
		$permissions = (string) $permissions;
		
		// going through (user, group, world)
		for ( $i = 0; $i < strlen($permissions); $i++ ) {
			// Read permission is set but execute not yet
			if ( (int) $permissions{$i} & 4 and !((int) $permissions{$i} & 1) ) {
				// Adding execute flag
				(int) $permissions{$i} = (int) $permissions{$i} + 1;
			}
		}
		return (string) $permissions;
	}

	/**
	 * This will return the last modification-time of a file. You can either give
	 * this function a relative or an absolute path to the file to check.
	 * NOTE: Some servers will not support this feature and the function works
	 * only on files, not directories! When successful,
	 * it will return the last modification-time as a unix-timestamp or, when
	 * $format is specified, a preformated timestring.
	 *
	 * @param string $file   The file to check
	 * @param string $format (optional) The format to give the date back 
	 *                       if not set, it will return a Unix timestamp
	 *
	 * @access public
	 * @return mixed Unix timestamp, a preformated date-string
	 * @throws ftpException
	 */
	function mdtm($file, $format = null) {
		$file = $this->_constructPath($file);
		if ( $this->_checkRemoteDir($file) !== false ) {
			throw new ftpMdtmException("Filename '$file' seems to be a directory.");
		}
		$res = @ftp_mdtm($this->_handle, $file);
		if ( $res == -1 ) {
			throw new ftpMdtmException("Could not get last-modification-date of '" . $file . "'.");
		}
		if ( $format !== null ) {
			$res = date($format, $res);
			if ( !$res ) {
				throw new ftpMdtmException("Date-format failed on timestamp '" . $res . "'.");
			}
		}
		return $res;
	}

	/**
	 * This will return the size of a given file in bytes. You can either give this
	 * function a relative or an absolute file-path. NOTE: Some servers do not
	 * support this feature!
	 *
	 * @param string $file The file to check
	 *
	 * @access public
	 * @return mixed Size in bytes
	 * @throws ftpException
	 */
	function size($file) {
		$file = $this->_constructPath($file);
		$res = @ftp_size($this->_handle, $file);
		if ( $res == -1 ) {
			throw new ftpSizeException($file);
		} else {
			return $res;
		}
	}

	/**
	 * This method returns a directory-list of the current directory or given one.
	 * To display the current selected directory, simply set the first parameter to
	 * null
	 * or leave it blank, if you do not want to use any other parameters.
	 * <br><br>
	 * There are 4 different modes of listing directories. Either to list only
	 * the files (using ftpClient::LS_MODE_FILES_ONLY), to list only directories
	 * (using ftpClient::LS_MODE_DIRS_ONLY) or to show both (using
	 * ftpClient::LS_MODE_DIRS_FILES, which is default).
	 * <br><br>
	 * The 4th one is the ftpClient::LS_MODE_RAW_LIST, which returns just the array
	 * created by the ftp_rawlist() - function built into PHP.
	 * <br><br>
	 * The other function-modes will return an array containing the requested data.
	 * The files and dirs are listed in human-sorted order, but if you select
	 * ftpClient::LS_MODE_DIRS_FILES the directories will be added above the files,
	 * although both sorted.
	 * <br><br>
	 * All elements in the arrays are associative arrays themselves. They have the
	 * following structure:
	 * <br><br>
	 * Dirs:<br>
	 *           ["name"]        =>  string The name of the directory<br>
	 *           ["rights"]      =>  string The rights of the directory (in style
	 *                               "rwxr-xr-x")<br>
	 *           ["user"]        =>  string The owner of the directory<br>
	 *           ["group"]       =>  string The group-owner of the directory<br>
	 *           ["files_inside"]=>  string The number of files/dirs inside the
	 *                               directory excluding "." and ".."<br>
	 *           ["date"]        =>  int The creation-date as Unix timestamp<br>
	 *           ["is_dir"]      =>  bool true, cause this is a dir<br>
	 * <br><br>
	 * Files:<br>
	 *           ["name"]        =>  string The name of the file<br>
	 *           ["size"]        =>  int Size in bytes<br>
	 *           ["rights"]      =>  string The rights of the file (in style 
	 *                               "rwxr-xr-x")<br>
	 *           ["user"]        =>  string The owner of the file<br>
	 *           ["group"]       =>  string The group-owner of the file<br>
	 *           ["date"]        =>  int The creation-date as Unix timestamp<br>
	 *           ["is_dir"]      =>  bool false, cause this is a file<br>
	 *
	 * @param string $dir  (optional) The directory to list or null for current
	 * @param int    $mode (optional) List files, dirs or both.
	 *
	 * @access public
	 * @return mixed The directory list as described above
	 * @throws ftpException
	 */
	function ls($dir = null, $mode = self::LS_MODE_DIRS_FILES) {
		if ( $dir === null ) {
			$dir = $this->pwd();
		}
		if ( ($mode != self::LS_MODE_FILES_ONLY) && ($mode != self::LS_MODE_DIR_ONLY) && ($mode != self::LS_MODE_RAWLIST) ) {
			$mode = self::LS_MODE_DIRS_FILES;
		}
		
		switch ( $mode ) {
			case self::LS_MODE_DIRS_FILES :
				$res = $this->_lsBoth($dir);
				break;
			case self::LS_MODE_DIR_ONLY :
				$res = $this->_lsDirs($dir);
				break;
			case self::LS_MODE_FILES_ONLY :
				$res = $this->_lsFiles($dir);
				break;
			case self::LS_MODE_RAWLIST :
				$res = @ftp_rawlist($this->_handle, $dir);
				break;
		}
		return $res;
	}

	/**
	 * This method will delete the given file or directory ($path) from the server
	 * (maybe recursive).
	 *
	 * Whether the given string is a file or directory is only determined by the
	 * last sign inside the string ("/" or not).
	 *
	 * If you specify a directory, you can optionally specify $recursive as true,
	 * to let the directory be deleted recursive (with all sub-directories and files
	 * inherited).
	 *
	 * You can either give a absolute or relative path for the file / dir. If you
	 * choose to use the relative path, it will be automatically completed with the
	 * actual selected directory.
	 *
	 * @param string $path      The absolute or relative path to the file/directory.
	 * @param bool   $recursive Recursively delete everything in $path
	 * @param bool   $filesonly Only delete the files, leaving directories
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function rm($path, $recursive = false, $filesonly = false) {
		$path = $this->_constructPath($path);
		if ( $this->_checkRemoteDir($path) === true ) {
			if ( $recursive ) {
				return $this->_rmDirRecursive($path, $filesonly);
			} else {
				return $this->_rmDir($path);
			}
		} else {
			return $this->_rmFile($path);
		}
	}

	/**
	 * This function will download a file from the ftp-server.
	 * 
	 * You can either specify an absolute path to the file (beginning with "/") or
	 * a relative one, which will be completed with the actual directory you
	 * selected on the server. You can specify the path to which the file will be
	 * downloaded on the local machine, if the file should be overwritten if it
	 * exists (optionally, default is no overwriting) and in which mode (FTP_ASCII
	 * or FTP_BINARY) the file should be downloaded (if you do not specify this,
	 * the method tries to determine it automatically from the mode-directory or
	 * uses the default-mode, set by you).
	 * 
	 * If you give a relative path to the local-file, the script-path is used as
	 * basepath.
	 *
	 * @param string $remote_file The absolute or relative path to the file
	 * @param string $local_file  The local file to put the downloaded in
	 * @param bool   $overwrite   (optional) Whether to overwrite existing file
	 * @param int    $mode        (optional) Either FTP_ASCII or FTP_BINARY
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function get($remote_file, $local_file, $overwrite = false, $mode = null) {
		if ( $mode === null ) {
			$mode = $this->checkFileExtension($remote_file);
		}
		
		$remote_file = $this->_constructPath($remote_file);
		
		if ( @file_exists($local_file) && !$overwrite ) {
			throw new ftpGetLocalFileExistsException($local_file);
		}
		if ( @file_exists($local_file) && !@is_writeable($local_file) && $overwrite ) {
			throw new ftpGetLocalFileNotWritableException($local_file);
		}
		
		if ( @function_exists('ftp_nb_get') ) {
			$res = @ftp_nb_get($this->_handle, $local_file, $remote_file, $mode);
			while ( $res == FTP_MOREDATA ) {
				$this->notify('nb_get');
				$res = @ftp_nb_continue($this->_handle);
			}
		} else {
			$res = @ftp_get($this->_handle, $local_file, $remote_file, $mode);
		}
		if ( !$res ) {
			throw new ftpGetException("File '" . $remote_file . "' could not be downloaded to '$local_file'.");
		} else {
			return true;
		}
	}

	/**
	 * This function will upload a file to the ftp-server.
	 * 
	 * You can either specify a absolute path to the remote-file (beginning with "/")
	 * or a relative one, which will be completed with the actual directory you
	 * selected on the server. You can specify the path from which the file will be
	 * uploaded on the local maschine, if the file should be overwritten if it exists
	 * (optionally, default is no overwriting) and in which mode (FTP_ASCII or
	 * FTP_BINARY) the file should be downloaded (if you do not specify this, the
	 * method tries to determine it automatically from the mode-directory or uses the
	 * default-mode, set by you).
	 * 
	 * If you give a relative path to the local-file, the script-path is used as
	 * basepath.
	 *
	 * @param string $local_file  The local file to upload
	 * @param string $remote_file The absolute or relative path to the file to upload to
	 * @param bool   $overwrite   (optional) Whether to overwrite existing file
	 * @param int    $mode        (optional) Either FTP_ASCII or FTP_BINARY
	 * @param int    $options     (optional) ftpClient::(PUT_BLOCKING | PUT_NONBLOCKING)
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function put($local_file, $remote_file, $overwrite = false, $mode = null, $options = 0) {
		if ( $options & (self::PUT_BLOCKING | self::PUT_NONBLOCKING) === (self::PUT_BLOCKING | self::PUT_NONBLOCKING) ) {
			throw new ftpPutOptionsException();
		}
		
		$usenb = !($options & (self::PUT_BLOCKING == self::PUT_BLOCKING));
		
		if ( !isset($mode) ) {
			$mode = $this->checkFileExtension($local_file);
		}
		$remote_file = $this->_constructPath($remote_file);
		
		if ( !@file_exists($local_file) ) {
			throw new ftpPutLocalFileDoesNotExistException($local_file);
		}
		if ( (@ftp_size($this->_handle, $remote_file) != -1) && !$overwrite ) {
			throw new ftpPutRemoteFileExistsNoOverwriteException($remote_file);
		}
		
		if ( function_exists('ftp_alloc') ) {
			ftp_alloc($this->_handle, filesize($local_file));
		}
		if ( $usenb && function_exists('ftp_nb_put') ) {
			$res = @ftp_nb_put($this->_handle, $remote_file, $local_file, $mode);
			while ( $res == FTP_MOREDATA ) {
				$this->notify('nb_put');
				$res = @ftp_nb_continue($this->_handle);
			}
		
		} else {
			$res = @ftp_put($this->_handle, $remote_file, $local_file, $mode);
		}
		if ( !$res ) {
			throw new ftpPutException("File '$local_file' could not be uploaded to '" . $remote_file . "'.");
		} else {
			return true;
		}
	}

	/**
	 * This functionality allows you to transfer a whole directory-structure from
	 * the remote-ftp to your local host. You have to give a remote-directory
	 * (ending with '/') and the local directory (ending with '/') where to put the
	 * files you download.
	 * The remote path is automatically completed with the current-remote-dir, if
	 * you give a relative path to this function. You can give a relative path for
	 * the $local_path, too. Then the script-basedir will be used for comletion of
	 * the path.
	 * The parameter $overwrite will determine, whether to overwrite existing files
	 * or not. Standard for this is false. Fourth you can explicitly set a mode for
	 * all transfer actions done. If you do not set this, the method tries to
	 * determine the transfer mode by checking your mode-directory for the file
	 * extension. If the extension is not inside the mode-directory, it will get
	 * your default mode.
	 * 
	 * Since 1.4 no error will be returned when a file exists while $overwrite is 
	 * set to false. 
	 *
	 * @param string $remote_path The path to download
	 * @param string $local_path  The path to download to
	 * @param bool   $overwrite   (optional) Whether to overwrite existing files
	 *                            (true) or not (false, standard).
	 * @param int    $mode        (optional) The transfermode (either FTP_ASCII or
	 * FTP_BINARY).
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function getRecursive($remote_path, $local_path, $overwrite = false, $mode = null) {
		$remote_path = $this->_constructPath($remote_path);
		if ( $this->_checkRemoteDir($remote_path) !== true ) {
			throw new ftpGetRecursiveException("Given remote-path '$remote_path' seems not to be a directory.");
		}
		
		if ( !@file_exists($local_path) ) {
			$res = @mkdir($local_path);
			if ( !$res ) {
				throw new ftpGetRecursiveException("Could not create dir '$local_path'");
			}
		} elseif ( !@is_dir($local_path) ) {
			throw new ftpGetRecursiveException("Given local-path '$local_path' seems not to be a directory.");
		}
		
		$dir_list = array();
		$dir_list = $this->ls($remote_path, self::LS_MODE_DIR_ONLY);
		
		foreach ( $dir_list as $dir_entry ) {
			if ( $dir_entry['name'] != '.' && $dir_entry['name'] != '..' ) {
				$remote_path_new = $remote_path . $dir_entry["name"] . "/";
				$local_path_new = $local_path . $dir_entry["name"] . "/";
				$result = $this->getRecursive($remote_path_new, $local_path_new, $overwrite, $mode);
			}
		}
		$file_list = array();
		$file_list = $this->ls($remote_path, self::LS_MODE_FILES_ONLY);
		
		foreach ( $file_list as $file_entry ) {
			$remote_file = $remote_path . $file_entry["name"];
			$local_file = $local_path . $file_entry["name"];
			$result = $this->get($remote_file, $local_file, $overwrite, $mode);
		}
		return true;
	}

	/**
	 * This functionality allows you to transfer a whole directory-structure from
	 * your local host to the remote-ftp. You have to give a remote-directory
	 * (ending with '/') and the local directory (ending with '/') where to put the
	 * files you download. The remote path is automatically completed with the
	 * current-remote-dir, if you give a relative path to this function. You can
	 * give a relative path for the $local_path, too. Then the script-basedir will
	 * be used for comletion of the path.
	 * The parameter $overwrite will determine, whether to overwrite existing files
	 * or not.
	 * Standard for this is false. Fourth you can explicitly set a mode for all
	 * transfer actions done. If you do not set this, the method tries to determine
	 * the transfer mode by checking your mode-directory for the file-extension. If
	 * the extension is not inside the mode-directory, it will get your default
	 * mode.
	 *
	 * @param string $local_path  The path to download to
	 * @param string $remote_path The path to download
	 * @param bool   $overwrite   (optional) Whether to overwrite existing files
	 *                            (true) or not (false, standard).
	 * @param int    $mode        (optional) The transfermode (either FTP_ASCII or
	 *                            FTP_BINARY).
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function putRecursive($local_path, $remote_path, $overwrite = false, $mode = null) {
		$remote_path = $this->_constructPath($remote_path);
		if ( !file_exists($local_path) || !is_dir($local_path) ) {
			throw new ftpPutRecursiveException("Given local-path '$local_path' seems not to be a directory.");
		}
		// try to create directory if it doesn't exist
		$old_path = $this->pwd();
		try {
			$this->cd($remote_path);
		} catch ( ftpException $e ) {
			$res = $this->mkdir($remote_path);
		}
		
		$this->cd($old_path);
		if ( $this->_checkRemoteDir($remote_path) !== true ) {
			throw new ftpPutRecursiveException("Given remote-path '$remote_path' seems not to be a directory.");
		}
		$dir_list = $this->_lsLocal($local_path);
		foreach ( $dir_list["dirs"] as $dir_entry ) {
			// local directories do not have arrays as entry
			$remote_path_new = $remote_path . $dir_entry . "/";
			$local_path_new = $local_path . $dir_entry . "/";
			$result = $this->putRecursive($local_path_new, $remote_path_new, $overwrite, $mode);
		}
		
		foreach ( $dir_list["files"] as $file_entry ) {
			$remote_file = $remote_path . $file_entry;
			$local_file = $local_path . $file_entry;
			$result = $this->put($local_file, $remote_file, $overwrite, $mode);
		}
		return true;
	}

	/**
	 * This checks, whether a file should be transfered in ascii- or binary-mode
	 * by it's file-extension. If the file-extension is not set or
	 * the extension is not inside one of the extension-dirs, the actual set
	 * transfer-mode is returned.
	 *
	 * @param string $filename The filename to be checked
	 *
	 * @access public
	 * @return int Either FTP_ASCII or FTP_BINARY
	 */
	function checkFileExtension($filename) {
		if ( ($pos = strrpos($filename, '.')) === false ) {
			return $this->getMode();
		} else {
			$ext = substr($filename, $pos + 1);
		}
		
		if ( isset($this->_file_extensions[$ext]) ) {
			return $this->_file_extensions[$ext];
		}
		
		return $this->getMode();
	}

	/**
	 * Returns the baseOptionsSet object
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
	 * Set a new options set object over the current one
	 *
	 * @param baseOptionsSet $inOptionsSet
	 * @return ftpClient
	 */
	function setOptionsSet($inOptionsSet) {
		if ( $inOptionsSet !== $this->_OptionsSet ) {
			$this->_OptionsSet = $inOptionsSet;
		}
		return $this;
	}

	/**
	 * Set the hostname
	 *
	 * @param string $host The hostname to set
	 * @return ftpClient
	 * @throws ftpException
	 */
	function setHostname($host) {
		if ( !is_string($host) ) {
			throw new ftpException("Hostname must be a string");
		}
		$this->getOptionsSet()->setOptions(array(self::OPTION_HOSTNAME => $host));
		return $this;
	}

	/**
	 * Set the Port
	 *
	 * @param int $port The port to set
	 *
	 * @access public
	 * @return ftpClient
	 * @throws ftpException
	 */
	function setPort($port) {
		if ( !is_int($port) || ($port < 0) || ($port > 65535) ) {
			throw new ftpException("Invalid port. Has to be integer > 0");
		}
		$this->getOptionsSet()->setOptions(array(self::OPTION_PORT => $port));
		return $this;
	}

	/**
	 * Set the Username
	 *
	 * @param string $user The username to set
	 *
	 * @access public
	 * @return ftpClient
	 * @throws ftpException
	 */
	function setUsername($user) {
		if ( empty($user) || !is_string($user) ) {
			throw new ftpException("Username $user invalid");
		}
		$this->getOptionsSet()->setOptions(array(self::OPTION_USERNAME => $user));
		return $this;
	}

	/**
	 * Set the password
	 *
	 * @param string $password The password to set
	 *
	 * @access public
	 * @return ftpClient
	 * @throws ftpException
	 */
	function setPassword($password) {
		if ( empty($password) || !is_string($password) ) {
			throw new ftpException("Password cannot be empty");
		}
		$this->getOptionsSet()->setOptions(array(self::OPTION_PASSWORD => $password));
		return $this;
	}

	/**
	 * Set the transfer-mode. You can use the predefined constants
	 * FTP_ASCII or FTP_BINARY. The mode will be stored for any further transfers.
	 *
	 * @param int $mode The mode to set
	 *
	 * @access public
	 * @return ftpClient
	 * @throws ftpException
	 */
	function setMode($mode) {
		if ( ($mode == FTP_ASCII) || ($mode == FTP_BINARY) ) {
			$this->getOptionsSet()->setOptions(array(self::OPTION_TRANSFER_MODE => $mode));
			return $this;
		} else {
			throw new ftpException('FTP-Mode has either to be FTP_ASCII or FTP_BINARY');
		}
	}

	/**
	 * Set the transfer-method to passive mode
	 *
	 * @access public
	 * @return ftpClient
	 */
	function setPassive() {
		$this->getOptionsSet()->setOptions(array(self::OPTION_TRANSFER_METHOD => self::TRANSFER_METHOD_PASSIVE));
		@ftp_pasv($this->_handle, true);
		return $this;
	}

	/**
	 * Set the transfer-method to active mode
	 *
	 * @access public
	 * @return ftpClient
	 */
	function setActive() {
		$this->getOptionsSet()->setOptions(array(self::OPTION_TRANSFER_METHOD => self::TRANSFER_METHOD_ACTIVE));
		@ftp_pasv($this->_handle, false);
		return $this;
	}

	/**
	 * Set the timeout for FTP operations
	 *
	 * Use this method to set a timeout for FTP operation. Timeout has to be an
	 * integer.
	 *
	 * @param int $timeout the timeout to use
	 *
	 * @access public
	 * @return ftpClient
	 * @throws ftpException
	 */
	function setTimeout($timeout = 0) {
		if ( !is_int($timeout) || ($timeout < 0) ) {
			throw new ftpException('Timeout '.$timeout.' is invalid, has to be an integer >= 0');
		}
		$this->getOptionsSet()->setOptions(array(self::OPTION_TIMEOUT => $timeout));
		if ( isset($this->_handle) && is_resource($this->_handle) ) {
			$res = @ftp_set_option($this->_handle, FTP_TIMEOUT_SEC, $timeout);
		} else {
			$res = true;
		}
		if ( !$res ) {
			throw new ftpException("Set timeout failed.");
		}
		return $this;
	}

	/**
	 * Adds an extension to a mode-directory
	 *
	 * The mode-directory saves file-extensions coresponding to filetypes
	 * (ascii e.g.: 'php', 'txt', 'htm',...; binary e.g.: 'jpg', 'gif', 'exe',...).
	 * The extensions have to be saved without the '.'. And
	 * can be predefined in an external file (see: getExtensionsFile()).
	 *
	 * The array is build like this: 'php' => FTP_ASCII, 'png' => FTP_BINARY
	 *
	 * To change the mode of an extension, just add it again with the new mode!
	 *
	 * @param int    $mode Either FTP_ASCII or FTP_BINARY
	 * @param string $ext  Extension
	 *
	 * @access public
	 * @return ftpClient
	 */
	function addExtension($mode, $ext) {
		$this->_file_extensions[$ext] = $mode;
		return $this;
	}

	/**
	 * This function removes an extension from the mode-directories 
	 * (described above).
	 *
	 * @param string $ext The extension to remove
	 *
	 * @access public
	 * @return ftpClient
	 */
	function removeExtension($ext) {
		if ( isset($this->_file_extensions[$ext]) ) {
			unset($this->_file_extensions[$ext]);
		}
		return $this;
	}

	/**
	 * This get's both (ascii- and binary-mode-directories) from the given file.
	 * Beware, if you read a file into the mode-directory, all former set values 
	 * will be unset!
	 * 
	 * Example file contents:
	 * [ASCII]
	 * asc = 0
	 * txt = 0
	 * [BINARY]
	 * bin = 1
	 * jpg = 1
	 *
	 * @param string $filename The file to get from
	 *
	 * @access public
	 * @return boolean
	 * @throws ftpException
	 */
	function getExtensionsFile($filename) {
		if ( !file_exists($filename) ) {
			throw new ftpException("Extensions-file '$filename' does not exist");
		}
		if ( !is_readable($filename) ) {
			throw new ftpException("Extensions-file '$filename' is not readable");
		}
		
		$exts = @parse_ini_file($filename, true);
		if ( !is_array($exts) ) {
			throw new ftpException("Extensions-file '$filename' could not be loaded");
		}
		
		$this->_file_extensions = array();
		
		if ( isset($exts['ASCII']) ) {
			foreach ( $exts['ASCII'] as $ext => $bogus ) {
				$this->_file_extensions[$ext] = FTP_ASCII;
			}
		}
		if ( isset($exts['BINARY']) ) {
			foreach ( $exts['BINARY'] as $ext => $bogus ) {
				$this->_file_extensions[$ext] = FTP_BINARY;
			}
		}
		return true;
	}

	/**
	 * Returns the hostname
	 *
	 * @access public
	 * @return string The hostname
	 */
	function getHostname() {
		return $this->getOptionsSet()->getOptions(self::OPTION_HOSTNAME);
	}

	/**
	 * Returns the port
	 *
	 * @access public
	 * @return int The port
	 */
	function getPort() {
		return $this->getOptionsSet()->getOptions(self::OPTION_PORT, 21);
	}

	/**
	 * Returns the username
	 *
	 * @access public
	 * @return string The username
	 */
	function getUsername() {
		return $this->getOptionsSet()->getOptions(self::OPTION_USERNAME);
	}

	/**
	 * Returns the password
	 *
	 * @access public
	 * @return string The password
	 */
	function getPassword() {
		return $this->getOptionsSet()->getOptions(self::OPTION_PASSWORD);
	}

	/**
	 * Returns the transfermode, default FTP_ASCII
	 *
	 * @access public
	 * @return int The transfermode, either FTP_ASCII or FTP_BINARY.
	 */
	function getMode() {
		return $this->getOptionsSet()->getOptions(self::OPTION_TRANSFER_MODE, FTP_ASCII);
	}

	/**
	 * Returns, whether the connection is set to passive mode or not
	 *
	 * @access public
	 * @return bool True if passive, false if active mode
	 */
	function isPassive() {
		return $this->getOptionsSet()->getOptions(self::OPTION_TRANSFER_METHOD, self::TRANSFER_METHOD_PASSIVE);
	}

	/**
	 * Returns the mode set for a file-extension
	 *
	 * @param string $ext The extension you wanna ask for
	 *
	 * @return int Either FTP_ASCII, FTP_BINARY or NULL (if not set a mode for it)
	 * @access public
	 */
	function getExtensionMode($ext) {
		if ( array_key_exists($ext, $this->_file_extensions) ) {
			return $this->_file_extensions[$ext];
		}
		return null;
	}

	/**
	 * Returns the current timeout value, defaulting to 20 seconds
	 *
	 * @return integer
	 */
	function getTimeoutOption() {
		return $this->getOptionsSet()->getOptions(self::OPTION_TIMEOUT, 20);
	}

	/**
	 * Returns the actual timeout set on the current connection.
	 *
	 * @access public
	 * @return int The actual timeout
	 */
	function getTimeout() {
		return ftp_get_option($this->_handle, FTP_TIMEOUT_SEC);
	}

	/**
	 * Adds a ftpClientObserver instance to the list of observers 
	 * that are listening for messages emitted by this Net_FTP instance.
	 *
	 * @param ftpClientObserver $observer
	 * @return boolean True if the observer is successfully attached.
	 * @access public
	 */
	function attach(ftpClientObserver $observer) {
		$this->_listeners[$observer->getId()] = $observer;
		return true;
	}

	/**
	 * Removes a ftpClientObserver instance from the list of observers.
	 *
	 * @param ftpClientObserver $observer
	 * @return boolean True if the observer is successfully detached.
	 * @access public
	 */
	function detach(ftpClientObserver $observer) {
		if ( !isset($this->_listeners[$observer->getId()]) ) {
			return false;
		}
		
		unset($this->_listeners[$observer->getId()]);
		return true;
	}
	
	/**
	 * Returns true if $observer is a currently attached listener 
	 *
	 * @param ftpClientObserver $observer
	 * @return boolean
	 */
	function isAttached(ftpClientObserver $observer) {
		if ( isset($this->_listeners[$observer->getId()]) ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Informs each registered observer instance that a new message has been
	 * sent.                                                                
	 *                                                                      
	 * @param mixed $event A hash describing the net event.
	 * @return void
	 */
	function notify($event) {
		if ( count($this->_listeners) > 0 ) {
			if ( false ) $listener = new ftpClientObserver();
			foreach ( $this->_listeners as $id => $listener ) {
				$listener->notify($event);
			}
		}
	}
	
	/**
	 * Sets the directory listing matcher
	 *
	 * Use this method to set the directory listing matcher to a specific pattern.
	 * Indicate the pattern as a perl regular expression and give an array
	 * containing as keys the fields selected in the regular expression and as
	 * values the offset of the subpattern in the pattern.
	 *
	 * Example:
	 * $pattern = '/(?:(d)|.)([rwxt-]+)\s+(\w+)\s+([\w\d-]+)\s+([\w\d-]+)\s+(\w+)
	 *             \s+(\S+\s+\S+\s+\S+)\s+(.+)/',
	 * $matchmap = array(
	 *     'is_dir'        => 1,
	 *     'rights'        => 2,
	 *     'files_inside'  => 3,
	 *     'user'          => 4,
	 *     'group'         => 5,
	 *     'size'          => 6,
	 *     'date'          => 7,
	 *     'name'          => 8,
	 * )
	 *
	 * Make sure at least the is_dir and name keys are set. The is_dir key should
	 * point to a subpattern that is empty for non-directories and non-empty
	 * for directories.
	 *
	 * @param string $pattern  The new matcher pattern to use
	 * @param array  $matchmap An mapping from key to subpattern offset
	 *
	 * @access public
	 * @return ftpClient
	 * @throws ftpException
	 */
	function setDirMatcher($pattern, $matchmap) {
		if ( !is_string($pattern) ) {
			throw new ftpException('The supplied pattern is not a string');
		}
		if ( !is_array($matchmap) ) {
			throw new ftpException('The supplied matchmap pattern is not an array');
		} else {
			foreach ( $matchmap as $val ) {
				if ( !is_numeric($val) ) {
					throw new ftpException('The supplied pattern contains invalid value ' . $val);
				}
			}
		}
		
		$this->_matcher = array('pattern' => $pattern, 'map' => $matchmap);
		return $this;
	}

	/**
	 * Rebuild the path, if given relative
	 *
	 * This method will make a relative path absolute by prepending the current
	 * remote directory in front of it.
	 *
	 * @param string $path The path to check and construct
	 *
	 * @access private
	 * @return string The build path
	 */
	private function _constructPath($path) {
		if ( (substr($path, 0, 1) != '/') && (substr($path, 0, 2) != './') ) {
			$actual_dir = @ftp_pwd($this->_handle);
			if ( substr($actual_dir, -1) != '/' ) {
				$actual_dir .= '/';
			}
			$path = $actual_dir . $path;
		}
		return $path;
	}

	/**
	 * Checks whether the given path is a remote directory by trying to
	 * chdir() into it (and back out)
	 *
	 * @param string $path Path to check
	 *
	 * @access private
	 * @return mixed True if $path is a directory
	 * @throws ftpException
	 */
	private function _checkRemoteDir($path) {
		$pwd = $this->pwd();
		$res = $this->cd($path);
		$this->cd($pwd);
		return true;
	}

	/**
	 * This will remove a file
	 *
	 * @param string $file The file to delete
	 *
	 * @access private
	 * @return boolean
	 * @throws ftpException
	 */
	private function _rmFile($file) {
		if ( substr($file, 0, 1) != "/" ) {
			$actual_dir = @ftp_pwd($this->_handle);
			if ( substr($actual_dir, (strlen($actual_dir) - 2), 1) != "/" ) {
				$actual_dir .= "/";
			}
			$file = $actual_dir . $file;
		}
		$res = @ftp_delete($this->_handle, $file);
		
		if ( !$res ) {
			throw new ftpException("Could not delete file '$file'.");
		} else {
			return true;
		}
	}

	/**
	 * This will remove a dir
	 *
	 * @param string $dir The dir to delete
	 *
	 * @access private
	 * @return boolean
	 * @throws ftpException
	 */
	private function _rmDir($dir) {
		if ( substr($dir, (strlen($dir) - 1), 1) != "/" ) {
			throw new ftpException("Directory name '" . $dir . "' is invalid, has to end with '/'");
		}
		$res = @ftp_rmdir($this->_handle, $dir);
		if ( !$res ) {
			throw new ftpException("Could not delete directory '$dir'.");
		} else {
			return true;
		}
	}

	/**
	 * This will remove a dir and all subdirs and -files
	 *
	 * @param string $dir       The dir to delete recursively
	 * @param bool   $filesonly Only delete files so the directory structure is
	 *                          preserved 
	 *
	 * @access private
	 * @return boolean
	 * @throws ftpException
	 */
	private function _rmDirRecursive($dir, $filesonly = false) {
		if ( substr($dir, (strlen($dir) - 1), 1) != "/" ) {
			throw new ftpException("Directory name '" . $dir . "' is invalid, has to end with '/'");
		}
		$file_list = $this->_lsFiles($dir);
		foreach ( $file_list as $file ) {
			$file = $dir . $file["name"];
			$res = $this->rm($file);
		}
		
		$dir_list = $this->_lsDirs($dir);
		foreach ( $dir_list as $new_dir ) {
			if ( $new_dir["name"] == '.' || $new_dir["name"] == '..' ) {
				continue;
			}
			$new_dir = $dir . $new_dir["name"] . "/";
			$res = $this->_rmDirRecursive($new_dir, $filesonly);
		}
		
		if ( !$filesonly ) {
			$res = $this->_rmDir($dir);
		}
		
		return true;
	}

	/**
	 * Lists up files and directories
	 *
	 * @param string $dir The directory to list up
	 *
	 * @access private
	 * @return array An array of dirs and files
	 */
	private function _lsBoth($dir) {
		$list_splitted = $this->_listAndParse($dir);
		
		if ( !is_array($list_splitted["files"]) ) {
			$list_splitted["files"] = array();
		}
		if ( !is_array($list_splitted["dirs"]) ) {
			$list_splitted["dirs"] = array();
		}
		$res = array();
		@array_splice($res, 0, 0, $list_splitted["files"]);
		@array_splice($res, 0, 0, $list_splitted["dirs"]);
		return $res;
	}

	/**
	 * Lists up directories
	 *
	 * @param string $dir The directory to list up
	 *
	 * @access private
	 * @return array An array of dirs
	 */
	private function _lsDirs($dir) {
		$list = $this->_listAndParse($dir);
		return $list["dirs"];
	}

	/**
	 * Lists up files
	 *
	 * @param string $dir The directory to list up
	 *
	 * @access private
	 * @return array An array of files
	 */
	private function _lsFiles($dir) {
		$list = $this->_listAndParse($dir);
		return $list["files"];
	}

	/**
	 * This lists up the directory-content and parses the items into well-formated
	 * arrays.
	 * The results of this array are sorted (dirs on top, sorted by name;
	 * files below, sorted by name).
	 *
	 * @param string $dir The directory to parse
	 *
	 * @access private
	 * @return array Lists of dirs and files
	 * @throws ftpException
	 */
	private function _listAndParse($dir) {
		$dirs_list = array();
		$files_list = array();
		$dir_list = @ftp_rawlist($this->_handle, $dir);
		if ( !is_array($dir_list) ) {
			throw new ftpLsException('Could not get raw directory listing of '.$dir);
		}
		
		foreach ( $dir_list as $k => $v ) {
			if ( strncmp($v, 'total: ', 7) == 0 && preg_match('/total: \d+/', $v) ) {
				unset($dir_list[$k]);
				break; // usually there is just one line like this
			}
		}
		
		// Handle empty directories
		if ( count($dir_list) == 0 ) {
			return array('dirs' => $dirs_list, 'files' => $files_list);
		}
		
		// Exception for some FTP servers seem to return this weird result instead
		// of an empty list
		if ( count($dirs_list) == 1 && $dirs_list[0] == 'total 0' ) {
			return array('dirs' => array(), 'files' => $files_list);
		}
		
		if ( !isset($this->_matcher) ) {
			$this->_matcher = $this->_determineOSMatch($dir_list);
		}
		
		foreach ( $dir_list as $entry ) {
			if ( !preg_match($this->_matcher['pattern'], $entry, $m) ) {
				continue;
			}
			$entry = array();
			foreach ( $this->_matcher['map'] as $key => $val ) {
				$entry[$key] = $m[$val];
			}
			$entry['stamp'] = $this->_parseDate($entry['date']);
			
			if ( $entry['is_dir'] ) {
				$dirs_list[] = $entry;
			} else {
				$files_list[] = $entry;
			}
		}
		@usort($dirs_list, array("Net_FTP", "_natSort"));
		@usort($files_list, array("Net_FTP", "_natSort"));
		$res["dirs"] = (is_array($dirs_list)) ? $dirs_list : array();
		$res["files"] = (is_array($files_list)) ? $files_list : array();
		return $res;
	}

	/**
	 * Determine server OS
	 * This determines the server OS and returns a valid regex to parse
	 * ls() output.
	 *
	 * @param array &$dir_list The raw dir list to parse
	 *
	 * @access private
	 * @return mixed An array of 'pattern' and 'map' on success
	 * @throws ftpException
	 */
	private function _determineOSMatch(&$dir_list) {
		foreach ( $dir_list as $entry ) {
			foreach ( $this->_ls_match as $os => $match ) {
				if ( preg_match($match['pattern'], $entry) ) {
					return $match;
				}
			}
		}
		$error =
			'The list style of your server seems not to be supported. Please'.
			'email a "$ftp->ls(ftpClient::LS_MODE_RAWLIST);" output plus info on the'.
			'server to the maintainer of this package to get it supported!'.
			'Thanks for your help!';
		
		throw new ftpException($error);
	}

	/**
	 * Lists a local directory
	 *
	 * @param string $dir_path The dir to list
	 *
	 * @access private
	 * @return array The list of dirs and files
	 */
	private function _lsLocal($dir_path) {
		$dir = dir($dir_path);
		$dir_list = array();
		$file_list = array();
		while ( false !== ($entry = $dir->read()) ) {
			if ( ($entry != '.') && ($entry != '..') ) {
				if ( is_dir($dir_path . $entry) ) {
					$dir_list[] = $entry;
				} else {
					$file_list[] = $entry;
				}
			}
		}
		$dir->close();
		$res['dirs'] = $dir_list;
		$res['files'] = $file_list;
		return $res;
	}

	/**
	 * Function for use with usort().
	 * Compares the list-array-elements by name.
	 *
	 * @param string $item_1 first item to be compared
	 * @param string $item_2 second item to be compared
	 *
	 * @access private
	 * @return int < 0 if $item_1 is less than $item_2, 0 if equal and > 0 otherwise
	 */
	private function _natSort($item_1, $item_2) {
		return strnatcmp($item_1['name'], $item_2['name']);
	}

	/**
	 * Parse dates to timestamps
	 *
	 * @param string $date Date
	 *
	 * @access private
	 * @return int Timestamp
	 * @throws ftpException
	 */
	private function _parseDate($date) {
		// Sep 10 22:06 => Sep 10, <year> 22:06
		if ( preg_match('/([A-Za-z]+)[ ]+([0-9]+)[ ]+([0-9]+):([0-9]+)/', $date, $res) ) {
			$year = date('Y');
			$month = $res[1];
			$day = $res[2];
			$hour = $res[3];
			$minute = $res[4];
			$date = "$month $day, $year $hour:$minute";
			$tmpDate = strtotime($date);
			if ( $tmpDate > time() ) {
				$year--;
				$date = "$month $day, $year $hour:$minute";
			}
		} elseif ( preg_match('/^\d\d-\d\d-\d\d/', $date) ) {
			// 09-10-04 => 09/10/04
			$date = str_replace('-', '/', $date);
		}
		$res = strtotime($date);
		if ( !$res ) {
			throw new ftpException('Date conversion failed for '.$date);
		}
		return $res;
	}
}