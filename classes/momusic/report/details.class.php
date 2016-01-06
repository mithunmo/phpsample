<?php
/**
 * mofilmReportEventFinalists
 *
 * Stored in details.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category momusicReportDetails
 * @version $Rev: 736 $
 */


/**
 * momusicReportDetails
 *
 * momusicReportDetails report class.
 * 
 * @package mofilm
 * @subpackage report
 * @category momusicReportDetails
 */
class momusicReportDetails extends mofilmReportBase {

	const OPTION_YEAR = 'report.year';
        const OPTION_FROM_YEAR = 'report.from';
        const OPTION_TO_YEAR = 'report.to';
	const OPTION_MONTH = 'report.month';
        const OPTION_FROM_MONTH = 'report.frommonth';


	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {

/*
		if ( !$this->getEventID() || !is_numeric($this->getEventID()) ) {
			throw new reportCentreException('Invalid value supplied for EventID');
		}
		if ( $this->checkEvent($this->getEventID()) > 0 ) {
			throw new reportCentreException('Event is not closed');
		}

*/
		return true;
	}

	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('trackID', 'track', 10));
		$this->addReportColumn(new reportColumn('licenseID', 'license', 20));
		$this->addReportColumn(new reportColumn('userID', 'user', 10));
		$this->addReportColumn(new reportColumn('trackName', 'trackName', 20));
		$this->addReportColumn(new reportColumn('createDate', 'Date', 10));
                $this->addReportColumn(new reportColumn('downloadDate', 'Date', 10));
		$this->addReportColumn(new reportColumn('musicSource', 'musicSource', 10));
		$this->addReportColumn(new reportColumn('movieID', 'Movie ID', 10));
		$this->addReportColumn(new reportColumn('bestAward', 'Award', 40));
	}

	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {

$query = 'SELECT userLicenses.createDate,userLicenses.createDate as downloaddate, userLicenses.trackName, userLicenses.licenseID,
 			userLicenses.userID, userLicenses.trackID, userLicenses.musicSource ,
		 	IFNULL(movies.ID , "NA" ) as movieID ,
				IFNULL(
					(
					 SELECT IF(position > 0, CONCAT(movieAwards.type, " (", movieAwards.position, ")"), movieAwards.type) AS award
					   FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
					  WHERE movieAwards.movieID = movies.ID
					    AND movieAwards.type IN ("Winner", "Finalist", "Runner Up", "Shortlisted")
					  ORDER BY FIELD(type, "Winner", "Finalist", "Runner Up", "Shortlisted") ASC, position ASC LIMIT 1), ""
				) AS bestAward
			FROM '.system::getConfig()->getDatabase('mofilm_content').'.userLicenses
			LEFT JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieLicenses ON userLicenses.ID = movieLicenses.licenseID
			LEFT JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movies ON movies.ID = movieLicenses.movieID
			WHERE createDate between :fromYear AND :toYear ';

/*
		$query = '
			SELECT sources.name AS Brand, movieAwards.position AS Rank, movieAwards.movieID AS movieID,
			       movies.shortDesc AS Title, CONCAT(users.firstname," ",users.surname) AS Filmmaker
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards 
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (sources.ID = movieAwards.sourceID)
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movies ON (movies.ID = movieAwards.movieID)
			       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.users ON (users.ID = movies.userID)
			 WHERE movieAwards.eventID = :Event
			   AND movieAwards.type = :Type
			 ORDER BY Brand, Rank';
*/
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$oStmt->bindValue(':fromYear' , $this->getFromYear(),  PDO::PARAM_STR);
		$oStmt->bindValue(':toYear' , $this->getToYear(), PDO::PARAM_STR);
                //$oStmt->bindValue(':FromMonth' , $this->getFromMonth(), PDO::PARAM_INT);

		if ( $oStmt->execute() ) {
			$this->getReportData()->query()->beginTransaction();
				$hash = array();
			foreach ( $oStmt as $row ) {

				$val = $row["trackID"].$row["userID"].$row["movieID"];
				if ( in_array( $val , $hash ) ) {
					systemLog::message($val."Exists");
				} else {
					$hash[] = (string)$val;
                                        $date = new DateTime($row["createDate"]);
                                        $row["createDate"] = $date->format('M-y');
					$this->getReportData()->addRow($row);
				}


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
		return 'Music Details';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return 'MOMUSIC Report ';
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

	function getYear() {
		return $this->getOption(self::OPTION_YEAR);
	}

	function getMonth() {
		return $this->getOption(self::OPTION_MONTH);
	}
        
        function getFromMonth(){
            return $this->getOption(self::OPTION_FROM_MONTH);
        }

	function getFromYear() {
		return $this->getOption(self::OPTION_FROM_YEAR);
	}
        
	function getToYear() {
		return $this->getOption(self::OPTION_TO_YEAR);
	}
	/**
	 * Gets the difference of the end date of event and current date
	 *
	 * @param integer $inEventID
	 * @return integer
	 */
	function checkEvent($inEventID) {
		$oEvent = mofilmEvent::getInstance($inEventID);
		$diff = (strtotime($oEvent->getEndDate()) - time());
		
		return $diff;
	}
}