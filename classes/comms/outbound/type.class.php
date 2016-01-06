<?php
/**
 * commsOutboundType
 *
 * Stored in commsOutboundType.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundType
 * @version $Rev: 10 $
 */


/**
 * commsOutboundType Class
 *
 * Provides access to records in comms.outboundTypes
 *
 * Creating a new record:
 * <code>
 * $oCommsOutboundType = new commsOutboundType();
 * $oCommsOutboundType->setOutboundTypeID($inOutboundTypeID);
 * $oCommsOutboundType->setDescription($inDescription);
 * $oCommsOutboundType->setClassName($inClassName);
 * $oCommsOutboundType->setMessageClassName($inMessageClassName);
 * $oCommsOutboundType->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsOutboundType = new commsOutboundType($inOutboundTypeID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsOutboundType = new commsOutboundType();
 * $oCommsOutboundType->setOutboundTypeID($inOutboundTypeID);
 * $oCommsOutboundType->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsOutboundType = commsOutboundType::getInstance($inOutboundTypeID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundType
 */
class commsOutboundType implements systemDaoInterface, systemDaoValidatorInterface {
	
	const T_APP_LOOP_BACK = 1;
	const T_EMAIL = 2;
	const T_IVR = 3;
	const T_SMS = 4;
	const T_WAP_PUSH = 5;
	const T_WBXML_RIGHTS = 6;
	
	/**
	 * Container for static instances of commsOutboundType
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
	 * Stores $_OutboundTypeID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_OutboundTypeID;

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
	 * Returns a new instance of commsOutboundType
	 *
	 * @param integer $inOutboundTypeID
	 * @return commsOutboundType
	 */
	function __construct($inOutboundTypeID = null) {
		$this->reset();
		if ( $inOutboundTypeID !== null ) {
			$this->setOutboundTypeID($inOutboundTypeID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsOutboundType containing non-unique properties
	 *
	 * @param string $inDescription
	 * @param string $inClassName
	 * @return commsOutboundType
	 * @static
	 */
	public static function factory($inDescription = null, $inClassName = null) {
		$oObject = new commsOutboundType;
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inClassName !== null ) {
			$oObject->setClassName($inClassName);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsOutboundType by primary key
	 *
	 * @param integer $inOutboundTypeID
	 * @return commsOutboundType
	 * @static
	 */
	public static function getInstance($inOutboundTypeID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inOutboundTypeID]) ) {
			return self::$_Instances[$inOutboundTypeID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsOutboundType();
		$oObject->setOutboundTypeID($inOutboundTypeID);
		if ( $oObject->load() ) {
			self::$_Instances[$inOutboundTypeID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsOutboundType
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.outboundTypes';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsOutboundType();
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
			SELECT outboundTypeID, description, className
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundTypes';

		$where = array();
		if ( $this->_OutboundTypeID !== 0 ) {
			$where[] = ' outboundTypeID = :OutboundTypeID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_OutboundTypeID !== 0 ) {
				$oStmt->bindValue(':OutboundTypeID', $this->_OutboundTypeID);
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
		$this->setOutboundTypeID((int)$inArray['outboundTypeID']);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.outboundTypes
					( outboundTypeID, description, className)
				VALUES
					(:OutboundTypeID, :Description, :ClassName)
				ON DUPLICATE KEY UPDATE
					description=VALUES(description),
					className=VALUES(className)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':OutboundTypeID', $this->_OutboundTypeID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':ClassName', $this->_ClassName);

					if ( $oStmt->execute() ) {
						if ( !$this->getOutboundTypeID() ) {
							$this->setOutboundTypeID($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.outboundTypes
			WHERE
				outboundTypeID = :OutboundTypeID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':OutboundTypeID', $this->_OutboundTypeID);

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
	 * @return commsOutboundType
	 */
	function reset() {
		$this->_OutboundTypeID = 0;
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
		$string .= " OutboundTypeID[$this->_OutboundTypeID] $newLine";
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
		$className = 'commsOutboundType';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"OutboundTypeID\" value=\"$this->_OutboundTypeID\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkOutboundTypeID($message);
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
	 * Checks that $_OutboundTypeID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkOutboundTypeID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_OutboundTypeID) && $this->_OutboundTypeID !== 0 ) {
			$inMessage .= "{$this->_OutboundTypeID} is not a valid value for OutboundTypeID";
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
		if ( $isValid && strlen($this->_Description) > 30 ) {
			$inMessage .= "Description cannot be more than 30 characters";
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
	 * @return commsOutboundType
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
		return $this->_OutboundTypeID;
	}

	/**
	 * Return value of $_OutboundTypeID
	 *
	 * @return integer
	 * @access public
	 */
	function getOutboundTypeID() {
		return $this->_OutboundTypeID;
	}

	/**
	 * Set $_OutboundTypeID to OutboundTypeID
	 *
	 * @param integer $inOutboundTypeID
	 * @return commsOutboundType
	 * @access public
	 */
	function setOutboundTypeID($inOutboundTypeID) {
		if ( $inOutboundTypeID !== $this->_OutboundTypeID ) {
			$this->_OutboundTypeID = $inOutboundTypeID;
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
	 * @return commsOutboundType
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
	 * @return commsOutboundType
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
	 * @return commsOutboundType
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}