<?php
/**
 * mofilmMovieMessageHistory
 *
 * Stored in mofilmMovieMessageHistory.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMovieMessageHistory
 * @category mofilmMovieMessageHistory
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieMessageHistory Class
 *
 * Provides access to records in mofilm_content.movieMessageHistory
 *
 * Creating a new record:
 * <code>
 * $oMofilmMovieMessageHistory = new mofilmMovieMessageHistory();
 * $oMofilmMovieMessageHistory->setId($inId);
 * $oMofilmMovieMessageHistory->setMovieID($inMovieID);
 * $oMofilmMovieMessageHistory->setMessageID($inMessageID);
 * $oMofilmMovieMessageHistory->setSenderID($inSenderID);
 * $oMofilmMovieMessageHistory->setRecipientID($inRecipientID);
 * $oMofilmMovieMessageHistory->setSendDate($inSendDate);
 * $oMofilmMovieMessageHistory->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMovieMessageHistory = new mofilmMovieMessageHistory($inId);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmMovieMessageHistory = new mofilmMovieMessageHistory();
 * $oMofilmMovieMessageHistory->setId($inId);
 * $oMofilmMovieMessageHistory->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmMovieMessageHistory = mofilmMovieMessageHistory::getInstance($inId);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmMovieMessageHistory
 * @category mofilmMovieMessageHistory
 */
