<?php
/**
 * mofilmReportUserApprovedVideos
 * 
 * Stored in mofilmReportUserApprovedVideos.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserApprovedVideos
 * @version $Rev: 302 $
 */


/**
 * mofilmReportUserApprovedVideos
 * 
 * Reports on all approved videos with full user details.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserApprovedVideos
 */
class mofilmReportUserApprovedVideos extends mofilmReportBase {
	
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
		$this->addReportColumn(new reportColumn('event', 'Event', 20));
		$this->addReportColumn(new reportColumn('brand', 'Brand', 20));
		$this->addReportColumn(new reportColumn('movieID', 'Movie ID', 10, reportColumn::FIELD_INTEGER));
		$this->addReportColumn(new reportColumn('movieTitle', 'Movie Title', 20));
		$this->addReportColumn(new reportColumn('userID', 'User ID', 10, reportColumn::FIELD_INTEGER));
		$this->addReportColumn(new reportColumn('fullname', 'Name', 20));
		$this->addReportColumn(new reportColumn('emailAddress', 'Email Address', 20));
		$this->addReportColumn(new reportColumn('address1', 'Address 1', 20));
		$this->addReportColumn(new reportColumn('address2', 'Address 2', 20));
		$this->addReportColumn(new reportColumn('city', 'City', 20));
		$this->addReportColumn(new reportColumn('postcode', 'ZIP / Postcode', 10));
		$this->addReportColumn(new reportColumn('country', 'Country', 20));
		$this->addReportColumn(new reportColumn('phone', 'Phone #', 10));
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
			$list = mofilmEvent::listOfObjects(null, null, false, mofilmEvent::ORDERBY_NAME);
			foreach ( $list as $oEvent ) {
				$events[] = $oEvent->getID();
			}
		}
		
		$query = "
			SELECT
				events.name AS event,
				sources.name AS brand, 
				movies.ID AS movieID, 
				movies.shortDesc AS movieTitle,
				movies.userID,
				CONCAT(users.firstName,' ',users.surname) AS fullname, 
				users.email AS emailAddress, 
				(SELECT userData.paramValue FROM ".system::getConfig()->getDatabase('mofilm_content').".userData WHERE userID=users.ID AND paramName='Address1') AS address1,
				(SELECT userData.paramValue FROM ".system::getConfig()->getDatabase('mofilm_content').".userData WHERE userID=users.ID AND paramName='Address2') AS address2,
				(SELECT userData.paramValue FROM ".system::getConfig()->getDatabase('mofilm_content').".userData WHERE userID=users.ID AND paramName='City') AS city,
				(SELECT userData.paramValue FROM ".system::getConfig()->getDatabase('mofilm_content').".userData WHERE userID=users.ID AND paramName='Postcode') AS postcode,
				IFNULL(territories.country, 'Unknown') AS country,
				(SELECT userData.paramValue FROM ".system::getConfig()->getDatabase('mofilm_content').".userData WHERE userID=users.ID AND paramName='Phone') AS phone
			
			FROM ".system::getConfig()->getDatabase('mofilm_content').".movies 
				LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".users ON (users.ID=movies.userID) 
				LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".movieSources ON (movieSources.movieID=movies.ID) 
				LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON (territories.ID = users.territoryID)
				LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".sources ON (sources.ID = movieSources.sourceID)
				LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".events ON (events.ID = sources.eventID)

			WHERE
				sources.eventID IN (".implode(',', $events).")
				AND movies.status = 'Approved'";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':Status', mofilmMovieBase::STATUS_APPROVED);
		
		if ( $oStmt->execute() ) {
			
			$this->getReportData()->query()->beginTransaction();
			
			foreach ( $oStmt as $row ) {
				$row['country'] = utilityStringFunction::capitaliseEncodedString($row['country']);
				$row['movieTitle'] = utf8_decode($row['movieTitle']);
				$row['fullname'] = utf8_decode($row['fullname']);
				$row['address1'] = utf8_decode($row['address1']);
				$row['address2'] = utf8_decode($row['address2']);
				$row['city'] = utf8_decode($row['city']);
				$row['postcode'] = utf8_decode($row['postcode']);
				$row['phone'] = (empty($row['phone']) ? '' : $row['phone']);
				
				$this->getReportData()->addRow($row);
			}
			
			$this->getReportData()->query()->commit();
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
		return 'User Approved Movies'.($this->getEventID() ? ' '.$this->getEventID() : '');
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		$string = 'Report of all approved user videos for ';
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