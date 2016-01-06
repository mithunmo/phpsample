<?php
/**
 * dbUpdateCommsCentre
 * 
 * Stored in commsCentre.class.php
 * 
 * Holds updates to the comms database
 *
 * @author Dave Redfern
 * @copyright Mofilm Ltd (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbUpdateCommsCentre
 * @version $Rev: 6 $
 */


/**
 * dbUpdateCommsCentre
 * 
 * Holds updates to the commsCentre database
 *
 * @package scorpio
 * @subpackage db
 * @category dbUpdateCommsCentre
 */
class dbUpdateCommsCentre extends dbUpdateDefinition {
	
	/**
	 * Creates a new system update utility
	 *
	 * @return dbUpdateLog
	 */
	function __construct() {
		parent::__construct(system::getConfig()->getDatabase('comms')->getParamValue());
	}
	
	/**
	 * Initialise our updates for this database
	 */
	function initialiseUpdates() {
	}
}