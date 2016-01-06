<?php
/**
 * mofilmEventStats
 * 
 * Stored in mofilmEventStats.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmEvent
 * @category mofilmEventStats
 * @version $Rev: 29 $
 */


/**
 * mofilmEventStats Class
 * 
 * Fetches statistics about the specified event.
 * 
 * @package mofilm
 * @subpackage mofilmEvent
 * @category mofilmEventStats
 */
class mofilmEventStats {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Event
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Event;
	
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
	const STAT_TOTAL_BRIEF_DOWNLOADS = 'Brief Downloads';
	
	
	
	/**
	 * Creates a new stats object
	 * 
	 * @param mofilmEvent $inEvent
	 * @param mofilmUser $inUser
	 */
	function __construct(mofilmEvent $inEvent = null, $inUser = null) {
		$this->reset();
		if ( $inEvent !== null ) {
			$this->setEvent($inEvent);
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
		$this->_Event = null;
		$this->_CurrentUser = null;
		$this->_OptionsSet = null;
	}
	
	
	
	/**
	 * Loads the object with data
	 * 
	 * @return boolean
	 */
	function load() {
		if ( $this->getEvent() && $this->getCurrentUser() ) {
			if ( $this->getCurrentUser()->getClientID() > 1 && !$this->getCurrentUser()->getPermissions()->isRoot() ) {
				$sources = $this->getEvent()->getSourceSet()->getObjectIDs();
				if ( count($sources) == 0 ) {
					return false;
				}
				
				$clientSources = $this->getCurrentUser()->getSourceSet()->getObjectIDs();
				foreach ( $sources as $key => $sourceID ) {
					if ( !in_array($sourceID, $clientSources) ) {
						unset($sources[$key]);
					}
				}
				
				if ( count($sources) == 0 ) {
					$sources = $this->getCurrentUser()->getSourceSet()->getObjectIDs();
				}
			} else {
				$sources = $this->getEvent()->getSourceSet()->getObjectIDs();
			}
			
			if ( count($sources) == 0 ) {
				return false;
			}
			
			$query = '
				SELECT movies.status, COUNT(movies.ID) AS movieCount
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movies.ID = movieSources.movieID)
				 WHERE movieSources.sourceID IN ('.implode(', ', $sources).')
				   AND movies.status IN ("'.implode('","', $this->getCurrentUser()->getPermissions()->getViewableVideoStatuses()).'")
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
			
			/*
			 * Fetch brief download count
			 * @todo DR: this has to be calculated on each event/user combination due to
			 *           source permission controls. Might have to cache this as it can
			 *           be slow when iterating a set of events.
			 */
			$query = '
				SELECT COUNT(*) AS dlCount
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userDownloads
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles
				             ON (
				                 userDownloads.downloadID = downloadFiles.ID AND downloadFiles.filetype = "brief"
				             )
				 WHERE userDownloads.sourceID IN ('.implode(', ', $sources).')
				 GROUP BY userDownloads.eventID';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				$cnt = $oStmt->fetchColumn();
				if ( !$cnt ) {
					$cnt = 0;
				}
				$this->setOption(self::STAT_TOTAL_BRIEF_DOWNLOADS, $cnt);
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
		$this->_Event = null;
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
	 * @return mofilmEventStats
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns $_Event
	 *
	 * @return mofilmEvent
	 */
	function getEvent() {
		return $this->_Event;
	}
	
	/**
	 * Set $_Event to $inEvent
	 *
	 * @param mofilmEvent $inEvent
	 * @return mofilmEventStats
	 */
	function setEvent(mofilmEvent $inEvent) {
		if ( $inEvent !== $this->_Event ) {
			$this->_Event = $inEvent;
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
	 * @return mofilmEventStats
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
	 * @return mofilmEventStats
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
	 * @return mofilmEventStats
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

	/**
	 * Returns the total removed count
	 *
	 * @return integer
	 */
	function getTotalBriefDownloads() {
		return $this->getOption(self::STAT_TOTAL_BRIEF_DOWNLOADS, 0);
	}
}