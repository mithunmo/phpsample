<?php
/**
 * mofilmPaymentDetails
 *
 * Stored in mofilmPaymentDetails.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmPaymentDetails
 * @category mofilmPaymentDetails
 * @version $Rev: 840 $
 */


/**
 * mofilmPaymentDetails Class
 *
 * Provides access to records in mofilm_content.paymentDetails
 *
 * Creating a new record:
 * <code>
 * $oMofilmPaymentDetail = new mofilmPaymentDetails();
 * $oMofilmPaymentDetail->setID($inID);
 * $oMofilmPaymentDetail->setEventID($inEventID);
 * $oMofilmPaymentDetail->setSourceID($inSourceID);
 * $oMofilmPaymentDetail->setUserID($inUserID);
 * $oMofilmPaymentDetail->setMovieID($inMovieID);
 * $oMofilmPaymentDetail->setGrantID($inGrantID);
 * $oMofilmPaymentDetail->setPaymentType($inPaymentType);
 * $oMofilmPaymentDetail->setSubmitterID($inSubmitterID);
 * $oMofilmPaymentDetail->setSubmitterComments($inSubmitterComments);
 * $oMofilmPaymentDetail->setApproverID($inApproverID);
 * $oMofilmPaymentDetail->setApproverComments($inApproverComments);
 * $oMofilmPaymentDetail->setPayableAmount($inPayableAmount);
 * $oMofilmPaymentDetail->setPaidAmount($inPaidAmount);
 * $oMofilmPaymentDetail->setStatus($inStatus);
 * $oMofilmPaymentDetail->setCreated($inCreated);
 * $oMofilmPaymentDetail->setDueDate($inDueDate);
 * $oMofilmPaymentDetail->setPaidDate($inPaidDate);
 * $oMofilmPaymentDetail->setPaymentDesc($inPaymentDesc);
 * $oMofilmPaymentDetail->setAccountUser($inAccountUser);
 * $oMofilmPaymentDetail->setAccountComments($inAccountComments);
 * $oMofilmPaymentDetail->setBankReference($inBankReference);
 * $oMofilmPaymentDetail->setHasMultipart($inHasMultipart);
 * $oMofilmPaymentDetail->setParentID($inParentID);
 * $oMofilmPaymentDetail->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmPaymentDetail = new mofilmPaymentDetails($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmPaymentDetail = new mofilmPaymentDetails();
 * $oMofilmPaymentDetail->setID($inID);
 * $oMofilmPaymentDetail->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmPaymentDetail = mofilmPaymentDetails::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmPaymentDetails
 * @category mofilmPaymentDetails
 */
