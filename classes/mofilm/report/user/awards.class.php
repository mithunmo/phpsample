<?php
/**
 * mofilmReportUserAwards
 * 
 * Stored in mofilmReportUserAwards.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2011
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserAwards
 * @version $Rev: 349 $
 */


/**
 * mofilmReportUserAwards
 * 
 * Reports on all users with an uploaded movie and the number of awards they have.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserAwards
 */
class mofilmReportUserAwards extends mofilmReportBase {
	
	/**
	 * @see reportBase::isValid()
	 *
	 * @return boolean
	 */
	function isValid() {
		return true;
	}
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('userID', 'User ID', 10, reportColumn::FIELD_INTEGER));
		$this->addReportColumn(new reportColumn('fullname', 'Name', 30));
		$this->addReportColumn(new reportColumn('shortlisted', 'Shortlisted', 10, reportColumn::FIELD_INTEGER));
		$this->addReportColumn(new reportColumn('runnerup', 'Runner Up', 10, reportColumn::FIELD_INTEGER));
		$this->addReportColumn(new reportColumn('finalist', 'Finalist', 10, reportColumn::FIELD_INTEGER));
		$this->addReportColumn(new reportColumn('winner', 'Event Winner', 10, reportColumn::FIELD_INTEGER));
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$tmp = array();
		$query = "
			SELECT users.ID AS userID, CONCAT(users.firstname, ' ', users.surname) AS fullname, users.email, movieAwards.type, COUNT(*) AS cnt
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".movies
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".users ON ( movies.userID = users.ID )
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".movieAwards ON ( movies.ID = movieAwards.movieID )
			WHERE movies.status = :MovieStatus
			  AND movies.active = :MovieActive
			GROUP BY users.ID, movieAwards.type
			ORDER BY fullname ASC";
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':MovieStatus', mofilmMovie::STATUS_APPROVED);
		$oStmt->bindValue(':MovieActive', mofilmMovie::ACTIVE_Y);

		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				if ( !isset($tmp[$row['userID']]) ) {
					$tmp[$row['userID']] = array(
						'userID' => $row['userID'],
						'fullname' => (strlen(trim($row['fullname'])) == 0 ? $row['email'] : utf8_decode($row['fullname'])),
						'shortlisted' => 0,
						'runnerup' => 0,
						'finalist' => 0,
						'winner' => 0,
					);
				}

				$tmp[$row['userID']][str_replace(' ', '', strtolower($row['type']))] = $row['cnt'];
			}

			$this->getReportData();
			$this->getReportData()->query()->beginTransaction();

			foreach ( $tmp as $row ) {
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
		return 'User Awards Breakdown';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return
			'Report detailing all users with an approved movie and the number of '.
			'awards of each type they have achieved to date.';
	}
	
	/**
	 * @see reportBase::getValidGroupByOptions()
	 *
	 * @return array
	 */
	function getValidGroupByOptions() {
		return array();
	}

	/**
	 * @see reportBase::getValidOrderByOptions()
	 *
	 * @return array
	 */
	function getValidOrderByOptions() {
		return array();
	}
}