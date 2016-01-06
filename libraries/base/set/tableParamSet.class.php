<?php
/**
 * baseTableParamSet
 * 
 * System base set
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage base
 * @category baseTableParamSet
 * @version $Rev: 727 $
 */


/**
 * baseTableParamSet
 * 
 * Provides a consistent interface to any param table so long as it conforms to the schema:
 * id, $this->_DbPropertyField, $this->_DbPropertyValueField where the primary key is made
 * of id & $this->_DbPropertyField.
 * 
 * The value field should be of "text" type or equivalent as all params stored are serialised
 * before being stored. This allows arrays and/or objects to be stored without having to 
 * first check the values.
 * 
 * This class should be used inside other objects and be lazy loaded when required.
 * 
 * <code>
 * // straight initialisation
 * $oParams = new baseTableParamSet('myDatabase','myTable','tableIndex', 4);
 * 
 * // within an object
 * class myObject {
 * 
 *     function getParams() {
 *         if ( !$this->_ParamSet instanceof baseTableParamSet ) {
 *             $this->_ParamSet = new baseTableParamSet(
 *                 'myDatabase', 'myTable', 'tableIndex', 'tableParamNameColumn',
 *                 'tableParamValueColumn', $this->_IndexID
 *             );
 *         }
 *         return $this->_ParamSet;
 *     }
 * }
 * $oObject = new myObject;
 * $oObject->getParams()->setParam('paramName', 'value');
 * // object parent save method should cascade to param set...
 * $oObject->save();
 * </code>
 * 
 * @package scorpio
 * @subpackage base
 * @category baseTableParamSet
 */
class baseTableParamSet extends baseSet implements systemDaoInterface {
	
	/**
	 * Database the params table exists on
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DbName						= false;
	/**
	 * Table name of the params table
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DbTable						= false;
	/**
	 * Primary key name in the table e.g. sessionID, serviceID etc.
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DbTableIndex				= false;
	/**
	 * Table index value to extract params for
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_IndexID						= false;
	/**
	 * Array of parameters to be removed from the system
	 *
	 * @var array
	 * @access protected
	 */
	protected $_BinParams					= array();
	/**
	 * Stores $_DbPropertyField
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DbPropertyField				= false;
	/**
	 * Stores $_DbPropertyValueField
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DbPropertyValueField		= false;
	/**
	 * Stores $_AutoSerialise
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_AutoSerialise				= true;
	
	
	
	/**
	 * Returns a new paramCollection
	 *
	 * @param string $inDbName
	 * @param string $inDbTable
	 * @param string $inDbTableIndex
	 * @param string $inDbPropertyField
	 * @param string $inDbPropertyValueField
	 * @param integer $inIndexID
	 */
	function __construct($inDbName, $inDbTable, $inDbTableIndex, $inDbPropertyField, $inDbPropertyValueField, $inIndexID, $inAutoSerialise = true) {
		if ( strlen($inDbName) > 1 && strlen($inDbTable) > 1 && strlen($inDbTableIndex) > 1  ) {
			$this->setDbName($inDbName);
			$this->setDbTable($inDbTable);
			$this->setTableIndex($inDbTableIndex);
			$this->setDbPropertyField($inDbPropertyField);
			$this->setDbPropertyValueField($inDbPropertyValueField);
			$this->setIndexID($inIndexID);
			$this->setAutoSerialise($inAutoSerialise);
			if ( $inIndexID !== '' && $inIndexID !== 0 ) {
				$this->load();
			}
		} else {
			throw new systemException("Missing one of dbName ($inDbName), dbTable ($inDbTable), dbTableIndex ($inDbTableIndex), dbPropertyField ($inDbPropertyField) or dbPropertyValueField ($inDbPropertyValueField)");
		}
	}
	
	/**
	 * Returns a textual representation of this object
	 * 
	 * @return string
	 */
	function __toString() {
		return "TableParamSet for $this->_DbName.$this->_DbTable with keyID $this->_DbTableIndex ($this->_IndexID)";
	}
	
	/**
	 * Returns object properties as an array
	 *
	 * @return array
	 */
	function toArray() {
		return get_object_vars($this);
	}
	
