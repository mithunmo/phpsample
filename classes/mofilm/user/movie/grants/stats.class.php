<?php
/**
 * mofilmUserMovieGrantsStats
 *
 * Stored in mofilmUserMovieGrantsStats.class.php
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserMovieGrantsStats
 * @category mofilmUserMovieGrantsStats
 * @version $Rev: 840 $
 */

/**
 * mofilmUserMovieGrantsStats Class
 *
 * Collects statistics for a specific mofilm user grants
 *
 * @package mofilm
 * @subpackage mofilmUserMovieGrantsStats
 * @category mofilmUserMovieGrantsStats
 */
class mofilmUserMovieGrantsStats {

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;

	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;
	
	/**
	 * Stores $_OptionsSet
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_OptionsSet;

	const STAT_TOTAL_APPROVED = 'totalApproved';
	const STAT_TOTAL_REJECTED = 'totalRejected';
	const STAT_TOTAL_PENDING = 'totalPending';
	const STAT_TOTAL_APPLIED = 'totalApplied';



	/**
	 * Creates a new stats instance
	 *
	 * @param integer $inUserID
	 */
	function __construct($inUserID = null) {
		$this->reset();
		if ( $inUserID !== null ) {
			$this->setUserID($inUserID);
			$this->load();
		}
	}

	/**
	 * Loads the object with data
	 *
	 * @return boolean
	 */
	function load() {
		if ( $this->getUserID() ) {
			// 'Approved','Pending','Rejected'
			$query = '
				SELECT
				    (SELECT COUNT(userMovieGrants.id) FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants WHERE status = 2 AND userID = :UserID) AS totalApproved,
					(SELECT COUNT(userMovieGrants.id) FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants WHERE status = 3 AND userID = :UserID) AS totalRejected,
					(SELECT COUNT(userMovieGrants.id) FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants WHERE status = 1 AND userID = :UserID) AS totalPending,
					(SELECT COUNT(userMovieGrants.id) FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants WHERE userID = :UserID) AS totalApplied';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':UserID', $this->getUserID(), PDO::PARAM_INT);
			if ( $oStmt->execute() ) {
				$res = $oStmt->fetchAll();
				if ( is_array($res) && count($res) == 1 ) {
					$this->getOptionsSet()->setOptions($res[0]);
				}
				unset($res);
			}
			$oStmt->closeCursor();
		}
		return true;
	}

	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_UserID = null;
		$this->_OptionsSet = null;
		$this->setModified(false);
	}

	

	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 * @access public
	 */
	function isModified() {
		return $this->_Modified;
	}

	/**
	 * Set $_Modified to $inModified
	 *
	 * @param boolean $inModified
	 * @return mofilmUserMovieGrantsStats
	 * @access public
	 */
	function setModified($inModified = true) {
		if ( $inModified !== $this->_Modified ) {
			$this->_Modified = $inModified;
		}
		return $this;
	}

	/**
	 * Return value of $_User
	 *
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return mofilmUserMovieGrantsStats
	 * @access public
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the baseOptionsSet object
	 *
	 * @return baseOptionsSet
	 * @access public
	 */
	function getOptionsSet() {
		if ( !$this->_OptionsSet instanceof baseOptionsSet ) {
			$this->_OptionsSet = new baseOptionsSet();
		}
		return $this->_OptionsSet;
	}

	/**
	 * Set a new instance of the options set
	 *
	 * @param baseOptionsSet $inOptionsSet
	 * @return mofilmUserMovieGrantsStats
	 * @access public
	 */
	function setOptionsSet(baseOptionsSet $inOptionsSet) {
		if ( $inOptionsSet !== $this->_OptionsSet ) {
			$this->_OptionsSet = $inOptionsSet;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns option named $inOption, or $inDefault if not found
	 *
	 * @param string $inOption
	 * @param mixed $inDefault
	 * @return mixed
	 */
	function getOption($inOption, $inDefault = null) {
		return $this->getOptionsSet()->getOptions($inOption, $inDefault);
	}

	/**
	 * Set the option $inOption to $inValue
	 *
	 * @param string $inOption
	 * @param mixed $inValue
	 * @return mofilmUserMovieGrantsStats
	 */
	function setOption($inOption, $inValue) {
		$this->getOptionsSet()->setOptions(array($inOption => $inValue));
		return $this;
	}

	/**
	 * Returns the total grants applied count
	 *
	 * @return integer
	 */
	function getGrantsAppliedCount() {
		return $this->getOption(self::STAT_TOTAL_APPLIED, 0);
	}
	
	/**
	 * Alias of getGrantsAppliedCount for consistency
	 * 
	 * @return integer
	 */
	function getTotalGrantsApplied() {
		return $this->getMovieCount();
	}

	/**
	 * Returns the total approved count
	 *
	 * @return integer
	 */
	function getTotalApproved() {
		return $this->getOption(self::STAT_TOTAL_APPROVED, 0);
	}

	/**
	 * Returns the total pending count
	 *
	 * @return integer
	 */
	function getTotalPending() {
		return $this->getOption(self::STAT_TOTAL_PENDING, 0);
	}

	/**
	 * Returns the total rejected count
	 *
	 * @return integer
	 */
	function getTotalRejected() {
		return $this->getOption(self::STAT_TOTAL_REJECTED, 0);
	}
}