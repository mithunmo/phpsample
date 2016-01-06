<?php
/**
 * reportCentreReport
 *
 * Stored in reportCentreReport.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package reportCentre
 * @subpackage reportCentreReport
 * @category reportCentreReport
 * @version $Rev: 10 $
 */


/**
 * reportCentreReport Class
 *
 * Provides access to records in reports.reports
 *
 * Creating a new record:
 * <code>
 * $oReportCentreReport = new reportCentreReport();
 * $oReportCentreReport->setReportID($inReportID);
 * $oReportCentreReport->setReportScheduleID($inReportScheduleID);
 * $oReportCentreReport->setUserID($inUserID);
 * $oReportCentreReport->setIsHidden($inIsHidden);
 * $oReportCentreReport->setReportStatusID($inReportStatusID);
 * $oReportCentreReport->setCreateDate($inCreateDate);
 * $oReportCentreReport->setRequestDate($inRequestDate);
 * $oReportCentreReport->setUpdateDate($inUpdateDate);
 * $oReportCentreReport->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oReportCentreReport = new reportCentreReport($inReportID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oReportCentreReport = new reportCentreReport();
 * $oReportCentreReport->setReportID($inReportID);
 * $oReportCentreReport->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oReportCentreReport = reportCentreReport::getInstance($inReportID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package reportCentre
 * @subpackage reportCentreReport
 * @category reportCentreReport
 */
