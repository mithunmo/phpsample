<?php
/**
 * mofilmEventBase
 * 
 * Stored in mofilmEventBase.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmEventBase
 * @category mofilmEventBase
 * @version $Rev: 10 $
 */


/**
 * mofilmEventBase Class
 * 
 * Provides access to records in mofilm_content.events
 * 
 * Creating a new record:
 * <code>
 * $oMofilmEvent = new mofilmEventBase();
 * $oMofilmEvent->setID($inID);
 * $oMofilmEvent->setName($inName);
 * $oMofilmEvent->setWebpath($inWebpath);
 * $oMofilmEvent->setHidden($inHidden);
 * $oMofilmEvent->setStartDate($inStartdate);
 * $oMofilmEvent->setEndDate($inEnddate);
 * $oMofilmEvent->setAwardStartdate($inAwardStartdate);
 * $oMofilmEvent->setAwardEnddate($inAwardEnddate);
 * $oMofilmEvent->setTermsID($inTermsID);
 * $oMofilmEvent->setInstructions($inInstructions);
 * $oMofilmEvent->setBgcolor($inBgcolor);
 * $oMofilmEvent->setCustom($inCustom);
 * $oMofilmEvent->setTba($inTba);
 * $oMofilmEvent->setStatus($inStatus);
 * $oMofilmEvent->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmEvent = new mofilmEventBase($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmEvent = new mofilmEventBase();
 * $oMofilmEvent->setID($inID);
 * $oMofilmEvent->load();
 * </code>
 * 
 * @package mofilm
 * @subpackage mofilmEventBase
 * @category mofilmEventBase
 */
class mofilmEventBase implements systemDaoInterface, systemDaoValidatorInterface {
	
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
	 * Stores $_Name
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Name;
			
	/**
	 * Stores $_Webpath
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Webpath;
	
	/**
	 * Stores $_Hidden
	 * 
	 * @var string (YES_Y,NO_N,)
	 * @access protected
	 */
	protected $_Hidden;
	
	CONST YES_Y = 'Y';
	CONST NO_N = 'N';
        CONST INVITE_I = 'I';		
	/**
	 * Stores $_Startdate
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_Startdate;
			
	/**
	 * Stores $_Enddate
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_Enddate;
	
	/**
	 * Stores $_AwardStartdate
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_AwardStartdate;
			
	/**
	 * Stores $_AwardEnddate
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_AwardEnddate;
			
	/**
	 * Stores $_TermsID
	 * 
	 * @var integer
	 * @access protected
	 */
	protected $_TermsID;
			
	/**
	 * Stores $_Instructions
	 * 
	 * @var string
	 * @access protected
	 */
	protected $_Instructions;

	/**
	 * Stores $_Bgcolor
	 * 
	 * @var string
	 * @access protected
	 */
	protected $_Bgcolor;

	/**
	 * Stores $_Tba
	 * 
	 * @var string (YES_Y, NO_N,)
	 * @access protected
	 */
	protected $_Tba;
	
	/**
	 * Stores $_Custom
	 * 
	 * @var string (YES_Y, NO_N,)
	 * @access protected
	 */
	protected $_Custom;

	/**
	 * Stores $_Product
	 * 
	 * @var string (YES_Y, NO_N,)
	 * @access protected
	 */
	protected $_ProductID;        
        
	/**
	 * Stores $_Status
	 * 
	 * @var string
	 * @access protected
	 */
	protected $_Status;
	
