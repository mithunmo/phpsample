<?php
/**
 * reportCentreReportSchedule
 *
 * Stored in reportCentreReportSchedule.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package reportCentre
 * @subpackage reportCentreReportSchedule
 * @category reportCentreReportSchedule
 * @version $Rev: 10 $
 */


/**
 * reportCentreReportSchedule Class
 *
 * Provides access to records in reports.reportSchedule
 *
 * Creating a new record:
 * <code>
 * $oReportSchedule = new reportCentreReportSchedule();
 * $oReportSchedule->setReportScheduleID($inReportScheduleID);
 * $oReportSchedule->setUserID($inUserID);
 * $oReportSchedule->setReportTypeID($inReportTypeID);
 * $oReportSchedule->setReportTitle($inReportTitle);
 * $oReportSchedule->setReportScheduleTypeID($inReportScheduleTypeID);
 * $oReportSchedule->setReportScheduleStatus($inReportScheduleStatus);
 * $oReportSchedule->setDeliveryTypeID($inDeliveryTypeID);
 * $oReportSchedule->setScheduledDate($inScheduledDate);
 * $oReportSchedule->setLastReportDate($inLastReportDate);
 * $oReportSchedule->setCreateDate($inCreateDate);
 * $oReportSchedule->setUpdateDate($inUpdateDate);
 * $oReportSchedule->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oReportSchedule = new reportCentreReportSchedule($inReportScheduleID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oReportSchedule = new reportCentreReportSchedule();
 * $oReportSchedule->setReportScheduleID($inReportScheduleID);
 * $oReportSchedule->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oReportSchedule = reportCentreReportSchedule::getInstance($inReportScheduleID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package reportCentre
 * @subpackage reportCentreReportSchedule
 * @category reportCentreReportSchedule
 */
