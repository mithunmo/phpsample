<?php
/**
 * commsPrsRoute
 *
 * Stored in commsPrsRoute.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsPrsRoute
 * @category commsPrsRoute
 * @version $Rev: 10 $
 */


/**
 * commsPrsRoute Class
 *
 * Provides access to records in comms.prsRoutes
 *
 * Creating a new record:
 * <code>
 * $oCommsPrsRoute = new commsPrsRoute();
 * $oCommsPrsRoute->setPrs($inPrs);
 * $oCommsPrsRoute->setCountryID($inCountryID);
 * $oCommsPrsRoute->setActive($inActive);
 * $oCommsPrsRoute->setShared($inShared);
 * $oCommsPrsRoute->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsPrsRoute = new commsPrsRoute($inPrs, $inCountryID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsPrsRoute = new commsPrsRoute();
 * $oCommsPrsRoute->setPrs($inPrs);
 * $oCommsPrsRoute->setCountryID($inCountryID);
 * $oCommsPrsRoute->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsPrsRoute = commsPrsRoute::getInstance($inPrs, $inCountryID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage commsPrsRoute
 * @category commsPrsRoute
 */
class commsPrsRoute implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsPrsRoute
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
	 * Stores $_Prs
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Prs;

	/**
	 * Stores $_CountryID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_CountryID;

	/**
	 * Stores $_Active
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Active;

	/**
	 * Stores $_Shared
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Shared;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of commsPrsRoute
	 *
	 * @param string $inPrs
	 * @param integer $inCountryID
	 * @return commsPrsRoute
	 */
	function __construct($inPrs = null, $inCountryID = null) {
		$this->reset();
		if ( $inPrs !== null && $inCountryID !== null ) {
			$this->setPrs($inPrs);
			$this->setCountryID($inCountryID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsPrsRoute containing non-unique properties
	 *
	 * @param integer $inActive
	 * @param integer $inShared
	 * @return commsPrsRoute
	 * @static
	 */
	public static function factory($inActive = null, $inShared = null) {
		$oObject = new commsPrsRoute;
		if ( $inActive !== null ) {
			$oObject->setActive($inActive);
		}
		if ( $inShared !== null ) {
			$oObject->setShared($inShared);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsPrsRoute by primary key
	 *
	 * @param string $inPrs
	 * @param integer $inCountryID
	 * @return commsPrsRoute
	 * @static
	 */
	public static function getInstance($inPrs, $inCountryID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inPrs.'.'.$inCountryID]) ) {
			return self::$_Instances[$inPrs.'.'.$inCountryID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsPrsRoute();
		$oObject->setPrs($inPrs);
		$oObject->setCountryID($inCountryID);
		if ( $oObject->load() ) {
			self::$_Instances[$inPrs.'.'.$inCountryID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of commsPrsRoute by unique key (prsCountry)
	 *
	 * @param string $inPrs
	 * @param integer $inCountryID
	 * @return commsPrsRoute
	 * @static
	 */
	public static function getInstanceByPrsCountry($inPrs, $inCountryID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inPrs.'.'.$inCountryID]) ) {
			return self::$_Instances[$inPrs.'.'.$inCountryID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsPrsRoute();
		$oObject->setPrs($inPrs);
		$oObject->setCountryID($inCountryID);
		if ( $oObject->load() ) {
			self::$_Instances[$inPrs.'.'.$inCountryID] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsPrsRoute
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.prsRoutes';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsPrsRoute();
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
			SELECT prs, countryID, active, shared
			  FROM '.system::getConfig()->getDatabase('comms').'.prsRoutes';

		$where = array();
		if ( $this->_Prs !== '' ) {
			$where[] = ' prs = :Prs ';
		}
		if ( $this->_CountryID !== 0 ) {
			$where[] = ' countryID = :CountryID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_Prs !== '' ) {
				$oStmt->bindValue(':Prs', $this->_Prs);
			}
			if ( $this->_CountryID !== 0 ) {
				$oStmt->bindValue(':CountryID', $this->_CountryID);
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
		$this->setPrs($inArray['prs']);
		$this->setCountryID((int)$inArray['countryID']);
		$this->setActive((int)$inArray['active']);
		$this->setShared((int)$inArray['shared']);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.prsRoutes
					( prs, countryID, active, shared)
				VALUES
					(:Prs, :CountryID, :Active, :Shared)
				ON DUPLICATE KEY UPDATE
					active=VALUES(active),
					shared=VALUES(shared)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':Prs', $this->_Prs);
					$oStmt->bindValue(':CountryID', $this->_CountryID);
					$oStmt->bindValue(':Active', $this->_Active);
					$oStmt->bindValue(':Shared', $this->_Shared);

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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.prsRoutes
			WHERE
				prs = :Prs AND
				countryID = :CountryID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':Prs', $this->_Prs);
			$oStmt->bindValue(':CountryID', $this->_CountryID);

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
	 * @return commsPrsRoute
	 */
	function reset() {
		$this->_Prs = '';
		$this->_CountryID = 0;
		$this->_Active = 0;
		$this->_Shared = 0;
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
		$string .= " Prs[$this->_Prs] $newLine";
		$string .= " CountryID[$this->_CountryID] $newLine";
		$string .= " Active[$this->_Active] $newLine";
		$string .= " Shared[$this->_Shared] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsPrsRoute';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"Prs\" value=\"$this->_Prs\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"CountryID\" value=\"$this->_CountryID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Active\" value=\"$this->_Active\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Shared\" value=\"$this->_Shared\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkPrs($message);
		}
		if ( $valid ) {
			$valid = $this->checkCountryID($message);
		}
		if ( $valid ) {
			$valid = $this->checkActive($message);
		}
		if ( $valid ) {
			$valid = $this->checkShared($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_Prs has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkPrs(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Prs) && $this->_Prs !== '' ) {
			$inMessage .= "{$this->_Prs} is not a valid value for Prs";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Prs) > 20 ) {
			$inMessage .= "Prs cannot be more than 20 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Prs) <= 1 ) {
			$inMessage .= "Prs must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
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
	 * Checks that $_Active has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkActive(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Active) && $this->_Active !== 0 ) {
			$inMessage .= "{$this->_Active} is not a valid value for Active";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Shared has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkShared(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Shared) && $this->_Shared !== 0 ) {
			$inMessage .= "{$this->_Shared} is not a valid value for Shared";
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
	 * @return commsPrsRoute
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
		return $this->_Prs.'.'.$this->_CountryID;
	}

	/**
	 * Return value of $_Prs
	 *
	 * @return string
	 * @access public
	 */
	function getPrs() {
		return $this->_Prs;
	}

	/**
	 * Set $_Prs to Prs
	 *
	 * @param string $inPrs
	 * @return commsPrsRoute
	 * @access public
	 */
	function setPrs($inPrs) {
		if ( $inPrs !== $this->_Prs ) {
			$this->_Prs = $inPrs;
			$this->setModified();
		}
		return $this;
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
	 * @return commsPrsRoute
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
	 * Return value of $_Active
	 *
	 * @return integer
	 * @access public
	 */
	function getActive() {
		return $this->_Active;
	}

	/**
	 * Set $_Active to Active
	 *
	 * @param integer $inActive
	 * @return commsPrsRoute
	 * @access public
	 */
	function setActive($inActive) {
		if ( $inActive !== $this->_Active ) {
			$this->_Active = $inActive;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Shared
	 *
	 * @return integer
	 * @access public
	 */
	function getShared() {
		return $this->_Shared;
	}

	/**
	 * Set $_Shared to Shared
	 *
	 * @param integer $inShared
	 * @return commsPrsRoute
	 * @access public
	 */
	function setShared($inShared) {
		if ( $inShared !== $this->_Shared ) {
			$this->_Shared = $inShared;
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
	 * @return commsPrsRoute
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}