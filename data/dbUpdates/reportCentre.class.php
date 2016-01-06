<?php
/**
 * dbUpdateReportCentre
 * 
 * Stored in dbUpdateReportCentre
 * 
 * Holds updates to the reports database
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage db
 * @category dbUpdateReportCentre
 * @version $Rev: 6 $
 */


/**
 * dbUpdateReportCentre
 * 
 * Holds updates to the reports database
 *
 * @package scorpio
 * @subpackage db
 * @category dbUpdateReportCentre
 */
class dbUpdateReportCentre extends dbUpdateDefinition {
	
	/**
	 * Creates a new system update utility
	 *
	 * @return dbUpdateLog
	 */
	function __construct() {
		parent::__construct(system::getConfig()->getDatabase('reports')->getParamValue());
	}
	
	/**
	 * Initialise our updates for this database
	 */
	function initialiseUpdates() {
	}
}