	/**
	 * Loads param collection into object
	 *
	 * @return void
	 */
	function load() {
		$oStmt = dbManager::getInstance()->prepare("SELECT $this->_DbPropertyField, $this->_DbPropertyValueField FROM $this->_DbName.$this->_DbTable WHERE $this->_DbTableIndex = :indexID");
		$oStmt->bindValue(':indexID', $this->_IndexID);
		$oStmt->execute();
		foreach ( $oStmt as $result ) {
			$this->_setItem($result[$this->_DbPropertyField], ($this->getAutoSerialise() ? unserialize($result[$this->_DbPropertyValueField]) : $result[$this->_DbPropertyValueField]));
		}
		$this->setModified(false);
	}
	
	/**
	 * Saves back params to the database if updated / changed, returns number of rows affected
	 *
	 * @return integer
	 */
	function save() {
		$results = 0;
		if ( $this->isModified()  ) {
			
			$sql = "
				INSERT INTO $this->_DbName.$this->_DbTable
					($this->_DbTableIndex, $this->_DbPropertyField, $this->_DbPropertyValueField)
				VALUES ";
			
			$values = array();
			$added = 0;
			foreach ( $this as $key => $value ) {
				if ( is_null($value) ||  is_null($key) || $key == '' ) {
					$this->_BinParams[] = $key;
				} else {
					$store = ($this->getAutoSerialise() ? serialize($value) : $value);
					$values[] = " ('$this->_IndexID', ".dbManager::getInstance()->quote($key).", ".dbManager::getInstance()->quote($store).")";
					$added++;
				}
			}
			
			$sql .= implode(",\n", $values);
			$sql .= "\nON DUPLICATE KEY UPDATE $this->_DbPropertyValueField = VALUES($this->_DbPropertyValueField)";
			
			$oDb = dbManager::getInstance();
			if ($added > 0) {
				$results = $oDb->exec($sql);
			}
			$this->setModified(false);
		}
		$this->delete();
		return $results;
	}
	
	/**
	 * Deletes the queued params from the database
	 *
	 * @return integer
	 */
	function delete() {
		$results = 0;
		if ( $this->_IndexID && count($this->_BinParams) > 0 ) {
			$where = implode("' OR $this->_DbPropertyField = '", $this->_BinParams);
			$sql = "DELETE FROM $this->_DbName.$this->_DbTable WHERE $this->_DbTableIndex = '$this->_IndexID' AND ($this->_DbPropertyField = '$where') LIMIT ".count($this->_BinParams);
			
			$oDb = dbManager::getInstance();
			$results = $oDb->exec($sql);
			$this->_BinParams = array();
		}
		return $results;
	}

	/**
	 * Deletes all params for the current index; returns rows affected
	 *
	 * @return integer
	 */
	function deleteAll() {
		$results = 0;
		if ( $this->_IndexID ) {
			$sql = "DELETE FROM $this->_DbName.$this->_DbTable WHERE $this->_DbTableIndex = '$this->_IndexID'";
			
			$oDb = dbManager::getInstance();
			$results = $oDb->exec($sql);
		}
		return $results;
	}
	
	/**
	 * Find a parameter(s) using a regular expression, always returns an array
	 *
	 * @param string $inRegex PERL regular expression
	 * @return array
	 */
	function findByRegex($inRegex) {
		$found = array();
		foreach ( $this as $key => $value ) {
			if ( preg_match($inRegex, $key) ) {
				$found[$key] = $value;
			}
		}
		return $found;
	}
	
	/**
	 * Clears all values
	 *
	 * @return void
	 */
	public function reset() {
		$this->setModified(false);
	}
	
	/**
	 * Returns the index for the param set
	 *
	 * @return mixed
	 */
	function getIndexID() {
		return  $this->_IndexID;
	}
	
