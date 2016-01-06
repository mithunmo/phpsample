{*
Template contains the following keys:

'appAuthor' -> the application author
'appCopyright' -> application copyright notice
'classBaseString' -> the prefix for the class (database name canonised)
'database' -> the database this table is in
'exceptionClass' -> name of the exception to class to throw on error
'oConfig' -> full base config
'className' -> our intended class name
'primaryKey' -> array of fields that make up the primary key
'uniqueKeys' -> array of unique indexes containing the fields that make the key
'uniqueKeyFields' -> array of unqiue fields from all unique indexes
'nonUniqueFields' -> array of fields NOT in any unique or primary keys
'oTableDefinition' -> the full table definition object
'textUtil' -> text transform utility class
'hasUpdateField' -> set if the table has a field named updateDate

Generator uses { & } as a Smarty var
*}
{'<?php'}
/**
 * {$className}
 *
 * Stored in {$className}.class.php
 *
 * @author {$appAuthor}
 * @copyright {$appCopyright}
 * @package {$classBaseString}
 * @subpackage {$className}
 * @category {$className}
 * @version $Rev: 801 $
 */


/**
 * {$className} Class
 *
 * Managers objects from {$database}.{$oTableDefinition->getTableName()} through a single
 * interface.
 *
 * @package {$classBaseString}
 * @subpackage {$className}
 * @category {$className}
 */
class {$className} {

	/**
	 * Stores the instance of the {$className}
	 *
	 * @var {$className}
	 * @access private
	 * @static
	 */
	private static $_Instance;

	/**
	 * Stores $_LoadObjectDetails
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_LoadObjectDetails = false;



	/**
	 * Creates a new {$className} instance
	 *
	 * @return {$className}
	 */
	function __construct() {
		$this->_LoadObjectDetails = false;
	}

	/**
	 * Creates a new empty object
	 *
	 * @return {$className|replace:'Manager':''}
	 * @static
	 */
	static function newObject() {
		/**
		 * @todo Set the object instance to create
		 */
		return new {$className|replace:'Manager':''}();
	}

	/**
	 * Returns a single instance of the manager object
	 *
	 * @return {$className}
	 * @static
	 */
	static function getInstance() {
		if ( !self::$_Instance instanceof {$className} ) {
			self::$_Instance = new self();
		}

		return self::$_Instance;
	}

	/**
	 * Returns an instance of the object matching $inObjectId
	 *
	 * @param integer $inObjectId
	 * @return {$className|replace:'Manager':''}
	 * @static
	 */
	static function getInstanceById($inObjectId) {
		return self::getInstance()->getObjectById($inObjectId);
	}

	/**
	 * Returns an array of $inLimit objects from $inOffset
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array({$className|replace:'Manager':''})
	 * @static
	 */
	static function listOfObjects($inOffset = 0, $inLimit = 30) {
		return self::getInstance()->getListOfObjects($inOffset, $inLimit);
	}



	/**
	 * Loads an object based on the supplied object Id
	 *
	 * @param integer $inObjectId
	 * @return {$className|replace:'Manager':''}
	 * @throws {$exceptionClass}
	 */
	function getObjectById($inObjectId) {
		if ( empty($inObjectId) || strlen($inObjectId) < 1 ) {
			throw new {$exceptionClass}('Expected object Id, nothing given');
		}
		if ( !is_numeric($inObjectId) ) {
			throw new {$exceptionClass}('Expected object Id to be numeric');
		}

		$query = '
			SELECT {$oTableDefinition->getTableName()}.*
			  FROM '.system::getConfig()->getDatabase('{$database}').'.{$oTableDefinition->getTableName()}
			 WHERE {$oTableDefinition->getTableName()}.id = '.dbManager::getInstance()->quote($inObjectId);

		return $this->_executeSqlQuery($query, false);
	}

