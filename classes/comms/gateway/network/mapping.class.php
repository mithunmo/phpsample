<?php
/**
 * commsGatewayNetworkMapping
 *
 * Stored in commsGatewayNetworkMapping.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsGatewayNetworkMapping
 * @category commsGatewayNetworkMapping
 * @version $Rev: 10 $
 */


/**
 * commsGatewayNetworkMapping Class
 *
 * Provides access to records in comms.gatewayNetworkMappings
 *
 * Creating a new record:
 * <code>
 * $oCommsGatewayNetworkMapping = new commsGatewayNetworkMapping();
 * $oCommsGatewayNetworkMapping->setGatewayID($inGatewayID);
 * $oCommsGatewayNetworkMapping->setNetworkID($inNetworkID);
 * $oCommsGatewayNetworkMapping->setGatewayRef($inGatewayRef);
 * $oCommsGatewayNetworkMapping->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsGatewayNetworkMapping = new commsGatewayNetworkMapping($inGatewayID, $inGatewayRef);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsGatewayNetworkMapping = new commsGatewayNetworkMapping();
 * $oCommsGatewayNetworkMapping->setGatewayID($inGatewayID);
 * $oCommsGatewayNetworkMapping->setGatewayRef($inGatewayRef);
 * $oCommsGatewayNetworkMapping->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsGatewayNetworkMapping = commsGatewayNetworkMapping::getInstance($inGatewayID, $inGatewayRef);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage commsGatewayNetworkMapping
 * @category commsGatewayNetworkMapping
 */
class commsGatewayNetworkMapping implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsGatewayNetworkMapping
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
	 * Stores $_GatewayID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GatewayID;

	/**
	 * Stores $_NetworkID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_NetworkID;

	/**
	 * Stores $_GatewayRef
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_GatewayRef;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of commsGatewayNetworkMapping
	 *
	 * @param integer $inGatewayID
	 * @param string $inGatewayRef
	 * @return commsGatewayNetworkMapping
	 */
	function __construct($inGatewayID = null, $inGatewayRef = null) {
		$this->reset();
		if ( $inGatewayID !== null && $inGatewayRef !== null ) {
			$this->setGatewayID($inGatewayID);
			$this->setGatewayRef($inGatewayRef);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsGatewayNetworkMapping containing non-unique properties
	 *
	 * @param integer $inNetworkID
	 * @return commsGatewayNetworkMapping
	 * @static
	 */
	public static function factory($inNetworkID = null) {
		$oObject = new commsGatewayNetworkMapping;
		if ( $inNetworkID !== null ) {
			$oObject->setNetworkID($inNetworkID);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsGatewayNetworkMapping by primary key
	 *
	 * @param integer $inGatewayID
	 * @param string $inGatewayRef
	 * @return commsGatewayNetworkMapping
	 * @static
	 */
	public static function getInstance($inGatewayID, $inGatewayRef) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inGatewayID.'.'.$inGatewayRef]) ) {
			return self::$_Instances[$inGatewayID.'.'.$inGatewayRef];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsGatewayNetworkMapping();
		$oObject->setGatewayID($inGatewayID);
		$oObject->setGatewayRef($inGatewayRef);
		if ( $oObject->load() ) {
			self::$_Instances[$inGatewayID.'.'.$inGatewayRef] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsGatewayNetworkMapping
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inGatewayID
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inGatewayID = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.gatewayNetworkMappings';
		if ( $inGatewayID !== null ) {
			$query .= ' WHERE gatewayID = '.dbManager::getInstance()->quote($inGatewayID);
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsGatewayNetworkMapping();
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
			SELECT gatewayID, networkID, gatewayRef
			  FROM '.system::getConfig()->getDatabase('comms').'.gatewayNetworkMappings';

		$where = array();
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
		$this->setGatewayID((int)$inArray['gatewayID']);
		$this->setNetworkID((int)$inArray['networkID']);
		$this->setGatewayRef($inArray['gatewayRef']);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.gatewayNetworkMappings
					( gatewayID, networkID, gatewayRef)
				VALUES
					(:GatewayID, :NetworkID, :GatewayRef)
				ON DUPLICATE KEY UPDATE
					networkID=VALUES(networkID)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':GatewayID', $this->_GatewayID);
					$oStmt->bindValue(':NetworkID', $this->_NetworkID);
					$oStmt->bindValue(':GatewayRef', $this->_GatewayRef);

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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.gatewayNetworkMappings
			WHERE
				gatewayID = :GatewayID AND
				gatewayRef = :GatewayRef
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':GatewayID', $this->_GatewayID);
			$oStmt->bindValue(':GatewayRef', $this->_GatewayRef);

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
	 * @return commsGatewayNetworkMapping
	 */
	function reset() {
		$this->_GatewayID = 0;
		$this->_NetworkID = 0;
		$this->_GatewayRef = '';
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
		$string .= " GatewayID[$this->_GatewayID] $newLine";
		$string .= " NetworkID[$this->_NetworkID] $newLine";
		$string .= " GatewayRef[$this->_GatewayRef] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsGatewayNetworkMapping';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"GatewayID\" value=\"$this->_GatewayID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"NetworkID\" value=\"$this->_NetworkID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"GatewayRef\" value=\"$this->_GatewayRef\" type=\"string\" /> $newLine";
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
			$valid = $this->checkGatewayID($message);
		}
		if ( $valid ) {
			$valid = $this->checkNetworkID($message);
		}
		if ( $valid ) {
			$valid = $this->checkGatewayRef($message);
		}
		return $valid;
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
	 * Checks that $_NetworkID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkNetworkID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_NetworkID) && $this->_NetworkID !== 0 ) {
			$inMessage .= "{$this->_NetworkID} is not a valid value for NetworkID";
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
		if ( $isValid && strlen($this->_GatewayRef) > 50 ) {
			$inMessage .= "GatewayRef cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_GatewayRef) <= 1 ) {
			$inMessage .= "GatewayRef must be more than 1 character";
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
	 * @return commsGatewayNetworkMapping
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
		return $this->_GatewayID.'.'.$this->_GatewayRef;
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
	 * @return commsGatewayNetworkMapping
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
	 * @return commsGatewayNetworkMapping
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
	 * @return commsGatewayNetworkMapping
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
	 * @return commsGatewayNetworkMapping
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}