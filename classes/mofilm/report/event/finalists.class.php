<?php
/**
 * mofilmReportEventFinalists
 *
 * Stored in mofilmReportEventFinalists.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventFinalists
 * @version $Rev: 736 $
 */


/**
 * mofilmReportEventFinalists
 *
 * mofilmReportEventFinalists report class.
 * 
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventFinalists
 */
class mofilmReportEventFinalists extends mofilmReportBase {

	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( !$this->getEventID() || !is_numeric($this->getEventID()) ) {
			throw new reportCentreException('Invalid value supplied for EventID');
		}
		if ( $this->checkEvent($this->getEventID()) > 0 ) {
			throw new reportCentreException('Event is not closed');
		}
		return true;
	}

	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('Brand', 'Brand', 20));
		$this->addReportColumn(new reportColumn('Rank', 'Rank', 10));
		$this->addReportColumn(new reportColumn('movieID', 'Movie ID', 10));
		$this->addReportColumn(new reportColumn('Filmmaker', 'Filmmaker', 40));
		$this->addReportColumn(new reportColumn('Title', 'Movie Title', 40));
	}

	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
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

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':Event', $this->getEventID());
		$oStmt->bindValue(':Type', mofilmMovieAward::TYPE_FINALIST);

		if ( $oStmt->execute() ) {
			$this->getReportData()->query()->beginTransaction();

			foreach ( $oStmt as $row ) {
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
		return 'Event Finalists';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return 'Report detailing all finalists for '.$this->getEvent()->getName().' by brand and rank.';
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