<?php
/**
 * mofilmReportUserMoviesByEvent
 * 
 * Stored in mofilmReportUserMoviesByEvent.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserMoviesByEvent
 * @version $Rev: 10 $
 */


/**
 * mofilmReportUserMoviesByEvent
 * 
 * Details all users that have uploaded movies to an event and totals all the movies that
 * they uploaded to the event. Can be run for a specific event or, if not set, all events.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserMoviesByEvent
 */
class mofilmReportUserMoviesByEvent extends mofilmReportBase {
	
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
		$this->addReportColumn(new reportColumn('firstname', 'Firstname', 30));
		$this->addReportColumn(new reportColumn('surname', 'Surname ', 30));
		$this->addReportColumn(new reportColumn('email', 'Email Address ', 30));
		$this->addReportColumn(new reportColumn('country', 'Country', 20));
		
		if ( $this->getEventID() ) {
			$this->addReportColumn(new reportColumn($this->getEvent()->getID(), $this->getEvent()->getName(), strlen($this->getEvent()->getName()), reportColumn::FIELD_INTEGER));
		} else {
			foreach ( mofilmEvent::listOfObjects(null, null, false, mofilmEvent::ORDERBY_STARTDATE) as $oEvent ) {
				$this->addReportColumn(new reportColumn($oEvent->getID(), $oEvent->getName(), strlen($oEvent->getName()), reportColumn::FIELD_INTEGER));
			}
		}
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$events = array();
		if ( $this->getEventID() ) {
			$events[] = $this->getEvent()->getID();
		} else {
			$list = mofilmEvent::listOfObjects(null, null, false, mofilmEvent::ORDERBY_STARTDATE);
			foreach ( $list as $oEvent ) {
				$events[] = $oEvent->getID();
			}
		}
		
		$query = "
			SELECT
				users.ID AS userID,
				users.firstname,
				users.surname, 
				users.email,
				IFNULL(territories.country, 'Unknown') AS country,
				sources.eventID,
				COUNT(*) AS eventCount
				
			FROM 
				".system::getConfig()->getDatabase('mofilm_content').".users
				INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".movies ON (users.ID = movies.userID)
				INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".movieSources ON (movies.ID = movieSources.movieID)
				INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".sources ON (movieSources.sourceID = sources.ID) 
				LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON ( territories.ID = users.territoryID )
				
			WHERE 
				users.`enabled` = :Enabled
				AND movies.active = :Active
				AND users.clientID = 0
				AND sources.eventID IN (".implode(',', $events).")";
		
		if ( $this->getMovieStatus() ) {
			$query .= ' AND movies.status = :Status ';
		}
		$query .= ' GROUP BY sources.eventID, users.ID';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':Enabled', mofilmUserBase::ENABLED_Y);
		$oStmt->bindValue(':Active', mofilmMovieBase::ACTIVE_Y);
		if ( $this->getMovieStatus() ) {
			$oStmt->bindValue(':Status', $this->getMovieStatus());
		}
		$tmpEventInfo = array();
		$tmpUserInfo = array();
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$row['country'] = utilityStringFunction::capitaliseEncodedString($row['country']);
				
				$tmpEventInfo[$row['userID']][$row['eventID']] = $row['eventCount'];
				$tmpUserInfo[] = array(
					'userID' => $row['userID'],
					'firstname' => utf8_decode($row['firstname']),
					'surname' => utf8_decode($row['surname']),
					'email' => $row['email'],
					'country' => utf8_decode($row['country']),
				);
			}
			
			$this->getReportData()->query()->beginTransaction();
			
			foreach ( $tmpUserInfo as $row ) {
				foreach ( $events as $eventID ) {
					if ( array_key_exists($row['userID'], $tmpEventInfo) ) {
						$row[$eventID] = (array_key_exists($eventID, $tmpEventInfo[$row['userID']]) ? $tmpEventInfo[$row['userID']][$eventID] : 0);
					} else {
						$row[$eventID] = 0;
					}
				}
				unset($row['userID']);
				
				$this->getReportData()->addRow($row);
			}
			
			$this->getReportData()->query()->commit();
			
			$totals = array(
				'firstname' => '',
				'surname' => '',
				'email' => '',
				'country' => 'Totals:',
				
			);
			$i = 4;
			foreach ( $events as $eventID ) {
				$totals[$eventID] = new reportDataSum(null, $i, $this->getReportData()->sumColumn($eventID));
				++$i;
			}
			
			$this->getReportData()->addRow($totals);
		}
		$oStmt->closeCursor();
		return true;
	}

	/**
	 * @see reportBase::getReportName()
	 *
	 * @return string
	 */
	function getReportName() {
		return 'User Movies by Event'.($this->getEventID() ? ' '.$this->getEventID() : '');
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		$string = 'Report of users who have uploaded at least one movie to ';
		if ( $this->getEventID() ) {
			$string .= ' the event '.$this->getEvent()->getName();
		} else {
			$string .= ' any event';
		}
		return $string;
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