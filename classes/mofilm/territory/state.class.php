<?php
/**
 * mofilmTerritoryState
 *
 * Stored in mofilmTerritoryState.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmTerritoryState
 * @category mofilmTerritoryState
 * @version $Rev: 10 $
 */


/**
 * mofilmTerritoryState Class
 *
 * Provides access to records in mofilm_content.territoryStates
 *
 * Creating a new record:
 * <code>
 * $oMofilmTerritoryState = new mofilmTerritoryState();
 * $oMofilmTerritoryState->setID($inID);
 * $oMofilmTerritoryState->setTerritoryID($inTerritoryID);
 * $oMofilmTerritoryState->setDescription($inDescription);
 * $oMofilmTerritoryState->setAbbreviation($inAbbreviation);
 * $oMofilmTerritoryState->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmTerritoryState = new mofilmTerritoryState($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmTerritoryState = new mofilmTerritoryState();
 * $oMofilmTerritoryState->setID($inID);
 * $oMofilmTerritoryState->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmTerritoryState = mofilmTerritoryState::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmTerritoryState
 * @category mofilmTerritoryState
 */
class mofilmTerritoryState implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmTerritoryState
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
	 * Stores $_TerritoryID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_TerritoryID;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_Abbreviation
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Abbreviation;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmTerritoryState
	 *
	 * @param integer $inID
	 * @return mofilmTerritoryState
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
	 * Creates a new mofilmTerritoryState containing non-unique properties
	 *
	 * @param integer $inTerritoryID
	 * @param string $inDescription
	 * @param string $inAbbreviation
	 * @return mofilmTerritoryState
	 * @static
	 */
	public static function factory($inTerritoryID = null, $inDescription = null, $inAbbreviation = null) {
		$oObject = new mofilmTerritoryState;
		if ( $inTerritoryID !== null ) {
			$oObject->setTerritoryID($inTerritoryID);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inAbbreviation !== null ) {
			$oObject->setAbbreviation($inAbbreviation);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmTerritoryState by primary key
	 *
	 * @param integer $inID
	 * @return mofilmTerritoryState
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
		$oObject = new mofilmTerritoryState();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmTerritoryState
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inTerritoryID
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inTerritoryID = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.territoryStates';
		if ( $inTerritoryID !== null && is_numeric($inTerritoryID) ) {
			$query .= ' WHERE territoryID = '.dbManager::getInstance()->quote($inTerritoryID);
		}
		
		$query .= ' ORDER BY territoryStates.description ASC ';
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmTerritoryState();
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
			SELECT ID, territoryID, description, abbreviation
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.territoryStates';

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
		$this->setTerritoryID((int)$inArray['territoryID']);
		$this->setDescription($inArray['description']);
		$this->setAbbreviation($inArray['abbreviation']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.territoryStates
					( ID, territoryID, description, abbreviation)
				VALUES
					(:ID, :TerritoryID, :Description, :Abbreviation)
				ON DUPLICATE KEY UPDATE
					territoryID=VALUES(territoryID),
					description=VALUES(description),
					abbreviation=VALUES(abbreviation)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':TerritoryID', $this->_TerritoryID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':Abbreviation', $this->_Abbreviation);

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.territoryStates
			WHERE
				ID = :ID
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
	 * @return mofilmTerritoryState
	 */
	function reset() {
		$this->_ID = 0;
		$this->_TerritoryID = 0;
		$this->_Description = '';
		$this->_Abbreviation = '';
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
		$string .= " TerritoryID[$this->_TerritoryID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " Abbreviation[$this->_Abbreviation] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmTerritoryState';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"TerritoryID\" value=\"$this->_TerritoryID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Abbreviation\" value=\"$this->_Abbreviation\" type=\"string\" /> $newLine";
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
			$valid = $this->checkTerritoryID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkAbbreviation($message);
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
	 * Checks that $_TerritoryID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkTerritoryID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_TerritoryID) && $this->_TerritoryID !== 0 ) {
			$inMessage .= "{$this->_TerritoryID} is not a valid value for TerritoryID";
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
		if ( $isValid && strlen($this->_Description) > 255 ) {
			$inMessage .= "Description cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Abbreviation has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkAbbreviation(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Abbreviation) && $this->_Abbreviation !== '' ) {
			$inMessage .= "{$this->_Abbreviation} is not a valid value for Abbreviation";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Abbreviation) > 50 ) {
			$inMessage .= "Abbreviation cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Abbreviation) <= 1 ) {
			$inMessage .= "Abbreviation must be more than 1 character";
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
	 * @return mofilmTerritoryState
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
	 * @return mofilmTerritoryState
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
	 * Return value of $_TerritoryID
	 *
	 * @return integer
	 * @access public
	 */
	function getTerritoryID() {
		return $this->_TerritoryID;
	}
	
	/**
	 * Returns the territory object
	 * 
	 * @return mofilmTerritory
	 */
	function getTerritory() {
		return mofilmTerritory::getInstance($this->getTerritoryID());
	}

	/**
	 * Set $_TerritoryID to TerritoryID
	 *
	 * @param integer $inTerritoryID
	 * @return mofilmTerritoryState
	 * @access public
	 */
	function setTerritoryID($inTerritoryID) {
		if ( $inTerritoryID !== $this->_TerritoryID ) {
			$this->_TerritoryID = $inTerritoryID;
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
	 * @return mofilmTerritoryState
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
	 * Return value of $_Abbreviation
	 *
	 * @return string
	 * @access public
	 */
	function getAbbreviation() {
		return $this->_Abbreviation;
	}

	/**
	 * Set $_Abbreviation to Abbreviation
	 *
	 * @param string $inAbbreviation
	 * @return mofilmTerritoryState
	 * @access public
	 */
	function setAbbreviation($inAbbreviation) {
		if ( $inAbbreviation !== $this->_Abbreviation ) {
			$this->_Abbreviation = $inAbbreviation;
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
	 * @return mofilmTerritoryState
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}