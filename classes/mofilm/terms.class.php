<?php
/**
 * mofilmTerms
 * 
 * Stored in mofilmTerms.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmTerms
 * @category mofilmTerms
 * @version $Rev: 10 $
 */


/**
 * mofilmTerms Class
 * 
 * Provides access to records in mofilm_content.terms
 * 
 * Creating a new record:
 * <code>
 * $oMofilmTerm = new mofilmTerms();
 * $oMofilmTerm->setID($inID);
 * $oMofilmTerm->setReplacesTerms($inReplacesTerms);
 * $oMofilmTerm->setVersion($inVersion);
 * $oMofilmTerm->setDescription($inDescription);
 * $oMofilmTerm->setHtmlLink($inHtmlLink);
 * $oMofilmTerm->setPdfLink($inPdfLink);
 * $oMofilmTerm->save();
 * </code>
 * 
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmTerm = new mofilmTerms($inID);
 * </code>
 * 
 * Access by manually calling load:
 * <code>
 * $oMofilmTerm = new mofilmTerms();
 * $oMofilmTerm->setID($inID);
 * $oMofilmTerm->load();
 * </code>
 * 
 * Accessing a record by instance:
 * <code>
 * $oMofilmTerm = mofilmTerms::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 * 
 * @package mofilm
 * @subpackage mofilmTerms
 * @category mofilmTerms
 */
class mofilmTerms implements systemDaoInterface, systemDaoValidatorInterface {
	
	/**
	 * Container for static instances of mofilmTerms
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
	 * Stores $_ReplacesTerms
	 * 
	 * @var integer 
	 * @access protected
	 */
	protected $_ReplacesTerms;
			
	/**
	 * Stores $_Version
	 * 
	 * @var datetime 
	 * @access protected
	 */
	protected $_Version;
			
	/**
	 * Stores $_Description
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_Description;
			
	/**
	 * Stores $_HtmlLink
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_HtmlLink;
			
	/**
	 * Stores $_PdfLink
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_PdfLink;
			
	
	
	/**
	 * Returns a new instance of mofilmTerms
	 * 
	 * @param integer $inID
	 * @return mofilmTerms
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
	 * Creates a new mofilmTerms containing non-unique properties
	 * 
	 * @param integer $inReplacesTerms
	 * @param datetime $inVersion
	 * @param string $inDescription
	 * @param string $inHtmlLink
	 * @param string $inPdfLink
	 * @return mofilmTerms
	 * @static 
	 */
	public static function factory($inReplacesTerms = null, $inVersion = null, $inDescription = null, $inHtmlLink = null, $inPdfLink = null) {
		$oObject = new mofilmTerms;
		if ( $inReplacesTerms !== null ) {
			$oObject->setReplacesTerms($inReplacesTerms);
		}
		if ( $inVersion !== null ) {
			$oObject->setVersion($inVersion);
		}
		if ( $inDescription !== null ) {
			$oObject->setDescription($inDescription);
		}
		if ( $inHtmlLink !== null ) {
			$oObject->setHtmlLink($inHtmlLink);
		}
		if ( $inPdfLink !== null ) {
			$oObject->setPdfLink($inPdfLink);
		}
		return $oObject;
	}
	
	/**
	 * Get an instance of mofilmTerms by primary key
	 * 
	 * @param integer $inID
	 * @return mofilmTerms
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
		$oObject = new mofilmTerms();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
			return $oObject;
		}
		return $oObject;
	}
				
	/**
	 * Returns an array of objects of mofilmTerms
	 * 
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static 
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.terms';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}
		
		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmTerms();
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
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_content').'.terms';
		
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
		$this->setReplacesTerms((int)$inArray['replacesTerms']);
		$this->setVersion($inArray['version']);
		$this->setDescription($inArray['description']);
		$this->setHtmlLink($inArray['htmlLink']);
		$this->setPdfLink($inArray['pdfLink']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.terms
					( ID, replacesTerms, version, description, htmlLink, pdfLink)
				VALUES 
					(:ID, :ReplacesTerms, :Version, :Description, :HtmlLink, :PdfLink)
				ON DUPLICATE KEY UPDATE
					replacesTerms=VALUES(replacesTerms),
					version=VALUES(version),
					description=VALUES(description),
					htmlLink=VALUES(htmlLink),
					pdfLink=VALUES(pdfLink)';
		
				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':ID', $this->_ID);
					$oStmt->bindValue(':ReplacesTerms', $this->_ReplacesTerms);
					$oStmt->bindValue(':Version', date(system::getConfig()->getDatabaseDatetimeFormat()));
					$oStmt->bindValue(':Description', $this->_Description);
					$oStmt->bindValue(':HtmlLink', $this->_HtmlLink);
					$oStmt->bindValue(':PdfLink', $this->_PdfLink);
								
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
		DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.terms
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
	 * @return mofilmTerms
	 */
	function reset() {
		$this->_ID = 0;
		$this->_ReplacesTerms = null;
		$this->_Version = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Description = '';
		$this->_HtmlLink = null;
		$this->_PdfLink = null;
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
		$string .= " ReplacesTerms[$this->_ReplacesTerms] $newLine";
		$string .= " Version[$this->_Version] $newLine";
		$string .= " Description[$this->_Description] $newLine";
		$string .= " HtmlLink[$this->_HtmlLink] $newLine";
		$string .= " PdfLink[$this->_PdfLink] $newLine";
		return $string;
	}
	
