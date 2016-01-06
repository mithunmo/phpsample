<?php
/**
 * mvcFileException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileException
 * @version $Rev: 841 $
 */


/**
 * mvcFileException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileException
 */
class mvcFileException extends systemException {
	
}

/**
 * mvcFileUploadException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileUploadException
 */
class mvcFileUploadException extends mvcFileException {
	
}

/**
 * mvcFileUploadInvalidException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileUploadInvalidException
 */
class mvcFileUploadInvalidException extends mvcFileUploadException {
	
}

/**
 * mvcFileUploadNoFileUploadedException class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileUploadNoFileUploadedException
 */
class mvcFileUploadNoFileUploadedException extends mvcFileUploadException {

}

/**
 * mvcFileUploadMoveUploadedFileException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileUploadMoveUploadedFileException
 */
class mvcFileUploadMoveUploadedFileException extends mvcFileUploadException {
	
}

/**
 * mvcFileUploadArrayException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileUploadArrayException
 */
class mvcFileUploadArrayException extends mvcFileUploadException {
	
}

/**
 * mvcFileFileStoreDoesNotExistException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileFileStoreDoesNotExistException
 */
class mvcFileFileStoreDoesNotExistException extends mvcFileException {
	
	/**
	 * @see mvcFileException::__construct()
	 *
	 * @param string $inFileStore
	 */
	function __construct($inFileStore) {
		parent::__construct("The file store ($inFileStore) does not exist");
	}
}

/**
 * mvcFileFileStoreNotReadableException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileFileStoreNotReadableException
 */
class mvcFileFileStoreNotReadableException extends mvcFileException {
	
	/**
	 * @see mvcFileException::__construct()
	 *
	 * @param string $inFileStore
	 */
	function __construct($inFileStore) {
		parent::__construct("The file store ($inFileStore) is not readable by the current process");
	}
}

/**
 * mvcFileFileStoreNotWritableException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileFileStoreNotWritableException
 */
class mvcFileFileStoreNotWritableException extends mvcFileException {
	
	/**
	 * @see mvcFileException::__construct()
	 *
	 * @param string $inFileStore
	 */
	function __construct($inFileStore) {
		parent::__construct("The file store ($inFileStore) is not writable by the current process");
	}
}

/**
 * mvcFileFileStoreBuildFailedException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileFileStoreBuildFailedException
 */
class mvcFileFileStoreBuildFailedException extends mvcFileException {

	/**
	 * @see mvcFileException::__construct()
	 *
	 * @param string $inFileStore
	 */
	function __construct($inFileStore) {
		parent::__construct("The file store ($inFileStore) could not be created");
	}
}

/**
 * mvcFileFileStoreBuildSubFoldersFailedException class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcFileFileStoreBuildSubFoldersFailedException
 */
class mvcFileFileStoreBuildSubFoldersFailedException extends mvcFileException {

	/**
	 * @see mvcFileException::__construct()
	 *
	 * @param string $inSubFolder
	 * @param string $inFileStore
	 */
	function __construct($inSubFolder, $inFileStore) {
		parent::__construct("The file folder structure ($inSubFolder) could not be created in the file store ($inFileStore)");
	}
}