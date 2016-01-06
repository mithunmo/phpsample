<?php
/**
 * commsGatewayAccount
 *
 * Stored in commsGatewayAccount.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage commsGatewayAccount
 * @category commsGatewayAccount
 * @version $Rev: 10 $
 */


/**
 * commsGatewayAccount Class
 *
 * Provides access to records in comms.gatewayAccounts
 *
 * Creating a new record:
 * <code>
 * $oCommsGatewayAccount = new commsGatewayAccount();
 * $oCommsGatewayAccount->setGatewayAccountID($inGatewayAccountID);
 * $oCommsGatewayAccount->setGatewayID($inGatewayID);
 * $oCommsGatewayAccount->setDescription($inDescription);
 * $oCommsGatewayAccount->setPrs($inPrs);
 * $oCommsGatewayAccount->setActive($inActive);
 * $oCommsGatewayAccount->setNetworkID($inNetworkID);
 * $oCommsGatewayAccount->setTariff($inTariff);
 * $oCommsGatewayAccount->setCountryID($inCountryID);
 * $oCommsGatewayAccount->setCurrencyID($inCurrencyID);
 * $oCommsGatewayAccount->setRequireAcknowledgement($inRequireAcknowledgement);
 * $oCommsGatewayAccount->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oCommsGatewayAccount = new commsGatewayAccount($inGatewayAccountID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oCommsGatewayAccount = new commsGatewayAccount();
 * $oCommsGatewayAccount->setGatewayAccountID($inGatewayAccountID);
 * $oCommsGatewayAccount->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oCommsGatewayAccount = commsGatewayAccount::getInstance($inGatewayAccountID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package comms
 * @subpackage commsGatewayAccount
 * @category commsGatewayAccount
 */
class commsGatewayAccount implements systemDaoInterface, systemDaoValidatorInterface {
	
	const GW_ACC_SIMULATOR = 1;
	const GW_ACC_APP_LOOP_BACK = 2;
	const GW_ACC_EMAIL = 3;
	
	/**
	 * Container for static instances of commsGatewayAccount
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
	 * Stores $_GatewayAccountID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GatewayAccountID;

	/**
	 * Stores $_GatewayID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GatewayID;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_Prs
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Prs;

	/**
	 * Stores $_Active
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Active;

	/**
	 * Stores $_NetworkID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_NetworkID;

	/**
	 * Stores $_Tariff
	 *
	 * @var float 
	 * @access protected
	 */
	protected $_Tariff;

	/**
	 * Stores $_CountryID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_CountryID;

	/**
	 * Stores $_CurrencyID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_CurrencyID;

	/**
	 * Stores $_RequireAcknowledgement
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_RequireAcknowledgement;
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
	 * Returns a new instance of commsGatewayAccount
	 *
	 * @param integer $inGatewayAccountID
	 * @return commsGatewayAccount
	 */
	function __construct($inGatewayAccountID = null) {
		$this->reset();
		if ( $inGatewayAccountID !== null ) {
			$this->setGatewayAccountID($inGatewayAccountID);
			$this->load();
		}
		return $this;
	}

	/**
	 * Creates a new commsGatewayAccount containing non-unique properties
	 *
	 * @param integer $inGatewayID
	 * @param string $inDescription
	 * @param string $inPrs
	 * @param integer $inActive
	 * @param integer $inNetworkID
	 * @param float $inTariff
	 * @param integer $inCountryID
	 * @param integer $inCurrencyID
	 * @param integer $inRequireAcknowledgement
	 * @return commsGatewayAccount
	 * @static
	 */
	public static function factory($inGatewayID = null, $inDescription = null, $inPrs = null, $inActive = null, $inNetworkID = null, $inTariff = null, $inCountryID = null, $inCurrencyID = null, $inRequireAcknowledgement = null) {
		$oObject = new commsGatewayAccount;
		if ( $inGatewayID !== null ) {
			$oObject->setGatewayID($inGatewayID);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inPrs !== null ) {
			$oObject->setPrs($inPrs);
		}
		if ( $inActive !== null ) {
			$oObject->setActive($inActive);
		}
		if ( $inNetworkID !== null ) {
			$oObject->setNetworkID($inNetworkID);
		}
		if ( $inTariff !== null ) {
			$oObject->setTariff($inTariff);
		}
		if ( $inCountryID !== null ) {
			$oObject->setCountryID($inCountryID);
		}
		if ( $inCurrencyID !== null ) {
			$oObject->setCurrencyID($inCurrencyID);
		}
		if ( $inRequireAcknowledgement !== null ) {
			$oObject->setRequireAcknowledgement($inRequireAcknowledgement);
		}
		return $oObject;
	}

