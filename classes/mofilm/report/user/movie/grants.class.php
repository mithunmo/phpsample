<?php
/**
 * mofilmReportUserMovieGrants
 * 
 * Stored in mofilmReportUserMovieGrants.class.php
 * 
 * @author Pavan Kumar P G
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserMovieGrants
 * @version $Rev: 1 $
 */


/**
 * mofilmReportUserMovieGrants
 * 
 * Exports grant data by event/brand wise
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserMovieGrants
 */
class mofilmReportUserMovieGrants extends mofilmReportBase {
	
	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		/*if ( !is_numeric($this->getSourceID()) && !is_null($this->getSourceID())) {
			throw new reportCentreException('Invalid value supplied for SourceID');
		}*/
		return true;
	}
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReportColumn(new reportColumn('eventname', 'Event', 30));
		$this->addReportColumn(new reportColumn('sourcename', 'Brand', 30));
		$this->addReportColumn(new reportColumn('fullname', 'User Name', 30));
		$this->addReportColumn(new reportColumn('email', 'Email Address', 40));
		$this->addReportColumn(new reportColumn('created', 'Requested On', 20));
		$this->addReportColumn(new reportColumn('requestedAmount', 'Requested Amount', 20));
		$this->addReportColumn(new reportColumn('grantedAmount', 'Granted Amount', 20));
		$this->addReportColumn(new reportColumn('status', 'Grant Status', 20));
		$this->addReportColumn(new reportColumn('moderatorName', 'Moderated By', 30));
		$this->addReportColumn(new reportColumn('moderated', 'Moderated On', 20));
		$this->addReportColumn(new reportColumn('movieID', 'Movie ID', 10));
		$this->addReportColumn(new reportColumn('award', 'Award', 20));
		}
	
	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		$query = "
			SELECT 
			  users.email, CONCAT(users.firstname,' ', users.surname) AS fullname,
			  IFNULL(CONCAT(musers.firstname,' ', musers.surname), '') AS moderatorName,
			  events.name as eventname, sources.name as sourcename, IFNULL(userMovieGrants.requestedAmount, '') AS requestedAmount,
			  IFNULL(userMovieGrants.grantedAmount, '') AS grantedAmount, userMovieGrants.created, userMovieGrants.status,
			  IFNULL(userMovieGrants.movieID, '') AS movieID,
			  IFNULL(userMovieGrants.moderated, '') AS moderated,
			  IFNULL(
					(
					 SELECT IF(position > 0, CONCAT(movieAwards.type, ' (', movieAwards.position, ')'), movieAwards.type) AS award
					   FROM ".system::getConfig()->getDatabase('mofilm_content').".movieAwards
					  WHERE movieAwards.movieID = userMovieGrants.movieID
					    AND movieAwards.type IN ('Winner', 'Finalist', 'Runner Up', 'Shortlisted')
					  ORDER BY FIELD(type, 'Winner', 'Finalist', 'Runner Up', 'Shortlisted') ASC, position ASC LIMIT 1), ''
				) AS award
			   
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".userMovieGrants
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".users ON (userMovieGrants.userID = users.ID)
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".grants ON (userMovieGrants.grantID = grants.ID)
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".sources ON (grants.sourceID = sources.ID)
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".events ON (sources.eventID = events.ID)
			       LEFT JOIN ".system::getConfig()->getDatabase('mofilm_content').".users AS musers ON (userMovieGrants.moderatorID = musers.ID)";
		
		$where = array();

		if ( $this->getEventID() > 0 ) {
			$where[] = " events.ID = :EventID ";
		}
		
		if ( $this->getSourceID() > 0 ) {
			$where[] = " grants.sourceID = :SourceID ";
		}
		
		if ( $this->getUserMovieGrantStatus() !== 'Any') {
			$where[] = " userMovieGrants.status = :GrantStatus";
		}

		if ( count($where) > 0 ) {
			$query .= " WHERE ".  implode(' AND ', $where);
		}

		$query .= " ORDER BY userMovieGrants.ID ASC";

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);
		
		if ( $this->getEventID() > 0 ) {
			$oStmt->bindValue(':EventID', $this->getEventID(), PDO::PARAM_INT);
		}
		
		if ( $this->getSourceID() > 0 ) {
			$oStmt->bindValue(':SourceID', $this->getSourceID(), PDO::PARAM_INT);
		}
		
		if ( $this->getUserMovieGrantStatus() !== 'Any') {
			$oStmt->bindValue(':GrantStatus', $this->getUserMovieGrantStatus(), PDO::PARAM_STR);
		}

		if ( $oStmt->execute() ) {
			
			$this->getReportData()->query()->beginTransaction();
			
			foreach ( $oStmt as $row ) {
				$row['eventname'] = utilityStringFunction::capitaliseEncodedString($row['eventname']);
				$row['sourcename'] = utilityStringFunction::capitaliseEncodedString($row['sourcename']);
				$row['fullname'] = utf8_decode($row['fullname']);

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
		return 'Grants data By Source';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return 'Details of applied grants data by Brand / Source wise '.$this->getSource()->getName();
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