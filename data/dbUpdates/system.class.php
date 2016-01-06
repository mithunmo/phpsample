<?php
/**
 * dbUpdateSystem
 * 
 * Stored in system.class.php
 * 
 * Holds updates to the main system database
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage db
 * @category dbUpdateSystem
 * @version $Rev: 299 $
 */


/**
 * dbUpdateSystem
 * 
 * Holds updates to the main system database
 *
 * @package scorpio
 * @subpackage db
 * @category dbUpdateSystem
 */
class dbUpdateSystem extends dbUpdateDefinition {
	
	/**
	 * Creates a new system update utility
	 *
	 * @return dbUpdateSystem
	 */
	function __construct() {
		parent::__construct(system::getConfig()->getDatabase('system')->getParamValue());
	}
	
	/**
	 * Initialise our updates for this database
	 */
	function initialiseUpdates() {
	}
}