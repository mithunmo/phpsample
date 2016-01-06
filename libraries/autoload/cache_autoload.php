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
	'cacheController' => 'cache/controller.class.php',

	'cacheException' => 'cache/exception.class.php',

	'cacheControllerException' => 'cache/exception.class.php',
	'cacheControllerNoDataForKeyException' => 'cache/exception.class.php',

	'cacheWriterException' => 'cache/exception.class.php',
	'cacheWriterExtensionNotLoadedException' => 'cache/exception.class.php',

	'cacheWriterFileException' => 'cache/exception.class.php',
	'cacheWriterFileStoreDoesNotExistException' => 'cache/exception.class.php',
	'cacheWriterFileStoreNotReadableException' => 'cache/exception.class.php',
	'cacheWriterFileStoreNotWritableException' => 'cache/exception.class.php',

	'cacheWriterCacheFileDoesNotExistException' => 'cache/exception.class.php',
	'cacheWriterCacheFileNotReadableException' => 'cache/exception.class.php',

	'cacheWriterDatabaseException' => 'cache/exception.class.php',
	'cacheWriterDatabaseCacheDatabaseNotSetException' => 'cache/exception.class.php',
	'cacheWriterDatabaseCacheTableNotSetException' => 'cache/exception.class.php',
	'cacheWriterDatabaseProtectedDatabaseException' => 'cache/exception.class.php',

	'cacheWriter' => 'cache/writer.class.php',
	'cacheWriterApc' => 'cache/writer/apc.class.php',
	'cacheWriterDatabase' => 'cache/writer/database.class.php',
	'cacheWriterDatabaseMysql' => 'cache/writer/database/mysql.class.php',
	'cacheWriterDatabaseSqlite' => 'cache/writer/database/sqlite.class.php',
	'cacheWriterFile' => 'cache/writer/file.class.php',
	'cacheWriterMemcache' => 'cache/writer/memcache.class.php',
);