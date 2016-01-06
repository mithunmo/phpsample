<?php
/**
 * momusicType
 *
 * Stored in momusicType.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package momusic
 * @subpackage momusicType
 * @category momusicType
 * @version $Rev: 840 $
 */


/**
 * momusicType Class
 *
 * Provides access to records in momusic.musicType
 *
 * Creating a new record:
 * <code>
 * $oMomusicType = new momusicType();
 * $oMomusicType->setID($inID);
 * $oMomusicType->setName($inName);
 * $oMomusicType->setType($inType);
 * $oMomusicType->setLastModified($inLastModified);
 * $oMomusicType->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMomusicType = new momusicType($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMomusicType = new momusicType();
 * $oMomusicType->setID($inID);
 * $oMomusicType->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMomusicType = momusicType::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package momusic
 * @subpackage momusicType
 * @category momusicType
 */
class momusicType implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	
	
	
	const PRIMARY_KEY_SEPARATOR = '.';
	const MUSIC_MOOD = 1;
	const MUSIC_STYLE  = 2;
	const MUSIC_GENRE  = 3;
	const MUSIC_INSTRUMENT = 4;
	static $type = array ("MUSIC_MOOD" => self::MUSIC_MOOD, "MUSIC_STYLE" => self::MUSIC_STYLE, "MUSIC_GENRE" => self::MUSIC_GENRE, "MUSIC_INSTRUMENT" => self::MUSIC_INSTRUMENT);
	

	/**
	 * Container for static instances of momusicType
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
	 * Stores the validator for this object
	 *
	 * @var utilityValidator
	 * @access protected
	 */
	protected $_Validator;

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
	 * @var integer 
	 * @access protected
	 */
	protected $_Type;

	/**
	 * Stores $_LastModified
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_LastModified;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of momusicType
	 *
	 * @param integer $inID
	 * @return momusicType
	 */
	function __construct($inID = null) {
		$this->reset();
		if ( $inID !== null ) {
			$this->setID($inID);
			$this->load();
		}
	}

	/**
	 * Object destructor, used to remove internal object instances
	 *
	 * @return void
 	 */
	function __destruct() {
		if ( $this->_Validator instanceof utilityValidator ) {
			$this->_Validator = null;
		}
	}

	/**
	 * Get an instance of momusicType by primary key
	 *
	 * @param integer $inID
	 * @return momusicType
	 * @static
	 */
	public static function getInstance($inID) {
		$key = $inID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new momusicType();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of momusicType
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, name, type, last_modified
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicType
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicType();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

	/**
	 * Returns an array of objects of momusicType
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function completeListOfObjectsByType($inType) {
		$values = array();

		$query = '
			SELECT distinct musicType.ID, musicType.name, musicType.type, musicType.last_modified
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicType';				  

		$where = array();
		$where[] = 'type = :type';
		$query .= ' WHERE ' . implode(' AND ', $where);
				
		
		$query .= ' order by name ASC';
		


		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':type', $inType);

		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicType();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}
	
	

	/**
	 * Returns an array of objects of momusicType
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjectsByType($inType, $inCatalog = null) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();
/*
                if ($inCatalog == "mozayic"){
                 $query = 	' SELECT distinct musicType.ID, musicType.name, musicType.type, musicType.last_modified
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicType, 
                           ' .system::getConfig()->getDatabase('momusic_content').'.work,  
                        '.system::getConfig()->getDatabase('momusic_content'). 
                                '.mozayic ' ;                                
                    
                } else {
*/
		$query = 
                        ' SELECT distinct musicType.ID, musicType.name, musicType.type, musicType.last_modified
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicType ';
 //               }
		$where = array();
		$where[] = 'type = :type';
		$query .= ' WHERE ' . implode(' AND ', $where);

/*                
                 if ($inCatalog == "mozayic"){
                     
                     
                    if ( $inType == momusicType::MUSIC_MOOD  ) {
                            $query .= ' AND  ( name = mood1 or name = mood2 or name = mood3 ) ';
                    }

                    if ( $inType == momusicType::MUSIC_STYLE  ) {
                            $query .= ' AND  ( name = style1 or name = style2 or name = style3 ) ';
                    }		
                    $query .= ' AND work.ID = mozayic.workID ';
                    
                }
*/
                
		$query .= ' order by name ASC';
		
                systemLog::message($query);
                       

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':type', $inType);

		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicType();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}
	
	

	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$values = array();

		$query = '
			SELECT ID, name, type, last_modified
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicType';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);

		$this->reset();
		if ( $oStmt->execute($values) ) {
			$row = $oStmt->fetch();
			if ( $row !== false && is_array($row) ) {
				$this->loadFromArray($row);
				$oStmt->closeCursor();
				$return = true;
			}
		}

		return $return;
	}

	/**
	 * Loads a record by array
	 *
	 * @param array $inArray
	 * @return void
 	 */
	function loadFromArray(array $inArray) {
		$this->setID((int)$inArray['ID']);
		$this->setName($inArray['name']);
		$this->setType((int)$inArray['type']);
		$this->setLastModified($inArray['last_modified']);
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
				throw new momusicException($message);
			}

			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('momusic_content').'.musicType
					( ID, name, type, last_modified )
				VALUES
					( :ID, :Name, :Type, :LastModified )
				ON DUPLICATE KEY UPDATE
					name=VALUES(name),
					type=VALUES(type),
					last_modified=VALUES(last_modified)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':Name', $this->getName());
				$oStmt->bindValue(':Type', $this->getType());
				$oStmt->bindValue(':LastModified', $this->getLastModified());

				if ( $oStmt->execute() ) {
					if ( !$this->getID() ) {
						$this->setID($oDB->lastInsertId());
					}
					$this->setModified(false);
					$return = true;
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
			DELETE FROM '.system::getConfig()->getDatabase('momusic_content').'.musicType
			WHERE
				ID = :ID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':ID', $this->getID());

		if ( $oStmt->execute() ) {
			$oStmt->closeCursor();
			$this->reset();
			return true;
		}

		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return momusicType
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Name = '';
		$this->_Type = 0;
		$this->_LastModified = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Validator = null;
		$this->setModified(false);
		$this->setMarkForDeletion(false);
		return $this;
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
	 * Returns the validator, creating one if not set
	 *
	 * @return utilityValidator
	 */
	function getValidator() {
		if ( !$this->_Validator instanceof utilityValidator ) {
			$this->_Validator = new utilityValidator();
		}
		return $this->_Validator;
	}

	/**
	 * Set a pre-built validator instance
	 *
	 * @param utilityValidator $inValidator
	 * @return momusicType
	 */
	function setValidator(utilityValidator $inValidator) {
		$this->_Validator = $inValidator;
		return $this;
	}

	/**
	 * Returns true if object is valid, any errors are added to $inMessage
	 *
	 * @param string $inMessage
	 * @return boolean
	 */
	function isValid(&$inMessage = '') {
		$valid = true;

		$oValidator = $this->getValidator();
		$oValidator->reset();
		$oValidator->setData($this->toArray())->setRules($this->getValidationRules());
		if ( !$oValidator->isValid() ) {
			foreach ( $oValidator->getMessages() as $key => $messages ) {
				$inMessage .= "Error with $key: ".implode(', ', $messages)."\n";
			}
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Returns the array of rules used to validate this object
	 *
	 * @return array
 	 */
	function getValidationRules() {
		return array(
			'_ID' => array(
				'number' => array(),
			),
			'_Name' => array(
				'string' => array(),
			),
			'_Type' => array(
				'number' => array(),
			),
			'_LastModified' => array(
				'dateTime' => array(),
			),
		);
	}



	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return momusicType
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the primaryKey
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->_ID;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * momusicType::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return momusicType
  	 */
	function setPrimaryKey($inKey) {
		list($ID) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setID($ID);
	}

	/**
	 * Return the current value of the property $_ID
	 *
	 * @return integer
 	 */
	function getID() {
		return $this->_ID;
	}

	/**
	 * Set the object property _ID to $inID
	 *
	 * @param integer $inID
	 * @return momusicType
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Name
	 *
	 * @return string
 	 */
	function getName() {
		return $this->_Name;
	}

	/**
	 * Set the object property _Name to $inName
	 *
	 * @param string $inName
	 * @return momusicType
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Type
	 *
	 * @return integer
 	 */
	function getType() {
		return $this->_Type;
	}

	/**
	 * Set the object property _Type to $inType
	 *
	 * @param integer $inType
	 * @return momusicType
	 */
	function setType($inType) {
		if ( $inType !== $this->_Type ) {
			$this->_Type = $inType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_LastModified
	 *
	 * @return systemDateTime
 	 */
	function getLastModified() {
		return $this->_LastModified;
	}

	/**
	 * Set the object property _LastModified to $inLastModified
	 *
	 * @param systemDateTime $inLastModified
	 * @return momusicType
	 */
	function setLastModified($inLastModified) {
		if ( $inLastModified !== $this->_LastModified ) {
			if ( !$inLastModified instanceof DateTime ) {
				$inLastModified = new systemDateTime($inLastModified, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_LastModified = $inLastModified;
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
	 * @return momusicType
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	
	public function getTypeName() {
		
		if ( $this->getType() == self::MUSIC_MOOD ) {
			return "mood";
		} else if ( $this->getType() == self::MUSIC_STYLE ) {
			return "style"; 
		} else if ( $this->getType() == self::MUSIC_GENRE ) {
			return "genre";
		} else if ( $this->getType() == self::MUSIC_INSTRUMENT ) {
			return "instrument";
		}
	}
}