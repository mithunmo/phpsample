<?php
/**
 * mofilmUserTerms
 * 
 * Stored in mofilmUserTerms.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserTerms
 * @category mofilmUserTerms
 * @version $Rev: 10 $
 */


/**
 * mofilmUserTerms Class
 * 
 * Provides access to records in mofilm_content.userTerms
 * 
 * Creating a new record:
 * <code>
 * $oMofilmUserTerm = new mofilmUserTerms();
 * $oMofilmUserTerm->setUserID($inUserID);
 * $oMofilmUserTerm->setTermsID($inTermsID);
 * $oMofilmUserTerm->setVersion($inVersion);
 * $oMofilmUserTerm->setAgreed($inAgreed);
 * $oMofilmUserTerm->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserTerm = new mofilmUserTerms($inUserID, $inTermsID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmUserTerm = new mofilmUserTerms();
 * $oMofilmUserTerm->setUserID($inUserID);
 * $oMofilmUserTerm->setTermsID($inTermsID);
 * $oMofilmUserTerm->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserTerm = mofilmUserTerms::getInstance($inUserID, $inTermsID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmUserTerms
 * @category mofilmUserTerms
 */
class mofilmUserTerms implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmUserTerms
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
	 * Stores $_UserID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;
			
	/**
	 * Stores $_TermsID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_TermsID;
			
	/**
	 * Stores $_Version
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_Version;
			
	/**
	 * Stores $_Agreed
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_Agreed;
	
	/**
	 * Stores an instance of mofilmTerms
	 *
	 * @var mofilmTerms
	 * @access protected
	 */
	protected $_Terms;

	/**
	 * Stores the deletion status
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion;


	
	/**
	 * Returns a new instance of mofilmUserTerms
	 * 
	 * @param integer $inUserID
	 * @param integer $inTermsID
	 * @return mofilmUserTerms
	 */
	function __construct($inUserID = null, $inTermsID = null) {
		$this->reset();
		if ( $inUserID !== null && $inTermsID !== null ) {
			$this->setUserID($inUserID);
			$this->setTermsID($inTermsID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new mofilmUserTerms containing non-unique properties
	 *
	 * @param integer $inTermsID
	 * @param datetime $inVersion
	 * @param datetime $inAgreed
	 * @return mofilmUserTerms
	 * @static 
	 */
	public static function factory($inTermsID = null, $inVersion = null, $inAgreed = null) {
		$oObject = new mofilmUserTerms;
		if ( !$inTermsID !== null ) {
			$oObject->setTermsID($inTermsID);
		}
		if ( $inVersion !== null ) {
			$oObject->setVersion($inVersion);
		}
		if ( $inAgreed !== null ) {
			$oObject->setAgreed($inAgreed);
		}
		return $oObject;
	}

	/**
	 * Creates a new user terms mapping from the supplied Terms object
	 *
	 * @param mofilmTerms $inTerms
	 * @return mofilmUserTerms
	 * @static
	 */
	public static function factoryFromTerms(mofilmTerms $inTerms) {
		$oObject = new mofilmUserTerms();
		$oObject->setTerms($inTerms);
		$oObject->setTermsID($inTerms->getID());
		$oObject->setVersion($inTerms->getVersion());
		$oObject->setAgreed(date(system::getConfig()->getDatabaseDatetimeFormat()));
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmUserTerms by primary key
	 * 
	 * @param integer $inUserID
	 * @param integer $inTermsID
	 * @return mofilmUserTerms
	 * @static 
	 */
	public static function getInstance($inUserID, $inTermsID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inUserID.'.'.$inTermsID]) ) {
			return self::$_Instances[$inUserID.'.'.$inTermsID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserTerms();
		$oObject->setUserID($inUserID);
		$oObject->setTermsID($inTermsID);
		if ( $oObject->load() ) {
			self::$_Instances[$inUserID.'.'.$inTermsID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmUserTerms
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inUserID
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inUserID = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userTerms';
		if ( $inUserID !== null ) {
			$query .= ' WHERE userID = '.dbManager::getInstance()->quote($inUserID);
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserTerms();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userTerms';
		
		$where = array();
		if ( $this->_UserID !== 0 ) {
			$where[] = ' userID = :UserID ';
		}
		if ( $this->_TermsID !== 0 ) {
			$where[] = ' termsID = :TermsID ';
		}
						
		if ( count($where) == 0 ) {
			return false;
		}
		
		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_UserID !== 0 ) {
				$oStmt->bindValue(':UserID', $this->_UserID);
			}
			if ( $this->_TermsID !== 0 ) {
				$oStmt->bindValue(':TermsID', $this->_TermsID);
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
		$this->setUserID((int)$inArray['userID']);
		$this->setTermsID((int)$inArray['termsID']);
		$this->setVersion($inArray['version']);
		$this->setAgreed($inArray['agreed']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userTerms
					( userID, termsID, version, agreed)
				VALUES 
					(:UserID, :TermsID, :Version, :Agreed)
				ON DUPLICATE KEY UPDATE
					version=VALUES(version),
					agreed=VALUES(agreed)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':TermsID', $this->getTermsID());
					$oStmt->bindValue(':Version', $this->getVersion());
					$oStmt->bindValue(':Agreed', $this->_Agreed);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userTerms
		WHERE
			userID = :UserID AND 
			termsID = :TermsID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->_UserID);
			$oStmt->bindValue(':TermsID', $this->_TermsID);
				
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
	 * @return mofilmUserTerms
	 */
	function reset() {
		$this->_UserID = 0;
		$this->_TermsID = 0;
		$this->_Version = '';
		$this->_Agreed = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->setModified(false);
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
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " TermsID[$this->_TermsID] $newLine";
		$string .= " Version[$this->_Version] $newLine";
		$string .= " Agreed[$this->_Agreed] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmUserTerms';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"TermsID\" value=\"$this->_TermsID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Version\" value=\"$this->_Version\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Agreed\" value=\"$this->_Agreed\" type=\"datetime\" /> $newLine";
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
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkTermsID($message);
		}
		if ( $valid ) {
			$valid = $this->checkVersion($message);
		}
		if ( $valid ) {
			$valid = $this->checkAgreed($message);
		}
		return $valid;
	}
		
	/**
	 * Checks that $_UserID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkUserID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_UserID) && $this->_UserID !== 0 ) {
			$inMessage .= "{$this->_UserID} is not a valid value for UserID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_TermsID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkTermsID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_TermsID) && $this->_TermsID !== 0 ) {
			$inMessage .= "{$this->_TermsID} is not a valid value for TermsID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Version has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkVersion(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Version) && $this->_Version !== '' ) {
			$inMessage .= "{$this->_Version} is not a valid value for Version";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Agreed has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkAgreed(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Agreed) && $this->_Agreed !== '' ) {
			$inMessage .= "{$this->_Agreed} is not a valid value for Agreed";
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
	 * @return mofilmUserTerms
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
		return $this->_UserID.'.'.$this->_TermsID;
	}
		
	/**
	 * Return value of $_UserID
	 * 
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}
	
	/**
	 * Set $_UserID to UserID
	 * 
	 * @param integer $inUserID
	 * @return mofilmUserTerms
	 * @access public
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_TermsID
	 * 
	 * @return integer
	 * @access public
	 */
	function getTermsID() {
		if ( !$this->_TermsID && $this->_Terms instanceof mofilmTerms ) {
			$this->_TermsID = $this->getTerms()->getID();
		}
		return $this->_TermsID;
	}
	
	/**
	 * Set $_TermsID to TermsID
	 * 
	 * @param integer $inTermsID
	 * @return mofilmUserTerms
	 * @access public
	 */
	function setTermsID($inTermsID) {
		if ( $inTermsID !== $this->_TermsID ) {
			$this->_TermsID = $inTermsID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Version
	 * 
	 * @return datetime
	 * @access public
	 */
	function getVersion() {
		if ( !$this->_Version && $this->_Terms instanceof mofilmTerms ) {
			$this->_Version = $this->getTerms()->getVersion();
		}
		return $this->_Version;
	}
	
	/**
	 * Set $_Version to Version
	 * 
	 * @param datetime $inVersion
	 * @return mofilmUserTerms
	 * @access public
	 */
	function setVersion($inVersion) {
		if ( $inVersion !== $this->_Version ) {
			$this->_Version = $inVersion;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Agreed
	 * 
	 * @return datetime
	 * @access public
	 */
	function getAgreed() {
		return $this->_Agreed;
	}
	
	/**
	 * Set $_Agreed to Agreed
	 * 
	 * @param datetime $inAgreed
	 * @return mofilmUserTerms
	 * @access public
	 */
	function setAgreed($inAgreed) {
		if ( $inAgreed !== $this->_Agreed ) {
			$this->_Agreed = $inAgreed;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the mofilm terms object, loading it if not already loaded
	 *
	 * @return mofilmTerms
	 */
	function getTerms() {
		if ( !$this->_Terms instanceof mofilmTerms ) {
			$this->_Terms = mofilmTerms::getInstance($this->getTermsID());
		}
		return $this->_Terms;
	}

	/**
	 * Sets a new instance of terms to the object
	 *
	 * @param mofilmTerms $inTerms
	 * @return mofilmUserTerms
	 */
	function setTerms(mofilmTerms $inTerms) {
		if ( !$this->_Terms !== $inTerms ) {
			$this->_Terms = $inTerms;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns true if the user has agreed to the current version of the terms
	 *
	 * @return boolean
	 */
	function hasUserAgreedTerms() {
		if ( $this->_Version == $this->getTerms()->getVersion() ) {
			return true;
		}
		return false;
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
	 * @return mofilmUserTerms
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}