<?php
/**
 * mofilmOriginXML
 * 
 * Stored in mofilmOriginXML.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmOriginXML
 * @category mofilmOriginXML
 * @version $Rev: 10 $
 */


/**
 * mofilmOriginXML Class
 * 
 * Provides access to records in mofilm_content.originXML
 * 
 * Creating a new record:
 * <code>
 * $oMofilmOriginXML = new mofilmOriginXML();
 * $oMofilmOriginXML->setID($inID);
 * $oMofilmOriginXML->setXml($inXml);
 * $oMofilmOriginXML->setReceived($inReceived);
 * $oMofilmOriginXML->setStatus($inStatus);
 * $oMofilmOriginXML->setMovieID($inMovieID);
 * $oMofilmOriginXML->setNotes($inNotes);
 * $oMofilmOriginXML->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmOriginXML = new mofilmOriginXML($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmOriginXML = new mofilmOriginXML();
 * $oMofilmOriginXML->setID($inID);
 * $oMofilmOriginXML->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmOriginXML = mofilmOriginXML::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmOriginXML
 * @category mofilmOriginXML
 */
class mofilmOriginXML implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmOriginXML
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
	 * Stores $_Xml
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Xml;
			
	/**
	 * Stores $_Received
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_Received;
			
	/**
	 * Stores $_Status
	 * 
	 * @var string (STATUS_QUEUED,STATUS_PROCESSING,STATUS_FAILED,STATUS_SUCCESS,)
	 * @access protected
	 */
	protected $_Status;
	const STATUS_QUEUED = 'Queued';
	const STATUS_PROCESSING = 'Processing';
	const STATUS_FAILED = 'Failed';
	const STATUS_SUCCESS = 'Success';
				
	/**
	 * Stores $_MovieID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;
			
	/**
	 * Stores $_Notes
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Notes;
			
	
	
	/**
	 * Returns a new instance of mofilmOriginXML
	 * 
	 * @param integer $inID
	 * @return mofilmOriginXML
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
	 * Creates a new mofilmOriginXML containing non-unique properties
	 * 
	 * @param string $inXml
	 * @param datetime $inReceived
	 * @param string $inStatus
	 * @param integer $inMovieID
	 * @param string $inNotes
	 * @return mofilmOriginXML
	 * @static 
	 */
	public static function factory($inXml = null, $inReceived = null, $inStatus = null, $inMovieID = null, $inNotes = null) {
		$oObject = new mofilmOriginXML;
		if ( $inXml !== null ) {
			$oObject->setXml($inXml);
		}
		if ( $inReceived !== null ) {
			$oObject->setReceived($inReceived);
		}
		if ( $inStatus !== null ) {
			$oObject->setStatus($inStatus);
		}
		if ( $inMovieID !== null ) {
			$oObject->setMovieID($inMovieID);
		}
		if ( $inNotes !== null ) {
			$oObject->setNotes($inNotes);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmOriginXML by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmOriginXML
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
		$oObject = new mofilmOriginXML();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmOriginXML
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.originXML';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmOriginXML();
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
	 * Returns an array of objects of mofilmOriginXML
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function getXmlFromQueue() {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.originXML
		WHERE status =:Status';
		
		$query .= ' LIMIT 1';
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':Status', self::STATUS_QUEUED);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmOriginXML();
					$oObject->loadFromArray($row);
					$list[] = $oObject;
				}
			}
			$oStmt->closeCursor();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return $oObject;
	}
	
	
	
	/**
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.originXML';
		
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
		$this->setXml($inArray['xml']);
		$this->setReceived($inArray['received']);
		$this->setStatus($inArray['status']);
		$this->setMovieID((int)$inArray['movieID']);
		$this->setNotes($inArray['notes']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.originXML
					( ID, xml, received, status, movieID, notes)
				VALUES 
					(:ID, :Xml, :Received, :Status, :MovieID, :Notes)
				ON DUPLICATE KEY UPDATE
					xml=VALUES(xml),
					received=VALUES(received),
					status=VALUES(status),
					movieID=VALUES(movieID),
					notes=VALUES(notes)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Xml', $this->_Xml);
					$oStmt->bindValue(':Received', $this->_Received);
					$oStmt->bindValue(':Status', $this->_Status);
					$oStmt->bindValue(':MovieID', $this->_MovieID);
					$oStmt->bindValue(':Notes', $this->_Notes);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.originXML
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
	 * @return mofilmOriginXML
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Xml = '';
		$this->_Received = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Status = 'Queued';
		$this->_MovieID = null;
		$this->_Notes = null;
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
		$string .= " Xml[$this->_Xml] $newLine";
		$string .= " Received[$this->_Received] $newLine";
		$string .= " Status[$this->_Status] $newLine";
		$string .= " MovieID[$this->_MovieID] $newLine";
		$string .= " Notes[$this->_Notes] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmOriginXML';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Xml\" value=\"$this->_Xml\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Received\" value=\"$this->_Received\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Status\" value=\"$this->_Status\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"MovieID\" value=\"$this->_MovieID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Notes\" value=\"$this->_Notes\" type=\"string\" /> $newLine";
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
			$valid = $this->checkXml($message);
		}
		if ( $valid ) {
			$valid = $this->checkReceived($message);
		}
		if ( $valid ) {
			$valid = $this->checkStatus($message);
		}
		if ( $valid ) {
			$valid = $this->checkMovieID($message);
		}
		if ( $valid ) {
			$valid = $this->checkNotes($message);
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
	 * Checks that $_Xml has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkXml(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Xml) && $this->_Xml !== '' ) {
			$inMessage .= "{$this->_Xml} is not a valid value for Xml";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Received has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkReceived(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Received) && $this->_Received !== '' ) {
			$inMessage .= "{$this->_Received} is not a valid value for Received";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Status has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkStatus(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Status) && $this->_Status !== '' ) {
			$inMessage .= "{$this->_Status} is not a valid value for Status";
			$isValid = false;
		}		
		if ( $isValid && $this->_Status != '' && !in_array($this->_Status, array(self::STATUS_QUEUED, self::STATUS_PROCESSING, self::STATUS_FAILED, self::STATUS_SUCCESS)) ) {
			$inMessage .= "Status must be one of STATUS_QUEUED, STATUS_PROCESSING, STATUS_FAILED, STATUS_SUCCESS";
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
		if ( !is_numeric($this->_MovieID) && $this->_MovieID !== null && $this->_MovieID !== 0 ) {
			$inMessage .= "{$this->_MovieID} is not a valid value for MovieID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Notes has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkNotes(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Notes) && $this->_Notes !== null && $this->_Notes !== '' ) {
			$inMessage .= "{$this->_Notes} is not a valid value for Notes";
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
	 * @return mofilmOriginXML
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
	 * @return mofilmOriginXML
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
	 * Return value of $_Xml
	 * 
	 * @return string
	 * @access public
	 */
	function getXml() {
		return $this->_Xml;
	}
	
	/**
	 * Set $_Xml to Xml
	 * 
	 * @param string $inXml
	 * @return mofilmOriginXML
	 * @access public
	 */
	function setXml($inXml) {
		if ( $inXml !== $this->_Xml ) {
			$this->_Xml = $inXml;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Received
	 * 
	 * @return datetime
	 * @access public
	 */
	function getReceived() {
		return $this->_Received;
	}
	
	/**
	 * Set $_Received to Received
	 * 
	 * @param datetime $inReceived
	 * @return mofilmOriginXML
	 * @access public
	 */
	function setReceived($inReceived) {
		if ( $inReceived !== $this->_Received ) {
			$this->_Received = $inReceived;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Status
	 * 
	 * @return string
	 * @access public
	 */
	function getStatus() {
		return $this->_Status;
	}
	
	/**
	 * Set $_Status to Status
	 * 
	 * @param string $inStatus
	 * @return mofilmOriginXML
	 * @access public
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
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
	 * @return mofilmOriginXML
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
	 * Return value of $_Notes
	 * 
	 * @return string
	 * @access public
	 */
	function getNotes() {
		return $this->_Notes;
	}
	
	/**
	 * Set $_Notes to Notes
	 * 
	 * @param string $inNotes
	 * @return mofilmOriginXML
	 * @access public
	 */
	function setNotes($inNotes) {
		if ( $inNotes !== $this->_Notes ) {
			$this->_Notes = $inNotes;
			$this->setModified();
		}
		return $this;
	}
}
