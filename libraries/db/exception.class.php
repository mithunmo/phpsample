<?php
/**
 * exception.class.php
 * 
 * Contains all the database system exceptions.
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbException
 * @version $Rev: 650 $
 */


/**
 * dbException
 * 
 * dbException class
 * 
 * @package scorpio
 * @subpackage db
 * @category dbException
 */
class dbException extends systemException {
	
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
 * dbManagerCannotBeInstantiated
 * 
 * dbManagerCannotBeInstantiated Exception class
 *
 * @package scorpio
 * @subpackage db
 * @category dbManagerCannotBeInstantiated
 */
class dbManagerCannotBeInstantiated extends dbException {
	
	/**
	 * Exception constructor
	 */
	function __construct() {
		parent::__construct('dbManager can not be instantiated, please use dbManager::getInstance()');
	}
}

/**
 * dbManagerMissingDsn
 * 
 * dbManagerMissingDsn Exception class
 *
 * @package scorpio
 * @subpackage db
 * @category dbManagerMissingDsn
 */
class dbManagerMissingDsn extends dbException {
	
	/**
	 * Exception constructor
	 */
	function __construct() {
		parent::__construct('dbManager is missing a valid DSN, please specify a DSN');
	}
}

/**
 * dbManagerDsnToDbOptionsParseFailed
 * 
 * dbManagerDsnToDbOptionsParseFailed Exception class
 *
 * @package scorpio
 * @subpackage db
 * @category dbManagerDsnToDbOptionsParseFailed
 */
class dbManagerDsnToDbOptionsParseFailed extends dbException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $inMessage
	 */
	function __construct($inMessage) {
		parent::__construct('dbManager could not parse the DSN string to a dbOptions object, specific error was: '.$inMessage);
	}
}

/**
 * dbManagerInvalidDriver
 * 
 * dbManagerInvalidDriver class
 *
 * @package scorpio
 * @subpackage db
 * @category dbManagerInvalidDriver
 */
class dbManagerInvalidDriver extends dbException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $inDriver
	 * @param array $inAvailDrivers
	 */
	function __construct($inDriver, $inAvailDrivers) {
		parent::__construct("Failed to locate $inDriver in available drivers: ".implode(', ', $inAvailDrivers).'.');
	}
}



/**
 * dbDriverException
 * 
 * dbDriverException class
 *
 * @package scorpio
 * @subpackage db
 * @category dbDriverException
 */
class dbDriverException extends dbException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $inMessage
	 * @param string $inError
	 */
	function __construct($inMessage, $inError = false) {
		if ( strtolower(php_sapi_name()) === 'cli' ) {
			$inMessage = $inMessage."\ndbOptions Error Message:\n".$inError;
		} else {
			$inMessage = $inMessage.'<br />dbOptions Error Message:<br />'.$inError;
		}
		parent::__construct($inMessage);
	}
}

/**
 * dbDriverTransactionException
 *
 * dbDriverTransactionException class
 * 
 * @package scorpio
 * @subpackage db
 * @category dbDriverTransactionException
 */
class dbDriverTransactionException extends dbDriverException {
	
	/**
	 * Exception constructor
	 */
	function __construct($inMessage) {
		parent::__construct($inMessage);
	}
}

/**
 * dbDriverMissingDbOption
 * 
 * dbDriverMissingDbOption class
 *
 * @package scorpio
 * @subpackage db
 * @category dbDriverMissingDbOption
 */
class dbDriverMissingDbOption extends dbDriverException {
	
	/**
	 * Exception constructor
	 *
	 * @param string $option
	 */
	function __construct($inOption) {
		parent::__construct("Missing $inOption from dbOptions, cannot continue");
	}
}



/**
 * dbOptionsException
 * 
 * dbOptionsException class
 *
 * @package scorpio
 * @subpackage db
 * @category dbOptionsException
 */
class dbOptionsException extends dbException {
	
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
 * dbUtilitiesException
 * 
 * dbUtilitiesException class
 *
 * @package scorpio
 * @subpackage db
 * @category dbUtilitiesException
 */
class dbUtilitiesException extends dbException {
	
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
 * dbMapperException
 * 
 * dbMapperException class
 *
 * @package scorpio
 * @subpackage db
 * @category dbMapperException
 */
class dbMapperException extends dbException {
	
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
 * dbMapperNoDatabaseSelected
 *
 * dbMapperNoDatabaseSelected class
 * 
 * @package scorpio
 * @subpackage db
 * @category dbMapperNoDatabaseSelected
 */
class dbMapperNoDatabaseSelected extends dbMapperException {
	
	/**
	 * Exception constructor
	 */
	function __construct() {
		parent::__construct('dbMapper has no database selected');
	}
}