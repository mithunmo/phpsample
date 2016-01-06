<?php
/**
 * mofilmReportSourceCountryDownloads
 * 
 * Stored in mofilmReportSourceCountryDownloads.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportSourceCountryDownloads
 * @version $Rev: 10 $
 */


/**
 * mofilmReportSourceCountryDownloads
 * 
 * Details downloads by country for the specified event / source.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportSourceCountryDownloads
 */
class mofilmReportSourceCountryDownloads extends mofilmReportBase {
	
	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( !$this->getSourceID() || !is_numeric($this->getSourceID()) ) {
			throw new reportCentreException('Invalid value supplied for SourceID');
		}
		return true;
	}
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('country', 'Country', 20));
		$this->addReportColumn(new reportColumn('count', 'Total', 20, reportColumn::FIELD_INTEGER));
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = "
			SELECT country, territoryID, COUNT(*) AS count
			FROM (
				SELECT IFNULL(territories.country, 'Unknown') AS country, IFNULL(territories.ID, 0) AS territoryID
				  FROM ".system::getConfig()->getDatabase('mofilm_content').".userDownloads
				       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".users ON (userDownloads.userID = users.ID)
				       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".downloadFiles ON (userDownloads.downloadID = downloadFiles.ID)
				       LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON (users.territoryID = territories.ID)
				 WHERE userDownloads.eventID = :EventID
				   AND userDownloads.sourceID = :SourceID
				   AND downloadFiles.filetype = :FileType
				   AND userDownloads.timestamp BETWEEN :tsStart AND :tsEnd
				 GROUP BY userDownloads.userID
			) AS t1
			GROUP BY country
			ORDER BY count DESC";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':EventID', $this->getSource()->getEventID(), PDO::PARAM_INT);
		$oStmt->bindValue(':SourceID', $this->getSource()->getID(), PDO::PARAM_INT);
		$oStmt->bindValue(':FileType', mofilmDownloadFile::FILETYPE_BRIEF);
		$oStmt->bindValue(':tsStart', $this->getSource()->getEvent()->getStartDate());
		$oStmt->bindValue(':tsEnd', $this->getSource()->getEvent()->getEndDate());
		
		if ( $oStmt->execute() ) {
			
			$this->getReportData()->query()->beginTransaction();
			
			foreach ( $oStmt as $row ) {
				$row['country'] = utilityStringFunction::capitaliseEncodedString($row['country']);
				
				$this->getReportData()->addRow($row);
			}
			
			$this->getReportData()->query()->commit();
			
			$row = array(
				'country' => 'Totals',
				'count' => $this->getReportData()->sumColumn('count'),
			);
			
			$this->getReportData()->addRow($row);
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
		return 'Source Country Downloads';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return
			'Report for unique brief downloads by territory for brand '.$this->getSource()->getName().
			' from event '.$this->getSource()->getEvent()->getName().
			' between '.$this->getSource()->getEvent()->getStartDate().' and '.$this->getSource()->getEvent()->getEndDate();
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