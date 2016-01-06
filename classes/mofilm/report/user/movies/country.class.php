<?php
/**
 * mofilmReportUserMoviesCountry
 *
 * Stored in mofilmReportUserMoviesCountry.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserMoviesCountry
 * @version $Rev: 736 $
 */


/**
 * mofilmReportUserMoviesCountry
 *
 * mofilmReportUserMoviesCountry report class.
 * 
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserMoviesCountry
 */
class mofilmReportUserMoviesCountry extends mofilmReportBase {

	const OPTION_TERRITORY_ID = 'report.territory.id';
	
	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( !$this->getTerritoryID() || ($this->getTerritoryID() && !is_numeric($this->getTerritoryID())) || $this->getTerritoryID() == 0 ) {
			throw new reportCentreException('Invalid territory selected for report');
		}
		return true;
	}

	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('movieID', 'Movie ID', 10));
		$this->addReportColumn(new reportColumn('Title', 'Movie Title', 40));
		$this->addReportColumn(new reportColumn('Name', 'Name', 30));
		$this->addReportColumn(new reportColumn('Email', 'Email Address', 30));
		$this->addReportColumn(new reportColumn('Phone', 'Phone Number', 20));
		$this->addReportColumn(new reportColumn('City', 'City', 20));
		$this->addReportColumn(new reportColumn('Country', 'Country', 20));
		$this->addReportColumn(new reportColumn('Event', 'Event', 20));
		$this->addReportColumn(new reportColumn('Brand', 'Brand', 20));
		$this->addReportColumn(new reportColumn('Rating', 'Rating', 10));
		$this->addReportColumn(new reportColumn('Preview', 'Preview', 20));
		$this->addReportColumn(new reportColumn('bestAward', 'Best Award', 20));
	}

	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = '
			SELECT
				movies.ID AS movieID,
				movies.shortDesc AS Title,
				users.email AS Email,
				territories.country AS Country,
				events.name AS Event,
				sources.name AS Brand,

				IFNULL(CONCAT(users.firstname, " ", users.surname), "") AS Name,
				IFNULL((SELECT userData.paramValue FROM '.system::getConfig()->getDatabase('mofilm_content').'.userData WHERE userID=users.ID AND paramName="Phone"), "") AS Phone,
				IFNULL((SELECT userData.paramValue FROM '.system::getConfig()->getDatabase('mofilm_content').'.userData WHERE userID=users.ID AND paramName="City"), "") AS City,
				IFNULL(movies.avgRating, 0) AS Rating,
				IFNULL(
					(
					 SELECT IF(position > 0, CONCAT(movieAwards.type, " (", movieAwards.position, ")"), movieAwards.type) AS award
					   FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieAwards
					  WHERE movieAwards.movieID = movies.ID
					    AND movieAwards.type IN ("Winner", "Finalist", "Runner Up", "Shortlisted")
					  ORDER BY FIELD(type, "Winner", "Finalist", "Runner Up", "Shortlisted") ASC, position ASC LIMIT 1), ""
				) AS bestAward

			FROM '. system::getConfig()->getDatabase('mofilm_content').'.movies
			     INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.users ON (movies.userID = users.ID)
			     INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.territories ON (users.territoryID = territories.ID)
			     INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieSources ON (movies.ID = movieSources.movieID)
			     INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.sources ON (sources.ID = movieSources.sourceID)
			     INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.events ON (events.ID = sources.eventID)

			WHERE movies.active = :Active
			  AND movies.status = :Status
			  AND users.enabled = :Enabled
			  AND territories.ID = :TerritoryID

			ORDER BY movies.ID DESC';
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':Active', mofilmMovieBase::ACTIVE_Y);
		$oStmt->bindValue(':Status', mofilmMovieBase::STATUS_APPROVED);
		$oStmt->bindValue(':Enabled', mofilmUserBase::ENABLED_Y);
		$oStmt->bindValue(':TerritoryID', $this->getTerritoryID());
		
		if ( $oStmt->execute() ) {
			$this->getReportData()->query()->beginTransaction();

			foreach ( $oStmt as $row ) {
				$row['Preview'] = $this->getPreviewLinkByMovieID($row['movieID']);
				$row['Country'] = utilityStringFunction::capitaliseEncodedString($row['Country']);
				
				$this->getReportData()->addRow($row);
			}

			$this->getReportData()->query()->commit();
		}
		$oStmt->closeCursor();

		return true;
	}

	/**
	 * Gets the previewLink per movieID
	 *
	 * @param integer $inMovieID
	 * @return string
	 */
	function getPreviewLinkByMovieID($inMovieID) {
		/* @var mofilmMovie $oMovie */
		$oMovie = mofilmMovieManager::getInstanceByID($inMovieID);
		$link = $oMovie->getShortUri(0, true);
		$oMovie = null;
		unset($oMovie);

		return $link;
	}


	/**
	 * @see reportBase::getReportName()
	 *
	 * @return string
	 */
	function getReportName() {
		return 'User Movies From '.$this->getTerritory()->getShortName();
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return 'Report detailing all users that have an approved, uploaded movie who are from '.$this->getTerritory()->getCountry().'.';
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
	 * Returns the territory to search on
	 *
	 * @return integer
	 */
	function getTerritoryID() {
		return $this->getOption(self::OPTION_TERRITORY_ID);
	}

	/**
	 * Returns the territory object
	 *
	 * @return mofilmTerritory
	 */
	function getTerritory() {
		return mofilmTerritory::getInstance($this->getTerritoryID());
	}

	/**
	 * Sets the territory id to search on
	 *
	 * @param integer $inOption
	 * @return reportBase
	 */
	function setTerritoryID($inOption) {
		return $this->setOptions(array(self::OPTION_TERRITORY_ID => $inOption));
	}
}