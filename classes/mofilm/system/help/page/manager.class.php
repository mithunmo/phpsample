<?php
/**
 * mofilmSystemHelpPageManager
 *
 * Stored in mofilmSystemHelpPageManager.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSystemHelpPageManager
 * @category mofilmSystemHelpPageManager
 * @version $Rev: 801 $
 */


/**
 * mofilmSystemHelpPageManager Class
 *
 * Managers objects from mofilm_system.helpPages through a single
 * interface.
 *
 * @package mofilm
 * @subpackage mofilmSystemHelpPageManager
 * @category mofilmSystemHelpPageManager
 */
class mofilmSystemHelpPageManager {

	/**
	 * Stores the instance of the mofilmSystemHelpPageManager
	 *
	 * @var mofilmSystemHelpPageManager
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
	 * Creates a new mofilmSystemHelpPageManager instance
	 *
	 * @return mofilmSystemHelpPageManager
	 */
	function __construct() {
		$this->_LoadObjectDetails = false;
	}

	/**
	 * Creates a new empty object
	 *
	 * @return mofilmSystemHelpPages
	 * @static
	 */
	static function newObject() {
		return new mofilmSystemHelpPages();
	}

	/**
	 * Returns a single instance of the manager object
	 *
	 * @return mofilmSystemHelpPageManager
	 * @static
	 */
	static function getInstance() {
		if ( !self::$_Instance instanceof mofilmSystemHelpPageManager ) {
			self::$_Instance = new self();
		}

		return self::$_Instance;
	}

	/**
	 * Returns an instance of the object matching $inObjectId
	 *
	 * @param integer $inObjectId
	 * @return mofilmSystemHelpPages
	 * @static
	 */
	static function getInstanceById($inObjectId) {
		return self::getInstance()->getObjectById($inObjectId);
	}

	/**
	 * Loads a help page by domain and reference
	 *
	 * @param string $inDomainName
	 * @param string $inReference
	 * @param string $inLanguage (optional) default English (en)
	 * @return mofilmSystemHelpPages
	 * @static
	 */
	static function getInstanceByDomainAndReference($inDomainName, $inReference, $inLanguage = 'en') {
		return self::getInstance()->getObjectByDomainAndReference($inDomainName, $inReference, $inLanguage);
	}

	/**
	 * Returns an array of $inLimit objects from $inOffset
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array(mofilmSystemHelpPages)
	 * @static
	 */
	static function listOfObjects($inOffset = 0, $inLimit = 30) {
		return self::getInstance()->getListOfObjects($inOffset, $inLimit);
	}

	/**
	 * Static method that fetches an array of pages preventing multiple SQL queries
	 *
	 * @param array $inArray
	 * @param boolean $inLoadDetails (optional) Set to true to pre-load objects
	 * @return array(mofilmSystemHelpPages)
	 * @throws mofilmException
	 * @static
	 */
	static function loadInstancesByArray($inArray, $inLoadDetails = false) {
		return self::getInstance()
			->setLoadObjectDetails($inLoadDetails)
			->loadObjectsByArray($inArray);
	}



	/**
	 * Loads an object based on the supplied object Id
	 *
	 * @param integer $inObjectId
	 * @return mofilmSystemHelpPages
	 * @throws mofilmException
	 */
	function getObjectById($inObjectId) {
		if ( empty($inObjectId) || strlen($inObjectId) < 1 ) {
			throw new mofilmException('Expected object Id, nothing given');
		}
		if ( !is_numeric($inObjectId) ) {
			throw new mofilmException('Expected object Id to be numeric');
		}

		$query = '
			SELECT helpPages.*
			  FROM '.system::getConfig()->getDatabase('system').'.helpPages
			 WHERE helpPages.id = '.dbManager::getInstance()->quote($inObjectId);

		return $this->_executeSqlQuery($query, false);
	}

	/**
	 * Loads the object by domain and reference, optionally for language $inLanguage
	 *
	 * @param string $inDomainName
	 * @param string $inReference
	 * @param string $inLanguage (optional) default English (en)
	 * @return mofilmSystemHelpPages
	 * @throws mofilmException
	 */
	function getObjectByDomainAndReference($inDomainName, $inReference, $inLanguage = 'en') {
		if ( empty($inDomainName) || strlen($inDomainName) < 1 ) {
			throw new mofilmException('Expected domain name, nothing given');
		}
		if ( empty($inReference) || strlen($inReference) < 1 ) {
			throw new mofilmException('Expected reference, nothing given');
		}

		$query = '
			SELECT helpPages.*
			  FROM '.system::getConfig()->getDatabase('system').'.helpPages
			 WHERE helpPages.domainName = '.dbManager::getInstance()->quote($inDomainName).'
			   AND helpPages.reference = '.dbManager::getInstance()->quote($inReference).'
			   AND helpPages.language = '.dbManager::getInstance()->quote($inLanguage);

		return $this->_executeSqlQuery($query, false);
	}

	/**
	 * Returns an array of objects allowing them to be paged
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array(mofilmSystemHelpPages)
	 */
	function getListOfObjects($inOffset = 0, $inLimit = 30) {
		if ( !is_numeric($inOffset) || $inOffset < 0 ) {
			$inOffset = 0;
		}
		if ( !is_numeric($inLimit) || $inLimit < 1 ) {
			$inLimit = 30;
		}

		$query = '
			SELECT helpPages.*
			  FROM '.system::getConfig()->getDatabase('system').'.helpPages
			 WHERE 1
			 LIMIT '.$inOffset.','.$inLimit;

		return $this->_executeSqlQuery($query, true);
	}

	/**
	 * Fetches an array of objects populating them in one go preventing multiple SQL queries
	 *
	 * @param array $inArray
	 * @return array(mofilmSystemHelpPages)
	 * @throws mofilmException
	 */
	function loadObjectsByArray(array $inArray = array()) {
		if ( count($inArray) < 1 ) {
			throw new mofilmException('Array contains no objects to load');
		}
		foreach ( $inArray as $key ) {
			if ( !is_numeric($key) ) {
				throw new Exception(sprintf('Array contains a non-numeric key (%s)', $key));
			}
		}

		$query = '
			SELECT helpPages.*
			  FROM '.system::getConfig()->getDatabase('system').'.helpPages
			 WHERE helpPages.ID IN ('.implode(', ', $inArray).')
			 ORDER BY FIELD(ID, '.implode(',', $inArray).')';

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

		mofilmSystemHelpPageRelatedSet::loadArrayOfObjectsWithProperties($inArray);
		mofilmSystemHelpPageTagSet::loadArrayOfObjectsWithProperties($inArray);
	}

	/**
	 * Converts a simple array so that it is indexed by the user Id
	 *
	 * @param array $inArray
	 * @return array
	 * @access private
	 * @throws mofilmException
	 */
	private function _createIndexedArray(array $inArray = array()) {
		$return = array();
		/* @var mofilmSystemHelpPages $oObject */
		foreach ( $inArray as $oObject ) {
			if ( is_object($oObject) && method_exists($oObject, 'getID') ) {
				$return[$oObject->getID()] = $oObject;
			} else {
				throw new mofilmException('Expected object with a method "getID" none found');
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
	 * @return mofilmSystemHelpPageManager
	 */
	function setLoadObjectDetails($inLoadObjectDetails) {
		if ( $inLoadObjectDetails !== $this->_LoadObjectDetails ) {
			$this->_LoadObjectDetails = $inLoadObjectDetails;
		}

		return $this;
	}
}