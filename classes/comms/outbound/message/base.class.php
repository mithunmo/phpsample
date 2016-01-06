<?php
/**
 * commsOutboundMessageBase
 *
 * Stored in commsOutboundMessageBase.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageBase
 * @version $Rev: 10 $
 */


/**
 * commsOutboundMessageBase Class
 *
 * Provides access to records in comms.outboundMessages
 *
 * Creating a new record:
 * <code>
 * $oCommsOutboundMessageBase = new commsOutboundMessageBase();
 * $oCommsOutboundMessageBase->setMessageID($inMessageID);
 * $oCommsOutboundMessageBase->setOutboundTypeID($inOutboundTypeID);
 * $oCommsOutboundMessageBase->setGatewayID($inGatewayID);
 * $oCommsOutboundMessageBase->setGatewayAccountID($inGatewayAccountID);
 * $oCommsOutboundMessageBase->setRecipient($inRecipient);
 * $oCommsOutboundMessageBase->setOriginator($inOriginator);
 * $oCommsOutboundMessageBase->setCharge($inCharge);
 * $oCommsOutboundMessageBase->setCurrencyID($inCurrencyID);
 * $oCommsOutboundMessageBase->setCreateDate($inCreateDate);
 * $oCommsOutboundMessageBase->setScheduledDate($inScheduledDate);
 * $oCommsOutboundMessageBase->setSentDate($inSentDate);
 * $oCommsOutboundMessageBase->setAcknowledgedDate($inAcknowledgedDate);
 * $oCommsOutboundMessageBase->setStatusID($inStatusID);
 * $oCommsOutboundMessageBase->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsOutboundMessageBase = new commsOutboundMessageBase($inMessageID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsOutboundMessageBase = new commsOutboundMessageBase();
 * $oCommsOutboundMessageBase->setMessageID($inMessageID);
 * $oCommsOutboundMessageBase->load();
 * </code>
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageBase
 */
class commsOutboundMessageBase implements systemDaoInterface, systemDaoValidatorInterface {
	
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
	 * Stores $_OutboundTypeID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_OutboundTypeID;

	/**
	 * Stores $_GatewayID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GatewayID;

	/**
	 * Stores $_GatewayAccountID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GatewayAccountID;
	
	/**
	 * Stores $_CustomerID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_CustomerID;
	
	/**
	 * Stores $_Recipient
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Recipient;

	/**
	 * Stores $_Originator
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Originator;

	/**
	 * Stores $_Charge
	 *
	 * @var float 
	 * @access protected
	 */
	protected $_Charge;

