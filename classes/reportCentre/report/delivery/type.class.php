<?php
/**
 * reportCentreReportDeliveryType
 *
 * Stored in reportCentreReportDeliveryType.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package reportCentre
 * @subpackage reportCentreReportDeliveryType
 * @category reportCentreReportDeliveryType
 * @version $Rev: 10 $
 */


/**
 * reportCentreReportDeliveryType Class
 *
 * Provides access to records in reports.reportDeliveryTypes
 *
 * Creating a new record:
 * <code>
 * $oReportCentreReportDeliveryType = new reportCentreReportDeliveryType();
 * $oReportCentreReportDeliveryType->setDeliveryTypeID($inDeliveryTypeID);
 * $oReportCentreReportDeliveryType->setTypeName($inTypeName);
 * $oReportCentreReportDeliveryType->setSendToInbox($inSendToInbox);
 * $oReportCentreReportDeliveryType->setSendToUserEmail($inSendToUserEmail);
 * $oReportCentreReportDeliveryType->setSendToGroup($inSendToGroup);
 * $oReportCentreReportDeliveryType->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oReportCentreReportDeliveryType = new reportCentreReportDeliveryType($inDeliveryTypeID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oReportCentreReportDeliveryType = new reportCentreReportDeliveryType();
 * $oReportCentreReportDeliveryType->setDeliveryTypeID($inDeliveryTypeID);
 * $oReportCentreReportDeliveryType->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oReportCentreReportDeliveryType = reportCentreReportDeliveryType::getInstance($inDeliveryTypeID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package reportCentre
 * @subpackage reportCentreReportDeliveryType
 * @category reportCentreReportDeliveryType
 */
class reportCentreReportDeliveryType implements systemDaoInterface, systemDaoValidatorInterface {
	
	const T_INBOX = 1;
	const T_EMAIL = 2;
	const T_INBOX_EMAIL = 3;
	const T_INBOX_GROUP = 4;
	
