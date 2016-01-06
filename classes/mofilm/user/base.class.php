<?php
/**
 * mofilmUserBase
 *
 * Stored in mofilmUserBase.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserBase
 * @category mofilmUserBase
 * @version $Rev: 371 $
 */


/**
 * mofilmUserBase Class
 *
 * Provides access to records in mofilm_content.users
 *
 * Creating a new record:
 * <code>
 * $oMofilmUser = new mofilmUserBase();
 * $oMofilmUser->setID($inID);
 * $oMofilmUser->setClientID($inClientID);
 * $oMofilmUser->setPassword($inPassword);
 * $oMofilmUser->setEmail($inEmail);
 * $oMofilmUser->setEnabled($inEnabled);
 * $oMofilmUser->setTerritoryID($inTerritoryID);
 * $oMofilmUser->setFirstname($inFirstname);
 * $oMofilmUser->setSurname($inSurname);
 * $oMofilmUser->setRegistered($inRegistered);
 * $oMofilmUser->setRegIP($inRegIP);
 * $oMofilmUser->setHash($inHash);
 * $oMofilmUser->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUser = new mofilmUserBase($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmUser = new mofilmUserBase();
 * $oMofilmUser->setID($inID);
 * $oMofilmUser->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmUser = mofilmUserBase::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmUserBase
 * @category mofilmUserBase
 */
class mofilmUserBase implements systemDaoInterface, systemDaoValidatorInterface {

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
	 * Stores $_ClientID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_ClientID;

	/**
	 * Stores $_Password
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Password;

	/**
	 * Stores $_Email
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Email;

	/**
	 * Stores $_Enabled
	 *
	 * @var string (ENABLED_Y,ENABLED_N,)
	 * @access protected
	 */
	protected $_Enabled;
	
	const ENABLED_Y = 'Y';
	const ENABLED_N = 'N';
	const AUTO_COMMIT_STATUS_ENABLED = 0;
	const AUTO_COMMIT_STATUS_DISABLED = 0;

	/**
	 * Stores $_TerritoryID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TerritoryID;

	/**
	 * Stores $_Firstname
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Firstname;

	/**
	 * Stores $_Surname
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Surname;

	/**
	 * Stores $_Registered
	 *
	 * @var datetime
	 * @access protected
	 */
	protected $_Registered;

	/**
	 * Stores $_RegIP
	 *
	 * @var string
	 * @access protected
	 */
	protected $_RegIP;

	/**
	 * Stores $_Hash
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Hash;

	/**
	 * Stores if the current password is hashed or not
	 *
	 * @var boolean
	 * @access private
	 */
	private $_PasswordHashed;
	
	/**
	 * Stores $_FacebookID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_FacebookID;	
	
	/**
	 * Stores $_AutoCommitStatus
	 * 
	 * @var integer
	 * @access protected 
	 */
	protected $_AutoCommitStatus;


