<?php
/**
 * translateException class
 * 
 * Stored in translateException.class.php
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateException
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 722 $
 */


/**
 * translateException
 * 
 * translateException class
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateException
 */
class translateException extends systemException {
	
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
 * translateAdaptorException
 * 
 * translateAdaptorException class
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorException
 */
class translateAdaptorException extends translateException {
	
}

/**
 * translateAdaptorRequestedLanguageNotAvailableException
 * 
 * translateAdaptorRequestedLanguageNotAvailableException class
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorRequestedLanguageNotAvailableException
 */
class translateAdaptorRequestedLanguageNotAvailableException extends translateAdaptorException {
	
	/**
	 * @see Exception::__construct()
	 * 
	 * @param string $inLocale
	 */
	function __construct($inLocale) {
		parent::__construct("The language '{$inLocale}' has to be added before it can be used");
	}
}

/**
 * translateAdaptorTranslationNotAvailableException
 * 
 * translateAdaptorTranslationNotAvailableException class
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateAdaptorTranslationNotAvailableException
 */
class translateAdaptorTranslationNotAvailableException extends translateAdaptorException {
	
	/**
	 * @see Exception::__construct()
	 * 
	 * @param string $inLocale
	 */
	function __construct($inLocale) {
		parent::__construct("No translation for the language '{$inLocale}' available");
	}
}