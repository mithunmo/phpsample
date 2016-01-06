<?php
/**
 * mofilmCommsNewsletterlink
 *
 * Stored in mofilmCommsNewsletterlink.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterlink
 * @category mofilmCommsNewsletterlink
 * @version $Rev: 806 $
 */


/**
 * mofilmCommsNewsletterlink Class
 *
 * Provides access to records in mofilm_comms.newsletterLink
 *
 * Creating a new record:
 * <code>
 * $oMofilmCommsNewsletterlink = new mofilmCommsNewsletterlink();
 * $oMofilmCommsNewsletterlink->setID($inID);
 * $oMofilmCommsNewsletterlink->setNewsletterID($inNewsletterID);
 * $oMofilmCommsNewsletterlink->setLinkName($inLinkName);
 * $oMofilmCommsNewsletterlink->setUserID($inUserID);
 * $oMofilmCommsNewsletterlink->setStatus($inStatus);
 * $oMofilmCommsNewsletterlink->setCreateDate($inCreateDate);
 * $oMofilmCommsNewsletterlink->setUpdateDate($inUpdateDate);
 * $oMofilmCommsNewsletterlink->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCommsNewsletterlink = new mofilmCommsNewsletterlink($inLinkName, $inUserID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmCommsNewsletterlink = new mofilmCommsNewsletterlink();
 * $oMofilmCommsNewsletterlink->setLinkName($inLinkName);
 * $oMofilmCommsNewsletterlink->setUserID($inUserID);
 * $oMofilmCommsNewsletterlink->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmCommsNewsletterlink = mofilmCommsNewsletterlink::getInstance($inLinkName, $inUserID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmCommsNewsletterlink
 * @category mofilmCommsNewsletterlink
 */
