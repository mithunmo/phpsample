<?php
/**
 * mofilmUserManager
 *
 * Stored in manager.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage user
 * @category mofilmUserManager
 * @version $Rev: 320 $
 */


/**
 * mofilmUserManager Class
 *
 * This is the main user object loader. It allows user objects to be
 * loaded by id or from an array of ids and to populate user objects
 * in a bulk fashion.
 *
 * By default userManager only loads active user records. To load
 * inactive, set the LoadOnlyActive to false.
 *
 * <code>
 * // fetch a user instance
 * $oUser = mofilmUserManager::getInstanceByID(12345);
 *
 * // load all user details
 * $oUser = mofilmUserManager::getInstance()
 *     ->setLoadUserDetails(true)
 *     ->getUserByID(12345);
 * </code>
 *
 * @package mofilm
 * @subpackage user
 * @category mofilmUserManager
 */
class mofilmUserManager {

	/**
	 * Stores $_LoadOnlyActive
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_LoadOnlyActive;

	/**
	 * Stores $_LoadUserDetails
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_LoadUserDetails;

	/**
	 * Stores $_PermissionsNamespace
	 *
	 * @var string
	 * @access protected
	 */
	protected $_PermissionsNamespace;



	/**
	 * Returns a new mofilmUserManager instance
	 *
	 * @return mofilmUserManager
	 */
	function __construct() {
		$this->_LoadOnlyActive = true;
		$this->_LoadUserDetails = false;
		$this->_PermissionsNamespace = null;
	}



	/**
	 * Returns an instance of the mofilmUserManager
	 *
	 * @return mofilmUserManager
	 * @static
	 */
	static function getInstance() {
		return new mofilmUserManager();
	}

	/**
	 * Static method to load a user based on the supplied ID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserBase
	 * @throws mofilmException
	 * @static
	 */
	static function getInstanceByID($inUserID) {
		$oUserMan = new mofilmUserManager();
		return $oUserMan->getUserByID($inUserID);
	}

	/**
	 * Static method to load a user based on the confirmation hash
	 *
	 * @param string $inHash
	 * @return mofilmUserBase
	 * @throws mofilmException
	 * @static
	 */
	static function getInstanceByHash($inHash) {
		$oUserMan = new mofilmUserManager();
		return $oUserMan->getUserByHash($inHash);
	}

	/**
	 * Static method to load a user based on their username
	 *
	 * @param string $inUsername
	 * @return mofilmUserBase
	 * @static
	 */
	static function getInstanceByUsername($inUsername) {
		$oUserMan = new mofilmUserManager();
		return $oUserMan->getUserByUsername($inUsername);
	}

	/**
	 * Static method to load a user based on their login credentials
	 *
	 * @param string $inUsername
	 * @param string $inPassword
	 * @return mofilmUserBase
	 * @static
	 */
	static function getInstanceByUserLogin($inUsername, $inPassword) {
		$oUserMan = new mofilmUserManager();
		return $oUserMan->getUserByUserLogin($inUsername, $inPassword);
	}
	
	/**
	 * Static method to load a user based on their facebook account
	 *
	 * @param string $inUsername
	 * @param string $inPassword
	 * @return mofilmUserBase
	 * @static
	 */
	static function getInstanceByFacebookLogin($inFacebook) {
		$oUserMan = new mofilmUserManager();
		return $oUserMan->getUserByFacebookLogin($inFacebook);
	}

	/**
	 * Static method to load a user based on their profile name
	 *
	 * @param string $inProfileName
	 * @return mofilmUserBase
	 * @static
	 */
	static function getInstanceByProfileName($inProfileName) {
		$oUserMan = new mofilmUserManager();
		return $oUserMan->getUserByProfileName($inProfileName);
	}

	/**
	 * Static method that fetches an array of users preventing multiple SQL queries
	 *
	 * @param array $inArray
	 * @return array(mofilmUserBase)
	 * @throws mofilmException
	 * @static
	 */
	static function loadInstancesByArray($inArray) {
		$oUserMan = new mofilmUserManager();
		return $oUserMan->loadUsersByArray($inArray);
	}

