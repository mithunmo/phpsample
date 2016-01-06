<?php
/**
 * mofilmCommsNewsletterhistory
 *
 * Stored in mofilmCommsNewsletterhistory.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterhistory
 * @category mofilmCommsNewsletterhistory
 * @version $Rev: 806 $
 */


/**
 * mofilmCommsNewsletterhistory Class
 *
 * Provides access to records in mofilm_comms.newsletterHistory
 *
 * Creating a new record:
 * <code>
 * $oMofilmCommsNewsletterhistory = new mofilmCommsNewsletterhistory();
 * $oMofilmCommsNewsletterhistory->setID($inID);
 * $oMofilmCommsNewsletterhistory->setNewsletterID($inNewsletterID);
 * $oMofilmCommsNewsletterhistory->setTransactionID($inTransactionID);
 * $oMofilmCommsNewsletterhistory->setUserID($inUserID);
 * $oMofilmCommsNewsletterhistory->setStatus($inStatus);
 * $oMofilmCommsNewsletterhistory->setUpdatedate($inUpdatedate);
 * $oMofilmCommsNewsletterhistory->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCommsNewsletterhistory = new mofilmCommsNewsletterhistory($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmCommsNewsletterhistory = new mofilmCommsNewsletterhistory();
 * $oMofilmCommsNewsletterhistory->setID($inID);
 * $oMofilmCommsNewsletterhistory->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmCommsNewsletterhistory = mofilmCommsNewsletterhistory::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmCommsNewsletterhistory
 * @category mofilmCommsNewsletterhistory
 */
