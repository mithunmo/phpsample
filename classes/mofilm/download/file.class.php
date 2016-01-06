<?php
/**
 * mofilmDownloadFile
 * 
 * Stored in mofilmDownloadFile.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmDownloadFile
 * @category mofilmDownloadFile
 * @version $Rev: 10 $
 */


/**
 * mofilmDownloadFile Class
 * 
 * Provides access to records in mofilm_content.downloadFiles
 * 
 * Creating a new record:
 * <code>
 * $oMofilmDownloadFile = new mofilmDownloadFile();
 * $oMofilmDownloadFile->setID($inID);
 * $oMofilmDownloadFile->setDateModified($inModified);
 * $oMofilmDownloadFile->setDescription($inDescription);
 * $oMofilmDownloadFile->setFiletype($inFiletype);
 * $oMofilmDownloadFile->setSourceID($inSourceID);
 * $oMofilmDownloadFile->setFilename($inFilename);
 * $oMofilmDownloadFile->setLang($inLang);
 * $oMofilmDownloadFile->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmDownloadFile = new mofilmDownloadFile($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmDownloadFile = new mofilmDownloadFile();
 * $oMofilmDownloadFile->setID($inID);
 * $oMofilmDownloadFile->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmDownloadFile = mofilmDownloadFile::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmDownloadFile
 * @category mofilmDownloadFile
 */
