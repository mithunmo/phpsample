<?php
/**
 * mofilmCommsNewsletter
 *
 * Stored in mofilmCommsNewsletter.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletter
 * @category mofilmCommsNewsletter
 * @version $Rev: 806 $
 */


/**
 * mofilmCommsNewsletter Class
 *
 * Provides access to records in mofilm_comms.newsLetter
 *
 * Creating a new record:
 * <code>
 * $oMofilmCommsNewsletter = new mofilmCommsNewsletter();
 * $oMofilmCommsNewsletter->setNlid($inID);
 * $oMofilmCommsNewsletter->setName($inName);
 * $oMofilmCommsNewsletter->setOutboundTypeId($inOutboundTypeId);
 * $oMofilmCommsNewsletter->setLanguage($inLanguage);
 * $oMofilmCommsNewsletter->setMessageSubject($inMessageSubject);
 * $oMofilmCommsNewsletter->setMessageBody($inMessageBody);
 * $oMofilmCommsNewsletter->setMessageText($inMessageText);
 * $oMofilmCommsNewsletter->setIsHtml($inIsHtml);
 * $oMofilmCommsNewsletter->setCreateDate($inCreateDate);
 * $oMofilmCommsNewsletter->setUpdateDate($inUpdateDate);
 * $oMofilmCommsNewsletter->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCommsNewsletter = new mofilmCommsNewsletter($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmCommsNewsletter = new mofilmCommsNewsletter();
 * $oMofilmCommsNewsletter->setNlid($inID);
 * $oMofilmCommsNewsletter->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmCommsNewsletter = mofilmCommsNewsletter::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmCommsNewsletter
 * @category mofilmCommsNewsletter
 */
