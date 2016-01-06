<?php
/**
 * dbUpdateWurfl
 * 
 * Stored in wurfl.class.php
 * 
 * Holds updates to the main wurfl database
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage db
 * @category dbUpdateWurfl
 * @version $Rev: 6 $
 */


/**
 * dbUpdateWurfl
 * 
 * Holds updates to the main wurfl database
 *
 * @package scorpio
 * @subpackage db
 * @category dbUpdateWurfl
 */
class dbUpdateWurfl extends dbUpdateDefinition {
	
	/**
	 * Creates a new system update utility
	 *
	 * @return dbUpdateWurfl
	 */
	function __construct() {
		parent::__construct(system::getConfig()->getDatabase('wurfl')->getParamValue());
	}
	
	/**
	 * Initialise our updates for this database
	 */
	function initialiseUpdates() {
		
	}
}