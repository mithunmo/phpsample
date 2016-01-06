<?php
/**
 * dbUpdateLogging
 * 
 * Stored in logging.class.php
 * 
 * Holds updates to the main log database
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage db
 * @category dbUpdateLogging
 * @version $Rev: 6 $
 */


/**
 * dbUpdateLogging
 * 
 * Holds updates to the main log database
 *
 * @package scorpio
 * @subpackage db
 * @category dbUpdateLogging
 */
class dbUpdateLogging extends dbUpdateDefinition {
	
	/**
	 * Creates a new system update utility
	 *
	 * @return dbUpdateLog
	 */
	function __construct() {
		parent::__construct(system::getConfig()->getDatabase('logging')->getParamValue());
	}
	
	/**
	 * Initialise our updates for this database
	 */
	function initialiseUpdates() {
		
	}
}