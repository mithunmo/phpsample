<?php
/**
 * mofilmReportUserUploads
 * 
 * Stored in mofilmReportUserUploads.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserUploads
 * @version $Rev: 10 $
 */


/**
 * mofilmReportUserUploads
 * 
 * Exports the user details for all users who have uploaded at least one movie
 * that was approved and is active. Optionally only users who uploaded to a
 * specific event.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserUploads
 */
class mofilmReportUserUploads extends mofilmReportBase {
	
	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		return true;
	}
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('firstname', 'Firstname', 30));
		$this->addReportColumn(new reportColumn('surname', 'Surname ', 30));
		$this->addReportColumn(new reportColumn('email', 'Email Address', 40));
		$this->addReportColumn(new reportColumn('city', 'City', 40));
		$this->addReportColumn(new reportColumn('country', 'Country', 20));
	}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		if ( $this->getEventID() ) {
			$query = "
				SELECT 
					users.firstname,
					users.surname, 
					users.email, 
					(SELECT userData.paramValue FROM ".system::getConfig()->getDatabase('mofilm_content').".userData WHERE userID=users.ID AND paramName='City') AS city,
					territories.country AS country
					
				FROM 
					".system::getConfig()->getDatabase('mofilm_content').".users
					INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".movies ON (users.ID = movies.userID)
					INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".movieSources ON (movies.ID = movieSources.movieID)
					INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".sources ON (movieSources.sourceID = sources.ID) 
					LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON ( territories.ID = users.territoryID )
					
				WHERE 
					users.`enabled` = :Enabled
					AND movies.status = :Status
					AND movies.active = :Active
					AND users.territoryID IS NOT NULL
					AND users.clientID = 0
					AND sources.eventID = :EventID
					
				GROUP BY users.ID";
		} else {
			$query = "
				SELECT 
					users.firstname,
					users.surname, 
					users.email, 
					(SELECT userData.paramValue FROM ".system::getConfig()->getDatabase('mofilm_content').".userData WHERE userID=users.ID AND paramName='City') AS city,
					territories.country AS country
					
				FROM 
					".system::getConfig()->getDatabase('mofilm_content').".users
					INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".movies ON (users.ID = movies.userID) 
					LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".territories ON ( territories.ID = users.territoryID )
					
				WHERE 
					users.`enabled` = :Enabled
					AND movies.status = :Status
					AND movies.active = :Active
					AND users.territoryID IS NOT NULL
					AND users.clientID = 0
					
				GROUP BY users.ID";
		}
		
		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		$oStmt->bindValue(':Enabled', mofilmUserBase::ENABLED_Y);
		$oStmt->bindValue(':Status', mofilmMovieBase::STATUS_APPROVED);
		$oStmt->bindValue(':Active', mofilmMovieBase::ACTIVE_Y);
		if ( $this->getEventID() ) {
			$oStmt->bindValue(':EventID', $this->getEventID(), PDO::PARAM_INT);
		}
		
		if ( $oStmt->execute() ) {
			
			$this->getReportData()->query()->beginTransaction();
			
			foreach ( $oStmt as $row ) {
				$row['country'] = utilityStringFunction::capitaliseEncodedString($row['country']);
				$row['firstname'] = utf8_decode($row['firstname']);
				$row['surname'] = utf8_decode($row['surname']);
				$row['city'] = utf8_decode($row['city']);
				
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
		return 'User Approved Uploads'.($this->getEventID() ? ' '.$this->getEventID() : '');
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		$string = 'Report of user details with an approved upload';
		if ( $this->getEventID() ) {
			$string .= ' for event '.$this->getEvent()->getName();
		}
		return $string;
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