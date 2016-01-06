<?php
/**
 * mofilmBannerClick
 *
 * Stored in mofilmBannerClick.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmBannerClick
 * @category mofilmBannerClick
 * @version $Rev: 10 $
 */


/**
 * mofilmBannerClick Class
 *
 * Provides access to records in mofilm_comms.bannerClicks
 *
 * Creating a new record:
 * <code>
 * $oMofilmBannerClick = new mofilmBannerClick();
 * $oMofilmBannerClick->setID($inID);
 * $oMofilmBannerClick->setBannerID($inBannerID);
 * $oMofilmBannerClick->setTimestamp($inTimestamp);
 * $oMofilmBannerClick->setIp($inIp);
 * $oMofilmBannerClick->setCountry($inCountry);
 * $oMofilmBannerClick->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmBannerClick = new mofilmBannerClick($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmBannerClick = new mofilmBannerClick();
 * $oMofilmBannerClick->setID($inID);
 * $oMofilmBannerClick->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmBannerClick = mofilmBannerClick::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmBannerClick
 * @category mofilmBannerClick
 */
class mofilmBannerClick implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmBannerClick
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
	 * Stores $_BannerID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_BannerID;

	/**
	 * Stores $_Timestamp
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_Timestamp;

	/**
	 * Stores $_Ip
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Ip;

	/**
	 * Stores $_Country
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Country;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmBannerClick
	 *
	 * @param integer $inID
	 * @return mofilmBannerClick
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
	 * Creates a new mofilmBannerClick containing non-unique properties
	 *
	 * @param integer $inBannerID
	 * @param datetime $inTimestamp
	 * @param string $inIp
	 * @param string $inCountry
	 * @return mofilmBannerClick
	 * @static
	 */
	public static function factory($inBannerID = null, $inTimestamp = null, $inIp = null, $inCountry = null) {
		$oObject = new mofilmBannerClick;
		if ( $inBannerID !== null ) {
			$oObject->setBannerID($inBannerID);
		}
		if ( $inTimestamp !== null ) {
			$oObject->setTimestamp($inTimestamp);
		}
		if ( $inIp !== null ) {
			$oObject->setIp($inIp);
		}
		if ( $inCountry !== null ) {
			$oObject->setCountry($inCountry);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmBannerClick by primary key
	 *
	 * @param integer $inID
	 * @return mofilmBannerClick
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
		$oObject = new mofilmBannerClick();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmBannerClick
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_comms').'.bannerClicks';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmBannerClick();
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
			SELECT ID, bannerID, timestamp, ip, country
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.bannerClicks';

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
		$this->setBannerID((int)$inArray['bannerID']);
		$this->setTimestamp($inArray['timestamp']);
		$this->setIp($inArray['ip']);
		$this->setCountry($inArray['country']);
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
			$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.bannerClicks
					( ID, bannerID, timestamp, ip, country)
				VALUES
					(:ID, :BannerID, :Timestamp, :Ip, :Country)
				ON DUPLICATE KEY UPDATE
					bannerID=VALUES(bannerID),
					timestamp=VALUES(timestamp),
					ip=VALUES(ip),
					country=VALUES(country)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':BannerID', $this->_BannerID);
					$oStmt->bindValue(':Timestamp', $this->_Timestamp);
					$oStmt->bindValue(':Ip', $this->_Ip);
					$oStmt->bindValue(':Country', $this->_Country);

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.bannerClicks
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
	 * @return mofilmBannerClick
	 */
	function reset() {
		$this->_ID = 0;
		$this->_BannerID = 0;
		$this->_Timestamp = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Ip = null;
		$this->_Country = null;
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
		$string .= " BannerID[$this->_BannerID] $newLine";
		$string .= " Timestamp[$this->_Timestamp] $newLine";
		$string .= " Ip[$this->_Ip] $newLine";
		$string .= " Country[$this->_Country] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmBannerClick';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"BannerID\" value=\"$this->_BannerID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Timestamp\" value=\"$this->_Timestamp\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Ip\" value=\"$this->_Ip\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Country\" value=\"$this->_Country\" type=\"string\" /> $newLine";
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
			$valid = $this->checkBannerID($message);
		}
		if ( $valid ) {
			$valid = $this->checkTimestamp($message);
		}
		if ( $valid ) {
			$valid = $this->checkIp($message);
		}
		if ( $valid ) {
			$valid = $this->checkCountry($message);
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
	 * Checks that $_BannerID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkBannerID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_BannerID) && $this->_BannerID !== 0 ) {
			$inMessage .= "{$this->_BannerID} is not a valid value for BannerID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Timestamp has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkTimestamp(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Timestamp) && $this->_Timestamp !== '' ) {
			$inMessage .= "{$this->_Timestamp} is not a valid value for Timestamp";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Ip has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkIp(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Ip) && $this->_Ip !== null && $this->_Ip !== '' ) {
			$inMessage .= "{$this->_Ip} is not a valid value for Ip";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Ip) > 15 ) {
			$inMessage .= "Ip cannot be more than 15 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Ip) <= 1 ) {
			$inMessage .= "Ip must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Country has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCountry(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Country) && $this->_Country !== null && $this->_Country !== '' ) {
			$inMessage .= "{$this->_Country} is not a valid value for Country";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Country) > 2 ) {
			$inMessage .= "Country cannot be more than 2 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Country) <= 1 ) {
			$inMessage .= "Country must be more than 1 character";
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
	 * @return mofilmBannerClick
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
	 * @return mofilmBannerClick
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
	 * Return value of $_BannerID
	 *
	 * @return integer
	 * @access public
	 */
	function getBannerID() {
		return $this->_BannerID;
	}

	/**
	 * Set $_BannerID to BannerID
	 *
	 * @param integer $inBannerID
	 * @return mofilmBannerClick
	 * @access public
	 */
	function setBannerID($inBannerID) {
		if ( $inBannerID !== $this->_BannerID ) {
			$this->_BannerID = $inBannerID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Timestamp
	 *
	 * @return datetime
	 * @access public
	 */
	function getTimestamp() {
		return $this->_Timestamp;
	}

	/**
	 * Set $_Timestamp to Timestamp
	 *
	 * @param datetime $inTimestamp
	 * @return mofilmBannerClick
	 * @access public
	 */
	function setTimestamp($inTimestamp) {
		if ( $inTimestamp !== $this->_Timestamp ) {
			$this->_Timestamp = $inTimestamp;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Ip
	 *
	 * @return string
	 * @access public
	 */
	function getIp() {
		return $this->_Ip;
	}

	/**
	 * Set $_Ip to Ip
	 *
	 * @param string $inIp
	 * @return mofilmBannerClick
	 * @access public
	 */
	function setIp($inIp) {
		if ( $inIp !== $this->_Ip ) {
			$this->_Ip = $inIp;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Country
	 *
	 * @return string
	 * @access public
	 */
	function getCountry() {
		return $this->_Country;
	}

	/**
	 * Set $_Country to Country
	 *
	 * @param string $inCountry
	 * @return mofilmBannerClick
	 * @access public
	 */
	function setCountry($inCountry) {
		if ( $inCountry !== $this->_Country ) {
			$this->_Country = $inCountry;
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
	 * @return mofilmBannerClick
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}