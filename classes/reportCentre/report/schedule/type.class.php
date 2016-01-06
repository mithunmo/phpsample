<?php
/**
 * reportCentreReportScheduleType
 *
 * Stored in reportCentreReportScheduleType.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package reportCentre
 * @subpackage reportCentreReportScheduleType
 * @category reportCentreReportScheduleType
 * @version $Rev: 10 $
 */


/**
 * reportCentreReportScheduleType Class
 *
 * Provides access to records in reports.reportScheduleTypes
 *
 * Creating a new record:
 * <code>
 * $oReportScheduleType = new reportCentreReportScheduleType();
 * $oReportScheduleType->setScheduleTypeID($inScheduleTypeID);
 * $oReportScheduleType->setDescription($inDescription);
 * $oReportScheduleType->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oReportScheduleType = new reportCentreReportScheduleType($inScheduleTypeID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oReportScheduleType = new reportCentreReportScheduleType();
 * $oReportScheduleType->setScheduleTypeID($inScheduleTypeID);
 * $oReportScheduleType->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oReportScheduleType = reportCentreReportScheduleType::getInstance($inScheduleTypeID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package reportCentre
 * @subpackage reportCentreReportScheduleType
 * @category reportCentreReportScheduleType
 */
class reportCentreReportScheduleType implements systemDaoInterface, systemDaoValidatorInterface {
	
	const T_ONCE = 1;
	const T_DAILY = 2;
	const T_WEEKLY = 3;
	const T_FORTNIGHTLY = 4;
	const T_MONTHLY = 5;
	const T_QUARTERLY = 6;
	const T_YEARLY = 7;
	
