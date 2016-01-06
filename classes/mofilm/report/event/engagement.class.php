<?php
/**
 * mofilmReportEventEngagement
 * 
 * Stored in mofilmReportEventEngagement.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventEngagement
 * @version $Rev: 10 $
 */


/**
 * mofilmReportEventEngagement
 * 
 * Tracks returning user engagement for each event by closing order. Initial
 * figure is unique users that first uploaded to the event. The purpose is to
 * track how many users are being retained into proceeding events.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventEngagement
 */
class mofilmReportEventEngagement extends mofilmReportBase {
	
	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		return true;
	}
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('event', '', 20));
		foreach ( mofilmEvent::listOfObjects(null, null, null, mofilmEvent::ORDERBY_ENDDATE_ASC) as $oEvent ) {
			if ( $oEvent->getID() == mofilmEvent::EVENT_MOFILM || $oEvent->getID() == mofilmEvent::EVENT_MINIMO ) {
				continue;
			}
			$this->addReportColumn(new reportColumn($oEvent->getID(), $oEvent->getName(), strlen($oEvent->getName()), reportColumn::FIELD_INTEGER));
		}
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		/**
		 * Store all users that have been found to have entered an event
		 * 
		 * @var array
		 */
		$users = array();
		
		/**
		 * Array of event objects in enddate order asc
		 * 
		 * @var array(mofilmEvent)
		 */
		$events = mofilmEvent::listOfObjects(null, null, null, mofilmEvent::ORDERBY_ENDDATE_ASC);
		
		if ( false ) $oEvent = new mofilmEvent();
		foreach ( $events as $oEvent ) {
			if ( $oEvent->getID() == mofilmEvent::EVENT_MOFILM || $oEvent->getID() == mofilmEvent::EVENT_MINIMO ) {
				continue;
			}
			
			/**
			 * An array of only new users for this event
			 * 
			 * @var array
			 */
			$newUsers = array();
			
			/**
			 * Select unique users who uploaded a video to this event
			 * 
			 * @var string
			 */
			$query = '
				SELECT DISTINCT(movies.userID)
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movies.ID = movieSources.movieID)
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (movieSources.sourceID = sources.ID)
				 WHERE sources.eventID = '.$oEvent->getID();
			
			/*
			 * If there are existing users, exclude these from the search for new users
			 */
			if ( count($users) > 0 ) {
				$query .= ' AND movies.userID NOT IN ('.implode(', ', $users).')';
			}
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->setFetchMode(PDO::FETCH_ASSOC);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$users[] = $row['userID'];
					$newUsers[] = $row['userID'];
				}
			}
			$oStmt->closeCursor();
			
			$row = array(
				'event' => $oEvent->getName(),
				$oEvent->getID() => count($newUsers),
			);
			
			/**
			 * Find number of users who re-engaged, and cache the query
			 * 
			 * @var string
			 */
			$query = '
				SELECT COUNT(DISTINCT(movies.userID)) AS count
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movies.ID = movieSources.movieID)
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (movieSources.sourceID = sources.ID)
				 WHERE movies.userID IN ('.implode(',', $newUsers).')
				   AND sources.eventID = :EventID';
			
			$oReEngageStatement = dbManager::getInstance()->prepare($query);
			$oReEngageStatement->setFetchMode(PDO::FETCH_ASSOC);
			
			/*
			 * Foreach event again, work out which new users re-engaged after their first event
			 */
			foreach ( $events as $oEventCompare ) {
				$eventCount = 0;
				if ( $oEventCompare->getID() == mofilmEvent::EVENT_MOFILM || $oEventCompare->getID() == mofilmEvent::EVENT_MINIMO || $oEventCompare->getID() == $oEvent->getID() ) {
					continue;
				}
				
				/*
				 * Only run on events that end at the same time or after the current event
				 */
				if ( count($newUsers) > 0 && $oEventCompare->getEndDate() >= $oEvent->getEndDate() ) {
					$oReEngageStatement->bindValue(':EventID', $oEventCompare->getID(), PDO::PARAM_INT);
					if ( $oReEngageStatement->execute() ) {
						$eventCount = $oReEngageStatement->fetchColumn();
					}
				}
				
				$row[$oEventCompare->getID()] = $eventCount;
			}
			
			$oReEngageStatement->closeCursor();
			
			$this->getReportData()->addRow($row);
		}
		
		/*
		 * Add XLS sums
		 */
		$row = array('event' => 'Column Sums');
		$i = 1;
		foreach ( $events as $oEvent ) {
			if ( $oEvent->getID() == mofilmEvent::EVENT_MOFILM || $oEvent->getID() == mofilmEvent::EVENT_MINIMO ) {
				continue;
			}
			
			++$i;
			$row[$oEvent->getID()] = new reportDataSum(null, $i, $this->getReportData()->sumColumn($oEvent->getID()));
		}
		$this->getReportData()->addRow($row);
		
		
		/**
		 * Calculate total unique users who engaged for sanity checking and cache statement
		 * 
		 * @var string
		 */
		$query = '
			SELECT COUNT(DISTINCT(movies.userID)) AS count
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movies.ID = movieSources.movieID)
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (movieSources.sourceID = sources.ID)
			 WHERE sources.eventID = :EventID';
		
		$oTotalUniqueStatement = dbManager::getInstance()->prepare($query);
		$oTotalUniqueStatement->setFetchMode(PDO::FETCH_ASSOC);
		
		$row = array('event' => 'Actual Unique Users');
		foreach ( $events as $oEvent ) {
			if ( $oEvent->getID() == mofilmEvent::EVENT_MOFILM || $oEvent->getID() == mofilmEvent::EVENT_MINIMO ) {
				continue;
			}
			
			$oTotalUniqueStatement->bindValue(':EventID', $oEvent->getID(), PDO::PARAM_INT);
			if ( $oTotalUniqueStatement->execute() ) {
				$row[$oEvent->getID()] = $oTotalUniqueStatement->fetchColumn();
			}
		}
		$oTotalUniqueStatement->closeCursor();
		
		$this->getReportData()->addRow($row);
		
		return true;
	}

	/**
	 * @see reportBase::getReportName()
	 *
	 * @return string
	 */
	function getReportName() {
		return 'Event Engagement';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return 'This report tracks returning user engagement for each events new users';
	}
	
	/**
	 * @see reportBase::getValidGroupByOptions() 
	 */
	function getValidGroupByOptions() {
		return array();
	}

	/**
	 * @see reportBase::getValidOrderByOptions()
	 */
	function getValidOrderByOptions() {
		return array();
	}
}