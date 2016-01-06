<?php
/**
 * commsOutboundLog
 *
 * Stored in commsOutboundLog.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundLog
 * @version $Rev: 10 $
 */


/**
 * commsOutboundLog Class
 *
 * Provides access to records in logging.outboundLog
 *
 * Creating a new record:
 * <code>
 * $oCommsOutboundLog = new commsOutboundLog();
 * $oCommsOutboundLog->setDate($inDate);
 * $oCommsOutboundLog->setOutboundTypeID($inOutboundTypeID);
 * $oCommsOutboundLog->setGatewayID($inGatewayID);
 * $oCommsOutboundLog->setGatewayAccountID($inGatewayAccountID);
 * $oCommsOutboundLog->setNetworkID($inNetworkID);
 * $oCommsOutboundLog->setSent($inSent);
 * $oCommsOutboundLog->setAcknowledged($inAcknowledged);
 * $oCommsOutboundLog->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsOutboundLog = new commsOutboundLog($inDate, $inOutboundTypeID, $inGatewayID, $inGatewayAccountID, $inNetworkID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsOutboundLog = new commsOutboundLog();
 * $oCommsOutboundLog->setDate($inDate);
 * $oCommsOutboundLog->setOutboundTypeID($inOutboundTypeID);
 * $oCommsOutboundLog->setGatewayID($inGatewayID);
 * $oCommsOutboundLog->setGatewayAccountID($inGatewayAccountID);
 * $oCommsOutboundLog->setNetworkID($inNetworkID);
 * $oCommsOutboundLog->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsOutboundLog = commsOutboundLog::getInstance($inDate, $inOutboundTypeID, $inGatewayID, $inGatewayAccountID, $inNetworkID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundLog
 */
class commsOutboundLog implements systemDaoInterface {

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;

