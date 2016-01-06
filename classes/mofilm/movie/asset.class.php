<?php
/**
 * mofilmMovieAsset
 * 
 * Stored in mofilmMovieAsset.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMovieAsset
 * @category mofilmMovieAsset
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieAsset Class
 * 
 * Provides access to records in mofilm_content.movieAssets
 * 
 * Creating a new record:
 * <code>
 * $oMofilmMovieAsset = new mofilmMovieAsset();
 * $oMofilmMovieAsset->setID($inID);
 * $oMofilmMovieAsset->setMovieID($inMovieID);
 * $oMofilmMovieAsset->setType($inType);
 * $oMofilmMovieAsset->setExt($inExt);
 * $oMofilmMovieAsset->setProfileID($inProfileID);
 * $oMofilmMovieAsset->setDescription($inDescription);
 * $oMofilmMovieAsset->setWidth($inWidth);
 * $oMofilmMovieAsset->setHeight($inHeight);
 * $oMofilmMovieAsset->setMetadata($inMetadata);
 * $oMofilmMovieAsset->setFilename($inFilename);
 * $oMofilmMovieAsset->setDateModified($inModified);
 * $oMofilmMovieAsset->setCdnURL($inCdnURL);
 * $oMofilmMovieAsset->setNotes($inNotes);
 * $oMofilmMovieAsset->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMovieAsset = new mofilmMovieAsset($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmMovieAsset = new mofilmMovieAsset();
 * $oMofilmMovieAsset->setID($inID);
 * $oMofilmMovieAsset->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmMovieAsset = mofilmMovieAsset::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmMovieAsset
 * @category mofilmMovieAsset
 */
