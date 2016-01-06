<?php
/**
 * mofilmUserStats
 *
 * Stored in stats.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmUserStats
 * @category mofilmUserStats
 * @version $Rev: 10 $
 */


/**
 * mofilmUserStats Class
 *
 * Collects statistics about all MOFILM users.
 *
 * @package mofilm
 * @subpackage mofilmUserStats
 * @category mofilmUserStats
 */
class mofilmUserStats {

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	/**
	 * Stores $_TotalUsers
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TotalUsers;
	
	/**
	 * Stores $_OptionsSet
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_OptionsSet;

	const STAT_TOTAL_ENABLED = 'Enabled Users';
	const STAT_TOTAL_REGISTERED = 'Registered Users';
	const STAT_TOTAL_DISABLED = 'Disabled Users';
	const STAT_TOTAL_NEW_REGISTERED_USERS = 'New Registered Users';
	const STAT_TOTAL_REFERAL_REGISTERED_USERS = 'Referal Registered Users';

	/**
	 * Creates a new stats instance
	 */
	function __construct() {
		$this->reset();
	}

	/**
	 * Loads the object with data
	 *
	 * @return boolean
	 */
	function load() {
		$yesterdayStart = date('Y-m-d 00:00:00', strtotime('yesterday'));
		$yesterdayEnd = date('Y-m-d 23:59:59', strtotime('yesterday'));
		
		$query = '
			SELECT
				(SELECT COUNT(*) FROM '.system::getConfig()->getDatabase('mofilm_content').'.users) AS totalUsers,
				(SELECT COUNT(*) FROM '.system::getConfig()->getDatabase('mofilm_content').'.users WHERE enabled = "Y") AS totalEnabled,
				(SELECT COUNT(*) FROM '.system::getConfig()->getDatabase('mofilm_content').'.users WHERE enabled != "Y") AS totalDisabled,
				(SELECT COUNT(*) FROM '.system::getConfig()->getDatabase('mofilm_content').'.users WHERE (registered IS NOT NULL AND registered != "0000-00-00 00:00:00")) AS totalRegistered,
				(
					SELECT COUNT(*)
					  FROM '.system::getConfig()->getDatabase('mofilm_content').'.users
					 WHERE (registered IS NOT NULL AND registered BETWEEN '.dbManager::getInstance()->quote($yesterdayStart).' AND '.dbManager::getInstance()->quote($yesterdayEnd).')
				) AS totalNewRegistered,
				(SELECT COUNT(*) 
					FROM '.system::getConfig()->getDatabase('mofilm_content').'.users 
					LEFT JOIN '.system::getConfig()->getDatabase('mofilm_content').'.userData ON (users.ID=userData.userID) 
					WHERE userData.paramName = "Refer" AND users.enabled = "Y" ) AS totalReferalRegistered';

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute() ) {
			$res = $oStmt->fetchAll();
			if ( is_array($res) && count($res) == 1 ) {
				$row = $res[0];
				$this->setTotalUsers($row['totalUsers']);
				$this->getOptionsSet()->setOptions(
					array(
						self::STAT_TOTAL_ENABLED => $row['totalEnabled'],
						self::STAT_TOTAL_DISABLED => $row['totalDisabled'],
						self::STAT_TOTAL_REGISTERED => $row['totalRegistered'],
						self::STAT_TOTAL_NEW_REGISTERED_USERS => $row['totalNewRegistered'],
						self::STAT_TOTAL_REFERAL_REGISTERED_USERS => $row['totalReferalRegistered'],
					)
				);
			}
			unset($res);
		}
		$oStmt->closeCursor();
		
		return true;
	}

	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_TotalUsers = 0;
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
	 * @return mofilmUserStats
	 * @access public
	 */
	function setModified($inModified = true) {
		if ( $inModified !== $this->_Modified ) {
			$this->_Modified = $inModified;
		}
		return $this;
	}

	/**
	 * Returns $_TotalUsers
	 *
	 * @return integer
	 */
	function getTotalUsers() {
		return $this->_TotalUsers;
	}
	
	/**
	 * Set $_TotalUsers to $inTotalUsers
	 *
	 * @param integer $inTotalUsers
	 * @return mofilmUserStats
	 */
	function setTotalUsers($inTotalUsers) {
		if ( $inTotalUsers !== $this->_TotalUsers ) {
			$this->_TotalUsers = $inTotalUsers;
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
	 * @return mofilmUserStats
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
	 * @return mofilmUserStats
	 */
	function setOption($inOption, $inValue) {
		$this->getOptionsSet()->setOptions(array($inOption => $inValue));
		return $this;
	}
	
	/**
	 * Returns the total approved count
	 *
	 * @return integer
	 */
	function getTotalEnabled() {
		return $this->getOption(self::STAT_TOTAL_ENABLED, 0);
	}

	/**
	 * Returns the total awaiting count
	 *
	 * @return integer
	 */
	function getTotalDisabled() {
		return $this->getOption(self::STAT_TOTAL_DISABLED, 0);
	}

	/**
	 * Returns the total rejected count
	 *
	 * @return integer
	 */
	function getTotalRegistered() {
		return $this->getOption(self::STAT_TOTAL_REGISTERED, 0);
	}
}