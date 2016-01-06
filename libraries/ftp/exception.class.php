<?php
/**
 * ftpException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage ftp
 * @category ftpException
 * @version $Rev: 650 $
 */


/**
 * ftpException class
 * 
 * @package scorpio
 * @subpackage ftp
 * @category ftpException
 */
class ftpException extends systemException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $message
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}

/**
 * ftpConnectException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpConnectException
 */
class ftpConnectException extends ftpException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $hostname
	 * @param integer $port
	 */
	function __construct($hostname, $port) {
		parent::__construct("Connection to host '$hostname:$port' failed");
	}
}

/**
 * ftpDisconnectException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpDisconnectException
 */
class ftpDisconnectException extends ftpException {
	
	/**
	 * Exception constructor
	 */
	function __construct() {
		parent::__construct('Disconnect failed.');
	}
}

/**
 * ftpLoginException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpLoginException
 */
class ftpLoginException extends ftpException {
	
	/**
	 * Exception constructor
	 * 
	 * @param string $username
	 * @param string $password
	 */
	function __construct($username, $password) {
		parent::__construct("Unable to login user $username");
	}
}

/**
 * ftpCdException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpCdException
 */
class ftpCdException extends ftpException {
	
	/**
	 * Exception constructor
	 * 
	 * @param string $dir
	 */
	function __construct($dir) {
		parent::__construct("Directory change failed for $dir");
	}
}

/**
 * ftpPwdException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpPwdException
 */
class ftpPwdException extends ftpException {
	
	/**
	 * Exception constructor
	 */
	function __construct() {
		parent::__construct("Could not determine the actual path");
	}
}

/**
 * ftpMkdirException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpMkdirException
 */
class ftpMkdirException extends ftpException {
	
	/**
	 * Exception constructor
	 * 
	 * @param string $dir
	 */
	function __construct($dir) {
		parent::__construct("Creation of '$dir' failed");
	}
}

/**
 * ftpExecCommandException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpExecCommandException
 */
class ftpExecCommandException extends ftpException {
	
	/**
	 * Exception constructor
	 * 
	 * @param string $command
	 */
	function __construct($command) {
		parent::__construct("Execution of command '$command' failed");
	}
}

/**
 * ftpSiteCommandException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpSiteCommandException
 */
class ftpSiteCommandException extends ftpException {
	
	/**
	 * Exception constructor
	 * 
	 * @param string $command
	 */
	function __construct($command) {
		parent::__construct("Execution of SITE command '$command' failed");
	}
}

/**
 * ftpChmodException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpChmodException
 */
class ftpChmodException extends ftpException {
	
	/**
	 * Exception constructor
	 * 
	 * @param string $target
	 * @param integer $permissions
	 */
	function __construct($target, $permissions) {
		parent::__construct("CHMOD $permissions $target failed");
	}
}

/**
 * ftpRenameException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpRenameException
 */
class ftpRenameException extends ftpException {
	
	/**
	 * Exception constructor
	 * 
	 * @param string $remote_from
	 * @param string $remote_to
	 */
	function __construct($remote_from, $remote_to) {
		parent::__construct("Could not rename " . $remote_from . " to " . $remote_to);
	}
}

/**
 * ftpLsException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpLsException
 */
class ftpLsException extends ftpException {}

/**
 * ftpMdtmException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpMdtmException
 */
class ftpMdtmException extends ftpException {}

/**
 * ftpSizeException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpSizeException
 */
class ftpSizeException extends ftpException {
	
	/**
	 * Exception constructor
	 * 
	 * @param string $file
	 */
	function __construct($file) {
		parent::__construct("Could not determine filesize of '$file'");
	}
}

/**
 * ftpGetException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpGetException
 */
class ftpGetException extends ftpException {}

/**
 * ftpGetRecursiveException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpGetRecursiveException
 */
class ftpGetRecursiveException extends ftpException {}

/**
 * ftpGetLocalFileExistsException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpGetLocalFileExistsException
 */
class ftpGetLocalFileExistsException extends ftpException {
	
	/**
	 * Exception constructor
	 * 
	 * @param string $file
	 */
	function __construct($file) {
		parent::__construct("Local file '$file' exists and may not be overwriten.");
	}
}

/**
 * ftpGetLocalFileNotWritableException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpGetLocalFileNotWritableException
 */
class ftpGetLocalFileNotWritableException extends ftpException {
	
	/**
	 * Exception constructor
	 * 
	 * @param string $file
	 */
	function __construct($file) {
		parent::__construct("Local file '$file' is not writeable. Can not overwrite.");
	}
}

/**
 * ftpPutException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpPutException
 */
class ftpPutException extends ftpException {}

/**
 * ftpPutRecursiveException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpPutRecursiveException
 */
class ftpPutRecursiveException extends ftpException {}

/**
 * ftpPutOptionsException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpPutOptionsException
 */
class ftpPutOptionsException extends ftpPutException {
	
	/**
	 * Exception constructor
	 */
	function __construct() {
		parent::__construct('Bad options given: ftpClient::PUT_NONBLOCKING and ftpClient::PUT_BLOCKING cannot both be set');
	}
}

/**
 * ftpPutLocalFileDoesNotExistException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpPutLocalFileDoesNotExistException
 */
class ftpPutLocalFileDoesNotExistException extends ftpException {
	
	/**
	 * Exception constructor
	 * 
	 * @param string $file
	 */
	function __construct($file) {
		parent::__construct("Local file '$file' does not exist.");
	}
}

/**
 * ftpPutRemoteFileExistsNoOverwriteException class
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpPutRemoteFileExistsNoOverwriteException
 */
class ftpPutRemoteFileExistsNoOverwriteException extends ftpException {
	
	/**
	 * Exception constructor
	 * 
	 * @param string $file
	 */
	function __construct($file) {
		parent::__construct("Remote file '$file' exists and may not be overwriten.");
	}
}