class mofilmCommsNewsletterlink implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmCommsNewsletterlink
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
	 * Stores $_LinkName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_LinkName;

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
	 * Stores $_CreateDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_CreateDate;

	/**
	 * Stores $_UpdateDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_UpdateDate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmCommsNewsletterlink
	 *
	 * @param string $inLinkName
	 * @param integer $inUserID
	 * @return mofilmCommsNewsletterlink
	 */
	function __construct($inLinkName = null, $inUserID = null) {
		$this->reset();
		if ( $inLinkName !== null && $inUserID !== null ) {
			$this->setLinkName($inLinkName);
			$this->setUserID($inUserID);
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
	 * Get an instance of mofilmCommsNewsletterlink by primary key
	 *
	 * @param string $inLinkName
	 * @param integer $inUserID
	 * @return mofilmCommsNewsletterlink
	 * @static
	 */
	public static function getInstance($inLinkName, $inUserID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inLinkName.'.'.$inUserID]) ) {
			return self::$_Instances[$inLinkName.'.'.$inUserID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmCommsNewsletterlink();
		$oObject->setLinkName($inLinkName);
		$oObject->setUserID($inUserID);
		if ( $oObject->load() ) {
			self::$_Instances[$inLinkName.'.'.$inUserID] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmCommsNewsletterlink
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
			SELECT ID, newsletterID, linkName, userID, status, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterLink
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmCommsNewsletterlink();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

	/**
	 * Gets the list of link count w.r.t to each link per newsletter
	 *
	 * @param integer $inNlId
	 * @return array
	 * @static
	 */
	public static function getNewsletterLinkById($inNlId) {
		$list = array();

		$query = '
			SELECT count(linkName) AS Count , linkName
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterLink
			 WHERE newsletterID = :NewsletterID
			   AND linkName != ""
			 GROUP BY linkName';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':NewsletterID', $inNlId);

		$i=0;
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$list[$i]["Count"] = $row["Count"];
				$list[$i]["linkName"]=$row["linkName"];
				$i++;

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
			SELECT ID, newsletterID, linkName, userID, status, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterLink';

		$where = array();
		if ( $this->_LinkName !== '' ) {
			$where[] = ' linkName = :LinkName ';
		}
		if ( $this->_UserID !== 0 ) {
			$where[] = ' userID = :UserID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $this->_LinkName !== '' ) {
			$oStmt->bindValue(':LinkName', $this->getLinkName());
		}
		if ( $this->_UserID !== 0 ) {
			$oStmt->bindValue(':UserID', $this->getUserID());
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
		$this->setID((int)$inArray['ID']);
		$this->setNewsletterID((int)$inArray['newsletterID']);
		$this->setLinkName($inArray['linkName']);
		$this->setUserID((int)$inArray['userID']);
		$this->setStatus((int)$inArray['status']);
		$this->setCreateDate($inArray['createDate']);
		$this->setUpdateDate($inArray['updateDate']);
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

			$this->getUpdateDate()->setTimestamp(time());
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterLink
					( ID, newsletterID, linkName, userID, status, createDate, updateDate)
				VALUES
					(:ID, :NewsletterID, :LinkName, :UserID, :Status, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					ID=VALUES(ID),
					newsletterID=VALUES(newsletterID),
					status=VALUES(status),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':NewsletterID', $this->getNewsletterID());
				$oStmt->bindValue(':LinkName', $this->getLinkName());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':CreateDate', $this->getCreateDate());
				$oStmt->bindValue(':UpdateDate', $this->getUpdateDate());

				if ( $oStmt->execute() ) {
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterLink
			WHERE
				linkName = :LinkName AND
				userID = :UserID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':LinkName', $this->getLinkName());
		$oStmt->bindValue(':UserID', $this->getUserID());

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
	 * @return mofilmCommsNewsletterlink
	 */
	function reset() {
		$this->_ID = 0;
		$this->_NewsletterID = 0;
		$this->_LinkName = '';
		$this->_UserID = 0;
		$this->_Status = 0;
		$this->_CreateDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_UpdateDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
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
	 * @return mofilmCommsNewsletterlink
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
				$inMessage .= "Error with $key: ".implode(', ', $messages)."\n";
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
			'_LinkName' => array(
				'string' => array(),
			),
			'_UserID' => array(
				'number' => array(),
			),
			'_Status' => array(
				'number' => array(),
			),
			'_CreateDate' => array(
				'dateTime' => array(),
			),
			'_UpdateDate' => array(
				'dateTime' => array(),
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
	 * @return mofilmCommsNewsletterlink
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
		return $this->_LinkName.self::PRIMARY_KEY_SEPARATOR.$this->_UserID;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * {@link mofilmCommsNewsletterlink::PRIMARY_KEY_SEPARATOR}.
 	 *
	 * @param string $inKey
	 * @return mofilmCommsNewsletterlink
  	 */
	function setPrimaryKey($inKey) {
		list($linkName, $userID) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setLinkName($linkName);
		$this->setUserID($userID);
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
	 * @return mofilmCommsNewsletterlink
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
	 * @return mofilmCommsNewsletterlink
	 */
	function setNewsletterID($inNewsletterID) {
		if ( $inNewsletterID !== $this->_NewsletterID ) {
			$this->_NewsletterID = $inNewsletterID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_LinkName
	 *
	 * @return string
 	 */
	function getLinkName() {
		return $this->_LinkName;
	}

	/**
	 * Set the object property _LinkName to $inLinkName
	 *
	 * @param string $inLinkName
	 * @return mofilmCommsNewsletterlink
	 */
	function setLinkName($inLinkName) {
		if ( $inLinkName !== $this->_LinkName ) {
			$this->_LinkName = $inLinkName;
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
	 * @return mofilmCommsNewsletterlink
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
	 * @return mofilmCommsNewsletterlink
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_CreateDate
	 *
	 * @return systemDateTime
 	 */
	function getCreateDate() {
		return $this->_CreateDate;
	}

	/**
	 * Set the object property _CreateDate to $inCreateDate
	 *
	 * @param systemDateTime $inCreateDate
	 * @return mofilmCommsNewsletterlink
	 */
	function setCreateDate($inCreateDate) {
		if ( $inCreateDate !== $this->_CreateDate ) {
			if ( !$inCreateDate instanceof DateTime ) {
				$inCreateDate = new systemDateTime($inCreateDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_CreateDate = $inCreateDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_UpdateDate
	 *
	 * @return systemDateTime
 	 */
	function getUpdateDate() {
		return $this->_UpdateDate;
	}

	/**
	 * Set the object property _UpdateDate to $inUpdateDate
	 *
	 * @param systemDateTime $inUpdateDate
	 * @return mofilmCommsNewsletterlink
	 */
	function setUpdateDate($inUpdateDate) {
		if ( $inUpdateDate !== $this->_UpdateDate ) {
			if ( !$inUpdateDate instanceof DateTime ) {
				$inUpdateDate = new systemDateTime($inUpdateDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_UpdateDate = $inUpdateDate;
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
	 * @return mofilmCommsNewsletterlink
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}