	/**
	 * Returns an array of objects allowing them to be paged
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array({$className|replace:'Manager':''})
	 */
	function getListOfObjects($inOffset = 0, $inLimit = 30) {
		if ( !is_numeric($inOffset) || $inOffset < 0 ) {
			$inOffset = 0;
		}
		if ( !is_numeric($inLimit) || $inLimit < 1 ) {
			$inLimit = 30;
		}

		$query = '
			SELECT {$oTableDefinition->getTableName()}.*
			  FROM '.system::getConfig()->getDatabase('{$database}').'.{$oTableDefinition->getTableName()}
			 WHERE 1
			 LIMIT '.$inOffset.','.$inLimit;

		return $this->_executeSqlQuery($query, true);
	}

	/**
	 * Fetches an array of objects populating them in one go preventing multiple SQL queries
	 *
	 * @param array $inArray
	 * @return array({$className|replace:'Manager':''})
	 * @throws {$exceptionClass}
	 */
	function loadObjectsByArray(array $inArray = array()) {
		if ( count($inArray) < 1 ) {
			throw new {$exceptionClass}('Array contains no objects to load');
		}
		foreach ( $inArray as $key ) {
			if ( !is_numeric($key) ) {
				throw new Exception(sprintf('Array contains a non-numeric key (%s)', $key));
			}
		}

		/**
		 * @todo: Set loading by primary key
		 */
		$query = '
			SELECT {$oTableDefinition->getTableName()}.*
			  FROM '.system::getConfig()->getDatabase('{$database}').'.{$oTableDefinition->getTableName()}
			 WHERE {$oTableDefinition->getTableName()}.id IN ('.implode(', ', $inArray).')
			 ORDER BY FIELD(id, '.implode(',', $inArray).')';

		return $this->_executeSqlQuery($query, true);
	}


	
	/**
	 * Executes the SQL query, populating the result as necessary
	 *
 	 * $inFetchAll controls whether one or all results are returned
	 *
	 * @param string $inSql
	 * @param boolean $inFetchAll
	 * @return mixed
	 * @access private
	 */
	private function _executeSqlQuery($inSql, $inFetchAll = false) {
		$return = array();
		$oStmt = dbManager::getInstance()->prepare($inSql);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = self::newObject();
				$oObject->loadFromArray($row);

				$return[] = $oObject;

				if ( $inFetchAll === false ) {
					break;
				}
			}
		}
		$oStmt->closeCursor();

		if ( $this->getLoadObjectDetails() ) {
			$this->_loadObjectDetails($return);
		}

		if ( $inFetchAll ) {
			return $return;
		} else {
			if ( isset($return[0]) && is_object($return[0]) ) {
				return $return[0];
			}
		}

		return false;
	}

	/**
	 * Pre-loads user data
	 *
	 * @param array $inArray
	 * @return void
	 * @access private
	 */
	private function _loadObjectDetails(array $inArray = array()) {
		$inArray = $this->_createIndexedArray($inArray);

	}

	/**
	 * Converts a simple array so that it is indexed by the user Id
	 *
	 * @param array $inArray
	 * @return array
	 * @access private
	 * @throws {$exceptionClass}
	 */
	private function _createIndexedArray(array $inArray = array()) {
		$return = array();
		foreach ( $inArray as $oObject ) {
			if ( is_object($oObject) && method_exists($oObject, 'getId') ) {
				$return[$oObject->getId()] = $oObject;
			} else {
				throw new {$exceptionClass}('Expected object with a method "getId" none found');
			}
		}

		return $return;
	}

	/**
	 * Returns true if sub-objects should be loaded
	 *
	 * @return boolean
	 */
	function getLoadObjectDetails() {
		return $this->_LoadObjectDetails;
	}

	/**
	 * Set to true to load sub-objects when fetching instances
	 *
	 * @param boolean $inLoadObjectDetails
	 * @return {$className}Manager
	 */
	function setLoadObjectDetails($inLoadObjectDetails) {
		if ( $inLoadObjectDetails !== $this->_LoadObjectDetails ) {
			$this->_LoadObjectDetails = $inLoadObjectDetails;
		}

		return $this;
	}
}