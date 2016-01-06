<?php
/**
 * dbUpdateDefinition
 * 
 * Stored in dbUpdateDefinition.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbUpdateDefinition
 * @version $Rev: 650 $
 */


/**
 * dbUpdateDefinition Class
 * 
 * A definition class contains updates for a specific database. Each update must be unique,
 * the same update will not be applied multiple times. For each database to be updated a
 * file needs to be created in /data/dbUpdates using the name of the database to be updated
 * as you would access it via the system::getConfig()->getDatabase() method.
 * 
 * This file should contain ONE and ONLY one class named dbUpdateMyDatabase.
 * 
 * @todo: DR re-write to be DB agnostic or use XML format to store data.
 * 
 * Provides access to records in scorpio_system.dbUpdates
 * 
 * Creating a new record:
 * <code>
 * $oDbUpdateDefinition = new dbUpdateDefinition();
 * $oDbUpdateDefinition->setDbName($inDbName);
 * $oDbUpdateDefinition->setVersion($inVersion);
 * $oDbUpdateDefinition->setLastUpdateID($inLastUpdateID);
 * $oDbUpdateDefinition->setCreateDate($inCreateDate);
 * $oDbUpdateDefinition->setUpdateDate($inUpdateDate);
 * $oDbUpdateDefinition->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oDbUpdateDefinition = new dbUpdateDefinition($inDbName);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oDbUpdateDefinition = new dbUpdateDefinition();
 * $oDbUpdateDefinition->setDbName($inDbName);
 * $oDbUpdateDefinition->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oDbUpdateDefinition = dbUpdateDefinition::getInstance($inDbName);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbUpdateDefinition
 */
