<?php
/**
 * dbUpdateMofilmReports
 * 
 * Stored in dbUpdateMofilmReports
 * 
 * Holds updates to the mofilm reports database
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage db
 * @category dbUpdateMofilmReports
 * @version $Rev: 349 $
 */


/**
 * dbUpdateMofilmReports
 * 
 * Holds updates to the Mofilm reports database
 *
 * @package scorpio
 * @subpackage db
 * @category dbUpdateMofilmReports
 */
class dbUpdateMofilmReports extends dbUpdateDefinition {
	
	/**
	 * Creates a new system update utility
	 *
	 * @return dbUpdateLog
	 */
	function __construct() {
		parent::__construct(system::getConfig()->getDatabase('reports')->getParamValue().'.mofilm');
	}
	
	/**
	 * Initialise our updates for this database
	 */
	function initialiseUpdates() {
	}
}