	/**
	 * Get an instance of commsGatewayAccount by primary key
	 *
	 * @param integer $inGatewayAccountID
	 * @return commsGatewayAccount
	 * @static
	 */
	public static function getInstance($inGatewayAccountID) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inGatewayAccountID]) ) {
			return self::$_Instances[$inGatewayAccountID];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new commsGatewayAccount();
		$oObject->setGatewayAccountID($inGatewayAccountID);
		if ( $oObject->load() ) {
			self::$_Instances[$inGatewayAccountID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Returns an array of objects of commsGatewayAccount
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inCountryID
	 * @param boolean $inActive
	 * @param boolean $inChargeable
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inCountryID = null, $inActive = false, $inChargeable = false) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('comms').'.gatewayAccounts WHERE 1';
		if ( $inCountryID !== null ) {
			$query .= ' AND countryID = '.dbManager::getInstance()->quote($inCountryID);
		}
		if ( $inActive === true ) {
			$query .= ' AND active = 1';
		}
		if ( $inChargeable === true ) {
			$query .= ' AND tariff > 0';
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new commsGatewayAccount();
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
	 * Returns a commsGatewayAccount object representing the most efficient route to bill $inTotalCharge on.
	 *
	 * @param integer $inCountryID
	 * @param float $inTotalCharge
	 * @return commsGatewayAccount
	 * @static 
	 */
	public static function findMostEfficientBillingGatewayForCharge($inCountryID, $inTotalCharge = 0) {
		if ( !$inCountryID ) {
			throw new commsOutboundException(__CLASS__.'::'.__METHOD__.'() unable to bill, no country given');
		}
		if ( $inTotalCharge == 0 ) {
			throw new commsOutboundException(__CLASS__.'::'.__METHOD__.'() unable to bill, no total charge given');
		}
		
		$gateways = commsGatewayAccount::listOfObjects(null, null, $inCountryID, true, true);
		if ( count($gateways) < 1 ) {
			throw new commsOutboundException(__CLASS__.'::'.__METHOD__.'() no gateway accounts found for countryID ('.$inCountryID.')');
		}
		
		if ( false ) $oGatewayAccount = new commsGatewayAccount();
		$return = false;
		foreach ( $gateways as $oGatewayAccount ) {
			if ( ($inTotalCharge / $oGatewayAccount->getTariff()) == (floor($inTotalCharge / $oGatewayAccount->getTariff())) ) {
				systemLog::debug("{$oGatewayAccount->getTariff()} is possible, messages required: ".$inTotalCharge / $oGatewayAccount->getTariff());
				if ( $return === false ) {
					$return = $oGatewayAccount;
				} elseif ( ($inTotalCharge / $oGatewayAccount->getTariff()) < ($inTotalCharge / $return->getTariff()) ) {
					systemLog::debug("More efficient rate found @ {$oGatewayAccount->getTariff()}");
					$return = $oGatewayAccount;
				}
			} elseif ( $oGatewayAccount->getTariff() > 0 ) {
				systemLog::warning("Will charge {$inTotalCharge} using gateway @ {$oGatewayAccount->getTariff()}");
				$return = $oGatewayAccount;
				break;
			}
		}
		return $return;
	}



	/**
	 * Find valid account for current PRS, charge, currencyID and networkID, returns true on success
	 *
	 * @return boolean
	 */
	function find() {
		if ( $this->_Prs && $this->_CurrencyID ) {
			if ( $this->_Tariff > 0.000 ) {
				$sql = "
					SELECT gatewayID, gatewayAccountID, requireAcknowledgement
					  FROM ".system::getConfig()->getDatabase('comms').".gatewayAccounts
					 WHERE active = '1' AND prs = :prs AND tariff = :tariff AND currencyID = :currencyID AND (networkID = :networkID OR networkID = '0')
					 ORDER BY networkID DESC
					 LIMIT 1";
			} else {
				$sql = "
					SELECT gatewayID, gatewayAccountID, requireAcknowledgement
					  FROM ".system::getConfig()->getDatabase('comms').".gatewayAccounts
					 WHERE active = '1' AND prs = :prs AND tariff = '0.00' AND currencyID = :currencyID AND (networkID = :networkID OR networkID = '0')
					 ORDER BY networkID DESC
					 LIMIT 1";
			}
			
			$oStmt = dbManager::getInstance()->prepare($sql);
			$oStmt->bindValue(':prs', $this->_Prs);
			$oStmt->bindValue(':tariff', $this->_Tariff);
			$oStmt->bindValue(':currencyID', $this->_CurrencyID);
			$oStmt->bindValue(':networkID', $this->_NetworkID);
			$oStmt->execute();
			$res = $oStmt->fetchAll();
			
			if ( is_array($res) && count($res) > 0 ) {
				$this->setGatewayID((int)$res[0]['gatewayID']);
				$this->setGatewayAccountID((int)$res[0]['gatewayAccountID']);
				$this->setRequireAcknowledgement((int)$res[0]['requireAcknowledgement']);
				return true;
			}
			
			systemLog::warning(__CLASS__.'::'.__METHOD__."() $this->_Tariff is not valid on $this->_Prs (networkID: $this->_NetworkID), searching for alternative tariff");
			$sql = "
				SELECT gatewayID, gatewayAccountID, tariff, requireAcknowledgement
				  FROM ".system::getConfig()->getDatabase('comms').".gatewayAccounts
				 WHERE active = '1' AND tariff <= :tariff AND currencyID = :currencyID AND (networkID = :networkID OR networkID = '0')
				 ORDER BY tariff DESC, networkID DESC
				 LIMIT 1";
			
			$oStmt = dbManager::getInstance()->prepare($sql);
			$oStmt->bindValue(':tariff', $this->_Tariff);
			$oStmt->bindValue(':currencyID', $this->_CurrencyID);
			$oStmt->bindValue(':networkID', $this->_NetworkID);
			$oStmt->execute();
			$res = $oStmt->fetchAll();
			
			if ( is_array($res) && count($res) > 0 ) {
				systemLog::warning(__CLASS__.'::'.__METHOD__."() found and using alternative tariff at {$res[0]['tariff']}");
				$this->setGatewayID((int)$res[0]['gatewayID']);
				$this->setGatewayAccountID((int)$res[0]['gatewayAccountID']);
				$this->setRequireAcknowledgement((int)$res[0]['requireAcknowledgement']);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = '
			SELECT gatewayAccountID, gatewayID, description, prs, active, networkID, tariff, countryID, currencyID, requireAcknowledgement
			  FROM '.system::getConfig()->getDatabase('comms').'.gatewayAccounts';

		$where = array();
		if ( $this->_GatewayAccountID !== 0 ) {
			$where[] = ' gatewayAccountID = :GatewayAccountID ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_GatewayAccountID !== 0 ) {
				$oStmt->bindValue(':GatewayAccountID', $this->_GatewayAccountID);
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
		$this->setGatewayAccountID((int)$inArray['gatewayAccountID']);
		$this->setGatewayID((int)$inArray['gatewayID']);
		$this->setDescription($inArray['description']);
		$this->setPrs($inArray['prs']);
		$this->setActive((int)$inArray['active']);
		$this->setNetworkID((int)$inArray['networkID']);
		$this->setTariff($inArray['tariff']);
		$this->setCountryID((int)$inArray['countryID']);
		$this->setCurrencyID((int)$inArray['currencyID']);
		$this->setRequireAcknowledgement((int)$inArray['requireAcknowledgement']);
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
				INSERT INTO '.system::getConfig()->getDatabase('comms').'.gatewayAccounts
					( gatewayAccountID, gatewayID, description, prs, active, networkID, tariff, countryID, currencyID, requireAcknowledgement)
				VALUES
					(:GatewayAccountID, :GatewayID, :Description, :Prs, :Active, :NetworkID, :Tariff, :CountryID, :CurrencyID, :RequireAcknowledgement)
				ON DUPLICATE KEY UPDATE
					gatewayID=VALUES(gatewayID),
					description=VALUES(description),
					prs=VALUES(prs),
					active=VALUES(active),
					networkID=VALUES(networkID),
					tariff=VALUES(tariff),
					countryID=VALUES(countryID),
					currencyID=VALUES(currencyID),
					requireAcknowledgement=VALUES(requireAcknowledgement)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':GatewayAccountID', $this->_GatewayAccountID);
					$oStmt->bindValue(':GatewayID', $this->_GatewayID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':Prs', $this->_Prs);
					$oStmt->bindValue(':Active', $this->_Active);
					$oStmt->bindValue(':NetworkID', $this->_NetworkID);
					$oStmt->bindValue(':Tariff', $this->_Tariff);
					$oStmt->bindValue(':CountryID', $this->_CountryID);
					$oStmt->bindValue(':CurrencyID', $this->_CurrencyID);
					$oStmt->bindValue(':RequireAcknowledgement', $this->_RequireAcknowledgement);

					if ( $oStmt->execute() ) {
						if ( !$this->getGatewayAccountID() ) {
							$this->setGatewayAccountID($oDB->lastInsertId());
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
				$this->_ParamSet->setIndexID($this->getGatewayAccountID());
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
			DELETE FROM '.system::getConfig()->getDatabase('comms').'.gatewayAccounts
			WHERE
				gatewayAccountID = :GatewayAccountID
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':GatewayAccountID', $this->_GatewayAccountID);

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
	 * @return commsGatewayAccount
	 */
	function reset() {
		$this->_GatewayAccountID = 0;
		$this->_GatewayID = 0;
		$this->_Description = '';
		$this->_Prs = null;
		$this->_Active = 0;
		$this->_NetworkID = 0;
		$this->_Tariff = 0;
		$this->_CountryID = 0;
		$this->_CurrencyID = 0;
		$this->_RequireAcknowledgement = 0;
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
		$string .= " GatewayAccountID[$this->_GatewayAccountID] $newLine";
		$string .= " GatewayID[$this->_GatewayID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " Prs[$this->_Prs] $newLine";
		$string .= " Active[$this->_Active] $newLine";
		$string .= " NetworkID[$this->_NetworkID] $newLine";
		$string .= " Tariff[$this->_Tariff] $newLine";
		$string .= " CountryID[$this->_CountryID] $newLine";
		$string .= " CurrencyID[$this->_CurrencyID] $newLine";
		$string .= " RequireAcknowledgement[$this->_RequireAcknowledgement] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'commsGatewayAccount';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"GatewayAccountID\" value=\"$this->_GatewayAccountID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"GatewayID\" value=\"$this->_GatewayID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Prs\" value=\"$this->_Prs\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Active\" value=\"$this->_Active\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"NetworkID\" value=\"$this->_NetworkID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Tariff\" value=\"$this->_Tariff\" type=\"float\" /> $newLine";
		$xml .= "\t<property name=\"CountryID\" value=\"$this->_CountryID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"CurrencyID\" value=\"$this->_CurrencyID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"RequireAcknowledgement\" value=\"$this->_RequireAcknowledgement\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkGatewayAccountID($message);
		}
		if ( $valid ) {
			$valid = $this->checkGatewayID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkPrs($message);
		}
		if ( $valid ) {
			$valid = $this->checkActive($message);
		}
		if ( $valid ) {
			$valid = $this->checkNetworkID($message);
		}
		if ( $valid ) {
			$valid = $this->checkTariff($message);
		}
		if ( $valid ) {
			$valid = $this->checkCountryID($message);
		}
		if ( $valid ) {
			$valid = $this->checkCurrencyID($message);
		}
		if ( $valid ) {
			$valid = $this->checkRequireAcknowledgement($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_GatewayAccountID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkGatewayAccountID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_GatewayAccountID) && $this->_GatewayAccountID !== 0 ) {
			$inMessage .= "{$this->_GatewayAccountID} is not a valid value for GatewayAccountID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_GatewayID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkGatewayID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_GatewayID) && $this->_GatewayID < 1 ) {
			$inMessage .= "{$this->_GatewayID} is not a valid value for GatewayID";
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
		if ( $isValid && strlen($this->_Description) > 255 ) {
			$inMessage .= "Description cannot be more than 255 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Prs has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkPrs(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Prs) && $this->_Prs !== null && $this->_Prs !== '' ) {
			$inMessage .= "{$this->_Prs} is not a valid value for Prs";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Prs) > 20 ) {
			$inMessage .= "Prs cannot be more than 20 characters";
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
	 * Checks that $_Tariff has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkTariff(&$inMessage = '') {
		$isValid = true;
		if ( !is_float($this->_Tariff) && $this->_Tariff != 0.000 ) {
			$inMessage .= "{$this->_Tariff} is not a valid value for Tariff";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_CountryID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCountryID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_CountryID) && $this->_CountryID !== 0 ) {
			$inMessage .= "{$this->_CountryID} is not a valid value for CountryID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_CurrencyID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkCurrencyID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_CurrencyID) && $this->_CurrencyID !== 0 ) {
			$inMessage .= "{$this->_CurrencyID} is not a valid value for CurrencyID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_RequireAcknowledgement has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkRequireAcknowledgement(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_RequireAcknowledgement) && $this->_RequireAcknowledgement !== 0 ) {
			$inMessage .= "{$this->_RequireAcknowledgement} is not a valid value for RequireAcknowledgement";
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
	 * @return commsGatewayAccount
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
		return $this->_GatewayAccountID;
	}

	/**
	 * Return value of $_GatewayAccountID
	 *
	 * @return integer
	 * @access public
	 */
	function getGatewayAccountID() {
		return $this->_GatewayAccountID;
	}

	/**
	 * Set $_GatewayAccountID to GatewayAccountID
	 *
	 * @param integer $inGatewayAccountID
	 * @return commsGatewayAccount
	 * @access public
	 */
	function setGatewayAccountID($inGatewayAccountID) {
		if ( $inGatewayAccountID !== $this->_GatewayAccountID ) {
			$this->_GatewayAccountID = $inGatewayAccountID;
			$this->setModified();
		}
		return $this;
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
	 * @return commsGatewayAccount
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
	 * @return commsGatewayAccount
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
	 * Return value of $_Prs
	 *
	 * @return string
	 * @access public
	 */
	function getPrs() {
		return $this->_Prs;
	}

	/**
	 * Set $_Prs to Prs
	 *
	 * @param string $inPrs
	 * @return commsGatewayAccount
	 * @access public
	 */
	function setPrs($inPrs) {
		if ( $inPrs !== $this->_Prs ) {
			$this->_Prs = $inPrs;
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
	 * @return commsGatewayAccount
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
	 * Return value of $_NetworkID
	 *
	 * @return integer
	 * @access public
	 */
	function getNetworkID() {
		return $this->_NetworkID;
	}
	
	/**
	 * Returns the commsNetwork object mapped to the account
	 * 
	 * @return commsNetwork
	 */
	function getNetwork() {
		return commsNetwork::getInstance($this->getNetworkID());
	}

	/**
	 * Set $_NetworkID to NetworkID
	 *
	 * @param integer $inNetworkID
	 * @return commsGatewayAccount
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
	 * Return value of $_Tariff
	 *
	 * @return float
	 * @access public
	 */
	function getTariff() {
		return $this->_Tariff;
	}

	/**
	 * Set $_Tariff to Tariff
	 *
	 * @param float $inTariff
	 * @return commsGatewayAccount
	 * @access public
	 */
	function setTariff($inTariff) {
		if ( $inTariff !== $this->_Tariff ) {
			$this->_Tariff = $inTariff;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_CountryID
	 *
	 * @return integer
	 * @access public
	 */
	function getCountryID() {
		return $this->_CountryID;
	}

	/**
	 * Set $_CountryID to CountryID
	 *
	 * @param integer $inCountryID
	 * @return commsGatewayAccount
	 * @access public
	 */
	function setCountryID($inCountryID) {
		if ( $inCountryID !== $this->_CountryID ) {
			$this->_CountryID = $inCountryID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_CurrencyID
	 *
	 * @return integer
	 * @access public
	 */
	function getCurrencyID() {
		return $this->_CurrencyID;
	}

	/**
	 * Set $_CurrencyID to CurrencyID
	 *
	 * @param integer $inCurrencyID
	 * @return commsGatewayAccount
	 * @access public
	 */
	function setCurrencyID($inCurrencyID) {
		if ( $inCurrencyID !== $this->_CurrencyID ) {
			$this->_CurrencyID = $inCurrencyID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_RequireAcknowledgement
	 *
	 * @return integer
	 * @access public
	 */
	function getRequireAcknowledgement() {
		return $this->_RequireAcknowledgement;
	}

	/**
	 * Set $_RequireAcknowledgement to RequireAcknowledgement
	 *
	 * @param integer $inRequireAcknowledgement
	 * @return commsGatewayAccount
	 * @access public
	 */
	function setRequireAcknowledgement($inRequireAcknowledgement) {
		if ( $inRequireAcknowledgement !== $this->_RequireAcknowledgement ) {
			$this->_RequireAcknowledgement = $inRequireAcknowledgement;
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
				system::getConfig()->getDatabase('comms'), 'gatewayAccountParams', 'gatewayAccountID', 'paramName', 'paramValue', $this->getGatewayAccountID(), false
			);
			if ( $this->getGatewayAccountID() > 0 ) {
				$this->_ParamSet->load();
			}
		}
		return $this->_ParamSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param baseTableParamSet $inObject
	 * @return commsGatewayAccount
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
	 * @return commsGatewayAccount
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}