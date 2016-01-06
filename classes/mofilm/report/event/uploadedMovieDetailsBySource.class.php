<?php
/**
 * mofilmReportEventUploadedMovieDetailsBySource
 * 
 * Stored in mofilmReportEventUploadedMovieDetailsBySource.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventUploadedMovieDetailsBySource
 * @version $Rev: 80 $
 */


/**
 * mofilmReportEventUploadedMovieDetailsBySource
 * 
 * Reports on the number of uploads by eventID, by source. This covers only
 * approved uploads that are active between the event start and end dates.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventUploadedMovieDetailsBySource
 */
class mofilmReportEventUploadedMovieDetailsBySource extends mofilmReportBase {
	
	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( !$this->getEventID() || !is_numeric($this->getEventID()) ) {
			throw new reportCentreException('Invalid value supplied for EventID');
		}
		if ( !$this->getSourceID() || !is_numeric($this->getSourceID()) ) {
			throw new reportCentreException('Invalid value supplied for SourceID');
		}
		return true;
	}
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		//Average Star Rating, Country of Origin, Name of Film, Short URL
		$this->addReportColumn(new reportColumn('fullname', 'Name', 20));
		$this->addReportColumn(new reportColumn('email', 'Email', 20));
		$this->addReportColumn(new reportColumn('movieID', 'Movie ID', 10));
		$this->addReportColumn(new reportColumn('title', 'Title', 30));
		$this->addReportColumn(new reportColumn('uri', 'Movie URI', 10));
		$this->addReportColumn(new reportColumn('country', 'Country', 10));
		$this->addReportColumn(new reportColumn('avgRating', 'Rating', 2));
		$this->addReportColumn(new reportColumn('Status', 'Status', 2));
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = "
			SELECT movies.ID AS movieID, movies.shortDesc as title,movies.status as Status, users.email,
			       IF(IFNULL(movieLinks.hash, ''), '', CONCAT('http://mofilm.com/video/', movieLinks.hash)) AS uri,
			       IFNULL(territories.country, 'Unknown') AS country,
			       IFNULL(movies.avgRating, 0) as avgRating,IFNULL(CONCAT(users.firstName,' ',users.surname), '' ) AS fullname
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".movies
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".movieSources ON (movies.ID = movieSources.movieID)
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".users ON (movies.userID = users.ID)
			       LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON (territories.ID = users.territoryID)
			       LEFT JOIN ".system::getConfig()->getDatabase('mofilm_comms').".movieLinks ON (movies.ID = movieLinks.movieID AND movieLinks.senderID = 0)
			 WHERE movieSources.sourceID = :SourceID
			   AND movies.status IN ( 'Approved', 'Pending' )
			   AND movies.active = 'Y'
			 GROUP BY movieID
			 ORDER BY movieID ASC";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':SourceID', $this->getSourceID(), PDO::PARAM_INT);
		
		if ( $oStmt->execute() ) {
			$this->getReportData()->query()->beginTransaction();
			$screeningUrl = system::getConfig()->getParam('mofilm', 'screeningUrl', 'http://mofilm.com/video').'/';
			foreach ( $oStmt as $row ) {
				$row['country'] = utilityStringFunction::capitaliseEncodedString($row['country']);

				if ( !$row['uri'] ) {
					$oObject = mofilmMovieLink::getInstanceByUnqSenderMovie(0, $row['movieID']);
					if ( $oObject->getID() == 0 || !$oObject->getHash() ) {
						$oObject->setMovieID($row['movieID']);
						$oObject->setSenderID(0);
						$oObject->setHash(mofilmUtilities::buildMiniHash($oObject, 6));
						$oObject->save();
					}

					$row['uri'] = $screeningUrl . $oObject->getHash();
				}

				$this->getReportData()->addRow($row);

				$oObject = null;
				unset($oObject);
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
		return 'Event Uploaded Movie Details';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return
			'Report detailing movie details for approved uploads for '.
			$this->getEvent()->getName().
			' and '.
			$this->getSource()->getName();
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