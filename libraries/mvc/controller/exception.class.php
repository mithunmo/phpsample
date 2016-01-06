<?php
/**
 * mvcControllerException.class.php
 * 
 * mvcControllerException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerException
 * @version $Rev: 650 $
 */


/**
 * mvcControllerException
 * 
 * mvcControllerException class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerException
 */
class mvcControllerException extends mvcException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}





/**
 * mvcMapException
 * 
 * mvcMapException class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcMapException
 */
class mvcMapException extends mvcControllerException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}

/**
 * mvcMapConfigFileDoesNotExist
 *
 * mvcMapConfigFileDoesNotExist exception class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcMapConfigFileDoesNotExist
 */
class mvcMapConfigFileDoesNotExist extends mvcMapException {
	
	/**
	 * @see mvcMapException::__construct()
	 *
	 * @param string $inConfigFile
	 */
	function __construct($inConfigFile) {
		parent::__construct("$inConfigFile does not exist");
	}
}

/**
 * mvcMapConfigFileNotReadable
 *
 * mvcMapConfigFileNotReadable exception class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcMapConfigFileNotReadable
 */
class mvcMapConfigFileNotReadable extends mvcMapException {
	
	/**
	 * @see mvcMapException::__construct()
	 *
	 * @param string $inConfigFile
	 */
	function __construct($inConfigFile) {
		parent::__construct("$inConfigFile cannot be read");
	}
}

/**
 * mvcMapConfigFileIsNotValidXml
 *
 * mvcMapConfigFileIsNotValidXml exception class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcMapConfigFileIsNotValidXml
 */
class mvcMapConfigFileIsNotValidXml extends mvcMapException {
	
	/**
	 * @see mvcMapException::__construct()
	 *
	 * @param string $inConfigFile
	 */
	function __construct($inConfigFile) {
		parent::__construct("$inConfigFile does not contain valid XML data");
	}
}

/**
 * mvcMapConfigFileIsNotWritable
 *
 * mvcMapConfigFileIsNotWritable exception class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcMapConfigFileIsNotWritable
 */
class mvcMapConfigFileIsNotWritable extends mvcMapException {
	
	/**
	 * @see mvcMapException::__construct()
	 *
	 * @param string $inConfigFile
	 */
	function __construct($inConfigFile) {
		parent::__construct("$inConfigFile cannot be written to, please check file permissions");
	}
}

/**
 * mvcMapConfigFileCouldNotBeWritten
 *
 * mvcMapConfigFileCouldNotBeWritten exception class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcMapConfigFileCouldNotBeWritten
 */
class mvcMapConfigFileCouldNotBeWritten extends mvcMapException {
	
	/**
	 * @see mvcMapException::__construct()
	 *
	 * @param string $inConfigFile
	 */
	function __construct($inConfigFile) {
		parent::__construct("The controllerMap data could not be written to $inConfigFile, there may have been no data");
	}
}

/**
 * mvcMapConfigurationDataNotLoaded
 *
 * mvcMapConfigurationDataNotLoaded exception class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcMapConfigurationDataNotLoaded
 */
class mvcMapConfigurationDataNotLoaded extends mvcMapException {
	
	/**
	 * @see mvcMapException::__construct()
	 */
	function __construct() {
		parent::__construct("No controllerMap data has been loaded");
	}
}

/**
 * mvcMapControllerNotFound
 *
 * mvcMapControllerNotFound exception class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcMapControllerNotFound
 */
class mvcMapControllerNotFound extends mvcMapException {
	
	/**
	 * @see mvcMapException::__construct()
	 *
	 * @param string $inControllerName
	 */
	function __construct($inControllerName) {
		parent::__construct("$inControllerName could not be located in controllerMap");
	}
}

/**
 * mvcMapNonUniqueControllerName
 *
 * mvcMapNonUniqueControllerName exception class
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcMapNonUniqueControllerName
 */
class mvcMapNonUniqueControllerName extends mvcMapException {

	/**
	 * @see mvcMapException::__construct()
	 *
	 * @param string $inControllerName
	 */
	function __construct($inControllerName) {
		parent::__construct("$inControllerName is not a unique controller; this is a configuration error");
	}
}

/**
 * mvcMapNoPathComponentsToSearch
 * 
 * mvcMapNoPathComponentsToSearch exception class
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcMapNoPathComponentsToSearch
 */
class mvcMapNoPathComponentsToSearch extends mvcMapException {
	
	/**
	 * @see mvcMapException::__construct()
	 *
	 * @param string $inRequestPath
	 */
	function __construct($inRequestPath) {
		parent::__construct("No path components were located from $inRequestPath");
	}
}