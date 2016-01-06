<?php
/**
 * commsNetworkMapping
 *
 * Stored in commsNetworkMapping.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsNetworkMapping
 * @category commsNetworkMapping
 * @version $Rev: 10 $
 */


/**
 * commsNetworkMapping Class
 *
 * Provides access to records in comms.networkMappings
 *
 * Creating a new record:
 * <code>
 * $oCommsNetworkMapping = new commsNetworkMapping();
 * $oCommsNetworkMapping->setMcc($inMcc);
 * $oCommsNetworkMapping->setMnc($inMnc);
 * $oCommsNetworkMapping->setMvno($inMvno);
 * $oCommsNetworkMapping->setCountryIsoCode($inCountryIsoCode);
 * $oCommsNetworkMapping->setNetworkID($inNetworkID);
 * $oCommsNetworkMapping->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsNetworkMapping = new commsNetworkMapping($inMcc, $inMnc, $inMvno);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsNetworkMapping = new commsNetworkMapping();
 * $oCommsNetworkMapping->setMcc($inMcc);
 * $oCommsNetworkMapping->setMnc($inMnc);
 * $oCommsNetworkMapping->setMvno($inMvno);
 * $oCommsNetworkMapping->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsNetworkMapping = commsNetworkMapping::getInstance($inMcc, $inMnc, $inMvno);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage commsNetworkMapping
 * @category commsNetworkMapping
 */
