<?php
/**
 * dbUpdateReport.class.php
 * 
 * Aggregates logs for a single database for display or storing depending on commit status
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbUpdateReport
 * @version $Rev: 707 $
 */


/**
 * dbUpdateReport Class
 * 
 * Aggregates logs for a single database for display or storing depending on commit status
 * 
 * @package scorpio
 * @subpackage db
 * @category dbUpdateReport
 */
class dbUpdateReport extends baseSet {
	
	/**
	 * Stores $_Database
	 *
	 * @var string
	 * @access private
	 */
	private $_Database;
	
	/**
	 * Stores $_StartDate
	 *
	 * @var datetime
	 * @access private
	 */
	private $_StartDate;
	
	/**
	 * Stores $_EndDate
	 *
	 * @var datetime
	 * @access private
	 */
	private $_EndDate;
	
	
	
	/**
	 * Creates a new db report object
	 *
	 * @param string $inDatabase
	 * @return dbUpdateReport
	 */
	function __construct($inDatabase = null) {
		$this->reset();
		if ( $inDatabase ) {
			$this->setDatabase($inDatabase);
		}
	}
	
	/**
	 * Store all log records generated so far by the update process
	 *
	 * @return boolean
	 */
	function save() {
		$return = false;
		if ( $this->getCount() > 0 ) {
			$return = true;
			if ( false ) $oLog = new dbUpdateLog();
			foreach ( $this as $oLog ) {
				$return = $oLog->save() && $return;
			}
		}
		return $return;
	}
	
	/**
	 * Resets the set
	 * 
	 * @return void
	 */
	function reset() {
		$this->_Database = null;
		$this->_StartDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_EndDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		parent::_resetSet();
	}
	
	
	
	/**
	 * Returns $_Database
	 *
	 * @return string
	 * @access public
	 */
	function getDatabase() {
		return $this->_Database;
	}
	
	/**
	 * Set $_Database to $inDatabase
	 *
	 * @param string $inDatabase
	 * @return dbUpdateReport
	 * @access public
	 */
	function setDatabase($inDatabase) {
		if ( $this->_Database !== $inDatabase ) {
			$this->_Database = $inDatabase;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_StartDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getStartDate() {
		return $this->_StartDate;
	}
	
	/**
	 * Set $_StartDate to $inStartDate
	 *
	 * @param datetime $inStartDate
	 * @return dbUpdateReport
	 * @access public
	 */
	function setStartDate($inStartDate) {
		if ( $this->_StartDate !== $inStartDate ) {
			$this->_StartDate = $inStartDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_EndDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getEndDate() {
		return $this->_EndDate;
	}
	
	/**
	 * Set $_EndDate to $inEndDate
	 *
	 * @param datetime $inEndDate
	 * @return dbUpdateReport
	 * @access public
	 */
	function setEndDate($inEndDate) {
		if ( $this->_EndDate !== $inEndDate ) {
			$this->_EndDate = $inEndDate;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Add a log item to the set
	 *
	 * @param dbUpdateLog $inLog
	 * @return dbUpdateReport
	 */
	function addLog(dbUpdateLog $inLog) {
		return $this->_setValue($inLog);
	}
	
	/**
	 * Removes the log item from the set
	 *
	 * @param dbUpdateLog $inLog
	 * @return dbUpdateReport
	 */
	function removeLog(dbUpdateLog $inLog) {
		return $this->_removeItemWithValue($inLog);
	}
	
	/**
	 * Returns the log from position $inKey in the set
	 *
	 * @param integer $inKey
	 * @return dbUpdateLog
	 */
	function getLog($inKey) {
		return $this->_getItem($inKey);
	}
	
	/**
	 * Returns true if any update failed
	 *
	 * @return boolean
	 */
	function hasError() {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oObject = new dbUpdateLog();
			foreach ( $this as $oObject ) {
				 if ( $oObject->isError() ) {
				 	return true;
				 }
			}
		}
		return false;
	}
	
	/**
	 * Returns the number of items in the set
	 *
	 * @return integer
	 */
	function getCount() {
		return $this->_itemCount();
	}
}