class mofilmCommsNewsletter implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmCommsNewsletter
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
	 * Stores $_Name
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Name;

	/**
	 * Stores $_OutboundTypeId
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_OutboundTypeId;

	/**
	 * Stores $_Language
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Language;

	/**
	 * Stores $_MessageSubject
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_MessageSubject;

	/**
	 * Stores $_MessageBody
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_MessageBody;

	/**
	 * Stores $_MessageText
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_MessageText;

	/**
	 * Stores $_IsHtml
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_IsHtml;

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
	 * stores $_NewsletterType
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_NewsletterType;

		/**
	 * Returns a new instance of mofilmCommsNewsletter
	 *
	 * @param integer $inID
	 * @return mofilmCommsNewsletter
	 */
	function __construct($inID = null) {
		$this->reset();
		if ( $inID !== null ) {
			$this->setNlid($inID);
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
	 * Get an instance of mofilmCommsNewsletter by primary key
	 *
	 * @param integer $inID
	 * @return mofilmCommsNewsletter
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
		$oObject = new mofilmCommsNewsletter();
		$oObject->setNlid($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Unloads and destroys a newsletter instance matching $inId
	 *
	 * @param integer $inId
	 * @return void
	 * @static
	 */
	public static function destroy($inId) {

		if ( isset(self::$_Instances[$inId]) ) {
			self::$_Instances[$inId] = null;
			unset(self::$_Instances[$inId]);
		}
	}
	
	/**
	 * Returns an array of objects of mofilmCommsNewsletter
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
			SELECT ID, name, outboundTypeId, language, messageSubject, messageBody, messageText, isHtml, newsletterType, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsLetter
			 WHERE 1';
				
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmCommsNewsletter();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

	/**
	 * Returns an array of objects of mofilmCommsNewsletter
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inNewsletterType
	 * @return array
	 * @static
	 */
	public static function listOfObjectsByType($inOffset = null, $inLimit = 30, $inNewsletterType) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, name, outboundTypeId, language, messageSubject, messageBody, messageText, isHtml, newsletterType, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsLetter ';
		
		if ( $inNewsletterType != "" ) {
			$query.= ' WHERE newsletterType = '.$inNewsletterType;
		}
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmCommsNewsletter();
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
			SELECT ID, name, outboundTypeId, language, messageSubject, messageBody, messageText, isHtml, newsletterType, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsLetter';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $this->_ID !== 0 ) {
			$oStmt->bindValue(':ID', $this->getNlid());
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
		$this->setNlid((int)$inArray['ID']);
		$this->setName($inArray['name']);
		$this->setOutboundTypeId((int)$inArray['outboundTypeId']);
		$this->setLanguage($inArray['language']);
		$this->setMessageSubject($inArray['messageSubject']);
		$this->setMessageBody($inArray['messageBody']);
		$this->setMessageText($inArray['messageText']);
		$this->setIsHtml((int)$inArray['isHtml']);
		$this->setNewsletterType((int)$inArray['newsletterType']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.newsLetter
					( ID, name, outboundTypeId, language, messageSubject, messageBody, messageText, isHtml, newsletterType, createDate, updateDate)
				VALUES
					(:ID, :Name, :OutboundTypeId, :Language, :MessageSubject, :MessageBody, :MessageText, :IsHtml, :NewsletterType, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					name=VALUES(name),
					outboundTypeId=VALUES(outboundTypeId),
					language=VALUES(language),
					messageSubject=VALUES(messageSubject),
					messageBody=VALUES(messageBody),
					messageText=VALUES(messageText),
					isHtml=VALUES(isHtml),
					newsletterType=VALUES(newsletterType),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getNlid());
				$oStmt->bindValue(':Name', $this->getName());
				$oStmt->bindValue(':OutboundTypeId', $this->getOutboundTypeId());
				$oStmt->bindValue(':Language', $this->getLanguage());
				$oStmt->bindValue(':MessageSubject', $this->getMessageSubject());
				$oStmt->bindValue(':MessageBody', $this->getMessageBody());
				$oStmt->bindValue(':MessageText', $this->getMessageText());
				$oStmt->bindValue(':IsHtml', $this->getIsHtml());
				$oStmt->bindValue(':NewsletterType', $this->getNewsletterType());
				$oStmt->bindValue(':CreateDate', $this->getCreateDate());
				$oStmt->bindValue(':UpdateDate', $this->getUpdateDate());

				if ( $oStmt->execute() ) {
					if ( !$this->getNlid() ) {
						$this->setNlid($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsLetter
			WHERE
				ID = :ID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':ID', $this->getNlid());

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
	 * @return mofilmCommsNewsletter
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Name = '';
		$this->_OutboundTypeId = 0;
		$this->_Language = '';
		$this->_MessageSubject = '';
		$this->_MessageBody = '';
		$this->_MessageText = '';
		$this->_IsHtml = 0;
		$this->_NewsletterType = 0;
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
	 * @return mofilmCommsNewsletter
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
			'_Name' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_OutboundTypeId' => array(
				'number' => array(),
			),
			'_Language' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_MessageSubject' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_MessageBody' => array(
				'string' => array(),
			),
			'_MessageText' => array(
				'string' => array(),
			),
			'_IsHtml' => array(
				'number' => array(),
			),
			'_NewsletterType' => array(
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
	 * @return mofilmCommsNewsletter
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
	 * {@link mofilmCommsNewsletter::PRIMARY_KEY_SEPARATOR}.
 	 *
	 * @param string $inKey
	 * @return mofilmCommsNewsletter
  	 */
	function setPrimaryKey($inKey) {
		list($ID) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setNlid($ID);
	}

	/**
	 * Return the current value of the property $_ID
	 *
	 * @return integer
 	 */
	function getNlid() {
		return $this->_ID;
	}

	/**
	 * Set the object property _ID to $inID
	 *
	 * @param integer $inID
	 * @return mofilmCommsNewsletter
	 */
	function setNlid($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Name
	 *
	 * @return string
 	 */
	function getName() {
		return $this->_Name;
	}

	/**
	 * Set the object property _Name to $inName
	 *
	 * @param string $inName
	 * @return mofilmCommsNewsletter
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_OutboundTypeId
	 *
	 * @return integer
 	 */
	function getOutboundTypeId() {
		return $this->_OutboundTypeId;
	}

	/**
	 * Set the object property _OutboundTypeId to $inOutboundTypeId
	 *
	 * @param integer $inOutboundTypeId
	 * @return mofilmCommsNewsletter
	 */
	function setOutboundTypeId($inOutboundTypeId) {
		if ( $inOutboundTypeId !== $this->_OutboundTypeId ) {
			$this->_OutboundTypeId = $inOutboundTypeId;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the outbound type object
	 * 
	 * @return commsOutboundType
	 */
	function getOutboundType(){
	    return commsOutboundType::getInstance($this->getOutboundTypeId());
	}
	
	/**
	 * Return the current value of the property $_Language
	 *
	 * @return string
 	 */
	function getLanguage() {
		return $this->_Language;
	}

	/**
	 * Set the object property _Language to $inLanguage
	 *
	 * @param string $inLanguage
	 * @return mofilmCommsNewsletter
	 */
	function setLanguage($inLanguage) {
		if ( $inLanguage !== $this->_Language ) {
			$this->_Language = $inLanguage;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_MessageSubject
	 *
	 * @return string
 	 */
	function getMessageSubject() {
		return $this->_MessageSubject;
	}

	/**
	 * Set the object property _MessageSubject to $inMessageSubject
	 *
	 * @param string $inMessageSubject
	 * @return mofilmCommsNewsletter
	 */
	function setMessageSubject($inMessageSubject) {
		if ( $inMessageSubject !== $this->_MessageSubject ) {
			$this->_MessageSubject = $inMessageSubject;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_MessageBody
	 *
	 * @return string
 	 */
	function getMessageBody() {
		return $this->_MessageBody;
	}

	/**
	 * Set the object property _MessageBody to $inMessageBody
	 *
	 * @param string $inMessageBody
	 * @return mofilmCommsNewsletter
	 */
	function setMessageBody($inMessageBody) {
		if ( $inMessageBody !== $this->_MessageBody ) {
			$this->_MessageBody = $inMessageBody;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_MessageText
	 *
	 * @return string
 	 */
	function getMessageText() {
		return $this->_MessageText;
	}

	/**
	 * Set the object property _MessageText to $inMessageText
	 *
	 * @param string $inMessageText
	 * @return mofilmCommsNewsletter
	 */
	function setMessageText($inMessageText) {
		if ( $inMessageText !== $this->_MessageText ) {
			$this->_MessageText = $inMessageText;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_IsHtml
	 *
	 * @return integer
 	 */
	function getIsHtml() {
		return $this->_IsHtml;
	}

	/**
	 * Set the object property _IsHtml to $inIsHtml
	 *
	 * @param integer $inIsHtml
	 * @return mofilmCommsNewsletter
	 */
	function setIsHtml($inIsHtml) {
		if ( $inIsHtml !== $this->_IsHtml ) {
			$this->_IsHtml = $inIsHtml;
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
	 * @return mofilmCommsNewsletter
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
	 * @return mofilmCommsNewsletter
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
	 * @return mofilmCommsNewsletter
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	/**
	 * Return the current value of the property $inNewsletterType
	 *
	 * @return integer
 	 */
	function getNewsletterType() {
		return $this->_NewsletterType;
	}

	/**
	 * Set the object property _NewsletterType to $inNewsletterType
	 *
	 * @param integer $inNewsletterType
	 * @return mofilmCommsNewsletter
	 */
	function setNewsletterType($inNewsletterType) {
		if ( $inNewsletterType !== $this->_NewsletterType ) {
			$this->_NewsletterType = $inNewsletterType;
			$this->setModified();
		}
		return $this;
	}
	
}