	/**
	 * Returns a new instance of mofilmUserBase
	 *
	 * @param integer $inID
	 * @return mofilmUserBase
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
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.users';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		if ( $this->_Hash !== null ) {
			$where[] = ' hash = :Hash ';
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
			if ( $this->_Hash !== null ) {
				$oStmt->bindValue(':Hash', $this->_Hash);
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
		$this->setAutoCommitStatus((int)$inArray["autoCommitStatus"]);
		$this->setClientID((int)$inArray['clientID']);
		$this->setPassword($inArray['password']);
		$this->setEmail($inArray['email']);
		$this->setEnabled($inArray['enabled']);
		$this->setTerritoryID((int)$inArray['territoryID']);
		$this->setFacebookID((int)$inArray['facebookID']);
		$this->setFirstname($inArray['firstname']);
		$this->setSurname($inArray['surname']);
		$this->setRegistered($inArray['registered']);
		$this->setRegIP($inArray['regIP']);
		$this->setHash($inArray['hash']);
		$this->setPasswordHashed(true);
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
			if ( !$this->getPasswordHashed() && $this->getPassword() ) {
				$this->setPassword(md5($this->getPassword()));
			}

			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.users
					( ID, clientID, password, email, enabled, territoryID, facebookID, firstname, surname, registered, regIP, hash, autoCommitStatus)
				VALUES
					(:ID, :ClientID, :Password, :Email, :Enabled, :TerritoryID, :FacebookID, :Firstname, :Surname, :Registered, :RegIP, :Hash, :AutoCommitStatus)
				ON DUPLICATE KEY UPDATE
					clientID=VALUES(clientID),
					password=VALUES(password),
					email=VALUES(email),
					enabled=VALUES(enabled),
					territoryID=VALUES(territoryID),
					facebookID=VALUES(facebookID),
					firstname=VALUES(firstname),
					surname=VALUES(surname),
					registered=VALUES(registered),
					regIP=VALUES(regIP),
					autoCommitStatus=VALUES(autoCommitStatus)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':ClientID', $this->_ClientID);
					$oStmt->bindValue(':Password', $this->_Password);
					$oStmt->bindValue(':Email', $this->_Email);
					$oStmt->bindValue(':Enabled', $this->_Enabled);
					$oStmt->bindValue(':TerritoryID', $this->_TerritoryID);
					$oStmt->bindValue(':FacebookID', $this->_FacebookID);
					$oStmt->bindValue(':Firstname', $this->_Firstname);
					$oStmt->bindValue(':Surname', $this->_Surname);
					$oStmt->bindValue(':Registered', $this->_Registered);
					$oStmt->bindValue(':RegIP', $this->_RegIP);
					$oStmt->bindValue(':Hash', $this->_Hash);
					$oStmt->bindValue(':AutoCommitStatus', $this->_AutoCommitStatus);

					if ( $oStmt->execute() ) {
						if ( !$this->getID() ) {
							$this->setID($oDB->lastInsertId());
						}
						$this->setPasswordHashed(true);
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.users
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
	 * @return mofilmUserBase
	 */
	function reset() {
		$this->_ID = 0;
		$this->_ClientID = null;
		$this->_Password = null;
		$this->_Email = '';
		$this->_Enabled = 'Y';
		$this->_TerritoryID = null;
		$this->_FacebookID = null;
		$this->_Firstname = null;
		$this->_Surname = null;
		$this->_Registered = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_RegIP = null;
		$this->_Hash = null;
		$this->_AutoCommitStatus = 1;
		$this->_PasswordHashed = false;
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
		$string .= " ClientID[$this->_ClientID] $newLine";
		$string .= " Password[$this->_Password] $newLine";
		$string .= " Email[$this->_Email] $newLine";
		$string .= " Enabled[$this->_Enabled] $newLine";
		$string .= " TerritoryID[$this->_TerritoryID] $newLine";
		$string .= " Firstname[$this->_Firstname] $newLine";
		$string .= " Surname[$this->_Surname] $newLine";
		$string .= " Registered[$this->_Registered] $newLine";
		$string .= " RegIP[$this->_RegIP] $newLine";
		$string .= " Hash[$this->_Hash] $newLine";
		$string .= " AutoCommitStatus[$this->_AutoCommitStatus] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmUserBase';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ClientID\" value=\"$this->_ClientID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Password\" value=\"$this->_Password\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Email\" value=\"$this->_Email\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Enabled\" value=\"$this->_Enabled\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"TerritoryID\" value=\"$this->_TerritoryID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Firstname\" value=\"$this->_Firstname\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Surname\" value=\"$this->_Surname\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Registered\" value=\"$this->_Registered\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"RegIP\" value=\"$this->_RegIP\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Hash\" value=\"$this->_Hash\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"AutoCommitStatus\" value=\"$this->_AutoCommitStatus\" type=\"string\" /> $newLine";
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
			$valid = $this->checkPassword($message);
		}
		if ( $valid ) {
			$valid = $this->checkEmail($message);
		}
		if ( $valid ) {
			$valid = $this->checkEnabled($message);
		}
		if ( $valid ) {
			$valid = $this->checkRegistered($message);
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
	 * Checks that $_Password has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkPassword(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Password) && $this->_Password !== null && $this->_Password !== '' ) {
			$inMessage .= "{$this->_Password} is not a valid value for Password";
			$isValid = false;
		}
		if ( !$this->getPasswordHashed() ) {
			if ( $isValid && strlen($this->_Password) > 32 ) {
				$inMessage .= "Password cannot be more than 32 characters";
				$isValid = false;
			}
		}
		return $isValid;
	}

	/**
	 * Checks that $_Email has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkEmail(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Email) && $this->_Email !== '' ) {
			$inMessage .= "{$this->_Email} is not a valid value for Email";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Email) > 70 ) {
			$inMessage .= "Email cannot be more than 70 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Email) <= 1 ) {
			$inMessage .= "Email must be more than 1 character";
			$isValid = false;
		}
		if ( !$this->getID() ) {
			$query = 'SELECT ID FROM '.system::getConfig()->getDatabase('mofilm_content').'.users WHERE users.email = :Email LIMIT 1';
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':Email', $this->_Email);
			if ( $oStmt->execute() ) {
				$res = $oStmt->fetchAll();
				if ( is_array($res) && count($res) > 0 ) {
					$isValid = false;
					$inMessage .= 'This email address has already been registered';
				}
			}
		}
		return $isValid;
	}

	/**
	 * Checks that $_Enabled has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkEnabled(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Enabled) && $this->_Enabled !== '' ) {
			$inMessage .= "{$this->_Enabled} is not a valid value for Enabled";
			$isValid = false;
		}
		if ( $isValid && $this->_Enabled != '' && !in_array($this->_Enabled, array(self::ENABLED_Y, self::ENABLED_N)) ) {
			$inMessage .= "Enabled must be one of ENABLED_Y, ENABLED_N";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Registered has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkRegistered(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Registered) && $this->_Registered !== '' ) {
			$inMessage .= "{$this->_Registered} is not a valid value for Registered";
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
	 * @return mofilmUserBase
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
	 * Returns a random SHA1 hash for use as a login hash
	 * 
	 * @return string
	 */
	function getLoginHash() {
		return sha1(
			implode(':',
				array(
					uniqid(md5(microtime(true)), true),
					$this->getEmail(),
					$this->getID(),
				)
			)
		);
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
	 * @return mofilmUserBase
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
	 * Return value of $_ClientID
	 *
	 * @return integer
	 * @access public
	 */
	function getClientID() {
		return $this->_ClientID;
	}

	/**
	 * Returns the mofilmClient object
	 *
	 * @return mofilmClient
	 */
	function getClient() {
		return mofilmClient::getInstance($this->getClientID());
	}

	/**
	 * Set $_ClientID to ClientID
	 *
	 * @param integer $inClientID
	 * @return mofilmUserBase
	 * @access public
	 */
	function setClientID($inClientID) {
		if ( $inClientID !== $this->_ClientID ) {
			$this->_ClientID = $inClientID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns true if the user has set a password
	 * 
	 * @return boolean
	 */
	function hasPassword() {
		return $this->getPassword() != null;
	}

	/**
	 * Return value of $_Password
	 *
	 * @return string
	 * @access public
	 */
	function getPassword() {
		return $this->_Password;
	}

	/**
	 * Set $_Password to Password
	 *
	 * @param string $inPassword
	 * @return mofilmUserBase
	 * @access public
	 */
	function setPassword($inPassword) {
		if ( $inPassword !== $this->_Password ) {
			$this->_Password = $inPassword;
			$this->setPasswordHashed(false);
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Wrapper to getEmail()
	 * 
	 * @return string
	 */
	function getUsername() {
		return $this->getEmail();
	}

	/**
	 * Return value of $_Email
	 *
	 * @return string
	 * @access public
	 */
	function getEmail() {
		return $this->_Email;
	}

	/**
	 * Set $_Email to Email
	 *
	 * @param string $inEmail
	 * @return mofilmUserBase
	 * @access public
	 */
	function setEmail($inEmail) {
		if ( $inEmail !== $this->_Email ) {
			$this->_Email = $inEmail;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns true if account has been enabled
	 * 
	 * @return boolean
	 */
	function isEnabled() {
		return $this->getEnabled() == self::ENABLED_Y;
	}

	/**
	 * Return value of $_Enabled
	 *
	 * @return string
	 * @access public
	 */
	function getEnabled() {
		return $this->_Enabled;
	}

	/**
	 * Set $_Enabled to Enabled
	 *
	 * @param string $inEnabled
	 * @return mofilmUserBase
	 * @access public
	 */
	function setEnabled($inEnabled) {
		if ( $inEnabled !== $this->_Enabled ) {
			$this->_Enabled = $inEnabled;
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
	 * @return mofilmUserBase
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
	 * Return value of $_Firstname
	 *
	 * @return string
	 * @access public
	 */
	function getFirstname() {
		return $this->_Firstname;
	}

	/**
	 * Set $_Firstname to Firstname
	 *
	 * @param string $inFirstname
	 * @return mofilmUserBase
	 * @access public
	 */
	function setFirstname($inFirstname) {
		if ( $inFirstname !== $this->_Firstname ) {
			$this->_Firstname = $inFirstname;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Surname
	 *
	 * @return string
	 * @access public
	 */
	function getSurname() {
		return $this->_Surname;
	}

	/**
	 * Set $_Surname to Surname
	 *
	 * @param string $inSurname
	 * @return mofilmUserBase
	 * @access public
	 */
	function setSurname($inSurname) {
		if ( $inSurname !== $this->_Surname ) {
			$this->_Surname = $inSurname;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the fullname from first and surnames
	 * 
	 * @return string
	 */
	function getFullname() {
		if ( $this->getFirstname() && $this->getSurname() ) {
			return $this->getFirstname().' '.$this->getSurname();
		} else {
			if ( strlen($this->getEmail()) > 0 ) {
				return $this->getEmail();
			} else {
				return 'MOFILM-'.$this->getID();
			}
		}
	}

	/**
	 * Returns the fullname from first and surnames
	 * 
	 * @return string
	 */
	function getPropername() {
		if ( $this->getFirstname() && $this->getSurname() ) {
			return $this->getFirstname().' '.$this->getSurname();
		} else {
				return 'MOFILM-'.$this->getID();
		}
	}

	
	/**
	 * Return value of $_Registered
	 *
	 * @return datetime
	 * @access public
	 */
	function getRegistered() {
		return $this->_Registered;
	}

	/**
	 * Set $_Registered to Registered
	 *
	 * @param datetime $inRegistered
	 * @return mofilmUserBase
	 * @access public
	 */
	function setRegistered($inRegistered) {
		if ( $inRegistered !== $this->_Registered ) {
			$this->_Registered = $inRegistered;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_RegIP
	 *
	 * @return string
	 * @access public
	 */
	function getRegIP() {
		return $this->_RegIP;
	}

	/**
	 * Set $_RegIP to RegIP
	 *
	 * @param string $inRegIP
	 * @return mofilmUserBase
	 * @access public
	 */
	function setRegIP($inRegIP) {
		if ( $inRegIP !== $this->_RegIP ) {
			$this->_RegIP = $inRegIP;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Hash
	 *
	 * @return string
	 * @access public
	 */
	function getHash() {
		return $this->_Hash;
	}

	/**
	 * Set $_Hash to Hash
	 *
	 * @param string $inHash
	 * @return mofilmUserBase
	 * @access public
	 */
	function setHash($inHash) {
		if ( $inHash !== $this->_Hash ) {
			$this->_Hash = $inHash;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_PasswordHashed
	 *
	 * @return boolean
	 */
	function getPasswordHashed() {
		return $this->_PasswordHashed;
	}

	/**
	 * Set $_PasswordHashed to $inPasswordHashed
	 *
	 * @param boolean $inPasswordHashed
	 * @return mofilmUserBase
	 */
	function setPasswordHashed($inPasswordHashed) {
		if ( $inPasswordHashed !== $this->_PasswordHashed ) {
			$this->_PasswordHashed = $inPasswordHashed;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_FacebookID
	 *
	 * @return integer
	 * @access public
	 */
	function getFacebookID() {
		return $this->_FacebookID;
	}

	/**
	 * Set $_FacebookID to FacebookID
	 *
	 * @param integer $inFacebookID
	 * @return mofilmUserBase
	 * @access public
	 */
	function setFacebookID($inFacebookID) {
		if ( $inFacebookID !== $this->_FacebookID ) {
			$this->_FacebookID = $inFacebookID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_AutoCommitStatus
	 *
	 * @return integer
	 * @access public
	 */
	function getAutoCommitStatus() {
		return $this->_AutoCommitStatus;
	}

	/**
	 * Set $_AutoCommitStatus to $inAutoCommitStatus
	 *
	 * @param integer $inAutoCommitStatus
	 * @return mofilmUserBase
	 * @access public
	 */
	function setAutoCommitStatus($inAutoCommitStatus) {
		if ( $inAutoCommitStatus !== $this->_AutoCommitStatus ) {
			$this->_AutoCommitStatus = $inAutoCommitStatus;
			$this->setModified();
		}
		return $this;
	}
	
}