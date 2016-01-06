<?php
/**
 * commsOutboundStatus
 *
 * Stored in commsOutboundStatus.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundStatus
 * @version $Rev: 10 $
 */


/**
 * commsOutboundStatus Class
 *
 * Provides access to records in comms.outboundStatus
 *
 * Creating a new record:
 * <code>
 * $oCommsOutboundStatu = new commsOutboundStatus();
 * $oCommsOutboundStatu->setStatusID($inStatusID);
 * $oCommsOutboundStatu->setDescription($inDescription);
 * $oCommsOutboundStatu->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsOutboundStatu = new commsOutboundStatus($inStatusID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsOutboundStatu = new commsOutboundStatus();
 * $oCommsOutboundStatu->setStatusID($inStatusID);
 * $oCommsOutboundStatu->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsOutboundStatu = commsOutboundStatus::getInstance($inStatusID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundStatus
 */
class commsOutboundStatus implements systemDaoInterface, systemDaoValidatorInterface {
	
	const S_WAITING = 1;
	const S_SENDING = 2;
	const S_SENT = 3;
	const S_COMPLETE_ACKD = 4;
	const S_COMPLETE = 5;
	const S_EXPIRED = 92;
	const S_ACK_FAILURE = 93;
	const S_DROPPED = 95;
	const S_FAILED = 99;
	
	/**
	 * Container for static instances of commsOutboundStatus
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
	 * Stores $_StatusID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_StatusID;

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
	 * Returns a new instance of commsOutboundStatus
	 *
	 * @param integer $inStatusID
	 * @return commsOutboundStatus
	 */
	function __construct($inStatusID = null) {
		$this->reset();
		if ( $inStatusID !== null ) {
			$this->setStatusID($inStatusID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsOutboundStatus containing non-unique properties
	 *
	 * @param string $inDescription
	 * @return commsOutboundStatus
	 * @static
	 */
	public static function factory($inDescription = null) {
		$oObject = new commsOutboundStatus;
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsOutboundStatus by primary key
	 *
	 * @param integer $inStatusID
	 * @return commsOutboundStatus
	 * @static
	 */
	public static function getInstance($inStatusID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inStatusID]) ) {
			return self::$_Instances[$inStatusID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsOutboundStatus();
		$oObject->setStatusID($inStatusID);
		if ( $oObject->load() ) {
			self::$_Instances[$inStatusID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsOutboundStatus
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.outboundStatus';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsOutboundStatus();
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
			SELECT statusID, description
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundStatus';

		$where = array();
		if ( $this->_StatusID !== 0 ) {
			$where[] = ' statusID = :StatusID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_StatusID !== 0 ) {
				$oStmt->bindValue(':StatusID', $this->_StatusID);
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
		$this->setStatusID((int)$inArray['statusID']);
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
				throw new commsException($message);
			}
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.outboundStatus
					( statusID, description)
				VALUES
					(:StatusID, :Description)
				ON DUPLICATE KEY UPDATE
					description=VALUES(description)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':StatusID', $this->_StatusID);
					$oStmt->bindValue(':Description', $this->_Description);

					if ( $oStmt->execute() ) {
						if ( !$this->getStatusID() ) {
							$this->setStatusID($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.outboundStatus
			WHERE
				statusID = :StatusID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':StatusID', $this->_StatusID);

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
	 * @return commsOutboundStatus
	 */
	function reset() {
		$this->_StatusID = 0;
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
		$string .= " StatusID[$this->_StatusID] $newLine";
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
		$className = 'commsOutboundStatus';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"StatusID\" value=\"$this->_StatusID\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkStatusID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		return $valid;
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
		if ( $isValid && strlen($this->_Description) > 60 ) {
			$inMessage .= "Description cannot be more than 60 characters";
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
	 * @return commsOutboundStatus
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
		return $this->_StatusID;
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
	 * @return commsOutboundStatus
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
	 * @return commsOutboundStatus
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
	 * @return commsOutboundStatus
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}