class reportCentreReport implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of reportCentreReport
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
	 * Stores $_ReportID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ReportID;

	/**
	 * Stores $_ReportScheduleID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ReportScheduleID;

	/**
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_IsHidden
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_IsHidden;

	/**
	 * Stores $_ReportStatusID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ReportStatusID;

	/**
	 * Stores $_CreateDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_CreateDate;

	/**
	 * Stores $_RequestDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_RequestDate;

	/**
	 * Stores $_UpdateDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_UpdateDate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;
	
	/**
	 * Stores an instance of baseTableParamSet
	 *
	 * @var baseTableParamSet
	 * @access protected
	 */
	protected $_ParamSet;
	
	/**
	 * Stores an instance of reportCentreReportSchedule
	 * 
	 * @var reportCentreReportSchedule
	 * @access protected
	 */
	protected $_ReportSchedule;
	
	
	
	/**
	 * Returns a new instance of reportCentreReport
	 *
	 * @param integer $inReportID
	 * @return reportCentreReport
	 */
	function __construct($inReportID = null) {
		$this->reset();
		if ( $inReportID !== null ) {
			$this->setReportID($inReportID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new reportCentreReport containing non-unique properties
	 *
	 * @param integer $inReportScheduleID
	 * @param integer $inUserID
	 * @param integer $inIsHidden
	 * @param integer $inReportStatusID
	 * @param datetime $inCreateDate
	 * @param datetime $inRequestDate
	 * @param datetime $inUpdateDate
	 * @return reportCentreReport
	 * @static
	 */
	public static function factory($inReportScheduleID = null, $inUserID = null, $inIsHidden = null, $inReportStatusID = null, $inCreateDate = null, $inRequestDate = null, $inUpdateDate = null) {
		$oObject = new reportCentreReport;
		if ( $inReportScheduleID !== null ) {
			$oObject->setReportScheduleID($inReportScheduleID);
		}
		if ( $inUserID !== null ) {
			$oObject->setUserID($inUserID);
		}
		if ( $inIsHidden !== null ) {
			$oObject->setIsHidden($inIsHidden);
		}
		if ( $inReportStatusID !== null ) {
			$oObject->setReportStatusID($inReportStatusID);
		}
		if ( $inCreateDate !== null ) {
			$oObject->setCreateDate($inCreateDate);
		}
		if ( $inRequestDate !== null ) {
			$oObject->setRequestDate($inRequestDate);
		}
		if ( $inUpdateDate !== null ) {
			$oObject->setUpdateDate($inUpdateDate);
		}
		return $oObject;
	}

	/**
	 * Get an instance of reportCentreReport by primary key
	 *
	 * @param integer $inReportID
	 * @return reportCentreReport
	 * @static
	 */
	public static function getInstance($inReportID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inReportID]) ) {
			return self::$_Instances[$inReportID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new reportCentreReport();
		$oObject->setReportID($inReportID);
		if ( $oObject->load() ) {
			self::$_Instances[$inReportID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of reportCentreReport
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inUserID
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inUserID = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('reports').'.reports';
		if ( $inUserID !== null ) {
			$query .= ' WHERE userID = '.dbManager::getInstance()->quote($inUserID);
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new reportCentreReport();
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
	 * Returns an array of objects of reportCentreReport
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inUserID
	 * @return reportCentreReportSet
	 * @static
	 */
	public static function getUserReportInboxItems($inOffset = 0, $inLimit = 30, $inUserID) {
		$query = '
			SELECT SQL_CALC_FOUND_ROWS * FROM '.system::getConfig()->getDatabase('reports').'.reports
			 WHERE userID = :UserID
			   AND isHidden = 0
			   AND requestDate < :RequestDate
			 ORDER BY requestDate DESC, createDate DESC';
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$count = 0;
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $inUserID, PDO::PARAM_INT);
			$oStmt->bindValue(':RequestDate', date(system::getConfig()->getDatabaseDatetimeFormat()));
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new reportCentreReport();
					$oObject->loadFromArray($row);
					$list[] = $oObject;
				}
			}
			$oStmt->closeCursor();
			
			$count = dbManager::getInstance()->query('SELECT FOUND_ROWS() AS repCount')->fetchColumn();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return new reportCentreReportSet($list, $count, $inOffset, $inLimit);
	}
	
	
	
	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT reportID, reportScheduleID, userID, isHidden, reportStatusID, createDate, requestDate, updateDate
			  FROM '.system::getConfig()->getDatabase('reports').'.reports';

		$where = array();
		if ( $this->_ReportID !== 0 ) {
			$where[] = ' reportID = :ReportID ';
		}
		if ( $this->_RequestDate !== '' && $this->_ReportScheduleID !== 0 ) {
			$where[] = ' requestDate = :RequestDate ';
			$where[] = ' reportScheduleID = :ScheduleID ';
		} 

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_ReportID !== 0 ) {
				$oStmt->bindValue(':ReportID', $this->_ReportID);
			}
			if ( $this->_RequestDate !== '' && $this->_ReportScheduleID !== 0 ) {
				$oStmt->bindValue(':RequestDate', $this->_RequestDate);
				$oStmt->bindValue(':ScheduleID', $this->_ReportScheduleID);
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
		$this->setReportID((int)$inArray['reportID']);
		$this->setReportScheduleID((int)$inArray['reportScheduleID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setIsHidden((int)$inArray['isHidden']);
		$this->setReportStatusID((int)$inArray['reportStatusID']);
		$this->setCreateDate($inArray['createDate']);
		$this->setRequestDate($inArray['requestDate']);
		$this->setUpdateDate($inArray['updateDate']);
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
			$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('reports').'.reports
					( reportID, reportScheduleID, userID, isHidden, reportStatusID, createDate, requestDate, updateDate)
				VALUES
					(:ReportID, :ReportScheduleID, :UserID, :IsHidden, :ReportStatusID, :CreateDate, :RequestDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					reportScheduleID=VALUES(reportScheduleID),
					userID=VALUES(userID),
					isHidden=VALUES(isHidden),
					reportStatusID=VALUES(reportStatusID),
					createDate=VALUES(createDate),
					requestDate=VALUES(requestDate),
					updateDate=VALUES(updateDate)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ReportID', $this->_ReportID);
					$oStmt->bindValue(':ReportScheduleID', $this->_ReportScheduleID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':IsHidden', $this->_IsHidden);
					$oStmt->bindValue(':ReportStatusID', $this->_ReportStatusID);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':RequestDate', $this->_RequestDate);
					$oStmt->bindValue(':UpdateDate', $this->_UpdateDate);

					if ( $oStmt->execute() ) {
						if ( !$this->getReportID() ) {
							$this->setReportID($oDB->lastInsertId());
						}
						$this->setModified(false);
						$return = true;
					}
				} catch ( Exception $e ) {
					systemLog::error($e->getMessage());
					throw $e;
				}
			}
			
			if ( $this->_ParamSet instanceof baseTableParamSet ) {
				$this->_ParamSet->setIndexID($this->getReportID());
				$return = $this->_ParamSet->save() && $return;
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
			DELETE FROM '.system::getConfig()->getDatabase('reports').'.reports
			WHERE
				reportID = :ReportID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ReportID', $this->_ReportID);

			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				
				$this->getParamSet()->delete();
				
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
	 * @return reportCentreReport
	 */
	function reset() {
		$this->_ReportID = 0;
		$this->_ReportScheduleID = 0;
		$this->_UserID = 0;
		$this->_IsHidden = 0;
		$this->_ReportStatusID = 0;
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_RequestDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_UpdateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		
		$this->_ParamSet = null;
		$this->_ReportSchedule = null;
		
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
		$string .= " ReportID[$this->_ReportID] $newLine";
		$string .= " ReportScheduleID[$this->_ReportScheduleID] $newLine";
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " IsHidden[$this->_IsHidden] $newLine";
		$string .= " ReportStatusID[$this->_ReportStatusID] $newLine";
		$string .= " CreateDate[$this->_CreateDate] $newLine";
		$string .= " RequestDate[$this->_RequestDate] $newLine";
		$string .= " UpdateDate[$this->_UpdateDate] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'reportCentreReport';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ReportID\" value=\"$this->_ReportID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ReportScheduleID\" value=\"$this->_ReportScheduleID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"IsHidden\" value=\"$this->_IsHidden\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ReportStatusID\" value=\"$this->_ReportStatusID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CreateDate\" value=\"$this->_CreateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"RequestDate\" value=\"$this->_RequestDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"UpdateDate\" value=\"$this->_UpdateDate\" type=\"datetime\" /> $newLine";
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
	 * Returns true if a report exists in the queue matching schedule and date
	 * 
	 * @return boolean
	 */
	function hasReportBeenQueued() {
		$return = false;
		$query = '
			SELECT COUNT(*) AS recCount
			  FROM '.system::getConfig()->getDatabase('reports').'.reportQueue
			       INNER JOIN '.system::getConfig()->getDatabase('reports').'.reports USING (reportID) 
			 WHERE reportQueue.scheduled = :RequestDate
			   AND reports.reportScheduleID = :ScheduleID';
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':RequestDate', $this->getRequestDate());
		$oStmt->bindValue(':ScheduleID', $this->getReportScheduleID());
		if ( $oStmt->execute() ) {
			$cnt = $oStmt->fetchColumn();
			if ( $cnt > 0 ) {
				$return = true;
			}
		}
		$oStmt->closeCursor();
		return $return;
	}
	
	/**
	 * Returns true if object is valid
	 *
	 * @return boolean
	 */
	function isValid(&$message = '') {
		$valid = true;
		if ( $valid ) {
			$valid = $this->checkReportID($message);
		}
		if ( $valid ) {
			$valid = $this->checkReportScheduleID($message);
		}
		if ( $valid ) {
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkIsHidden($message);
		}
		if ( $valid ) {
			$valid = $this->checkReportStatusID($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkRequestDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateDate($message);
		}
		return $valid;
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
	 * Checks that $_ReportScheduleID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkReportScheduleID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ReportScheduleID) && $this->_ReportScheduleID !== 0 ) {
			$inMessage .= "{$this->_ReportScheduleID} is not a valid value for ReportScheduleID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_UserID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkUserID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_UserID) && $this->_UserID !== 0 ) {
			$inMessage .= "{$this->_UserID} is not a valid value for UserID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_IsHidden has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkIsHidden(&$inMessage = '') {
		$isValid = true;
		return $isValid;
	}

	/**
	 * Checks that $_ReportStatusID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkReportStatusID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ReportStatusID) && $this->_ReportStatusID !== 0 ) {
			$inMessage .= "{$this->_ReportStatusID} is not a valid value for ReportStatusID";
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
	 * Checks that $_RequestDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkRequestDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_RequestDate) && $this->_RequestDate !== '' ) {
			$inMessage .= "{$this->_RequestDate} is not a valid value for RequestDate";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_UpdateDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkUpdateDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_UpdateDate) && $this->_UpdateDate !== '' ) {
			$inMessage .= "{$this->_UpdateDate} is not a valid value for UpdateDate";
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
		$modified = $this->_Modified;
		if ( !$modified && $this->_ParamSet !== null ) {
			$modified = $modified || $this->_ParamSet->isModified();
		}
		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return reportCentreReport
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
		return $this->_ReportID;
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
	 * @return reportCentreReport
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
	 * Return value of $_ReportScheduleID
	 *
	 * @return integer
	 * @access public
	 */
	function getReportScheduleID() {
		return $this->_ReportScheduleID;
	}

	/**
	 * Set $_ReportScheduleID to ReportScheduleID
	 *
	 * @param integer $inReportScheduleID
	 * @return reportCentreReport
	 * @access public
	 */
	function setReportScheduleID($inReportScheduleID) {
		if ( $inReportScheduleID !== $this->_ReportScheduleID ) {
			$this->_ReportScheduleID = $inReportScheduleID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_UserID
	 *
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set $_UserID to UserID
	 *
	 * @param integer $inUserID
	 * @return reportCentreReport
	 * @access public
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_IsHidden
	 *
	 * @return integer
	 * @access public
	 */
	function getIsHidden() {
		return $this->_IsHidden;
	}

	/**
	 * Set $_IsHidden to IsHidden
	 *
	 * @param integer $inIsHidden
	 * @return reportCentreReport
	 * @access public
	 */
	function setIsHidden($inIsHidden) {
		if ( $inIsHidden !== $this->_IsHidden ) {
			$this->_IsHidden = $inIsHidden;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_ReportStatusID
	 *
	 * @return integer
	 * @access public
	 */
	function getReportStatusID() {
		return $this->_ReportStatusID;
	}
	
	/**
	 * Returns the report status object
	 * 
	 * @return reportCentreReportStatus
	 */
	function getReportStatus() {
		return reportCentreReportStatus::getInstance($this->getReportStatusID());
	}

	/**
	 * Set $_ReportStatusID to ReportStatusID
	 *
	 * @param integer $inReportStatusID
	 * @return reportCentreReport
	 * @access public
	 */
	function setReportStatusID($inReportStatusID) {
		if ( $inReportStatusID !== $this->_ReportStatusID ) {
			$this->_ReportStatusID = $inReportStatusID;
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
	 * @return reportCentreReport
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
	 * Return value of $_RequestDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getRequestDate() {
		return $this->_RequestDate;
	}

	/**
	 * Set $_RequestDate to RequestDate
	 *
	 * @param datetime $inRequestDate
	 * @return reportCentreReport
	 * @access public
	 */
	function setRequestDate($inRequestDate) {
		if ( $inRequestDate !== $this->_RequestDate ) {
			$this->_RequestDate = $inRequestDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_UpdateDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getUpdateDate() {
		return $this->_UpdateDate;
	}

	/**
	 * Set $_UpdateDate to UpdateDate
	 *
	 * @param datetime $inUpdateDate
	 * @return reportCentreReport
	 * @access public
	 */
	function setUpdateDate($inUpdateDate) {
		if ( $inUpdateDate !== $this->_UpdateDate ) {
			$this->_UpdateDate = $inUpdateDate;
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
	 * @return reportCentreReport
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	

	/**
	 * Returns an instance of baseTableParamSet, which is lazy loaded upon request
	 *
	 * @return baseTableParamSet
	 */
	function getParamSet() {
		if ( !$this->_ParamSet instanceof baseTableParamSet ) {
			$this->_ParamSet = new baseTableParamSet(
				system::getConfig()->getDatabase('reports'), 'reportParams', 'reportID', 'paramName', 'paramValue', $this->getReportID()
			);
			if ( $this->getReportID() > 0 ) {
				$this->_ParamSet->load();
			}
		}
		return $this->_ParamSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param baseTableParamSet $inObject
	 * @return reportCentreReport
	 */
	function setParamSet(baseTableParamSet $inObject) {
		$this->_ParamSet = $inObject;
		return $this;
	}

	/**
	 * Returns the report schedule, loading it if not set
	 * 
	 * @return reportCentreReportSchedule
	 */
	function getReportSchedule() {
		if ( !$this->_ReportSchedule instanceof reportCentreReportSchedule ) {
			$this->_ReportSchedule = new reportCentreReportSchedule($this->getReportScheduleID());
		}
		return $this->_ReportSchedule;
	}
	
	/**
	 * Set the report schedule
	 * 
	 * @param reportCentreReportSchedule $inSchedule
	 * @return reportCentreReport
	 */
	function setReportSchedule(reportCentreReportSchedule $inSchedule) {
		$this->_ReportSchedule = $inSchedule;
		$this->setReportScheduleID($inSchedule->getReportScheduleID());
		return $this;
	}
}