class mofilmMovieMessageHistory implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmMovieMessageHistory
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
	 * Stores $_Id
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Id;

	/**
	 * Stores $_MovieID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;

	/**
	 * Stores $_MessageID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MessageID;

	/**
	 * Stores $_SenderID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SenderID;

	/**
	 * Stores $_RecipientID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_RecipientID;

	/**
	 * Stores $_SendDate
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_SendDate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;
	
	/**
	 * Stores $_Message
	 *
	 * @var mofilmUserPrivateMessage
	 * @access protected
	 */
	protected $_Message;
	
	
	
	/**
	 * Returns a new instance of mofilmMovieMessageHistory
	 *
	 * @param integer $inId
	 * @return mofilmMovieMessageHistory
	 */
	function __construct($inId = null) {
		$this->reset();
		if ( $inId !== null ) {
			$this->setId($inId);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new mofilmMovieMessageHistory containing non-unique properties
	 *
	 * @param integer $inSenderID
	 * @param integer $inRecipientID
	 * @param datetime $inSendDate
	 * @return mofilmMovieMessageHistory
	 * @static
	 */
	public static function factory($inSenderID = null, $inRecipientID = null, $inSendDate = null) {
		$oObject = new mofilmMovieMessageHistory;
		if ( $inSenderID !== null ) {
			$oObject->setSenderID($inSenderID);
		}
		if ( $inRecipientID !== null ) {
			$oObject->setRecipientID($inRecipientID);
		}
		if ( $inSendDate !== null ) {
			$oObject->setSendDate($inSendDate);
		}
		return $oObject;
	}
	
	/**
	 * Creates a new object from the PM and optional movieID
	 * 
	 * @param mofilmUserPrivateMessage $inMessage
	 * @param integer $inMovieID
	 * @return mofilmMovieMessageHistory
	 * @static
	 */
	public static function factoryFromPrivateMessage(mofilmUserPrivateMessage $inMessage, $inMovieID = null) {
		$oObject = new mofilmMovieMessageHistory();
		$oObject->setMessageID($inMessage->getMessageID());
		$oObject->setMovieID($inMovieID);
		$oObject->setRecipientID($inMessage->getToUserID());
		$oObject->setSenderID($inMessage->getFromUserID());
		return $oObject;
	}

	/**
	 * Get an instance of mofilmMovieMessageHistory by primary key
	 *
	 * @param integer $inId
	 * @return mofilmMovieMessageHistory
	 * @static
	 */
	public static function getInstance($inId) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inId]) ) {
			return self::$_Instances[$inId];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmMovieMessageHistory();
		$oObject->setId($inId);
		if ( $oObject->load() ) {
			self::$_Instances[$inId] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of mofilmMovieMessageHistory by unique key (uniqMovieMessage)
	 *
	 * @param integer $inMovieID
	 * @param integer $inMessageID
	 * @return mofilmMovieMessageHistory
	 * @static
	 */
	public static function getInstanceByUniqMovieMessage($inMovieID, $inMessageID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMovieID.'.'.$inMessageID]) ) {
			return self::$_Instances[$inMovieID.'.'.$inMessageID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmMovieMessageHistory();
		$oObject->setMovieID($inMovieID);
		$oObject->setMessageID($inMessageID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMovieID.'.'.$inMessageID] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmMovieMessageHistory
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inMovieID
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inMovieID = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieMessageHistory WHERE 1';
		if ( $inMovieID !== null ) {
			$query .= ' AND movieID = '.dbManager::getInstance()->quote($inMovieID).' ORDER BY sendDate DESC';
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMovieMessageHistory();
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
	 * Returns true if the message is attached to a movie
	 * 
	 * @param integer $inMessageID
	 * @return boolean
	 * @static
	 */
	public static function isMessageAttachedToMovie($inMessageID) {
		$return = false;
		$query = '
			SELECT movieID
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieMessageHistory
			 WHERE messageID = :MessageID
			 LIMIT 1';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':MessageID', $inMessageID, PDO::PARAM_INT);
		if ( $oStmt->execute() ) {
			$movieID = $oStmt->fetchColumn();
			if ( $movieID && is_numeric($movieID) && $movieID > 0 ) {
				$return = true;
			}
		}
		$oStmt->closeCursor();
		return $return;
	}

	/**
	 * Returns true if the message is attached to a movie
	 * 
	 * @param integer $inMessageID
	 * @return integer
	 * @static
	 */
	public static function getMovieFromMessageID($inMessageID) {
		$return = false;
		$query = '
			SELECT movieID
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieMessageHistory
			 WHERE messageID = :MessageID
			 LIMIT 1';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':MessageID', $inMessageID, PDO::PARAM_INT);
		if ( $oStmt->execute() ) {
			$movieID = $oStmt->fetchColumn();
			if ( $movieID && is_numeric($movieID) && $movieID > 0 ) {
				$return = $movieID;
			}
		}
		$oStmt->closeCursor();
		return $return;
	}



	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT id, movieID, messageID, senderID, recipientID, sendDate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieMessageHistory';

		$where = array();
		if ( $this->_Id !== 0 ) {
			$where[] = ' id = :Id ';
		}
		if ( $this->_MovieID !== 0 ) {
			$where[] = ' movieID = :MovieID ';
		}
		if ( $this->_MessageID !== 0 ) {
			$where[] = ' messageID = :MessageID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_Id !== 0 ) {
				$oStmt->bindValue(':Id', $this->_Id);
			}
			if ( $this->_MovieID !== 0 ) {
				$oStmt->bindValue(':MovieID', $this->_MovieID);
			}
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
		$this->setId((int)$inArray['id']);
		$this->setMovieID((int)$inArray['movieID']);
		$this->setMessageID((int)$inArray['messageID']);
		$this->setSenderID((int)$inArray['senderID']);
		$this->setRecipientID((int)$inArray['recipientID']);
		$this->setSendDate($inArray['sendDate']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieMessageHistory
					( id, movieID, messageID, senderID, recipientID, sendDate)
				VALUES
					(:Id, :MovieID, :MessageID, :SenderID, :RecipientID, :SendDate)
				ON DUPLICATE KEY UPDATE
					senderID=VALUES(senderID),
					recipientID=VALUES(recipientID),
					sendDate=VALUES(sendDate)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':Id', $this->_Id);
					$oStmt->bindValue(':MovieID', $this->_MovieID);
					$oStmt->bindValue(':MessageID', $this->_MessageID);
					$oStmt->bindValue(':SenderID', $this->_SenderID);
					$oStmt->bindValue(':RecipientID', $this->_RecipientID);
					$oStmt->bindValue(':SendDate', $this->_SendDate);

					if ( $oStmt->execute() ) {
						if ( !$this->getId() ) {
							$this->setId($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieMessageHistory
			WHERE
				id = :Id
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':Id', $this->_Id);

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
	 * @return mofilmMovieMessageHistory
	 */
	function reset() {
		$this->_Id = 0;
		$this->_MovieID = 0;
		$this->_MessageID = 0;
		$this->_SenderID = 0;
		$this->_RecipientID = 0;
		$this->_SendDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Message = null;
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
		$string .= " Id[$this->_Id] $newLine";
		$string .= " MovieID[$this->_MovieID] $newLine";
		$string .= " MessageID[$this->_MessageID] $newLine";
		$string .= " SenderID[$this->_SenderID] $newLine";
		$string .= " RecipientID[$this->_RecipientID] $newLine";
		$string .= " SendDate[$this->_SendDate] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmMovieMessageHistory';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"Id\" value=\"$this->_Id\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"MovieID\" value=\"$this->_MovieID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"MessageID\" value=\"$this->_MessageID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"SenderID\" value=\"$this->_SenderID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"RecipientID\" value=\"$this->_RecipientID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"SendDate\" value=\"$this->_SendDate\" type=\"datetime\" /> $newLine";
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
			$valid = $this->checkId($message);
		}
		if ( $valid ) {
			$valid = $this->checkMovieID($message);
		}
		if ( $valid ) {
			$valid = $this->checkMessageID($message);
		}
		if ( $valid ) {
			$valid = $this->checkSenderID($message);
		}
		if ( $valid ) {
			$valid = $this->checkRecipientID($message);
		}
		if ( $valid ) {
			$valid = $this->checkSendDate($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_Id has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkId(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Id) && $this->_Id !== 0 ) {
			$inMessage .= "{$this->_Id} is not a valid value for Id";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_MovieID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMovieID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MovieID) && $this->_MovieID !== 0 ) {
			$inMessage .= "{$this->_MovieID} is not a valid value for MovieID";
			$isValid = false;
		}
		return $isValid;
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
	 * Checks that $_SenderID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkSenderID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_SenderID) && $this->_SenderID !== 0 ) {
			$inMessage .= "{$this->_SenderID} is not a valid value for SenderID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_RecipientID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkRecipientID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_RecipientID) && $this->_RecipientID !== 0 ) {
			$inMessage .= "{$this->_RecipientID} is not a valid value for RecipientID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_SendDate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkSendDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_SendDate) && $this->_SendDate !== '' ) {
			$inMessage .= "{$this->_SendDate} is not a valid value for SendDate";
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
	 * @return mofilmMovieMessageHistory
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
		return $this->_Id;
	}

	/**
	 * Return value of $_Id
	 *
	 * @return integer
	 * @access public
	 */
	function getId() {
		return $this->_Id;
	}

	/**
	 * Set $_Id to Id
	 *
	 * @param integer $inId
	 * @return mofilmMovieMessageHistory
	 * @access public
	 */
	function setId($inId) {
		if ( $inId !== $this->_Id ) {
			$this->_Id = $inId;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_MovieID
	 *
	 * @return integer
	 * @access public
	 */
	function getMovieID() {
		return $this->_MovieID;
	}

	/**
	 * Set $_MovieID to MovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieMessageHistory
	 * @access public
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
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
	 * @return mofilmMovieMessageHistory
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
	 * Return value of $_SenderID
	 *
	 * @return integer
	 * @access public
	 */
	function getSenderID() {
		return $this->_SenderID;
	}
	
	/**
	 * Returns the mofilmUser object for sender
	 * 
	 * @return mofilmUser
	 */
	function getSender() {
		return mofilmUserManager::getInstanceByID($this->getSenderID());
	}

	/**
	 * Set $_SenderID to SenderID
	 *
	 * @param integer $inSenderID
	 * @return mofilmMovieMessageHistory
	 * @access public
	 */
	function setSenderID($inSenderID) {
		if ( $inSenderID !== $this->_SenderID ) {
			$this->_SenderID = $inSenderID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_RecipientID
	 *
	 * @return integer
	 * @access public
	 */
	function getRecipientID() {
		return $this->_RecipientID;
	}
	
	/**
	 * Returns the mofilmUser recipient
	 * 
	 * @return mofilmUser
	 */
	function getRecipient() {
		return mofilmUserManager::getInstanceByID($this->getRecipientID());
	}
	
	/**
	 * Set $_RecipientID to RecipientID
	 *
	 * @param integer $inRecipientID
	 * @return mofilmMovieMessageHistory
	 * @access public
	 */
	function setRecipientID($inRecipientID) {
		if ( $inRecipientID !== $this->_RecipientID ) {
			$this->_RecipientID = $inRecipientID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_SendDate
	 *
	 * @return datetime
	 * @access public
	 */
	function getSendDate() {
		return $this->_SendDate;
	}

	/**
	 * Set $_SendDate to SendDate
	 *
	 * @param datetime $inSendDate
	 * @return mofilmMovieMessageHistory
	 * @access public
	 */
	function setSendDate($inSendDate) {
		if ( $inSendDate !== $this->_SendDate ) {
			$this->_SendDate = $inSendDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Message
	 *
	 * @return mofilmUserPrivateMessage
	 */
	function getMessage() {
		if ( !$this->_Message instanceof mofilmUserPrivateMessage ) {
			$this->_Message = new mofilmUserPrivateMessage($this->getMessageID());
		}
		return $this->_Message;
	}
	
	/**
	 * Set $_Message to $inMessage
	 *
	 * @param mofilmUserPrivateMessage $inMessage
	 * @return mofilmMovieMessageHistory
	 */
	function setMessage(mofilmUserPrivateMessage $inMessage) {
		$this->_Message = $inMessage;
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
	 * @return mofilmMovieMessageHistory
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}