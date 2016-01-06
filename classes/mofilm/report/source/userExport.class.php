<?php
/**
 * mofilmReportSourceUserExport
 * 
 * Stored in mofilmReportSourceUserExport.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportSourceUserExport
 * @version $Rev: 10 $
 */


/**
 * mofilmReportSourceUserExport
 * 
 * Exports user details who downloaded a brief from a source
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportSourceUserExport
 */
class mofilmReportSourceUserExport extends mofilmReportBase {
	
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
		$this->addReportColumn(new reportColumn('userID', 'User ID', 10, reportColumn::FIELD_INTEGER));
		$this->addReportColumn(new reportColumn('email', 'Email Address', 40));
		$this->addReportColumn(new reportColumn('firstname', 'Firstname', 30));
		$this->addReportColumn(new reportColumn('surname', 'Surname ', 30));
		$this->addReportColumn(new reportColumn('country', 'Country', 20));
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = "
			SELECT users.ID AS userID, users.email, users.firstname, users.surname, IFNULL(territories.country, 'Unknown') AS country
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".userDownloads
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".users ON (userDownloads.userID = users.ID)
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".downloadFiles ON (userDownloads.downloadID = downloadFiles.ID)
			       LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON (users.territoryID = territories.ID)
			 WHERE userDownloads.sourceID = :SourceID
			   AND downloadFiles.filetype = :FileType
			 GROUP BY userDownloads.userID
			 ORDER BY users.email ASC";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':SourceID', $this->getSourceID(), PDO::PARAM_INT);
		$oStmt->bindValue(':FileType', mofilmDownloadFile::FILETYPE_BRIEF);
		
		if ( $oStmt->execute() ) {
			
			$this->getReportData()->query()->beginTransaction();
			
			foreach ( $oStmt as $row ) {
				$row['country'] = utilityStringFunction::capitaliseEncodedString($row['country']);
				$row['firstname'] = utf8_decode($row['firstname']);
				$row['surname'] = utf8_decode($row['surname']);
				
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
		return 'Source User Export '.$this->getSourceID();
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return 'Details of users who downloaded a brief for '.$this->getSource()->getName();
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