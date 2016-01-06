<?php
/**
 * mofilmSourceBase
 * 
 * Stored in mofilmSourceBase.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSourceBase
 * @category mofilmSourceBase
 * @version $Rev: 10 $
 */


/**
 * mofilmSourceBase Class
 * 
 * Provides access to records in mofilm_content.sources
 * 
 * Creating a new record:
 * <code>
 * $oMofilmSource = new mofilmSourceBase();
 * $oMofilmSource->setID($inID);
 * $oMofilmSource->setEventID($inEventID);
 * $oMofilmSource->setName($inName);
 * $oMofilmSource->setHidden($inHidden);
 * $oMofilmSource->setCustom($inCustom);
 * $oMofilmSource->setStartDate($inStartDate);
 * $oMofilmSource->setEndDate($inEndDate);
 * $oMofilmSource->setTermsID($inTermsID);
 * $oMofilmSource->setInstructions($inInstructions);
 * $oMofilmSource->setBgcolor($inBgcolor);
 * $oMofilmSource->setTripbudget($inTripbudget);
 * $oMofilmSource->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmSource = new mofilmSourceBase($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmSource = new mofilmSourceBase();
 * $oMofilmSource->setID($inID);
 * $oMofilmSource->load();
 * </code>
 * 
 * @package mofilm
 * @subpackage mofilmSourceBase
 * @category mofilmSourceBase
 */
class mofilmSourceBase implements systemDaoInterface, systemDaoValidatorInterface {
	
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
	 * Stores $_EventID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_EventID;
        
        /**
	 * Stores $_BrandID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_BrandID;
			
	/**
	 * Stores $_Name
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Name;
			
	/**
	 * Stores $_Hidden
	 * 
	 * @var string (YES_Y,NO_N,)
	 * @access protected
	 */
	protected $_Hidden;
	
	/**
	 * Stores $_Custom
	 * 
	 * @var string (YES_Y,NO_N,)
	 * @access protected
	 */
	protected $_Custom;
	
	const YES_Y = 'Y';
	const NO_N = 'N';
        const INVITE_I = 'I';
				
	/**
	 * Stores $_StartDate
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_StartDate;
			
	/**
	 * Stores $_EndDate
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_EndDate;
			
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
	 * Stores $_Tripbudget
	 * 
	 * @var integer
	 * @access protected
	 */
	protected $_Tripbudget;

	/**
	 * Stores $_Status
	 * 
	 * @var string
	 * @access protected
	 */
	protected $_Status;

