<?php
/**
 * commsApplicationMessage
 *
 * Stored in commsApplicationMessage.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsApplicationMessage
 * @category commsApplicationMessage
 * @version $Rev: 106 $
 */


/**
 * commsApplicationMessage Class
 *
 * Provides access to records in comms.applicationMessages
 *
 * Creating a new record:
 * <code>
 * $oCommsApplicationMessage = new commsApplicationMessage();
 * $oCommsApplicationMessage->setMessageID($inMessageID);
 * $oCommsApplicationMessage->setApplicationID($inApplicationID);
 * $oCommsApplicationMessage->setOutboundTypeID($inOutboundTypeID);
 * $oCommsApplicationMessage->setMessageGroupID($inMessageGroupID);
 * $oCommsApplicationMessage->setLanguage($inLanguage);
 * $oCommsApplicationMessage->setMessageHeader($inMessageHeader);
 * $oCommsApplicationMessage->setMessageBody($inMessageBody);
 * $oCommsApplicationMessage->setIsHtml($inIsHtml);
 * $oCommsApplicationMessage->setDelay($inDelay);
 * $oCommsApplicationMessage->setMessageOrder($inMessageOrder);
 * $oCommsApplicationMessage->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsApplicationMessage = new commsApplicationMessage($inMessageID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsApplicationMessage = new commsApplicationMessage();
 * $oCommsApplicationMessage->setMessageID($inMessageID);
 * $oCommsApplicationMessage->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsApplicationMessage = commsApplicationMessage::getInstance($inMessageID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage commsApplicationMessage
 * @category commsApplicationMessage
 */
