<?php
/**
 * mofilmMovieLinkView
 *
 * Stored in mofilmMovieLinkView.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMovieLinkView
 * @category mofilmMovieLinkView
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieLinkView Class
 *
 * Provides access to records in mofilm_comms.movieLinkViews
 *
 * Creating a new record:
 * <code>
 * $oMofilmMovieLinkView = new mofilmMovieLinkView();
 * $oMofilmMovieLinkView->setMovieLinkID($inMovieLinkID);
 * $oMofilmMovieLinkView->setTimestamp($inTimestamp);
 * $oMofilmMovieLinkView->setIp($inIp);
 * $oMofilmMovieLinkView->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMofilmMovieLinkView = new mofilmMovieLinkView();
 * </code>
 *
 *
 * Accessing a record by instance:
 * <code>
 * $oMofilmMovieLinkView = mofilmMovieLinkView::getInstance();
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package mofilm
 * @subpackage mofilmMovieLinkView
 * @category mofilmMovieLinkView
 */
class mofilmMovieLinkView implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Container for static instances of mofilmMovieLinkView
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
	 * Stores $_MovieLinkID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_MovieLinkID;

	/**
	 * Stores $_Timestamp
	 *
	 * @var datetime 
	 * @access protected
	 */
	protected $_Timestamp;

	/**
	 * Stores $_Ip
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Ip;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of mofilmMovieLinkView
	 *
	 * @return mofilmMovieLinkView
	 */
	function __construct() {
		$this->reset();
		return $this;
	}

	/**
	 * Creates a new mofilmMovieLinkView containing non-unique properties
	 *
	 * @param integer $inMovieLinkID
	 * @param datetime $inTimestamp
	 * @param string $inIp
	 * @return mofilmMovieLinkView
	 * @static
	 */
	public static function factory($inMovieLinkID = null, $inTimestamp = null, $inIp = null) {
		$oObject = new mofilmMovieLinkView;
		if ( $inMovieLinkID !== null ) {
			$oObject->setMovieLinkID($inMovieLinkID);
		}
		if ( $inTimestamp !== null ) {
			$oObject->setTimestamp($inTimestamp);
		}
		if ( $inIp !== null ) {
			$oObject->setIp($inIp);
		}
		return $oObject;
	}

	/**
	 * Get an instance of mofilmMovieLinkView by primary key
	 *
	 * @return mofilmMovieLinkView
	 * @static
	 */
	public static function getInstance() {
		/**
		 * Check for an existing instance
		 */
		$oObject = new mofilmMovieLinkView();
		return $oObject;
	}

	/**
	 * Returns an array of objects of mofilmMovieLinkView
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		$query = 'SELECT * FROM '.system::getConfig()->getDatabase('mofilm_comms').'.movieLinkViews';
		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();
		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$oObject = new mofilmMovieLinkView();
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
			SELECT movieLinkID, timestamp, ip
			  FROM '.system::getConfig()->getDatabase('mofilm_comms').'.movieLinkViews';

		$where = array();
		if ( $this->_MovieLinkID !== 0 ) {
			$where[] = ' movieLinkID = :MovieLinkID ';
		}
		if ( $this->_Timestamp !== '' ) {
			$where[] = ' timestamp = :Timestamp ';
		}
		if ( $this->_Ip !== '' ) {
			$where[] = ' ip = :Ip ';
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $this->_MovieLinkID !== 0 ) {
				$oStmt->bindValue(':MovieLinkID', $this->_MovieLinkID);
			}
			if ( $this->_Timestamp !== '' ) {
				$oStmt->bindValue(':Timestamp', $this->_Timestamp);
			}
			if ( $this->_Ip !== '' ) {
				$oStmt->bindValue(':Ip', $this->_Ip);
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
		$this->setMovieLinkID((int)$inArray['movieLinkID']);
		$this->setTimestamp($inArray['timestamp']);
		$this->setIp($inArray['ip']);
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
			$this->setUpdateDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('mofilm_comms').'.movieLinkViews
					( movieLinkID, timestamp, ip)
				VALUES
					(:MovieLinkID, :Timestamp, :Ip)
				ON DUPLICATE KEY UPDATE
					movieLinkID=VALUES(movieLinkID),
					timestamp=VALUES(timestamp),
					ip=VALUES(ip)';

				try {
					$oDB = dbManager::getInstance();
					$oStmt = $oDB->prepare($query);
					$oStmt->bindValue(':MovieLinkID', $this->_MovieLinkID);
					$oStmt->bindValue(':Timestamp', $this->_Timestamp);
					$oStmt->bindValue(':Ip', $this->_Ip);

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
			DELETE FROM '.system::getConfig()->getDatabase('mofilm_comms').'.movieLinkViews
			WHERE
				movieLinkID = :MovieLinkID
				AND timestamp = :Timestamp
				AND ip = :Ip
			LIMIT 1';

		try {
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieLinkID', $this->_MovieLinkID);
			$oStmt->bindValue(':Timestamp', $this->_Timestamp);
			$oStmt->bindValue(':Ip', $this->_Ip);
			
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
	 * @return mofilmMovieLinkView
	 */
	function reset() {
		$this->_MovieLinkID = 0;
		$this->_Timestamp = date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue());
		$this->_Ip = '';
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
		$string .= " MovieLinkID[$this->_MovieLinkID] $newLine";
		$string .= " Timestamp[$this->_Timestamp] $newLine";
		$string .= " Ip[$this->_Ip] $newLine";
		return $string;
	}

	/**
	 * Returns object as XML with each property separated by $newLine
	 *
	 * @param string $newLine
	 * @return string
	 */
	function toXml($newLine = "\n") {
		$className = 'mofilmMovieLinkView';
		$xml  = "<$className>$newLine";
		$xml .= "\t<property name=\"MovieLinkID\" value=\"$this->_MovieLinkID\" type=\"integer\" /> $newLine";
		$xml .= "\t<property name=\"Timestamp\" value=\"$this->_Timestamp\" type=\"datetime\" /> $newLine";
		$xml .= "\t<property name=\"Ip\" value=\"$this->_Ip\" type=\"string\" /> $newLine";
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
			$valid = $this->checkMovieLinkID($message);
		}
		if ( $valid ) {
			$valid = $this->checkTimestamp($message);
		}
		if ( $valid ) {
			$valid = $this->checkIp($message);
		}
		return $valid;
	}

	/**
	 * Checks that $_MovieLinkID has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkMovieLinkID(&$inMessage = '') {
		$isValid = true;
		if ( !is_numeric($this->_MovieLinkID) && $this->_MovieLinkID !== 0 ) {
			$inMessage .= "{$this->_MovieLinkID} is not a valid value for MovieLinkID";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Timestamp has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkTimestamp(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Timestamp) && $this->_Timestamp !== '' ) {
			$inMessage .= "{$this->_Timestamp} is not a valid value for Timestamp";
			$isValid = false;
		}
		return $isValid;
	}

	/**
	 * Checks that $_Ip has a valid value
	 *
	 * @return boolean
	 * @access protected
	 */
	protected function checkIp(&$inMessage = '') {
		$isValid = true;
		if ( !is_string($this->_Ip) && $this->_Ip !== '' ) {
			$inMessage .= "{$this->_Ip} is not a valid value for Ip";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Ip) > 15 ) {
			$inMessage .= "Ip cannot be more than 15 characters";
			$isValid = false;
		}
		if ( $isValid && strlen($this->_Ip) <= 1 ) {
			$inMessage .= "Ip must be more than 1 character";
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
	 * @return mofilmMovieLinkView
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
		return ;
	}

	/**
	 * Return value of $_MovieLinkID
	 *
	 * @return integer
	 * @access public
	 */
	function getMovieLinkID() {
		return $this->_MovieLinkID;
	}

	/**
	 * Set $_MovieLinkID to MovieLinkID
	 *
	 * @param integer $inMovieLinkID
	 * @return mofilmMovieLinkView
	 * @access public
	 */
	function setMovieLinkID($inMovieLinkID) {
		if ( $inMovieLinkID !== $this->_MovieLinkID ) {
			$this->_MovieLinkID = $inMovieLinkID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Timestamp
	 *
	 * @return datetime
	 * @access public
	 */
	function getTimestamp() {
		return $this->_Timestamp;
	}

	/**
	 * Set $_Timestamp to Timestamp
	 *
	 * @param datetime $inTimestamp
	 * @return mofilmMovieLinkView
	 * @access public
	 */
	function setTimestamp($inTimestamp) {
		if ( $inTimestamp !== $this->_Timestamp ) {
			$this->_Timestamp = $inTimestamp;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return value of $_Ip
	 *
	 * @return string
	 * @access public
	 */
	function getIp() {
		return $this->_Ip;
	}

	/**
	 * Set $_Ip to Ip
	 *
	 * @param string $inIp
	 * @return mofilmMovieLinkView
	 * @access public
	 */
	function setIp($inIp) {
		if ( $inIp !== $this->_Ip ) {
			$this->_Ip = $inIp;
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
	 * @return mofilmMovieLinkView
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}