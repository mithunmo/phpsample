<?php
/**
 * reportException
 *
 * Stored in reportException.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportException
 * @version $Rev: 650 $
 */


/**
 * reportException
 *
 * reportException class
 *
 * @package scorpio
 * @subpackage report
 * @category reportException
 */
class reportException extends systemException {

	/**
	 * Exception constructor
	 *
	 * @param string $inMessage
	 */
	function __construct($inMessage) {
		parent::__construct($inMessage);
	}
}



/**
 * reportManagerException
 *
 * reportManagerException class
 *
 * @package scorpio
 * @subpackage report
 * @category reportManagerException
 */
class reportManagerException extends reportException {
	
}

/**
 * reportManagerUnknownOutputFormatException
 *
 * reportManagerUnknownOutputFormatException class
 *
 * @package scorpio
 * @subpackage report
 * @category reportManagerUnknownOutputFormatException
 */
class reportManagerUnknownOutputFormatException extends reportManagerException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $inType
	 * @param string $inClass
	 */
	function __construct($inType, $inClass) {
		parent::__construct("Unknown output format ($inType), no writer found ($inClass)");
	}
}



/**
 * reportWriterException
 *
 * reportWriterException class
 *
 * @package scorpio
 * @subpackage report
 * @category reportWriterException
 */
class reportWriterException extends reportException {
	
}

/**
 * reportWriterOutputFileNotWritableException
 *
 * reportWriterOutputFileNotWritableException class
 *
 * @package scorpio
 * @subpackage report
 * @category reportWriterOutputFileNotWritableException
 */
class reportWriterOutputFileNotWritableException extends reportWriterException {

	/**
	 * Exception constructor
	 *
	 * @param string $inOutputFile
	 */
	function __construct($inOutputFile) {
		parent::__construct("Output file ($inOutputFile) is not writable");
	}
}