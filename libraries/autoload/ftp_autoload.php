<?php
/**
 * system Autoload component
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemAutoload
 */
return array(
	'ftpClient' => 'ftp/client.class.php',
	'ftpClientObserver' => 'ftp/client/observer.class.php',

	'ftpException' => 'ftp/exception.class.php',
	'ftpConnectException' => 'ftp/exception.class.php',
	'ftpDisconnectException' => 'ftp/exception.class.php',
	'ftpLoginException' => 'ftp/exception.class.php',
	'ftpCdException' => 'ftp/exception.class.php',
	'ftpPwdException' => 'ftp/exception.class.php',
	'ftpMkdirException' => 'ftp/exception.class.php',
	'ftpExecCommandException' => 'ftp/exception.class.php',
	'ftpSiteCommandException' => 'ftp/exception.class.php',
	'ftpChmodException' => 'ftp/exception.class.php',
	'ftpRenameException' => 'ftp/exception.class.php',
	'ftpLsException' => 'ftp/exception.class.php',
	'ftpMdtmException' => 'ftp/exception.class.php',
	'ftpSizeException' => 'ftp/exception.class.php',
	'ftpGetException' => 'ftp/exception.class.php',
	'ftpGetRecursiveException' => 'ftp/exception.class.php',
	'ftpGetLocalFileExistsException' => 'ftp/exception.class.php',
	'ftpGetLocalFileNotWritableException' => 'ftp/exception.class.php',
	'ftpPutException' => 'ftp/exception.class.php',
	'ftpPutRecursiveException' => 'ftp/exception.class.php',
	'ftpPutOptionsException' => 'ftp/exception.class.php',
	'ftpPutLocalFileDoesNotExistException' => 'ftp/exception.class.php',
	'ftpPutRemoteFileExistsNoOverwriteException' => 'ftp/exception.class.php',
);