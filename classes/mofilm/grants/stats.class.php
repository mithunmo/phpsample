<?php
/**
 * mofilmGrantsStats
 * 
 * Stored in mofilmGrantsStats.class.php
 * 
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmGrants
 * @category mofilmGrantsStats
 * @version $Rev: 1 $
 */


/**
 * mofilmGrantsStats Class
 * 
 * Fetches statistics about the specified grants.
 * 
 * @package mofilm
 * @subpackage mofilmGrants
 * @category mofilmGrantsStats
 */
class mofilmGrantsStats {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Source
	 *
	 * @var mofilmSource
	 * @access protected
	 */
	protected $_Source;
	
	/**
	 * Stores $_CurrentUser
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_CurrentUser;
	
	/**
	 * Stores $_TotalGrants
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TotalGrants;
	
	/**
	 * Stores $_OptionsSet
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_OptionsSet;
	
	const STAT_TOTAL_PENDING = 'Pending';
	const STAT_TOTAL_APPROVED = 'Approved';
	const STAT_TOTAL_REJECTED = 'Rejected';
	
	
	/**
	 * Creates a new stats object
	 * 
	 * @param mofilmSource $inSource
	 * @param mofilmUser $inUser
	 */
	function __construct($inSource = null, $inUser = null) {
		$this->reset();
		if ( $inSource !== null ) {
			$this->setSource($inSource);
		}
		if ( $inUser instanceof mofilmUser ) {
			$this->setCurrentUser($inUser);
		}
	}
	
	/**
	 * Remove cyclical references when object destroyed
	 * 
	 * @return void
	 */
	function __destruct() {
		$this->_Source = null;
		$this->_CurrentUser = null;
		$this->_OptionsSet = null;
	}
	
	
	
	/**
	 * Loads the object with data
	 * 
	 * @return boolean
	 */
	function load() {
		if ( $this->getSource() && $this->getCurrentUser() ) {
		    
			$query = '
				SELECT userMovieGrants.status, count(userMovieGrants.ID) as grantsCount
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.grants ON (grants.ID = userMovieGrants.grantID)
				 WHERE grants.sourceID = :SourceID
				   AND userMovieGrants.status IN ("'.implode('","', $this->getCurrentUser()->getPermissions()->getViewableGrantsStatuses()).'")
				 GROUP BY userMovieGrants.status
				 ORDER BY CAST(userMovieGrants.status AS char) ASC';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':SourceID', $this->getSource()->getID(), PDO::PARAM_INT);
			if ( $oStmt->execute() ) {
				$total = 0;
				foreach ( $oStmt as $row ) {
					$this->setOption($row['status'], $row['grantsCount']);
					$total += $row['grantsCount'];
				}
				$this->setTotalGrants($total);
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
		$this->_Source = null;
		$this->_CurrentUser = null;
		$this->_TotalGrants = 0;
		$this->_OptionsSet = null;
		$this->setModified(false);
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
	 * @return mofilmSourceStats
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns $_Source
	 *
	 * @return mofilmSource
	 */
	function getSource() {
		return $this->_Source;
	}
	
	/**
	 * Set $_Source to $inSource
	 *
	 * @param mofilmSource $inSource
	 * @return mofilmSourceStats
	 */
	function setSource($inSource) {
		if ( $inSource !== $this->_Source ) {
			$this->_Source = $inSource;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_CurrentUser
	 *
	 * @return mofilmUser
	 */
	function getCurrentUser() {
		return $this->_CurrentUser;
	}
	
	/**
	 * Set $_CurrentUser to $inCurrentUser
	 *
	 * @param mofilmUser $inCurrentUser
	 * @return mofilmEventStats
	 */
	function setCurrentUser(mofilmUser $inCurrentUser) {
		if ( $inCurrentUser !== $this->_CurrentUser ) {
			$this->_CurrentUser = $inCurrentUser;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_TotalGrants
	 *
	 * @return integer
	 */
	function getTotalGrants() {
		return $this->_TotalGrants;
	}
	
	/**
	 * Set $_TotalGrants to $inTotalGrants
	 *
	 * @param integer $inTotalGrants
	 * @return mofilmGrantsStats
	 */
	function setTotalGrants($inTotalGrants) {
		if ( $inTotalGrants !== $this->_TotalGrants ) {
			$this->_TotalGrants = $inTotalGrants;
			$this->setModified();
		}
		return $this;
	}

	
	
	/**
	 * Returns $_OptionsSet
	 *
	 * @return baseOptionsSet
	 */
	function getOptionsSet() {
		if ( !$this->_OptionsSet instanceof baseOptionsSet ) {
			$this->_OptionsSet = new baseOptionsSet();
		}
		return $this->_OptionsSet;
	}
	
	/**
	 * Set $_OptionsSet to $inOptionsSet
	 *
	 * @param baseOptionsSet $inOptionsSet
	 * @return mofilmSourceStats
	 */
	function setOptionsSet(baseOptionsSet $inOptionsSet) {
		if ( $inOptionsSet !== $this->_OptionsSet ) {
			$this->_OptionsSet = $inOptionsSet;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the option value for $inOption, or $inDefault if not found
	 * 
	 * @param string $inOption
	 * @param mixed $inDefault
	 * @return mixed
	 */
	function getOption($inOption, $inDefault = null) {
		return $this->getOptionsSet()->getOptions($inOption, $inDefault);
	}
	
	/**
	 * Sets a single option $inOption to $inValue
	 * 
	 * @param string $inOption
	 * @param mixed $inValue
	 * @return imageConvertor
	 */
	function setOption($inOption, $inValue) {
		$this->getOptionsSet()->setOptions(array($inOption => $inValue));
		return $this;
	}
	
	/**
	 * Sets an array of options
	 * 
	 * @param array $inOptions
	 * @return mofilmSourceStats
	 */
	function setOptions(array $inOptions) {
		$this->getOptionsSet()->setOptions($inOptions);
		return $this;
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
	 * Returns the total rejected count
	 *
	 * @return integer
	 */
	function getTotalRejected() {
		return $this->getOption(self::STAT_TOTAL_REJECTED, 0);
	}

	/**
	 * Returns the total pending count
	 *
	 * @return integer
	 */
	function getTotalPending() {
		return $this->getOption(self::STAT_TOTAL_PENDING, 0);
	}
}