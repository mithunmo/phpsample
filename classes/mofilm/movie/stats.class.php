<?php
/**
 * mofilmMovieStats
 * 
 * Stored in mofilmMovieStats.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmEvent
 * @category mofilmMovieStats
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieStats Class
 * 
 * Fetches statistics about the specified event.
 * 
 * @package mofilm
 * @subpackage mofilmEvent
 * @category mofilmMovieStats
 */
class mofilmMovieStats {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_CurrentUser
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_CurrentUser;
	
	/**
	 * Stores $_TotalMovies
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TotalMovies;
	
	/**
	 * Stores $_OptionsSet
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_OptionsSet;
	
	const STAT_TOTAL_ENCODING= 'Encoding';
	const STAT_TOTAL_PENDING = 'Pending';
	const STAT_TOTAL_REMOVED = 'Removed';
	const STAT_TOTAL_REJECTED = 'Rejected';
	const STAT_TOTAL_APPROVED = 'Approved';
	const STAT_TOTAL_DISPUTED = 'Disputed';
	const STAT_TOTAL_FAILED_ENCODING = 'Failed Encoding';
	
	
	
	/**
	 * Creates a new stats object
	 * 
	 * @param mofilmUser $inUser
	 */
	function __construct($inUser = null) {
		$this->reset();
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
		$this->_CurrentUser = null;
		$this->_OptionsSet = null;
	}
	
	
	
	/**
	 * Loads the object with data
	 * 
	 * @return boolean
	 */
	function load() {
		if ( $this->getCurrentUser() ) {
			if ( $this->getCurrentUser()->getClientID() > mofilmClient::MOFILM && !$this->getCurrentUser()->getPermissions()->isRoot() ) {
				$sources = $this->getCurrentUser()->getSourceSet()->getObjectIDs();
			}
			if ( count($sources) == 0 && ($this->getCurrentUser()->getClientID() != mofilmClient::MOFILM && !$this->getCurrentUser()->getPermissions()->isRoot()) ) {
				return false;
			}
			if ( count($sources) > 0 ) {
				$sources = ' AND movieSources.sourceID IN ('.implode(', ', $sources).')';
			} else {
				$sources = '';
			}
			
			$query = '
				SELECT movies.status, COUNT(movies.ID) AS movieCount
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movies.ID = movieSources.movieID)
				 WHERE movies.status IN ("'.implode('","', $this->getCurrentUser()->getPermissions()->getViewableVideoStatuses()).'")
				  '.$sources.'
				 GROUP BY movies.status
				 ORDER BY CAST(movies.status AS char) ASC';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				$total = 0;
				foreach ( $oStmt as $row ) {
					$this->setOption($row['status'], $row['movieCount']);
					$total += $row['movieCount'];
				}
				$this->setTotalMovies($total);
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
		$this->_CurrentUser = null;
		$this->_TotalMovies = 0;
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
	 * @return mofilmMovieStats
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
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
	 * @return mofilmMovieStats
	 */
	function setCurrentUser(mofilmUser $inCurrentUser) {
		if ( $inCurrentUser !== $this->_CurrentUser ) {
			$this->_CurrentUser = $inCurrentUser;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_TotalMovies
	 *
	 * @return integer
	 */
	function getTotalMovies() {
		return $this->_TotalMovies;
	}
	
	/**
	 * Set $_TotalMovies to $inTotalMovies
	 *
	 * @param integer $inTotalMovies
	 * @return mofilmMovieStats
	 */
	function setTotalMovies($inTotalMovies) {
		if ( $inTotalMovies !== $this->_TotalMovies ) {
			$this->_TotalMovies = $inTotalMovies;
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
	 * @return mofilmMovieStats
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
	 * @return mofilmMovieStats
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
	 * Returns the total disputed count
	 *
	 * @return integer
	 */
	function getTotalDisputed() {
		return $this->getOption(self::STAT_TOTAL_DISPUTED, 0);
	}

	/**
	 * Returns the total encoding count
	 *
	 * @return integer
	 */
	function getTotalEncoding() {
		return $this->getOption(self::STAT_TOTAL_ENCODING, 0);
	}

	/**
	 * Returns the total failed encoding count
	 *
	 * @return integer
	 */
	function getTotalFailedEncoding() {
		return $this->getOption(self::STAT_TOTAL_FAILED_ENCODING, 0);
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

	/**
	 * Returns the total removed count
	 *
	 * @return integer
	 */
	function getTotalRemoved() {
		return $this->getOption(self::STAT_TOTAL_REMOVED, 0);
	}
}