	/**
	 * Container for static instances of reportCentreReportDeliveryType
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
	 * Stores $_DeliveryTypeID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_DeliveryTypeID;

	/**
	 * Stores $_TypeName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_TypeName;

	/**
	 * Stores $_SendToInbox
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SendToInbox;

	/**
	 * Stores $_SendToUserEmail
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SendToUserEmail;

	/**
	 * Stores $_SendToGroup
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SendToGroup;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of reportCentreReportDeliveryType
	 *
	 * @param integer $inDeliveryTypeID
	 * @return reportCentreReportDeliveryType
	 */
	function __construct($inDeliveryTypeID = null) {
		$this->reset();
		if ( $inDeliveryTypeID !== null ) {
			$this->setDeliveryTypeID($inDeliveryTypeID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new reportCentreReportDeliveryType containing non-unique properties
	 *
	 * @param string $inTypeName
	 * @param integer $inSendToInbox
	 * @param integer $inSendToUserEmail
	 * @param integer $inSendToGroup
	 * @return reportCentreReportDeliveryType
	 * @static
	 */
	public static function factory($inTypeName = null, $inSendToInbox = null, $inSendToUserEmail = null, $inSendToGroup = null) {
		$oObject = new reportCentreReportDeliveryType;
		if ( $inTypeName !== null ) {
			$oObject->setTypeName($inTypeName);
		}
		if ( $inSendToInbox !== null ) {
			$oObject->setSendToInbox($inSendToInbox);
		}
		if ( $inSendToUserEmail !== null ) {
			$oObject->setSendToUserEmail($inSendToUserEmail);
		}
		if ( $inSendToGroup !== null ) {
			$oObject->setSendToGroup($inSendToGroup);
		}
		return $oObject;
	}

	/**
	 * Get an instance of reportCentreReportDeliveryType by primary key
	 *
	 * @param integer $inDeliveryTypeID
	 * @return reportCentreReportDeliveryType
	 * @static
	 */
	public static function getInstance($inDeliveryTypeID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inDeliveryTypeID]) ) {
			return self::$_Instances[$inDeliveryTypeID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new reportCentreReportDeliveryType();
		$oObject->setDeliveryTypeID($inDeliveryTypeID);
		if ( $oObject->load() ) {
			self::$_Instances[$inDeliveryTypeID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of reportCentreReportDeliveryType
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('reports').'.reportDeliveryTypes ORDER BY typeName ASC';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new reportCentreReportDeliveryType();
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
			SELECT deliveryTypeID, typeName, sendToInbox, sendToUserEmail, sendToGroup
			  FROM '.system::getConfig()->getDatabase('reports').'.reportDeliveryTypes';

		$where = array();
		if ( $this->_DeliveryTypeID !== 0 ) {
			$where[] = ' deliveryTypeID = :DeliveryTypeID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_DeliveryTypeID !== 0 ) {
				$oStmt->bindValue(':DeliveryTypeID', $this->_DeliveryTypeID);
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
		$this->setDeliveryTypeID((int)$inArray['deliveryTypeID']);
		$this->setTypeName($inArray['typeName']);
		$this->setSendToInbox((int)$inArray['sendToInbox']);
		$this->setSendToUserEmail((int)$inArray['sendToUserEmail']);
		$this->setSendToGroup((int)$inArray['sendToGroup']);
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
				INSERT INTO '.system::getConfig()->getDatabase('reports').'.reportDeliveryTypes
					( deliveryTypeID, typeName, sendToInbox, sendToUserEmail, sendToGroup)
				VALUES
					(:DeliveryTypeID, :TypeName, :SendToInbox, :SendToUserEmail, :SendToGroup)
				ON DUPLICATE KEY UPDATE
					typeName=VALUES(typeName),
					sendToInbox=VALUES(sendToInbox),
					sendToUserEmail=VALUES(sendToUserEmail),
					sendToGroup=VALUES(sendToGroup)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':DeliveryTypeID', $this->_DeliveryTypeID);
					$oStmt->bindValue(':TypeName', $this->_TypeName);
					$oStmt->bindValue(':SendToInbox', $this->_SendToInbox);
					$oStmt->bindValue(':SendToUserEmail', $this->_SendToUserEmail);
					$oStmt->bindValue(':SendToGroup', $this->_SendToGroup);

					if ( $oStmt->execute() ) {
						if ( !$this->getDeliveryTypeID() ) {
							$this->setDeliveryTypeID($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('reports').'.reportDeliveryTypes
			WHERE
				deliveryTypeID = :DeliveryTypeID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':DeliveryTypeID', $this->_DeliveryTypeID);

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
	 * @return reportCentreReportDeliveryType
	 */
	function reset() {
		$this->_DeliveryTypeID = 0;
		$this->_TypeName = '';
		$this->_SendToInbox = 0;
		$this->_SendToUserEmail = 0;
		$this->_SendToGroup = 0;
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
		$string .= " DeliveryTypeID[$this->_DeliveryTypeID] $newLine";
		$string .= " TypeName[$this->_TypeName] $newLine";
		$string .= " SendToInbox[$this->_SendToInbox] $newLine";
		$string .= " SendToUserEmail[$this->_SendToUserEmail] $newLine";
		$string .= " SendToGroup[$this->_SendToGroup] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'reportCentreReportDeliveryType';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"DeliveryTypeID\" value=\"$this->_DeliveryTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"TypeName\" value=\"$this->_TypeName\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"SendToInbox\" value=\"$this->_SendToInbox\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"SendToUserEmail\" value=\"$this->_SendToUserEmail\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"SendToGroup\" value=\"$this->_SendToGroup\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkDeliveryTypeID($message);
		}
		if ( $valid ) {
			$valid = $this->checkTypeName($message);
		}
		if ( $valid ) {
			$valid = $this->checkSendToInbox($message);
		}
		if ( $valid ) {
			$valid = $this->checkSendToUserEmail($message);
		}
		if ( $valid ) {
			$valid = $this->checkSendToGroup($message);
		}
		return $valid;
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
		if ( $isValid && strlen($this->_TypeName) > 150 ) {
			$inMessage .= "TypeName cannot be more than 150 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_TypeName) <= 1 ) {
			$inMessage .= "TypeName must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_SendToInbox has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkSendToInbox(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_SendToInbox) && $this->_SendToInbox !== 0 ) {
			$inMessage .= "{$this->_SendToInbox} is not a valid value for SendToInbox";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_SendToUserEmail has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkSendToUserEmail(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_SendToUserEmail) && $this->_SendToUserEmail !== 0 ) {
			$inMessage .= "{$this->_SendToUserEmail} is not a valid value for SendToUserEmail";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_SendToGroup has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkSendToGroup(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_SendToGroup) && $this->_SendToGroup !== 0 ) {
			$inMessage .= "{$this->_SendToGroup} is not a valid value for SendToGroup";
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
	 * @return reportCentreReportDeliveryType
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
		return $this->_DeliveryTypeID;
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
	 * Set $_DeliveryTypeID to DeliveryTypeID
	 *
	 * @param integer $inDeliveryTypeID
	 * @return reportCentreReportDeliveryType
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
	 * @return reportCentreReportDeliveryType
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
	 * Return value of $_SendToInbox
	 *
	 * @return integer
	 * @access public
	 */
	function getSendToInbox() {
		return $this->_SendToInbox;
	}

	/**
	 * Set $_SendToInbox to SendToInbox
	 *
	 * @param integer $inSendToInbox
	 * @return reportCentreReportDeliveryType
	 * @access public
	 */
	function setSendToInbox($inSendToInbox) {
		if ( $inSendToInbox !== $this->_SendToInbox ) {
			$this->_SendToInbox = $inSendToInbox;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_SendToUserEmail
	 *
	 * @return integer
	 * @access public
	 */
	function getSendToUserEmail() {
		return $this->_SendToUserEmail;
	}

	/**
	 * Set $_SendToUserEmail to SendToUserEmail
	 *
	 * @param integer $inSendToUserEmail
	 * @return reportCentreReportDeliveryType
	 * @access public
	 */
	function setSendToUserEmail($inSendToUserEmail) {
		if ( $inSendToUserEmail !== $this->_SendToUserEmail ) {
			$this->_SendToUserEmail = $inSendToUserEmail;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_SendToGroup
	 *
	 * @return integer
	 * @access public
	 */
	function getSendToGroup() {
		return $this->_SendToGroup;
	}

	/**
	 * Set $_SendToGroup to SendToGroup
	 *
	 * @param integer $inSendToGroup
	 * @return reportCentreReportDeliveryType
	 * @access public
	 */
	function setSendToGroup($inSendToGroup) {
		if ( $inSendToGroup !== $this->_SendToGroup ) {
			$this->_SendToGroup = $inSendToGroup;
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
	 * @return reportCentreReportDeliveryType
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}