	CONST STATUS_DRAFT = 'DRAFT';
	CONST STATUS_CLOSED_COMPLETED = 'CLOSED - COMPLETED';
	CONST STATUS_PUBLISHED = 'PUBLISHED';
	CONST STATUS_JUDGING = 'JUDGING';
	CONST STATUS_WINNERS = 'WINNERS';
    CONST STATUS_CLOSED_CANCELED= 'CLOSED - CANCELED';
	
	
	/**
	 * Returns a new instance of mofilmEventBase
	 * 
	 * @param integer $inID
	 * @return mofilmEventBase
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
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.events';
		
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
		$this->setName($inArray['name']);
		$this->setWebpath($inArray['webpath']);
		$this->setHidden($inArray['hidden']);
		$this->setStartDate($inArray['startdate']);
		$this->setEndDate($inArray['enddate']);
		$this->setAwardStartDate($inArray['awardstartdate']);
		$this->setAwardEndDate($inArray['awardenddate']);
		$this->setTermsID((int)$inArray['termsID']);
		$this->setInstructions($inArray['instructions']);
		$this->setBgcolor($inArray['bgcolor']);
		$this->setCustom($inArray['custom']);
		$this->setTba($inArray['tba']);
		$this->setStatus($inArray['status']);
                $this->setProductID($inArray["productID"]);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.events
					( ID, name, webpath, hidden, startdate, enddate, awardstartdate, awardenddate, termsID, instructions, bgcolor, tba, custom, status, productID)
				VALUES 
					(:ID, :Name, :Webpath, :Hidden, :Startdate, :Enddate, :AwardStartdate, :AwardEnddate, :TermsID, :Instructions, :Bgcolor, :Tba, :Custom, :Status, :ProductID)
				ON DUPLICATE KEY UPDATE
					name=VALUES(name),
					webpath=VALUES(webpath),
					hidden=VALUES(hidden),
					startdate=VALUES(startdate),
					enddate=VALUES(enddate),
					awardstartdate=VALUES(awardstartdate),
					awardenddate=VALUES(awardenddate),
					termsID=VALUES(termsID),
					instructions=VALUES(instructions),
					bgcolor=VALUES(bgcolor),
					custom=VALUES(custom),
					tba=VALUES(tba),
                                        productID=VALUES(productID),
					status=VALUES(status)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Name', $this->_Name);
					$oStmt->bindValue(':Webpath', $this->_Webpath);
					$oStmt->bindValue(':Hidden', $this->_Hidden);
					$oStmt->bindValue(':Startdate', $this->_Startdate);
					$oStmt->bindValue(':Enddate', $this->_Enddate);
					$oStmt->bindValue(':AwardStartdate', $this->_AwardStartdate);
					$oStmt->bindValue(':AwardEnddate', $this->_AwardEnddate);
					$oStmt->bindValue(':TermsID', $this->_TermsID);
					$oStmt->bindValue(':Instructions', $this->_Instructions);
					$oStmt->bindValue(':Bgcolor', $this->_Bgcolor);
					$oStmt->bindValue(':Custom', $this->_Custom);
					$oStmt->bindValue(':Tba', $this->_Tba);
					$oStmt->bindValue(':Status', $this->_Status);
                                        $oStmt->bindValue(':ProductID', $this->_ProductID);

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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.events
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
	 * @return mofilmEventBase
	 */
	function reset() {
		$this->_ID = 0;
		$this->_Name = '';
		$this->_Webpath = null;
		$this->_Hidden = self::NO_N;
		$this->_Startdate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Enddate = date(system::getConfig()->getDatabaseEndDatetimeFormat()->getParamValue());
		$this->_AwardStartdate = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_AwardEnddate = date(system::getConfig()->getDatabaseEndDatetimeFormat()->getParamValue());
		$this->_TermsID = null;
		$this->_Instructions = null;
		$this->_Bgcolor = null;
		$this->_Custom = self::NO_N;
                $this->_ProductID = 0;
		$this->_Tba = self::YES_Y;
		$this->_Status = null;
		$this->setModified(false);
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
		$string .= " Name[$this->_Name] $newLine";
		$string .= " Webpath[$this->_Webpath] $newLine";
		$string .= " Hidden[$this->_Hidden] $newLine";
		$string .= " Startdate[$this->_Startdate] $newLine";
		$string .= " Enddate[$this->_Enddate] $newLine";
		$string .= " AwardStartdate[$this->_AwardStartdate] $newLine";
		$string .= " AwardEnddate[$this->_AwardEnddate] $newLine";
		$string .= " TermsID[$this->_TermsID] $newLine";
		$string .= " Instructions[$this->_Instructions] $newLine";
		$string .= " Bgcolor[$this->_Bgcolor] $newLine";
		$string .= " Custom[$this->_Custom] $newLine";
		$string .= " Tba[$this->_Tba] $newLine";
		$string .= " Status[$this->_Status] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmEventBase';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Name\" value=\"$this->_Name\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Webpath\" value=\"$this->_Webpath\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Hidden\" value=\"$this->_Hidden\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Startdate\" value=\"$this->_Startdate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Enddate\" value=\"$this->_Enddate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"AwardStartdate\" value=\"$this->_AwardStartdate\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"AwardEnddate\" value=\"$this->_AwardEnddate\" type=\"datetime\" /> $newLine";		
		$xml .= "\t<property name=\"TermsID\" value=\"$this->_TermsID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Instructions\" value=\"$this->_Instructions\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Bgcolor\" value=\"$this->_Bgcolor\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Custom\" value=\"$this->_Custom\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Tba\" value=\"$this->_Tba\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Status\" value=\"$this->_Status\" type=\"string\" /> $newLine";
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
			$valid = $this->checkName($message);
		}
		if ( $valid ) {
			$valid = $this->checkWebpath($message);
		}
		if ( $valid ) {
			$valid = $this->checkHidden($message);
		}
		if ( $valid ) {
			$valid = $this->checkStartdate($message);
		}
		if ( $valid ) {
			$valid = $this->checkEnddate($message);
		}
		if ( $valid ) {
			$valid = $this->checkAwardStartdate($message);
		}
		if ( $valid ) {
			$valid = $this->checkAwardEnddate($message);
		}
		if ( $valid ) {
			$valid = $this->checkTermsID($message);
		}
		if ( $valid ) {
			$valid = $this->checkInstructions($message);
		}
		if ( $valid ) {
			$valid = $this->checkCustom($message);
		}
		if ( $valid ) {
			$valid = $this->checkTba($message);
		}
		if ( $valid ) {
			$valid = $this->checkStatus($message);
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
	 * Checks that $_Name has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkName(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Name) && $this->_Name !== '' ) {
			$inMessage .= "{$this->_Name} is not a valid value for Name";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_Name) > 40 ) {
			$inMessage .= "Name cannot be more than 40 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Name) <= 1 ) {
			$inMessage .= "Name must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Webpath has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkWebpath(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Webpath) && $this->_Webpath !== null && $this->_Webpath !== '' ) {
			$inMessage .= "{$this->_Webpath} is not a valid value for Webpath";
			$isValid = false;
		}		
				
		return $isValid;
	}
	
	/**
	 * Checks that $_Hidden has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkHidden(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Hidden) && $this->_Hidden !== '' ) {
			$inMessage .= "{$this->_Hidden} is not a valid value for Hidden";
			$isValid = false;
		}		
		if ( $isValid && $this->_Hidden != '' && !in_array($this->_Hidden, array(self::YES_Y, self::NO_N,self::INVITE_I)) ) {
			$inMessage .= "Hidden must be one of YES_Y, NO_N, INVITE_I";
			$isValid = false;
		}		
		return $isValid;
	}
		
	/**
	 * Checks that $_Startdate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkStartdate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Startdate) && $this->_Startdate !== '' ) {
			$inMessage .= "{$this->_Startdate} is not a valid value for Startdate";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Enddate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkEnddate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Enddate) && $this->_Enddate !== '' ) {
			$inMessage .= "{$this->_Enddate} is not a valid value for Enddate";
			$isValid = false;
		}
		return $isValid;
	}
	
	/**
	 * Checks that $_AwardStartdate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkAwardStartdate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_AwardStartdate) && $this->_AwardStartdate !== '' ) {
			$inMessage .= "{$this->_AwardStartdate} is not a valid value for AwardStartdate";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_AwardEnddate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkAwardEnddate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_AwardEnddate) && $this->_AwardEnddate !== '' ) {
			$inMessage .= "{$this->_AwardEnddate} is not a valid value for AwardEnddate";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_TermsID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkTermsID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_TermsID) && $this->_TermsID !== null && $this->_TermsID !== 0 ) {
			$inMessage .= "{$this->_TermsID} is not a valid value for TermsID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Instructions has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkInstructions(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Instructions) && $this->_Instructions !== null && $this->_Instructions !== '' ) {
			$inMessage .= "{$this->_Instructions} is not a valid value for Instructions";
			$isValid = false;
		}		
				
		return $isValid;
	}

	/**
	 * Checks that $_Custom has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkCustom(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Custom) && $this->_Custom !== '' ) {
			$inMessage .= "{$this->_Custom} is not a valid value for Custom";
			$isValid = false;
		}		
		if ( $isValid && $this->_Custom != '' && !in_array($this->_Custom, array(self::YES_Y, self::NO_N)) ) {
			$inMessage .= "Custom must be one of YES_Y, NO_N";
			$isValid = false;
		}		
		return $isValid;
	}
	
	/**
	 * Checks that $_Tba has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkTba(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Tba) && $this->_Tba !== '' ) {
			$inMessage .= "{$this->_Tba} is not a valid value for Tba";
			$isValid = false;
		}		
		if ( $isValid && $this->_Tba != '' && !in_array($this->_Tba, array(self::YES_Y, self::NO_N)) ) {
			$inMessage .= "Tba must be one of YES_Y, NO_N";
			$isValid = false;
		}		
		return $isValid;
	}
	
	/**
	 * Checks that $_Status has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkStatus(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Status) && $this->_Status !== null && $this->_Status !== '' ) {
			$inMessage .= "{$this->_Status} is not a valid value for Status";
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
	 * @return mofilmEventBase
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
	 * @return mofilmEventBase
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
	 * Return value of $_Name
	 * 
	 * @return string
	 * @access public
	 */
	function getName() {
		return $this->_Name;
	}
	
	/**
	 * Set $_Name to Name
	 * 
	 * @param string $inName
	 * @return mofilmEventBase
	 * @access public
	 */
	function setName($inName) {
		if ( $inName !== $this->_Name ) {
			$this->_Name = $inName;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Webpath
	 * 
	 * @return string
	 * @access public
	 */
	function getWebpath() {
		return $this->_Webpath;
	}
	
	/**
	 * Set $_Webpath to Webpath
	 * 
	 * @param string $inWebpath
	 * @return mofilmEventBase
	 * @access public
	 */
	function setWebpath($inWebpath) {
		if ( $inWebpath !== $this->_Webpath ) {
			$this->_Webpath = $inWebpath;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns true if the event is hidden
	 * 
	 * @return boolean
	 */
	function isHidden() {
		return $this->getHidden() == self::YES_Y;
	}
	
	/**
	 * Return value of $_Hidden
	 * 
	 * @return string
	 * @access public
	 */
	function getHidden() {
		return $this->_Hidden;
	}
	
	/**
	 * Set $_Hidden to Hidden
	 * 
	 * @param string $inHidden
	 * @return mofilmEventBase
	 * @access public
	 */
	function setHidden($inHidden) {
		if ( $inHidden !== $this->_Hidden ) {
			$this->_Hidden = $inHidden;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Startdate
	 * 
	 * @return datetime
	 * @access public
	 */
	function getStartDate() {
		return $this->_Startdate;
	}
	
	/**
	 * Set $_Startdate to Startdate
	 * 
	 * @param datetime $inStartdate
	 * @return mofilmEventBase
	 * @access public
	 */
	function setStartDate($inStartdate) {
		if ( $inStartdate !== $this->_Startdate ) {
			$this->_Startdate = $inStartdate;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Enddate
	 * 
	 * @return datetime
	 * @access public
	 */
	function getEndDate() {
 		return $this->_Enddate;
	}
	
	/**
	 * Set $_Enddate to Enddate
	 * 
	 * @param datetime $inEnddate
	 * @return mofilmEventBase
	 * @access public
	 */
	function setEndDate($inEnddate) {
		if ( $inEnddate !== $this->_Enddate ) {
			$this->_Enddate = $inEnddate;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_AwardStartdate
	 * 
	 * @return datetime
	 * @access public
	 */
	function getAwardStartDate() {
		return $this->_AwardStartdate;
	}
	
	/**
	 * Set $_AwardStartdate to AwardStartdate
	 * 
	 * @param datetime $inAwardStartdate
	 * @return mofilmEventBase
	 * @access public
	 */
	function setAwardStartDate($inAwardStartdate) {
		if ( $inAwardStartdate !== $this->_AwardStartdate ) {
			$this->_AwardStartdate = $inAwardStartdate;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_AwardEnddate
	 * 
	 * @return datetime
	 * @access public
	 */
	function getAwardEndDate() {
		return $this->_AwardEnddate;
	}
	
	/**
	 * Set $_AwardEnddate to AwardEnddate
	 * 
	 * @param datetime $inAwardEnddate
	 * @return mofilmEventBase
	 * @access public
	 */
	function setAwardEndDate($inAwardEnddate) {
		if ( $inAwardEnddate !== $this->_AwardEnddate ) {
			$this->_AwardEnddate = $inAwardEnddate;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_TermsID
	 * 
	 * @return integer
	 * @access public
	 */
	function getTermsID() {
		return $this->_TermsID;
	}
	
	/**
	 * Returns the mofilmTerms object
	 * 
	 * @return mofilmTerm
	 */
	function getTerms() {
		return mofilmTerms::getInstance($this->getTermsID());
	}
	
	/**
	 * Set $_TermsID to TermsID
	 * 
	 * @param integer $inTermsID
	 * @return mofilmEventBase
	 * @access public
	 */
	function setTermsID($inTermsID) {
		if ( $inTermsID !== $this->_TermsID ) {
			$this->_TermsID = $inTermsID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Instructions
	 * 
	 * @return string
	 * @access public
	 */
	function getInstructions() {
		return $this->_Instructions;
	}
	
	/**
	 * Set $_Instructions to Instructions
	 * 
	 * @param string $inInstructions
	 * @return mofilmEventBase
	 * @access public
	 */
	function setInstructions($inInstructions) {
		if ( $inInstructions !== $this->_Instructions ) {
			$this->_Instructions = $inInstructions;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Bgcolor
	 * 
	 * @return string
	 * @access public
	 */
	function getBgcolor() {
		return $this->_Bgcolor;
	}

	/**
	 * Set $_Bgcolor to Bgcolor
	 * 
	 * @param string $inBgcolor
	 * @return mofilmEventBase
	 * @access public
	 */
	function setBgcolor($inBgcolor) {
		if ( $inBgcolor !== $this->_Bgcolor ) {
			$this->_Bgcolor = $inBgcolor;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Custom
	 * 
	 * @return string
	 * @access public
	 */
	function getCustom() {
		return $this->_Custom;
	}

	/**
	 * Set $_Custom to Custom
	 * 
	 * @param string $inCustom
	 * @return mofilmEventBase
	 * @access public
	 */
	function setCustom($inCustom) {
		if ( $inCustom !== $this->_Custom ) {
			$this->_Custom = $inCustom;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Tba
	 * 
	 * @return string
	 * @access public
	 */
	function getTba() {
		return $this->_Tba;
	}

	/**
	 * Set $_Tba to Tba
	 * 
	 * @param string $inTba
	 * @return mofilmEventBase
	 * @access public
	 */
	function setTba($inTba) {
		if ( $inTba !== $this->_Tba ) {
			$this->_Tba = $inTba;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Status
	 * 
	 * @return string
	 * @access public
	 */
	function getStatus() {
		return $this->_Status;
	}

	/**
	 * Set $_Status to Status
	 * 
	 * @param string $inStatus
	 * @return mofilmEventBase
	 * @access public
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the name to use for the logo image
	 * 
	 * @return string
	 */
	function getLogoName() {
		return $this->getID();
	}
        
	/**
	 * Return value of $_ProductID
	 * 
	 * @return string
	 * @access public
	 */
	function getProductID() {
		return $this->_ProductID;
	}

	/**
	 * Set $_ProductID to ProductID
	 * 
	 * @param string $inProductID
	 * @return mofilmEventBase
	 * @access public
	 */
	function setProductID($inProductID) {
		if ( $inProductID !== $this->_ProductID ) {
			$this->_ProductID = $inProductID;
			$this->setModified();
		}
		return $this;
	}
        
        
}