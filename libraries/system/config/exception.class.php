<?php
/**
 * systemConfigException.class.php
 * 
 * systemConfigException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemConfigException
 * @version $Rev: 650 $
 */


/**
 * systemConfigException
 * 
 * systemConfigException class
 *
 * @package scorpio
 * @subpackage system
 * @category systemConfigException
 */
class systemConfigException extends systemException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($message) {
		parent::__construct($message);
	}
}



/**
 * systemConfigRootConfigFileMissing
 *
 * systemConfigRootConfigFileMissing class
 * 
 * @package scorpio
 * @subpackage system
 * @category systemConfigRootConfigFileMissing
 */
class systemConfigRootConfigFileMissing extends systemConfigException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($filename) {
		parent::__construct("The root config file ($filename) could not be located or is not readable. System can not start!");
	}
}



/**
 * systemConfigFileNotReadable
 *
 * systemConfigFileNotReadable class
 * 
 * @package scorpio
 * @subpackage system
 * @category systemConfigFileNotReadable
 */
class systemConfigFileNotReadable extends systemConfigException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($filename) {
		parent::__construct("$filename either does not exist or cannot be read. Check file permissions.");
	}
}



/**
 * systemConfigFileNotValidXml
 *
 * systemConfigFileNotValidXml class
 * 
 * @package scorpio
 * @subpackage system
 * @category systemConfigFileNotValidXml
 */
class systemConfigFileNotValidXml extends systemConfigException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($filename) {
		parent::__construct("$filename is not valid XML and could not be parsed by SimpleXML.");
	}
}

/**
 * systemConfigFileCannotBeWritten
 *
 * systemConfigFileCannotBeWritten class
 *
 * @package scorpio
 * @subpackage system 
 * @category systemConfigFileCannotBeWritten
 */
class systemConfigFileCannotBeWritten extends systemConfigException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($filename) {
		parent::__construct("$filename could not be opened for writing, unable to save current config.");
	}
}



/**
 * systemConfigParamCannotBeOverridden
 *
 * systemConfigParamCannotBeOverridden class
 * 
 * @package scorpio
 * @subpackage system
 * @category systemConfigParamCannotBeOverridden
 */
class systemConfigParamCannotBeOverridden extends systemConfigException {
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct(systemConfigParam $oParam) {
		if ( $oParam instanceof systemConfigSection ) {
			parent::__construct("Section {$oParam->getParamName()} cannot be overridden by config file.");
		} else {
			parent::__construct("{$oParam->getParamName()} cannot be overridden with {$oParam->getParamValue()}. Check config file settings.");
		}
	}
}