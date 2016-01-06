<?php
/**
 * momusicTag
 * 
 * Stored in momusicTag.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage momusicTag
 * @category momusicTag
 * @version $Rev: 10 $
 */


/**
 * momusicTag Class
 * 
 * Provides access to records in momusic_content.tags
 * 
 * Creating a new record:
 * <code>
 * $omomusicTag = new momusicTag();
 * $omomusicTag->setID($inID);
 * $omomusicTag->setName($inName);
 * $omomusicTag->setType($inType);
 * $omomusicTag->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $omomusicTag = new momusicTag($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $omomusicTag = new momusicTag();
 * $omomusicTag->setID($inID);
 * $omomusicTag->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $omomusicTag = momusicTag::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage momusicTag
 * @category momusicTag
 */
class momusicTag implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of momusicTag
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
	 * Stores $_Type
	 * 
	 * @var string (TYPE_TAG,TYPE_GENRE,TYPE_CATEGORY,)
	 * @access protected
	 */
	protected $_Type;
	const TYPE_TAG = 'tag';
	const TYPE_GENRE = 'genre';
	const TYPE_CATEGORY = 'category';
				
	
	
	/**
	 * Returns a new instance of momusicTag
	 * 
	 * @param integer $inID
	 * @return momusicTag
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
	 * Creates a new momusicTag containing non-unique properties
	 * 
	 * @param string $inName
	 * @param string $inType
	 * @return momusicTag
	 * @static 
	 */
	public static function factory($inName = null, $inType = null) {
		$oObject = new momusicTag;
		if ( $inName !== null ) {
			$oObject->setName($inName);
		}
		if ( $inType !== null ) {
			$oObject->setType($inType);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of momusicTag by primary key
	 * 
	 * @param integer $inID
	 * @return momusicTag
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
		$oObject = new momusicTag();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get an instance of momusicTag by tag
	 * 
	 * @param string $inTag
	 * @return momusicTag
	 * @static 
	 */
	public static function getInstanceByTag($inTag) {
		/**
		 * Check for an existing instance
		 */
		if ( count(self::$_Instances) > 0 ) {
			foreach ( self::$_Instances as $oObject ) {
				if ( $oObject->getName() == $inTag ) {
					return $oObject;
				}
			}
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new momusicTag();
		$oObject->setName($inTag);
		if ( $oObject->load() ) {
			self::$_Instances[$oObject->getID()] = $oObject;
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of momusicTag by tag and type
	 * 
	 * @param string $inTag
	 * @param string $inTagType
	 * @return momusicTag
	 * @static 
	 */
	public static function getInstanceByTagAndType($inTag, $inTagType) {
		/**
		 * Check for an existing instance
		 */
		if ( count(self::$_Instances) > 0 ) {
			foreach ( self::$_Instances as $oObject ) {
				if ( $oObject->getName() == $inTag && $oObject->getType() == $inTagType ) {
					return $oObject;
				}
			}
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new momusicTag();
		$oObject->setName($inTag);
		$oObject->setType($inTagType);
		if ( $oObject->load() ) {
			self::$_Instances[$oObject->getID()] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Get an instance of momusicTag by tag and type
	 * 
	 * @param string $inTag
	 * @param string $inTagType
	 * @return momusicTag
	 * @static 
	 */
	public static function getTagsByMovieID($inMovieID, $inType) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('momusic_content').'.tags';
		if ( $inMovieID !== null && in_array($inType, array(self::TYPE_GENRE, self::TYPE_TAG, self::TYPE_CATEGORY)) ) {
			$query .= ' INNER JOIN '.system::getConfig()->getDatabase('momusic_content').'.musicTags
				   ON (musicTags.tagID = tags.ID)';
			$query .= ' WHERE musicTags.movieID = '.dbManager::getInstance()->quote($inMovieID).' AND type = '.dbManager::getInstance()->quote($inType);
		}
		$query .= ' ORDER BY name ASC ';

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new momusicTag();
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
	 * Returns an array of objects of momusicTag
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param string $inType
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inType = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('momusic_content').'.tags';
		if ( $inType !== null && in_array($inType, array(self::TYPE_GENRE, self::TYPE_TAG, self::TYPE_CATEGORY)) ) {
			$query .= ' WHERE type = '.dbManager::getInstance()->quote($inType);
		}
		$query .= ' ORDER BY name ASC ';
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new momusicTag();
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
	 * Returns an array of objects of momusicTag
	 * 
	 * @param string $inKeyword
	 * @return array
	 * @static 
	 */
	public static function searchAutocompleteTag($inKeyword) {
		$query = 'SELECT name FROM '.system::getConfig()->getDatabase('momusic_content').'.tags WHERE type in ('.dbManager::getInstance()->quote(self::TYPE_TAG).','.dbManager::getInstance()->quote(self::TYPE_GENRE).') AND name like '.  dbManager::getInstance()->quote($inKeyword.'%');
		$query .= ' ORDER BY name ASC ';

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$list[] = $row['name'];
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
	 * Preloads the tag instances for bulk loading into movies
	 * 
	 * @return void
	 * @static
	 */
	public static function preloadInstances() {
		foreach ( self::listOfObjects() as $oObject ) {
			self::$_Instances[$oObject->getID()] = $oObject;
		}
	}
	
	
	
	/**
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('momusic_content').'.tags';
		
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
		$this->setType($inArray['type']);
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
				INSERT INTO '.system::getConfig()->getDatabase('momusic_content').'.tags
					( ID, name, type)
				VALUES 
					(:ID, :Name, :Type)
				ON DUPLICATE KEY UPDATE
					name=VALUES(name),
					type=VALUES(type)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Name', $this->_Name);
					$oStmt->bindValue(':Type', $this->_Type);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('momusic_content').'.tags
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
	 * @return momusicTag
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Name = NULL;
		$this->_Type = NULL;
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
		$string .= " Type[$this->_Type] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'momusicTag';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Name\" value=\"$this->_Name\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Type\" value=\"$this->_Type\" type=\"string\" /> $newLine";
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
			$valid = $this->checkType($message);
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
		if ( $isValid && strlen($this->_Name) > 200 ) {
			$inMessage .= "Name cannot be more than 200 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Name) <= 1 ) {
			$inMessage .= "Name must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Type has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkType(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Type) && $this->_Type !== '' ) {
			$inMessage .= "{$this->_Type} is not a valid value for Type";
			$isValid = false;
		}		
		if ( $isValid && $this->_Type != '' && !in_array($this->_Type, array(self::TYPE_TAG, self::TYPE_GENRE, self::TYPE_CATEGORY)) ) {
			$inMessage .= "Type must be one of TYPE_TAG, TYPE_GENRE, TYPE_CATEGORY";
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
	 * @return momusicTag
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
	 * @return momusicTag
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
	 * @return momusicTag
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
	 * Return value of $_Type
	 * 
	 * @return string
	 * @access public
	 */
	function getType() {
		return $this->_Type;
	}
	
	/**
	 * Set $_Type to Type
	 * 
	 * @param string $inType
	 * @return momusicTag
	 * @access public
	 */
	function setType($inType) {
		if ( $inType !== $this->_Type ) {
			$this->_Type = $inType;
			$this->setModified();
		}
		return $this;
	}
}