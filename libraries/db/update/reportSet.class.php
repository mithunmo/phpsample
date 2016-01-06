<?php
/**
 * dbUpdateReportSet.class.php
 * 
 * Holds a set of reports about a system update
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbUpdateReportSet
 * @version $Rev: 650 $
 */


/**
 * dbUpdateReportSet Class
 * 
 * Aggregates a set of reports for display or storing depending on commit status.
 * Each individual report contains data for ONE database, this class holds
 * multiple reports on multiple databases.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbUpdateReportSet
 */
class dbUpdateReportSet extends baseSet {
	
	/**
	 * Returns a new report set
	 *
	 * @return dbUpdateReportSet
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * Resets the set
	 *
	 * @return void
	 */
	function reset() {
		$this->_resetSet();
	}
	
	
	
	/**
	 * Add a report to the set
	 *
	 * @param dbUpdateReport $inReport
	 * @return dbUpdateReportSet
	 */
	function addReport(dbUpdateReport $inReport) {
		return $this->_setValue($inReport);
	}
	
	/**
	 * Returns a report at index $inKey
	 *
	 * @param integer $inKey
	 * @return dbUpdateReport
	 */
	function getReport($inKey) {
		return $this->_getItem($inKey);
	}
	
	/**
	 * Removes a report from the set
	 *
	 * @param dbUpdateReport $inReport
	 * @return dbUpdateReportSet
	 */
	function removeReport(dbUpdateReport $inReport) {
		return $this->_removeItemWithValue($inReport);
	}
	
	/**
	 * Returns number of reports in set
	 *
	 * @return integer
	 */
	function getCount() {
		return $this->_itemCount();
	}
	
	/**
	 * Returns true if any update failed
	 *
	 * @return boolean
	 */
	function hasError() {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new dbUpdateReport();
			foreach ( $this as $oObject ) {
				 if ( $oObject->hasError() ) {
				 	return true;
				 }
			}
		}
		return false;
	}
}