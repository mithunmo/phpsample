<?php
/**
 * commsInboundMessageBase
 *
 * Stored in commsInboundMessageBase.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage inbound
 * @category commsInboundMessageBase
 * @version $Rev: 10 $
 */


/**
 * commsInboundMessageBase Class
 *
 * Provides access to records in comms.inboundMessages
 *
 * Creating a new record:
 * <code>
 * $oCommsInboundMessageBase = new commsInboundMessageBase();
 * $oCommsInboundMessageBase->setMessageID($inMessageID);
 * $oCommsInboundMessageBase->setInboundTypeID($inInboundTypeID);
 * $oCommsInboundMessageBase->setGatewayID($inGatewayID);
 * $oCommsInboundMessageBase->setGatewayRef($inGatewayRef);
 * $oCommsInboundMessageBase->setSender($inSender);
 * $oCommsInboundMessageBase->setCustomerID($inCustomerID);
 * $oCommsInboundMessageBase->setStatusID($inStatusID);
 * $oCommsInboundMessageBase->setCreateDate($inCreateDate);
 * $oCommsInboundMessageBase->setUpdateDate($inUpdateDate);
 * $oCommsInboundMessageBase->setReceivedDate($inReceivedDate);
 * $oCommsInboundMessageBase->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsInboundMessageBase = new commsInboundMessageBase($inMessageID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsInboundMessageBase = new commsInboundMessageBase();
 * $oCommsInboundMessageBase->setMessageID($inMessageID);
 * $oCommsInboundMessageBase->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsInboundMessageBase = commsInboundMessageBase::getInstance($inMessageID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage inbound
 * @category commsInboundMessageBase
 */
