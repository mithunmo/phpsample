<?php
/**
 * mofilmCommsListType
 *
 * Stored in mofilmCommsListType.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsListType
 * @category mofilmCommsListType
 * @version $Rev: 10 $
 */


/**
 * mofilmCommsListType Class
 *
 * Provides access to records in mofilm_comms.lists
 *
 * Creating a new record:
 * <code>
 * $oMofilmCommsListType = new mofilmCommsListType();
 * $oMofilmCommsListType->setID($inID);
 * $oMofilmCommsListType->setName($inName);
 * $oMofilmCommsListType->setDescription($inDescription);
 * $oMofilmCommsListType->setFolder($inFolder);
 * $oMofilmCommsListType->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmCommsListType = new mofilmCommsListType($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmCommsListType = new mofilmCommsListType();
 * $oMofilmCommsListType->setID($inID);
 * $oMofilmCommsListType->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmCommsListType = mofilmCommsListType::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmCommsListType
 * @category mofilmCommsListType
 */
class mofilmCommsListType implements systemDaoInterface, systemDaoValidatorInterface {
	
	const T_DAILY_DIGEST = 1;
	const T_MOFILM_NEWS = 2;
	
	/**
	 * Container for static instances of mofilmCommsListType
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
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_Folder
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Folder;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmCommsListType
	 *
	 * @param integer $inID
	 * @return mofilmCommsListType
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
	 * Creates a new mofilmCommsListType containing non-unique properties
	 *
	 * @param string $inName
	 * @param string $inDescription
	 * @param string $inFolder
	 * @return mofilmCommsListType
	 * @static
	 */
	public static function factory($inName = null, $inDescription = null, $inFolder = null) {
		$oObject = new mofilmCommsListType;
		if ( $inName !== null ) {
			$oObject->setName($inName);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inFolder !== null ) {
			$oObject->setFolder($inFolder);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmCommsListType by primary key
	 *
	 * @param integer $inID
	 * @return mofilmCommsListType
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
		$oObject = new mofilmCommsListType();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmCommsListType
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_comms').'.lists';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmCommsListType();
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
			SELECT ID, name, description, folder
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.lists';

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
		$this->setDescription($inArray['description']);
		$this->setFolder($inArray['folder']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.lists
					( ID, name, description, folder)
				VALUES
					(:ID, :Name, :Description, :Folder)
				ON DUPLICATE KEY UPDATE
					name=VALUES(name),
					description=VALUES(description),
					folder=VALUES(folder)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Name', $this->_Name);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':Folder', $this->_Folder);

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.lists
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
	 * @return mofilmCommsListType
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Name = '';
		$this->_Description = '';
		$this->_Folder = '';
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
		$string .= " Name[$this->_Name] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " Folder[$this->_Folder] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmCommsListType';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Name\" value=\"$this->_Name\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Folder\" value=\"$this->_Folder\" type=\"string\" /> $newLine";
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
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkFolder($message);
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
		if ( $isValid && strlen($this->_Name) > 40 ) {
			$inMessage .= "Name cannot be more than 40 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Name) <= 1 ) {
			$inMessage .= "Name must be more than 1 character";
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
		return $isValid;
	}

	/**
	 * Checks that $_Folder has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkFolder(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Folder) && $this->_Folder !== '' ) {
			$inMessage .= "{$this->_Folder} is not a valid value for Folder";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Folder) > 30 ) {
			$inMessage .= "Folder cannot be more than 30 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Folder) <= 1 ) {
			$inMessage .= "Folder must be more than 1 character";
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
	 * @return mofilmCommsListType
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
	 * @return mofilmCommsListType
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
	 * @return mofilmCommsListType
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
	 * @return mofilmCommsListType
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
	 * Return value of $_Folder
	 *
	 * @return string
	 * @access public
	 */
	function getFolder() {
		return $this->_Folder;
	}

	/**
	 * Set $_Folder to Folder
	 *
	 * @param string $inFolder
	 * @return mofilmCommsListType
	 * @access public
	 */
	function setFolder($inFolder) {
		if ( $inFolder !== $this->_Folder ) {
			$this->_Folder = $inFolder;
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
	 * @return mofilmCommsListType
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}