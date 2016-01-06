<?php
/**
 * systemReporterInterface.class.php
 * 
 * System Reporter Interface
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemReporterInterface
 * @version $Rev: 736 $
 */


/**
 * systemReporterInterface
 * 
 * systemReporterInterface provides standard method for the log summary system
 * 
 * @package scorpio
 * @subpackage system
 * @category systemReporterInterface
 */
interface systemReporterInterface {
	
	/**
	 * Returns the report data for the source
	 * 
	 * @return string
	 */
	function getData();
	
	/**
	 * Returns an array of dates for use in the reporter agent
	 * 
	 * @return array
	 */
	function getDates();
	
	/**
	 * Adds a date to the reporters list of dates
	 * 
	 * @param string $inDate Valid date in string format e.g. 21/01/2010
	 * @return systemReporterInterface
	 */
	function addDate($inDate);
	
	/**
	 * Sets an array of dates to report on
	 * 
	 * @param array $inDates
	 * @return systemReporterInterface
	 */
	function setDates(array $inDates);
}