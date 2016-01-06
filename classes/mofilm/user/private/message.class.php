<?php
/**
 * mofilmUserPrivateMessage
 * 
 * Stored in mofilmUserPrivateMessage.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern 2007
 * @package scorpio
 * @subpackage system
 * @category mofilmUserPrivateMessage
 * @version $Rev: 10 $
 */


/**
 * mofilmUserPrivateMessage Class
 * 
 * Provides access to records in system.userPrivateMessages
 * 
 * Creating a new record:
 * <code>
 * $oSystemPrivateMessage = new mofilmUserPrivateMessage();
 * $oSystemPrivateMessage->setMessageID($inMessageID);
 * $oSystemPrivateMessage->setToUserID($inToUserID);
 * $oSystemPrivateMessage->setFromUserID($inFromUserID);
 * $oSystemPrivateMessage->setSubject($inSubject);
 * $oSystemPrivateMessage->setMessage($inMessage);
 * $oSystemPrivateMessage->setStatus($inStatus);
 * $oSystemPrivateMessage->setCreateDate($inCreateDate);
 * $oSystemPrivateMessage->setReadDate($inReadDate);
 * $oSystemPrivateMessage->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oSystemPrivateMessage = new mofilmUserPrivateMessage($inMessageID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oSystemPrivateMessage = new mofilmUserPrivateMessage();
 * $oSystemPrivateMessage->setMessageID($inMessageID);
 * $oSystemPrivateMessage->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oSystemPrivateMessage = mofilmUserPrivateMessage::getInstance($inMessageID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package scorpio
 * @subpackage system
 * @category mofilmUserPrivateMessage
 */
