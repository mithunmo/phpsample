<?php
/**
 * mofilmBanner
 *
 * Stored in mofilmBanner.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmBanner
 * @category mofilmBanner
 * @version $Rev: 245 $
 */


/**
 * mofilmBanner Class
 *
 * Provides access to records in mofilm_comms.banners
 *
 * Creating a new record:
 * <code>
 * $oMofilmBanner = new mofilmBanner();
 * $oMofilmBanner->setID($inID);
 * $oMofilmBanner->setDescription($inDescription);
 * $oMofilmBanner->setUrl($inUrl);
 * $oMofilmBanner->setAffiliate($inAffiliate);
 * $oMofilmBanner->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmBanner = new mofilmBanner($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmBanner = new mofilmBanner();
 * $oMofilmBanner->setID($inID);
 * $oMofilmBanner->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmBanner = mofilmBanner::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmBanner
 * @category mofilmBanner
 */
class mofilmBanner implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmBanner
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
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_Url
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Url;

	/**
	 * Stores $_Affiliate
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Affiliate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmBanner
	 *
	 * @param integer $inID
	 * @return mofilmBanner
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
	 * Creates a new mofilmBanner containing non-unique properties
	 *
	 * @param string $inDescription
	 * @param string $inUrl
	 * @param string $inAffiliate
	 * @return mofilmBanner
	 * @static
	 */
	public static function factory($inDescription = null, $inUrl = null, $inAffiliate = null) {
		$oObject = new mofilmBanner;
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inUrl !== null ) {
			$oObject->setUrl($inUrl);
		}
		if ( $inAffiliate !== null ) {
			$oObject->setAffiliate($inAffiliate);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmBanner by primary key
	 *
	 * @param integer $inID
	 * @return mofilmBanner
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
		$oObject = new mofilmBanner();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmBanner
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_comms').'.banners';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmBanner();
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
	 * Returns an array of all the unique affiliate strings in the banners table
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 * @throws Exception
	 */
	public static function getUniqueAffiliates($inOffset = null, $inLimit = 30) {
		$query = '
			SELECT affiliate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.banners
			 WHERE affiliate != "" AND affiliate IS NOT NULL
			 GROUP BY affiliate
			 ORDER BY affiliate ASC';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$list[] = $row['affiliate'];
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
			SELECT ID, description, url, affiliate
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.banners';

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
		$this->setDescription($inArray['description']);
		$this->setUrl($inArray['url']);
		$this->setAffiliate($inArray['affiliate']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.banners
					( ID, description, url, affiliate)
				VALUES
					(:ID, :Description, :Url, :Affiliate)
				ON DUPLICATE KEY UPDATE
					description=VALUES(description),
					url=VALUES(url),
					affiliate=VALUES(affiliate)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':Url', $this->_Url);
					$oStmt->bindValue(':Affiliate', $this->_Affiliate);

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.banners
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
	 * @return mofilmBanner
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Description = null;
		$this->_Url = '';
		$this->_Affiliate = null;
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
		$string .= " ID[$this->_ID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " Url[$this->_Url] $newLine";
		$string .= " Affiliate[$this->_Affiliate] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmBanner';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Url\" value=\"$this->_Url\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Affiliate\" value=\"$this->_Affiliate\" type=\"string\" /> $newLine";
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
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkUrl($message);
		}
		if ( $valid ) {
			$valid = $this->checkAffiliate($message);
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
	 * Checks that $_Description has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkDescription(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Description) && $this->_Description !== null && $this->_Description !== '' ) {
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
	 * Checks that $_Url has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkUrl(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Url) && $this->_Url !== '' ) {
			$inMessage .= "{$this->_Url} is not a valid value for Url";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Affiliate has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkAffiliate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Affiliate) && $this->_Affiliate !== null && $this->_Affiliate !== '' ) {
			$inMessage .= "{$this->_Affiliate} is not a valid value for Affiliate";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Affiliate) > 30 ) {
			$inMessage .= "Affiliate cannot be more than 30 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Affiliate) <= 1 ) {
			$inMessage .= "Affiliate must be more than 1 character";
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
	 * @return mofilmBanner
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
	 * @return mofilmBanner
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
	 * @return mofilmBanner
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
	 * Return value of $_Url
	 *
	 * @return string
	 * @access public
	 */
	function getUrl() {
		return $this->_Url;
	}

	/**
	 * Set $_Url to Url
	 *
	 * @param string $inUrl
	 * @return mofilmBanner
	 * @access public
	 */
	function setUrl($inUrl) {
		if ( $inUrl !== $this->_Url ) {
			$this->_Url = $inUrl;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Affiliate
	 *
	 * @return string
	 * @access public
	 */
	function getAffiliate() {
		return $this->_Affiliate;
	}

	/**
	 * Set $_Affiliate to Affiliate
	 *
	 * @param string $inAffiliate
	 * @return mofilmBanner
	 * @access public
	 */
	function setAffiliate($inAffiliate) {
		if ( $inAffiliate !== $this->_Affiliate ) {
			$this->_Affiliate = $inAffiliate;
			$this->setModified();
		}
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
	 * @return mofilmBanner
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}