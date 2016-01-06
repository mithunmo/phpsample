<?php
/**
 * mofilmMovieBase
 * 
 * Stored in mofilmMovieBase.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMovieBase
 * @category mofilmMovieBase
 * @version $Rev: 146 $
 */


/**
 * mofilmMovieBase Class
 * 
 * Provides access to records in mofilm_content.movies
 * 
 * Creating a new record:
 * <code>
 * $oMofilmMovie = new mofilmMovieBase();
 * $oMofilmMovie->setID($inID);
 * $oMofilmMovie->setUserID($inUserID);
 * $oMofilmMovie->setStatus($inStatus);
 * $oMofilmMovie->setActive($inActive);
 * $oMofilmMovie->setShortDesc($inShortDesc);
 * $oMofilmMovie->setLongDesc($inLongDesc);
 * $oMofilmMovie->setRuntime($inRuntime);
 * $oMofilmMovie->setProductionYear($inProductionYear);
 * $oMofilmMovie->setUploaded($inUploaded);
 * $oMofilmMovie->setDateModified($inModified);
 * $oMofilmMovie->setModerated($inModerated);
 * $oMofilmMovie->setModeratorID($inModeratorID);
 * $oMofilmMovie->setModeratorComments($inModeratorComments);
 * $oMofilmMovie->setAvgRating($inAvgRating);
 * $oMofilmMovie->setRatingCount($inRatingCount);
 * $oMofilmMovie->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMovie = new mofilmMovieBase($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmMovie = new mofilmMovieBase();
 * $oMofilmMovie->setID($inID);
 * $oMofilmMovie->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmMovie = mofilmMovieBase::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmMovieBase
 * @category mofilmMovieBase
 */
class mofilmMovieBase implements systemDaoInterface, systemDaoValidatorInterface {
	
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
	 * Stores $_UserID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;
			
	/**
	 * Stores $_Status
	 * 
	 * @var string (STATUS_ENCODING,STATUS_PENDING,STATUS_REMOVED,STATUS_REJECTED,STATUS_APPROVED,STATUS_DISPUTED,STATUS_FAILED_ENCODING,)
	 * @access protected
	 */
	protected $_Status;
	const STATUS_ENCODING = 'Encoding';
	const STATUS_PENDING = 'Pending';
	const STATUS_REMOVED = 'Removed';
	const STATUS_REJECTED = 'Rejected';
	const STATUS_APPROVED = 'Approved';
	const STATUS_DISPUTED = 'Disputed';
	const STATUS_FAILED_ENCODING = 'Failed Encoding';
				
	/**
	 * Stores $_Active
	 * 
	 * @var string (ACTIVE_Y,ACTIVE_N,)
	 * @access protected
	 */
	protected $_Active;
	const ACTIVE_Y = 'Y';
	const ACTIVE_N = 'N';