	/**
	 * Stores $_Date
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Date;

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
	 * Stores $_NetworkID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_NetworkID;

	/**
	 * Stores $_Sent
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Sent;

	/**
	 * Stores $_Acknowledged
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Acknowledged;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of commsOutboundLog
	 *
	 * @param string $inDate
	 * @param integer $inOutboundTypeID
	 * @param integer $inGatewayID
	 * @param integer $inGatewayAccountID
	 * @param integer $inNetworkID
	 * @return commsOutboundLog
	 */
	function __construct($inOutboundTypeID = null, $inGatewayID = null, $inGatewayAccountID = null, $inNetworkID = null) {
		$this->reset();
		if ( $inOutboundTypeID !== null && $inGatewayID !== null && $inGatewayAccountID !== null && $inNetworkID !== null ) {
			$this->setDate(date(system::getConfig()->getDatabaseDateFormat()));
			$this->setOutboundTypeID($inOutboundTypeID);
			$this->setGatewayID($inGatewayID);
			$this->setGatewayAccountID($inGatewayAccountID);
			$this->setNetworkID($inNetworkID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsOutboundLog containing non-unique properties
	 *
	 * @param integer $inOutboundTypeID
	 * @param integer $inGatewayID
	 * @param integer $inGatewayAccountID
	 * @param integer $inNetworkID
	 * @param integer $inSent
	 * @param integer $inAcknowledged
	 * @return commsOutboundLog
	 * @static
	 */
	public static function factory($inOutboundTypeID = null, $inGatewayID = null, $inGatewayAccountID = null, $inNetworkID = null, $inSent = null, $inAcknowledged = null) {
		$oObject = new commsOutboundLog;
		$oObject->setDate(date(system::getConfig()->getDatabaseDateFormat()));
		if ( $inOutboundTypeID !== null ) {
			$oObject->setOutboundTypeID($inOutboundTypeID);
		}
		if ( $inGatewayID !== null ) {
			$oObject->setGatewayID($inGatewayID);
		}
		if ( $inGatewayAccountID !== null ) {
			$oObject->setGatewayAccountID($inGatewayAccountID);
		}
		if ( $inSent !== null ) {
			$oObject->setSent($inSent);
		}
		if ( $inAcknowledged !== null ) {
			$oObject->setAcknowledged($inAcknowledged);
		}
		return $oObject;
	}
	
	/**
	 * Creates a new commsOutboundLog from the supplied message object
	 *
	 * @param commsOutboundMessage $inMessage
	 * @return commsOutboundLog
	 * @static
	 */
	public static function factoryFromOutboundMessage($inMessage) {
		$oObject = new commsOutboundLog;
		$oObject->setDate(date(system::getConfig()->getDatabaseDateFormat()));
		$oObject->setOutboundTypeID($inMessage->getOutboundTypeID());
		$oObject->setGatewayID($inMessage->getGatewayID());
		$oObject->setGatewayAccountID($inMessage->getGatewayAccountID());
		return $oObject;
	}

	/**
	 * Get an instance of commsOutboundLog by primary key
	 *
	 * @param integer $inOutboundTypeID
	 * @param integer $inGatewayID
	 * @param integer $inGatewayAccountID
	 * @param integer $inNetworkID
	 * @return commsOutboundLog
	 * @static
	 */
	public static function getInstance($inOutboundTypeID, $inGatewayID, $inGatewayAccountID, $inNetworkID) {
		$oObject = new commsOutboundLog();
		$oObject->setDate(date(system::getConfig()->getDatabaseDateFormat()));
		$oObject->setOutboundTypeID($inOutboundTypeID);
		$oObject->setGatewayID($inGatewayID);
		$oObject->setGatewayAccountID($inGatewayAccountID);
		$oObject->setNetworkID($inNetworkID);
		$oObject->load();
		return $oObject;
	}

	/**
	 * Get instance of commsOutboundLog by unique key (date)
	 *
	 * @param string $inDate
	 * @param integer $inOutboundTypeID
	 * @param integer $inGatewayID
	 * @param integer $inGatewayAccountID
	 * @param integer $inNetworkID
	 * @return commsOutboundLog
	 * @static
	 */
	public static function getInstanceByDate($inDate, $inOutboundTypeID, $inGatewayID, $inGatewayAccountID, $inNetworkID) {
		$oObject = new commsOutboundLog();
		$oObject->setDate($inDate);
		$oObject->setOutboundTypeID($inOutboundTypeID);
		$oObject->setGatewayID($inGatewayID);
		$oObject->setGatewayAccountID($inGatewayAccountID);
		$oObject->setNetworkID($inNetworkID);
		$oObject->load();
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsOutboundLog
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('logging').'.outboundLog';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsOutboundLog();
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
			SELECT date, outboundTypeID, gatewayID, gatewayAccountID, networkID, sent, acknowledged
			  FROM '.system::getConfig()->getDatabase('logging').'.outboundLog';

		$where = array();
		if ( $this->_Date !== '' ) {
			$where[] = ' date = :Date ';
		}
		if ( $this->_OutboundTypeID !== 0 ) {
			$where[] = ' outboundTypeID = :OutboundTypeID ';
		}
		if ( $this->_GatewayID !== 0 ) {
			$where[] = ' gatewayID = :GatewayID ';
		}
		if ( $this->_GatewayAccountID !== 0 ) {
			$where[] = ' gatewayAccountID = :GatewayAccountID ';
		}
		if ( $this->_NetworkID !== 0 ) {
			$where[] = ' networkID = :NetworkID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_Date !== '' ) {
				$oStmt->bindValue(':Date', $this->_Date);
			}
			if ( $this->_OutboundTypeID !== 0 ) {
				$oStmt->bindValue(':OutboundTypeID', $this->_OutboundTypeID);
			}
			if ( $this->_GatewayID !== 0 ) {
				$oStmt->bindValue(':GatewayID', $this->_GatewayID);
			}
			if ( $this->_GatewayAccountID !== 0 ) {
				$oStmt->bindValue(':GatewayAccountID', $this->_GatewayAccountID);
			}
			if ( $this->_NetworkID !== 0 ) {
				$oStmt->bindValue(':NetworkID', $this->_NetworkID);
			}
			
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
		$this->setDate($inArray['date']);
		$this->setOutboundTypeID((int)$inArray['outboundTypeID']);
		$this->setGatewayID((int)$inArray['gatewayID']);
		$this->setGatewayAccountID((int)$inArray['gatewayAccountID']);
		$this->setNetworkID((int)$inArray['networkID']);
		$this->setSent((int)$inArray['sent']);
		$this->setAcknowledged((int)$inArray['acknowledged']);
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
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('logging').'.outboundLog
					( date, outboundTypeID, gatewayID, gatewayAccountID, networkID, sent, acknowledged)
				VALUES
					(:Date, :OutboundTypeID, :GatewayID, :GatewayAccountID, :NetworkID, :Sent, :Acknowledged)
				ON DUPLICATE KEY UPDATE
					sent=VALUES(sent),
					acknowledged=VALUES(acknowledged)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':Date', $this->_Date);
					$oStmt->bindValue(':OutboundTypeID', $this->_OutboundTypeID);
					$oStmt->bindValue(':GatewayID', $this->_GatewayID);
					$oStmt->bindValue(':GatewayAccountID', $this->_GatewayAccountID);
					$oStmt->bindValue(':NetworkID', $this->_NetworkID);
					$oStmt->bindValue(':Sent', $this->_Sent);
					$oStmt->bindValue(':Acknowledged', $this->_Acknowledged);

					if ( $oStmt->execute() ) {
						$this->setModified(false);
						$return = true;
					}
					$oStmt->closeCursor();
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
			DELETE FROM '.system::getConfig()->getDatabase('logging').'.outboundLog
			WHERE
				date = :Date AND
				outboundTypeID = :OutboundTypeID AND
				gatewayID = :GatewayID AND
				gatewayAccountID = :GatewayAccountID AND
				networkID = :NetworkID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':Date', $this->_Date);
			$oStmt->bindValue(':OutboundTypeID', $this->_OutboundTypeID);
			$oStmt->bindValue(':GatewayID', $this->_GatewayID);
			$oStmt->bindValue(':GatewayAccountID', $this->_GatewayAccountID);
			$oStmt->bindValue(':NetworkID', $this->_NetworkID);

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
	 * @return commsOutboundLog
	 */
	function reset() {
		$this->_Date = date(system::getConfig()->getDatabaseDateFormat()->getParamValue());
		$this->_OutboundTypeID = 0;
		$this->_GatewayID = 0;
		$this->_GatewayAccountID = 0;
		$this->_NetworkID = 0;
		$this->_Sent = 0;
		$this->_Acknowledged = 0;
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
		$string .= " Date[$this->_Date] $newLine";
		$string .= " OutboundTypeID[$this->_OutboundTypeID] $newLine";
		$string .= " GatewayID[$this->_GatewayID] $newLine";
		$string .= " GatewayAccountID[$this->_GatewayAccountID] $newLine";
		$string .= " NetworkID[$this->_NetworkID] $newLine";
		$string .= " Sent[$this->_Sent] $newLine";
		$string .= " Acknowledged[$this->_Acknowledged] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsOutboundLog';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"Date\" value=\"$this->_Date\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"OutboundTypeID\" value=\"$this->_OutboundTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"GatewayID\" value=\"$this->_GatewayID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"GatewayAccountID\" value=\"$this->_GatewayAccountID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"NetworkID\" value=\"$this->_NetworkID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Sent\" value=\"$this->_Sent\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Acknowledged\" value=\"$this->_Acknowledged\" type=\"integer\" /> $newLine";
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
	 * @return commsOutboundLog
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
		return $this->_Date.'.'.$this->_OutboundTypeID.'.'.$this->_GatewayID.'.'.$this->_GatewayAccountID.'.'.$this->_NetworkID;
	}

	/**
	 * Return value of $_Date
	 *
	 * @return string
	 * @access public
	 */
	function getDate() {
		return $this->_Date;
	}

	/**
	 * Set $_Date to Date
	 *
	 * @param string $inDate
	 * @return commsOutboundLog
	 * @access public
	 */
	function setDate($inDate) {
		if ( $inDate !== $this->_Date ) {
			$this->_Date = $inDate;
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
	 * Set $_OutboundTypeID to OutboundTypeID
	 *
	 * @param integer $inOutboundTypeID
	 * @return commsOutboundLog
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
	 * @return commsOutboundLog
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
	 * @return commsOutboundLog
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
	 * Return value of $_NetworkID
	 *
	 * @return integer
	 * @access public
	 */
	function getNetworkID() {
		return $this->_NetworkID;
	}

	/**
	 * Set $_NetworkID to NetworkID
	 *
	 * @param integer $inNetworkID
	 * @return commsOutboundLog
	 * @access public
	 */
	function setNetworkID($inNetworkID) {
		if ( $inNetworkID !== $this->_NetworkID ) {
			$this->_NetworkID = $inNetworkID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Sent
	 *
	 * @return integer
	 * @access public
	 */
	function getSent() {
		return $this->_Sent;
	}

	/**
	 * Set $_Sent to Sent
	 *
	 * @param integer $inSent
	 * @return commsOutboundLog
	 * @access public
	 */
	function setSent($inSent) {
		if ( $inSent !== $this->_Sent ) {
			$this->_Sent = $inSent;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Acknowledged
	 *
	 * @return integer
	 * @access public
	 */
	function getAcknowledged() {
		return $this->_Acknowledged;
	}

	/**
	 * Set $_Acknowledged to Acknowledged
	 *
	 * @param integer $inAcknowledged
	 * @return commsOutboundLog
	 * @access public
	 */
	function setAcknowledged($inAcknowledged) {
		if ( $inAcknowledged !== $this->_Acknowledged ) {
			$this->_Acknowledged = $inAcknowledged;
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
	 * @return commsOutboundLog
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	/**
	 * Increments sent count
	 * 
	 * @return commsOutboundLog
	 */
	function incrementSent() {
		$this->_Sent++;
		$this->setModified();
		return $this;
	}
	
	/**
	 * Increments ack'd count
	 * 
	 * @return commsOutboundLog
	 */
	function incrementAcknowledged() {
		$this->_Acknowledged++;
		$this->setModified();
		return $this;
	}
}