class mofilmUserPrivateMessage implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmUserPrivateMessage
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
	 * Stores $_ToUserID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_ToUserID;
			
	/**
	 * Stores $_FromUserID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_FromUserID;
			
	/**
	 * Stores $_Subject
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Subject;
			
	/**
	 * Stores $_Message
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Message;
			
	/**
	 * Stores $_Status
	 * 
	 * @var string (STATUS_NEW,STATUS_READ,STATUS_REPLIED,STATUS_DELETED)
	 * @access protected
	 */
	protected $_Status;
	const STATUS_NEW = 'New';
	const STATUS_READ = 'Read';
	const STATUS_REPLIED = 'Replied';
	const STATUS_DELETED = 'Deleted';
				
	/**
	 * Stores $_CreateDate
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_CreateDate;
			
	/**
	 * Stores $_ReadDate
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_ReadDate;
			
	
	
	/**
	 * Returns a new instance of mofilmUserPrivateMessage
	 * 
	 * @param integer $inMessageID
	 * @return mofilmUserPrivateMessage
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
	 * Creates a new mofilmUserPrivateMessage containing non-unique properties
	 * 
	 * @param integer $inToUserID
	 * @param integer $inFromUserID
	 * @param string $inSubject
	 * @param string $inMessage
	 * @param string $inStatus
	 * @param datetime $inCreateDate
	 * @param datetime $inReadDate
	 * @return mofilmUserPrivateMessage
	 * @static 
	 */
	public static function factory($inToUserID = null, $inFromUserID = null, $inSubject = null, $inMessage = null, $inStatus = null, $inCreateDate = null, $inReadDate = null) {
		$oObject = new mofilmUserPrivateMessage;
		if ( $inToUserID !== null ) {
			$oObject->setToUserID($inToUserID);
		}
		if ( $inFromUserID !== null ) {
			$oObject->setFromUserID($inFromUserID);
		}
		if ( $inSubject !== null ) {
			$oObject->setSubject($inSubject);
		}
		if ( $inMessage !== null ) {
			$oObject->setMessage($inMessage);
		}
		if ( $inStatus !== null ) {
			$oObject->setStatus($inStatus);
		}
		if ( $inCreateDate !== null ) {
			$oObject->setCreateDate($inCreateDate);
		}
		if ( $inReadDate !== null ) {
			$oObject->setReadDate($inReadDate);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmUserPrivateMessage by primary key
	 * 
	 * @param integer $inMessageID
	 * @return mofilmUserPrivateMessage
	 * @static 
	 */
	public static function getInstance($inMessageID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMessageID]) ) {
			return self::$_Instances[$inMessageID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserPrivateMessage();
		$oObject->setMessageID($inMessageID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMessageID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
	
	/**
	 * Returns an array of all the new messages for $inUserID
	 *
	 * @param integer $inUserID
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function getNewMessagesForUser($inUserID, $inOffset = 0, $inLimit = 30) {
		$query = '
			SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPrivateMessages
			 WHERE toUserID = :UserID
			   AND status = "New"
			 ORDER BY createDate DESC LIMIT '.$inOffset.','.$inLimit;
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $inUserID);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserPrivateMessage();
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
	 * Returns the total message count for the specified user
	 * 
	 * @param integer $inUserID
	 * @param string $inType Message type, New, Read or Replied
	 * @return integer
	 * @static
	 */
	public static function getMessageCount($inUserID, $inType = null) {
		$count = 0;
		$query = '
			SELECT COUNT(*) AS msgCount FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPrivateMessages
			 WHERE toUserID = :UserID';
		
		if ( in_array($inType, array(self::STATUS_NEW, self::STATUS_READ, self::STATUS_REPLIED)) ) {
			$query .= ' AND status = '.dbManager::getInstance()->quote($inType);
		}
		
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $inUserID);
			if ( $oStmt->execute() ) {
				$count = $oStmt->fetchColumn();
			}
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $count;
	}
	
	/**
	 * Returns an array of objects of mofilmUserPrivateMessage, for the the user $inUserID
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inUserID
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = 0, $inLimit = 30, $inUserID = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPrivateMessages';
		
		if ( $inUserID !== null ) {
			$query .= ' WHERE toUserID = :UserID AND status != '.dbManager::getInstance()->quote(self::STATUS_DELETED);
		}
		$query .= ' ORDER BY createDate DESC LIMIT '.$inOffset.','.$inLimit;
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $inUserID !== null ) {
				$oStmt->bindValue(':UserID', $inUserID);
			}
			
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserPrivateMessage();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPrivateMessages';
		
		$where = array();
		if ( $this->_MessageID !== 0 ) {
			$where[] = ' messageID = :MessageID ';
		}
						
		if ( count($where) > 0 ) {
			$query .= ' WHERE '.implode(' AND ', $where);
		}

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
		$this->setToUserID((int)$inArray['toUserID']);
		$this->setFromUserID((int)$inArray['fromUserID']);
		$this->setSubject($inArray['subject']);
		$this->setMessage($inArray['message']);
		$this->setStatus($inArray['status']);
		$this->setCreateDate($inArray['createDate']);
		$this->setReadDate($inArray['readDate']);
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
				throw new systemException($message);
			}
						
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userPrivateMessages
					( messageID, toUserID, fromUserID, subject, message, status, createDate, readDate)
				VALUES 
					(:MessageID, :ToUserID, :FromUserID, :Subject, :Message, :Status, :CreateDate, :ReadDate)
				ON DUPLICATE KEY UPDATE
					toUserID=VALUES(toUserID),
					fromUserID=VALUES(fromUserID),
					subject=VALUES(subject),
					message=VALUES(message),
					status=VALUES(status),
					createDate=VALUES(createDate),
					readDate=VALUES(readDate)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MessageID', $this->_MessageID);
					$oStmt->bindValue(':ToUserID', $this->_ToUserID);
					$oStmt->bindValue(':FromUserID', $this->_FromUserID);
					$oStmt->bindValue(':Subject', $this->_Subject);
					$oStmt->bindValue(':Message', $this->_Message);
					$oStmt->bindValue(':Status', $this->_Status);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':ReadDate', $this->_ReadDate);
								
					if ( $oStmt->execute() ) {
						if ( !$this->getMessageID() ) {
							$this->setMessageID($oDB->lastInsertId());
						}
						
						try {
							$oLog = mofilmUserPrivateMessageLog::getInstance($this->getMessageID());
							$oLog->setMessageID($this->getMessageID());
							$oLog->setFromUserID($this->getFromUserID());
							$oLog->setMessage($this->getMessage());
							$oLog->setSentDate($this->getCreateDate());
							$oLog->setSubject($this->getSubject());
							$oLog->setToUserID($this->getToUserID());
							$oLog->save();
						} catch ( Exception $e ) {
							systemLog::error($e->getMessage());
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
	 * Marks the object as deleted
	 * 
	 * @return boolean
	 */
	function delete() {
		$this->setStatus(self::STATUS_DELETED);
		return $this->save();
	}
	
	/**
	 * Resets object properties to defaults
	 * 
	 * @return mofilmUserPrivateMessage
	 */
	function reset() {
		$this->_MessageID = 0;
		$this->_ToUserID = 0;
		$this->_FromUserID = 0;
		$this->_Subject = '';
		$this->_Message = '';
		$this->_Status = 'New';
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_ReadDate = '';
		$this->setModified(false);
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
		$string .= " ToUserID[$this->_ToUserID] $newLine";
		$string .= " FromUserID[$this->_FromUserID] $newLine";
		$string .= " Subject[$this->_Subject] $newLine";
		$string .= " Message[$this->_Message] $newLine";
		$string .= " Status[$this->_Status] $newLine";
		$string .= " CreateDate[$this->_CreateDate] $newLine";
		$string .= " ReadDate[$this->_ReadDate] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmUserPrivateMessage';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"MessageID\" value=\"$this->_MessageID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ToUserID\" value=\"$this->_ToUserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"FromUserID\" value=\"$this->_FromUserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Subject\" value=\"$this->_Subject\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Message\" value=\"$this->_Message\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Status\" value=\"$this->_Status\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"CreateDate\" value=\"$this->_CreateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"ReadDate\" value=\"$this->_ReadDate\" type=\"datetime\" /> $newLine";
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
			$valid = $this->checkToUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkFromUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkSubject($message);
		}
		if ( $valid ) {
			$valid = $this->checkMessage($message);
		}
		if ( $valid ) {
			$valid = $this->checkStatus($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkReadDate($message);
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
	 * Checks that $_ToUserID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkToUserID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ToUserID) && $this->_ToUserID !== 0 ) {
			$inMessage .= "{$this->_ToUserID} is not a valid value for ToUserID";
			$isValid = false;
		}
		if ( $isValid && $this->_ToUserID == 0 ) {
			$inMessage .= "Recipient must be set, please select a user to send the message to";
			$isValid = false;
		}
		if ( $isValid && $this->_ToUserID == $this->_FromUserID ) {
			$inMessage .= "Recipient cannot be the same as the sender";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_FromUserID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkFromUserID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_FromUserID) && $this->_FromUserID !== 0 ) {
			$inMessage .= "{$this->_FromUserID} is not a valid value for FromUserID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Subject has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkSubject(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Subject) && $this->_Subject !== '' ) {
			$inMessage .= "{$this->_Subject} is not a valid value for Subject";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_Subject) > 255 ) {
			$inMessage .= "Subject cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Subject) <= 1 ) {
			$inMessage .= "Subject must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Message has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkMessage(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Message) && $this->_Message !== '' ) {
			$inMessage .= "{$this->_Message} is not a valid value for Message";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_Message) > 60000 ) {
			$inMessage .= "Message can be a maximum of 60,000 characters only";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Status has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkStatus(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Status) && $this->_Status !== '' ) {
			$inMessage .= "{$this->_Status} is not a valid value for Status";
			$isValid = false;
		}		
		if ( $isValid && $this->_Status != '' && !in_array($this->_Status, array(self::STATUS_NEW, self::STATUS_READ, self::STATUS_REPLIED, self::STATUS_DELETED)) ) {
			$inMessage .= "Status must be one of STATUS_NEW, STATUS_READ";
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
	 * Checks that $_ReadDate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkReadDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ReadDate) && $this->_ReadDate !== '' ) {
			$inMessage .= "{$this->_ReadDate} is not a valid value for ReadDate";
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
	 * @return mofilmUserPrivateMessage
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
	 * @return mofilmUserPrivateMessage
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
	 * Return value of $_ToUserID
	 * 
	 * @return integer
	 * @access public
	 */
	function getToUserID() {
		return $this->_ToUserID;
	}
	
	/**
	 * Returns the recipient mofilmUser object
	 *
	 * @return mofilmUser
	 */
	function getRecipient() {
		return mofilmUserManager::getInstanceByID($this->_ToUserID);
	}
	
	/**
	 * Set $_ToUserID to ToUserID
	 * 
	 * @param integer $inToUserID
	 * @return mofilmUserPrivateMessage
	 * @access public
	 */
	function setToUserID($inToUserID) {
		if ( $inToUserID !== $this->_ToUserID ) {
			$this->_ToUserID = $inToUserID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_FromUserID
	 * 
	 * @return integer
	 * @access public
	 */
	function getFromUserID() {
		return $this->_FromUserID;
	}
	
	/**
	 * Returns the sender mofilmUser
	 *
	 * @return mofilmUser
	 */
	function getSender() {
		return mofilmUserManager::getInstanceByID($this->_FromUserID);
	}
	
	/**
	 * Set $_FromUserID to FromUserID
	 * 
	 * @param integer $inFromUserID
	 * @return mofilmUserPrivateMessage
	 * @access public
	 */
	function setFromUserID($inFromUserID) {
		if ( $inFromUserID !== $this->_FromUserID ) {
			$this->_FromUserID = $inFromUserID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Subject
	 * 
	 * @return string
	 * @access public
	 */
	function getSubject() {
		return $this->_Subject;
	}
	
	/**
	 * Set $_Subject to Subject
	 * 
	 * @param string $inSubject
	 * @return mofilmUserPrivateMessage
	 * @access public
	 */
	function setSubject($inSubject) {
		if ( $inSubject !== $this->_Subject ) {
			$this->_Subject = $inSubject;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Message
	 * 
	 * @return string
	 * @access public
	 */
	function getMessage() {
		return $this->_Message;
	}
	
	/**
	 * Set $_Message to Message
	 * 
	 * @param string $inMessage
	 * @return mofilmUserPrivateMessage
	 * @access public
	 */
	function setMessage($inMessage) {
		if ( $inMessage !== $this->_Message ) {
			$this->_Message = $inMessage;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Status
	 * 
	 * @return string
	 * @access public
	 */
	function getStatus() {
		return $this->_Status;
	}
	
	/**
	 * Set $_Status to Status
	 * 
	 * @param string $inStatus
	 * @return mofilmUserPrivateMessage
	 * @access public
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
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
	 * @return mofilmUserPrivateMessage
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
	 * Return value of $_ReadDate
	 * 
	 * @return datetime
	 * @access public
	 */
	function getReadDate() {
		return $this->_ReadDate;
	}
	
	/**
	 * Set $_ReadDate to ReadDate
	 * 
	 * @param datetime $inReadDate
	 * @return mofilmUserPrivateMessage
	 * @access public
	 */
	function setReadDate($inReadDate) {
		if ( $inReadDate !== $this->_ReadDate ) {
			$this->_ReadDate = $inReadDate;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns true if this message is attached to a movie
	 * 
	 * @return boolean
	 */
	function isAttachedToMovie() {
		return mofilmMovieMessageHistory::isMessageAttachedToMovie($this->getMessageID());
	}
	
	/**
	 * Returns the movieID as an integer
	 * 
	 * @return integer
	 */
	function getAttachedMovieID() {
		return mofilmMovieMessageHistory::getMovieFromMessageID($this->getMessageID());
	}
	
	/**
	 * Returns the movie object attached to this message
	 * 
	 * @return mofilmMovie
	 */
	function getAttachedMovie() {
		return mofilmMovieManager::getInstanceByID(
			mofilmMovieMessageHistory::getMovieFromMessageID($this->getMessageID())
		);
	}
}