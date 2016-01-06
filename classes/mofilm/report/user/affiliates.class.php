<?php
/**
 * mofilmReportUserAffiliates
 * 
 * Stored in mofilmReportUserAffiliates.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2011
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserAffiliates
 * @version $Rev: 10 $
 */


/**
 * mofilmReportUserAffiliates
 * 
 * Exports affiliates details of winning videos.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserAffiliates
 */
class mofilmReportUserAffiliates extends mofilmReportBase {
	
	const OPTION_AFFILIATE = 'report.affiliate';


	
	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( !$this->getEventID() || !is_numeric($this->getEventID()) ) {
			throw new reportCentreException('Invalid value supplied for EventID');
		}
		if ( !$this->getAffiliate() || strlen($this->getAffiliate()) < 1 ) {
			throw new reportCentreException('Missing an affiliate to report on');
		}
		return true;
	}
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('email', 'Email Address', 40));
		$this->addReportColumn(new reportColumn('Name', 'Name', 30));
		$this->addReportColumn(new reportColumn('movieID', 'Movie ID', 30));
		$this->addReportColumn(new reportColumn('VideoTitle', 'Video Title', 40));
		$this->addReportColumn(new reportColumn('Brand', 'Brand', 20));
		$this->addReportColumn(new reportColumn('link', 'Watch Link', 20));
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = '
			SELECT users.email AS email, CONCAT(users.firstname, " ", users.surname) AS Name,
			       movies.ID AS movieID, movies.shortDesc AS VideoTitle, sources.name AS Brand
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userAffiliates
			        LEFT JOIN '.system::getConfig()->getDatabase('mofilm_content').'.users ON (users.ID = userAffiliates.userID)
			        LEFT JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movies ON (movies.userID=users.ID)
			        LEFT JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movieSources.movieID=movies.ID)
			        LEFT JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (sources.ID=movieSources.sourceID)
			 WHERE users.ID IS NOT NULL
			   AND movies.ID IS NOT NULL
			   AND userAffiliates.affiliate = :Affiliate
			   AND users.enabled = :Enabled
			   AND movies.status = :Status
			   AND sources.eventID = :EventId';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':Affiliate', $this->getAffiliate());
		$oStmt->bindValue(':Enabled', mofilmUserBase::ENABLED_Y);
		$oStmt->bindValue(':Status', mofilmMovie::STATUS_APPROVED);
		$oStmt->bindValue(':EventId', $this->getEventID());

		if ( $oStmt->execute() ) {
			
			$this->getReportData()->query()->beginTransaction();
			
			foreach ( $oStmt as $row ) {
				$row['Name'] = utf8_decode($row['Name']);
				$row['VideoTitle'] = utf8_decode($row['VideoTitle']);

				/* @var mofilmMovie $oMovie */
				$oMovie = mofilmMovieManager::getInstanceByID($row['movieID']);
				$row['link'] = $oMovie->getShortUri(0, true);
				$oMovie = null;
				unset($oMovie);
				
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
		return 'User Affiliates Report';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return
			'Details of all approved, uploaded movies for affiliate '.$this->getAffiliate().
			' for event '.$this->getEvent()->getName();
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
	
	
	
	/**
	 * Returns the affiliate
	 *
	 * @return string
	 */
	function getAffiliate() {
		return $this->getOption(self::OPTION_AFFILIATE, '');
	}
	
	/**
	 * Sets the affiliate to report on
	 *
	 * @param string $inOption
	 * @return reportBase
	 */
	function setAffiliate($inOption) {
		return $this->setOptions(array(self::OPTION_AFFILIATE => $inOption));
	}
}