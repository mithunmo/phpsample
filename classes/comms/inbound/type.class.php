<?php
/**
 * commsInboundType
 *
 * Stored in commsInboundType.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage inbound
 * @category commsInboundType
 * @version $Rev: 10 $
 */


/**
 * commsInboundType Class
 *
 * Provides access to records in comms.inboundTypes
 *
 * Creating a new record:
 * <code>
 * $oCommsInboundType = new commsInboundType();
 * $oCommsInboundType->setInboundTypeID($inInboundTypeID);
 * $oCommsInboundType->setDescription($inDescription);
 * $oCommsInboundType->setClassName($inClassName);
 * $oCommsInboundType->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsInboundType = new commsInboundType($inInboundTypeID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsInboundType = new commsInboundType();
 * $oCommsInboundType->setInboundTypeID($inInboundTypeID);
 * $oCommsInboundType->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsInboundType = commsInboundType::getInstance($inInboundTypeID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage inbound
 * @category commsInboundType
 */
class commsInboundType implements systemDaoInterface, systemDaoValidatorInterface {
	
	const T_SMS = 1;
	const T_IVR = 2;
	const T_WAP = 3;
	const T_WEB = 4;
	const T_EMAIL = 5;
	
	/**
	 * Container for static instances of commsInboundType
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
	 * Stores $_InboundTypeID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_InboundTypeID;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

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
	 * Returns a new instance of commsInboundType
	 *
	 * @param integer $inInboundTypeID
	 * @return commsInboundType
	 */
	function __construct($inInboundTypeID = null) {
		$this->reset();
		if ( $inInboundTypeID !== null ) {
			$this->setInboundTypeID($inInboundTypeID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsInboundType containing non-unique properties
	 *
	 * @param string $inDescription
	 * @param string $inClassName
	 * @return commsInboundType
	 * @static
	 */
	public static function factory($inDescription = null, $inClassName = null) {
		$oObject = new commsInboundType;
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inClassName !== null ) {
			$oObject->setClassName($inClassName);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsInboundType by primary key
	 *
	 * @param integer $inInboundTypeID
	 * @return commsInboundType
	 * @static
	 */
	public static function getInstance($inInboundTypeID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inInboundTypeID]) ) {
			return self::$_Instances[$inInboundTypeID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsInboundType();
		$oObject->setInboundTypeID($inInboundTypeID);
		if ( $oObject->load() ) {
			self::$_Instances[$inInboundTypeID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsInboundType
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.inboundTypes';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsInboundType();
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
			SELECT inboundTypeID, description, className
			  FROM '.system::getConfig()->getDatabase('comms').'.inboundTypes';

		$where = array();
		if ( $this->_InboundTypeID !== 0 ) {
			$where[] = ' inboundTypeID = :InboundTypeID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_InboundTypeID !== 0 ) {
				$oStmt->bindValue(':InboundTypeID', $this->_InboundTypeID);
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
		$this->setInboundTypeID((int)$inArray['inboundTypeID']);
		$this->setDescription($inArray['description']);
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
				throw new commsException($message);
			}
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.inboundTypes
					( inboundTypeID, description, className)
				VALUES
					(:InboundTypeID, :Description, :ClassName)
				ON DUPLICATE KEY UPDATE
					description=VALUES(description),
					className=VALUES(className),
					messageClassName=VALUES(messageClassName)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':InboundTypeID', $this->_InboundTypeID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':ClassName', $this->_ClassName);

					if ( $oStmt->execute() ) {
						if ( !$this->getInboundTypeID() ) {
							$this->setInboundTypeID($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.inboundTypes
			WHERE
				inboundTypeID = :InboundTypeID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':InboundTypeID', $this->_InboundTypeID);

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
	 * @return commsInboundType
	 */
	function reset() {
		$this->_InboundTypeID = 0;
		$this->_Description = '';
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
		$string .= " InboundTypeID[$this->_InboundTypeID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
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
		$className = 'commsInboundType';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"InboundTypeID\" value=\"$this->_InboundTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
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
			$valid = $this->checkInboundTypeID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkClassName($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_InboundTypeID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkInboundTypeID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_InboundTypeID) && $this->_InboundTypeID !== 0 ) {
			$inMessage .= "{$this->_InboundTypeID} is not a valid value for InboundTypeID";
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
		if ( $isValid && strlen($this->_ClassName) > 50 ) {
			$inMessage .= "ClassName cannot be more than 50 characters";
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
	 * @return commsInboundType
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
		return $this->_InboundTypeID;
	}

	/**
	 * Return value of $_InboundTypeID
	 *
	 * @return integer
	 * @access public
	 */
	function getInboundTypeID() {
		return $this->_InboundTypeID;
	}

	/**
	 * Set $_InboundTypeID to InboundTypeID
	 *
	 * @param integer $inInboundTypeID
	 * @return commsInboundType
	 * @access public
	 */
	function setInboundTypeID($inInboundTypeID) {
		if ( $inInboundTypeID !== $this->_InboundTypeID ) {
			$this->_InboundTypeID = $inInboundTypeID;
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
	 * @return commsInboundType
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
	 * @return commsInboundType
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
	 * @return commsInboundType
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}