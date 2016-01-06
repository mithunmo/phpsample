<?php
/**
 * mofilmUserPrivateMessageLog
 * 
 * Stored in mofilmUserPrivateMessageLog.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category mofilmUserPrivateMessageLog
 * @version $Rev: 10 $
 */


/**
 * mofilmUserPrivateMessageLog Class
 * 
 * This class is from Scorpio Framework baseAdminSite.
 * 
 * Provides access to records in system.userPrivateMessageLog
 * 
 * Creating a new record:
 * <code>
 * $oSystemPrivateMessageLog = new mofilmUserPrivateMessageLog();
 * $oSystemPrivateMessageLog->setMessageID($inMessageID);
 * $oSystemPrivateMessageLog->setToUserID($inToUserID);
 * $oSystemPrivateMessageLog->setFromUserID($inFromUserID);
 * $oSystemPrivateMessageLog->setSubject($inSubject);
 * $oSystemPrivateMessageLog->setMessage($inMessage);
 * $oSystemPrivateMessageLog->setSentDate($inSentDate);
 * $oSystemPrivateMessageLog->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oSystemPrivateMessageLog = new mofilmUserPrivateMessageLog($inMessageID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oSystemPrivateMessageLog = new mofilmUserPrivateMessageLog();
 * $oSystemPrivateMessageLog->setMessageID($inMessageID);
 * $oSystemPrivateMessageLog->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oSystemPrivateMessageLog = mofilmUserPrivateMessageLog::getInstance($inMessageID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package scorpio
 * @subpackage system
 * @category mofilmUserPrivateMessageLog
 */
class mofilmUserPrivateMessageLog implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmUserPrivateMessageLog
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
	 * @var string
	 * @access protected
	 */
	protected $_Status;
	const STATUS_SENT = 'Sent';
	const STATUS_DELETED = 'Deleted';
	
	/**
	 * Stores $_SentDate
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_SentDate;
			
	
	
	/**
	 * Returns a new instance of mofilmUserPrivateMessageLog
	 * 
	 * @param integer $inMessageID
	 * @return mofilmUserPrivateMessageLog
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
	 * Creates a new mofilmUserPrivateMessageLog containing non-unique properties
	 * 
	 * @param integer $inToUserID
	 * @param integer $inFromUserID
	 * @param string $inSubject
	 * @param string $inMessage
	 * @param datetime $inSentDate
	 * @return mofilmUserPrivateMessageLog
	 * @static 
	 */
	public static function factory($inToUserID = null, $inFromUserID = null, $inSubject = null, $inMessage = null, $inSentDate = null) {
		$oObject = new mofilmUserPrivateMessageLog;
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
		if ( $inSentDate !== null ) {
			$oObject->setSentDate($inSentDate);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmUserPrivateMessageLog by primary key
	 * 
	 * @param integer $inMessageID
	 * @return mofilmUserPrivateMessageLog
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
		$oObject = new mofilmUserPrivateMessageLog();
		$oObject->setMessageID($inMessageID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMessageID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmUserPrivateMessageLog
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inUserID
	 * @param string $inStatus (optional) defaults to only sent messages
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = 0, $inLimit = 30, $inUserID = null, $inStatus = self::STATUS_SENT) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPrivateMessageLog WHERE 1';
		
		if ( $inUserID !== null ) {
			$query .= ' AND fromUserID = :UserID ';
		}
		if ( $inStatus !== null && in_array($inStatus, array(self::STATUS_DELETED, self::STATUS_SENT)) ) {
			$query .= ' AND status = '.dbManager::getInstance()->quote($inStatus);
		}
		$query .= ' ORDER BY sentDate DESC LIMIT '.$inOffset.','.$inLimit;
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $inUserID !== null ) {
				$oStmt->bindValue(':UserID', $inUserID);
			}
			
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserPrivateMessageLog();
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
	 * @param string $inStatus (optional) defaults to only sent
	 * @return integer
	 * @static
	 */
	public static function getMessageCount($inUserID, $inStatus = self::STATUS_SENT) {
		$count = 0;
		$query = '
			SELECT COUNT(*) AS msgCount FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPrivateMessageLog
			 WHERE fromUserID = :UserID';
		
		if ( $inStatus !== null && in_array($inStatus, array(self::STATUS_DELETED, self::STATUS_SENT)) ) {
			$query .= ' AND status = '.dbManager::getInstance()->quote($inStatus);
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
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userPrivateMessageLog';
		
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
		$this->setSentDate($inArray['sentDate']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userPrivateMessageLog
					( messageID, toUserID, fromUserID, subject, message, status, sentDate)
				VALUES 
					(:MessageID, :ToUserID, :FromUserID, :Subject, :Message, :Status, :SentDate)
				ON DUPLICATE KEY UPDATE
					toUserID=VALUES(toUserID),
					fromUserID=VALUES(fromUserID),
					subject=VALUES(subject),
					message=VALUES(message),
					status=VALUES(status),
					sentDate=VALUES(sentDate)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MessageID', $this->_MessageID);
					$oStmt->bindValue(':ToUserID', $this->_ToUserID);
					$oStmt->bindValue(':FromUserID', $this->_FromUserID);
					$oStmt->bindValue(':Subject', $this->_Subject);
					$oStmt->bindValue(':Message', $this->_Message);
					$oStmt->bindValue(':Status', $this->_Status);
					$oStmt->bindValue(':SentDate', $this->_SentDate);
								
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
	 * @return mofilmUserPrivateMessageLog
	 */
	function reset() {
		$this->_MessageID = 0;
		$this->_ToUserID = 0;
		$this->_FromUserID = 0;
		$this->_Subject = '';
		$this->_Message = '';
		$this->_Status = self::STATUS_SENT;
		$this->_SentDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
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
		$string .= " SentDate[$this->_SentDate] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmUserPrivateMessageLog';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"MessageID\" value=\"$this->_MessageID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ToUserID\" value=\"$this->_ToUserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"FromUserID\" value=\"$this->_FromUserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Subject\" value=\"$this->_Subject\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Message\" value=\"$this->_Message\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"SentDate\" value=\"$this->_SentDate\" type=\"datetime\" /> $newLine";
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
			$valid = $this->checkSentDate($message);
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
				
		return $isValid;
	}
		
	/**
	 * Checks that $_SentDate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkSentDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_SentDate) && $this->_SentDate !== '' ) {
			$inMessage .= "{$this->_SentDate} is not a valid value for SentDate";
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
	 * @return mofilmUserPrivateMessageLog
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
	 * @return mofilmUserPrivateMessageLog
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
	 * @return mofilmUserPrivateMessageLog
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
	 * Set $_FromUserID to FromUserID
	 * 
	 * @param integer $inFromUserID
	 * @return mofilmUserPrivateMessageLog
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
	 * @return mofilmUserPrivateMessageLog
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
	 * @return mofilmUserPrivateMessageLog
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
	 * Returns $_Status
	 *
	 * @return string
	 */
	function getStatus() {
		return $this->_Status;
	}
	
	/**
	 * Set $_Status to $inStatus
	 *
	 * @param string $inStatus
	 * @return mofilmUserPrivateMessageLog
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
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
	 * @return mofilmUserPrivateMessageLog
	 * @access public
	 */
	function setSentDate($inSentDate) {
		if ( $inSentDate !== $this->_SentDate ) {
			$this->_SentDate = $inSentDate;
			$this->setModified();
		}
		return $this;
	}
}