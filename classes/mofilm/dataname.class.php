<?php
/**
 * mofilmDataname
 * 
 * Stored in mofilmDataname.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmDataname
 * @category mofilmDataname
 * @version $Rev: 308 $
 */


/**
 * mofilmDataname Class
 * 
 * Provides access to records in mofilm_content.datanames
 * 
 * Creating a new record:
 * <code>
 * $oMofilmDataname = new mofilmDataname();
 * $oMofilmDataname->setID($inID);
 * $oMofilmDataname->setName($inName);
 * $oMofilmDataname->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmDataname = new mofilmDataname($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmDataname = new mofilmDataname();
 * $oMofilmDataname->setID($inID);
 * $oMofilmDataname->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmDataname = mofilmDataname::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmDataname
 * @category mofilmDataname
 */
class mofilmDataname implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmDataname
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
	 * Stores $_Name
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Name;
	
	const DATA_USER_IP = 'userIP';
	const DATA_SELFMADE = 'Selfmade';
	const DATA_MUSIC = 'Music';
	const DATA_COPYRIGHT = 'Copyright';
	const DATA_PREVIEW = 'Preview';
	const DATA_EMPLOYEE = 'Employee';
	const DATA_OLDID = 'oldID';
	const DATA_OTHER_TRACK_SOURCE = 'OtherTrackSource';
	const DATA_USER_COUNTRY_CODE = 'UserCountryCode';
	const DATA_MOVIE_LICENSEID = "licenseID";
        const DATA_BROADCAST_DATE = "BroadcastDate";
        const DATA_BROADCAST_NOTE = "BroadcastNote";
	
	
	
	/**
	 * Returns a new instance of mofilmDataname
	 * 
	 * @param integer $inID
	 * @return mofilmDataname
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
	 * Creates a new mofilmDataname containing non-unique properties
	 * 
	 * @param string $inName
	 * @return mofilmDataname
	 * @static 
	 */
	public static function factory($inName = null) {
		$oObject = new mofilmDataname;
		if ( $inName !== null ) {
			$oObject->setName($inName);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmDataname by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmDataname
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
		$oObject = new mofilmDataname();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmDataname by dataname
	 * 
	 * @param string $inDataname
	 * @return mofilmDataname
	 * @static 
	 */
	public static function getInstanceByDataname($inDataname) {
		/**
		 * Check for an existing instance
		 */
		if ( count(self::$_Instances) > 0 ) {
			foreach ( self::$_Instances as $oObject ) {
				if ( $oObject->getName() == $inDataname ) {
					return $oObject;
				}
			}
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmDataname();
		$oObject->setName($inDataname);
		if ( $oObject->load() ) {
			self::$_Instances[$oObject->getID()] = $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmDataname
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.datanames';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmDataname();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.datanames';
		
		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		if ( $this->_ID === 0 && $this->_Name ) {
			$where[] = ' name = :Name ';
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
			if ( $this->_ID === 0 && $this->_Name ) {
				$oStmt->bindValue(':Name', $this->_Name);
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
		$this->setName($inArray['name']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.datanames
					( ID, name)
				VALUES 
					(:ID, :Name)
				ON DUPLICATE KEY UPDATE
					name=VALUES(name)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Name', $this->_Name);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.datanames
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
	 * @return mofilmDataname
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Name = '';
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
		$string .= " ID[$this->_ID] $newLine";
		$string .= " Name[$this->_Name] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmDataname';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Name\" value=\"$this->_Name\" type=\"string\" /> $newLine";
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
			$valid = $this->checkName($message);
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
	 * Checks that $_Name has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkName(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Name) && $this->_Name !== '' ) {
			$inMessage .= "{$this->_Name} is not a valid value for Name";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_Name) > 30 ) {
			$inMessage .= "Name cannot be more than 30 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Name) <= 1 ) {
			$inMessage .= "Name must be more than 1 character";
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
	 * @return mofilmDataname
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
	 * @return mofilmDataname
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
	 * Return value of $_Name
	 * 
	 * @return string
	 * @access public
	 */
	function getName() {
		return $this->_Name;
	}
	
	/**
	 * Set $_Name to Name
	 * 
	 * @param string $inName
	 * @return mofilmDataname
	 * @access public
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}
}