class reportCentreReportSchedule implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of reportCentreReportSchedule
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
	 * Stores $_ReportTypeID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ReportTypeID;

	/**
	 * Stores $_ReportTitle
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ReportTitle;

	/**
	 * Stores $_ReportScheduleTypeID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ReportScheduleTypeID;

	/**
	 * Stores $_ReportScheduleStatus
	 *
	 * @var string (REPORTSCHEDULESTATUS_ACTIVE,REPORTSCHEDULESTATUS_INACTIVE,REPORTSCHEDULESTATUS_REMOVED,REPORTSCHEDULESTATUS_COMPLETE,)
	 * @access protected
	 */
	protected $_ReportScheduleStatus;
	const REPORTSCHEDULESTATUS_ACTIVE = 'Active';
	const REPORTSCHEDULESTATUS_INACTIVE = 'Inactive';
	const REPORTSCHEDULESTATUS_REMOVED = 'Removed';
	const REPORTSCHEDULESTATUS_COMPLETE = 'Complete';

	/**
	 * Stores $_DeliveryTypeID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_DeliveryTypeID;

	/**
	 * Stores $_ScheduledDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_ScheduledDate;

	/**
	 * Stores $_LastReportDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_LastReportDate;

	/**
	 * Stores $_CreateDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_CreateDate;

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
	 * Stores $_ReportType
	 *
	 * @var reportCentreReportType
	 * @access protected
	 */
	protected $_ReportType;
	
	/**
	 * Stores an instance of reportBase
	 * 
	 * @var reportBase
	 * @access protected
	 */
	protected $_ReportInstance;
	
	

	/**
	 * Returns a new instance of reportCentreReportSchedule
	 *
	 * @param integer $inReportScheduleID
	 * @return reportCentreReportSchedule
	 */
	function __construct($inReportScheduleID = null) {
		$this->reset();
		if ( $inReportScheduleID !== null ) {
			$this->setReportScheduleID($inReportScheduleID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new reportCentreReportSchedule containing non-unique properties
	 *
	 * @param integer $inUserID
	 * @param integer $inReportTypeID
	 * @param string $inReportTitle
	 * @param integer $inReportScheduleTypeID
	 * @param string $inReportScheduleStatus
	 * @param integer $inDeliveryTypeID
	 * @param datetime $inScheduledDate
	 * @param datetime $inLastReportDate
	 * @param datetime $inCreateDate
	 * @param datetime $inUpdateDate
	 * @return reportCentreReportSchedule
	 * @static
	 */
	public static function factory($inUserID = null, $inReportTypeID = null, $inReportTitle = null, $inReportScheduleTypeID = null, $inReportScheduleStatus = null, $inDeliveryTypeID = null, $inScheduledDate = null, $inLastReportDate = null, $inCreateDate = null, $inUpdateDate = null) {
		$oObject = new reportCentreReportSchedule;
		if ( $inUserID !== null ) {
			$oObject->setUserID($inUserID);
		}
		if ( $inReportTypeID !== null ) {
			$oObject->setReportTypeID($inReportTypeID);
		}
		if ( $inReportTitle !== null ) {
			$oObject->setReportTitle($inReportTitle);
		}
		if ( $inReportScheduleTypeID !== null ) {
			$oObject->setReportScheduleTypeID($inReportScheduleTypeID);
		}
		if ( $inReportScheduleStatus !== null ) {
			$oObject->setReportScheduleStatus($inReportScheduleStatus);
		}
		if ( $inDeliveryTypeID !== null ) {
			$oObject->setDeliveryTypeID($inDeliveryTypeID);
		}
		if ( $inScheduledDate !== null ) {
			$oObject->setScheduledDate($inScheduledDate);
		}
		if ( $inLastReportDate !== null ) {
			$oObject->setLastReportDate($inLastReportDate);
		}
		if ( $inCreateDate !== null ) {
			$oObject->setCreateDate($inCreateDate);
		}
		if ( $inUpdateDate !== null ) {
			$oObject->setUpdateDate($inUpdateDate);
		}
		return $oObject;
	}

	/**
	 * Get an instance of reportCentreReportSchedule by primary key
	 *
	 * @param integer $inReportScheduleID
	 * @return reportCentreReportSchedule
	 * @static
	 */
	public static function getInstance($inReportScheduleID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inReportScheduleID]) ) {
			return self::$_Instances[$inReportScheduleID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new reportCentreReportSchedule();
		$oObject->setReportScheduleID($inReportScheduleID);
		if ( $oObject->load() ) {
			self::$_Instances[$inReportScheduleID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of reportCentreReportSchedule
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('reports').'.reportSchedule';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new reportCentreReportSchedule();
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
	 * Returns an array of objects of reportCentreReportSchedule
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inUserID
	 * @return reportCentreReportSet
	 * @static
	 */
	public static function getUserScheduleInboxItems($inOffset = 0, $inLimit = 30, $inUserID) {
		$query = '
			SELECT SQL_CALC_FOUND_ROWS * FROM '.system::getConfig()->getDatabase('reports').'.reportSchedule
			 WHERE userID = :UserID
			   AND reportScheduleStatus != "Removed" AND reportScheduleStatus != "Complete"
			 ORDER BY createDate DESC';
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$count = 0;
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $inUserID, PDO::PARAM_INT);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new reportCentreReportSchedule();
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
			SELECT reportScheduleID, userID, reportTypeID, reportTitle, reportScheduleTypeID, reportScheduleStatus, deliveryTypeID, scheduledDate, lastReportDate, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('reports').'.reportSchedule';

		$where = array();
		if ( $this->_ReportScheduleID !== 0 ) {
			$where[] = ' reportScheduleID = :ReportScheduleID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_ReportScheduleID !== 0 ) {
				$oStmt->bindValue(':ReportScheduleID', $this->_ReportScheduleID);
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
		$this->setReportScheduleID((int)$inArray['reportScheduleID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setReportTypeID((int)$inArray['reportTypeID']);
		$this->setReportTitle($inArray['reportTitle']);
		$this->setReportScheduleTypeID((int)$inArray['reportScheduleTypeID']);
		$this->setReportScheduleStatus($inArray['reportScheduleStatus']);
		$this->setDeliveryTypeID((int)$inArray['deliveryTypeID']);
		$this->setScheduledDate($inArray['scheduledDate']);
		$this->setLastReportDate($inArray['lastReportDate']);
		$this->setCreateDate($inArray['createDate']);
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
				INSERT INTO '.system::getConfig()->getDatabase('reports').'.reportSchedule
					( reportScheduleID, userID, reportTypeID, reportTitle, reportScheduleTypeID,reportScheduleStatus, deliveryTypeID, scheduledDate, lastReportDate, createDate, updateDate)
				VALUES
					(:ReportScheduleID, :UserID, :ReportTypeID, :ReportTitle, :ReportScheduleTypeID, :ReportScheduleStatus, :DeliveryTypeID, :ScheduledDate, :LastReportDate, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					reportTypeID=VALUES(reportTypeID),
					reportTitle=VALUES(reportTitle),
					reportScheduleTypeID=VALUES(reportScheduleTypeID),
					reportScheduleStatus=VALUES(reportScheduleStatus),
					deliveryTypeID=VALUES(deliveryTypeID),
					scheduledDate=VALUES(scheduledDate),
					lastReportDate=VALUES(lastReportDate),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ReportScheduleID', $this->_ReportScheduleID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':ReportTypeID', $this->_ReportTypeID);
					$oStmt->bindValue(':ReportTitle', $this->_ReportTitle);
					$oStmt->bindValue(':ReportScheduleTypeID', $this->_ReportScheduleTypeID);
					$oStmt->bindValue(':ReportScheduleStatus', $this->_ReportScheduleStatus);
					$oStmt->bindValue(':DeliveryTypeID', $this->_DeliveryTypeID);
					$oStmt->bindValue(':ScheduledDate', $this->_ScheduledDate);
					$oStmt->bindValue(':LastReportDate', $this->_LastReportDate);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':UpdateDate', $this->_UpdateDate);

					if ( $oStmt->execute() ) {
						if ( !$this->getReportScheduleID() ) {
							$this->setReportScheduleID($oDB->lastInsertId());
						}
						$this->setModified(false);
						$return = true;
						
						if ( $this->getReportScheduleStatus() == self::REPORTSCHEDULESTATUS_REMOVED ) {
							reportCentreReportQueue::removeSchedule($this->getReportScheduleID());
						}
					}
				} catch ( Exception $e ) {
					systemLog::error($e->getMessage());
					throw $e;
				}
			}
			
			if ( $this->_ParamSet instanceof baseTableParamSet ) {
				$this->_ParamSet->setIndexID($this->getReportScheduleID());
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
			DELETE FROM '.system::getConfig()->getDatabase('reports').'.reportSchedule
			WHERE
				reportScheduleID = :ReportScheduleID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ReportScheduleID', $this->_ReportScheduleID);

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
	 * @return reportCentreReportSchedule
	 */
	function reset() {
		$this->_ReportScheduleID = 0;
		$this->_UserID = 0;
		$this->_ReportTypeID = 0;
		$this->_ReportTitle = '';
		$this->_ReportScheduleTypeID = 0;
		$this->_ReportScheduleStatus = 'Active';
		$this->_DeliveryTypeID = 0;
		$this->_ScheduledDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_LastReportDate = '';
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_UpdateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		
		$this->_ParamSet = null;
		$this->_ReportType = null;
		$this->_ReportInstance = null;
		
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
		$string .= " ReportScheduleID[$this->_ReportScheduleID] $newLine";
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " ReportTypeID[$this->_ReportTypeID] $newLine";
		$string .= " ReportTitle[$this->_ReportTitle] $newLine";
		$string .= " ReportScheduleTypeID[$this->_ReportScheduleTypeID] $newLine";
		$string .= " ReportScheduleStatus[$this->_ReportScheduleStatus] $newLine";
		$string .= " DeliveryTypeID[$this->_DeliveryTypeID] $newLine";
		$string .= " ScheduledDate[$this->_ScheduledDate] $newLine";
		$string .= " LastReportDate[$this->_LastReportDate] $newLine";
		$string .= " CreateDate[$this->_CreateDate] $newLine";
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
		$className = 'reportCentreReportSchedule';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ReportScheduleID\" value=\"$this->_ReportScheduleID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ReportTypeID\" value=\"$this->_ReportTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ReportTitle\" value=\"$this->_ReportTitle\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"ReportScheduleTypeID\" value=\"$this->_ReportScheduleTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ReportScheduleStatus\" value=\"$this->_ReportScheduleStatus\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"DeliveryTypeID\" value=\"$this->_DeliveryTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ScheduledDate\" value=\"$this->_ScheduledDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"LastReportDate\" value=\"$this->_LastReportDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"CreateDate\" value=\"$this->_CreateDate\" type=\"datetime\" /> $newLine";
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
	 * Returns true if object is valid
	 *
	 * @return boolean
	 */
	function isValid(&$message = '') {
		$valid = true;
		if ( $valid ) {
			$valid = $this->checkReportScheduleID($message);
		}
		if ( $valid ) {
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkReportTypeID($message);
		}
		if ( $valid ) {
			$valid = $this->checkReportTitle($message);
		}
		if ( $valid ) {
			$valid = $this->checkReportScheduleTypeID($message);
		}
		if ( $valid ) {
			$valid = $this->checkReportScheduleStatus($message);
		}
		if ( $valid ) {
			$valid = $this->checkDeliveryTypeID($message);
		}
		if ( $valid ) {
			$valid = $this->checkScheduledDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkLastReportDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateDate($message);
		}
		return $valid;
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
	 * Checks that $_ReportTypeID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkReportTypeID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ReportTypeID) && $this->_ReportTypeID !== 0 ) {
			$inMessage .= "{$this->_ReportTypeID} is not a valid value for ReportTypeID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_ReportTitle has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkReportTitle(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ReportTitle) && $this->_ReportTitle !== '' ) {
			$inMessage .= "{$this->_ReportTitle} is not a valid value for ReportTitle";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_ReportTitle) > 255 ) {
			$inMessage .= "ReportTitle cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_ReportTitle) <= 1 ) {
			$inMessage .= "ReportTitle must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_ReportScheduleTypeID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkReportScheduleTypeID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ReportScheduleTypeID) && $this->_ReportScheduleTypeID !== 0 ) {
			$inMessage .= "{$this->_ReportScheduleTypeID} is not a valid value for ReportScheduleTypeID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_ReportScheduleStatus has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkReportScheduleStatus(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ReportScheduleStatus) && $this->_ReportScheduleStatus !== '' ) {
			$inMessage .= "{$this->_ReportScheduleStatus} is not a valid value for ReportScheduleStatus";
			$isValid = false;
		}
		if ( $isValid && $this->_ReportScheduleStatus != '' && !in_array($this->_ReportScheduleStatus, array(self::REPORTSCHEDULESTATUS_ACTIVE, self::REPORTSCHEDULESTATUS_INACTIVE, self::REPORTSCHEDULESTATUS_REMOVED, self::REPORTSCHEDULESTATUS_COMPLETE)) ) {
			$inMessage .= "ReportScheduleStatus must be one of REPORTSCHEDULESTATUS_ACTIVE, REPORTSCHEDULESTATUS_INACTIVE, REPORTSCHEDULESTATUS_REMOVED, REPORTSCHEDULESTATUS_COMPLETE";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_DeliveryTypeID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkDeliveryTypeID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_DeliveryTypeID) && $this->_DeliveryTypeID !== 0 ) {
			$inMessage .= "{$this->_DeliveryTypeID} is not a valid value for DeliveryTypeID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_ScheduledDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkScheduledDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ScheduledDate) && $this->_ScheduledDate !== '' ) {
			$inMessage .= "{$this->_ScheduledDate} is not a valid value for ScheduledDate";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_LastReportDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkLastReportDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_LastReportDate) && $this->_LastReportDate !== '' ) {
			$inMessage .= "{$this->_LastReportDate} is not a valid value for LastReportDate";
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
	 * @return reportCentreReportSchedule
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
		return $this->_ReportScheduleID;
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
	 * @return reportCentreReportSchedule
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
	 * @return reportCentreReportSchedule
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
	 * Return value of $_ReportTypeID
	 *
	 * @return integer
	 * @access public
	 */
	function getReportTypeID() {
		return $this->_ReportTypeID;
	}

	/**
	 * Set $_ReportTypeID to ReportTypeID
	 *
	 * @param integer $inReportTypeID
	 * @return reportCentreReportSchedule
	 * @access public
	 */
	function setReportTypeID($inReportTypeID) {
		if ( $inReportTypeID !== $this->_ReportTypeID ) {
			$this->_ReportTypeID = $inReportTypeID;
			$this->_ReportType = null;
			$this->_ReportInstance = null;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_ReportTitle
	 *
	 * @return string
	 * @access public
	 */
	function getReportTitle() {
		return $this->_ReportTitle;
	}

	/**
	 * Set $_ReportTitle to ReportTitle
	 *
	 * @param string $inReportTitle
	 * @return reportCentreReportSchedule
	 * @access public
	 */
	function setReportTitle($inReportTitle) {
		if ( $inReportTitle !== $this->_ReportTitle ) {
			$this->_ReportTitle = $inReportTitle;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_ReportScheduleTypeID
	 *
	 * @return integer
	 * @access public
	 */
	function getReportScheduleTypeID() {
		return $this->_ReportScheduleTypeID;
	}
	
	/**
	 * Returns the report schedule type object
	 * 
	 * @return reportCentreReportScheduleType
	 */
	function getReportScheduleType() {
		return reportCentreReportScheduleType::getInstance($this->getReportScheduleTypeID());
	}

	/**
	 * Set $_ReportScheduleTypeID to ReportScheduleTypeID
	 *
	 * @param integer $inReportScheduleTypeID
	 * @return reportCentreReportSchedule
	 * @access public
	 */
	function setReportScheduleTypeID($inReportScheduleTypeID) {
		if ( $inReportScheduleTypeID !== $this->_ReportScheduleTypeID ) {
			$this->_ReportScheduleTypeID = $inReportScheduleTypeID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_ReportScheduleStatus
	 *
	 * @return string
	 * @access public
	 */
	function getReportScheduleStatus() {
		return $this->_ReportScheduleStatus;
	}

	/**
	 * Set $_ReportScheduleStatus to ReportScheduleStatus
	 *
	 * @param string $inReportScheduleStatus
	 * @return reportCentreReportSchedule
	 * @access public
	 */
	function setReportScheduleStatus($inReportScheduleStatus) {
		if ( $inReportScheduleStatus !== $this->_ReportScheduleStatus ) {
			$this->_ReportScheduleStatus = $inReportScheduleStatus;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_DeliveryTypeID
	 *
	 * @return integer
	 * @access public
	 */
	function getDeliveryTypeID() {
		return $this->_DeliveryTypeID;
	}
	
	/**
	 * Returns the delivery type object
	 * 
	 * @return reportCentreReportDeliveryType
	 */
	function getDeliveryType() {
		return reportCentreReportDeliveryType::getInstance($this->getDeliveryTypeID());
	}

	/**
	 * Set $_DeliveryTypeID to DeliveryTypeID
	 *
	 * @param integer $inDeliveryTypeID
	 * @return reportCentreReportSchedule
	 * @access public
	 */
	function setDeliveryTypeID($inDeliveryTypeID) {
		if ( $inDeliveryTypeID !== $this->_DeliveryTypeID ) {
			$this->_DeliveryTypeID = $inDeliveryTypeID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_ScheduledDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getScheduledDate() {
		return $this->_ScheduledDate;
	}

	/**
	 * Set $_ScheduledDate to ScheduledDate
	 *
	 * @param datetime $inScheduledDate
	 * @return reportCentreReportSchedule
	 * @access public
	 */
	function setScheduledDate($inScheduledDate) {
		if ( $inScheduledDate !== $this->_ScheduledDate ) {
			$this->_ScheduledDate = $inScheduledDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_LastReportDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getLastReportDate() {
		return $this->_LastReportDate;
	}

	/**
	 * Set $_LastReportDate to LastReportDate
	 *
	 * @param datetime $inLastReportDate
	 * @return reportCentreReportSchedule
	 * @access public
	 */
	function setLastReportDate($inLastReportDate) {
		if ( $inLastReportDate !== $this->_LastReportDate ) {
			$this->_LastReportDate = $inLastReportDate;
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
	 * @return reportCentreReportSchedule
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
	 * @return reportCentreReportSchedule
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
	 * @return reportCentreReportSchedule
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
				system::getConfig()->getDatabase('reports'), 'reportScheduleParams', 'reportScheduleID', 'paramName', 'paramValue', $this->getReportScheduleID()
			);
			if ( $this->getReportScheduleID() > 0 ) {
				$this->_ParamSet->load();
			}
		}
		return $this->_ParamSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param baseTableParamSet $inObject
	 * @return reportCentreReportSchedule
	 */
	function setParamSet(baseTableParamSet $inObject) {
		$this->_ParamSet = $inObject;
		return $this;
	}

	/**
	 * Returns $_ReportType
	 *
	 * @return reportCentreReportType
	 */
	function getReportType() {
		if ( !$this->_ReportType instanceof reportCentreReportType ) {
			$this->_ReportType = reportCentreReportType::getInstance($this->getReportTypeID());
		}
		return $this->_ReportType;
	}
	
	/**
	 * Set $_ReportType to $inReportType
	 *
	 * @param reportCentreReportType $inReportType
	 * @return reportCentreReportSchedule
	 */
	function setReportType(reportCentreReportType $inReportType) {
		$this->setReportTypeID($inReportType->getReportTypeID());
		$this->_ReportType = $inReportType;
		$this->_ReportInstance = null;
		return $this;
	}
	
	/**
	 * Fetches the runnable report using the schedule params
	 * 
	 * @return reportBase
	 */
	function getReportInstance() {
		if ( !$this->_ReportInstance instanceof reportBase ) {
			$class = $this->getReportType()->getClassName();
			
			$this->_ReportInstance = new $class($this->getParamSet()->getParam(), new mofilmReportStyle());
		}
		return $this->_ReportInstance;
	}
	
	/**
	 * Returns the next run date for the report schedule
	 * 
	 * @return datetime
	 */
	function getNextQueueDate() {
		$date = $this->getLastReportDate();
		if ( !$date ) {
			return $this->getScheduledDate();
		}
		return $this->getReportScheduleType()->getNextQueueDate($date);
	}
	
	/**
	 * Returns the next due user report record
	 * 
	 * @return reportCentreReport
	 */
	function getNextUserReport() {
		$oReport = new reportCentreReport();
		$oReport->setUserID($this->getUserID());
		$oReport->setReportSchedule($this);
		$oReport->setRequestDate($this->getNextQueueDate());
		$oReport->load();
		if ( $oReport->getReportID() == 0 ) {
			$oReport->setUserID($this->getUserID());
			$oReport->setReportSchedule($this);
			$oReport->setRequestDate($this->getNextQueueDate());
			$oReport->setIsHidden(!$this->getDeliveryType()->getSendToInbox());
			$oReport->setReportStatusID(reportCentreReportStatus::S_QUEUED);
		}
		
		return $oReport;
	}
}