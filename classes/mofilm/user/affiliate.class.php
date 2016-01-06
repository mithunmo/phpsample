<?php
/**
 * mofilmUserAffiliate
 * 
 * Stored in mofilmUserAffiliate.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserAffiliate
 * @category mofilmUserAffiliate
 * @version $Rev: 10 $
 */


/**
 * mofilmUserAffiliate Class
 * 
 * Provides access to records in mofilm_content.userAffiliates
 * 
 * Creating a new record:
 * <code>
 * $oMofilmUserAffiliate = new mofilmUserAffiliate();
 * $oMofilmUserAffiliate->setUserID($inUserID);
 * $oMofilmUserAffiliate->setAffiliate($inAffiliate);
 * $oMofilmUserAffiliate->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserAffiliate = new mofilmUserAffiliate($inUserID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmUserAffiliate = new mofilmUserAffiliate();
 * $oMofilmUserAffiliate->setUserID($inUserID);
 * $oMofilmUserAffiliate->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserAffiliate = mofilmUserAffiliate::getInstance($inUserID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmUserAffiliate
 * @category mofilmUserAffiliate
 */
class mofilmUserAffiliate implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmUserAffiliate
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
	 * Stores $_Affiliate
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Affiliate;
	
	
	
	/**
	 * Returns a new instance of mofilmUserAffiliate
	 * 
	 * @param integer $inUserID
	 * @return mofilmUserAffiliate
	 */
	function __construct($inUserID = null) {
		$this->reset();
		if ( $inUserID !== null ) {
			$this->setUserID($inUserID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new mofilmUserAffiliate containing non-unique properties
	 * 
	 * @param string $inAffiliate
	 * @return mofilmUserAffiliate
	 * @static 
	 */
	public static function factory($inAffiliate = null) {
		$oObject = new mofilmUserAffiliate;
		if ( $inAffiliate !== null ) {
			$oObject->setAffiliate($inAffiliate);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmUserAffiliate by primary key
	 * 
	 * @param integer $inUserID
	 * @return mofilmUserAffiliate
	 * @static 
	 */
	public static function getInstance($inUserID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inUserID]) ) {
			return self::$_Instances[$inUserID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserAffiliate();
		$oObject->setUserID($inUserID);
		if ( $oObject->load() ) {
			self::$_Instances[$inUserID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
	
	/**
	 * Returns an array of objects of mofilmUserAffiliate
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userAffiliates';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserAffiliate();
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
			SELECT userID, affiliate
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userAffiliates';
		
		$where = array();
		if ( $this->_UserID !== 0 ) {
			$where[] = ' userID = :UserID ';
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
		$this->setAffiliate($inArray['affiliate']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userAffiliates
					( userID, affiliate)
				VALUES 
					(:UserID, :Affiliate)
				ON DUPLICATE KEY UPDATE
					affiliate=VALUES(affiliate)';
				
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':Affiliate', $this->_Affiliate);
					
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userAffiliates
			WHERE
				userID = :UserID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->_UserID);
			
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
	 * @return mofilmUserAffiliate
	 */
	function reset() {
		$this->_UserID = 0;
		$this->_Affiliate = '';
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
		$string .= " Affiliate[$this->_Affiliate] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmUserAffiliate';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Affiliate\" value=\"$this->_Affiliate\" type=\"string\" /> $newLine";
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
			$valid = $this->checkAffiliate($message);
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
			$inMessage .= " is not a valid value for UserID";
			$isValid = false;
		}
		return $isValid;
	}
	
	/**
	 * Checks that $_Affiliate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkAffiliate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Affiliate) && $this->_Affiliate !== '' ) {
			$inMessage .= " is not a valid value for Affiliate";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Affiliate) > 30 ) {
			$inMessage .= "Affiliate cannot be more than 30 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Affiliate) <= 1 ) {
			$inMessage .= "Affiliate must be more than 1 character";
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
	 * @return mofilmUserAffiliate
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
		return $this->_UserID;
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
	 * @return mofilmUserAffiliate
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
	 * Return value of $_Affiliate
	 * 
	 * @return string
	 * @access public
	 */
	function getAffiliate() {
		return $this->_Affiliate;
	}
	
	/**
	 * Set $_Affiliate to Affiliate
	 * 
	 * @param string $inAffiliate
	 * @return mofilmUserAffiliate
	 * @access public
	 */
	function setAffiliate($inAffiliate) {
		if ( $inAffiliate !== $this->_Affiliate ) {
			$this->_Affiliate = $inAffiliate;
			$this->setModified();
		}
		return $this;
	}
}