	/**
	 * Returns object as XML with each property separated by $newLine
	 * 
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmTerms';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"ID\" value=\"$this->_ID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"ReplacesTerms\" value=\"$this->_ReplacesTerms\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Version\" value=\"$this->_Version\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Description\" value=\"$this->_Description\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"HtmlLink\" value=\"$this->_HtmlLink\" type=\"string\" /> $newLine";
		$xml .= "\t<property name=\"PdfLink\" value=\"$this->_PdfLink\" type=\"string\" /> $newLine";
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
			$valid = $this->checkReplacesTerms($message);
		}
		if ( $valid ) {
			$valid = $this->checkVersion($message);
		}
		if ( $valid ) {
			$valid = $this->checkDescription($message);
		}
		if ( $valid ) {
			$valid = $this->checkHtmlLink($message);
		}
		if ( $valid ) {
			$valid = $this->checkPdfLink($message);
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
	 * Checks that $_ReplacesTerms has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkReplacesTerms(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_ReplacesTerms) && $this->_ReplacesTerms !== null && $this->_ReplacesTerms !== 0 ) {
			$inMessage .= "{$this->_ReplacesTerms} is not a valid value for ReplacesTerms";
			$isValid = false;
		}
		return $isValid;
	}
		
	/**
	 * Checks that $_Version has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkVersion(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Version) && $this->_Version !== '' ) {
			$inMessage .= "{$this->_Version} is not a valid value for Version";
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
				
		return $isValid;
	}
		
	/**
	 * Checks that $_HtmlLink has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkHtmlLink(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_HtmlLink) && $this->_HtmlLink !== null && $this->_HtmlLink !== '' ) {
			$inMessage .= "{$this->_HtmlLink} is not a valid value for HtmlLink";
			$isValid = false;
		}		
				
		return $isValid;
	}
		
	/**
	 * Checks that $_PdfLink has a valid value
	 * 
	 * @return boolean
	 * @access protected
	 */
	protected function checkPdfLink(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_PdfLink) && $this->_PdfLink !== null && $this->_PdfLink !== '' ) {
			$inMessage .= "{$this->_PdfLink} is not a valid value for PdfLink";
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
	 * @return mofilmTerms
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
	 * @return mofilmTerms
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
	 * Return value of $_ReplacesTerms
	 * 
	 * @return integer
	 * @access public
	 */
	function getReplacesTerms() {
		return $this->_ReplacesTerms;
	}
	
	/**
	 * Returns the older terms that were replaced by this set of terms
	 * 
	 * @return mofilmTerms
	 */
	function getOldTerms() {
		return mofilmTerms::getInstance($this->getReplacesTerms());
	}
	
	/**
	 * Set $_ReplacesTerms to ReplacesTerms
	 * 
	 * @param integer $inReplacesTerms
	 * @return mofilmTerms
	 * @access public
	 */
	function setReplacesTerms($inReplacesTerms) {
		if ( $inReplacesTerms !== $this->_ReplacesTerms ) {
			$this->_ReplacesTerms = $inReplacesTerms;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_Version
	 * 
	 * @return datetime
	 * @access public
	 */
	function getVersion() {
		return $this->_Version;
	}
	
	/**
	 * Set $_Version to Version
	 * 
	 * @param datetime $inVersion
	 * @return mofilmTerms
	 * @access public
	 */
	function setVersion($inVersion) {
		if ( $inVersion !== $this->_Version ) {
			$this->_Version = $inVersion;
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
	 * @return mofilmTerms
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
	 * Return value of $_HtmlLink
	 * 
	 * @return string
	 * @access public
	 */
	function getHtmlLink() {
		return $this->_HtmlLink;
	}
	
	/**
	 * Set $_HtmlLink to HtmlLink
	 * 
	 * @param string $inHtmlLink
	 * @return mofilmTerms
	 * @access public
	 */
	function setHtmlLink($inHtmlLink) {
		if ( $inHtmlLink !== $this->_HtmlLink ) {
			$this->_HtmlLink = $inHtmlLink;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_PdfLink
	 * 
	 * @return string
	 * @access public
	 */
	function getPdfLink() {
		return $this->_PdfLink;
	}
	
	/**
	 * Set $_PdfLink to PdfLink
	 * 
	 * @param string $inPdfLink
	 * @return mofilmTerms
	 * @access public
	 */
	function setPdfLink($inPdfLink) {
		if ( $inPdfLink !== $this->_PdfLink ) {
			$this->_PdfLink = $inPdfLink;
			$this->setModified();
		}
		return $this;
	}
}