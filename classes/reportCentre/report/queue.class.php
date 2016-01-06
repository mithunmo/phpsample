<?php
/**
 * reportCentreReportQueue
 *
 * Stored in reportCentreReportQueue.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package reportCentre
 * @subpackage reportCentreReportQueue
 * @category reportCentreReportQueue
 * @version $Rev: 10 $
 */


/**
 * reportCentreReportQueue Class
 *
 * Provides access to records in reports.reportQueue
 *
 * Creating a new record:
 * <code>
 * $oReportCentreReportQueue = new reportCentreReportQueue();
 * $oReportCentreReportQueue->setScheduled($inScheduled);
 * $oReportCentreReportQueue->setReportID($inReportID);
 * $oReportCentreReportQueue->setCreateDate($inCreateDate);
 * $oReportCentreReportQueue->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oReportCentreReportQueue = new reportCentreReportQueue();
 * </code>
 *
 *
 * Accessing a record by instance:
 * <code>
 * $oReportCentreReportQueue = reportCentreReportQueue::getInstance();
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package reportCentre
 * @subpackage reportCentreReportQueue
 * @category reportCentreReportQueue
 */
class reportCentreReportQueue implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of reportCentreReportQueue
	 *
	 * @var array
	 * @access protected
	 * @static
	 */
	protected static $_Instances = array();

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;

	/**
	 * Stores $_Scheduled
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_Scheduled;

	/**
	 * Stores $_ReportID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ReportID;

	/**
	 * Stores $_CreateDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_CreateDate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;
	
	/**
	 * Stores $_Report
	 *
	 * @var reportCentreReport
	 * @access protected
	 */
	protected $_Report;
	
	
	
	/**
	 * Returns a new instance of reportCentreReportQueue
	 *
	 * @return reportCentreReportQueue
	 */
	function __construct() {
		$this->reset();
		return $this;
	}

	/**
	 * Creates a new reportCentreReportQueue containing non-unique properties
	 *
	 * @param datetime $inScheduled
	 * @param integer $inReportID
	 * @param datetime $inCreateDate
	 * @return reportCentreReportQueue
	 * @static
	 */
	public static function factory($inScheduled = null, $inReportID = null, $inCreateDate = null) {
		$oObject = new reportCentreReportQueue;
		if ( $inScheduled !== null ) {
			$oObject->setScheduled($inScheduled);
		}
		if ( $inReportID !== null ) {
			$oObject->setReportID($inReportID);
		}
		if ( $inCreateDate !== null ) {
			$oObject->setCreateDate($inCreateDate);
		}
		return $oObject;
	}

	/**
	 * Get an instance of reportCentreReportQueue by primary key
	 *
	 * @return reportCentreReportQueue
	 * @static
	 */
	public static function getInstance() {
		/**
		 * Check for an existing instance
		 */
		$oObject = new reportCentreReportQueue();
		return $oObject;
	}
	
	/**
	 * Returns the next queued report from the queue
	 * 
	 * @return reportCentreReportQueue
	 * @static
	 */
	public static function getNextQueuedReport() {
		$query = '
			SELECT *
			  FROM '.system::getConfig()->getDatabase('reports').'.reportQueue
			 WHERE scheduled <= :tsNow
			 ORDER BY scheduled ASC
			 LIMIT 1';
		
		$oObject = null;
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(":tsNow", date(system::getConfig()->getDatabaseDatetimeFormat()));
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new reportCentreReportQueue();
					$oObject->loadFromArray($row);
				}
			}
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of reportCentreReportQueue
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('reports').'.reportQueue';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new reportCentreReportQueue();
					$oObject->loadFromArray($row);
					$list[] = $oObject;
				}
			}
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $list;
	}

	/**
	 * Adds the report schedule to the report queue for processing
	 * 
	 * @param reportCentreReportSchedule $inSchedule
	 * @return void
	 * @static
	 */
	public static function addSchedule(reportCentreReportSchedule $inSchedule) {
		$oReport = $inSchedule->getNextUserReport();
		$oReport->getReportSchedule()->setLastReportDate($oReport->getRequestDate())->save();
		$oReport->save();
		
		self::addReport($oReport);
	}
	
	/**
	 * Adds the user report to the reporting queue
	 * 
	 * @param reportCentreReport $inReport
	 * @return boolean
	 * @static
	 */
	public static function addReport(reportCentreReport $inReport) {
		$oQueue = new reportCentreReportQueue();
		$oQueue->setReportID($inReport->getReportID());
		$oQueue->setScheduled($inReport->getRequestDate());
		return $oQueue->save();
	}

	/**
	 * Removes all reports from the queue with $inScheduleID
	 *
	 * @param integer $inScheduleID
	 * @return void
	 * @static
	 */
	public static function removeSchedule($inScheduleID) {
		$query = '
			SELECT reportQueue.*
			  FROM '.system::getConfig()->getDatabase('reports').'.reportQueue
			       INNER JOIN '.system::getConfig()->getDatabase('reports').'.reports USING (reportID)
			 WHERE reports.reportScheduleID = :ScheduleID';
		
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ScheduleID', $inScheduleID);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new reportCentreReportQueue();
					$oObject->loadFromArray($row);
					$oObject->getReport()->setIsHidden(true);
					$oObject->getReport()->setReportStatusID(reportCentreReportStatus::S_REMOVED_SCHEDULE);
					$oObject->getReport()->save();
					$oObject->delete();
				}
			}
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
	}



	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT scheduled, reportID, createDate
			  FROM '.system::getConfig()->getDatabase('reports').'.reportQueue';

		$where = array();
		if ( $this->_Scheduled !== '' ) {
			$where[] = ' scheduled = :Scheduled ';
		}
		if ( $this->_ReportID !== 0 ) {
			$where[] = ' reportID = :ReportID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_Scheduled !== '' ) {
				$oStmt->bindValue(':Scheduled', $this->_Scheduled);
			}
			if ( $this->_ReportID !== 0 ) {
				$oStmt->bindValue(':ReportID', $this->_ReportID);
			}

			$this->reset();
			if ( $oStmt->execute() ) {
				$row = $oStmt->fetch();
				if ( $row !== false && is_array($row) ) {
					$this->loadFromArray($row);
					$oStmt->closeCursor();
					$return = true;
				}
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $return;
	}

	/**
	 * Loads a record by array
	 *
	 * @param array $inArray
	 */
	function loadFromArray($inArray) {
		$this->setScheduled($inArray['scheduled']);
		$this->setReportID((int)$inArray['reportID']);
		$this->setCreateDate($inArray['createDate']);
		$this->setModified(false);
	}

	/**
	 * Saves object to the table
	 *
	 * @return boolean
	 */
	function save() {
		$return = false;
		if ( $this->isModified() ) {
			$message = '';
			if ( !$this->isValid($message) ) {
				throw new reportCentreException($message);
			}
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('reports').'.reportQueue
					( scheduled, reportID, createDate)
				VALUES
					(:Scheduled, :ReportID, :CreateDate)
				ON DUPLICATE KEY UPDATE
					scheduled=VALUES(scheduled),
					reportID=VALUES(reportID),
					createDate=VALUES(createDate)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':Scheduled', $this->_Scheduled);
					$oStmt->bindValue(':ReportID', $this->_ReportID);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);

					if ( $oStmt->execute() ) {
						$this->setModified(false);
						$return = true;
					}
				} catch ( Exception $e ) {
					systemLog::error($e->getMessage());
					throw $e;
				}
			}
		}
		return $return;
	}

	/**
	 * Deletes the object from the table
	 *
	 * @return boolean
	 */
	function delete() {
		$query = '
			DELETE FROM '.system::getConfig()->getDatabase('reports').'.reportQueue
			WHERE
				scheduled = :Scheduled
				AND reportID = :ReportID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':Scheduled', $this->_Scheduled);
			$oStmt->bindValue(':ReportID', $this->_ReportID);
			
			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				$this->reset();
				return true;
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return reportCentreReportQueue
	 */
	function reset() {
		$this->_Scheduled = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_ReportID = 0;
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Report = null;
		$this->setModified(false);
		$this->setMarkForDeletion(false);
		return $this;
	}

	/**
	 * Returns object as a string with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toString($newLine = "\n") {
		$string  = '';
		$string .= " Scheduled[$this->_Scheduled] $newLine";
		$string .= " ReportID[$this->_ReportID] $newLine";
		$string .= " CreateDate[$this->_CreateDate] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'reportCentreReportQueue';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"Scheduled\" value=\"$this->_Scheduled\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"ReportID\" value=\"$this->_ReportID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CreateDate\" value=\"$this->_CreateDate\" type=\"datetime\" /> $newLine";
		$xml .= "</$className>$newLine";
		return $xml;
	}

	/**
	 * Returns properties of object as an array
	 *
	 * @return array
	 */
	function toArray() {
		return get_object_vars($this);
	}



	/**
	 * Returns true if object is valid
	 *
	 * @return boolean
	 */
	function isValid(&$message = '') {
		$valid = true;
		if ( $valid ) {
			$valid = $this->checkScheduled($message);
		}
		if ( $valid ) {
			$valid = $this->checkReportID($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_Scheduled has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkScheduled(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Scheduled) && $this->_Scheduled !== '' ) {
			$inMessage .= "{$this->_Scheduled} is not a valid value for Scheduled";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_ReportID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkReportID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ReportID) && $this->_ReportID !== 0 ) {
			$inMessage .= "{$this->_ReportID} is not a valid value for ReportID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_CreateDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCreateDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_CreateDate) && $this->_CreateDate !== '' ) {
			$inMessage .= "{$this->_CreateDate} is not a valid value for CreateDate";
			$isValid = false;
		}
		return $isValid;
	}



	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return reportCentreReportQueue
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the primaryKey index
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->getScheduled().':'.$this->getReportID();
	}

	/**
	 * Return value of $_Scheduled
	 *
	 * @return datetime
	 * @access public
	 */
	function getScheduled() {
		return $this->_Scheduled;
	}

	/**
	 * Set $_Scheduled to Scheduled
	 *
	 * @param datetime $inScheduled
	 * @return reportCentreReportQueue
	 * @access public
	 */
	function setScheduled($inScheduled) {
		if ( $inScheduled !== $this->_Scheduled ) {
			$this->_Scheduled = $inScheduled;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_ReportID
	 *
	 * @return integer
	 * @access public
	 */
	function getReportID() {
		return $this->_ReportID;
	}

	/**
	 * Set $_ReportID to ReportID
	 *
	 * @param integer $inReportID
	 * @return reportCentreReportQueue
	 * @access public
	 */
	function setReportID($inReportID) {
		if ( $inReportID !== $this->_ReportID ) {
			$this->_ReportID = $inReportID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_CreateDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getCreateDate() {
		return $this->_CreateDate;
	}

	/**
	 * Set $_CreateDate to CreateDate
	 *
	 * @param datetime $inCreateDate
	 * @return reportCentreReportQueue
	 * @access public
	 */
	function setCreateDate($inCreateDate) {
		if ( $inCreateDate !== $this->_CreateDate ) {
			$this->_CreateDate = $inCreateDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_MarkForDeletion
	 *
	 * @return boolean
	 */
	function getMarkForDeletion() {
		return $this->_MarkForDeletion;
	}

	/**
	 * Set $_MarkForDeletion to $inMarkForDeletion
	 *
	 * @param boolean $inMarkForDeletion
	 * @return reportCentreReportQueue
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	/**
	 * Returns the report instance
	 *
	 * @return reportCentreReport
	 */
	function getReport() {
		if ( !$this->_Report instanceof reportCentreReport ) {
			$this->_Report = reportCentreReport::getInstance($this->getReportID());
		}
		return $this->_Report;
	}
	
	/**
	 * Set $_Report to $inReport
	 *
	 * @param reportCentreReport $inReport
	 * @return reportCentreReportQueue
	 */
	function setReport($inReport) {
		if ( $inReport !== $this->_Report ) {
			$this->_Report = $inReport;
			$this->setModified();
		}
		return $this;
	}
}