<?php
/**
 * commsGateway
 *
 * Stored in commsGateway.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsGateway
 * @category commsGateway
 * @version $Rev: 10 $
 */


/**
 * commsGateway Class
 *
 * Provides access to records in comms.gateways
 *
 * Creating a new record:
 * <code>
 * $oCommsGateway = new commsGateway();
 * $oCommsGateway->setGatewayID($inGatewayID);
 * $oCommsGateway->setActive($inActive);
 * $oCommsGateway->setDescription($inDescription);
 * $oCommsGateway->setClassName($inClassName);
 * $oCommsGateway->setTransportClass($inTransportClass);
 * $oCommsGateway->setCreateDate($inCreateDate);
 * $oCommsGateway->setUpdateDate($inUpdateDate);
 * $oCommsGateway->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsGateway = new commsGateway($inGatewayID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsGateway = new commsGateway();
 * $oCommsGateway->setGatewayID($inGatewayID);
 * $oCommsGateway->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsGateway = commsGateway::getInstance($inGatewayID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage commsGateway
 * @category commsGateway
 */
class commsGateway implements systemDaoInterface, systemDaoValidatorInterface {
	
	const GW_SIMULATOR = 1;
	const GW_APP_LOOP_BACK = 2;
	const GW_EMAIL = 3;
	
