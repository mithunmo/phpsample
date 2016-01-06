<?php
/**
 * cacheException
 * 
 * Stored in exceptions.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cache
 * @version $Rev: 650 $
 */


/**
 * cacheException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheException
 */
class cacheException extends systemException {
	
	/**
	 * @see Exception::__construct()
	 */
	function __construct($inMessage, $inCode = null) {
		parent::__construct($inMessage, $inCode);
	}
}



/**
 * cacheControllerException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheControllerException
 */
class cacheControllerException extends cacheException {
	
	/**
	 * @see Exception::__construct()
	 */
	function __construct($inMessage, $inCode = null) {
		parent::__construct($inMessage, $inCode);
	}
}

/**
 * cacheControllerNoDataForKeyException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheControllerNoDataForKeyException
 */
class cacheControllerNoDataForKeyException extends cacheControllerException {
	
	/**
	 * @see Exception::__construct()
	 */
	function __construct() {
		parent::__construct('There is no data set to generate a key; set data before calling generateCacheId()');
	}
}



/**
 * cacheWriterException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterException
 */
class cacheWriterException extends cacheException {
	
	/**
	 * @see Exception::__construct()
	 */
	function __construct($inMessage, $inCode = null) {
		parent::__construct($inMessage, $inCode);
	}
}

/**
 * cacheWriterExtensionNotLoadedException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterExtensionNotLoadedException
 */
class cacheWriterExtensionNotLoadedException extends cacheWriterException {
	
	/**
	 * @see Exception::__construct()
	 */
	function __construct($inExtension) {
		parent::__construct("Extension ($inExtension) is not loaded, but is required for this writer");
	}
}



/**
 * cacheWriterFileException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterFileException
 */
class cacheWriterFileException extends cacheWriterException {
	
	/**
	 * @see Exception::__construct()
	 */
	function __construct($inMessage, $inCode = null) {
		parent::__construct($inMessage, $inCode);
	}
}

/**
 * cacheWriterFileStoreDoesNotExistException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterFileStoreDoesNotExistException
 */
class cacheWriterFileStoreDoesNotExistException extends cacheWriterFileException {
	
	/**
	 * @see Exception::__construct()
	 */
	function __construct($inFileStore) {
		parent::__construct("The file store located at ($inFileStore) does not exist");
	}
}

/**
 * cacheWriterFileStoreNotReadableException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterFileStoreNotReadableException
 */
class cacheWriterFileStoreNotReadableException extends cacheWriterFileException {

	/**
	 * @see Exception::__construct()
	 */
	function __construct($inFileStore) {
		parent::__construct("The file store located at ($inFileStore) is not readable by the current process");
	}
}

/**
 * cacheWriterFileStoreNotWritableException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterFileStoreNotWritableException
 */
class cacheWriterFileStoreNotWritableException extends cacheWriterFileException {

	/**
	 * @see Exception::__construct()
	 */
	function __construct($inFileStore) {
		parent::__construct("The file store located at ($inFileStore) is not writable by the current process");
	}
}

/**
 * cacheWriterCacheFileDoesNotExistException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterCacheFileDoesNotExistException
 */
class cacheWriterCacheFileDoesNotExistException extends cacheWriterFileException {
	
	/**
	 * @see Exception::__construct()
	 */
	function __construct($inCacheId, $inFile) {
		parent::__construct("Cache file ($inFile) does not exist for id ($inCacheId)");
	}
}

/**
 * cacheWriterCacheFileNotReadableException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterCacheFileNotReadableException
 */
class cacheWriterCacheFileNotReadableException extends cacheWriterFileException {
	
	/**
	 * @see Exception::__construct()
	 */
	function __construct($inCacheId, $inFile) {
		parent::__construct("Cache file ($inFile) for ($inCacheId) is not readable");
	}
}



/**
 * cacheWriterDatabaseException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterDatabaseException
 */
class cacheWriterDatabaseException extends cacheWriterException {
	
	/**
	 * @see Exception::__construct()
	 */
	function __construct($inMessage, $inCode = null) {
		parent::__construct($inMessage, $inCode);
	}
}

/**
 * cacheWriterDatabaseCacheDatabaseNotSetException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterDatabaseCacheDatabaseNotSetException
 */
class cacheWriterDatabaseCacheDatabaseNotSetException extends cacheWriterDatabaseException {
	
	/**
	 * @see Exception::__construct()
	 */
	function __construct() {
		parent::__construct("The cache database has not been specified");
	}
}

/**
 * cacheWriterDatabaseCacheTableNotSetException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterDatabaseCacheTableNotSetException
 */
class cacheWriterDatabaseCacheTableNotSetException extends cacheWriterDatabaseException {
	
	/**
	 * @see Exception::__construct()
	 */
	function __construct() {
		parent::__construct("The cache table has not been specified");
	}
}

/**
 * cacheWriterDatabaseProtectedTableException
 *
 * @package scorpio
 * @subpackage cache
 * @category cacheWriterDatabaseProtectedTableException
 */
class cacheWriterDatabaseProtectedDatabaseException extends cacheWriterDatabaseException {

	/**
	 * @see Exception::__construct()
	 */
	function __construct($inDatabase) {
		parent::__construct("Fatal error: ($inDatabase) is a system database and cannot be used with clearCache.");
	}
}