<?php
/**
 * dbUpdateMofilmComms
 * 
 * Stored in mofilmMessages.class.php
 * 
 * Holds updates to the mofilm commsCentre database
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage db
 * @category dbUpdateMofilmMessages
 * @version $Rev: 393 $
 */


/**
 * dbUpdateMofilmMessages
 * 
 * Holds updates to the Mofilm commsCentre database
 *
 * @package scorpio
 * @subpackage db
 * @category dbUpdateMofilmMessages
 */
class dbUpdateMofilmMessages extends dbUpdateDefinition {
	
	/**
	 * Creates a new system update utility
	 *
	 * @return dbUpdateLog
	 */
	function __construct() {
		parent::__construct(system::getConfig()->getDatabase('comms')->getParamValue().'.mofilm');
	}
	
	/**
	 * Initialise our updates for this database
	 */
	function initialiseUpdates() {
		
	}
}