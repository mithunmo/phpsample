<?php
/**
 * commsInboundLog
 *
 * Stored in commsInboundLog.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage inbound
 * @category commsInboundLog
 * @version $Rev: 10 $
 */


/**
 * commsInboundLog Class
 *
 * Provides access to records in logging.inboundLog
 *
 * Creating a new record:
 * <code>
 * $oCommsInboundLog = new commsInboundLog();
 * $oCommsInboundLog->setDate($inDate);
 * $oCommsInboundLog->setInboundTypeID($inInboundTypeID);
 * $oCommsInboundLog->setGatewayID($inGatewayID);
 * $oCommsInboundLog->setPrs($inPrs);
 * $oCommsInboundLog->setNetworkID($inNetworkID);
 * $oCommsInboundLog->setReceived($inReceived);
 * $oCommsInboundLog->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsInboundLog = new commsInboundLog($inDate, $inInboundTypeID, $inGatewayID, $inNetworkID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsInboundLog = new commsInboundLog();
 * $oCommsInboundLog->setDate($inDate);
 * $oCommsInboundLog->setInboundTypeID($inInboundTypeID);
 * $oCommsInboundLog->setGatewayID($inGatewayID);
 * $oCommsInboundLog->setNetworkID($inNetworkID);
 * $oCommsInboundLog->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsInboundLog = commsInboundLog::getInstance($inDate, $inInboundTypeID, $inGatewayID, $inNetworkID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage inbound
 * @category commsInboundLog
 */
class commsInboundLog implements systemDaoInterface {

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
	 * Stores $_Prs
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Prs;

