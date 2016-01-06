<?php
/**
 * commsStopKeyword
 *
 * Stored in commsStopKeyword.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsStopKeyword
 * @category commsStopKeyword
 * @version $Rev: 10 $
 */


/**
 * commsStopKeyword Class
 *
 * Provides access to records in comms.stopKeywords
 *
 * Creating a new record:
 * <code>
 * $oCommsStopKeyword = new commsStopKeyword();
 * $oCommsStopKeyword->setCountryID($inCountryID);
 * $oCommsStopKeyword->setKeyword($inKeyword);
 * $oCommsStopKeyword->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsStopKeyword = new commsStopKeyword($inCountryID, $inKeyword);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsStopKeyword = new commsStopKeyword();
 * $oCommsStopKeyword->setCountryID($inCountryID);
 * $oCommsStopKeyword->setKeyword($inKeyword);
 * $oCommsStopKeyword->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsStopKeyword = commsStopKeyword::getInstance($inCountryID, $inKeyword);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage commsStopKeyword
 * @category commsStopKeyword
 */
class commsStopKeyword implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsStopKeyword
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
	 * Stores $_CountryID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_CountryID;

	/**
	 * Stores $_Keyword
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Keyword;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of commsStopKeyword
	 *
	 * @param integer $inCountryID
	 * @param string $inKeyword
	 * @return commsStopKeyword
	 */
	function __construct($inCountryID = null, $inKeyword = null) {
		$this->reset();
		if ( $inCountryID !== null && $inKeyword !== null ) {
			$this->setCountryID($inCountryID);
			$this->setKeyword($inKeyword);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsStopKeyword containing non-unique properties
	 *
	 * @return commsStopKeyword
	 * @static
	 */
	public static function factory() {
		$oObject = new commsStopKeyword;
		return $oObject;
	}

	/**
	 * Get an instance of commsStopKeyword by primary key
	 *
	 * @param integer $inCountryID
	 * @param string $inKeyword
	 * @return commsStopKeyword
	 * @static
	 */
	public static function getInstance($inCountryID, $inKeyword) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inCountryID.'.'.$inKeyword]) ) {
			return self::$_Instances[$inCountryID.'.'.$inKeyword];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsStopKeyword();
		$oObject->setCountryID($inCountryID);
		$oObject->setKeyword($inKeyword);
		if ( $oObject->load() ) {
			self::$_Instances[$inCountryID.'.'.$inKeyword] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of commsStopKeyword by unique key (countryStopCode)
	 *
	 * @param integer $inCountryID
	 * @param string $inKeyword
	 * @return commsStopKeyword
	 * @static
	 */
	public static function getInstanceByCountryStopCode($inCountryID, $inKeyword) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inCountryID.'.'.$inKeyword]) ) {
			return self::$_Instances[$inCountryID.'.'.$inKeyword];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsStopKeyword();
		$oObject->setCountryID($inCountryID);
		$oObject->setKeyword($inKeyword);
		if ( $oObject->load() ) {
			self::$_Instances[$inCountryID.'.'.$inKeyword] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsStopKeyword
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.stopKeywords';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsStopKeyword();
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
			SELECT countryID, keyword
			  FROM '.system::getConfig()->getDatabase('comms').'.stopKeywords';

		$where = array();
		if ( $this->_CountryID !== 0 ) {
			$where[] = ' countryID = :CountryID ';
		}
		if ( $this->_Keyword !== '' ) {
			$where[] = ' keyword = :Keyword ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_CountryID !== 0 ) {
				$oStmt->bindValue(':CountryID', $this->_CountryID);
			}
			if ( $this->_Keyword !== '' ) {
				$oStmt->bindValue(':Keyword', $this->_Keyword);
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
		$this->setCountryID((int)$inArray['countryID']);
		$this->setKeyword($inArray['keyword']);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.stopKeywords
					( countryID, keyword)
				VALUES
					(:CountryID, :Keyword)
';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':CountryID', $this->_CountryID);
					$oStmt->bindValue(':Keyword', $this->_Keyword);

					if ( $oStmt->execute() ) {
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.stopKeywords
			WHERE
				countryID = :CountryID AND
				keyword = :Keyword
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':CountryID', $this->_CountryID);
			$oStmt->bindValue(':Keyword', $this->_Keyword);

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
	 * @return commsStopKeyword
	 */
	function reset() {
		$this->_CountryID = 0;
		$this->_Keyword = '';
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
		$string .= " CountryID[$this->_CountryID] $newLine";
		$string .= " Keyword[$this->_Keyword] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsStopKeyword';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"CountryID\" value=\"$this->_CountryID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Keyword\" value=\"$this->_Keyword\" type=\"string\" /> $newLine";
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
			$valid = $this->checkCountryID($message);
		}
		if ( $valid ) {
			$valid = $this->checkKeyword($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_CountryID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCountryID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_CountryID) && $this->_CountryID !== 0 ) {
			$inMessage .= "{$this->_CountryID} is not a valid value for CountryID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Keyword has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkKeyword(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Keyword) && $this->_Keyword !== '' ) {
			$inMessage .= "{$this->_Keyword} is not a valid value for Keyword";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Keyword) > 20 ) {
			$inMessage .= "Keyword cannot be more than 20 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Keyword) <= 1 ) {
			$inMessage .= "Keyword must be more than 1 character";
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
	 * @return commsStopKeyword
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
		return $this->_CountryID.'.'.$this->_Keyword;
	}

	/**
	 * Return value of $_CountryID
	 *
	 * @return integer
	 * @access public
	 */
	function getCountryID() {
		return $this->_CountryID;
	}

	/**
	 * Set $_CountryID to CountryID
	 *
	 * @param integer $inCountryID
	 * @return commsStopKeyword
	 * @access public
	 */
	function setCountryID($inCountryID) {
		if ( $inCountryID !== $this->_CountryID ) {
			$this->_CountryID = $inCountryID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Keyword
	 *
	 * @return string
	 * @access public
	 */
	function getKeyword() {
		return $this->_Keyword;
	}

	/**
	 * Set $_Keyword to Keyword
	 *
	 * @param string $inKeyword
	 * @return commsStopKeyword
	 * @access public
	 */
	function setKeyword($inKeyword) {
		if ( $inKeyword !== $this->_Keyword ) {
			$this->_Keyword = $inKeyword;
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
	 * @return commsStopKeyword
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}