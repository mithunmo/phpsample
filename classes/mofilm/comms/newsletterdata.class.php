<?php
/**
 * mofilmCommsNewsletterdata
 *
 * Stored in mofilmCommsNewsletterdata.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterdata
 * @category mofilmCommsNewsletterdata
 * @version $Rev: 806 $
 */


/**
 * mofilmCommsNewsletterdata Class
 *
 * Provides access to records in mofilm_comms.newsletterData
 *
 * Creating a new record:
 * <code>
 * $oMofilmCommsNewsletterdatum = new mofilmCommsNewsletterdata();
 * $oMofilmCommsNewsletterdatum->setID($inID);
 * $oMofilmCommsNewsletterdatum->setNewsletterID($inNewsletterID);
 * $oMofilmCommsNewsletterdatum->setStatus($inStatus);
 * $oMofilmCommsNewsletterdatum->setClassname($inClassname);
 * $oMofilmCommsNewsletterdatum->setParams($inParams);
 * $oMofilmCommsNewsletterdatum->setEmailName($inEmailName);
 * $oMofilmCommsNewsletterdatum->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCommsNewsletterdatum = new mofilmCommsNewsletterdata($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmCommsNewsletterdatum = new mofilmCommsNewsletterdata();
 * $oMofilmCommsNewsletterdatum->setID($inID);
 * $oMofilmCommsNewsletterdatum->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmCommsNewsletterdatum = mofilmCommsNewsletterdata::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmCommsNewsletterdata
 * @category mofilmCommsNewsletterdata
 */