class mofilmPaymentDetails implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmPaymentDetails
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
	 * Stores $_EventID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_EventID;

	/**
	 * Stores $_SourceID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SourceID;

	/**
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_MovieID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;

	/**
	 * Stores $_GrantID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GrantID;

	/**
	 * Stores $_PaymentType
	 *
	 * @var string (PAYMENTTYPE_AD_HOC,PAYMENTTYPE_ADVANCE_GRANT,PAYMENTTYPE_WINNER_PAYMENT,PAYMENTTYPE_GRANT_PAYMENT,)
	 * @access protected
	 */
	protected $_PaymentType;
	const PAYMENTTYPE_AD_HOC = 'Fee';
	const PAYMENTTYPE_ADVANCE_GRANT = 'Advance Grant';
	const PAYMENTTYPE_WINNER_PAYMENT = 'Prize';
	const PAYMENTTYPE_GRANT_PAYMENT = 'Grant';

	/**
	 * Stores $_SubmitterID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SubmitterID;

	/**
	 * Stores $_SubmitterComments
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_SubmitterComments;

	/**
	 * Stores $_ApproverID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ApproverID;

	/**
	 * Stores $_ApproverComments
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ApproverComments;

	/**
	 * Stores $_PayableAmount
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_PayableAmount;

	/**
	 * Stores $_PaidAmount
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_PaidAmount;

	/**
	 * Stores $_Status
	 *
	 * @var string (STATUS_PENDING_APPROVAL,STATUS_APPROVED,STATUS_DELAYED,STATUS_CANCELED,STATUS_DRAFT,STATUS_PAID,)
	 * @access protected
	 */
	protected $_Status;
	const STATUS_PENDING_APPROVAL = 'Pending Approval';
	const STATUS_APPROVED = 'Approved';
	const STATUS_DELAYED = 'Delayed';
	const STATUS_CANCELED = 'Canceled';
	const STATUS_DRAFT = 'Draft';
	const STATUS_PAID = 'Paid';

	/**
	 * Stores $_Created
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_Created;

	/**
	 * Stores $_DueDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_DueDate;

	/**
	 * Stores $_PaidDate
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_PaidDate;

	/**
	 * Stores $_PaymentDesc
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_PaymentDesc;

	/**
	 * Stores $_AccountUser
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_AccountUser;

	/**
	 * Stores $_AccountComments
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_AccountComments;

	/**
	 * Stores $_BankReference
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_BankReference;

	/**
	 * Stores $_HasMultipart
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_HasMultipart;

	/**
	 * Stores $_ParentID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ParentID;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmPaymentDetails
	 *
	 * @param integer $inID
	 * @return mofilmPaymentDetails
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
	 * Get an instance of mofilmPaymentDetails by primary key
	 *
	 * @param integer $inID
	 * @return mofilmPaymentDetails
	 * @static
	 */
	public static function getInstance($inID) {
		$key = $inID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmPaymentDetails();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmPaymentDetails
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	 public static function listOfObjects($statusSearch = null,$filmMakerSearch = null, $inOffset = null, $inLimit = 30) {
       
        /*
         * Holds values to be assigned during query execution. Values do not need
         * to be escaped because they are injected into named place-holders in the
         * prepared query. Add items using $values[':PlaceHolder'] = $value;
         */
        $values = array();
        $queryStatus = '';
        if ($statusSearch != null && isset($statusSearch)) {
            $queryStatus .= 'status = "'.trim($statusSearch).'" AND ';
        }
        if ($filmMakerSearch != null) {

            if(strpos($filmMakerSearch, ' ') > 0){
                $nameArray = explode(' ',trim($filmMakerSearch));
                $userStr = 'WHERE firstname LIKE "%'.$nameArray[0].'%" AND surname LIKE "%'.$nameArray[1].'%" ';
            }else{
                $userQStr = 'WHERE firstname LIKE "%'.$filmMakerSearch.'%" OR surname LIKE "%'.$filmMakerSearch.'%" ';
     
            }
            $userQ = 'SELECT ID FROM ' . system::getConfig()->getDatabase('mofilm_content') . '.users 
                      '.$userQStr;
            $uStmt = dbManager::getInstance()->prepare($userQ);
            if ($uStmt->execute($values)) {
                $userStr = '';
                foreach ($uStmt as $row) {
                    if($userStr != ''){
                       $userStr .= ',';
                    }
                    $userStr .= $row['ID'];
                }
            }
            if($userStr != ''){
                $queryStatus .= 'userID IN ('.$userStr.') AND ';
            }
        }
        $query = '
                    SELECT *                      FROM ' . system::getConfig()->getDatabase('mofilm_content') . '.paymentDetails
                     WHERE '.$queryStatus.' 1 ORDER BY ID DESC';

        if ($inOffset !== null) {
            $query .= ' LIMIT ' . $inOffset . ',' . $inLimit;
        }

        $list = array();

        $oStmt = dbManager::getInstance()->prepare($query);
        if ($oStmt->execute($values)) {
            foreach ($oStmt as $row) {
                $oObject = new mofilmPaymentDetails();
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
		$values = array();

		$query = '
			SELECT ID, eventID, sourceID, userID, movieID, grantID, paymentType, submitterID, submitterComments, approverID, approverComments, payableAmount, paidAmount, status, created, dueDate, paidDate, PaymentDesc, accountUser, accountComments, bankReference, hasMultipart, parentID
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.paymentDetails';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);

		$this->reset();
		if ( $oStmt->execute($values) ) {
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
		$this->setEventID((int)$inArray['eventID']);
		$this->setSourceID((int)$inArray['sourceID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setMovieID((int)$inArray['movieID']);
		$this->setGrantID((int)$inArray['grantID']);
		$this->setPaymentType($inArray['paymentType']);
		$this->setSubmitterID((int)$inArray['submitterID']);
		$this->setSubmitterComments($inArray['submitterComments']);
		$this->setApproverID((int)$inArray['approverID']);
		$this->setApproverComments($inArray['approverComments']);
		$this->setPayableAmount($inArray['payableAmount']);
		$this->setPaidAmount($inArray['paidAmount']);
		$this->setStatus($inArray['status']);
		$this->setCreated($inArray['created']);
		$this->setDueDate($inArray['dueDate']);
		$this->setPaidDate($inArray['paidDate']);
		$this->setPaymentDesc($inArray['PaymentDesc']);
		$this->setAccountUser((int)$inArray['accountUser']);
		$this->setAccountComments($inArray['accountComments']);
		$this->setBankReference($inArray['bankReference']);
		$this->setHasMultipart((int)$inArray['hasMultipart']);
		$this->setParentID((int)$inArray['parentID']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.paymentDetails
					( ID, eventID, sourceID, userID, movieID, grantID, paymentType, submitterID, submitterComments, approverID, approverComments, payableAmount, paidAmount, status, created, dueDate, paidDate, PaymentDesc, accountUser, accountComments, bankReference, hasMultipart, parentID )
				VALUES
					( :ID, :EventID, :SourceID, :UserID, :MovieID, :GrantID, :PaymentType, :SubmitterID, :SubmitterComments, :ApproverID, :ApproverComments, :PayableAmount, :PaidAmount, :Status, :Created, :DueDate, :PaidDate, :PaymentDesc, :AccountUser, :AccountComments, :BankReference, :HasMultipart, :ParentID )
				ON DUPLICATE KEY UPDATE
					eventID=VALUES(eventID),
					sourceID=VALUES(sourceID),
					userID=VALUES(userID),
					movieID=VALUES(movieID),
					grantID=VALUES(grantID),
					paymentType=VALUES(paymentType),
					submitterID=VALUES(submitterID),
					submitterComments=VALUES(submitterComments),
					approverID=VALUES(approverID),
					approverComments=VALUES(approverComments),
					payableAmount=VALUES(payableAmount),
					paidAmount=VALUES(paidAmount),
					status=VALUES(status),
					created=VALUES(created),
					dueDate=VALUES(dueDate),
					paidDate=VALUES(paidDate),
					PaymentDesc=VALUES(PaymentDesc),
					accountUser=VALUES(accountUser),
					accountComments=VALUES(accountComments),
					bankReference=VALUES(bankReference),
					hasMultipart=VALUES(hasMultipart),
					parentID=VALUES(parentID)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':EventID', $this->getEventID());
				$oStmt->bindValue(':SourceID', $this->getSourceID());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':MovieID', $this->getMovieID());
				$oStmt->bindValue(':GrantID', $this->getGrantID());
				$oStmt->bindValue(':PaymentType', $this->getPaymentType());
				$oStmt->bindValue(':SubmitterID', $this->getSubmitterID());
				$oStmt->bindValue(':SubmitterComments', $this->getSubmitterComments());
				$oStmt->bindValue(':ApproverID', $this->getApproverID());
				$oStmt->bindValue(':ApproverComments', $this->getApproverComments());
				$oStmt->bindValue(':PayableAmount', $this->getPayableAmount());
				$oStmt->bindValue(':PaidAmount', $this->getPaidAmount());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':Created', $this->getCreated());
				$oStmt->bindValue(':DueDate', $this->getDueDate());
				$oStmt->bindValue(':PaidDate', $this->getPaidDate());
				$oStmt->bindValue(':PaymentDesc', $this->getPaymentDesc());
				$oStmt->bindValue(':AccountUser', $this->getAccountUser());
				$oStmt->bindValue(':AccountComments', $this->getAccountComments());
				$oStmt->bindValue(':BankReference', $this->getBankReference());
				$oStmt->bindValue(':HasMultipart', $this->getHasMultipart());
				$oStmt->bindValue(':ParentID', $this->getParentID());

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.paymentDetails
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
	 * @return mofilmPaymentDetails
	 */
	function reset() {
		$this->_ID = 0;
		$this->_EventID = null;
		$this->_SourceID = null;
		$this->_UserID = 0;
		$this->_MovieID = null;
		$this->_GrantID = 0;
		$this->_PaymentType = '';
		$this->_SubmitterID = null;
		$this->_SubmitterComments = null;
		$this->_ApproverID = null;
		$this->_ApproverComments = null;
		$this->_PayableAmount = '';
		$this->_PaidAmount = null;
		$this->_Status = '';
		$this->_Created = null;
		$this->_DueDate = null;
		$this->_PaidDate = null;
		$this->_PaymentDesc = null;
		$this->_AccountUser = null;
		$this->_AccountComments = null;
		$this->_BankReference = null;
		$this->_HasMultipart = null;
		$this->_ParentID = null;
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
	 * @return mofilmPaymentDetails
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
			'_EventID' => array(
				'number' => array(),
			),
			'_SourceID' => array(
				'number' => array(),
			),
			'_UserID' => array(
				'number' => array(),
			),
			'_MovieID' => array(
				'number' => array(),
			),
			'_GrantID' => array(
				'number' => array(),
			),
			'_PaymentType' => array(
				'inArray' => array('values' => array(self::PAYMENTTYPE_AD_HOC, self::PAYMENTTYPE_ADVANCE_GRANT, self::PAYMENTTYPE_WINNER_PAYMENT, self::PAYMENTTYPE_GRANT_PAYMENT),),
			),
			'_SubmitterID' => array(
				'number' => array(),
			),
			'_SubmitterComments' => array(
				'string' => array('min' => 1,'max' => 200,),
			),
			'_ApproverID' => array(
				'number' => array(),
			),
			'_ApproverComments' => array(
				'string' => array('min' => 1,'max' => 300,),
			),
			'_PayableAmount' => array(
				'string' => array('min' => 1,'max' => 100,),
			),
			'_PaidAmount' => array(
				'string' => array('min' => 1,'max' => 100,),
			),
			'_Status' => array(
				'inArray' => array('values' => array(self::STATUS_PENDING_APPROVAL, self::STATUS_APPROVED, self::STATUS_DELAYED, self::STATUS_CANCELED, self::STATUS_DRAFT, self::STATUS_PAID),),
			),
			'_Created' => array(
				'dateTime' => array(),
			),
			'_DueDate' => array(
				'dateTime' => array(),
			),
			'_PaidDate' => array(
				'dateTime' => array(),
			),
			'_PaymentDesc' => array(
				'string' => array('min' => 1,'max' => 300,),
			),
			'_AccountUser' => array(
				'number' => array(),
			),
			'_AccountComments' => array(
				'string' => array('min' => 1,'max' => 200,),
			),
			'_BankReference' => array(
				'string' => array('min' => 1,'max' => 100,),
			),
			'_HasMultipart' => array(
				'number' => array(),
			),
			'_ParentID' => array(
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

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmPaymentDetails
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
	 * mofilmPaymentDetails::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmPaymentDetails
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
	 * @return mofilmPaymentDetails
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_EventID
	 *
	 * @return integer
 	 */
	function getEventID() {
		return $this->_EventID;
	}

	/**
	 * Set the object property _EventID to $inEventID
	 *
	 * @param integer $inEventID
	 * @return mofilmPaymentDetails
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SourceID
	 *
	 * @return integer
 	 */
	function getSourceID() {
		return $this->_SourceID;
	}

	/**
	 * Set the object property _SourceID to $inSourceID
	 *
	 * @param integer $inSourceID
	 * @return mofilmPaymentDetails
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
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
	 * @return mofilmPaymentDetails
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_MovieID
	 *
	 * @return integer
 	 */
	function getMovieID() {
		return $this->_MovieID;
	}

	/**
	 * Set the object property _MovieID to $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmPaymentDetails
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_GrantID
	 *
	 * @return integer
 	 */
	function getGrantID() {
		return $this->_GrantID;
	}

	/**
	 * Set the object property _GrantID to $inGrantID
	 *
	 * @param integer $inGrantID
	 * @return mofilmPaymentDetails
	 */
	function setGrantID($inGrantID) {
		if ( $inGrantID !== $this->_GrantID ) {
			$this->_GrantID = $inGrantID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_PaymentType
	 *
	 * @return string
 	 */
	function getPaymentType() {
		return $this->_PaymentType;
	}

	/**
	 * Set the object property _PaymentType to $inPaymentType
	 *
	 * @param string $inPaymentType
	 * @return mofilmPaymentDetails
	 */
	function setPaymentType($inPaymentType) {
		if ( $inPaymentType !== $this->_PaymentType ) {
			$this->_PaymentType = $inPaymentType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SubmitterID
	 *
	 * @return integer
 	 */
	function getSubmitterID() {
		return $this->_SubmitterID;
	}

	/**
	 * Set the object property _SubmitterID to $inSubmitterID
	 *
	 * @param integer $inSubmitterID
	 * @return mofilmPaymentDetails
	 */
	function setSubmitterID($inSubmitterID) {
		if ( $inSubmitterID !== $this->_SubmitterID ) {
			$this->_SubmitterID = $inSubmitterID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SubmitterComments
	 *
	 * @return string
 	 */
	function getSubmitterComments() {
		return $this->_SubmitterComments;
	}

	/**
	 * Set the object property _SubmitterComments to $inSubmitterComments
	 *
	 * @param string $inSubmitterComments
	 * @return mofilmPaymentDetails
	 */
	function setSubmitterComments($inSubmitterComments) {
		if ( $inSubmitterComments !== $this->_SubmitterComments ) {
			$this->_SubmitterComments = $inSubmitterComments;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ApproverID
	 *
	 * @return integer
 	 */
	function getApproverID() {
		return $this->_ApproverID;
	}

	/**
	 * Set the object property _ApproverID to $inApproverID
	 *
	 * @param integer $inApproverID
	 * @return mofilmPaymentDetails
	 */
	function setApproverID($inApproverID) {
		if ( $inApproverID !== $this->_ApproverID ) {
			$this->_ApproverID = $inApproverID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ApproverComments
	 *
	 * @return string
 	 */
	function getApproverComments() {
		return $this->_ApproverComments;
	}

	/**
	 * Set the object property _ApproverComments to $inApproverComments
	 *
	 * @param string $inApproverComments
	 * @return mofilmPaymentDetails
	 */
	function setApproverComments($inApproverComments) {
		if ( $inApproverComments !== $this->_ApproverComments ) {
			$this->_ApproverComments = $inApproverComments;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_PayableAmount
	 *
	 * @return string
 	 */
	function getPayableAmount() {
		return $this->_PayableAmount;
	}

	/**
	 * Set the object property _PayableAmount to $inPayableAmount
	 *
	 * @param string $inPayableAmount
	 * @return mofilmPaymentDetails
	 */
	function setPayableAmount($inPayableAmount) {
		if ( $inPayableAmount !== $this->_PayableAmount ) {
			$this->_PayableAmount = $inPayableAmount;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_PaidAmount
	 *
	 * @return string
 	 */
	function getPaidAmount() {
		return $this->_PaidAmount;
	}

	/**
	 * Set the object property _PaidAmount to $inPaidAmount
	 *
	 * @param string $inPaidAmount
	 * @return mofilmPaymentDetails
	 */
	function setPaidAmount($inPaidAmount) {
		if ( $inPaidAmount !== $this->_PaidAmount ) {
			$this->_PaidAmount = $inPaidAmount;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Status
	 *
	 * @return string
 	 */
	function getStatus() {
		return $this->_Status;
	}

	/**
	 * Set the object property _Status to $inStatus
	 *
	 * @param string $inStatus
	 * @return mofilmPaymentDetails
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Created
	 *
	 * @return systemDateTime
 	 */
	function getCreated() {
		return $this->_Created;
	}

	/**
	 * Set the object property _Created to $inCreated
	 *
	 * @param systemDateTime $inCreated
	 * @return mofilmPaymentDetails
	 */
	function setCreated($inCreated) {
		if ( $inCreated !== $this->_Created ) {
			if ( !$inCreated instanceof DateTime ) {
				$inCreated = new systemDateTime($inCreated, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_Created = $inCreated;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_DueDate
	 *
	 * @return systemDateTime
 	 */
	function getDueDate() {
		return $this->_DueDate;
	}

	/**
	 * Set the object property _DueDate to $inDueDate
	 *
	 * @param systemDateTime $inDueDate
	 * @return mofilmPaymentDetails
	 */
	function setDueDate($inDueDate) {
		if ( $inDueDate !== $this->_DueDate ) {
			if ( !$inDueDate instanceof DateTime ) {
				$inDueDate = new systemDateTime($inDueDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_DueDate = $inDueDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_PaidDate
	 *
	 * @return systemDateTime
 	 */
	function getPaidDate() {
		return $this->_PaidDate;
	}

	/**
	 * Set the object property _PaidDate to $inPaidDate
	 *
	 * @param systemDateTime $inPaidDate
	 * @return mofilmPaymentDetails
	 */
	function setPaidDate($inPaidDate) {
		if ( $inPaidDate !== $this->_PaidDate ) {
			if ( !$inPaidDate instanceof DateTime ) {
				$inPaidDate = new systemDateTime($inPaidDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_PaidDate = $inPaidDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_PaymentDesc
	 *
	 * @return string
 	 */
	function getPaymentDesc() {
		return $this->_PaymentDesc;
	}

	/**
	 * Set the object property _PaymentDesc to $inPaymentDesc
	 *
	 * @param string $inPaymentDesc
	 * @return mofilmPaymentDetails
	 */
	function setPaymentDesc($inPaymentDesc) {
		if ( $inPaymentDesc !== $this->_PaymentDesc ) {
			$this->_PaymentDesc = $inPaymentDesc;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_AccountUser
	 *
	 * @return integer
 	 */
	function getAccountUser() {
		return $this->_AccountUser;
	}

	/**
	 * Set the object property _AccountUser to $inAccountUser
	 *
	 * @param integer $inAccountUser
	 * @return mofilmPaymentDetails
	 */
	function setAccountUser($inAccountUser) {
		if ( $inAccountUser !== $this->_AccountUser ) {
			$this->_AccountUser = $inAccountUser;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_AccountComments
	 *
	 * @return string
 	 */
	function getAccountComments() {
		return $this->_AccountComments;
	}

	/**
	 * Set the object property _AccountComments to $inAccountComments
	 *
	 * @param string $inAccountComments
	 * @return mofilmPaymentDetails
	 */
	function setAccountComments($inAccountComments) {
		if ( $inAccountComments !== $this->_AccountComments ) {
			$this->_AccountComments = $inAccountComments;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_BankReference
	 *
	 * @return string
 	 */
	function getBankReference() {
		return $this->_BankReference;
	}

	/**
	 * Set the object property _BankReference to $inBankReference
	 *
	 * @param string $inBankReference
	 * @return mofilmPaymentDetails
	 */
	function setBankReference($inBankReference) {
		if ( $inBankReference !== $this->_BankReference ) {
			$this->_BankReference = $inBankReference;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_HasMultipart
	 *
	 * @return integer
 	 */
	function getHasMultipart() {
		return $this->_HasMultipart;
	}

	/**
	 * Set the object property _HasMultipart to $inHasMultipart
	 *
	 * @param integer $inHasMultipart
	 * @return mofilmPaymentDetails
	 */
	function setHasMultipart($inHasMultipart) {
		if ( $inHasMultipart !== $this->_HasMultipart ) {
			$this->_HasMultipart = $inHasMultipart;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ParentID
	 *
	 * @return integer
 	 */
	function getParentID() {
		return $this->_ParentID;
	}

	/**
	 * Set the object property _ParentID to $inParentID
	 *
	 * @param integer $inParentID
	 * @return mofilmPaymentDetails
	 */
	function setParentID($inParentID) {
		if ( $inParentID !== $this->_ParentID ) {
			$this->_ParentID = $inParentID;
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
	 * @return mofilmPaymentDetails
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}