<?php
/**
 * mofilmReportEventUploads
 * 
 * Stored in mofilmReportEventUploads.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventUploads
 * @version $Rev: 10 $
 */


/**
 * mofilmReportEventUploads
 * 
 * Reports on the number of uploads by eventID, by source. This covers only
 * approved uploads that are active between the event start and end dates.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventUploads
 */
class mofilmReportEventUploads extends mofilmReportBase {
	
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
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = "
			SELECT movieSources.sourceID, DATE_FORMAT(movies.uploaded, '%d-%b-%Y') AS repDate, COUNT(*) AS sourceCnt
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".movies
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".movieSources ON ( movieSources.movieID = movies.ID )
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".sources ON ( sources.ID = movieSources.sourceID )
			 WHERE sources.eventID = :EventID
			   AND movies.uploaded BETWEEN :tsStart AND :tsEnd
			   AND movies.status = 'Approved'
			   AND movies.active = 'Y'
			 GROUP BY repDate, sourceID
			 ORDER BY DATE_FORMAT(movies.uploaded, '%Y%m%d') ASC";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':EventID', $this->getEventID(), PDO::PARAM_INT);
		$oStmt->bindValue(':tsStart', $this->getEvent()->getStartDate());
		$oStmt->bindValue(':tsEnd', $this->getEvent()->getEndDate());
		
		if ( $oStmt->execute() ) {
			/*
			 * Create a temporary array so that we can aggregate the data to a row per date
			 */
			$tmp = array();
			foreach ( $oStmt as $row ) {
				$tmp[$row['repDate']][$row['sourceID']] = $row['sourceCnt']; 
			}
			
			/*
			 * Fetch the sources so that we can ensure the row indexes match the columns
			 */
			$sources = $this->getEvent()->getSourceSet()->getObjectIDs();
			
			$this->getReportData()->query()->beginTransaction();
			
			foreach ( $tmp as $date => $data ) {
				$row = array('date' => $date, 'total' => array_sum(array_values($data)));
				foreach ( $sources as $sourceID ) {
					$row[$sourceID] = (array_key_exists($sourceID, $data) ? $data[$sourceID] : 0);
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
		return 'Event Approved Uploads';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return
			'Report for approved uploads by source for '.$this->getEvent()->getName().
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