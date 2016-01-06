<?php
/**
 * commsNetwork
 *
 * Stored in commsNetwork.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsNetwork
 * @category commsNetwork
 * @version $Rev: 10 $
 */


/**
 * commsNetwork Class
 *
 * Provides access to records in comms.networks
 *
 * Creating a new record:
 * <code>
 * $oCommsNetwork = new commsNetwork();
 * $oCommsNetwork->setNetworkID($inNetworkID);
 * $oCommsNetwork->setDescription($inDescription);
 * $oCommsNetwork->setUri($inUri);
 * $oCommsNetwork->setCreateDate($inCreateDate);
 * $oCommsNetwork->setUpdateDate($inUpdateDate);
 * $oCommsNetwork->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsNetwork = new commsNetwork($inNetworkID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsNetwork = new commsNetwork();
 * $oCommsNetwork->setNetworkID($inNetworkID);
 * $oCommsNetwork->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsNetwork = commsNetwork::getInstance($inNetworkID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage commsNetwork
 * @category commsNetwork
 */
class commsNetwork implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsNetwork
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
	 * Stores $_NetworkID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_NetworkID;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_Uri
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Uri;

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
	 * Stores an instance of baseTableParamSet
	 *
	 * @var baseTableParamSet
	 * @access protected
	 */
	protected $_ParamSet;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of commsNetwork
	 *
	 * @param integer $inNetworkID
	 * @return commsNetwork
	 */
	function __construct($inNetworkID = null) {
		$this->reset();
		if ( $inNetworkID !== null ) {
			$this->setNetworkID($inNetworkID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsNetwork containing non-unique properties
	 *
	 * @param string $inDescription
	 * @param string $inUri
	 * @param datetime $inCreateDate
	 * @param datetime $inUpdateDate
	 * @return commsNetwork
	 * @static
	 */
	public static function factory($inDescription = null, $inUri = null, $inCreateDate = null, $inUpdateDate = null) {
		$oObject = new commsNetwork;
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inUri !== null ) {
			$oObject->setUri($inUri);
		}
		if ( $inCreateDate !== null ) {
			$oObject->setCreateDate($inCreateDate);
		}
		if ( $inUpdateDate !== null ) {
			$oObject->setUpdateDate($inUpdateDate);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsNetwork by primary key
	 *
	 * @param integer $inNetworkID
	 * @return commsNetwork
	 * @static
	 */
	public static function getInstance($inNetworkID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inNetworkID]) ) {
			return self::$_Instances[$inNetworkID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsNetwork();
		$oObject->setNetworkID($inNetworkID);
		if ( $oObject->load() ) {
			self::$_Instances[$inNetworkID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsNetwork
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.networks ORDER BY description ASC';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsNetwork();
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
			SELECT networkID, description, uri, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('comms').'.networks';

		$where = array();
		if ( $this->_NetworkID !== 0 ) {
			$where[] = ' networkID = :NetworkID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_NetworkID !== 0 ) {
				$oStmt->bindValue(':NetworkID', $this->_NetworkID);
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
		$this->setNetworkID((int)$inArray['networkID']);
		$this->setDescription($inArray['description']);
		$this->setUri($inArray['uri']);
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
				throw new commsException($message);
			}
			$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.networks
					( networkID, description, uri, createDate, updateDate)
				VALUES
					(:NetworkID, :Description, :Uri, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					description=VALUES(description),
					uri=VALUES(uri),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':NetworkID', $this->_NetworkID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':Uri', $this->_Uri);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':UpdateDate', $this->_UpdateDate);

					if ( $oStmt->execute() ) {
						if ( !$this->getNetworkID() ) {
							$this->setNetworkID($oDB->lastInsertId());
						}
						$this->setModified(false);
						$return = true;
					}
				} catch ( Exception $e ) {
					systemLog::error($e->getMessage());
					throw $e;
				}
			}
			
			if ( $this->_ParamSet instanceof baseTableParamSet ) {
				$this->_ParamSet->setIndexID($this->getNetworkID());
				$this->_ParamSet->save();
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.networks
			WHERE
				networkID = :NetworkID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':NetworkID', $this->_NetworkID);

			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				
				$this->getParamSet()->deleteAll();
				
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
	 * @return commsNetwork
	 */
	function reset() {
		$this->_NetworkID = 0;
		$this->_Description = '';
		$this->_Uri = '';
		$this->_CreateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_UpdateDate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_ParamSet = null;
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
		$string .= " NetworkID[$this->_NetworkID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " Uri[$this->_Uri] $newLine";
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
		$className = 'commsNetwork';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"NetworkID\" value=\"$this->_NetworkID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Uri\" value=\"$this->_Uri\" type=\"string\" /> $newLine";
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
			$valid = $this->checkNetworkID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkUri($message);
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
	 * Checks that $_NetworkID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkNetworkID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_NetworkID) && $this->_NetworkID !== 0 ) {
			$inMessage .= "{$this->_NetworkID} is not a valid value for NetworkID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Description has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkDescription(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Description) && $this->_Description !== '' ) {
			$inMessage .= "{$this->_Description} is not a valid value for Description";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) > 50 ) {
			$inMessage .= "Description cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Uri has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkUri(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Uri) && $this->_Uri !== '' ) {
			$inMessage .= "{$this->_Uri} is not a valid value for Uri";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Uri) > 255 ) {
			$inMessage .= "Uri cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Uri) <= 1 ) {
			$inMessage .= "Uri must be more than 1 character";
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
		$modified = $this->_Modified;
		if ( !$modified && $this->_ParamSet !== null ) {
			$modified = $modified || $this->_ParamSet->isModified();
		}
		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return commsNetwork
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
		return $this->_NetworkID;
	}

	/**
	 * Return value of $_NetworkID
	 *
	 * @return integer
	 * @access public
	 */
	function getNetworkID() {
		return $this->_NetworkID;
	}

	/**
	 * Set $_NetworkID to NetworkID
	 *
	 * @param integer $inNetworkID
	 * @return commsNetwork
	 * @access public
	 */
	function setNetworkID($inNetworkID) {
		if ( $inNetworkID !== $this->_NetworkID ) {
			$this->_NetworkID = $inNetworkID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Description
	 *
	 * @return string
	 * @access public
	 */
	function getDescription() {
		return $this->_Description;
	}

	/**
	 * Set $_Description to Description
	 *
	 * @param string $inDescription
	 * @return commsNetwork
	 * @access public
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Uri
	 *
	 * @return string
	 * @access public
	 */
	function getUri() {
		return $this->_Uri;
	}

	/**
	 * Set $_Uri to Uri
	 *
	 * @param string $inUri
	 * @return commsNetwork
	 * @access public
	 */
	function setUri($inUri) {
		if ( $inUri !== $this->_Uri ) {
			$this->_Uri = $inUri;
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
	 * @return commsNetwork
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
	 * @return commsNetwork
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
	 * Returns an instance of baseTableParamSet, which is lazy loaded upon request
	 *
	 * @return baseTableParamSet
	 */
	function getParamSet() {
		if ( !$this->_ParamSet instanceof baseTableParamSet ) {
			$this->_ParamSet = new baseTableParamSet(
				system::getConfig()->getDatabase('comms'), 'networkParams', 'networkID', 'paramName', 'paramValue', $this->getNetworkID(), false
			);
			if ( $this->getNetworkID() > 0 ) {
				$this->_ParamSet->load();
			}
		}
		return $this->_ParamSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param baseTableParamSet $inObject
	 * @return commsNetwork
	 */
	function setParamSet(baseTableParamSet $inObject) {
		$this->_ParamSet = $inObject;
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
	 * @return commsNetwork
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}