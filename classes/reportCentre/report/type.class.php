<?php
/**
 * reportCentreReportType
 *
 * Stored in reportCentreReportType.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package reportCentre
 * @subpackage reportCentreReportType
 * @category reportCentreReportType
 * @version $Rev: 10 $
 */


/**
 * reportCentreReportType Class
 *
 * Provides access to records in reports.reportTypes
 *
 * Creating a new record:
 * <code>
 * $oReportCentreReportType = new reportCentreReportType();
 * $oReportCentreReportType->setReportTypeID($inReportTypeID);
 * $oReportCentreReportType->setTypeName($inTypeName);
 * $oReportCentreReportType->setDescription($inDescription);
 * $oReportCentreReportType->setVisible($inVisible);
 * $oReportCentreReportType->setClassName($inClassName);
 * $oReportCentreReportType->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oReportCentreReportType = new reportCentreReportType($inReportTypeID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oReportCentreReportType = new reportCentreReportType();
 * $oReportCentreReportType->setReportTypeID($inReportTypeID);
 * $oReportCentreReportType->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oReportCentreReportType = reportCentreReportType::getInstance($inReportTypeID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package reportCentre
 * @subpackage reportCentreReportType
 * @category reportCentreReportType
 */
class reportCentreReportType implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of reportCentreReportType
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
	 * Stores $_ReportTypeID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ReportTypeID;

	/**
	 * Stores $_TypeName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_TypeName;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_Visible
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Visible;

	/**
	 * Stores $_ClassName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ClassName;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of reportCentreReportType
	 *
	 * @param integer $inReportTypeID
	 * @return reportCentreReportType
	 */
	function __construct($inReportTypeID = null) {
		$this->reset();
		if ( $inReportTypeID !== null ) {
			$this->setReportTypeID($inReportTypeID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new reportCentreReportType containing non-unique properties
	 *
	 * @param string $inTypeName
	 * @param string $inDescription
	 * @param integer $inVisible
	 * @param string $inClassName
	 * @return reportCentreReportType
	 * @static
	 */
	public static function factory($inTypeName = null, $inDescription = null, $inVisible = null, $inClassName = null) {
		$oObject = new reportCentreReportType;
		if ( $inTypeName !== null ) {
			$oObject->setTypeName($inTypeName);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inVisible !== null ) {
			$oObject->setVisible($inVisible);
		}
		if ( $inClassName !== null ) {
			$oObject->setClassName($inClassName);
		}
		return $oObject;
	}

	/**
	 * Get an instance of reportCentreReportType by primary key
	 *
	 * @param integer $inReportTypeID
	 * @return reportCentreReportType
	 * @static
	 */
	public static function getInstance($inReportTypeID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inReportTypeID]) ) {
			return self::$_Instances[$inReportTypeID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new reportCentreReportType();
		$oObject->setReportTypeID($inReportTypeID);
		if ( $oObject->load() ) {
			self::$_Instances[$inReportTypeID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of reportCentreReportType
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inVisible = false) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('reports').'.reportTypes';
		
		if ( $inVisible ) {
			$query .= ' WHERE visible = 1';
		}
		
		$query .= ' ORDER BY typeName ASC ';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new reportCentreReportType();
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
			SELECT reportTypeID, typeName, description, visible, className
			  FROM '.system::getConfig()->getDatabase('reports').'.reportTypes';

		$where = array();
		if ( $this->_ReportTypeID !== 0 ) {
			$where[] = ' reportTypeID = :ReportTypeID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_ReportTypeID !== 0 ) {
				$oStmt->bindValue(':ReportTypeID', $this->_ReportTypeID);
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
		$this->setReportTypeID((int)$inArray['reportTypeID']);
		$this->setTypeName($inArray['typeName']);
		$this->setDescription($inArray['description']);
		$this->setVisible((int)$inArray['visible']);
		$this->setClassName($inArray['className']);
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
				INSERT INTO '.system::getConfig()->getDatabase('reports').'.reportTypes
					( reportTypeID, typeName, description, visible, className)
				VALUES
					(:ReportTypeID, :TypeName, :Description, :Visible, :ClassName)
				ON DUPLICATE KEY UPDATE
					typeName=VALUES(typeName),
					description=VALUES(description),
					visible=VALUES(visible),
					className=VALUES(className)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ReportTypeID', $this->_ReportTypeID);
					$oStmt->bindValue(':TypeName', $this->_TypeName);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':Visible', $this->_Visible);
					$oStmt->bindValue(':ClassName', $this->_ClassName);

					if ( $oStmt->execute() ) {
						if ( !$this->getReportTypeID() ) {
							$this->setReportTypeID($oDB->lastInsertId());
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
		if ( $this->getReportTypeID() ) {
			$this->setVisible(0);
			$this->save();
			return true;
		}
		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return reportCentreReportType
	 */
	function reset() {
		$this->_ReportTypeID = 0;
		$this->_TypeName = '';
		$this->_Description = '';
		$this->_Visible = 0;
		$this->_ClassName = '';
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
		$string .= " ReportTypeID[$this->_ReportTypeID] $newLine";
		$string .= " TypeName[$this->_TypeName] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " Visible[$this->_Visible] $newLine";
		$string .= " ClassName[$this->_ClassName] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'reportCentreReportType';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ReportTypeID\" value=\"$this->_ReportTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"TypeName\" value=\"$this->_TypeName\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Visible\" value=\"$this->_Visible\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ClassName\" value=\"$this->_ClassName\" type=\"string\" /> $newLine";
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
			$valid = $this->checkReportTypeID($message);
		}
		if ( $valid ) {
			$valid = $this->checkTypeName($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkVisible($message);
		}
		if ( $valid ) {
			$valid = $this->checkClassName($message);
		}
		return $valid;
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
	 * Checks that $_TypeName has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkTypeName(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_TypeName) && $this->_TypeName !== '' ) {
			$inMessage .= "{$this->_TypeName} is not a valid value for TypeName";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_TypeName) > 120 ) {
			$inMessage .= "TypeName cannot be more than 120 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_TypeName) <= 1 ) {
			$inMessage .= "TypeName must be more than 1 character";
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
		if ( $isValid && strlen($this->_Description) > 255 ) {
			$inMessage .= "Description cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Visible has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkVisible(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Visible) && $this->_Visible !== 0 ) {
			$inMessage .= "{$this->_Visible} is not a valid value for Visible";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_ClassName has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkClassName(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ClassName) && $this->_ClassName !== '' ) {
			$inMessage .= "{$this->_ClassName} is not a valid value for ClassName";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_ClassName) > 100 ) {
			$inMessage .= "ClassName cannot be more than 100 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_ClassName) <= 1 ) {
			$inMessage .= "ClassName must be more than 1 character";
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
	 * @return reportCentreReportType
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
		return $this->_ReportTypeID;
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
	 * @return reportCentreReportType
	 * @access public
	 */
	function setReportTypeID($inReportTypeID) {
		if ( $inReportTypeID !== $this->_ReportTypeID ) {
			$this->_ReportTypeID = $inReportTypeID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_TypeName
	 *
	 * @return string
	 * @access public
	 */
	function getTypeName() {
		return $this->_TypeName;
	}

	/**
	 * Set $_TypeName to TypeName
	 *
	 * @param string $inTypeName
	 * @return reportCentreReportType
	 * @access public
	 */
	function setTypeName($inTypeName) {
		if ( $inTypeName !== $this->_TypeName ) {
			$this->_TypeName = $inTypeName;
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
	 * @return reportCentreReportType
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
	 * Return value of $_Visible
	 *
	 * @return integer
	 * @access public
	 */
	function getVisible() {
		return $this->_Visible;
	}

	/**
	 * Set $_Visible to Visible
	 *
	 * @param integer $inVisible
	 * @return reportCentreReportType
	 * @access public
	 */
	function setVisible($inVisible) {
		if ( $inVisible !== $this->_Visible ) {
			$this->_Visible = $inVisible;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_ClassName
	 *
	 * @return string
	 * @access public
	 */
	function getClassName() {
		return $this->_ClassName;
	}

	/**
	 * Set $_ClassName to ClassName
	 *
	 * @param string $inClassName
	 * @return reportCentreReportType
	 * @access public
	 */
	function setClassName($inClassName) {
		if ( $inClassName !== $this->_ClassName ) {
			$this->_ClassName = $inClassName;
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
	 * @return reportCentreReportType
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}

	/**
	 * Returns the supported output formats for this report
	 * 
	 * @return array
	 */
	function getOutputTypes() {
		$oObject = new $this->_ClassName();
		return $oObject->getSupportedReportWriters();
	}
	
	/**
	 * Returns an icon name for the specified output type
	 * 
	 * @param string $inType
	 * @return string
	 */
	function getIcon($inType) {
		switch ( $inType ) {
			case reportManager::OUTPUT_CSV: $icon = 'mime-text-csv.png'; break;
			case reportManager::OUTPUT_HTML: $icon = 'mime-text-html.png'; break;
			case reportManager::OUTPUT_ODS: $icon = 'mime-application-ods.png'; break;
			case reportManager::OUTPUT_PDF: $icon = 'mime-application-pdf.png'; break;
			case reportManager::OUTPUT_XLS: $icon = 'mime-application-excel.png'; break;
			case reportManager::OUTPUT_XLSX: $icon = 'mime-application-excel.png'; break;
			case reportManager::OUTPUT_XML: $icon = 'mime-text-xml.png'; break;
			default: $icon = 'generic.png';
		}
		return $icon;
	}
}