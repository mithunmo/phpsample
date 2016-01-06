<?php
/**
 * mofilmSystemHelpPages
 *
 * Stored in mofilmSystemHelpPages.class.php
 *
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSystemHelpPages
 * @category mofilmSystemHelpPages
 * @version $Rev: 806 $
 */


/**
 * mofilmSystemHelpPages Class
 *
 * Provides access to records in mofilm_system.helpPages
 *
 * Creating a new record:
 * <code>
 * $oMofilmSystemHelpPage = new mofilmSystemHelpPages();
 * $oMofilmSystemHelpPage->setID($inID);
 * $oMofilmSystemHelpPage->setDomainName($inDomainName);
 * $oMofilmSystemHelpPage->setReference($inReference);
 * $oMofilmSystemHelpPage->setTitle($inTitle);
 * $oMofilmSystemHelpPage->setContent($inContent);
 * $oMofilmSystemHelpPage->setLanguage($inLanguage);
 * $oMofilmSystemHelpPage->setCreatedDate($inCreatedDate);
 * $oMofilmSystemHelpPage->setUpdatedDate($inUpdatedDate);
 * $oMofilmSystemHelpPage->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmSystemHelpPage = new mofilmSystemHelpPages($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmSystemHelpPage = new mofilmSystemHelpPages();
 * $oMofilmSystemHelpPage->setID($inID);
 * $oMofilmSystemHelpPage->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmSystemHelpPage = mofilmSystemHelpPages::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmSystemHelpPages
 * @category mofilmSystemHelpPages
 */