class mofilmMovieAsset implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmMovieAsset
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
	 * Stores $_MovieID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieID;
			
	/**
	 * Stores $_Type
	 * 
	 * @var string (TYPE_SOURCE,TYPE_THUMBNAIL,TYPE_FILE,TYPE_CCA)
	 * @access protected
	 */
	protected $_Type;
	const TYPE_SOURCE = 'Source';
	const TYPE_THUMBNAIL = 'ThumbNail';
	const TYPE_FILE = 'File';
	const TYPE_CCA = 'Cca';
	const TYPE_CCA_VERIFIED = 'CCA VERIFIED';
				
	/**
	 * Stores $_Ext
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Ext;
			
	/**
	 * Stores $_ProfileID
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_ProfileID;
			
	/**
	 * Stores $_Description
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Description;
			
	/**
	 * Stores $_Width
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_Width;
			
	/**
	 * Stores $_Height
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_Height;
			
	/**
	 * Stores $_Metadata, an array of values
	 * 
	 * @var array 
	 * @access protected
	 */
	protected $_Metadata;
			
	/**
	 * Stores $_Filename
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Filename;
			
	/**
	 * Stores $_DateModified
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_DateModified;
			
	/**
	 * Stores $_CdnURL
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_CdnURL;
			
	/**
	 * Stores $_Notes
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Notes;
	
	/**
	 * Stores $_MarkForDeletion
	 *
	 * @var boolean
	 * @access private
	 */
	private $_MarkForDeletion;
	
	
	
	/**
	 * Returns a new instance of mofilmMovieAsset
	 * 
	 * @param integer $inID
	 * @return mofilmMovieAsset
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
	 * Creates a new mofilmMovieAsset containing non-unique properties
	 * 
	 * @param integer $inMovieID
	 * @param string $inType
	 * @param string $inExt
	 * @param integer $inProfileID
	 * @param string $inDescription
	 * @param integer $inWidth
	 * @param integer $inHeight
	 * @param array $inMetadata
	 * @param string $inFilename
	 * @param datetime $inModified
	 * @param string $inCdnURL
	 * @param string $inNotes
	 * @return mofilmMovieAsset
	 * @static 
	 */
	public static function factory($inMovieID = null, $inType = null, $inExt = null, $inProfileID = null, $inDescription = null, $inWidth = null, $inHeight = null, $inMetadata = null, $inFilename = null, $inModified = null, $inCdnURL = null, $inNotes = null) {
		$oObject = new mofilmMovieAsset;
		if ( $inMovieID !== null ) {
			$oObject->setMovieID($inMovieID);
		}
		if ( $inType !== null ) {
			$oObject->setType($inType);
		}
		if ( $inExt !== null ) {
			$oObject->setExt($inExt);
		}
		if ( $inProfileID !== null ) {
			$oObject->setProfileID($inProfileID);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inWidth !== null ) {
			$oObject->setWidth($inWidth);
		}
		if ( $inHeight !== null ) {
			$oObject->setHeight($inHeight);
		}
		if ( $inMetadata !== null ) {
			$oObject->setMetadata($inMetadata);
		}
		if ( $inFilename !== null ) {
			$oObject->setFilename($inFilename);
		}
		if ( $inModified !== null ) {
			$oObject->setDateModified($inModified);
		}
		if ( $inCdnURL !== null ) {
			$oObject->setCdnURL($inCdnURL);
		}
		if ( $inNotes !== null ) {
			$oObject->setNotes($inNotes);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmMovieAsset by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmMovieAsset
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
		$oObject = new mofilmMovieAsset();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmMovieAsset
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inMovieID
	 * @param string $inFilename
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inMovieID = null, $inType = null, $inFilename = null) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAssets WHERE 1';
		if ( $inMovieID !== null ) {
			$query .= ' AND movieID = '.dbManager::getInstance()->quote($inMovieID);
		}
		if ( $inType !== null && in_array($inType, self::getTypes()) ) {
			$query .= ' AND type = '.dbManager::getInstance()->quote($inType);
		}
		if ( $inFilename !== null && strlen($inFilename) > 3 ) {
			$query .= ' AND filename LIKE '.dbManager::getInstance()->quote($inFilename);
		}
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMovieAsset();
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
	 * Returns the array of asset types
	 * 
	 * @return array
	 * @static
	 */
	public static function getTypes() {
		return array(self::TYPE_FILE, self::TYPE_SOURCE, self::TYPE_THUMBNAIL);
	}
	
	
	
	/**
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAssets';
		
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
		$this->setMovieID((int)$inArray['movieID']);
		$this->setType($inArray['type']);
		$this->setExt($inArray['ext']);
		$this->setProfileID((int)$inArray['profileID']);
		$this->setDescription($inArray['description']);
		$this->setWidth((int)$inArray['width']);
		$this->setHeight((int)$inArray['height']);
		$this->setFilename($inArray['filename']);
		$this->setDateModified($inArray['modified']);
		$this->setCdnURL($inArray['cdnURL']);
		$this->setNotes($inArray['notes']);
		
		$arr = unserialize($inArray['metadata']);
		if ( is_array($arr) ) {
			$this->setMetadata($arr);
		}
		unset ($arr);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieAssets
					( ID, movieID, type, ext, profileID, description, width, height, metadata, filename, modified, cdnURL, notes)
				VALUES 
					(:ID, :MovieID, :Type, :Ext, :ProfileID, :Description, :Width, :Height, :Metadata, :Filename, :Modified, :CdnURL, :Notes)
				ON DUPLICATE KEY UPDATE
					movieID=VALUES(movieID),
					type=VALUES(type),
					ext=VALUES(ext),
					profileID=VALUES(profileID),
					description=VALUES(description),
					width=VALUES(width),
					height=VALUES(height),
					metadata=VALUES(metadata),
					filename=VALUES(filename),
					modified=VALUES(modified),
					cdnURL=VALUES(cdnURL),
					notes=VALUES(notes)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':MovieID', $this->_MovieID);
					$oStmt->bindValue(':Type', $this->_Type);
					$oStmt->bindValue(':Ext', $this->_Ext);
					$oStmt->bindValue(':ProfileID', $this->_ProfileID);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':Width', $this->_Width);
					$oStmt->bindValue(':Height', $this->_Height);
					$oStmt->bindValue(':Metadata', serialize($this->_Metadata));
					$oStmt->bindValue(':Filename', $this->_Filename);
					$oStmt->bindValue(':Modified', $this->_DateModified);
					$oStmt->bindValue(':CdnURL', $this->_CdnURL);
					$oStmt->bindValue(':Notes', $this->_Notes);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAssets
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
	 * @return mofilmMovieAsset
	 */
	function reset() {
		$this->_ID = 0;
		$this->_MovieID = 0;
		$this->_Type = 'File';
		$this->_Ext = '';
		$this->_ProfileID = 0;
		$this->_Description = '';
		$this->_Width = null;
		$this->_Height = null;
		$this->_Metadata = array();
		$this->_Filename = null;
		$this->_DateModified = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_CdnURL = null;
		$this->_Notes = null;
		$this->_MarkForDeletion = false;
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
		$string .= " MovieID[$this->_MovieID] $newLine";
		$string .= " Type[$this->_Type] $newLine";
		$string .= " Ext[$this->_Ext] $newLine";
		$string .= " ProfileID[$this->_ProfileID] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " Width[$this->_Width] $newLine";
		$string .= " Height[$this->_Height] $newLine";
		$string .= " Metadata[$this->_Metadata] $newLine";
		$string .= " Filename[$this->_Filename] $newLine";
		$string .= " Modified[$this->_DateModified] $newLine";
		$string .= " CdnURL[$this->_CdnURL] $newLine";
		$string .= " Notes[$this->_Notes] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmMovieAsset';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"MovieID\" value=\"$this->_MovieID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Type\" value=\"$this->_Type\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Ext\" value=\"$this->_Ext\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"ProfileID\" value=\"$this->_ProfileID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Width\" value=\"$this->_Width\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Height\" value=\"$this->_Height\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Metadata\" value=\"$this->_Metadata\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Filename\" value=\"$this->_Filename\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Modified\" value=\"$this->_DateModified\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"CdnURL\" value=\"$this->_CdnURL\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Notes\" value=\"$this->_Notes\" type=\"string\" /> $newLine";
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
			$valid = $this->checkMovieID($message);
		}
		if ( $valid ) {
			$valid = $this->checkType($message);
		}
		if ( $valid ) {
			$valid = $this->checkExt($message);
		}
		if ( $valid ) {
			$valid = $this->checkProfileID($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkWidth($message);
		}
		if ( $valid ) {
			$valid = $this->checkHeight($message);
		}
		if ( $valid ) {
			$valid = $this->checkMetadata($message);
		}
		if ( $valid ) {
			$valid = $this->checkFilename($message);
		}
		if ( $valid ) {
			$valid = $this->checkDateModified($message);
		}
		if ( $valid ) {
			$valid = $this->checkCdnURL($message);
		}
		if ( $valid ) {
			$valid = $this->checkNotes($message);
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
	 * Checks that $_MovieID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkMovieID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MovieID) && $this->_MovieID !== 0 ) {
			$inMessage .= "{$this->_MovieID} is not a valid value for MovieID";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Type has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkType(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Type) && $this->_Type !== '' ) {
			$inMessage .= "{$this->_Type} is not a valid value for Type";
			$isValid = false;
		}		
		if ( $isValid && $this->_Type != '' && !in_array($this->_Type, array(self::TYPE_SOURCE, self::TYPE_THUMBNAIL, self::TYPE_FILE, self::TYPE_CCA)) ) {
			$inMessage .= "Type must be one of TYPE_SOURCE, TYPE_THUMBNAIL, TYPE_FILE";
			$isValid = false;
		}		
		return $isValid;
	}
		
	/**
	 * Checks that $_Ext has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkExt(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Ext) && $this->_Ext !== '' ) {
			$inMessage .= "{$this->_Ext} is not a valid value for Ext";
			$isValid = false;
		}		
		if ( $isValid && strlen($this->_Ext) > 8 ) {
			$inMessage .= "Ext cannot be more than 8 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Ext) <= 1 ) {
			$inMessage .= "Ext must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_ProfileID has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkProfileID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ProfileID) && $this->_ProfileID !== 0 ) {
			$inMessage .= "{$this->_ProfileID} is not a valid value for ProfileID";
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
		if ( $isValid && strlen($this->_Description) > 30 ) {
			$inMessage .= "Description cannot be more than 30 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Width has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkWidth(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Width) && $this->_Width !== null && $this->_Width !== 0 ) {
			$inMessage .= "{$this->_Width} is not a valid value for Width";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Height has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkHeight(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_Height) && $this->_Height !== null && $this->_Height !== 0 ) {
			$inMessage .= "{$this->_Height} is not a valid value for Height";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Metadata has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkMetadata(&$inMessage = '') {
		$isValid = true;
		return $isValid;
	}
		
	/**
	 * Checks that $_Filename has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkFilename(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Filename) && $this->_Filename !== null && $this->_Filename !== '' ) {
			$inMessage .= "{$this->_Filename} is not a valid value for Filename";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Modified has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkDateModified(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_DateModified) && $this->_DateModified !== '' ) {
			$inMessage .= "{$this->_DateModified} is not a valid value for Modified";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_CdnURL has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkCdnURL(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_CdnURL) && $this->_CdnURL !== null && $this->_CdnURL !== '' ) {
			$inMessage .= "{$this->_CdnURL} is not a valid value for CdnURL";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Notes has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkNotes(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Notes) && $this->_Notes !== null && $this->_Notes !== '' ) {
			$inMessage .= "{$this->_Notes} is not a valid value for Notes";
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
	 * @return mofilmMovieAsset
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
	 * @return mofilmMovieAsset
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
	 * Return value of $_MovieID
	 * 
	 * @return integer
	 * @access public
	 */
	function getMovieID() {
		return $this->_MovieID;
	}
	
	/**
	 * Set $_MovieID to MovieID
	 * 
	 * @param integer $inMovieID
	 * @return mofilmMovieAsset
	 * @access public
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Type
	 * 
	 * @return string
	 * @access public
	 */
	function getType() {
		return $this->_Type;
	}
	
	/**
	 * Set $_Type to Type
	 * 
	 * @param string $inType
	 * @return mofilmMovieAsset
	 * @access public
	 */
	function setType($inType) {
		if ( $inType !== $this->_Type ) {
			$this->_Type = $inType;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Ext
	 * 
	 * @return string
	 * @access public
	 */
	function getExt() {
		return $this->_Ext;
	}
	
	/**
	 * Set $_Ext to Ext
	 * 
	 * @param string $inExt
	 * @return mofilmMovieAsset
	 * @access public
	 */
	function setExt($inExt) {
		if ( $inExt !== $this->_Ext ) {
			$this->_Ext = $inExt;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_ProfileID
	 * 
	 * @return integer
	 * @access public
	 */
	function getProfileID() {
		return $this->_ProfileID;
	}
	
	/**
	 * Set $_ProfileID to ProfileID
	 * 
	 * @param integer $inProfileID
	 * @return mofilmMovieAsset
	 * @access public
	 */
	function setProfileID($inProfileID) {
		if ( $inProfileID !== $this->_ProfileID ) {
			$this->_ProfileID = $inProfileID;
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
	 * @return mofilmMovieAsset
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
	 * Return value of $_Width
	 * 
	 * @return integer
	 * @access public
	 */
	function getWidth() {
		return $this->_Width;
	}
	
	/**
	 * Set $_Width to Width
	 * 
	 * @param integer $inWidth
	 * @return mofilmMovieAsset
	 * @access public
	 */
	function setWidth($inWidth) {
		if ( $inWidth !== $this->_Width ) {
			$this->_Width = $inWidth;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Height
	 * 
	 * @return integer
	 * @access public
	 */
	function getHeight() {
		return $this->_Height;
	}
	
	/**
	 * Set $_Height to Height
	 * 
	 * @param integer $inHeight
	 * @return mofilmMovieAsset
	 * @access public
	 */
	function setHeight($inHeight) {
		if ( $inHeight !== $this->_Height ) {
			$this->_Height = $inHeight;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Metadata
	 * 
	 * @return array
	 * @access public
	 */
	function getMetadata() {
		return $this->_Metadata;
	}
	
	/**
	 * Set $_Metadata to Metadata
	 * 
	 * @param array $inMetadata
	 * @return mofilmMovieAsset
	 * @access public
	 */
	function setMetadata(array $inMetadata = array()) {
		if ( $inMetadata !== $this->_Metadata ) {
			$this->_Metadata = $inMetadata;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Filename
	 * 
	 * @return string
	 * @access public
	 */
	function getFilename() {
		return $this->_Filename;
	}
	
	/**
	 * Returns just the filename minus any path
	 * 
	 * @return string
	 */
	function getFileBasename() {
		return basename($this->getFilename());
	}
	
	/**
	 * Returns just the path to the file
	 * 
	 * @return string
	 */
	function getFileDirectory() {
		return dirname($this->getFilename());
	}
	
	/**
	 * Returns whether file exists
	 * 
	 * @return string
	 */
	function getFileExists() {
		return file_exists($this->getFilename());
	}
	
	/**
	 * Set $_Filename to Filename
	 * 
	 * @param string $inFilename
	 * @return mofilmMovieAsset
	 * @access public
	 */
	function setFilename($inFilename) {
		if ( $inFilename !== $this->_Filename ) {
			$this->_Filename = $inFilename;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Modified
	 * 
	 * @return datetime
	 * @access public
	 */
	function getDateModified() {
		return $this->_DateModified;
	}
	
	/**
	 * Set $_Modified to Modified
	 * 
	 * @param datetime $inModified
	 * @return mofilmMovieAsset
	 * @access public
	 */
	function setDateModified($inModified) {
		if ( $inModified !== $this->_DateModified ) {
			$this->_DateModified = $inModified;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_CdnURL
	 * 
	 * @return string
	 * @access public
	 */
	function getCdnURL() {
		return $this->_CdnURL;
	}
	
	/**
	 * Set $_CdnURL to CdnURL
	 * 
	 * @param string $inCdnURL
	 * @return mofilmMovieAsset
	 * @access public
	 */
	function setCdnURL($inCdnURL) {
		if ( $inCdnURL !== $this->_CdnURL ) {
			$this->_CdnURL = $inCdnURL;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Notes
	 * 
	 * @return string
	 * @access public
	 */
	function getNotes() {
		return $this->_Notes;
	}
	
	/**
	 * Set $_Notes to Notes
	 * 
	 * @param string $inNotes
	 * @return mofilmMovieAsset
	 * @access public
	 */
	function setNotes($inNotes) {
		if ( $inNotes !== $this->_Notes ) {
			$this->_Notes = $inNotes;
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
	 * @return mofilmMovieAsset
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}