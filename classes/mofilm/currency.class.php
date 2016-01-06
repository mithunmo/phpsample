<?php
/**
 * mofilmCurrency
 *
 * Stored in mofilmCurrency.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCurrency
 * @category mofilmCurrency
 * @version $Rev: 10 $
 */


/**
 * mofilmCurrency Class
 *
 * Provides access to records in mofilm_content.currencies
 *
 * Creating a new record:
 * <code>
 * $oMofilmCurrency = new mofilmCurrency();
 * $oMofilmCurrency->setID($inID);
 * $oMofilmCurrency->setDescription($inDescription);
 * $oMofilmCurrency->setIsoCodeString($inIsoCodeString);
 * $oMofilmCurrency->setIsoCodeNumeric($inIsoCodeNumeric);
 * $oMofilmCurrency->setSymbol($inSymbol);
 * $oMofilmCurrency->setPosition($inPosition);
 * $oMofilmCurrency->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCurrency = new mofilmCurrency($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmCurrency = new mofilmCurrency();
 * $oMofilmCurrency->setID($inID);
 * $oMofilmCurrency->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmCurrency = mofilmCurrency::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmCurrency
 * @category mofilmCurrency
 */
class mofilmCurrency implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmCurrency
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
	 * Stores $_ID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ID;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_IsoCodeString
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_IsoCodeString;

	/**
	 * Stores $_IsoCodeNumeric
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_IsoCodeNumeric;

	/**
	 * Stores $_Symbol
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Symbol;

	/**
	 * Stores $_Position
	 *
	 * @var string (POSITION_BEFORE,POSITION_AFTER,)
	 * @access protected
	 */
	protected $_Position;
	const POSITION_BEFORE = 'Before';
	const POSITION_AFTER = 'After';

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	
	/**
	 * Returns a new instance of mofilmCurrency
	 *
	 * @param integer $inID
	 * @return mofilmCurrency
	 */
	function __construct($inID = null) {
		$this->reset();
		if ( $inID !== null ) {
			$this->setID($inID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new mofilmCurrency containing non-unique properties
	 *
	 * @param string $inDescription
	 * @param string $inIsoCodeString
	 * @param integer $inIsoCodeNumeric
	 * @param string $inSymbol
	 * @param string $inPosition
	 * @return mofilmCurrency
	 * @static
	 */
	public static function factory($inDescription = null, $inIsoCodeString = null, $inIsoCodeNumeric = null, $inSymbol = null, $inPosition = null) {
		$oObject = new mofilmCurrency;
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inIsoCodeString !== null ) {
			$oObject->setIsoCodeString($inIsoCodeString);
		}
		if ( $inIsoCodeNumeric !== null ) {
			$oObject->setIsoCodeNumeric($inIsoCodeNumeric);
		}
		if ( $inSymbol !== null ) {
			$oObject->setSymbol($inSymbol);
		}
		if ( $inPosition !== null ) {
			$oObject->setPosition($inPosition);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmCurrency by primary key
	 *
	 * @param integer $inID
	 * @return mofilmCurrency
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
		$oObject = new mofilmCurrency();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmCurrency
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.currencies ORDER BY description ASC';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmCurrency();
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
			SELECT ID, description, isoCodeString, isoCodeNumeric, symbol, position
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.currencies';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_ID !== 0 ) {
				$oStmt->bindValue(':ID', $this->_ID);
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
		$this->setID((int)$inArray['ID']);
		$this->setDescription($inArray['description']);
		$this->setIsoCodeString($inArray['isoCodeString']);
		$this->setIsoCodeNumeric((int)$inArray['isoCodeNumeric']);
		$this->setSymbol($inArray['symbol']);
		$this->setPosition($inArray['position']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.currencies
					( ID, description, isoCodeString, isoCodeNumeric, symbol, position)
				VALUES
					(:ID, :Description, :IsoCodeString, :IsoCodeNumeric, :Symbol, :Position)
				ON DUPLICATE KEY UPDATE
					description=VALUES(description),
					isoCodeString=VALUES(isoCodeString),
					isoCodeNumeric=VALUES(isoCodeNumeric),
					symbol=VALUES(symbol),
					position=VALUES(position)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':IsoCodeString', $this->_IsoCodeString);
					$oStmt->bindValue(':IsoCodeNumeric', $this->_IsoCodeNumeric);
					$oStmt->bindValue(':Symbol', $this->_Symbol);
					$oStmt->bindValue(':Position', $this->_Position);

					if ( $oStmt->execute() ) {
						if ( !$this->getID() ) {
							$this->setID($oDB->lastInsertId());
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.currencies
			WHERE
				currencyID = :ID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ID', $this->_ID);

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
	 * @return mofilmCurrency
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Description = '';
		$this->_IsoCodeString = '';
		$this->_IsoCodeNumeric = 0;
		$this->_Symbol = '';
		$this->_Position = 'Before';
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
		$string .= " ID[$this->_ID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " IsoCodeString[$this->_IsoCodeString] $newLine";
		$string .= " IsoCodeNumeric[$this->_IsoCodeNumeric] $newLine";
		$string .= " Symbol[$this->_Symbol] $newLine";
		$string .= " Position[$this->_Position] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmCurrency';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"IsoCodeString\" value=\"$this->_IsoCodeString\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"IsoCodeNumeric\" value=\"$this->_IsoCodeNumeric\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Symbol\" value=\"$this->_Symbol\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Position\" value=\"$this->_Position\" type=\"string\" /> $newLine";
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
			$valid = $this->checkID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkIsoCodeString($message);
		}
		if ( $valid ) {
			$valid = $this->checkIsoCodeNumeric($message);
		}
		if ( $valid ) {
			$valid = $this->checkSymbol($message);
		}
		if ( $valid ) {
			$valid = $this->checkPosition($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_ID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ID) && $this->_ID !== 0 ) {
			$inMessage .= "{$this->_ID} is not a valid value for ID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Description has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkDescription(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Description) && $this->_Description !== '' ) {
			$inMessage .= "{$this->_Description} is not a valid value for Description";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) > 50 ) {
			$inMessage .= "Description cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_IsoCodeString has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkIsoCodeString(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_IsoCodeString) && $this->_IsoCodeString !== '' ) {
			$inMessage .= "{$this->_IsoCodeString} is not a valid value for IsoCodeString";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_IsoCodeString) > 3 ) {
			$inMessage .= "IsoCodeString cannot be more than 3 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_IsoCodeString) <= 1 ) {
			$inMessage .= "IsoCodeString must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_IsoCodeNumeric has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkIsoCodeNumeric(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_IsoCodeNumeric) && $this->_IsoCodeNumeric !== 0 ) {
			$inMessage .= "{$this->_IsoCodeNumeric} is not a valid value for IsoCodeNumeric";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Symbol has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkSymbol(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Symbol) && $this->_Symbol !== '' ) {
			$inMessage .= "{$this->_Symbol} is not a valid value for Symbol";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Symbol) > 5 ) {
			$inMessage .= "Symbol cannot be more than 5 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Symbol) <= 1 ) {
			$inMessage .= "Symbol must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Position has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkPosition(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Position) && $this->_Position !== '' ) {
			$inMessage .= "{$this->_Position} is not a valid value for Position";
			$isValid = false;
		}
		if ( $isValid && $this->_Position != '' && !in_array($this->_Position, array(self::POSITION_BEFORE, self::POSITION_AFTER)) ) {
			$inMessage .= "Position must be one of POSITION_BEFORE, POSITION_AFTER";
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
	 * @return mofilmCurrency
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
		return $this->_ID;
	}

	/**
	 * Return value of $_ID
	 *
	 * @return integer
	 * @access public
	 */
	function getID() {
		return $this->_ID;
	}

	/**
	 * Set $_ID to ID
	 *
	 * @param integer $inID
	 * @return mofilmCurrency
	 * @access public
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Description
	 *
	 * @return string
	 * @access public
	 */
	function getDescription() {
		return $this->_Description;
	}

	/**
	 * Set $_Description to Description
	 *
	 * @param string $inDescription
	 * @return mofilmCurrency
	 * @access public
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_IsoCodeString
	 *
	 * @return string
	 * @access public
	 */
	function getIsoCodeString() {
		return $this->_IsoCodeString;
	}

	/**
	 * Set $_IsoCodeString to IsoCodeString
	 *
	 * @param string $inIsoCodeString
	 * @return mofilmCurrency
	 * @access public
	 */
	function setIsoCodeString($inIsoCodeString) {
		if ( $inIsoCodeString !== $this->_IsoCodeString ) {
			$this->_IsoCodeString = $inIsoCodeString;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_IsoCodeNumeric
	 *
	 * @return integer
	 * @access public
	 */
	function getIsoCodeNumeric() {
		return $this->_IsoCodeNumeric;
	}

	/**
	 * Set $_IsoCodeNumeric to IsoCodeNumeric
	 *
	 * @param integer $inIsoCodeNumeric
	 * @return mofilmCurrency
	 * @access public
	 */
	function setIsoCodeNumeric($inIsoCodeNumeric) {
		if ( $inIsoCodeNumeric !== $this->_IsoCodeNumeric ) {
			$this->_IsoCodeNumeric = $inIsoCodeNumeric;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Symbol
	 *
	 * @return string
	 * @access public
	 */
	function getSymbol() {
		return $this->_Symbol;
	}

	/**
	 * Set $_Symbol to Symbol
	 *
	 * @param string $inSymbol
	 * @return mofilmCurrency
	 * @access public
	 */
	function setSymbol($inSymbol) {
		if ( $inSymbol !== $this->_Symbol ) {
			$this->_Symbol = $inSymbol;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Position
	 *
	 * @return string
	 * @access public
	 */
	function getPosition() {
		return $this->_Position;
	}

	/**
	 * Set $_Position to Position
	 *
	 * @param string $inPosition
	 * @return mofilmCurrency
	 * @access public
	 */
	function setPosition($inPosition) {
		if ( $inPosition !== $this->_Position ) {
			$this->_Position = $inPosition;
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
	 * @return mofilmCurrency
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}