class mofilmDownloadFile implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmDownloadFile
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
	 * Stores $_Modified
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_DateModified;
			
	/**
	 * Stores $_Description
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Description;
			
	/**
	 * Stores $_Filetype
	 * 
	 * @var string (FILETYPE_NDA,FILETYPE_BRIEF,FILETYPE_PDF,FILETYPE_MUSIC,FILETYPE_ASSETS,FILETYPE_GUIDELINES,)
	 * @access protected
	 */
	protected $_Filetype;
	const FILETYPE_NDA = 'nda';
	const FILETYPE_BRIEF = 'brief';
	const FILETYPE_PDF = 'pdf';
	const FILETYPE_MUSIC = 'music';
	const FILETYPE_ASSETS = 'assets';
	const FILETYPE_GUIDELINES = 'guidelines';
			
	/**
	 * Stores $_Filename
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Filename;
	
	/**
	 * Stores $_DownloadHash, a unique download hash
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DownloadHash;
	
	/**
	 * Stores an instance of baseTableParamSet
	 *
	 * @var baseTableParamSet
	 * @access protected
	 */
	protected $_ParamSet;
	
	/**
	 * Stores an instance of mofilmDownloadSourceSet
	 *
	 * @var mofilmDownloadSourceSet
	 * @access protected
	 */
	protected $_SourceSet;
	
	/**
	 * Stores $_Lang
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Lang;
	
	
	/**
	 * Returns a new instance of mofilmDownloadFile
	 * 
	 * @param integer $inID
	 * @return mofilmDownloadFile
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
	 * Creates a new mofilmDownloadFile containing non-unique properties
	 * 
	 * @param datetime $inModified
	 * @param string $inDescription
	 * @param string $inFiletype
	 * @param string $inFilename
	 * @return mofilmDownloadFile
	 * @static 
	 */
	public static function factory($inModified = null, $inDescription = null, $inFiletype = null, $inFilename = null) {
		$oObject = new mofilmDownloadFile;
		if ( $inModified !== null ) {
			$oObject->setDateModified($inModified);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inFiletype !== null ) {
			$oObject->setFiletype($inFiletype);
		}
		if ( $inFilename !== null ) {
			$oObject->setFilename($inFilename);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmDownloadFile by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmDownloadFile
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
		$oObject = new mofilmDownloadFile();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmDownloadFile by primary key
	 * 
	 * @param string $inHash
	 * @return mofilmDownloadFile
	 * @static 
	 */
	public static function getInstanceByHash($inHash) {
		$oObject = new mofilmDownloadFile();
		
		$query = '
			SELECT downloadFiles.*, downloadSources.downloadHash
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.downloadSources ON (downloadFiles.ID = downloadSources.downloadID)
			 WHERE downloadSources.downloadHash = :Hash
			 LIMIT 1';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':Hash', $inHash);
		if ( $oStmt->execute() ) {
			$row = $oStmt->fetch();
			if ( $row !== false && is_array($row) ) {
				$oObject->loadFromArray($row);
			}
		}
		$oStmt->closeCursor();
		
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmDownloadFile
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param integer $inSourceID
	 * @param string $inFileType
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inSourceID = null, $inFileType = null) {
		$query = 'SELECT downloadFiles.* FROM '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles';
		$where = array();
		if ( $inSourceID !== null && $inSourceID > 0 ) {
			$query .= ' INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.downloadSources ON (downloadFiles.ID = downloadSources.downloadID)';
			$where[] = 'downloadSources.sourceID = '.dbManager::getInstance()->quote($inSourceID);
		}
		if ( $inFileType !== null && strlen($inFileType) > 0 ) {
			$where[] = 'downloadFiles.filetype = '.dbManager::getInstance()->quote($inFileType);
		}
		
		if ( count($where) > 0 ) {
			$query .= ' WHERE '.implode(' AND ', $where);
		}
		
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmDownloadFile();
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
	 * Returns array of defined file types
	 * 
	 * @return
	 * @static
	 */
	static function getFileTypes() {
		return array(
			self::FILETYPE_ASSETS, self::FILETYPE_NDA, self::FILETYPE_BRIEF, self::FILETYPE_GUIDELINES,
			self::FILETYPE_MUSIC, self::FILETYPE_PDF,
		);
	}
	
	
	
	/**
	 * Loads a record from the database based on the primary key or first unique index
	 * 
	 * @return boolean
	 */
	function load() {
		$return = false;
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles';
		
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
		$this->setDateModified($inArray['modified']);
		$this->setDescription($inArray['description']);
		$this->setFiletype($inArray['filetype']);
		$this->setFilename($inArray['filename']);
		if ( array_key_exists('downloadHash', $inArray) ) {
			$this->setDownloadHash($inArray['downloadHash']);
		}
		$this->setLang($inArray['lang']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles
					( ID, modified, description, filetype, filename, lang)
				VALUES 
					(:ID, :Modified, :Description, :Filetype, :Filename, :Lang)
				ON DUPLICATE KEY UPDATE
					modified=VALUES(modified),
					description=VALUES(description),
					filetype=VALUES(filetype),
					filename=VALUES(filename),
					lang=VALUES(lang)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':Modified', $this->_DateModified);
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':Filetype', $this->_Filetype);
					$oStmt->bindValue(':Filename', $this->_Filename);
					$oStmt->bindValue(':Lang', $this->_Lang);
								
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
			
			if ( $this->_ParamSet instanceof baseTableParamSet ) {
				$this->_ParamSet->setIndexID($this->getID());
				$this->_ParamSet->save();
			}
			if ( $this->_SourceSet instanceof mofilmDownloadSourceSet ) {
				$this->_SourceSet->setDownloadID($this->getID());
				$this->_SourceSet->save();
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles
		WHERE
			ID = :ID	
		LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':ID', $this->_ID);
				
			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				
				$this->getParamSet()->deleteAll();
				$this->getSourceSet()->delete();
				
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
	 * @return mofilmDownloadFile
	 */
	function reset() {
		$this->_ID = 0;
		$this->_DateModified = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Description = '';
		$this->_Filetype = '';
		$this->_Filename = '';
		$this->_Lang = '';
		$this->_DownloadHash = null;
		$this->_ParamSet = null;
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
		$string .= " Modified[$this->_DateModified] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " Filetype[$this->_Filetype] $newLine";
		$string .= " Filename[$this->_Filename] $newLine";
		$string .= " Lang[$this->_Lang] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmDownloadFile';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Modified\" value=\"$this->_DateModified\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Filetype\" value=\"$this->_Filetype\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Filename\" value=\"$this->_Filename\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"Lang\" value=\"$this->_Lang\" type=\"string\" /> $newLine";
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
			$valid = $this->checkDateModified($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkFiletype($message);
		}
		if ( $valid ) {
			$valid = $this->checkFilename($message);
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
		if ( $isValid && strlen($this->_Description) > 40 ) {
			$inMessage .= "Description cannot be more than 40 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Description) <= 1 ) {
			$inMessage .= "Description must be more than 1 character";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_Filetype has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkFiletype(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Filetype) && $this->_Filetype !== '' ) {
			$inMessage .= "{$this->_Filetype} is not a valid value for Filetype";
			$isValid = false;
		}		
		if ( $isValid && $this->_Filetype != '' && !in_array($this->_Filetype, array(self::FILETYPE_NDA, self::FILETYPE_BRIEF, self::FILETYPE_PDF, self::FILETYPE_MUSIC, self::FILETYPE_ASSETS, self::FILETYPE_GUIDELINES)) ) {
			$inMessage .= "Filetype must be one of FILETYPE_NDA, FILETYPE_BRIEF, FILETYPE_PDF, FILETYPE_MUSIC, FILETYPE_ASSETS, FILETYPE_GUIDELINES";
			$isValid = false;
		}		
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
		if ( !is_string($this->_Filename) && $this->_Filename !== '' ) {
			$inMessage .= "{$this->_Filename} is not a valid value for Filename";
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
		if ( !$modified && $this->_SourceSet !== null ) {
			$modified = $modified || $this->_SourceSet->isModified();
		}
		return $modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return mofilmDownloadFile
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
	 * @return mofilmDownloadFile
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
	 * @return mofilmDownloadFile
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
	 * @return mofilmDownloadFile
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
	 * Return the current value of the property $_Lang
	 *
	 * @return string
 	 */
	function getLang() {
		return $this->_Lang;
	}

	/**
	 * Set the object property _Lang to $inLang
	 *
	 * @param string $inLang
	 * @return mofilmDownloadFile
	 */
	function setLang($inLang) {
		if ( $inLang !== $this->_Lang ) {
			$this->_Lang = $inLang;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Filetype
	 * 
	 * @return string
	 * @access public
	 */
	function getFiletype() {
		return $this->_Filetype;
	}
	
	/**
	 * Set $_Filetype to Filetype
	 * 
	 * @param string $inFiletype
	 * @return mofilmDownloadFile
	 * @access public
	 */
	function setFiletype($inFiletype) {
		if ( $inFiletype !== $this->_Filetype ) {
			$this->_Filetype = $inFiletype;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns true if the file exists and is available
	 * 
	 * @return boolean
	 */
	function isAvailable() {
		return file_exists($this->getFileLocation()) && is_readable($this->getFileLocation());
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
	 * Returns just the basename of the filename
	 * 
	 * @return string
	 */
	function getShortFilename() {
		return basename($this->getFilename());
	}
	
	/**
	 * Returns the full path to the file as it exists on the file system
	 * 
	 * @return string
	 */
	function getFileLocation() {
		return mofilmConstants::getDownloadsFolder().DIRECTORY_SEPARATOR.$this->getFilename();
	}
	
	/**
	 * Returns the URI stub for downloading the file
	 * 
	 * @return string
	 */
	function getFileUri() {
		if ( $this->getDownloadHash() ) {
			return '/download/'.$this->getDownloadHash().'/'.basename($this->getFilename());
		} else {
			return '/download/'.$this->getID().'/'.basename($this->getFilename());
		}
	}
	
	/**
	 * Returns a human readable file size, if file is not available returns "0 B"
	 * 
	 * @return string
	 */
	function getFilesize() {
		if ( $this->isAvailable() ) {
			return utilityStringFunction::humanReadableSize(filesize($this->getFileLocation()));
		} else {
			return '0 B';
		}
	}
	
	/**
	 * Set $_Filename to Filename
	 * 
	 * @param string $inFilename
	 * @return mofilmDownloadFile
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
	 * Returns $_DownloadHash, when loaded
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
	 * @return mofilmDownloadFile
	 */
	function setDownloadHash($inDownloadHash) {
		if ( $inDownloadHash !== $this->_DownloadHash ) {
			$this->_DownloadHash = $inDownloadHash;
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
				system::getConfig()->getDatabase('mofilm_content'), 'downloadData', 'downloadID', 'dataName', 'dataValue', $this->getID(), false
			);
			if ( $this->getID() > 0 ) {
				$this->_ParamSet->load();
			}
		}
		return $this->_ParamSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param baseTableParamSet $inObject
	 * @return mofilmDownloadFile
	 */
	function setParamSet(baseTableParamSet $inObject) {
		$this->_ParamSet = $inObject;
		return $this;
	}

	/**
	 * Returns an instance of mofilmDownloadSourceSet, which is lazy loaded upon request
	 *
	 * @return mofilmDownloadSourceSet
	 */
	function getSourceSet() {
		if ( !$this->_SourceSet instanceof mofilmDownloadSourceSet ) {
			$this->_SourceSet = new mofilmDownloadSourceSet($this->getID());
		}
		return $this->_SourceSet;
	}
	
	/**
	 * Set the pre-loaded object to the class
	 *
	 * @param mofilmDownloadSourceSet $inObject
	 * @return mofilmDownloadFile
	 */
	function setSourceSet(mofilmDownloadSourceSet $inObject) {
		$this->_SourceSet = $inObject;
		return $this;
	}
	
	/**
	 * Returns true if its a extenal link else false
	 * 
	 */
	function isExtenalLink($inLink=NULL) {
		if ( $inLink != NULL ) {
		    $res = filter_var($inLink, FILTER_VALIDATE_URL);
		} else {
		    $res = filter_var($this->_Filename, FILTER_VALIDATE_URL);
		}
		
		if ( $res ) {
		    return TRUE;
		} else{
		    return FALSE;
		}
//		$res = filter_var($this->_Filename, FILTER_VALIDATE_URL);
	}
}