<?php
/**
 * mofilmUserDownload
 * 
 * Stored in mofilmUserDownload.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserDownload
 * @category mofilmUserDownload
 * @version $Rev: 10 $
 */


/**
 * mofilmUserDownload Class
 * 
 * Provides access to records in mofilm_content.userDownloads
 * 
 * Creating a new record:
 * <code>
 * $oMofilmUserDownload = new mofilmUserDownload();
 * $oMofilmUserDownload->setID($inID);
 * $oMofilmUserDownload->setUserID($inUserID);
 * $oMofilmUserDownload->setDownloadID($inDownloadID);
 * $oMofilmUserDownload->setTimestamp($inTimestamp);
 * $oMofilmUserDownload->setIp($inIp);
 * $oMofilmUserDownload->setSourceID($inSourceID);
 * $oMofilmUserDownload->setEventID($inEventID);
 * $oMofilmUserDownload->setCountry($inCountry);
 * $oMofilmUserDownload->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserDownload = new mofilmUserDownload($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmUserDownload = new mofilmUserDownload();
 * $oMofilmUserDownload->setID($inID);
 * $oMofilmUserDownload->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserDownload = mofilmUserDownload::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmUserDownload
 * @category mofilmUserDownload
 */
class mofilmUserDownload implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmUserDownload
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
	 * Stores $_UserID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;
			
	/**
	 * Stores $_DownloadID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_DownloadID;
			
	/**
	 * Stores $_Timestamp
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_Timestamp;
			
	/**
	 * Stores $_Ip
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Ip;
			
	/**
	 * Stores $_SourceID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_SourceID;
			
	/**
	 * Stores $_EventID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_EventID;
			
	/**
	 * Stores $_Country
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Country;

	/**
	 * Stores $_MarkForDeletion
	 *
	 * @var boolean
	 * @access private
	 */
	private $_MarkForDeletion;
	
	
	
	/**
	 * Returns a new instance of mofilmUserDownload
	 * 
	 * @param integer $inID
	 * @return mofilmUserDownload
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
	 * Creates a new mofilmUserDownload containing non-unique properties
	 * 
	 * @param integer $inUserID
	 * @param integer $inDownloadID
	 * @param datetime $inTimestamp
	 * @param string $inIp
	 * @param integer $inSourceID
	 * @param integer $inEventID
	 * @param string $inCountry
	 * @return mofilmUserDownload
	 * @static 
	 */
	public static function factory($inUserID = null, $inDownloadID = null, $inTimestamp = null, $inIp = null, $inSourceID = null, $inEventID = null, $inCountry = null) {
		$oObject = new mofilmUserDownload;
		if ( $inUserID !== null ) {
			$oObject->setUserID($inUserID);
		}
		if ( $inDownloadID !== null ) {
			$oObject->setDownloadID($inDownloadID);
		}
		if ( $inTimestamp !== null ) {
			$oObject->setTimestamp($inTimestamp);
		}
		if ( $inIp !== null ) {
			$oObject->setIp($inIp);
		}
		if ( $inSourceID !== null ) {
			$oObject->setSourceID($inSourceID);
		}
		if ( $inEventID !== null ) {
			$oObject->setEventID($inEventID);
		}
		if ( $inCountry !== null ) {
			$oObject->setCountry($inCountry);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmUserDownload by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmUserDownload
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
		$oObject = new mofilmUserDownload();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmUserDownload
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inUserID
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inUserID = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userDownloads';
		if ( $inUserID !== null ) {
			$query .= ' WHERE userID = '.dbManager::getInstance()->quote($inUserID);
		}
		$query .= ' ORDER BY timestamp DESC ';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUserDownload();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.userDownloads';
		
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
		$this->setUserID((int)$inArray['userID']);
		$this->setDownloadID((int)$inArray['downloadID']);
		$this->setTimestamp($inArray['timestamp']);
		$this->setIp($inArray['ip']);
		$this->setSourceID((int)$inArray['sourceID']);
		$this->setEventID((int)$inArray['eventID']);
		$this->setCountry($inArray['country']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userDownloads
					( ID, userID, downloadID, timestamp, ip, sourceID, eventID, country)
				VALUES 
					(:ID, :UserID, :DownloadID, :Timestamp, :Ip, :SourceID, :EventID, :Country)
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					downloadID=VALUES(downloadID),
					timestamp=VALUES(timestamp),
					ip=VALUES(ip),
					sourceID=VALUES(sourceID),
					eventID=VALUES(eventID),
					country=VALUES(country)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':DownloadID', $this->_DownloadID);
					$oStmt->bindValue(':Timestamp', $this->_Timestamp);
					$oStmt->bindValue(':Ip', $this->_Ip);
					$oStmt->bindValue(':SourceID', $this->_SourceID);
					$oStmt->bindValue(':EventID', $this->_EventID);
					$oStmt->bindValue(':Country', $this->_Country);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userDownloads
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
	 * @return mofilmUserDownload
	 */
	function reset() {
		$this->_ID = 0;
		$this->_UserID = 0;
		$this->_DownloadID = 0;
		$this->_Timestamp = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Ip = '';
		$this->_SourceID = null;
		$this->_EventID = 0;
		$this->_Country = null;
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
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " DownloadID[$this->_DownloadID] $newLine";
		$string .= " Timestamp[$this->_Timestamp] $newLine";
		$string .= " Ip[$this->_Ip] $newLine";
		$string .= " SourceID[$this->_SourceID] $newLine";
		$string .= " EventID[$this->_EventID] $newLine";
		$string .= " Country[$this->_Country] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmUserDownload';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"DownloadID\" value=\"$this->_DownloadID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Timestamp\" value=\"$this->_Timestamp\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Ip\" value=\"$this->_Ip\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"SourceID\" value=\"$this->_SourceID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"EventID\" value=\"$this->_EventID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Country\" value=\"$this->_Country\" type=\"string\" /> $newLine";
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
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDownloadID($message);
		}
		if ( $valid ) {
			$valid = $this->checkTimestamp($message);
		}
		if ( $valid ) {
			$valid = $this->checkIp($message);
		}
		if ( $valid ) {
			$valid = $this->checkSourceID($message);
		}
		if ( $valid ) {
			$valid = $this->checkEventID($message);
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
	 * Checks that $_UserID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkUserID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_UserID) && $this->_UserID !== 0 ) {
			$inMessage .= "{$this->_UserID} is not a valid value for UserID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_DownloadID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkDownloadID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_DownloadID) && $this->_DownloadID !== 0 ) {
			$inMessage .= "{$this->_DownloadID} is not a valid value for DownloadID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Timestamp has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkTimestamp(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Timestamp) && $this->_Timestamp !== '' ) {
			$inMessage .= "{$this->_Timestamp} is not a valid value for Timestamp";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Ip has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkIp(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Ip) && $this->_Ip !== '' ) {
			$inMessage .= "{$this->_Ip} is not a valid value for Ip";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_Ip) > 15 ) {
			$inMessage .= "Ip cannot be more than 15 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Ip) <= 1 ) {
			$inMessage .= "Ip must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_SourceID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkSourceID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_SourceID) && $this->_SourceID !== null && $this->_SourceID !== 0 ) {
			$inMessage .= "{$this->_SourceID} is not a valid value for SourceID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_EventID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkEventID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_EventID) && $this->_EventID !== 0 ) {
			$inMessage .= "{$this->_EventID} is not a valid value for EventID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Country has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkCountry(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Country) && $this->_Country !== null && $this->_Country !== '' ) {
			$inMessage .= "{$this->_Country} is not a valid value for Country";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_Country) > 2 ) {
			$inMessage .= "Country cannot be more than 2 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Country) <= 1 ) {
			$inMessage .= "Country must be more than 1 character";
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
	 * @return mofilmUserDownload
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
	 * @return mofilmUserDownload
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
	 * Return value of $_UserID
	 * 
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}
	
	/**
	 * Set $_UserID to UserID
	 * 
	 * @param integer $inUserID
	 * @return mofilmUserDownload
	 * @access public
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_DownloadID
	 * 
	 * @return integer
	 * @access public
	 */
	function getDownloadID() {
		return $this->_DownloadID;
	}
	
	/**
	 * Set $_DownloadID to DownloadID
	 * 
	 * @param integer $inDownloadID
	 * @return mofilmUserDownload
	 * @access public
	 */
	function setDownloadID($inDownloadID) {
		if ( $inDownloadID !== $this->_DownloadID ) {
			$this->_DownloadID = $inDownloadID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Timestamp
	 * 
	 * @return datetime
	 * @access public
	 */
	function getTimestamp() {
		return $this->_Timestamp;
	}
	
	/**
	 * Set $_Timestamp to Timestamp
	 * 
	 * @param datetime $inTimestamp
	 * @return mofilmUserDownload
	 * @access public
	 */
	function setTimestamp($inTimestamp) {
		if ( $inTimestamp !== $this->_Timestamp ) {
			$this->_Timestamp = $inTimestamp;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Ip
	 * 
	 * @return string
	 * @access public
	 */
	function getIp() {
		return $this->_Ip;
	}
	
	/**
	 * Set $_Ip to Ip
	 * 
	 * @param string $inIp
	 * @return mofilmUserDownload
	 * @access public
	 */
	function setIp($inIp) {
		if ( $inIp !== $this->_Ip ) {
			$this->_Ip = $inIp;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_SourceID
	 * 
	 * @return integer
	 * @access public
	 */
	function getSourceID() {
		return $this->_SourceID;
	}
	
	/**
	 * Set $_SourceID to SourceID
	 * 
	 * @param integer $inSourceID
	 * @return mofilmUserDownload
	 * @access public
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_EventID
	 * 
	 * @return integer
	 * @access public
	 */
	function getEventID() {
		return $this->_EventID;
	}
	
	/**
	 * Set $_EventID to EventID
	 * 
	 * @param integer $inEventID
	 * @return mofilmUserDownload
	 * @access public
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Country
	 * 
	 * @return string
	 * @access public
	 */
	function getCountry() {
		return $this->_Country;
	}
	
	/**
	 * Set $_Country to Country
	 * 
	 * @param string $inCountry
	 * @return mofilmUserDownload
	 * @access public
	 */
	function setCountry($inCountry) {
		if ( $inCountry !== $this->_Country ) {
			$this->_Country = $inCountry;
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
	 * @return mofilmUserDownload
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}