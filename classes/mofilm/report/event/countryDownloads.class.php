<?php
/**
 * mofilmReportEventCountryDownloads
 * 
 * Stored in mofilmReportEventCountryDownloads.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventCountryDownloads
 * @version $Rev: 10 $
 */


/**
 * mofilmReportEventCountryDownloads
 * 
 * Details downloads by country for the specified event.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventCountryDownloads
 */
class mofilmReportEventCountryDownloads extends mofilmReportBase {
	
	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( !$this->getEventID() || !is_numeric($this->getEventID()) ) {
			throw new reportCentreException('Invalid value supplied for EventID');
		}
		return true;
	}
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('country', 'Country', 20));
		$this->addReportColumn(new reportColumn('count', 'Total', 20, reportColumn::FIELD_INTEGER));
		$this->addReportColumn(new reportColumn('uniqueRegs', 'Unique Registrations', 20, reportColumn::FIELD_INTEGER));
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$uniqueUsers = $this->getUniqueUserRegistrationsByTerritory();
		
		/*
		 * Original report used LEFT JOIN, but err, that would over count
		 * and match any record - including 'nulls', so this is a patched
		 * version that actually works.
		 */
		$query = "
			SELECT IFNULL(territories.country, 'Unknown') AS country, IFNULL(territories.ID, 0) AS territoryID, COUNT(*) AS count
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".userDownloads
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".users ON (users.ID = userDownloads.userID)
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".downloadFiles ON (downloadFiles.ID=userDownloads.downloadID AND filetype = :FileType)
			       LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON (territories.ID = users.territoryID)
			 WHERE userDownloads.eventID = :EventID
			   AND userDownloads.timestamp BETWEEN :tsStart AND :tsEnd
			 GROUP BY territories.ID
			 ORDER BY count DESC";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':EventID', $this->getEventID(), PDO::PARAM_INT);
		$oStmt->bindValue(':FileType', mofilmDownloadFile::FILETYPE_BRIEF);
		$oStmt->bindValue(':tsStart', $this->getEvent()->getStartDate());
		$oStmt->bindValue(':tsEnd', $this->getEvent()->getEndDate());
		
		if ( $oStmt->execute() ) {
			$this->getReportData()->query()->beginTransaction();
			foreach ( $oStmt as $row ) {
				$row['country'] = utilityStringFunction::capitaliseEncodedString($row['country']);
				
				if ( array_key_exists($row['territoryID'], $uniqueUsers) ) {
					$row['uniqueRegs'] = $uniqueUsers[$row['territoryID']];
				} else {
					$row['uniqueRegs'] = 0;
				}
				unset($row['territoryID']);
				
				$this->getReportData()->addRow($row);
			}
			$this->getReportData()->query()->commit();
			
			$row = array(
				'country' => 'Totals',
				'count' => $this->getReportData()->sumColumn('count'),
				'uniqueRegs' => $this->getReportData()->sumColumn('uniqueRegs')
			);
			
			$this->getReportData()->addRow($row);
		}
		$oStmt->closeCursor();
		return true;
	}

	/**
	 * Gets unique user registrations by date between event start and end
	 * 
	 * @return array
	 */
	function getUniqueUserRegistrationsByTerritory() {
		$query = "
			SELECT IFNULL(territoryID, 0) AS territoryID, COUNT(users.ID) AS userCount
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".users
			 WHERE enabled='Y'
			   AND registered BETWEEN :tsStart AND :tsEnd
			 GROUP BY territoryID";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':tsStart', $this->getEvent()->getStartDate());
		$oStmt->bindValue(':tsEnd', $this->getEvent()->getEndDate());
		
		$return = array();
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$return[$row['territoryID']] = $row['userCount'];
			}
		}
		$oStmt->closeCursor();
		return $return;
	}

	/**
	 * @see reportBase::getReportName()
	 *
	 * @return string
	 */
	function getReportName() {
		return 'Event Country Downloads';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return
			'Report for brief downloads by territory for '.$this->getEvent()->getName().
			' between '.$this->getEvent()->getStartDate().' and '.$this->getEvent()->getEndDate();
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