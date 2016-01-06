<?php
/**
 * mofilmReportEventCountryDownloadsBySource
 * 
 * Stored in mofilmReportEventCountryDownloadsBySource.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventCountryDownloadsBySource
 * @version $Rev: 10 $
 */


/**
 * mofilmReportEventCountryDownloadsBySource
 * 
 * Details downloads by country for the specified event by source.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventCountryDownloadsBySource
 */
class mofilmReportEventCountryDownloadsBySource extends mofilmReportBase {
	
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
		$this->addReportColumn(new reportColumn('country', 'Country', 40));
		
		foreach ( $this->getEvent()->getSourceSet() as $oSource ) {
			$this->addReportColumn(new reportColumn($oSource->getID(), $oSource->getName(), strlen($oSource->getName()), reportColumn::FIELD_INTEGER));
		}
		
		$this->addReportColumn(new reportColumn('total', 'Total', 10, reportColumn::FIELD_INTEGER));
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = "
			SELECT sourceID, territoryID, country, SUM(count) AS count
			  FROM (
					SELECT userDownloads.sourceID, IFNULL(territories.ID, 0) AS territoryID, IFNULL(territories.country, 'Unknown') AS country, 1 AS count
					  FROM ".system::getConfig()->getDatabase('mofilm_content').".userDownloads
					       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".users ON (userDownloads.userID = users.ID) 
					       LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON (users.territoryID = territories.ID) 
					       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".downloadFiles ON (userDownloads.downloadID = downloadFiles.ID AND downloadFiles.filetype = :FileType) 
					 WHERE userDownloads.eventID = :EventID
					   AND userDownloads.timestamp BETWEEN :tsStart AND :tsEnd
					 GROUP BY userDownloads.userID, userDownloads.sourceID, territoryID
			 ) AS t1
			 GROUP BY sourceID, territoryID
			 ORDER BY country ASC";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':EventID', $this->getEventID(), PDO::PARAM_INT);
		$oStmt->bindValue(':FileType', mofilmDownloadFile::FILETYPE_BRIEF);
		$oStmt->bindValue(':tsStart', $this->getEvent()->getStartDate());
		$oStmt->bindValue(':tsEnd', $this->getEvent()->getEndDate());
		
		if ( $oStmt->execute() ) {
			/*
			 * Create temporary array to hold the data so we can sort it
			 */
			$tmp = array();
			foreach ( $oStmt as $row ) {
				$row['country'] = utilityStringFunction::capitaliseEncodedString($row['country']);
								
				$tmp[$row['country']][$row['sourceID']] = $row['count'];
			}
			
			$this->getReportData()->query()->beginTransaction();
			
			/*
			 * Fetch the sources so that we can ensure the row indexes match the columns
			 */
			$sources = $this->getEvent()->getSourceSet()->getObjectIDs();
			foreach ( $tmp as $country => $data ) {
				$row = array('country' => $country, 'total' => array_sum(array_values($data)));
				foreach ( $sources as $sourceID ) {
					$row[$sourceID] = (array_key_exists($sourceID, $data) ? $data[$sourceID] : 0);
				}
				
				$this->getReportData()->addRow($row);
			}
			
			$this->getReportData()->query()->commit();
			
			/*
			 * Add some totals to the bottom of the sheet
			 */
			$row = array('country' => 'Totals');
			foreach ( $sources as $sourceID ) {
				$row[$sourceID] = $this->getReportData()->sumColumn($sourceID);
			}
			$row['total'] = array_sum($row);
			
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
		return 'Event Source Country Dnlds';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return
			'Report for unique brief downloads by territory for '.$this->getEvent()->getName().
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