class commsApplicationMessage implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsApplicationMessage
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
	 * Stores $_ApplicationID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ApplicationID;

	/**
	 * Stores $_OutboundTypeID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_OutboundTypeID;

	/**
	 * Stores $_MessageGroupID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MessageGroupID;
	
	/**
	 * Stores $_CurrencyID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_CurrencyID;
	
	/**
	 * Stores $_Charge
	 *
	 * @var decimal
	 * @access protected
	 */
	protected $_Charge;
	
	/**
	 * Stores $_Language
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Language;

	/**
	 * Stores $_MessageHeader
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_MessageHeader;

	/**
	 * Stores $_MessageBody
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_MessageBody;

	/**
	 * Stores $_IsHtml
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_IsHtml;

	/**
	 * Stores $_Delay
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Delay;

	/**
	 * Stores $_MessageOrder
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MessageOrder;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of commsApplicationMessage
	 *
	 * @param integer $inMessageID
	 * @return commsApplicationMessage
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
	 * Creates a new commsApplicationMessage containing non-unique properties
	 *
	 * @param string $inLanguage
	 * @param string $inMessageHeader
	 * @param string $inMessageBody
	 * @param integer $inIsHtml
	 * @param integer $inDelay
	 * @return commsApplicationMessage
	 * @static
	 */
	public static function factory($inLanguage = null, $inMessageHeader = null, $inMessageBody = null, $inIsHtml = null, $inDelay = null) {
		$oObject = new commsApplicationMessage;
		if ( $inLanguage !== null ) {
			$oObject->setLanguage($inLanguage);
		}
		if ( $inMessageHeader !== null ) {
			$oObject->setMessageHeader($inMessageHeader);
		}
		if ( $inMessageBody !== null ) {
			$oObject->setMessageBody($inMessageBody);
		}
		if ( $inIsHtml !== null ) {
			$oObject->setIsHtml($inIsHtml);
		}
		if ( $inDelay !== null ) {
			$oObject->setDelay($inDelay);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsApplicationMessage by primary key
	 *
	 * @param integer $inMessageID
	 * @return commsApplicationMessage
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
		$oObject = new commsApplicationMessage();
		$oObject->setMessageID($inMessageID);
		if ( $oObject->load() ) {
			self::$_Instances[$inMessageID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of commsApplicationMessage by unique key (uniqueMessage)
	 *
	 * @param integer $inApplicationID
	 * @param integer $inOutboundTypeID
	 * @param integer $inMessageGroupID
	 * @param integer $inMessageOrder
	 * @return commsApplicationMessage
	 * @static
	 */
	public static function getInstanceByUniqueMessage($inApplicationID, $inOutboundTypeID, $inMessageGroupID, $inMessageOrder, $inLanguage) {
		$instanceKey = $inApplicationID.'.'.$inOutboundTypeID.'.'.$inMessageGroupID.'.'.$inMessageOrder.'.'.$inLanguage;
		
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$instanceKey]) ) {
			return self::$_Instances[$instanceKey];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsApplicationMessage();
		$oObject->setApplicationID($inApplicationID);
		$oObject->setOutboundTypeID($inOutboundTypeID);
		$oObject->setMessageGroupID($inMessageGroupID);
		$oObject->setMessageOrder($inMessageOrder);
		$oObject->setLanguage($inLanguage);
		if ( $oObject->load() ) {
			self::$_Instances[$instanceKey] = $oObject;
		}
		return $oObject;
	}
	
	/**
	 * Returns an array of messages for the application and group in the required order
	 * 
	 * @param integer $inApplicationID
	 * @param integer $inMessageGroupID
	 * @param string $inLanguage
	 * @return array
	 * @static
	 */
	public static function getApplicationMessagesByGroup($inApplicationID, $inMessageGroupID, $inLanguage = 'en') {
		if ( (!$inApplicationID && $inApplicationID !== 0) && !$inMessageGroupID ) {
			throw new commsException(__CLASS__.'::'.__METHOD__.'() requires applicationID and messageGroupID');
		}
		
		$query = '
			SELECT * FROM '.system::getConfig()->getDatabase('comms').'.applicationMessages
			 WHERE applicationID = :ApplicationID
			   AND messageGroupID = :MessageGroupID
			   AND language = :Language
			 ORDER BY messageOrder ASC';

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ApplicationID', $inApplicationID, PDO::PARAM_INT);
			$oStmt->bindValue(':MessageGroupID', $inMessageGroupID, PDO::PARAM_INT);
			$oStmt->bindValue(':Language', $inLanguage, PDO::PARAM_STR);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsApplicationMessage();
					$oObject->loadFromArray($row);
					$list[] = $oObject;
				}
			}
			
			if ( count($list) == 0 ) {
				$oStmt->bindValue(':ApplicationID', 0, PDO::PARAM_INT);
				$oStmt->bindValue(':Language', 'en', PDO::PARAM_STR);
				if ( $oStmt->execute() ) {
					foreach ( $oStmt as $row ) {
						$oObject = new commsApplicationMessage();
						$oObject->loadFromArray($row);
						$list[] = $oObject;
					}
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
	 * Returns an array of objects of commsApplicationMessage, optionally for an application / message group
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inApplicationID (optional) get message for the specified app
	 * @param integer $inMessageGroupID (optional) get messages in this group
	 * @param string $inLanguage (optional) get message only in this language
	 * @param integer $inOutboundTypeID (optional) get only messages of this type
	 * @param boolean $inOrder (optional) orders messages by messageHeader ascending (A-Z)
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inApplicationID = null, $inMessageGroupID = null, $inLanguage = null, $inOutboundTypeID = null, $inOrder = false) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.applicationMessages WHERE 1';
		if ( $inApplicationID !== null ) {
			$query .= ' AND applicationID = '.dbManager::getInstance()->quote($inApplicationID);
		}
		if ( $inMessageGroupID !== null ) {
			$query .= ' AND messageGroupID = '.dbManager::getInstance()->quote($inMessageGroupID);
		}
		if ( $inLanguage !== null ) {
			$query .= ' AND language = '.dbManager::getInstance()->quote($inLanguage);
		}
		if ( $inOutboundTypeID !== null ) {
			$query .= ' AND outboundTypeID = '.dbManager::getInstance()->quote($inOutboundTypeID);
		}
		if ( $inOrder === true ) {
			$query .= ' ORDER BY messageHeader ASC ';
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsApplicationMessage();
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
			SELECT messageID, applicationID, outboundTypeID, messageGroupID, currencyID, charge, language, messageHeader, messageBody, isHtml, delay, messageOrder
			  FROM '.system::getConfig()->getDatabase('comms').'.applicationMessages';

		$where = array();
		if ( $this->_MessageID !== 0 ) {
			$where[] = ' messageID = :MessageID ';
		}
		
		if ( $this->_ApplicationID !== 0 ) {
			$where[] = ' applicationID = :ApplicationID ';
		}
		if ( $this->_OutboundTypeID !== 0 ) {
			$where[] = ' outboundTypeID = :OutboundTypeID ';
		}
		if ( $this->_MessageGroupID !== 0 ) {
			$where[] = ' messageGroupID = :MessageGroupID ';
		}
		if ( $this->_MessageOrder !== 0 ) {
			$where[] = ' messageOrder = :MessageOrder ';
		}
		if ( $this->_Language !== '' ) {
			$where[] = ' language = :Language ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_MessageID !== 0 ) {
				$oStmt->bindValue(':MessageID', $this->_MessageID);
			}
			
			if ( $this->_ApplicationID !== 0 ) {
				$oStmt->bindValue(':ApplicationID', $this->_ApplicationID);
			}
			if ( $this->_OutboundTypeID !== 0 ) {
				$oStmt->bindValue(':OutboundTypeID', $this->_OutboundTypeID);
			}
			if ( $this->_MessageGroupID !== 0 ) {
				$oStmt->bindValue(':MessageGroupID', $this->_MessageGroupID);
			}
			if ( $this->_MessageOrder !== 0 ) {
				$oStmt->bindValue(':MessageOrder', $this->_MessageOrder);
			}
			if ( $this->_Language !== '' ) {
				$oStmt->bindValue(':Language', $this->_Language);
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
		$this->setApplicationID((int)$inArray['applicationID']);
		$this->setOutboundTypeID((int)$inArray['outboundTypeID']);
		$this->setMessageGroupID((int)$inArray['messageGroupID']);
		$this->setCurrencyID((int)$inArray['currencyID']);
		$this->setCharge((float)$inArray['charge']);
		$this->setLanguage($inArray['language']);
		$this->setMessageHeader($inArray['messageHeader']);
		$this->setMessageBody($inArray['messageBody']);
		$this->setIsHtml((int)$inArray['isHtml']);
		$this->setDelay((int)$inArray['delay']);
		$this->setMessageOrder((int)$inArray['messageOrder']);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.applicationMessages
					( messageID, applicationID, outboundTypeID, messageGroupID, currencyID, charge, language, messageHeader, messageBody, isHtml, delay, messageOrder)
				VALUES
					(:MessageID, :ApplicationID, :OutboundTypeID, :MessageGroupID, :CurrencyID, :Charge, :Language, :MessageHeader, :MessageBody, :IsHtml, :Delay, :MessageOrder)
				ON DUPLICATE KEY UPDATE
					applicationID=VALUES(applicationID),
					outboundTypeID=VALUES(outboundTypeID),
					messageGroupID=VALUES(messageGroupID),
					currencyID=VALUES(currencyID),
					charge=VALUES(charge),
					language=VALUES(language),
					messageHeader=VALUES(messageHeader),
					messageBody=VALUES(messageBody),
					isHtml=VALUES(isHtml),
					delay=VALUES(delay),
					messageOrder=VALUES(messageOrder)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MessageID', $this->_MessageID);
					$oStmt->bindValue(':ApplicationID', $this->_ApplicationID);
					$oStmt->bindValue(':OutboundTypeID', $this->_OutboundTypeID);
					$oStmt->bindValue(':MessageGroupID', $this->_MessageGroupID);
					$oStmt->bindValue(':CurrencyID', $this->_CurrencyID);
					$oStmt->bindValue(':Charge', $this->_Charge);
					$oStmt->bindValue(':Language', $this->_Language);
					$oStmt->bindValue(':MessageHeader', $this->_MessageHeader);
					$oStmt->bindValue(':MessageBody', $this->_MessageBody);
					$oStmt->bindValue(':IsHtml', $this->_IsHtml);
					$oStmt->bindValue(':Delay', $this->_Delay);
					$oStmt->bindValue(':MessageOrder', $this->_MessageOrder);
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
	 * Deletes the object from the table
	 *
	 * @return boolean
	 */
	function delete() {
		$query = '
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.applicationMessages
			WHERE
				messageID = :MessageID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MessageID', $this->_MessageID);

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
	 * @return commsApplicationMessage
	 */
	function reset() {
		$this->_MessageID = 0;
		$this->_ApplicationID = 0;
		$this->_OutboundTypeID = 0;
		$this->_MessageGroupID = 0;
		$this->_CurrencyID = 0;
		$this->_Charge = 0.000;
		$this->_Language = '';
		$this->_MessageHeader = null;
		$this->_MessageBody = '';
		$this->_IsHtml = 0;
		$this->_Delay = 0;
		$this->_MessageOrder = 0;
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
		$string .= " MessageID[$this->_MessageID] $newLine";
		$string .= " ApplicationID[$this->_ApplicationID] $newLine";
		$string .= " OutboundTypeID[$this->_OutboundTypeID] $newLine";
		$string .= " MessageGroupID[$this->_MessageGroupID] $newLine";
		$string .= " CurrencyID[$this->_CurrencyID] $newLine";
		$string .= " Charge[$this->_Charge] $newLine";
		$string .= " Language[$this->_Language] $newLine";
		$string .= " MessageHeader[$this->_MessageHeader] $newLine";
		$string .= " MessageBody[$this->_MessageBody] $newLine";
		$string .= " IsHtml[$this->_IsHtml] $newLine";
		$string .= " Delay[$this->_Delay] $newLine";
		$string .= " MessageOrder[$this->_MessageOrder] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsApplicationMessage';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"MessageID\" value=\"$this->_MessageID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ApplicationID\" value=\"$this->_ApplicationID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"OutboundTypeID\" value=\"$this->_OutboundTypeID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"MessageGroupID\" value=\"$this->_MessageGroupID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CurrencyID\" value=\"$this->_CurrencyID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Charge\" value=\"$this->_Charge\" type=\"decimal\" /> $newLine";
		$xml .= "\t<property name=\"Language\" value=\"$this->_Language\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"MessageHeader\" value=\"$this->_MessageHeader\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"MessageBody\" value=\"$this->_MessageBody\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"IsHtml\" value=\"$this->_IsHtml\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Delay\" value=\"$this->_Delay\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"MessageOrder\" value=\"$this->_MessageOrder\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkApplicationID($message);
		}
		if ( $valid ) {
			$valid = $this->checkOutboundTypeID($message);
		}
		if ( $valid ) {
			$valid = $this->checkMessageGroupID($message);
		}
		if ( $valid ) {
			$valid = $this->checkLanguage($message);
		}
		if ( $valid ) {
			$valid = $this->checkMessageHeader($message);
		}
		if ( $valid ) {
			$valid = $this->checkMessageBody($message);
		}
		if ( $valid ) {
			$valid = $this->checkIsHtml($message);
		}
		if ( $valid ) {
			$valid = $this->checkDelay($message);
		}
		if ( $valid ) {
			$valid = $this->checkMessageOrder($message);
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
	 * Checks that $_ApplicationID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkApplicationID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ApplicationID) && $this->_ApplicationID !== 0 ) {
			$inMessage .= "{$this->_ApplicationID} is not a valid value for ApplicationID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_OutboundTypeID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkOutboundTypeID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_OutboundTypeID) && $this->_OutboundTypeID < 1 ) {
			$inMessage .= "{$this->_OutboundTypeID} is not a valid value for OutboundTypeID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_MessageGroupID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMessageGroupID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MessageGroupID) && $this->_MessageGroupID < 1 ) {
			$inMessage .= "{$this->_MessageGroupID} is not a valid value for MessageGroupID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Language has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkLanguage(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Language) && $this->_Language !== '' ) {
			$inMessage .= "{$this->_Language} is not a valid value for Language";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Language) > 10 ) {
			$inMessage .= "Language cannot be more than 10 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Language) <= 1 ) {
			$inMessage .= "Language must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_MessageHeader has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMessageHeader(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_MessageHeader) && $this->_MessageHeader !== null && $this->_MessageHeader !== '' ) {
			$inMessage .= "{$this->_MessageHeader} is not a valid value for MessageHeader";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_MessageHeader) > 255 ) {
			$inMessage .= "MessageHeader cannot be more than 255 characters";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_MessageBody has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMessageBody(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_MessageBody) && $this->_MessageBody !== '' ) {
			$inMessage .= "{$this->_MessageBody} is not a valid value for MessageBody";
			$isValid = false;
		}
		if ( strlen($this->_MessageBody) > 64000 ) {
			$inMessage .= 'The message body is too long; max length is 64000 characters, ~64KB';
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_IsHtml has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkIsHtml(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_IsHtml) && $this->_IsHtml !== 0 ) {
			$inMessage .= "{$this->_IsHtml} is not a valid value for IsHtml";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Delay has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkDelay(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Delay) && $this->_Delay !== 0 ) {
			$inMessage .= "{$this->_Delay} is not a valid value for Delay";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_MessageOrder has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMessageOrder(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MessageOrder) && $this->_MessageOrder !== 0 ) {
			$inMessage .= "{$this->_MessageOrder} is not a valid value for MessageOrder";
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
	 * @return commsApplicationMessage
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
	 * @return commsApplicationMessage
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
	 * Return value of $_ApplicationID
	 *
	 * @return integer
	 * @access public
	 */
	function getApplicationID() {
		return $this->_ApplicationID;
	}

	/**
	 * Set $_ApplicationID to ApplicationID
	 *
	 * @param integer $inApplicationID
	 * @return commsApplicationMessage
	 * @access public
	 */
	function setApplicationID($inApplicationID) {
		if ( $inApplicationID !== $this->_ApplicationID ) {
			$this->_ApplicationID = $inApplicationID;
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
	 * Returns the outbound type object
	 * 
	 * @return commsOutboundType
	 */
	function getOutboundType() {
		return commsOutboundType::getInstance($this->getOutboundTypeID());
	}

	/**
	 * Set $_OutboundTypeID to OutboundTypeID
	 *
	 * @param integer $inOutboundTypeID
	 * @return commsApplicationMessage
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
	 * Return value of $_MessageGroupID
	 *
	 * @return integer
	 * @access public
	 */
	function getMessageGroupID() {
		return $this->_MessageGroupID;
	}
	
	/**
	 * Returns the message group object
	 * 
	 * @return commsApplicationMessageGroup
	 */
	function getMessageGroup() {
		return commsApplicationMessageGroup::getInstance($this->getMessageGroupID());
	}

	/**
	 * Set $_MessageGroupID to MessageGroupID
	 *
	 * @param integer $inMessageGroupID
	 * @return commsApplicationMessage
	 * @access public
	 */
	function setMessageGroupID($inMessageGroupID) {
		if ( $inMessageGroupID !== $this->_MessageGroupID ) {
			$this->_MessageGroupID = $inMessageGroupID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_CurrencyID
	 *
	 * @return integer
	 */
	function getCurrencyID() {
		return $this->_CurrencyID;
	}
	
	/**
	 * Set $_CurrencyID to $inCurrencyID
	 *
	 * @param integer $inCurrencyID
	 * @return commsApplicationMessage
	 */
	function setCurrencyID($inCurrencyID) {
		if ( $inCurrencyID !== $this->_CurrencyID ) {
			$this->_CurrencyID = $inCurrencyID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Charge
	 *
	 * @return decimal
	 */
	function getCharge() {
		return $this->_Charge;
	}
	
	/**
	 * Set $_Charge to $inCharge
	 *
	 * @param decimal $inCharge
	 * @return commsApplicationMessage
	 */
	function setCharge($inCharge) {
		if ( $inCharge !== $this->_Charge ) {
			$this->_Charge = $inCharge;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Language
	 *
	 * @return string
	 * @access public
	 */
	function getLanguage() {
		return $this->_Language;
	}

	/**
	 * Set $_Language to Language
	 *
	 * @param string $inLanguage
	 * @return commsApplicationMessage
	 * @access public
	 */
	function setLanguage($inLanguage) {
		if ( $inLanguage !== $this->_Language ) {
			$this->_Language = $inLanguage;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_MessageHeader
	 *
	 * @return string
	 * @access public
	 */
	function getMessageHeader() {
		return $this->_MessageHeader;
	}

	/**
	 * Set $_MessageHeader to MessageHeader
	 *
	 * @param string $inMessageHeader
	 * @return commsApplicationMessage
	 * @access public
	 */
	function setMessageHeader($inMessageHeader) {
		if ( $inMessageHeader !== $this->_MessageHeader ) {
			$this->_MessageHeader = $inMessageHeader;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_MessageBody
	 *
	 * @return string
	 * @access public
	 */
	function getMessageBody() {
		return $this->_MessageBody;
	}

	/**
	 * Set $_MessageBody to MessageBody
	 *
	 * @param string $inMessageBody
	 * @return commsApplicationMessage
	 * @access public
	 */
	function setMessageBody($inMessageBody) {
		if ( $inMessageBody !== $this->_MessageBody ) {
			$this->_MessageBody = $inMessageBody;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_IsHtml
	 *
	 * @return integer
	 * @access public
	 */
	function getIsHtml() {
		return $this->_IsHtml;
	}

	/**
	 * Set $_IsHtml to IsHtml
	 *
	 * @param integer $inIsHtml
	 * @return commsApplicationMessage
	 * @access public
	 */
	function setIsHtml($inIsHtml) {
		if ( $inIsHtml !== $this->_IsHtml ) {
			$this->_IsHtml = $inIsHtml;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Delay
	 *
	 * @return integer
	 * @access public
	 */
	function getDelay() {
		return $this->_Delay;
	}

	/**
	 * Set $_Delay to Delay
	 *
	 * @param integer $inDelay
	 * @return commsApplicationMessage
	 * @access public
	 */
	function setDelay($inDelay) {
		if ( $inDelay !== $this->_Delay ) {
			$this->_Delay = $inDelay;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_MessageOrder
	 *
	 * @return integer
	 * @access public
	 */
	function getMessageOrder() {
		return $this->_MessageOrder;
	}

	/**
	 * Set $_MessageOrder to MessageOrder
	 *
	 * @param integer $inMessageOrder
	 * @return commsApplicationMessage
	 * @access public
	 */
	function setMessageOrder($inMessageOrder) {
		if ( $inMessageOrder !== $this->_MessageOrder ) {
			$this->_MessageOrder = $inMessageOrder;
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
	 * @return commsApplicationMessage
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}