<?php
/**
 * mofilmUserTeam
 * 
 * Stored in mofilmUserTeam.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserTeam
 * @category mofilmUserTeam
 * @version $Rev: 10 $
 */


/**
 * mofilmUserTeam Class
 * 
 * Provides access to records in mofilm_content.userTeams
 * 
 * Creating a new record:
 * <code>
 * $oMofilmUserTeam = new mofilmUserTeam();
 * $oMofilmUserTeam->setUserID($inUserID);
 * $oMofilmUserTeam->setTeamID($inTeamID);
 * $oMofilmUserTeam->setAdmin($inAdmin);
 * $oMofilmUserTeam->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserTeam = new mofilmUserTeam($inUserID, $inTeamID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmUserTeam = new mofilmUserTeam();
 * $oMofilmUserTeam->setUserID($inUserID);
 * $oMofilmUserTeam->setTeamID($inTeamID);
 * $oMofilmUserTeam->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserTeam = mofilmUserTeam::getInstance($inUserID, $inTeamID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmUserTeam
 * @category mofilmUserTeam
 */
class mofilmUserTeam implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmUserTeam
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
	 * Stores $_TeamID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_TeamID;
			
	/**
	 * Stores $_Admin
	 * 
	 * @var string (ADMIN_Y,ADMIN_N,)
	 * @access protected
	 */
	protected $_Admin;
	const ADMIN_Y = 'Y';
	const ADMIN_N = 'N';
	
	/**
	 * Store the team object
	 *
	 * @var mofilmTeam
	 * @access protected
	 */
	protected $_Team;

	/**
	 * Stores the deletion status
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion;


	
	/**
	 * Returns a new instance of mofilmUserTeam
	 * 
	 * @param integer $inUserID
	 * @param integer $inTeamID
	 * @return mofilmUserTeam
	 */
	function __construct($inUserID = null, $inTeamID = null) {
		$this->reset();
		if ( $inUserID !== null && $inTeamID !== null ) {
			$this->setUserID($inUserID);
			$this->setTeamID($inTeamID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new mofilmUserTeam containing non-unique properties
	 *
	 * @param integer $inTeamID
	 * @param string $inAdmin
	 * @return mofilmUserTeam
	 * @static 
	 */
	public static function factory($inTeamID = null, $inAdmin = null) {
		$oObject = new mofilmUserTeam;
		if ( $inTeamID !== null ) {
			$oObject->setTeamID($inTeamID);
		}
		if ( $inAdmin !== null ) {
			$oObject->setAdmin($inAdmin);
		}
		return $oObject;
	}

	/**
	 * Creates a new mofilmUserTeam from the Team object
	 *
	 * @param mofilmTeam $inTeam
	 * @param string $inAdmin
	 * @return mofilmUserTeam
	 * @static
	 */
	public static function factoryFromTeam(mofilmTeam $inTeam, $inAdmin = self::ADMIN_N) {
		$oObject = new mofilmUserTeam();
		$oObject->setTeam($inTeam);
		$oObject->setTeamID($inTeam->getID());
		$oObject->setAdmin($inAdmin);
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmUserTeam by primary key
	 * 
	 * @param integer $inUserID
	 * @param integer $inTeamID
	 * @return mofilmUserTeam
	 * @static 
	 */
	public static function getInstance($inUserID, $inTeamID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inUserID.'.'.$inTeamID]) ) {
			return self::$_Instances[$inUserID.'.'.$inTeamID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserTeam();
		$oObject->setUserID($inUserID);
		$oObject->setTeamID($inTeamID);
		if ( $oObject->load() ) {
			self::$_Instances[$inUserID.'.'.$inTeamID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmUserTeam
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userTeams';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserTeam();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userTeams';
		
		$where = array();
		if ( $this->_UserID !== 0 ) {
			$where[] = ' userID = :UserID ';
		}
		if ( $this->_TeamID !== 0 ) {
			$where[] = ' teamID = :TeamID ';
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
			if ( $this->_TeamID !== 0 ) {
				$oStmt->bindValue(':TeamID', $this->_TeamID);
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
		$this->setTeamID((int)$inArray['teamID']);
		$this->setAdmin($inArray['admin']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userTeams
					( userID, teamID, admin)
				VALUES 
					(:UserID, :TeamID, :Admin)
				ON DUPLICATE KEY UPDATE
					admin=VALUES(admin)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':TeamID', $this->getTeamID());
					$oStmt->bindValue(':Admin', $this->_Admin);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userTeams
		WHERE
			userID = :UserID AND 
			teamID = :TeamID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->_UserID);
			$oStmt->bindValue(':TeamID', $this->_TeamID);
				
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
	 * @return mofilmUserTeam
	 */
	function reset() {
		$this->_UserID = 0;
		$this->_TeamID = 0;
		$this->_Admin = '';
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
		$string .= " TeamID[$this->_TeamID] $newLine";
		$string .= " Admin[$this->_Admin] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmUserTeam';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"TeamID\" value=\"$this->_TeamID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Admin\" value=\"$this->_Admin\" type=\"string\" /> $newLine";
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
			$valid = $this->checkTeamID($message);
		}
		if ( $valid ) {
			$valid = $this->checkAdmin($message);
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
	 * Checks that $_TeamID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkTeamID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_TeamID) && $this->_TeamID !== 0 ) {
			$inMessage .= "{$this->_TeamID} is not a valid value for TeamID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Admin has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkAdmin(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Admin) && $this->_Admin !== '' ) {
			$inMessage .= "{$this->_Admin} is not a valid value for Admin";
			$isValid = false;
		}		
		if ( $isValid && $this->_Admin != '' && !in_array($this->_Admin, array(self::ADMIN_Y, self::ADMIN_N)) ) {
			$inMessage .= "Admin must be one of ADMIN_Y, ADMIN_N";
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
	 * @return mofilmUserTeam
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
		return $this->_UserID.'.'.$this->_TeamID;
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
	 * @return mofilmUserTeam
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
	 * Return value of $_TeamID
	 * 
	 * @return integer
	 * @access public
	 */
	function getTeamID() {
		if ( !$this->_TeamID && $this->_Team instanceof mofilmTeam ) {
			$this->_TeamID = $this->getTeam()->getID();
		}
		return $this->_TeamID;
	}
	
	/**
	 * Set $_TeamID to TeamID
	 * 
	 * @param integer $inTeamID
	 * @return mofilmUserTeam
	 * @access public
	 */
	function setTeamID($inTeamID) {
		if ( $inTeamID !== $this->_TeamID ) {
			$this->_TeamID = $inTeamID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Admin
	 * 
	 * @return string
	 * @access public
	 */
	function getAdmin() {
		return $this->_Admin;
	}
	
	/**
	 * Set $_Admin to Admin
	 * 
	 * @param string $inAdmin
	 * @return mofilmUserTeam
	 * @access public
	 */
	function setAdmin($inAdmin) {
		if ( $inAdmin !== $this->_Admin ) {
			$this->_Admin = $inAdmin;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the team instance, loading it if not already loaded
	 *
	 * @return mofilmTeam
	 */
	function getTeam() {
		if ( !$this->_Team instanceof mofilmTeam ) {
			$this->_Team = mofilmTeam::getInstance($this->getTeamID());
		}
		return $this->_Team;
	}

	/**
	 * Sets the team object instance
	 *
	 * @param mofilmTeam $inTeam
	 * @return mofilmUserTeam
	 */
	function setTeam(mofilmTeam $inTeam) {
		if ( $this->_Team !== $inTeam ) {
			$this->_Team = $inTeam;
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
	 * @return mofilmUserTeam
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}