abstract class dbUpdateDefinition extends baseSet implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of dbUpdateDefinition
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
	 * Stores $_DbName
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_DbName;
			
	/**
	 * Stores $_Version
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_Version;
			
	/**
	 * Stores $_LastUpdateID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_LastUpdateID;
			
	/**
	 * Stores $_CreateDate
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_CreateDate;
			
	/**
	 * Stores $_UpdateDate
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_UpdateDate;
			
	
	
	/**
	 * Returns a new instance of dbUpdateDefinition
	 * 
	 * @param string $inDbName
	 * @return dbUpdateDefinition
	 */
	function __construct($inDbName = null) {
		$this->reset();
		if ( $inDbName !== null ) {
			$this->setDbName($inDbName);
			$this->load();
		}
		$this->setDbName($inDbName);
		$this->initialiseUpdates();
	}
	
	
	
	/**
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('system').'.dbUpdates';
		
		$where = array();
		if ( $this->_DbName !== '' ) {
			$where[] = ' dbName = :DbName ';
		}
						
		if ( count($where) > 0 ) {
			$query .= ' WHERE '.implode(' AND ', $where);
		}

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_DbName !== '' ) {
				$oStmt->bindValue(':DbName', $this->_DbName);
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
		$this->setDbName($inArray['dbName']);
		$this->setVersion((int)$inArray['version']);
		$this->setLastUpdateID((int)$inArray['lastUpdateID']);
		$this->setCreateDate($inArray['createDate']);
		$this->setUpdateDate($inArray['updateDate']);
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
				throw new systemException($message);
			}
			$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));			
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('system').'.dbUpdates
					( dbName, version, lastUpdateID, createDate, updateDate)
				VALUES 
					(:DbName, :Version, :LastUpdateID, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					version=VALUES(version),
					lastUpdateID=VALUES(lastUpdateID),
					updateDate=VALUES(updateDate)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':DbName', $this->_DbName);
					$oStmt->bindValue(':Version', $this->_Version);
					$oStmt->bindValue(':LastUpdateID', $this->_LastUpdateID);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':UpdateDate', $this->_UpdateDate);
								
					if ( $oStmt->execute() ) {
						$this->setModified(false);
						$return = true;
					}
					$oStmt->closeCursor();
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
		DELETE FROM '.system::getConfig()->getDatabase('system').'.dbUpdates
		WHERE
			dbName = :DbName	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':DbName', $this->_DbName);
				
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
	 * @return dbUpdateDefinition
	 */
	function reset() {
		$this->_DbName = '';
		$this->_Version = 0;
		$this->_LastUpdateID = 0;
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_UpdateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->setModified(false);
		return $this;
	}
	
	/**
	 * Applies the set of updates to the current database, creates a log of any update applied
	 *
	 * @param boolean $inCommit
	 * @param boolean $inHaltOnError
	 * @param dbUpdateReport $inReport
	 * @return boolean
	 */
	function update($inCommit = false, $inHaltOnError = true, dbUpdateReport $inReport) {
		if ( !$this->isUpToDate() ) {
			$updateCnt = $this->getCount();
			for ( $i=$this->getVersion(); $i<$updateCnt; $i++ ) {
				$update = $this->getUpdate($i);
				$function = false;
				
				$oLog = new dbUpdateLog();
				$oLog->setUpdateType(dbUpdateLog::UPDATETYPE_SQL);
				$oLog->setUpdateCommand($update);
				if ( !$inCommit ) {
					$oLog->setUpdateResult(dbUpdateLog::UPDATERESULT_TEST);
				}
				
				try {
					$oReflect = new ReflectionClass($this);
					if ( $oReflect->hasMethod($update) ) {
						$oLog->setUpdateType(dbUpdateLog::UPDATETYPE_FUNCTION);
						$function = true;
					}
				} catch ( Exception $e ) {
				}
				
				try {
					if ( $function ) {
						/*
						 * Execute our method call
						 */
						$oLog->addMessage("Execute: \$this->$update()");
						if ( $inCommit ) {
							$this->$update();
							$oLog->addMessage("Function executed without error");
							$oLog->setUpdateResult(dbUpdateLog::UPDATERESULT_SUCCESS);
						} else {
							$oLog->addMessage("Function would have been executed");
							$oLog->setUpdateResult(dbUpdateLog::UPDATERESULT_TEST);
						}
					} else {
						/*
						 * Run the SQL query
						 */
						if ( $inCommit ) {
							$oStmt = dbManager::getInstance()->query($update);
							if ( $oStmt instanceof PDOStatement ) {
								$res = $oStmt->rowCount();
							} else {
								$res = $oStmt;
							}
							$oLog->addMessage("Query executed successfully with $res affected rows");
							$oLog->setUpdateResult(dbUpdateLog::UPDATERESULT_SUCCESS);
						} else {
							$res = dbManager::getInstance()->prepare($update);
							if ( $res ) {
								$oLog->addMessage("Query OK");
								$oLog->setUpdateResult(dbUpdateLog::UPDATERESULT_TEST);
							} else {
								$oLog->setUpdateResult(dbUpdateLog::UPDATERESULT_FAILURE);
								$oLog->addMessage("Query could not be prepared, check syntax");
							}
							$res = null;
						}
					}
				} catch ( Exception $e ) {
					$oLog->setUpdateResult(dbUpdateLog::UPDATERESULT_FAILURE);
					$oLog->addMessage("ERROR!\n".$e->getMessage()."\n\nException Trace:\n".$e->getTraceAsString());
				}
				
				if ( $inCommit ) {
					$oLog->save();
					
					if ( !$oLog->isError() ) {
						$this->setVersion($this->getVersion()+1);
					}
					
					$this->setLastUpdateID($oLog->getDbUpdateID());
					$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue()));
					$this->save();
				}
				$inReport->addLog($oLog);
				
				if ( $inHaltOnError && $oLog->isError() ) {
					return false;
				}
			}
		}
		return true;
	}
	
	
	
	/**
	 * Returns object as a string with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toString($newLine = "\n") {
		$string  = '';
		$string .= " DbName[$this->_DbName] $newLine";
		$string .= " Version[$this->_Version] $newLine";
		$string .= " LastUpdateID[$this->_LastUpdateID] $newLine";
		$string .= " CreateDate[$this->_CreateDate] $newLine";
		$string .= " UpdateDate[$this->_UpdateDate] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'dbUpdateDefinition';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"DbName\" value=\"$this->_DbName\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Version\" value=\"$this->_Version\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"LastUpdateID\" value=\"$this->_LastUpdateID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CreateDate\" value=\"$this->_CreateDate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"UpdateDate\" value=\"$this->_UpdateDate\" type=\"datetime\" /> $newLine";
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
			$valid = $this->checkDbName($message);
		}
		if ( $valid ) {
			$valid = $this->checkVersion($message);
		}
		if ( $valid ) {
			$valid = $this->checkLastUpdateID($message);
		}
		if ( $valid ) {
			$valid = $this->checkCreateDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkUpdateDate($message);
		}
		return $valid;
	}
		
	/**
	 * Checks that $_DbName has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkDbName(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_DbName) && $this->_DbName !== '' ) {
			$inMessage .= "{$this->_DbName} is not a valid value for DbName";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_DbName) > 255 ) {
			$inMessage .= "DbName cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_DbName) <= 1 ) {
			$inMessage .= "DbName must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Version has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkVersion(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Version) && $this->_Version !== 0 ) {
			$inMessage .= "{$this->_Version} is not a valid value for Version";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_LastUpdateID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkLastUpdateID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_LastUpdateID) && $this->_LastUpdateID !== 0 ) {
			$inMessage .= "{$this->_LastUpdateID} is not a valid value for LastUpdateID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_CreateDate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkCreateDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_CreateDate) && $this->_CreateDate !== '' ) {
			$inMessage .= "{$this->_CreateDate} is not a valid value for CreateDate";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_UpdateDate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkUpdateDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_UpdateDate) && $this->_UpdateDate !== '' ) {
			$inMessage .= "{$this->_UpdateDate} is not a valid value for UpdateDate";
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
	 * @return dbUpdateDefinition
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
		return $this->_DbName;
	}
		
	/**
	 * Return value of $_DbName
	 * 
	 * @return string
	 * @access public
	 */
	function getDbName() {
		return $this->_DbName;
	}
	
	/**
	 * Set $_DbName to DbName
	 * 
	 * @param string $inDbName
	 * @return dbUpdateDefinition
	 * @access public
	 */
	function setDbName($inDbName) {
		if ( $inDbName !== $this->_DbName ) {
			$this->_DbName = $inDbName;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Version
	 * 
	 * @return integer
	 * @access public
	 */
	function getVersion() {
		return $this->_Version;
	}
	
	/**
	 * Set $_Version to Version
	 * 
	 * @param integer $inVersion
	 * @return dbUpdateDefinition
	 * @access public
	 */
	function setVersion($inVersion) {
		if ( $inVersion !== $this->_Version ) {
			$this->_Version = $inVersion;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_LastUpdateID
	 * 
	 * @return integer
	 * @access public
	 */
	function getLastUpdateID() {
		return $this->_LastUpdateID;
	}
	
	/**
	 * Returns the last log entry for this database update
	 *
	 * @return dbUpdateLog
	 */
	function getLastUpdateLogEntry() {
		return dbUpdateLog::getInstance($this->getLastUpdateID());
	}
	
	/**
	 * Set $_LastUpdateID to LastUpdateID
	 * 
	 * @param integer $inLastUpdateID
	 * @return dbUpdateDefinition
	 * @access public
	 */
	function setLastUpdateID($inLastUpdateID) {
		if ( $inLastUpdateID !== $this->_LastUpdateID ) {
			$this->_LastUpdateID = $inLastUpdateID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_CreateDate
	 * 
	 * @return datetime
	 * @access public
	 */
	function getCreateDate() {
		return $this->_CreateDate;
	}
	
	/**
	 * Set $_CreateDate to CreateDate
	 * 
	 * @param datetime $inCreateDate
	 * @return dbUpdateDefinition
	 * @access public
	 */
	function setCreateDate($inCreateDate) {
		if ( $inCreateDate !== $this->_CreateDate ) {
			$this->_CreateDate = $inCreateDate;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_UpdateDate
	 * 
	 * @return datetime
	 * @access public
	 */
	function getUpdateDate() {
		return $this->_UpdateDate;
	}
	
	/**
	 * Set $_UpdateDate to UpdateDate
	 * 
	 * @param datetime $inUpdateDate
	 * @return dbUpdateDefinition
	 * @access public
	 */
	function setUpdateDate($inUpdateDate) {
		if ( $inUpdateDate !== $this->_UpdateDate ) {
			$this->_UpdateDate = $inUpdateDate;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Sets up the updates to be applied to this database
	 *
	 * @return void
	 */
	abstract function initialiseUpdates();
	
	/**
	 * Returns true if there are no updates to apply
	 *
	 * @return boolean
	 */
	function isUpToDate() {
		return $this->getVersion()==$this->getCount();
	}
	
	/**
	 * Returns the number of updates that need to be applied
	 *
	 * @return integer
	 */
	function getUpdateCount() {
		return max(array(0, $this->getCount()-$this->getVersion()));
	}
	
	/**
	 * Adds a new update to the set
	 *
	 * @param string $inUpdate
	 * @return dbUpdateDefinition
	 */
	function addUpdate($inUpdate) {
		return $this->_setValue($inUpdate);
	}
	
	/**
	 * Gets the next update by key
	 *
	 * @param integer $inKey
	 * @return string
	 */
	function getUpdate($inKey) {
		return $this->_getItem($inKey);
	}
	
	/**
	 * Removes an update from the set
	 *
	 * @param string $inUpdate
	 * @return dbUpdateDefinition
	 */
	function removeUpdate($inUpdate) {
		return $this->_removeItemWithValue($inUpdate);
	}
	
	/**
	 * Returns the number of updates in the set
	 *
	 * @return integer
	 */
	function getCount() {
		return $this->_itemCount();
	}
}