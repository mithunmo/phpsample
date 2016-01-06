<?php
/**
 * mofilmContributor
 * 
 * Stored in mofilmContributor.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmContributor
 * @category mofilmContributor
 * @version $Rev: 10 $
 */


/**
 * mofilmContributor Class
 * 
 * Provides access to records in mofilm_content.contributors
 * 
 * Creating a new record:
 * <code>
 * $oMofilmContributor = new mofilmContributor();
 * $oMofilmContributor->setID($inID);
 * $oMofilmContributor->setName($inName);
 * $oMofilmContributor->setPhoto($inPhoto);
 * $oMofilmContributor->setWebsite($inWebsite);
 * $oMofilmContributor->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmContributor = new mofilmContributor($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmContributor = new mofilmContributor();
 * $oMofilmContributor->setID($inID);
 * $oMofilmContributor->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmContributor = mofilmContributor::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmContributor
 * @category mofilmContributor
 */
class mofilmContributor implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmContributor
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
			
	/**
	 * Stores $_Photo
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Photo;
			
	/**
	 * Stores $_Website
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Website;
			
	/**
	 * Stores $_CreateDate
	 * 
	 * @var date
	 * @access protected
	 */		
	protected $_CreateDate;
	
	/**
	 * Returns a new instance of mofilmContributor
	 * 
	 * @param integer $inID
	 * @return mofilmContributor
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
	 * Creates a new mofilmContributor containing non-unique properties
	 * 
	 * @param string $inName
	 * @param string $inPhoto
	 * @param string $inWebsite
	 * @return mofilmContributor
	 * @static 
	 */
	public static function factory($inName = null, $inPhoto = null, $inWebsite = null) {
		$oObject = new mofilmContributor;
		if ( $inName !== null ) {
			$oObject->setName($inName);
		}
		if ( $inPhoto !== null ) {
			$oObject->setPhoto($inPhoto);
		}
		if ( $inWebsite !== null ) {
			$oObject->setWebsite($inWebsite);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmContributor by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmContributor
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
		$oObject = new mofilmContributor();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
	
	
	/**
	 * Get instance of mofilmContributor by unique key (email)
	 *
	 * @param string $inEmail
	 * @return mofilmContributor
	 * @static
	 */
	public static function getInstanceByEmail($inEmail) {
		$query = 'SELECT * FROM ' . system::getConfig()->getDatabase('mofilm_content') . '.contributors';
				
		$where = array();
		$where[] = ' name = :Name ';
		$query .= ' WHERE ' . implode(' AND ', $where);
		
		$oContributorObject;
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':Name', $inEmail);

			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmContributor();
					$oObject->loadFromArray($row);
					$oContributorObject = $oObject;
				}
			}
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $oContributorObject;
	}
	
	/**
	 * Get array of mofilmContributor by email id
	 *
	 * @param string $inEmail
	 * @return mofilmContributor
	 * @static
	 */
	public static function getArrayOfInstancesByEmail($inEmail) {
		$query = 'SELECT * FROM ' . system::getConfig()->getDatabase('mofilm_content') . '.contributors';
				
		$where = array();
		$where[] = ' name = :Name ';
		$query .= ' WHERE ' . implode(' AND ', $where);
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':Name', $inEmail);

			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmContributor();
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
	 * Returns an array of objects of mofilmContributor
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.contributors';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmContributor();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.contributors';
		
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
		$this->setName($inArray['name']);
		$this->setPhoto($inArray['photo']);
		$this->setWebsite($inArray['website']);
		$this->setCreateDate($inArray['createDate']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.contributors
					( ID, name, photo, website, createDate)
				VALUES 
					(:ID, :Name, :Photo, :Website, :createDate)
				ON DUPLICATE KEY UPDATE
					name=VALUES(name),
					photo=VALUES(photo),
					website=VALUES(website),
					createDate=VALUES(createDate)';
				
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Name', $this->_Name);
					$oStmt->bindValue(':Photo', $this->_Photo);
					$oStmt->bindValue(':createDate', $this->_CreateDate);
					$oStmt->bindValue(':Website', $this->_Website);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.contributors
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
	 * @return mofilmContributor
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Name = '';
		$this->_Photo = null;
		$this->_Website = null;
		$this->_CreateDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());		
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
		$string .= " Photo[$this->_Photo] $newLine";
		$string .= " Website[$this->_Website] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmContributor';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Name\" value=\"$this->_Name\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Photo\" value=\"$this->_Photo\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Website\" value=\"$this->_Website\" type=\"string\" /> $newLine";
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
		if ( $valid ) {
			$valid = $this->checkPhoto($message);
		}
		if ( $valid ) {
			$valid = $this->checkWebsite($message);
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
		if ( $isValid && strlen($this->_Name) > 50 ) {
			$inMessage .= "Name cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Name) <= 1 ) {
			$inMessage .= "Name must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Photo has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkPhoto(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Photo) && $this->_Photo !== null && $this->_Photo !== '' ) {
			$inMessage .= "{$this->_Photo} is not a valid value for Photo";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Website has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkWebsite(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Website) && $this->_Website !== null && $this->_Website !== '' ) {
			$inMessage .= "{$this->_Website} is not a valid value for Website";
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
	 * @return mofilmContributor
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
	 * @return mofilmContributor
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
	 * @return mofilmContributor
	 * @access public
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Photo
	 * 
	 * @return string
	 * @access public
	 */
	function getPhoto() {
		return $this->_Photo;
	}
	
	/**
	 * Set $_Photo to Photo
	 * 
	 * @param string $inPhoto
	 * @return mofilmContributor
	 * @access public
	 */
	function setPhoto($inPhoto) {
		if ( $inPhoto !== $this->_Photo ) {
			$this->_Photo = $inPhoto;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Website
	 * 
	 * @return string
	 * @access public
	 */
	function getWebsite() {
		return $this->_Website;
	}
	
	/**
	 * Set $_Website to Website
	 * 
	 * @param string $inWebsite
	 * @return mofilmContributor
	 * @access public
	 */
	function setWebsite($inWebsite) {
		if ( $inWebsite !== $this->_Website ) {
			$this->_Website = $inWebsite;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return the current value of the property $_CreateDate
	 *
	 * @return systemDateTime
 	 */
	function getCreateDate() {
		return $this->_CreateDate;
	}

	/**
	 * Set the object property _CreateDate to $inCreateDate
	 *
	 * @param systemDateTime $inCreateDate
	 * @return mofilmUserMusicLicensess
	 */
	function setCreateDate($inCreateDate) {
		if ( $inCreateDate !== $this->_CreateDate ) {
			if ( !$inCreateDate instanceof DateTime ) {
				$inCreateDate = new systemDateTime($inCreateDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_CreateDate = $inCreateDate;
			$this->setModified();
		}
		return $this;
	}
	
}