	/**
	 * Stores $_Private
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Private;

	/**
	 * Stores $_ShortDesc
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_ShortDesc;
			
	/**
	 * Stores $_LongDesc
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_LongDesc;
	
	/**
	 * Stores $_Credits
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Credits;	
			
	/**
	 * Stores $_Runtime
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_Runtime;
			
	/**
	 * Stores $_ProductionYear
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_ProductionYear;
			
	/**
	 * Stores $_Uploaded
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_Uploaded;
			
	/**
	 * Stores $_DateModified
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_DateModified;
			
	/**
	 * Stores $_Moderated
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_Moderated;
			
	/**
	 * Stores $_ModeratorID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_ModeratorID;
			
	/**
	 * Stores $_ModeratorComments
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_ModeratorComments;
			
	/**
	 * Stores $_AvgRating
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_AvgRating;
			
	/**
	 * Stores $_RatingCount
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_RatingCount;

	/**
	 * Stores a loaded user object
	 * 
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_User;

	/**
	 * Stores a loaded referrer user object
	 * 
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_Referrer;
	
	/**
	 * Returns a new instance of mofilmMovieBase
	 * 
	 * @param integer $inID
	 * @return mofilmMovieBase
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies';
		
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
		$this->setUserID((int)$inArray['userID']);
		$this->setStatus($inArray['status']);
		$this->setActive($inArray['active']);
		$this->setPrivate((int)$inArray['private']);
		$this->setShortDesc($inArray['shortDesc']);
		$this->setLongDesc($inArray['longDesc']);
		$this->setCredits($inArray['credits']);
		$this->setRuntime((int)$inArray['runtime']);
		$this->setProductionYear($inArray['productionYear']);
		$this->setUploaded($inArray['uploaded']);
		$this->setDateModified($inArray['modified']);
		$this->setModerated($inArray['moderated']);
		$this->setModeratorID((int)$inArray['moderatorID']);
		$this->setModeratorComments($inArray['moderatorComments']);
		$this->setAvgRating((int)$inArray['avgRating']);
		$this->setRatingCount((int)$inArray['ratingCount']);
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
				$this->setDateModified(date(system::getConfig()->getDatabaseDatetimeFormat()));
				
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movies
					( ID, userID, status, active, private, shortDesc, longDesc, credits, runtime, productionYear, uploaded, modified, moderated, moderatorID, moderatorComments, avgRating, ratingCount)
				VALUES 
					(:ID, :UserID, :Status, :Active, :Private, :ShortDesc, :LongDesc, :Credits, :Runtime, :ProductionYear, :Uploaded, :Modified, :Moderated, :ModeratorID, :ModeratorComments, :AvgRating, :RatingCount)
				ON DUPLICATE KEY UPDATE
					userID=VALUES(userID),
					status=VALUES(status),
					active=VALUES(active),
					private=VALUES(private),
					shortDesc=VALUES(shortDesc),
					longDesc=VALUES(longDesc),
					credits=VALUES(credits),
					runtime=VALUES(runtime),
					productionYear=VALUES(productionYear),
					uploaded=VALUES(uploaded),
					modified=VALUES(modified),
					moderated=VALUES(moderated),
					moderatorID=VALUES(moderatorID),
					moderatorComments=VALUES(moderatorComments),
					avgRating=VALUES(avgRating),
					ratingCount=VALUES(ratingCount)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':UserID', $this->_UserID);
					$oStmt->bindValue(':Status', $this->_Status);
					$oStmt->bindValue(':Active', $this->_Active);
					$oStmt->bindValue(':Private', $this->_Private);
					$oStmt->bindValue(':ShortDesc', $this->_ShortDesc);
					$oStmt->bindValue(':LongDesc', $this->_LongDesc);
					$oStmt->bindValue(':Credits', $this->_Credits);
					$oStmt->bindValue(':Runtime', $this->_Runtime);
					$oStmt->bindValue(':ProductionYear', $this->_ProductionYear);
					$oStmt->bindValue(':Uploaded', $this->_Uploaded);
					$oStmt->bindValue(':Modified', $this->_DateModified);
					$oStmt->bindValue(':Moderated', $this->_Moderated);
					$oStmt->bindValue(':ModeratorID', $this->_ModeratorID);
					$oStmt->bindValue(':ModeratorComments', $this->_ModeratorComments);
					$oStmt->bindValue(':AvgRating', $this->_AvgRating);
					$oStmt->bindValue(':RatingCount', $this->_RatingCount);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
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
	 * @return mofilmMovieBase
	 */
	function reset() {
		$this->_ID = 0;
		$this->_UserID = 0;
		$this->_Status = 'Encoding';
		$this->_Active = 'Y';
		$this->_Private = 0;
		$this->_ShortDesc = '';
		$this->_LongDesc = null;
		$this->_Credits = '';
		$this->_Runtime = null;
		$this->_ProductionYear = null;
		$this->_Uploaded = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_DateModified = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Moderated = null;
		$this->_ModeratorID = null;
		$this->_ModeratorComments = null;
		$this->_AvgRating = null;
		$this->_RatingCount = null;
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
		$string .= " UserID[$this->_UserID] $newLine";
		$string .= " Status[$this->_Status] $newLine";
		$string .= " Active[$this->_Active] $newLine";
		$string .= " Private[$this->_Private] $newLine";
		$string .= " ShortDesc[$this->_ShortDesc] $newLine";
		$string .= " LongDesc[$this->_LongDesc] $newLine";
		$string .= " Credits[$this->_Credits] $newLine";
		$string .= " Runtime[$this->_Runtime] $newLine";
		$string .= " ProductionYear[$this->_ProductionYear] $newLine";
		$string .= " Uploaded[$this->_Uploaded] $newLine";
		$string .= " Modified[$this->_DateModified] $newLine";
		$string .= " Moderated[$this->_Moderated] $newLine";
		$string .= " ModeratorID[$this->_ModeratorID] $newLine";
		$string .= " ModeratorComments[$this->_ModeratorComments] $newLine";
		$string .= " AvgRating[$this->_AvgRating] $newLine";
		$string .= " RatingCount[$this->_RatingCount] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmMovieBase';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"UserID\" value=\"$this->_UserID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Status\" value=\"$this->_Status\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Active\" value=\"$this->_Active\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Private\" value=\"$this->_Private\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ShortDesc\" value=\"$this->_ShortDesc\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"LongDesc\" value=\"$this->_LongDesc\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Credits\" value=\"$this->_Credits\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Runtime\" value=\"$this->_Runtime\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ProductionYear\" value=\"$this->_ProductionYear\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Uploaded\" value=\"$this->_Uploaded\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Modified\" value=\"$this->_DateModified\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Moderated\" value=\"$this->_Moderated\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"ModeratorID\" value=\"$this->_ModeratorID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ModeratorComments\" value=\"$this->_ModeratorComments\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"AvgRating\" value=\"$this->_AvgRating\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"RatingCount\" value=\"$this->_RatingCount\" type=\"integer\" /> $newLine";
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
			$valid = $this->checkUserID($message);
		}
		if ( $valid ) {
			$valid = $this->checkStatus($message);
		}
		if ( $valid ) {
			$valid = $this->checkActive($message);
		}
		if ( $valid ) {
			$valid = $this->checkShortDesc($message);
		}
		if ( $valid ) {
			$valid = $this->checkLongDesc($message);
		}
		if ( $valid ) {
			$valid = $this->checkRuntime($message);
		}
		if ( $valid ) {
			$valid = $this->checkProductionYear($message);
		}
		if ( $valid ) {
			$valid = $this->checkUploaded($message);
		}
		if ( $valid ) {
			$valid = $this->checkDateModified($message);
		}
		if ( $valid ) {
			$valid = $this->checkModerated($message);
		}
		if ( $valid ) {
			$valid = $this->checkModeratorID($message);
		}
		if ( $valid ) {
			$valid = $this->checkModeratorComments($message);
		}
		if ( $valid ) {
			$valid = $this->checkAvgRating($message);
		}
		if ( $valid ) {
			$valid = $this->checkRatingCount($message);
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
	 * Checks that $_UserID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkUserID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_UserID) && $this->_UserID !== 0 ) {
			$inMessage .= "{$this->_UserID} is not a valid value for UserID";
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
		if ( !is_string($this->_Status) && $this->_Status !== '' ) {
			$inMessage .= "{$this->_Status} is not a valid value for Status";
			$isValid = false;
		}
		$statuses = array(
			self::STATUS_ENCODING, self::STATUS_PENDING, self::STATUS_REMOVED, self::STATUS_REJECTED,
			self::STATUS_APPROVED, self::STATUS_DISPUTED, self::STATUS_FAILED_ENCODING
		);
		if ( $isValid && $this->_Status != '' && !in_array($this->_Status, $statuses) ) {
			$inMessage .= "Status must be one of STATUS_ENCODING, STATUS_PENDING, STATUS_REMOVED, STATUS_REJECTED, STATUS_APPROVED, STATUS_DISPUTED, STATUS_FAILED_ENCODING";
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
		if ( !is_string($this->_Active) && $this->_Active !== '' ) {
			$inMessage .= "{$this->_Active} is not a valid value for Active";
			$isValid = false;
		}		
		if ( $isValid && $this->_Active != '' && !in_array($this->_Active, array(self::ACTIVE_Y, self::ACTIVE_N)) ) {
			$inMessage .= "Active must be one of ACTIVE_Y, ACTIVE_N";
			$isValid = false;
		}		
		return $isValid;
	}
		
	/**
	 * Checks that $_ShortDesc has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkShortDesc(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ShortDesc) && $this->_ShortDesc !== '' ) {
			systemLog::message("original message".$this->_ShortDesc);
			$this->_ShortDesc = "default title";
			$inMessage .= "{$this->_ShortDesc} is not a valid value for ShortDesc";
			//$isValid = false;
		}		
		if ( $isValid && strlen($this->_ShortDesc) > 150 ) {
			$inMessage .= "ShortDesc cannot be more than 60 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_ShortDesc) <= 1 ) {
			$inMessage .= "ShortDesc must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_LongDesc has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkLongDesc(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_LongDesc) && $this->_LongDesc !== null && $this->_LongDesc !== '' ) {
			$inMessage .= "{$this->_LongDesc} is not a valid value for LongDesc";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Runtime has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkRuntime(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Runtime) && $this->_Runtime !== null && $this->_Runtime !== 0 ) {
			$inMessage .= "{$this->_Runtime} is not a valid value for Runtime";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_ProductionYear has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkProductionYear(&$inMessage = '') {
		$isValid = true;
		$this->_ProductionYear = (int) $this->_ProductionYear;
		systemLog::message("py".$this->_ProductionYear);
		if ( is_string($this->_ProductionYear) && $this->_ProductionYear !== null && $this->_ProductionYear !== '' ) {
			$inMessage .= "{$this->_ProductionYear} is not a valid value for ProductionYear";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_ProductionYear) > 4 ) {
			$inMessage .= "ProductionYear cannot be more than 4 characters";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Uploaded has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkUploaded(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Uploaded) && $this->_Uploaded !== '' ) {
			$inMessage .= "{$this->_Uploaded} is not a valid value for Uploaded";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_DateModified has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkDateModified(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_DateModified) && $this->_DateModified !== null && $this->_DateModified !== '' ) {
			$inMessage .= "{$this->_DateModified} is not a valid value for Modified";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Moderated has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkModerated(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Moderated) && $this->_Moderated !== null && $this->_Moderated !== '' ) {
			$inMessage .= "{$this->_Moderated} is not a valid value for Moderated";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_ModeratorID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkModeratorID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ModeratorID) && $this->_ModeratorID !== null && $this->_ModeratorID !== 0 ) {
			$inMessage .= "{$this->_ModeratorID} is not a valid value for ModeratorID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_ModeratorComments has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkModeratorComments(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_ModeratorComments) && $this->_ModeratorComments !== null && $this->_ModeratorComments !== '' ) {
			$inMessage .= "{$this->_ModeratorComments} is not a valid value for ModeratorComments";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_AvgRating has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkAvgRating(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_AvgRating) && $this->_AvgRating !== null && $this->_AvgRating !== 0 ) {
			$inMessage .= "{$this->_AvgRating} is not a valid value for AvgRating";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_RatingCount has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkRatingCount(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_RatingCount) && $this->_RatingCount !== null && $this->_RatingCount !== 0 ) {
			$inMessage .= "{$this->_RatingCount} is not a valid value for RatingCount";
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
	 * @return mofilmMovieBase
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
	 * @return mofilmMovieBase
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
	 * Return value of $_UserID
	 * 
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}
	
	/**
	 * Returns the mofilmUser object
	 * 
	 * @return mofilmUser
	 */
	function getUser() {
		if ( !$this->_User instanceof mofilmUser ) {
			$this->_User = mofilmUserManager::getInstanceByID($this->getUserID());
			if ( !$this->_User ) {
				$this->_User = new mofilmUser();
			}
		}
		return $this->_User;
	}
	
	/**
	 * Set a pre-built user object to the movie
	 * 
	 * @param mofilmUser $inUser
	 * @return mofilmMovieBase
	 */
	function setUser(mofilmUser $inUser) {
		$this->_User = $inUser;
		return $this;
	}
	
	/**
	 * Set $_UserID to UserID
	 * 
	 * @param integer $inUserID
	 * @return mofilmMovieBase
	 * @access public
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->_User = null;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the mofilm referrer User object
	 * 
	 * @return mofilmUser
	 */
	function getReferrer() {
		if ( !$this->_Referrer instanceof mofilmUser ) {
			$referrerID = $this->getUser()->getParamSet()->getParam(mofilmUser::PARAM_REFERRED);
			if ( isset ($referrerID) && $referrerID > 0 ) {
				$this->_Referrer = mofilmUserManager::getInstanceByID($referrerID);
			}
		}
		
		if ( $this->_Referrer instanceof mofilmUser ) {
			return $this->_Referrer;
		} else {
			return false;
		}
	}
	
	/**
	 * Returns the number of day between referred user registration date and video uploaded date
	 * 
	 * @return integer
	 */
	function getReferredDays() {
		$upl = new DateTime($this->getUploadDate());
		$reg = new DateTime($this->_User->getRegistered());
		$diff = $upl->diff($reg);
		return $diff->format('%a');
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
	 * @return mofilmMovieBase
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
	 * Return value of $_Active
	 * 
	 * @return string
	 * @access public
	 */
	function getActive() {
		return $this->_Active;
	}
	
	/**
	 * Set $_Active to Active
	 * 
	 * @param string $inActive
	 * @return mofilmMovieBase
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
	 * Returns true if the movie is flagged as private
	 *
	 * @return boolean
	 */
	function isPrivate() {
		return $this->_Private == 1;
	}

	/**
	 * Returns the value of $_Private
	 *
	 * @return integer
	 */
	function getPrivate() {
		return $this->_Private;
	}

	/**
	 * Set $_Private to $inPrivate
	 *
	 * @param integer $inPrivate
	 * @return mofilmMovieBase
	 */
	function setPrivate($inPrivate) {
		if ( $inPrivate !== $this->_Private ) {
			$this->_Private = $inPrivate;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_ShortDesc
	 * 
	 * @return string
	 * @access public
	 */
	function getShortDesc() {
		return $this->_ShortDesc;
	}
	
	/**
	 * Alias of getShortDesc()
	 * 
	 * @return string
	 */
	function getTitle() {
		return $this->getShortDesc();
	}
	
	/**
	 * Set $_ShortDesc to ShortDesc
	 * 
	 * @param string $inShortDesc
	 * @return mofilmMovieBase
	 * @access public
	 */
	function setShortDesc($inShortDesc) {
		if ( $inShortDesc !== $this->_ShortDesc ) {
			$this->_ShortDesc = $inShortDesc;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_LongDesc
	 * 
	 * @return string
	 * @access public
	 */
	function getLongDesc() {
		return $this->_LongDesc;
	}
	
	/**
	 * Alias of getLongDesc
	 * 
	 * @return string
	 */
	function getDescription() {
		return $this->getLongDesc();
	}
	
	/**
	 * Set $_LongDesc to LongDesc
	 * 
	 * @param string $inLongDesc
	 * @return mofilmMovieBase
	 * @access public
	 */
	function setLongDesc($inLongDesc) {
		if ( $inLongDesc !== $this->_LongDesc ) {
			$this->_LongDesc = $inLongDesc;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Credits
	 *
	 * @return string
	 */
	function getCredits() {
		return $this->_Credits;
	}
	
	/**
	 * Set $_Credits to $inCredits
	 *
	 * @param string $inCredits
	 * @return mofilmMovieBase
	 */
	function setCredits($inCredits) {
		if ( $inCredits !== $this->_Credits ) {
			$this->_Credits = $inCredits;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Runtime
	 * 
	 * @return integer
	 * @access public
	 */
	function getRuntime() {
		return $this->_Runtime;
	}
	
	/**
	 * Alias of getRuntime()
	 * 
	 * @return integer
	 * @access public
	 */
	function getDuration() {
		return $this->getRuntime();
	}
	
	/**
	 * Set $_Runtime to Runtime
	 * 
	 * @param integer $inRuntime
	 * @return mofilmMovieBase
	 * @access public
	 */
	function setRuntime($inRuntime) {
		if ( $inRuntime !== $this->_Runtime ) {
			$this->_Runtime = $inRuntime;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_ProductionYear
	 * 
	 * @return string
	 * @access public
	 */
	function getProductionYear() {
		return $this->_ProductionYear;
	}
	
	/**
	 * Set $_ProductionYear to ProductionYear
	 * 
	 * @param string $inProductionYear
	 * @return mofilmMovieBase
	 * @access public
	 */
	function setProductionYear($inProductionYear) {
		if ( $inProductionYear !== $this->_ProductionYear ) {
			$this->_ProductionYear = $inProductionYear;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Uploaded
	 * 
	 * @return datetime
	 * @access public
	 */
	function getUploaded() {
		return $this->_Uploaded;
	}
	
	/**
	 * Alias of getUploaded()
	 * 
	 * @return datetime
	 * @access public
	 */
	function getUploadDate() {
		return $this->getUploaded();
	}
	
	/**
	 * Set $_Uploaded to Uploaded
	 * 
	 * @param datetime $inUploaded
	 * @return mofilmMovieBase
	 * @access public
	 */
	function setUploaded($inUploaded) {
		if ( $inUploaded !== $this->_Uploaded ) {
			$this->_Uploaded = $inUploaded;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_DateModified
	 * 
	 * @return datetime
	 * @access public
	 */
	function getDateModified() {
		return $this->_DateModified;
	}
	
	/**
	 * Set $_DateModified to $inDateModified
	 * 
	 * @param datetime $inDateModified
	 * @return mofilmMovieBase
	 * @access public
	 */
	function setDateModified($inDateModified) {
		if ( $inDateModified !== $this->_DateModified ) {
			$this->_DateModified = $inDateModified;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Moderated
	 * 
	 * @return datetime
	 * @access public
	 */
	function getModerated() {
		return $this->_Moderated;
	}
	
	/**
	 * Set $_Moderated to Moderated
	 * 
	 * @param datetime $inModerated
	 * @return mofilmMovieBase
	 * @access public
	 */
	function setModerated($inModerated) {
		if ( $inModerated !== $this->_Moderated ) {
			$this->_Moderated = $inModerated;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_ModeratorID
	 * 
	 * @return integer
	 * @access public
	 */
	function getModeratorID() {
		return $this->_ModeratorID;
	}
	
	/**
	 * Returns the mofilmUser object that moderated the movie
	 * 
	 * @return mofilmUser
	 */
	function getModerator() {
		return mofilmUserManager::getInstance()
			->setLoadOnlyActive(false)
			->getUserByID($this->getModeratorID());
	}
	
	/**
	 * Set $_ModeratorID to ModeratorID
	 * 
	 * @param integer $inModeratorID
	 * @return mofilmMovieBase
	 * @access public
	 */
	function setModeratorID($inModeratorID) {
		if ( $inModeratorID !== $this->_ModeratorID ) {
			$this->_ModeratorID = $inModeratorID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_ModeratorComments
	 * 
	 * @return string
	 * @access public
	 */
	function getModeratorComments() {
		return $this->_ModeratorComments;
	}
	
	/**
	 * Set $_ModeratorComments to ModeratorComments
	 * 
	 * @param string $inModeratorComments
	 * @return mofilmMovieBase
	 * @access public
	 */
	function setModeratorComments($inModeratorComments) {
		if ( $inModeratorComments !== $this->_ModeratorComments ) {
			$this->_ModeratorComments = $inModeratorComments;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_AvgRating
	 * 
	 * @return integer
	 * @access public
	 */
	function getAvgRating() {
		return $this->_AvgRating;
	}
	
	/**
	 * Set $_AvgRating to AvgRating
	 * 
	 * @param integer $inAvgRating
	 * @return mofilmMovieBase
	 * @access public
	 */
	function setAvgRating($inAvgRating) {
		if ( $inAvgRating !== $this->_AvgRating ) {
			$this->_AvgRating = $inAvgRating;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_RatingCount
	 * 
	 * @return integer
	 * @access public
	 */
	function getRatingCount() {
		return $this->_RatingCount;
	}
	
	/**
	 * Set $_RatingCount to RatingCount
	 * 
	 * @param integer $inRatingCount
	 * @return mofilmMovieBase
	 * @access public
	 */
	function setRatingCount($inRatingCount) {
		if ( $inRatingCount !== $this->_RatingCount ) {
			$this->_RatingCount = $inRatingCount;
			$this->setModified();
		}
		return $this;
	}
}