class mofilmCommsNewsletterhistory implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
	 */
	const PRIMARY_KEY_SEPARATOR = '.';
	
	/**
	 * If the newsletter is viewed by the user then the status is set to 1
	 * 
	 * @var integer
	 */
	const VIEWED_NEWSLETTER = 1;

	/**
	 * If the newsletter is not viewed by user then the status is set to 0
	 *
	 * @var integer
	 */
	const NOT_VIEWED_NEWSLETTER = 1;



	/**
	 * Container for static instances of mofilmCommsNewsletterhistory
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
	 * Stores the validator for this object
	 *
	 * @var utilityValidator
	 * @access protected
	 */
	protected $_Validator;

	/**
	 * Stores $_ID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_ID;

	/**
	 * Stores $_NewsletterID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_NewsletterID;

	/**
	 * Stores $_TransactionID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TransactionID;

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_Status
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Status;

	/**
	 * Stores $_Updatedate
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Updatedate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of mofilmCommsNewsletterhistory
	 *
	 * @param integer $inID
	 * @return mofilmCommsNewsletterhistory
	 */
	function __construct($inID = null) {
		$this->reset();
		if ( $inID !== null ) {
			$this->setID($inID);
			$this->load();
		}
	}

	/**
	 * Object destructor, used to remove internal object instances
	 *
	 * @return void
	 */
	function __destruct() {
		if ( $this->_Validator instanceof utilityValidator ) {
			$this->_Validator = null;
		}
	}

	/**
	 * Get an instance of mofilmCommsNewsletterhistory by primary key
	 *
	 * @param integer $inID
	 * @return mofilmCommsNewsletterhistory
	 * @static
	 */
	public static function getInstance($inID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inID]) ) {
			return self::$_Instances[$inID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmCommsNewsletterhistory();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmCommsNewsletterhistory
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, newsletterID, transactionID, userID, status, updateDate
			  FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.newsletterHistory
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT ' . $inOffset . ',' . $inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmCommsNewsletterhistory();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

	/**
	 * Gets the list of data per newsletter id
	 *
	 * @param integer $inNlId
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array(mofilmCommsNewsletterhistory)
	 * @static
	 */
	public static function getNlById($inNlId, $inOffset = null, $inLimit = 30) {
		$list = array();

		$query = '
			SELECT *
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterHistory
			 WHERE newsletterID = :NewsletterID ';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT ' . $inOffset . ',' . $inLimit;
		}

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':NewsletterID', $inNlId);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmCommsNewsletterhistory();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

	/**
	 * Updates the record of newsletterHistory whether its bean read or not
	 *
	 * @param integer $inId
	 * @param integer $inStatus
	 * @return void
	 * @static
	 */
	public static function updateReadStatus($inId, $inStatus) {
		$query = '
			UPDATE '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterHistory
			   SET status = :Status,
			       updateDate = :UpdateDate
			 WHERE ID = :ID';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Status', $inStatus);
		$oStmt->bindValue(':UpdateDate', date(system::getConfig()->getDatabaseDateFormat()->getParamValue()));
		$oStmt->bindValue(':ID', $inId);
		$oStmt->execute();
		$oStmt->closeCursor();
	}

	/**
	 *  Gets the list of newsletterHistory objects based on Read status =1
	 *
	 * @param integer $inNlId
	 * @return array(mofilmCommsNewsletterhistory)
	 * @static
	 */
	public static function getReadCountByDate($inNlId) {
		$list = array();

		$query = '
			SELECT *
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterHistory
			 WHERE newsletterID = :NewsletterID
			   AND status = :Status
			 ORDER BY updateDate ASC';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':NewsletterID', $inNlId);
		$oStmt->bindValue(':Status', 1);

		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmCommsNewsletterhistory();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

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
			SELECT ID, newsletterID, transactionID, userID, status, updateDate
			  FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.newsletterHistory';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE ' . implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $this->_ID !== 0 ) {
			$oStmt->bindValue(':ID', $this->getID());
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

		return $return;
	}

	/**
	 * Loads a record by array
	 *
	 * @param array $inArray
	 * @return void
	 */
	function loadFromArray(array $inArray) {
		$this->setID((int) $inArray['ID']);
		$this->setNewsletterID((int) $inArray['newsletterID']);
		$this->setTransactionID((int) $inArray['transactionID']);
		$this->setUserID((int) $inArray['userID']);
		$this->setStatus((int) $inArray['status']);
		$this->setUpdatedate($inArray['updateDate']);
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

			$this->setUpdatedate(date("Y-m-d"));
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO ' . system::getConfig()->getDatabase('mofilm_comms') . '.newsletterHistory
					( ID, newsletterID, transactionID, userID, status, updateDate)
				VALUES
					(:ID, :NewsletterID, :TransactionID, :UserID, :Status, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					newsletterID=VALUES(newsletterID),
					transactionID=VALUES(transactionID),
					userID=VALUES(userID),
					status=VALUES(status),
					updateDate=VALUES(updateDate)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':NewsletterID', $this->getNewsletterID());
				$oStmt->bindValue(':TransactionID', $this->getTransactionID());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':UpdateDate', $this->getUpdatedate());

				if ( $oStmt->execute() ) {
					if ( !$this->getID() ) {
						$this->setID($oDB->lastInsertId());
					}
					$this->setModified(false);
					$return = true;
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
			DELETE FROM ' . system::getConfig()->getDatabase('mofilm_comms') . '.newsletterHistory
			WHERE
				ID = :ID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':ID', $this->getID());

		if ( $oStmt->execute() ) {
			$oStmt->closeCursor();
			$this->reset();
			return true;
		}

		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mofilmCommsNewsletterhistory
	 */
	function reset() {
		$this->_ID = 0;
		$this->_NewsletterID = 0;
		$this->_TransactionID = 0;
		$this->_UserID = 0;
		$this->_Status = 0;
		$this->_Updatedate = date(system::getConfig()->getDatabaseDateFormat()->getParamValue());
		$this->_Validator = null;
		$this->setModified(false);
		$this->setMarkForDeletion(false);
		return $this;
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
	 * Returns the validator, creating one if not set
	 *
	 * @return utilityValidator
	 */
	function getValidator() {
		if ( !$this->_Validator instanceof utilityValidator ) {
			$this->_Validator = new utilityValidator();
		}
		return $this->_Validator;
	}

	/**
	 * Set a pre-built validator instance
	 *
	 * @param utilityValidator $inValidator
	 * @return mofilmCommsNewsletterhistory
	 */
	function setValidator(utilityValidator $inValidator) {
		$this->_Validator = $inValidator;
		return $this;
	}

	/**
	 * Returns true if object is valid, any errors are added to $inMessage
	 *
	 * @param string $inMessage
	 * @return boolean
	 */
	function isValid(&$inMessage = '') {
		$valid = true;

		$oValidator = $this->getValidator();
		$oValidator->reset();
		$oValidator->setData($this->toArray())->setRules($this->getValidationRules());
		if ( !$oValidator->isValid() ) {
			foreach ( $oValidator->getMessages() as $key => $messages ) {
				$inMessage .= "Error with $key: " . implode(', ', $messages) . "\n";
			}
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Returns the array of rules used to validate this object
	 *
	 * @return array
	 */
	function getValidationRules() {
		return array(
			'_ID' => array(
				'number' => array(),
			),
			'_NewsletterID' => array(
				'number' => array(),
			),
			'_TransactionID' => array(
				'number' => array(),
			),
			'_UserID' => array(
				'number' => array(),
			),
			'_Status' => array(
				'number' => array(),
			),
			'_Updatedate' => array(
				'string' => array(),
			),
		);
	}


	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( !$modified && $this->getUpdateDate() ) {
			$this->_Modified = $this->getUpdateDate()->isModified() || $modified;
		}

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmCommsNewsletterhistory
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the primaryKey
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->_ID;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * {@link mofilmCommsNewsletterhistory::PRIMARY_KEY_SEPARATOR}.
	 *
	 * @param string $inKey
	 * @return mofilmCommsNewsletterhistory
	 */
	function setPrimaryKey($inKey) {
		list($ID) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setID($ID);
	}

	/**
	 * Return the current value of the property $_ID
	 *
	 * @return integer
	 */
	function getID() {
		return $this->_ID;
	}

	/**
	 * Set the object property _ID to $inID
	 *
	 * @param integer $inID
	 * @return mofilmCommsNewsletterhistory
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_NewsletterID
	 *
	 * @return integer
	 */
	function getNewsletterID() {
		return $this->_NewsletterID;
	}

	/**
	 * Set the object property _NewsletterID to $inNewsletterID
	 *
	 * @param integer $inNewsletterID
	 * @return mofilmCommsNewsletterhistory
	 */
	function setNewsletterID($inNewsletterID) {
		if ( $inNewsletterID !== $this->_NewsletterID ) {
			$this->_NewsletterID = $inNewsletterID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_TransactionID
	 *
	 * @return integer
	 */
	function getTransactionID() {
		return $this->_TransactionID;
	}

	/**
	 * Set the object property _TransactionID to $inTransactionID
	 *
	 * @param integer $inTransactionID
	 * @return mofilmCommsNewsletterhistory
	 */
	function setTransactionID($inTransactionID) {
		if ( $inTransactionID !== $this->_TransactionID ) {
			$this->_TransactionID = $inTransactionID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_UserID
	 *
	 * @return integer
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set the object property _UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmCommsNewsletterhistory
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Status
	 *
	 * @return integer
	 */
	function getStatus() {
		return $this->_Status;
	}

	/**
	 * Set the object property _Status to $inStatus
	 *
	 * @param integer $inStatus
	 * @return mofilmCommsNewsletterhistory
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Updatedate
	 *
	 * @return string
	 */
	function getUpdatedate() {
		return $this->_Updatedate;
	}

	/**
	 * Set the object property _Updatedate to $inUpdatedate
	 *
	 * @param string $inUpdatedate
	 * @return mofilmCommsNewsletterhistory
	 */
	function setUpdatedate($inUpdatedate) {
		if ( $inUpdatedate !== $this->_Updatedate ) {
			$this->_Updatedate = $inUpdatedate;
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
	 * @return mofilmCommsNewsletterhistory
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}