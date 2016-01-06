<?php
/**
 * mofilmReportUserInactive
 *
 * Stored in mofilmReportUserInactive.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserInactive
 * @version $Rev: 736 $
 */


/**
 * mofilmReportUserInactive
 *
 * mofilmReportUserInactive report class.
 * 
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserInactive
 */
class mofilmReportUserInactive extends reportBase {

	const OPTION_TERRITORY_ID = 'report.territory.id';

	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( $this->getTerritoryID() && !is_numeric($this->getTerritoryID()) ) {
			throw new reportCentreException('Invalid territory selected for report');
		}
		return true;
	}

	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('userID', 'userID', 20));	
		$this->addReportColumn(new reportColumn('Name', 'Name', 20));
		$this->addReportColumn(new reportColumn('email', 'Email Address', 20));
		$this->addReportColumn(new reportColumn('territory', 'Territory', 20));
		$this->addReportColumn(new reportColumn('RegisteredDate', 'Registered Date', 20));
		$this->addReportColumn(new reportColumn('TotalDownloads', 'Total Downloads', 10));
		$this->addReportColumn(new reportColumn('RecentFile', 'Recent Download', 20));
	}

	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = '
			SELECT
				users.ID AS userID,
				users.registered as RegisteredDate,
				users.email,
				IFNULL( CONCAT(users.firstname, " ", users.surname), "") AS Name,
				IFNULL( (SELECT COUNT(*) FROM '.system::getConfig()->getDatabase('mofilm_content').'.userDownloads WHERE users.ID = userDownloads.userID), "") AS TotalDownloads,
				IFNULL(
					(
						SELECT downloadFiles.description
						  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userDownloads
						       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.downloadFiles ON (userDownloads.downloadID = downloadFiles.ID)
						 WHERE users.ID = userDownloads.userID
						 ORDER BY userDownloads.timestamp DESC
						 LIMIT 1
					),
				"") AS RecentFile,
				IFNULL(territories.country, "Unknown") AS territory
			FROM '.system::getConfig()->getDatabase('mofilm_content').'.users
			     LEFT JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movies ON (users.ID = movies.userID)
			     LEFT JOIN '.system::getConfig()->getDatabase('mofilm_content').'.territories ON (users.territoryID = territories.ID)
		   WHERE movies.userID IS NULL
		     AND users.enabled = :Enabled
		     AND users.clientID = 0';

		if ( $this->getTerritoryID() ) {
			$query .= ' AND users.territoryID = :TerritoryID ';
		}
		$query .= ' GROUP BY users.ID';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':Enabled', mofilmUser::ENABLED_Y);
		if ( $this->getTerritoryID() ) {
			$oStmt->bindValue(':TerritoryID', $this->getTerritoryID());
		}

		if ( $oStmt->execute() ) {
			$this->getReportData()->query()->beginTransaction();

			foreach ( $oStmt as $row ) {
				$row['territory'] = utilityStringFunction::capitaliseEncodedString($row['territory']);
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
		if ( $this->getTerritoryID() ) {
			return 'Inactive Users In '.$this->getTerritory()->getShortName();
		} else {
			return 'Inactive Users';
		}
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		if ( $this->getTerritoryID() ) {
			return 'Report detailing all users from '.$this->getTerritory()->getCountry() .' who have not uploaded a movie.';
		} else {
			return 'Report detailing all users from any country who have not uploaded a movie.';
		}
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