class commsNetworkMapping implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of commsNetworkMapping
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
	 * Stores $_Mcc
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Mcc;

	/**
	 * Stores $_Mnc
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Mnc;

	/**
	 * Stores $_Mvno
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Mvno;

	/**
	 * Stores $_CountryIsoCode
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_CountryIsoCode;

	/**
	 * Stores $_NetworkID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_NetworkID;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;


	/**
	 * Returns a new instance of commsNetworkMapping
	 *
	 * @param integer $inMcc
	 * @param integer $inMnc
	 * @param integer $inMvno
	 * @return commsNetworkMapping
	 */
	function __construct($inMcc = null, $inMnc = null, $inMvno = null) {
		$this->reset();
		if ( $inMcc !== null && $inMnc !== null && $inMvno !== null ) {
			$this->setMcc($inMcc);
			$this->setMnc($inMnc);
			$this->setMvno($inMvno);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsNetworkMapping containing non-unique properties
	 *
	 * @param string $inCountryIsoCode
	 * @param integer $inNetworkID
	 * @return commsNetworkMapping
	 * @static
	 */
	public static function factory($inCountryIsoCode = null, $inNetworkID = null) {
		$oObject = new commsNetworkMapping;
		if ( $inCountryIsoCode !== null ) {
			$oObject->setCountryIsoCode($inCountryIsoCode);
		}
		if ( $inNetworkID !== null ) {
			$oObject->setNetworkID($inNetworkID);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsNetworkMapping by primary key
	 *
	 * @param integer $inMcc
	 * @param integer $inMnc
	 * @param integer $inMvno
	 * @return commsNetworkMapping
	 * @static
	 */
	public static function getInstance($inMcc, $inMnc, $inMvno) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inMcc.'.'.$inMnc.'.'.$inMvno]) ) {
			return self::$_Instances[$inMcc.'.'.$inMnc.'.'.$inMvno];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsNetworkMapping();
		$oObject->setMcc($inMcc);
		$oObject->setMnc($inMnc);
		$oObject->setMvno($inMvno);
		if ( $oObject->load() ) {
			self::$_Instances[$inMcc.'.'.$inMnc.'.'.$inMvno] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsNetworkMapping
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.networkMappings';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsNetworkMapping();
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
			SELECT mcc, mnc, mvno, countryIsoCode, networkID
			  FROM '.system::getConfig()->getDatabase('comms').'.networkMappings';

		$where = array();
		if ( $this->_Mcc !== 0 ) {
			$where[] = ' mcc = :Mcc ';
		}
		if ( $this->_Mnc !== 0 ) {
			$where[] = ' mnc = :Mnc ';
		}
		if ( $this->_Mvno !== 0 ) {
			$where[] = ' mvno = :Mvno ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_Mcc !== 0 ) {
				$oStmt->bindValue(':Mcc', $this->_Mcc);
			}
			if ( $this->_Mnc !== 0 ) {
				$oStmt->bindValue(':Mnc', $this->_Mnc);
			}
			if ( $this->_Mvno !== 0 ) {
				$oStmt->bindValue(':Mvno', $this->_Mvno);
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
		$this->setMcc((int)$inArray['mcc']);
		$this->setMnc((int)$inArray['mnc']);
		$this->setMvno((int)$inArray['mvno']);
		$this->setCountryIsoCode($inArray['countryIsoCode']);
		$this->setNetworkID((int)$inArray['networkID']);
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
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.networkMappings
					( mcc, mnc, mvno, countryIsoCode, networkID)
				VALUES
					(:Mcc, :Mnc, :Mvno, :CountryIsoCode, :NetworkID)
				ON DUPLICATE KEY UPDATE
					countryIsoCode=VALUES(countryIsoCode),
					networkID=VALUES(networkID)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':Mcc', $this->_Mcc);
					$oStmt->bindValue(':Mnc', $this->_Mnc);
					$oStmt->bindValue(':Mvno', $this->_Mvno);
					$oStmt->bindValue(':CountryIsoCode', $this->_CountryIsoCode);
					$oStmt->bindValue(':NetworkID', $this->_NetworkID);

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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.networkMappings
			WHERE
				mcc = :Mcc AND
				mnc = :Mnc AND
				mvno = :Mvno
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':Mcc', $this->_Mcc);
			$oStmt->bindValue(':Mnc', $this->_Mnc);
			$oStmt->bindValue(':Mvno', $this->_Mvno);

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
	 * @return commsNetworkMapping
	 */
	function reset() {
		$this->_Mcc = 0;
		$this->_Mnc = 0;
		$this->_Mvno = 0;
		$this->_CountryIsoCode = '';
		$this->_NetworkID = 0;
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
		$string .= " Mcc[$this->_Mcc] $newLine";
		$string .= " Mnc[$this->_Mnc] $newLine";
		$string .= " Mvno[$this->_Mvno] $newLine";
		$string .= " CountryIsoCode[$this->_CountryIsoCode] $newLine";
		$string .= " NetworkID[$this->_NetworkID] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsNetworkMapping';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"Mcc\" value=\"$this->_Mcc\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Mnc\" value=\"$this->_Mnc\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Mvno\" value=\"$this->_Mvno\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CountryIsoCode\" value=\"$this->_CountryIsoCode\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"NetworkID\" value=\"$this->_NetworkID\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkMcc($message);
		}
		if ( $valid ) {
			$valid = $this->checkMnc($message);
		}
		if ( $valid ) {
			$valid = $this->checkMvno($message);
		}
		if ( $valid ) {
			$valid = $this->checkCountryIsoCode($message);
		}
		if ( $valid ) {
			$valid = $this->checkNetworkID($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_Mcc has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMcc(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Mcc) && $this->_Mcc !== 0 ) {
			$inMessage .= "{$this->_Mcc} is not a valid value for Mcc";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Mnc has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMnc(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Mnc) && $this->_Mnc !== 0 ) {
			$inMessage .= "{$this->_Mnc} is not a valid value for Mnc";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Mvno has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMvno(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Mvno) && $this->_Mvno !== 0 ) {
			$inMessage .= "{$this->_Mvno} is not a valid value for Mvno";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_CountryIsoCode has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCountryIsoCode(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_CountryIsoCode) && $this->_CountryIsoCode !== '' ) {
			$inMessage .= "{$this->_CountryIsoCode} is not a valid value for CountryIsoCode";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_CountryIsoCode) > 10 ) {
			$inMessage .= "CountryIsoCode cannot be more than 10 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_CountryIsoCode) <= 1 ) {
			$inMessage .= "CountryIsoCode must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
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
	 * @return commsNetworkMapping
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
		return $this->_Mcc.'.'.$this->_Mnc.'.'.$this->_Mvno;
	}

	/**
	 * Return value of $_Mcc
	 *
	 * @return integer
	 * @access public
	 */
	function getMcc() {
		return $this->_Mcc;
	}

	/**
	 * Set $_Mcc to Mcc
	 *
	 * @param integer $inMcc
	 * @return commsNetworkMapping
	 * @access public
	 */
	function setMcc($inMcc) {
		if ( $inMcc !== $this->_Mcc ) {
			$this->_Mcc = $inMcc;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Mnc
	 *
	 * @return integer
	 * @access public
	 */
	function getMnc() {
		return $this->_Mnc;
	}

	/**
	 * Set $_Mnc to Mnc
	 *
	 * @param integer $inMnc
	 * @return commsNetworkMapping
	 * @access public
	 */
	function setMnc($inMnc) {
		if ( $inMnc !== $this->_Mnc ) {
			$this->_Mnc = $inMnc;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Mvno
	 *
	 * @return integer
	 * @access public
	 */
	function getMvno() {
		return $this->_Mvno;
	}

	/**
	 * Set $_Mvno to Mvno
	 *
	 * @param integer $inMvno
	 * @return commsNetworkMapping
	 * @access public
	 */
	function setMvno($inMvno) {
		if ( $inMvno !== $this->_Mvno ) {
			$this->_Mvno = $inMvno;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_CountryIsoCode
	 *
	 * @return string
	 * @access public
	 */
	function getCountryIsoCode() {
		return $this->_CountryIsoCode;
	}

	/**
	 * Set $_CountryIsoCode to CountryIsoCode
	 *
	 * @param string $inCountryIsoCode
	 * @return commsNetworkMapping
	 * @access public
	 */
	function setCountryIsoCode($inCountryIsoCode) {
		if ( $inCountryIsoCode !== $this->_CountryIsoCode ) {
			$this->_CountryIsoCode = $inCountryIsoCode;
			$this->setModified();
		}
		return $this;
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
	 * @return commsNetworkMapping
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
	 * @return commsNetworkMapping
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}