<?php
/**
 * mofilmMovieLink
 *
 * Stored in mofilmMovieLink.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMovieLink
 * @category mofilmMovieLink
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieLink Class
 *
 * Provides access to records in mofilm_comms.movieLinks
 *
 * Creating a new record:
 * <code>
 * $oMofilmMovieLink = new mofilmMovieLink();
 * $oMofilmMovieLink->setID($inID);
 * $oMofilmMovieLink->setSenderID($inSenderID);
 * $oMofilmMovieLink->setMovieID($inMovieID);
 * $oMofilmMovieLink->setCreated($inCreated);
 * $oMofilmMovieLink->setHash($inHash);
 * $oMofilmMovieLink->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMovieLink = new mofilmMovieLink($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmMovieLink = new mofilmMovieLink();
 * $oMofilmMovieLink->setID($inID);
 * $oMofilmMovieLink->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmMovieLink = mofilmMovieLink::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmMovieLink
 * @category mofilmMovieLink
 */
class mofilmMovieLink implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmMovieLink
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
	 * Stores $_SenderID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_SenderID;

	/**
	 * Stores $_MovieID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;

	/**
	 * Stores $_Created
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_Created;

	/**
	 * Stores $_Hash
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Hash;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmMovieLink
	 *
	 * @param integer $inID
	 * @return mofilmMovieLink
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
	 * Creates a new mofilmMovieLink containing non-unique properties
	 *
	 * @param datetime $inCreated
	 * @return mofilmMovieLink
	 * @static
	 */
	public static function factory($inCreated = null) {
		$oObject = new mofilmMovieLink;
		if ( $inCreated !== null ) {
			$oObject->setCreated($inCreated);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmMovieLink by primary key
	 *
	 * @param integer $inID
	 * @return mofilmMovieLink
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
		$oObject = new mofilmMovieLink();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of mofilmMovieLink by unique key (hash)
	 *
	 * @param string $inHash
	 * @return mofilmMovieLink
	 * @static
	 */
	public static function getInstanceByHash($inHash) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inHash]) ) {
			return self::$_Instances[$inHash];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmMovieLink();
		$oObject->setHash($inHash);
		if ( $oObject->load() ) {
			self::$_Instances[$inHash] = $oObject;
		}
		return $oObject;
	}

	/**
	 * Get instance of mofilmMovieLink by unique key (unqSenderMovie)
	 *
	 * @param integer $inSenderID
	 * @param integer $inMovieID
	 * @return mofilmMovieLink
	 * @static
	 */
	public static function getInstanceByUnqSenderMovie($inSenderID, $inMovieID) {
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmMovieLink();
		$oObject->setSenderID($inSenderID);
		$oObject->setMovieID($inMovieID);
		$oObject->load();

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmMovieLink
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_comms').'.movieLinks';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMovieLink();
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
			SELECT ID, senderID, movieID, created, hash
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.movieLinks';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		if ( $this->_Hash !== '' ) {
			$where[] = ' hash = :Hash ';
		}
		if ( $this->_SenderID !== 0 ) {
			$where[] = ' senderID = :SenderID ';
		}
		if ( $this->_MovieID !== 0 ) {
			$where[] = ' movieID = :MovieID ';
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
			if ( $this->_Hash !== '' ) {
				$oStmt->bindValue(':Hash', $this->_Hash);
			}
			if ( $this->_SenderID !== 0 ) {
				$oStmt->bindValue(':SenderID', $this->_SenderID);
			}
			if ( $this->_MovieID !== 0 ) {
				$oStmt->bindValue(':MovieID', $this->_MovieID);
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
		$this->setSenderID((int)$inArray['senderID']);
		$this->setMovieID((int)$inArray['movieID']);
		$this->setCreated($inArray['created']);
		$this->setHash($inArray['hash']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.movieLinks
					( ID, senderID, movieID, created, hash)
				VALUES
					(:ID, :SenderID, :MovieID, :Created, :Hash)
				ON DUPLICATE KEY UPDATE
					created=VALUES(created)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':SenderID', $this->_SenderID);
					$oStmt->bindValue(':MovieID', $this->_MovieID);
					$oStmt->bindValue(':Created', $this->_Created);
					$oStmt->bindValue(':Hash', $this->_Hash);

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.movieLinks
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
	 * @return mofilmMovieLink
	 */
	function reset() {
		$this->_ID = 0;
		$this->_SenderID = 0;
		$this->_MovieID = 0;
		$this->_Created = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Hash = '';
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
		$string .= " SenderID[$this->_SenderID] $newLine";
		$string .= " MovieID[$this->_MovieID] $newLine";
		$string .= " Created[$this->_Created] $newLine";
		$string .= " Hash[$this->_Hash] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmMovieLink';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"SenderID\" value=\"$this->_SenderID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"MovieID\" value=\"$this->_MovieID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Created\" value=\"$this->_Created\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Hash\" value=\"$this->_Hash\" type=\"string\" /> $newLine";
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
			$valid = $this->checkSenderID($message);
		}
		if ( $valid ) {
			$valid = $this->checkMovieID($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreated($message);
		}
		if ( $valid ) {
			$valid = $this->checkHash($message);
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
	 * Checks that $_SenderID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkSenderID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_SenderID) && $this->_SenderID !== 0 ) {
			$inMessage .= "{$this->_SenderID} is not a valid value for SenderID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_MovieID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMovieID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MovieID) && $this->_MovieID !== 0 ) {
			$inMessage .= "{$this->_MovieID} is not a valid value for MovieID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Created has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCreated(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Created) && $this->_Created !== '' ) {
			$inMessage .= "{$this->_Created} is not a valid value for Created";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Hash has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkHash(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Hash) && $this->_Hash !== '' ) {
			$inMessage .= "{$this->_Hash} is not a valid value for Hash";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Hash) > 32 ) {
			$inMessage .= "Hash cannot be more than 32 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Hash) <= 1 ) {
			$inMessage .= "Hash must be more than 1 character";
			$isValid = false;
		}
		if ( !$this->getID() ) {
			$hash = $this->getHash();
			$oStmt = dbManager::getInstance()->prepare('SELECT hash FROM '.system::getConfig()->getDatabase('mofilm_comms').'.movieLinks WHERE hash = :Hash');
			$oStmt->bindParam(':Hash', $hash);
			
			$loop = true;
			$i = 10;
			while ( $loop === true ) {
				$oStmt->execute();
				if ( $oStmt->rowCount() > 0 ) {
					$hash = mofilmUtilities::buildMiniHash($this, 6);
				} else {
					$loop = false;
				}
				
				--$i;
				if ( $i == 0 ) {
					$loop = false;
				}
			}
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
	 * @return mofilmMovieLink
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
	 * @return mofilmMovieLink
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
	 * Return value of $_SenderID
	 *
	 * @return integer
	 * @access public
	 */
	function getSenderID() {
		return $this->_SenderID;
	}

	/**
	 * Set $_SenderID to SenderID
	 *
	 * @param integer $inSenderID
	 * @return mofilmMovieLink
	 * @access public
	 */
	function setSenderID($inSenderID) {
		if ( $inSenderID !== $this->_SenderID ) {
			$this->_SenderID = $inSenderID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_MovieID
	 *
	 * @return integer
	 * @access public
	 */
	function getMovieID() {
		return $this->_MovieID;
	}

	/**
	 * Set $_MovieID to MovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieLink
	 * @access public
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Created
	 *
	 * @return datetime
	 * @access public
	 */
	function getCreated() {
		return $this->_Created;
	}

	/**
	 * Set $_Created to Created
	 *
	 * @param datetime $inCreated
	 * @return mofilmMovieLink
	 * @access public
	 */
	function setCreated($inCreated) {
		if ( $inCreated !== $this->_Created ) {
			$this->_Created = $inCreated;
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
	 * @return mofilmMovieLink
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
	 * @return mofilmMovieLink
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}