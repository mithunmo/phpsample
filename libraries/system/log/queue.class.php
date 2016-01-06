<?php
/**
 * systemLogQueue
 * 
 * Stored in systemLogQueue.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLogQueue
 * @version $Rev: 650 $
 */


/**
 * systemLogQueue Class
 * 
 * Provides access to records in logging.logQueue
 * 
 * Creating a new record:
 * <code>
 * $oSystemLogQueue = new systemLogQueue();
 * $oSystemLogQueue->setLogFile($inLogFile);
 * $oSystemLogQueue->setLogMessage($inLogMessage);
 * $oSystemLogQueue->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oSystemLogQueue = new systemLogQueue();
 * </code>
 * 
 * 
 * Accessing a record by instance:
 * <code>
 * $oSystemLogQueue = systemLogQueue::getInstance();
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLogQueue
 */
class systemLogQueue implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of systemLogQueue
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
	 * Stores $_LogFile
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_LogFile;
			
	/**
	 * Stores $_LogMessage
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_LogMessage;
			
	
	
	/**
	 * Returns a new instance of systemLogQueue
	 * 
	 * @return systemLogQueue
	 */
	function __construct() {
		$this->reset();
		
		return $this;
	}
	
	/**
	 * Creates a new systemLogQueue containing non-unique properties
	 * 
	 * @param string $inLogFile
	 * @param string $inLogMessage
	 * @return systemLogQueue
	 * @static 
	 */
	public static function factory($inLogFile = null, $inLogMessage = null) {
		$oObject = new systemLogQueue;
		if ( $inLogFile !== null ) {
			$oObject->setLogFile($inLogFile);
		}
		if ( $inLogMessage !== null ) {
			$oObject->setLogMessage($inLogMessage);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of systemLogQueue by primary key
	 * 
	 * @return systemLogQueue
	 * @static 
	 */
	public static function getInstance() {
		/**
		 * Check for an existing instance
		 */
		$oObject = new systemLogQueue();
		return $oObject;
	}
		
	/**
	 * Returns an array of objects of systemLogQueue
	 * 
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inLimit = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('logging').'.logQueue';
		
		if ( $inLimit !== null ) {
			$query .= ' LIMIT '.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new systemLogQueue();
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
	 * Returns the number of items in the current log queue
	 *
	 * @return integer
	 * @static 
	 */
	public static function getQueueCount() {
		$query = 'SELECT COUNT(*) AS Count FROM '.system::getConfig()->getDatabase('logging').'.logQueue';
		
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				$row = $oStmt->fetch();
				if ( $row !== false && is_array($row) ) {
					$oStmt->closeCursor();
					return $row['Count'];
				}
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			throw $e;
		}
		return 0;
	}
	
	
	
	/**
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('logging').'.logQueue';
		
		$where = array();
		if ( $this->_LogFile !== '' ) {
			$where[] = ' logFile = :LogFile ';
		}
		if ( $this->_LogMessage !== '' ) {
			$where[] = ' logMessage = :LogMessage ';
		}
						
		if ( count($where) > 0 ) {
			$query .= ' WHERE '.implode(' AND ', $where);
		}

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_LogFile !== '' ) {
				$oStmt->bindValue(':LogFile', $this->_LogFile);
			}
			if ( $this->_LogMessage !== '' ) {
				$oStmt->bindValue(':LogMessage', $this->_LogMessage);
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
		$this->setLogFile($inArray['logFile']);
		$this->setLogMessage($inArray['logMessage']);
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
				throw new systemLogException($message);
			}
						
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('logging').'.logQueue
					( logFile, logMessage)
				VALUES 
					(:LogFile, :LogMessage)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':LogFile', $this->_LogFile);
					$oStmt->bindValue(':LogMessage', $this->_LogMessage);
								
					if ( $oStmt->execute() ) {

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
		DELETE FROM '.system::getConfig()->getDatabase('logging').'.logQueue
		WHERE logFile = :LogFile
		  AND logMessage = :LogMessage
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':LogFile', $this->_LogFile);
			$oStmt->bindValue(':LogMessage', $this->_LogMessage);
			
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
	 * @return systemLogQueue
	 */
	function reset() {
		$this->_LogFile = '';
		$this->_LogMessage = '';
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
		$string .= " LogFile[$this->_LogFile] $newLine";
		$string .= " LogMessage[$this->_LogMessage] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'systemLogQueue';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"LogFile\" value=\"$this->_LogFile\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"LogMessage\" value=\"$this->_LogMessage\" type=\"string\" /> $newLine";
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
			$valid = $this->checkLogFile($message);
		}
		if ( $valid ) {
			$valid = $this->checkLogMessage($message);
		}
		return $valid;
	}
		
	/**
	 * Checks that $_LogFile has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkLogFile(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_LogFile) && $this->_LogFile !== '' ) {
			$inMessage .= "{$this->_LogFile} is not a valid value for LogFile";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_LogFile) > 255 ) {
			$inMessage .= "LogFile cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_LogFile) <= 1 ) {
			$inMessage .= "LogFile must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_LogMessage has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkLogMessage(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_LogMessage) && $this->_LogMessage !== '' ) {
			$inMessage .= "{$this->_LogMessage} is not a valid value for LogMessage";
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
	 * @return systemLogQueue
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
		return ;
	}
		
	/**
	 * Return value of $_LogFile
	 * 
	 * @return string
	 * @access public
	 */
	function getLogFile() {
		return $this->_LogFile;
	}
	
	/**
	 * Set $_LogFile to LogFile
	 * 
	 * @param string $inLogFile
	 * @return systemLogQueue
	 * @access public
	 */
	function setLogFile($inLogFile) {
		if ( $inLogFile !== $this->_LogFile ) {
			$this->_LogFile = $inLogFile;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_LogMessage
	 * 
	 * @return string
	 * @access public
	 */
	function getLogMessage() {
		return $this->_LogMessage;
	}
	
	/**
	 * Set $_LogMessage to LogMessage
	 * 
	 * @param string $inLogMessage
	 * @return systemLogQueue
	 * @access public
	 */
	function setLogMessage($inLogMessage) {
		if ( $inLogMessage !== $this->_LogMessage ) {
			$this->_LogMessage = $inLogMessage;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Puts the log entry to the file system, removing the entry from the DB
	 *
	 * @return boolean
	 * @access public
	 */
	function storeLogFile() {
		$bytes = @file_put_contents($this->_LogFile, $this->_LogMessage, FILE_APPEND|LOCK_EX);
		if ( $bytes > 0 ) {
			return $this->delete();
		}
		return false;
	}
}