	/**
	 * Stores $_NetworkID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_NetworkID;

	/**
	 * Stores $_Received
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Received;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;

	

	/**
	 * Returns a new instance of commsInboundLog
	 *
	 * @param integer $inInboundTypeID
	 * @param integer $inGatewayID
	 * @param integer $inNetworkID
	 * @return commsInboundLog
	 */
	function __construct($inInboundTypeID = null, $inGatewayID = null, $inNetworkID = null) {
		$this->reset();
		if ( $inInboundTypeID !== null && $inGatewayID !== null && $inNetworkID !== null ) {
			$this->setDate(date(system::getConfig()->getDatabaseDateFormat()));
			$this->setInboundTypeID($inInboundTypeID);
			$this->setGatewayID($inGatewayID);
			$this->setNetworkID($inNetworkID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsInboundLog
	 *
	 * @param integer $inInboundTypeID
	 * @param integer $inGatewayID
	 * @param integer $inNetworkID
	 * @param string $inPrs
	 * @param integer $inReceived
	 * @return commsInboundLog
	 * @static
	 */
	public static function factory($inInboundTypeID = null, $inGatewayID = null, $inNetworkID = null, $inPrs = null, $inReceived = null) {
		$oObject = new commsInboundLog;
		$oObject->setDate(date(system::getConfig()->getDatabaseDateFormat()));
		if ( $inInboundTypeID !== null ) {
			$oObject->setInboundTypeID($inInboundTypeID);
		}
		if ( $inGatewayID !== null ) {
			$oObject->setGatewayID($inGatewayID);
		}
		if ( $inNetworkID !== null ) {
			$oObject->setNetworkID($inNetworkID);
		}
		if ( $inPrs !== null ) {
			$oObject->setPrs($inPrs);
		}
		if ( $inReceived !== null ) {
			$oObject->setReceived($inReceived);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsInboundLog by primary key
	 *
	 * @param integer $inInboundTypeID
	 * @param integer $inGatewayID
	 * @param integer $inNetworkID
	 * @return commsInboundLog
	 * @static
	 */
	public static function getInstance($inInboundTypeID, $inGatewayID, $inNetworkID) {
		$oObject = new commsInboundLog();
		$oObject->setDate(date(system::getConfig()->getDatabaseDateFormat()));
		$oObject->setInboundTypeID($inInboundTypeID);
		$oObject->setGatewayID($inGatewayID);
		$oObject->setNetworkID($inNetworkID);
		$oObject->load();
		return $oObject;
	}

	/**
	 * Get instance of commsInboundLog by unique key (date)
	 *
	 * @param string $inDate
	 * @param integer $inInboundTypeID
	 * @param integer $inGatewayID
	 * @param integer $inNetworkID
	 * @return commsInboundLog
	 * @static
	 */
	public static function getInstanceByDate($inDate, $inInboundTypeID, $inGatewayID, $inNetworkID) {
		$oObject = new commsInboundLog();
		$oObject->setDate($inDate);
		$oObject->setInboundTypeID($inInboundTypeID);
		$oObject->setGatewayID($inGatewayID);
		$oObject->setNetworkID($inNetworkID);
		$oObject->load();
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsInboundLog
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('logging').'.inboundLog';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsInboundLog();
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
			SELECT date, inboundTypeID, gatewayID, prs, networkID, received
			  FROM '.system::getConfig()->getDatabase('logging').'.inboundLog';

		$where = array();
		if ( $this->_Date !== '' ) {
			$where[] = ' date = :Date ';
		}
		if ( $this->_InboundTypeID !== 0 ) {
			$where[] = ' inboundTypeID = :InboundTypeID ';
		}
		if ( $this->_GatewayID !== 0 ) {
			$where[] = ' gatewayID = :GatewayID ';
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
			if ( $this->_InboundTypeID !== 0 ) {
				$oStmt->bindValue(':InboundTypeID', $this->_InboundTypeID);
			}
			if ( $this->_GatewayID !== 0 ) {
				$oStmt->bindValue(':GatewayID', $this->_GatewayID);
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
		$this->setInboundTypeID((int)$inArray['inboundTypeID']);
		$this->setGatewayID((int)$inArray['gatewayID']);
		$this->setPrs($inArray['prs']);
		$this->setNetworkID((int)$inArray['networkID']);
		$this->setReceived((int)$inArray['received']);
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
				INSERT INTO '.system::getConfig()->getDatabase('logging').'.inboundLog
					( date, inboundTypeID, gatewayID, prs, networkID, received)
				VALUES
					(:Date, :InboundTypeID, :GatewayID, :Prs, :NetworkID, :Received)
				ON DUPLICATE KEY UPDATE
					prs=VALUES(prs),
					received=VALUES(received)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':Date', $this->_Date);
					$oStmt->bindValue(':InboundTypeID', $this->_InboundTypeID);
					$oStmt->bindValue(':GatewayID', $this->_GatewayID);
					$oStmt->bindValue(':Prs', $this->_Prs);
					$oStmt->bindValue(':NetworkID', $this->_NetworkID);
					$oStmt->bindValue(':Received', $this->_Received);

					if ( $oStmt->execute() ) {
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
			DELETE FROM '.system::getConfig()->getDatabase('logging').'.inboundLog
			WHERE
				date = :Date AND
				inboundTypeID = :InboundTypeID AND
				gatewayID = :GatewayID AND
				networkID = :NetworkID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':Date', $this->_Date);
			$oStmt->bindValue(':InboundTypeID', $this->_InboundTypeID);
			$oStmt->bindValue(':GatewayID', $this->_GatewayID);
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
	 * @return commsInboundLog
	 */
	function reset() {
		$this->_Date = date(system::getConfig()->getDatabaseDateFormat()->getParamValue());
		$this->_InboundTypeID = 0;
		$this->_GatewayID = 0;
		$this->_Prs = '';
		$this->_NetworkID = 0;
		$this->_Received = 0;
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
		$string .= " InboundTypeID[$this->_InboundTypeID] $newLine";
		$string .= " GatewayID[$this->_GatewayID] $newLine";
		$string .= " Prs[$this->_Prs] $newLine";
		$string .= " NetworkID[$this->_NetworkID] $newLine";
		$string .= " Received[$this->_Received] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsInboundLog';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"Date\" value=\"$this->_Date\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"InboundTypeID\" value=\"$this->_InboundTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"GatewayID\" value=\"$this->_GatewayID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Prs\" value=\"$this->_Prs\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"NetworkID\" value=\"$this->_NetworkID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Received\" value=\"$this->_Received\" type=\"integer\" /> $newLine";
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
	 * @return commsInboundLog
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
		return $this->_Date.'.'.$this->_InboundTypeID.'.'.$this->_GatewayID.'.'.$this->_NetworkID;
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
	 * @return commsInboundLog
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
	 * @return commsInboundLog
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
	 * @return commsInboundLog
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
	 * Return value of $_Prs
	 *
	 * @return string
	 * @access public
	 */
	function getPrs() {
		return $this->_Prs;
	}

	/**
	 * Set $_Prs to Prs
	 *
	 * @param string $inPrs
	 * @return commsInboundLog
	 * @access public
	 */
	function setPrs($inPrs) {
		if ( $inPrs !== $this->_Prs ) {
			$this->_Prs = $inPrs;
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
	 * @return commsInboundLog
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
	 * Return value of $_Received
	 *
	 * @return integer
	 * @access public
	 */
	function getReceived() {
		return $this->_Received;
	}

	/**
	 * Set $_Received to Received
	 *
	 * @param integer $inReceived
	 * @return commsInboundLog
	 * @access public
	 */
	function setReceived($inReceived) {
		if ( $inReceived !== $this->_Received ) {
			$this->_Received = $inReceived;
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
	 * @return commsInboundLog
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	/**
	 * Increases the received count
	 * 
	 * @return commsInboundLog
	 */
	function incrementReceived() {
		$this->_Received++;
		$this->setModified();
		return $this;
	}
}