class commsInboundMessageBase implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsInboundMessageBase
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
	 * Stores $_MessageID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MessageID;

	/**
	 * Stores $_InboundTypeID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_InboundTypeID;

	/**
	 * Stores $_GatewayID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GatewayID;

	/**
	 * Stores $_GatewayRef
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_GatewayRef;

	/**
	 * Stores $_Sender
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Sender;

	/**
	 * Stores $_CustomerID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_CustomerID;

	/**
	 * Stores $_StatusID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_StatusID;

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
	 * Stores $_SentDate
	 *
	 * @var datetime
	 * @access protected
	 */
	protected $_SentDate;

	/**
	 * Stores $_ReceivedDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_ReceivedDate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;

	

	/**
	 * Returns a new instance of commsInboundMessageBase
	 *
	 * @param integer $inMessageID
	 * @return commsInboundMessageBase
	 */
	function __construct($inMessageID = null) {
		$this->reset();
		if ( $inMessageID !== null ) {
			$this->setMessageID($inMessageID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT messageID, inboundTypeID, gatewayID, gatewayRef, sender, customerID, statusID, createDate, updateDate, sentDate, receivedDate
			  FROM '.system::getConfig()->getDatabase('comms').'.inboundMessages';

		$where = array();
		if ( $this->_MessageID !== 0 ) {
			$where[] = ' messageID = :MessageID ';
		}
		if ( $this->_GatewayID !== 0 ) {
			$where[] = ' gatewayID = :GatewayID ';
		}
		if ( $this->_GatewayRef !== '' ) {
			$where[] = ' gatewayRef = :GatewayRef ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_MessageID !== 0 ) {
				$oStmt->bindValue(':MessageID', $this->_MessageID);
			}
			if ( $this->_GatewayID !== 0 ) {
				$oStmt->bindValue(':GatewayID', $this->_GatewayID);
			}
			if ( $this->_GatewayRef !== '' ) {
				$oStmt->bindValue(':GatewayRef', $this->_GatewayRef);
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
		$this->setMessageID((int)$inArray['messageID']);
		$this->setInboundTypeID((int)$inArray['inboundTypeID']);
		$this->setGatewayID((int)$inArray['gatewayID']);
		$this->setGatewayRef($inArray['gatewayRef']);
		$this->setSender($inArray['sender']);
		$this->setCustomerID((int)$inArray['customerID']);
		$this->setStatusID((int)$inArray['statusID']);
		$this->setCreateDate($inArray['createDate']);
		$this->setUpdateDate($inArray['updateDate']);
		$this->setSentDate($inArray['sentDate']);
		$this->setReceivedDate($inArray['receivedDate']);
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
			$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.inboundMessages
					( messageID, inboundTypeID, gatewayID, gatewayRef, sender, customerID, statusID, createDate, updateDate, sentDate, receivedDate)
				VALUES
					(:MessageID, :InboundTypeID, :GatewayID, :GatewayRef, :Sender, :CustomerID, :StatusID, :CreateDate, :UpdateDate, :SentDate, :ReceivedDate)
				ON DUPLICATE KEY UPDATE
					inboundTypeID=VALUES(inboundTypeID),
					sender=VALUES(sender),
					customerID=VALUES(customerID),
					statusID=VALUES(statusID),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate),
					sentDate=VALUES(sentDate),
					receivedDate=VALUES(receivedDate)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MessageID', $this->_MessageID);
					$oStmt->bindValue(':InboundTypeID', $this->_InboundTypeID);
					$oStmt->bindValue(':GatewayID', $this->_GatewayID);
					$oStmt->bindValue(':GatewayRef', $this->_GatewayRef);
					$oStmt->bindValue(':Sender', $this->_Sender);
					$oStmt->bindValue(':CustomerID', $this->_CustomerID);
					$oStmt->bindValue(':StatusID', $this->_StatusID);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':UpdateDate', $this->_UpdateDate);
					$oStmt->bindValue(':SentDate', $this->_SentDate);
					$oStmt->bindValue(':ReceivedDate', $this->_ReceivedDate);

					if ( $oStmt->execute() ) {
						if ( !$this->getMessageID() ) {
							$this->setMessageID($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.inboundMessages
			WHERE
				messageID = :MessageID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MessageID', $this->_MessageID);

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
	 * @return commsInboundMessageBase
	 */
	function reset() {
		$this->_MessageID = 0;
		$this->_InboundTypeID = 0;
		$this->_GatewayID = 0;
		$this->_GatewayRef = '';
		$this->_Sender = '';
		$this->_CustomerID = 0;
		$this->_StatusID = 0;
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_UpdateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_SentDate = '0000-00-00 00:00:00';
		$this->_ReceivedDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
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
		$string .= " MessageID[$this->_MessageID] $newLine";
		$string .= " InboundTypeID[$this->_InboundTypeID] $newLine";
		$string .= " GatewayID[$this->_GatewayID] $newLine";
		$string .= " GatewayRef[$this->_GatewayRef] $newLine";
		$string .= " Sender[$this->_Sender] $newLine";
		$string .= " CustomerID[$this->_CustomerID] $newLine";
		$string .= " StatusID[$this->_StatusID] $newLine";
		$string .= " CreateDate[$this->_CreateDate] $newLine";
		$string .= " UpdateDate[$this->_UpdateDate] $newLine";
		$string .= " SentDate[$this->_SentDate] $newLine";
		$string .= " ReceivedDate[$this->_ReceivedDate] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsInboundMessageBase';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"MessageID\" value=\"$this->_MessageID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"InboundTypeID\" value=\"$this->_InboundTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"GatewayID\" value=\"$this->_GatewayID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"GatewayRef\" value=\"$this->_GatewayRef\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Sender\" value=\"$this->_Sender\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"CustomerID\" value=\"$this->_CustomerID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"StatusID\" value=\"$this->_StatusID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CreateDate\" value=\"$this->_CreateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"UpdateDate\" value=\"$this->_UpdateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"SentDate\" value=\"$this->_SentDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"ReceivedDate\" value=\"$this->_ReceivedDate\" type=\"datetime\" /> $newLine";
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
			$valid = $this->checkMessageID($message);
		}
		if ( $valid ) {
			$valid = $this->checkInboundTypeID($message);
		}
		if ( $valid ) {
			$valid = $this->checkGatewayID($message);
		}
		if ( $valid ) {
			$valid = $this->checkGatewayRef($message);
		}
		if ( $valid ) {
			$valid = $this->checkSender($message);
		}
		if ( $valid ) {
			$valid = $this->checkCustomerID($message);
		}
		if ( $valid ) {
			$valid = $this->checkStatusID($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkReceivedDate($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_MessageID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMessageID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MessageID) && $this->_MessageID !== 0 ) {
			$inMessage .= "{$this->_MessageID} is not a valid value for MessageID";
			$isValid = false;
		}
		return $isValid;
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
	 * Checks that $_GatewayID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkGatewayID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_GatewayID) && $this->_GatewayID !== 0 ) {
			$inMessage .= "{$this->_GatewayID} is not a valid value for GatewayID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_GatewayRef has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkGatewayRef(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_GatewayRef) && $this->_GatewayRef !== '' ) {
			$inMessage .= "{$this->_GatewayRef} is not a valid value for GatewayRef";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_GatewayRef) > 100 ) {
			$inMessage .= "GatewayRef cannot be more than 100 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_GatewayRef) <= 1 ) {
			$inMessage .= "GatewayRef must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Sender has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkSender(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Sender) && $this->_Sender !== '' ) {
			$inMessage .= "{$this->_Sender} is not a valid value for Sender";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Sender) > 255 ) {
			$inMessage .= "Sender cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Sender) <= 1 ) {
			$inMessage .= "Sender must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_CustomerID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCustomerID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_CustomerID) && $this->_CustomerID !== 0 ) {
			$inMessage .= "{$this->_CustomerID} is not a valid value for CustomerID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_StatusID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkStatusID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_StatusID) && $this->_StatusID !== 0 ) {
			$inMessage .= "{$this->_StatusID} is not a valid value for StatusID";
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
	 * Checks that $_ReceivedDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkReceivedDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ReceivedDate) && $this->_ReceivedDate !== '' ) {
			$inMessage .= "{$this->_ReceivedDate} is not a valid value for ReceivedDate";
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
	 * @return commsInboundMessageBase
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
		return $this->_MessageID;
	}

	/**
	 * Return value of $_MessageID
	 *
	 * @return integer
	 * @access public
	 */
	function getMessageID() {
		return $this->_MessageID;
	}

	/**
	 * Set $_MessageID to MessageID
	 *
	 * @param integer $inMessageID
	 * @return commsInboundMessageBase
	 * @access public
	 */
	function setMessageID($inMessageID) {
		if ( $inMessageID !== $this->_MessageID ) {
			$this->_MessageID = $inMessageID;
			$this->setModified();
		}
		return $this;
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
	 * @return commsInboundMessageBase
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
	 * Return value of $_GatewayID
	 *
	 * @return integer
	 * @access public
	 */
	function getGatewayID() {
		return $this->_GatewayID;
	}

	/**
	 * Set $_GatewayID to GatewayID
	 *
	 * @param integer $inGatewayID
	 * @return commsInboundMessageBase
	 * @access public
	 */
	function setGatewayID($inGatewayID) {
		if ( $inGatewayID !== $this->_GatewayID ) {
			$this->_GatewayID = $inGatewayID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_GatewayRef
	 *
	 * @return string
	 * @access public
	 */
	function getGatewayRef() {
		return $this->_GatewayRef;
	}

	/**
	 * Set $_GatewayRef to GatewayRef
	 *
	 * @param string $inGatewayRef
	 * @return commsInboundMessageBase
	 * @access public
	 */
	function setGatewayRef($inGatewayRef) {
		if ( $inGatewayRef !== $this->_GatewayRef ) {
			$this->_GatewayRef = $inGatewayRef;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Sender
	 *
	 * @return string
	 * @access public
	 */
	function getSender() {
		return $this->_Sender;
	}

	/**
	 * Set $_Sender to Sender
	 *
	 * @param string $inSender
	 * @return commsInboundMessageBase
	 * @access public
	 */
	function setSender($inSender) {
		if ( $inSender !== $this->_Sender ) {
			$this->_Sender = $inSender;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_CustomerID
	 *
	 * @return integer
	 * @access public
	 */
	function getCustomerID() {
		return $this->_CustomerID;
	}

	/**
	 * Set $_CustomerID to CustomerID
	 *
	 * @param integer $inCustomerID
	 * @return commsInboundMessageBase
	 * @access public
	 */
	function setCustomerID($inCustomerID) {
		if ( $inCustomerID !== $this->_CustomerID ) {
			$this->_CustomerID = $inCustomerID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_StatusID
	 *
	 * @return integer
	 * @access public
	 */
	function getStatusID() {
		return $this->_StatusID;
	}

	/**
	 * Set $_StatusID to StatusID
	 *
	 * @param integer $inStatusID
	 * @return commsInboundMessageBase
	 * @access public
	 */
	function setStatusID($inStatusID) {
		if ( $inStatusID !== $this->_StatusID ) {
			$this->_StatusID = $inStatusID;
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
	 * @return commsInboundMessageBase
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
	 * @return commsInboundMessageBase
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
	 * Returns $_SentDate
	 *
	 * @return datetime
	 */
	function getSentDate() {
		return $this->_SentDate;
	}
	
	/**
	 * Set $_SentDate to $inSentDate
	 *
	 * @param datetime $inSentDate
	 * @return commsInboundMessageBase
	 */
	function setSentDate($inSentDate) {
		if ( $inSentDate !== $this->_SentDate ) {
			$this->_SentDate = $inSentDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_ReceivedDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getReceivedDate() {
		return $this->_ReceivedDate;
	}

	/**
	 * Set $_ReceivedDate to ReceivedDate
	 *
	 * @param datetime $inReceivedDate
	 * @return commsInboundMessageBase
	 * @access public
	 */
	function setReceivedDate($inReceivedDate) {
		if ( $inReceivedDate !== $this->_ReceivedDate ) {
			$this->_ReceivedDate = $inReceivedDate;
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
	 * @return commsInboundMessageBase
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}