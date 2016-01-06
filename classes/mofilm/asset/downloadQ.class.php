<?php
/**
 * mofilmAssetDownloadQ
 * 
 * Stored in mofilmAssetDownloadQ.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmAssetDownloadQ
 * @category mofilmAssetDownloadQ
 * @version $Rev: 10 $
 */


/**
 * mofilmAssetDownloadQ Class
 * 
 * Provides access to records in mofilm_content.assetDownloadQ
 * 
 * Creating a new record:
 * <code>
 * $oMofilmAssetDownloadQ = new mofilmAssetDownloadQ();
 * $oMofilmAssetDownloadQ->setAssetID($inAssetID);
 * $oMofilmAssetDownloadQ->setScheduled($inScheduled);
 * $oMofilmAssetDownloadQ->setTries($inTries);
 * $oMofilmAssetDownloadQ->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmAssetDownloadQ = new mofilmAssetDownloadQ($inAssetID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmAssetDownloadQ = new mofilmAssetDownloadQ();
 * $oMofilmAssetDownloadQ->setAssetID($inAssetID);
 * $oMofilmAssetDownloadQ->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmAssetDownloadQ = mofilmAssetDownloadQ::getInstance($inAssetID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmAssetDownloadQ
 * @category mofilmAssetDownloadQ
 */
class mofilmAssetDownloadQ implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmAssetDownloadQ
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
	 * Stores $_AssetID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_AssetID;
			
	/**
	 * Stores $_Scheduled
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_Scheduled;
			
	/**
	 * Stores $_Tries
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_Tries;
			
	
	
	/**
	 * Returns a new instance of mofilmAssetDownloadQ
	 * 
	 * @param integer $inAssetID
	 * @return mofilmAssetDownloadQ
	 */
	function __construct($inAssetID = null) {
		$this->reset();
		if ( $inAssetID !== null ) {
			$this->setAssetID($inAssetID);
			$this->load();
		}
		return $this;
	}
	
	/**
	 * Creates a new mofilmAssetDownloadQ containing non-unique properties
	 * 
	 * @param datetime $inScheduled
	 * @param integer $inTries
	 * @return mofilmAssetDownloadQ
	 * @static 
	 */
	public static function factory($inScheduled = null, $inTries = null) {
		$oObject = new mofilmAssetDownloadQ;
		if ( $inScheduled !== null ) {
			$oObject->setScheduled($inScheduled);
		}
		if ( $inTries !== null ) {
			$oObject->setTries($inTries);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmAssetDownloadQ by primary key
	 * 
	 * @param integer $inAssetID
	 * @return mofilmAssetDownloadQ
	 * @static 
	 */
	public static function getInstance($inAssetID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inAssetID]) ) {
			return self::$_Instances[$inAssetID];
		}
		
		/**
		 * No instance, create one
		 */
		$oObject = new mofilmAssetDownloadQ();
		$oObject->setAssetID($inAssetID);
		if ( $oObject->load() ) {
			self::$_Instances[$inAssetID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmAssetDownloadQ
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.assetDownloadQ';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmAssetDownloadQ();
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
	 * Returns an array of objects of mofilmAssetDownloadQ
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function getAssetToDownload() {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.assetDownloadQ
		WHERE scheduled < :scheduled ';	
		
		$query .= ' LIMIT 1';

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':scheduled', date('Y-m-d H:i:s'));
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmAssetDownloadQ();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.assetDownloadQ';
		
		$where = array();
		if ( $this->_AssetID !== 0 ) {
			$where[] = ' assetID = :AssetID ';
		}
						
		if ( count($where) == 0 ) {
			return false;
		}
		
		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_AssetID !== 0 ) {
				$oStmt->bindValue(':AssetID', $this->_AssetID);
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
		$this->setAssetID((int)$inArray['assetID']);
		$this->setScheduled($inArray['scheduled']);
		$this->setTries((int)$inArray['tries']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.assetDownloadQ
					( assetID, scheduled, tries)
				VALUES 
					(:AssetID, :Scheduled, :Tries)
				ON DUPLICATE KEY UPDATE
					scheduled=VALUES(scheduled),
					tries=VALUES(tries)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':AssetID', $this->_AssetID);
					$oStmt->bindValue(':Scheduled', $this->_Scheduled);
					$oStmt->bindValue(':Tries', $this->_Tries);
								
					if ( $oStmt->execute() ) {
						if ( !$this->getAssetID() ) {
							$this->setAssetID($oDB->lastInsertId());
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.assetDownloadQ
		WHERE
			assetID = :AssetID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':AssetID', $this->_AssetID);
				
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
	 * @return mofilmAssetDownloadQ
	 */
	function reset() {
		$this->_AssetID = 0;
		$this->_Scheduled = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Tries = 0;
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
		$string .= " AssetID[$this->_AssetID] $newLine";
		$string .= " Scheduled[$this->_Scheduled] $newLine";
		$string .= " Tries[$this->_Tries] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmAssetDownloadQ';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"AssetID\" value=\"$this->_AssetID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Scheduled\" value=\"$this->_Scheduled\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Tries\" value=\"$this->_Tries\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkAssetID($message);
		}
		if ( $valid ) {
			$valid = $this->checkScheduled($message);
		}
		if ( $valid ) {
			$valid = $this->checkTries($message);
		}
		return $valid;
	}
		
	/**
	 * Checks that $_AssetID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkAssetID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_AssetID) && $this->_AssetID !== 0 ) {
			$inMessage .= "{$this->_AssetID} is not a valid value for AssetID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Scheduled has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkScheduled(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Scheduled) && $this->_Scheduled !== '' ) {
			$inMessage .= "{$this->_Scheduled} is not a valid value for Scheduled";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Tries has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkTries(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Tries) && $this->_Tries !== 0 ) {
			$inMessage .= "{$this->_Tries} is not a valid value for Tries";
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
	 * @return mofilmAssetDownloadQ
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
		return $this->_AssetID;
	}
		
	/**
	 * Return value of $_AssetID
	 * 
	 * @return integer
	 * @access public
	 */
	function getAssetID() {
		return $this->_AssetID;
	}
	
	/**
	 * Set $_AssetID to AssetID
	 * 
	 * @param integer $inAssetID
	 * @return mofilmAssetDownloadQ
	 * @access public
	 */
	function setAssetID($inAssetID) {
		if ( $inAssetID !== $this->_AssetID ) {
			$this->_AssetID = $inAssetID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Scheduled
	 * 
	 * @return datetime
	 * @access public
	 */
	function getScheduled() {
		return $this->_Scheduled;
	}
	
	/**
	 * Set $_Scheduled to Scheduled
	 * 
	 * @param datetime $inScheduled
	 * @return mofilmAssetDownloadQ
	 * @access public
	 */
	function setScheduled($inScheduled) {
		if ( $inScheduled !== $this->_Scheduled ) {
			$this->_Scheduled = $inScheduled;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Tries
	 * 
	 * @return integer
	 * @access public
	 */
	function getTries() {
		return $this->_Tries;
	}
	
	/**
	 * Set $_Tries to Tries
	 * 
	 * @param integer $inTries
	 * @return mofilmAssetDownloadQ
	 * @access public
	 */
	function setTries($inTries) {
		if ( $inTries !== $this->_Tries ) {
			$this->_Tries = $inTries;
			$this->setModified();
		}
		return $this;
	}
}