	/**
	 * Container for static instances of reportCentreReportScheduleType
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
	 * Stores $_ScheduleTypeID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ScheduleTypeID;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;
	
	/**
	 * Stores $_TimeOffset
	 *
	 * @var string
	 * @access protected
	 */
	protected $_TimeOffset;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of reportCentreReportScheduleType
	 *
	 * @param integer $inScheduleTypeID
	 * @return reportCentreReportScheduleType
	 */
	function __construct($inScheduleTypeID = null) {
		$this->reset();
		if ( $inScheduleTypeID !== null ) {
			$this->setScheduleTypeID($inScheduleTypeID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new reportCentreReportScheduleType containing non-unique properties
	 *
	 * @param string $inDescription
	 * @param string $inTimeOffset
	 * @return reportCentreReportScheduleType
	 * @static
	 */
	public static function factory($inDescription = null, $inTimeOffset = null) {
		$oObject = new reportCentreReportScheduleType;
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inTimeOffset !== null ) {
			$oObject->setTimeOffset($inTimeOffset);
		}
		return $oObject;
	}

	/**
	 * Get an instance of reportCentreReportScheduleType by primary key
	 *
	 * @param integer $inScheduleTypeID
	 * @return reportCentreReportScheduleType
	 * @static
	 */
	public static function getInstance($inScheduleTypeID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inScheduleTypeID]) ) {
			return self::$_Instances[$inScheduleTypeID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new reportCentreReportScheduleType();
		$oObject->setScheduleTypeID($inScheduleTypeID);
		if ( $oObject->load() ) {
			self::$_Instances[$inScheduleTypeID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of reportCentreReportScheduleType
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('reports').'.reportScheduleTypes';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new reportCentreReportScheduleType();
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
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT scheduleTypeID, description, timeOffset
			  FROM '.system::getConfig()->getDatabase('reports').'.reportScheduleTypes';

		$where = array();
		if ( $this->_ScheduleTypeID !== 0 ) {
			$where[] = ' scheduleTypeID = :ScheduleTypeID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_ScheduleTypeID !== 0 ) {
				$oStmt->bindValue(':ScheduleTypeID', $this->_ScheduleTypeID);
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
		$this->setScheduleTypeID((int)$inArray['scheduleTypeID']);
		$this->setDescription($inArray['description']);
		$this->setTimeOffset($inArray['timeOffset']);
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
				INSERT INTO '.system::getConfig()->getDatabase('reports').'.reportScheduleTypes
					( scheduleTypeID, description, timeOffset)
				VALUES
					(:ScheduleTypeID, :Description, :TimeOffset)
				ON DUPLICATE KEY UPDATE
					description=VALUES(description),
					timeOffset=VALUES(timeOffset)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ScheduleTypeID', $this->_ScheduleTypeID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':TimeOffset', $this->_TimeOffset);

					if ( $oStmt->execute() ) {
						if ( !$this->getScheduleTypeID() ) {
							$this->setScheduleTypeID($oDB->lastInsertId());
						}
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
			DELETE FROM '.system::getConfig()->getDatabase('reports').'.reportScheduleTypes
			WHERE
				scheduleTypeID = :ScheduleTypeID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ScheduleTypeID', $this->_ScheduleTypeID);

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
	 * @return reportCentreReportScheduleType
	 */
	function reset() {
		$this->_ScheduleTypeID = 0;
		$this->_Description = '';
		$this->_TimeOffset = '';
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
		$string .= " ScheduleTypeID[$this->_ScheduleTypeID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " TimeOffset[$this->_TimeOffset] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'reportCentreReportScheduleType';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ScheduleTypeID\" value=\"$this->_ScheduleTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"TimeOffset\" value=\"$this->_TimeOffset\" type=\"string\" /> $newLine";
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
			$valid = $this->checkScheduleTypeID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_ScheduleTypeID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkScheduleTypeID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ScheduleTypeID) && $this->_ScheduleTypeID !== 0 ) {
			$inMessage .= "{$this->_ScheduleTypeID} is not a valid value for ScheduleTypeID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Description has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkDescription(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Description) && $this->_Description !== '' ) {
			$inMessage .= "{$this->_Description} is not a valid value for Description";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) > 50 ) {
			$inMessage .= "Description cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
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
	 * @return reportCentreReportScheduleType
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
		return $this->_ScheduleTypeID;
	}

	/**
	 * Return value of $_ScheduleTypeID
	 *
	 * @return integer
	 * @access public
	 */
	function getScheduleTypeID() {
		return $this->_ScheduleTypeID;
	}

	/**
	 * Set $_ScheduleTypeID to ScheduleTypeID
	 *
	 * @param integer $inScheduleTypeID
	 * @return reportCentreReportScheduleType
	 * @access public
	 */
	function setScheduleTypeID($inScheduleTypeID) {
		if ( $inScheduleTypeID !== $this->_ScheduleTypeID ) {
			$this->_ScheduleTypeID = $inScheduleTypeID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Description
	 *
	 * @return string
	 * @access public
	 */
	function getDescription() {
		return $this->_Description;
	}

	/**
	 * Returns $_TimeOffset
	 *
	 * @return string
	 */
	function getTimeOffset() {
		return $this->_TimeOffset;
	}
	
	/**
	 * Set $_TimeOffset to $inTimeOffset
	 *
	 * @param string $inTimeOffset
	 * @return reportCentreReportScheduleType
	 */
	function setTimeOffset($inTimeOffset) {
		if ( $inTimeOffset !== $this->_TimeOffset ) {
			$this->_TimeOffset = $inTimeOffset;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Set $_Description to Description
	 *
	 * @param string $inDescription
	 * @return reportCentreReportScheduleType
	 * @access public
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
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
	 * @return reportCentreReportScheduleType
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	/**
	 * Calculates the next due date based on the schedule type
	 * 
	 * @param datetime $inDate
	 * @return datetime
	 * @throws RuntimeException
	 */
	public function getNextQueueDate($inDate) {
		if ( !$inDate instanceof systemDateTime ) {
			$inDate = systemDateTime::getInstance($inDate, system::getConfig()->getSystemTimeZone()->getParamValue());
		}

		if ( $this->getTimeOffset() ) {
			return $inDate
					->modify($this->getTimeOffset())
					->format(system::getConfig()->getDatabaseDatetimeFormat());
		} else {
			throw new RuntimeException('Time offset has not been specified for schedule type: '.$this->getDescription());
		}
	}
}