	/**
	 * Loads an array of user objects with additional data, used with search results
	 *
	 * @param array $inArray
	 * @static
	 */
	static function loadUserObjectArrayWithData(array $inArray = array()) {
		$oUserMan = new mofilmUserManager();
		$oUserMan->_loadUserDetails($inArray);
	}
	
	/**
	 * Loads an array of movie objects with the appropriate user. Expects $inMovies to be indexed by movie ID
	 * 
	 * @param array $inMovies
	 * @return boolean
	 * @static
	 */
	static function loadArrayOfMoviesWithProperties(array $inMovies) {
		$return = false;
		$properties = array();
		if ( count($inMovies) > 0 ) {
			$query = '
				SELECT movies.ID AS movieID, users.*
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.users ON (movies.userID = users.ID)
				 WHERE movies.ID IN ('.implode(',', array_keys($inMovies)).')';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmUser();
					$oObject->loadFromArray($row);
		 			$properties[$row['movieID']] = $oObject;
				}
			}
			$oStmt->closeCursor();
			
			if ( false ) $oMovie = new mofilmMovie();
			foreach ( $inMovies as $oMovie ) {
				if ( $oMovie instanceof mofilmMovie ) {
					if ( array_key_exists($oMovie->getID(), $properties) ) {
		 				$oMovie->setUser($properties[$oMovie->getID()]);
		 				$return = true;
		 			}
				}
			}
		}
		return $return;
	}
	
	/**
	 * Returns an array of properties that a user can have
	 * 
	 * Note: this is used with the param set and should be kept updated
	 * as new properties (userData values) are added to the system.
	 * This is a mapping of propertyName to displayName
	 * 
	 * @return array
	 * @static
	 */
	static function getProperties() {
		return array(
			'Address1' => 'Address Line 1',
 	 	 	'Address2' => 'Address Line 2',
 	 	 	'City' => 'City',
			'County' => 'County',
			'Postcode' => 'Postcode',
			'DateOfBirth' => 'Date of Birth',
			'Phone' => 'Phone',
			'Skype' => 'Skype Username',
 	 	 	'Company' => 'Company',
 	 	 	'Occupation' => 'Occupation',
 	 	 	'Website' => 'Website',
		);
	}



	/**
	 * Loads a user based on the supplied ID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserBase
	 * @throws mofilmException
	 */
	function getUserByID($inUserID) {
		if ( empty($inUserID) || strlen($inUserID) < 1 ) {
			throw new mofilmException('Expected user ID, nothing given');
		}
		if ( !is_numeric($inUserID) ) {
			throw new mofilmException('Expected user ID to be numeric');
		}

		$query = '
			SELECT users.*
			  FROM '.$this->_buildPermissionsTableSql().'
			 WHERE users.ID = '.$inUserID.$this->_buildWhereSql();

		return $this->_executeSqlQuery($query, false);
	}

	/**
	 * Loads a user based on the supplied confirmation hash
	 *
	 * @param string $inHash
	 * @return mofilmUserBase
	 * @throws mofilmException
	 */
	function getUserByHash($inHash) {
		if ( empty($inHash) || strlen($inHash) < 1 ) {
			throw new mofilmException('Expected user hash, nothing given');
		}

		$query = '
			SELECT users.*
			  FROM '.$this->_buildPermissionsTableSql().'
			 WHERE users.hash = '.dbManager::getInstance()->quote($inHash).$this->_buildWhereSql();

		return $this->_executeSqlQuery($query, false);
	}

	/**
	 * Loads a user based on the supplied username only
	 *
	 * @param string $inUsername
	 * @return mofilmUserBase
	 * @throws mofilmException
	 */
	function getUserByUsername($inUsername) {
		if ( empty($inUsername) || strlen($inUsername) < 3 ) {
			throw new mofilmException('Expected username, nothing given');
		}

		$query = '
			SELECT users.*
			  FROM '.$this->_buildPermissionsTableSql().'
			 WHERE users.email = '.dbManager::getInstance()->quote($inUsername);

		$query .= $this->_buildWhereSql();

		return $this->_executeSqlQuery($query, false);
	}

	/**
	 * Loads a user based on the supplied profile name
	 *
	 * @param string $inProfileName
	 * @return mofilmUserBase
	 * @throws mofilmException
	 */
	function getUserByProfileName($inProfileName) {
		if ( empty($inProfileName) || strlen($inProfileName) < 1 ) {
			throw new mofilmException('Expected user profile name, nothing given');
		}
		
		$query = '
			SELECT users.*
			  FROM '.$this->_buildPermissionsTableSql().'
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.userProfiles ON (users.ID = userProfiles.userID)
			 WHERE userProfiles.profileName = '.dbManager::getInstance()->quote($inProfileName).'
			   AND userProfiles.active = 1';
		
		$query .= $this->_buildWhereSql();

		return $this->_executeSqlQuery($query, false);
	}

	/**
	 * Loads a user based on the supplied login credentials
	 *
	 * @param string $inUsername
	 * @param string $inPassword
	 * @return mofilmUserBase
	 * @throws mofilmException
	 */
	function getUserByUserLogin($inUsername, $inPassword) {
		if ( empty($inUsername) || strlen($inUsername) < 3 ) {
			throw new mofilmException('Expected username, nothing given');
		}
		if ( empty($inPassword) || strlen($inPassword) < 1 ) {
			throw new mofilmException('Expected password, nothing given');
		}

		$query = '
			SELECT users.*
			  FROM '.$this->_buildPermissionsTableSql().'
			 WHERE users.email = '.dbManager::getInstance()->quote($inUsername).'
			   AND users.password = '.dbManager::getInstance()->quote(md5($inPassword));

		$query .= $this->_buildWhereSql();

		return $this->_executeSqlQuery($query, false);
	}
	
	/**
	 * Loads a user based on the supplied facebook user id
	 *
	 * @param string $inFacebookID
	 * @return mofilmUserBase
	 * @throws mofilmException
	 */
	function getUserByFacebookLogin($inFacebookID) {
		if ( empty($inFacebookID) || strlen($inFacebookID) < 2 ) {
			throw new mofilmException('Expected facebookID, nothing given');
		}

		$query = '
			SELECT users.*
			  FROM '.$this->_buildPermissionsTableSql().'
			 WHERE users.facebookID = '.dbManager::getInstance()->quote($inFacebookID);

		$query .= $this->_buildWhereSql();
		
		return $this->_executeSqlQuery($query, false);
	}	

	/**
	 * Fetches an array of users populating them in one go preventing multiple SQL queries
	 *
	 * Note: returns an array of mofilmUsers ordered by the order they appear in $inArray.
	 * This is done by using: ORDER BY FIELD(ID, 1...n+1). Arrays of ints should be kept
	 * relatively small (i.e. less than 100) for performance reasons.
	 *
	 * @param array $inArray
	 * @return array(mofilmUserBase)
	 * @throws mofilmException
	 */
	function loadUsersByArray(array $inArray = array()) {
		if ( count($inArray) < 1 ) {
			throw new mofilmException('Array contains no users to load');
		}
		$query = '
			SELECT users.*
			  FROM '.$this->_buildPermissionsTableSql().'
			 WHERE users.ID IN ('.implode(',', $inArray).')'.$this->_buildWhereSql();
		
		$query .= ' ORDER BY FIELD(ID, '.implode(',', $inArray).')';

		return $this->_executeSqlQuery($query, true);
	}

	/**
	 * Builds the permissions links as necessary
	 *
	 * @return string
	 * @access private
	 */
	private function _buildPermissionsTableSql() {
		$return = system::getConfig()->getDatabase('mofilm_content').'.users ';

		return $return;
	}

	/**
	 * Builds up an additional where clause
	 *
	 * @return string
	 * @access private
	 */
	private function _buildWhereSql() {
		$return = '';
		if ( $this->getLoadOnlyActive() ) {
			$return .= ' AND users.enabled = "Y" ';
		}
		return $return;
	}

	/**
	 * Executes the SQL query, populating the result as necessary, $inFetchAll controls
	 * whether one or all results are returned
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
				$oObject = new mofilmUser();
				$oObject->loadFromArray($row);
				$oObject->setPermissionsNamespace($this->getPermissionsNamespace());
				$return[] = $oObject;

				if ( $inFetchAll === false ) {
					break;
				}
			}
		}
		$oStmt->closeCursor();

		if ( $this->getLoadUserDetails() ) {
			$this->_loadUserDetails($return);
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
	 * @return array
	 * @access private
	 */
	private function _loadUserDetails(array $inArray = array()) {
		$inArray = $this->_createIndexedArray($inArray);
		
		mofilmUserMovieStats::loadArrayOfUsersWithProperties($inArray);
		mofilmUserPoints::loadArrayOfUsersWithProperties($inArray);
	}

	/**
	 * Converts a simple array so that it is indexed by the user ID
	 *
	 * @param array $inArray
	 * @return array
	 * @access private
	 */
	private function _createIndexedArray(array $inArray = array()) {
		$return = array();
		foreach ( $inArray as $oUser ) {
			if ( $oUser instanceof mofilmUserBase ) {
				$return[$oUser->getID()] = $oUser;
			} else {
				throw new mofilmException(__CLASS__.'::'.__METHOD__.' Operation failed on a none user object');
			}
		}
		return $return;
	}



	/**
	 * Returns $_LoadOnlyActive
	 *
	 * @return boolean
	 * @access public
	 */
	function getLoadOnlyActive() {
		return $this->_LoadOnlyActive;
	}

	/**
	 * Set $_LoadOnlyActive to $inLoadOnlyActive
	 *
	 * @param boolean $inLoadOnlyActive
	 * @return mofilmUserManager
	 * @access public
	 */
	function setLoadOnlyActive($inLoadOnlyActive) {
		if ( $this->_LoadOnlyActive !== $inLoadOnlyActive ) {
			$this->_LoadOnlyActive = $inLoadOnlyActive;
		}
		return $this;
	}

	/**
	 * Returns $_LoadUserDetails
	 *
	 * @return boolean
	 * @access public
	 */
	function getLoadUserDetails() {
		return $this->_LoadUserDetails;
	}

	/**
	 * Set $_LoadUserDetails to $inLoadUserDetails
	 *
	 * @param boolean $inLoadUserDetails
	 * @return mofilmUserManager
	 * @access public
	 */
	function setLoadUserDetails($inLoadUserDetails) {
		if ( $this->_LoadUserDetails !== $inLoadUserDetails ) {
			$this->_LoadUserDetails = $inLoadUserDetails;
		}
		return $this;
	}

	/**
	 * Return value of $_PermissionsNamespace
	 *
	 * @return string
	 * @access public
	 */
	function getPermissionsNamespace() {
		if ( !$this->_PermissionsNamespace ) {
			$namespace = system::getConfig()->getParam('permissions', 'namespace', false)->getParamValue();
			if ( $namespace ) {
				$this->_PermissionsNamespace = $namespace;
			}
		}
		return $this->_PermissionsNamespace;
	}

	/**
	 * Set $_PermissionsNamespace to $inPermissionsNamespace
	 *
	 * @param string $inPermissionsNamespace
	 * @return mofilmUserManager
	 * @access public
	 */
	function setPermissionsNamespace($inPermissionsNamespace) {
		if ( $inPermissionsNamespace !== $this->_PermissionsNamespace ) {
			$this->_PermissionsNamespace = $inPermissionsNamespace;
		}
		return $this;
	}
}