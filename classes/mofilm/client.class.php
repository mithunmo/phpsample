<?php
/**
 * mofilmClient
 * 
 * Stored in mofilmClient.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmClient
 * @category mofilmClient
 * @version $Rev: 10 $
 */


/**
 * mofilmClient Class
 * 
 * Provides access to records in mofilm_content.clients
 * 
 * Creating a new record:
 * <code>
 * $oMofilmClient = new mofilmClient();
 * $oMofilmClient->setID($inID);
 * $oMofilmClient->setCompanyName($inCompanyName);
 * $oMofilmClient->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmClient = new mofilmClient($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmClient = new mofilmClient();
 * $oMofilmClient->setID($inID);
 * $oMofilmClient->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmClient = mofilmClient::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmClient
 * @category mofilmClient
 */
class mofilmClient implements systemDaoInterface, systemDaoValidatorInterface {
	
	const MOFILM = 1;
	
	/**
	 * Container for static instances of mofilmClient
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
	 * Stores $_CompanyName
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_CompanyName;
	
	/**
	 * Stores $_DisableAllUsers
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_DisableAllUsers;
	
	/**
	 * Stores an instance of mofilmClientSourceSet
	 *
	 * @var mofilmClientSourceSet
	 * @access protected
	 */
	protected $_SourceSet;
	
	
	
	/**
	 * Returns a new instance of mofilmClient
	 * 
	 * @param integer $inID
	 * @return mofilmClient
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
	 * Creates a new mofilmClient containing non-unique properties
	 * 
	 * @param string $inCompanyName
	 * @return mofilmClient
	 * @static 
	 */
	public static function factory($inCompanyName = null) {
		$oObject = new mofilmClient;
		if ( $inCompanyName !== null ) {
			$oObject->setCompanyName($inCompanyName);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmClient by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmClient
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
		$oObject = new mofilmClient();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmClient
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.clients ORDER BY companyName ASC';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmClient();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.clients';
		
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
		$this->setCompanyName($inArray['companyName']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.clients
					( ID, companyName)
				VALUES 
					(:ID, :CompanyName)
				ON DUPLICATE KEY UPDATE
					companyName=VALUES(companyName)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':CompanyName', $this->_CompanyName);
								
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
			
			if ( $this->getDisableAllUsers() ) {
				$this->disableClientUsers();
			}
			
			if ( $this->_SourceSet instanceof mofilmClientSourceSet ) {
				$this->_SourceSet->setClientID($this->getID());
				$this->_SourceSet->save();
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.clients
		WHERE
			ID = :ID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ID', $this->_ID);
				
			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				
				if ( $this->hasLogo() ) {
					systemLog::notice('Attempting to remove logo @'.$this->getLogoLocation());
					@unlink($this->getLogoLocation());
				}
				
				$this->disableClientUsers();
				
				$this->getSourceSet()->delete();
				
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
	 * Disables all users associated with this client
	 * 
	 * @return boolean
	 */
	function disableClientUsers() {
		if ( $this->getID() ) {
			$query = 'UPDATE '.system::getConfig()->getDatabase('mofilm_content').'.users SET enabled = "N" WHERE clientID = :ClientID';
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ClientID', $this->getID());
			$oStmt->execute();
			$oStmt->closeCursor();
			systemLog::notice('Disabled all users on clientID '.$this->getID());
			return true;
		}
		return false;
	}
	
	/**
	 * Resets object properties to defaults
	 * 
	 * @return mofilmClient
	 */
	function reset() {
		$this->_ID = 0;
		$this->_CompanyName = '';
		$this->_DisableAllUsers = false;
		$this->_SourceSet = null;
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
		$string .= " CompanyName[$this->_CompanyName] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmClient';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CompanyName\" value=\"$this->_CompanyName\" type=\"string\" /> $newLine";
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
			$valid = $this->checkCompanyName($message);
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
	 * Checks that $_CompanyName has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkCompanyName(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_CompanyName) && $this->_CompanyName !== '' ) {
			$inMessage .= "{$this->_CompanyName} is not a valid value for CompanyName";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_CompanyName) > 40 ) {
			$inMessage .= "CompanyName cannot be more than 40 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_CompanyName) <= 1 ) {
			$inMessage .= "CompanyName must be more than 1 character";
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
		$modified = $this->_Modified;
		if ( !$modified && $this->_SourceSet !== null ) {
			$modified = $modified || $this->_SourceSet->isModified();
		}
		return $modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return mofilmClient
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
	 * @return mofilmClient
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
	 * Return value of $_CompanyName
	 * 
	 * @return string
	 * @access public
	 */
	function getCompanyName() {
		return $this->_CompanyName;
	}
	
	/**
	 * Set $_CompanyName to CompanyName
	 * 
	 * @param string $inCompanyName
	 * @return mofilmClient
	 * @access public
	 */
	function setCompanyName($inCompanyName) {
		if ( $inCompanyName !== $this->_CompanyName ) {
			$this->_CompanyName = $inCompanyName;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the logo name
	 * 
	 * @return string
	 */
	function getLogoName() {
		return 'logo_'.preg_replace('/[^a-z0-9]/', '', strtolower($this->getCompanyName())).'_s.jpg';
	}
	
	/**
	 * Returns true if the client has a logo uploaded
	 * 
	 * @return boolean
	 */
	function hasLogo() {
		return file_exists($this->getLogoLocation());
	}
	
	/**
	 * Returns the full path to the logo
	 * 
	 * @return string
	 */
	function getLogoLocation() {
		return mofilmConstants::getBrandLogosFolder().system::getDirSeparator().$this->getLogoName();
	}
	
	/**
	 * Returns the web path to the logo
	 * 
	 * @return string
	 */
	function getLogoWebLocation() {
		return str_replace(system::getConfig()->getPathWebsites().system::getDirSeparator().'base', '', $this->getLogoLocation());
	}
	
	
	
	/**
	 * Returns $_DisableAllUsers
	 *
	 * @return boolean
	 */
	function getDisableAllUsers() {
		return $this->_DisableAllUsers;
	}
	
	/**
	 * Set $_DisableAllUsers to $inDisableAllUsers
	 *
	 * @param boolean $inDisableAllUsers
	 * @return mofilmClient
	 */
	function setDisableAllUsers($inDisableAllUsers) {
		if ( $inDisableAllUsers !== $this->_DisableAllUsers ) {
			$this->_DisableAllUsers = $inDisableAllUsers;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns an instance of mofilmClientSourceSet, which is lazy loaded upon request
	 *
	 * @return mofilmClientSourceSet
	 */
	function getSourceSet() {
		if ( !$this->_SourceSet instanceof mofilmClientSourceSet ) {
			$this->_SourceSet = new mofilmClientSourceSet($this->getID());
		}
		return $this->_SourceSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param mofilmClientSourceSet $inObject
	 * @return mofilmClient
	 */
	function setSourceSet(mofilmClientSourceSet $inObject) {
		$this->_SourceSet = $inObject;
		return $this;
	}
}