	/**
	 * Stores $_SponsorID
	 * 
	 * @var string
	 * @access protected
	 */
	protected $_SponsorID;
        
        
	/**
	 * Stores $_MarkForDeletion
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion;
	
	/**
	 * Stores $_DownloadHash, a unique file hash 
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DownloadHash;
	
	const STATUS_OPEN = 'open';
	const STATUS_CLOSED = 'closed';
	const STATUS_PENDING = 'pending';
	
	CONST SOURCE_STATUS_DRAFT = 'DRAFT';
	CONST SOURCE_STATUS_CLOSED = 'CLOSED';
	CONST SOURCE_STATUS_PUBLISHED = 'PUBLISHED';
	CONST SOURCE_STATUS_JUDGING = 'JUDGING';
	CONST SOURCE_STATUS_WINNERS = 'WINNERS';


	/**
	 * Returns a new instance of mofilmSourceBase
	 * 
	 * @param integer $inID
	 * @return mofilmSourceBase
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.sources';
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
		$this->setEventID((int)$inArray['eventID']);
                $this->setBrandID((int)$inArray['brandID']);
		$this->setName($inArray['name']);
		$this->setHidden($inArray['hidden']);
		$this->setCustom($inArray['custom']);
		$this->setStartDate($inArray['startDate']);
		$this->setEndDate($inArray['endDate']);
                $this->setSponsorID($inArray["sponsorID"]);
		$this->setTermsID((int)$inArray['termsID']);
		$this->setInstructions($inArray['instructions']);
		$this->setBgcolor($inArray['bgcolor']);
		$this->setTripbudget($inArray['tripbudget']);
		$this->setSourceStatus($inArray['status']);
		if ( array_key_exists('downloadHash', $inArray) ) {
			$this->setDownloadHash($inArray['downloadHash']);
		}
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.sources
					( ID, eventID,brandID, name, hidden, custom, startDate, endDate, termsID, instructions, bgcolor, tripbudget, status, sponsorID)
				VALUES 
					(:ID, :EventID,:BrandID, :Name, :Hidden, :Custom, :StartDate, :EndDate, :TermsID, :Instructions, :Bgcolor, :Tripbudget, :Status, :sponsorID)
				ON DUPLICATE KEY UPDATE
					eventID=VALUES(eventID),
                                        brandID=VALUES(brandID),
					name=VALUES(name),
					hidden=VALUES(hidden),
					custom=VALUES(custom),
					startDate=VALUES(startDate),
					endDate=VALUES(endDate),
					termsID=VALUES(termsID),
					instructions=VALUES(instructions),
					bgcolor=VALUES(bgcolor),
                                        sponsorID=VALUES(sponsorID),
					tripbudget=VALUES(tripbudget),
					status=VALUES(status)';

		systemLog::message($query);
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':EventID', $this->_EventID);
                                        $oStmt->bindValue(':BrandID', $this->_BrandID);
					$oStmt->bindValue(':Name', $this->_Name);
					$oStmt->bindValue(':Hidden', $this->_Hidden);
					$oStmt->bindValue(':Custom', $this->_Custom);
					$oStmt->bindValue(':StartDate', $this->_StartDate);
					$oStmt->bindValue(':EndDate', $this->_EndDate);
					$oStmt->bindValue(':TermsID', $this->_TermsID);
					$oStmt->bindValue(':Instructions', $this->_Instructions);
					$oStmt->bindValue(':Bgcolor', $this->_Bgcolor);
                                        $oStmt->bindValue(":sponsorID", $this->_SponsorID);
					$oStmt->bindValue(':Tripbudget', $this->_Tripbudget);
					$oStmt->bindValue(':Status', $this->_Status);

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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.sources
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
	 * @return mofilmSourceBase
	 */
	function reset() {
		$this->_ID = 0;
		$this->_EventID = 0;
                $this->_BrandID = 0;
                $this->_SponsorID = 0;
		$this->_Name = '';
		$this->_Hidden = self::NO_N;
		$this->_Custom = self::NO_N;
		$this->_StartDate = null;
		$this->_EndDate = null;
		$this->_TermsID = null;
		$this->_Instructions = null;
		$this->_Bgcolor = null;
		$this->_Tripbudget = 0;
		$this->_Status = null;
		$this->_MarkForDeletion = false;
		$this->_DownloadHash = null;
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
		$string .= " EventID[$this->_EventID] $newLine";
                $string .= " BrandID[$this->_BrandID] $newLine";
		$string .= " Name[$this->_Name] $newLine";
		$string .= " Hidden[$this->_Hidden] $newLine";
		$string .= " Custom[$this->_Custom] $newLine";
		$string .= " StartDate[$this->_StartDate] $newLine";
		$string .= " EndDate[$this->_EndDate] $newLine";
		$string .= " TermsID[$this->_TermsID] $newLine";
		$string .= " Instructions[$this->_Instructions] $newLine";
		$string .= " Bgcolor[$this->_Bgcolor] $newLine";
		$string .= " Tripbudget[$this->_Tripbudget] $newLine";
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
		$className = 'mofilmSourceBase';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"EventID\" value=\"$this->_EventID\" type=\"integer\" /> $newLine";
                $xml .= "\t<property name=\"BrandID\" value=\"$this->_BrandID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Name\" value=\"$this->_Name\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Hidden\" value=\"$this->_Hidden\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Custom\" value=\"$this->_Custom\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"StartDate\" value=\"$this->_StartDate\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"EndDate\" value=\"$this->_EndDate\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"TermsID\" value=\"$this->_TermsID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Instructions\" value=\"$this->_Instructions\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Bgcolor\" value=\"$this->_Bgcolor\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Tripbudget\" value=\"$this->_Tripbudget\" type=\"string\" /> $newLine";
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
			$valid = $this->checkEventID($message);
		}
		if ( $valid ) {
			$valid = $this->checkName($message);
		}
		if ( $valid ) {
			$valid = $this->checkHidden($message);
		}
		if ( $valid ) {
			$valid = $this->checkCustom($message);
		}
		if ( $valid ) {
			$valid = $this->checkStartDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkEndDate($message);
		}
		if ( $valid ) {
			$valid = $this->checkTermsID($message);
		}
		if ( $valid ) {
			$valid = $this->checkInstructions($message);
		}
		if ( $valid ) {
			$valid = $this->checkTripbudget($message);
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
	 * Checks that $_EventID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkEventID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_EventID) && $this->_EventID !== 0 ) {
			$inMessage .= "{$this->_EventID} is not a valid value for EventID";
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
		if ( $isValid && $this->_Hidden != '' && !in_array($this->_Hidden, array(self::YES_Y, self::NO_N, self::INVITE_I)) ) {
			$inMessage .= "Hidden must be one of YES_Y, NO_N, INVITE_I";
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
	 * Checks that $_StartDate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkStartDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_StartDate) && $this->_StartDate !== null && $this->_StartDate !== '' ) {
			$inMessage .= "{$this->_StartDate} is not a valid value for StartDate";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_EndDate has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkEndDate(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_EndDate) && $this->_EndDate !== null && $this->_EndDate !== '' ) {
			$inMessage .= "{$this->_EndDate} is not a valid value for EndDate";
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
	 * Checks that $_Tripbudget has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkTripbudget(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Tripbudget) && $this->_Tripbudget !== 0 ) {
			$inMessage .= "{$this->_Tripbudget} is not a valid value for Tripbudget";
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
	 * @return mofilmSourceBase
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
	 * @return mofilmSourceBase
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
	 * Return value of $_EventID
	 * 
	 * @return integer
	 * @access public
	 */
	function getEventID() {
		return $this->_EventID;
	}
        
        /**
	 * Return value of $_BrandID
	 * 
	 * @return integer
	 * @access public
	 */
	function getBrandID() {
		return $this->_BrandID;
	}
	
	/**
	 * Returns the mofilmEvent object
	 * 
	 * @return mofilmEvent
	 */
	function getEvent() {
		return mofilmEvent::getInstance($this->getEventID());
	}
	
	/**
	 * Returns the mofilmGrants object
	 * 
	 * @return mofilmGrants
	 */
	function getGrants() {
		return mofilmGrants::getInstanceBySourceID($this->getID());
	}
	
	/**
	 * Returns the mofilmSourceDataSet object
	 * 
	 * @return mofilmSourceDataSet
	 */
	function getSourceDataSet() {
		return mofilmSourceDataSet::getInstanceBySourceID($this->getID());
	}
	
	/**
	 * Returns the mofilmSourceDataPrizeSet object
	 * 
	 * @return mofilmSourceDataPrizeSet
	 */
	function getSourcePrizeSet() {
		if ( $this->getID() !== 0 ) {
			return mofilmSourcePrizeSet::listOfObjects($this->getID());
		}
	}
	
	/**
	 * Set $_EventID to EventID
	 * 
	 * @param integer $inEventID
	 * @return mofilmSourceBase
	 * @access public
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
			$this->setModified();
		}
		return $this;
	}
        
        /**
	 * Set $_BrandID to BrandID
	 * 
	 * @param integer $inBrandID
	 * @return mofilmSourceBase
	 * @access public
	 */
	function setBrandID($inBrandID) {
		if ( $inBrandID !== $this->_BrandID ) {
			$this->_BrandID = $inBrandID;
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
	 * @return mofilmSourceBase
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
	 * Returns true if the source is hidden
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
	 * @return mofilmSourceBase
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
	 * Returns true if the source is custom
	 * 
	 * @return boolean
	 */
	function isCustom() {
		return $this->getCustom() == self::YES_Y;
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
	 * @return mofilmSourceBase
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
	 * Return value of $_StartDate
	 * 
	 * @return string
	 * @access public
	 */
	function getStartDate() {
		return $this->_StartDate;
	}
	
	/**
	 * Set $_StartDate to StartDate
	 * 
	 * @param string $inStartDate
	 * @return mofilmSourceBase
	 * @access public
	 */
	function setStartDate($inStartDate) {
		if ( $inStartDate !== $this->_StartDate ) {
			$this->_StartDate = $inStartDate;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_EndDate
	 * 
	 * @return string
	 * @access public
	 */
	function getEndDate() {
		return $this->_EndDate;
	}
	
	/**
	 * Set $_EndDate to EndDate
	 * 
	 * @param string $inEndDate
	 * @return mofilmSourceBase
	 * @access public
	 */
	function setEndDate($inEndDate) {
		if ( $inEndDate !== $this->_EndDate ) {
			$this->_EndDate = $inEndDate;
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
	 * @return mofilmSourceBase
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
	 * @return mofilmSourceBase
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
	 * @return mofilmSourceBase
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
	 * Return value of $_Tripbudget
	 * 
	 * @return integer
	 * @access public
	 */
	function getTripbudget() {
		return $this->_Tripbudget;
	}
	
	/**
	 * Set $_Tripbudget to Tripbudget
	 * 
	 * @param integer $inTripbudget
	 * @return mofilmSourceBase
	 * @access public
	 */
	function setTripbudget($inTripbudget) {
		if ( $inTripbudget !== $this->_Tripbudget ) {
			$this->_Tripbudget = $inTripbudget;
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
	function getSourceStatus() {
		return $this->_Status;
	}
	
	/**
	 * Set $_Status to SourceStatus
	 * 
	 * @param string $inStatus
	 * @return mofilmEventBase
	 * @access public
	 */
	function setSourceStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the current status as a string
	 * 
	 * @return string
	 */
	function getStatus() {
		$now = date(system::getConfig()->getDatabaseDatetimeFormat());
		$startDate = $this->getStartDate();
		$endDate = $this->getEndDate();
		if ( !$startDate ) {
			$startDate = $this->getEvent()->getStartDate();
		}
		if ( !$endDate ) {
			$endDate = $this->getEvent()->getEndDate();
		}
		
		if ( $now < $startDate ) {
			return self::STATUS_PENDING;
		} elseif ($now > $endDate) {
			return self::STATUS_CLOSED;
		} else {
			return self::STATUS_OPEN;
		}
	}
	
	/**
	 * Returns true if source is open
	 * 
	 * @return boolean
	 */
	function isOpen() {
		return $this->getStatus() == self::STATUS_OPEN;
	}
	
	/**
	 * Returns true if source is closed
	 * 
	 * @return boolean
	 */
	function isClosed() {
		return $this->getStatus() == self::STATUS_CLOSED;
	}
	
	/**
	 * Returns true if source is pending
	 * 
	 * @return boolean
	 */
	function isPending() {
		return $this->getStatus() == self::STATUS_PENDING;
	}

	/**
	 * Return value of $_MarkForDeletion
	 *
	 * @return boolean
	 * @access public
	 */
	function getMarkForDeletion() {
		return $this->_MarkForDeletion;
	}

	/**
	 * Set $_MarkForDeletion to $inMarkForDeletion
	 *
	 * @param boolean $inMarkForDeletion
	 * @return mofilmSourceBase
	 * @access public
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the download hash if it has been loaded
	 *
	 * @return string
	 */
	function getDownloadHash() {
		if ( !$this->_DownloadHash ) {
			$this->_DownloadHash = mofilmUtilities::buildMiniHash($this);
		}
		return $this->_DownloadHash;
	}
	
	/**
	 * Set $_DownloadHash to $inDownloadHash
	 *
	 * @param string $inDownloadHash
	 * @return mofilmSourceBase
	 */
	function setDownloadHash($inDownloadHash) {
		if ( $inDownloadHash !== $this->_DownloadHash ) {
			$this->_DownloadHash = $inDownloadHash;
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
	 * Returns the name to use for the logo image
	 * 
	 * @return string
	 */
	function getSponsorID() {
		return $this->_SponsorID;
	}
        
	/**
	 * Set $_DownloadHash to $inDownloadHash
	 *
	 * @param string $inDownloadHash
	 * @return mofilmSourceBase
	 */
	function setSponsorID($inSponsorID) {
		if ( $inSponsorID !== $this->_SponsorID ) {
			$this->_SponsorID = $inSponsorID;
			$this->setModified();
		}
		return $this;
	}
        
        function getUser($inUserID){
            if ($inUserID == 0 ){
                return "MOFILM";
            } else {
                return mofilmUserManager::getInstanceByID($inUserID)->getFullname();
            }
        }
        
}