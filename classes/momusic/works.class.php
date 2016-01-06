<?php
/**
 * momusicWorks
 *
 * Stored in momusicWorks.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package momusic
 * @subpackage momusicWorks
 * @category momusicWorks
 * @version $Rev: 840 $
 */


/**
 * momusicWorks Class
 *
 * Provides access to records in momusic_content.musicWorks
 *
 * Creating a new record:
 * <code>
 * $oMomusicWork = new momusicWorks();
 * $oMomusicWork->setID($inID);
 * $oMomusicWork->setArtistID($inArtistID);
 * $oMomusicWork->setTrackName($inTrackName);
 * $oMomusicWork->setPath($inPath);
 * $oMomusicWork->setDuration($inDuration);
 * $oMomusicWork->setSource($inSource);
 * $oMomusicWork->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMomusicWork = new momusicWorks($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMomusicWork = new momusicWorks();
 * $oMomusicWork->setID($inID);
 * $oMomusicWork->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMomusicWork = momusicWorks::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package momusic
 * @subpackage momusicWorks
 * @category momusicWorks
 */
class momusicWorks implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of momusicWorks
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
	 * Stores $_ArtistID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ArtistID;

	/**
	 * Stores $_TrackName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_TrackName;

	/**
	 * Stores $_Path
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Path;

	/**
	 * Stores $_Duration
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Duration;

	/**
	 * Stores $_Source
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Source;

	/**
	 * Stores $_Sku
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Sku;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;
	
	/**
	 * Stores $_Status
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Status;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of momusicWorks
	 *
	 * @param integer $inID
	 * @return momusicWorks
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
	 * Get an instance of momusicWorks by primary key
	 *
	 * @param integer $inID
	 * @return momusicWorks
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
		$oObject = new momusicWorks();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get instance of momusicWorksddd by unique key (sku)
	 *
	 * @param integer $inSku
	 * @return momusicWorksddd
	 * @static
	 */
	public static function getInstanceBySku($inSku) {
		$key = $inSku;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new momusicWorks();
		$oObject->setSku($inSku);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}
		
	/**
	 * Returns an array of objects of momusicWorks
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
			SELECT ID, artistID, trackName, path, duration, source, sku, description, status
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicWorks
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicWorks();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}


	/**
	 * Returns an array of objects of momusicWork
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function getInstanceSongName($inName, $inArtist) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT artistID, trackName, path, duration,description 
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicWorks ';
		
		$where = array();
		$where[] = ' trackName = :trackName';
		$values[':trackName'] = $inName;
		$where[] = ' artistID = :artistID';
		$values[':artistID'] = $inArtist;
			

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);
		
		$oWorkObject = null;

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicWorks();
				$oObject->loadFromArray($row);
				$oWorkObject = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $oWorkObject;
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
			SELECT ID, artistID, trackName, path, duration, source, sku, description, status
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.musicWorks';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}

		if ( $this->_Sku !== 0 ) {
			$where[] = ' sku = :Sku ';
			$values[':Sku'] = $this->getSku();
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
		$this->setArtistID($inArray['artistID']);
		$this->setTrackName($inArray['trackName']);
		$this->setPath($inArray['path']);
		$this->setDuration((int)$inArray['duration']);
		$this->setSource($inArray['source']);
		$this->setSku((int)$inArray['sku']);
		$this->setDescription($inArray['description']);		
		$this->setStatus((int)$inArray['status']);
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
				INSERT INTO '.system::getConfig()->getDatabase('momusic_content').'.musicWorks
					( ID, artistID, trackName, path, duration, source, sku, description, status )
				VALUES
					( :ID, :ArtistID, :TrackName, :Path, :Duration, :Source, :Sku, :Description, :Status )
				ON DUPLICATE KEY UPDATE
					artistID=VALUES(artistID),
					trackName=VALUES(trackName),
					path=VALUES(path),
					duration=VALUES(duration),
					source=VALUES(source),
					description=VALUES(description),
					status=VALUES(status)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':ArtistID', $this->getArtistID());
				$oStmt->bindValue(':TrackName', $this->getTrackName());
				$oStmt->bindValue(':Path', $this->getPath());
				$oStmt->bindValue(':Duration', $this->getDuration());
				$oStmt->bindValue(':Source', $this->getSource());
				$oStmt->bindValue(':Sku', $this->getSku());
				$oStmt->bindValue(':Description', $this->getDescription());				
				$oStmt->bindValue(':Status', $this->getStatus());

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
			DELETE FROM '.system::getConfig()->getDatabase('momusic_content').'.musicWorks
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
	 * @return momusicWorks
	 */
	function reset() {
		$this->_ID = 0;
		$this->_ArtistID = '';
		$this->_TrackName = '';
		$this->_Path = '';
		$this->_Duration = 0;
		$this->_Source = '';
		$this->_Sku = 0;
		$this->_Description = '';		
		$this->_Status = 1;
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
	 * @return momusicWorks
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
			'_ArtistID' => array(
				'string' => array(),
			),
			'_TrackName' => array(
				'string' => array(),
			),
			'_Path' => array(
				'string' => array(),
			),
			'_Duration' => array(
				'number' => array(),
			),
			'_Source' => array(
				'string' => array(),
			),
			'_Sku' => array(
				'number' => array(),
			),
			'_Description' => array(
				'string' => array(),
			),
			'_Status' => array(
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
	 * @return momusicWorks
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
	 * momusicWorks::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return momusicWorks
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
	 * @return momusicWorks
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ArtistID
	 *
	 * @return integer
 	 */
	function getArtistID() {
		return $this->_ArtistID;
	}

	/**
	 * Set the object property _ArtistID to $inArtistID
	 *
	 * @param integer $inArtistID
	 * @return momusicWorks
	 */
	function setArtistID($inArtistID) {
		if ( $inArtistID !== $this->_ArtistID ) {
			$this->_ArtistID = $inArtistID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_TrackName
	 *
	 * @return string
 	 */
	function getTrackName() {
		return $this->_TrackName;
	}

	/**
	 * Set the object property _TrackName to $inTrackName
	 *
	 * @param string $inTrackName
	 * @return momusicWorks
	 */
	function setTrackName($inTrackName) {
		if ( $inTrackName !== $this->_TrackName ) {
			$this->_TrackName = $inTrackName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Path
	 *
	 * @return string
 	 */
	function getPath() {
		return $this->_Path;
	}

	/**
	 * Set the object property _Path to $inPath
	 *
	 * @param string $inPath
	 * @return momusicWorks
	 */
	function setPath($inPath) {
		if ( $inPath !== $this->_Path ) {
			$this->_Path = $inPath;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Duration
	 *
	 * @return integer
 	 */
	function getDuration() {
		return $this->_Duration;
	}

	/**
	 * Set the object property _Duration to $inDuration
	 *
	 * @param integer $inDuration
	 * @return momusicWorks
	 */
	function setDuration($inDuration) {
		if ( $inDuration !== $this->_Duration ) {
			$this->_Duration = $inDuration;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Source
	 *
	 * @return string
 	 */
	function getSource() {
		return $this->_Source;
	}

	/**
	 * Set the object property _Source to $inSource
	 *
	 * @param string $inSource
	 * @return momusicWorks
	 */
	function setSource($inSource) {
		if ( $inSource !== $this->_Source ) {
			$this->_Source = $inSource;
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
	 * @return momusicWorks
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
	
	/**
	 * Return the current value of the property $_Sku
	 *
	 * @return integer
 	 */
	function getSku() {
		return $this->_Sku;
	}

	/**
	 * Set the object property _Sku to $inSku
	 *
	 * @param integer $inSku
	 * @return momusicWork
	 */
	function setSku($inSku) {
		if ( $inSku !== $this->_Sku ) {
			$this->_Sku = $inSku;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Description
	 *
	 * @return string
 	 */
	function getDescription() {
		return $this->_Description;
	}

	/**
	 * Set the object property _Description to $inDescription
	 *
	 * @param string $inDescription
	 * @return momusicWork
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return the current value of the property $_Status
	 *
	 * @return integer
 	 */
	function getStatus() {
		return $this->_Status;
}

	/**
	 * Set the object property _Status to $inStatus
	 *
	 * @param integer $inStatus
	 * @return momusicWorks1
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

}