class mofilmCommsNewsletterdata implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * If the status is 2 then newsletter is sent
	 *
	 * @var integer
	 */
	const NEWSLETTER_SENT = 2;

	/**
	 * If the status is 0 then newsletter is yet to be sent
	 *
	 * @var integer
	 */
	const NEWSLETTER_NOT_SENT = 0;

	/**
	 * It indicates a newsletter marketing message
	 *
	 * @var integer
	 */
	const NEWSLETTER_MKT_MESSAGE = 0;

	/**
	 * It indicates a newsletter important message
	 *
	 * @var integer
	 */
	const NEWSLETTER_IMP_MESSAGE = 1;
	
	/**
	 * Parameter to set the eventID
	 * 
	 */
	const PARAM_NL_EVENTID = "newsletter.EventID";
	
	/**
	 * Parameter to set the videoRating
	 * 
	 */
	const PARAM_NL_VIDEO_RATING = "newsletter.videoRating";
	
	/**
	 * Parameter to set the newsletter attachemnt
	 */
	const PARAM_NL_ATTACH = "newsletter.attachment";
	
	/**
	 * Parameter to set the newsletter Brand
	 */
	const PARAM_NL_SOURCEID = "newsletter.SourceID";
	
	
	
	/**
	 * Container for static instances of mofilmCommsNewsletterdata
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
	 * Stores $_Status
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Status;

	/**
	 * Stores $_Classname
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Classname;

	/**
	 * Stores $_Params
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Params;

	/**
	 * Stores $_EmailName
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_EmailName;

	/**
	 * stores $_ScheduledDate
	 *
	 * @var date
	 * @access protected
	 */
	protected $_ScheduledDate;

	/**
	 * stores $_MessageType
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MessageType;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;
	
	/**
	 * Stores $_ParamSet
	 * 
	 * @var type 
	 * @access protected 
	 */
	protected $_ParamSet;

	/**
	 * Returns a new instance of mofilmCommsNewsletterdata
	 *
	 * @param integer $inID
	 * @return mofilmCommsNewsletterdata
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
	 * Get an instance of mofilmCommsNewsletterdata by primary key
	 *
	 * @param integer $inID
	 * @return mofilmCommsNewsletterdata
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
		$oObject = new mofilmCommsNewsletterdata();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmCommsNewsletterdata
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inNewsletterType = 1) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, newsletterID, status, classname, params, emailName, messageType, scheduledDate
			FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterData ';
		
		$query .= " ORDER BY scheduledDate DESC ";
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				if ( mofilmCommsNewsletter::getInstance($row["newsletterID"])->getNewsletterType() == $inNewsletterType ) {
					$oObject = new mofilmCommsNewsletterdata();
					$oObject->loadFromArray($row);
					$list[] = $oObject;
				}
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

	/**
	 * Gets the next instance of newsletter to be sent
	 *
	 * @return array(mofilmCommsNewsletterdata)
	 * @static
	 */
	public static function getNlById() {
		$list = array();

		$query = '
			SELECT * FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterData
			 WHERE status = :Status
			 ORDER BY scheduledDate ASC';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Status', mofilmCommsNewsletterdata::NEWSLETTER_NOT_SENT);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmCommsNewsletterdata();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

	/**
	 * Updates the record of newsletterdata
	 *
	 * @param integer $inId
	 * @param integer $inStatus
	 * @return void
	 * @static
	 */
	public static function updateStatus($inId, $inStatus) {
		$query = '
			UPDATE '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterData
			   SET status = :Status
			 WHERE ID = :ID';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Status', $inStatus);
		$oStmt->bindValue(':ID', $inId);
		$oStmt->execute();
		$oStmt->closeCursor();
	}



	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT ID, newsletterID, status, classname, params, emailName, messageType, scheduledDate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterData';

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
		$this->setID((int)$inArray['ID']);
		$this->setNewsletterID((int)$inArray['newsletterID']);
		$this->setStatus((int)$inArray['status']);
		$this->setClassname($inArray['classname']);
		$this->setParams($inArray['params']);
		$this->setEmailName((int)$inArray['emailName']);
		$this->setMessageType(((int)$inArray['messageType']));
		$this->setScheduledDate($inArray['scheduledDate']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterData
					( ID, newsletterID, status, classname, params, emailName, messageType, scheduledDate)
				VALUES
					(:ID, :NewsletterID, :Status, :Classname, :Params, :EmailName, :MessageType, :ScheduledDate)
				ON DUPLICATE KEY UPDATE
					newsletterID=VALUES(newsletterID),
					status=VALUES(status),
					classname=VALUES(classname),
					params=VALUES(params),
					emailName=VALUES(emailName),
					messageType=VALUES(messageType),
					scheduledDate=VALUES(scheduledDate)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':NewsletterID', $this->getNewsletterID());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':Classname', $this->getClassname());
				$oStmt->bindValue(':Params', $this->getParams());
				$oStmt->bindValue(':EmailName', $this->getEmailName());
				$oStmt->bindValue(':MessageType', $this->getMessageType());
				$oStmt->bindValue(':ScheduledDate', $this->getScheduledDate());

				if ( $oStmt->execute() ) {
					if ( !$this->getID() ) {
						$this->setID($oDB->lastInsertId());
					}
					$this->setModified(false);
					$return = true;
				}
			}
			if ( $this->_ParamSet instanceof baseTableParamSet ) {
				$this->_ParamSet->setIndexID($this->getID());
				$return = $this->_ParamSet->save() && $return;
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
		$return = false;
		$query = '
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterData
			WHERE
				ID = :ID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':ID', $this->getID());

		if ( $oStmt->execute() ) {
			$oStmt->closeCursor();
			//$this->reset();
			$return = true;
		}
		
		$query = '
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.newsletterDataParams
			WHERE
				newsletterDataID  = :ID ';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':ID', $this->getID());

		if ( $oStmt->execute() ) {
			$oStmt->closeCursor();
			$this->reset();
			$return = true;
		}


		return $return;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mofilmCommsNewsletterdata
	 */
	function reset() {
		$this->_ID = 0;
		$this->_NewsletterID = 0;
		$this->_Status = 0;
		$this->_Classname = '';
		$this->_Params = '';
		$this->_EmailName = 0;
		$this->_MessageType = '';
		$this->_Validator = null;
		$this->_ParamSet = null;
		$this->_ScheduledDate=date(system::getConfig()->getDatabaseDatetimeFormat());
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
	 * @return mofilmCommsNewsletterdata
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
			'_Status' => array(
				'number' => array(),
			),
			'_Classname' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_Params' => array(
				'string' => array('min' => 1,'max' => 255,),
			),
			'_EmailName' => array(
				'number' => array(),
			),
			'_MessageType' => array(
				'number' => array(),
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
		if ( !$modified && $this->_ParamSet instanceof baseTableParamSet ) {
			$modified = $this->_ParamSet->isModified() || $modified;
		}

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmCommsNewsletterdata
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
	 * {@link mofilmCommsNewsletterdata::PRIMARY_KEY_SEPARATOR}.
 	 *
	 * @param string $inKey
	 * @return mofilmCommsNewsletterdata
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
	 * @return mofilmCommsNewsletterdata
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
	 * @return mofilmCommsNewsletterdata
	 */
	function setNewsletterID($inNewsletterID) {
		if ( $inNewsletterID !== $this->_NewsletterID ) {
			$this->_NewsletterID = $inNewsletterID;
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
	 * @return mofilmCommsNewsletterdata
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Classname
	 *
	 * @return string
 	 */
	function getClassname() {
		return $this->_Classname;
	}

	/**
	 * Set the object property _Classname to $inClassname
	 *
	 * @param string $inClassname
	 * @return mofilmCommsNewsletterdata
	 */
	function setClassname($inClassname) {
		if ( $inClassname !== $this->_Classname ) {
			$this->_Classname = $inClassname;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Params
	 *
	 * @return string
 	 */
	function getParams() {
		return $this->_Params;
	}

	/**
	 * Set the object property _Params to $inParams
	 *
	 * @param string $inParams
	 * @return mofilmCommsNewsletterdata
	 */
	function setParams($inParams) {
		if ( $inParams !== $this->_Params ) {
			$this->_Params = $inParams;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_EmailName
	 *
	 * @return integer
 	 */
	function getEmailName() {
		return $this->_EmailName;
	}
	
	/**
	 * Returns mofilmCommsNewsletter instance
	 *
	 * @return mofilmCommsNewsletter object
	 */
	function getNewsletter() {
		return mofilmCommsNewsletter::getInstance($this->getNewsletterID());
	}

	/**
	 * Set the object property _EmailName to $inEmailName
	 *
	 * @param integer $inEmailName
	 * @return mofilmCommsNewsletterdata
	 */
	function setEmailName($inEmailName) {
		if ( $inEmailName !== $this->_EmailName ) {
			$this->_EmailName = $inEmailName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ScheduledDate
	 *
	 * @return date
 	 */
	function getScheduledDate() {
		return $this->_ScheduledDate;
	}

	/**
	 * Set the object property _ScheduledDate to $inScheduledDate
	 *
	 * @param date $inScheduledDate
	 * @return mofilmCommsNewsletterdata
	 */
	function setScheduledDate($inScheduledDate) {
		if ( $inScheduledDate !== $this->_ScheduledDate ) {
			$this->_ScheduledDate = $inScheduledDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_MessageType
	 *
	 * @return integer
 	 */
	function getMessageType() {
		return $this->_MessageType;
	}

	/**
	 * Set the object property _MessageType to $inMessageType
	 *
	 * @param integer $inMessageType
	 * @return mofilmCommsNewsletterdata
	 */
	function setMessageType($inMessageType) {
		if ( $inMessageType !== $this->_MessageType ) {
			$this->_MessageType = $inMessageType;
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
	 * @return mofilmCommsNewsletterdata
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}

	/**
	 * Returns an instance of ParamSet, which is lazy loaded upon request
	 *
	 * @return baseTableParamSet
	 */
	function getParamSet() {
		if ( !$this->_ParamSet instanceof baseTableParamSet ) {
			$this->_ParamSet = new baseTableParamSet(system::getConfig()->getDatabase('mofilm_comms'), 'newsletterDataParams', 'newsletterDataID', 'paramName', 'paramValue', $this->getID(), false);
		}
		return $this->_ParamSet;
	}

	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param baseTableParamSet $inObject
	 * @return mofilmCommsNewsletterdata
	 */
	function setParamSet(baseTableParamSet $inObject) {
		$this->_ParamSet = $inObject;
		return $this;
	}
	
	/**
	 *
	 * @param integer $inTransactionID
	 * @param integer $inUserID
	 * @param array $inParam
	 * @return mofilmCommsNewsletterMessageCompilerInterface 
	 */
	function getCompiler($inTransactionID, $inUserID, $inParam) {
		$class = mofilmCommsNewsletterttype::getInstance(mofilmCommsNewsletter::getInstance($this->getNewsletterID())->getNewsletterType())->getCompilerClass();
		$oCompiler = new $class($this);
		
		if ( !$oCompiler instanceof mofilmCommsNewsletterMessageCompilerInterface ) {
			throw new mofilmException(sprintf('Expected CompilerInterface not %s', $class));
		}
		
		$oCompiler->setTransactionID($inTransactionID);
		$oCompiler->setUserID($inUserID);		
		if ( isset($inParam) && count($inParam) > 1 ) {
			$oCompiler->setMessageParam($inParam);
		}
		
		return $oCompiler;		
	}
}