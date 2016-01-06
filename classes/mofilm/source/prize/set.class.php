<?php
/**
 * mofilmSourcePrizeSet
 *
 * Stored in mofilmSourcePrizeSet.class.php
 *
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSourcePrizeSet
 * @category mofilmSourcePrizeSet
 * @version $Rev: 840 $
 */


/**
 * mofilmSourcePrizeSet Class
 *
 * Provides access to records in mofilm_content.sourcesPrize
 *
 * Creating a new record:
 * <code>
 * $oMofilmSourcePrizeSet = new mofilmSourcePrizeSet();
 * $oMofilmSourcePrizeSet->setID($inID);
 * $oMofilmSourcePrizeSet->setSourceID($inSourceID);
 * $oMofilmSourcePrizeSet->setPosition($inPosition);
 * $oMofilmSourcePrizeSet->setAmount($inAmount);
 * $oMofilmSourcePrizeSet->setDescription($inDescription);
 * $oMofilmSourcePrizeSet->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmSourcePrizeSet = new mofilmSourcePrizeSet($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmSourcePrizeSet = new mofilmSourcePrizeSet();
 * $oMofilmSourcePrizeSet->setID($inID);
 * $oMofilmSourcePrizeSet->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmSourcePrizeSet = mofilmSourcePrizeSet::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmSourcePrizeSet
 * @category mofilmSourcePrizeSet
 */