class mofilmSystemHelpPages implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmSystemHelpPages
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
	 * Stores the validator for this object
	 *
	 * @var utilityValidator
	 * @access protected
	 */
	protected $_Validator;

	/**
	 * Stores $_ID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_ID;

	/**
	 * Stores $_DomainName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DomainName;

	/**
	 * Stores $_Reference
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Reference;

	/**
	 * Stores $_Title
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Title;

	/**
	 * Stores $_Content
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Content;

	/**
	 * Stores $_Language
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Language;

	/**
	 * Stores $_CreatedDate
	 *
	 * @var systemDateTime
	 * @access protected
	 */
	protected $_CreatedDate;

	/**
	 * Stores $_UpdatedDate
	 *
	 * @var systemDateTime
	 * @access protected
	 */
	protected $_UpdatedDate;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;

	/**
	 * Stores an instance of mofilmSystemHelpPageRelatedSet
	 *
	 * @var mofilmSystemHelpPageRelatedSet
	 * @access protected
	 */
	protected $_RelatedSet;

	/**
	 * Stores an instance of mofilmSystemHelpPageTagSet
	 *
	 * @var mofilmSystemHelpPageTagSet
	 * @access protected
	 */
	protected $_TagSet;



	/**
	 * Returns a new instance of mofilmSystemHelpPages
	 *
	 * @param integer $inID
	 * @return mofilmSystemHelpPages
	 */
	function __construct($inID = null) {
		$this->reset();
		if ( $inID !== null ) {
			$this->setID($inID);
			$this->load();
		}
	}

	/**
	 * Object destructor, used to remove internal object instances
	 *
	 * @return void
	 */
	function __destruct() {
		if ( $this->_Validator instanceof utilityValidator ) {
			$this->_Validator = null;
		}
	}

	/**
	 * Get an instance of mofilmSystemHelpPages by primary key
	 *
	 * @param integer $inID
	 * @return mofilmSystemHelpPages
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
		$oObject = new mofilmSystemHelpPages();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$inID] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get instance of mofilmSystemHelpPages by unique key (reference)
	 *
	 * @param string $inReference
	 * @return mofilmSystemHelpPages
	 * @static
	 */
	public static function getInstanceByReference($inReference) {
		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$inReference]) ) {
			return self::$_Instances[$inReference];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmSystemHelpPages();
		$oObject->setReference($inReference);
		if ( $oObject->load() ) {
			self::$_Instances[$inReference] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmSystemHelpPages
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param string $inDomainName
	 * @param string $searchValue
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30, $inDomainName = null, $searchValue = null) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();
		$where = array();

		$query = '
			SELECT ID, domainName, reference, title, content, language, createdDate, updatedDate
			  FROM ' . system::getConfig()->getDatabase('mofilm_system') . '.helpPages';

		if ( null !== $inDomainName && strlen($inDomainName) > 1 ) {
			$where[] = 'domainName = :DomainName';
			$values[':DomainName'] = $inDomainName;
		}

		if ( null !== $searchValue && strlen($searchValue) > 4 ) {
			$where[] = 'MATCH (reference, title) AGAINST (:searchValue)';
			$values[':searchValue'] = $searchValue;
		}

		if ( count($where) > 0 ) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		if ( $inOffset !== null ) {
			$query .= ' LIMIT ' . $inOffset . ',' . $inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmSystemHelpPages();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

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
			SELECT ID, domainName, reference, title, content, language, createdDate, updatedDate
			  FROM ' . system::getConfig()->getDatabase('mofilm_system') . '.helpPages';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
		}
		if ( $this->_Reference !== '' ) {
			$where[] = ' reference = :Reference ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE ' . implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $this->_ID !== 0 ) {
			$oStmt->bindValue(':ID', $this->getID());
		}
		if ( $this->_Reference !== '' ) {
			$oStmt->bindValue(':Reference', $this->getReference());
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

		return $return;
	}

	/**
	 * Loads a record by array
	 *
	 * @param array $inArray
	 * @return void
	 */
	function loadFromArray(array $inArray) {
		$this->setID((int) $inArray['ID']);
		$this->setDomainName($inArray['domainName']);
		$this->setReference($inArray['reference']);
		$this->setTitle($inArray['title']);
		$this->setContent($inArray['content']);
		$this->setLanguage($inArray['language']);
		$this->setCreatedDate($inArray['createdDate']);
		$this->setUpdatedDate($inArray['updatedDate']);
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
				INSERT INTO ' . system::getConfig()->getDatabase('mofilm_system') . '.helpPages
					( ID, domainName, reference, title, content, language, createdDate, updatedDate)
				VALUES
					(:ID, :DomainName, :Reference, :Title, :Content, :Language, :CreatedDate, :UpdatedDate)
				ON DUPLICATE KEY UPDATE
					domainName=VALUES(domainName),
					title=VALUES(title),
					content=VALUES(content),
					language=VALUES(language),
					createdDate=VALUES(createdDate),
					updatedDate=VALUES(updatedDate)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':DomainName', $this->getDomainName());
				$oStmt->bindValue(':Reference', $this->getReference());
				$oStmt->bindValue(':Title', $this->getTitle());
				$oStmt->bindValue(':Content', $this->getContent());
				$oStmt->bindValue(':Language', $this->getLanguage());
				$oStmt->bindValue(':CreatedDate', $this->getCreatedDate());
				$oStmt->bindValue(':UpdatedDate', $this->getUpdatedDate());

				if ( $oStmt->execute() ) {
					if ( !$this->getID() ) {
						$this->setID($oDB->lastInsertId());
					}
					$this->setModified(false);
					$return = true;
				}
			}

			if ( $this->_RelatedSet instanceof mofilmSystemHelpPageRelatedSet ) {
				$this->_RelatedSet->setHelpPageID($this->getID());
				$this->_RelatedSet->save();
			}
			if ( $this->_TagSet instanceof mofilmSystemHelpPageTagSet ) {
				$this->_TagSet->setHelpPageID($this->getID());
				$this->_TagSet->save();
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
			DELETE FROM ' . system::getConfig()->getDatabase('mofilm_system') . '.helpPages
			WHERE
				ID = :ID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':ID', $this->getID());

		if ( $oStmt->execute() ) {
			$oStmt->closeCursor();

			$this->getRelatedSet()->delete();
			$this->getTagSet()->delete();

			$this->reset();
			return true;
		}

		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mofilmSystemHelpPages
	 */
	function reset() {
		$this->_ID = 0;
		$this->_DomainName = '';
		$this->_Reference = '';
		$this->_Title = '';
		$this->_Content = '';
		$this->_Language = 'en';
		$this->_CreatedDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_UpdatedDate = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());

		$this->_Validator = null;
		$this->_RelatedSet = null;
		$this->_TagSet = null;

		$this->setModified(false);
		$this->setMarkForDeletion(false);
		return $this;
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
	 * Returns the validator, creating one if not set
	 *
	 * @return utilityValidator
	 */
	function getValidator() {
		if ( !$this->_Validator instanceof utilityValidator ) {
			$this->_Validator = new utilityValidator();
		}
		return $this->_Validator;
	}

	/**
	 * Set a pre-built validator instance
	 *
	 * @param utilityValidator $inValidator
	 * @return mofilmSystemHelpPages
	 */
	function setValidator(utilityValidator $inValidator) {
		$this->_Validator = $inValidator;
		return $this;
	}

	/**
	 * Returns true if object is valid, any errors are added to $inMessage
	 *
	 * @param string $inMessage
	 * @return boolean
	 */
	function isValid(&$inMessage = '') {
		$valid = true;

		$oValidator = $this->getValidator();
		$oValidator->reset();
		$oValidator->setData($this->toArray())->setRules($this->getValidationRules());
		if ( !$oValidator->isValid() ) {
			foreach ( $oValidator->getMessages() as $key => $messages ) {
				$inMessage .= "Error with $key: " . implode(', ', $messages) . "\n";
			}
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Returns the array of rules used to validate this object
	 *
	 * @return array
	 */
	function getValidationRules() {
		return array(
			'_ID' => array(
				'number' => array(),
			),
			'_DomainName' => array(
				'string' => array('min' => 1, 'max' => 255,),
			),
			'_Reference' => array(
				'string' => array('min' => 1, 'max' => 100,),
			),
			'_Title' => array(
				'string' => array('min' => 1, 'max' => 255,),
			),
			'_Content' => array(
				'string' => array(),
			),
			'_Language' => array(
				'string' => array('min' => 1, 'max' => 10,),
			),
			'_CreatedDate' => array(
				'dateTime' => array(),
			),
			'_UpdatedDate' => array(
				'dateTime' => array(),
			),
		);
	}


	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;

		if ( !$modified && $this->_RelatedSet instanceof mofilmSystemHelpPageRelatedSet ) {
			$modified = $modified || $this->_RelatedSet->isModified();
		}
		if ( !$modified && $this->_TagSet instanceof mofilmSystemHelpPageTagSet ) {
			$modified = $modified || $this->_TagSet->isModified();
		}

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmSystemHelpPages
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the primaryKey
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->_ID;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * {@link mofilmSystemHelpPages::PRIMARY_KEY_SEPARATOR}.
	 *
	 * @param string $inKey
	 * @return mofilmSystemHelpPages
	 */
	function setPrimaryKey($inKey) {
		list($ID) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setID($ID);
	}

	/**
	 * Return the current value of the property $_ID
	 *
	 * @return integer
	 */
	function getID() {
		return $this->_ID;
	}

	/**
	 * Set the object property _ID to $inID
	 *
	 * @param integer $inID
	 * @return mofilmSystemHelpPages
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_DomainName
	 *
	 * @return string
	 */
	function getDomainName() {
		return $this->_DomainName;
	}

	/**
	 * Set the object property _DomainName to $inDomainName
	 *
	 * @param string $inDomainName
	 * @return mofilmSystemHelpPages
	 */
	function setDomainName($inDomainName) {
		if ( $inDomainName !== $this->_DomainName ) {
			$this->_DomainName = $inDomainName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Reference
	 *
	 * @return string
	 */
	function getReference() {
		return $this->_Reference;
	}

	/**
	 * Set the object property _Reference to $inReference
	 *
	 * @param string $inReference
	 * @return mofilmSystemHelpPages
	 */
	function setReference($inReference) {
		if ( $inReference !== $this->_Reference ) {
			$this->_Reference = $inReference;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Title
	 *
	 * @return string
	 */
	function getTitle() {
		return $this->_Title;
	}

	/**
	 * Set the object property _Title to $inTitle
	 *
	 * @param string $inTitle
	 * @return mofilmSystemHelpPages
	 */
	function setTitle($inTitle) {
		if ( $inTitle !== $this->_Title ) {
			$this->_Title = $inTitle;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Content
	 *
	 * @return string
	 */
	function getContent() {
		return $this->_Content;
	}

	/**
	 * Set the object property _Content to $inContent
	 *
	 * @param string $inContent
	 * @return mofilmSystemHelpPages
	 */
	function setContent($inContent) {
		if ( $inContent !== $this->_Content ) {
			$this->_Content = $inContent;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Language
	 *
	 * @return string
	 */
	function getLanguage() {
		return $this->_Language;
	}

	/**
	 * Set the object property _Language to $inLanguage
	 *
	 * @param string $inLanguage
	 * @return mofilmSystemHelpPages
	 */
	function setLanguage($inLanguage) {
		if ( $inLanguage !== $this->_Language ) {
			$this->_Language = $inLanguage;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_CreatedDate
	 *
	 * @return systemDateTime
	 */
	function getCreatedDate() {
		return $this->_CreatedDate;
	}

	/**
	 * Set the object property _CreatedDate to $inCreatedDate
	 *
	 * @param systemDateTime $inCreatedDate
	 * @return mofilmSystemHelpPages
	 */
	function setCreatedDate($inCreatedDate) {
		if ( $inCreatedDate !== $this->_CreatedDate ) {
			if ( !$inCreatedDate instanceof DateTime ) {
				$inCreatedDate = new systemDateTime($inCreatedDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_CreatedDate = $inCreatedDate;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_UpdatedDate
	 *
	 * @return systemDateTime
	 */
	function getUpdatedDate() {
		return $this->_UpdatedDate;
	}

	/**
	 * Set the object property _UpdatedDate to $inUpdatedDate
	 *
	 * @param systemDateTime $inUpdatedDate
	 * @return mofilmSystemHelpPages
	 */
	function setUpdatedDate($inUpdatedDate) {
		if ( $inUpdatedDate !== $this->_UpdatedDate ) {
			if ( !$inUpdatedDate instanceof DateTime ) {
				$inUpdatedDate = new systemDateTime($inUpdatedDate, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_UpdatedDate = $inUpdatedDate;
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
	 * @return mofilmSystemHelpPages
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}



	/**
	 * Returns the mofilmSystemHelpPageRelatedSet object, loading it if not already set
	 *
	 * @return mofilmSystemHelpPageRelatedSet
	 */
	function getRelatedSet() {
		if ( !$this->_RelatedSet instanceof mofilmSystemHelpPageRelatedSet ) {
			$this->_RelatedSet = new mofilmSystemHelpPageRelatedSet($this->getID());
		}
		return $this->_RelatedSet;
	}

	/**
	 * Set the mofilmSystemHelpPageRelatedSet object directly
	 *
	 * @param mofilmSystemHelpPageRelatedSet $inObject
	 * @return mofilmSystemHelpPages
	 */
	function setRelatedSet(mofilmSystemHelpPageRelatedSet $inObject) {
		$this->_RelatedSet = $inObject;
		return $this;
	}

	/**
	 * Returns the mofilmSystemHelpPageTagSet object, loading it if not already set
	 *
	 * @return mofilmSystemHelpPageTagSet
	 */
	function getTagSet() {
		if ( !$this->_TagSet instanceof mofilmSystemHelpPageTagSet ) {
			$this->_TagSet = new mofilmSystemHelpPageTagSet($this->getID());
		}
		return $this->_TagSet;
	}

	/**
	 * Set the mofilmSystemHelpPageTagSet object directly
	 *
	 * @param mofilmSystemHelpPageTagSet $inObject
	 * @return mofilmSystemHelpPages
	 */
	function setTagSet(mofilmSystemHelpPageTagSet $inObject) {
		$this->_TagSet = $inObject;
		return $this;
	}
}