	/**
	 * Container for static instances of commsGateway
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
	 * Stores $_GatewayID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GatewayID;

	/**
	 * Stores $_Active
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Active;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_ClassName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ClassName;

	/**
	 * Stores $_TransportClass
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_TransportClass;

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
	 * Stores an instance of commsGatewayNetworkMappingSet
	 *
	 * @var commsGatewayNetworkMappingSet
	 * @access protected
	 */
	protected $_NetworkMappingSet;
	
	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of commsGateway
	 *
	 * @param integer $inGatewayID
	 * @return commsGateway
	 */
	function __construct($inGatewayID = null) {
		$this->reset();
		if ( $inGatewayID !== null ) {
			$this->setGatewayID($inGatewayID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsGateway containing non-unique properties
	 *
	 * @param integer $inActive
	 * @param string $inDescription
	 * @param string $inClassName
	 * @param string $inTransportClass
	 * @param datetime $inCreateDate
	 * @param datetime $inUpdateDate
	 * @return commsGateway
	 * @static
	 */
	public static function factory($inActive = null, $inDescription = null, $inClassName = null, $inTransportClass = null, $inCreateDate = null, $inUpdateDate = null) {
		$oObject = new commsGateway;
		if ( $inActive !== null ) {
			$oObject->setActive($inActive);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inClassName !== null ) {
			$oObject->setClassName($inClassName);
		}
		if ( $inTransportClass !== null ) {
			$oObject->setTransportClass($inTransportClass);
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
	 * Get an instance of commsGateway by primary key
	 *
	 * @param integer $inGatewayID
	 * @return commsGateway
	 * @static
	 */
	public static function getInstance($inGatewayID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inGatewayID]) ) {
			return self::$_Instances[$inGatewayID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsGateway();
		$oObject->setGatewayID($inGatewayID);
		if ( $oObject->load() ) {
			self::$_Instances[$inGatewayID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsGateway
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.gateways';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsGateway();
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
			SELECT gatewayID, active, description, className, transportClass, createDate, updateDate
			  FROM '.system::getConfig()->getDatabase('comms').'.gateways';

		$where = array();
		if ( $this->_GatewayID !== 0 ) {
			$where[] = ' gatewayID = :GatewayID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_GatewayID !== 0 ) {
				$oStmt->bindValue(':GatewayID', $this->_GatewayID);
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
		$this->setGatewayID((int)$inArray['gatewayID']);
		$this->setActive((int)$inArray['active']);
		$this->setDescription($inArray['description']);
		$this->setClassName($inArray['className']);
		$this->setTransportClass($inArray['transportClass']);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.gateways
					( gatewayID, active, description, className, transportClass, createDate, updateDate)
				VALUES
					(:GatewayID, :Active, :Description, :ClassName, :TransportClass, :CreateDate, :UpdateDate)
				ON DUPLICATE KEY UPDATE
					active=VALUES(active),
					description=VALUES(description),
					className=VALUES(className),
					transportClass=VALUES(transportClass),
					createDate=VALUES(createDate),
					updateDate=VALUES(updateDate)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':GatewayID', $this->_GatewayID);
					$oStmt->bindValue(':Active', $this->_Active);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':ClassName', $this->_ClassName);
					$oStmt->bindValue(':TransportClass', $this->_TransportClass);
					$oStmt->bindValue(':CreateDate', $this->_CreateDate);
					$oStmt->bindValue(':UpdateDate', $this->_UpdateDate);

					if ( $oStmt->execute() ) {
						if ( !$this->getGatewayID() ) {
							$this->setGatewayID($oDB->lastInsertId());
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
				$this->_ParamSet->setIndexID($this->getGatewayID());
				$this->_ParamSet->save();
			}
			if ( $this->_NetworkMappingSet instanceof commsGatewayNetworkMappingSet ) {
				$this->_NetworkMappingSet->setGatewayID($this->getGatewayID());
				$this->_NetworkMappingSet->save();
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.gateways
			WHERE
				gatewayID = :GatewayID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':GatewayID', $this->_GatewayID);

			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				
				$this->getParamSet()->deleteAll();
				$this->getNetworkMappingSet()->delete();
				
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
	 * @return commsGateway
	 */
	function reset() {
		$this->_GatewayID = 0;
		$this->_Active = 0;
		$this->_Description = '';
		$this->_ClassName = '';
		$this->_TransportClass = '';
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
		$string .= " GatewayID[$this->_GatewayID] $newLine";
		$string .= " Active[$this->_Active] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " ClassName[$this->_ClassName] $newLine";
		$string .= " TransportClass[$this->_TransportClass] $newLine";
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
		$className = 'commsGateway';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"GatewayID\" value=\"$this->_GatewayID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Active\" value=\"$this->_Active\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"ClassName\" value=\"$this->_ClassName\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"TransportClass\" value=\"$this->_TransportClass\" type=\"string\" /> $newLine";
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
			$valid = $this->checkGatewayID($message);
		}
		if ( $valid ) {
			$valid = $this->checkActive($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkClassName($message);
		}
		if ( $valid ) {
			$valid = $this->checkTransportClass($message);
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
	 * Checks that $_GatewayID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkGatewayID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_GatewayID) && $this->_GatewayID !== 0 ) {
			$inMessage .= "{$this->_GatewayID} is not a valid value for GatewayID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Active has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkActive(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Active) && $this->_Active !== 0 ) {
			$inMessage .= "{$this->_Active} is not a valid value for Active";
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
		if ( $isValid && strlen($this->_Description) > 30 ) {
			$inMessage .= "Description cannot be more than 30 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_ClassName has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkClassName(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ClassName) && $this->_ClassName !== '' ) {
			$inMessage .= "{$this->_ClassName} is not a valid value for ClassName";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_ClassName) > 30 ) {
			$inMessage .= "ClassName cannot be more than 30 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_ClassName) <= 1 ) {
			$inMessage .= "ClassName must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_TransportClass has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkTransportClass(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_TransportClass) && $this->_TransportClass !== '' ) {
			$inMessage .= "{$this->_TransportClass} is not a valid value for TransportClass";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_TransportClass) > 50 ) {
			$inMessage .= "TransportClass cannot be more than 50 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_TransportClass) <= 1 ) {
			$inMessage .= "TransportClass must be more than 1 character";
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
		if ( !$modified && $this->_NetworkMappingSet !== null ) {
			$modified = $modified || $this->_NetworkMappingSet->isModified();
		}
		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return commsGateway
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
		return $this->_GatewayID;
	}

	/**
	 * Return value of $_GatewayID
	 *
	 * @return integer
	 * @access public
	 */
	function getGatewayID() {
		return $this->_GatewayID;
	}

	/**
	 * Set $_GatewayID to GatewayID
	 *
	 * @param integer $inGatewayID
	 * @return commsGateway
	 * @access public
	 */
	function setGatewayID($inGatewayID) {
		if ( $inGatewayID !== $this->_GatewayID ) {
			$this->_GatewayID = $inGatewayID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Active
	 *
	 * @return integer
	 * @access public
	 */
	function getActive() {
		return $this->_Active;
	}

	/**
	 * Set $_Active to Active
	 *
	 * @param integer $inActive
	 * @return commsGateway
	 * @access public
	 */
	function setActive($inActive) {
		if ( $inActive !== $this->_Active ) {
			$this->_Active = $inActive;
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
	 * @return commsGateway
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
	 * Return value of $_ClassName
	 *
	 * @return string
	 * @access public
	 */
	function getClassName() {
		return $this->_ClassName;
	}

	/**
	 * Set $_ClassName to ClassName
	 *
	 * @param string $inClassName
	 * @return commsGateway
	 * @access public
	 */
	function setClassName($inClassName) {
		if ( $inClassName !== $this->_ClassName ) {
			$this->_ClassName = $inClassName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_TransportClass
	 *
	 * @return string
	 * @access public
	 */
	function getTransportClass() {
		return $this->_TransportClass;
	}

	/**
	 * Set $_TransportClass to TransportClass
	 *
	 * @param string $inTransportClass
	 * @return commsGateway
	 * @access public
	 */
	function setTransportClass($inTransportClass) {
		if ( $inTransportClass !== $this->_TransportClass ) {
			$this->_TransportClass = $inTransportClass;
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
	 * @return commsGateway
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
	 * @return commsGateway
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
				system::getConfig()->getDatabase('comms'), 'gatewayParams', 'gatewayID', 'paramName', 'paramValue', $this->getGatewayID(), false
			);
			if ( $this->getGatewayID() > 0 ) {
				$this->_ParamSet->load();
			}
		}
		return $this->_ParamSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param baseTableParamSet $inObject
	 * @return commsGateway
	 */
	function setParamSet(baseTableParamSet $inObject) {
		$this->_ParamSet = $inObject;
		return $this;
	}

	/**
	 * Returns an instance of commsGatewayNetworkMappingSet, which is lazy loaded upon request
	 *
	 * @return commsGatewayNetworkMappingSet
	 */
	function getNetworkMappingSet() {
		if ( !$this->_NetworkMappingSet instanceof commsGatewayNetworkMappingSet ) {
			$this->_NetworkMappingSet = new commsGatewayNetworkMappingSet($this->getGatewayID());
		}
		return $this->_NetworkMappingSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param commsGatewayNetworkMappingSet $inObject
	 * @return commsGateway
	 */
	function setNetworkMappingSet(commsGatewayNetworkMappingSet $inObject) {
		$this->_NetworkMappingSet = $inObject;
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
	 * @return commsGateway
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}