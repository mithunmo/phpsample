<?php
/**
 * mofilmCommsSubscription
 *
 * Stored in mofilmCommsSubscription.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsSubscription
 * @category mofilmCommsSubscription
 * @version $Rev: 208 $
 */


/**
 * mofilmCommsSubscription Class
 *
 * Provides access to records in mofilm_comms.subscriptions
 *
 * Creating a new record:
 * <code>
 * $oMofilmCommsSubscription = new mofilmCommsSubscription();
 * $oMofilmCommsSubscription->setEmailID($inEmailID);
 * $oMofilmCommsSubscription->setListID($inListID);
 * $oMofilmCommsSubscription->setSubscribed($inSubscribed);
 * $oMofilmCommsSubscription->setHash($inHash);
 * $oMofilmCommsSubscription->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCommsSubscription = new mofilmCommsSubscription($inEmailID, $inListID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmCommsSubscription = new mofilmCommsSubscription();
 * $oMofilmCommsSubscription->setEmailID($inEmailID);
 * $oMofilmCommsSubscription->setListID($inListID);
 * $oMofilmCommsSubscription->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmCommsSubscription = mofilmCommsSubscription::getInstance($inEmailID, $inListID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmCommsSubscription
 * @category mofilmCommsSubscription
 */
class mofilmCommsSubscription implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmCommsSubscription
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
	 * Stores $_EmailID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_EmailID;

	/**
	 * Stores $_ListID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_ListID;

	/**
	 * Stores $_Subscribed
	 *
	 * @var datetime
	 * @access protected
	 */
	protected $_Subscribed;

	/**
	 * Stores $_Hash
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Hash;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of mofilmCommsSubscription
	 *
	 * @param integer $inEmailID
	 * @param integer $inListID
	 * @return mofilmCommsSubscription
	 */
	function __construct($inEmailID = null, $inListID = null) {
		$this->reset();
		if ( $inEmailID !== null && $inListID !== null ) {
			$this->setEmailID($inEmailID);
			$this->setListID($inListID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new mofilmCommsSubscription containing non-unique properties
	 *
	 * @param datetime $inSubscribed
	 * @return mofilmCommsSubscription
	 * @static
	 */
	public static function factory($inSubscribed = null) {
		$oObject = new mofilmCommsSubscription;
		if ( $inSubscribed !== null ) {
			$oObject->setSubscribed($inSubscribed);
		}
		return $oObject;
	}

	/**
	 * Creates a new comms subscription from the comms email object
	 *
	 * @param mofilmCommsEmail $inEmail
	 * @param integer $inListID
	 * @return mofilmCommsSubscription
	 * @static
	 */
	public static function factoryFromCommsEmail(mofilmCommsEmail $inEmail, $inListID) {
		$oObject = new mofilmCommsSubscription();
		$oObject->setEmailID($inEmail->getID());
		$oObject->setHash(md5($inEmail->getID() . ':' . $inEmail->getEmail() . ':' . $inListID));
		$oObject->setListID($inListID);
		return $oObject;
	}

	/**
	 * Get an instance of mofilmCommsSubscription by primary key
	 *
	 * @param integer $inEmailID
	 * @param integer $inListID
	 * @return mofilmCommsSubscription
	 * @static
	 */
	public static function getInstance($inEmailID, $inListID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inEmailID . '.' . $inListID]) ) {
			return self::$_Instances[$inEmailID . '.' . $inListID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmCommsSubscription();
		$oObject->setEmailID($inEmailID);
		$oObject->setListID($inListID);
		if ( $oObject->load() ) {
			self::$_Instances[$inEmailID . '.' . $inListID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of mofilmCommsSubscription by unique key (hash)
	 *
	 * @param string $inHash
	 * @return mofilmCommsSubscription
	 * @static
	 */
	public static function getInstanceByHash($inHash) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inHash]) ) {
			return self::$_Instances[$inHash];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmCommsSubscription();
		$oObject->setHash($inHash);
		if ( $oObject->load() ) {
			self::$_Instances[$inHash] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmCommsSubscription
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.subscriptions';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT ' . $inOffset . ',' . $inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmCommsSubscription();
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
	 * Returns an array of mofilmCommsSubscription based on listID
	 *
	 * @param integer $inList
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function getEmailListByListID($inList,$inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.subscriptions';
				
		$where = array();
		$where[] = ' listID = :listID ';
		$query .= ' WHERE ' . implode(' AND ', $where);
		if ( $inOffset !== null ) {
			$query .= ' LIMIT ' . $inOffset . ',' . $inLimit;
		}
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':listID', $inList);

			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmCommsSubscription();
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
	 * Gets the list of emailId by listid
	 *
	 * @param integer listid
	 * @return array list of emailID
	 *
	 */
	function getEmailByListId($inListid) {

		$query = 'SELECT emailID FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.subscriptions';
		$where = array();
		$where[] = ' listID = :listID ';
		$query .= ' WHERE ' . implode(' AND ', $where);
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':listID', $inListid);
		$resultList = array();
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$resultList[] = $row['emailID'];
			}
		}

		$oStmt->closeCursor();
		return $resultList;
	}


	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT emailID, listID, subscribed, hash
			  FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.subscriptions';

		$where = array();
		if ( $this->_EmailID !== 0 ) {
			$where[] = ' emailID = :EmailID ';
		}
		if ( $this->_ListID !== 0 ) {
			$where[] = ' listID = :ListID ';
		}
		if ( $this->_Hash !== '' ) {
			$where[] = ' hash = :Hash ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE ' . implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_EmailID !== 0 ) {
				$oStmt->bindValue(':EmailID', $this->_EmailID);
			}
			if ( $this->_ListID !== 0 ) {
				$oStmt->bindValue(':ListID', $this->_ListID);
			}
			if ( $this->_Hash !== '' ) {
				$oStmt->bindValue(':Hash', $this->_Hash);
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
		$this->setEmailID((int) $inArray['emailID']);
		$this->setListID((int) $inArray['listID']);
		$this->setSubscribed($inArray['subscribed']);
		$this->setHash($inArray['hash']);
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
				throw new mofilmException($message);
			}
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO ' . system::getConfig()->getDatabase('mofilm_comms') . '.subscriptions
					( emailID, listID, subscribed, hash)
				VALUES
					(:EmailID, :ListID, :Subscribed, :Hash)
				ON DUPLICATE KEY UPDATE
					subscribed=VALUES(subscribed)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':EmailID', $this->_EmailID);
					$oStmt->bindValue(':ListID', $this->_ListID);
					$oStmt->bindValue(':Subscribed', $this->_Subscribed);
					$oStmt->bindValue(':Hash', $this->_Hash);

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
			DELETE FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.subscriptions
			WHERE
				emailID = :EmailID AND
				listID = :ListID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':EmailID', $this->_EmailID);
			$oStmt->bindValue(':ListID', $this->_ListID);

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
	 * @return mofilmCommsSubscription
	 */
	function reset() {
		$this->_EmailID = 0;
		$this->_ListID = 0;
		$this->_Subscribed = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Hash = '';
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
		$string = '';
		$string .= " EmailID[$this->_EmailID] $newLine";
		$string .= " ListID[$this->_ListID] $newLine";
		$string .= " Subscribed[$this->_Subscribed] $newLine";
		$string .= " Hash[$this->_Hash] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmCommsSubscription';
		$xml = "<$className>$newLine";
		$xml .= "\t<property name=\"EmailID\" value=\"$this->_EmailID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ListID\" value=\"$this->_ListID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Subscribed\" value=\"$this->_Subscribed\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Hash\" value=\"$this->_Hash\" type=\"string\" /> $newLine";
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
			$valid = $this->checkEmailID($message);
		}
		if ( $valid ) {
			$valid = $this->checkListID($message);
		}
		if ( $valid ) {
			$valid = $this->checkSubscribed($message);
		}
		if ( $valid ) {
			$valid = $this->checkHash($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_EmailID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkEmailID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_EmailID) && $this->_EmailID !== 0 ) {
			$inMessage .= "{$this->_EmailID} is not a valid value for EmailID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_ListID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkListID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ListID) && $this->_ListID !== 0 ) {
			$inMessage .= "{$this->_ListID} is not a valid value for ListID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Subscribed has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkSubscribed(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Subscribed) && $this->_Subscribed !== '' ) {
			$inMessage .= "{$this->_Subscribed} is not a valid value for Subscribed";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Hash has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkHash(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Hash) && $this->_Hash !== '' ) {
			$inMessage .= "{$this->_Hash} is not a valid value for Hash";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Hash) > 32 ) {
			$inMessage .= "Hash cannot be more than 32 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Hash) <= 1 ) {
			$inMessage .= "Hash must be more than 1 character";
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
	 * @return mofilmCommsSubscription
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
		return $this->_EmailID . '.' . $this->_ListID;
	}

	/**
	 * Return value of $_EmailID
	 *
	 * @return integer
	 * @access public
	 */
	function getEmailID() {
		return $this->_EmailID;
	}

	/**
	 * Set $_EmailID to EmailID
	 *
	 * @param integer $inEmailID
	 * @return mofilmCommsSubscription
	 * @access public
	 */
	function setEmailID($inEmailID) {
		if ( $inEmailID !== $this->_EmailID ) {
			$this->_EmailID = $inEmailID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_ListID
	 *
	 * @return integer
	 * @access public
	 */
	function getListID() {
		return $this->_ListID;
	}

	/**
	 * Set $_ListID to ListID
	 *
	 * @param integer $inListID
	 * @return mofilmCommsSubscription
	 * @access public
	 */
	function setListID($inListID) {
		if ( $inListID !== $this->_ListID ) {
			$this->_ListID = $inListID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Subscribed
	 *
	 * @return datetime
	 * @access public
	 */
	function getSubscribed() {
		return $this->_Subscribed;
	}

	/**
	 * Set $_Subscribed to Subscribed
	 *
	 * @param datetime $inSubscribed
	 * @return mofilmCommsSubscription
	 * @access public
	 */
	function setSubscribed($inSubscribed) {
		if ( $inSubscribed !== $this->_Subscribed ) {
			$this->_Subscribed = $inSubscribed;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Hash
	 *
	 * @return string
	 * @access public
	 */
	function getHash() {
		return $this->_Hash;
	}

	/**
	 * Set $_Hash to Hash
	 *
	 * @param string $inHash
	 * @return mofilmCommsSubscription
	 * @access public
	 */
	function setHash($inHash) {
		if ( $inHash !== $this->_Hash ) {
			$this->_Hash = $inHash;
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
	 * @return mofilmCommsSubscription
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}

	/**
	 * Delets the row based on the emailID
	 * @param integer $inEmailID
	 */
	function deleteByEmailID($inEmailID) {
		$query = 'DELETE FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.subscriptions
		WHERE emailID = :emailID';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':emailID', $inEmailID);

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

}