class mofilmSourcePrizeSet extends baseSet implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmSourcePrizeSet
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
	 * Stores $_SourceID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SourceID;

	/**
	 * Stores $_Position
	 *
	 * @var string (POSITION_GRAND_PRIZE,POSITION_2ND,POSITION_3RD,POSITION_4TH,POSITION_5TH,POSITION_6TH,POSITION_7TH,POSITION_8TH,POSITION_9TH,POSITION_10TH,)
	 * @access protected
	 */
	protected $_Position;
	const POSITION_GRAND_PRIZE = 'Grand Prize';
	const POSITION_2ND = '2nd';
	const POSITION_3RD = '3rd';
	const POSITION_4TH = '4th';
	const POSITION_5TH = '5th';
	const POSITION_6TH = '6th';
	const POSITION_7TH = '7th';
	const POSITION_8TH = '8th';
	const POSITION_9TH = '9th';
	const POSITION_10TH = '10th';

	/**
	 * Stores $_Amount
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Amount;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmSourcePrizeSet
	 *
	 * @param integer $inSourceID
	 * @return mofilmSourcePrizeSet
	 */
	function __construct($inSourceID = null) {
		$this->reset();
		if ( $inSourceID !== 0 ) {
			$this->setSourceID($inSourceID);
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
	 * Get an instance of mofilmSourcePrizeSet by primary key
	 *
	 * @param integer $inID
	 * @return mofilmSourcePrizeSet
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
		$oObject = new mofilmSourcePrizeSet();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmSourcePrizeSet by sourceID
	 *
	 * @param integer $inSourceID
	 * @return mofilmSourcePrizeSet
	 * @static
	 */
	public static function getInstanceBySourceID($inSourceID) {
		$key = $inSourceID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmSourcePrizeSet();
		$oObject->setSourceID($inSourceID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmSourcePrizeSet
	 *
	 * @param integer $inSourceID
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inSourceID = null, $inOffset = null, $inLimit = 30) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, sourceID, position, amount, description
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourcesPrize';

		$where = array();
		if ( $inSourceID !== 0 ) {
			$where[] = ' sourceID = :sourceID ';
			$values[':sourceID'] = $inSourceID;
		}
		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$query .= ' ORDER BY position';

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmSourcePrizeSet();
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
			SELECT ID, sourceID, position, amount, description
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourcesPrize';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}
		if ( $this->_SourceID !== 0 ) {
			$where[] = ' sourceID = :sourceID ';
			$values[':sourceID'] = $this->getSourceID();
		}
		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);
		$query .= ' ORDER BY position';

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
		$this->setSourceID((int)$inArray['sourceID']);
		$this->setPosition($inArray['position']);
		$this->setAmount((int)$inArray['amount']);
		$this->setDescription($inArray['description']);
		$this->setModified(false);
	}

	/**
	 * Saves object to the table
	 *
	 * @return boolean
	 */
	function save() {
		if ( $this->_SourceID ) {
			if ( $this->isModified() ) {
				$query = '
					INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.sourcesPrize
						(sourceID, position, amount, description )
					VALUES';

				if ( $this->getCount() > 0 ) {
					$values = array();
					foreach ( $this as $oObject ) {
						$values[] = "($this->_SourceID, {$oObject->getPosition()}, {$oObject->getAmount()}, ".dbManager::getInstance()->quote($oObject->getDescription()).")";
					}
					$query .= implode(', ', $values);

					$res = dbManager::getInstance()->exec($query);
					
					if ( $res > 0 ) {
						$this->setModified(false);
					}
				}
				return true;
			}
		}
		return false;
	}

//						'( :ID, :SourceID, :Position, :Amount, :Description )
//					ON DUPLICATE KEY UPDATE
//						sourceID=VALUES(sourceID),
//						position=VALUES(position),
//						amount=VALUES(amount),
//						description=VALUES(description)				';
//
//					$oDB = dbManager::getInstance();
//					$oStmt = $oDB->prepare($query);
//					$oStmt->bindValue(':ID', $this->getID());
//					$oStmt->bindValue(':SourceID', $this->getSourceID());
//					$oStmt->bindValue(':Position', $this->getPosition());
//					$oStmt->bindValue(':Amount', $this->getAmount());
//					$oStmt->bindValue(':Description', $this->getDescription());

//					if ( $oStmt->execute() ) {
//						if ( !$this->getID() ) {
//							$this->setID($oDB->lastInsertId());
//						}
//						$this->setModified(false);
//						$return = true;
//					}
//			}
//
//			return $return;
//		}
//	}

	/**
	 * Deletes the object from the table
	 *
	 * @return boolean
	 */
	function delete() {
		$query = '
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.sourcesPrize
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
	 * @return mofilmSourcePrizeSet
	 */
	function reset() {
		$this->_ID = 0;
		$this->_SourceID = 0;
		$this->_Position = '';
		$this->_Amount = 0;
		$this->_Description = '';
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
	 * @return mofilmSourcePrizeSet
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
			'_SourceID' => array(
				'number' => array(),
			),
			'_Position' => array(
				'inArray' => array('values' => array(self::POSITION_GRAND_PRIZE, self::POSITION_2ND, self::POSITION_3RD, self::POSITION_4TH, self::POSITION_5TH, self::POSITION_6TH, self::POSITION_7TH, self::POSITION_8TH, self::POSITION_9TH, self::POSITION_10TH),),
			),
			'_Amount' => array(
				'number' => array(),
			),
			'_Description' => array(
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

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmSourcePrizeSet
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
	 * mofilmSourcePrizeSet::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmSourcePrizeSet
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
	 * @return mofilmSourcePrizeSet
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
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
	 * @return mofilmSourcePrizeSet
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Position
	 *
	 * @return string
 	 */
	function getPosition() {
		return $this->_Position;
	}

	/**
	 * Set the object property _Position to $inPosition
	 *
	 * @param string $inPosition
	 * @return mofilmSourcePrizeSet
	 */
	function setPosition($inPosition) {
		if ( $inPosition !== $this->_Position ) {
			$this->_Position = $inPosition;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Amount
	 *
	 * @return integer
 	 */
	function getAmount() {
		return $this->_Amount;
	}

	/**
	 * Set the object property _Amount to $inAmount
	 *
	 * @param integer $inAmount
	 * @return mofilmSourcePrizeSet
	 */
	function setAmount($inAmount) {
		if ( $inAmount !== $this->_Amount ) {
			$this->_Amount = $inAmount;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Description
	 *
	 * @return string
 	 */
	function getDescription() {
		return $this->_Description;
	}

	/**
	 * Set the object property _Description to $inDescription
	 *
	 * @param string $inDescription
	 * @return mofilmSourcePrizeSet
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
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
	 * @return mofilmSourcePrizeSet
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	/**
	 * Sets an array of prize objects to the set
	 * 
	 * @param array $inArray Array of mofilmSourcePrize objects
	 * @return mofilmSourcePrizeSet
	 */
	function setObjects(array $inArray = array()) {
		return $this->_setItem($inArray);
	}
	
	/**
	 * Add the prize to the object
	 *
	 * @param mofilmSourcePrizeSet $inObject
	 * @return mofilmSourcePrizeSet
	 */
	function setObject(mofilmSourcePrizeSet $inObject) {
		return $this->_setValue($inObject);
	}
}