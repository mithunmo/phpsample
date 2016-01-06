<?php
/**
 * momusicSources
 *
 * Stored in momusicSources.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package momusic
 * @subpackage momusicSources
 * @category momusicSources
 * @version $Rev: 840 $
 */


/**
 * momusicSources Class
 *
 * Provides access to records in momusic_content.musicSources
 *
 * Creating a new record:
 * <code>
 * $oMomusicSource = new momusicSources();
 * $oMomusicSource->setMusicID($inMusicID);
 * $oMomusicSource->setEventID($inEventID);
 * $oMomusicSource->setSourceID($inSourceID);
 * $oMomusicSource->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMomusicSource = new momusicSources();
 * </code>
 *
 *
 * Accessing a record by instance:
 * <code>
 * $oMomusicSource = momusicSources::getInstance();
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package momusic
 * @subpackage momusicSources
 * @category momusicSources
 */
class momusicSources implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of momusicSources
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
	 * Stores $_MusicID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MusicID;

	/**
	 * Stores $_EventID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_EventID;

	/**
	 * Stores $_SourceID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SourceID;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of momusicSources
	 *
	 * @return momusicSources
	 */
	function __construct() {
		$this->reset();
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
	 * Get an instance of momusicSources by primary key
	 *
	 * @return momusicSources
	 * @static
	 */
	public static function getInstance() {
		//$key = ;

		/**
		 * Check for an existing instance
		 */
		$oObject = new momusicSources();

		return $oObject;
	}

	/**
	 * Returns an array of objects of momusicSources
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
			SELECT musicID, eventID, sourceID
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicSources
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicSources();
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
			SELECT musicID, eventID, sourceID
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicSources';

		$where = array();
		if ( $this->_MusicID !== 0 ) {
			$where[] = ' musicID = :MusicID ';
			$values[':MusicID'] = $this->getMusicID();
		}
		if ( $this->_EventID !== 0 ) {
			$where[] = ' eventID = :EventID ';
			$values[':EventID'] = $this->getEventID();
		}
		if ( $this->_SourceID !== 0 ) {
			$where[] = ' sourceID = :SourceID ';
			$values[':SourceID'] = $this->getSourceID();
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
		$this->setMusicID((int)$inArray['musicID']);
		$this->setEventID((int)$inArray['eventID']);
		$this->setSourceID((int)$inArray['sourceID']);
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
				INSERT INTO '.system::getConfig()->getDatabase('momusic_content').'.musicSources
					( musicID, eventID, sourceID )
				VALUES
					( :MusicID, :EventID, :SourceID )
				ON DUPLICATE KEY UPDATE
					musicID=VALUES(musicID),
					eventID=VALUES(eventID),
					sourceID=VALUES(sourceID)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':MusicID', $this->getMusicID());
				$oStmt->bindValue(':EventID', $this->getEventID());
				$oStmt->bindValue(':SourceID', $this->getSourceID());

				if ( $oStmt->execute() ) {
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
			DELETE FROM '.system::getConfig()->getDatabase('momusic_content').'.musicSources
			WHERE

			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);

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
	 * @return momusicSources
	 */
	function reset() {
		$this->_MusicID = 0;
		$this->_EventID = 0;
		$this->_SourceID = 0;
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
	 * @return momusicSources
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
			'_MusicID' => array(
				'number' => array(),
			),
			'_EventID' => array(
				'number' => array(),
			),
			'_SourceID' => array(
				'number' => array(),
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
	 * @return momusicSources
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
		return ;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * momusicSources::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return momusicSources
  	 */
	function setPrimaryKey($inKey) {
		list() = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
	}

	/**
	 * Return the current value of the property $_MusicID
	 *
	 * @return integer
 	 */
	function getMusicID() {
		return $this->_MusicID;
	}

	/**
	 * Set the object property _MusicID to $inMusicID
	 *
	 * @param integer $inMusicID
	 * @return momusicSources
	 */
	function setMusicID($inMusicID) {
		if ( $inMusicID !== $this->_MusicID ) {
			$this->_MusicID = $inMusicID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_EventID
	 *
	 * @return integer
 	 */
	function getEventID() {
		return $this->_EventID;
	}

	/**
	 * Set the object property _EventID to $inEventID
	 *
	 * @param integer $inEventID
	 * @return momusicSources
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SourceID
	 *
	 * @return integer
 	 */
	function getSourceID() {
		return $this->_SourceID;
	}

	/**
	 * Set the object property _SourceID to $inSourceID
	 *
	 * @param integer $inSourceID
	 * @return momusicSources
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
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
	 * @return momusicSources
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}