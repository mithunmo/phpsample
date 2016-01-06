<?php
/**
 * mofilmUserGrantsRating
 *
 * Stored in mofilmUserGrantsRating.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserGrantsRating
 * @category mofilmUserGrantsRating
 * @version $Rev: 840 $
 */


/**
 * mofilmUserGrantsRating Class
 *
 * Provides access to records in mofilm_content.userMovieGrantsRating
 *
 * Creating a new record:
 * <code>
 * $oMofilmUserGrantsRating = new mofilmUserGrantsRating();
 * $oMofilmUserGrantsRating->setID($inID);
 * $oMofilmUserGrantsRating->setGrantID($inGrantID);
 * $oMofilmUserGrantsRating->setUserID($inUserID);
 * $oMofilmUserGrantsRating->setRating($inRating);
 * $oMofilmUserGrantsRating->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmUserGrantsRating = new mofilmUserGrantsRating($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMofilmUserGrantsRating = new mofilmUserGrantsRating();
 * $oMofilmUserGrantsRating->setID($inID);
 * $oMofilmUserGrantsRating->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmUserGrantsRating = mofilmUserGrantsRating::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmUserGrantsRating
 * @category mofilmUserGrantsRating
 */
class mofilmUserGrantsRating implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of mofilmUserGrantsRating
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
	 * Stores $_GrantID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_GrantID;

	/**
	 * Stores $_UserID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_UserID;

	/**
	 * Stores $_Rating
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Rating;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmUserGrantsRating
	 *
	 * @param integer $inID
	 * @return mofilmUserGrantsRating
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
	 * Get an instance of mofilmUserGrantsRating by primary key
	 *
	 * @param integer $inID
	 * @return mofilmUserGrantsRating
	 * @static
	 */
	public static function getInstance($inID) {
		$key = $inID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserGrantsRating();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Get instance of mofilmUserGrantsRating by unique key (grantID)
	 *
	 * @param integer $inGrantID
	 * @param integer $inUserID
	 * @return mofilmUserGrantsRating
	 * @static
	 */
	public static function getInstanceByGrantID($inGrantID, $inUserID) {
		$key = $inGrantID.'.'.$inUserID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new mofilmUserGrantsRating();
		$oObject->setGrantID($inGrantID);
		$oObject->setUserID($inUserID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmUserGrantsRating
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, grantID, userID, rating
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrantsRating';

        
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUserGrantsRating();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

	public static function listOfGrantRatings($inGrantID) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, grantID, userID, rating
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrantsRating where grantID = '.$inGrantID;
        
		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmUserGrantsRating();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}

	public static function averageGrantRating($inGrantID) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT avg(rating) as avgrating, count(rating) as cnt
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrantsRating'
                        . ' where grantID = ' . $inGrantID .' group by grantID';
                
                

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
                $oStmt->execute();
                $row = $oStmt->fetch();

		$oStmt->closeCursor();

		return $row;
	}
        

	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$values = array();

		$query = '
			SELECT ID, grantID, userID, rating
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrantsRating';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}
		if ( $this->_GrantID !== 0 ) {
			$where[] = ' grantID = :GrantID ';
			$values[':GrantID'] = $this->getGrantID();
		}
		if ( $this->_UserID !== 0 ) {
			$where[] = ' userID = :UserID ';
			$values[':UserID'] = $this->getUserID();
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);

		$this->reset();
		if ( $oStmt->execute($values) ) {
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
		$this->setID((int)$inArray['ID']);
		$this->setGrantID((int)$inArray['grantID']);
		$this->setUserID((int)$inArray['userID']);
		$this->setRating((int)$inArray['rating']);
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
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrantsRating
					( ID, grantID, userID, rating )
				VALUES
					( :ID, :GrantID, :UserID, :Rating )
				ON DUPLICATE KEY UPDATE
					rating=VALUES(rating)				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':GrantID', $this->getGrantID());
				$oStmt->bindValue(':UserID', $this->getUserID());
				$oStmt->bindValue(':Rating', $this->getRating());

				if ( $oStmt->execute() ) {
					if ( !$this->getID() ) {
						$this->setID($oDB->lastInsertId());
					}
					$this->setModified(false);
					$return = true;
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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrantsRating
			WHERE
				ID = :ID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':ID', $this->getID());

		if ( $oStmt->execute() ) {
			$oStmt->closeCursor();
			$this->reset();
			return true;
		}

		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mofilmUserGrantsRating
	 */
	function reset() {
		$this->_ID = 0;
		$this->_GrantID = 0;
		$this->_UserID = 0;
		$this->_Rating = 0;
		$this->_Validator = null;
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
	 * @return mofilmUserGrantsRating
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
				$inMessage .= "Error with $key: ".implode(', ', $messages)."\n";
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
			'_GrantID' => array(
				'number' => array(),
			),
			'_UserID' => array(
				'number' => array(),
			),
			'_Rating' => array(
				'number' => array(),
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

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mofilmUserGrantsRating
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
	 * mofilmUserGrantsRating::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return mofilmUserGrantsRating
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
	 * @return mofilmUserGrantsRating
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_GrantID
	 *
	 * @return integer
 	 */
	function getGrantID() {
		return $this->_GrantID;
	}

	/**
	 * Set the object property _GrantID to $inGrantID
	 *
	 * @param integer $inGrantID
	 * @return mofilmUserGrantsRating
	 */
	function setGrantID($inGrantID) {
		if ( $inGrantID !== $this->_GrantID ) {
			$this->_GrantID = $inGrantID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_UserID
	 *
	 * @return integer
 	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set the object property _UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserGrantsRating
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Rating
	 *
	 * @return integer
 	 */
	function getRating() {
		return $this->_Rating;
	}

	/**
	 * Set the object property _Rating to $inRating
	 *
	 * @param integer $inRating
	 * @return mofilmUserGrantsRating
	 */
	function setRating($inRating) {
		if ( $inRating !== $this->_Rating ) {
			$this->_Rating = $inRating;
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
	 * @return mofilmUserGrantsRating
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}