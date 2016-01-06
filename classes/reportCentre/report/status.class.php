<?php
/**
 * reportCentreReportStatus
 *
 * Stored in reportCentreReportStatus.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package reportCentre
 * @subpackage reportCentreReportStatus
 * @category reportCentreReportStatus
 * @version $Rev: 10 $
 */


/**
 * reportCentreReportStatus Class
 *
 * Provides access to records in reports.reportStatus
 *
 * Creating a new record:
 * <code>
 * $oReportCentreReportStatus = new reportCentreReportStatus();
 * $oReportCentreReportStatus->setReportStatusID($inReportStatusID);
 * $oReportCentreReportStatus->setDescription($inDescription);
 * $oReportCentreReportStatus->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oReportCentreReportStatus = new reportCentreReportStatus($inReportStatusID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oReportCentreReportStatus = new reportCentreReportStatus();
 * $oReportCentreReportStatus->setReportStatusID($inReportStatusID);
 * $oReportCentreReportStatus->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oReportCentreReportStatus = reportCentreReportStatus::getInstance($inReportStatusID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package reportCentre
 * @subpackage reportCentreReportStatus
 * @category reportCentreReportStatus
 */
class reportCentreReportStatus implements systemDaoInterface, systemDaoValidatorInterface {
	
	const S_QUEUED = 1;
	const S_PROCESSING = 2;
	const S_REFRESHING = 3;
	const S_SCHEDULED = 4;
	const S_FAILED_NO_RESULTS = 5;
	const S_COMPLETED = 6;
	const S_FAILED_UNKNOWN = 7;
	const S_REMOVED_SCHEDULE = 8;
	
	/**
	 * Container for static instances of reportCentreReportStatus
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
	 * Stores $_ReportStatusID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ReportStatusID;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of reportCentreReportStatus
	 *
	 * @param integer $inReportStatusID
	 * @return reportCentreReportStatus
	 */
	function __construct($inReportStatusID = null) {
		$this->reset();
		if ( $inReportStatusID !== null ) {
			$this->setReportStatusID($inReportStatusID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new reportCentreReportStatus containing non-unique properties
	 *
	 * @param string $inDescription
	 * @return reportCentreReportStatus
	 * @static
	 */
	public static function factory($inDescription = null) {
		$oObject = new reportCentreReportStatus;
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		return $oObject;
	}

	/**
	 * Get an instance of reportCentreReportStatus by primary key
	 *
	 * @param integer $inReportStatusID
	 * @return reportCentreReportStatus
	 * @static
	 */
	public static function getInstance($inReportStatusID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inReportStatusID]) ) {
			return self::$_Instances[$inReportStatusID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new reportCentreReportStatus();
		$oObject->setReportStatusID($inReportStatusID);
		if ( $oObject->load() ) {
			self::$_Instances[$inReportStatusID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of reportCentreReportStatus
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('reports').'.reportStatus';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new reportCentreReportStatus();
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
			SELECT reportStatusID, description
			  FROM '.system::getConfig()->getDatabase('reports').'.reportStatus';

		$where = array();
		if ( $this->_ReportStatusID !== 0 ) {
			$where[] = ' reportStatusID = :ReportStatusID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_ReportStatusID !== 0 ) {
				$oStmt->bindValue(':ReportStatusID', $this->_ReportStatusID);
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
		$this->setReportStatusID((int)$inArray['reportStatusID']);
		$this->setDescription($inArray['description']);
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
				INSERT INTO '.system::getConfig()->getDatabase('reports').'.reportStatus
					( reportStatusID, description)
				VALUES
					(:ReportStatusID, :Description)
				ON DUPLICATE KEY UPDATE
					description=VALUES(description)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ReportStatusID', $this->_ReportStatusID);
					$oStmt->bindValue(':Description', $this->_Description);

					if ( $oStmt->execute() ) {
						if ( !$this->getReportStatusID() ) {
							$this->setReportStatusID($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('reports').'.reportStatus
			WHERE
				reportStatusID = :ReportStatusID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ReportStatusID', $this->_ReportStatusID);

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
	 * @return reportCentreReportStatus
	 */
	function reset() {
		$this->_ReportStatusID = 0;
		$this->_Description = '';
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
		$string .= " ReportStatusID[$this->_ReportStatusID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'reportCentreReportStatus';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ReportStatusID\" value=\"$this->_ReportStatusID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
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
			$valid = $this->checkReportStatusID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		return $valid;
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
		if ( $isValid && strlen($this->_Description) > 100 ) {
			$inMessage .= "Description cannot be more than 100 characters";
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
	 * @return reportCentreReportStatus
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
		return $this->_ReportStatusID;
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
	 * Set $_ReportStatusID to ReportStatusID
	 *
	 * @param integer $inReportStatusID
	 * @return reportCentreReportStatus
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
	 * Return value of $_Description
	 *
	 * @return string
	 * @access public
	 */
	function getDescription() {
		return $this->_Description;
	}

	/**
	 * Set $_Description to Description
	 *
	 * @param string $inDescription
	 * @return reportCentreReportStatus
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
	 * @return reportCentreReportStatus
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}