<?php
/**
 * feedException
 * 
 * Stored in feedException
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage feed
 * @category feedException
 * @version $Rev: 650 $
 */


/**
 * feedException
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedException
 */
class feedException extends systemException {
	
}



/**
 * feedManagerException
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedManagerException
 */
class feedManagerException extends feedException {
	
}

/**
 * feedManagerUnableToReadFeedException
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedManagerUnableToReadFeedException
 */
class feedManagerUnableToReadFeedException extends feedManagerException {
	
	/**
	 * @see Exception::__construct()
	 * 
	 * @param string $inUri
	 */
	function __construct($inUri) {
		parent::__construct("Unable to open/read $inUri");
	}
}

/**
 * feedManagerUnableToDetectFeedException
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedManagerUnableToDetectFeedException
 */
class feedManagerUnableToDetectFeedException extends feedManagerException {
	
	/**
	 * @see Exception::__construct()
	 * 
	 * @param string $inUri
	 */
	function __construct($inUri) {
		parent::__construct("Unable to detect feed format from $inUri");
	}
}

/**
 * feedManagerUnsupportedFeedTypeException
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedManagerUnsupportedFeedTypeException
 */
class feedManagerUnsupportedFeedTypeException extends feedManagerException {
	
	/**
	 * @see Exception::__construct()
	 * 
	 * @param string $inUri
	 * @param string $inType
	 */
	function __construct($inUri, $inType) {
		parent::__construct("Unable to parse feed format $inType from $inUri");
	}
}