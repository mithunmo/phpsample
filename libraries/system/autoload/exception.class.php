<?php
/**
 * systemAutoloadException.class.php
 * 
 * systemAutoloadException class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemAutoloadException
 * @version $Rev: 650 $
 */


/**
 * systemAutoloadException
 * 
 * system Autoload Exception
 *
 * @package scorpio
 * @subpackage system
 * @category systemAutoloadException
 */
class systemAutoloadException extends systemException {
	
	/**
	 * Error message from autoload system, %class% and %path% are replaced with the relevant values
	 *
	 * @var string
	 */
	protected $_Message		= 'Unknown error occured while trying to work with %class%';
	
	/**
	 * @see systemException::__construct()
	 */
	function __construct($inClassname, $inClassPath) {
		parent::__construct(str_ireplace(array('%class%','%path%'), array($inClassname, $inClassPath), $this->_Message));
	}
}



/**
 * systemAutoloaderFileDoesNotExist
 *
 * @package scorpio
 * @subpackage system
 * @category systemAutoloaderFileDoesNotExist
 */
class systemAutoloadFileDoesNotExist extends systemAutoloadException {
	
	/**
	 * @see systemAutoloadException::_message
	 */
	protected $_Message = '%path% does not exist';
}



/**
 * systemAutoloaderFileIsNotReadable
 *
 * @package scorpio
 * @subpackage system
 * @category systemAutoloaderFileIsNotReadable
 */
class systemAutoloadFileIsNotReadable extends systemAutoloadException {
	
	/**
	 * @see systemAutoloadException::_message
	 */
	protected $_Message = '%path% is not readable';
}



/**
 * systemAutoloaderClassCouldNotBeLoaded
 *
 * @package scorpio
 * @subpackage system
 * @category systemAutoloaderClassCouldNotBeLoaded
 */
class systemAutoloadClassCouldNotBeLoaded extends systemAutoloadException {
	
	/**
	 * @see systemAutoloadException::_message
	 */
	protected $_Message = '%class% could not be loaded from %path% possible parse error';
}



/**
 * systemAutoloadInvalidCacheFile
 *
 * @package scorpio
 * @subpackage system
 * @category systemAutoloadInvalidCacheFile
 */
class systemAutoloadInvalidCacheFile extends systemAutoloadException {
	
	/**
	 * @see systemAutoloadException::_message
	 */
	protected $_Message = 'Autoload cache file from %path% either did not return autoload data';
}



/**
 * systemAutoloaderClassDoesNotExistInAutoloadCache
 *
 * @package scorpio
 * @subpackage system
 * @category systemAutoloaderClassDoesNotExistInAutoloadCache
 */
class systemAutoloadClassDoesNotExistInAutoloadCache extends systemAutoloadException {
	
	/**
	 * @see systemAutoloadException::_message
	 */
	protected $_Message = '%class% does not exist in autoload cache file %path%';
}