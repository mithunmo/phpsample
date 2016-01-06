<?php
/**
 * mofilmReportEventUserDownloads
 * 
 * Stored in mofilmReportEventUserDownloads.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventUserDownloads
 * @version $Rev: 10 $
 */


/**
 * mofilmReportEventUserDownloads
 * 
 * Reports on event downloads by userID for the specified event.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventUserDownloads
 */
class mofilmReportEventUserDownloads extends mofilmReportBase {
	
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
		$this->addReportColumn(new reportColumn('date', 'Date', 20));
		
		foreach ( $this->getEvent()->getSourceSet() as $oSource ) {
			$this->addReportColumn(new reportColumn($oSource->getID(), $oSource->getName(), strlen($oSource->getName()), reportColumn::FIELD_INTEGER));
		}
		
		$this->addReportColumn(new reportColumn('total', 'Total', 20, reportColumn::FIELD_INTEGER));
		$this->addReportColumn(new reportColumn('uniqueUsers', 'Unique Users', 20, reportColumn::FIELD_INTEGER));
		$this->addReportColumn(new reportColumn('uniqueRegs', 'Unique Registrations', 20, reportColumn::FIELD_INTEGER));
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$uniqueUsers = $this->getUniqueUsersByDate();
		$uniqueRegs = $this->getUniqueUserRegistrationsByDate();
		
		$query = "
			SELECT userDownloads.sourceID, DATE_FORMAT(userDownloads.timestamp, '%d-%b-%Y') AS repDate
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".userDownloads
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".downloadFiles ON (
			           downloadFiles.ID = userDownloads.downloadID AND filetype = :FileType
			       )
			 WHERE userDownloads.eventID = :EventID
			   AND userDownloads.timestamp BETWEEN :tsStart AND :tsEnd
			 GROUP BY userDownloads.userID, userDownloads.sourceID
			 ORDER BY DATE_FORMAT(timestamp, '%Y%m%d') ASC";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':EventID', $this->getEventID(), PDO::PARAM_INT);
		$oStmt->bindValue(':FileType', mofilmDownloadFile::FILETYPE_BRIEF);
		$oStmt->bindValue(':tsStart', $this->getEvent()->getStartDate());
		$oStmt->bindValue(':tsEnd', $this->getEvent()->getEndDate());
		
		if ( $oStmt->execute() ) {
			/*
			 * Create a temporary array so that we can aggregate the data to a row per date
			 */
			$tmp = array();
			foreach ( $oStmt as $row ) {
				if ( !isset($tmp[$row['repDate']][$row['sourceID']]) ) {
					$tmp[$row['repDate']][$row['sourceID']] = 1;
				} else {
					$tmp[$row['repDate']][$row['sourceID']]++;
				} 
			}
			
			$this->getReportData()->query()->beginTransaction();
			
			/*
			 * Fetch the sources so that we can ensure the row indexes match the columns
			 */
			$sources = $this->getEvent()->getSourceSet()->getObjectIDs();
			foreach ( $tmp as $date => $data ) {
				$row = array('date' => $date, 'total' => array_sum(array_values($data)));
				foreach ( $sources as $sourceID ) {
					$row[$sourceID] = (array_key_exists($sourceID, $data) ? $data[$sourceID] : 0);
				}
				
				if ( array_key_exists($date, $uniqueUsers) ) {
					$row['uniqueUsers'] = $uniqueUsers[$date];
				}
				if ( array_key_exists($date, $uniqueRegs) ) {
					$row['uniqueRegs'] = $uniqueRegs[$date];
				} else {
					$row['uniqueRegs'] = 0;
				}
				$this->getReportData()->addRow($row);
			}
			
			$this->getReportData()->query()->commit();
			
			/*
			 * Add some totals to the bottom of the sheet
			 */
			$row = array('date' => 'Totals');
			foreach ( $sources as $sourceID ) {
				$row[$sourceID] = $this->getReportData()->sumColumn($sourceID);
			}
			$row['total'] = array_sum($row);
			$row['uniqueUsers'] = array_sum(array_values($uniqueUsers));
			$row['uniqueRegs'] = array_sum(array_values($uniqueRegs));
			
			$this->getReportData()->addRow($row);
		}
		$oStmt->closeCursor();
		return true;
	}
	
	/**
	 * Returns an array of unique users by date, with the date formatted to D-m-Y
	 * 
	 * @return array
	 */
	function getUniqueUsersByDate() {
		$query = "
			SELECT DATE_FORMAT(userDownloads.timestamp, '%d-%b-%Y') AS repDate, COUNT(DISTINCT(userDownloads.userID)) AS userCount
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".userDownloads
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".downloadFiles ON (
			           downloadFiles.ID = userDownloads.downloadID AND filetype = :FileType
			       )
			 WHERE userDownloads.eventID = :EventID
			   AND userDownloads.timestamp BETWEEN :tsStart AND :tsEnd
			 GROUP BY repDate";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':EventID', $this->getEventID(), PDO::PARAM_INT);
		$oStmt->bindValue(':FileType', mofilmDownloadFile::FILETYPE_BRIEF);
		$oStmt->bindValue(':tsStart', $this->getEvent()->getStartDate());
		$oStmt->bindValue(':tsEnd', $this->getEvent()->getEndDate());
		
		$return = array();
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$return[$row['repDate']] = $row['userCount'];
			}
		}
		$oStmt->closeCursor();
		return $return;
	}
	
	/**
	 * Gets unique user registrations by date between event start and end
	 * 
	 * @return array
	 */
	function getUniqueUserRegistrationsByDate() {
		$query = "
			SELECT DATE_FORMAT(users.registered, '%d-%b-%Y') AS repDate, COUNT(users.ID) AS userCount
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".users
			 WHERE enabled='Y'
			   AND registered BETWEEN :tsStart AND :tsEnd
			 GROUP BY repDate";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':tsStart', $this->getEvent()->getStartDate());
		$oStmt->bindValue(':tsEnd', $this->getEvent()->getEndDate());
		
		$return = array();
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$return[$row['repDate']] = $row['userCount'];
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
		return 'Event User Downloads';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return
			'Report for brief downloads by unique user for '.$this->getEvent()->getName().
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