	/**
	 * Stores $_CurrencyID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_CurrencyID;

	/**
	 * Stores $_CreateDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_CreateDate;

	/**
	 * Stores $_ScheduledDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_ScheduledDate;

	/**
	 * Stores $_SentDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_SentDate;

	/**
	 * Stores $_AcknowledgedDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_AcknowledgedDate;

	/**
	 * Stores $_StatusID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_StatusID;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;

	

	/**
	 * Returns a new instance of commsOutboundMessageBase
	 *
	 * @param integer $inMessageID
	 * @return commsOutboundMessageBase
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
			SELECT messageID, outboundTypeID, gatewayID, gatewayAccountID, recipient, originator, charge, currencyID, createDate, scheduledDate, sentDate, acknowledgedDate, statusID
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessages';

		$where = array();
		if ( $this->_MessageID !== 0 ) {
			$where[] = ' messageID = :MessageID ';
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
		$this->setOutboundTypeID((int)$inArray['outboundTypeID']);
		$this->setGatewayID((int)$inArray['gatewayID']);
		$this->setGatewayAccountID((int)$inArray['gatewayAccountID']);
		$this->setCustomerID((int)$inArray['customerID']);
		$this->setRecipient($inArray['recipient']);
		$this->setOriginator($inArray['originator']);
		$this->setCharge($inArray['charge']);
		$this->setCurrencyID((int)$inArray['currencyID']);
		$this->setCreateDate($inArray['createDate']);
		$this->setScheduledDate($inArray['scheduledDate']);
		$this->setSentDate($inArray['sentDate']);
		$this->setAcknowledgedDate($inArray['acknowledgedDate']);
		$this->setStatusID((int)$inArray['statusID']);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.outboundMessages
					( messageID, outboundTypeID, gatewayID, gatewayAccountID, customerID, recipient, originator, charge, currencyID, createDate, scheduledDate, sentDate, acknowledgedDate, statusID)
				VALUES
					(:MessageID, :OutboundTypeID, :GatewayID, :GatewayAccountID, :CustomerID, :Recipient, :Originator, :Charge, :CurrencyID, :CreateDate, :ScheduledDate, :SentDate, :AcknowledgedDate, :StatusID)
				ON DUPLICATE KEY UPDATE
					outboundTypeID=VALUES(outboundTypeID),
					gatewayID=VALUES(gatewayID),
					gatewayAccountID=VALUES(gatewayAccountID),
					customerID=VALUES(customerID),
					recipient=VALUES(recipient),
					originator=VALUES(originator),
					charge=VALUES(charge),
					currencyID=VALUES(currencyID),
					createDate=VALUES(createDate),
					scheduledDate=VALUES(scheduledDate),
					sentDate=VALUES(sentDate),
					acknowledgedDate=VALUES(acknowledgedDate),
					statusID=VALUES(statusID)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MessageID', $this->_MessageID);
					$oStmt->bindValue(':OutboundTypeID', $this->getOutboundTypeID());
					$oStmt->bindValue(':GatewayID', $this->getGatewayID());
					$oStmt->bindValue(':GatewayAccountID', $this->getGatewayAccountID());
					$oStmt->bindValue(':CustomerID', $this->getCustomerID());
					$oStmt->bindValue(':Recipient', $this->getRecipient());
					$oStmt->bindValue(':Originator', $this->getOriginator());
					$oStmt->bindValue(':Charge', $this->getCharge());
					$oStmt->bindValue(':CurrencyID', $this->getCurrencyID());
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':ScheduledDate', $this->_ScheduledDate);
					$oStmt->bindValue(':SentDate', $this->_SentDate);
					$oStmt->bindValue(':AcknowledgedDate', $this->_AcknowledgedDate);
					$oStmt->bindValue(':StatusID', $this->_StatusID);

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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.outboundMessages
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
	 * @return commsOutboundMessageBase
	 */
	function reset() {
		$this->_MessageID = 0;
		$this->_OutboundTypeID = 0;
		$this->_GatewayID = 0;
		$this->_GatewayAccountID = 0;
		$this->_CustomerID = 0;
		$this->_Recipient = '';
		$this->_Originator = null;
		$this->_Charge = null;
		$this->_CurrencyID = null;
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat());
		$this->_ScheduledDate = date(system::getConfig()->getDatabaseDatetimeFormat());
		$this->_SentDate = null;
		$this->_AcknowledgedDate = null;
		$this->_StatusID = 1;
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
		$string .= " OutboundTypeID[$this->_OutboundTypeID] $newLine";
		$string .= " GatewayID[$this->_GatewayID] $newLine";
		$string .= " GatewayAccountID[$this->_GatewayAccountID] $newLine";
		$string .= " CustomerID[$this->_CustomerID] $newLine";
		$string .= " Recipient[$this->_Recipient] $newLine";
		$string .= " Originator[$this->_Originator] $newLine";
		$string .= " Charge[$this->_Charge] $newLine";
		$string .= " CurrencyID[$this->_CurrencyID] $newLine";
		$string .= " CreateDate[$this->_CreateDate] $newLine";
		$string .= " ScheduledDate[$this->_ScheduledDate] $newLine";
		$string .= " SentDate[$this->_SentDate] $newLine";
		$string .= " AcknowledgedDate[$this->_AcknowledgedDate] $newLine";
		$string .= " StatusID[$this->_StatusID] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsOutboundMessageBase';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"MessageID\" value=\"$this->_MessageID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"OutboundTypeID\" value=\"$this->_OutboundTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"GatewayID\" value=\"$this->_GatewayID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"GatewayAccountID\" value=\"$this->_GatewayAccountID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CustomerID\" value=\"$this->_CustomerID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Recipient\" value=\"$this->_Recipient\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Originator\" value=\"$this->_Originator\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Charge\" value=\"$this->_Charge\" type=\"float\" /> $newLine";
		$xml .= "\t<property name=\"CurrencyID\" value=\"$this->_CurrencyID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CreateDate\" value=\"$this->_CreateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"ScheduledDate\" value=\"$this->_ScheduledDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"SentDate\" value=\"$this->_SentDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"AcknowledgedDate\" value=\"$this->_AcknowledgedDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"StatusID\" value=\"$this->_StatusID\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkOutboundTypeID($message);
		}
		if ( $valid ) {
			$valid = $this->checkGatewayID($message);
		}
		if ( $valid ) {
			$valid = $this->checkGatewayAccountID($message);
		}
		if ( $valid ) {
			$valid = $this->checkRecipient($message);
		}
		if ( $valid ) {
			$valid = $this->checkCharge($message);
		}
		if ( $valid ) {
			$valid = $this->checkCurrencyID($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkScheduledDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkStatusID($message);
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
	 * Checks that $_GatewayAccountID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkGatewayAccountID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_GatewayAccountID) && $this->_GatewayAccountID !== 0 ) {
			$inMessage .= "{$this->_GatewayAccountID} is not a valid value for GatewayAccountID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Recipient has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkRecipient(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Recipient) && $this->_Recipient !== '' ) {
			$inMessage .= "{$this->_Recipient} is not a valid value for Recipient";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Recipient) > 255 ) {
			$inMessage .= "Recipient cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Recipient) <= 1 ) {
			$inMessage .= "Recipient must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Charge has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCharge(&$inMessage = '') {
		$isValid = true;
		if ( !is_float($this->_Charge) && $this->_Charge != null && $this->_Charge != 0.000 ) {
			$inMessage .= "{$this->_Charge} is not a valid value for Charge";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_CurrencyID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCurrencyID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_CurrencyID) && $this->_CurrencyID !== null && $this->_CurrencyID !== 0 ) {
			$inMessage .= "{$this->_CurrencyID} is not a valid value for CurrencyID";
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
	 * @return commsOutboundMessageBase
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
	 * @return commsOutboundMessageBase
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
	 * Return value of $_OutboundTypeID
	 *
	 * @return integer
	 * @access public
	 */
	function getOutboundTypeID() {
		return $this->_OutboundTypeID;
	}
	
	/**
	 * Returns the outbound type object
	 * 
	 * @return commsOutboundType
	 */
	function getOutboundType() {
		return commsOutboundType::getInstance($this->getOutboundTypeID());
	}

	/**
	 * Set $_OutboundTypeID to OutboundTypeID
	 *
	 * @param integer $inOutboundTypeID
	 * @return commsOutboundMessageBase
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
	 * @return commsOutboundMessageBase
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
	 * Return value of $_GatewayAccountID
	 *
	 * @return integer
	 * @access public
	 */
	function getGatewayAccountID() {
		return $this->_GatewayAccountID;
	}

	/**
	 * Set $_GatewayAccountID to GatewayAccountID
	 *
	 * @param integer $inGatewayAccountID
	 * @return commsOutboundMessageBase
	 * @access public
	 */
	function setGatewayAccountID($inGatewayAccountID) {
		if ( $inGatewayAccountID !== $this->_GatewayAccountID ) {
			$this->_GatewayAccountID = $inGatewayAccountID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_CustomerID
	 *
	 * @return integer
	 */
	function getCustomerID() {
		return $this->_CustomerID;
	}
	
	/**
	 * Set $_CustomerID to $inCustomerID
	 *
	 * @param integer $inCustomerID
	 * @return commsOutboundMessageBase
	 */
	function setCustomerID($inCustomerID) {
		if ( $inCustomerID !== $this->_CustomerID ) {
			$this->_CustomerID = $inCustomerID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Recipient
	 *
	 * @return string
	 * @access public
	 */
	function getRecipient() {
		return $this->_Recipient;
	}

	/**
	 * Set $_Recipient to Recipient
	 *
	 * @param string $inRecipient
	 * @return commsOutboundMessageBase
	 * @access public
	 */
	function setRecipient($inRecipient) {
		if ( $inRecipient !== $this->_Recipient ) {
			$this->_Recipient = $inRecipient;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Originator
	 *
	 * @return string
	 * @access public
	 */
	function getOriginator() {
		return $this->_Originator;
	}

	/**
	 * Set $_Originator to Originator
	 *
	 * @param string $inOriginator
	 * @return commsOutboundMessageBase
	 * @access public
	 */
	function setOriginator($inOriginator) {
		if ( $inOriginator !== $this->_Originator ) {
			$this->_Originator = $inOriginator;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Charge
	 *
	 * @return float
	 * @access public
	 */
	function getCharge() {
		return $this->_Charge;
	}

	/**
	 * Set $_Charge to Charge
	 *
	 * @param float $inCharge
	 * @return commsOutboundMessageBase
	 * @access public
	 */
	function setCharge($inCharge) {
		if ( $inCharge !== $this->_Charge ) {
			$this->_Charge = $inCharge;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_CurrencyID
	 *
	 * @return integer
	 * @access public
	 */
	function getCurrencyID() {
		return $this->_CurrencyID;
	}

	/**
	 * Set $_CurrencyID to CurrencyID
	 *
	 * @param integer $inCurrencyID
	 * @return commsOutboundMessageBase
	 * @access public
	 */
	function setCurrencyID($inCurrencyID) {
		if ( $inCurrencyID !== $this->_CurrencyID ) {
			$this->_CurrencyID = $inCurrencyID;
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
	 * @return commsOutboundMessageBase
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
	 * @return commsOutboundMessageBase
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
	 * Return value of $_SentDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getSentDate() {
		return $this->_SentDate;
	}

	/**
	 * Set $_SentDate to SentDate
	 *
	 * @param datetime $inSentDate
	 * @return commsOutboundMessageBase
	 * @access public
	 */
	function setSentDate($inSentDate) {
		if ( $inSentDate !== $this->_SentDate ) {
			$this->_SentDate = $inSentDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_AcknowledgedDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getAcknowledgedDate() {
		return $this->_AcknowledgedDate;
	}

	/**
	 * Set $_AcknowledgedDate to AcknowledgedDate
	 *
	 * @param datetime $inAcknowledgedDate
	 * @return commsOutboundMessageBase
	 * @access public
	 */
	function setAcknowledgedDate($inAcknowledgedDate) {
		if ( $inAcknowledgedDate !== $this->_AcknowledgedDate ) {
			$this->_AcknowledgedDate = $inAcknowledgedDate;
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
	 * Returns the status object
	 * 
	 * @return commsOutboundStatus
	 */
	function getStatus() {
		return commsOutboundStatus::getInstance($this->getStatusID());
	}

	/**
	 * Set $_StatusID to StatusID
	 *
	 * @param integer $inStatusID
	 * @return commsOutboundMessageBase
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
	 * @return commsOutboundMessageBase
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}