	/**
	 * Allows you to reset the indexID
	 *
	 * @param integer $inIndexID
	 * @return baseTableParamSet
	 */
	public function setIndexID($inIndexID) {
		if ($this->_IndexID !== $inIndexID) {
			$this->_IndexID = $inIndexID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Sets a new param -> value pair
	 *
	 * @param string $inParamName
	 * @param mixed $inParamValue
	 * @return baseTableParamSet
	 */
	public function setParam($inParamName, $inParamValue) {
		return $this->_setItem($inParamName, $inParamValue);
	}
	
	/**
	 * Returns the value of $inParamName, if $inParamName is null returns all params, if $inDefault is specifed returns $inDefault if no param match
	 *
	 * @param string $inParamName
	 * @param mixed $inDefault
	 * @return mixed
	 */
	public function getParam($inParamName = null, $inDefault = null) {
		$value = $this->_getItem($inParamName);
		if ($value === false) {
			return $inDefault;
		}
		return $value;
	}
	
	/**
	 * Unsets a param from the set
	 *
	 * @param string $inParamName
	 * @return baseTableParamSet
	 */
	public function unsetParam($inParamName) {
		if ( $this->getParam($inParamName) ) {
			$this->_BinParams[] = $inParamName;
			return $this->_removeItem($inParamName);
		}
		return $this;
	}
	
	/**
	 * Returns the number of params in the set
	 *
	 * @return integer
	 */
	public function getParamCount() {
		return parent::_itemCount();
	}
	
	/**
	 * Merges $inParams into this param set
	 * 
	 * @param array $inParams
	 * @return baseTableParamSet
	 */
	public function mergeParams(array $inParams) {
		return $this->_mergeSets($inParams);
	}
	
	/**
	 * Returns $_DbName
	 *
	 * @return string
	 */
	public function getDbName() {
		return $this->_DbName;
	}
	
	/**
	 * Set DbName to $inDbName
	 *
	 * @param string $inDbName
	 * @return baseTableParamSet
	 */
	public function setDbName($inDbName) {
		if ( $inDbName !== $this->_DbName ) {
			$this->_DbName = $inDbName;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_DbTable
	 *
	 * @return string
	 */
	function getDbTable() {
		return $this->_DbTable;
	}
	
	/**
	 * Set $_DbTable to $inDbTable
	 *
	 * @param string $inDbTable
	 * @return baseTableParamSet
	 */
	function setDbTable($inDbTable) {
		if ( $inDbTable !== $this->_DbTable ) {
			$this->_DbTable = $inDbTable;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return $_DbTableIndex
	 *
	 * @return string
	 */
	function getDbTableIndex() {
		return $this->_DbTableIndex;
	}
	
	/**
	 * Set $_DbTableIndex to $inTableIndex
	 *
	 * @param string $inTableIndex
	 * @return baseTableParamSet
	 */
	function setTableIndex($inTableIndex) {
		if ( $inTableIndex !== $this->_DbTableIndex ) {
			$this->_DbTableIndex = $inTableIndex;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_DbPropertyField
	 *
	 * @return string
	 */
	function getDbPropertyField() {
		return $this->_DbPropertyField;
	}
	
	/**
	 * Set DbPropertyField property
	 *
	 * @param string $inDbPropertyField
	 * @return baseTableParamSet
	 */
	function setDbPropertyField($inDbPropertyField) {
		if ( $inDbPropertyField !== $this->_DbPropertyField ) {
			$this->_DbPropertyField = $inDbPropertyField;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_DbPropertyValueField
	 *
	 * @return string
	 */
	function getDbPropertyValueField() {
		return $this->_DbPropertyValueField;
	}
	
	/**
	 * Set DbPropertyValueField property
	 *
	 * @param string $inDbPropertyValueField
	 * @return baseTableParamSet
	 */
	function setDbPropertyValueField($inDbPropertyValueField) {
		if ( $inDbPropertyValueField !== $this->_DbPropertyValueField ) {
			$this->_DbPropertyValueField = $inDbPropertyValueField;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_AutoSerialise
	 *
	 * @return boolean
	 * @access public
	 */
	function getAutoSerialise() {
		return $this->_AutoSerialise;
	}
	
	/**
	 * Set $_AutoSerialise to $inAutoSerialise
	 *
	 * @param boolean $inAutoSerialise
	 * @return baseTableParamSet
	 * @access public
	 */
	function setAutoSerialise($inAutoSerialise) {
		if ( $this->_AutoSerialise !== $inAutoSerialise ) {
			$this->_AutoSerialise = $inAutoSerialise;
			$this->setModified();
		}
		return $this;
	}
}